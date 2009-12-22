<?php
class AutoloaderTest extends PHPUnit_Framework_TestCase
{
	/**
	 * 以一个数组形式传入路径，数组每个元素都是字串
	 * 期望效果：在class_exists("HelloWorld")的时候自动把HelloWorld.php加载进来
	 */
	public function testPassArrayParameter()
	{
		new LtAutoloader(array(
			dirname(__FILE__) . DIRECTORY_SEPARATOR . "class_dir_1" . DIRECTORY_SEPARATOR,
			dirname(__FILE__) . DIRECTORY_SEPARATOR . "class_dir_2" . DIRECTORY_SEPARATOR,
		));
		$this->assertTrue(class_exists("GoodBye"));
	}

	/**
	 * 以一个数组形式传入路径，有至少一个元素是数组（不解决）
	 * 期望效果：
	 */
	public function testPassMixArrayParameter()
	{
		
	}

	/**
	 * 以多个数组形式传入路径（不解决）
	 * 期望效果：
	 */
	public function testPassSeveralArrayParameter()
	{
		
	}

	/**
	 * 以一个字符参数形式传入路径
	 * 期望效果：当以new Autoloader($path)时，等同于new Autoloader(array($path))
	 */
	public function testPassOneStringParameter()
	{
		$dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . "class_dir_1" . DIRECTORY_SEPARATOR;
		$autoloaderToTest = new LtAutoloader($dir);
		$autoloaderStandard = new LtAutoloader(array($dir));
		$this->assertEquals($autoloaderToTest->fileMapping, $autoloaderStandard->fileMapping);
	}

	/**
	 * 以多个字符参数形式传入路径
	 * 期望效果：当以new Autoloader($path1, $path2)时，等同于new Autoloader(array($path1, $path2))
	 */
	public function testPassSeveralStringParameter()
	{
		$autoloaderToTest = new LtAutoloader(
			dirname(__FILE__) . DIRECTORY_SEPARATOR . "class_dir_1" . DIRECTORY_SEPARATOR,
			dirname(__FILE__) . DIRECTORY_SEPARATOR . "class_dir_2" . DIRECTORY_SEPARATOR
		);
		$autoloaderStandard = new LtAutoloader(array(
			dirname(__FILE__) . DIRECTORY_SEPARATOR . "class_dir_1" . DIRECTORY_SEPARATOR,
			dirname(__FILE__) . DIRECTORY_SEPARATOR . "class_dir_2" . DIRECTORY_SEPARATOR
		));
		$this->assertEquals($autoloaderToTest->fileMapping, $autoloaderStandard->fileMapping);
	}

	/**
	 * 参数为空
	 * 期望效果：参数为空不报错，但不生成fileMapping
	 */
	public function testPassNoParameter()
	{
		$autoloaderToTest = new LtAutoloader();
		$this->assertEquals($autoloaderToTest->fileMapping, null);
	}

	/**
	 * 路径名是目录:相对路径
	 * 期望效果：在class_exists("HelloWorld")的时候自动把HelloWorld.php加载进来
	 */
	public function testDirectoryRelativePath()
	{
		new LtAutoloader(array(
			"Autoloader/class_dir_1",
			"Autoloader/class_dir_2",
		));
		$this->assertTrue(class_exists("GoodBye"));
	}

	/**
	 * 路径名是目录:绝对路径
	 * 此处请改成您环境当中的路径
	 * 期望效果：在class_exists("HelloWorld")的时候自动把HelloWorld.php加载进来
	 */
	public function testDirectoryAbsolutePath()
	{
		new LtAutoloader(array(
			"D:/lotus/unittest/Autoloader/class_dir_1",
			"D:/lotus/unittest/Autoloader/class_dir_2",
		));
		$this->assertTrue(class_exists("GoodBye"));
	}

	/**
	 * 路径名是目录:目录名带拖尾斜线
	 * 期望效果：在class_exists("HelloWorld")的时候自动把HelloWorld.php加载进来
	 */
	public function testDirectoryWithLine()
	{
		new LtAutoloader(array(
			"Autoloader/class_dir_1/",
			"Autoloader/class_dir_2/",
		));
		$this->assertTrue(class_exists("GoodBye"));
	}

	/**
	 * 路径名是目录:目录名不带拖尾斜线
	 * 期望效果：在class_exists("HelloWorld")的时候自动把HelloWorld.php加载进来
	 */
	public function testDirectoryWithoutLine()
	{
		new LtAutoloader(array(
			"Autoloader/class_dir_1",
			"Autoloader/class_dir_2",
		));
		$this->assertTrue(class_exists("GoodBye"));
	}

	/**
	 * 路径名是文件:相对路径 （不解决）
	 * @expectedException Exception
	 * 期望效果：在class_exists("HelloWorld")的时候自动把HelloWorld.php加载进来
	 */
	public function testFileRelativePath()
	{
		
	}

	/**
	 * 路径名是文件:绝对路径 （不解决）
	 * @expectedException Exception
	 * 期望效果：在class_exists("HelloWorld")的时候自动把HelloWorld.php加载进来
	 */
	public function testFileAbsolutePath()
	{
		
	}

	/**
	 * 路径名是既不是文件也不是目录 （不解决）
	 * @expectedException Exception
	 * 期望效果：在class_exists("HelloWorld")的时候自动把HelloWorld.php加载进来
	 */
	public function testNotFileOrNotDirectory()
	{
		
	}

	/**
	 * 路径名是目录:目录名带空格
	 * 期望效果：在class_exists("HelloWorld")的时候自动把HelloWorld.php加载进来
	 * 在windows下面没问题，但是在Linux里会有问题的
	 */
	public function testDirectoryWithBlank()
	{
		new LtAutoloader(array(
			"Autoloader/class_dir 1/",
			"Autoloader/class_dir 2/",
		));
		$this->assertTrue(class_exists("GoodBye"));
	}

	/**
	 * 路径名是目录:目录名不带空格
	 * 期望效果：在class_exists("HelloWorld")的时候自动把HelloWorld.php加载进来
	 */
	public function testDirectoryWithoutBlank()
	{
		new LtAutoloader(array(
			"Autoloader/class_dir_1/",
			"Autoloader/class_dir_2/",
		));
		$this->assertTrue(class_exists("GoodBye"));
	}
}