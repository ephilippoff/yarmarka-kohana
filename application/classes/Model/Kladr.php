<?php if ( ! defined('SYSPATH')) exit('No direct script access allowed');


class Model_Kladr extends Model_Database
{
	protected $_db = 'kladr'; // use this db instance for every query in this model

	public function get_cities($city_name, $limit = 20)
	{
		$limit      = intval($limit);
		$city_name  = $this->prepare_str($city_name);

		$query = DB::select()
			->from('vw_city')
			->where('fts', '@@', DB::expr("to_tsquery('$city_name')"))
			->limit($limit);

		return $query->as_object()
			->cached(Date::DAY)
			->execute($this->_db);
	}

	public function get_address($address, $city_id, $housenum_required = FALSE, $limit = 20)
	{
		$limit 		= intval($limit);
		$address 	= $this->prepare_str($address);
		$city_id	= trim($city_id);

		$sql = "SELECT * FROM vw_address WHERE fts @@ to_tsquery('$address') AND cityid = ?";
		$query = DB::select()
			->from('vw_address')
			->where('fts', '@@', DB::expr("to_tsquery('$address')"))
			->where('cityid', '=', $city_id)
			->limit($limit);

		if ($housenum_required)
		{
			$query->where('aolevel', '=', '11');
		}

		return $query->as_object()
			->cached(Date::DAY)
			->execute($this->_db);
	}

	public function get_city_by_id($id)
	{
		$query = DB::select()
			->from('vw_city')
			->where('id', '=', $id);

		return $query->as_object()
			->execute($this->_db)
			->current();
	}

	public function get_address_by_id($id)
	{
		$query = DB::select()
			->from('vw_address')
			->where('id', '=', $id);

		return $query->as_object()
			->execute($this->_db)
			->current();
	}

	/**
	 * Prepare string for fulltext search
	 * 
	 * @param  string $str
	 * @return string
	 */
	private function prepare_str($str)
	{
		$prep_words = array();

		$words = array_filter(explode(' ', $str), 'trim');
		foreach ($words as $word)
		{
			$prep_words[] = mb_strtolower(trim($word), 'UTF-8').':*';
		}

		return join('&', $prep_words);
	}
}

/* End of file Kladr.php */
/* Location: ./application/classes/Model/Kladr.php */