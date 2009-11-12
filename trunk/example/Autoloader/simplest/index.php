<?php
/* 
 * 加载Autoloader类文件
 */
$lotusHome = dirname(dirname(dirname(dirname(__FILE__))));
include $lotusHome . "/runtime/Autoloader/Autoloader.php";
/* 
 * 加载Autoloader类文件
 */

/*
 * 初始化Autoloader类
 * 将example/Autoloader/simplest/Classes目录加到“自动加载目录列表中”
 */
$autoloader = new LtAutoloader;
$directories = array("Classes");
$autoloader->init($autoloader->scanDir($directories));
/*
 * 初始化Autoloader类
 */

/*
 * 初始化完成，开始享受Autoloader的便利
 * 之前你并没有手工include/require HelloWorld.php文件
 * 当你new HelloWorld()的时候，Autoloader便会为你自动把HelloWorld.php包含进来
 */
$hello = new HelloWorld();
$hello->sayHello();