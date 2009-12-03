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

$fonts = dirname(__FILE__) . "/fonts";
//$fonts = "D:\AppServ\www\lotus\example\Captcha\fonts";

$captcha = new LtCaptcha();
$captcha->conf->secretKey = "lotusphp";
$captcha->conf->length = 5;
$captcha->conf->fontDir = $fonts;

$seed = uniqid();
$word = $captcha->getWord($seed);
$captcha->generateImage($word);