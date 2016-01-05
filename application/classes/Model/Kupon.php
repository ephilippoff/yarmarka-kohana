<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Kupon extends ORM
{
	protected $_table_name = 'kupon';

	const INITIAL		= "initial";
	const AVAIL		= "avail";
	const RESERVE		= "reserve";
	const SOLD		= "sold";

	protected $_belongs_to = array(
		'kupon_group_obj'	=> array('model' => 'Kupon_Group', 'foreign_key' => 'kupon_group_id'),
		'order_obj'	=> array('model' => 'Order', 'foreign_key' => 'order_id'),
		'invoice'	=> array('model' => 'Invoices', 'foreign_key' => 'invoice_id')
	);

	protected $_states = array(
		"initial" => "исх", "avail" => "доступен", "reserve" => "в резерве", "sold" => "продан"
	);

	public function with_objects()
	{
		return $this->select(array('object.title', 'object_title'))
			->join('object', 'left')
			->on('kupon.object_id', '=', 'object.id');
	}	
	
	public function with_invoices()
	{
		return $this->select(array('invoices.user_id', 'user_id'))
			->join('invoices', 'left')
			->on('kupon.invoice_id', '=', 'invoices.id');
	}	

	public function sum_by_field($field)
	{
		$query = DB::select('object_id', DB::expr('SUM('.$field.')'))
				->from('kupon')
				->group_by('object_id')
				->as_object()
				->execute();
		
		return $query;				
	}

	function get_balance_by_begin_state($kupon_id, $state = "avail", $order_id = NULL)
	{
		$query = DB::select(array(DB::expr('SUM("count")'), 'begin_count'))
							->from('object_movement')
								->where("kupon_id","=",$kupon_id)
								->where("begin_state","=", $state);
		if ($order_id) {
			$query = $query->where("order_id", "=", $order_id);
		}

		return $query;
	}

	function get_balance_by_end_state($kupon_id, $state = "avail", $order_id = NULL)
	{
		$query = DB::select(array(DB::expr('SUM("count")'), 'end_count'))
							->from('object_movement')
								->where("kupon_id","=",$kupon_id)
								->where("end_state","=", $state);
		if ($order_id) {
			$query = $query->where("order_id", "=", $order_id);
		}

		return $query;
	}

	function get_balance($state = "avail", $order_id = NULL, $date = FALSE)
	{
		if (!$this->loaded()) return FALSE;

		$kupon_id = $this->id;

		$end_state_query = $this->get_balance_by_end_state($kupon_id, $state, $order_id);
		$begin_state_query = $this->get_balance_by_begin_state($kupon_id, $state, $order_id);

		if ($date) {
			$end_state_query = $end_state_query->where("date", "<", $date);
			$begin_state_query = $begin_state_query->where("date", "<", $date);
		}

		$balance = DB::select($end_state_query, $begin_state_query)
					->execute();

		return $balance->get("end_count") - $balance->get("begin_count");
	}


	function change_state($begin_state, $end_state, $order_id = NULL, $access_key = NULL, $description = NULL)
	{
		// if ($begin_state == self::SOLD) {
		// 	return FALSE;
		// }

		$kupon_id = $this->id;

		$om = ORM::factory('Object_Movement');
		$om->begin_state = $begin_state;
		$om->end_state = $end_state;
		$om->kupon_id = $kupon_id;
		$om->order_id = $order_id;
		$om->count = 1;
		$om->description = $description;
		$om->save();

		$this->access_key = $access_key;
		$this->order_id = $order_id;
		$this->state = $end_state;
		$this->save();

		return TRUE;
	}

	function check_available()
	{
		if (!$this->loaded()) return FALSE;

		if ($this->get_balance() > 0 ) {
			return TRUE;
		}

		return FALSE;
	}

	function check_and_restore_reserve_if_possible($access_key)
	{
		if (!$this->loaded()) return FALSE;

		if ($this->access_key == $access_key) {
			return TRUE;
		}

		if ($this->check_available())
		{
			$this->reserve(NULL, $access_key);
			return TRUE;
		}

		return FALSE;
	}

	function get_last_operation()
	{
		if (!$this->loaded()) return FALSE;

		return ORM::factory('Object_Movement')->where("kupon_id","=",$this->id)->order_by("date","desc")->find();
	}

	function get_avail($group_id)
	{
		return $this->where("kupon_group_id","=",$group_id)->where("state","IN", array(self::AVAIL))->order_by("id");
	}

	function get_sold($group_id)
	{
		return $this->where("kupon_group_id","=",$group_id)->where("state","IN", array(self::SOLD));
	}

	function get_avail_count($group_id)
	{
		return $this->where("kupon_group_id","=",$group_id)->where("state","IN", array(self::AVAIL))->count_all();
	}

	function get_sold_count($group_id)
	{
		return $this->get_sold($group_id)->count_all();
	}

	function reserve($order_id = NULL, $access_key = NULL)
	{
		return $this->change_state($this->state, self::RESERVE, $order_id, $access_key);
	}

	function return_to_avail($description = NULL, $orderId = NULL)
	{
		if ($this->state == "avail") return;
		
		return $this->change_state($this->state, self::AVAIL, $orderId, NULL, $description);
	}

	function to_sold($order_id, $access_key = NULL)
	{
		if ($this->state == "sold") return;
		$number = self::crypt_number(" ".self::generate_number());
		$this->number = $number;
		return $this->change_state($this->state, self::SOLD, $order_id, $access_key);
	}

	static function generate_number()
	{
		return rand(100000000, 999999999);
	}

	static function crypt_number($number)
	{
		return self::dsCrypt($number);
	}

	static function decrypt_number($number)
	{
		return self::dsCrypt($number, true);
	}

	static function check_number($number)
	{
		$number = trim($number);
		$number = self::crypt_number(" ".$number);

		$kupon = ORM::factory('Kupon')->where("number","=",$number)->find();

		return ($kupon->loaded());
	}

	/**
	 * Обратимое шифрование методом "Двойного квадрата" (Reversible crypting of "Double square" method)
	 * @param  String $input   Строка с исходным текстом
	 * @param  bool   $decrypt Флаг для дешифрования
	 * @return String          Строка с результатом Шифрования|Дешифрования
	 * @author runcore
	 */
	static function dsCrypt($input,$decrypt=false) {
		$o = $s1 = $s2 = array(); // Arrays for: Output, Square1, Square2
		// формируем базовый массив с набором символов
		$basea = array('?','(','@',';','$','#',"]","&",'*');  // base symbol set
		$basea = array_merge($basea, range('a','z'), range('A','Z'), range(0,9) );
		$basea = array_merge($basea, array('!',')','_','+','|','%','/','[','.',' ') );
		$dimension=9; // of squares
		for($i=0;$i<$dimension;$i++) { // create Squares
			for($j=0;$j<$dimension;$j++) {
				$s1[$i][$j] = $basea[$i*$dimension+$j];
				$s2[$i][$j] = str_rot13($basea[($dimension*$dimension-1) - ($i*$dimension+$j)]);
			}
		}
		unset($basea);
		$m = floor(strlen($input)/2)*2; // !strlen%2
		$symbl = $m==strlen($input) ? '':$input[strlen($input)-1]; // last symbol (unpaired)
		$al = array();
		// crypt/uncrypt pairs of symbols
		for ($ii=0; $ii<$m; $ii+=2) {
			$symb1 = $symbn1 = strval($input[$ii]);
			$symb2 = $symbn2 = strval($input[$ii+1]);
			$a1 = $a2 = array();
			for($i=0;$i<$dimension;$i++) { // search symbols in Squares
				for($j=0;$j<$dimension;$j++) {
					if ($decrypt) {
						if ($symb1===strval($s2[$i][$j]) ) $a1=array($i,$j);
						if ($symb2===strval($s1[$i][$j]) ) $a2=array($i,$j);
						if (!empty($symbl) && $symbl===strval($s2[$i][$j])) $al=array($i,$j);
					}
					else {
						if ($symb1===strval($s1[$i][$j]) ) $a1=array($i,$j);
						if ($symb2===strval($s2[$i][$j]) ) $a2=array($i,$j);
						if (!empty($symbl) && $symbl===strval($s1[$i][$j])) $al=array($i,$j);
					}
				}
			}
			if (sizeof($a1) && sizeof($a2)) {
				$symbn1 = $decrypt ? $s1[$a1[0]][$a2[1]] : $s2[$a1[0]][$a2[1]];
				$symbn2 = $decrypt ? $s2[$a2[0]][$a1[1]] : $s1[$a2[0]][$a1[1]];
			}
			$o[] = $symbn1.$symbn2;
		}
		if (!empty($symbl) && sizeof($al)) // last symbol
			$o[] = $decrypt ? $s1[$al[1]][$al[0]] : $s2[$al[1]][$al[0]];
		return implode('',$o);
	}


}