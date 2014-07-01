<?php defined('SYSPATH') OR die('No direct script access.');

class Image_Diff {

  // not bigger 20
	private $matrix = 15;

	public function __construct()
	{
		$this->matrix = Kohana::$config->load('images.diff_matrix');
	}

	public function get_image_info($image_path)
	{
		list($width, $height, $type, $attr) = getimagesize($image_path);
		$image_type = '';
		switch ($type) 
		{
			case IMAGETYPE_JPEG:
				$image_type = 'jpeg';
			break;
			case IMAGETYPE_GIF:
				$image_type = 'gif';
			break;
			case IMAGETYPE_PNG:
				$image_type = 'png';
			break;
			case IMAGETYPE_BMP:
				$image_type = 'bmp';
			break;
			default:
				$image_type = '';
		}

		return array('width' => $width, 'height' => $height, 'type' => $image_type);
	}

	public function generate_array($image_path)
	{
		$image_info = $this->get_image_info($image_path);
		$func = 'imagecreatefrom'.$image_info['type'];
		if (function_exists($func))
		{
			$main_img = $func($image_path);
			$tmp_img = imagecreatetruecolor($this->matrix, $this->matrix);
			$res = imagecopyresampled($tmp_img, $main_img, 0, 0, 0, 0, $this->matrix, $this->matrix, $image_info['width'], $image_info['height']);


			$pixelmap = array();
			$average_pixel = 0;
			for($x = 0; $x < $this->matrix; $x++)
			{
				for($y = 0; $y < $this->matrix; $y++)
				{
					$color = imagecolorat($tmp_img, $x, $y);
					$color = imagecolorsforindex($tmp_img, $color);
					// $pixelmap[$x][$y]= 0.299 * $color['red'] + 0.587 * $color['green'] + 0.114 * $color['blue'];
					// $pixelmap[$x][$y]= sqrt(0.241 * pow($color['red'], 2) + 0.691 * pow($color['green'], 2) + 0.068 * pow($color['blue'], 2) );
					$pixelmap[$x][$y]= 0.2126 * $color['red'] + 0.7152 * $color['green'] + 0.0722 * $color['blue'];
					$average_pixel += $pixelmap[$x][$y];
				}
			}

			$average_pixel = $average_pixel/($this->matrix * $this->matrix);

			imagedestroy($main_img);
			imagedestroy($tmp_img);


			$test = array();
			$count = 0;
			for($x = 0; $x < $this->matrix; $x++)
			{
				for($y = 0; $y < $this->matrix; $y++)
				{
					$count++;
					$row = ($pixelmap[$x][$y] == 0) ? 0 : round( 2*(($pixelmap[$x][$y] > $average_pixel) ? ($pixelmap[$x][$y] / $average_pixel) : (($average_pixel/$pixelmap[$x][$y]) * -1) ) );
					$row_str = sprintf("%02d", ($x + 10));
					$row_str .= sprintf("%02d", ($y + 10));
					$row_str .= sprintf("%03d", (255 + intval($row)));
					$result[] = intval($row_str);
				}
			}

			return $result;

		} 
		else 
		{
			//raise exception
			throw new Exception('File type  not supported!');
		}
	}

	public function diff_images($image_path1, $image_path2)
	{
		$array1 = $this->generate_array($image_path1);
		$array2 = $this->generate_array($image_path2);
		$result = 0;
		$result = count( array_intersect($array1, $array2) );
		return round($result / ( $this->matrix * $this->matrix ), 6);
	}
}

/* End of file ImageDiff.php */
/* Location: ./application/classes/ImageDiff.php */