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
	 */
	public function testBase()
	{		
		foreach ($this->confList as $conf)
		{
			foreach ($conf[1] as $ext)
			{
				$conf[0]["adapter"] = $ext;
				$dbh = $this->getDbHandle($conf[0]);
				foreach($this->testDataList as $testData)
				{
					$this->assertEquals($dbh->query($testData[0], $testData[1]), $testData[2]);
				}
			}
		}
	}

	public function getDbHandle($conf)
	{
		$dcb = new LtDbConfigBuilder;
		$dcb->addSingleHost($conf);
		LtDbStaticData::$servers = $dcb->getServers();

		$db = new LtDb;
		$db->init();
		return $db->dbh;
	}
}
