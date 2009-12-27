<?php
/**
 * 利用测试用例单步调试的模板
 */
require "common.inc";

/**
 * 调试代码
 */
include './Autoloader/Performance.php';
$i = new PerformanceTest4Autoloader();
$i->testPerformance();