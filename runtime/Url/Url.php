<?php
class LtUrl
{
	public static $configHandle;
	public $routingTable;
	public $baseUrl;

	public function __construct()
	{
		$this->routingTable = array('pattern' => ":module/:action/*",
			'default' => array('module' => 'default', 'action' => 'index'),
			'reqs' => array('module' => '[a-zA-Z0-9\.\-_]+',
				'action' => '[a-zA-Z0-9\.\-_]+'
				),
			'varprefix' => ':',
			'delimiter' => '/',
			'postfix' => '',
			'protocol' => 'PATH_INFO', // REWRITE STANDARD
			);
		self::$configHandle = new LtConfig;
	}
	public function init()
	{
		if ($tmp = self::$configHandle->get("router.routing_table"))
		{
			$this->routingTable = $tmp;
		}
		$protocol = strtoupper($this->routingTable['protocol']);
		if ('REWRITE' == $protocol)
		{
			$this->baseUrl = pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME) . '/';
		}
		else
		{
			$this->baseUrl = '';
		}
	}

	public function generate($module, $action, $args = array())
	{
		$args['module'] = $module;
		$args['action'] = $action;
		$url = ''; 
		// $url = $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
		// $url .= $_SERVER['HTTP_HOST'];
		// $url .= $_SERVER['SERVER_PORT'] == '80' ? '' : ':'.$_SERVER['SERVER_PORT'];
		$url .= $this->baseUrl;
		$url .= $this->reverseMatchingRoutingTable($args);
		return $url;
	}

	/**
	 * 将变量反向匹配路由表, 返回匹配后的url
	 * 
	 * @param array $params 
	 * @return string 
	 */
	public function reverseMatchingRoutingTable($args)
	{
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
				if (isset($args[$varname]))
				{
					$regex = "/^{$this->routingTable['reqs'][$varname]}\$/i";
					if (preg_match($regex, $args[$varname]))
					{
						$ret = str_replace($v, $args[$varname], $ret);
						unset($args[$varname]);
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
				foreach($args as $key => $value)
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
			$ret = $_SERVER['SCRIPT_NAME'] . $delimiter . $ret . $postfix;
		}
		else
		{
			$ret = $ret . $postfix;
		}
		return $ret;
	}
}
