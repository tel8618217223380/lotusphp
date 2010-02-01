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
	public $mvcMode = false;
	protected $lotusRuntimeDir;
	protected $cacheHandle;

	public function __construct()
	{
		$this->lotusRuntimeDir = dirname(__FILE__) . DIRECTORY_SEPARATOR;
	}

	public function init()
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
		/**
		@todo ¹¹Ôì»º´æÅäÖÃ
		*/
		$ccb->addSingleHost(array("adapter" => "phps", "host" => "/tmp/cache_files/"));
		LtCache::$servers = $ccb->getServers();
		$cache = new LtCache;
		$cache->init();
		$this->cacheHandle = $cache->getCacheHandle();
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
		if (isset($this->option["autoload_path"]))
		{
			$autoloadDirs[] = $this->option["autoload_path"];
		}
		$autoloader = new LtAutoloader;
		$autoloader->autoloadPath = $autoloadDirs;

		if (!empty($this->option["autoload_cache_dir"]))
		{
			$autoloader->conf->mappingFileRoot = $this->option["autoload_cache_dir"];
		}
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
		if (isset($this->option["config_file"]))
		{
			$conf->configFile = $this->option["config_file"];
			$conf->init();
		}
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
		$dispatcher->viewDir = $this->option['view_dir'];
		$dispatcher->viewTplDir = $this->option['view_tpl_dir'];
		$dispatcher->viewTplAutoCompile = isset($this->option['view_tpl_auto_compile'])?$this->option['view_tpl_auto_compile']:true;
		$dispatcher->dispatchAction($router->module, $router->action);
	}

	protected function initDb()
	{
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
