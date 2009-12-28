<?php
/**
 * 本测试文档演示了LtCaptcha的正确使用方法 
 * 按本文档操作一定会得到正确的结果
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "include_classes.inc";
class RightWayToUseCaptcha extends PHPUnit_Framework_TestCase
{
	/**
	 * 最常用的使用方式（推荐） 
	 * -------------------------------------------------------------------
	 * LtCaptcha要求： 
	 *  # 调用getImageResource()时传入唯一的seed，不能是常量
	 * 
	 * -------------------------------------------------------------------
	 * LtCaptcha建议（不强求）：
	 *  # 使用md5(uniqid())得到随机不冲突的seed
	 * 
	 * 本测试用例期望效果：
	 * 无法对图片进行单元测试，实际使用方法和效果参见example/Captcha/simplest.php
	 */
	public function testMostUsedWay()
	{
		/**
		 * Lotus组件初始化三步曲
		 */
		// 1. 实例化
		$captcha = new LtCaptcha;
		// 2. 设置属性
		$captcha->conf->length = 5;
		// 3. 调init()方法
		$captcha->init();

		/**
		 * 初始化完毕，测试其效果
		 */
		$this->assertTrue(is_resource($captcha->getImageResource(md5(uniqid()))));
	}

	/**
	 * 测试verify接口是否能正常工作
	 */
	public function testVerify()
	{
		$cp = new CaptchaProxy();
		$cp->init();
		$seed = md5(uniqid());
		$cp->getImageResource($seed);
		$word = $cp->getSavedCaptchaWord($seed);
		$this->assertTrue($cp->verify($seed, $word));
	}
}
