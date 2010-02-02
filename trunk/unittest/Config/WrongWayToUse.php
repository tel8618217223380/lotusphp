<?php
/**
 * 本测试文档演示了LtConfig的错误使用方法
 * 不要按本文档描述的方式使用LtConfig
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "common.inc.php";
class WrongWayToUseConfig extends PHPUnit_Framework_TestCase
{
	/**
	 * 使用config file中未定义的key
	 * 
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testNotExistKey()
	{
		$conf = new LtConfig;
		$conf->configFile = dirname(__FILE__) . "/test_data/conf.php";
		$conf->init();
		$conf->get("Does_not_define_a_variable");
	}
	/**
	 * config file中没有return array
	 * 
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testNotReturnArray()
	{
		$conf = new LtConfig;
		$conf->configFile = dirname(__FILE__) . "/test_data/conf_err.php";
		$conf->init();
	}
	/**
	 * config file不存在
	 * 
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testNotExistsConfigFile()
	{
		$conf = new LtConfig;
		$conf->configFile = dirname(__FILE__) . "/test_data/conf_not_exists.php";
		$conf->init();
	}
	protected function setUp()
	{
		LtConfig::$storeHandle = null;
	}
	protected function tearDown()
	{
	}
}
