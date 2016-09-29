<?php defined('SYSPATH') OR die('No direct access allowed.');


class Model_Favourite extends ORM {

	protected static $secret = 'secret_#ode123ocvxvhias0a!&sdf';
	protected $_table_name = 'favorite';

	function saveget_by_cookie($id) {

		$code = Cookie::get("code");

		if (!$code) {
			$code = sha1(session_id().self::$secret);
			Cookie::set("code", $code, strtotime( '+90 days' ));
		}

		$user = Auth::instance()->get_user();

		$favourites = ORM::factory('Favourite')->where("objectid", "=", (int) $id);

		if ($user) {
			$favourites = $favourites->where_open()
								->where("code", "=", $code)
								->or_where('userid', '=', $user->id)
							->where_close();

				
		} else {
			$favourites = $favourites->where("code", "=", $code);
		}
		$favourites_ = clone $favourites;

		if ( count($favourites->find_all()) ) {
			$favourites_->delete_all();
			return FALSE;
		}

		$favourite = ORM::factory('Favourite');
		$favourite->code = $code;
		$favourite->objectid = $id;
		if ($user ) {
			$favourite->userid = $user->id;
		}
		$favourite->save();

		return $favourite->get_row_as_obj(array("id"));
	}

	function get_list_by_cookie() {

		$code = Cookie::get("code");

		if (!$code) {
			return array();
		} else {

			$favourites = ORM::factory('Favourite');

			if (Auth::instance()->get_user()) {

				$userid = Auth::instance()->get_user()->id;
				$favourites = $favourites->where_open()
											->where("code", "=", $code)
											->or_where('userid', '=', $userid)
										 ->where_close();

			} else {

				$favourites = $favourites->where("code", "=", $code);

			}
			
								
			$favourites = $favourites->order_by("id")
									->limit(100)
									->getprepared_all(array("objectid"));

			return array_map(function($value) {
				return $value->objectid;
			}, $favourites);
		}
	}

} // End Model_Edition Model
