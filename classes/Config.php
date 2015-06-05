<?php

/**
 * Class Config
 */
class Config
{
	/**
	 * @var bool Stores if data is loaded.
	 */
	private static $init = false;
	/**
	 * @var array Stores configuration data.
	 */
	private static $data = array();

	/**
	 * Loads data from /config/settings.php file.
	 */
	protected static function loadData()
	{
		$settings = include __DIR__ . '/../config/settings.php';

		self::$data = array_merge(self::$data, $settings);
	}

	/**
	 * Return configuration value.
	 *
	 * @param $key Key of value to be returned.
	 * @param string $default Default value to be returned if it doesn't exist.
	 * @return string
	 */
	public static function get($key, $default = '')
	{
		if (!self::$init) {
			self::loadData();
			self::$init = true;
		}
		
		if (isset(self::$data[$key])) {
			return self::$data[$key];
		}

		return $default;
	}

	/**
	 * Perform in-memory update of configuration values.
	 *
	 * @param $key
	 * @param $value
	 */
	public static function set($key, $value)
	{
		self::$data[$key] = $value;
	}
}
