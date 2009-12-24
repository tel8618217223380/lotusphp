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
	 *  # 以这样的流程使用LtAutoloader：依次执行new LtAutoloader(), addDirs($dirs), init()
	 *  # 需要被自动加载的文件都以.php或者.inc结尾
	 *    如果既有php文件，又有html文件，html文件将被忽略，php文件正常加载
	 *    可配置，详情参见LtAutoloaderCofig
	 *
	 * LtAutoloader不在意：
	 *  # 目录名有没有拖尾斜线
	 *  # 目录下面有无子目录
	 *  # 文件名和文件路径跟类名有无关联
	 *  # 定义和使用类时，类名是大写还是小写
	 *
	 * LtAutoloader不支持（出错演示和不支持的原因参见WrongWayToUse.php）：
	 *  # 传入的参数不是目录名（如/proj/lib/class.php）
	 *  # 传入的参数不是真实存在的目录（如http://some_dir这样的）
	 *  # 目录名或者文件名带空格（如Zend Framework）
	 *  # 类或接口重名，函数和函数重名
	 * 
	 * LtAutoloader不强求，但建议最好这样（就是说你不按下面写的做，也可以运行）：
	 *  # addDirs()的时候，使用绝对路径，而不是相对路径（相对目录容易出错，尤其是写命令行程序的时候）
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
			dirname(__FILE__) . DIRECTORY_SEPARATOR . "class_dir_1",
			dirname(__FILE__) . DIRECTORY_SEPARATOR . "class_dir_2",
			dirname(__FILE__) . DIRECTORY_SEPARATOR . "function_dir_1",
			dirname(__FILE__) . DIRECTORY_SEPARATOR . "function_dir_2"
		);
		$autoloader->init();
		$this->assertTrue(new Goodbye() instanceof GoodBye);
		$this->assertTrue(class_exists("HelloWorld"));
		$this->assertEquals(HelloLotus::sayHello(), "hello");
		$this->assertEquals(say_hello(), "hello");
		$this->assertEquals(say_hello_2(), "hello_2");
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

	/**
	测试接口: PrepareDirs() 
	输入: 一个多维数组形式, 数组内的值必需是实际存在的目录(相对或绝对路径).
	输出: 一个一维数组形式, 数组内的值是绝对路径, 保存在dirs属性.
	*/
	public function testPrepareDirs()
	{
		$dirs = array('Dirs/dir1\\','Dirs/dir2/',array('Dirs/dir3',array('Dirs/dir3/dir4/dir5/dir6')),'','Dirs/dir8');
		$obj = array(
		dirname(__FILE__).DIRECTORY_SEPARATOR.'Dirs'.DIRECTORY_SEPARATOR.'dir1',
		dirname(__FILE__).DIRECTORY_SEPARATOR.'Dirs'.DIRECTORY_SEPARATOR.'dir2',
		dirname(__FILE__),
		dirname(__FILE__).DIRECTORY_SEPARATOR.'Dirs'.DIRECTORY_SEPARATOR.'dir8',	
		dirname(__FILE__).DIRECTORY_SEPARATOR.'Dirs'.DIRECTORY_SEPARATOR.'dir3',	
		dirname(__FILE__).DIRECTORY_SEPARATOR.'Dirs'.DIRECTORY_SEPARATOR.'dir3'.DIRECTORY_SEPARATOR.'dir4'.DIRECTORY_SEPARATOR.'dir5'.DIRECTORY_SEPARATOR.'dir6',	
		);
		$autoloaderToBeTest = new LtAutoloaderProxy();
		$autoloaderToBeTest->prepareDirs($dirs);
		$this->assertEquals($autoloaderToBeTest->dirs, $obj);
	}

	public function parseLibNamesDataProvider()
	{
		return array(
			//最常用的Class写法
			array("<?php
			class Src", array("class" => array("Src"), "function" => array())),
			
			//class关键字大写，class和类名间有多个空格或者tab
			array("
			  Class   	Source{}", array("class" => array("Source"), "function" => array())),

			//接口，interface和接口名间有换行
			array("Interface 
			Trade{}", array("class" => array("Trade"), "function" => array())),

			//函数
			array("function 
			function1(){}", array("class" => array(), "function" => array("function1"))),
		);
	}

	/**
	 * @dataProvider parseLibNamesDataProvider
	 */
	public function testParseLibNams($src, $expected)
	{
		$ap = new LtAutoloaderProxy();
		$this->assertEquals($ap->parseLibNames($src), $expected);
	}
}