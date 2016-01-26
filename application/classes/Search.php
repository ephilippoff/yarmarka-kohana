<?php defined('SYSPATH') or die('No direct script access.');

class Search {

	public static function get_url_to_main_category($city_id = NULL)
	{
		$city_id = is_null($city_id) ? Arr::get($_COOKIE, 'location_city_id') : $city_id;
		$region_id = Arr::get($_COOKIE, 'location_region_id', Kohana::$config->load('common.default_region_id'));

		$geo = ORM::factory('City', $city_id);
		if ( ! $geo->loaded())
		{
			$geo = ORM::factory('Region', $region_id);
		}

		$url = array();
		if ( ! $geo->loaded())
		{
			$url[] = 'search';
		}
		else
		{
			$url[] = $geo->seo_name;
			$category = ORM::factory('Category', 1);
			$url[] = $category->seo_name;
		}

		return join('/', $url);
	}

	public static function get_filters_by_params($params = array(), $alias = "object")
	{
		$filter = array();
		if (count($params) <= 0)
			return $filter;

		$attributes = array();
		$_attributes = ORM::factory('Attribute')
				->where("seo_name", "IN", array_keys($params))
				->find_all();
		foreach ($_attributes as $attribute)
		{
			$attributes[$attribute->seo_name] = array(
									"id"   => $attribute->id,
									"type" => $attribute->type
								);
		}

		foreach ($params as $seo_name => $value) {
			$type = $attributes[$seo_name]["type"];
			$table_name = "data_".strtolower($type);

			if ($type == "list") {
				$filter[] = DB::select("id")
							->from(array($table_name, $table_name."_filter"))
							->where($table_name."_filter."."object","=", DB::expr($alias.".id"))
							->where($table_name."_filter."."attribute", "=", $attributes[$seo_name]["id"])
							->where($table_name."_filter."."value", ((is_array($value)) ? "IN" : "="), $value)
							->limit(1);
			} elseif ($type == "integer" OR $type == "numeric") {
				$value = new Obj($value);
				$query = DB::select("id")
							->from(array($table_name, $table_name."_filter"))
							->where($table_name."_filter."."object","=", DB::expr($alias.".id"))
							->where($table_name."_filter."."attribute", "=", $attributes[$seo_name]["id"])
							->limit(1);
				if ($value->min) {
					$query = $query->where($table_name."_filter."."value_min", ">=", $value->min);
				}
				if ($value->max) {
					$query = $query->where($table_name."_filter."."value_min", "<=", $value->max);
				}
				$filter[] = $query;
			} elseif ($type == "text") {
				$filter[] = DB::select("id")
							->from(array($table_name, $table_name."_filter"))
							->where($table_name."_filter."."object","=", DB::expr($alias.".id"))
							->where($table_name."_filter."."attribute", "=", $attributes[$seo_name]["id"])
							->where(DB::expr("w_lower(".$table_name."_filter."."value".")"), "LIKE", "%".trim(strtolower($value))."%")
							->limit(1);
			}
			// } elseif ($type == "boolean") {
			// 	$filter[] = DB::select("id")
			// 				->from(array($table_name, $table_name."_filter"))
			// 				->where($table_name."_filter."."object","=", DB::expr($alias.".id"))
			// 				->where($table_name."_filter."."attribute", "=", $attributes[$seo_name]["id"])
			// 				->where($table_name."_filter."."value", "=", 1)
			// 				->limit(1);
			// }
		}

		return $filter;
	}

	public static function get_search_cache()
	{
		$shash = Cookie::get('shash');
		$shash = "8550145e167f5e0c7bae0fa3bfb5ab548e0f46f3";
		if ($shash) {
			return ORM::factory('Search_Cache')
							->get_query_by_hash($shash)->find();
		}
		return NULL;
	}

