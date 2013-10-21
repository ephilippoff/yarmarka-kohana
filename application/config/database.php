<?php defined('SYSPATH') or die('No direct access allowed.');

return array
(
	'default' => array
	(
		'type'       => 'PDO',
		'connection' => array(
			/**
			 * The following options are available for MySQL:
			 *
			 * string   hostname     server hostname, or socket
			 * string   database     database name
			 * string   username     database username
			 * string   password     database password
			 * boolean  persistent   use persistent connections?
			 * array    variables    system variables as "key => value" pairs
			 *
			 * Ports and sockets may be appended to the hostname.
			 */
			'dsn'		 => 'pgsql:dbname=yarmarka_biz;host=127.0.0.1',
			'username'   => 'postgres',
			'password'   => 'root',
			'persistent' => FALSE,
		),
		'identifier'   => '"',
		'table_prefix' => '',
		'charset'      => 'utf8',
		'caching'      => FALSE,
		'profiling'    => TRUE,
	),
	'kladr' => array
	(
		'type'       => 'PDO',
		'connection' => array(
			'dsn'		 => 'pgsql:dbname=kladr;host=127.0.0.1',
			'username'   => 'postgres',
			'password'   => 'root',
			'persistent' => FALSE,
		),
		'identifier'   => '"',
		'table_prefix' => '',
		'charset'      => 'utf8',
		'caching'      => FALSE,
		'profiling'    => TRUE,
	),
	'db_dns' => array
	(
		'type'       => 'MySQL',
		'connection' => array(
			'hostname'	 => 'localhost',
			'database'	 => 'db_dns',
			'username'   => 'root',
			'password'   => 'root',
			'persistent' => FALSE,
		),
		'identifier'   => '"',
		'table_prefix' => '',
		'charset'      => 'utf8',
		'caching'      => FALSE,
		'profiling'    => TRUE,
	),
);
