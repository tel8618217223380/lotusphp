<?php
/**
 * 本测试文档演示了LtDb的正确使用方法 
 * 按本文档操作一定会得到正确的结果
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "include_classes.inc";
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
				 */
				$dbh = $db->getDbHandle();
				foreach($this->testDataList as $testData)
				{
					$this->assertEquals($dbh->query($testData[0], $testData[1]), $testData[2]);
				}

				/**
				 * 用法 2： 使用Table Gateway查询引擎
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
				 */
				$smc = $db->getSqlMapClient();
				$this->assertEquals($smc->execute("getAgeTotal"), array(0 => array("age_total" => 1)));
			}
		}
	}

	public function testDistDb()
	{
		
	}
}
