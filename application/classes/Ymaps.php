<?php if ( ! defined('SYSPATH')) exit('No direct script access allowed');

/**
 * Класс для работы с API яндек карт
 */
class Ymaps
{
	private $_api_key;
	private static $_instance;

	public function __construct($config = NULL)
	{
		if ( ! $config)
		{
			$config = Kohana::$config->load('maps');
			$this->api_key = $config['ymaps_api_key'];
		}
	}

	public static function instance()
	{
		if ( ! self::$_instance)
		{
			self::$_instance = new Ymaps(Kohana::$config->load('maps'));
		}

		return self::$_instance;
	}

	/**
	 * Get geo coord by name
	 * 
	 * @param  string $name
	 * @return array
	 */
	public function get_coord_by_name($name)
	{
		$coord = FALSE;
		
		$params = array(
			'geocode' => trim($name),
			'format'  => 'json',
			'results' => 1,
			'key'     => $this->_api_key,
		);
		$response = json_decode(file_get_contents('http://geocode-maps.yandex.ru/1.x/?' . http_build_query($params, '', '&')));

		if ($response->response->GeoObjectCollection->metaDataProperty->GeocoderResponseMetaData->found > 0)
		{
			$coord = explode(' ', $response->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos);
		}

		return $coord;
	}
}

/* End of file Ymaps.php */
/* Location: ./application/classes/Ymaps.php */