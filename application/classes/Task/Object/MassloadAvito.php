<?php defined('SYSPATH') or die('No direct script access.');

class Task_Object_MassloadAvito extends Minion_Task
{
	const STEP = 1;

	protected $_options = array(
		'link'	=> FALSE,
		'user'  => FALSE,
		'category'	=> FALSE,
	);

	protected function _execute(array $params)
	{
		$link 				= $params['link'];
		$user_id 			= $params['user'];
		$direct_category 	= $params['category'];

		$settings = Array();
		if ($link AND $user_id)
		{
			$settings[] = array("user_id"=>$user_id, "link"=>$link);
		} else {
			$user_settings = ORM::factory('User_Settings')->where("name","=","massload_link")->order_by("id","desc")->find_all();
			foreach ($user_settings as $setting)
				$settings[] = array("user_id"=>$setting->user_id, "link"=>$setting->value);
		}

		foreach ($settings as $setting)
		{
			$link 		= $setting["link"];
			$user_id 	= $setting["user_id"];
			$user 		=  ORM::factory('User', $user_id);
			Minion_CLI::write('user role: '.$user->role);
			Auth::instance()->force_login($user);

			$tmp = tempnam("/tmp", "imgurl");
			copy($link, $tmp);

			$pathtofile = $tmp;
			Minion_CLI::write('filexist:'.file_exists($pathtofile));
			
			$ml = new Massload;
			$avito = new Massload_Avito;
			$files = $avito->convert_file($pathtofile);

			foreach ($files as $category=>$filepath)
				Minion_CLI::write('Next file converted:'.Minion_CLI::color($filepath, 'cyan'));

			if ($direct_category)
			{
				Minion_CLI::write('Next category would be loaded:'.Minion_CLI::color($direct_category, 'green'));
				$files = Array( $direct_category => $files[$direct_category]);
			}

			foreach ($files as $category=>$filepath){

				$_file = Array(
					'tmp_name' => $filepath,
					'size' => filesize($filepath),
					'name' => pathinfo($filepath, PATHINFO_FILENAME),
					'type' => mime_content_type($filepath),
				);

				@list($new_filepath, $imagepath, $errors, $count) = $ml->checkFile($_file, $category, $user->id);

				Minion_CLI::write('File after check. Count Errors :'.Minion_CLI::color(count($errors), 'cyan'));
				Minion_CLI::write('File after check. Errors :'.Minion_CLI::color(Debug::vars($errors), 'yellow'));
				Minion_CLI::write('File after check. Count Adverts :'.Minion_CLI::color($count, 'cyan'));

				$iteration = round($count/self::STEP);

				$ml->preProccess($new_filepath, $category, $user->id);

				for ($i = 0; $i<$iteration; $i++)
				{
					Minion_CLI::write("Loading...");
					$data =  $ml->saveStrings($new_filepath, $imagepath, $category, self::STEP, $i, $user->id);
					foreach ($data as $advert){
						$object_id = $parent_id = $is_edit = "";
						if (array_key_exists("object_id", $advert)){
							$object_id = ' object_id:'.Minion_CLI::color($advert["object_id"], 'cyan');
							$parent_id = ' parent_id:'.Minion_CLI::color($advert["parent_id"], 'cyan');
							$is_edit = ' is_edit:'.Minion_CLI::color($advert["is_edit"], 'cyan');
						}
						$error = ' error:'.Minion_CLI::color(Debug::vars( $advert["error"]), 'yellow');
						$external_id = ' external_id:'.Minion_CLI::color($advert["external_id"], 'cyan');
						Minion_CLI::write($object_id.$parent_id.$is_edit.$error.$external_id);
					}
				}
			} //end foreach by category
		} //end foreach by $links

		//Minion_CLI::write('Start signature loading:'.Minion_CLI::color($total, 'cyan'));

		
	}



}
