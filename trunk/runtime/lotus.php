<?php
class Lotus
{	
	public $appConfig;
	public $appOption;
	public $autoloadFiles;

	public function initConfig()
	{
	}

	public function initDb()
	{
	}

	public function init()
	{
		/**
		 * @todo if ("dev" != $this->envMode) {$this->autoloadFiles = apc_get($cacheKey)}
		 */
		/**
		 * Initial the autoloader
		 */
		$lotusRuntime = dirname(__FILE__) . DIRECTORY_SEPARATOR;
		require $lotusRuntime . "Autoloader/Autoloader.php";
		$autoloader = new Autoloader;
		if (!$this->autoloadFiles)
		{
			$this->autoloadFiles = $autoloader->scanDir(array($lotusRuntime));
		}
		$autoloader->init($this->autoloadFiles);
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
}