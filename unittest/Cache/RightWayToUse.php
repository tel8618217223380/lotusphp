<?php
/**
 * 本测试文档演示了LtCache的正确使用方法 
 * 按本文档操作一定会得到正确的结果
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "common.inc.php";
class RightWayToUseCache extends PHPUnit_Framework_TestCase
{
	/**
	 * 最常用的使用方式（推荐） 
	 * -------------------------------------------------------------------
	 * LtCache要求： 
	 *       # key必须是数字或者字串，不能是数组，对象 
	 * 
	 * -------------------------------------------------------------------
	 * LtCache不在意：
	 *       # value的数据类型是什么（但一般来说resource型数据是不能被缓存的） 
	 * 
	 * -------------------------------------------------------------------
	 * LtCache建议（不强求）：
	 *       # 为保证key不冲突，最好定义多个group，将不同领域的数据分开存 
	 * 
	 * 本测试用例期望效果：
	 * 能成功通过add(), get(), del(), update()接口读写数据
	 */
	public function testMostUsedWay()
	{
		/**
		 * 构造缓存配置
		 */
		$ccb = new LtCacheConfigBuilder;
		$ccb->addSingleHost(
			array("adapter" => "phps",
				"host" => "/tmp/Lotus/unittest/cache/phps"
			));
		LtCache::$servers = $ccb->getServers();
		/**
		 * 实例化组件入口类
		 */
		$cache = new LtCache;
		$cache->init();
		/**
		 * 初始化完毕, 测试其效果, 使用不同的tableName防止key冲突
		 */
		$ch = $cache->getTDG("test");

		$this->assertTrue($ch->add("test_key", "test_value"));
		$this->assertEquals("test_value", $ch->get("test_key"));
		$this->assertTrue($ch->update("test_key", "new_value"));
		$this->assertEquals("new_value", $ch->get("test_key"));
		$this->assertTrue($ch->del("test_key"));
		$this->assertFalse($ch->get("test_key"));
		//删除、更新不存在的key
		$this->assertFalse($ch->del("some_key_not_exists"));
		$this->assertFalse($ch->update("some_key_not_exists", "any value"));
		//添加重复的key
		$this->assertTrue($ch->add("key1", "value1"));
		$this->assertFalse($ch->add("key1", "value1"));
		$ch->del("key1");
	}

	/**
	 * @todo ttl测试
	 */
	public function testOpcodeCacheAdapter()
	{
		$opcodeCacheAdapters = array();
		if (extension_loaded('apc'))
		{
			echo "\n-----apc loaded-----\n";
			$opcodeCacheAdapters[] = "apc";
		}
		if (extension_loaded('eaccelerator'))
		{
			echo "\n-----eAccelerator loaded-----\n";
			$opcodeCacheAdapters[] = "eaccelerator";
		}
		if (extension_loaded('xcache'))
		{
			echo "\n-----xcache loaded-----\n";
			$opcodeCacheAdapters[] = "xcache";
		}
		foreach($opcodeCacheAdapters as $adapter)
		{
			$result = callWeb("Cache/opcode_cache_proxy.php", array(
				"adapter" => $adapter,
				"operation" => "add",
				"key" => "test_key",
				"table_name" => "test",
				"value" => "test_value"
			));
			$this->assertTrue(unserialize($result));
			
			$result = callWeb("Cache/opcode_cache_proxy.php", array(
				"adapter" => $adapter,
				"operation" => "get",
				"key" => "test_key",
				"table_name" => "test"
			));
			$this->assertEquals("test_value", unserialize($result));
			
			$result = callWeb("Cache/opcode_cache_proxy.php", array(
				"adapter" => $adapter,
				"operation" => "update",
				"key" => "test_key",
				"table_name" => "test",
				"value" => "new_value"
			));
			$this->assertTrue(unserialize($result));
			
			$result = callWeb("Cache/opcode_cache_proxy.php", array(
				"adapter" => $adapter,
				"operation" => "get",
				"key" => "test_key",
				"table_name" => "test"
			));
			$this->assertEquals("new_value", unserialize($result));
			
			$result = callWeb("Cache/opcode_cache_proxy.php", array(
				"adapter" => $adapter,
				"operation" => "del",
				"key" => "test_key",
				"table_name" => "test"
			));
			$this->assertEquals("test_value", unserialize($result));
			
			$result = callWeb("Cache/opcode_cache_proxy.php", array(
				"adapter" => $adapter,
				"operation" => "get",
				"key" => "test_key",
				"table_name" => "test"
			));
			$this->assertFalse(unserialize($result));
			
		}
	}

	public function testOtherCacheAdapter()
	{
		/**
		 * 构造缓存配置
		 */
		$ccb = new LtCacheConfigBuilder;

		/**
		 * 测试其它适配器add(), get(), del(), update()接口
		 */
		$ccb->addHost("group_phps", "node_0", "master", array("adapter" => "phps", "host" => "/tmp/Lotus/unittest/cache/phps_agdu/"));
		$ccb->addHost("group_file", "node_0", "master", array("adapter" => "file", "host" => "/tmp/Lotus/unittest/cache/file_agdu/"));
		if (extension_loaded('memcache'))
		{
			echo "\n-----memcache loaded-----\n";
			$ccb->addHost("group_memcache", "node_0", "master", array("adapter" => "memcache", "host" => "localhost", "port" => 11211));
		}
		if (extension_loaded('memcached'))
		{
			echo "\n-----memcached loaded-----\n";
			$ccb->addHost("group_memcached", "node_0", "master", array("adapter" => "memcached", "host" => "localhost", "port" => 11211));
		}
		LtCache::$servers = $ccb->getServers();
		
		/**
		 * 实例化组件入口类
		 */
		foreach(LtCache::$servers as $k => $v)
		{
			$cache = new LtCache;
			$cache->group = $k;
			$cache->node = "node_0";
			$cache->init();
			echo "\n------" . $cache->group . '------' . $cache->node . "------\n";

			$ch = $cache->getTDG("test_agdu");
			$this->assertTrue($ch->add("test_key", "test_value"));
			$this->assertEquals("test_value", $ch->get("test_key"));
			$this->assertTrue($ch->update("test_key", "new_value"));
			$this->assertEquals("new_value", $ch->get("test_key"));
			$this->assertTrue($ch->del("test_key"));
			$this->assertFalse($ch->get("test_key"));
			
			//测试TTL功能
			$this->assertTrue($ch->add("test_key", "test_value", 2));
			sleep(1);
			$this->assertEquals("test_value", $ch->get("test_key"));
			sleep(2);
			$this->assertFalse($ch->get("test_key"));
		}
	}

	public function testMostUsedWayWithMultiGroup()
	{
		/**
		 * 构造缓存配置
		 */
		$ccb = new LtCacheConfigBuilder;
		$ccb->addHost("phps_cache_1", "node_0", "master", array("adapter" => "phps", "host" => "/tmp/Lotus/unittest/cache_files_1"));
		$ccb->addHost("phps_cache_2", "node_0", "master", array("adapter" => "phps", "host" => "/tmp/Lotus/unittest/cache_files_2"));
		LtCache::$servers = $ccb->getServers();

		/**
		 * 操作prod_info
		 */
		$cache1 = new LtCache;
		$cache1->group = "phps_cache_1";
		$cache1->init();

		$ch = $cache1->getTDG("prod_info");
		$this->assertTrue($ch->add("key_1", "prod_1"));
		$this->assertEquals("prod_1", $ch->get("key_1"));
		$this->assertTrue($ch->update("key_1", "new_value"));
		$this->assertEquals("new_value", $ch->get("key_1"));
		$this->assertTrue($ch->del("key_1"));
		$this->assertFalse($ch->get("key_1"));

		/**
		 * 操作trade_info
		 * trade_info也用了key_1这个 键，但他并不会跟prod_info的key_1冲突，因为他们的host是不一样的
		 */
		$cache2 = new LtCache;
		$cache2->group = "phps_cache_2";
		$cache2->init();

		$ch = $cache2->getTDG("trade_info");
		$this->assertTrue($ch->add("key_1", "prod_1"));
		$this->assertEquals("prod_1", $ch->get("key_1"));
		$this->assertTrue($ch->update("key_1", "new_value"));
		$this->assertEquals("new_value", $ch->get("key_1"));
		$this->assertTrue($ch->del("key_1"));
		$this->assertFalse($ch->get("key_1"));
	}

	/**
	 * 测试分布式缓存的配置方法
	 * 适用场景： 
	 * 一个类似淘宝、ebay的电子商务网站
	 */
	public function testConfigBuilderDistCache()
	{
		$ccb = new LtCacheConfigBuilder;

		/**
		 * 系统数据缓存
		 * 特点：数据条数少且稳定，每条数据量小，变化频率低，访问频率高，适合用APC
		 * prod_cat表示发布商品时选择的系统商品类目 
		 * geo_code表示收货地址中用到的行政区划，省市区三级
		 * 他们都使用本地共享内存，用不同的tablename，防止key冲突
		 * 使用的时候就像这样：
		 * $cache = new LtCache;
		 * $cache->group = "local_memory";
		 * $prodCatCache = $cache->getTDG("prod_cat");
		 * $geoCodeCache = $cache->getTDG("geo_code");
		 */
		$ccb->addHost("local_memory", "node_0", "master", array("adapter" => "apc"));

		/**
		 * 用户 名片数据和商品统计数据缓存 
		 * 特点：数据条数极多，每条数据量小，变化频率高，访问频率很高，适合用memcached 
		 * user_card表示用户 名片数据，存储用户 的昵称、信用点数，最后活动时间；prod_stat表示商品统计数据，存储商品的点击数，收藏数，最后编辑时间 
		 * 如果使用同一个memcache服务器（主机地址和端口都相同 ），用不同的tablename，防止key冲突
		 * 使用的时候就像这样：
		 * $cache = new LtCache;
		 * $cache->group = "memcache";
		 * $prodStatCache = $cache->getTDG("prod_stat");
		 * $userCardCache = $cache->getTDG("user_card");
		 */
		$ccb->addHost("memcache", "node_0", "master", array("adapter" => "memcached", "host" => "10.0.0.1", "port" => 11211));
		$ccb->addHost("memcache", "node_1", "master", array("adapter" => "memcached", "host" => "10.0.0.2", "port" => 11211));

		/**
		 * 商品数据和订单数据缓存 
		 * 特点：数据条数极多，每条数据量大，占用空间大，变化频率低，适合用文件缓存 
		 * prod_info表示商品数据，存储商品标题、描述等 信息 
		 * trade_info表示订单数据，存储订单详情，及该订单涉及的商品的快照、交易双方的信用等级
		 * 如果在同 一个目录下，需要用不同的tablename，防止key冲突
		 * 使用的时候就像这样：
		 * $cache = new LtCache;
		 * $cache->group = "local_file";
		 * $prodStatCache = $cache->getTDG("prod_detail");
		 * $userCardCache = $cache->getTDG("trade_detail");
		 */
		$ccb->addHost("local_file", "node_0", "master", array("adapter" => "phps", "host" => "/var/data/LtCache/test/phps/"));

		$this->assertEquals(
			array("local_memory" => array("node_0" => array("master" => array(
							array("adapter" => "apc",

								),
							),
						),
					),
				"memcache" => array("node_0" => array("master" => array(
							array("adapter" => "memcached",

								"host" => "10.0.0.1",
								"port" => 11211,
								),
							),
						),
					"node_1" => array("master" => array(
							array("adapter" => "memcached",

								"host" => "10.0.0.2",
								"port" => 11211,
								),
							),
						),
					),
				"local_file" => array("node_0" => array("master" => array(
							array("adapter" => "phps",
								"host" => "/var/data/LtCache/test/phps/",
								),
							),
						),
					),
				),
			$ccb->getServers()
			); //end $this->assertEquals
	}

	protected function setUp()
	{
	}

	protected function tearDown()
	{
		LtCache::$servers = null;
	}
}
