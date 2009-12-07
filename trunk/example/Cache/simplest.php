<?php
/*
 * 加载Cache类文件
 * 加载的类很多，且需要注意先后顺序，推荐使用LtAutoloader自动加载
 */
$lotusHome = dirname(dirname(dirname(__FILE__)));
include $lotusHome . "/runtime/Cache/Cache.php";
include $lotusHome . "/runtime/Cache/CacheConfig.php";
include $lotusHome . "/runtime/Cache/adapter/CacheAdapter.php";
include $lotusHome . "/runtime/Cache/adapter/CacheAdapterApc.php";
include $lotusHome . "/runtime/Cache/adapter/CacheAdapterEAccelerator.php";
include $lotusHome . "/runtime/Cache/adapter/CacheAdapterPhps.php";
include $lotusHome . "/runtime/Cache/adapter/CacheAdapterXcache.php";
/* 
 * 加载Cache类文件
 */

/*
 * 使用apc
 */
$cache = new LtCache;
$cache->conf->adapter= "apc";
$cache->init();
if(!$cache->get("test_key"))
{
	$cache->add("test_key", "hello apc");
}
echo $cache->get("test_key");

/*
 * 使用phps(serialize)
 */
$cache->conf->adapter= "phps";
$cache->conf->options= array("cache_file_root" => dirname(__FILE__) . '\phps_files');
$cache->init();
if(!$cache->get("test_key"))
{
	$cache->add("test_key", "hello phps");
}
echo $cache->get("test_key");