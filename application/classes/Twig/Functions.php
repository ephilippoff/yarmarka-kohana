<?php defined('SYSPATH') or die('No direct script access.');


class Twig_Functions
{
    public static function requestblock($path, $params = array())
    {
        return Request::factory($path)->post($params)->execute();
    }

    public static function requestoldview($path)
    {
        return View::factory($path);
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
        return "/".$file;
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
}