<?php
class Lotus
{
	public $option;
	public $devMode = true;
	protected $lotusRuntimeDir;

	public function __construct()
	{
		$this->lotusRuntimeDir = dirname(__FILE__) . DIRECTORY_SEPARATOR;
	}

	public function init()
	{
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
	}

	protected function prepareAutoloader()
	{
		/**
		 * Load core component
		 */
		require_once $this->lotusRuntimeDir . "Autoloader/Autoloader.php";
		require_once $this->lotusRuntimeDir . "Autoloader/AutoloaderConfig.php";
		require_once $this->lotusRuntimeDir . "Cache/Cache.php";
		require_once $this->lotusRuntimeDir . "Cache/CacheConfig.php";
		require_once $this->lotusRuntimeDir . "Cache/adapter/CacheAdapter.php";
		require_once $this->lotusRuntimeDir . "Cache/adapter/CacheAdapterApc.php";
		require_once $this->lotusRuntimeDir . "Cache/adapter/CacheAdapterEAccelerator.php";
		require_once $this->lotusRuntimeDir . "Cache/adapter/CacheAdapterFile.php";
		require_once $this->lotusRuntimeDir . "Cache/adapter/CacheAdapterPhps.php";
		require_once $this->lotusRuntimeDir . "Cache/adapter/CacheAdapterXcache.php";
		require_once $this->lotusRuntimeDir . "ObjectUtil/ObjectUtil.php";

		/**
		 * Init Cache component to sotre LtAutoloader, LtConfig data
		 */
		$cache = LtObjectUtil::singleton("LtCache");
		if(isset($this->option["cache_adapter"]))
		{
			$cache->conf->adapter = $this->option["cache_adapter"];
		}
		if (isset($this->option["cache_options"]))
		{
			$cache->conf->options = $this->option["cache_options"];
		}
		$cache->init();

		/**
		 * Prepare autoloader to load all lotus components and user-defined libraries;
		 */
		$autoloadDirs = array($this->lotusRuntimeDir);
		if (isset($this->option["autoload_path"]))
		{
			$autoloadDirs[] = $this->option["autoload_path"];
		}
		$autoloader = new LtAutoloader();
		$autoloader->autoloadPath = $autoloadDirs;
		if (!$this->devMode)
		{
			$autoloader->storeHandle = LtObjectUtil::singleton("LtCache");
		}
		$autoloader->init();
	}

	protected function prepareConfig()
	{
		$conf = LtObjectUtil::singleton("LtConfig");
		if (!$this->devMode)
		{
			$conf->storeHandle = LtObjectUtil::singleton("LtCache");
		}
		if (isset($this->option["config_file"]))
		{
			$conf->configFile = $this->option["config_file"];
			$this->init();
		}
	}

	protected function initDb()
	{
		if(isset(LtObjectUtil::singleton("LtConfig")->app["DB"]))
		{
			LtDbStaticData::$servers = LtObjectUtil::singleton("LtConfig")->app["DB"]["servers"];
			if(isset(LtObjectUtil::singleton("LtConfig")->app["DB"]["tables"]))
			{
				LtDbStaticData::$tables = LtObjectUtil::singleton("LtConfig")->app["DB"]["tables"];
			}
		}
	}
}