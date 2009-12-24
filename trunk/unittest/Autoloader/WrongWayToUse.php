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
	 * @expectedException Exception
	 */
	public function testParameterIsNotDirectory()
	{
		new LtAutoloader("class_dir_1", "class_dir_2/HelloWorld.php");
	}

	/**
	 * 目录名带空格 （不解决）
	 * @expectedException Exception
	 */
	public function testDirNameWithBlank()
	{
		new LtAutoloader("zend framework");
	}

	/**
	 * 类和类重名 （不解决）
	 * @expectedException Exception
	 */
	public function testDumplicateNameOfClasses()
	{
		$autoloader = new LtAutoloaderProxy("class_with_same_name");
		$autoloader->addClass("ClassA", __FILE__);
		$autoloader->addClass("classa", __FILE__);
	}

	/**
	 * 接口和接口重名 （不解决）
	 * @expectedException Exception
	 */
	public function testDumplicateNameOfInterfaces()
	{
		$autoloader = new LtAutoloader("interface_with_same_name");
		$autoloader->init();
	}
}