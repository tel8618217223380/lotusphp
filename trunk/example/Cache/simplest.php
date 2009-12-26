<?php
/**
 * 加载Cache类文件
 * 加载的类很多，且需要注意先后顺序，推荐使用LtAutoloader自动加载
 */
$lotusHome = substr(__FILE__, 0, strpos(__FILE__, "example"));
include $lotusHome . "/runtime/Cache/Cache.php";
include $lotusHome . "/runtime/Cache/CacheConfig.php";
include $lotusHome . "/runtime/Cache/adapter/CacheAdapter.php";
include $lotusHome . "/runtime/Cache/adapter/CacheAdapterApc.php";
include $lotusHome . "/runtime/Cache/adapter/CacheAdapterEAccelerator.php";
include $lotusHome . "/runtime/Cache/adapter/CacheAdapterPhps.php";
include $lotusHome . "/runtime/Cache/adapter/CacheAdapterXcache.php";
/**
 * 加载Cache类文件
 */
$cache = new LtCache;

/**
 * 默认使用phps(serialize)作为Cache存储引擎，参见CacheConfig.php里面的默认值设置
 * $cache->conf->adapter= "phps";
 * $cache->conf->options= array("cache_file_root" => dirname(__FILE__) . '\phps_files');
 */
$cache->init();
if(!$cache->get("test_key"))
{
	$cache->add("test_key", "hello phps");
}
echo "\n" . $cache->get("test_key");

/**
 * 使用apc
 */
$cache->conf->adapter= "apc";
$cache->init();
if(!$cache->get("test_key"))
{
	$cache->add("test_key", "hello apc");
}
echo "\n" . $cache->get("test_key");
