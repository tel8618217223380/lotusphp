<?php
class Lotus
{
	public $option;
	public $envMode = "dev";
	public $lotusCoreClass = array();
	protected $lotusRuntimeDir;
	protected $entranceFile;
	protected $sysCacheKey = array();

	public function __construct()
	{
		$this->lotusRuntimeDir = dirname(__FILE__) . DIRECTORY_SEPARATOR;
		$includedFiles = get_included_files();
		$this->entranceFile = $includedFiles[0];
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
			"ltautoloader"			=> $this->lotusRuntimeDir . "Autoloader/Autoloader.php",
			"ltautoloaderconfig"			=> $this->lotusRuntimeDir . "Autoloader/AutoloaderConfig.php",
			"ltcache"				=> $this->lotusRuntimeDir . "Cache/Cache.php",
			"ltcacheConfig"			=> $this->lotusRuntimeDir . "Cache/CacheConfig.php",
			"ltcacheAdapter"		=> $this->lotusRuntimeDir . "Cache/adapter/CacheAdapter.php",
			"ltcacheAdapterApc"		=> $this->lotusRuntimeDir . "Cache/adapter/CacheAdapterApc.php",
			"ltcacheEAccelerator"	=> $this->lotusRuntimeDir . "Cache/adapter/CacheAdapterEAccelerator.php",
			"ltcacheAdapterPhps"	=> $this->lotusRuntimeDir . "Cache/adapter/CacheAdapterPhps.php",
			"ltcacheAdapterXcache"	=> $this->lotusRuntimeDir . "Cache/adapter/CacheAdapterXcache.php",
			"ltobjectUtil"			=> $this->lotusRuntimeDir . "ObjectUtil/ObjectUtil.php",
		);
		$this->lotusCoreClass = array_merge($lotusClass, $this->lotusCoreClass);

		/**
		 * Init autoloader to load Cache, ObjectUtil components
		 */
		require $this->lotusCoreClass["ltautoloader"];
		$autoloader = new LtAutoloader();
		$autoloader->storeHandle->fileMapping = $this->lotusCoreClass;
		$autoloader->boot();

		/**
		 * Init Cache component to sotre LtAutoloader->fileMapping, Config->app
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
		$this->sysCacheKey["autoloader_key"] = "lotus_autoloader_" . crc32($this->entranceFile);
		if ("dev" != $this->envMode && $fileMapping = $cache->get($this->sysCacheKey["autoloader_key"]))
		{
			$autoloader = new LtAutoloader();
			$autoloader->fileMapping = $fileMapping;
			$autoloader->boot();
		}
		else
		{
			$autoloader = new LtAutoloader($autoloadDirs);
			if ("dev" != $this->envMode)
			{
				$cache->add($this->sysCacheKey["autoloader_key"], $autoloader->fileMapping);
			}
		}
	}

	/**
	 * @警告
	 * 这里会包含两个用户定义的配置文件，为了不和配置文件里的变量名发生重名
	 * prepareConfig()方法不定义和使用变量名，改用$this->xxx属性
	 */
	protected function prepareConfig()
	{
		$this->sysCacheKey["config_key"] = "lotus_config_" . crc32($this->entranceFile);
		if ("dev" != $this->envMode && LtObjectUtil::singleton("LtConfig")->app = LtObjectUtil::singleton("LtCache")->get($this->sysCacheKey["config_key"]))
		{
		}
		else
		{
			LtObjectUtil::singleton("LtConfig")->app = isset($this->option["config_file"]) ? include($this->option["config_file"]) : array();
			if (isset($this->option["app_config_file"]))
			{
				LtObjectUtil::singleton("LtConfig")->app = array_merge(LtObjectUtil::singleton("LtConfig")->app, include($this->option["app_config_file"]));
			}
			if ("dev" != $this->envMode)
			{
				LtObjectUtil::singleton("LtCache")->add($this->sysCacheKey["config_key"], LtObjectUtil::singleton("LtConfig")->app);
			}
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