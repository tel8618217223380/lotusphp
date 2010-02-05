<?php
/**
 * 本测试文档演示了Lotus的正确使用方法 
 * 按本文档操作一定会得到正确的结果
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "common.inc.php";
class RightWayToUseLotus extends PHPUnit_Framework_TestCase
{
	/**
	 * 最常用的使用方式（推荐）

	 * 本测试用例期望效果：
	 * 能成功通过query()接口存取数据
	 */
	public function testMostUsedWay()
	{
		/**
		 * 初始化Lotus类
		 */
		$lotus = new Lotus();
		/**
		 * 项目目录, 按照约定的目录结构,自动加载配置文件,自动加载类
		 */
		$lotus->option['proj_dir'] = dirname(__FILE__) . '/proj_dir/';
		/**
		 * 临时目录,默认是proj_dir/tmp/
		 * 开发模式下的Autoloader 和 MVC的模板引擎 及 文件类型Cache
		 */
		$lotus->option['tmp_dir'] = '/tmp/Lotus/unittest/lotus/';

		/**
		 * 应用名称对项目目录下的子目录名称
		 */
		$lotus->option['app_name'] = 'app_name1';
		/**
		 * 是否使用MVC
		 */
		$lotus->mvcMode = true;
		/**
		 * 是否显示调试信息
		 */
		$lotus->debug = true;

		/**
		 * devMode的默认值是true，即默认处于开发模式 
		 * devMode等于false的时候（如生产环境，测试环境），性能会有提高 
		 * $lotus->devMode = false;
		 * 当指定cache后,自动设置生产模式
		 * 没有指定cache，自动设置开发模式
		 */
		/**
		 * 使用cache可以提升性能
		 */ 
		// $lotus->option["cache_server"] = array("adapter" => "phps", "host" => "/tmp/Lotus/unittest/lotus/");
		$lotus->init();

		/**
		 * 显示调试信息
		 */
		if ($lotus->debug)
		{
			echo "<!--totalTime: {$lotus->debugInfo['totalTime']}s  memoryUsage: {$lotus->debugInfo['memoryUsage']} devMode: {$lotus->debugInfo['devMode']}-->";
		}

		/**
		 * class_exists默认调用自动加载
		 */
		$this->asserttrue(class_exists("LtCaptcha"));
	}
	protected function setUp()
	{
		$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1'; 
		// $_GET['module'] = 'Default';
		// $_GET['action'] = 'Index';
		$_SERVER['PATH_INFO'] = '/Default/Index';
	}
	protected function tearDown()
	{
	}
}
