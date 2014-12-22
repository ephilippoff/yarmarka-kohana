<?=View::factory('add/index',array(
		"object"	=> $object,
		"params" 	=> $params,
		"form_data" => $form_data,
		"errors" 	=> $errors,
		"assets" 	=> $assets,
		"expired_orginfo" =>$expired_orginfo,
		"token" => $token,
		"user" => $user
	))?>