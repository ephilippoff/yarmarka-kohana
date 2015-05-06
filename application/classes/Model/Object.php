<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Object extends ORM {

	protected $_table_name = 'object';

	protected $_belongs_to = array(
		'user'			=> array('model' => 'User', 'foreign_key' => 'author'),
		'company'		=> array('model' => 'User', 'foreign_key' => 'author_company_id'),
		'category_obj'	=> array('model' => 'Category', 'foreign_key' => 'category'),
		'city_obj'		=> array('model' => 'City', 'foreign_key' => 'city_id'),
		'location_obj'	=> array('model' => 'Location', 'foreign_key' => 'location_id')
	);

	protected $_has_many = array(
		'contacts'			=> array('model' => 'Contact', 'through' => 'object_contacts'),
		'user_messages'		=> array('model' => 'User_Messages', 'foreign_key' => 'object_id'),
		'complaints'		=> array('model' => 'Complaint', 'foreign_key' => 'object_id'),
	);

	public function filters()
	{
		return array(
			'user_text' => array(
				array(array($this, 'filter_user_text')),
			),
			'title' => array(
				array(array($this, 'filter_title')),
			),
			'price' => array(
				array('intval'),
			),
		);
	}

	public function filter_user_text($user_text)
	{
		if ($this->loaded())
		{
			$this->full_text = $this->generate_full_text($user_text);
		}

		return $user_text;
	}

	public function filter_title($title)
	{
		$this->seo_name = URL::title($title, '-', TRUE);

		return $title;
	}

	public function generate_full_text($user_text = NULL)
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		if (is_null($user_text))
		{
			$user_text = $this->user_text;
		}

		return strip_tags($this->title).', '.strip_tags($user_text).', '.join(', ', $this->get_attributes_values(NULL, FALSE));
	}

	public function generate_title($template = NULL)
	{
		if (is_null($template))
		{
			$template = $this->category_obj->template;
		}

		if ( ! $this->loaded() OR ! $template)
		{
			return FALSE;
		}

		if ( ! preg_match_all('/{([^}]*)}/i', $template, $matches))
		{
			return $template;
		}

		if (in_array('adres-raion', $matches[1]))
		{
			if ($this->location_obj->loaded())
			{
				$template = str_replace('{adres-raion}', $this->location_obj->address, $template);
			}
		}

		$attrs = $this->get_attributes();
		foreach ($attrs as $attr)
		{
			if (in_array($attr['seotitle'], $matches[1]))
			{
				$value = '';
				switch ($attr['types'])
				{
					case 'integer':
					case 'numeric':
						if ( ! intval($attr['min_value']) AND intval($attr['max_value']))
						{
							$value = ' до '.$attr['max_value'];
						} 
						elseif (intval($attr['min_value']) AND ! intval($attr['max_value'])) 
						{
							$value = $attr['min_value'];
						}
						elseif (intval($attr['min_value']) AND intval($attr['max_value'])) 
						{
							$value = ' от '.$attr['min_value'].' до '.$attr['max_value'];
						}
					break;
					case 'list':
						if ($attr['parent_other'])
						{
							$value = '';
						} 
						else 
						{
							$value = $attr['tvalue'];
						}
					break;
					default:
						$value = $attr['tvalue'];
					break;
				}

				$template = str_replace('{'.$attr['seotitle'].'}', $value, $template);
			}
		}

		//зачищаем оставшиеся параметры из заголовка, которые не распарсились
		foreach ($matches[0] as $match)
		{
			$template = str_replace($match, '', $template);
		}

		return $template;
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
	
	public function with_visits()
	{
		return $this->select(DB::expr('(SELECT SUM(visits) FROM object_statistic1 WHERE object_id = object.id) as stat_visits'));
	}
	
	public function with_used_service($service_id = 0)
	{
		return $this->select(DB::expr('EXISTS(select id from service_object where service_object.object = object.id and service = '.(int)$service_id.') as used_service'));
	}
	
	public function with_selection()
	{	
		return $this->select(DB::expr('EXISTS(select id 
											  from object_service_photocard 
											  where object_service_photocard.object_id = object.id and object_service_photocard.type = 2) 
											  as in_selection'));		
	}
	
	public function with_used_premium()
	{
		return $this->select(DB::expr('EXISTS(select id 
											  from object_rating 
											  where object_rating.object_id = object.id and object_rating.date_expiration > NOW()) 
											  as premium_used'));
	}
	
	public function with_used_lider()
	{
		return $this->select(DB::expr('EXISTS(select id 
											from object_service_photocard as osp
											where osp.object_id = object.id 
											and osp.type = 1 
											and osp.date_expiration >= CURRENT_TIMESTAMP
											and osp.active = 1
											and osp.invoice_id <> 0) 
											as lider_used'));
	}	
	
	public function with_used_reklama()
	{
		return $this->select(DB::expr("EXISTS(select id 
											  from reklama 
											  where link like '%'||object.id||'%' 
											  and CURRENT_DATE >= start_date 
											  and CURRENT_DATE <= end_date 
											  and active = 1) 
											  as reklama_used"));
	}
	
	public function with_used_ticket()
	{
		return $this->select(DB::expr('EXISTS(select id 
											from object_service_ticket as ost
											where ost.object_id = object.id 
											and ost.active = 1 
											and ost.invoice_id <> 0
											and NOW() <= ost.date_expiration)
											as ticket_used'));
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
		return CI::site('obyavlenie/'.$this->category_obj->get_seo_without_geo().'/'.($this->seo_name ? $this->seo_name.'-' : '').$this->id,
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
		$interval = Kohana::$config->load('common.days_count_between_service_up_by_cat');
		$interval = (isset($interval[$this->category])) ? $interval[$this->category] : Kohana::$config->load('common.days_count_between_service_up');
		return strtotime($this->date_created) + 86400 * $interval;
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

	public function prolong($date_expiration, $update = TRUE)
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		$this->date_expiration	= $date_expiration;
		$this->date_created		= DB::expr('NOW()');
		$this->is_published		= 1;
		$this->in_archive		= FALSE;

		if ($update)
		{
			// update object
			$object = $this->update();
		}

		// add to log
		/*DB::insert('object_archive_log', array('object_id', 'movedon', 'direction'))
			->values(array($object->id, DB::expr('NOW()'), 2))
			->execute();*/

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

		// @todo переделать на ORM
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

	/**
	 * Return column cities as array
	 *
	 * @return array
	 */
	public function get_cities()
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		$str = trim(str_replace(array('{', '}'), '', $this->cities));
		
		if ($str)
		{
			return explode(',', $str);
		}
		else
		{
			return array();
		}
	}

	public function get_filename()
	{
		if ( ! $this->loaded() OR ! $this->main_image_id)
		{
			return FALSE;
		}

		$attachment = ORM::factory('Object_Attachment')
			->where('id', '=', $this->main_image_id)
			->cached(Date::MINUTE)
			->find();

		return $attachment->loaded() ? $attachment->filename : FALSE;
	}

	public function save(Validation $validation = NULL)
	{
		if ($this->cities AND is_array($this->cities))
		{
			$this->cities = '{'.join(',', $this->cities).'}';
		}
		
		// по дефолту заполняем cities только для новых объяв
		if ($this->city_id AND ! $this->loaded())
		{
			$this->cities = '{'.$this->city_id.'}';
		}

		if ($this->geo_loc)		
		{
			list($lat, $lon) = explode(',', $this->geo_loc);
			$this->location = DB::expr("PointFromText('POINT($lon $lat)',900913)");
		}

		if ( ! $this->date_expired)
		{
			$this->date_expired = DB::expr('NOW()');
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
		$temp_object->save();

		return TRUE;
	}

	public function add_contact($contact_type_id, $contact_str)
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		// is contact exists at this object
		$contact_exists = ORM::factory('Contact')
			->where_object_id($this->id);
		if (Model_Contact_Type::is_phone($contact_type_id))
		{
			$contact_exists->by_phone_number($contact_str)
				->find();
		}
		else
		{
			$contact_exists->where('contact', '=', trim($contact_str))
				->where('contact_type_id', '=', intval($contact_type_id))
				->find();
		}

		$contact = ORM::factory('Contact');
		if ( ! $contact_exists->loaded())
		{
			$contact->contact_type_id	= intval($contact_type_id);
			$contact->contact			= trim($contact_str);
			$contact->show 				= 1;
			$contact = $contact->create();


			$contact->add('objects', $this->id);
			$contact->reload();
		}

		return $contact->loaded() ? $contact : $contact_exists;
	}
	
	public function delete_contacts()
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}
		
		DB::delete('object_contacts')
			->where('object_id', '=', $this->id)
			->execute($this->_db);

		return $this;
	}

	public function is_valid()
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		$validate_object = (bool) ($this->city_id > 0 AND ! empty($this->title));
		
		if ($this->category_obj->text_required == 1) 
			$validate_object = $validate_object AND ! empty($this->user_text);
		
		if ( ! $validate_object)
		{
			return FALSE;
		}

		$has_valid_contacts = FALSE;
		foreach ($this->contacts->find_all() as $contact)
		{
			if ($contact->verified_user_id == $this->author)
			{
				$has_valid_contacts = TRUE;
				break;
			}
		}

		if ( ! $has_valid_contacts)
		{
			return FALSE;
		}


		return TRUE;
	}

	public function generate_signature()
	{
		if ( ! $this->loaded())
			return TRUE;
		return Object_Utils::generate_signature($this->full_text);
	}

	public function get_active_by_user_and_category($user_id, $category_id)
	{
		return $this->where("author","=",$user_id)
				->where("category","=",$category_id)
				->where("is_published","=",1)
				->where("active","=",1);
	}

	public function unpublish_expired_in_objectload_category($objectload_id, $user_id, $category_id, $category_names)
	{


		$objects = ORM::factory('Objectload_Files')
				->get_union_subquery_by_category($objectload_id, $category_names);

		$filters = $this->get_filter_subquery_by_category($category_names);

		$count = 0;
		if ($objects)
		{
			$query = ORM::factory('Object')
					->where_open()
					->where('number', 'NOT IN', $objects)
						->or_where('number', 'IS', NULL)
					->where_close()
					->where('author', '=', $user_id)
					->where('category','=', $category_id)
					->where('is_published','=', 1)
					->where('active','=', 1)
					->where('is_union','IS', NULL);

			foreach ($filters as $filter) {
				$query = $query->where($filter,"IS NOT",NULL);
			}

			

			$f = $query->set('is_published', 0)
						->set('parent_id', NULL)
						->update_all();

			$count = $query->count_all();
		}

		return $count;		
	}

	public function get_filter_subquery_by_category($categories)
	{
		$filters = array();
		foreach ($categories as $category) {
			$config = Kohana::$config->load('massload/bycategory.'.$category);

			foreach ($config["filter"] as $seo_name => $value)
			{
		
				$query = DB::select(DB::expr("data_list.id"))
							->from("data_list")
							->join("attribute")
								->on("attribute.id","=","data_list.attribute")
							->where("attribute.seo_name","=", $seo_name)
							->where("data_list.object","=",DB::expr("object.id"))
							->limit(1);
				if (is_array($value))		
					$query = $query->where("data_list.value","IN", $value);
				else 
					$query = $query->where("data_list.value","=", $value);

				$filters[] = $query;
			}

		}
		return $filters;
	}

	public function publish_and_prolonge_objectload($objectload_id, $user_id)
	{
		$objects = ORM::factory('Objectload_files')
				->get_union_subquery_by_category($objectload_id);

		$count = 0;
		if ($objects)
		{
			$count = ORM::factory('Object')
					->where('number', 'IN', $objects)
					->where('author','=', $user_id)
					->count_all();

			$date_expiration = date('Y-m-d H:i:s', strtotime('+60 days'));

			$f = ORM::factory('Object')
				->where('number', 'IN', $objects)
				->where('author','=', $user_id)
				->set('date_expiration', $date_expiration)
				->set('in_archive', 'f')
				->set('active', 1)
				->set('is_published', 1)
				->set('is_bad', 0)
				->update_all();
		}

		return $count;	
	}

	public function info_by_ids($ids)
	{

       return $this->select("object.id","object.title",array("category.id","category_id"),array("category.title","category_title"))
        	->join("category")
        		->on("category","=","category.id")
        	->where("object.id","IN",DB::expr("(".$ids.")"))
        	->where("active","=","1")
        	->where("is_published","=","1")
        	->where("date_expired","<=",DB::expr("NOW()"))
        	->order_by("date_created","desc")
        	->cached(60*60);
	}
	
	public function get_favorite($user_id)
	{
		if ( ! $this->loaded() OR !$user_id)
		{
			return FALSE;
		}

		$favorite = ORM::factory('Favourite')
			->where('objectid', '=', $this->id)
			->where('userid', '=', $user_id)				
			->find();

		return $favorite->loaded() ? 1 : 0;
	}	
	
	public function increase_stat_contacts_show()
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}	

		$object_statistic = ORM::factory('Object_Statistic')
				->where('object_id', '=', $this->id)
				->where('date', '=', DB::expr('CURRENT_DATE'))
				->find();
				
		if ($object_statistic->loaded())
		{
			$object_statistic->contacts_show_count++;
			$object_statistic->save();
		}
		
	}
}

/* End of file Object.php */
/* Location: ./application/classes/Model/Object.php */