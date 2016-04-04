<?php

	class Sitemap {

		protected $gzipLevel = 9;
		protected $compareBufferSize = 524288;
		protected $maxPerStep1 = 50000;
		protected $maxFiles = 100;
		protected $cityName = 'surgut';

		protected $prettyUrls;
		protected $config;
		protected $gzipFile;

		public function __construct() {
			$this->initConfig();
			$this->initPrettyUrls();

			ini_set('memory_limit', '-1');
			set_time_limit(0);
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

		protected function openFile($file) {
			$mode = 'wb' . $this->gzipLevel;	
			$this->fd = gzopen($file, $mode);
		}

		protected function writeFile($data) {
			gzwrite($this->fd, $data);
		}

		protected function closeFile() {
			gzclose($this->fd);
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

		protected function getSitemapHeader() {
			return '<?xml version="1.0" encoding="UTF-8"?>' . "\r\n"
				. '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		}

		protected function getSitemapFooter() {
			return '</urlset>';
		}

		protected function getSitemapEntry($entry) {
			$res = '<url>';
			foreach($entry as $key => $value) {
				$res .= '<' . $key . '>' . $value . '</' . $key . '>';
			}
			$res .= '</url>';
			return $res;
		}

		protected function getStep1Data($file) {
			$this->openFile($file);
			$this->writeFile($this->getSitemapHeader());
			$categories = ORM::factory('Category')->find_all();
			$counter = 0;
			foreach ($categories as $category) {
	            if (!$category->url) continue;

	            $this->writeFile($this->getSitemapEntry(array(
	                'loc' => $this->makeUrl($category->url),
	                'changefreq' => 'daily',
	                'priority' => '0.8'
	            )));
	            $counter++;

	            if ($counter >= $this->maxPerStep1) {
	            	break;
	            }

	            $elements = ORM::factory('Attribute_Element')
	                            ->get_elements_with_published_objects($category->id)
	                            ->cached(Date::DAY)
	                            ->find_all();

	            foreach ($elements as $element) {
	                if (!$element->url) continue;
	                $this->writeFile($this->getSitemapEntry(array(
	                    'loc' => $this->makeUrl($category->url . '/' . $element->url),
	                    'changefreq' => 'daily',
	                    'priority' => '0.8'
	                )));
	                $counter++;
	                if ($counter >= $this->maxPerStep1) {
		            	break;
		            }
	            }
	            if ($counter >= $this->maxPerStep1) {
	            	break;
	            }
	        }
	        $this->writeFile($this->getSitemapFooter());
	        $this->closeFile();
		}

		protected function getStep2Data($lastModified, $file) {
			$objects = ORM::factory('Object')
				->where('date_created', '>', date('Y-m-d H:i:s', $lastModified))
				->or_where('date_updated', '>', date('Y-m-d H:i:s', $lastModified))
				->find_all();
			$ok = false;
			foreach($objects as $object) {
				if (!$ok) {
					$this->openFile($file);
					$ok = true;
				}
				$this->writeFile($this->getSitemapEntry(array(
						'loc' => $object->get_url()
						, 'changefreq' => 'monthly'
						, 'priority' => '0.5'
						, 'lastmod' => date('Y-m-d\TH:i:sP', strtotime($object->date_updated ? $object->date_updated : $object->date_created))
					)));
			}
			if ($ok) {
				$this->closeFile();
			}
		}

		public function rebuild() {
			$sitemapsPath = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'sitemaps' . DIRECTORY_SEPARATOR;
			$bigOutputFileName = $sitemapsPath . 'index.xml';
			// step 1
			$step1FileName = '1.xml.gz';
			$step1OutFile = $sitemapsPath . $step1FileName;
			$step1OutFileTemp = $step1OutFile . '.tmp';
			$this->getStep1Data($step1OutFileTemp);
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
			$this->getStep2Data($lastModified, $step2OutFile);

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