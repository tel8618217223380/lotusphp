<?php
class Lotus
{
	public function initAutoloader()
	{
		$lotusRuntime = dirname(__FILE__) . DIRECTORY_SEPARATOR;
		require $lotusRuntime . "Autoloader/Autoloader.php";
		$autoloader = new Autoloader;
		$autoloader->init($autoloader->scanDir(array($lotusRuntime)));
	}

	public function init()
	{
		$this->initAutoloader();
	}
}