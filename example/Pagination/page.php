<?php
/**
 * 加载Cookie类文件
 */
$page = isset($_GET['page']) ? $_GET['page'] : 1;

$lotusHome = substr(__FILE__, 0, strpos(__FILE__, "example"));
include $lotusHome . "/runtime/Pagination/Pagination.php";
include $lotusHome . "/runtime/Pagination/PaginationConfig.php";
$pagination = new LtPagination;
$pagination->conf->total_rows = 1000; //总数
$pagination->conf->cur_page = $page; //当前页
$pagination->conf->page_size = 25; //每页显示数
$pagination->conf->base_url = 'page.php?page=:page'; // :page会自动被替换掉
$pagination->init();

echo <<<END
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>pagination</title>
<style type="text/css">
.pages a { margin:0 1px; padding:1px 4px; color: #002F79; background:#f9fcff; border:1px solid #A1C4CD; text-align: center; text-decoration: none; font:normal 12px/14px verdana; }
.pages a:hover { border:#37A717 1px solid; color: #002F79; background:#EDFFE4; text-decoration:none; font-weight: bold; }
.pages input { margin:0 1px; padding:1px 4px; border:1px solid #87C932; color:#499000; font:bold 12px/15px Verdana; }
.pages strong { padding:2px; margin: 0 3px; font:bold 10px/12px Tahoma; }
</style>
</head>
<body>
<p>分页演示</p>
$pagination->pages
<p>分页演示</p>
</body>
</html>
END;
