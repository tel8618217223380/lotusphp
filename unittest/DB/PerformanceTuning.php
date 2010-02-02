<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "common.inc.php";
require_once $lotusHome . "runtime/Cache/Cache.php";
require_once $lotusHome . "runtime/Cache/CacheAdapterFactory.php";
require_once $lotusHome . "runtime/Cache/CacheConfigBuilder.php";
require_once $lotusHome . "runtime/Cache/CacheConnectionManager.php";
require_once $lotusHome . "runtime/Cache/CacheHandle.php";
require_once $lotusHome . "runtime/Cache/Adapter/CacheAdapter.php";
require_once $lotusHome . "runtime/Cache/Adapter/CacheAdapterApc.php";
require_once $lotusHome . "runtime/Cache/Adapter/CacheAdapterEAccelerator.php";
require_once $lotusHome . "runtime/Cache/Adapter/CacheAdapterFile.php";
require_once $lotusHome . "runtime/Cache/Adapter/CacheAdapterMemcached.php";
require_once $lotusHome . "runtime/Cache/Adapter/CacheAdapterPhps.php";
require_once $lotusHome . "runtime/Cache/Adapter/CacheAdapterXcache.php";
class PerformanceTuningDb extends PHPUnit_Framework_TestCase
{
	/**
	 * 本测试展示了如何用LtCache给LtDb提高性能
	 */
	public function testPerformance()
	{
		/**
		 * 初始化LtCache，LtDb用LtCache作存储层的时候性能才会提高
		 */
	}
}
