<?php
$userRole = array (
  'zhaoyi' => 'Administrators,Users',
);
$role = array (
  '*' => '�����ɫ',
  'Administrators' => '����Ա',
  'Users' => '�����û�',
  'Guests' => '��������',
  'OWNER' => '������',
  'USER' => '�û�',
  'ANONYMOUS' => '����',
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
  'Index/Index' => '������ҳ',
  '*/Index' => '����module�µ�index����',
  'User/View' => '�û����',
  'User/Signin' => '��½ҳ��',
  'User/DoSignin' => '�ύ��½��',
  'admin/*' => 'admin module�µ�����action',
  'delete file' => 'ɾ���ļ�',
);
