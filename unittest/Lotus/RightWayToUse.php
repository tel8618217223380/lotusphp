<?php
/**
 * 本测试文档演示了Lotus的正确使用方法 
 * 按本文档操作一定会得到正确的结果
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "include_classes.inc";
class RightWayToUseLotus extends PHPUnit_Framework_TestCase
{
	/**
	 * 最常用的使用方式（推荐）
	 * 
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
		 * envMode的默认值是dev，即开发模式
		 * envMode不等于dev的时候（如prod-生产环境，testing-测试环境），性能会有提高
		 * $lotus->envMode = "prod";
		 */
		$lotus->envMode = "prod";
		$lotus->option["cache_adapter"] = "file";
		$lotus->init();
		
		/**
		 * 
		 */
		$this->asserttrue(class_exists("LtCaptcha"));
	}
}
