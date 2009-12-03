<?php
/*
 * 加载Db类文件
 */
$lotusHome = dirname(dirname(dirname(__FILE__)));
include $lotusHome . "/runtime/Url/Url.php";
include $lotusHome . "/runtime/Url/UrlConfig.php";
/*
 * 加载Db类文件
 */

$url = new LtUrl;
$url->conf->patern = "rewrite";
echo $url->generate("Index", "Add");