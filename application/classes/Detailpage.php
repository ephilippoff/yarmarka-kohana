<?php defined('SYSPATH') or die('No direct script access.');

class Detailpage
{

    public static function factory($service = 'Default', $object)
    {   
        $service = "_".$service;
        // Set class name
        $service = 'Detailpage'.$service;

        return new $service($object);
    }

}