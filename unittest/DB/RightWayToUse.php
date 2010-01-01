<?php
/**
 * 本测试文档演示了LtDb的正确使用方法 
 * 按本文档操作一定会得到正确的结果
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "common.inc.php";
class RightWayToUseDb extends PHPUnit_Framework_TestCase
{
	/**
	 * 测试单机单库的配置方法
	 */
	public function configBuilderDataProvider()
	{
		$singleHost1 = array(
			"password"       => "123456",
			"dbname"         => "test",
		);
		$expected1["group_0"]["node_0"]["master"][] = array(
			"host"           => "localhost",
			"port"           => 3306,
			"username"       => "root",
			"password"       => "123456",
			"adapter"        => "mysql",
			"charset"        => "UTF-8",
			"pconnect"       => false,
			"connection_ttl" => 30,
			"dbname"         => null,
			"schema"         => "test",
		);
		$singleHost2 = array(
			"password"       => "123456",
			"dbname"         => "test",
			"adapter"        => "mssql",
			"port"           => 1433,
		);
		$expected2["group_0"]["node_0"]["master"][] = array(
			"host"           => "localhost",
			"port"           => 1433,
			"username"       => "root",
			"password"       => "123456",
			"adapter"        => "mssql",
			"charset"        => "UTF-8",
			"pconnect"       => false,
			"connection_ttl" => 30,
			"dbname"         => null,
			"schema"         => "test",
		);
		$singleHost3 = array(
			"password"       => "123456",
			"dbname"         => "test",
			"schema"         => "sys_data",
			"adapter"        => "pgsql",
			"port"           => 5432,
		);
		$expected3["group_0"]["node_0"]["master"][] = array(
			"host"           => "localhost",
			"port"           => 5432,
			"username"       => "root",
			"password"       => "123456",
			"adapter"        => "pgsql",
			"charset"        => "UTF-8",
			"pconnect"       => false,
			"connection_ttl" => 30,
			"dbname"         => "test",
			"schema"         => "sys_data",
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
		$dcb = new LtDbConfigBuilder;
		$dcb->addSingleHost($singleHost);
		$this->assertEquals($expected, $dcb->getServers());
	}

	/**
	 * 测试分布式数据库的配置方法
	 */
	public function testConfigBuilderDistDb()
	{
		$dcb = new LtDbConfigBuilder;
		/**
		 * 配置系统数据组
		 * 一个节点， 一主两从，分布在三台不同的机器上
		 */
		$dcb->addHost("sys_group", "sys_node_1", "master", array("host" => "10.0.0.1", "port" => 5432, "password" => "123456", "dbname" => "sys_data", "schema" => "public", "adapter" => "pgsql", "pconnect" => true));
		$dcb->addHost("sys_group", "sys_node_1", "slave", array("host" => "10.0.0.2", "adapter" => "pdo_pgsql"));
		$dcb->addHost("sys_group", "sys_node_1", "slave", array("host" => "10.0.0.3"));
		
		/**
		 * 配置用户数据组
		 * 两个节点
		 * 每个节点一主一从
		 * 都在同一台机器上，不同节点数据库名不同，主从服务器的端口不同
		 */
		$dcb->addHost("user_group", "user_node_1", "master", array("host" => "10.0.1.1", "password" => "123456", "adapter" => "mysqli", "dbname" => "member_1"));
		$dcb->addHost("user_group", "user_node_1", "slave", array("port" => 3307));
		$dcb->addHost("user_group", "user_node_2", "master", array("dbname" => "member_2"));
		$dcb->addHost("user_group", "user_node_2", "slave", array("port" => 3307));

		/**
		 * 配置交易数据组
		 * 三个节点
		 * 每个节点两台机器互为主从
		 */
		$dcb->addHost("trade_group", "trade_node_1", "master", array("host" => "10.0.2.1", "password" => "123456", "adapter" => "oci", "dbname" => "finance", "schema" => "trade"));
		$dcb->addHost("trade_group", "trade_node_1", "master", array("host" => "10.0.2.2"));
		$dcb->addHost("trade_group", "trade_node_2", "master", array("host" => "10.0.2.3"));
		$dcb->addHost("trade_group", "trade_node_2", "master", array("host" => "10.0.2.4"));
		$dcb->addHost("trade_group", "trade_node_3", "master", array("host" => "10.0.2.5"));
		$dcb->addHost("trade_group", "trade_node_3", "master", array("host" => "10.0.2.6"));

		$this->assertEquals(
		array(
			"sys_group" => array(
				"sys_node_1" => array(
					"master" => array(
						array(
									"host"           => "10.0.0.1",
									"port"           => 5432,
									"username"       => "root",
									"password"       => "123456",
									"adapter"        => "pgsql",
									"charset"        => "UTF-8",
									"pconnect"       => true,
									"connection_ttl" => 30,
									"dbname"         => "sys_data",
									"schema"         => "public",
						),
					),
					"slave" => array(
						array(
									"host"           => "10.0.0.2",
									"port"           => 5432,
									"username"       => "root",
									"password"       => "123456",
									"adapter"        => "pdo_pgsql",
									"charset"        => "UTF-8",
									"pconnect"       => true,
									"connection_ttl" => 30,
									"dbname"         => "sys_data",
									"schema"         => "public",
						),
						array(
									"host"           => "10.0.0.3",
									"port"           => 5432,
									"username"       => "root",
									"password"       => "123456",
									"adapter"        => "pdo_pgsql",
									"charset"        => "UTF-8",
									"pconnect"       => true,
									"connection_ttl" => 30,
									"dbname"         => "sys_data",
									"schema"         => "public",
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
						),
					),
					"slave" => array(
						array(
									"host"           => "10.0.1.1",
									"port"           => 3307,
									"username"       => "root",
									"password"       => "123456",
									"adapter"        => "mysqli",
									"charset"        => "UTF-8",
									"pconnect"       => false,
									"connection_ttl" => 30,
									"dbname"         => null,
									"schema"         => "member_1",
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
						),
					),
					"slave" => array(
						array(
									"host"           => "10.0.1.1",
									"port"           => 3307,
									"username"       => "root",
									"password"       => "123456",
									"adapter"        => "mysqli",
									"charset"        => "UTF-8",
									"pconnect"       => false,
									"connection_ttl" => 30,
									"dbname"         => null,
									"schema"         => "member_2",
						),
					),
				),
			),
			"trade_group" => array(
				"trade_node_1" => array(
					"master" => array(
						array(
									"host"           => "10.0.2.1",
									"port"           => 3306,
									"username"       => "root",
									"password"       => "123456",
									"adapter"        => "oci",
									"charset"        => "UTF-8",
									"pconnect"       => false,
									"connection_ttl" => 30,
									"dbname"         => "finance",
									"schema"         => "trade",
						),
						array(
									"host"           => "10.0.2.2",
									"port"           => 3306,
									"username"       => "root",
									"password"       => "123456",
									"adapter"        => "oci",
									"charset"        => "UTF-8",
									"pconnect"       => false,
									"connection_ttl" => 30,
									"dbname"         => "finance",
									"schema"         => "trade",
						),
					),
				),
				"trade_node_2" => array(
					"master" => array(
						array(
									"host"           => "10.0.2.3",
									"port"           => 3306,
									"username"       => "root",
									"password"       => "123456",
									"adapter"        => "oci",
									"charset"        => "UTF-8",
									"pconnect"       => false,
									"connection_ttl" => 30,
									"dbname"         => "finance",
									"schema"         => "trade",
						),
						array(
									"host"           => "10.0.2.4",
									"port"           => 3306,
									"username"       => "root",
									"password"       => "123456",
									"adapter"        => "oci",
									"charset"        => "UTF-8",
									"pconnect"       => false,
									"connection_ttl" => 30,
									"dbname"         => "finance",
									"schema"         => "trade",
						),
					),
				),
				"trade_node_3" => array(
					"master" => array(
						array(
									"host"           => "10.0.2.5",
									"port"           => 3306,
									"username"       => "root",
									"password"       => "123456",
									"adapter"        => "oci",
									"charset"        => "UTF-8",
									"pconnect"       => false,
									"connection_ttl" => 30,
									"dbname"         => "finance",
									"schema"         => "trade",
						),
						array(
									"host"           => "10.0.2.6",
									"port"           => 3306,
									"username"       => "root",
									"password"       => "123456",
									"adapter"        => "oci",
									"charset"        => "UTF-8",
									"pconnect"       => false,
									"connection_ttl" => 30,
									"dbname"         => "finance",
									"schema"         => "trade",
						),
					),
				),
			),
		),
		$dcb->getServers()
		);//end $this->assertEquals
	}
}
