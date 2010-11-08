<?php
class Lotus
{
	/**
	 * Lotus Option array
	 * 
	 * @var array array();
	 */
	public $option;
	public $devMode = true;

	protected $proj_dir;
	protected $app_dir;
	protected $app_name;
	protected $app_tmp;
	public $autoloadPath;
	protected $lotusRuntimeDir;
	protected $coreCacheHandle;
	protected $configHandle;

	public function __construct()
	{
		$this->lotusRuntimeDir = dirname(__FILE__) . DIRECTORY_SEPARATOR;
	}

	public function init()
	{
		if (isset($this->option["proj_dir"]) && !empty($this->option["proj_dir"]))
		{
			$this->app_dir = rtrim($this->option["app_dir"], '\\/') . '/';
			if (isset($this->option["app_name"]))
			{
				$this->app_dir = $this->app_dir . $this->option["app_name"] . '/';
			}
	
			$this->proj_dir = rtrim($this->option["proj_dir"], '\\/') . '/';
	
			if (empty($this->option["app_tmp"]))
			{
				$this->app_tmp = $this->proj_dir . 'tmp/';
			}
			else
			{
				$this->app_tmp = rtrim($this->option["app_tmp"], '\\/') . '/';
			}
		}

		/**
		 * Load core component
		 */
		require_once $this->lotusRuntimeDir . "Store.php";
		require_once $this->lotusRuntimeDir . "StoreMemory.php";
		require_once $this->lotusRuntimeDir . "StoreFile.php";

		require_once $this->lotusRuntimeDir . "Autoloader/Autoloader.php";

		if (!$this->devMode)
		{
			/**
			 * accelerate LtAutoloader, LtConfig
			 */
			$this->coreCacheHandle = new LtStoreFile;
			$prefix = sprintf("%u", crc32(serialize($this->app_dir)));
			$this->coreCacheHandle->prefix = 'Lotus-' . $prefix . '-';
			$this->coreCacheHandle->useSerialize = true;
			$this->coreCacheHandle->init();
		}

		/**
		 * init Autoloader
		 */
		$this->prepareAutoloader();

		/**
		 * init Config
		 */
		$this->prepareConfig();
	}

	/**
	 * Autoload all lotus components and user-defined libraries;
	 */
	protected function prepareAutoloader()
	{
		$autoloader = new LtAutoloader;
		$autoloader->autoloadPath[] = $this->lotusRuntimeDir;
		if ($this->autoloadPath)
		{
			$autoloader->autoloadPath[] = $this->autoloadPath;
		}
		if ($this->proj_dir)
		{
			$autoloader->autoloadPath[] = $this->proj_dir . 'lib';
			$autoloader->autoloadPath[] = $this->app_dir . 'action';
			$autoloader->autoloadPath[] = $this->app_dir . 'lib';
		}

		if (!$this->devMode)
		{
			$autoloader->storeHandle = $this->coreCacheHandle;
		}
		$autoloader->init();
	}

	protected function prepareConfig()
	{
		$this->configHandle = LtObjectUtil::singleton('LtConfig');
		if (!$this->devMode)
		{
			$configFile = 'conf/conf.php';
			$this->configHandle->storeHandle = $this->coreCacheHandle;
		}
		else
		{
			$configFile = 'conf/conf_dev.php';
		}
		$this->configHandle->init();
		if ($this->app_dir && is_file($this->app_dir . $configFile))
		{
			$this->configHandle->loadConfigFile($this->app_dir . $configFile);
		}
	}

	protected function runMVC()
	{
		$router = LtObjectUtil::singleton('LtRouter');
		LtObjectUtil::singleton('LtUrl');
		$dispatcher = LtObjectUtil::singleton('LtDispatcher');
		$dispatcher->viewDir = $this->app_dir . 'view/';
		$dispatcher->viewTplDir = $this->app_tmp . 'templateView/' . $this->app_name . '/';
		if (!$this->devMode)
		{
			$dispatcher->viewTplAutoCompile = false;
		}
		else
		{
			$dispatcher->viewTplAutoCompile = true;
		}
		$dispatcher->dispatchAction($router->module, $router->action);
	}
}
