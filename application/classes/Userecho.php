<?php

class Userecho
{
	public static function get_sso_token($user)
	{
		if (!$user) return '';
		
		$api_key = "e72b437ba582bf1dc610caba5d6316a7"; // Your project personal api key
		$project_key = "yarmarka"; // Your project alias

		$name = $user->org_type == 2 ? trim($user->org_name) : trim($user->fullname);
		if (!$name) $name = 'Пользователь №'.$user->id;
		
		$message = array(
			"guid"			=> $user->id, // User ID in your system - using for identify user in next time (first time auto-registion)
			"expires_date"	=> gmdate("Y-m-d H:i:s", time()+(86400)), // sso_token expiration date in format "Y-m-d H:i:s". Recommend set date now() + 1 day
			"display_name"	=> $name, // User display name in your system
			"email"			=> $user->email, // User email - using for notification about changes on feedback
			"locale"		=> "ru", // Default user language
			"avatar_url"	=> "http://test.com/1234.png" // NOT USED NOW. user avatar URL
			);

		// random bytes value, length = 16
		// Recommend use random to genetare $iv
		$iv  = '$%^ty^Y&563456GH';

		// key hash, length = 16
		$key_hash = substr( hash('sha1', $api_key.$project_key, true), 0, 16);
		// if you use mb_string functions, try it  
		//$key_hash = mb_substr( hash('sha1', $api_key.$project_key, true), 0, 16, 'Windows-1251')

		$message_json = json_encode($message);

		// double XOR first block message_json
		for ($i = 0; $i < 16; $i++)
		 $message_json[$i] = $message_json[$i] ^ $iv[$i];

		// fill tail of message_json by bytes equaled count empty bytes (to 16)
		$pad = 16 - (strlen($message_json) % 16);
		$message_json = $message_json . str_repeat(chr($pad), $pad);

		// encode json
		$cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128,'','cbc','');
		mcrypt_generic_init($cipher, $key_hash, $iv);
		$encrypted_bytes = mcrypt_generic($cipher,$message_json);
		mcrypt_generic_deinit($cipher);

		// encode bytes to url safe string
		return urlencode(base64_encode($encrypted_bytes));
	}
}