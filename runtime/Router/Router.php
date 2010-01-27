<?php
/**
 * The Router class
 */
class LtRouter
{ 
	public $conf;
	public $routingTable;
	public $module;
	public $action;

	public function __construct()
	{
		$this->conf = new LtRouterConfig;
		$this->routingTable = $this->conf->routingTable;
		// unset($this->conf);
	}

	public function init()
	{
		$delimiter = $this->routingTable['delimiter'];
		$postfix = $this->routingTable['postfix'];
		$module = '';
		$action = '';
		$params = array(); 
		// HTTP HTTPS
		if (isset($_SERVER['SERVER_PROTOCOL']))
		{
			if (isset($_SERVER['PATH_INFO']))
			{ 
				// 忽略后缀
				$url = rtrim($_SERVER['PATH_INFO'], "$postfix");
				$url = explode($delimiter, trim($url, "/"));
			}
			else
			{
				$url = array();
				foreach($_GET as $v)
				{
					$url[]=$v;
				}
			}
			$params = $this->matchingRoutingTable($url);
			$module = $params['module'];
			$action = $params['action'];
		}
		else
		{ 
			// CLI
			$i = 0;
			while (isset($_SERVER['argv'][$i]) && isset($_SERVER['argv'][$i + 1]))
			{
				if (("-m" == $_SERVER['argv'][$i] || "--module" == $_SERVER['argv'][$i]))
				{
					$module = $_SERVER['argv'][$i + 1];
				}
				else if (("-a" == $_SERVER['argv'][$i] || "--action" == $_SERVER['argv'][$i]))
				{
					$action = $_SERVER['argv'][$i + 1];
				}
				else
				{
					$key = $_SERVER['argv'][$i];
					$params[$key] = $_SERVER['argv'][$i + 1];
				}
				$i = $i + 2;
			}
		}
		// 如果$_GET中不存在配置的变量则添加 
		foreach($params as $k=>$v)
		{
			!isset($_GET[$k]) && $_GET[$k] = $v;
		}
		$this->module = $module;
		$this->action = $action;
	}

	/**
	 * url 匹配路由表
	 * 
	 * @param  $ [string|array] $url
	 * @return 
	 */
	public function matchingRoutingTable($url)
	{
		$ret = $this->routingTable['default']; //初始化返回值为路由默认值
		$reqs = $this->routingTable['reqs'];
		$delimiter = $this->routingTable['delimiter'];
		$varprefix = $this->routingTable['varprefix'];
		$postfix = $this->routingTable['postfix'];
		$pattern = explode($delimiter, trim($this->routingTable['pattern'], $delimiter));

		/**
		 * 预处理url
		 */
		if (is_string($url))
		{
			$url = rtrim($url, $postfix); //忽略后缀
			$url = explode($delimiter, trim($url, $delimiter));
		}

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
					$ret[$url[$pos ++]] = urldecode($url[$pos]);
					$pos++;
				}
			}
			else
			{ 
				// 静态
			}
		}
		return $ret;
	}
	/**
	 * 将变量反向匹配路由表, 返回匹配后的url
	 * 
	 * @param array $params 
	 * @return string 
	 */
	public function reverseMatchingRoutingTable($params)
	{
		$url = $params;
		$ret = $this->routingTable['pattern'];
		$default = $this->routingTable['default'];
		$reqs = $this->routingTable['reqs'];
		$delimiter = $this->routingTable['delimiter'];
		$varprefix = $this->routingTable['varprefix'];
		$postfix = $this->routingTable['postfix'];

		$pattern = explode($delimiter, trim($this->routingTable['pattern'], $delimiter));

		foreach($pattern as $k => $v)
		{
			if ($v[0] == $varprefix)
			{ 
				// 变量
				$varname = substr($v, 1); 
				// 匹配变量
				if (isset($url[$varname]))
				{
					$regex = "/^{$this->routingTable['reqs'][$varname]}\$/i";
					if (preg_match($regex, $url[$varname]))
					{
						$ret = str_replace($v, $url[$varname], $ret);
						unset($url[$varname]);
					}
				}
				else if (isset($default[$varname]))
				{
					$ret = str_replace($v, $default[$varname], $ret);
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
						$tmp .= $key . $delimiter . rawurlencode($value) . $delimiter;
					}
				}
				$tmp = rtrim($tmp, $delimiter);
				$ret = str_replace($v, $tmp, $ret);
				$ret = rtrim($ret, $delimiter);
			}
			else
			{ 
				// 静态
			}
		}
		$protocol = strtoupper($this->routingTable['protocol']);
		if ('REWRITE' == $protocol)
		{
			$ret = $ret . $postfix;
		}
		else if ('PATH_INFO' == $protocol)
		{
			$ret = $_SERVER['SCRIPT_NAME'] . '/' . $ret . $postfix;
		}
		else
		{
			$ret = $ret . $postfix;
		}
		return $ret;
	}

	public function url($module, $action, $args = array())
	{
		$args['module'] = $module;
		$args['action'] = $action;
		return $this->reverseMatchingRoutingTable($args);
	}
}
