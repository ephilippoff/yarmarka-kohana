<?php 

	define('YARMARKA_CORE_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);

	/* TODO - rewrite auto load to work with namespaces like on C# projects */

	/* Bad -> enumerate all includes */
	require_once YARMARKA_CORE_PATH . implode(DIRECTORY_SEPARATOR, array( 'Models', 'User.php' ));