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

    public static function check_kupon($kupon, $action, $key = NULL)
    {
        $user =  Auth::instance()->get_user();
        $settings = Kohana::$config->load("acl.settings");
        $cl = Kohana::$config->load("acl.cl.".$action);

        $result = FALSE;

        if (in_array("owner", $cl) AND $kupon->order_id) {
            $order = ORM::factory('Order', $kupon->order_id);
            if ($user)
            {
                if ($order->user_id == $user->id)
                {
                    $result = "by_owner";
                }
            } else {

                $key = ($key) ? (string) $key : Cart::get_key();
                if ($order->key == $key || $key == $kupon->access_key)
                {
                    $result = "by_owner";
                }
            }
        }

        if ( $user and in_array($user->role, $cl) ) {
            $result = "by_role";
        }

        return $result;
    }

    public static function check($action)
    {
        $user =  Auth::instance()->get_user();
        $settings = Kohana::$config->load("acl.settings");
        $cl = Kohana::$config->load("acl.cl.".$action);

        $result = FALSE;

        if (!$user and !in_array("auth", $cl)) {
            $result = FALSE;
        }

        if ( $user and in_array($user->role, $cl) ) {
            $result = "by_role";
        }

        return $result;
    }
}