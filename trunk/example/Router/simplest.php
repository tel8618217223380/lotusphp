<?php
/**
 * 加载Router类文件
 */
$lotusHome = dirname(dirname(dirname(__FILE__)));
include $lotusHome . "/runtime/Router/Router.php";
include $lotusHome . "/runtime/Router/RouterConfig.php";
/**
 * simplest.php
 * simplest.php?module=abc&action=123
 * simplest.php/abc/123
 */
$router = new LtRouter();
/**
 * 可以设置默认module action ,如不设置则使用config中的配置
 */
$router -> conf -> defaultModule = "User";
$router -> conf -> defaultAction = "Signin";
$router -> init();

echo 'module=' . $router -> module;
echo ' action=' . $router -> action;
