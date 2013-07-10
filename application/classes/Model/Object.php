<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Object extends ORM {

	protected $_table_name = 'object';

	protected $_belongs_to = array(
		'user'			=> array('model' => 'User', 'foreign_key' => 'author'),
		'category_obj'	=> array('model' => 'Category', 'foreign_key' => 'category'),
		'city_obj'		=> array('model' => 'City', 'foreign_key' => 'city_id'),
	);

	protected $_has_many = array(
		'contacts'			=> array('model' => 'Object_Contact', 'foreign_key' => 'object_id'),
		'user_messages'		=> array('model' => 'User_Messages', 'foreign_key' => 'object_id'),
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

	public function with_main_photo()
	{
		return $this->select(array('object_attachment.filename', 'main_image_filename'))
			->select(array('object_attachment.title', 'main_image_title'))
			->join('object_attachment', 'left')
			->on('object.main_image_id', '=', 'object_attachment.id');
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
		return $this->in_archive AND ! $this->is_banned();
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

	/**
	 * Дата когда можно поднять объявление
	 * 
	 * @access public
	 * @return time
	 */
	public function get_service_up_timestamp()
	{
		return strtotime($this->date_created) + 86400 * Kohana::$config->load('common.days_count_between_service_up');
	}

	public function up()
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		$this->date_created = DB::expr('NOW()');
		$this->date_updated = DB::expr('NOW()');
		
		$result = $this->update();

		$this->reload();

		return $result;
	}

	public function prolong($date_expiration, $to_forced_moderation = FALSE, $no_bad = FALSE)
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		$this->date_expiration	= $date_expiration;
		$this->date_created		= DB::expr('NOW()');
		$this->is_published		= 1;
		$this->in_archive		= FALSE;

		if ($to_forced_moderation)
		{
			$this->to_forced_moderation = TRUE;
		}
			
		if ($no_bad)
		{
			$this->is_bad = 0;
		}

		// update object
		$object = $this->update();

		// add to log
		DB::insert('object_archive_log', array('object_id', 'movedon', 'direction'))
			->values(array($object->id, DB::expr('NOW()'), 2))
			->execute();

		return $object;
	}

	public function toggle_published()
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		$this->is_published = (int) ! $this->is_published;
		return $this->update();
	}

} // End Access Model
