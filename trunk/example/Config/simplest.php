<?php
/* 
 * 加载Config类文件
 */
$lotusHome = substr(__FILE__, 0, strpos(__FILE__, "example"));
include $lotusHome . "/runtime/Config/Config.php";
include $lotusHome . "/runtime/Config/ConfigExpression.php";
/* 
 * 加载Config类文件
 */

/*
 * 开始使用Config
 * 这个组件配合Lotus ObjectUtil::singleton()可以实现全局统一的配置
 * 使用方便，容易管理
 */
$config = new LtConfig();
$config->app["cookie"]["secret_key"] = "hello";
print_r($config);