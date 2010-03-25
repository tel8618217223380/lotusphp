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
	public $debug; // default false
	public $debugInfo;

	protected $proj_dir;
	protected $app_dir;
	protected $app_name;
	protected $tmp_dir;
	protected $devMode; // default true
	protected $lotusRuntimeDir;
	protected $cacheInst;

	public function __construct()
	{
		$this->mvcMode = false; // 默认不使用MVC
		$this->devMode = true; // 默认为开发模式
		$this->debug = false; // 默认不显示调试信息
		$this->lotusRuntimeDir = dirname(__FILE__) . DIRECTORY_SEPARATOR;
	}

	public function init()
	{
		if ($this->debug)
		{
			$this->debugInfo['memoryUsage'] = memory_get_usage();
			$this->debugInfo['totalTime'] = microtime(true);
		}
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
		if (empty($this->option["tmp_dir"]))
		{
			$this->tmp_dir = $this->proj_dir . 'tmp/';
		}
		else
		{
			$this->tmp_dir = rtrim($this->option["tmp_dir"], '\\/') . '/';
		}

		/**
		 * 加载共享组件
		 */
		require_once $this->lotusRuntimeDir . "Store.php";
		require_once $this->lotusRuntimeDir . "StoreMemory.php";
		require_once $this->lotusRuntimeDir . "StoreFile.php";
		require_once $this->lotusRuntimeDir . "ObjectUtil/ObjectUtil.php";

		if (!empty($this->option['cache_server']))
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
			$v = $this->option['cache_server'];
			$ccb->addHost($v[0], $v[1], $v[2], $v[3]);
			LtCache::$servers = $ccb->getServers();
			$this->cacheInst = LtObjectUtil::singleton('LtCache');
			$this->cacheInst->init();
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
		/**
		 * debugInfo
		 */
		if ($this->debug)
		{
			$endTime = microtime(true);
			$this->debugInfo['totalTime'] = round(($endTime - $this->debugInfo['totalTime']), 6);
			$memoryUsage = memory_get_usage() - $this->debugInfo['memoryUsage'];
			$this->debugInfo['memoryUsage'] = ($memoryUsage >= 1048576) ? round((round($memoryUsage / 1048576 * 100) / 100), 2) . 'MB' : (($memoryUsage >= 1024) ? round((round($memoryUsage / 1024 * 100) / 100), 2) . 'KB' : $memoryUsage . 'BYTES');
			$this->debugInfo['devMode'] = $this->devMode ? 'true' : 'false';
		}
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
		$autoloader = LtObjectUtil::singleton('LtAutoloader');
		$autoloader->autoloadPath = $autoloadDirs;
		/**
		 * 开发模式下保存分析结果
		 */
		$autoloader->conf->mappingFileRoot = $this->tmp_dir . 'autoloader/';
		if (isset($this->option["is_load_function"]))
		{
			$autoloader->conf->isLoadFunction = $this->option["is_load_function"];
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
		$conf = LtObjectUtil::singleton("LtConfig");
		$conf->init();

		$router = LtObjectUtil::singleton('LtRouter');
		$router->init();

		$url = LtObjectUtil::singleton('LtUrl');
		$url->init();
		/**
		 * mvc
		 */
		$dispatcher = LtObjectUtil::singleton('LtDispatcher');
		$dispatcher->viewDir = $this->app_dir . 'view/';
		$dispatcher->viewTplDir = $this->tmp_dir . 'templateView/' . $this->app_name . '/';
		$dispatcher->viewTplAutoCompile = isset($this->option['view_tpl_auto_compile'])?$this->option['view_tpl_auto_compile']:true;
		$dispatcher->dispatchAction($router->module, $router->action);
	}

	protected function initDb()
	{
		/**
		 * 
		 * @todo 处理conf , Db 性能
		 */
		if (!$this->devMode)
		{
			LtDb::$storeHandle = $this->cacheInst->getTDG('LtDB');
		}
		else
		{
			LtDb::$storeHandle = LtObjectUtil::singleton('LtStoreMemory');
		}
		if (!LtDb::$storeHandle->get("servers"))
		{
			$conf = LtObjectUtil::singleton("LtConfig");
			if ($dbServer = $conf->get('db_only_one'))
			{
				$dcb = LtObjectUtil::singleton('LtDbConfigBuilder');
				$dcb->addSingleHost($dbServer);
			}
			else if ($dbServer = $conf->get('db_server'))
			{
				$dcb = LtObjectUtil::singleton('LtDbConfigBuilder');
				foreach($dbServer as $v)
				{
					$dcb->addHost($v[0], $v[1], $v[2], $v[3]);
				}
			}
			else
			{
				return null;
			}
			LtDb::$storeHandle->add("servers", $dcb->getServers(), 0);
		}
		LtObjectUtil::singleton('LtDb');
	}
}
