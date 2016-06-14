<?php defined('SYSPATH') or die('No direct script access.');


class Twig_Functions
{
	public static function get_global($name) {
		if (array_key_exists($name, $GLOBALS)) {
			return $GLOBALS[$name];
		}
		return NULL;
	}

	public static function requestblock($path, $params = array())
	{
		return Request::factory($path)->post($params)->execute();
	}

	public static function requestmethod($classname, $actionname, $params = array())
	{
		return forward_static_call(array($classname, $actionname), $params);
	}

	public static function requestoldview($path, $params = array())
	{
		return View::factory($path, $params)->render();
	}

	public static function repeat($str, $times) {
		$res = '';
		for($i = 0;$i < $times;$i++) {
			$res .= $str;
		}
		return $res;
	}

	public static function get_categories() {
		$service = Services_Factory::factory('Categories');
		$categories = $service->getCategoryWithChilds(1, 5, array('title', 'url'), 2);
		// var_dump($categories); die;
		$flattern = function ($items, $level, $cb) {
			$res = array();
			foreach($items as $item) {
				$res []= array(
						'id' => $item['id']
						, 'title' => $item['title']
						, 'level' => $level + 1
						, 'childs' => $item['childs']
						, 'url' => $item['url']
					);
				$res = array_merge($res, $cb($item['childs'], $level + 1, $cb));
			}
			return $res;
		};

		$categories = $flattern($categories, 0, $flattern);
		return $categories;
	}

	public static function css($file)
	{
		try {
			$return = @Assets::factory('main')->css($file);
			$return = @$return."";
		} catch (Exception $e) {
			$return = "";
		}
		return $return;
	}

	public static function js($file)
	{
		try {
			$return = @Assets::factory('main')->js($file);
			$return = @$return."";
		} catch (Exception $e) {
			$return = "";
		}
		return $return;
	}

	public static function js_asset($file)
	{
		return "/".Assets::factory('main')->js($file, array("only_file_name" => TRUE));
	}

	public static function url($link)
	{
		return "/".$link;
	}

	public static function staticfile($file, $set_version = FALSE)
	{
		return Config::getStaticPath().$file;
	}

	public static function is_debug_mode()
	{
		return Config::is_debug_mode();
	}

	public static function debug($param)
	{
		//return Debug::vars($param);
		$text = '<pre>';
		$text .= var_export($param, true);
		$text .= '</pre>';

		return $text;
	}

	public static function obj($array = array())
	{
		return new Obj($array);
	}

	public static function domain($domain_str, $url_str, $protocol_str = "http://", $old = FALSE, $id = FALSE)
	{
		$new_engine_cities = Kohana::$config->load("common.new_engine_cities");
		if ($new_engine_cities AND $id) {
			if (in_array($id, $new_engine_cities)) {
				return Domain::get_domain_by_city($domain_str, $url_str, $protocol_str);
			} else {
				if (strpos($url_str, 'user') === false) {
					return Domain::get_domain_by_city_old($domain_str, $url_str, $protocol_str);
				} else {
					return Domain::get_domain_by_city("c", $url_str, $protocol_str);
				}
				
			}
		}

		return ($old) ? Domain::get_domain_by_city_old($domain_str, $url_str, $protocol_str) : Domain::get_domain_by_city($domain_str, $url_str, $protocol_str);
	}

	public static function sameUrlOnAnotherDomain($domain, $protocol_str = "http://") {
		return Domain::get_domain_by_city($domain, $domain . '\\' . Yarmarka\Models\Request::current()->getUrl(), $protocol_str);
	}

	public static function file_exist($path)
	{
		return is_file(URL::SERVER('DOCUMENT_ROOT').$path);
	}

	public static function strim($str, $param = NULL)
	{
		if ($param) {
			return trim($str, $param);
		}
		return trim($str);
	}

	public static function check_object_access($object, $action)
	{
		return Acl::check_object($object, $action);
	}


	public static function check_access($action)
	{
		return Acl::check($action);
	}

	public static function get_stat_cached_info($id)
	{
		return Cachestat::factory($id."insearch")->fetch();
	}

	public static function get_cart_info()
	{
		return Cart::get_info();
	}

	public static function get_favorites_info()
	{
		return ORM::factory('Favourite')->get_list_by_cookie();
	}

	public static function get_myobjects_info()
	{

		$myobject_count = Search::searchquery(
				array(
					"active" => TRUE,
					"published" =>TRUE,
					"user_id" => Auth::instance()->get_user()->id,
					"filters" => array()
				), 
				array(), 
				array("count" => TRUE)
			)->cached(Date::MINUTE * 10)->execute()->get("count");
		return $myobject_count;
	}

	public static function get_form_element()
	{
		$arguments = func_get_args();
		$name = $arguments[0];
		array_shift($arguments);
		return call_user_func_array("Form::".$name, $arguments);
	}

	public static function get_config($path)
	{
		return Kohana::$config->load($path);
	}

	public static function get_user()
	{
		return Auth::instance()->get_user();
	}

	public static function get_image_paths($filename)
	{
		return Imageci::getSitePaths($filename);
	}

	public static function get_file($path)
	{
		$_path = "/".trim($path);
		if (is_file(URL::SERVER('DOCUMENT_ROOT').$_path)) {
			return $_path;
		} else {

			return "http://yarmarka.biz/".$path;
		}
	}
	
