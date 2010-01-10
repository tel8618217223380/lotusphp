<?php
/**
 * 本测试文档演示了LtCookie的错误使用方法 
 * 不要按本文档描述的方式使用LtCookie
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "common.inc.php";
class WrongWayToUseCookie extends PHPUnit_Framework_TestCase
{
	/**
	 * 调用getImageResource()和verify()接口不带seed参数
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testBlankSeed()
	{
		$Cookie = new LtCookie;
		$Cookie->init();
		$im = $Cookie->getImageResource("");
	}
}
