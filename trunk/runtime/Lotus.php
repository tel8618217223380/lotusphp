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
	protected $app_tmp;
	protected $devMode; // default true
	protected $lotusRuntimeDir;
	protected $cacheInst;

	public function __construct()
	{
		$this->mvcMode = true; // 默认使用MVC
		$this->devMode = true; // 默认开发模式
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
		 * 加载共享组件
		 */
		require_once $this->lotusRuntimeDir . "Store.php";
		require_once $this->lotusRuntimeDir . "StoreMemory.php";
		require_once $this->lotusRuntimeDir . "StoreFile.php";
		require_once $this->lotusRuntimeDir . "ObjectUtil/ObjectUtil.php";

		if (!empty($this->option['app_cache']))
		{
			/**
			 * Init Cache component to sotre LtAutoloader, LtConfig data ...
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
			require_once $this->lotusRuntimeDir . "Cache/Adapter/CacheAdapterMemcache.php";
			require_once $this->lotusRuntimeDir . "Cache/Adapter/CacheAdapterMemcached.php";
			require_once $this->lotusRuntimeDir . "Cache/Adapter/CacheAdapterPhps.php";
			require_once $this->lotusRuntimeDir . "Cache/Adapter/CacheAdapterXcache.php";
			require_once $this->lotusRuntimeDir . "Cache/QueryEngine/TableDataGateway/CacheTableDataGateway.php";

			$ccb = LtObjectUtil::singleton('LtCacheConfigBuilder');
			$ccb->addSingleHost($this->option['app_cache']);
			LtCache::$servers = $ccb->getServers();
			$this->cacheInst = LtObjectUtil::singleton('LtCache');
			$this->cacheInst->init();
			//$this->cacheInst->group = "group_0";
			//$this->cacheInst->node = "node_0";
			$this->devMode = false; // 生产模式
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

	protected function prepareAutoloader()
	{
		/**
		 * Load core component
		 */
		require_once $this->lotusRuntimeDir . "Autoloader/Autoloader.php";
		require_once $this->lotusRuntimeDir . "ObjectUtil/ObjectUtil.php";
		/**
		 * Prepare autoloader to load all lotus components and user-defined libraries;
		 */
		$autoloadDirs = array($this->lotusRuntimeDir);
		$autoloadDirs[] = $this->proj_dir . 'lib';
		$autoloadDirs[] = $this->app_dir . 'action';
		$autoloadDirs[] = $this->app_dir . 'lib';
		$autoloader = LtObjectUtil::singleton('LtAutoloader');
		$autoloader->autoloadPath = $autoloadDirs;
		/**
		 * 开发模式下保存分析结果
		 */
		$autoloader->conf["mapping_file_root"] = $this->app_tmp . 'autoloader/';
		if (isset($this->option["is_load_function"]))
		{
			$autoloader->conf["load_function"] = $this->option["load_function"];
		}
		if (!$this->devMode)
		{
			$tb = sprintf("%u", crc32(serialize($autoloadDirs)));
			LtAutoloader::$storeHandle = $this->cacheInst->getTDG($tb);
		}
		$autoloader->init();
	}

	protected function prepareConfig()
	{
		$configFile = $this->app_dir . 'conf/conf.php';
		if (!$this->devMode)
		{
			$tb = sprintf("%u", crc32(serialize($configFile)));
			LtConfig::$storeHandle = $this->cacheInst->getTDG($tb);
		}
		$conf = LtObjectUtil::singleton("LtConfig");
		$conf->init();
		$conf->loadConfigFile($configFile);
	}

	protected function runMVC()
	{ 
		$router = LtObjectUtil::singleton('LtRouter');
		$router->init();
		$url = LtObjectUtil::singleton('LtUrl');
		$url->init();
		$dispatcher = LtObjectUtil::singleton('LtDispatcher');
		$dispatcher->viewDir = $this->app_dir . 'view/';
		$dispatcher->viewTplDir = $this->app_tmp . 'templateView/' . $this->app_name . '/';
		$dispatcher->viewTplAutoCompile = isset($this->option['view_tpl_auto_compile'])?$this->option['view_tpl_auto_compile']:true;
		$dispatcher->dispatchAction($router->module, $router->action);
	}
}
