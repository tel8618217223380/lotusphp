<?php
/*
 * 加载Cache类文件
 * 加载的类很多，且需要注意先后顺序，推荐使用LtAutoloader自动加载
 */
$lotusHome = dirname(dirname(dirname(__FILE__)));
include $lotusHome . "/runtime/Cache/Cache.php";
include $lotusHome . "/runtime/Cache/CacheAdapter.php";
include $lotusHome . "/runtime/Cache/CacheAdapterApc.php";
include $lotusHome . "/runtime/Cache/CacheAdapterXcache.php";
/* 
 * Load rumtime class end
 */

$cache = new LtCache;
$cache->init("xcache");
$cache->add("test_key", "hello cache");
echo $cache->get("test_key");