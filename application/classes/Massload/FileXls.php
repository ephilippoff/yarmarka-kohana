<?php defined('SYSPATH') or die('No direct script access.');

class Massload_FileXls extends Massload_File
{
	private static function openFile($pathtofile)
	{
		return Spreadsheet::factory(
		          array(
		                    'filename' => $pathtofile
		          ), FALSE)
		          ->load()
		          ->read();
	}

	private static function closeFile($file_instance)
	{

	}

	public static function forEachRow($config, $pathtofile, $callback)
	{
		$file = self::openFile($pathtofile);
		$i = 0;
		foreach ($file as $item)
		{
			if ($i == 0) {$i++;continue;}
			$values = array_values($item);
			$row = new Obj((array) $values);
			$row = self::to_assoc_object($row, $config);				
			$row = self::clear_row($row);
			$return = $callback($row, $i);
			$i++;

			if ($return == 'break') break;
			if ($return == 'continue') continue;
			
			
		}
		unset($file);
	}

	public static function forRow($config, $pathtofile, $row_num, $callback)
	{
		$file = self::openFile($pathtofile);

		$values = array_values($file[$row_num]);
		$row = new Obj((array) $values);
		$row = self::to_assoc_object($row, $config);	
		$row = self::clear_row($row);
		$return = $callback($row, $row_num);

		unset($file);
	}

	public static function forAll($pathtofile, $callback)
	{
		$file = self::openFile($pathtofile);
		$i = 0;
		foreach ($file as $key=>$item )
		{

			$row = new Obj((array) $item);
			$return = $callback($row, $i);
			$i++;
			if ($return == 'break') break;
			if ($return == 'continue') continue;
					
		}
		unset($file);
	}

}