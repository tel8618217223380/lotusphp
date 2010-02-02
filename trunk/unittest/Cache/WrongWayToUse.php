<?php
/**
 * 本测试文档演示了LtCache的错误使用方法 
 * 不要按本文档描述的方式使用LtCache
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "common.inc.php";
class WrongWayToUseCache extends PHPUnit_Framework_TestCase
{
	/**
	 * 使用尚未支持的adapter，如memcached, sqlite,
	 * 
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testInvalidAdapter()
	{
		$cache = new LtCache;
		$cache->conf->adapter = "db2"; // 使用尚未支持的 adapter
		$cache->init();
	}
	/**
	 * 把conf->adapter置为null了
	 * 
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testNullAdapter()
	{
		$cache = new LtCache;
		$cache->conf->adapter = null; // 本来默认是phps
		$cache->init();
	}
	/**
	 * phps没传入正确的option 名称
	 * 
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testPhpsOption()
	{
		$cache = new LtCache;
		$cache->conf->adapter = "phps";
		$cache->conf->options = array("cache_file_path" => "/tmp/LtCache");
		$cache->init();
		$cache->del('Must set path');
	}
	/**
	 * phps没传入正确的option 值
	 * 
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testPhpsOptionVal()
	{
		$cache = new LtCache;
		$cache->conf->adapter = "phps";
		$cache->conf->options = array("cache_file_root" => "/t?mp/LtCache");
		$cache->init();
		$cache->del('Must set path');
	}
	/**
	 * file没传入正确的option 名称
	 * 
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testFileOption()
	{
		$cache = new LtCache;
		$cache->conf->adapter = "file";
		$cache->conf->options = array("cache_file_path" => "/tmp/LtCache");
		$cache->init();
		$cache->del('Must set path');
	}
	/**
	 * file没传入正确的option 值
	 * 
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testFileOptionVal()
	{
		$cache = new LtCache;
		$cache->conf->adapter = "file";
		$cache->conf->options = array("cache_file_root" => "/t?mp/LtCache");
		$cache->init();
		$cache->del('Must set path');
	}
	/**
	 * 同一namespace下，key冲突且都未过期
	 * 
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testFileKeyConflict()
	{
		$adapter = 'file';
		$cache = new LtCache;
		$cache->conf->adapter = $adapter;
		$cache->init();
		$cache->add('key', 'value', 2, 'samename');
		$cache->add('key', 'value', 0, 'samename');
	}
	/**
	 * 同一namespace下，key冲突且都未过期
	 * 
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testPhpsKeyConflict()
	{
		$adapter = 'phps';
		$cache = new LtCache;
		$cache->conf->adapter = $adapter;
		$cache->init();
		$cache->add('key', 'value', 2, 'samename');
		$cache->add('key', 'value', 0, 'samename');
	}
	/**
	 * 删除不存在的key
	 * 
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testFileKeyDel()
	{
		$adapter = 'file';
		$cache = new LtCache;
		$cache->conf->adapter = $adapter;
		$cache->init();
		$cache->del('key_not_exists', 'value', 2, 'samename');
	}
	/**
	 * 删除不存在的key
	 * 
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testPhpsKeyDel()
	{
		$adapter = 'phps';
		$cache = new LtCache;
		$cache->conf->adapter = $adapter;
		$cache->init();
		$cache->del('key_not_exists', 'value', 2, 'samename');
	}
	/**
	 * 定义了namespaceMapping，add/get/del/update时却不传入namespace
	 * 
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testnamespaceMapping1()
	{
		$adapter = 'phps';
		$cache = new LtCache;
		$cache->conf->adapter = $adapter;
		$cache->namespaceMapping = array('namespace1' => 1, 'namespace2' => 2, 'namespace3' => 3);
		$cache->init();
		$cache->add('key', 'value', 2);
	}
	/**
	 * 定义了namespaceMapping，add/get/del/update时传入了未定义的namespace
	 * 
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testnamespaceMapping2()
	{
		$adapter = 'phps';
		$cache = new LtCache;
		$cache->conf->adapter = $adapter;
		$cache->namespaceMapping = array('namespace1' => 1, 'namespace2' => 2, 'namespace3' => 3);
		$cache->init();
		$cache->add('key', 'value', 2,'testname');
	}
	protected function setUp()
	{
	}
	protected function tearDown()
	{
	}
}
