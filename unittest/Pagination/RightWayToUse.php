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

		$conf['num_display_entries'] = 9; //数字链接显示数量
		$conf['num_links'] = 4; //当前页码的前面和后面链接的数量 
		$conf['per_page'] = 25; //每个页面中希望展示的项目数量
		$conf['show_first'] = true;
		$conf['show_prev'] = true;
		$conf['show_next'] = true;
		$conf['show_last'] = true;
		$conf['show_goto'] = true;
		$conf['show_info'] = true;
		$conf['first_text'] = 'First';
		$conf['prev_text'] = 'Prev';
		$conf['next_text'] = 'Next';
		$conf['last_text'] = 'Last';
		$conf['full_tag_open'] = '<div id="pager">';
		$conf['full_tag_close'] = '</div>';
		$conf['num_tag_open'] = '<ul class="pages">';
		$conf['num_tag_close'] = '</ul>';
		$conf['link_tag_open'] = '<li class="page-number"><a href=":url">';
		$conf['link_tag_close'] = '</a></li>';
		$conf['link_tag_cur_open'] = '<li class="page-number pgCurrent">';
		$conf['link_tag_cur_close'] = '</li>';
		$conf['button_tag_open'] = '<li class="pgNext"><a href=":url">';
		$conf['button_tag_close'] = '</a></li>';
		$conf['button_tag_empty_open'] = '<li class="pgNext pgEmpty">';
		$conf['button_tag_empty_close'] = '</li>';

		LtPagination::$configHandle->addConfig($conf);
		/**
		 * 
		 * @todo 配置文件设置输出html形式 , 不使用handle, 这样也可以吧?
		 */
		$pagination->init();

		$pager = $pagination->Pager(1, 1000, '?page=:page');
	}

	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}
}
