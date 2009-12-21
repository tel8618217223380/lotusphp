<?php
class AutoloaderTest extends PHPUnit_Framework_TestCase
{
	/**
	 * 以一个数组形式传入路径
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
	 * 以一个字串参数形式传入路径
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
	 * 以多个字串参数形式传入路径
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
	 * 期望效果：参数为空不报错，但不生成fileMapping（参见性能提高）
	 */
	public function testPassNoParameter()
	{
		$autoloaderToTest = new LtAutoloader();
		$this->assertEquals($autoloaderToTest->fileMapping, null);
	}
}