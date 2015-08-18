<?php defined('SYSPATH') or die('No direct script access.');


class Twig_Functions
{
    public static function requestblock($path, $params = array())
    {
        return Request::factory($path)->post($params)->execute();
    }

    public static function requestoldview($path, $params = array())
    {
        return View::factory($path, $params)->render();
    }

    public static function css($file)
    {
        return Assets::factory('main')->css($file);
    }

    public static function js($file)
    {
        return Assets::factory('main')->js($file);
    }

    public static function url($link)
    {
        return "/".$link;
    }

    public static function staticfile($file)
    {
        return "/static/develop/".$file;
    }

    public static function debug($param)
    {
        return Debug::vars($param);
    }

    public static function obj($array = array())
    {
        return new Obj($array);
    }

    public static function domain($domain_str, $url_str, $protocol_str = "http://")
    {
        return Domain::get_domain_by_city($domain_str, $url_str, $protocol_str);
    }

    public static function file_exist($path)
    {
        return is_file($_SERVER['DOCUMENT_ROOT'].$path);
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

        // $myobject_count = Search::searchquery(
        //         array(
        //             "active" => TRUE,
        //             "published" =>TRUE,
        //             "user_id" => Auth::instance()->get_user()->id,
        //             "filters" => array()
        //         ), 
        //         array(), 
        //         array("count" => TRUE)
        //     )->execute()->get("count");
        return 1;
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
        $path = trim('/'.$path);
        if (is_file($_SERVER['DOCUMENT_ROOT'].$path)) {
            return $path;
        } else {
            return "http://yarmarka.biz/".$path;
        }
    }
}