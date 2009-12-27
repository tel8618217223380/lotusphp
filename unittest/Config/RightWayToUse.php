<?php
/**
 * 本测试文档演示了LtConfig的正确使用方法 
 * 按本文档操作一定会得到正确的结果
 */
chdir(dirname(__FILE__));
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "include_classes.inc";
class RightWayToUseConfig extends PHPUnit_Framework_TestCase
{
	/**
	 * -------------------------------------------------------------------
	 * LtConfig要求：
			# 配置文件以return array()形式返回一个配置数组

	 * -------------------------------------------------------------------
	 * LtConfig不在意：
	    # 配置文件中用什么变量名和常量名

	 * -------------------------------------------------------------------
	 * 本测试用例期望效果：
	 * 通过LtConfig类能取到定义在config_file里面的配置信息
	 */
	public function testMostUsedWay()
	{
		$conf = new LtConfig;
		$conf->configFile = "./conf/conf.php";
		$conf->init();

		$this->assertTrue(new Goodbye() instanceof GoodBye);
		$this->assertTrue(class_exists("HelloWorld"));
		$this->assertEquals(HelloLotus::sayHello(), "hello");
		$this->assertEquals(say_hello(), "hello");
		$this->assertEquals(say_hello_2(), "hello_2");
	}
}

