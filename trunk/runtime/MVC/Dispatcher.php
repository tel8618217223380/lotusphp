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
		$actionClassName = $module . $action . $classType;
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
			$actionInstance->context = $newContext;
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
