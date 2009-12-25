<?php
/**
 * 本测试文档演示了LtAutoloader的正确使用方法
 * 按本文档操作一定会得到正确的结果
 *
 * @todo 增加performance_tuning.php的测试用例
 * @todo 增加loadClass(), scanDirs(), conf->loadFunction的测试
 */
chdir(dirname(__FILE__));
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "AutoloaderProxy.php";
class RightWayToUseAutoloader extends PHPUnit_Framework_TestCase
{
	/**
	 *
	 * @example $autoloader = new LtAutoloader();
	 * $autoloader->autoloadPath = array('class/path_1','func/path_2');
	 * $autoloader->storeKeyPrefix = "abc_";
	 * $autoloader->init();
	 * -------------------------------------------------------------------
	 * 需要被自动加载的文件都以.php或者.inc结尾
	 * 如果既有php文件，又有html文件，html文件将被忽略，php文件正常加载
	 * 可配置，详情参见LtAutoloaderCofig
	 * -------------------------------------------------------------------
	 * LtAutoloader不在意：
	 * 目录名有没有拖尾斜线
	 * 目录下面有无子目录
	 * 文件名和文件路径跟类名有无关联
	 * 定义和使用类时，类名是大写还是小写
	 * -------------------------------------------------------------------
	 * LtAutoloader不支持（出错演示和不支持的原因参见WrongWayToUse.php）：
	 * 传入的参数不是目录名（如/proj/lib/class.php）
	 * 传入的参数不是真实存在的目录（如http://some_dir这样的）
	 * 目录名或者文件名带空格（如Zend Framework）
	 * 类或接口重名，函数和函数重名
	 * -------------------------------------------------------------------
	 * LtAutoloader建议（不强求）：
	 * autoloadPath使用绝对路径
	 * 使用class而不是function来封装你的逻辑
	 * 每个class都放在单独的一个文件中，且不要在已经定义了类的文件里再定义函数
	 * class/function里不要使用__FILE__魔术变量
	 * -------------------------------------------------------------------
	 * 本测试用例期望效果：
	 * 在new CLASS_NAME, class_exists("CLASS_NAME"), extends CLASS_NAME的时候
	 * 自动把包含该类的文件加载进来
	 */
	public function testMostUsedWay()
	{
		$autoloader = new LtAutoloader;
		$autoloader->autoloadPath = array(
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
	 * 本用例展示了怎样给LtAutoloader传递需要自动加载的目录
	 */
	public function setAutoloadPathDataProvider()
	{
		$cd = dirname(__FILE__);//current dir, 当前目录
		return array(
			//用一个数组传递多个目录，绝对路径，不带拖尾斜线
			array(
				array("$cd/class_dir_1", "$cd/class_dir_2"),
				array("$cd" . DIRECTORY_SEPARATOR . "class_dir_1", "$cd" . DIRECTORY_SEPARATOR . "class_dir_2"),
			),

			//只有一个目录，可以不用数组传
			array(
				"$cd/class_dir_1",
				array("$cd" . DIRECTORY_SEPARATOR . "class_dir_1"),
			),
			
			//用二维数组传递多个目录（不推荐）
			array(
				array("class_dir_1", array("class_dir_2")),
				array("$cd" . DIRECTORY_SEPARATOR . "class_dir_1", "$cd" . DIRECTORY_SEPARATOR . "class_dir_2"),
			),

			//相对路径（不推荐）
			array(
				array("class_dir_1", "./class_dir_2"),
				array("$cd" . DIRECTORY_SEPARATOR . "class_dir_1", "$cd" . DIRECTORY_SEPARATOR . "class_dir_2"),
			),

			//带拖尾斜线
			array(
				array("class_dir_1/", "class_dir_2\\"),
				array("$cd" . DIRECTORY_SEPARATOR . "class_dir_1", "$cd" . DIRECTORY_SEPARATOR . "class_dir_2"),
			),

			// 目录分隔符\/任意
			array(
				array("$cd\class_dir_1", "$cd/class_dir_1"),
				array("$cd" . DIRECTORY_SEPARATOR . "class_dir_1", "$cd" . DIRECTORY_SEPARATOR . "class_dir_1"),
			),

			// 可以是文件
			array(
				"$cd/class_dir_1/GoodBye.php", 
				array("$cd" . DIRECTORY_SEPARATOR . "class_dir_1" . DIRECTORY_SEPARATOR . 'GoodBye.php')
			),
			/**
			添加新的测试条件，只需要复制下面这段代码，去掉注释，换掉相应的参数，即可
			array(
				array("$cd/class_dir_1", "$cd/class_dir_2"), //$userParameter，setAutoloadPath()的参数
				array("$cd" . DIRECTORY_SEPARATOR . "class_dir_1", "$cd" . DIRECTORY_SEPARATOR . "class_dir_2"), //$expected，正确结果
			),
			*/
		);
	}

	/**
	 * 本用例展示了LtAutoloader能识别哪些类和函数定义
	 */
	public function parseLibNamesDataProvider()
	{
		return array(
			// 最常用的Class写法
			array("<?php
				class Src",
				array("class" => array("Src"), "function" => array())
				),
			// class关键字大写，class和类名间有多个空格或者tab
			array("
			  Class   	Source{}",
				array("class" => array("Source"), "function" => array())
				),
			// 接口，interface和接口名间有换行
			array("Interface
				Trade{}", array("class" => array("Trade"), "function" => array())
				),
			// 函数
			array("function
				function1(){}", array("class" => array(), "function" => array("function1"))
				),
			// 添加新的测试条件请复制下面这段代码并换掉相应的参数.
			// array("<?php
			// class Src", //$src，定义类或函数的代码
			// array("class" => array("Src"), "function" => array()) //$expected，正确结果
			// ),
			);
	}

	/**
	 * 本用例展示了怎样设置允许加载的文件类型
	 */
	public function isAllowedFileDataProvider()
	{
		return array(
			array(
				array("php3", "php5"),
				"test.php",
				false,
				),

			array(
				array("php", "php5"),
				"test.php5",
				true,
				),
			// 添加新的测试条件请复制下面这段代码并换掉相应的参数.
			// array(
			// array("php", "php5"), //$extArray，允许加载的文件类型
			// "test.php3", //$filename，用于测试的文件名
			// false, //$expected，正确结果
			// ),
			);
	}

	/**
	 * 本用例展示了怎样设置禁止扫描的子目录名称
	 */
	public function isSkippedDirDataProvider()
	{
		return array(
			array(
				array(".setting", "bak"),
				".setting",
				true,
				),

			array(
				array(".setting", "bak"),
				"source",
				false,
				),
			// 添加新的测试条件请复制下面这段代码并换掉相应的参数.
			// array(
			// array(".setting", "bak"), //$dirBlackListArray，允许加载的文件类型
			// ".setting", //$dir，用于测试的目录名
			// false, //$expected，正确结果
			// ),
			);
	}
	/**
	 * @dataProvider setAutoloadPathDataProvider
	 */
	public function testpreparePath($userParameter, $expected)
	{
		$ap = new LtAutoloaderProxy();
		$path = $ap->var2array($userParameter);
		$path = $ap->preparePath($path);
		$this->assertEquals($path, $expected);
	}
	/**
	 *
	 * @dataProvider parseLibNamesDataProvider
	 */
	public function testParseLibNams($src, $expected)
	{
		$ap = new LtAutoloaderProxy();
		$this->assertEquals($ap->parseLibNames($src), $expected);
	}

	/**
	 *
	 * @dataProvider isAllowedFileDataProvider
	 */
	public function testIsAllowedFile($extArray, $filename, $expected)
	{
		$ap = new LtAutoloaderProxy();
		$ap->conf->allowFileExtension = $extArray;
		$this->assertEquals($ap->isAllowedFile($filename), $expected);
	}

	/**
	 *
	 * @dataProvider isSkippedDirDataProvider
	 */
	public function testIsSkippedDir($dirBlackListArray, $dir, $expected)
	{
		$ap = new LtAutoloaderProxy();
		$ap->conf->skipDirNames = $dirBlackListArray;
		$this->assertEquals($ap->isSkippedDir($dir), $expected);
	}

	public function testscanDirs()
	{
		$ap = new LtAutoloaderProxy();
		$ap->scanDirs(array('class_dir_1','class_dir_2'));
		$this->assertEquals($ap->storeHandle->get($ap->storeKeyPrefix.'goodbye'), dirname(__FILE__) . DIRECTORY_SEPARATOR . "class_dir_1" . DIRECTORY_SEPARATOR . 'GoodBye.php');

		$this->assertEquals($ap->storeHandle->get($ap->storeKeyPrefix.'helloworld'), dirname(__FILE__) . DIRECTORY_SEPARATOR . "class_dir_2" . DIRECTORY_SEPARATOR . 'HelloWorld.php');

		$this->assertEquals($ap->storeHandle->get($ap->storeKeyPrefix.'hellolotus'), dirname(__FILE__) . DIRECTORY_SEPARATOR . "class_dir_2" . DIRECTORY_SEPARATOR  . "subdir" . DIRECTORY_SEPARATOR . 'anotherClass.inc');

	}

}
