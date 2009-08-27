<?php
class Lotus
{	
	public $appConfig;
	public $appOption;
	public $autoloadFiles;
	public function initAutoloader()
	{
		$lotusRuntime = dirname(__FILE__) . DIRECTORY_SEPARATOR;
		require $lotusRuntime . "Autoloader/Autoloader.php";
		$autoloader = new Autoloader;
		if (!$this->autoloadFiles)
		{
			$this->autoloadFiles = $autoloader->scanDir(array($lotusRuntime));
		}
		$autoloader->init($this->autoloadFiles);
	}

	public function init()
	{
		/**
		 * @todo if ("dev" != $this->envMode) {$this->autoloadFiles = apc_get($cacheKey)}
		 */
		$this->initAutoloader();
	}
}