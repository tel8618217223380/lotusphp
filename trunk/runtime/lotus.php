<?php
class Lotus
{
	public $appOption;
	public $envMode = "dev";
	public $lotusCoreClass = array();
	public $cacheAdapter;
	public $cacheOptions;
	public $lotusRuntimeDir;

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
		require $this->lotusCoreClass["LtAutoloader"];
		$autoloader = new LtAutoloader();
		$autoloader->fileMapping["class"] = $this->lotusCoreClass;
		$autoloader->init();
		$cache = LtObjectUtil::singleton("LtCache");
		$cache->conf->adapter = $this->cacheAdapter;
		$cache->init();
		spl_autoload_unregister(array($autoloader, "loadClass"));
		unset($autoloader);

		/**
		 * Prepare autoloader, and cache the file mapping
		 */
		$autoloadDirs = array($this->lotusRuntimeDir);
		if (isset($this->appOption["proj_lib"]))
		{
			$autoloadDirs[] = $this->appOption["proj_lib"];
		}
		if (isset($this->appOption["app_lib"]))
		{
			$autoloadDirs[] = $this->appOption["app_lib"];
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
		echo "init db\n";
	}
}