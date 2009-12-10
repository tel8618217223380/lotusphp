<?php
/**
* 加载RBAC类文件
*/
$lotusHome = dirname(dirname(dirname(__FILE__)));
include $lotusHome . "/runtime/RBAC/Rbac.php";
include $lotusHome . "/runtime/RBAC/RbacConfig.php";
include $lotusHome . "/runtime/RBAC/adapter/RbacAdapter.php";
include $lotusHome . "/runtime/RBAC/adapter/RbacAdapterFile.php";

class options
{
	public $aclfile;
	public function __construct()
	{
		$this -> aclfile = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'acl.php';
	}
}

/**
* 初始化RBAC
*/
$rbac = new LtRbac;
$rbac -> conf -> adapter = "file";
$rbac -> conf -> options = new options;
$rbac -> init();

/**
* 检查用户对资源的
*/
var_dump($rbac -> checkAcl('zhaoyi', 'admin/killa'));
