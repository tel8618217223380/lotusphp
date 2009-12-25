<?php
class RightWayToUseCache extends PHPUnit_Framework_TestCase
{
	/**
	 * 最常用的使用方式（推荐）
	 * 
	 * LtCache要求：
	 *  # 以这样的流程使用LtCache：
	 *    1. new LtCache()
	 *    2. LtCache->sysHash = array(), LtCache->adapter = "apc" //这步是建议的，而非必须的，用默认值也能运行
	 *    3. init()
	 *
	 * 本测试用例期望效果：
	 * 能成功通过add(), get(), del()接口读写数据
	 */
	public function testMostUsedWay()
	{
		$cache = new LtCache;
		$cache->sysHash = array(
			"thread" => 1,
			"post" => 2,
			"user" => 3,
		);
		$cache->init();
		
		$this->assertTrue($cache->add("thread", 1, "LtCache is good!"));
		$this->assertEquals($cache->get("thread", 1), "LtCache is good!");
		$this->assertEquals($cache->del("thread", 1));
		$this->assertFalse($cache->get("thread", 1));
	}
}