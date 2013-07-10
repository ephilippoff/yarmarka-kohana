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
			$seo = ORM::factory('Seo', array('url' => $url));
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
}
