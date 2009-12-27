<?php
/**
 * 本测试文档演示了LtCache的错误使用方法
 * 不要按本文档描述的方式使用LtCache
 */
class WrongWayToUseCache extends PHPUnit_Framework_TestCase
{
	/**
	 * 1. 使用尚未支持的adapter，如memcached, sqlite
	 * 2. 使用需要option的adapter（如file, phps），却没传入正确的option
	 * 3. 同一namespace下，key冲突且都未过期
	 * 4. 定义了namespaceMapping，add/get/del/update时却不传入namespace
	 * 5. 定义了namespaceMapping，add/get/del/update时传入了未定义的namespace
	 */
}