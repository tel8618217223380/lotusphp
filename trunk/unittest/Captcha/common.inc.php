<?php
$lotusHome = substr(__FILE__, 0, strpos(__FILE__, "unittest"));
require_once $lotusHome . "runtime/Captcha/Captcha.php";
require_once $lotusHome . "runtime/Captcha/CaptchaConfig.php";
require_once $lotusHome . "runtime/Captcha/CaptchaImageEngine.php";

class CaptchaProxy extends LtCaptcha
{
	public function getSavedCaptchaWord($seed)
	{
		return parent::getSavedCaptchaWord($seed);
	}
}