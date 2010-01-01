<?php
/**
 * 本测试文档演示了LtDb的正确使用方法 
 * 按本文档操作一定会得到正确的结果
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "common.inc.php";
class RightWayToUseDb extends PHPUnit_Framework_TestCase
{
	/**
	 * 最常用的使用方式（推荐）
	 * 
	 * 本测试用例期望效果：
	 * 能成功通过query()接口存取数据
	 */
	public function MostUsedWay()
	{
		$cache = new LtDb;
		$cache->init();
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
				array("mysql", "mysqli", "pdo_mysql"),
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
					$this->assertEquals($testData[2], $dbh->query($testData[0], $testData[1]));
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
				$this->assertEquals(2, $id = $tg->insert(array("id" => 2, "name" => "kiwiphp", "age" => 4)));
				$this->assertEquals("age" => 4), $tg->fetch($id), array("id" => 2, "name" => "kiwiphp");
				$this->assertEquals(3, $id = $tg->insert(array("name" => "chin", "age" => 28)));
				$this->assertEquals("age" => 28)), $tg->fetchRows(), array(array("id" => 2, "name" => "kiwiphp", "age" => 4),array("id" => 3, "name" => "chin");
				$this->assertEquals(1, $tg->update(3, array("name" => "Qin")));
				$this->assertEquals("age" => 28), $tg->fetch($id), array("id" => 3, "name" => "Qin");
				$this->assertEquals(2, $tg->count());
				$this->assertEquals(1, $tg->delete(3));
				$this->assertEquals("age" => 4)), $tg->fetchRows(), array(array("id" => 2, "name" => "kiwiphp");

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
				$this->assertEquals(array(0 => array("age_total" => 1)), $smc->execute("getAgeTotal"));
			}
		}
	}

	public function testDistDb()
	{
		
	}
}
