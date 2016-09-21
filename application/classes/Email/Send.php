<?php defined('SYSPATH') or die('No direct script access.');

/**
 * use Email_Send::factory('addedit')->to('almaznv@yandex.ru')->set_params(array(*))->send('Создали объявление');
 */
class Email_Send  {

    private $_template_name = '';
    private $_to = '';
    private $_params = '';

    public static function factory($template_name)
    {
        
        // Add the model prefix
        $class = 'Email_Send';

        return new $class($template_name);
    }

    public function __construct($template_name)
    {
        $this->_template_name = $template_name;
    }

    public function to($email)
    {
        $this->_to = $email;

        return $this;
    }

    public function set_params($params)
    {
        $this->_params = $params;

        return $this;
    }

    public function send($title)
    {


        $msg  = call_user_func_array("Email_Send::{$this->_template_name}", $this->_params);

        return Email::send($this->_to, Kohana::$config->load('email.default_from'), $title, $msg);
    }

    public static function addedit(
        $is_edit,
        $object,
        $domain = FALSE
    )
    {
        $params = array(
            'is_edit' => $is_edit,
            'object' => $object,
            'domain' => $domain
        );

        return Twig::factory('emails/addedit', $params)->render();
    }

    public static function block_contact(
        $phone,
        $objects,
        $domain = FALSE
    )
    {

        $params = array(
            'phone' => $phone,
            'objects' => $objects,
            'domain' => $domain
        );

        return Twig::factory('emails/block_contact', $params)->render();
    }

    public static function decline_contact(
        $phone,
        $objects,
        $domain = FALSE
    )
    {

        $params = array(
            'phone' => $phone,
            'objects' => $objects,
            'domain' => $domain
        );

        return Twig::factory('emails/decline_contact', $params)->render();
    }

    public static function contact_verification_code(
        $contact,
        $code,
        $domain = FALSE
    )
    {
        $params = array(
            'contact' => $contact, 
            'code' => $code,
            'domain' => $domain
        );

        return Twig::factory('emails/contact_verification_code', $params)->render();
    }

    public static function forgot_password(
        $url,
        $domain = FALSE
    )
    {
        $params = array(
            'url' => $url,
            'domain' => $domain
        );

        return Twig::factory('emails/forgot_password', $params)->render();
    }

    public static function moderate_object(
        $actions_for_user_negative,
        $actions_for_user_positive,
        $domain = FALSE
    )
    {
        $params = array(
            'actions_negative' => $actions_for_user_negative,
            'actions_positive' => $actions_for_user_positive,
            'domain' => $domain
        );

        return Twig::factory('emails/moderate_object', $params)->render();
    }

    public static function massload_report(
        $objectload,
        $common_stat,
        $category_stat,
        $org_name,
        $domain = FALSE
    )
    {
        $params = array(
            'objectload' => $objectload, 
            'common_stat' => $common_stat, 
            'category_stat' => $category_stat,
            'org_name' => $org_name,
            'logo' => 'http://yarmarka.biz/images/logo.png'
        );

        return  Twig::factory('emails/massload_report', $params)->render();
    }

    public static function object_expiration(
        $objects,
        $domain = FALSE
    )
    {
        
        $params = array(
            'objects' => $objects,
            'domain' => $domain
        );

        return Twig::factory('emails/object_expiration', $params)->render();
    }

    public static function object_to_archive(
        $objects,
        $domain = FALSE
    )
    {
        
        $params = array(
            'objects' => $objects,
            'domain' => $domain
        );

        return Twig::factory('emails/object_to_archive', $params)->render();
    }

    public static function payment_success($order, $orderItems, $domain = FALSE)
    {
        $params = array(
            'order' => $order,
            'orderItems' => $orderItems,
            'domain' => $domain
        );

        return Twig::factory('emails/payment_success2', $params)->render();
    }

    public static function register_data(
        $login,
        $password,
        $domain = FALSE
    )
    {
        
        $params = array(
            'login' => $login,
            'passw' => $password,
            'domain' => $domain
        );

        return Twig::factory('emails/register_data', $params)->render();
    }

    public static function register_success($code, $domain = FALSE)
    {
        $params = array(
                    'code' => $code,
                    'domain' => $domain
                );

        return Twig::factory('emails/register_success', $params)->render();
    }

}
