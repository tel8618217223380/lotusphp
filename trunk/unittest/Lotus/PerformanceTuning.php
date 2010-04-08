<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "common.inc.php";
class PerformanceTuningLotus extends PHPUnit_Framework_TestCase
{
	public function testPerformance()
	{
		/**
		 * 注意: 修改配置后请手工删除临时目录文件
		 * 
		 * 
		 * 项目目录, 按照约定的目录结构,自动加载配置文件,自动加载类
		 */
		$option['proj_dir'] = dirname(__FILE__) . '/proj_dir/';
		/**
		 * 临时目录,默认是proj_dir/tmp/
		 * 开发模式下的Autoloader 和 MVC的模板引擎
		 */
		$option['app_tmp'] = '/tmp/Lotus/unittest/lotus-appname2';

		/**
		 * 应用名称对项目目录下的子目录名称
		 */
		$option['app_name'] = 'app_name2';
		/**
		 * 禁止加载函数文件, 防止测试过程中函数冲突
		 */
		$option['load_function'] = false;
		/**
		 * 配置LtAutoloader组件是否将runtime目录的类文件映射保存到局部变量内,
		 * 启用后Autoloader的自动加载方法先查找局部变量,然后再到storeHandle查找.
		 * 
		 * 这是可选的, 如不需要, 请不要设置,
		 * Lotus.php内使用isset($this->option["runtime_filemap"])判断
		 * 
		 * 使用后初始化时间会稍大,内存占用会稍高,
		 * 减少的是创建类实例时每个文件读一次取文件路径的时间.
		 */ 
		// $option['runtime_filemap'] = true;
		/**
		 * 初始化Lotus类
		 */
		$lotus = new Lotus();
		$lotus->option = $option;
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
			$lotus = new Lotus();
			$lotus->option = $option;
			$lotus->init();
			unset($lotus);
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
