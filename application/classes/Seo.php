<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Seo helper
 * 
 * @uses ORM
 * @package 
 * @copyright 2012
 * @author Mikhail Makeev <mihail.makeev@gmail.com> 
 * @version $id$
 */
class Seo extends ORM
{
	protected static $_cache = array();

	public static function get_title($url = FALSE)
	{
		if ( ! $url)
		{
			$url = Request::current()->uri();
		}
		return self::get_seo($url, 'title', self::get_seo('/', 'title'));
	}

	public static function set_title($value, $url = FALSE)
	{
		if ( ! $url)
		{
			$url = Request::current()->uri();
		}

		self::set_seo('title', $value, $url);
	}

	public static function get_keywords($url = FALSE)
	{
		if ( ! $url)
		{
			$url = Request::current()->uri();
		}
		return self::get_seo($url, 'keywords');
	}

	public static function set_keywords($value, $url = FALSE)
	{
		if ( ! $url)
		{
			$url = Request::current()->uri();
		}

		self::set_seo('keywords', $value, $url);
	}

	public static function get_description($url = FALSE)
	{
		if ( ! $url)
		{
			$url = Request::current()->uri();
		}
		return self::get_seo($url, 'description');
	}

	public static function set_description($value, $url = FALSE)
	{
		if ( ! $url)
		{
			$url = Request::current()->uri();
		}

		self::set_seo('description', $value, $url);
	}

	public static function get_seo($url, $element = '', $default = NULL)
	{
		$url = trim(trim($url), '/');

		if ( ! $seo = Arr::get(self::$_cache, $url))
		{
			$seo = ORM::factory('Seo', array('url' => $url))->cached(Date::WEEK);
			if ($seo->loaded())
			{
				$seo = $seo->as_array();
				self::$_cache[$url] = $seo;
			}
			else
			{
				$seo = array('url' => $url);
			}
		}

		return $element ? Arr::get($seo, $element, $default) : $seo;
	}

	public static function set_seo($element, $value, $url)
	{
		$seo = self::get_seo($url);
		if ( ! trim(@$seo[$element]))
		{
			$seo[$element] = $value;
		}
		self::$_cache[$seo['url']] = $seo;
	}

	public static function is_noindex()
	{
		$route_name = Route::name(Request::current()->route());
		$noindex_routes = Kohana::$config->load('seo.noindex_routes');
		
		return in_array($route_name, $noindex_routes);
	}

	public static function get_postfix()
	{
		return Kohana::$config->load('seo.title_postfix');
	}

	public static function get_seo_attributes($url, $query_params, $category, $city, $options = array())
	{
		$result = array();

		$region = ORM::factory('Region',73);

		$seo_pattern = ORM::factory('Seo_Pattern')->get_pattern($category->id, $query_params);

		if ($seo_pattern->loaded()) {
			$seo_params = ORM::factory('Seo')
								->by_hashed_url($url)
								->find();
			$seo_params = ($seo_params->loaded()) ? $seo_params->get_row_as_obj() : FALSE;

			if ($seo_params) {
				$result = unserialize($seo_params->params);
			} else {
				$result = ORM::factory('Seo')->save_seo($seo_pattern, $url, $category, $city);
			}

			foreach (array("h1","title","description","footer","anchor") as $value) {
				$result[$value] = Seo::preformat_seo_text($result[$value], $query_params,  $city, $region);
			}
		} else {
			$result["noindex"] = TRUE;
		}

		return $result;
	}

