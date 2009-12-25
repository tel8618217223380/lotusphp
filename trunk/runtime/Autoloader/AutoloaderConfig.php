<?php
class LtAutoloaderConfig
{
	/**
	 * 是否自动加载定义了函数的文件
	 * 
	 * 可选项：
	 *  # true   自动加载
	 *  # false  跳过函数，只自动加载定义了class或者interface的文件
	 * @var boolean
	 */
	public $loadFunction = true;

	/**
	 * 要扫描的文件类型
	 * 
	 * 若该属性设置为array("php","inc","php3")，则扩展名为"php","inc","php3"的文件会被扫描，其它扩展名的文件会被忽略
	 * @var array
	 */
	public $allowFileExtension = array("php", "inc");

	/**
	 * 不扫描的目录
	 * 
	 * 若该属性设置为array(".svn", ".setting")，则所有名为".setting"的目录也会被忽略
	 * @var array
	 */
	public $skipDirNames = array('.svn');
}