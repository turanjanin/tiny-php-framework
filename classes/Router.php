<?php

/**
 * Class Router
 */
class Router
{
	/**
	 * @var Router Singleton instance of Router class.
	 */
	private static $routerInstance;
	/**
	 * @var AltoRouter
	 */
	private $altoRouter;

	/**
	 * @var string Name of matched route.
	 */
	private $name;
	/**
	 * @var string Name of matched module.
	 */
	private $module;
	/**
	 * @var string Name of matched action.
	 */
	private $action;
	/**
	 * @var array Additional parameters in matched route.
	 */
	private $params = array();

	/**
	 * Loads available routes from /config/routes.php and performs matching.
	 *
	 * If route is not found, 404 error will be displayed.
	 */
	private function __construct()
	{
		$this->altoRouter = new AltoRouter();
		$this->altoRouter->setBasePath(Config::get('base_path'));

		$routes = include __DIR__ . '/../config/routes.php';

		try {
			foreach ($routes as $route) {
				$method = (!empty($route[3])) ? $route[3] : 'GET';

				$name = (!empty($route[2])) ? $route[2] : null;

				$this->altoRouter->map($method, $route[0], $route[1], $name);
			}
		} catch (Exception $e) {
			Helper::debugError($e);
		}


		$match = $this->altoRouter->match();

		if ($match === false) {
			Event::emit(Event::ROUTE_NOT_FOUND);
			self::error404();
		}

		$this->name = $match['name'];

		$target = explode('.', $match['target']);
		$this->module = ucwords($target[0]) . 'Module';
		$this->action = $target[1];

		$this->params = $match['params'];
	}

	/**
	 * Calls recognized action from matched module.
	 */
	public function callAction()
	{
		$class = $this->module;
		
		$object = new $class();

		Event::emit(Event::PRE_DISPATCH);
		
		call_user_func(array($object, $this->action));

		Event::emit(Event::POST_DISPATCH);
	}

	/**
	 * Returns module name.
	 *
	 * @return string
	 */
	public static function getModule()
	{
		return self::instance()->module;
	}

	/**
	 * Returns module action.
	 *
	 * @return string
	 */
	public static function getAction()
	{
		return self::instance()->action;
	}

	/**
	 * Returns value of route parameter.
	 *
	 * @param string $name
	 * @param string $default
	 * @return string
	 */
	public static function getParam($name, $default = '')
	{
		$router = self::instance();

		if (isset($router->params[$name])) {
			return $router->params[$name];
		}

		return $default;
	}

	/**
	 * Returns if this request is asynchronous.
	 *
	 * @return bool
	 */
	public static function isAjaxRequest()
	{
		return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
			&& strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	}

	/**
	 * Generate URL based on route name and parameters.
	 *
	 * @param string $name Route name.
	 * @param array $params Optional route parameters.
	 * @return string Assembled URL.
	 * @throws Exception When route doesn't exist.
	 */
	public static function generate($name, $params = array())
	{
		return self::instance()->altoRouter->generate($name, $params);
	}

	/**
	 * Smarty helper for using generate() inside templates.
	 *
	 * Usage in templates: {url route='route_name' param1='value'1 param2='value2'...}
	 *
	 * @param array $params
	 * @param Smarty $smarty
	 * @return string Assembled URL.
	 */
	public static function urlHelper($params, &$smarty)
	{
		$route = $params['route'];
		unset($params['route']);
		
		return self::generate($route, $params);
	}

	/**
	 * Shows 404 - Not found page.
	 *
	 * @param string $message
	 */
	public static function error404($message = '404 - Not Found')
	{
		header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
		
		Helper::showError($message);
	}

	/**
	 * This magic method must be private to support Singleton pattern.
	 */
	private function __clone()
	{
	}

	/**
	 * Returns singleton instance of Router class.
	 *
	 * @return Router
	 */
	public static function instance()
	{
		if (!self::$routerInstance) {
			self::$routerInstance = new Router();
		}
		
		return self::$routerInstance;
	}
}
