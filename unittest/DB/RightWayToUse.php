<?php
class RightWayToUseDb extends PHPUnit_Framework_TestCase
{
	/**
	 * 最常用的使用方式（推荐）
	 * 
	 * LtDb要求：
	 *  # 以这样的流程使用LtDb：
	 *    1. new LtDb()
	 *    2. LtDb->sysHash = array(), LtDb->adapter = "apc" //这步是建议的，而非必须的，用默认值也能运行
	 *    3. init()
	 *
	 * 本测试用例期望效果：
	 * 能成功通过query()接口存取数据
	 */
	public function testMostUsedWay()
	{
		$cache = new LtDb;
		$cache->conf->adapter = "file";
		$cache->init();
		
		$this->assertTrue($cache->add(1, "This is thread 1"));
		$this->assertEquals($cache->get(1), "This is thread 1");
		$this->assertTrue($cache->del(1));
		$this->assertFalse($cache->get(1));
	}

	public function __construct()
	{
		parent::__construct();
		$this->adapterList = array(
			//"$adapter" => $options
			//"apc" => null,
			//"eAccelerator" => null, //ea不支持命令行模式
			"file" => null,
			"phps" => null,
			//"xcache" => null,
			//"memcached" => null,
		);
		$this->testDataList = array(
			//$key => value
			1 => 2,
			1.1 => null,
			-1 => "",
			"array" => array(1,2,4),
			"object" => new LtDb(), //file cache可以通过这个测试
			"test_key" => "test_value",
		);
	}

	public function getCacheHandle($adapter, $options = null)
	{
		$cache = new LtDb;
		$cache->conf->adapter = $adapter;
		if ($options)
		{
			$cache->conf->options = $options;
		}
		$cache->init();
		return $cache;
	}

	/**
	 * 基本功能测试
	 */
	public function testBase()
	{		
		foreach ($this->adapterList as $ad => $op)
		{
			$ch = $this->getCacheHandle($ad, $op);
			foreach ($this->testDataList as $k => $v)
			{
				$this->assertTrue($ch->add($k, $v));
				$this->assertEquals($ch->get($k), $v);
				$this->assertTrue($ch->update($k, 0));
				$this->assertEquals($ch->get($k), 0);
				$this->assertTrue($ch->update($k, $v));
				$this->assertEquals($ch->get($k), $v);
				$this->assertTrue($ch->del($k));
				$this->assertFalse($ch->get($k));
			}
		}
	}
}
