<?php
/**
 * 加载Router类文件
 */
$lotusHome = substr(__FILE__, 0, strpos(__FILE__, "example"));
include $lotusHome . "/runtime/Router/Router.php";
include $lotusHome . "/runtime/Router/RouterConfig.php";
/**
 * simplest.php
 * simplest.php?module=abc&action=123
 * simplest.php/abc/123
 */
$router = new LtRouter();
$router->init();

echo 'module=' . $router->module;
echo ' action=' . $router->action;
