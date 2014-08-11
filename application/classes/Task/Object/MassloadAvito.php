<?php defined('SYSPATH') or die('No direct script access.');

class Task_Object_MassloadAvito extends Minion_Task
{
	const STEP = 1;
	const SETTING_NAME = "massload_link";

	protected $_options = array(
		'link'	=> FALSE,
		'user'  => FALSE,
		'category'	=> FALSE,
		'send_email'=> FALSE
	);

	protected function _execute(array $params)
	{
		$link 				= $params['link'];
		$user_id 			= $params['user'];
		$direct_category 	= $params['category'];
		$send_email 	= $params['send_email'];

		$settings = Array();
		if ($link AND $user_id)
		{
			$settings[] = array("user_id"=>$user_id, "link"=>$link);
		} else {
			$user_settings = ORM::factory('User_Settings')->where("name","=",self::SETTING_NAME)->order_by("id","desc")->find_all();
			foreach ($user_settings as $setting)
				$settings[] = array("user_id"=>$setting->user_id, "link"=>$setting->value);
		}

		foreach ($settings as $setting)
		{
			try {

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

					$object_count = 0;
					$edited_count = 0;
					$error_count  = 0;
					$error_adverts = Array();

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
								$object_count++;
								if ($advert["is_edit"]){
									$edited_count++;	
								}
							} else {
								$error_count++;
								$error_adverts[] = $advert["external_id"];
							}
							$error = ' error:'.Minion_CLI::color(Debug::vars( $advert["error"]), 'yellow');
							$external_id = ' external_id:'.Minion_CLI::color($advert["external_id"], 'cyan');
							Minion_CLI::write($object_id.$parent_id.$is_edit.$error.$external_id);
						}
					}
					$config = Kohana::$config->load('massload/bycategory.'.$category);
					$mail_message = 'Отчет по загрузке файла для компании : '.$user->org_name.' ('.$user->id.' '.$user->email.')</br>';
					$mail_message .= 'Ссылка на файл : '.$link.' </br>';
					$mail_message .= 'Категория : '.$config["name"].' </br>';
					$mail_message .= 'Результат : </br>';
					$mail_message .= '- всего обработано объявлений : '.$object_count.'</br>';
					$mail_message .= '- обновлено объявлений : '.$edited_count.'</br>';
					$mail_message .= '- объявлений с ошибками (не были загружены) : '.$error_count.'</br>';	
					if ($send_email) {
						Email::send(array($user->email), Kohana::$config->load('email.default_from'), 'Отчет по загрузке объявлений на сайт "Ярмарка-онлайн"', $mail_message);
					}
					//$mail_message .= '- ID объявлений с ошибками : '.join(', ',$error_adverts).'</br>';
					try {
						Email::send(Kohana::$config->load('common.admin_emails'), Kohana::$config->load('email.default_from'), 'Отчет по загрузке объявлений', $mail_message);
					} catch(Exception $ee){}
				} //end foreach by category
			} 
				catch(Exception $e)
			{
				$exception_message  = 'Ошибки при массовой загрузке: </br>';
				$exception_message .= 'message: '.($e->getMessage()).'</br>';
				$exception_message .= 'input_params: '.Debug::vars($setting).'</br>';
				$exception_message .= 'stack: '.($e->getTraceAsString()).'</br>';
				try {
				Email::send(Kohana::$config->load('common.admin_emails'), Kohana::$config->load('email.default_from'), 'Ошибки при массовой загрузке', $exception_message);
				} catch(Exception $eee){}
				Minion_CLI::write('critical error: '.Minion_CLI::color($e->getMessage(), 'cyan'));
			}

			
		} //end foreach by $links

		//Minion_CLI::write('Start signature loading:'.Minion_CLI::color($total, 'cyan'));

		
	}



}
