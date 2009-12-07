<?php
/*
 * 加载Router类文件
 */
$lotusHome = dirname(dirname(dirname(__FILE__)));
include $lotusHome . "/runtime/RBAC/RBAC.php";
include $lotusHome . "/runtime/RBAC/RBACConfig.php";

// 用户
// 用户角色

// 设置角色
$rbac = new LtRBAC('User');
// 检查角色 module action 权限
$rbac->checkPrivilege('Index','test');
