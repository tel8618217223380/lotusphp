<?php
class DbHandlerTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->dbConfigBuilder = new LtDbConfigBuilder();
		$this->dbConfigBuilder->addSingleHost(array(
			"host" => "localhost",
			"port" => "3306",
			"username" => "root",
			"password" => "123456",
			"dbname" => "test",
			"adapter" => "mysql",
			"charset" => "UTF-8",
			"connection_ttl" => 10,
		));
	}

	/**
	 * 测试"USE 数据库名"的返回值
	 * 期望返回：0
	 * 解释：dbh->query("USE mysql")执行成功，返回受影响的行数，USE mysql的影响行数为0
	 */
	public function testMysqlUseDbReturn()
	{				
		LtDbStaticData::$servers = $this->dbConfigBuilder->getServers();
		$this->dbh = new LtDbHandler();
		$this->dbh->init();
		$this->assertEquals($this->dbh->query("USE mysql"), 0);
	}

	/**
	 * 测试"USE 数据库名"的执行效果
	 * 期望效果：执行dbh->query("USE mysql")后，当前默认数据库是mysql
	 */
	public function testMysqlUseDbExecute()
	{				
		LtDbStaticData::$servers = $this->dbConfigBuilder->getServers();
		$this->dbh = new LtDbHandler();
		$this->dbh->init();
		$this->dbh->query("USE mysql");
		$row = mysql_fetch_row(mysql_query("SELECT DATABASE()"));
		$this->assertEquals($row[0], "mysql");
	}

	public function tearDown()
	{
		
	}
}