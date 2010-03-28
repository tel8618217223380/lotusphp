<?php
/**
 * 本测试文档演示了LtPagination的正确使用方法 
 * 按本文档操作一定会得到正确的结果
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "common.inc.php";
/**
 * 
 * @todo 应该将计算分页和输出html分开，允许用户指定展现层handle，可以提供默认handle
 */
class RightWayToUsePagination extends PHPUnit_Extensions_OutputTestCase
{
	/**
	 * 最常用的使用方式
	 */
	public function testMostUsedWay()
	{
		$pagination = new LtPagination;
		/**
		 * 
		 * @todo 配置文件设置输出html形式 , 不使用handle, 这样也可以吧?
		 */
		$pagination->init();
		$pager = $pagination->Pager(1, 25, 1000, '?page=:page');
	}

	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}
}
