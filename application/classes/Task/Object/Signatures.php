<?php defined('SYSPATH') or die('No direct script access.');

class Task_Object_Signatures extends Minion_Task
{
	protected $_options = array(
		'limit'	=> 1000,
		'category' => 3
	);

	protected function _execute(array $params)
	{
		// $user 		=  ORM::factory('User', 327190);
		// Minion_CLI::write('user role: '.$user->role);
		// Auth::instance()->force_login($user);
		// $limit 		= $params['limit'];
		// $category 	= $params['category'];
		// $offset 	= 0;
		// $total 		=  ORM::factory('Object')
		// 						->where("category","=",$category)
		// 						->where("active","=",1)
		// 						->where("is_published","=",1)
		// 						->where("parent_id","", DB::expr('IS NULL'))
		// 						->where("author","", DB::expr('IS NOT NULL'))
		// 						//->limit($limit)
		// 						//->offset($offset)
		// 						->count_all();


		// Minion_CLI::write('Start signature loading:'.Minion_CLI::color($total, 'cyan'));

		// $objects = $this->get_objects($limit, $offset, $category);
		// $i = 0;

		// while (count($objects))
		// {
		// 	foreach ($objects as $object)
		// 	{
		// 		Minion_CLI::write('Обрабатываю: '.Minion_CLI::color($object->id, 'red'));
		// 		try {
		// 			$trigger_info = Object::PlacementAds_JustRunTriggers(Array("object_id" => $object->id, "only_run_triggers" => 1));
		// 		} catch (Exception $e){
		// 			Minion_CLI::write('Серьезная ошибка: '.Minion_CLI::color($e->getMessage(), 'brown'));
		// 		}
		// 		if (array_key_exists("object_id", $trigger_info))
		// 		{
		// 			if ((int) $trigger_info["parent_id"]>0)
		// 				Minion_CLI::write(
		// 					'Объединили объявление: '.Minion_CLI::color($object->id, 'yellow')." - ".Minion_CLI::color($trigger_info["parent_id"], 'green') 
		// 					);
		// 			else 
		// 				Minion_CLI::write('Уникальное: '.Minion_CLI::color($object->id, 'cyan'));

		// 		} else {
		// 			Minion_CLI::write('Ошибка: '.Minion_CLI::color($object->id, 'red')." = ".json_encode($trigger_info));
		// 		}
		// 	}

		// 	Minion_CLI::write('Processed: '.Minion_CLI::color($offset+count($objects).'/'.$total, 'cyan'));

		// 	$offset += $limit;
		// 	$objects = $this->get_objects($limit, $offset, $category);
		// }
	}

	public function get_objects($limit, $offset, $category)
	{
		return ORM::factory('Object')
					->where("category","=",$category)
					->where("active","=",1)
					->where("is_published","=",1)
					->where("parent_id","", DB::expr('IS NULL'))
					->where("author","", DB::expr('IS NOT NULL'))
					->order_by("date_created", "desc")
					->limit($limit)
					->offset($offset)
					->find_all();
	}
}

/* End of file Signatures.php */
/* Location: ./application/classes/Task/Object/Signatures.php */