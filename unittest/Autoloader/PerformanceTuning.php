<?php
/**
 * 本测试展示了如何用LtCache给LtAutoloader提高性能
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "include_classes.inc";
require_once $lotusHome . "runtime/Cache/Cache.php";
require_once $lotusHome . "runtime/Cache/CacheConfig.php";
require_once $lotusHome . "runtime/Cache/adapter/CacheAdapter.php";
require_once $lotusHome . "runtime/Cache/adapter/CacheAdapterApc.php";
require_once $lotusHome . "runtime/Cache/adapter/CacheAdapterEAccelerator.php";
require_once $lotusHome . "runtime/Cache/adapter/CacheAdapterFile.php";
require_once $lotusHome . "runtime/Cache/adapter/CacheAdapterPhps.php";
require_once $lotusHome . "runtime/Cache/adapter/CacheAdapterXcache.php";
class PerformanceTuning4Autoloader extends PHPUnit_Framework_TestCase
{
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
			dirname(__FILE__) . "/test_data/class_dir_1",
			dirname(__FILE__) . "/test_data/class_dir_2",
			dirname(__FILE__) . "/test_data/function_dir_1",
			dirname(__FILE__) . "/test_data/function_dir_2",
		);
		
		/**
		 * 运行autoloader成功加载一个类
		 * 这是为了证明：使用LtCache作为LtAutoloader的存储，功能是正常的
		 */
		$autoloader = new LtAutoloader;
		$autoloader->storeHandle = $cacheHandle;
		$autoloader->conf->isLoadFunction = false;
		$autoloader->autoloadPath = $autoloadPath;
		$autoloader->init();
		$this->assertTrue(class_exists("HelloWorld"));
		
		/**
		 * 运行5000次，要求在1秒内运行完
		 */
		$times = 5000;
		$startTime = microtime(true);
		for($i = 0; $i < $times; $i++)
		{
			$autoloader = new LtAutoloader;
			$autoloader->storeHandle = $cacheHandle;
			$autoloader->conf->isLoadFunction = false;
			$autoloader->autoloadPath = $autoloadPath;
			$autoloader->init();
		}
		$endTime = microtime(true);
		$totalTime = number_format(($endTime-$startTime), 6);
		$average = number_format(($totalTime/$times), 6);
		echo "\n--------------------------------------------------\n";
		echo "times $times totalTime $totalTime average $average";
		echo "\n--------------------------------------------------\n";
		$this->assertTrue(1 > $totalTime);
	}
}
