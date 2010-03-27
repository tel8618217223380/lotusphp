<?php
$lotusHome = substr(__FILE__, 0, strpos(__FILE__, "example")) . '/';
include $lotusHome . "runtime/Config.php";
include $lotusHome . "runtime/Store.php";
include $lotusHome . "runtime/StoreMemory.php";
include $lotusHome . "runtime/Pagination/Pagination.php";

$page = isset($_GET['page']) ? $_GET['page'] : 1;

$pagination = new LtPagination;
$pagination->init();
$pager = $pagination->Pager($page,25,1000,'?page=:page');

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
<div id="pager" ><?php echo $pager;?></div>
</body>
</html>
