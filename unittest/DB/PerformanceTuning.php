<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "include_classes.inc";
require_once $lotusHome . "runtime/Cache/Cache.php";
require_once $lotusHome . "runtime/Cache/CacheConfig.php";
require_once $lotusHome . "runtime/Cache/adapter/CacheAdapter.php";
require_once $lotusHome . "runtime/Cache/adapter/CacheAdapterApc.php";
require_once $lotusHome . "runtime/Cache/adapter/CacheAdapterEAccelerator.php";
require_once $lotusHome . "runtime/Cache/adapter/CacheAdapterFile.php";
require_once $lotusHome . "runtime/Cache/adapter/CacheAdapterPhps.php";
require_once $lotusHome . "runtime/Cache/adapter/CacheAdapterXcache.php";
class PerformanceTuning4Db extends PHPUnit_Framework_TestCase
{
	/**
	 * 本测试展示了如何用LtCache给LtDb提高性能
	 */
	public function testPerformance()
	{
		/**
		 * 初始化LtCache，LtDb用LtCache作存储层的时候性能才会提高
		 */
		$cacheHandle = new LtCache;
		$cacheHandle->conf->adapter = 'phps';
		$cacheHandle->init();
		
		//准备DbConf
		/**
		 * 运行Db成功加操作一次数据表
		 * 这是为了证明：使用LtCache作为LtDb的存储，功能是正常的
		 */
		
		/**
		 * 运行5000次，要求在1秒内运行完
		 */
		$startTime = microtime(true);
		for($i = 0; $i < 5000; $i++)
		{
			
		}
		$endTime = microtime(true);
		$this->assertTrue(1 > $endTime-$startTime);
	}
}
