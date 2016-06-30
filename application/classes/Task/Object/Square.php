<?php defined('SYSPATH') or die('No direct script access.');

class Task_Object_Square extends Minion_Task
{

	protected $_options = array(
		'limit'	=> 10000
	);

	protected function _execute(array $params)
	{
		$limit = $params['limit'];

		$square_attributes = array(
			"ploshchad",
			"ploshchad-2x2",
			"ploshchad-uchastka-v-sotkakh"
		);

		$s = DB::select('data_numeric.object')
				->from('data_numeric')
				->join('attribute')
					->on('data_numeric.attribute','=','attribute.id')
				->where('attribute.seo_name','IN',$square_attributes);

		$not_s = DB::select('data_integer.object')
				->from('data_integer')
				->join('attribute')
					->on('data_integer.attribute','=','attribute.id')
				->where('attribute.seo_name','=', 'price-per-square');

		$objects = ORM::factory('Object')
						->where('active','=',1)
						->where('is_published','=',1)
						//->where('city_id','=',1919)
						->where('id','IN',$s)
						->where('id','NOT IN',$not_s)
						->limit($limit)->order_by('id','desc')->getprepared_all();

		foreach ($objects as $object) {

			$value = ORM::factory('Data_Integer')->save_price_per_square($object->id, $object->category);

			Minion_CLI::write($object->id.': '.Minion_CLI::color(($value) ? $value->value_min : $value, 'cyan'));

			$oc = ORM::factory('Object_Compiled')
					->where("object_id","=",$object->id)
					->find();
			$compiled = array();
			if ($oc->loaded()) {
				$compiled = unserialize($oc->compiled);

				$compiled = array_merge($compiled, Object_Compile::getAttributes($object));

				$oc->object_id = $object->id;
				$oc->compiled = serialize($compiled);
				$oc->save();
			}
		}

	}

}