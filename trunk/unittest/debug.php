<?php
/**
 * 利用测试用例单步调试的模板
 */
require_once "phpunit_bootstrap.inc";

/**
 * 调试代码
 */
require_once './DB/include_classes.inc';
require_once './DB/RightWayToUse.php';
$i = new RightWayToUseDb();
$i->testBase();