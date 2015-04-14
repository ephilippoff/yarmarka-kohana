<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Uploads extends ORM {
	protected $_table_name = 'uploads';

	public function build () {

		DB::insert('uploads', array('filename', 'tablename'))
					->select( self::get_subquery_object_attachment() )->execute();

		DB::insert('uploads', array('filename', 'tablename'))
					->select( self::get_subquery_userunits() )->execute();

		DB::insert('uploads', array('filename', 'tablename'))
					->select( self::get_subquery_article() )->execute();

		DB::insert('uploads', array('filename', 'tablename'))
					->select( self::get_subquery_user_settings() )->execute();

		DB::insert('uploads', array('filename', 'tablename'))
					->select( self::get_subquery__user_logo() )->execute();

		DB::insert('uploads', array('filename', 'tablename'))
					->select( self::get_subquery_userpage_banner() )->execute();

		DB::insert('uploads', array('filename', 'tablename'))
					->select( self::get_subquery_org_inn_scan() )->execute();
	}

	private static function get_subquery_userunits()
	{
		return DB::select("filename",DB::expr("'userunits'"))
					->from("user_units")
					->where("filename","IS NOT",DB::expr("NULL"))
					->where("filename","<>","");
	}

	private static function get_subquery_article()
	{
		return DB::select("photo",DB::expr("'article'"))->from("articles")->where("photo","IS NOT",DB::expr("NULL"))->where("photo","<>","");
	}

	

	private static function get_subquery_user_settings()
	{
		return DB::select("value",DB::expr("'user_settings'"))->from("user_settings")
								->where("name", "IN", array("INN_photo", "logo"));
	}

	private static function get_subquery_object_attachment()
	{
		return DB::select("filename",DB::expr("'obj_attach'"))->from("object_attachment")->where("type","=",0)->where("filename","<>","");
	}

	private static function get_subquery__user_logo()
	{
		return DB::select("filename",DB::expr("'user_log'"))->from("user")->where("filename","IS NOT",DB::expr("NULL"))->where("filename","<>","");
	}

	private static function get_subquery_userpage_banner()
	{
		return DB::select("userpage_banner",DB::expr("'userpage_banner'"))->from("user")->where("userpage_banner","IS NOT",DB::expr("NULL"))->where("userpage_banner","<>","");
	}

	private static function get_subquery_org_inn_scan()
	{
		return DB::select("org_inn_skan",DB::expr("'org_inn_skan'"))->from("user")->where("org_inn_skan","IS NOT",DB::expr("NULL"))->where("org_inn_skan","<>","");
	}
}