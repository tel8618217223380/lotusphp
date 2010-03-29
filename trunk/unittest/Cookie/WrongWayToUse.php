<?php
/**
 * 本测试文档演示了LtCookie的错误使用方法 
 * 不要按本文档描述的方式使用LtCookie
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "common.inc.php";
class WrongWayToUseCookie extends PHPUnit_Framework_TestCase
{
	/**
	 * @todo 补齐测试用例
	 * 不设置密钥就开始使用LtCookie
	 * 
	 * @expectedException PHPUnit_Framework_Error 
	 */
	public function testNoSecretKeySet()
	{
	}

	protected function setUp()
	{
	}
	protected function tearDown()
	{
	}
}
