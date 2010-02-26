<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "common.inc.php";

class PerformanceTuningMVC extends PHPUnit_Framework_TestCase
{
	/**
	 * 模板编译性能测试
	 */
	public function testPerformance()
	{
		/**
		 * 加载Action类文件
		 */
		$appDir = dirname(__FILE__) . "/test_data/simplest_app";
		require_once "$appDir/action/User-Add.Action.php";
		require_once "$appDir/action/stock-Price.Component.php";
		/**
		 * 实例化
		 */
		$dispatcher = new LtDispatcher;
		$dispatcher->viewDir = "$appDir/view/";
		$dispatcher->viewTplDir = "/tmp/Lotus/unittest/MVC/";
		ob_start();
		$dispatcher->dispatchAction("User", "Add");
		ob_end_clean();
		touch($dispatcher->viewDir . "User-Add.view.php"); 
		unlink($dispatcher->viewTplDir . "layout/top_navigator@User-Add.view.php");

		/**
		 * 运行100次，要求在1秒内运行完
		 */
		$base_memory_usage = memory_get_usage();
		$times = 100;
		$startTime = microtime(true);
		for($i = 0; $i < $times; $i++)
		{
			ob_start();
			$dispatcher->dispatchAction("User", "Add");
			ob_end_clean();
			touch($dispatcher->viewDir . "User-Add.view.php"); 
		}
		$endTime = microtime(true);
		$totalTime = round(($endTime - $startTime), 6);
		$averageTime = round(($totalTime / $times), 6);

		$memory_usage = memory_get_usage() - $base_memory_usage;
		$averageMemory = $this->size($memory_usage / $times);
		$memory_usage = $this->size($memory_usage);

		echo "\n------------------MVC Template View----------------------\n";
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
