<?php defined('SYSPATH') or die('No direct script access.');

// -- Environment setup --------------------------------------------------------

// Load the core Kohana class
require SYSPATH.'classes/Kohana/Core'.EXT;

if (is_file(APPPATH.'classes/Kohana'.EXT))
{
	// Application extends the core
	require APPPATH.'classes/Kohana'.EXT;
}
else
{
	// Load empty core extension
	require SYSPATH.'classes/Kohana'.EXT;
}

/**
 * Set the default time zone.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 * @link http://www.php.net/manual/timezones
 */
// date_default_timezone_set('America/Chicago');

/**
 * Set the default locale.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 * @link http://www.php.net/manual/function.setlocale
 */
setlocale(LC_ALL, 'en_US.utf-8');

/**
 * Enable the Kohana auto-loader.
 *
 * @link http://kohanaframework.org/guide/using.autoloading
 * @link http://www.php.net/manual/function.spl-autoload-register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Optionally, you can enable a compatibility auto-loader for use with
 * older modules that have not been updated for PSR-0.
 *
 * It is recommended to not enable this unless absolutely necessary.
 */
//spl_autoload_register(array('Kohana', 'auto_load_lowercase'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @link http://www.php.net/manual/function.spl-autoload-call
 * @link http://www.php.net/manual/var.configuration#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

// -- Configuration and initialization -----------------------------------------

/**
 * Set the default language
 */
I18n::lang('en-us');

Kohana::$environment = (@$_SERVER['HTTP_HOST'] === 'c.yarmarka.biz') ? Kohana::PRODUCTION : Kohana::DEVELOPMENT;

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - integer  cache_life  lifetime, in seconds, of items cached              60
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 * - boolean  expose      set the X-Powered-By header                        FALSE
 */
Kohana::init(array(
	'base_url'   	=> '/',
	'index_file'	=> '',
	'profile'		=> FALSE,
	'caching'		=> Kohana::$environment === Kohana::PRODUCTION,
	'errors'		=> Kohana::$environment === Kohana::DEVELOPMENT, //не работает для ошибок из ORM
));

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Log::$write_on_add = TRUE;
Kohana::$log->attach(new Log_File(APPPATH.'logs/important'), Log::EMERGENCY, Log::ERROR);
Kohana::$log->attach(new Log_File(APPPATH.'logs/notice'), array(Log::NOTICE, Log::INFO));
// Disable debug log for production
if (Kohana::$environment !== Kohana::PRODUCTION)
{
	Kohana::$log->attach(new Log_File(APPPATH.'logs/debug'), array(Log::DEBUG));
}

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Config_File);
Kohana::$config->attach(new Config_File('config/local'));

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
	'auth'       => MODPATH.'auth',       // Basic authentication
	'cache'      => MODPATH.'cache',      // Caching with multiple backends
	'codebench'  => MODPATH.'codebench',  // Benchmarking tool
	'database'   => MODPATH.'database',   // Database access
	'image'      => MODPATH.'image',      // Image manipulation
	'minion'     => MODPATH.'minion',     // CLI Tasks
	'orm'        => MODPATH.'orm',        // Object Relationship Mapping
	'assets'     => MODPATH.'asset-merger',        // js css assets manager
	'pagination' => MODPATH.'pagination',        // kohana pagination module
	'phpexcel'   => MODPATH.'phpexcel',
	'captcha'	=> MODPATH.'captcha',
	'unittest'   => MODPATH.'unittest',   // Unit testing
	//'userguide'  => MODPATH.'userguide',  // User guide and API documentation
	'twig'       => MODPATH.'kohana-twig',
	));

// set default cache driver
Cache::$default = 'memcache';

// session overload
Cookie::$salt = 'cookiesecrethere@@##44aasdsd';

// create unique user session_id
if ( ! Session::instance()->get('session_id'))
{
	Session::instance()->set('session_id', uniqid('', TRUE));
}

include 'routing.php';