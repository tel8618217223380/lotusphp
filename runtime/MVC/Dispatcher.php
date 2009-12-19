<?php
/**
 * The Dispatcher class
 */
class LtDispatcher
{
	public $appDir;

	public function __construct()
	{
	}

	protected function _dispatch($module, $action, $context = null, $classType = "Action")
	{
		$classType = ucfirst($classType);
		$actionClassName = $action . $classType;
		$actionFile = $this->appDir . 'module' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . strtolower($classType) . DIRECTORY_SEPARATOR . $actionClassName . '.php';
		if (file_exists($actionFile))
		{
			if (!in_array($actionFile, get_included_files()))
			{
				include($actionFile);
			}
		}
		else
		{
			if (function_exists("on{$classType}FileNotFound"))
			{
				call_user_func("on{$classType}FileNotFound", $module, $action);
			}
			else
			{
				throw new Exception("$classType file not found: $module/$action");
			}
		}
		if (!class_exists($actionClassName))
		{
			DebugHelper::debug("{$classType}_CLASS_NOT_FOUND", array(strtolower($classType) => $action));
		}
		else
		{
			if (!($context instanceof Context))
			{
				$newContext = new LtContext();
			}
			else
			{
				$newContext = clone $context;
			}
			$newContext->uri['module'] = $module;
			$newContext->uri[strtolower($classType)] = $action;
			$actionInstance = new $actionClassName($newContext);
			/**
			 * Logic of $actionInstance->afterConstruct();
			 */
			$actionInstance->executeChain();
		}
	}

	/**
	 * Disptach the module/action calling.
	 *
	 * @param $module string
	 * @param $action string
	 * @return void
	 * @todo allow one action dispatch another action
	 */
	public function dispatchAction($module, $action, $context = null)
	{
		$this->_dispatch($module, $action, $context);
	}

	/**
	 * Disptach the module/component calling.
	 *
	 * @param $module string
	 * @param $component string
	 * @param $data mixed
	 * @return void
	 */
	public function dispatchComponent($module, $component, $context = null)
	{
		$cloneOfContext = clone $context;
		$this->_dispatch($module, $component, $cloneOfContext, "Component");
	}
}
