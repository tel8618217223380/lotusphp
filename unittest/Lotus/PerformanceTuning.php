<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "common.inc.php";
class PerformanceTuningLotus extends PHPUnit_Framework_TestCase
{
	public function testPerformance()
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
		$lotus->option['app_tmp'] = '/tmp/Lotus/unittest/lotus-appname2';

		/**
		 * 应用名称对项目目录下的子目录名称
		 */
		$lotus->option['app_name'] = 'app_name2';
		/**
		 * 禁止加载函数文件, 防止测试过程中函数冲突
		 */
		$lotus->option['load_function'] = false;

		/**
		 * 生产环境下禁用可以提升性能
		 */ 
		// $lotus->option['view_tpl_auto_compile'] = false;
		/**
		 * 默认MVC模式 true
		 */
		$lotus->mvcMode = true;
		/**
		 * 默认开发模式 false
		 * 关闭开发模式会在临时目录缓存autoloader和config来提升性能
		 */
		$lotus->devMode = false;

		/**
		 * 开始工作
		 */
		$lotus->init();

		/**
		 * class_exists默认调用自动加载
		 */
		$this->asserttrue(class_exists("LtCaptcha"));

		/**
		 * 运行100次，要求在1秒内运行完
		 */
		$base_memory_usage = memory_get_usage();
		$times = 100;
		$startTime = microtime(true);

		for($i = 0; $i < $times; $i++)
		{
			$lotus->init();
		}

		$endTime = microtime(true);
		$totalTime = round(($endTime - $startTime), 6);
		$averageTime = round(($totalTime / $times), 6);

		$memory_usage = memory_get_usage() - $base_memory_usage;
		$averageMemory = formatSize($memory_usage / $times);
		$memory_usage = formatSize($memory_usage);

		echo "\n---------------------Lotus------------------------------\n";
		echo "times      \t$times\n";
		echo "totalTime   \t{$totalTime}s\taverageTime   \t{$averageTime}s\n";
		echo "memoryUsage \t{$memory_usage}\taverageMemory \t{$averageMemory}";
		echo "\n---------------------------------------------------------\n";
		$this->assertTrue(1 > $totalTime);
	}

	protected function setUp()
	{
		$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
		$_SERVER['PATH_INFO'] = '/Index/Index';
	}

	protected function tearDown()
	{
	}
}
