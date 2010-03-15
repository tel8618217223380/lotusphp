<?php
/**
 * 本测试文档演示了LtCookie的正确使用方法 
 * 按本文档操作一定会得到正确的结果
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "common.inc.php";
class RightWayToUseCookie extends PHPUnit_Framework_TestCase
{
	/**
	 * 最常用的使用方式（推荐） 
	 * -------------------------------------------------------------------
	 * LtCookie要求： 
	 *  # 使用LtCookie必须设置密钥（LtCookie->conf->secretKey）
	 * 
	 * -------------------------------------------------------------------
	 * LtCookie建议（不强求）：
	 *  # 使用杂乱无章的字符串作为密钥（LtCookie->conf->secretKey）
	 * 
	 * 本测试用例期望效果：
	 * 无法对HTTP Cookie头进行单元测试，实际使用方法和效果参见example/Cookie/simplest.php
	 */
	public function testMostUsedWay()
	{

			$result = callWeb("Cookie/cookie_proxy.php", array("operation" => "set"), true);
			print_r($result);
			$result = callWeb("Cookie/cookie_proxy.php", array("operation" => "get"), true);
			print_r($result);
			$result = callWeb("Cookie/cookie_proxy.php", array("operation" => "del"), true);
			print_r($result);
			$this->assertTrue(true);
	}

	/**
	 * 测试加密解密接口是否能正常工作
	 */
	public function testEncrypt()
	{
		$cp = new CookieProxy;
		$cp->conf->secretKey = "KDHiUS(*^*";
		$cp->init();
		$encrypted = $cp->encrypt("lotusphp");
		$this->assertEquals("lotusphp", $cp->decrypt($encrypted));
	}
	protected function setUp()
	{
	}
	protected function tearDown()
	{
	}
}
