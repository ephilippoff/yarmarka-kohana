<?php defined('SYSPATH') or die('No direct script access.');


class Twig_Filters
{
    public static function contacthide($value)
    {
        return Contact::hide($value);
    }
}