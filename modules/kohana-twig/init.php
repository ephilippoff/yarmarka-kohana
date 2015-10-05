<?php defined('SYSPATH') or die('No direct script access.');

define('TWIGPATH', __DIR__ . DIRECTORY_SEPARATOR);

require_once TWIGPATH.'vendor/twig/twig/lib/Twig/Autoloader.php';
Twig::init();