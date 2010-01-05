<?php
/**
 * The Router class
 */
class LtRouter
{
	public $routingTable = array('pattern' => "{module}/{action}/{*}",
		'default' => array('module' => 'default', 'action' => 'index'),
		'reqs' => array('module' => '[a-zA-Z0-9\.\-_]+', 'action' => '[a-zA-Z0-9\.\-_]+'),
		'delimiter' => '/'
		);
	public $module;
	public $action;
	public $params;

	public function __construct()
	{
	}
	/**
	 * $url = "news/list/catid/4/page/10";
	 */
	public function matchingRoutingTable($url)
	{
		$pattern = $this->routingTable['pattern'];
		$default = $this->routingTable['default'];
		$reqs = $this->routingTable['reqs'];
		$delimiter = $this->routingTable['delimiter'];

		$url = explode($delimiter, $url);
		preg_match_all("/\{([a-z]+|\*)\}/", $pattern, $out);
		foreach($out[1] as $k => $v)
		{
			if ('*' != $v)
			{
				if (isset($url[$k]))
				{
					$regex = "/^{$this->routingTable['reqs'][$v]}\$/i";
					if (preg_match($regex, $url[$k]))
					{
						$ret[$v] = $url[$k];
					}
				}
			}
			else
			{
				while (isset($url[$k]) && isset($url[$k + 1]))
				{
					$ret[$url[$k ++]] = $url[$k];
					$k++;
				}
			}
		}
		$this->params = $ret;
	}

	public function url($params)
	{
		$url = $params;
		$pattern = $this->routingTable['pattern'];
		$default = $this->routingTable['default'];
		$reqs = $this->routingTable['reqs'];
		$delimiter = $this->routingTable['delimiter'];
		preg_match_all("/\{([a-z]+|\*)\}/", $pattern, $out);
		foreach($out[1] as $k => $v)
		{
			if ('*' != $v)
			{
				if (array_key_exists($v, $url))
				{
					$regex = "/^{$this->routingTable['reqs'][$v]}\$/i";
					if (preg_match($regex, $url[$v]))
					{
						$pattern = str_replace($out[0][$k], $url[$v], $pattern);
						unset($url[$v]);
					}
				}
			}
			else
			{
				$tmp = '';
				foreach($url as $key => $value)
				{
					$tmp .= $key . $delimiter . $value . $delimiter;
				}
				$tmp = rtrim($tmp, $delimiter);
				$pattern = str_replace($out[0][$k], $tmp, $pattern);
			}
		}
		return $pattern;
	}

	public function init()
	{
		$module = '';
		$action = ''; 
		// index.php?module=module&action=action
		if (isset($_SERVER['SERVER_PROTOCOL']))
		{
			if (isset($_REQUEST['module']))
			{
				$module = $_REQUEST['module'];
			}
			if (isset($_REQUEST['action']))
			{
				$action = $_REQUEST['action'];
			} 
			// index.php/module/action/
			if (empty($module) && empty($action) && isset($_SERVER['PATH_INFO']))
			{
				list($module, $action) = explode('/', trim($_SERVER['PATH_INFO'], '/'));
			}
		}
		else
		{ 
			// CLI模式
			$i = 0;
			while ((empty($module) || empty($action)) && isset($_SERVER['argv'][$i]))
			{
				if (("-m" == $_SERVER['argv'][$i] || "--module" == $_SERVER['argv'][$i]) && isset($_SERVER['argv'][$i + 1]))
				{
					$module = $_SERVER['argv'][$i + 1];
				}
				else if (("-a" == $_SERVER['argv'][$i] || "--action" == $_SERVER['argv'][$i]) && isset($_SERVER['argv'][$i + 1]))
				{
					$action = $_SERVER['argv'][$i + 1];
				}
				$i ++;
			}
		} 
		// module名字只能大小写字母 数字 . - _
		if (!empty($module) && !preg_match('/^[a-zA-Z0-9\.\-_]+$/', $module))
		{
			if (function_exists('onModuleNameIllegal'))
			{
				call_user_func('onModuleNameIllegal', $module);
			}
			else
			{
				throw new Exception("Module name is illegal: {$module}");
			}
		} 
		// action 名字只能大小写字母 数字 . - _
		if (!empty($action) && !preg_match('/^[a-zA-Z0-9\.\-_]+$/', $action))
		{
			if (function_exists('onaActionNameIllegal'))
			{
				call_user_func('onaActionNameIllegal', $action);
			}
			else
			{
				throw new Exception("Action name is illegal: {$action}");
			}
		}
		$this->module = $module ? $module : 'Module';
		$this->action = $action ? $action : 'Action';
	}
}
