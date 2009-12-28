<?php
/**
 * 本测试文档演示了LtConfig的错误使用方法
 * 不要按本文档描述的方式使用LtConfig
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "include_classes.inc";
class WrongWayToUseConfig extends PHPUnit_Framework_TestCase
{
	/**
	 * 1. 使用config file中未定义的key
	 * 2. config file中没有return array
	 */
}
