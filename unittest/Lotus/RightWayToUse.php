<?php
/**
 * 本测试文档演示了Lotus的正确使用方法
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "common.inc.php";
class RightWayToUseLotus extends PHPUnit_Framework_TestCase
{
	/**
	 * 最常用的使用方式
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
		 * 开发模式下的Autoloader 和 MVC的模板引擎
		 */
		$lotus->option['app_tmp'] = '/tmp/Lotus/unittest/lotus-appname1/';

		/**
		 * 应用名称对项目目录下的子目录名称
		 */
		$lotus->option['app_name'] = 'app_name1';

		/**
		 * 是否自动加载函数文件, 默认为AutoloaderConfig.php的设置
		 */
		$lotus->option['load_function'] = true;

		/**
		 * 默认使用MVC
		 * $lotus->mvcMode = true;
		 */

		/**
		 * 使用cache可以提升性能
		 */
		//$lotus->option["app_cache"] = array("adapter" => "phps", "host" => "/tmp/Lotus/unittest/lotus-appname1/cache/");

		/**
		 * run
		 */
		$lotus->init();

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
