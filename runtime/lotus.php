<?php
class Lotus
{
	public $appOption;
	public $envMode = "dev";
	public $lotusCoreClass = array();
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
		$lotusClass = array(
			"LtAutoloader"			=> $this->lotusRuntimeDir . "Autoloader/Autoloader.php",
			"LtCacheAdapter"		=> $this->lotusRuntimeDir . "Cache/CacheAdapter.php",
			"LtCacheAdapterApc"		=> $this->lotusRuntimeDir . "Cache/CacheAdapterApc.php",
			"LtCacheEAccelerator"	=> $this->lotusRuntimeDir . "Cache/CacheAdapterEAccelerator.php",
			"LtCacheAdapterPhps"	=> $this->lotusRuntimeDir . "Cache/CacheAdapterPhps.php",
			"LtCacheAdapterXcache"	=> $this->lotusRuntimeDir . "Cache/CacheAdapterXcache.php",
		);
		$this->lotusCoreClass = array_merge($lotusClass, $this->lotusCoreClass);
		require $this->lotusCoreClass["Autoloader"];
		$autoloader = new LtAutoloader();
		$autoloader->fileMapping = $this->lotusCoreClass;
		$autoloader->init();
	}

	protected function initConfig()
	{
		echo "init config\n";
	}

	protected function initDb()
	{
		echo "init db\n";
	}
}