<?php
/**
 * 本测试展示了如何用LtCache给LtConfig提高性能
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "common.inc.php";
require_once $lotusHome . "runtime/Cache/Cache.php";
require_once $lotusHome . "runtime/Cache/CacheConfig.php";
require_once $lotusHome . "runtime/Cache/Adapter/CacheAdapter.php";
require_once $lotusHome . "runtime/Cache/Adapter/CacheAdapterApc.php";
require_once $lotusHome . "runtime/Cache/Adapter/CacheAdapterEAccelerator.php";
require_once $lotusHome . "runtime/Cache/Adapter/CacheAdapterFile.php";
require_once $lotusHome . "runtime/Cache/Adapter/CacheAdapterPhps.php";
require_once $lotusHome . "runtime/Cache/Adapter/CacheAdapterXcache.php";
class PerformanceTuning4Config extends PHPUnit_Framework_TestCase
{
	public function testPerformance()
	{
		/**
		 * 初始化LtCache，LtConfig用LtCache作存储层的时候性能才会提高
		 */
		$cacheHandle = new LtCache;
		$cacheHandle->conf->adapter = 'phps';
		$cacheHandle->init();
		
		//准备confif_file
		$config_file = dirname(__FILE__) . "/test_data/conf.php";
		
		/**
		 * 运行autoloader成功取到一个配置
		 * 这是为了证明：使用LtCache作为LtConfig的存储，功能是正常的
		 */
		$conf = new LtConfig;
		$conf->storeHandle = $cacheHandle;
		$conf->configFile = $config_file;
		$conf->init();
		$this->assertEquals($conf->get("db.conn.host"), "localhost");
		
		/**
		 * 运行5000次，要求在1秒内运行完
		 */
		$startTime = microtime(true);
		for($i = 0; $i < 1000; $i++)
		{
			$conf = new LtConfig;
			$conf->storeHandle = $cacheHandle;
			$conf->configFile = $config_file;
			$conf->init();
		}
		$endTime = microtime(true);
		$this->assertTrue(1 > $endTime-$startTime);
	}
}
