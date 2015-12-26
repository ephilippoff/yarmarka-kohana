<?php defined('SYSPATH') OR die('No direct script access.');

class Service_NewsPaper extends Service
{
	const NEWSPAPER_SETTING_NAME = 'newspaper';
	public static $_types = array(
		"free" => "Бесплатное",
		"point" => "С точкой",
		"photo" => "Фото объявление",
		"green_border" => "В зеленой рамке",
		"red_border" => "В розовой рамке",
		"black_border" => "В черной рамке",
		"blue_border" => "В синей рамке",
		"yellow_marker" => "Маркер жёлтый",
		"blue_marker" => "Маркер синий",
		"red_marker" => "Маркер красный",
	);

	public static $_cities = array(
		"surgut" => "Ярмарка - Сургут",
		"nizhnevartovsk" => "Ярмарка - Нижневартовск",
		"tyumen" => "Ярмарка - Тюмень"
	);

	protected $_name = "newspaper";
	protected $_title = "Объявление в газету";
	protected $_is_multiple = TRUE;
	public $_contents = array();

	public function __construct($object_id = NULL)
	{
		$object = ORM::factory('Object',$object_id);
		if ($object->loaded()) {
			$this->object($object);
		}
		$this->_initialize();
	}

	public function get_info()
	{
		return array(
			"types" => self::$_types,
			"prices" => $this->_price_config
		);
	}

	public function get()
	{
		$quantity = ($this->quantity()) ? $this->quantity() : 1;
		$price = $price_total = $this->getNewsPaperPrice();
		$discount = 0;
		$discount_reason = "";
		$discount_name = FALSE;
		$price_total = $price * $quantity - $discount;
		$description = $this->get_params_description().$discount_reason;
		$city = $this->city();
		$contents = $this->contents();
		$types =  array_keys($contents);
		if ($types == array("free")) {
			$discount_name = "free_advert";
		}
		return array(
			"contents" => $contents,
			"city" => ($this->city()) ? $city : NULL,
			"name" => $this->_name,
			"title" => $this->_title,
			"price" => $price,
			"quantity" => $quantity,
			"discount" => $discount,
			"discount_name" => $discount_name,
			"discount_reason" => $discount_reason,
			"price_total" => $price_total,
			"description" => $description
		);
	}

	public function getNewsPaperPrice()
	{
		$contents = $this->contents();
		$city = $this->city();
		$price_config = $this->_price_config;
		$result = 0;
		foreach ($contents as $key => $value) {
			$result += $price_config[$city][$key] * $value;
		}
		return $result;
	}

	public function get_params_description($params =  array())
	{
		$contents = $this->contents();
		$city = Arr::get(self::$_cities, $this->city(), "");
		$result = array();
		foreach ($contents as $key => $value) {
			$value = (int) $value;
			$result[] = self::$_types[$key]."(".$value.")";
		}
		return $city."; ".implode(", ", $result);
	}

	public function set_params($params = array())
	{
		parent::set_params($params);
		$params = new Obj($params);

		if ($params->types) 
		{
			$contents = array();
			foreach ($params->types as $type) {
				$contents[$type] = $params->{"quantity_".$type};
			}

			$this->contents($contents);
		}

		if ($params->city) 
		{
			$this->city($params->city);
		}

	}

	public function contents($contents = NULL)
	{
		if (!$contents) return $this->_contents;
		$this->_contents = $contents;
		return $this;
	}

	public function city($city = NULL)
	{
		if (!$city) return $this->_city;
		$this->_city = $city;
		return $this;
	}

	public function apply($orderItem)
	{
		$types = array_keys((array) $orderItem->service->contents);
		if ( $types == array("free") ) return;
		$configBilling = Kohana::$config->load("billing");

		$orderItems = array($orderItem);
		$subj = "Оплата объявлений в газету (".$orderItem->service->price_total." руб). Заказ №".$orderItem->order_id;

		ORM::factory('Order_Log')->write($orderItem->order_id, "notice", $subj );

		$order = ORM::factory('Order', $orderItem->order_id);
		$object = ORM::factory('Object', $orderItem->object->id);
		$user = ORM::factory('User', $object->author);
		$contacts = array();
		$contacts = $object->contacts;

		$msg = View::factory('emails/payment_success_apply_notify',
				array(
					'order' => $order, 
					'orderItems' => $orderItems, 
					'object' => $object,
					'contacts' => $contacts,
					'user'=> $user
				));

		ORM::factory('Order_Log')->write($orderItem->order_id, "notice", vsprintf("Отправка уведомелния операторам на размещение в  газету: %s", array(join(", ",$configBilling["operators_for_notify"])) ) );

		foreach ($configBilling["operators_for_notify"] as $email) {
			Email::send($email, Kohana::$config->load('email.default_from'), $subj, $msg);
		}
	}

	static function apply_service($object_id, $order_id, $description)
	{

	}
}