<?php defined('SYSPATH') or die('No direct script access.');

class Controller_User_Auth extends Controller_Template {

    var $user; // current user
    private $errors = array();

    public function before()
    {
        parent::before();
        $this->use_layout   = FALSE;
        $this->auto_render  = FALSE;

        $this->user = Auth::instance()->get_user();
        $this->domain = new Domain();
        if ($proper_domain = $this->domain->is_domain_incorrect()) {
            HTTP::redirect("http://".$proper_domain, 301);
        }
    }


    public function action_registration()
    {
        $twig = Twig::factory('user/registration');
        $twig->city = $this->domain->get_city();

        $is_post = (HTTP_Request::POST === $this->request->method());
        $post_data = new Obj($this->request->post());
        $error = new Obj();
        $success = FALSE;
        $token = NULL;
        if ($is_post)
        {
            $post_data->login = strtolower(trim($post_data->login));
            $token = $post_data->csrf;
            $validation = ORM::factory('User')
                                ->register_validation((array) $post_data);

            if ( !$validation->check())
            {
                $error = new Obj($validation->errors('validation/auth'));
            } else {
                try {

                    $user_id = ORM::factory('User')
                                    ->registration( $post_data->login, 
                                                    $post_data->pass, 
                                                    $post_data->type );
                } catch (Exception $e)
                {
                    $error->login = "Произошла непредвиденная ошибка. Информация о ошибке отправлена администратору.";

                    Admin::send_error("Ошибка при регистрации пользователя", array(
                            $e->getMessage(), Debug::vars($post_data), $e->getTraceAsString()
                    ));
                }
            }
        } else {
            $token = Security::token();
        }

        $limited_categories = ORM::factory('Category')
                    ->where("max_count_for_user",">",0)
                    ->cached(Date::DAY)
                    ->find_all();

        $twig->token = $token;
        $twig->limited_categories = $limited_categories;
        $twig->captcha = Captcha::instance()->render();
        $twig->success = (isset($user_id));
        $twig->params = $post_data;
        $twig->error = $error;
        $twig->auth = Auth::instance()->get_user();

        $twig->crumbs = array(
            array("title" => "Регистрация"),
        );

        $this->response->body($twig);
    }

    public function action_account_verification()
    {
        $twig = Twig::factory('user/account_verification');
        $twig->city = $this->domain->get_city();
        $twig->user = $this->user; 
        
        $code =$this->request->param("category_path");
        $user = ORM::factory('User')
                        ->where("code","=",trim($code))
                        ->where("is_blocked","=",2)->find();

        if ($user->loaded())
        {
            $user->delete_code();
            $contact = ORM::factory('Contact')
                            ->by_contact_and_type($user->email, Model_Contact_Type::EMAIL)
                            ->find();

            $contact->contact = $user->email;
            $contact->contact_type_id = Model_Contact_Type::EMAIL;
            $contact->verified_user_id = $user->id;
            $contact->show = 1;
            $contact->moderate = 1;
            $contact->save();

            $user_contact = ORM::factory('User_Contact');
            $user_contact->user_id = $user->id;
            $user_contact->contact_id = $contact->id;
            $user_contact->save();

            Auth::instance()->trueforcelogin($user);
            $twig->message = "Добро пожаловать! Вы успешно зарегистрировались";
            $twig->success = TRUE;
            $twig->redirectTo = "http://".Kohana::$config->load("common.main_domain");

        } else {
            $twig->success = FALSE;
            $twig->message = "Ссылка устарела, либо вы уже активировали эту учетную запись ранее. ";
        }

        $twig->crumbs = array(
            array("title" => "Подтверждение регистрации")
        );

        $this->response->body($twig);
    }

