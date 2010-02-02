<?php
/**
 * 本测试文档演示了LtPagination的正确使用方法 
 * 按本文档操作一定会得到正确的结果
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "common.inc.php";
/**
 * @todo 应该将计算分页和输出html分开，允许用户指定展现层handle，可以提供默认handle
 */
class RightWayToUsePagination extends PHPUnit_Extensions_OutputTestCase
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
		$pagination->conf->total_rows = 1000; //总数
		$pagination->conf->cur_page = 5; //当前页
		$pagination->conf->page_size = 25; //每页显示数
		$pagination->conf->base_url = 'page.php?page=:page'; // :page会自动被替换掉 
		// 3. 调init()方法
		$pagination->init(); 
		// 初始化完毕，测试其效果
		echo $pagination->pages;
		// 判断输出内容是否正确
		$this->expectOutputString('<div class="pages"><a href="page.php?page=1" style="font-weight:bold">&lsaquo;</a><a href="page.php?page=2">2</a><a href="page.php?page=3">3</a><a href="page.php?page=4">4</a><strong>5</strong><a href="page.php?page=6">6</a><a href="page.php?page=7">7</a><a href="page.php?page=8">8</a><a href="page.php?page=9">9</a><a href="page.php?page=40" style="font-weight:bold">&rsaquo;</a>5/40 goto<input type="text" size="3" onkeydown="javascript: if(event.keyCode==13){ location=\'page.php?page=:page\'.replace(\':page\',this.value);return false;}" />page total 1000</div>');
	}
	protected function setUp()
	{
	}
	protected function tearDown()
	{
	}
}
