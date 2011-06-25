<?php
class Lotus
{
	/**
	 * Lotus Option array
	 * 
	 * @var array array(
	 * 	"proj_dir"     =>
	 * 	"app_name"     =>
	 * 	"autoload_dir" =>
	 * );
	 */
	public $option;
	public $devMode = true;
	public $defaultStoreDir;
	
	public $configHandle;

	protected $proj_dir;
	protected $app_dir;
	protected $cache_dir;
	protected $lotusRuntimeDir;
	protected $coreCacheHandle;

	public function __construct()
	{
		$this->lotusRuntimeDir = dirname(__FILE__) . DIRECTORY_SEPARATOR;
	}

	public function init()
	{
		$underMVC = false;
		if (isset($this->option["proj_dir"]) && !empty($this->option["proj_dir"]))
		{
			$this->proj_dir = rtrim($this->option["proj_dir"], '\\/') . '/';
			if (isset($this->option["app_name"]) && !empty($this->option["app_name"]))
			{
				$this->app_dir = $this->proj_dir . "app/" . $this->option["app_name"] . "/";
				$this->cache_dir = $this->proj_dir . "cache/";
				$underMVC = true;
			}
			else
			{
				trigger_error("Lotus option [app_name] is missing.");
			}
		}

		/**
		 * Load core component
		 */
		require_once $this->lotusRuntimeDir . "Store.php";
		require_once $this->lotusRuntimeDir . "StoreMemory.php";
		require_once $this->lotusRuntimeDir . "StoreFile.php";

		if ($this->defaultStoreDir)
		{
			if ($defaultStoreDir = realpath($this->defaultStoreDir))
			{
				LtStoreFile::$defaultStoreDir = $defaultStoreDir;
			}
			else
			{
				trigger_error("invalid [default store dir]: " . $this->defaultStoreDir);
			}
		}
		if (!$this->devMode)
		{
			/**
			 * accelerate LtAutoloader, LtConfig
			 */
			$this->coreCacheHandle = new LtStoreFile;
			$prefix = sprintf("%u", crc32(serialize($this->app_dir)));
			$this->coreCacheHandle->prefix = 'Lotus-' . $prefix;
			$this->coreCacheHandle->useSerialize = true;
			$this->coreCacheHandle->storeDir = $this->cache_dir;
			$this->coreCacheHandle->init();
		}

		/**
		 * Init Autoloader, do this before init all other lotusphp component.
		 */
		$this->prepareAutoloader();

		/**
		 * init Config
		 */
		$this->prepareConfig();
		
		/**
		 * Run dispatcher when under MVC mode
		 */
		if ($underMVC)
		{
			$this->runMVC();
		}
	}

	/**
	 * Autoload all lotus components and user-defined libraries;
	 */
	protected function prepareAutoloader()
	{
		require_once $this->lotusRuntimeDir . "Autoloader/Autoloader.php";
		$autoloader = new LtAutoloader;
		$autoloader->autoloadPath[] = $this->lotusRuntimeDir;
		if (isset($this->option["autoload_dir"]))
		{
			$autoloader->autoloadPath[] = $this->option["autoload_dir"];
		}
		if ($this->proj_dir)
		{
			is_dir($this->proj_dir . 'lib') && $autoloader->autoloadPath[] = $this->proj_dir . 'lib';
			is_dir($this->app_dir . 'action') && $autoloader->autoloadPath[] = $this->app_dir . 'action';
			is_dir($this->app_dir . 'lib') && $autoloader->autoloadPath[] = $this->app_dir . 'lib';
		}

		if (!$this->devMode)
		{
			$autoloader->storeHandle = $this->coreCacheHandle;
		}
		else
		{
			$autoloader->cacheDir = $this->cache_dir;
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
		$router->init();
		$dispatcher = LtObjectUtil::singleton('LtDispatcher');
		$dispatcher->configHandle = $this->configHandle;
		$dispatcher->viewDir = $this->app_dir . 'view/';

		$prefix = sprintf("%u", crc32(serialize($this->app_dir)));
		if (!$this->devMode)
		{
			// 生产环境下，修改模板文件后，必需手工删除模板引擎编译后的文件
			$dispatcher->viewTplDir = $this->cache_dir . 'Lotus-' . $prefix . '-tpl/';
			$dispatcher->viewTplAutoCompile = false;
		}
		else
		{
			// 开发模式下模板引擎比较源文件编译后的文件日期来决定是否重新编译
			$dispatcher->viewTplDir = $this->cache_dir . 'Lotus-' . $prefix . '-tpl-dev/';
			$dispatcher->viewTplAutoCompile = true;
		}
		$dispatcher->dispatchAction($router->module, $router->action);
	}
}
