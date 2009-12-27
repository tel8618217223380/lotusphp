<?php
$lotusHome = substr(__FILE__, 0, strpos(__FILE__, "unittest"));
require_once $lotusHome . "runtime/Cache/Cache.php";
require_once $lotusHome . "runtime/Cache/CacheConfig.php";
require_once $lotusHome . "runtime/Cache/adapter/CacheAdapter.php";
require_once $lotusHome . "runtime/Cache/adapter/CacheAdapterApc.php";
require_once $lotusHome . "runtime/Cache/adapter/CacheAdapterEAccelerator.php";
require_once $lotusHome . "runtime/Cache/adapter/CacheAdapterFile.php";
require_once $lotusHome . "runtime/Cache/adapter/CacheAdapterPhps.php";
require_once $lotusHome . "runtime/Cache/adapter/CacheAdapterXcache.php";