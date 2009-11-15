<?php
/*
 * 加载Autoloader类文件
 */
$lotusHome = dirname(dirname(dirname(__FILE__)));
include $lotusHome . "/runtime/Autoloader/Autoloader.php";

/*
 * 将扫描目录得到的class file mapping缓存到内存中
 */
$cacheKey = "autoloader_cache_key";
if ($cachedFileMapping = apc_fetch($cacheKey))//若从apc中获取到了class file mapping，则不要扫描目录了
{
	$autoloader = new LtAutoloader();
  $autoloader->setFileMapping($autoloader);
  $autoloader->init();
}
else//若apc中没有class file mapping，则扫描目录获得之，并存入apc
{
  $directories = array("Classes");
	$autoloader = new LtAutoloader($directories);
	$fileMapping = $autoloader->getFileMapping();
	apc_add($cacheKey, $fileMapping);
}

/*
 * 初始化完毕，正常使用
 */
$hello = new HelloWorld();
$hello->sayHello();