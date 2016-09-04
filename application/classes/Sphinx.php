<?php defined('SYSPATH') or die('No direct script access.');

class Sphinx {
	public static $_prefix = "";

	function __construct()
	{
		include APPPATH.'classes/Sphinxapi.php';
		Sphinx::$_prefix = Kohana::$config->load("common.sphinx_prefix");
	}

	public static function search($_keywords, $category_id = 0, $city_id = 0, $save = FALSE, $object_id = 0, $offset = 0, $limit = 0)
	{

		$keywords = Sphinx::GetSphinxKeyword($_keywords);

		$objects = Sphinx::searchObjects($keywords, $category_id, $city_id, $offset, $limit);
		$pricerows = Sphinx::searchPricerow($keywords, $category_id, $city_id, $object_id, $offset, $limit);

		$objectsFound = $objects["total_found"];	
		$pricerowFound = $pricerows["total_found"];	

		/*if ($save)
			$CI->Search_m->saveQuery($_keywords, $objectsFound, $pricerowFound, $category_id, $city_id);
*/
		$objects["mode"] = "default";
		$pricerows["mode"] = "default";


		$result = array(
			"objects" => $objects,
			"objects_result_info" => Sphinx::getObjectResultDescription($objects),
			"pricerows" => $pricerows,
			"pricerows_result_info" => Sphinx::getPricerowsResultDescription($pricerows),
			"common_result_info" => Sphinx::getResultDescription($objects, $pricerows)
		);

		

		return $result;
	}

	public function searchGroupByCategory($_keywords, $city_id = 0, $category_id = 0)
	{
		$keywords = Sphinx::GetSphinxKeyword($_keywords);
		$objects = Sphinx::searchObjects($keywords, $category_id, $city_id, 0, 0, "category");
		$objectsFound = $objects["total_found"];

		$result = array(
			"categories" => self::getCategories($objects),
			"found" => $objectsFound
		);

		return $result;
	}

	public function searchGroupByCity($_keywords, $city_id = 0, $category_id = 0)
	{
		$keywords = Sphinx::GetSphinxKeyword($_keywords);
		$objects = Sphinx::searchObjects($keywords, $category_id, $city_id, 0, 0, "city");
		$objectsFound = $objects["total_found"];

		$result = array(
			"cities" => self::getCities($objects),
			"found" => $objectsFound
		);

		return $result;
	}

	public static function searchObjects($keywords, $category_id = 0, $city_id = 0, $offset = 0, $limit = 0, $groupby = FALSE)
	{
		$mode = SPH_MATCH_EXTENDED2;

		$sphinx = new SphinxClient();
		$sphinx->SetServer ( '127.0.0.1', 9312 ); 		
		$sphinx->SetConnectTimeout (3);
		$sphinx->SetArrayResult ( true );		
		$sphinx->SetMatchMode($mode);
		$sphinx->SetFieldWeights(array (
				'city_title' => 50,
				'cat_title' => 40,
				'full_text' => 90
		));
		
		if ($groupby == "category") {
			$sphinx->SetGroupBy('category', SPH_GROUPBY_ATTR,"@count desc");
		} elseif ($groupby == "city") {
			$sphinx->SetGroupBy('city_id', SPH_GROUPBY_ATTR);
		} else {
			$sphinx->SetLimits ( $offset, $limit );
			$sphinx->setSortMode (SPH_SORT_RELEVANCE, "date_created" ); 
		}

		$object_index_name = "yarmarka".Sphinx::$_prefix;

		if (is_array($category_id)) {
			$sphinx->SetFilter('category', $category_id);
		} elseif($category_id>0){
			$sphinx->SetFilter('category', array($category_id));
		}

		if (is_array($city_id)) {
			$sphinx->SetFilter('city_id', $city_id);
		} elseif($city_id>0){
			$sphinx->SetFilter('city_id', array($city_id));
		}

		return $sphinx->Query("@* ".$keywords, $object_index_name);
	}

	public static function searchPricerow($keywords, $category_id = 0, $city_id = 0, $object_id = 0,$offset = 0, $limit = 0)
	{
		$mode = SPH_MATCH_EXTENDED2;

		$sphinx = new SphinxClient();
		$sphinx->SetServer ( '127.0.0.1', 9312 ); 		
		$sphinx->SetConnectTimeout (3);
		$sphinx->SetArrayResult ( true );
		$sphinx->SetMatchMode($mode);

		$sphinx->SetLimits ( $offset, $limit );
		$sphinx->SetSortMode ( SPH_SORT_RELEVANCE );

		$pricelist_index_name = "yarmarka_pricelist".Sphinx::$_prefix;

		if (is_array($category_id)) {
			$sphinx->SetFilter('category_id', $category_id);
		} elseif($category_id>0){
			$sphinx->SetFilter('category_id', array($category_id));
		}
		
		if ($city_id > 0){
			$sphinx->SetFilter('city_id', array($city_id));
		}

		if ($object_id > 0){
			$sphinx->SetFilter('object_id', array($object_id));
		}

		if ($offset > 0)
			$sphinx->setLimits($offset, $limit);

		return $sphinx->Query($keywords, $pricelist_index_name);

	}

