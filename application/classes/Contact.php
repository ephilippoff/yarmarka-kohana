<?php defined('SYSPATH') OR die('No direct script access.');

class Contact
{
	/**
	 * Проверяет верифицирован контакт или нет,
	 * по умолчанию для текущего пользоватля
	 * 
	 * @param  string  $contact_str
	 * @return boolean
	 */
	public static function is_verified($contact_id, $user = NULL)
	{
//		$CI =& get_instance();
//		$CI->load->library('validation');
//		$CI->load->model(array('Contacts_m', 'Verified_contacts_m'));
//
//		$contact = $CI->Contacts_m->get_by_id($contact_id);
//		if ( ! $contact)
//		{
//			return FALSE;
//		}
//
//		if ( ! $user)
//		{
//			$user = User_m::GetCurrentUser();
//		}
//
//		if ($user AND (int) $contact->verified_user_id === (int) $user->id)
//		{
//			return TRUE;
//		}
//
//		return (bool) $CI->Verified_contacts_m->get_by_session_and_contact(session_id(), $contact->id);
	}

	public static function clear_phone_number($contact)
	{
		return preg_replace('/[^0-9]*/', '', $contact);
	}

	public static function is_phone_contact_type($contact_type_id)
	{
		return in_array(intval($contact_type_id), array(1,2), TRUE);
	}

	public static function format_phone($phone)
	{
		$phone = self::clear_phone_number($phone);

		if (strpos($phone, '79') === 0)
		{
			// mobile phone
			return '+'.$phone[0].' ('.substr($phone, 1, 3).') '.substr($phone, 4, 3).'-'.substr($phone, 7, 2).'-'.substr($phone, 9, 2);
		}
		else
		{
			// home phone
			return '+'.$phone[0].' ('.substr($phone, 1, 4).') '.substr($phone, 5, 2).'-'.substr($phone, 7, 2).'-'.substr($phone, 9);
		}
	}

	public static function hide($contact)
	{
		if (strpos($contact, '@'))
		{
			$contact = preg_replace("/(.*)@/", '...@', $contact);
		}
		elseif (preg_match("/[0-9]{0,15}/", $contact))
		{
			$contact = self::format_phone($contact);
			$contact = preg_replace("/\)(.*)/", ') XX-XX-XX', $contact);
		}
		else
		{
			// @todo скрывать скайп, icq и другие контакты?
		}

		return $contact;
	}
	

}

/* End of file contact_helper.php */
/* Location: ./application/helpers/contact_helper.php */