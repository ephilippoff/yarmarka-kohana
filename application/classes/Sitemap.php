<?php

	class Sitemap {

		protected $gzipLevel = 9;
		protected $compareBufferSize = 524288;
		protected $maxPerStep1 = 50000;
		protected $maxFiles = 100;
		protected $cityName = 'surgut';

		protected $prettyUrls;
		protected $config;

		public function __construct() {
			$this->initConfig();
			$this->initPrettyUrls();
		}

		protected function initConfig() {
			$this->config = Kohana::$config->load('common');
		}

		protected function initPrettyUrls() {
			$data = ORM::factory('PrettyUrl')->find_all();
			$this->prettyUrls = array();
			foreach($data as $item) {
				$this->prettyUrls[$item->ugly] = $item->pretty;
			}
		}

		protected function checkForPretty($url) {
			if (array_key_exists($url, $this->prettyUrls)) {
				return $this->prettyUrls[$url];
			}
			return $url;
		}

		protected function writeFile($file, $data) {
			$mode = 'wb' . $this->gzipLevel;	

			$fd = gzopen($file, $mode);
			gzwrite($fd, $data);
			gzclose($fd);
		}

		protected function filesEq($a, $b) {
			if (!is_file($a) || !is_file($b)) {
				return false;
			}
			$fileA = fopen($a, 'rb');
			$fileB = fopen($b, 'rb');

			while (!feof($fileA) && !feof($fileB)) {
				if (fread($fileA, $this->compareBufferSize) != fread($fileB, $this->compareBufferSize)) {
					break;
				}
			}

			$res = feof($fileA) && feof($fileB);
			fclose($fileA);
			fclose($fileB);
			return $res;
		}

		protected function makeUrl($part) {
			return $this->checkForPretty('http://' . $this->cityName . '.' . $this->config['main_domain'] . '/' . $part);
		}

		protected function compileSiteMapFile($entries) {
			$res = '<?xml version="1.0" encoding="UTF-8"?>' . "\r\n"
				. '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

			foreach($entries as $entry) {
				$res .= '<url>';
				foreach($entry as $key => $value) {
					$res .= '<' . $key . '>' . $value . '</' . $key . '>';
				}
				$res .= '</url>';
			}

			return $res . '</urlset>';
		}

		protected function getStep1Data() {
			$categories = ORM::factory('Category')->find_all();
			$entries = array();
			foreach ($categories as $category) {
	            if (!$category->url) continue;

	            $entry = array(
	                'loc' => $this->makeUrl($category->url),
	                'changefreq' => 'daily',
	                'priority' => '0.8'
	            );

	            $entries[] = $entry;

	            $elements = ORM::factory('Attribute_Element')
	                            ->get_elements_with_published_objects($category->id)
	                            ->cached(Date::DAY)
	                            ->find_all();

	            foreach ($elements as $element) {
	                if (!$element->url) continue;
	                $entry_element = array(
	                    'loc' => $this->makeUrl($category->url . '/' . $element->url),
	                    'changefreq' => 'daily',
	                    'priority' => '0.8'
	                );
	                $entries[] = $entry_element;
	            }
	        }
	        if (count($entries) > $this->maxPerStep1) {
	        	$entries = array_slice($entries, 0, $this->maxPerStep1);
	        }
	        return $entries;
		}

		protected function getStep2Data($lastModified) {
			$objects = ORM::factory('Object')
				->where('date_created', '>', date('Y-m-d H:i:s', $lastModified))
				->or_where('date_updated', '>', date('Y-m-d H:i:s', $lastModified))
				->find_all();
			$entries = array();
			foreach($objects as $object) {
				$entries []= array(
						'loc' => $object->get_url()
						, 'changefreq' => 'monthly'
						, 'priority' => '0.5'
						, 'lastmod' => date('Y-m-d\TH:i:sP', strtotime($object->date_updated ? $object->date_updated : $object->date_created))
					);
			}
			return $entries;
		}

		public function rebuild() {
			$sitemapsPath = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'sitemaps' . DIRECTORY_SEPARATOR;
			$bigOutputFileName = $sitemapsPath . 'index.xml';
			// step 1
			$step1FileName = '1.xml.gz';
			$step1OutFile = $sitemapsPath . $step1FileName;
			$step1OutFileTemp = $step1OutFile . '.tmp';
			$data = $this->compileSiteMapFile($this->getStep1Data());
			$this->writeFile($step1OutFileTemp, $data);
			$changed = !$this->filesEq($step1OutFile, $step1OutFileTemp);
			if ($changed) {
				copy($step1OutFileTemp, $step1OutFile);
			}
			unlink($step1OutFileTemp);

			// step 2
			$lastModified = is_file($bigOutputFileName) ? filemtime($bigOutputFileName) : FALSE;
			if ($lastModified === FALSE) {
				$lastModified = 0;
			}
			$step2OutFile = $sitemapsPath . uniqid() . '.xml.gz';
			$data = $this->getStep2Data($lastModified);
			if (count($data)) {
				$data = $this->compileSiteMapFile($data);
				$this->writeFile($step2OutFile, $data);
			}

			// create big sitemap
			$files = scandir($sitemapsPath);
			$filesMap = array();
			foreach($files as $file) {
				if (!preg_match('/.*\.xml\.gz/', $file) || $file == $step1FileName) {
					continue;
				}
				$filesMap[$file] = filemtime($sitemapsPath . $file);
			}
			arsort($filesMap);
			if (count($filesMap) > $this->maxFiles) {
				$filesMap = array_slice($filesMap, 0, $this->maxFiles, true);
			}
			$filesMap[$step1FileName] = filemtime($sitemapsPath . $step1FileName);

			// output big sitemap
			$fd = fopen($bigOutputFileName, 'wb');
			fwrite($fd, '<?xml version="1.0" encoding="UTF-8"?>' . "\r\n"
				. '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\r\n");
			foreach($filesMap as $key => $value) {
				fwrite($fd, 
					'<sitemap>'
						. '<loc>' . $this->makeUrl('sitemaps/' . $key) . '</loc>'
						. '<lastmod>' . date('Y-m-d\TH:i:sP', $value) . '</lastmod>'
					. '</sitemap>');
			}
			fwrite($fd, '</sitemapindex>');
			fclose($fd);
		}

	}

?>