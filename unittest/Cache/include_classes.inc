<?php
$lotusHome = substr(__FILE__, 0, strpos(__FILE__, "unittest"));
require $lotusHome . "runtime/Cache/Cache.php";
require $lotusHome . "runtime/Cache/CacheConfig.php";
require $lotusHome . "runtime/Cache/adapter/CacheAdapter.php";
require $lotusHome . "runtime/Cache/adapter/CacheAdapterApc.php";
require $lotusHome . "runtime/Cache/adapter/CacheAdapterEAccelerator.php";
require $lotusHome . "runtime/Cache/adapter/CacheAdapterFile.php";
require $lotusHome . "runtime/Cache/adapter/CacheAdapterPhps.php";
require $lotusHome . "runtime/Cache/adapter/CacheAdapterXcache.php";