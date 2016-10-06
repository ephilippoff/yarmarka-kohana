<?php defined('SYSPATH') or die('No direct script access.');

/**
 * use Email_Send::factory('addedit')->to('almaznv@yandex.ru')->set_params(array(*))->send('Создали объявление');
 */
class Email_Send  {

    private $_template_name = '';
    private $_to = '';
    private $_user = NULL;
    private $_params = array();
    private $_utm_campaign = FALSE;

    private $_ref_params = array(
                'utm_source' => 'yarmarka_mail',
                'utm_medium' => 'email',
                'utm_campaign' => 'unknown',
                'utm_content' => 'unknown'
            );

    private $_notices = array(
        'addedit',
        'massload_report',
        'object_expiration',
        'object_to_archive'
    );

    private $_withnews = array(
        'addedit',
        'object_expiration',
        'object_to_archive'
    );

    private $_news = array();

    public static function factory($template_name)
    {
        
        $class = 'Email_Send';

        return new $class($template_name);
    }

    public function __construct($template_name)
    {
        $this->_template_name = $template_name;
    }

    public function to($email, $user = FALSE)
    {
        $this->_to = $email;

        if ($user) {
            $this->_user = $user;
        } else {
            $this->_user = Auth::instance()->get_user();
        }

        return $this;
    }

    public function set_ref_params($params)
    {
        $this->_ref_params = $params;

        return $this;
    }

    public function set_params($params)
    {
        $this->_params = $params;

        return $this;
    }

    public function set_utm_campaign($name)
    {
        $this->_utm_campaign = $name;

        return $this;
    }

    private function set_news()
    {
        $params = $this->_params;

        if (!isset($params['domain']) OR !is_numeric($params['domain'])) return;

        

         $search_query = Search::searchquery(
            array(
                "expiration" => TRUE,
                "premium" => TRUE,
                "active" => TRUE,
                "published" =>TRUE,
                "city_published" => $params['domain'],
                "category_seo_name" => "novosti"
            ),
            array("limit" => 3, "page" => 1)
        );
        
        return Search::getresult($search_query->execute()->as_array());
    }

    private function set_promo_objects()
    {
        $params = $this->_params;

        if (!isset($params['domain']) OR !is_numeric($params['domain'])) return;


        $ids = ORM::factory('Object_Service_Email')->get_actual($params['domain']);
  
        $search_query = Search::searchquery(
            array(
                "active" => TRUE,
                "published" =>TRUE,
                "id" => $ids
            ),
            array("limit" => 15, "page" => 1)
        );
        
        $result = Search::getresult($search_query->execute()->as_array());
        shuffle($result);

  
        return $result;
    }

    

    public function send($subj = FALSE, $msg = FALSE)
    {
        $params = $this->_params;

        if ($this->_user AND in_array($this->_template_name, $this->_notices)) {
            
            $setting_notices = ORM::factory('User_Settings')
                            ->get_by_name($this->_user->id, "email_notices_off")
                            ->find();
            if ($setting_notices->loaded()) {
                return;
            }

        }

        if ($this->_user AND in_array($this->_template_name, $this->_news)) {
            
            $setting_notices = ORM::factory('User_Settings')
                            ->get_by_name($this->_user->id, "email_news_off")
                            ->find();
            if ($setting_notices->loaded()) {
                return;
            }
            
        }

        if (!is_array($this->_to)) {
            $params['user_email'] = $this->_to;
        }

        if ($this->_utm_campaign) {
            $this->_ref_params['utm_campaign'] = $this->_utm_campaign;
        }

        $params['ref_params'] = $this->_ref_params;

        $params['last_news'] = FALSE;
        $params['promo_objects'] = FALSE;
        if (in_array($this->_template_name, $this->_withnews)) {
            $params['last_news'] = $this->set_news();
            $params['promo_objects'] = $this->set_promo_objects();
        }

        if (isset($params['domain']) AND $params['domain'] AND is_numeric($params['domain'])) {
            $city = ORM::factory('City')->where('id','=',$params['domain'])->cached(Date::WEEK)->find();
            if ($city->loaded() AND $city->id <> 1) {
                $params['domain'] = sprintf('http://%s.%s', $city->seo_name, Kohana::$config->load('common.main_domain'));
            } else {
                $params['domain'] = FALSE;
            }

        }

        if (!$subj) {
            $subj  = $this->{'get_subj_'.$this->_template_name}( $params );
        }
        if (!$msg) {
            $msg  = $this->{'get_template_'.$this->_template_name}( $params );
        }
        

        return Email::send($this->_to, Kohana::$config->load('email.default_from'), $subj, $msg);
    }

