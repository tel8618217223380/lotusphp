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
		// ---------------------------------
		// 下面是 必填项
		// ---------------------------------
		/**
		 * 项目目录, 按照约定的目录结构,自动加载配置文件,自动加载类
		 */
		$lotus->option['proj_dir'] = dirname(__FILE__) . '/proj_dir/';
		/**
		 * 应用名称对项目目录下的子目录名称
		 */
		$lotus->option['app_name'] = 'app_name1';
		// ---------------------------------
		// 下面是 可选项
		// ---------------------------------
		/**
		 * 临时目录,默认是proj_dir/tmp/
		 * 缓存autoloader config , 保存MVC的模板编译后的文件
		 */
		$lotus->option['app_tmp'] = '/tmp/Lotus/unittest/lotus-appname1/';
		/**
		 * 是否自动加载函数文件, 默认 true
		 */
		//$lotus->option['load_function'] = true;
		/**
		 * 配置LtAutoloader组件是否将runtime目录的类文件映射保存到局部变量内,
		 * 启用后Autoloader的自动加载方法先查找局部变量,然后再到storeHandle查找.
		 * 
		 * 这是可选的, 如不需要, 请不要设置,
		 * Lotus.php内使用isset($this->option["runtime_filemap"])判断
		 */
		//$lotus->option['runtime_filemap'] = '';
		/**
		 * MVC模式 默认 true
		 */
		//$lotus->mvcMode = true;
		/**
		 * 开发模式, 默认 false 即默认为生产环境
		 */
		$lotus->devMode = true;
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
		LtObjectUtil::$instances = array();
	}
}
