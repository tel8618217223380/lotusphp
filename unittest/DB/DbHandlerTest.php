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
	
	public function testMysql()
	{				
		LtDbStaticData::$servers = $this->dbConfigBuilder->getServers();
		$this->dbh = new LtDbHandler();
		$this->assertEquals($this->dbh->query("USE mysql"), 0);
	}

	public function tearDown()
	{
		
	}
}