    public function get_subj_accept_request_to_link_company($params)
    {
        return $params['accept_decline']
                    ? "Привязка к компании ".$params['request_user']->org_name." подтверждена"
                    : "Привязка к компании ".$params['request_user']->org_name." НЕ подтверждена";
    }

    public function get_subj_decline_orginfo($params)
    {
        return 'Модератор отклонил загруженный ИНН';
    }

    public function get_subj_request_to_link_company($params)
    {
        return 'Запрос на разрешение подачи объявлений от лица вашей компании';
    }

    public function get_template_accept_request_to_link_company($params)
    {
        return Twig::factory('emails/user_manage/accept_request_to_link_company', $params)->render();
    }
    public function get_template_decline_orginfo($params)
    {
        return Twig::factory('emails/user_manage/decline_orginfo', $params)->render();
    }
    public function get_template_request_to_link_company($params)
    {
        return Twig::factory('emails/user_manage/request_to_link_company', $params)->render();
    }

    public function get_subj_response_for_object($params)
    {
        return 'Вам было отправлено сообщение по объявлению';
    }

    public function get_template_response_for_object($params)
    {
        return Twig::factory('emails/response_for_object', $params)->render();
    }

    public function get_subj_addedit($params)
    {
        return $params['is_edit']
                    ? 'Объявление "'.$params['object']->title.'" успешно отредактировано'
                    : 'Объявление "'.$params['object']->title.'" успешно добавлено на сайт';
    }

    public function get_subj_block_contact($params)
    {
        return 'Сообщение от модератора';
    }

    public function get_subj_decline_contact($params)
    {
        return 'Сообщение от модератора';
    }

    public function get_subj_contact_verification_code($params)
    {
        return 'Подтверждение E-mail';
    }

    public function get_subj_forgot_password($params)
    {
        return 'Восстановление пароля';
    }

    public function get_subj_moderate_object($params)
    {
        return 'Сообщение от модератора';
    }

    public function get_subj_massload_report($params)
    {
        return "Отчет по загрузке объявлений";
    }

    public function get_subj_object_expiration($params)
    {
        return 'Истекает срок публикации ваших объявлений';
    }

    public function get_subj_object_to_archive($params)
    {
        return 'Ваши объявления перемещены в архив';
    }

    public function get_subj_payment_success($params)
    {
        return "Потверждение оплаты. Заказ №".$params['order']->id;
    }

    public function get_subj_register_data($params)
    {
        return 'Для вас создана учетная запись';
    }

    public function get_subj_register_success($params)
    {
        return 'Пожалуйста, подтвердите свою регистрацию';
    }

    public function get_template_addedit($params)
    {
        return Twig::factory('emails/addedit', $params)->render();
    }

    public function get_template_block_contact($params)
    {
        return Twig::factory('emails/block_contact', $params)->render();
    }

    public function get_template_decline_contact($params)
    {
        return Twig::factory('emails/decline_contact', $params)->render();
    }

    public function get_template_contact_verification_code($params)
    {
        return Twig::factory('emails/contact_verification_code', $params)->render();
    }

    public function get_template_forgot_password($params)
    {
        return Twig::factory('emails/forgot_password', $params)->render();
    }

    public function get_template_moderate_object($params)
    {
        return Twig::factory('emails/moderate_object', $params)->render();
    }

    public function get_template_massload_report($params)
    {
        return  Twig::factory('emails/massload_report', $params)->render();
    }

    public function get_template_object_expiration($params)
    {
        return Twig::factory('emails/object_expiration', $params)->render();
    }

    public function get_template_object_to_archive($params)
    {
        return Twig::factory('emails/object_to_archive', $params)->render();
    }

    public function get_template_payment_success($params)
    {
        return Twig::factory('emails/payment_success2', $params)->render();
    }

    public function get_template_register_data($params)
    {
        return Twig::factory('emails/register_data', $params)->render();
    }

    public function get_template_register_success($params)
    {
        return Twig::factory('emails/register_success', $params)->render();
    }

}
