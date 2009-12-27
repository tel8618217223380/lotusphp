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
					"password" => "123456",
					"dbname" => "mysql",
				),
				array("mysql", "mysqli", "pdo_mysql"),
			),
		);
		$this->testDataList = array(
			//array("SQL语句", 正确结果)
			array("USE test", 0),
			array("SELECT DATABASE()", array(array("DATABASE()" => "test"))),
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
				$this->assertEquals($dbh->query($testData[0]), $testData[1]);
			}
		}
	}

	public function getDbHandle($conf)
	{
		$db = new LtDb;
		foreach ($conf as $k => $v)
		{
			$db->conf->conn[$k] = $v;
		}
		$db->init();
		return $db;
	}
}
