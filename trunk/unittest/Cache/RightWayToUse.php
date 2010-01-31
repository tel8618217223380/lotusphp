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
	 *  # key必须是数字或者字串，不能是数组，对象 
	 * 
	 * -------------------------------------------------------------------
	 * LtCache不在意：
	 *  # value的数据类型是什么（但一般来说resource型数据是不能被缓存的） 
	 * 
	 * -------------------------------------------------------------------
	 * LtCache建议（不强求）：
	 *  # 为保证key不冲突，最好定义多个group，将不同领域的数据分开存
	 * 
	 * 本测试用例期望效果：
	 * 能成功通过add(), get(), del(), update()接口读写数据
	 */
	public function testMostUsedWay()
	{
		/**
		 * 实例化组件入口类
		 */
		$cache = new LtCache;
		$cache->init();

		//初始化完毕，测试其效果
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
		 * 实例化组件入口类
		 */
		$cache = new LtCache;
		$cache->init();

		//初始化完毕，测试其效果
		$ch = $cache->getCacheHandle();

		$this->assertTrue($ch->add("test_key", "test_value"));
		$this->assertEquals("test_value", $ch->get("test_key"));
		$this->assertTrue($ch->update("test_key", "new_value"));
		$this->assertEquals("new_value", $ch->get("test_key"));
		$this->assertTrue($ch->del("test_key"));
		$this->assertFalse($ch->get("test_key"));
	}

	/**
	 * 测试单机单库的配置方法
	 */
	public function configBuilderDataProvider()
	{
		$singleHost1 = array(
			"adapter"        => "phps",
			"host"           => "/tmp/cache_file_root_1",
		);
		$expected1["group_0"]["node_0"]["master"][] = array(
			"adapter"        => "phps",
			"host"           => "/tmp/cache_file_root_1",
		);
		$singleHost2 = array(
			"adapter"        => "apc",
		);
		$expected2["group_0"]["node_0"]["master"][] = array(
			"adapter"        => "apc",
		);
		$singleHost3 = array(
			"adapter"        => "memcached",
			"host"           => "localhost",
			"port"           => 11211,
		);
		$expected3["group_0"]["node_0"]["master"][] = array(
			"adapter"        => "memcached",
			"host"           => "localhost",
			"port"           => 11211,
		);
		return array(
			array($singleHost1, $expected1),
			array($singleHost2, $expected2),
			array($singleHost3, $expected3),
		);
	}

	/**
	 * @dataProvider configBuilderDataProvider
	 */
	public function testConfigBuilder($singleHost, $expected)
	{
		$ccb = new LtCacheConfigBuilder;
		$ccb->addSingleHost($singleHost);
		$this->assertEquals($expected, $ccb->getServers());
	}

	/**
	 * 测试分布式缓存的配置方法
	 * 适用场景：
	 * 一个类似淘宝、ebay的电子商务网站
	 */
	public function testConfigBuilderDistDb()
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
		$ccb->addHost("prod_stat", "node_0", "master", array("adapter" => "memcached", "key_prefix" => 4, "host" => "10.0.0.1", "port" => 11211));
		//如果全用不同的memcache服务器（主机地址或端口不相同 ），可以不指定key_prefix
		//$ccb->addHost("user_card", "node_0", "master", array("adapter" => "memcached", "host" => "10.0.0.1", "port" => 11211));
		//$ccb->addHost("prod_stat", "node_0", "master", array("adapter" => "memcached", "host" => "10.0.0.1", "port" => 11212));

		/**
		 * 商品数据和订单数据缓存
		 * 特点：数据条数极多，每条数据量大，占用空间大，变化频率低，适合用文件缓存
		 * prod_info表示商品数据，存储商品标题、描述等 信息
		 * trade_info表示订单数据，存储订单详情，及该订单涉及的商品的快照、交易双方的信用等级
		 * 如果在同 一个目录下，需要用不同的key_prefix，防止key冲突
		 */
		$ccb->addHost("prod_info", "node_0", "master", array("adapter" => "phps", "host" => "/var/data/cache_files/prod_info"));
		$ccb->addHost("trade_info", "node_0", "master", array("adapter" => "phps", "host" => "/var/data/cache_files/trade_info"));
		//如果在同 一个目录下，需要用不同的key_prefix，防止key冲突
		$ccb->addHost("prod_info", "node_0", "master", array("adapter" => "phps", "key_prefix" => 5, "host" => "/var/data/cache_files"));
		$ccb->addHost("trade_info", "node_0", "master", array("adapter" => "phps", "key_prefix" => 6, "host" => "/var/data/cache_files"));

		$this->assertEquals(
		array(
			"sys_group" => array(
				"sys_node_1" => array(
					"master" => array(
						array(
									"adapter"        => "apc",
						),
					),
				),
			),
			"user_group" => array(
				"user_node_1" => array(
					"master" => array(
						array(
									"host"           => "10.0.1.1",
									"port"           => 3306,
									"username"       => "root",
									"password"       => "123456",
									"adapter"        => "mysqli",
									"charset"        => "UTF-8",
									"pconnect"       => false,
									"connection_ttl" => 30,
									"dbname"         => null,
									"schema"         => "member_1",
									"connection_adapter" => "mysqli",
								  "sql_adapter"        => "mysql",
						),
					),
				),
				"user_node_2" => array(
					"master" => array(
						array(
									"host"           => "10.0.1.1",
									"port"           => 3306,
									"username"       => "root",
									"password"       => "123456",
									"adapter"        => "mysqli",
									"charset"        => "UTF-8",
									"pconnect"       => false,
									"connection_ttl" => 30,
									"dbname"         => null,
									"schema"         => "member_2",
									"connection_adapter" => "mysqli",
								  "sql_adapter"        => "mysql",
						),
					),
				),
			),
			"trade_group" => array(
				"trade_node_1" => array(
					"master" => array(
						array(
									"host"           => "10.0.2.1",
									"port"           => 1521,
									"username"       => "root",
									"password"       => "123456",
									"adapter"        => "oci",
									"charset"        => "UTF-8",
									"pconnect"       => true,
									"connection_ttl" => 3600,
									"dbname"         => "finance",
									"schema"         => "trade",
									"connection_adapter" => "oci",
								  "sql_adapter"        => "oracle",
						),
						array(
									"host"           => "10.0.2.2",
									"port"           => 1521,
									"username"       => "root",
									"password"       => "123456",
									"adapter"        => "oci",
									"charset"        => "UTF-8",
									"pconnect"       => true,
									"connection_ttl" => 3600,
									"dbname"         => "finance",
									"schema"         => "trade",
									"connection_adapter" => "oci",
								  "sql_adapter"        => "oracle",
						),
					),
				),
				"trade_node_2" => array(
					"master" => array(
						array(
									"host"           => "10.0.2.3",
									"port"           => 1521,
									"username"       => "root",
									"password"       => "123456",
									"adapter"        => "oci",
									"charset"        => "UTF-8",
									"pconnect"       => true,
									"connection_ttl" => 3600,
									"dbname"         => "finance",
									"schema"         => "trade",
									"connection_adapter" => "oci",
								  "sql_adapter"        => "oracle",
						),
						array(
									"host"           => "10.0.2.4",
									"port"           => 1521,
									"username"       => "root",
									"password"       => "123456",
									"adapter"        => "oci",
									"charset"        => "UTF-8",
									"pconnect"       => true,
									"connection_ttl" => 3600,
									"dbname"         => "finance",
									"schema"         => "trade",
									"connection_adapter" => "oci",
								  "sql_adapter"        => "oracle",
						),
					),
				),
				"trade_node_3" => array(
					"master" => array(
						array(
									"host"           => "10.0.2.5",
									"port"           => 1521,
									"username"       => "root",
									"password"       => "123456",
									"adapter"        => "oci",
									"charset"        => "UTF-8",
									"pconnect"       => true,
									"connection_ttl" => 3600,
									"dbname"         => "finance",
									"schema"         => "trade",
									"connection_adapter" => "oci",
								  "sql_adapter"        => "oracle",
						),
						array(
									"host"           => "10.0.2.6",
									"port"           => 1521,
									"username"       => "root",
									"password"       => "123456",
									"adapter"        => "oci",
									"charset"        => "UTF-8",
									"pconnect"       => true,
									"connection_ttl" => 3600,
									"dbname"         => "finance",
									"schema"         => "trade",
									"connection_adapter" => "oci",
								  "sql_adapter"        => "oracle",
						),
					),
				),
			),
		),
		$ccb->getServers()
		);//end $this->assertEquals
	}
}
