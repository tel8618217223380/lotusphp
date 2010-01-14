<?php
class LtPagination
{
	public $conf;
	public $pages;

	public function __construct()
	{ 
		$this->conf = new LtPaginationConfig;
	}

	public function init()
	{
		$this->pages = $this->createLinks();
	}

	public function createLinks()
	{
		$count = $this->conf->total_rows; //总数
		$page = $this->conf->cur_page; //当前页
		$pagesize = $this->conf->page_size; //每页显示数
		$baseurl = urldecode($this->conf->base_url);
		$max = 0; //最大分页数,0表示不限制
		
		
		$numofpage = ceil($count / $pagesize); //页数
		$page = max(intval($page), 1);
		$page = min($page,$numofpage);
		$max && $numofpage > $max && $numofpage = $max;
		if ($numofpage <= 1)
		{
			return $this->conf->full_tag_open . "$page/$numofpage page total $count" . $this->conf->full_tag_close;
		}
		else
		{
			$url = str_replace(':page', 1, $baseurl);
			$pages = $this->conf->full_tag_open . "<a href=\"{$url}\" style=\"font-weight:bold\">" . $this->conf->first_link . "</a>";
			$flag = 0;
			for($i = $page-3;$i <= $page-1;$i++) // 当前页前边显示3页
			{
				if ($i < 1) continue;
				$url = str_replace(':page', $i, $baseurl);
				$pages .= "<a href=\"{$url}\">$i</a>";
			}
			$pages .= $this->conf->cur_tag_open . "$page" . $this->conf->cur_tag_close; //当前页
			if ($page < $numofpage)
			{
				for($i = $page + 1;$i <= $numofpage;$i++)
				{
					$url = str_replace(':page', $i, $baseurl);
					$pages .= "<a href=\"{$url}\">$i</a>";
					$flag++;
					if ($flag == 4) break; //当前页后边显示4页
				}
			}
			$url = str_replace(':page', $numofpage, $baseurl);
			$pages .="<a href=\"{$url}\" style=\"font-weight:bold\">" . $this->conf->last_link . "</a>" ;
			$pages .= "$page/$numofpage goto<input type=\"text\" size=\"3\" onkeydown=\"javascript: if(event.keyCode==13){ location='{$baseurl}'.replace(':page',this.value);return false;}\" />page total $count" . $this->conf->full_tag_close;
			return $pages;
		}
	}
}
