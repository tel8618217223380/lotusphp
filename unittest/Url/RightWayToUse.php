<?php
/**
 * 本测试文档演示了Url的正确使用方法 
 * 按本文档操作一定会得到正确的结果
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "common.inc.php";
class RightWayToUseUrl extends PHPUnit_Framework_TestCase
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
		// 初始化LtUrl
		$url = new LtUrl;
		// 不初始化路由表则使用默认配置如下
		$url->routingTable = array('pattern' => ":module/:action/*",
			'default' => array('module' => 'default', 'action' => 'index'),
			'reqs' => array('module' => '[a-zA-Z0-9\.\-_]+', 'action' => '[a-zA-Z0-9\.\-_]+'),
			'varprefix' => ':',
			'delimiter' => '/',
			'postfix' => '',
			'protocol' => '',
			);
		$url->init(); 
		// 初始化结束
		// 测试生成超链接
		$href = $url->generate('news', 'list', array('catid' => 4, 'page' => 10));
		$this->assertEquals('news/list/catid/4/page/10', $href);
	}

	/**
	 * ============================================================
	 * 下面是内部接口的测试用例,是给开发者保证质量用的,使用者可以不往下看
	 * ============================================================
	 */
	/**
	 * 测试解析路由表
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
	 * 路由反向解析出url
	 * 
	 * @dataProvider matchDataProvider
	 */
	public function testReverseMatch($userParameter, $expected, $routingTable)
	{
		$url = new LtUrl;
		$url->routingTable = $routingTable;
		$this->assertEquals($userParameter, $url->reverseMatchingRoutingTable($expected));
	}
	protected function setUp()
	{
	}
	protected function tearDown()
	{
	}
}
