<?php
/**
 * 本测试文档演示了LtDb的正确使用方法 
 * 按本文档操作一定会得到正确的结果
 * 
 * 使用分布式数据注意事项：
 *   1. 同一节点下相同角色的服务器必须使用同一种数据系统
 *      不能master1用oracle, master2用pgsql
 *   2. 同一节点的master和slave服务器可以使用不同的数据库系统
 *      比如所有master都用oracle,所有slave都用mysql
 *      这种情况下，使用DbHandle和SqlMap查询DB前
 *      必须手工指定DbHandle->role是master还是slave
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "common.inc.php";
class RightWayToUseDb extends PHPUnit_Framework_TestCase
{
	public function MostUsedWay()
	{
		/**
		 * 用法 1： 直接操作数据库
		 * 
		 * 优点：学习成本低，快速入门
		 * 
		 * 适用场景：
		 *     1. 临时写个脚本操作数据库，不想花时间学习LtDb的查询引擎
		 *     2. 只写少量脚本，不是一个完整持续的项目，不需要SqlMap来管理SQL语句
		 */
		$dbh = $db->getDbHandle();
		foreach($this->testDataList as $testData)
		{
			$result = $dbh->query($testData[0], $testData[1]);
			$this->assertEquals($result, $testData[2]);
		}

		/**
		 * 用法 2： 使用Table Gateway查询引擎
		 * 
		 * 优点：自动生成SQL语句
		 * 
		 * 适用场景：
		 *     1. 对数据表进行增简单的删查改操作，尤其是单条数据的操作
		 *     2. 简单的SELECT，动态合成WHERE子句
		 */
		$tg = $db->getTableGateway("test_user");
		$this->assertEquals($id = $tg->insert(array("id" => 2, "name" => "kiwiphp", "age" => 4)), 2);
		$this->assertEquals($tg->fetch($id), array("id" => 2, "name" => "kiwiphp", "age" => 4));
		$this->assertEquals($id = $tg->insert(array("name" => "chin", "age" => 28)), 3);
		$this->assertEquals($tg->fetchRows(), array(array("id" => 2, "name" => "kiwiphp", "age" => 4),array("id" => 3, "name" => "chin", "age" => 28)));
		$this->assertEquals($tg->update(3, array("name" => "Qin")), 1);
		$this->assertEquals($tg->fetch($id), array("id" => 3, "name" => "Qin", "age" => 28));
		$this->assertEquals($tg->count(), 2);
		$this->assertEquals($tg->delete(3), 1);
		$this->assertEquals($tg->fetchRows(), array(array("id" => 2, "name" => "kiwiphp", "age" => 4)));

		/**
		 * 用法3：使用SqlMapClient
		 * 
		 * 优点：自定义SQL，不受任何限制；SQL语句统一存储在配置文件里，便于DBA审查、管理
		 * 
		 * 适用场景：
		 *     1. Table Gateway无法实现的查询，尤其是复杂SELECT、子查询
		 *     2. 动态传入表名
		 */
		$smc = $db->getSqlMapClient();
		$this->assertEquals($smc->execute("getAgeTotal"), array(0 => array("age_total" => 1)));
	}
	/**
	 * 测试Mysql
	 */
	public function testMysql()
	{
		$host = array("password" => "123456", "dbname" => "test");
		foreach (array("mysql", "mysqli", "pdo_mysql") as $adapter)
		{
			$host["adapter"] = $adapter;
			/**
			 * 配置数据库连接信息
			 */
			$dcb = new LtDbConfigBuilder;
			$dcb->addSingleHost($host);
			LtDbStaticData::$servers = $dcb->getServers();

			/**
			 * 实例化组件入口类
			 */
			$db = new LtDb;
			$db->init();

			$dbh = $db->getDbHandle();
			foreach (array(
				//array("SQL语句", 参数,  正确结果)
				array("SELECT 'ok'", null, array(0 => array("ok" => "ok"))),
	
				array("INSERT INTO test_user VALUES (:id, :name, :age)", array("id" => 1, "name" => "lotus", "age" => 5), 1),
				array("UPDATE test_user SET age = :age", array("age" => 50), 1),
				array("SELECT * FROM test_user WHERE id = :id", array("id" => 1), array("0" => array("id" => 1, "name" => "lotus", "age" => 50))),
				array("DELETE FROM test_user", null, 1),
				array("SELECT * FROM test_user WHERE id = :id", array("id" => 1), null),
			) as $testData)
			{
				$this->assertEquals($testData[2], $dbh->query($testData[0], $testData[1]));
			}
		}
	}

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
