<?php defined('SYSPATH') OR die('No direct script access.');

class Service_Cities extends Service
{
	protected $_cities = array(
			1944 => "г. Лангепас",
			1976 => "г. Пыть-Ях",
			1975 => "г. Покачи",
			2070 => "г. Вагай",
			1980 => "г. Урай",
			1945 => "г. Лянтор",
			2072 => "г. Белоярский",
			1982 => "г. Югорск",
			1977 => "г. Радужный",
			1949 => "г. Нягань",
			1946 => "г. Мегион",
			1942 => "г. Когалым",
			1908 => "г. Заводоуковск",
			1921 => "г. Ялуторовск",
			1918 => "г. Тобольск",
			1920 => "г. Уват",
			1909 => "г. Ишим",
			1981 => "г. Ханты-Мансийск",
			1947 => "г. Нефтеюганск",
			1943 => "г. Лабытнанги",
			1979 => "г. Сургут",
			1978 => "г. Советский",
			1948 => "г. Нижневартовск",
			1919 => "г. Тюмень",
			2081 => "г. Екатеринбург",
			3046 => "пгт. Излучинск"
		);

	protected $_name = "cities";
	protected $_title = "Объявление в несколько городов";
	protected $_is_multiple = FALSE;
	public $_selected_cities = array();

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
			"exists_cities" => $this->_object->get_cities(),
			"cities" => $this->_cities,
			"price" => $this->getPrice()
		);
	}

	public function get()
	{
		
		$price = $this->getPrice();
		$cities = $this->cities();
		$quantity = count($cities)-1;
		$discount = 0;
		$discount_reason = "";
		$discount_name = FALSE;
		$price_total = $price * $quantity - $discount;
		$description = $this->get_params_description();
		
		return array(
			"cities" => $cities,
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

	public function get_params_description($params =  array())
	{
		$cities = $this->cities();
		$result = array();
		foreach ($cities as $city_key) {
			$result[] = $this->_cities[$city_key];
		}
		return implode(", ", $result);
	}

	public function set_params($params = array())
	{
		parent::set_params($params);
		$params = new Obj($params);

		if ($params->cities) 
		{
			$cities = array();
			if (in_array((int) $this->_object->city_id, array_keys($this->_cities))) {
				$cities[] =  $this->_object->city_id;
			}

			foreach ($params->cities as $city) {
				if (in_array((int) $city, array_keys($this->_cities)) AND !in_array((int) $city, $cities)) {
					$cities[] = (int) $city;
				}
			}
			
			$this->cities($cities);
		}

	}

	public function cities($cities = NULL)
	{
		if (!$cities) return $this->_selected_cities;
		$this->_selected_cities = $cities;
		return $this;
	}

	public function apply($orderItem)
	{
		$cities = $orderItem->service->cities;

		self::saveServiceInfoToCompiled($orderItem->object->id);
		self::apply_service($orderItem->object->id, $cities );

		ORM::factory('Order_Log')->write($orderItem->order_id, "notice", vsprintf("Активация услуги 'Несколько городов': № %s", array( $orderItem->order_id ) ) );
		
	}

	static function apply_service($object_id, $new_cities)
	{
		$object = ORM::factory('Object', $object_id);
		if ($object->loaded()) {

			if ( strtotime( $object->date_expiration ) < strtotime( Lib_PlacementAds_AddEdit::lifetime_to_date("45d") ) ) {
				
				$object->date_expiration = Lib_PlacementAds_AddEdit::lifetime_to_date("45d");
				$object->save();

			}

			$cities = $object->get_cities();

			foreach ($new_cities as $new_city) {
				if (!in_array($new_city, $cities)) {
					$cities[] = $new_city;
				}
			}
			$object->cities = $cities;
			$object->save();
		}

	}
}