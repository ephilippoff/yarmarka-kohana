<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Object extends ORM {

	protected $_table_name = 'object';

	protected $_belongs_to = array(
		'user'			=> array('model' => 'User', 'foreign_key' => 'author'),
		'category_obj'	=> array('model' => 'Category', 'foreign_key' => 'category'),
		'city_obj'		=> array('model' => 'City', 'foreign_key' => 'city_id'),
		'location_obj'	=> array('model' => 'Location', 'foreign_key' => 'location_id'),
	);

	protected $_has_many = array(
		'contacts'			=> array('model' => 'Object_Contact', 'foreign_key' => 'object_id'),
		'user_messages'		=> array('model' => 'User_Messages', 'foreign_key' => 'object_id'),
		'complaints'		=> array('model' => 'Complaint', 'foreign_key' => 'object_id'),
	);

	public function filters()
	{
		return array(
			'user_text' => array(
				array(array($this, 'generate_full_text')),
			),
		);
	}

	public function generate_full_text($user_text)
	{
		if ($this->loaded())
		{
			$this->full_text = strip_tags($this->title).', '.strip_tags($user_text).', '.join(', ', $this->get_attributes_values(NULL, FALSE));
		}

		return $user_text;
	}

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

	public function is_moderate()
	{
		return (bool) $this->moder_state;
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

	public function get_contacts()
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		return $this->contacts->with('contact_type')->find_all();
	}

	public function get_attributes($object_id = NULL)
	{
		if (is_null($object_id))
		{
			$object_id = $this->id;
		}

		if ( ! $object_id = intval($object_id))
		{
			return FALSE;
		}

		$sql = "select ref.id as ref_id, ref.is_selectable, types,aid,atitle,seotitle,dlref as reference,min_value,max_value,idvalue,tvalue,boolvalue,ref.is_required,parent_other,unit,fe.group as fegroup,gr.title as grtitle  from (
			  select 'list' as types,attr.id as aid,attr.title as atitle,attr.seo_name as seotitle,dl.reference as dlref,0 as min_value,0 as max_value,ae.id as idvalue,ae.title as tvalue,0 as boolvalue,ae.parent_other as parent_other,attr.unit   
			  from data_list as dl left join attribute as attr on dl.attribute=attr.id left join attribute_element as ae on dl.value=ae.id
			  where dl.object=:object_id
			  union all
			  select 'integer' as types,attr.id as aid,attr.title as atitle,attr.seo_name as seotitle,dl.reference as dlref,dl.value_min as min_value,dl.value_max as max_value,0 as idvalue,'' as tvalue,0 as boolvalue,0 as parent_other,attr.unit 
			  from data_integer as dl left join attribute as attr on dl.attribute=attr.id
			  where dl.object=:object_id
			  union all
			  select 'numeric' as types,attr.id as aid,attr.title as atitle,attr.seo_name as seotitle,dl.reference as dlref,dl.value_min as min_value,dl.value_max as max_value,0 as idvalue, '' as tvalue,0 as boolvalue,0 as parent_other,attr.unit 
			  from data_numeric as dl left join attribute as attr on dl.attribute=attr.id
			  where dl.object=:object_id
			  union all
			  select 'text' as types,attr.id as aid,attr.title as atitle,attr.seo_name as seotitle,dl.reference as dlref,0 as min_value,0 as max_value,0 as idvalue,dl.value as tvalue,0 as boolvalue,0 as parent_other,attr.unit 
			  from data_text as dl left join attribute as attr on dl.attribute=attr.id
			  where dl.object=:object_id
			  union all
			  select 'boolean' as types,attr.id as aid,attr.title as atitle,attr.seo_name as seotitle,dl.reference as dlref,0 as min_value,0 as max_value,0 as idvalue, '' as tvalue,dl.value as boolvalue,0 as parent_other,attr.unit 
			  from data_boolean as dl left join attribute as attr on dl.attribute=attr.id
			  where dl.object=:object_id) as allattrs
			left join reference as ref on allattrs.dlref=ref.id
			left join form_element as fe on fe.reference=ref.id and fe.type='add'
			left join \"group\" as gr on gr.id = fe.group
			order by ref.weight";

		return DB::query(Database::SELECT, $sql)
			->param(':object_id', $object_id)
			->execute();
	}

	public function get_attributes_values($object_id = NULL, $mark_required = TRUE)
	{
		$attrs = $this->get_attributes($object_id);
		if ( ! $attrs)
		{
			return FALSE;
		}

		$result = array();
		foreach ($attrs as $row) 
		{
			if ($row['tvalue'])
			{
				$value = $row['tvalue'];
			}
			elseif($row['min_value'] != '')
			{
				$value = $row['min_value'];
				if ($row['unit'])
				{
					$value .= ' '.$row['unit'];
				}
			}
			elseif ($row['boolvalue']) 
			{
				$value = $row['atitle'];
			}

			if ($mark_required AND $row['is_required'])
			{
				$value .= '*';
			}

			$result[] = $value;
		}

		return $result;
	}
	
	//Взять для объявления значение integer-атрибута по его id
	public function get_intattr_value_by_id($object_id = NULL, $attr_id = NULL)
	{
		$object_id = (int)$object_id; 
		$attr_id   = (int)$attr_id;
		
		if (!$object_id or !$attr_id) return FALSE;		
		
		$query = DB::select('value_min')
				->from('data_integer')
				->where('object', '=', $object_id)
				->where('attribute', '=', $attr_id)
				->execute()->get('value_min', 0);
		return $query;
	}

	public function get_cities()
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		$str =trim(str_replace(array('{', '}'), '', $this->cities));
		
		if ($str)
		{
			return explode(',', $str);
		}
		else
		{
			return array();
		}
	}

	public function save(Validation $validation = NULL)
	{
		if ($this->cities AND is_array($this->cities))
		{
			$this->cities = '{'.join(',', $this->cities).'}';
		}

		parent::save($validation);
	}

	public function disable_comments()
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		$block = ORM::factory('User_Messages_Blocks');
		$block->object_id 	= $this->id;
		$block->user_id 	= $this->author;
		$block->save();

		return TRUE;
	}

	public function to_forced_moderation()
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		$this->is_published			= 1;
		$this->moder_state 			= 0;
		$this->to_forced_moderation = TRUE;
		$this->is_bad 				= 0;

		return $this->save();
	}

	public function send_to_db_dns()
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		return Model::factory('Dbdns')->add_record($this->id);
	}

	public function send_to_terrasoft()
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		$temp_object = ORM::factory('Temp_Objects');
		$temp_object->record_id = $this->id;
		$temp_object->status 	= 0;
		$temp_object->tablename = 'object';
		
		return $temp_object->save();
	}
}

/* End of file Object.php */
/* Location: ./application/classes/Model/Object.php */