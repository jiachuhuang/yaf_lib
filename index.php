<?php

define('APPLICATION_PATH', dirname(__FILE__));

define('ENV', ini_get('yaf.environ'));

switch (ENV) {
	case 'product':
		error_reporting(0);
		ini_set('display_errors', 'Off');
		define('DISPLAY_ERRORS', FALSE);
		break;
	
	case 'test':
		error_reporting(E_ALL);
		ini_set('display_errors', 'On');
		define('DISPLAY_ERRORS', TRUE);
		break;

	case 'dev':
		error_reporting(E_ALL);
		ini_set('display_errors', 'On');
		define('DISPLAY_ERRORS', TRUE);
		break;

	default:
		exit('Invalid ENV');
		break;
}

require 'common.php';

set_error_handler('error_handler');
set_exception_handler('exception_handler');
register_shutdown_function('shutdown_function');

$application = new Yaf_Application( APPLICATION_PATH . "/conf/application.ini");

$application->bootstrap()->run();

load_class('session');
