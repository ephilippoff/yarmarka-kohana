<?php

/**
 * The directory in which your application specific resources are located.
 * The application directory must contain the bootstrap.php file.
 *
 * @link http://kohanaframework.org/guide/about.install#application
 */
$application = '../application';

/**
 * The directory in which your modules are located.
 *
 * @link http://kohanaframework.org/guide/about.install#modules
 */
$modules = '../modules';

/**
 * The directory in which the Kohana resources are located. The system
 * directory must contain the classes/kohana.php file.
 *
 * @link http://kohanaframework.org/guide/about.install#system
 */
$system = '../system';

/**
 * The default extension of resource files. If you change this, all resources
 * must be renamed to use the new extension.
 *
 * @link http://kohanaframework.org/guide/about.install#ext
 */
define('EXT', '.php');

/**
 * Set the PHP error reporting level. If you set this in php.ini, you remove this.
 * @link http://www.php.net/manual/errorfunc.configuration#ini.error-reporting
 *
 * When developing your application, it is highly recommended to enable notices
 * and strict warnings. Enable them by using: E_ALL | E_STRICT
 *
 * In a production environment, it is safe to ignore notices and strict warnings.
 * Disable them by using: E_ALL ^ E_NOTICE
 *
 * When using a legacy application with PHP >= 5.3, it is recommended to disable
 * deprecated notices. Disable with: E_ALL & ~E_DEPRECATED
 */
error_reporting(E_ALL | E_STRICT);

/**
 * End of standard configuration! Changing any of the code below should only be
 * attempted by those with a working knowledge of Kohana internals.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 */

// Set the full path to the docroot
define('DOCROOT', realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR);

// Make the application relative to the docroot, for symlink'd index.php
if ( ! is_dir($application) AND is_dir(DOCROOT.$application))
	$application = DOCROOT.$application;

// Make the modules relative to the docroot, for symlink'd index.php
if ( ! is_dir($modules) AND is_dir(DOCROOT.$modules))
	$modules = DOCROOT.$modules;

// Make the system relative to the docroot, for symlink'd index.php
if ( ! is_dir($system) AND is_dir(DOCROOT.$system))
	$system = DOCROOT.$system;

// Define the absolute paths for configured directories
define('APPPATH', realpath($application).DIRECTORY_SEPARATOR);
define('MODPATH', realpath($modules).DIRECTORY_SEPARATOR);
define('SYSPATH', realpath($system).DIRECTORY_SEPARATOR);

// Clean up the configuration vars
unset($application, $modules, $system);

/*if (file_exists('install'.EXT))
{
	// Load the installation check
	return include 'install'.EXT;
}*/

/**
 * Define the start time of the application, used for profiling.
 */
if ( ! defined('KOHANA_START_TIME'))
{
	define('KOHANA_START_TIME', microtime(TRUE));
}

/**
 * Define the memory usage at the start of the application, used for profiling.
 */
if ( ! defined('KOHANA_START_MEMORY'))
{
	define('KOHANA_START_MEMORY', memory_get_usage());
}

// Bootstrap the application
require APPPATH.'bootstrap'.EXT;

//load yarmarka logic
require realpath('..') . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, array( 'core', 'autoload.php' ));

if (PHP_SAPI == 'cli') // Try and load minion
{
	class_exists('Minion_Task') OR die('Please enable the Minion module for CLI support.');
	set_exception_handler(array('Minion_Exception', 'handler'));

	Minion_Task::factory(Minion_CLI::options())->execute();
}
else
{
	/* pretty urls processor */

	// construct the url
	// 0. protocol
	$urlProtocol = 'http';
	if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
		$urlProtocol = 'https';
	}
	$urlProtocol .= '://';
	// 1. host
	$urlHost = $_SERVER['HTTP_HOST'];
	// 2. relative path
	$urlPath = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
	// 3. query string
	$urlQuery = $_SERVER['QUERY_STRING'];
	// build url
	$urlCompiled = $urlProtocol . $urlHost 
		. ($urlPath != NULL ? $urlPath : '/') 
		. ($urlQuery != NULL ? ('?' . $urlQuery) : '');

	//var_dump($urlCompiled);die;

	// get the data. TODO - think to cache it
	$item = ORM::factory('PrettyUrl')
		->where('pretty', '=', $urlCompiled)
		->find();

	if ($item->loaded()) {
		$parsedUrl = parse_url($item->ugly);

		if (isset($parsedUrl['host'])) {
			$_SERVER['HTTP_HOST'] = $parsedUrl['host'];
		}

		if (isset($parsedUrl['path'])) {
			$_SERVER['PATH_INFO'] = $parsedUrl['path'];
		}

		if (isset($parsedUrl['query'])) {
			// clear GET
			foreach($_GET as $key => $value) {
				unset($_GET[$key]);
			}
			$tmp = array();
			parse_str($parsedUrl['query'], $tmp);
			foreach($tmp as $key => $value) {
				$_GET[$key] = $value;
			}
		}

		// append global attributes to override all others
		$GLOBALS['title'] = htmlspecialchars($item->title);
		$GLOBALS['h1'] = $item->h1;
		$GLOBALS['description'] = htmlspecialchars($item->description);
		$GLOBALS['keywords'] = htmlspecialchars($item->keywords);
		$GLOBALS['footer'] = $item->footer;
	}

	//header('Content-Type: text/plain');
	//var_dump($_SERVER);die;

	/* done */

	/**
	 * Execute the main request. A source of the URI can be passed, eg: $_SERVER['PATH_INFO'].
	 * If no source is specified, the URI will be automatically detected.
	 */
	echo Request::factory(TRUE, array(), FALSE)
		->execute()
		->send_headers(TRUE)
		->body();
}
