<?php defined('SYSPATH') or die('No direct script access.');

class Searchpage
{

    public static function factory($service = 'Default')
    {   
        $service = "_".$service;
        // Set class name
        $service = 'Searchpage'.$service;

        return new $service();
    }

}