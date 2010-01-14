<?php
class LtPaginationConfig
{
	public $base_url = ''; 
	public $page_size = 25; //每页显示多少条
	public $cur_page = 1; // 当前页
	public $total_rows = 0; // 总数
	public $full_tag_open = "<div class=\"pages\">";
	public $full_tag_close = "</div>";
	public $first_link = '&lsaquo;'; //首页链接名称
	public $last_link = '&rsaquo;'; //尾页链接名称
	public $cur_tag_open = '<strong>'; //当前页标签
	public $cur_tag_close = '</strong>'; 
}
