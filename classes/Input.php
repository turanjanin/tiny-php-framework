<?php

/**
 * Class Input
 *
 * Utility class for handling user input. All methods in this class are defined as static.
 */
class Input
{
	/**
	 * Returns $_GET value, if exists. Otherwise, returns default value.
	 *
	 * @param $name Key from $_GET array.
	 * @param string $default Default value to be returned if $_GET[$name] doesn't exist.
	 * @return string
	 */
	public static function get($name, $default = '')
	{
		return (isset($_GET[$name])) ? $_GET[$name] : $default;
	}

	/**
	 * Returns $_POST value, if exists. Otherwise, returns default value.
	 *
	 * @param $name Key form $_POST array.
	 * @param string $default Default value to be returned if $_POST[$name] doesn't exist.
	 * @return string
	 */
	public static function post($name, $default = '')
	{
		return (isset($_POST[$name])) ? $_POST[$name] : $default;
	}

	/**
	 * Returns name of current module.
	 *
	 * @uses Router::getModule()
	 * @return string
	 */
	public static function routeModule()
	{
		return Router::getModule();
	}

	/**
	 * Returns name of current action.
	 *
	 * @uses Router::getAction()
	 * @return string
	 */
	public static function routeAction()
	{
		return Router::getAction();
	}

	/**
	 * Returns parameter from current route.
	 *
	 * @uses Router::getParam()
	 * @param $name Name of parameter to be returned.
	 * @param string $default Default value to be returned if parameter doesn't exist.
	 * @return string
	 */
	public static function routeParam($name, $default = '')
	{
		return Router::getParam($name, $default);
	}
}
