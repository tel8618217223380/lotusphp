<?php
/**
 * 本测试文档演示了LtCache的错误使用方法 
 * 不要按本文档描述的方式使用LtCache
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "common.inc.php";
class WrongWayToUseCache extends PHPUnit_Framework_TestCase
{
	/**
	 * 使用尚未支持的adapter
	 * 
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testInvalidAdapter()
	{
		/**
		 * 构造缓存配置
		 */
		$ccb = new LtCacheConfigBuilder;
		$ccb->addSingleHost(array("adapter" => "not_exists", "host" => "/tmp/Lotus/unittest/cache/"));

		/**
		 * 实例化组件入口类
		 */
		$cache = new LtCache;
		$cache->configHandle->addConfig(array("cache.servers" => $ccb->getServers()));
		$cache->init();
		$ch = $cache->getTDG('test');
		$ch->add("test_key", "test_value");
	}
	/**
	 * phps 没有设置host或者没有设置正确的host
	 * 
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testNotSetPhpsHost()
	{
		/**
		 * 构造缓存配置
		 */
		$ccb = new LtCacheConfigBuilder;
		$ccb->addSingleHost(array("adapter" => "phps"));
		/**
		 * 实例化组件入口类
		 */
		$cache = new LtCache;
		$cache->configHandle->addConfig(array("cache.servers" => $ccb->getServers()));
		$cache->init();
		$ch = $cache->getTDG('test');
		$ch->add("test_key", "test_value");
		$ch->del("test_key");
	}
	/**
	 * file 没有设置host或者没有设置正确的host
	 * 
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testNotSetFileHost()
	{
		/**
		 * 构造缓存配置
		 */
		$ccb = new LtCacheConfigBuilder;
		$ccb->addSingleHost(array("adapter" => "file"));
		/**
		 * 实例化组件入口类
		 */
		$cache = new LtCache;
		$cache->configHandle->addConfig(array("cache.servers" => $ccb->getServers()));
		$cache->init();
		$ch = $cache->getTDG('test');
		$ch->add("test_key", "test_value");
		$ch->del("test_key");
	}

	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}
}
