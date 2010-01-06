<?php
/**
 * 利用测试用例单步调试的模板
 */
require_once "phpunit_bootstrap.inc.php";

/**
 * 调试代码
 */
require_once './Autoloader/common.inc.php';
require_once './Autoloader/RightWayToUse.php';
$i = new LtAutoloaderProxy;

$r=$i->parseLibNames(file_get_contents('../runtime/Cache/Adapter/CacheAdapterPhps.php'));
print_r($r);