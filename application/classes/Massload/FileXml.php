<?php defined('SYSPATH') or die('No direct script access.');

class Massload_FileXml extends Massload_File
{
	private static function openFile($pathtofile)
	{
		return simplexml_load_file($pathtofile);
	}

	private static function closeFile($file_instance)
	{

	}

	public static function forEachRow($config, $pathtofile, $callback)
	{
		$file = self::openFile($pathtofile);
		$i = 0;
		foreach ($file as $key=>$item )
		{
			$row = new Obj((array) $item);

			$images = Array();
			foreach ($item->images->Image as $image)
			{
				$attributes = $image->attributes();
				$images[] = (string) $attributes["url"][0];
			}			
			$row = self::clear_row($row);
			$row->images = join(";", $images);
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

		
			$row = new Obj((array) $file->Ad->{$row_num});

			$images = Array();
			if (property_exists($file->Ad->{$row_num}, "images")){
				foreach ($file->Ad->{$row_num}->images->Image as $image)
				{
					$attributes = $image->attributes();
					$images[] = (string) $attributes["url"][0];
				}	
			}
			$row = self::clear_row($row);
			$row->images = join(";", $images);
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