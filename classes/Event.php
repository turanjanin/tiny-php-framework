<?php

/**
 * Class Event
 */
class Event
{
	/**
	 * @var array Two-dimensional list of events and subscribed listeners.
	 */
	protected static $listeners = array();

	const APP_INIT          = 'app.init';
	const ROUTE_NOT_FOUND   = 'app.route_not_found';
	const PRE_DISPATCH      = 'app.pre_dispatch';
	const POST_DISPATCH     = 'app.post_dispatch';
	const PRE_DISPLAY       = 'app.pre_display';
	const POST_DISPLAY      = 'app.post_display';
	const APP_SHUTDOWN      = 'app.shutdown';


	/**
	 * Subscribes to event.
	 *
	 * @param $name Name of event to subscribe to.
	 * @param callable $listener Function to be executed when event occurs.
	 */
	public static function subscribe($name, callable $listener)
	{
		if (isset(self::$listeners[$name])) {
			self::$listeners[$name][] = $listener;
		} else {
			self::$listeners[$name] = array($listener);
		}
	}

	/**
	 * Emits new event.
	 *
	 * @param $name Name of event to be emitted.
	 * @param array $data Additional data that describes event.
	 */
	public static function emit($name, $data = array())
	{
		if (isset(self::$listeners[$name])) {
			foreach (self::$listeners[$name] as $listener) {
				call_user_func($listener, $name, $data);
			}
		}
	}
}

