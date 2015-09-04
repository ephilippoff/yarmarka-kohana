<?php defined('SYSPATH') or die('No direct script access.');


class Twig_Filters
{
    public static function contacthide($value)
    {
        return Contact::hide($value);
    }
	
    public static function formatphone($value)
    {
        return Contact::format_phone($value);
    }	

    public static function values($array)
    {
        return array_values($array);
    }

    public static function phoneico($value)
    {
        return "<i class='fa fa-phone mr5'></i>".$value;
    }

    public static function mobilephoneico($value)
    {
        return "<i class='fa fa-mobile-phone mr5'></i>".$value;
    }

    public static function emailico($value)
    {
        return "<i class='fa fa-envelope-o mr5'></i>".$value;
    }
}