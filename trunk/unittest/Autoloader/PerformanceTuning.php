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
		 * 运行1000次，要求在1秒内运行完
		 */
		$base_memory_usage = memory_get_usage();
		$times = 1000;
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
		$totalTime = round(($endTime-$startTime), 6);
		$averageTime = round(($totalTime/$times), 6);

		$memory_usage = memory_get_usage() - $base_memory_usage;
		$memory_usage = ($memory_usage >= 1048576) ? round((round($memory_usage / 1048576 * 100) / 100), 2) . 'MB' : (($memory_usage >= 1024) ? round((round($memory_usage / 1024 * 100) / 100), 2) . 'KB' : $memory_usage . 'BYTES');
		
		$averageMemory = round(($memory_usage/$times),2);
		$averageMemory = ($averageMemory >= 1048576) ? round((round($averageMemory / 1048576 * 100) / 100), 2) . 'MB' : (($averageMemory >= 1024) ? round((round($averageMemory / 1024 * 100) / 100), 2) . 'KB' : $averageMemory . 'BYTES');

		echo "\n--------------------------------------------------\n";
		echo "times\t$times\n";
		echo "totalTime\t{$totalTime}s\taverageTime\t\t{$averageTime}s\n";
		echo "memoryUsage\t{$memory_usage}\taverageMemory\t{$averageMemory}";
		echo "\n--------------------------------------------------\n";
		$this->assertTrue(1 > $totalTime);
	}
}
