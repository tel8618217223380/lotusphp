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
	public $aclFile;
	public function __construct()
	{
		$this -> aclFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'acl.php';
	}
}

/**
* 初始化RBAC
*/
$rbac = new LtRbac;
$rbac -> conf -> adapter = "file";
$rbac -> conf -> options = new options;
$rbac -> init();

// 添加角色
// $rbac->addRole('test','测试');
// 删除角色
// $rbac->delRole('test');
print_r($rbac->getRole());

// 添加权限资源
// $rbac->addResource('test/test','测试');
// 删除权限资源
// $rbac->delResource('test/test');
print_r($rbac->getResource());

// 允许角色权限
// $rbac->allow('test',array('test/*','test1','test2','test3'));
// 禁止角色权限
// $rbac->deny('test',array('t1','t2','t3'));
// 删除角色权限
// $rbac->delRoleAcl('test');
print_r($rbac->getAcl());

// 添加用户
// $rbac->addUser('lotusphp','SYSTEM,ADMIN,POWER_USER');
// 删除用户
// $rbac->delUser('lotusphp');
// 获取用户角色
// echo $rbac->getUserRole('zhaoyi');
print_r($rbac->getUserRole());

// 检查用户对资源的访问权限
// var_dump($rbac -> checkAcl('zhaoyi', 'admin/killa'));