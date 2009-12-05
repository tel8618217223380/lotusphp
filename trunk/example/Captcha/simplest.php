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
$captcha = new LtCaptcha();
$captcha->conf->secretKey = "lotusphp";

//绘制验证码图片
$seed = uniqid();
$captcha->generateImage($seed);

/*
 * 校验用户输入的验证码是否正确
if ($captcha->verify($seed, $_REQUEST["captcha"]))
{
	echo "验证码输入正确";
}
*/