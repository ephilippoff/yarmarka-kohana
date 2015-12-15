<?php 

	defined('SYSPATH') or die('No direct script access.');

	class Controller_Files extends Controller_Template {

		private $_relativePath = '/uploads/raw/';
		private $_fullPath = NULL;
		private $_currentRelativePath = '';
		private $_currentFullPath = NULL;
		private $_user = NULL;

		private $_ckEditor = NULL;
		private $_ckEditorFuncNum = NULL;

		public function before() {
			$this->use_layout = false;
			$this->auto_render = false;

			//check user role
			//only admins can use file manager
			$this->_user = Auth::instance()->get_user();
			if (!$this->_user || $this->_user->role != 1) {
				throw new HTTP_Exception_403;
			}

			//init path
			$this->_fullPath = self::relativeToAbsolute($this->_relativePath);
			//check if path not exists -> create it and set 775
			if (!is_dir($this->_fullPath)) {
				mkdir($this->_fullPath);
				chmod($this->_fullPath, 0775);
			}

			//check relative path
			if (array_key_exists('path', $_REQUEST)) {
				$this->_currentRelativePath = trim(str_replace('..', '', $_REQUEST['path']), DIRECTORY_SEPARATOR);
			}
			$this->_currentFullPath = self::relativeToAbsolute(
				self::concat($this->_relativePath, $this->_currentRelativePath));
			if (!is_dir($this->_currentFullPath)) {
				throw new HTTP_Exception_404;
			}

			//save ckeditor variables
			$keys = array( 
					'CKEditor' => '_ckEditor', 
					'CKEditorFuncNum' => '_ckEditorFuncNum' 
				);
			foreach($keys as $key => $value) {
				if (array_key_exists($key, $_REQUEST)) {
					$this->{$value} = $_REQUEST[$key];
				}
			}
		}

		public function action_index() {
			$items = self::ls($this->_currentFullPath);

			for($i = 0;$i < count($items);$i++) {
				$items[$i]['relativePath'] = self::concat(
					self::concat($this->_relativePath, $this->_currentRelativePath),
					$items[$i]['name']);
				if ($items[$i]['type'] == 'd') {
					$items[$i]['href'] = $this->getFolderUrl($items[$i]['name']);
				}
			}
			$this->response->body(View::factory('files/index')->set(array(
					'items' => $items,
					'ckEditor' => $this->_ckEditor,
					'ckEditorFuncNum' => $this->_ckEditorFuncNum,
					'currentRelativePath' => $this->_currentRelativePath,
					'up' => $this->_fullPath == $this->_currentFullPath 
						? NULL 
						: $this->getPathUrl(dirname($this->_currentRelativePath))
				)));
		}

		public function action_createFolder() {
			if (!array_key_exists('name', $_REQUEST)) {
				$this->redirectToIndex();
			}

			$fullPath = self::concat($this->_currentFullPath, $_REQUEST['name']);
			mkdir($fullPath);
			chmod($fullPath, 0775);
			$this->redirectToIndex();
		}

		public function action_uploadFile() {
			if (!array_key_exists('file', $_FILES) || !$_FILES['file']) {
				$this->redirectToIndex();
			}

			$info = pathinfo($_FILES['file']['name']);
			$newFileName = $info['filename'] . md5(time()) . '.' . $info['extension'];
			$newFileFullPath = self::concat($this->_currentFullPath, $newFileName);
			move_uploaded_file($_FILES['file']['tmp_name'], $newFileFullPath);
			$this->redirectToIndex();
		}

		protected function redirectToIndex() {
			$url = '/files';
			$tokens = array();
			if (!empty($this->_currentRelativePath)) {
				$tokens['path'] = $this->_currentRelativePath;
			}
			if ($this->_ckEditor !== NULL) {
				$tokens['CKEditor'] = $this->_ckEditor;
			}
			if ($this->_ckEditorFuncNum !== NULL) {
				$tokens['CKEditorFuncNum'] = $this->_ckEditorFuncNum;
			}

			$append = array();
			foreach($tokens as $key => $value) {
				$append []= $key . '=' . $value;
			}

			$this->redirect($url . (count($append) > 0 ? ('?' . implode('&', $append)) : ''));
		}

		protected function getFolderUrl($item) {
			return $this->getPathUrl(self::concat($this->_currentRelativePath, $item));
		}

		protected function getPathUrl($relativePath) {
			$prefix = '/files?path=' . $relativePath;
			if ($this->_ckEditor !== NULL && $this->_ckEditorFuncNum !== NULL) {
				$prefix .= '&CKEditor=' . $this->_ckEditor . '&CKEditorFuncNum=' . $this->_ckEditorFuncNum;
			}
			return $prefix;	
		}

		protected static function ls($path) {
			$ret = array();
			foreach(scandir($path) as $item) {
				if ($item == '..' || $item == '.') {
					continue;
				}
				$fullPathItem = self::concat($path, $item);
				$ret []= array(
						'name' => $item,
						'fullPath' => $fullPathItem,
						'type' => is_dir($fullPathItem) ? 'd' : 'f'
					);
			}
			return $ret;
		}

		protected static function relativeToAbsolute($x) {
			return self::concat($_SERVER['DOCUMENT_ROOT'], $x);
		}

		protected static function concat($a, $b) {
			return implode(DIRECTORY_SEPARATOR, array( rtrim($a, DIRECTORY_SEPARATOR), trim($b, DIRECTORY_SEPARATOR) ));
		}

	}
