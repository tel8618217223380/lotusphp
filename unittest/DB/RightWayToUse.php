<<<<<<< .mine
<?php
/**
 * 本测试文档演示了LtDb的正确使用方法 
 * 按本文档操作一定会得到正确的结果
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "common.inc.php";
class RightWayToUseDb extends PHPUnit_Framework_TestCase
{
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
			"port"           => 1433,
		);
		$expected3["group_0"]["node_0"]["master"][] = array(
			"host"           => "localhost",
			"port"           => 1433,
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
	 * 
	 */
	public function testConfigBuilderDistDb()
	{
		$dcb = new LtDbConfigBuilder;
		//配置系统数据组
		$dcb->addHost(array("host" => "127.0.0.1", "password" => "123456", "dbname" => "sys_data"), "master", "sys_node_1", "sys_group");
		$dcb->addHost(array("host" => "127.0.0.2"), "slave", "sys_node_1", "sys_group");
		$dcb->addHost(array("host" => "127.0.0.3"), "slave", "sys_node_1", "sys_group");
		//配置用户数据组
		$dcb->addHost(array("host" => "127.0.0.4", "dbname" => "member_1"), "master", "user_node_1", "user_group");
		$dcb->addHost(array("dbname" => "member_2"), "master", "user_node_2", "user_group");

		$this->assertEquals(
		array(
			"sys_group" => array(
				"sys_node_1" => array(
					"master" => array(
						array(
									"host"           => "127.0.0.1",
									"port"           => 3306,
									"username"       => "root",
									"password"       => "123456",
									"adapter"        => "mysql",
									"charset"        => "UTF-8",
									"pconnect"       => false,
									"connection_ttl" => 30,
									"dbname"         => null,
									"schema"         => "sys_data",
						),
					),//end master
					"slave" => array(
						array(
									"host"           => "127.0.0.2",
									"port"           => 3306,
									"username"       => "root",
									"password"       => "123456",
									"adapter"        => "mysql",
									"charset"        => "UTF-8",
									"pconnect"       => false,
									"connection_ttl" => 30,
									"dbname"         => null,
									"schema"         => "sys_data",
						),
						array(
									"host"           => "127.0.0.3",
									"port"           => 3306,
									"username"       => "root",
									"password"       => "123456",
									"adapter"        => "mysql",
									"charset"        => "UTF-8",
									"pconnect"       => false,
									"connection_ttl" => 30,
									"dbname"         => null,
									"schema"         => "sys_data",
						),
					),//end slave
				),//end sys_node_1
			),//end sys_group
			"user_group" => array(
				"user_node_1" => array(
					"master" => array(
						array(
									"host"           => "127.0.0.4",
									"port"           => 3306,
									"username"       => "root",
									"password"       => "123456",
									"adapter"        => "mysql",
									"charset"        => "UTF-8",
									"pconnect"       => false,
									"connection_ttl" => 30,
									"dbname"         => null,
									"schema"         => "member_1",
						),
					),//end master
				),//end user_node_1
				"user_node_2" => array(
					"master" => array(
						array(
									"host"           => "127.0.0.4",
									"port"           => 3306,
									"username"       => "root",
									"password"       => "123456",
									"adapter"        => "mysql",
									"charset"        => "UTF-8",
									"pconnect"       => false,
									"connection_ttl" => 30,
									"dbname"         => null,
									"schema"         => "member_2",
						),
					),//end master
				),//end user_node_2
			),//end user_group
		),
		$dcb->getServers()
		);//end $this->assertEquals
	}
}
