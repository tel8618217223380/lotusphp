<?php
// username = role
$userRole['zhaoyi'] = 'Administrators,Users';

// role = comment
$role['*'] = '任意角色';
$role['Administrators'] = '管理员';
$role['Users'] = '例子用户';
$role['Guests'] = '例子来宾';
$role['OWNER'] = '所有者';
$role['USER'] = '用户';
$role['ANONYMOUS'] = '匿名';

// [allow or deny][role] = array( resource )
$acl['allow']['*'][] = 'Index/Index';
$acl['deny']['*'][] = '';

$acl['allow']['Administrators'][] = 'admin/*';
$acl['allow']['Administrators'][] = 'admin/test';
$acl['allow']['Administrators'][] = 'User/AddUser';
$acl['deny']['Administrators'][] = 'admin/kill';

$acl['allow']['Users'][] = 'User/View';
$acl['allow']['Users'][] = 'User/Signin';
$acl['allow']['Users'][] = 'User/DoSignin';
$acl['deny']['Users'][] = 'User/AddUser';

$acl['allow']['Guests'][] = '*/Index';
$acl['deny']['Guests'][] = '*';

// permissions resource
$resource['Index/Index'] = '访问首页';
$resource['*/Index'] = '任意module下的index动作';
$resource['User/View'] = '用户浏览';
$resource['User/Signin'] = '登陆页面';
$resource['User/DoSignin'] = '提交登陆表单';
$resource['admin/*'] = 'admin module下的任意action';
$resource['delete file'] = '删除文件';
