<?php
class Lotus
{
	public $option;
	public $envMode = "dev";
	public $lotusCoreClass = array();
	protected $lotusRuntimeDir;

	public function __construct()
	{
		$this->lotusRuntimeDir = dirname(__FILE__) . DIRECTORY_SEPARATOR;
	}

	public function boot()
	{
		$this->prepareAutoloader();
		$this->prepareConfig();

		/**
		 * Initial other components
		 */
		foreach (get_class_methods($this) as $method)
		{
			if ("init" == substr($method, 0, 4))
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
		$lotusClass = array(
			"LtAutoloader"			=> $this->lotusRuntimeDir . "Autoloader/Autoloader.php",
			"LtCache"				=> $this->lotusRuntimeDir . "Cache/Cache.php",
			"LtCacheConfig"			=> $this->lotusRuntimeDir . "Cache/CacheConfig.php",
			"LtCacheAdapter"		=> $this->lotusRuntimeDir . "Cache/adapter/CacheAdapter.php",
			"LtCacheAdapterApc"		=> $this->lotusRuntimeDir . "Cache/adapter/CacheAdapterApc.php",
			"LtCacheEAccelerator"	=> $this->lotusRuntimeDir . "Cache/adapter/CacheAdapterEAccelerator.php",
			"LtCacheAdapterPhps"	=> $this->lotusRuntimeDir . "Cache/adapter/CacheAdapterPhps.php",
			"LtCacheAdapterXcache"	=> $this->lotusRuntimeDir . "Cache/adapter/CacheAdapterXcache.php",
			"LtObjectUtil"	=> $this->lotusRuntimeDir . "ObjectUtil/ObjectUtil.php",
		);
		$this->lotusCoreClass = array_merge($lotusClass, $this->lotusCoreClass);

		/**
		 * Init autoloader to load Cache, ObjectUtil components
		 */
		require $this->lotusCoreClass["LtAutoloader"];
		$autoloader = new LtAutoloader();
		$autoloader->fileMapping["class"] = $this->lotusCoreClass;
		$autoloader->init();

		/**
		 * Init Cache component to sotre LtAutoloader->fileMapping, Config->app
		 */
		$cache = LtObjectUtil::singleton("LtCache");
		$cache->conf->adapter = $this->option["cache_adapter"];
		if (isset($this->option["cache_options"]))
		{
			$cache->conf->options = $this->option["cache_options"];
		}
		$cache->init();
		spl_autoload_unregister(array($autoloader, "loadClass"));
		unset($autoloader);

		/**
		 * Prepare autoloader to load all lotus components and user-defined libraries;
		 */
		$autoloadDirs = array($this->lotusRuntimeDir);
		if (isset($this->option["proj_lib"]))
		{
			$autoloadDirs[] = $this->option["proj_lib"];
		}
		if (isset($this->option["app_lib"]))
		{
			$autoloadDirs[] = $this->option["app_lib"];
		}
		$includedFiles = get_included_files();
		$key = "lotus_autoloader_" . crc32($includedFiles[0]);
		if ("dev" != $this->envMode && $fileMapping = $cache->get($key))
		{
			$autoloader = new LtAutoloader();
			$autoloader->fileMapping = $fileMapping;
			$autoloader->init();
		}
		else
		{
			$autoloader = new LtAutoloader($autoloadDirs);
			if ("dev" != $this->envMode)
			{
				$cache->add($key, $autoloader->fileMapping);
			}
		}
	}

	protected function prepareConfig()
	{
		$config = LtObjectUtil::singleton("LtConfig");
		$cache = LtObjectUtil::singleton("LtCache");
		$includedFiles = get_included_files();
		$key = "lotus_config_" . crc32($includedFiles[0]);
		if ("dev" != $this->envMode && $cachedConfig = $cache->get($key))
		{
			$config->app = $cachedConfig;
		}
		else
		{
			$projConfig = isset($appOption["config_file"]) ? include($appOption["config_file"]) : array();
			$appConfig = isset($appOption["app_config_file"]) ? include($appOption["app_config_file"]) : array();
			$config->app = array_merge($projConfig, $appConfig);
			if ("dev" != $this->envMode)
			{
				$cache->add($key, $config->app);
			}
		}
	}

	protected function initDb()
	{
		if(isset(LtObjectUtil::singleton("LtConfig")->app["db"]))
		{
			Db::$server = LtObjectUtil::singleton("LtConfig")->app["db"]["servers"];
			Db::$tables = LtObjectUtil::singleton("LtConfig")->app["db"]["tables"];
		}
	}
}