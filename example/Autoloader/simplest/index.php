<?php
/* 
 * Load rumtime class start
 */
$lotusHome = dirname(dirname(dirname(dirname(__FILE__))));
include $lotusHome . "/runtime/Autoloader/Autoloader.php";
/* 
 * Load rumtime class end
 */

$autoloader = new Autoloader;
$autoloader->init($autoloader->scanDir(array($lotusHome . DIRECTORY_SEPARATOR . "runtime" . DIRECTORY_SEPARATOR)));
$url = new Url;
var_dump($url);