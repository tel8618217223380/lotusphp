<?php
require_once 'PHPUnit/Extensions/PerformanceTestCase.php';

chdir(dirname(__FILE__));
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "AutoloaderProxy.php";

class PerformanceTest extends PHPUnit_Extensions_PerformanceTestCase
{
	public function testPerformance()
	{
		$this->setMaxRunningTime(1);
		for($i = 0; $i < 100; $i++)
		{
			$ap = new LtAutoloaderProxy;
			// ------ cache init ----------
			$ap->storeHandle = new LtCache;
			$ap->storeHandle->conf->adapter = 'file'; // 本应用file优于phps
			$ap->storeHandle->init();
			// ------ cache init end ------
			$ap->storeKeyPrefix = "abc";
			$ap->conf->isLoadFunction = false;
			$ap->autoloadPath = array(
			dirname(__FILE__) . DIRECTORY_SEPARATOR . "class_dir_1",
			);
			$ap->init();
		}
	}
}
