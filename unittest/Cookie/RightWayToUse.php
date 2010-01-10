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
	 *  # 调用getImageResource()时传入唯一的seed，不能是常量
	 * 
	 * -------------------------------------------------------------------
	 * LtCookie建议（不强求）：
	 *  # 使用md5(uniqid())得到随机不冲突的seed
	 * 
	 * 本测试用例期望效果：
	 * 无法对图片进行单元测试，实际使用方法和效果参见example/Cookie/simplest.php
	 */
	public function testMostUsedWay()
	{
		/**
		 * Lotus组件初始化三步曲
		 */
		// 1. 实例化
		$Cookie = new LtCookie;
		// 2. 设置属性
		$Cookie->conf->secretKey = "open_the_d00r";
		// 3. 调init()方法
		$Cookie->init();

		/**
		 * 初始化完毕，测试其效果
		 */
		
	}

	/**
	 * 测试加密解密接口是否能正常工作
	 */
	public function testEncrypt()
	{
		$cp = new CookieProxy;
		$cp->conf->secretKey = "open_the_d00r";
		$cp->init();
		$encrypted = $cp->encrypt("lotusphp");
		$this->assertEquals("lotusphp", $cp->decrypt($encrypted));
	}
}
