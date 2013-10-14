<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_Location 
 * 
 * @uses ORM
 * @package 
 * @copyright 2013
 * @author Mikhail Makeev <mihail.makeev@gmail.com> 
 * @version $id$
 */
class Model_Location extends ORM {

	protected $_has_many = array(
		'users' 	=> array(),
		'objects'	=> array(),
	);

	public function filters()
	{
		return array(
			'lat' => array(
				array(array($this, 'prepare_coord')),
			),
			'lon' => array(
				array(array($this, 'prepare_coord')),
			),
		);
	}

	public function prepare_coord($coord)
	{
		return number_format(floatval($coord), 6, '.', '');
	}

	public function save(Validation $validation = NULL)
	{
		if ($this->lat AND $this->lon)
		{
			//$this->location = DB::expr("PointFromText('POINT({$this->lon} {$this->lat})',900913)");
		}

		parent::save($validation);
	}

	public function where_lat_lon($lat, $lon)
	{
		return $this->where('lat', '=', $this->prepare_coord($lat))
			->where('lon', '=', $this->prepare_coord($lon));
	}

	public function get_lon_lat_str()
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		return $this->lon.','.$this->lat;
	}

	public function get_lat_lon_str()
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		return $this->lat.','.$this->lon;
	}
}

/* End of file Location.php */
/* Location: ./application/classes/Model/Location.php */