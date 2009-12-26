<?php
/*
 * 加载Captcha类文件
 */
$lotusHome = substr(__FILE__, 0, strpos(__FILE__, "example"));
include $lotusHome . "/runtime/Captcha/Captcha.php";
include $lotusHome . "/runtime/Captcha/CaptchaConfig.php";
include $lotusHome . "/runtime/Captcha/CaptchaImageEngine.php";
/*
 * 加载Captcha类文件
 */

/*
 * 开始使用Captcha
 */
$captcha = new LtCaptcha();