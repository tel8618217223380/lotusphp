<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "common.inc.php";
class PerformanceTuningCache extends PHPUnit_Framework_TestCase
{
	public function testPerformance()
	{
		/**
		 * 构造缓存配置
		 */
		$ccb = new LtCacheConfigBuilder; 
		// $ccb->addSingleHost(array("adapter" => "apc"));
		// $ccb->addSingleHost(array("adapter" => "eAccelerator"));
		// $ccb->addSingleHost(array("adapter" => "File", "host" => "/tmp/Lotus/unittest/cache/testPerformance/"));
		// $ccb->addSingleHost(array("adapter" => "memcache", "host" => "localhost", "port" => 11211));
		// $ccb->addSingleHost(array("adapter" => "memcached", "host" => "localhost", "port" => 11211));
		$ccb->addSingleHost(array("adapter" => "phps", "host" => "/tmp/Lotus/unittest/cache/testPerformance/")); 
		// $ccb->addSingleHost(array("adapter" => "Xcache", "key_prefix" => "test_xcache_"));
		
		
		LtCache::$servers = $ccb->getServers();

		/**
		 * 实例化组件入口类
		 */
		$cache = new LtCache;
		$cache->init(); 
		// 初始化完毕，测试其效果
		$ch = $cache->getTDG('test-performance');

		$this->assertTrue($ch->add("test_key", "test_value"));
		$this->assertEquals("test_value", $ch->get("test_key"));
		$this->assertTrue($ch->update("test_key", "new_value"));
		$this->assertEquals("new_value", $ch->get("test_key"));
		$this->assertTrue($ch->del("test_key"));
		$this->assertFalse($ch->get("test_key"));

		/**
		 * 运行1000次，要求在1秒内运行完
		 */
		$base_memory_usage = memory_get_usage();
		$times = 1000;
		$startTime = microtime(true);
		// ----------------------------测试读取 
		$ch->add("test_key", "test_value");
		for($i = 0; $i < $times; $i++)
		{
			$ch->get("test_key");
		}
		$ch->update("test_key", "new_value");
		$ch->del("test_key");
		// ----------------------------测试完成 
		$endTime = microtime(true);
		$totalTime = round(($endTime - $startTime), 6);
		$averageTime = round(($totalTime / $times), 6);

		$memory_usage = memory_get_usage() - $base_memory_usage;
		$averageMemory = $this->size($memory_usage / $times);
		$memory_usage = $this->size($memory_usage);

		echo "\n--------------LtCache     get          -----------------\n";
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
		LtCache::$servers = null;
	}
	private function size($size)
	{
		if ($size >= 1073741824)
		{
			$size = round($size / 1073741824, 2) . ' GB';
		}
		else if ($size >= 1048576)
		{
			$size = round($size / 1048576, 2) . ' MB';
		}
		else if ($size >= 1024)
		{
			$size = round($size / 1024, 2) . ' KB';
		}
		else
		{
			$size = round($size, 2) . ' Bytes';
		}
		return $size;
	}
}
