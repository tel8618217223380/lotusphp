<?php
/**
 * 本测试文档演示了LtAutoloader的错误使用方法
 * 不要按本文档描述的方式使用LtAutoloader
 */
include dirname(__FILE__) . DIRECTORY_SEPARATOR . "AutoloaderProxy.php";
class WrongWayToUseAutoloader extends PHPUnit_Framework_TestCase
{
	/**
	 * 路径名不是目录 （不解决）
	 * @expectedException PHPUnit_Framework_Notice
	 */
	public function testAddDirs1()
	{
		$autoloader = new LtAutoloader();
		$autoloader->addDirs(__FILE__);
	}

	/**
	 * 目录名带空格 （不解决）
	 * @expectedException PHPUnit_Framework_Notice
	 */
	public function testDirNameWithSpace()
	{
		$autoloader = new LtAutoloader();
		$autoloader->addDirs("./dirname with space");
	}

	/**
	 * 类和类重名 （不解决）
	 * @expectedException PHPUnit_Framework_Notice
	 */
	public function testDumplicateNameOfClasses()
	{
		$autoloader = new LtAutoloaderProxy();
		$autoloader->addClass("ClassA", __FILE__);
		$autoloader->addClass("classa", __FILE__);
	}

	/**
	 * 函数和函数重名 （不解决）
	 * @expectedException PHPUnit_Framework_Notice
	 */
	public function testDumplicateNameOfFunctions()
	{
		$autoloader = new LtAutoloaderProxy();
		$autoloader->addFunction("Function1", __FILE__);
		$autoloader->addFunction("function1", __FILE__);
	}
}