<?php
/**
 * 本测试文档演示了LtCache的错误使用方法
 * 不要按本文档描述的方式使用LtCache
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "include_classes.inc";
class WrongWayToUseCache extends PHPUnit_Framework_TestCase
{
	/**
	 * 1. 使用尚未支持的adapter，如memcached, sqlite, 或者把conf->adapter置为null了（本来默认是phps）
	 * 2. 使用需要option的adapter（如file, phps），却没传入正确的option
	 * 3. 同一namespace下，key冲突且都未过期
	 * 4. 定义了namespaceMapping，add/get/del/update时却不传入namespace
	 * 5. 定义了namespaceMapping，add/get/del/update时传入了未定义的namespace
	 */

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
	 * 使用需要option的adapter（如file, phps），却没传入正确的option 
	 * 
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testOption()
	{
		$cache = new LtCache;
		$cache->conf->adapter = "phps"; // 本来默认是phps
		$cache->conf->options = array("cache_file_path" => "/tmp/LtCache");
		$cache->init();
		$cache->del('Must set path');
	}
}