<?php defined('SYSPATH') or die('No direct script access.');

class Task_DeleteObjects extends Minion_Task
{
	protected $_options = array(
		'limit' => 10,
	);

	protected function _execute(array $params)
	{
		$limit 	= $params['limit'];

		Minion_CLI::write('Delete inactive objects (active = 0)');
		$objects = ORM::factory('Object')
			->where('active', '=', 0)
			->order_by("date_created", "desc")
			->limit($limit)
			->find_all();

		Minion_CLI::write('Affected rows:'.Minion_CLI::color($objects->count(), 'cyan'));
		foreach ($objects as $object)
		{
			$attachments = ORM::factory('Object_Attachment')
				->where('object_id', '=',  $object->id)
				->where('type','=',0)
				->find_all();
			$missed_count = 0;
			Minion_CLI::write('Finded attachments:'.Minion_CLI::color($attachments->count(), 'cyan'));
			foreach ($attachments as $attachment) {
				$sizes_finded = array();
				$sizes_deleted = array();
				foreach (Imageci::getSitePaths($attachment->filename) as $key => $_filename) {
					array_push($sizes_finded, $key);
					$filename = "./".$_filename;
					if (file_exists($filename) AND !is_dir($filename)) {
						array_push($sizes_deleted, $key);
						unlink($filename);
						if (file_exists($filename)) {
							Minion_CLI::write('!!!!!!!!!!!!!!!!!!!!!!!!!');
						}
					}
				}

				if (count($sizes_deleted)  AND join($sizes_finded,",") <> join($sizes_deleted,",")) {
					Minion_CLI::write('sizes finded:'.Minion_CLI::color(join($sizes_finded,","), 'red'));
					
				}
				if (count($sizes_deleted)) {
					Minion_CLI::write('sizes deleted:'.Minion_CLI::color(join($sizes_deleted,","), 'brown'));
				} else {
					$missed_count++;
				}
				
				$attachment->delete();
			}
			Minion_CLI::write('missed delete:'.Minion_CLI::color($missed_count, 'brown'));
			$object->delete();
		}

	}
}