<?php
/**
 * 本测试文档演示了LtCaptcha的错误使用方法 
 * 不要按本文档描述的方式使用LtCaptcha
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "include_classes.inc";
class WrongWayToUseCaptcha extends PHPUnit_Framework_TestCase
{
	/**
	 * 调用getImageResource()和verify()接口不带seed参数
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testBlankSeed()
	{
		$captcha = new LtCaptcha;
		$captcha->init();
		$im = $captcha->getImageResource("");
	}
}
