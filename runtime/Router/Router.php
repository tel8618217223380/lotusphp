<?php
/**
 * The Router class
 */
class LtRouter
{
	// public $routingTable = array(
	// 'pattern' => ":module/:action/*",
	// 'default' => array('module' => 'default', 'action' => 'index'),
	// 'reqs' => array('module' => '[a-zA-Z0-9\.\-_]+', 'action' => '[a-zA-Z0-9\.\-_]+'),
	// 'varprefix' => ':',
	// 'delimiter' => '/'
	// );
	public $routingTable;
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
		$ret = $this->routingTable['default']; //返回值
		$reqs = $this->routingTable['reqs'];
		$delimiter = $this->routingTable['delimiter'];
		$varprefix = $this->routingTable['varprefix'];
		$pattern = explode($delimiter, trim($this->routingTable['pattern'], $delimiter));

		/**
		 * 预处理url
		 */
		$url = explode($delimiter, trim($url, $delimiter));

		foreach($pattern as $k => $v)
		{
			if ($v[0] == $varprefix)
			{ 
				// 变量
				$varname = substr($v, 1); 
				// 匹配变量
				if (isset($url[$k]))
				{
					if (isset($this->routingTable['reqs'][$varname]))
					{
						$regex = "/^{$this->routingTable['reqs'][$varname]}\$/i";
						if (preg_match($regex, $url[$k]))
						{
							$ret[$varname] = $url[$k];
						}
					}
				}
			}
			else if ($v[0] == '*')
			{ 
				// 通配符
				$pos = $k;
				while (isset($url[$pos]) && isset($url[$pos + 1]))
				{
					$ret[$url[$pos ++]] = $url[$pos];
					$pos++;
				}
			}
			else
			{ 
				// 静态
			}
		}

		$this->params = $ret;
	}

	public function url($params)
	{
		$url = $params;
		$ret = $this->routingTable['pattern'];
		$default = $this->routingTable['default'];
		$reqs = $this->routingTable['reqs'];
		$delimiter = $this->routingTable['delimiter'];
		$varprefix = $this->routingTable['varprefix'];

		$pattern = explode($delimiter, trim($this->routingTable['pattern'], $delimiter));

		foreach($pattern as $k => $v)
		{
			if ($v[0] == $varprefix)
			{ 
				// 变量
				$varname = substr($v, 1); 
				// 匹配变量
				if (array_key_exists($varname, $url))
				{
					$regex = "/^{$this->routingTable['reqs'][$varname]}\$/i";
					if (preg_match($regex, $url[$varname]))
					{
						$ret = str_replace($v, $url[$varname], $ret);
						unset($url[$varname]);
					}
				}
			}
			else if ($v[0] == '*')
			{ 
				// 通配符
				$tmp = '';
				foreach($url as $key => $value)
				{
					if (!isset($default[$key]))
					{
						$tmp .= $key . $delimiter . $value . $delimiter;
					}
				}
				$tmp = rtrim($tmp, $delimiter);
				$ret = str_replace($v, $tmp, $ret);
			}
			else
			{ 
				// 静态
			}
		}
		return $ret;
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
