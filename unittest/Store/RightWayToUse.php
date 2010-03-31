<?php
/**
 * 本测试文档演示了LtStore的正确使用方法 
 * 按本文档操作一定会得到正确的结果
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "common.inc.php";
class RightWayToUseStore extends PHPUnit_Framework_TestCase
{

	public function testMostUsedWayLtStoreMemory()
	{
		$storeHandle = new LtStoreMemory;

		$this->assertTrue($storeHandle->add("test_key", "test_value"));
		$this->assertEquals("test_value", $storeHandle->get("test_key"));
		$this->assertTrue($storeHandle->update("test_key", "new_value"));
		$this->assertEquals("new_value", $storeHandle->get("test_key"));
		$this->assertTrue($storeHandle->del("test_key"));
		$this->assertFalse($storeHandle->get("test_key")); 
		// 删除、更新不存在的key
		$this->assertFalse($storeHandle->del("some_key_not_exists"));
		$this->assertFalse($storeHandle->update("some_key_not_exists", "any value")); 
		// 添加重复的key
		$this->assertTrue($storeHandle->add("key1", "value1"));
		$this->assertFalse($storeHandle->add("key1", "value1"));
		$storeHandle->del("key1");
	}

	public function testMostUsedWayLtStoreFile()
	{
		$storeHandle = new LtStoreFile;

		$this->assertTrue($storeHandle->add("test_key", "test_value"));
		$this->assertEquals("test_value", $storeHandle->get("test_key"));
		$this->assertTrue($storeHandle->update("test_key", "new_value"));
		$this->assertEquals("new_value", $storeHandle->get("test_key"));
		$this->assertTrue($storeHandle->del("test_key"));
		$this->assertFalse($storeHandle->get("test_key")); 
		// 删除、更新不存在的key
		$this->assertFalse($storeHandle->del("some_key_not_exists"));
		$this->assertFalse($storeHandle->update("some_key_not_exists", "any value")); 
		// 添加重复的key
		$this->assertTrue($storeHandle->add("key1", "value1"));
		$this->assertFalse($storeHandle->add("key1", "value1"));
		$storeHandle->del("key1");
	}


	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}
}
