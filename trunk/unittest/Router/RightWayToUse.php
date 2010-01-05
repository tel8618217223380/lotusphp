<?php
/**
 * 本测试文档演示了Router的正确使用方法 
 * 按本文档操作一定会得到正确的结果
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "common.inc.php";
class RightWayToUseRouter extends PHPUnit_Framework_TestCase
{

	public function testMostUsedWay()
	{
		$router = new LtRouter;
		$router->routingTable = $this->routingTable;
		$router->matchingRoutingTable('news/list/catid/4/page/10');
		$this->assertEquals(
			array('module'=>'news','action'=>'list','catid'=>4,'page'=>10), 
			$router->params
			);

		$url = $router->url(array('module'=>'news','action'=>'list','catid'=>4,'page'=>10));
		$this->assertEquals('news/list/catid/4/page/10', $url);

	}
	public function __construct()
	{
		parent::__construct();
		$this->routingTable = array('pattern' => ":module/:action/*",
		'default' => array('module' => 'default', 'action' => 'index'),
		'reqs' => array('module' => '[a-zA-Z0-9\.\-_]+', 'action' => '[a-zA-Z0-9\.\-_]+'),
		'varprefix' => ':',
		'delimiter' => '/'
		);

	}
	/**
	 * 命令行模式
	 */
	public function testCLI()
	{
		$_SERVER['argv'] = array('--module', 'hello', '--action', 'world',);
		$router = new LtRouter;
		$router->init();
		$this->assertEquals('hello', $router->module);
		$this->assertEquals('world', $router->action);
	}
	public function testCLI2()
	{
		$_SERVER['argv'] = array('-m', 'hello', '-a', 'world',);
		$router = new LtRouter;
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
		$router = new LtRouter;
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
		$router = new LtRouter;
		$router->init();
		$this->assertEquals('hello', $router->module);
		$this->assertEquals('world', $router->action);
	}
	/**
	 * index.php
	 */
	public function testDefault()
	{
		$router = new LtRouter;
		$router->init();
		$this->assertEquals('Module', $router->module);
		$this->assertEquals('Action', $router->action);
	}

	/**
	 * 
	 * @expectedException Exception
	 */
	public function testModuleErr()
	{
		$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
		$_REQUEST = array('module' => 'hel?lo',
			'action' => 'world',
			);
		$router = new LtRouter;
		$router->init();
	}

	/**
	 * 
	 * @expectedException Exception
	 */
	public function testActionErr()
	{
		$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
		$_REQUEST = array('module' => 'hello',
			'action' => 'wor/ld',
			);
		$router = new LtRouter;
		$router->init();
	}
}
