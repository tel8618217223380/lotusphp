<?php
class LtPagination
{
	public static $configHandle;
	public $conf;

	public function __construct()
	{
		self::$configHandle = new LtConfig;
	}

	public function init()
	{
		if ($tmp = self::$configHandle->get("pagination.pager"))
		{
			$this->$conf = $tmp;
		}
	}

	/**
	 * 
	 * @param  $page int 当前页
	 * @param  $pagesize int 每页显示数
	 * @param  $count int 总数
	 * @param  $url string 字串中使用 :page 表示页参数 不在意位置 如/a/:page/c/e
	 */
	public function pager($page, $pagesize, $count, $url)
	{
		$pagecount = ceil($count / $pagesize); 
		$pager  = $this->renderPager($page, $pagecount, $url);
		$pager .= $this->renderButton('goto', $page, $pagecount);
		$pager .= $this->renderButton('info', $page, $pagecount);
		return $pager;
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
		$pager = '<ul class="pages">';

		$pager .= $this->renderButton('first', $pagenumber, $pagecount);
		$pager .= $this->renderButton('prev', $pagenumber, $pagecount);

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
				$currentButton .= '<li class="page-number pgCurrent">' . $page . '</li>';
			}
			else
			{
				$currentButton .= '<li class="page-number">' . "<a href=\"$url\">" . $page . '</a>' . '</li>';
			}
		}
		$pager .= $currentButton;
		$pager .= $this->renderButton('next', $pagenumber, $pagecount);
		$pager .= $this->renderButton('last', $pagenumber, $pagecount);
		$pager .= '</ul>';

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
				break;
			case "prev":
				$destPage = $pagenumber - 1;
				break;
			case "next":
				$destPage = $pagenumber + 1;
				break;
			case "last":
				$destPage = $pagecount;
				break;
		}
		$url = str_replace(':page', $destPage, $baseurl);
		$button = '<li class="pgNext">' . "<a href=\"$url\">" . $buttonLabel . '</a>' . '</li>';

		if ($buttonLabel == "first" || $buttonLabel == "prev")
		{
			if ($pagenumber <= 1)
			{
				$button = '<li class="pgNext pgEmpty">' . $buttonLabel . '</li>';
			}
		}
		else
		{
			if ($pagenumber >= $pagecount)
			{
				$button = '<li class="pgNext pgEmpty">' . $buttonLabel . '</li>';
			}
		}
		return $button;
	}
}
