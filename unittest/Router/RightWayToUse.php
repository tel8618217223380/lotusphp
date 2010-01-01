<?php
/**
 * 本测试文档演示了Router的正确使用方法 
 * 按本文档操作一定会得到正确的结果
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "common.inc.php";
class RightWayToUseRouter extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
	} 
	public function tearDown()
	{
	} 
	/**
	 * 命令行模式
	 */
	public function testCLI()
	{
		$_SERVER['argv'] = array('--module', 'hello', '--action', 'world',);
		$router = new LtRouter();
		$router->init();
		$this->assertEquals('hello', $router->module);
		$this->assertEquals('world', $router->action);
	} 
	public function testCLI2()
	{
		$_SERVER['argv'] = array('-m', 'hello', '-a', 'world',);
		$router = new LtRouter();
		$router->init();
		$this->assertEquals('hello', $router->module);
		$this->assertEquals('world', $router->action);
	} 
	/**
	 * index.php?module=hello&action=world
	 */
	public function testNormal()
	{
		$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
		$_REQUEST['module'] = 'hello';
		$_REQUEST['action'] = 'world';
		$router = new LtRouter();
		$router->init();
		$this->assertEquals('hello', $router->module);
		$this->assertEquals('world', $router->action);
	} 
	/**
	 * index.php/hello/world
	 */
	public function testPathinfo()
	{
		$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
		$_SERVER["PATH_INFO"] = '/hello/world';
		$router = new LtRouter();
		$router->init();
		$this->assertEquals('hello', $router->module);
		$this->assertEquals('world', $router->action);
	} 
	/**
	 * index.php
	 */
	public function testDefault()
	{
		$router = new LtRouter();
		$router->init();
		$this->assertEquals('Module', $router->module);
		$this->assertEquals('Action', $router->action);
	} 

	/**
	 * index.php
	 */
	public function testCustom()
	{
		$router = new LtRouter();
		$router->conf->module = 'hello';
		$router->conf->action = 'world';
		$router->init();
		$this->assertEquals('hello', $router->module);
		$this->assertEquals('world', $router->action);
	} 

	/**
	 * @expectedException Exception
	 */
	public function testModuleErr()
	{
		$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
		$_REQUEST = array(
			'module' => 'hel?lo',
			'action' => 'world',
		);
		$router = new LtRouter();
		$router->init();
	}

	/**
	 * @expectedException Exception
	 */
	public function testActionErr()
	{
		$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
		$_REQUEST = array(
			'module' => 'hello',
			'action' => 'wor/ld',
		);
		$router = new LtRouter();
		$router->init();
	}
} 