	public static function get_date_diff($date1, $date2)
	{
		$result = "";
		try {
		$date1 = new DateTime($date1);
		$date2 = new DateTime($date2);
		}
		catch (Exception $e)
		{
			return "";
		}
		
		$diff = $date1->diff($date2);

		if ($diff->y) { $result .= $diff->format("%y л. "); }
		if ($diff->m) { $result .= $diff->format("%m мес. "); }
		if ($diff->d) { $result .= $diff->format("%d дн. "); }
		if ($diff->h) { $result .= $diff->format("%h ч. "); }
		if ($diff->i) { $result .= $diff->format("%i мин. "); }

		return $result;		
	}

	public static function get_service_icon_info($services = array(), $show_not_activated = FALSE)
	{
		$services = (array) $services;
		$result = array();

		foreach ($services as $name => $service_items) {

			if (in_array($name, array('up','premium','lider'))) {
				foreach ($service_items as $service_item) {
					$service_item = new Obj($service_item);
					$result_item = array();
					if ($name == "up" AND strtotime(date("Y-m-d H:i:s"). ' + 7 days') > strtotime(date("Y-m-d H:i:s")) ) {
						$result_item["name"] = $name;
						$result_item["icon_class"] = "fa-angle-double-up";
						$result_item["title"] = "Поднято ".date("Y-m-d H:i", strtotime($service_item->date_created));
					}
					elseif ($name == "premium" AND strtotime($service_item->date_expiration) > strtotime(date("Y-m-d H:i:s")) ) {
						$result_item["name"] = $name;
						$result_item["icon_class"] = "fa-info-circle";
						$result_item["title"] = "Текущая услуга 'Премиум' действует до ".date("Y-m-d H:i", strtotime($service_item->date_expiration));
					}
					elseif ($name == "lider" AND strtotime($service_item->date_expiration) > strtotime(date("Y-m-d H:i:s")) ) {
						$result_item["name"] = $name;
						$result_item["icon_class"] = "fa-info-circle";
						$result_item["title"] = "Текущая услуга 'Лидер' действует до ".date("Y-m-d H:i", strtotime($service_item->date_expiration));
					}
					elseif ($name == "cities") {
						$result_item["name"] = $name;
						$result_item["icon_class"] = "fa-info-circle";
						$result_item["title"] = "Приобретена услуга 'В несколько городов'";
					}

					if ( in_array(Arr::get($result_item, "name"), array("up","lider","premium")) AND $show_not_activated) {
						$not_activated = $service_item->count - $service_item->activated;
						if ($not_activated > 0) {
							$result_item["icon_class"] .= " fa-pulse";
							$result_item["title"] = "Осталось неактивированных ".$not_activated.". ".$result_item["title"];
						}
					}
					$result[] = $result_item;
				}
			} elseif (in_array($name, array('cities'))) {
				if ($name == "cities") {
					$result_item["name"] = $name;
					$result_item["icon_class"] = "fa-info-circle";
					$result_item["title"] = vsprintf("Объявление размещено в %d городах", array(count($service_items)));
					$result[] = $result_item;
				}

			}
			
		}

		return $result;
	}

	public static function get_request_uri()
	{
		return URL::SERVER('REQUEST_URI');
	}

	public static function get_session_value($name)
	{
		if (!$name) {
			return NULL;
		}

		if (isset($_SESSION[$name]) AND $_SESSION[$name]) {
			return $_SESSION[$name];
		}
		
		return NULL;
	}

	

	public static function get_userecho_token()
	{
		return Userecho::get_sso_token(Auth::instance()->get_user());
	}

	public static function get_current_city()
	{
		$domain = URL::SERVER('HTTP_HOST');
		
		$config = Kohana::$config->load("common");
		$main_domain = $config["main_domain"];

		$city_name = strtolower(trim( str_replace($main_domain, "", $domain), "."));

		$city = ORM::factory('City')->where('seo_name',"=",$city_name)->cached(Date::WEEK)->find();

		// $cities = array(
		// 	"surgut" => "Сургут",
		// 	"tyumen" => "Тюмень",
		// 	"nizhnevartovsk" => "Нижневартовск"
		// );
		return $city->title;
	}

	public static function get_city()
	{
		$domain = URL::SERVER('HTTP_HOST');
		
		$config = Kohana::$config->load("common");
		$main_domain = $config["main_domain"];

		$city_name = strtolower(trim( str_replace($main_domain, "", $domain), "."));

		$city = ORM::factory('City')->where('seo_name',"=",$city_name)->cached(Date::WEEK)->find();

		// $cities = array(
		// 	"surgut" => "Сургут",
		// 	"tyumen" => "Тюмень",
		// 	"nizhnevartovsk" => "Нижневартовск"
		// );
		return $city;
	}

	public static function custommenu($root)
	{
		$structure = ORM::factory('Structure')
			->where("url","=",$root)
			->find();

		$result = array();

		Twig_Functions::get_customenu_three($structure, $result);

		return $result;
	}

	public static function get_customenu_three($structure, &$result)
	{
		$user = Auth::instance()->get_user();

		$result["root"] = $structure->as_array();
		$result["childs"] = array();
		$subs = ORM::factory('Structure')
				->where("parent_id","=", $structure->id)
				->find_all();
		foreach ($subs as $sub) {
			if ($sub->for_admin AND !Acl::check("object.add.type")) {
				continue;
			}
			
			$c = ORM::factory('Structure')
				->where("parent_id","=", $sub->id)
				->count_all();
			if ($c > 0) {
				$tmp = array();
				
				Twig_Functions::get_customenu_three($sub, $tmp);
				$result["childs"][] = $tmp;
			} else {
				$result["childs"][] = $sub->as_array();
			}
		}
	}

	public static function pluk($arr, $property, $is_obj = false) {
		$res = array();
		foreach($arr as $item) {
			if ($is_obj) {
				$res []= $item->{$property};
			} else {
				$res []= $item[$property];
			}
		}
		return $res;
	}

	
}