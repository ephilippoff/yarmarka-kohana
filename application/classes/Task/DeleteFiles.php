<?php defined('SYSPATH') or die('No direct script access.');

class Task_DeleteFiles extends Minion_Task
{
	protected $_options = array(
		'limit' => 10,
		'remove' => FALSE
	);

	private $count = 0;
	private $total = 0;


	private function get_files($dir = "./uploads/orig", $limit, $callback, $check){
		$stop = FALSE;
		if ($handle = opendir($dir)) {
			while (false !== ($item = readdir($handle)) AND !$stop) {
				$filename = "$dir/$item";
				//Minion_CLI::write('Full Path :'.Minion_CLI::color($filename, 'yellow'));
				if (is_file($filename)) {
					$stop = $callback($item, $dir, $check);
					//$files[] = "$dir/$item";
				} elseif (is_dir($filename) && ($item != ".") && ($item != "..")){
					$this->get_files($filename, $limit, $callback, $check);
					if (!count(glob($filename.'/*') )) {
						//Minion_CLI::write('EmptyDir '.$filename);
						rmdir($filename);
					}
				}
			}
			closedir($handle);
		}
	}

	function get_all_files($limit, $callback) {
		$sizes = Imageci::getSizes();
		$sizes[] = "orig";
		arsort($sizes);
		foreach ($sizes as $size) {
			Minion_CLI::write('size '.$size. ' count = '.Minion_CLI::color($this->count, 'yellow'). ' total = '.Minion_CLI::color($this->total, 'yellow'));
			$check = ($size == "orig");
			$this->get_files($dir = "./uploads/$size", $limit, $callback, $check);
		}
	}

	public static function check_userunits($filename)
	{
		$attachment = ORM::factory('User_Units')
								->where("filename","=",$filename)
								->find();
		return ($attachment->loaded() ? $attachment : FALSE);
	}

	public static function check_article($filename)
	{
		$attachment = ORM::factory('Article')
								->where("photo","=",$filename)
								->find();
		return ($attachment->loaded() ? $attachment : FALSE);
	}

	public static function check_org_inn_scan($filename)
	{
		$attachment = ORM::factory('User')
								->where("org_inn_skan","=",$filename)
								->find();
		return ($attachment->loaded() ? $attachment : FALSE);
	}

	public static function check_user_settings($filename)
	{
		$attachment = ORM::factory('User_Settings')
								->where("value","=",$filename)
								->where("name", "IN", array("INN_photo", "logo"))
								->find();
		return ($attachment->loaded() ? $attachment : FALSE);
	}

	public static function check_object_attachment($filename)
	{
		$attachment = ORM::factory('Object_Attachment')
								->where("filename","=",$filename)
								->find();
		return ($attachment->loaded() ? $attachment : FALSE);
	}

	public static function check_user_logo($filename)
	{
		$attachment = ORM::factory('User')
								->where("filename","=",$filename)
								->find();
		return ($attachment->loaded() ? $attachment : FALSE);
	}

	public static function check_userpage_banner($filename)
	{
		$attachment = ORM::factory('User')
								->where("userpage_banner","=",$filename)
								->find();
		return ($attachment->loaded() ? $attachment : FALSE);
	}

	protected function _execute(array $params)
	{
		$limit  = $params['limit'];
		$remove  = $params['remove'];

		$count = &$this->count;
		$total = &$this->total;

		$this->get_all_files(1, function($filename, $dir = '', $check) use ($limit, &$count, &$total, $remove){
			$total++;
			//if ($check) {
				if ( !Task_DeleteFiles::check_user_settings($filename)
						AND !Task_DeleteFiles::check_object_attachment($filename) 
							AND !Task_DeleteFiles::check_user_logo($filename) 
								AND !Task_DeleteFiles::check_userpage_banner($filename)
									AND !Task_DeleteFiles::check_org_inn_scan($filename)
										AND !Task_DeleteFiles::check_article($filename)
											AND !Task_DeleteFiles::check_userunits($filename)) {
					$count++;
					if ($remove) {
						Imageci::deleteImage($filename);
					} else {
						Imageci::moveImage($filename, 'to_delete');
					}
					Minion_CLI::write('Removed:'.Minion_CLI::color("[$count/$total] : ".$filename, 'cyan'));
				}
			//}
			return ($count >= $limit);
		});
	}

	//  Minion_CLI::write('Delete inactive objects (active = 0)');
	//  $objects = ORM::factory('Object')
	//      ->where('active', '=', 0)
	//      ->order_by("date_created", "desc")
	//      ->limit($limit)
	//      ->find_all();

	//  Minion_CLI::write('Affected rows:'.Minion_CLI::color($objects->count(), 'cyan'));
	//  foreach ($objects as $object)
	//  {
	//      $attachments = ORM::factory('Object_Attachment')
	//          ->where('object_id', '=',  $object->id)
	//          ->where('type','=',0)
	//          ->find_all();
	//      $missed_count = 0;
	//      Minion_CLI::write('Finded attachments:'.Minion_CLI::color($attachments->count(), 'cyan'));
	//      foreach ($attachments as $attachment) {
	//          $sizes_finded = array();
	//          $sizes_deleted = array();
	//          foreach (Imageci::getSitePaths($attachment->filename) as $key => $_filename) {
	//              array_push($sizes_finded, $key);
	//              $filename = "./".$_filename;
	//              if (file_exists($filename) AND !is_dir($filename)) {
	//                  array_push($sizes_deleted, $key);
	//                  unlink($filename);
	//                  if (file_exists($filename)) {
	//                      Minion_CLI::write('!!!!!!!!!!!!!!!!!!!!!!!!!');
	//                  }
	//              }
	//          }

	//          if (count($sizes_deleted)  AND join($sizes_finded,",") <> join($sizes_deleted,",")) {
	//              Minion_CLI::write('sizes finded:'.Minion_CLI::color(join($sizes_finded,","), 'red'));
					
	//          }
	//          if (count($sizes_deleted)) {
	//              Minion_CLI::write('sizes deleted:'.Minion_CLI::color(join($sizes_deleted,","), 'brown'));
	//          } else {
	//              $missed_count++;
	//          }
				
	//          $attachment->delete();
	//      }
	//      Minion_CLI::write('missed delete:'.Minion_CLI::color($missed_count, 'brown'));
	//      $object->delete();
	//  }

	// }
}