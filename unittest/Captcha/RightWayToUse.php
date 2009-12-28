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
	 *  # key必须是数字或者字串，不能是数组，对象 
	 * 
	 * -------------------------------------------------------------------
	 * LtCaptcha不在意：
	 *  # value的数据类型是什么（但一般来说resource型数据是不能被缓存的） 
	 * 
	 * -------------------------------------------------------------------
	 * LtCaptcha建议（不强求）：
	 *  # 如果你的服务器上有apc/eaccelerator/xCaptcha等opcode Captcha
	 *    最好不要再使用file adapter
	 *  # 为保证key不冲突，最好使用namespace功能
	 * 
	 * 本测试用例期望效果：
	 * 能成功通过add(), get(), del(), update()接口读写数据
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

		//初始化完毕，测试其效果
		$this->assertTrue($captcha->getImageResource("test_key"));
	}
}
