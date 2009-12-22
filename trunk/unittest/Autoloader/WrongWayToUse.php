<?php
/**
 * 本测试文档演示了LtAutoloader的错误使用方法
 * 不要按本文档描述的方式使用LtAutoloader
 */
class AutoloaderTest extends PHPUnit_Framework_TestCase
{
	/**
	 * 传入的数组是二维数组：以一个数组形式传入目录名，但至少有一个元素是数组
	 * @expectedException Exception
	 */
	public function testPassMixArrayParameter()
	{
		
	}

	/**
	 * 以多个参数形式传入，但至少有一个参数是数组
	 * @expectedException Exception
	 */
	public function testParameterHasOneArrayAndOther()
	{
		
	}

	/**
	 * 路径名不是目录 （不解决）
	 * @expectedException Exception
	 */
	public function testParameterIsNotDirectory()
	{
		
	}

	/**
	 * 目录名带空格 （不解决）
	 * @expectedException Exception
	 */
	public function testDirNameWithBlank()
	{
		
	}

	/**
	 * 类和类重名 （不解决）
	 * @expectedException Exception
	 */
	public function testDumplicateNameOfClasses()
	{
		
	}

	/**
	 * 接口和接口重名 （不解决）
	 * @expectedException Exception
	 */
	public function testDumplicateNameOfInterfaces()
	{
		
	}
}