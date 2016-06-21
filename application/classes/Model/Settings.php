<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Settings extends ORM {

	const PREMOD = 'PREMOD';

	protected $_table_name = 'settings';


	public function isPremodEnabled() {
		$setting = $this->where('name','=',self::PREMOD)->find();
		if ($setting->loaded() AND $setting->value) {
			return TRUE;
		}
		return FALSE;
	}

	public function premodControl() {

		$setting = ORM::factory('Settings')->where('name','=',self::PREMOD)->find();

		$setting->name = self::PREMOD;
		$setting->value = ($setting->loaded() AND $setting->value) ? 0 : 1;
		$setting->save();

		return $setting->value;

	}


}