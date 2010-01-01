<?php
/**
 * 利用测试用例单步调试的模板
 */
require_once "phpunit_bootstrap.inc.php";

/**
 * 调试代码
 */
require_once './DB/common.inc.php';
require_once './DB/RightWayToUse.php';
$i = new RightWayToUseDb();

$i->testMysql("DROP TABLE IF EXISTS test_user", null, true);