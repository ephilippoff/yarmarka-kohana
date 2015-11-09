<?php

	class RamblerApi {

		const ENDPOINT = 'http://api.kassa.rambler.ru/v2/{apikey}/{format}/';

		//TODO - move to settings files
		private $_apiKey = 'no-key';
		private $_apiFormat = 'json';
		private $_proxy = NULL;

		//create only with factory
		protected function __construct() {

		}

		protected function makeUrl($func) {
			return str_replace(
				array(
						'{apikey}',
						'{format}'
					),
				array(
						$this->_apiKey,
						$this->_apiFormat
					),
				self::ENDPOINT) . $func;
		}

		protected function makeCurlOptions($func, $method, $data, $headers) {
			$result = array(
					CURLOPT_URL => $this->makeUrl($func),
					CURLOPT_RETURNTRANSFER => 1,
					CURLOPT_CUSTOMREQUEST => $method
				);

			if (!empty($this->_proxy)) {
				$result[CURLOPT_PROXY] = $this->_proxy;
			}

			if ($method != 'GET' && !empty($data)) {
				$result[CURLOPT_POSTFIELDS] = $data;
			} else if ($method == 'GET' && !empty($data)) {
				$result[CURLOPT_URL] .= '?' . http_build_query($data);
			}

			if (!empty($headers)) {
				$result[CURLOPT_HTTPHEADER] = $headers;
			}

			return $result;
		}

		protected function exec($func, $data = array(), $method = 'GET', $headers = array()) {
			$curl = curl_init();

			curl_setopt_array($curl, $this->makeCurlOptions($func, $method, $data, $headers));

			$result = curl_exec($curl);

			curl_close($curl);

			return $this->decode($result);
		}

		protected function decode($data) {
			if ($this->_apiFormat == 'json') {
				return $this->jsonDecode($data);
			}

			throw new Exception('Not implemented');
		}

		protected function jsonDecode($data) {
			return json_decode($data, true);
		}


		/* api methods */
		public function getCities() {
			return $this->exec('cities');
		}

		public function getTypes() {
			return $this->exec('classtypes');
		}

		public function getPlaces($cityId) {
			return $this->exec('place/list', array(
					'cityid' => $cityId
				));
		}
		/* api methods done */

		/* factory method */
		/* works only in kohana */
		public static function factory($config = NULL) {
			if ($config === NULL) {
				//load config from kohana config files
				$config = Kohana::$config->load('rambler_api');
				//format config
				$config = array(
						'_apiKey' => $config->get('key'),
						'_apiFormat' => $config->get('format'),
						'_proxy' => $config->get('proxy')
					);
			}

			$configKeys = array( '_apiKey', '_apiFormat', '_proxy' );

			$instance = new self();
			foreach($configKeys as $key) {
				if (isset($config[$key])) {
					$instance->{$key} = $config[$key];
				}
			}

			return $instance;
		}
	}

?>