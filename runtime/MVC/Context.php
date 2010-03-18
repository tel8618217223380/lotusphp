<?php
class LtContext
{
	/**
	 * The uri property
	 * 
	 * @var array 
	 */
	public $uri;

	protected $strip;

	public function __construct()
	{
		/**
		 * set_magic_quotes_runtime(0)
		 */
		if (version_compare(PHP_VERSION, '6.0.0-dev', '<') && get_magic_quotes_gpc())
		{
			$this->strip = true;
		}
		else
		{
			$this->strip = false;
		}
	}

	/**
	 * return the client input in $_SERVER['argv']
	 * 
	 * @param integer $offset 
	 * @return string 
	 */
	public function argv($offset)
	{
		return isset($_SERVER['argv']) && isset($_SERVER['argv'][$offset]) ? $_SERVER['argv'][$offset] : null;
	}

	/**
	 * return the client input in $_FILES
	 * 
	 * @param string $name 
	 * @return array 
	 */
	public function file($name)
	{
		return isset($_FILES[$name]) ? $_FILES[$name] : null;
	}

	/**
	 * return the client input in $_GET
	 * 
	 * @param string $name 
	 * @return string 
	 */
	public function get($name)
	{
		if (isset($_GET[$name]))
		{
			return $this->strip ? stripslashes($_GET[$name]) : $_GET[$name];
		}
		else
		{
			return null;
		}
	}

	/**
	 * return the client input in $_POST
	 * 
	 * @param string $name 
	 * @return string 
	 */
	public function post($name)
	{
		if (isset($_POST[$name]))
		{
			return $this->strip ? stripslashes($_POST[$name]) : $_POST[$name];
		}
		else
		{
			return null;
		}
	}

	/**
	 * return the client input in $_REQUEST
	 * 
	 * @param string $name 
	 * @return string 
	 */
	public function request($name)
	{
		return isset($_REQUEST[$name]) ? $_REQUEST[$name] : null;
	}

	/**
	 * return the client input in $_SERVER
	 * 
	 * @param string $name 
	 * @return string 
	 */
	public function server($name)
	{
		if ('REMOTE_ADDR' == $name)
		{
			if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			{
				$clientIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
			else
			{
				$clientIp = $_SERVER[$name];
			}
			return $clientIp;
		}
		else
		{
			return isset($_SERVER[$name]) ? $_SERVER[$name] : null;
		}
	}
}
