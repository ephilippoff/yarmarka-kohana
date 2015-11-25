<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_User extends Model_Auth_User {

	protected $_table_name = 'user';

	/**
	 * A user has many tokens and roles
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(	
		'user_tokens'	=> array('model' => 'User_Token'),
		'objects'		=> array('foreign_key' => 'author'),
		'access'		=> array('model' => 'Access'),
		'invoices'		=> array(),
		'subscriptions'	=> array(),
		'user_messages' => array('model' => 'User_Messages', 'foreign_key' => 'user_id'),
		'contacts'		=> array('model' => 'Contact', 'through' => 'user_contacts'),
		'link_requests' => array('model' => 'User_Link_Request', 'foreign_key' => 'linked_user_id'),
		'users'			=> array('model' => 'User', 'foreign_key' => 'linked_to_user'),		
		'units' 		=> array('model' => 'User_Units', 'foreign_key' => 'user_id'),
		'business_types'=> array('model' => 'Business_Type', 'through' => 'user_business'),
	);

	protected $_belongs_to = array(
		'user_role' 	=> array('model' => 'Role', 'foreign_key' => 'role'),
		'user_city'		=> array('model' => 'City', 'foreign_key' => 'city_id'),
		'user_type'		=> array('model' => 'User_Types', 'foreign_key' => 'org_type'),
		'location' 		=> array('model' => 'Location', 'foreign_key' => 'location_id'),
		'linked_to'		=> array('model' => 'User', 'foreign_key' => 'linked_to_user'),
	);

	/**
	 * Rules for the user model. Because the password is _always_ a hash
	 * when it's set,you need to run an additional not_empty rule in your controller
	 * to make sure you didn't hash an empty string. The password rules
	 * should be enforced outside the model or with a model helper method.
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return array(
			'passw' => array(
				array('not_empty'),
				array('min_length', array(':value', 6)),
			),
			'email' => array(
				array('email'),
				array(array($this, 'unique'), array('email', ':value')),
				array(array($this, 'check_domain'), array(':value')),
			),
			'role' => array(
				array('not_empty'),
			),
		);
	}

	/**
	 * Filters to run when data is set in this model. The password filter
	 * automatically hashes the password when it's set in the model.
	 *
	 * @return array Filters
	 */
	public function filters()
	{
		return array(
			'passw' => array(
				array(array(Auth::instance(), 'hash'))
			),
			'phone' => array(
				array(array($this, 'trigger_save_phone'), array(':value')),
			),
		);
	}

	/**
	 * Labels for fields in this model
	 *
	 * @return array Labels
	 */
	public function labels()
	{
		return array(
			'username'         => 'username',
			'email'            => 'email address',
			'password'         => 'password',
		);
	}

	public function unique_key($value)
	{
		return Valid::email($value) ? 'email' : 'login';
	}

	public function complete_login()
	{
		if ($this->_loaded)
		{
			$access = ORM::factory('Access');
			$access->user_id	= $this->id;
			$access->ip			= Request::$client_ip;
			$access->save();
		}
	}

	public function save(Validation $validation = NULL)
	{
		$this->_table_name = 'user';
		parent::save($validation);
	}

	public function update(Validation $validation = NULL)
	{
		$this->_table_name = 'user';
		parent::update($validation);
	}

	public function delete()
	{
		$this->_table_name = 'user';
		parent::delete();
	}

	public function get_hash()
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		return sha1($this->login.$this->passw.'secret_##42');
	}

	/**
	 * User contacts
	 *
	 * @param  int/array $type
	 * @return object Database_Result object
	 */
	public function get_contacts($type = NULL)
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		$object_contact = ORM::factory('Contact');

		$query = $object_contact->select('contact_type.name')
			->with('contact_type')
			->where_user_id($this->id)
			->order_by('id');

		if ($type)
		{
			if (is_array($type))
			{
				$query->where('contact_type_id', 'IN', $type);
			}
			else
			{
				$query->where('contact_type_id', '=', intval($type));
			}
		}


		return $query->find_all();
	}

	public function add_contact($contact_type_id, $contact_str, $is_moderate = FALSE, $skip_linking = FALSE)
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		$contact = ORM::factory('Contact');
		$contact->contact_type_id	= intval($contact_type_id);
		$contact->contact			= trim($contact_str);
		$contact->moderate 			= intval($is_moderate);
		$contact->show 				= 1;
		$contact = $contact->create();

		//Привязывать контакт к пользователю или нет
		if (!$skip_linking)//Если пропуск отключен
			if ( ! $contact->has('users', $this->id))
			{
				$contact->add('users', $this->id);
			}

		return $contact;
	}

	public function add_verified_contact($contact_type_id, $contact_str, $is_moderate = FALSE, $skip_linking = FALSE)
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		if (Model_Contact_Type::is_phone($contact_type_id))
		{
			$contact_clear = Text::clear_phone_number($contact_str);
		}
		else
		{
			$contact_clear = trim($contact_str);
		}

		// create contact if not exists
		$contact = $this->add_contact($contact_type_id, $contact_str, $is_moderate, $skip_linking);

		// remove contact from other users
		DB::delete('user_contacts')
			->where('contact_id', '=', $contact->id)
			->where('user_id', '!=', $this->id)
			->execute();

		// unpublish objects with that contact
		/* http://yarmarka.myjetbrains.com/youtrack/issue/yarmarka-295
		if ($this->id != $contact->verified_user_id)
		{
			$objects = ORM::factory('Object')
				->join('object_contacts')
				->on('object.id', '=', 'object_contacts.object_id')
				->where('contact_id', '=', $contact->id)
				->find_all();

			foreach ($objects as $object)
			{
				$object->is_published = 0;
				$object->save();

				$object->remove('contacts', $contact);
			}
		}
		*/

		if (!$is_moderate) {
			// set contact verified for current user
			$contact->verified_user_id = $this->id;
		}

		$contact->save();

		return $contact;
	}

	public function delete_contact($contact_id)
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		$contact = ORM::factory('Contact');
		$contact->where_user_id($this->id)
			->where('contact.id', '=', intval($contact_id))
			->find();
		if ($contact->loaded())
		{
			$this->remove('contacts', $contact);
			return TRUE;
		}

		return FALSE;
	}

	public function get_user_name()
	{
		if (trim($this->fullname))
			return $this->fullname;

		if (trim($this->login))
			return $this->login;

		return $this->email;
	}

	public function can_edit_object($object_id)
	{
		$object = ORM::factory('Object', $object_id);
		if ( ! $object->loaded() OR ! $this->loaded())
		{
			return FALSE;
		}

		//$is_white_ip 	= ORM::factory('Ipwhite')->get_by_ip(Request::$client_ip)->loaded();
		$is_author 		= ($this->id === $object->author);
		$is_admin 		= ($this->role == 1 OR $this->role == 3);
		$is_company 	= ($this->id === $object->author_company_id);

		return ($is_author OR $is_admin OR $is_company);
	}

	public function check_domain($email)
	{
		if ( ! $email)
		{
			return TRUE;
		}
		
		$disallowed_domains = Kohana::$config->load('common.disallowed_email_domains');
		list($email_name, $domain) = explode('@', $email);

		return ! in_array($domain, $disallowed_domains);
	}

	public function check_ip()
	{
		$user_by_ip 	= ORM::factory('User')->where('ip_addr', '=', Request::$client_ip)->find();
		$is_white_ip 	= ORM::factory('Ipwhite')->get_by_ip(Request::$client_ip)->loaded();

		if ($user_by_ip->loaded() AND ! $is_white_ip AND ceil((time() - strtotime($user_by_ip->regdate))/Date::HOUR) <= 12)
		{
			return FALSE;
		}

		return TRUE;
	}
	
	public function getAllUnits()
	{
		return $this->units->find_all()->as_array();
	}

	public function count_company_objects($company_id)
	{
		return $this->objects->where('author_company_id', '=', $company_id)
			->count_all();
	}

	public function trigger_save_email($email)
	{
		if ($email AND Valid::email($email))
		{
			$contact = ORM::factory('Contact');
			$contact->contact 			= trim(mb_strtolower($email));
			$contact->contact_type_id 	= Model_Contact_Type::EMAIL;
			$contact->create();
			if ($contact->id)
			{
				$contact->verified_user_id 	= $this->id;
				$contact->save();
			}
		}

		return $email;
	}

	public function trigger_save_phone($phone)
	{
		if ($phone)
		{
			$contact = ORM::factory('Contact');
			$contact->contact 			= trim($phone);
			$contact->contact_type_id 	= Model_Contact_Type::MOBILE;
			$contact->create();
		}

		return $phone;
	}

	public function check_max_user_objects($user, $category, $object_id)
	{
		if ( ! is_object($user))
		{
			$user = ORM::factory('User', $user);
		}

		if ( ! is_object($category))
		{
			$category = ORM::factory('Category', $category);
		}

		$objects = $user->objects->where('category', '=', $category_id)->where('is_published', '=', 1)->where('active', '=', 1);
		if ($object_id)
		{
			$objects->where('id', '!=', $object_id);
		}

		if ($user->org_type == 1 AND $category->max_count_for_user AND $objects->count_all() >= $category->max_count_for_user)
		{
			return FALSE;
		}

		return TRUE;
	}

	public function get_user_by_email($email)
	{
		/*return $this->join("contacts","left")
					->on("contacts.verified_user_id","=","user.id")
			  	->where(DB::expr("w_lower(contacts.contact_clear)"),"=",$email);*/
		$email = trim(strtolower($email));
		return $this->where(DB::expr('w_lower(email)'), '=', $email);

	}

	public function create_forgot_password_code($code = NULL)
	{
		

		if (!$this->loaded() OR !$this->email)
			return FALSE;

		if ($this->is_blocked == 1)
			return FALSE;

		if (!$code)
			$code = User::generate_code($this->email);

		$this->code = $code;
		$this->save();

		return $code;
	}

	public function get_user_by_code($code)
	{
		return $this->where("code","=",trim($code));
	}

	public function delete_code()
	{
		if (!$this->loaded())
			return;

		$this->code = "";
		$this->is_blocked = 0;
		$this->save();

	}

	public function register_validation(Array $data)
	{
		return Validation::factory($data)
					->rule('csrf', 'not_empty', array(':value', "CSRF"))
					->rule('csrf', 'Security::check')
					->rule('login', 'not_empty', array(':value', "Email"))
					->rule('login', 'email', array(':value', "Email"))
					->rule('login', 'login_exist', array(':value', @$data['login']))
					->rule('pass', 'not_empty', array(':value', "Пароль"))
					->rule('pass', 'min_length', array(':value', 6, "Пароль"))
					->rule('pass2', 'not_empty', array(':value', "Пароль (повторно)"))
					->rule('pass2', 'matches', array($data, "pass", "pass2"))
					->rule('type', 'not_empty', array(':value', "Статус"))
					->rule('type', 'valid_org_type', array(':value', "Статус"))
					->rule('captcha', 'not_empty', array(':value', ""))
					->rule('captcha', 'captcha', array(':value', ""));
	}

	public function registration($email, $password, $type = 1)
	{
		$email = strtolower(trim($email));
		$user = ORM::factory('User')
							->get_user_by_email($email)
							->find();
		if ($user->loaded())
			return FALSE;

		$this->login = $email;
		$this->email = $email;
		$this->passw = $password;
		$this->role = 2;
		$this->code = self::generate_code($email);
		$this->is_blocked = 2;
		$this->ip_addr = $_SERVER["REMOTE_ADDR"];
		$this->org_type = $type;
		$this->save();

		$this->trigger_save_email($email);

		$this->send_register_success();
		
		return $this->id;

	}
	/**
	 * [send_register_success send email about success registration and link to complete registration]
	 * @return [void]
	 */
	public function send_register_success()
	{
		if (!$this->loaded())
			return;

		$msg = View::factory('emails/register_success', array('activationCode' => $this->code));
		Email::send($this->email, Kohana::$config->load('email.default_from'), 'Подтверждение регистрации на Ярмарке', $msg);
	}

	private static function generate_code($str)
	{
		return sha1($str.microtime());
	}

	private static function password_hash($password)
	{
		return sha1($password . md5($password . 'secret_salted_hash##!&&1'));

	}

	/**
	 * [reset_orgtype сброс типа учетной записи на Частное лицо]
	 * @param  boolean $soft [мягкая смена, если true то объявыления не снимаются]
	 * @return [type]        [void]
	 */
	public function reset_orgtype($soft = FALSE)
	{
		if (!$this->loaded())
			return;
				
		$db = Database::instance();
		try
		{
			$db->begin();
			
			//Сбрасываем тип на "Частное лицо"
			$this->org_type = 1;
			$this->save();

			//Отвязываем сотрудников если были
			$this->unlink_users();

			if (!$soft)
			{	
				//находим категории с ограничениями
				$categories = ORM::factory('Category')
								->where("max_count_for_user",">",0)
								->find_all();

				foreach ($categories as $category) {

					//снимаем объявления в категория с ограничениями
					$query = DB::select("id")
									->from("object")
									->where("author","=",$this->id)
									->where("active","=",1)
									->where("category","=",$category->id)
									->where("is_published","=",1)
									->order_by("date_created","desc")
									->offset($category->max_count_for_user);

					ORM::factory('Object')
							->where("id","IN",$query)
							->set("is_published", 0)
							->update_all();
					
				}
			}
			$db->commit();
		}
		catch(Exception $e)
		{
			$db->rollback();
			return $e->getMessage();
		}

	}
	/**
	 * [reset_to_company Меняем тип учетной записи на "Компания"]
	 * @return [type] [description]
	 */
	public function reset_to_company()
	{
		if (!$this->loaded())
			return;

		ORM::factory('User_Link_Request')
			->delete_requests($this->id);

		$this->linked_to_user = NULL;
		$this->org_type = 2;
		$this->save();	
	}

	public function count_employers()
	{
		return ORM::factory('User')
					->where("linked_to_user","=",$this->id)
					->count_all();
	}

	public function reset_parent_user()
	{
		if (!$this->loaded())
			return;

		$this->linked_to_user = NULL;
		$this->save();	
	}

	public function is_valid_orginfo()
	{
		if (!$this->loaded())	
			return TRUE;

		if ($this->org_type <> 2)
			return TRUE;

		if ($this->org_moderate === 1)
			return TRUE;

		return FALSE;
	}

	public function is_expired_date_validation()
	{
		if (!$this->loaded())
			return;

		if ($this->org_moderate === 0)
			return FALSE;
		
		$date_new_registration = Kohana::$config->load("common.date_new_registration");
		if (strtotime($this->regdate) > strtotime($date_new_registration))
			return TRUE;
		
		$date 		  = new DateTime();
		$date_expired = ORM::factory('User_Settings')
								->where("user_id","=",$this->id)
								->where("name","=","date-expired")
								->where("type","=","orginfo")
								->find();

		if (!$date_expired->loaded())
		{
			$date_expired->user_id = $this->id;
			$date_expired->type = "orginfo";
			$date_expired->name = "date-expired";			
			$date_expired->value  = $date->add(date_interval_create_from_date_string('14 days'))->format('Y-m-d H:i:s');
			$date_expired->save();
		}
		elseif ($date_expired->loaded() AND strtotime($date->format('Y-m-d H:i:s')) >= strtotime($date_expired->value))
		{
			return TRUE;
		}

		return FALSE;
	}

	public function link_user($user_id, $force_for_company = FALSE)
	{	
		if (!$this->loaded())
		{
			return "Пользователь с таким email адресом не зарегистрирован";
		} elseif ($this->loaded() AND $this->linked_to_user == $user_id)
		{
			return "Учетная запись этого пользователя уже привязана к вашей компании";
		} elseif ($this->loaded() AND $this->linked_to_user)
		{
			return "Учетная запись этого пользователя уже привязана к другой компании";				
		} elseif ($this->loaded() AND $this->org_type <> 1 AND !$force_for_company)
		{
			return "Учетная запись этого пользователя 'Компания'. Вы не можете его добавить";
		} 

		$this->linked_to_user = $user_id;
		$this->save();

		$this->reset_orgtype($force_for_company);

		return;
	}

	public function unlink_user($user_id, $linked_user_id)
	{
		return ORM::factory('User')
				->where("linked_to_user","=",$user_id)
				->where("id","=",$linked_user_id)
				->set("linked_to_user", DB::expr("NULL"))
				->update_all();
	}

	public function unlink_users()
	{
		return $this->where("linked_to_user","=",$this->id)
					->set("linked_to_user", DB::expr("NULL"))
					->update_all();
	}

	public function ban($reason = NULL)
	{
		if (!$this->loaded())
			return;
		
		$this->is_blocked = 1;
		$this->block_reason = $reason;
		$this->save();

		ORM::factory('Object')
				->where("author","=",$this->id)
				->set("is_published", 0)
				->update_all();
	}


	public function is_excess_max_count_objects_in_category($category, $object_id_exclusion = 0)
	{
		if (!$this->loaded())
			return -1;

		$category_id = $category->id;
		$category_limit = $category->max_count_for_user;
		$individual_limit = ORM::factory('Category')
							->get_individual_limited($this->id, $category->id);

		if (count($individual_limit)>0)
			$individual_limit = $individual_limit[0]["individual_limit"];
		$limit = ($individual_limit) ? $individual_limit : (($category_limit) ? $category_limit : 50000);
		$count = $this->objects
					->where('category', '=', $category_id)
					->where('is_published', '=', 1)
					->where('active', '=', 1)
					->where('id', '<>', (int) $object_id_exclusion)
					->count_all();

		return ($count >= $limit);
	}
	
	public function register_validation_short(Array $data)
	{
		return Validation::factory($data)
					->rule('login', 'not_empty', array(':value', "Email"))
					->rule('login', 'email', array(':value', "Email"))
					->rule('login', 'login_exist', array(':value', @$data['login']))
					->rule('pass', 'not_empty', array(':value', "Пароль"))
					->rule('pass', 'min_length', array(':value', 6, "Пароль"))
					->rule('pass2', 'not_empty', array(':value', "Пароль (повторно)"))
					->rule('pass2', 'matches', array($data, "pass", "pass2"))
					->rule('org_name', 'not_empty', array(':value', "Название"));
	}

	public function registration_short($email, $password, $org_name, $type = 2)
	{
		$email = strtolower(trim($email));
		$user = ORM::factory('User')
							->get_user_by_email($email)
							->find();
		if ($user->loaded())
			return FALSE;

		$this->login = $email;
		$this->email = $email;
		$this->passw = $password;
		$this->org_name = $org_name;
		$this->role = 2;
		$this->code = '';
		$this->ip_addr = $_SERVER["REMOTE_ADDR"];
		$this->org_type = $type;
		
		$this->save();

		$this->trigger_save_email($email);
		
		return $this->id;

	}

	public function get_good_companies($city_id = NULL) {

		// $with_good_info = DB::select("user_id")
		// 						->from("user_settings")
		// 						->where("type","=","orginfo")
		// 						->where("name","=","moderate")
		// 						->where("value","=","moderate");

		$companies = ORM::factory('User')
					// ->where("id","IN",$with_good_info)
					->where("is_blocked","=",0)
					->where("org_type","=",2)
					->where("estimate","=","100_perfect");

		return $companies;
	}

	public function get_compile() {
		if (!$this->loaded()) return;
		return $this->select_array(array(
				"id","email","fullname","city",
				"org_name","org_full_name","org_post_address",
				"org_phone","org_type","role","regdate",
				"filename", "about", "last_visit_date", "org_moderate"
		));
	}
}

/* End of file User.php */
/* Location: ./application/classes/Model/User.php */