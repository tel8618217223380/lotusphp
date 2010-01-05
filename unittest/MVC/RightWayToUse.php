<?php
/**
 * 本测试文档演示了MVC的正确使用方法 
 * 按本文档操作一定会得到正确的结果
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "common.inc.php";
class RightWayToUseMVC extends PHPUnit_Extensions_OutputTestCase
{
	/**
	 * MVC用法示例
	 */
	public function testMostUsedWay()
	{
		/**
		 * 加载Action类文件
		 */
		$appDir = dirname(__FILE__) . "/test_data/simplest_app";
		require_once "$appDir/action/UserSigninAction.php";
		
		/*
		 * 实例化
		 */
		$dispatcher = new LtDispatcher;
		$dispatcher->viewDir = "$appDir/view/";
		$dispatcher->dispatchAction("User", "Signin");
		$this->expectOutputString('<h1>200 - Welcome, please signin</h1>

<form>
<input type="text" name="username" value="lotusphp" />
</form>');
	}
}
