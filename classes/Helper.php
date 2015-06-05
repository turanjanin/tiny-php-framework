<?php

/**
 * Class Helper
 */
class Helper
{
	/**
	 * Redirects user to given URL and stops execution of a script.
	 *
	 * @param string $url URL to be redirected to.
	 * @return void
	 */
	public static function redirect($url)
	{
		header('Location: ' . $url);
		exit;
	}

	/**
	 * Redirects user to given route.
	 *
	 * @param string $name Name of the route to be redirected to.
	 * @param array $params Optional parameters for assembling route's URL.
	 */
	public static function redirectToRoute($name, $params = array())
	{
		self::redirect(Router::generate($name, $params));
	}


	/**
	 * Returns file extension from filename.
	 *
	 * @param string $filename
	 * @return string
	 */
	public static function getExtension($filename)
	{
		$tmp = explode('.', $filename);
		$ext = array_pop($tmp);

		return $ext;
	}

	/**
	 * Displays error message inside error.tpl template.
	 *
	 * @param string $message Error message to be displayed.
	 */
	public static function showError($message)
	{
		Template::assign('page_title', 'There was an error');
		Template::assign('message', $message);
		Template::display('error.tpl');
		exit;
	}

	/**
	 * Displays human-friendly debug message.
	 *
	 * @param Exception $e
	 * @param array $data Additional debug info to be displayed.
	 */
	public static function debugError(Exception $e, array $data = array())
	{
		$html = '<blockquote><div><strong>Exception:</strong> ' . $e->getMessage() . '</div><pre>';

		foreach ($data as $key => $value) {
			$html .= '<div><strong>' . ucfirst($key) . ':</strong> ' . print_r($value, true) . '</div>';
		}

		$html .= '</pre></blockquote>';

		die($html);
	}
}
