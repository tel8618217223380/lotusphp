<?php
/*
 * 载入类文件
 */
$lotusHome = dirname(dirname(dirname(__FILE__)));
include $lotusHome . "/runtime/Url/Url.php";
include $lotusHome . "/runtime/Url/UrlConfig.php";

/*
 * 初始化Url配置，设定默认的pattern
 */
Url::singleton(array("pattern" => "rewrite"));