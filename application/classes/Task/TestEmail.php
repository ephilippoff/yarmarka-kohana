<?php defined('SYSPATH') or die('No direct script access.');


class Task_TestEmail extends Minion_Task
{

	private static $to = 'almaznv@yandex.ru';

	protected $_options = array(
	);

	protected function _execute(array $params)
	{
		$order = ORM::factory('Order')->where('id','=',1517)->find()->get_row_as_obj();

		$order_tems = ORM::factory('Order_Item')->where("order_id","=", $order->id)->order_by("id");

		$orderItems = array();
		$sum = Model_Order::each_item($order_tems, function($service, $item, $model_item) use (&$orderItems) {
			$orderItems[] = $item;
			return $item;
		});

		
		
		

		$objects = ORM::factory('Object')->order_by('id','desc')->limit(2)->find_all();

		$object = ORM::factory('Object', 4028377);

		$contact = ORM::factory('Contact')->order_by('id','desc')->find();

		

		$objectload_id = 3739;
		$objectload = ORM::factory('Objectload', $objectload_id);
		$user = ORM::factory('User',$objectload->user_id);

		$massload_email = ORM::factory('User_Settings')
								->where("name","=","massload_email")
								->where("user_id","=",$objectload->user_id)
								->find_all();
		
		if (!$objectload_id OR !$massload_email OR !$objectload->loaded())
			return;

		$common_stat = new Obj($objectload->get_statistic());
		$category_stat = array();

		$of = ORM::factory('Objectload_Files')
				->where("objectload_id", "=", $objectload_id)
				->find_all();

		foreach ($of as $file)
		{	
			$cfg = Kohana::$config->load('massload/bycategory.'.$file->category);
			$category_stat[$cfg["name"]] = array(
					"id" => $file->id,
					"title" => $cfg["name"],
					"stat" => new Obj($file->get_statistic())
				);
		}

		$domain = 1948;

		$this->payment_success($order, $orderItems, $domain);

		$this->addedit(TRUE, $object, $domain);
		$this->addedit(FALSE, $object, $domain);
		
		$this->block_contact('123213', $objects, $domain);

		$this->decline_contact('213123', $objects, $domain);

		$this->contact_verification_code($contact, '123213', $domain);

		$this->forgot_password('http://vagapov.site/sdfdsfdsf', $domain);

		$this->moderate_object(
			array('<p> + Action 1</p>','<p> + Action 2</p>'),
			array('<p> - Action 3</p>','<p> - Action 4</p>'), $domain
		);


		$this->massload_report($objectload,  $common_stat, $category_stat, $user->org_name, $domain);

		$this->object_expiration($objects, $domain);
		$this->object_to_archive($objects, $domain);

		$this->register_data('aaaaaaa','passsssssss', $domain);
		$this->register_success('coooooooodddddeeeeeee', $domain);

	}

	private function payment_success($order, $orderItems, $domain = FALSE)
	{
		$params = array(
			'order' => $order,
		    'orderItems' => $orderItems,
		    'domain' => $domain
		);


		  

		Minion_CLI::write( Email_Send::factory('payment_success')
			->to( Task_TestEmail::$to )
			->set_params($params)
			->send()
		);

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

	    Minion_CLI::write( Email_Send::factory('addedit')
	    			->to( Task_TestEmail::$to )
	    			->set_params($params)
	    			->send()
	    		);
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

	     Minion_CLI::write( Email_Send::factory('block_contact')
	    	    			->to( Task_TestEmail::$to )
	    	    			->set_params($params)
	    	    			->send()
	    	    		);
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

	     Minion_CLI::write( Email_Send::factory('decline_contact')
	    	    			->to( Task_TestEmail::$to )
	    	    			->set_params($params)
	    	    			->send()
	    	    		);
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

	     Minion_CLI::write( Email_Send::factory('contact_verification_code')
	    	    			->to( Task_TestEmail::$to )
	    	    			->set_params($params)
	    	    			->send()
	    	    		);
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

	     Minion_CLI::write( Email_Send::factory('forgot_password')
	    	    			->to( Task_TestEmail::$to )
	    	    			->set_params($params)
	    	    			->send()
	    	    		);
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

	     Minion_CLI::write( Email_Send::factory('moderate_object')
	    	    			->to( Task_TestEmail::$to )
	    	    			->set_params($params)
	    	    			->send()
	    	    		);
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

	     Minion_CLI::write( Email_Send::factory('massload_report')
	    	    			->to( Task_TestEmail::$to )
	    	    			->set_params($params)
	    	    			->send()
	    	    		);
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

	     Minion_CLI::write( Email_Send::factory('object_expiration')
	    	    			->to( Task_TestEmail::$to )
	    	    			->set_params($params)
	    	    			->send()
	    	    		);
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

	     Minion_CLI::write( Email_Send::factory('object_to_archive')
	    	    			->to( Task_TestEmail::$to )
	    	    			->set_params($params)
	    	    			->send()
	    	    		);
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

	     Minion_CLI::write( Email_Send::factory('register_data')
	    	    			->to( Task_TestEmail::$to )
	    	    			->set_params($params)
	    	    			->send()
	    	    		);
	}

	public static function register_success($code, $domain = FALSE)
	{
	    $params = array(
	                'code' => $code,
	                'domain' => $domain
	            );

	     Minion_CLI::write( Email_Send::factory('register_success')
	    	    			->to( Task_TestEmail::$to )
	    	    			->set_params($params)
	    	    			->send()
	    	    		);
	}



}
