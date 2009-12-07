<?php
/**
* The Router class
*/
class LtRouter
{
	public $module;
	public $action;

	public function __construct()
	{
		$this->conf = new LtRouterConfig();
		$this -> route();
	}

	public function route()
	{
		$module = '';
		$action = '';
		if (isset($_SERVER['SERVER_PROTOCOL']))
		{	// index.php?module=module&action=action
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
				list($module,$action) = explode('/', trim($_SERVER['PATH_INFO'], '/'));
			}
		}
		else
		{	// CLIģʽ
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
		// module����ֻ�ܴ�Сд��ĸ ���� . - _
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
		// action ����ֻ�ܴ�Сд��ĸ ���� . - _
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
		if(empty($module) && empty($action))
		{
			$module = $this->conf->defaultModule;
			$action = $this->conf->defaultAction;
		}
		$this->setModule($module);
		$this->setAction($action);
		return true;
	}

	public function getModule()
	{
		return $this->module;
	}

	public function getAction()
	{
		return $this->action;
	}

	public function setModule($module)
	{
		$this->module = $module;
	}

	public function setAction($action)
	{
		$this->action = $action;
	}
}
