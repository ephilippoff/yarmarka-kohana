<?php defined('SYSPATH') OR die('No direct script access.');

class Valid extends Kohana_Valid {
	/**
	 * Checks if a field is not 0
	 *
	 * @return  boolean
	 */
	public static function not_0($value)
	{
		return intval($value) !== 0;
	}

	public static function not_category_0($value)
	{
		return intval($value) !== 0;
	}

	public static function check_city_value($value, $dictionary)
	{
		if (array_key_exists("city_".$value, $dictionary)){
			$value = (int) $dictionary["city_".$value];//(int) ORM::factory('City')->by_title($value)->id;
		} else {
			$value = 0;
		}
		return intval($value) > 0;
	}

	public static function check_dictionary_value($value, $name, $dictionary)
	{
		if (array_key_exists($name."_".$value, $dictionary)){
			$value = (int) $dictionary[$name."_".$value];//ORM::factory('Attribute_Element')->by_value_and_attribute($value, $name)->find()->id;
		} else {
			$value = 0;
		}
		return intval($value) > 0;
	}

	public static function check_contact($number)
	{
		if (Valid::email($number))
		{
			$return = 5;
		} else 
		{
			$return = (int) ORM::factory('Contact_Type')->detect_contact_type_massload(Text::clear_phone_number($number));
		}
		if ($return == 1 OR $return == 2){
			if (strlen(Text::clear_phone_number($number)) <> 11)
				$return = 0;
		}
		return intval($return) > 0;
	}

	public static function check_photo($link, $path)
	{
		$type = NULL;
		if (filter_var($link, FILTER_VALIDATE_URL))
			$type = 'url';
		elseif (is_dir($path.$link."/"))
			$type = 'dir';
		elseif ( file_exists($path.$link) )
			$type = 'file';
		elseif ( $link == "0" OR $link == "" )
			$type = 'null';

		return isset($type);
	}

	public static function empty_contacts($contacts)
	{
		return FALSE;
		//return (count($this->contacts)>0) ? TRUE : FALSE;
	}

	public static function limit_object_for_user($user, $category, $object_id)
	{
		return ORM::factory('Category')->check_max_user_objects($user, $category, $object_id);
	}

	public static function not_empty_html($_value)
	{
		$value = strip_tags($_value);
		if (is_object($value) AND $value instanceof ArrayObject)
		{
			// Get the array from the ArrayObject
			$value = $value->getArrayCopy();
		}

		// Value cannot be NULL, FALSE, '', or an empty array
		return ! in_array($value, array(NULL, FALSE, '', array()), TRUE);
	}


	public static function login_exist($_email)
	{	
		$email = strtolower(trim($_email));
		return !ORM::factory('User')->get_user_by_email($email)->find()->loaded();
	}

	public static function valid_org_type($_type)
	{
		return ($_type == "1" OR $_type == "2");
	}

	public static function not_empty_photo($value)
	{
		return (is_array($value) AND $value["tmp_name"]);
	}

	public static function inn($inn)
	{
		if ( preg_match('/\D/', $inn) ) return false;
    
	    $inn = (string) $inn;
	    $len = strlen($inn);
	    
	    if ( $len === 10 )
	    {
	        return $inn[9] === (string) (((
	            2*$inn[0] + 4*$inn[1] + 10*$inn[2] + 
	            3*$inn[3] + 5*$inn[4] +  9*$inn[5] + 
	            4*$inn[6] + 6*$inn[7] +  8*$inn[8]
	        ) % 11) % 10);
	    }
	    elseif ( $len === 12 )
	    {
	        $num10 = (string) (((
	             7*$inn[0] + 2*$inn[1] + 4*$inn[2] +
	            10*$inn[3] + 3*$inn[4] + 5*$inn[5] + 
	             9*$inn[6] + 4*$inn[7] + 6*$inn[8] +
	             8*$inn[9]
	        ) % 11) % 10);
	        
	        $num11 = (string) (((
	            3*$inn[0] +  7*$inn[1] + 2*$inn[2] +
	            4*$inn[3] + 10*$inn[4] + 3*$inn[5] +
	            5*$inn[6] +  9*$inn[7] + 4*$inn[8] +
	            6*$inn[9] +  8*$inn[10]
	        ) % 11) % 10);
	        
	        return $inn[11] === $num11 && $inn[10] === $num10;
	    }
	    
	    return false;
	}

	public static function min_value($value, $title, $min_value = 0)
	{
		return (!$value OR $value > $min_value);
	}

	public static function max_value($value, $title, $max_value = 999999999)
	{
		return intval($value) < $max_value;
	}

	public static function captcha($value)
	{
		$value = mb_strtolower( trim($value) );
		return Captcha::valid($value);
	}

	public static function mobile_verified($value, $session_id, $contact = NULL)
	{
		$type_id = Model_Contact_Type::get_type_id("mobile");

		if (!$contact) {
			$contact = ORM::factory('Contact')->by_contact_and_type($value, $type_id)->find();
		}

		return $contact->is_verified($session_id);
	}

	public static function phone_verified($value, $session_id, $contact = NULL)
	{
		$type_id = Model_Contact_Type::get_type_id("phone");
		if (!$contact) {
			$contact = ORM::factory('Contact')->by_contact_and_type($value, $type_id)->find();
		}

		return $contact->is_verified($session_id);
	}

	public static function email_verified($value, $session_id, $contact = NULL)
	{
		$type_id = Model_Contact_Type::get_type_id("email");
		if (!$contact) {
			$contact = ORM::factory('Contact')->by_contact_and_type($value, $type_id)->find();
		}
		return $contact->is_verified($session_id);
	}

	public static function contact_blocked($value, $contact = NULL)
	{
		return $contact->is_blocked();
	}

	public static function mobile_sms_phonemax($value, $session_id, $max_value = 2)
	{
		$sms_count = ORM::factory('Sms')->cnt_by_phone($value, $session_id);

		return ($sms_count + 1 < $max_value);
	}

	public static function mobile_sms_sessionmax($value, $session_id, $max_value = 10)
	{
		$sms_count = ORM::factory('Sms')->cnt_by_session_id($session_id);

		return ($sms_count + 1 < $max_value);
	}

	public static function contact_already_verified($value, $contact, $email = "")
	{
		$current_user 		= Auth::instance()->get_user();

		if ($current_user AND $contact->verified_user_id AND $contact->verified_user_id <> $current_user->id)
		{
			return FALSE;
		} elseif (!$current_user AND $contact->verified_user_id) {
			return FALSE;
		}

		return TRUE;
	}

	public static function is_mobile_contact($value)
	{
		$value = trim(strtolower(Text::clear_phone_number($value)));
		return (bool) preg_match('/^79\d{9}$/', $value);
	}

	public static function is_city_contact($value)
	{
		$value = trim(strtolower(Text::clear_phone_number($value)));
		return (!Valid::is_mobile_contact($value) AND preg_match('/^7\d{10}$/', $value));
	}

	public static function is_email_contact($value)
	{
		$value = trim(strtolower($value));
		return Valid::email($value);
	}
}
