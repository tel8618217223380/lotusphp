<?php
/**
 * 利用测试用例单步调试的模板
 */
require_once "phpunit_bootstrap.inc.php";

/**
 * 调试代码
 */
require_once './Cache/common.inc.php';
require_once './Cache/RightWayToUse.php';
$i = new RightWayToUseCache;
$i->testKeyValue();
