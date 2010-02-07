<?php
/**
 * 本测试展示了如何用LtCache给LtConfig提高性能
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "common.inc.php";

require_once $lotusHome . "runtime/Cache/Cache.php";
require_once $lotusHome . "runtime/Cache/CacheAdapterFactory.php";
require_once $lotusHome . "runtime/Cache/CacheConfigBuilder.php";
require_once $lotusHome . "runtime/Cache/CacheConnectionManager.php";
require_once $lotusHome . "runtime/Cache/CacheHandle.php";
require_once $lotusHome . "runtime/Cache/Adapter/CacheAdapter.php";
require_once $lotusHome . "runtime/Cache/Adapter/CacheAdapterApc.php";
require_once $lotusHome . "runtime/Cache/Adapter/CacheAdapterEAccelerator.php";
require_once $lotusHome . "runtime/Cache/Adapter/CacheAdapterFile.php";
require_once $lotusHome . "runtime/Cache/Adapter/CacheAdapterMemcache.php";
require_once $lotusHome . "runtime/Cache/Adapter/CacheAdapterMemcached.php";
require_once $lotusHome . "runtime/Cache/Adapter/CacheAdapterPhps.php";
require_once $lotusHome . "runtime/Cache/Adapter/CacheAdapterXcache.php";
require_once $lotusHome . "runtime/Cache/QueryEngine/TableDataGateway/CacheTableDataGateway.php";

class PerformanceTuningUrl extends PHPUnit_Framework_TestCase
{
	public function testPerformance()
	{
		// 初始化LtUrl
		$url = new LtUrl;
		// 不初始化路由表则使用默认配置如下
		$url->routingTable = array('pattern' => ":module/:action/*",
			'default' => array('module' => 'default', 'action' => 'index'),
			'reqs' => array('module' => '[a-zA-Z0-9\.\-_]+', 'action' => '[a-zA-Z0-9\.\-_]+'),
			'varprefix' => ':',
			'delimiter' => '/',
			'postfix' => '',
			'protocol' => '',
			);
		$url->init(); 
		// 初始化结束
		// 测试生成超链接
		$href = $url->generate('news', 'list', array('catid' => 4, 'page' => 10));
		$this->assertEquals('news/list/catid/4/page/10', $href);

		/**
		 * 运行10000次，要求在1秒内运行完
		 */
		$base_memory_usage = memory_get_usage();
		$times = 1000;
		$startTime = microtime(true);
		for($i = 0; $i < $times; $i++)
		{
			$url->generate('news', 'list', array('catid' => 4, 'page' => 10));
		}
		$endTime = microtime(true);
		$totalTime = round(($endTime - $startTime), 6);
		$averageTime = round(($totalTime / $times), 6);

		$memory_usage = memory_get_usage() - $base_memory_usage;
		$memory_usage = ($memory_usage >= 1048576) ? round((round($memory_usage / 1048576 * 100) / 100), 2) . 'MB' : (($memory_usage >= 1024) ? round((round($memory_usage / 1024 * 100) / 100), 2) . 'KB' : $memory_usage . 'BYTES');

		$averageMemory = round(($memory_usage / $times), 2);
		$averageMemory = ($averageMemory >= 1048576) ? round((round($averageMemory / 1048576 * 100) / 100), 2) . 'MB' : (($averageMemory >= 1024) ? round((round($averageMemory / 1024 * 100) / 100), 2) . 'KB' : $averageMemory . 'BYTES');

		echo "\n----------------------Url-----------------------------\n";
		echo "times      \t$times\n";
		echo "totalTime   \t{$totalTime}s\taverageTime   \t{$averageTime}s\n";
		echo "memoryUsage \t{$memory_usage}\taverageMemory \t{$averageMemory}";
		echo "\n---------------------------------------------------------\n";
		$this->assertTrue(1 > $totalTime);
	}
	protected function setUp()
	{
	}
	protected function tearDown()
	{
	}
}