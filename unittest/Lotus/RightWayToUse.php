<?php
/**
 * 本测试文档演示了Lotus的正确使用方法 
 * 按本文档操作一定会得到正确的结果
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "common.inc.php";
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
		$lotus = new Lotus;
		
		/**
		 * devMode的默认值是true，即默认处于开发模式
		 * devMode等于false的时候（如生产环境，测试环境），性能会有提高
		 * $lotus->devMode = false;
		 */
		$lotus->devMode = true;
		//$lotus->option["cache_adapter"] = "file";
		// 必需指定配置文件
		$lotus->option["config_file"] = dirname(__FILE__) . "/conf.php";
		$lotus->init();
		
		/**
		 * 
		 */
		$this->asserttrue(class_exists("LtCaptcha"));
	}
}
