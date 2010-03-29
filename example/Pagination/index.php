<?php
$lotusHome = substr(__FILE__, 0, strpos(__FILE__, "example")) . '/';
include $lotusHome . "runtime/Config.php";
include $lotusHome . "runtime/Store.php";
include $lotusHome . "runtime/StoreMemory.php";
include $lotusHome . "runtime/Pagination/Pagination.php";

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$conf['pagination.pager']['num_display_entries'] = 9; //数字链接显示数量 
$conf['pagination.pager']['num_links'] = 2; //当前页码的前面和后面链接的数量 
$conf['pagination.pager']['per_page'] = 25; //每个页面中希望展示的项目数量 
$conf['pagination.pager']['show_first'] = true;
$conf['pagination.pager']['show_prev'] = true;
$conf['pagination.pager']['show_next'] = true;
$conf['pagination.pager']['show_last'] = true;
$conf['pagination.pager']['show_goto'] = true;
$conf['pagination.pager']['show_info'] = true;
$conf['pagination.pager']['first_text'] = 'First';
$conf['pagination.pager']['prev_text'] = 'Prev';
$conf['pagination.pager']['next_text'] = 'Next';
$conf['pagination.pager']['last_text'] = 'Last';
$conf['pagination.pager']['full_tag_open'] = '<div id="pager">';
$conf['pagination.pager']['full_tag_close'] = '</div>';
$conf['pagination.pager']['num_tag_open'] = '<ul class="pages">';
$conf['pagination.pager']['num_tag_close'] = '</ul>';
$conf['pagination.pager']['link_tag_open'] = '<li class="page-number"><a href=":url">';
$conf['pagination.pager']['link_tag_close'] = '</a></li>';
$conf['pagination.pager']['link_tag_cur_open'] = '<li class="page-number pgCurrent">';
$conf['pagination.pager']['link_tag_cur_close'] = '</li>';
$conf['pagination.pager']['button_tag_open'] = '<li class="pgNext"><a href=":url">';
$conf['pagination.pager']['button_tag_close'] = '</a></li>';
$conf['pagination.pager']['button_tag_empty_open'] = '<li class="pgNext pgEmpty">';
$conf['pagination.pager']['button_tag_empty_close'] = '</li>';
$pagination = new LtPagination;
LtPagination::$configHandle->addConfig($conf);
$pagination->init();
$pager = $pagination->Pager($page, 1000, '?page=:page');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Pagination</title>
<style type="text/css">
a:link { color:#000; text-decoration:none; }
a:visited { color:#000; text-decoration:none; }
a:active, a:hover { text-decoration:none; }
#pager ul.pages { display:block; border:none; text-transform:uppercase; font-size:10px; margin:10px 0 50px; padding:0; }
#pager ul.pages li { list-style:none; float:left; border:1px solid #ccc; text-decoration:none; margin:0 5px 0 0; padding:5px; }
#pager ul.pages li:hover { border:1px solid #003f7e; }
#pager ul.pages li.pgEmpty { border:1px solid #eee; color:#eee; }
#pager ul.pages li.pgCurrent { border:1px solid #003f7e; color:#000; font-weight:700; background-color:#eee; }
</style>
</head>
<body>
<h3>LotusPHP LtPagination Demo</h3>
<?php echo $pager; ?>
</body>
</html>