    public function action_login()
    {
        $twig = Twig::factory('user/login');
        $twig->city = $this->domain->get_city();
        $is_post = ($_SERVER['REQUEST_METHOD']=='POST');
        $post_data = new Obj($this->request->post());

        $domain = Arr::get($_GET, 'domain', NULL);
        if (!$domain)
            $domain = URL::base('http');
        else
            $domain = "http://".Kohana::$config->load("common.main_domain");

        $return_page = Arr::get($_GET, 'return', "");
        if (!strrpos($return_page,"p://")) {
            $return_page = $domain.$return_page;
        }

        $ulogin_errors = '';
        $ulogin = Ulogin::factory();
        $userdata = $ulogin->userdata();

        if ($userdata)
        {
            if ($userdata['email'] and $userdata['verified_email'] == 1)
            {
                $redirect_to = $return_page;                
                
                $user = ORM::factory('User')
                        ->get_user_by_email($userdata['email'])
                        ->find();

                if ($user->loaded())
                {
                    try 
                    {
                        $user->login_from_social();
                    }
                    catch (Exception $e)
                    {
                        $ulogin_errors = $e->getMessage();
                    }
                }
                else
                {
                    try 
                    {
                        $user_id = ORM::factory('User')
                                    ->registration_from_social($userdata);                      
                    } 
                    catch (Exception $e)
                    {
                        $ulogin_errors = "Произошла непредвиденная ошибка при создании учетной записи. Вы можете пройти регистрацию через стандартную форму на сайте. Приносим свои извинения за доставленное неудобство.";

                        Admin::send_error("Ошибка при регистрации пользователя из сервиса", array(
                                $e->getMessage(), Debug::vars($userdata), $e->getTraceAsString()
                        ));
                    }
                }
                
                if (!$ulogin_errors)
                    $this->redirect($redirect_to);
            }
            else
                $ulogin_errors = 'Нет данных для авторизации с помощью сервиса. Попробуйте воспользоваться другим сервисом или авторизуйтесь через форму сайта.';
        } 

        $token = NULL;
        $error = NULL;
        $success = NULL;
        if ($is_post){
            $token = $post_data->csrf;
            $validation = Validation::factory((array) $post_data)
                    ->rule('csrf', 'not_empty', array(':value', "CSRF"))
                    ->rule('csrf', 'Security::check')
                    ->rule('login', 'not_empty', array(':value', 'Логин (E-mail)') )
                    ->rule('pass', 'not_empty', array(':value', 'Пароль') )
                    ->rule('captcha', 'not_empty', array(':value', ""))
                    ->rule('captcha', 'captcha', array(':value', ""));


            if ( !$validation->check())
            {
                $error = new Obj($validation->errors('validation/auth'));
            } else {
                $auth = Auth::instance();
                try {
                    $auth->login($post_data->login, $post_data->pass, TRUE);
                    
                } 
                    catch (Exception $e)
                {
                    $error = $e->getMessage();

                } 
                if (!$error)
                {
                    $this->redirect($return_page);
                }
            }
        } else {
            $token = Security::token();
            if ($this->user AND $return_page)
            {
                Auth::instance()->trueforcelogin($this->user);

                $this->redirect($return_page);
            }
        }

        $twig->captcha = Captcha::instance()->render();
        $twig->ulogin_errors = $ulogin_errors;
        $twig->ulogin_html = $ulogin->render();
        $twig->token = $token;
        $twig->user = $this->user; 
        $twig->params = $post_data;
        $twig->error = $error;
        $twig->crumbs = array(
            array("title" => "Вход")
        );
        $this->response->body($twig);
    }

    public function action_forgot_password()
    {
        $twig = Twig::factory('user/forgot_password');
        $twig->city = $this->domain->get_city();
        $is_post = ($_SERVER['REQUEST_METHOD']=='POST');
        $post_data = new Obj($this->request->post());

        $token = NULL;
        $error = NULL;
        if ($is_post){
            $token = $post_data->csrf;
            $email = mb_strtolower(trim($post_data->email), 'UTF-8');

            $validation = Validation::factory((array) $post_data)
                    ->rule('csrf', 'not_empty', array(':value', "CSRF"))
                    ->rule('csrf', 'Security::check')
                    ->rule('email', 'not_empty', array(':value', 'E-mail') )
                    ->rule('captcha', 'not_empty', array(':value', ""))
                    ->rule('captcha', 'captcha', array(':value', ""));

            if ( !$validation->check())
            {
                $error = new Obj($validation->errors('validation/auth'));
            } else {
echo 111;
                $user = ORM::factory('User')
                            ->get_user_by_email($email)
                            ->find();
                if (!$user->loaded())
                {
                    $error = "Этот email не зарегистрирован";
                } elseif ($user->is_blocked == 1)
                {
                    $error = "Этот email заблокирован за нарушение правил сайта";
                }
                else 
                {
                    $code = $user->create_forgot_password_code();
                    $url  = URL::base('http')."user/forgot_password_link/".$code;

                    $last_object = ORM::factory('Object')->where('author','=',$user->id)->order_by('id','desc')->find();

                    $params = array(
                        'url' => $url,
                        'domain' => ($last_object->loaded()) ? $last_object->city_id : FALSE
                    );

                    Email_Send::factory('forgot_password')
                                ->to( $user->email )
                                ->set_params($params)
                                ->set_utm_campaign('forgot_password')
                                ->send();

                    $this->redirect(URL::base('http').'user/forgot_password?success=1');
                }
            }
        } else {
            $token = Security::token();
        }

        $twig->status = NULL;
        
        $success = $this->request->query('success');
        $failure = $this->request->query('failure');
        if ($success)
            $twig->status = "success";
        if ($failure)
            $twig->status = "failure";

        $twig->token = $token;
        $twig->captcha = Captcha::instance()->render();
        $twig->error = $error;
        $twig->params = $post_data;
        $twig->user = $this->user; 
        $twig->crumbs = array(
            array("title" => "Восстановление пароля")
        );
        $this->response->body($twig);
    }

    public function action_forgot_password_link()
    {
        $code = trim($this->request->param('category_path'));
        if (!$code)
            throw new HTTP_Exception_404;

        $user = ORM::factory('User')
                    ->get_user_by_code(trim($code))
                    ->find();
        if (!$user->loaded())
            $this->redirect(URL::base('http').'user/forgot_password?failure=1');
        else 
        {
            $user->delete_code();
            Auth::instance()->trueforcelogin($user);
            $this->redirect(URL::base('http').'user/password');
        }

    }

    public function action_logout()
    {
        $domain = Arr::get($_GET, 'domain', NULL);
        if (!$domain)
            $domain = URL::base('http');
        else
            $domain = "http://".Kohana::$config->load("common.main_domain");

        $return_page = Arr::get($_GET, 'return', "");
        if (!strrpos($return_page,"p://")) {
            $return_page = $domain.$return_page;
        }

        setcookie('user_id', '', time()-1, '/', Region::get_cookie_domain());
        setcookie('authautologin_s', '', time()-1, '/', Region::get_cookie_domain());

        Auth::instance()->logout(TRUE, TRUE);

        $this->redirect(($return_page) ? $return_page : '/');
    }
}