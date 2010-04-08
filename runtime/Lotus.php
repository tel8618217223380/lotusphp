<?php
class Lotus
{
	/**
	 * Lotus Option array
	 * 
	 * @var array array();
	 */
	public $option;
	public $mvcMode = true;
	public $devMode = false;

	protected $proj_dir;
	protected $app_dir;
	protected $app_name;
	protected $app_tmp;
	protected $lotusRuntimeDir;
	protected $coreCacheHandle;
	protected $configHandle;

	public function __construct()
	{
		$this->lotusRuntimeDir = dirname(__FILE__) . DIRECTORY_SEPARATOR;
	}

	public function init()
	{
		if (empty($this->option["proj_dir"]))
		{
			trigger_error('option[\'proj_dir\'] must be set');
		}
		if (empty($this->option["app_name"]))
		{
			trigger_error('option[\'app_name\'] must be set');
		}

		$this->proj_dir = rtrim($this->option["proj_dir"], '\\/') . '/';
		$this->app_name = $this->option["app_name"];
		$this->app_dir = $this->proj_dir . $this->app_name . '/';
		if (empty($this->option["app_tmp"]))
		{
			$this->app_tmp = $this->proj_dir . 'tmp/';
		}
		else
		{
			$this->app_tmp = rtrim($this->option["app_tmp"], '\\/') . '/';
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
			$this->coreCacheHandle->cacheFileRoot = $this->app_tmp . 'coreCache/';
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

		/**
		 * run MVC
		 */
		if ($this->mvcMode)
		{
			$this->runMVC();
		}
	}
	/**
	 * Autoload all lotus components and user-defined libraries;
	 */
	protected function prepareAutoloader()
	{
		$autoloader = new LtAutoloader;
		if (isset($this->option["runtime_filemap"]))
		{
			$autoloader->useFileMap = true; 
			// runtime目录的类文件映射保存在$coreFileMapping中。
			$autoloader->fileMapPath[] = $this->lotusRuntimeDir;
		}
		else
		{
			$autoloader->autoloadPath[] = $this->lotusRuntimeDir;
		}
		$autoloader->autoloadPath[] = $this->proj_dir . 'lib';
		$autoloader->autoloadPath[] = $this->app_dir . 'action';
		$autoloader->autoloadPath[] = $this->app_dir . 'lib';
		/**
		 * 开发模式下缓存分析结果, 当修改源文件后重新生成缓存 
		 * 源文件没有修改直接取缓存数据
		 */
		$autoloader->conf["mapping_file_root"] = $this->app_tmp . 'autoloader-dev/';
		if (isset($this->option["load_function"]))
		{
			$autoloader->conf["load_function"] = $this->option["load_function"];
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
			$configFile = $this->app_dir . 'conf/conf.php';
			$this->configHandle->storeHandle = $this->coreCacheHandle;
		}
		else
		{
			$configFile = $this->app_dir . 'conf/conf_dev.php';
		}
		$this->configHandle->init();
		$this->configHandle->loadConfigFile($configFile);
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
