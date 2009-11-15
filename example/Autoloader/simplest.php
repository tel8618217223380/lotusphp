<?php
/*
 * 加载Autoloader类文件
 */
$lotusHome = dirname(dirname(dirname(__FILE__)));
include $lotusHome . "/runtime/Autoloader/Autoloader.php";

/*
 * 初始化Autoloader类
 * 将当前目录下的Classes和function目录加到“自动加载目录列表中”
 */
$directories = array("Classes", "function");
$autoloader = new LtAutoloader($directories);

/*
 * 看看有哪些文件被包含进来了
 * function目录下的文件全都自动饱含进来了，他们并没有被用到
 * 非class(没有定义class/interface的文件)会被LtAutoloader全部自动包含进来，不是按需加载
 */
print_r(get_included_files());

/*
 * 初始化完成，开始享受Autoloader的便利
 * 之前你并没有手工include/require Classes和function目录的任何文件
 */
$hello = new HelloWorld();//当你new HelloWorld()的时候，Autoloader便会为你自动把HelloWorld.php包含进来
$hello->sayHello();

/*
 * 再看看有哪些文件被包含进来了
 * 多了个Classes/HelloWorld.php，这是被"$hello = new HelloWorld();"触发的
 * class文件(定义了class/interface的文件)是按需加载（用到的时候才包含进来）的
 */
print_r(get_included_files());
