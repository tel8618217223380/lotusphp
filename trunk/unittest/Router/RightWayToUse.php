<?php
/**
 * 本测试文档演示了Router的正确使用方法 
 * 按本文档操作一定会得到正确的结果
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "common.inc.php";
class RightWayToUseRouter extends PHPUnit_Framework_TestCase
{
	/**
	 * 路由表说明
	 * $routingTable['pattern'] = 匹配模板 
	 * $routingTable['default'] = 默认值 
	 * $routingTable['reqs'] = 默认值的正则匹配 
	 * $routingTable['varprefix'] = 识别变量的前缀 
	 * $routingTable['delimiter'] = 分隔符 
	 * $routingTable['postfix'] = url后缀
	 * $routingTable['protocol'] = STANDARD REWRITE PATH_INFO
	 */
	public function testMostUsedWay()
	{ 
		// 模拟浏览器访问
		$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
		$_SERVER["PATH_INFO"] = '/news/list/catid/4/page/10'; 
		// 初始化LtRouter
		$router = new LtRouter;
		// 不初始化路由表则使用默认配置如下
		$router->routingTable = array('pattern' => ":module/:action/*",
			'default' => array('module' => 'default', 'action' => 'index'),
			'reqs' => array('module' => '[a-zA-Z0-9\.\-_]+', 'action' => '[a-zA-Z0-9\.\-_]+'),
			'varprefix' => ':',
			'delimiter' => '/',
			'postfix' => '',
			'protocol' => '',
			);
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
	// @todo 
	// 单元测试似乎有bug
	// 只要使用构造函数同时使用@dataProvider
	// 就会 Missing argument 1 错误
	// public function __construct()
	// {
	// parent::__construct();
	// $this->routingTable = array('pattern' => ":module/:action/*",
	// 'default' => array('module' => 'default', 'action' => 'index'),
	// 'reqs' => array('module' => '[a-zA-Z0-9\.\-_]+', 'action' => '[a-zA-Z0-9\.\-_]+'),
	// 'varprefix' => ':',
	// 'delimiter' => '/',
	// 'postfix' => '',
	// );
	// }
	/**
	 * ============================================================
	 * 下面是内部接口的测试用例,是给开发者保证质量用的,使用者可以不往下看
	 * ============================================================
	 */
	/**
	 * 测试路由正向反向解析
	 * $routingTable['pattern'] = 匹配模板 
	 * $routingTable['default'] = 默认值 
	 * $routingTable['reqs'] = 默认值的正则匹配 
	 * $routingTable['varprefix'] = 识别变量的前缀 
	 * $routingTable['delimiter'] = 分隔符 
	 * $routingTable['postfix'] = url后缀 
	 * 
	 * 
	 * 添加新的测试条请增加一个数组 
	 * array('url', params, routingTable)
	 */
	public static function matchDataProvider()
	{
		return array(
			array('news/list/catid/4/page/10',
				array('module' => 'news', 'action' => 'list', 'catid' => 4, 'page' => 10),
				array('pattern' => ":module/:action/*",
					'default' => array('module' => 'default', 'action' => 'index'),
					'reqs' => array('module' => '[a-zA-Z0-9\.\-_]+', 'action' => '[a-zA-Z0-9\.\-_]+'),
					'varprefix' => ':',
					'delimiter' => '/',
					'postfix' => '',
					'protocol' => '',
					),),
			array('news-list-catid-5-page-11.html',
				array('module' => 'news', 'action' => 'list', 'catid' => 5, 'page' => 11),
				array('pattern' => ":module-:action-*",
					'default' => array('module' => 'default', 'action' => 'index'),
					'reqs' => array('module' => '[a-zA-Z0-9\.\-_]+', 'action' => '[a-zA-Z0-9\.\-_]+'),
					'varprefix' => ':',
					'delimiter' => '-',
					'postfix' => '.html',
					'protocol' => '',
					),),
			array('default/index',
				array('module' => 'default', 'action' => 'index'),
				array('pattern' => ":module/:action/*",
					'default' => array('module' => 'default', 'action' => 'index'),
					'reqs' => array('module' => '[a-zA-Z0-9\.\-_]+', 'action' => '[a-zA-Z0-9\.\-_]+'),
					'varprefix' => ':',
					'delimiter' => '/',
					'postfix' => '',
					'protocol' => '',
					),),
			array('default-index.htm',
				array('module' => 'default', 'action' => 'index'),
				array('pattern' => ":module-:action-*",
					'default' => array('module' => 'default', 'action' => 'index'),
					'reqs' => array('module' => '[a-zA-Z0-9\.\-_]+', 'action' => '[a-zA-Z0-9\.\-_]+'),
					'varprefix' => ':',
					'delimiter' => '-',
					'postfix' => '.htm',
					'protocol' => '',
					),),
			// ADD other
			);
	}
	/**
	 * 正向解析url
	 * 
	 * @dataProvider matchDataProvider
	 */
	public function testMatch($userParameter, $expected, $routingTable)
	{
		$router = new LtRouter;
		$router->routingTable = $routingTable;
		$router->matchingRoutingTable($userParameter);
		$this->assertEquals($expected, $router->params);
	}

	/**
	 * 路由反向解析出url
	 * 
	 * @dataProvider matchDataProvider
	 */
	public function testReverseMatch($userParameter, $expected, $routingTable)
	{
		$router = new LtRouter;
		$router->routingTable = $routingTable;
		$this->assertEquals($userParameter, $router->url($expected));
	}
}