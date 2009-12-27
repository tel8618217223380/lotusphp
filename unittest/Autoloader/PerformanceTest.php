<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "AutoloaderProxy.php";

class PerformanceTest4Autoloader extends PHPUnit_Extensions_PerformanceTestCase
{
	/**
	 * 本测试展示了如何用LtCache给LtAutoloader提高性能
	 */
	public function testPerformance()
	{
		/**
		 * 初始化LtCache，LtAutoloader用LtCache作存储层的时候性能才会提高
		 */
		$cacheHandle = new LtCache;
		$cacheHandle->conf->adapter = 'phps';
		$cacheHandle->init();
		
		//准备autoloadPath
		$autoloadPath = array(
			dirname(__FILE__) . DIRECTORY_SEPARATOR . "class_dir_1",
			dirname(__FILE__) . DIRECTORY_SEPARATOR . "class_dir_2",
			dirname(__FILE__) . DIRECTORY_SEPARATOR . "function_dir_1",
			dirname(__FILE__) . DIRECTORY_SEPARATOR . "function_dir_2",
		);
		
		/**
		 * 运行autoloader成功加载一个类
		 * 这是为了证明：使用LtCache作为LtAutoloader的存储，功能是正常的
		 */
		$ap = new LtAutoloaderProxy;
		$ap->storeHandle = $cacheHandle;
		$ap->conf->isLoadFunction = false;
		$ap->autoloadPath = $autoloadPath;
		$ap->init();
		$this->assertTrue(class_exists("HelloWorld"));
		
		/**
		 * 运行5000次，要求在1秒内运行完
		 */
		$this->setMaxRunningTime(1);
		for($i = 0; $i < 5000; $i++)
		{
			$ap = new LtAutoloaderProxy;
			$ap->storeHandle = $cacheHandle;
			$ap->conf->isLoadFunction = false;
			$ap->autoloadPath = $autoloadPath;
			$ap->init();
		}
	}
}
