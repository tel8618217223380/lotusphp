<?php
class Lotus
{	
	public $appConfig;
	public $appOption;
	public $autoloadFiles;
	public $lotusCoreClass = array();
	public $lotusRuntimeDir;

	public function __construct()
	{
		$this->lotusRuntimeDir = dirname(__FILE__) . DIRECTORY_SEPARATOR;
	}

	public function prepareAutoloader()
	{
		require $this->lotusCoreClass["Autoloader"];
		$autoloader = new LtAutoloader;
		if (!$this->autoloadFiles)
		{
			$this->autoloadFiles = $autoloader->scanDir($this->lotusRuntimeDir);
		}
		$autoloader->autoloadFiles = $this->autoloadFiles;
		$autoloader->init();
	}

	public function initConfig()
	{
		echo "init config\n";
	}

	public function initDb()
	{
		echo "init db\n";
	}

	public function boot()
	{
		$lotusClass = array(
			"Autoloader" => $this->lotusRuntimeDir . "Autoloader/Autoloader.php",
			"Cache" => $this->lotusRuntimeDir . "Cache/Cache.php",
		);
		$this->lotusCoreClass = array_merge($lotusClass, $this->lotusCoreClass);
		/**
		 * @todo if ("dev" != $this->envMode) {$this->autoloadFiles = apc_get($cacheKey)}
		 */
		$this->prepareAutoloader();
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
}