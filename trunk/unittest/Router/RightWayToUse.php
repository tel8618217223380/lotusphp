<?php
/**
 * 本测试文档演示了Router的正确使用方法 
 * 按本文档操作一定会得到正确的结果
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "common.inc.php";
class RightWayToUseRouter extends PHPUnit_Framework_TestCase
{
	/**
	 * 使用方法
	 */
	public function testMostUsedWay()
	{ 
		// 模拟浏览器访问
		$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
		$_SERVER["PATH_INFO"] = '/news/list/catid/4/page/10'; 
		// 初始化LtRouter
		$router = new LtRouter;
		$router->routingTable = $this->routingTable;
		$router->init(); 
		// 初始化结束
		$this->assertEquals(
			array('module' => 'news', 'action' => 'list', 'catid' => 4, 'page' => 10),
			$router->params
			);
		$url = $router->url(array('module' => 'news', 'action' => 'list', 'catid' => 4, 'page' => 10));
		$this->assertEquals('news/list/catid/4/page/10', $url);
	}
	/**
	 * index.php?module=hello&action=world
	 */
	public function testNormal()
	{
		$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
		$_GET['module'] = 'hello';
		$_GET['action'] = 'world';
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
		$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
		$router = new LtRouter;
		$router->init();
		$this->assertEquals('default', $router->module);
		$this->assertEquals('index', $router->action);
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
	 * 准备路由表供测试
	 */
	public function __construct()
	{
		parent::__construct();
		$this->routingTable = array('pattern' => ":module/:action/*",
			'default' => array('module' => 'default', 'action' => 'index'),
			'reqs' => array('module' => '[a-zA-Z0-9\.\-_]+', 'action' => '[a-zA-Z0-9\.\-_]+'),
			'varprefix' => ':',
			'delimiter' => '/',
			'postfix' => '',
			);
	}
	/**
	 * ============================================================
	 * 下面是内部接口的测试用例,是给开发者保证质量用的,使用者可以不往下看
	 * ============================================================
	 */
	/**
	 * 测试路由正向解析出变量
	 */
	public function matchDataProvider()
	{
		return array(
			array(
				'news/list/catid/4/page/10',
				array('module' => 'news', 'action' => 'list', 'catid' => 4, 'page' => 10),
				array('pattern' => ":module/:action/*",
					'default' => array('module' => 'default', 'action' => 'index'),
					'reqs' => array('module' => '[a-zA-Z0-9\.\-_]+', 'action' => '[a-zA-Z0-9\.\-_]+'),
					'varprefix' => ':',
					'delimiter' => '/',
					'postfix' => '',
					),),
			array(
				'news/list/catid/5/page/11',
				array('module' => 'news', 'action' => 'list', 'catid' => 5, 'page' => 11),
				array('pattern' => ":module/:action/*",
					'default' => array('module' => 'default', 'action' => 'index'),
					'reqs' => array('module' => '[a-zA-Z0-9\.\-_]+', 'action' => '[a-zA-Z0-9\.\-_]+'),
					'varprefix' => ':',
					'delimiter' => '/',
					'postfix' => '',
					),),
			);
	}
	/**
	 * 
	 * @dataProvider matchDataProvider
	 */
	public function testmatch($userParameter, $expected, $routingTable)
	{
		$router = new LtRouter;
		$router->routingTable = $routingTable;
		$router->matchingRoutingTable($userParameter);
		$this->assertEquals($expected, $router->params); 
		// $url = $router->url(array('module'=>'news','action'=>'list','catid'=>4,'page'=>10));
		// $this->assertEquals('news/list/catid/4/page/10', $url);
	}
}
