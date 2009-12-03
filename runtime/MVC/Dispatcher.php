<?php
/**
 * The Dispatcher class
 */
class LtDispatcher
{
	protected function _dispatch($module, $action, $context = null, $classType = "Action")
	{
		$classType = ucfirst($classType);
		$actionClassName = $action . $classType;
		$actionFile = Kiwi::$appOptions["app_dir"] . 'module' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . strtolower($classType) . DIRECTORY_SEPARATOR . $actionClassName . '.php';
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
				$newContext = new Context();
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
			if (null === $actionInstance->responseType)
			{
				$actionInstance->responseType = "html";
			}
			if (null === $actionInstance->view)
			{
				$actionInstance->view = View::factory($actionInstance->responseType);
			}
			if ("html" == $actionInstance->responseType)
			{
				$actionInstance->view->templateDir = Kiwi::$appOptions["app_dir"] . 'module' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR;
				$actionInstance->view->template = $action;
				$actionInstance->view->layoutDir = Kiwi::$appOptions["app_dir"] . 'layout' . DIRECTORY_SEPARATOR;
			}
			$actionInstance->view->context = $actionInstance->context;
			if (!$actionInstance->constructed)
			{
				DebugHelper::debug('SUBCLASS_NOT_CALL_PARENT_CONSTRUCTOR', array('class' => $actionClassName));
			}
			$actionInstance->beforeExecute();
			call_user_func(array($actionInstance, 'execute'));
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
