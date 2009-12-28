<?php
/**
 * 利用测试用例单步调试的模板
 */
require_once "phpunit_bootstrap.inc";

/**
 * 调试代码
 */
require_once './Captcha/include_classes.inc';
require_once './Captcha/RightWayToUse.php';
$i = new RightWayToUseCaptcha();
$i->testVerify();