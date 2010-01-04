<?php
/**
 * The Router class
 */
class LtRouter
{
	public $module;
	public $action;
	public $conf;

	public function __construct()
	{
		$this->conf = new LtRouterConfig;
	}

	public function matchingRoutingTable()
	{
		$ret = array();
		$url = "news/list/catid/4/page/10";
		$url = explode('/', $url);
		$routingTable['pattern'] = "{module}/{action}/{*}";
//		$routingTable['config'] = array(
//			'module' => '([a-z][a-z0-9]*)*',
//			'action' => '[a-z][a-z0-9]*)*',
//			);
//		$routingTable['defaltVar'] = array(
//			'module' => 'default',
//			'action' => 'index',
//			);
		$pattern = $routingTable['pattern'];

		preg_match_all("/\{([a-z]+|\*)\}/", $pattern, $out);
		foreach($out[1] as $k => $v)
		{
			if ('*' != $v)
			{
				if (isset($url[$k]))
				{ 
					// $regex = "/^{$routingTable['config'][$v]}\$/i";
					// if (preg_match($regex, $url[$k]))
					// {
					$ret[$v] = $url[$k]; 
					// }
				}
			}
			else
			{
				while (isset($url[$k]))
				{
					$ret[$url[$k ++]] = $url[$k];
					$k++;
				}
			}
		}
		return $ret;
	}

	public function init()
	{
		$module = '';
		$action = '';
		if (isset($_SERVER['SERVER_PROTOCOL'])) // index.php?module=module&action=action
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
		else // CLI模式
		{
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
		$this->module = $module ? $module : $this->conf->module;
		$this->action = $action ? $action : $this->conf->action;
		unset($this->conf);
	}
}
