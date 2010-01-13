<?php
/**
 * 本测试文档演示了LtPagination的正确使用方法 
 * 按本文档操作一定会得到正确的结果 
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "common.inc.php";
class RightWayToUseCache extends PHPUnit_Framework_TestCase
{
	/**
	 * 最常用的使用方式 
	 */
	public function testMostUsedWay()
	{
		/**
		 * Lotus组件初始化三步曲
		 */
		// 1. 实例化
		$pagination = new LtPagination;
		// 2. 设置属性
		$pagination->conf['base_url'] = 'comment/view/page/:page';
		$pagination->conf['total_rows'] = 586;
		$pagination->conf['per_page'] = 20;
		$pagination->conf['uri_segment'] = 4;
		// 3. 调init()方法
		$pagination->init();

		//初始化完毕，测试其效果
		$pages = $pagination->generate();

	}

}
