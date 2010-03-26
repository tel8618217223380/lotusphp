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
		$lotus->option['app_tmp'] = '/tmp/Lotus/unittest/lotus/';

		/**
		 * 应用名称对项目目录下的子目录名称
		 */
		$lotus->option['app_name'] = 'app_name2';
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
		$lotus->option["app_cache"] = array("adapter" => "phps", "host" => "/tmp/Lotus/unittest/lotus/");
		/**
		开始工作
		*/
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
		$averageMemory = $this->size($memory_usage / $times);
		$memory_usage = $this->size($memory_usage);

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
		$_SERVER['PATH_INFO'] = '/Default/Index';
	}

	protected function tearDown()
	{
	}

	private function size($size)
	{
		if ($size >= 1073741824)
		{
			$size = round($size / 1073741824, 2) . ' GB';
		}
		else if ($size >= 1048576)
		{
			$size = round($size / 1048576, 2) . ' MB';
		}
		else if ($size >= 1024)
		{
			$size = round($size / 1024, 2) . ' KB';
		}
		else
		{
			$size = round($size, 2) . ' Bytes';
		}
		return $size;
	}
}
