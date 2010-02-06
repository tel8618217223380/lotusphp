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
		LtCache::$servers = $ccb->getServers();
		/**
		 * 实例化组件入口类
		 */
		$cache = new LtCache;
		$cache->init();
		$ch = $cache->getTDG('test');
		$ch->add("test_key", "test_value");
	}

	/**
	 * key冲突且都未过期
	 * 
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testFileKeyConflict()
	{
		/**
		 * 构造缓存配置
		 */
		$ccb = new LtCacheConfigBuilder;
		$ccb->addSingleHost(array("adapter" => "phps", "host" => "/tmp/Lotus/unittest/cache/phps_keyconflict/"));
		LtCache::$servers = $ccb->getServers();
		$cache = new LtCache;
		$cache->init();
		$ch = $cache->getTDG('test');

		$ch->add("test_key", "test_value");
		$ch->add("test_key", "test_value");
		$ch->del("test_key", "test_value");
	}

	/**
	 * 删除不存在的key
	 * 
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testFileKeyDel()
	{
		/**
		 * 构造缓存配置
		 */
		$ccb = new LtCacheConfigBuilder;
		$ccb->addSingleHost(array("adapter" => "phps", "host" => "/tmp/Lotus/unittest/cache/phps_keynotexists/", "key_prefix" => "test"));
		LtCache::$servers = $ccb->getServers();
		$cache = new LtCache;
		$cache->init();
		$ch = $cache->getTDG('test');

		$ch->del('key_not_exists', 'value', 2);
	}

	protected function setUp()
	{
	}

	protected function tearDown()
	{
		LtCache::$servers = null;
	}
}
