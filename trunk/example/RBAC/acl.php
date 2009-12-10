<?php
$userRole = array (
  'zhaoyi' => 'Administrators,Users',
);
$role = array (
  '*' => '任意角色',
  'Administrators' => '管理员',
  'Users' => '例子用户',
  'Guests' => '例子来宾',
  'OWNER' => '所有者',
  'USER' => '用户',
  'ANONYMOUS' => '匿名',
);
$acl = array (
  'allow' => 
  array (
    '*' => 
    array (
      0 => 'Index/Index',
    ),
    'Administrators' => 
    array (
      0 => 'admin/*',
      1 => 'admin/test',
      2 => 'User/AddUser',
    ),
    'Users' => 
    array (
      0 => 'User/View',
      1 => 'User/Signin',
      2 => 'User/DoSignin',
    ),
    'Guests' => 
    array (
      0 => '*/Index',
    ),
  ),
  'deny' => 
  array (
    '*' => 
    array (
      0 => '',
    ),
    'Administrators' => 
    array (
      0 => 'admin/kill',
    ),
    'Users' => 
    array (
      0 => 'User/AddUser',
    ),
    'Guests' => 
    array (
      0 => '*',
    ),
  ),
);
$resource = array (
  'Index/Index' => '访问首页',
  '*/Index' => '任意module下的index动作',
  'User/View' => '用户浏览',
  'User/Signin' => '登陆页面',
  'User/DoSignin' => '提交登陆表单',
  'admin/*' => 'admin module下的任意action',
  'delete file' => '删除文件',
);
