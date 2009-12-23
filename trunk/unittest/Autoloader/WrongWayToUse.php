<?php
/**
 * 本测试文档演示了LtAutoloader的错误使用方法
 * 不要按本文档描述的方式使用LtAutoloader
 */
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
		new LtAutoloader("class_with_same_name");
	}

	/**
	 * 接口和接口重名 （不解决）
	 * @expectedException Exception
	 */
	public function testDumplicateNameOfInterfaces()
	{
		new LtAutoloader("interface_with_same_name");
	}

	/**
	 * 类和接口重名 （不解决）
	 * @expectedException Exception
	 */
	public function testDumplicateNameOfClassAndInterface()
	{
		new LtAutoloader("class_interface_with_same_name");
	}

	/**
	 * 实例化LtAutoloader时没带参数，没有给LtAutoloader->fileMapping赋值就调用LtAutoloader->startToAutoload()了
	 * @expectedException Exception
	 */
	public function testDumplicateNameOfClassAndInterface()
	{
		new LtAutoloader("class_interface_with_same_name");
	}
}