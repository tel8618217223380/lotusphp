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
			//$ap->storeHandle = new LtCacheAdapterFile;
			//$ap->storeKeyPrefix = "abc";
			$ap->conf->isLoadFunction = false;
			$ap->autoloadPath = array(
			dirname(__FILE__) . DIRECTORY_SEPARATOR . "class_dir_1",
			);
			$ap->init();
		}
	}
}
