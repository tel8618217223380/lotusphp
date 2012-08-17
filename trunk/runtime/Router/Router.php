<?php
/**
 * The Router class
 */
class LtRouter
{
	/**
	 * @var LtConfig
	 */
	public $configHandle;
	public $module;
	public $action;

	private $default = array('module'=>'default', 'action'=>'index'); // module action 的默认值
	private $delimiter = '-';    // 分隔符
	private $postfix = '.html';  // 后缀
	private $protocol = 'STANDARD'; // REWRITE PATH_INFO STANDARD

	public function __construct()
	{
		if (! $this->configHandle instanceof LtConfig)
		{
			if (class_exists("LtObjectUtil"))
			{
				$this->configHandle = LtObjectUtil::singleton("LtConfig");
			}
			else
			{
				$this->configHandle = new LtConfig;
			}
		}
	}

	public function init()
	{
		$routingTable = $this->configHandle->get("router.routing_table");

		if (!empty($routingTable))
		{
			if (isset($routingTable['default']))
			{
				$this->default = $routingTable['default'];
			}
			if (isset($routingTable['delimiter']))
			{
				$this->delimiter = $routingTable['delimiter'];
			}
			if (isset($routingTable['postfix']))
			{
				$this->postfix = $routingTable['postfix'];
			}
			if (isset($routingTable['protocol']))
			{
				$this->protocol = $routingTable['protocol'];
			}
		}

		$this->protocol = strtoupper($this->protocol);
		$this->module = $this->default['module'];
		$this->action = $this->default['action'];

		if (isset($_SERVER['SERVER_PROTOCOL'])) // HTTP HTTPS
		{
			$this->routeFromWeb();
		}
		else // CLI
		{
			$this->routeFromCli();
		}
	}

	private function routeFromWeb()
	{
		switch ($this->protocol)
		{
			case 'REWRITE':
				if (! $this->isStandardUrl())
				{
					$url = $this->getRewriteUrl();
					$this->setUrlToGet($url);
				}
				break;
			case 'PATH_INFO':
				if (! $this->isStandardUrl())
				{
					$this->delimiter = '/';
					$url = $this->getPathInfoUrl();
					$this->setUrlToGet($url);
				}
				break;
			default :
				$this->delimiter = '';
				$this->postfix = '';
				break;
		}
		if (isset($_GET['module']))
		{
			$this->module = $_GET['module'];
		}
		else
		{
			$_GET['module'] = $this->module;
		}
		if (isset($_GET['action']))
		{
			$this->action = $_GET['action'];
		}
		else
		{
			$_GET['action'] = $this->action;
		}
	}

	private function isStandardUrl()
	{
		if (strpos($_SERVER['REQUEST_URI'], '.php?module=') || strpos($_SERVER['REQUEST_URI'], '/?module='))
		{
			return true;
		}
		return false;
	}

	private function getRewriteUrl()
	{
		if (strcmp($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME']))
		{
			return substr($_SERVER['REQUEST_URI'], strlen(pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME)));
		}
		return '';
	}

	/*
	 * 不使用$_SERVER['PATH_INFO']是因为多个//自动合并成一个/
	 */
	private function getPathInfoUrl()
	{
		return substr($_SERVER['REQUEST_URI'], strlen($_SERVER['SCRIPT_NAME']));
	}

	private function setUrlToGet($url)
	{
		if (empty($url))
		{
			return false;
		}

		$url = str_replace($this->postfix, '', $url);
		$url = str_replace(array("?", "&", "="), $this->delimiter, $url);

		$arr = explode($this->delimiter, ltrim($url, "/"));

		if (count($arr) > 1)
		{
			$this->module = array_shift($arr);
			$this->action = array_shift($arr);
			$_GET['module'] = $this->module;
			$_GET['action'] = $this->action;
			$this->setValueToGet($arr);
		}
		return true;
	}

	private function setValueToGet($arr, $start = 0)
	{
		$i = $start;
		while (isset($arr[$i]) && isset($arr[$i + 1]))
		{
			$key = $arr[$i];
			if ($key !== '')
			{
				$arr[$i + 1] = str_replace('%FF', rawurlencode($this->delimiter), $arr[$i + 1]);
				$key = str_replace('%FF', $this->delimiter, $key);
				$_GET[$key] = rawurldecode($arr[$i + 1]);
			}
			$i = $i + 2;
		}
	}

	private function routeFromCli()
	{
		$arr = $_SERVER['argv'];
		array_shift($arr);
		$i = 0;
		while (isset($arr[$i]) && isset($arr[$i + 1]))
		{
			$key = rawurldecode(ltrim($arr[$i], '-'));
			$_GET[$key] = rawurldecode($arr[$i + 1]);
			$i = $i + 2;
		}

		if (isset($_GET['m']))
		{
			$this->module = $_GET['m'];
		}
		elseif (isset($_GET['module']))
		{
			$this->module = $_GET['module'];
		}

		if (isset($_GET['a']))
		{
			$this->action = $_GET['a'];
		}
		elseif (isset($_GET['action']))
		{
			$this->action = $_GET['action'];
		}
	}

	public function __toString()
	{
		return $this->module.'/'.$this->action;
	}
}
