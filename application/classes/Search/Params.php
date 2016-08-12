<?php defined('SYSPATH') OR die('No direct script access.');

class Search_Params
{

    public function __construct(Array $params = array())
    {

        foreach ( $params as  $key => $param) {
            $this->{$key} = $param;
        }

        return $this;
    }

    

    function __get($key)
    {
        if (property_exists($this, $key))
            return $key;
        else
            return NULL;
    }


}