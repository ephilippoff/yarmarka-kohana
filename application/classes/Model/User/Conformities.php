<?php defined('SYSPATH') OR die('No direct access allowed.');


class Model_User_Conformities extends ORM {

	protected $_table_name = 'user_conformities';


	function delete_conformity($user_id, $massload, $type, $value)
	{
		return DB::delete('user_conformities')
				->where('type', '=', $type)
				->where('value', '=', $value)
				->where('user_id', '=', $user_id)
				->where('massload', '=', $massload)
			->execute();
	}

} // End User_Types Model
