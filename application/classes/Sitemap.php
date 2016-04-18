<?php

	class Sitemap {

		protected $gzipLevel = 9;
		protected $compareBufferSize = 524288;
		protected $maxPerStep1 = 50000;
		protected $maxPerStep2 = 43000;
		//protected $maxPerStep2 = 20;
		protected $selectLimit = 1000;
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
			return htmlspecialchars($this->checkForPretty('http://' . $this->cityName . '.' . $this->config['main_domain'] . '/' . $part));
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
			$total = 0;
			$counter = 0;
			$lastCategoryUrl = NULL;
			// prepare query

			// 1. published objects inner query
			$objectSubQuery =  DB::select('value')
				->from('data_list')
				->join('object')
					->on('data_list.object','=','object.id') 
				->where('active','=','1')
				->where('is_published','=','1')
				->where('category','=', DB::expr('category.id'));

			// 2. prepare attributes elements query
			$categoriesQuery = DB::select(
						array('attribute_element.url', 'element_url')
						, array('category.url', 'category_url')
					)
				->from('attribute_element')
				->join('attribute')
					->on('attribute_element.attribute','=','attribute.id')
				->join('reference')
					->on('attribute.id','=','reference.attribute')
				->join('category')
					->on('category.id', '=', 'reference.category')
				// ->where('attribute_element.id', 'IN', $objectSubQuery)
				->where('reference.is_seo_used','=',1)
				->order_by('category.id')
				->limit($this->selectLimit)
				->offset($total);
			// prepare query done
			while(true) {
				// exec
				$x = time();
				$categoriesQuery->offset($total);
				$categories = $categoriesQuery->execute();
				
				foreach ($categories as $item) {
					$total++;
		            if (!$item['category_url']) continue;

		            if ($lastCategoryUrl != $item['category_url']) {
						$this->writeFile($this->getSitemapEntry(array(
							'loc' => $this->makeUrl($item['category_url']),
							'changefreq' => 'daily',
							'priority' => '0.8'
						)));
						$lastCategoryUrl = $item['category_url'];
						$counter++;
						if ($counter >= $this->maxPerStep1) {
							break;
						}
					}

		            if (!$item['element_url']) {
		            	continue;
		            }

					$this->writeFile($this->getSitemapEntry(array(
						'loc' => $this->makeUrl($item['category_url'] . '/' . $item['element_url']),
						'changefreq' => 'daily',
						'priority' => '0.8'
					)));
					$counter++;
					if ($counter >= $this->maxPerStep1) {
						break;
					}
		        }
		        echo 'Seconds: ' . (time() - $x) . ' Rows: ' . $total . "\r\n";
		        if ($counter >= $this->maxPerStep1 || count($categories) == 0) {
		        	break;
		        }
		    }

		    $this->writeFile($this->getSitemapFooter());
	        $this->closeFile();
		}

		protected function getStep2Data($lastModified, $file) {
			$total = 0;
			$lastPage = -1;
			$ok = false;

			while($total < $this->maxPerStep2 && $lastPage != 0) {
$x = time();
				$objects = ORM::factory('Object')
					->where('date_created', '>', date('Y-m-d H:i:s', $lastModified))
					->or_where('date_updated', '>', date('Y-m-d H:i:s', $lastModified))
					->limit(min($this->maxPerStep2 - $total, $this->selectLimit))
					->offset($total)
					->order_by(DB::expr('(case when date_updated is null then date_created else date_updated end)'), 'desc')
					->find_all()
					->as_array();
				$lastPage = count($objects);
				$total += $lastPage;
				foreach($objects as $object) {
					if (!$ok) {
						$this->openFile($file);
						$this->writeFile($this->getSitemapHeader());
						$ok = true;
					}
					$this->writeFile($this->getSitemapEntry(array(
							'loc' => $this->makeUrl($object->get_url())
							, 'changefreq' => 'monthly'
							, 'priority' => '0.5'
							, 'lastmod' => date('Y-m-d\TH:i:sP', strtotime($object->date_updated ? $object->date_updated : $object->date_created))
						)));
				}
echo 'Seconds: ' . (time() - $x) . ' Rows: ' . $lastPage . "\r\n";
			}

			if ($ok) {
				$this->writeFile($this->getSitemapFooter());
				$this->closeFile();
			}
		}

		public function rebuild() {
			$sitemapsPath = ($_SERVER['DOCUMENT_ROOT'] ? $_SERVER['DOCUMENT_ROOT'] : '.') . DIRECTORY_SEPARATOR . 'sitemaps' . DIRECTORY_SEPARATOR;
			$bigOutputFileName = $sitemapsPath . 'index.xml';
			// step 1
			$step1FileName = '1.xml.gz';
			$step1OutFile = $sitemapsPath . $step1FileName;
			$step1OutFileTemp = $step1OutFile . '.tmp';
			$x = time();
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