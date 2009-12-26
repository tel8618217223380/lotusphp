<?php

$lotusHome = substr(__FILE__, 0, strpos(__FILE__, "example"));
include $lotusHome . "/runtime/RBAC/RBAC.php";

// 角色
$roles = array('Administrators','Users');

// 访问控制列表
$acl['allow']['*'][] = 'Index/Index';
$acl['deny']['*'][] = '';

$acl['allow']['Administrators'][] = 'admin/*';
$acl['allow']['Administrators'][] = 'User/*';

$acl['allow']['Users'][] = 'User/View';
$acl['allow']['Users'][] = 'User/Signin';
$acl['allow']['Users'][] = 'User/DoSignin';

$acl['deny']['Users'][] = 'User/AddUser';

// RBAC
$rbac = new LtRbac();
$rbac->acl = $acl;
var_dump($rbac->checkAcl($roles, 'admin/test'));
