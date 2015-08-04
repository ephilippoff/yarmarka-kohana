<?php defined('SYSPATH') OR die('No direct script access.');

class Acl
{

    public static function check_object($object, $action)
    {
        $user =  Auth::instance()->get_user();
        $settings = Kohana::$config->load("acl.settings");
        $cl = Kohana::$config->load("acl.cl.".$action);

        

        $result = FALSE;

        if (!$user and !in_array("auth", $cl)) {
            $result = FALSE;
        }

        if ($user and in_array("owner", $cl) and $user->id == $object->author) {
            $result = "by_owner";
        }

        if ( $user and in_array($user->role, $cl) ) {
            $result = "by_role";
        }

        return $result;
    }
}