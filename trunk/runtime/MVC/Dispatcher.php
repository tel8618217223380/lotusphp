<?php
/**
 * The Dispatcher class
 */
class LtDispatcher
{
	public $configHandle;
	public $viewDir;
	public $viewTplDir;
	public $viewTplAutoCompile;
	public $data;

	public function __construct()
	{

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

	protected function _dispatch($module, $action, $context = null, $classType = "Action")
	{
		$classType = ucfirst($classType);
		$actionClassName = $module . $action . $classType;
		if (!class_exists($actionClassName))
		{
			$this->error_404();
			//DebugHelper::debug("{$classType}_CLASS_NOT_FOUND", array(strtolower($classType) => $action));
			//trigger_error("{$actionClassName} CLASS NOT FOUND! module={$module} action={$action} classType={$classType}");
		}
		else
		{
			if (!($context instanceof LtContext))
			{
				$newContext = new LtContext;
			}
			else
			{
				$newContext = clone $context;
			}
			$newContext->uri['module'] = $module;
			$newContext->uri[strtolower($classType)] = $action;
			$actionInstance = new $actionClassName();
			$actionInstance->configHandle = $this->configHandle;
			$actionInstance->context = $newContext;
			$actionInstance->viewDir = $this->viewDir;
			$actionInstance->viewTplDir = $this->viewTplDir; // 模板编译目录
			$actionInstance->viewTplAutoCompile = $this->viewTplAutoCompile;
			$actionInstance->executeChain();
			$this->data = $actionInstance->data;
		}
	}
	
	protected function error_404()
	{
		header("HTTP/1.0 404 Not Found");
		header("Status: 404 Not Found");
		if ($this->configHandle instanceof LtConfig)
		{
			$filename = $this->configHandle->get('error_404');
			if(is_file($filename))
			{
				include $filename;
				exit();
			}
		}
		// 必需大于 512 bytes，否则404在某些浏览器中不显示
		echo '<!DOCTYPE html ><html><head><title>Error 404</title></head><body>404 Not Found
                                                                                                    
                                                                                                    
                                                                                                    
                                                                                                    
                                                                                                    
            </body></html>';
		exit();
	}
}
