<?php
/**
 * 本测试文档演示了LtDb的正确使用方法 
 * 按本文档操作一定会得到正确的结果
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "common.inc.php";
class RightWayToUseDb extends PHPUnit_Framework_TestCase
{
	public function testConfigBuilder()
	{
		
	}

	public function __construct()
	{
		parent::__construct();
		$this->confList = array(
			"mysql" => array(
				array(
					"password"       => "123456",
					"dbname"         => "test",
				),
				array(
					"mysql",
					//"mysqli",
					//"pdo_mysql"
				),
			),
		);
		$this->testDataList = array(
			//array("SQL语句", 参数,  正确结果)
			array("DROP TABLE IF EXISTS test_user", null, true),
			//array("USE test", null, true),不再支持通过query()执行USE DATABASE和SET NAMES
			array("CREATE TABLE test_user (
					id INT NOT NULL ,
					name VARCHAR( 20 ) NOT NULL ,
					age INT NOT NULL ,
					PRIMARY KEY ( id ) 
			)", null, true),
			array("ALTER TABLE test_user CHANGE id id INT( 11 ) NOT NULL AUTO_INCREMENT", null, true),
			array("INSERT INTO test_user VALUES (:id, :name, :age)", array("id" => 1, "name" => "lotus", "age" => 5), 1),
			array("UPDATE test_user SET age = :age", array("age" => 50), 1),
			array("SELECT * FROM test_user WHERE id = :id", array("id" => 1), array("0" => array("id" => 1, "name" => "lotus", "age" => 50))),
			array("DELETE FROM test_user", null, 1),
			array("SELECT * FROM test_user WHERE id = :id", array("id" => 1), null),
		);
	}

	/**
	 * 基本功能测试
	 * 单机单库，适合只有一个数据库的小型应用
	 */
	public function testMostUsedWay()
	{
		foreach ($this->confList as $conf)
		{
			foreach ($conf[1] as $ext)
			{
				$conf[0]["adapter"] = $ext;
				/**
				 * 配置数据库连接信息
				 */
				$dcb = new LtDbConfigBuilder;
				$dcb->addSingleHost($conf[0]);
				LtDbStaticData::$servers = $dcb->getServers();

				/**
				 * 实例化组件入口类
				 */
				$db = new LtDb;
				$db->init();

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
		}
	}

	/**
	 * 分布式数据库操作测试
	 * 本例演示了垂直切分（多个Group）和水平切分（一个Group下多个节点）
	 * 由于实际测试环境的限制，本例中不同的Group和Node只用dbnamw来区分，只有一个host
	 *    Group 1：系统数据组，存储系统数据，因为数据较少，只包含一个节点
	 *        Node 1：dbname=sys_data
	 *    Group 2: 用户数据组，存储用户生产的数据，因为数据量可能会大，包含两个节点
	 *        Node 1：dbname=member_1
	 *        Node 2：dbname=member_2
	 */
	public function DistDb()
	{
		foreach ($this->confList as $conf)
		{
			foreach ($conf[1] as $ext)
			{
				$conf[0]["adapter"] = $ext;
				$hostConfig = $conf[0];
				$dcb = new LtDbConfigBuilder;
				//配置系统数据组
				$dcb->addGroup("sys_group");
				$dcb->addNode("sys_node_1", "sys_group");
				$hostConfig["dbname"] = "sys_data";
				$dcb->addHost($hostConfig, "master", "sys_node_1", "sys_group");
				//配置用户数据组				
				$dcb->addGroup("user_group");
				$dcb->addNode("user_node_1", "user_group");
				$hostConfig["dbname"] = "member_1";
				$dcb->addHost($hostConfig, "master", "user_node_1", "user_group");
				$dcb->addNode("user_node_2", "user_group");
				$hostConfig["dbname"] = "member_2";
				$dcb->addHost($hostConfig, "master", "user_node_2", "user_group");

				LtDbStaticData::$servers = $dcb->getServers();

				/**
				 * LtDb的第一个实例
				 */
				$db1 = new LtDb;
				$db1->group = "sys_group";
				$db1->init();

				//用DbHandle直接操作数据库
				$dbh1 = $db1->getDbHandle();
				foreach($this->testDataList as $testData)
				{
					$this->assertEquals($dbh1->query("CREATE TABLE sys_category (
					id INT NOT NULL auto_increment,
					name VARCHAR( 20 ) NOT NULL ,
					PRIMARY KEY ( id ) 
					)"), true);
				}

				//使用Table Gateway查询引擎
				$tg1 = $db1->getTableGateway("sys_category");
				$this->assertEquals($id = $tg1->insert(array("id" => 2, "name" => "PHP")), 1);
				$this->assertEquals($tg1->fetch($id), array("id" => 1, "name" => "PHP"));

				//使用SqlMapClient
				$smc1 = $db1->getSqlMapClient();
				$this->assertEquals($smc1->execute("sys.getSysCateTotal"), array(0 => array("age_total" => 2)));
			}
		}
	}
}
