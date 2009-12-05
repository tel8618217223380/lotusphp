<?php
/* 
 * 加载Captcha类文件
 */
$lotusHome = dirname(dirname(dirname(__FILE__)));
include $lotusHome . "/runtime/Captcha/Captcha.php";
include $lotusHome . "/runtime/Captcha/CaptchaConfig.php";
/* 
 * 加载Captcha类文件
 */

/*
 * 开始使用Captcha
 */
//构造设置Captcha的参数
$captcha = new LtCaptcha();
$captcha->conf->secretKey = "lotusphp";

$seed = uniqid();
$captcha->generateImage($seed);
