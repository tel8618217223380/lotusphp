<?php
/**
* ����RBAC���ļ�
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
* ��ʼ��RBAC
*/
$rbac = new LtRbac;
$rbac -> conf -> adapter = "file";
$rbac -> conf -> options = new options;
$rbac -> init();

// ��ӽ�ɫ
// $rbac->addRole('test','����');
// ɾ����ɫ
// $rbac->delRole('test');
print_r($rbac->getRole());

// ���Ȩ����Դ
// $rbac->addResource('test/test','����');
// ɾ��Ȩ����Դ
// $rbac->delResource('test/test');
print_r($rbac->getResource());

// �����ɫȨ��
// $rbac->allow('test',array('test/*','test1','test2','test3'));
// ��ֹ��ɫȨ��
// $rbac->deny('test',array('t1','t2','t3'));
// ɾ����ɫȨ��
// $rbac->delRoleAcl('test');
print_r($rbac->getAcl());

// ����û�
// $rbac->addUser('lotusphp','SYSTEM,ADMIN,POWER_USER');
// ɾ���û�
// $rbac->delUser('lotusphp');
// ��ȡ�û���ɫ
// echo $rbac->getUserRole('zhaoyi');
print_r($rbac->getUserRole());

// ����û�����Դ�ķ���Ȩ��
// var_dump($rbac -> checkAcl('zhaoyi', 'admin/killa'));