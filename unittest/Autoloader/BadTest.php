<?php
class AutoloaderTest extends PHPUnit_Framework_TestCase
{
	/**
	 * 以一个数组形式传入路径，有至少一个元素是数组（不解决）
	 * @expectedException Exception
	 */
	public function testPassMixArrayParameter()
	{
		
	}

	/**
	 * 以多个数组形式传入路径（不解决）
	 * @expectedException Exception
	 */
	public function testPassSeveralArrayParameter()
	{
		
	}

	/**
	 * 路径名是既不是文件也不是目录 （不解决）
	 * @expectedException Exception
	 */
	public function testNotFileOrNotDirectory()
	{
		
	}

	/**
	 * 路径名是目录:目录名带空格 （不解决）
	 * @expectedException Exception
	 */
	public function testPathWithBlank()
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