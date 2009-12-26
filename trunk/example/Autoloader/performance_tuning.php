<?php
/**
 * 加载Autoloader类文件
 */
$lotusHome = substr(__FILE__, 0, strpos(__FILE__, "example"));
include $lotusHome . "/runtime/Autoloader/Autoloader.php";
include $lotusHome . "/runtime/Autoloader/AutoloaderConfig.php";

$directories = array("Classes");
$autoloader = new LtAutoloader();
$autoloader->setAutoloadPath($directories);

/**
 * 给LtAutoloader设置存储句柄
 */
class LtCacheAdapterApc
{
	public function add($key, $value, $ttl=0)
	{
		return apc_add($key, $value, $ttl);
	}
	
	public function del($key)
	{
		return apc_delete($key);
	}
	
	public function get($key)
	{
		return apc_fetch($key);
	}
}
$autoloader->storeHandle = new LtCacheAdapterApc;

/**
 * 通常，用apc, xcache内存缓存来作为LtAutoloader的存储层
 * 如果你有在一台机器上有多个程序都在使用apc, xcache加速LtAutoloader，记得给缓存key加个前缀，以免冲突
 * 如果就一个程序使用，则可以不加前缀（默认前缀是空字串）
 */
$autoloader->storeKeyPrefix = "abc";
$autoloader->init();

/*
 * 初始化完毕，正常使用
 */
$hello = new HelloWorld();
$hello->sayHello();
