<?php
/*
 * 加载Autoloader类文件
 */
$lotusHome = dirname(dirname(dirname(__FILE__)));
include $lotusHome . "/runtime/Autoloader/Autoloader.php";
include $lotusHome . "/runtime/Autoloader/AutoloaderConfig.php";

/*
 * 定义MyAutoloader类
 */
class MyAutoloader extends LtAutoloader
{
	/*
	 * 覆盖LtAutoloader->init()方法
	 * 不加载非class文件
	 */
}

/*
 * 试用MyAutoloader
 */
$directories = array("Classes", "function");
$myAutoloader = new MyAutoloader();
$myAutoloader->addDirs($directories);

/*
 * 看看有哪些文件被包含进来了
 * function目录下的文件全都没包含进来
 */
print_r(get_included_files());
