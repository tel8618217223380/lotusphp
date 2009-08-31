<?php
/* 
 * Load rumtime class start
 */
$lotusHome = dirname(dirname(dirname(dirname(__FILE__))));
include $lotusHome . "/runtime/Cache/Cache.php";
include $lotusHome . "/runtime/Cache/CacheAdapter.php";
include $lotusHome . "/runtime/Cache/CacheAdapterApc.php";
include $lotusHome . "/runtime/Cache/CacheAdapterXcache.php";
/* 
 * Load rumtime class end
 */

$cache = new LtCache;
$cache->init("xcache");
$cache->add("test_key","hello cache");
echo $cache->get("test_key");