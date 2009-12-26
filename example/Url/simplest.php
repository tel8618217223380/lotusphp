<?php
/*
 * 加载Url类文件
 */
$lotusHome = substr(__FILE__, 0, strpos(__FILE__, "example"));
include $lotusHome . "/runtime/Url/Url.php";
include $lotusHome . "/runtime/Url/UrlConfig.php";
/*
 * 加载Url类文件
 */

$url = new LtUrl;
$url->conf->patern = "rewrite";
echo $url->generate("Index", "Add");