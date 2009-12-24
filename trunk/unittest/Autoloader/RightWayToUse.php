<?php
/**
 * 本测试文档演示了LtAutoloader的正确使用方法
 * 按本文档操作一定会得到正确的结果
 */
include dirname(__FILE__) . DIRECTORY_SEPARATOR . "AutoloaderProxy.php";
class RightWayToUseAutoloade extends PHPUnit_Framework_TestCase
{
	/**
	 * 最常用的使用方式（推荐）
	 * 
	 * LtAutoloader要求：
	 *  # new LtAutoloader()的时候，以多个（或者一个）参数的形式传入，每个参数都是目录名
	 *  # 需要被自动加载的文件都以.php或者.inc结尾（如果既有php文件，又有html文件，html文件将被忽略，php文件正常加载）
	 *
	 * LtAutoloader不在意：
	 *  # 目录名是绝对路径还是相对路径（相对目录容易出错，尤其是写命令行程序的时候，推荐使用绝对路径）
	 *  # 目录名有没有拖尾斜线
	 *  # 目录下面有无子目录
	 *  # 文件名和文件路径跟类名有无关联
	 *  # 定义和使用类时，类名是大写还是小写
	 *
	 * LtAutoloader不支持：
	 *  # 传入的参数不是目录名（如/proj/lib/class.php）
	 *  # 传入的参数不是真实存在的目录（如http://some_dir这样的）
	 *  # 目录名或者文件名带空格（如Zend Framework）
	 * 
	 * LtAutoloader不需求，但建议最好这样：
	 *  # 使用class而不是function来封装你的逻辑
	 *  # 每个class都放在单独的一个文件中，且不要在已经定义了类的文件里再定义函数
	 *  # class/function里不要使用__FILE__魔术变量
	 *
	 * 本测试用例期望效果：
	 * 在new CLASS_NAME, class_exists("CLASS_NAME"), extends CLASS_NAME的时候自动把包含该类的文件加载进来
	 */
	public function testMostUsedWay()
	{
		$autoloader = new LtAutoloader;
		$autoloader->addDirs(
			dirname(__FILE__) . DIRECTORY_SEPARATOR . "class_dir_1" . DIRECTORY_SEPARATOR,
			"class_dir_2",//为了测试这个相对目录，请到unittest/Autoloader目录下运行php ..\TestHelper.php RightWayToUse.php
			"function_dir_1"
		);
		$autoloader->init();
		$this->assertTrue(new Goodbye() instanceof GoodBye);
		$this->assertTrue(class_exists("HelloWorld"));
		$this->assertEquals(HelloLotus::sayHello(), "hello");
		$this->assertEquals(say_hello(), "hello");
	}

	/**
	 * 以一个数组形式传入类文件所在的目录，数组每个元素都是目录名
	 * 
	 * 适用场合：
	 * 多个目录名不是写死在代码中，当需要动态组合时，用数组方便
	 */
	public function testAddDirs()
	{
		$dirs = array(dirname(__FILE__) . DIRECTORY_SEPARATOR . "class_dir_1");
		if (is_dir(dirname(__FILE__) . DIRECTORY_SEPARATOR . "class_dir_2"))
		{
			$dirs[] = dirname(__FILE__) . DIRECTORY_SEPARATOR . "class_dir_2";
		}
		$ap = new LtAutoloaderProxy();
		$ap->addDirs($dirs);
		$this->assertEquals($ap->dirs, array(
			dirname(__FILE__) . DIRECTORY_SEPARATOR . "class_dir_1",
			dirname(__FILE__) . DIRECTORY_SEPARATOR . "class_dir_2"
		));
	}

	public function testParseLibNams()
	{
		$ap = new LtAutoloaderProxy();
		$src = "
		class Src
		{}
		Class   Source{}
		Interface 
		Trade{}
		function function1(){}
		";
		$this->assertEquals($ap->parseLibNames($src), array(
			"class" => array("Src", "Source", "Trade"),
			"function" => array("function1")
		));
	}
}