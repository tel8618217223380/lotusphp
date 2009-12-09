<?php
/**
 * 加载Router类文件
 */
$lotusHome = dirname(dirname(dirname(__FILE__)));
include $lotusHome . "/runtime/RBAC/RBAC.php"; 

class demo
{
	public $acl;

	public function __construct()
	{

	}

	public function Index()
	{
		// 用户角色
		$userRole['zhaoyi'] = 'Administrators,Users';
		
		// 角色信息
		$role['*'] = '任意角色';
		$role['Administrators'] = '管理员';
		$role['Users'] = '例子用户';
		$role['Guests'] = '例子来宾';
		$role['OWNER'] = '所有者';
		$role['USER'] = '用户';
		$role['ANONYMOUS'] = '匿名';

		// 角色权限
		$acl['allow']['*'][] = 'Index/Index';
		$acl['deny']['*'][] = '';

		$acl['allow']['Administrators'][]='admin/*';
		$acl['allow']['Administrators'][]='admin/test';
		$acl['allow']['Administrators'][]='User/AddUser';

		$acl['allow']['Users'][]='User/View';
		$acl['allow']['Users'][]='User/Signin';
		$acl['allow']['Users'][]='User/DoSignin';
		$acl['deny']['Users'][]='User/AddUser';
		
		$acl['allow']['Guests'][]='*/Index';
		$acl['deny']['Guests'][]='*';

		// 权限资源
		$permissions['Index/Index'] = '访问首页';
		$permissions['*/Index'] = '任意module下的index动作';
		$permissions['User/View'] = '用户浏览';
		$permissions['User/Signin'] = '登陆页面';
		$permissions['User/DoSignin'] = '提交登陆表单';
		$permissions['admin/*'] = 'admin module下的任意action';
		$permissions['delete file'] = '删除文件';

		
		$this->acl = new LtRBAC();
		$this->acl->setUserRole($userRole);
		$this->acl->setRole($role);
		$this->acl->setAcl($acl);
		$this->acl->setPermissions($permissions);

		var_dump($this->acl -> checkAcl('User/View'));		
	}

}

$demo = new demo();
$demo->index();
