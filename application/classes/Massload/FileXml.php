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

	public static function forRow($config, $pathtofile, $step, $iteration, $callback)
	{
		$file = self::openFile($pathtofile);

		for ($i = $iteration*$step; $i<$iteration*$step+$step; $i++)
		{
			$row = new Obj((array) $file->Ad->{$i});

			$images = Array();
			if (property_exists($file->Ad->{$i}, "images")){
				foreach ($file->Ad->{$i}->images->Image as $image)
				{
					$attributes = $image->attributes();
					$images[] = (string) $attributes["url"][0];
				}	
			}
			$row = self::clear_row($row);
			$row->images = join(";", $images);
			$return = $callback($row, $i);
			if ($return == 'break') break;
			if ($return == 'continue') continue;
		}
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