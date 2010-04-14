<?php
/**
 * 本测试文档演示了Lotus的错误使用方法
 * 不要按本文档描述的方式使用Lotus
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "common.inc.php";
class WrongWayToUseLotus extends PHPUnit_Framework_TestCase
{
	/**
	 * 没有设置proj_dir
	 * 
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testNotSetProjDir()
	{
		$lotus = new Lotus();
		//$lotus->option['proj_dir'] = dirname(__FILE__) . '/proj_dir/';
		$lotus->option['app_dir'] = dirname(__FILE__) . '/proj_dir/';
		$lotus->init();
	}
	/**
	 * 没有设置app_dir
	 * 
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testNotSetAppDir()
	{
		$lotus = new Lotus();
		$lotus->option['proj_dir'] = dirname(__FILE__) . '/proj_dir/';
		//$lotus->option['app_dir'] = dirname(__FILE__) . '/proj_dir/';
		$lotus->init();
	}
	protected function setUp()
	{
	}
	protected function tearDown()
	{
	}
}
