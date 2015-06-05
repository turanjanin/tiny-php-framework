<?php

/**
 * Class Template
 */
class Template
{
	/**
	 * @var Template Singleton instance of Template class.
	 */
	private static $tplInstance;
	/**
	 * @var Smarty
	 */
	private $smarty;

	/**
	 * @throws SmartyException
	 */
	private function __construct()
	{
		$this->smarty = new Smarty();
		
		$this->smarty->template_dir = './templates/';
		$this->smarty->compile_dir  = './cache/';
		
		$this->smarty->assign('js_files', '');

		$this->smarty->registerPlugin('function', 'url', 'Router::urlHelper');
	}

	/**
	 * Assigns Smarty variable.
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return Smarty_Internal_Data
	 */
	public static function assign($name, $value)
	{
		return self::instance()->smarty->assign($name, $value);
	}

	/**
	 * Displays a Smarty template.
	 *
	 * @param string $name Relative path to the template.
	 */
	public static function display($name)
	{
		Event::emit(Event::PRE_DISPLAY);

		try {
			self::instance()->smarty->display($name);

		} catch (SmartyException $e) {
			if (Config::get('debug')) {
				Helper::debugError($e, ['template' => $name]);
			} else {
				die('Template error');
			}
		}

		Event::emit(Event::POST_DISPLAY);
	}

	/**
	 * Inserts link to javascript file in <head> portion of the template.
	 *
	 * @param string $file
	 */
	public static function addJavascript($file)
	{
		$html = '<script type="text/javascript" src="' . Config::get('site_url') . '/javascript/' . $file . '"></script>';
		
		Template::instance()->smarty->tpl_vars['js_files']->value .= "\n" . $html;
	}

	/**
	 * This magic method must be private to support Singleton pattern.
	 */
	private function __clone()
	{
	}

	/**
	 * Returns singleton instance of Template class.
	 *
	 * @return Template
	 */
	public static function instance()
	{
		if (!self::$tplInstance) {
			self::$tplInstance = new Template();
		}
		
		return self::$tplInstance;
	}
}
