<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Reklama extends ORM {

	protected $_table_name = 'reklama';
	
	public function rules()
	{
		return array(
			'title' => array(
				array('not_empty'),
			),
			'link' => array(
				array('not_empty'),
			),
		);
	}

	public function filters()
	{
		return array(
			'title' => array(
				array('trim'),
			),
			'seo_name' => array(
				array('trim'),
			),
		);
	}

	public function apply_service($object, $params)
    {
    	
    	  
    	$quantity = $params->service->quantity;
    	$category = $params->service->category;
    	$text = $params->service->text;
    	$image = $params->service->image;

    	$categories = array(
    	    'tg1' => 1,
    	    'tg2' => 2,
    	    'tg3' => 3,
    	    'tg4' => 5,
    	    'tgmain' => 4
    	);

    	$in = '('.join(',', array($categories[$category])).')';

    	$rgcategories = ORM::factory('Reklama_Group_Category')->where('group_id', 'in', DB::expr($in))->find_all()->as_array('id', 'category_id');
    	//Оставляем уникальные id категорий
    	$rgcategories = array_unique($rgcategories);
    	//Если есть категории

    	ORM::factory('Reklama')->values(array(
    	    "title"=> $text,
    	    "object_title"=> $object->title,
    	    "link"=>"/detail/".$object->id,
    	    "class"=> $image,
    	    "cities" =>"{".$object->city_id."}",
    	    "groups" =>"{".$categories[$category]."}",
    	    "comments" => "auto",
    	    "active" => 1,
    	    "type" => 3,
    	    "categories" => '{'.join(',', $rgcategories).'}',
    	    "start_date"=>DB::expr("NOW()"),
    	    "end_date"=>DB::expr("NOW() + interval '$quantity weeks'")
    	))->save();

    }

}