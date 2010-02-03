<?php
class Lotus
{
	/**
	 * Lotus Option array
	 * 
	 * @var array array();
	 */
	public $option;
	public $mvcMode;

	protected $proj_dir;
	protected $app_dir;
	protected $app_name;

	protected $devMode;
	protected $lotusRuntimeDir;
	protected $cacheHandle;

	public function __construct()
	{
		$this->mvcMode = false; // 默认不使用MVC
		$this->devMode = true; // 默认为开发模式
		$this->lotusRuntimeDir = dirname(__FILE__) . DIRECTORY_SEPARATOR;
	}

	public function init()
	{
		if (!isset($this->option["proj_dir"]) || empty($this->option["proj_dir"]))
		{
			trigger_error('option[\'proj_dir\'] must be set');
		}
		if (!isset($this->option["app_name"]) || empty($this->option["app_name"]))
		{
			trigger_error('option[\'app_name\'] must be set');
		}
		$this->proj_dir = rtrim($this->option["proj_dir"], '\\/') . '/';
		$this->app_name = $this->option["app_name"];
		$this->app_dir = $proj_dir . $this->app_name . '/';

		if (!empty($this->option["cache"]))
		{
			/**
			 * Init Cache component to sotre LtAutoloader, LtConfig data
			 */
			require_once $this->lotusRuntimeDir . "Cache/Cache.php";
			require_once $this->lotusRuntimeDir . "Cache/CacheAdapterFactory.php";
			require_once $this->lotusRuntimeDir . "Cache/CacheConfigBuilder.php";
			require_once $this->lotusRuntimeDir . "Cache/CacheConnectionManager.php";
			require_once $this->lotusRuntimeDir . "Cache/CacheHandle.php";
			require_once $this->lotusRuntimeDir . "Cache/Adapter/CacheAdapter.php";
			require_once $this->lotusRuntimeDir . "Cache/Adapter/CacheAdapterApc.php";
			require_once $this->lotusRuntimeDir . "Cache/Adapter/CacheAdapterEAccelerator.php";
			require_once $this->lotusRuntimeDir . "Cache/Adapter/CacheAdapterFile.php";
			require_once $this->lotusRuntimeDir . "Cache/Adapter/CacheAdapterMemcached.php";
			require_once $this->lotusRuntimeDir . "Cache/Adapter/CacheAdapterPhps.php";
			require_once $this->lotusRuntimeDir . "Cache/Adapter/CacheAdapterXcache.php";
			$ccb = new LtCacheConfigBuilder;
			$ccb->addSingleHost($this->option["cache"]);
			LtCache::$servers = $ccb->getServers();
			$cache = new LtCache;
			$cache->init();
			$this->cacheHandle = $cache->getCacheHandle();
			$this->devMode = false; // 生产模式
		}

		/**
		 * Init Cache end.
		 */
		$this->prepareAutoloader();
		$this->prepareConfig();
		/**
		 * Initial other components
		 */
		foreach (get_class_methods($this) as $method)
		{
			if (4 < strlen($method) && "init" == substr($method, 0, 4))
			{
				$this->$method();
			}
		}
		/**
		 * run MVC
		 */
		$this->mvcMode && $this->runMVC();
	}

	protected function prepareAutoloader()
	{
		/**
		 * Load core component
		 */
		require_once $this->lotusRuntimeDir . "Autoloader/Autoloader.php";
		require_once $this->lotusRuntimeDir . "Autoloader/AutoloaderConfig.php";
		require_once $this->lotusRuntimeDir . "ObjectUtil/ObjectUtil.php";

		/**
		 * Prepare autoloader to load all lotus components and user-defined libraries;
		 */
		$autoloadDirs = array($this->lotusRuntimeDir);
		$autoloadDirs[] = $this->proj_dir . 'lib';
		$autoloadDirs[] = $this->app_dir . 'action';
		$autoloadDirs[] = $this->app_dir . 'lib';

		$autoloader = new LtAutoloader;
		$autoloader->autoloadPath = $autoloadDirs;
		/**
		 * 开发模式下保存分析结果
		 */
		$autoloader->conf->mappingFileRoot = $this->proj_dir . 'tmp/LtAutoload';

		if (!$this->devMode)
		{
			LtAutoloader::$storeHandle = $this->cacheHandle;
		}
		$autoloader->init();
	}

	protected function prepareConfig()
	{
		$conf = LtObjectUtil::singleton("LtConfig");
		if (!$this->devMode)
		{
			LtConfig::$storeHandle = $this->cacheHandle;
		}
		$conf->configFile = $this->app_dir . 'conf/conf.php';
		$conf->init();
	}

	protected function runMVC()
	{
		/**
		 * router
		 */
		$router = LtObjectUtil::singleton('LtRouter');
		$router->init();
		/**
		 * mvc
		 */
		$dispatcher = new LtDispatcher;
		$dispatcher->viewDir = $this->app_dir . 'view/';
		$dispatcher->viewTplDir = $this->proj_dir . 'tmp/view_tpl/' . $this->app_name;
		$dispatcher->viewTplAutoCompile = isset($this->option['view_tpl_auto_compile'])?$this->option['view_tpl_auto_compile']:true;
		$dispatcher->dispatchAction($router->module, $router->action);
	}

	protected function initDb()
	{
		/**
		 * 
		 * @todo 处理conf
		 */
		$conf = LtObjectUtil::singleton("LtConfig");
		if ($singleHost = $conf->get('singleHost'))
		{
			$dcb = new LtDbConfigBuilder;
			$dcb->addSingleHost($singleHost);
			LtDb::$storeHandle = new LtDbStore;
			LtDb::$storeHandle->add("servers", $dcb->getServers(), 0, LtDb::$namespace);
			$db = LtObjectUtil::singleton('LtDb');
			$db->init();
		}
	}
}
