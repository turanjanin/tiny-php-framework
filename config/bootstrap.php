<?php
/**
 * This file can be used for bootstrapping your application. You can set global variables,
 * subscribe to events, init additional services, etc...
 */

// Ensure that we can see all errors in debug mode.
if (Config::get('debug')) {
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}

// Try to infer base path and site URL from environment variables.
// TODO: test this in various environments and setups.
$basePath = str_replace(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '', str_replace('\\', '/', dirname(__DIR__)));

Config::set('base_path', $basePath);
Config::set('site_url', '//' . $_SERVER['HTTP_HOST'] . $basePath);


//date_default_timezone_set('Europe/Belgrade');

// Add your own code here...