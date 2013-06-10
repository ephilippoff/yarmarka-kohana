<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Object extends ORM {

	protected $_table_name = 'object';

	protected $_belongs_to = array(
		'user'			=> array('model' => 'User', 'foreign_key' => 'author'),
		'category_obj'	=> array('model' => 'Category', 'foreign_key' => 'category'),
		'city_obj'		=> array('model' => 'City', 'foreign_key' => 'city_id'),
	);

	public function remove_from_favorites()
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		return DB::delete('favorite')
			->where('objectid', '=', intval($this->id))
			->execute();
	}

	public function user_favorites($user_id)
	{
		return $this->join('favorite', 'inner')
			->on('object.id', '=', 'favorite.objectid')
			->where('favorite.userid', '=', intval($user_id));
	}

	public function where_region($region_id)
	{
		return $this->join('city', 'inner')
			->on('object.city_id', '=', 'city.id')
			->where('city.region_id', '=', intval($region_id));
	}

	public function get_real_date_created($format = 'd.m.Y')
	{
		if ( ! $this->loaded())
		{
			return NULL;
		}

		return date($format, strtotime($this->real_date_created));
	}

	public function is_active()
	{
		return  ! $this->in_archive AND $this->is_published() AND ! $this->is_banned();
	}

	public function in_archive()
	{
		return $this->in_archive AND $this->is_published() AND ! $this->is_banned();
	}

	public function is_published()
	{
		return (bool) $this->is_published;
	}
	
	public function is_banned()
	{
		return $this->is_bad > 0;
	}

	public function get_url()
	{
		return CI::site('obyavlenie/'.$this->category_obj->get_seo_name(NULL, $this->city_id).'/'.($this->seo_name ? $this->seo_name.'-' : '').$this->id,
				'http', TRUE, Region::get_domain_by_city($this->city_id));
	}

} // End Access Model
