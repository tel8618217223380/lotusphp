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
	 *    # key必须是数字或者字串，不能是数组，对象 
	 * 
	 * -------------------------------------------------------------------
	 * LtCache不在意：
	 *    # value的数据类型是什么（但一般来说resource型数据是不能被缓存的） 
	 * 
	 * -------------------------------------------------------------------
	 * LtCache建议（不强求）：
	 *    # 为保证key不冲突，最好定义多个group，将不同领域的数据分开存 
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
		//$ccb->addSingleHost(array("adapter" => "apc", "key_prefix" => "test_apc_"));
		//$ccb->addSingleHost(array("adapter" => "eAccelerator", "key_prefix" => "test_eAccelerator_"));
		//$ccb->addSingleHost(array("adapter" => "File", "host" => "/tmp/Lotus/unittest/cache/"));
		//$ccb->addSingleHost(array("adapter" => "memcache", "host" => "localhost", "port" => 11211));
		//$ccb->addSingleHost(array("adapter" => "memcached", "host" => "localhost", "port" => 11211));
		$ccb->addSingleHost(array("adapter" => "phps", "host" => "/tmp/Lotus/unittest/cache/", "key_prefix" => "test_phps"));
		//$ccb->addSingleHost(array("adapter" => "Xcache", "key_prefix" => "test_xcache_"));
		LtCache::$servers = $ccb->getServers();

		/**
		 * 实例化组件入口类
		 */
		$cache = new LtCache;
		$cache->init(); 
		// 初始化完毕，测试其效果
		$ch = $cache->getCacheHandle();

		$this->assertTrue($ch->add("test_key", "test_value"));
		$this->assertEquals("test_value", $ch->get("test_key"));
		$this->assertTrue($ch->update("test_key", "new_value"));
		$this->assertEquals("new_value", $ch->get("test_key"));
		$this->assertTrue($ch->del("test_key"));
		$this->assertFalse($ch->get("test_key"));
	}

	public function testMostUsedWayWithMultiGroup()
	{
		/**
		 * 构造缓存配置
		 */
		$ccb = new LtCacheConfigBuilder;
		$ccb->addHost("prod_info", "node_0", "master", array("adapter" => "phps", "host" => "/tmp/Lotus/unittest/cache/prod_info"));
		$ccb->addHost("trade_info", "node_0", "master", array("adapter" => "phps", "host" => "/tmp/Lotus/unittest/cache/trade_info"));
		LtCache::$servers = $ccb->getServers();

		/**
		 * 操作prod_info
		 */
		$cache1 = new LtCache;
		$cache1->group = "prod_info";
		$cache1->init();

		$ch = $cache1->getCacheHandle();
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
		$cache2->group = "trade_info";
		$cache2->init();

		$ch = $cache2->getCacheHandle();
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
		 * 他们都使用本地共享内存，用不同的key_prefix，防止key冲突
		 */
		$ccb->addHost("prod_cat", "node_0", "master", array("adapter" => "apc", "key_prefix" => 1));
		$ccb->addHost("geo_code", "node_0", "master", array("adapter" => "apc", "key_prefix" => 2));

		/**
		 * 用户 名片数据和商品统计数据缓存 
		 * 特点：数据条数极多，每条数据量小，变化频率高，访问频率很高，适合用memcached 
		 * user_card表示用户 名片数据，存储用户 的昵称、信用点数，最后活动时间；prod_stat表示商品统计数据，存储商品的点击数，收藏数，最后编辑时间 
		 * 如果使用同一个memcache服务器（主机地址和端口都相同 ），用不同的key_prefix，防止key冲突
		 */
		$ccb->addHost("user_card", "node_0", "master", array("adapter" => "memcached", "key_prefix" => 3, "host" => "10.0.0.1", "port" => 11211));
		$ccb->addHost("user_card", "node_1", "master", array("adapter" => "memcached", "key_prefix" => 3, "host" => "10.0.0.2", "port" => 11211));
		$ccb->addHost("prod_stat", "node_0", "master", array("adapter" => "memcached", "key_prefix" => 4, "host" => "10.0.0.1", "port" => 11211));
		$ccb->addHost("prod_stat", "node_1", "master", array("adapter" => "memcached", "key_prefix" => 4, "host" => "10.0.0.2", "port" => 11211)); 
		// 如果全用不同的memcache服务器（主机地址或端口不相同 ），可以不指定key_prefix
		// $ccb->addHost("user_card", "node_0", "master", array("adapter" => "memcached", "host" => "10.0.0.1", "port" => 11211));
		// $ccb->addHost("prod_stat", "node_0", "master", array("adapter" => "memcached", "host" => "10.0.0.1", "port" => 11212));
		/**
		 * 商品数据和订单数据缓存 
		 * 特点：数据条数极多，每条数据量大，占用空间大，变化频率低，适合用文件缓存 
		 * prod_info表示商品数据，存储商品标题、描述等 信息 
		 * trade_info表示订单数据，存储订单详情，及该订单涉及的商品的快照、交易双方的信用等级
		 * 如果在同 一个目录下，需要用不同的key_prefix，防止key冲突
		 */
		$ccb->addHost("prod_info", "node_0", "master", array("adapter" => "phps", "host" => "/var/data/LtCache/test/phps/prod_info"));
		$ccb->addHost("trade_info", "node_0", "master", array("adapter" => "phps", "host" => "/var/data/LtCache/test/phps/trade_info")); 
		// 如果在同 一个目录下，需要用不同的key_prefix，防止key冲突
		// $ccb->addHost("prod_info", "node_0", "master", array("adapter" => "phps", "key_prefix" => 5, "host" => "/var/data/LtCache/test/phps/"));
		// $ccb->addHost("trade_info", "node_0", "master", array("adapter" => "phps", "key_prefix" => 6, "host" => "/var/data/LtCache/test/phps/"));
		$this->assertEquals(
			array("prod_cat" => array("node_0" => array("master" => array(
							array("adapter" => "apc",
								"key_prefix" => 1,
								),
							),
						),
					),
				"geo_code" => array("node_0" => array("master" => array(
							array("adapter" => "apc",
								"key_prefix" => 2,
								),
							),
						),
					),
				"user_card" => array("node_0" => array("master" => array(
							array("adapter" => "memcached",
								"key_prefix" => 3,
								"host" => "10.0.0.1",
								"port" => 11211,
								),
							),
						),
					"node_1" => array("master" => array(
							array("adapter" => "memcached",
								"key_prefix" => 3,
								"host" => "10.0.0.2",
								"port" => 11211,
								),
							),
						),
					),
				"prod_stat" => array("node_0" => array("master" => array(
							array("adapter" => "memcached",
								"key_prefix" => 4,
								"host" => "10.0.0.1",
								"port" => 11211,
								),
							),
						),
					"node_1" => array("master" => array(
							array("adapter" => "memcached",
								"key_prefix" => 4,
								"host" => "10.0.0.2",
								"port" => 11211,
								),
							),
						),
					),
				"prod_info" => array("node_0" => array("master" => array(
							array("adapter" => "phps",
								"host" => "/var/data/LtCache/test/phps/prod_info",
								),
							),
						),
					),
				"trade_info" => array("node_0" => array("master" => array(
							array("adapter" => "phps",
								"host" => "/var/data/LtCache/test/phps/trade_info",
								),
							),
						),
					),
				),
			$ccb->getServers()
			); //end $this->assertEquals
	}
	/**
	 * 测试ttl
	 */
	public function testCacheTTL()
	{
		/**
		 * 准备测试数据
		 */
		$testDataList = array(
			// $key => value
			1 => 2,
			1.1 => null,
			-1 => "",
			true => false,
			"array" => array(1, 2, 4),
			"object" => new LtCache,
			"test_key" => "test_value",
			);
		$ttl_add = 0;
		$ttl_update = 2;

		/**
		 * 构造缓存配置
		 */
		$ccb = new LtCacheConfigBuilder;
		$ccb->addHost("test_ttl", "node_0", "master", array("adapter" => "phps", "host" => "/tmp/Lotus/unittest/cache/phps_ttl/"));
		$ccb->addHost("test_ttl", "node_1", "master", array("adapter" => "file", "host" => "/tmp/Lotus/unittest/cache/file_ttl/"));
		//$ccb->addHost("test_ttl", "node_2", "master", array("adapter" => "eAccelerator", "key_prefix" => "test"));

		LtCache::$servers = $ccb->getServers();

		/**
		 * 测试test_ttl组各节点
		 */
		$cache = new LtCache;
		foreach(LtCache::$servers['test_ttl'] as $k => $v)
		{
			$cache->group = "test_ttl";
			$cache->node = $k;
			$cache->init();
			echo "\n------".$cache->group .'------'.$cache->node."------\n";

			$ch = $cache->getCacheHandle();

			foreach ($testDataList as $k => $v)
			{
				$this->assertTrue($ch->add($k, $v, $ttl_add));
				sleep(1);
				$this->assertEquals($v, $ch->get($k));
				$this->assertTrue($ch->update($k, $v, $ttl_update));
				sleep(1);
				$this->assertEquals($v, $ch->get($k));
				sleep(2);
				$this->assertFalse($ch->get($k));
			}
		}
	}

	protected function setUp()
	{
	}
	protected function tearDown()
	{
		LtCache::$servers = null;
	}
}
