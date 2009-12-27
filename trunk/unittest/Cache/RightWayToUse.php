<?php
/**
 * @todo 测试用户通过数组传入namespaceMapping时能否正常工作
 * @todo 自行实现的phps/file cache，注意：
 *       1. update，del时检查key是否存在 2.add时，如果该key已经存在，但已过期，也允许add
 */
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
			"object" => new LtCache(), //file cache可以通过这个测试
			"test_key" => "test_value",
		);
	}

	public function getCacheHandle($adapter, $options = null)
	{
		$cache = new LtCache;
		$cache->conf->adapter = $adapter;
		if ($options)
		{
			$cache->conf->options = $options;
		}
		$cache->init();
		return $cache;
	}
	/**
	基本功能测试
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

	/**
	测试多个namespace的用法
	*/
	public function testNameSpace()
	{
		$ttl   = 0;
		$key   = 'key';
		$v1    = 'data1';
		$v2    = 'data2';
		$v3    = 'data3';
		$v4    = 'data4';
		$v5    = 'data5';
		$v6    = 'data6';
		$name1 = '';
		$name2 = 'namespace2';
		$name3 = 'namespace3';
		foreach ($this->adapterList as $ad => $op)
		{
			$ch = $this->getCacheHandle($ad, $op);
			$this->assertTrue($ch->add($key, $v1, $ttl, $name1));
			$this->assertTrue($ch->add($key, $v2, $ttl, $name2));
			$this->assertTrue($ch->add($key, $v3, $ttl, $name3));

			$this->assertEquals($ch->get($key, $name1), $v1);
			$this->assertEquals($ch->get($key, $name2), $v2);
			$this->assertEquals($ch->get($key, $name3), $v3);

			$this->assertTrue($ch->update($key, 0, $ttl, $name1));
			$this->assertTrue($ch->update($key, 0, $ttl, $name2));
			$this->assertTrue($ch->update($key, 0, $ttl, $name3));

			$this->assertEquals($ch->get($key, $name1), 0);
			$this->assertEquals($ch->get($key, $name2), 0);
			$this->assertEquals($ch->get($key, $name3), 0);

			$this->assertTrue($ch->update($key, $v4, $ttl, $name1));
			$this->assertTrue($ch->update($key, $v5, $ttl, $name2));
			$this->assertTrue($ch->update($key, $v6, $ttl, $name3));

			$this->assertEquals($ch->get($key, $name1), $v4);
			$this->assertEquals($ch->get($key, $name2), $v5);
			$this->assertEquals($ch->get($key, $name3), $v6);

			$this->assertTrue($ch->del($key, $name1));
			$this->assertTrue($ch->del($key, $name2));
			$this->assertTrue($ch->del($key, $name3));

			$this->assertFalse($ch->get($key, $name1));
			$this->assertFalse($ch->get($key, $name2));
			$this->assertFalse($ch->get($key, $name3));
		}
	}

	/**
	通过数组传入 namespaceMapping
	*/
	public function testNamespaceMapping()
	{
		$ttl   = 0;
		$key   = 'key';
		$v1    = 'data1';
		$v2    = 'data2';
		$v3    = 'data3';
		$v4    = 'data4';
		$v5    = 'data5';
		$v6    = 'data6';
		$name1 = '';
		$name2 = 'namespace2';
		$name3 = 'namespace3';

		foreach ($this->adapterList as $ad => $op)
		{
			$ch = $this->getCacheHandle($ad, $op);
			// 通过数组传入 namespaceMapping
			$ch->namespaceMapping = array('default'=>'default', ''=>'', 'namespace2'=>'namespace2', 'namespace3'=>'namespace3');

			$this->assertTrue($ch->add($key, $v1, $ttl, $name1));
			$this->assertTrue($ch->add($key, $v2, $ttl, $name2));
			$this->assertTrue($ch->add($key, $v3, $ttl, $name3));

			$this->assertEquals($ch->get($key, $name1), $v1);
			$this->assertEquals($ch->get($key, $name2), $v2);
			$this->assertEquals($ch->get($key, $name3), $v3);

			$this->assertTrue($ch->update($key, 0, $ttl, $name1));
			$this->assertTrue($ch->update($key, 0, $ttl, $name2));
			$this->assertTrue($ch->update($key, 0, $ttl, $name3));

			$this->assertEquals($ch->get($key, $name1), 0);
			$this->assertEquals($ch->get($key, $name2), 0);
			$this->assertEquals($ch->get($key, $name3), 0);

			$this->assertTrue($ch->update($key, $v4, $ttl, $name1));
			$this->assertTrue($ch->update($key, $v5, $ttl, $name2));
			$this->assertTrue($ch->update($key, $v6, $ttl, $name3));

			$this->assertEquals($ch->get($key, $name1), $v4);
			$this->assertEquals($ch->get($key, $name2), $v5);
			$this->assertEquals($ch->get($key, $name3), $v6);

			$this->assertTrue($ch->del($key, $name1));
			$this->assertTrue($ch->del($key, $name2));
			$this->assertTrue($ch->del($key, $name3));

			$this->assertFalse($ch->get($key, $name1));
			$this->assertFalse($ch->get($key, $name2));
			$this->assertFalse($ch->get($key, $name3));
		}
	}


	/**
	测试ttl
	*/
	public function testTTL()
	{
		$ttl_add = 0;
		$ttl_update = 2;
		foreach ($this->adapterList as $ad => $op)
		{
			$ch = $this->getCacheHandle($ad, $op);
			foreach ($this->testDataList as $k => $v)
			{
				$this->assertTrue($ch->add($k, $v, $ttl_add));
				sleep(1);
				$this->assertEquals($ch->get($k), $v);
				$this->assertTrue($ch->update($k, $v, $ttl_update));
				sleep(1);
				$this->assertEquals($ch->get($k), $v);
				sleep(2);
				$this->assertFalse($ch->get($k));
			}
		}
	}
}