	/**
	 * [search description]
	 * @param  array  $params array(
     *       "premium" => TRUE,
     *       "id" => 3570644,//"id" => array(3570644),
     *       "email" => "xxx@xxx.ru" by user.email
     *       "active" => TRUE,
     *       "published" =>TRUE,
     *       "city_id" => array(1919),//"city_id" => 1919,
     *       "category_id" => 96,//"category_id" => array(96),
     *       "user_id" => 327190,
     *       "user_text" => "Тратата "
     *       "source" => 1,
     *       "photo" => TRUE,
     *       "video" => TRUE,
     *      	"private" => TRUE,
     *		 "type_tr" => 123,
     *       "org" => TRUE,
     *       "filters" =>array(
     *                    'tip-sdelki5' => 3250,
     *                    'build-type'  => 3196,
     *               )
     *       "page" => 1,
     *       "limit" => 3,
     *       "not_id" => array(1,2),
     *       "not_user_id" => array(1,2)
     *   )
	 * @return [ORM]         [description]
	 */
	public static function searchquery($filters = array(), $params = array(), $options = array())
	{
		$params = new Obj(array_merge($filters, $params));
		$options = new Obj($options);

		$select = "o.*";
		$table_name = "vw_objectcompiled";
		if ( $options->count ) {
			$select = "count(o.id) as count";
			$table_name = "object";
		} elseif ($options->group_category) {
			$select = "category.title, category.url, count(o.id)";
			$table_name = "object";
		}
		

		if ($params->hash) {
			$suc = ORM::factory('Search_Url_Cache')->where("hash","=",$params->hash)->find();
			if ($suc->loaded()){
				$suc_params = unserialize($suc->params);
				$params = new Obj(array_merge((array) $params, $suc_params->search_filters));
			}
		}

		$order = ($params->order) ? $params->order : "date_created";
		$order_direction = ($params->order_direction) ? $params->order_direction : "DESC";

		$limit = ($params->limit) ? (int) $params->limit : 30;
		$page = ($params->page) ? (int) $params->page : 0;

		$active = (isset($params->active)) ? $params->active : TRUE;

		$object = DB::select(DB::expr($select))
						->from(array($table_name,"o"));

		if (isset($params->published) AND $params->published === TRUE) {
			$object = $object->where("o.is_published", "=", 1);
		} 
		if (isset($params->published) AND $params->published === FALSE) {
			$object = $object->where("o.is_published", "=", 0);
		}

		if ($active) {
			$object = $object->where("o.active", "=", 1);
		}

		if ($params->id AND is_array($params->id)) {
			$object = $object->where("o.id", "IN", $params->id);
		} elseif ($params->id) {
			$object = $object->where("o.id", "=", $params->id);
		}

		if ($params->not_id AND is_array($params->not_id)) {
			$object = $object->where("o.id", "NOT IN", $params->not_id);
		}

		if ($params->email) {
			$useremail_subquery = DB::select("useremail.id")
										->from(array("user","useremail") )
										->where("o.author","=",DB::expr("useremail.id") )
										->where(DB::expr("w_lower(useremail.email)"),"LIKE", "%".$params->email."%")
										->limit(1);
			$object = $object->where(DB::expr('exists'), DB::expr(''), $useremail_subquery);
		}

		if ($params->contact) {
			$object = $object->where('', 'EXISTS', DB::expr('(SELECT oc.id FROM object_contacts as oc 
											JOIN contacts as c ON c.id = oc.contact_id 
											WHERE oc.object_id=o.id AND c.contact_clear LIKE \'%'.$params->contact['clear'].'%\')'));
		}

		if ( isset($params->moder_state) ) { 
			$object = $object->where('o.moder_state', '=', $params->moder_state);
		}

		if ($params->compile_exists AND $table_name == "vw_objectcompiled") {
			$object = $object->where("o.compiled", "IS NOT", NULL);
		}

		if ($params->complaint_exists) {
			$object = $object->where('', 'EXISTS', DB::expr('(SELECT cmpl.id FROM complaints as cmpl 
					WHERE cmpl.object_id=o.id)'));
		}

		if ($params->user_role) {
			$object = $object->where('', 'EXISTS', DB::expr('(SELECT id FROM "user" as usr
					WHERE usr.id=o.author and usr.role='.$params->user_role.')'));
		}

		if ($params->user_text) {
			$object = $object->where(DB::expr("w_lower(o.full_text)"), "like", "%".mb_strtolower($params->user_text)."%");
		}

		if ($params->user_id) {
			if (!$params->user_company_include) {
				$object = $object->where("o.author", "=", $params->user_id);
			} else {
				$object = $object->where("o.author_company_id", "=", $params->user_id);
				
			}
		}

		if ($params->not_user_id AND is_array($params->not_user_id)) {
			$object = $object->where("o.author", "NOT IN", $params->not_user_id);
		}

		if ( $params->category_id AND is_array($params->category_id) ) {
			$object = $object->where("o.category", "IN", $params->category_id);
		} elseif ($params->category_id) {
			$object = $object->where("o.category", "=", $params->category_id);
		}

		if ($params->category_seo_name) {
			$object = $object->join("category", "inner")
								->on("category.id","=", "o.category");
			$object = $object->where("category.seo_name", (is_array($params->category_seo_name)) ? "IN" : "=", $params->category_seo_name );
		}

		if ($params->not_category_seo_name) {
			$object = $object->join("category", "inner")
								->on("category.id","=", "o.category");
			$object = $object->where("category.seo_name", (is_array($params->not_category_seo_name)) ? "NOT IN" : "<>", $params->not_category_seo_name );
		}

		if ($params->date_created) { 
			if (isset($params->date_created["from"])) {
				$object = $object->where("o.date_created", ">=", $params->date_created["from"]);
			}
			if (isset($params->date_created["to"])) {
				$object = $object->where("o.date_created", "<", $params->date_created["to"]);
			}
		}

		if ($params->real_date_created) { 
			if (isset($params->real_date_created["from"])) {
				$object = $object->where("o.real_date_created", ">=", $params->real_date_created["from"]);
			}
			if (isset($params->real_date_created["to"])) {
				$object = $object->where("o.real_date_created", "<", $params->real_date_created["to"]);
			}
		}

		if ( $params->city_id AND is_array($params->city_id) ) {
			$object = $object->where("o.city_id", "IN", $params->city_id);
		} elseif ($params->city_id) {
			$object = $object->where(DB::expr($params->city_id), "=", DB::expr("ANY(o.cities)"));
		}

		if ($params->premium) {
			$object = $object->join("object_rating", "inner")
								->on("object_rating.object_id","=", "o.id");

			$object = $object->where("object_rating.date_expiration", ">", DB::expr("NOW()"));
			if ( $params->city_id AND is_array($params->city_id) ) {
				$object = $object->where("object_rating.city_id", "IN", $params->city_id);
			} elseif ($params->city_id) {
				$object = $object->where("object_rating.city_id", "=" , $params->city_id);
			}
		}

		if ($params->photocard) {
			$object = DB::select(DB::expr($select))
							->from(array($table_name,"o"));

			if (isset($params->published) AND $params->published === TRUE) {
				$object = $object->where("o.is_published", "=", 1);
			} 
			if (isset($params->published) AND $params->published === FALSE) {
				$object = $object->where("o.is_published", "=", 0);
			}

			if ($active) {
				$object = $object->where("o.active", "=", 1);
			}

			$object = $object->join("object_service_photocard", "inner")
								->on("object_service_photocard.object_id","=", "o.id");

			$object = $object->where("object_service_photocard.date_expiration", ">", DB::expr("NOW()"));
			$object = $object->where("object_service_photocard.active","=", 1);

			if ( $params->category_id ) {
				$category_id = $params->category_id;
				if (!is_array($category_id)) {
					$category_id = array($category_id);
				}
				$object = $object->where("object_service_photocard.categories", "&&", DB::expr('ARRAY['.join(',', $category_id).']') );
			}

			if ( $params->city_id ) {
				$city_id = $params->city_id;
				if (!is_array($city_id)) {
					$city_id = array($city_id);
				}
				$object = $object->where("object_service_photocard.cities", "&&", DB::expr('ARRAY['.join(',', $city_id).']') );
			}
		}

		if ( $params->source ) {
			$object = $object->where("o.source_id", "=", (int) $params->source);
		}

		if ($params->expiration) {
			$object = $object->where("o.date_expired", "<", DB::expr("NOW()"));
		}

		if ($params->expirationInverse) {
			$object = $object->where("o.date_expired", ">", DB::expr("NOW()"));
		}

		if ($params->type_tr) {
			$object = $object->where('o.type_tr', '=', (int) $params->type_tr);
		}

		if ($params->is_favorite) {
			$code = Cookie::get("code");
			if (!$code) $code = "sdaf980sd6fgsdfg9sdfgsd89076";
			$favorite_subquery = DB::select("objectid")
										->from("favorite")
										->where("code", "=", $code);
			$object = $object->where("o.id","IN",$favorite_subquery);
		}

		$multimedia_filter = array();
		$photo_types = array(0, 4);
		$video_types = array(2, 3);

		if ($params->photo) {
			$multimedia_filter = array_merge($multimedia_filter, $photo_types);
		}

		if ($params->video) {
			$multimedia_filter = array_merge($multimedia_filter, $video_types);
		}

		if (count($multimedia_filter)) {
			$multimedia_subquery = DB::select("photo.object_id")
										->from(array("object_attachment","photo") )
										->where("photo.object_id","=", DB::expr("o.id"))
										->where("photo.type", "IN", $multimedia_filter)
										->limit(1);
			$object = $object->where(DB::expr('exists'), DB::expr(''), $multimedia_subquery);
		}

		$orgtype_filter = array();
		$private_types = array(1);
		$org_types = array(2);

		if ($params->private) {
			$orgtype_filter = array_merge($orgtype_filter, $private_types);
		}

		if ($params->org) {
			$orgtype_filter = array_merge($orgtype_filter, $org_types);
		}

		if (count($orgtype_filter)) {
			$orgtype_subquery = DB::select("userorg.id")
										->from(array("user","userorg") )
										->where("userorg.id","=", DB::expr("o.author_company_id"))
										->where("userorg.org_type", "IN", $orgtype_filter)
										->limit(1);
			$object = $object->where(DB::expr('exists'), DB::expr(''), $orgtype_subquery);
		}

		if ($params->without_attribute) {
			$without_attribute_subquery = DB::select("list.id")
										->from(array("data_list","list") )
										->where("list.object","=", DB::expr("o.id"))
										->where("list.attribute", "=", (int) $params->without_attribute);
			$object = $object->where(DB::expr('not exists'), DB::expr(''), $without_attribute_subquery);
		}

		
		$filters = self::get_filters_by_params($params->filters, "o");
		foreach ($filters as $filter)
			$object = $object->where(DB::expr('exists'), DB::expr(''), $filter);

		if (!$options->count and !$options->group_category) {
			$object = $object->limit($limit);

			$object = $object->offset($limit*( ($page == 0)? 0: $page-1 ) );

		
			if (is_array($order)) {
				foreach ($order as $order_item) {
					$object = $object->order_by("o.".$order_item[0], $order_item[1]);
				}
			} else {
				$object = $object->order_by("o.".$order, $order_direction);
			}
		}

		if ($options->group_category) {
			$object = $object->join("category","left")
                     			->on("o.category","=","category.id")
                     			->group_by( "category.title", "category.url" );
		}

		if ($params->search_text) {
			$city_id = $params->city_id ? $params->city_id : 0;
			$category_id = $params->category_id ? $params->category_id : 0;

			$sphinx_result = Sphinx::search($params->search_text, $category_id, $city_id, FALSE, NULL, 0, 1000);
			$objects = Sphinx::getObjects($sphinx_result);
			$ids = implode(",",$objects);
			$object = $object->where("o.id","IN",DB::expr("(".$ids.")"));
		}

		return $object;
	}

	public static function getresult($results =  array())
	{
		$result = array();
		foreach ($results as $object) {
			$result[] = array_merge($object, array("compiled" => self::getresultrow($object)) );
		}

		return $result;
	}

	public static function getresultrow($object)
	{
		$compiled = array();
		if ( array_key_exists("compiled", $object)) {
			$compiled = unserialize($object["compiled"]);
		}
		if (!$compiled) {
			$compiled = array();
		}

		if ( array_key_exists("images", $compiled)) {
			$compiled["images"] = Object_Compiled::getImages( $compiled["images"] );
		}

		if ( array_key_exists("attributes", $compiled)) {
			$compiled["attributes"] = Object_Compiled::getAttributes( $compiled["attributes"] );
		}

		if ( array_key_exists("author", $compiled)) {
			if ($compiled["author"]["filename"]) {
				$compiled["author"]["logo"] = Imageci::getSavePaths($compiled["author"]["filename"]);
			}
		}
		return $compiled;
	}

	public static function count_estimate($sql, $table_name = NULL, $limit_str = NULL)
	{
		$sql = self::prepare_sql($sql, $table_name, $limit_str);
		$query = DB::select(DB::expr("count_estimate('SELECT * FROM $table_name $sql') as count"))->execute()->current("count_estimate");
		return $query;
	}

	public static function prepare_sql($sql = "", $table_name = "\"vw_objectcompiled\" AS \"o\"", $limit_str = "ORDER BY")
	{
		preg_match("/FROM $table_name (.*) $limit_str/", $sql, $match);
		if (count($match) == 2){
			return $match[1];
		}
	}

}

/* End of file Search.php */
/* Location: ./application/classes/Search.php */