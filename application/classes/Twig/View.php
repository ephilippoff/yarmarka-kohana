<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Twig view
 */
class Twig_View extends View {

	public static function factory($file = NULL, array $data = NULL)
	{
		return new Twig_View($file, $data);
	}

	public function set_filename($file)
	{
		if (($path = Kohana::find_file('twigs', $file, 'html')) === FALSE)
		{
			throw new View_Exception('The requested twigview :file could not be found', array(
				':file' => $file,
			));
		}

		// Store the file path locally
		$this->_file = $path;

		return $this;
	}
}