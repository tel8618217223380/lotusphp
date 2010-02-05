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
		 * 开发模式下的Autoloader 和 MVC的模板引擎 及 文件类型Cache
		 */
		$lotus->option['tmp_dir'] = '/tmp/Lotus/unittest/lotus/';

		/**
		 * 应用名称对项目目录下的子目录名称
		 */
		$lotus->option['app_name'] = 'app_name1';
		/**
		 * 是否自动加载函数文件, 默认为AutoloaderConfig.php的设置
		 */
		$lotus->option['is_load_function'] = false;

		/**
		 * 是否使用MVC
		 */
		$lotus->mvcMode = false;
		/**
		 * 是否显示调试信息
		 */
		$lotus->debug = false;

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
		//$lotus->option["cache_server"] = array("adapter" => "phps", "host" => "/tmp/Lotus/unittest/lotus/");
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

		/**
		 * 运行1000次，要求在1秒内运行完
		 */
		$base_memory_usage = memory_get_usage();
		$times = 1000;
		$startTime = microtime(true);

		for($i = 0; $i < $times; $i++)
		{
			$lotus->init();
		}

		$endTime = microtime(true);
		$totalTime = round(($endTime - $startTime), 6);
		$averageTime = round(($totalTime / $times), 6);

		$memory_usage = memory_get_usage() - $base_memory_usage;
		$memory_usage = ($memory_usage >= 1048576) ? round((round($memory_usage / 1048576 * 100) / 100), 2) . 'MB' : (($memory_usage >= 1024) ? round((round($memory_usage / 1024 * 100) / 100), 2) . 'KB' : $memory_usage . 'BYTES');

		$averageMemory = round(($memory_usage / $times), 2);
		$averageMemory = ($averageMemory >= 1048576) ? round((round($averageMemory / 1048576 * 100) / 100), 2) . 'MB' : (($averageMemory >= 1024) ? round((round($averageMemory / 1024 * 100) / 100), 2) . 'KB' : $averageMemory . 'BYTES');

		echo "\n---------------------Lotus------------------------------\n";
		echo "times      \t$times\n";
		echo "totalTime   \t{$totalTime}s\taverageTime   \t{$averageTime}s\n";
		echo "memoryUsage \t{$memory_usage}\taverageMemory \t{$averageMemory}";
		echo "\n---------------------------------------------------------\n";
		$this->assertTrue(1 > $totalTime);
	}
	protected function setUp()
	{
	}
	protected function tearDown()
	{
	}
}