	public static function getObjects($result)
	{
		$result = $result["objects"];
		$ids = array(0);
		if($result && is_array(@$result["matches"])) {
			foreach ($result['matches'] as $match) {
				$ids[] = $match['id'];
			}
		}
		return $ids; 
	}

	public static function getCategories($result)
	{
		$objects = array();
		$object_pricerows = array();
		if($result && is_array(@$result["matches"])) {
			foreach ($result['matches'] as $match) {
				$object = $match['attrs'];
				$objects[] = new Obj(array(
					"id" => $object["category"],
					"title" => $object["cat_title"],
					"url" => $object["cat_url"],
					"count" => $object["@count"]
				));

			}
		}

		return $objects;
	}

	public static function getCities($result)
	{
		$objects = array();
		$object_pricerows = array();
		if($result && is_array(@$result["matches"])) {
			foreach ($result['matches'] as $match) {
				$object = $match['attrs'];
				$objects[] = new Obj(array(
					"id" => $object["city_id"],
					"title" => $object["city_title"],
					"seo_name" => $object["city_seo_name"],
					"count" => $object["@count"]
				));

			}
		}

		return $objects;
	}

	public static function getPricerows($_result, $city_id)
	{
		$result = $_result["pricerows"];
		$pricerows = array();
		$object_pricerows = array();
		if($result && is_array(@$result["matches"])) {
			foreach ($result['matches'] as $match) {
				$pricerow = $match['attrs'];
				$pricerow["city_name"] = "";
				if ($city_id > 0 AND $pricerow["city_id"])
				{
					$cityrow = ORM::factory('City',$pricerow["city_id"]);
					if ($cityrow->loaded())
						$pricerow["city_name"] = $cityrow->title;
				}
				if ($pricerow["full"])
				{
					$pricerow["values"] = unserialize($pricerow["full"]);
				}
				$pricerows[] = $pricerow;

				if (array_key_exists($pricerow["object_id"], $object_pricerows))
					$object_pricerows[$pricerow["object_id"]][] = $pricerow;
				else
					$object_pricerows[$pricerow["object_id"]] = array();
			}
		}

		return $pricerows;
	}

	public static function getObjectPricerows($_result, $count = 5)
	{
		$counter = 1;
		$result = $_result["pricerows"];
		$object_pricerows = array();
		if($result && is_array(@$result["matches"])) {
			foreach ($result['matches'] as $match) {
				$pricerow = $match['attrs'];

				if (array_key_exists($pricerow["object_id"], $object_pricerows))
				{
					$object_pricerows[$pricerow["object_id"]][] = $pricerow;
					$counter++;
					if ($counter >$count)
						break;
				}
				else
					$object_pricerows[$pricerow["object_id"]] = array();

				
				
			}
		}

		return $object_pricerows;
	}

	public static function getResultDescription($objects, $pricerows)
	{

		$result = array();
		if ($objects)
			$result[] = Sphinx::getObjectResultDescription($objects);

		if ($pricerows)
			$result[] = Sphinx::getPricerowsResultDescription($pricerows);

		return "Найдено: ".implode(", ", $result);
	}

	public static function getObjectResultDescription($result)
	{
		if (!array_key_exists("total", $result))
			return "";

		$founded = $result["total"];
		$mode    = $result["mode"];

		$result_string = $founded." объявлений";

		return $result_string;
	}

	public static function getPricerowsResultDescription($result)
	{
		if (!array_key_exists("total", $result))
			return "";

		$founded = $result["total"];
		$mode    = $result["mode"];

		$result_string = $founded." позиций в прайc-листах";

		return $result_string;
	}

	private static function GetSphinxKeyword($sQuery)
	{
		mb_internal_encoding("UTF-8");
		//$sQuery = Text::remove_symbols($sQuery);
		$aKeyword = array();
		$aRequestString=explode(' ', $sQuery);

		if ($aRequestString) {
			foreach ($aRequestString as $sValue)
			{
				if (mb_strlen($sValue)>2)
				{
					$aKeyword[] .= "(".$sValue." | *".$sValue."*)";
				}
			}
			$sSphinxKeyword = implode(" & ", $aKeyword);
		}

		return $sSphinxKeyword;
	}
};