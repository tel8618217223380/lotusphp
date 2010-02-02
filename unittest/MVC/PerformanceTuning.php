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
		require_once "$appDir/action/UserAddAction.php";
		require_once "$appDir/action/stockPriceComponent.php";
		/**
		 * 实例化
		 */
		$dispatcher = new LtDispatcher;
		$dispatcher->viewDir = "$appDir/view/";
		$dispatcher->viewTplDir = "/tmp/LtTemplateView/test/";
		ob_start();
		$dispatcher->dispatchAction("User", "Add");
		ob_clean();
		touch("$appDir/view/User_Add.php"); 
		//unlink("$appDir/viewTpl/layout/top_navigator-User_Add.php");

		/**
		 * 运行1000次，要求在10秒内运行完
		 * @todo 解决高并发情况下的文件写出错
		 */
		$base_memory_usage = memory_get_usage();
		$times = 1000;
		$startTime = microtime(true);
		for($i = 0; $i < $times; $i++)
		{
			ob_start();
			$dispatcher->dispatchAction("User", "Add");
			ob_clean();
			touch("$appDir/view/User_Add.php"); 
			// unlink("$appDir/viewTpl/layout/top_navigator-User_Add.php");
		}
		$endTime = microtime(true);
		$totalTime = round(($endTime - $startTime), 6);
		$averageTime = round(($totalTime / $times), 6);

		$memory_usage = memory_get_usage() - $base_memory_usage;
		$memory_usage = ($memory_usage >= 1048576) ? round((round($memory_usage / 1048576 * 100) / 100), 2) . 'MB' : (($memory_usage >= 1024) ? round((round($memory_usage / 1024 * 100) / 100), 2) . 'KB' : $memory_usage . 'BYTES');

		$averageMemory = round(($memory_usage / $times), 2);
		$averageMemory = ($averageMemory >= 1048576) ? round((round($averageMemory / 1048576 * 100) / 100), 2) . 'MB' : (($averageMemory >= 1024) ? round((round($averageMemory / 1024 * 100) / 100), 2) . 'KB' : $averageMemory . 'BYTES');

		echo "\n---------------------autoloader--------------------------\n";
		echo "times      \t$times\n";
		echo "totalTime   \t{$totalTime}s\taverageTime   \t{$averageTime}s\n";
		echo "memoryUsage \t{$memory_usage}\taverageMemory \t{$averageMemory}";
		echo "\n---------------------------------------------------------\n";
		$this->assertTrue(10 > $totalTime);
	}
}