	public static function preformat_seo_text($str, $query_params, $city, $region)
	{
		if ( ! $str)
		{
			return FALSE;
		}
		libxml_use_internal_errors(TRUE);
		// convert values inside <params> tags
		preg_match_all('#\<params\>(.+?)\<\/params\>#s', $str, $matches);
		$params = $matches[1];
		foreach ($params as $params_html) 
		{
			$params_doc = new DOMDocument();
			$new_params_html = '';

			$params_doc->loadHTML($params_html);

			// parse and looking for value tags
			$values = $params_doc->getElementsByTagName('value');
			// convert tags to text
			foreach ($values as $value)
			{
				// @todo crazy php encoding magic
				$value_text = mb_convert_encoding(utf8_decode($value->nodeValue), 'ISO-8859-1', 'UTF-8');
				$value_tag = mb_convert_encoding($params_doc->saveHTML($value), 'ISO-8859-1', "UTF-8");

				$attr_name 	= $value->getAttribute('attr');
				$wordform 	= intval($value->getAttribute('wordform'));
				$default 	= utf8_decode($value->getAttribute('default'));

				// looking for tag attribute in query string
				if (in_array($attr_name, array_keys($query_params)))
				{
					if (is_array($query_params[$attr_name])) {
						$attr = ORM::factory('Attribute_Element',$query_params[$attr_name][0]);
					} else {
						$attr = ORM::factory('Attribute_Element',$query_params[$attr_name]);
					}
					

					$attr_title = $wordform > 1 ? $attr->{'title'.$wordform} : $attr->title;
					$new_value_text = str_replace($attr_name, $attr_title, $value_text);
					
					$new_params_html = str_replace($value_tag, $new_value_text, ($new_params_html ? $new_params_html : $params_html));
				}
				elseif ($default)
				{
					$new_params_html = str_replace($value_tag, $default, ($new_params_html ? $new_params_html : $params_html));
				}
			}

			$default_param = '';
			$def_params = $params_doc->getElementsByTagName('default');
			foreach ($def_params as $def_param)
			{
				$default_param = utf8_decode($def_param->nodeValue);
			}

			if ( ! $new_params_html)
			{
				$str = str_replace('<params>'.$params_html.'</params>', $default_param, $str);
			}
			else
			{
				$new_params_html = preg_replace('#<([a-z]*).*?>.*?</\1>#', '', $new_params_html);
				$str = str_replace('<params>'.$params_html.'</params>', $new_params_html, $str);
			}
		}

		$doc = new DOMDocument();
		$doc->loadHTML($str);

		// parse and looking for value tags
		$values = $doc->getElementsByTagName('value');
		// convert tags to text
		foreach ($values as $value)
		{
			$value_text = mb_convert_encoding($value->nodeValue, 'ISO-8859-1', 'UTF-8'); 
			$value_tag = mb_convert_encoding($doc->saveHTML($value), 'ISO-8859-1', 'UTF-8');

			$attr_name 	= $value->getAttribute('attr');
			$wordform 	= intval($value->getAttribute('wordform'));
			$default 	= utf8_decode($value->getAttribute('default'));

			// looking for tag attribute in query string
			if (in_array($attr_name, array_keys($query_params)))
			{
				if (is_array($query_params[$attr_name])) {
					$attr = ORM::factory('Attribute_Element',$query_params[$attr_name][0]);
				} else {
					$attr = ORM::factory('Attribute_Element',$query_params[$attr_name]);
				}
				$attr_title = $wordform > 1 ? $attr->{'title'.$wordform} : $attr->title;
				$new_value_text = str_replace($attr_name, $attr_title, $value_text);
				$str = str_replace($value_tag, $new_value_text, $str);
			}
			elseif ($default)
			{
				$str = str_replace($value_tag, $default, $str);
			}
		}

		// parse and looking for region tags
		$regions = $doc->getElementsByTagName('region');
		// convert tags to text
		foreach ($regions as $region_node)
		{
			$tag_text = mb_convert_encoding($region_node->nodeValue, 'ISO-8859-1', "UTF-8"); 
			$region_tag = mb_convert_encoding($doc->saveHTML($region_node), 'ISO-8859-1', "UTF-8");

			$wordform 	= $region_node->getAttribute('wordform');
			$default 	= utf8_decode($region_node->getAttribute('default'));

			if ($region OR $city)
			{
				// use city or region if city not exists
				$region = $city 
					? $city
					: $region;
				$region_title = $wordform == 2 ? $region->sinonim : $region->title;
				$new_region_text = str_replace('region', $region_title, $tag_text);
				$str = str_replace($region_tag, $new_region_text, $str);
			}
			elseif ($default) 
			{
				$str = str_replace($region_tag, $default, $str);
			}
		}

		// parse and looking for count tags
		$counts = $doc->getElementsByTagName('count');
		// convert tags to text
		foreach ($counts as $count)
		{
			$tag_text = mb_convert_encoding($count->nodeValue, 'ISO-8859-1', "UTF-8"); 
			$count_tag = mb_convert_encoding($doc->saveHTML($count), 'ISO-8859-1', "UTF-8");

			$default = utf8_decode($count->getAttribute('default'));
			$count_value = 0;

			if ($count_value > 5 OR strlen($count_value) > 1) // fix for case when count = '>1500'
			{
				$new_count_text = str_replace('count', $count_value, $tag_text);
				$str = str_replace($count_tag, $new_count_text, $str);
			}
			elseif ($default)
			{
				$str = str_replace($count_tag, $default, $str);
			}
		}
		
		// strip undefined and not converted tags
		$str = preg_replace('#<([a-z]*).*?>.*?</\1>#', '', $str);
		// replace duoble spaces
		$str = str_replace('  ', ' ', $str);

		return trim($str);
	}

	public static function preformat_pattern($str)
	{
		$str = self::randome_one_tag($str);
		$str = self::randome_tag($str);
		
		return $str;
	}

	public static function randome_tag($str)
	{
		preg_match('#\<random\>(.+?)\<\/random\>#s', $str, $matches);
		if (!count($matches)) return $str;

		$str_array = explode('|', $matches[1]);
		shuffle($str_array);

		$res = join(',', $str_array);

		return preg_replace('#(\<random\>.+?\<\/random\>)#s', $res, $str);
	}

	public static function randome_one_tag($str)
	{
		preg_match('#\<randomone\>(.+?)\<\/randomone\>#s', $str, $matches);
		if (!count($matches)) return $str;

		$str_array = explode('|', $matches[1]);
		$random_key = array_rand($str_array);

		$res = $str_array[$random_key];

		return preg_replace('#(\<randomone\>.+?\<\/randomone\>)#s', $res, $str);
	}
}
