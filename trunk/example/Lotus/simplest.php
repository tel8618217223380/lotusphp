<?php
/*
 * 加载Lotus类文件
 */
$lotusHome = dirname(dirname(dirname(__FILE__)));
include $lotusHome . "/runtime/lotus.php";

/*
 * 初始化Lotus类
 */
$lotus = new Lotus();
$lotus->cacheAdapter = "xcache";
$lotus->boot();