<?php
/* 
 * Load rumtime class start
 */
$lotusHome = dirname(dirname(dirname(__FILE__)));
include $lotusHome . "/runtime/Url/Url.php";
include $lotusHome . "/runtime/Url/UrlConfig.php";
/* 
 * Load rumtime class end
 */

$url = new LtUrl;
$url->conf->patern = "rewrite";
echo $url->generate("Index", "Add");
//$url->link(__LINE__);
//$url->link(__LINE__);