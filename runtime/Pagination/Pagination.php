<?php
class LtPagination
{
	public static $configHandle;
	public $conf;

	public function __construct()
	{
		$this->conf['pagination.pager']['num_display_entries'] = 9; //数字链接显示数量 
		$this->conf['pagination.pager']['num_links'] = 4; //当前页码的前面和后面链接的数量 
		$this->conf['pagination.pager']['per_page'] = 25; //每个页面中希望展示的项目数量 
		$this->conf['pagination.pager']['show_first'] = true;
		$this->conf['pagination.pager']['show_prev'] = true;
		$this->conf['pagination.pager']['show_next'] = true;
		$this->conf['pagination.pager']['show_last'] = true;
		$this->conf['pagination.pager']['show_goto'] = true;
		$this->conf['pagination.pager']['show_info'] = true;
		$this->conf['pagination.pager']['first_text'] = 'First';
		$this->conf['pagination.pager']['prev_text'] = 'Prev';
		$this->conf['pagination.pager']['next_text'] = 'Next';
		$this->conf['pagination.pager']['last_text'] = 'Last';
		$this->conf['pagination.pager']['full_tag_open'] = '<div class="pages">';
		$this->conf['pagination.pager']['full_tag_close'] = '</div>';
		$this->conf['pagination.pager']['num_tag_open'] = '';
		$this->conf['pagination.pager']['num_tag_close'] = '';
		$this->conf['pagination.pager']['link_tag_open'] = '<a href=":url">';
		$this->conf['pagination.pager']['link_tag_close'] = '</a>';
		$this->conf['pagination.pager']['link_tag_cur_open'] = '<strong>';
		$this->conf['pagination.pager']['link_tag_cur_close'] = '</strong>';
		$this->conf['pagination.pager']['button_tag_open'] = '<a href=":url" style="font-weight:bold">';
		$this->conf['pagination.pager']['button_tag_close'] = '</a>';
		$this->conf['pagination.pager']['button_tag_empty_open'] = '';
		$this->conf['pagination.pager']['button_tag_empty_close'] = '';
		self::$configHandle = new LtConfig;
	}

	public function init()
	{
		$conf = self::$configHandle->get("pagination.pager");
		if (!empty($conf))
		{
			$this->conf = $conf;
		}
	}

	/**
	 * 
	 * @param  $page int 当前页
	 * @param  $count int 这个数值是你查询数据库得到的数据总量
	 * @param  $url string 字串中使用 :page 表示页参数 不在意位置 如/a/:page/c/e
	 */
	public function pager($page, $count, $url)
	{
		$per_page = empty($this->conf['per_page']) ? 25 : $this->conf['per_page'];
		$pagecount = ceil($count / $per_page);
		$pager = $this->renderPager($page, $pagecount, $url);
		$pager .= $this->renderButton('goto', $page, $pagecount, $url);
		$pager .= $this->renderButton('info', $page, $pagecount, $url);
		return $this->conf['full_tag_open'] . $pager . $this->conf['full_tag_close'];
	}

	/**
	 * 
	 * @param  $pagenumber int 当前页
	 * @param  $pagecount int 总页数
	 * @return string 
	 */
	public function renderPager($pagenumber, $pagecount, $baseurl = '?page=:page')
	{
		$baseurl = urldecode($baseurl);
		$pager = $this->conf['num_tag_open'];

		$pager .= $this->renderButton('first', $pagenumber, $pagecount, $baseurl);
		$pager .= $this->renderButton('prev', $pagenumber, $pagecount, $baseurl);

		$startPoint = 1;
		$endPoint = 9;

		if ($pagenumber > 4)
		{
			$startPoint = $pagenumber - 4;
			$endPoint = $pagenumber + 4;
		}

		if ($endPoint > $pagecount)
		{
			$startPoint = $pagecount - 8;
			$endPoint = $pagecount;
		}

		if ($startPoint < 1)
		{
			$startPoint = 1;
		}
		$currentButton = '';
		for ($page = $startPoint; $page <= $endPoint; $page++)
		{
			$url = str_replace(':page', $page, $baseurl);
			if ($page == $pagenumber)
			{
				$currentButton .= $this->conf['link_tag_cur_open'] . $page . $this->conf['link_tag_cur_close'];
			}
			else
			{
				$currentButton .= str_replace(':url', $url, $this->conf['link_tag_open']) . $page . $this->conf['link_tag_close'];
			}
		}
		$pager .= $currentButton;
		$pager .= $this->renderButton('next', $pagenumber, $pagecount, $baseurl);
		$pager .= $this->renderButton('last', $pagenumber, $pagecount, $baseurl);
		$pager .= $this->conf['num_tag_close'];

		return $pager;
	}

	/**
	 * 
	 * @param  $buttonLabel string 显示文字
	 * @param  $pagenumber int 当前页
	 * @param  $pagecount int 总页数
	 * @return string 
	 */
	public function renderButton($buttonLabel, $pagenumber, $pagecount, $baseurl = '?page=:page')
	{
		$baseurl = urldecode($baseurl);
		$destPage = 1;
		if ('goto' == $buttonLabel)
		{
			$button = "goto <input type=\"text\" size=\"3\" onkeydown=\"javascript: if(event.keyCode==13){ location='{$baseurl}'.replace(':page',this.value);return false;}\" />";
			return $button;
		}
		if ('info' == $buttonLabel)
		{
			$button = " $pagenumber/$pagecount ";
			return $button;
		}
		switch ($buttonLabel)
		{
			case "first":
				$destPage = 1;
				$bottenText = $this->conf['first_text'];
				break;
			case "prev":
				$destPage = $pagenumber - 1;
				$bottenText = $this->conf['prev_text'];
				break;
			case "next":
				$destPage = $pagenumber + 1;
				$bottenText = $this->conf['next_text'];
				break;
			case "last":
				$destPage = $pagecount;
				$bottenText = $this->conf['last_text'];
				break;
		}
		$url = str_replace(':page', $destPage, $baseurl);
		$button = str_replace(':url', $url, $this->conf['button_tag_open']) . $bottenText . $this->conf['button_tag_close'];

		if ($buttonLabel == "first" || $buttonLabel == "prev")
		{
			if ($pagenumber <= 1)
			{
				$button = $this->conf['button_tag_empty_open'] . $bottenText . $this->conf['button_tag_empty_close'];
			}
		}
		else
		{
			if ($pagenumber >= $pagecount)
			{
				$button = $this->conf['button_tag_empty_open'] . $bottenText . $this->conf['button_tag_empty_close'];
			}
		}
		return $button;
	}
}
