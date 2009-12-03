<?php
/* 
 * 加载Cookie类文件
 */
$lotusHome = dirname(dirname(dirname(__FILE__)));
include $lotusHome . "/runtime/Cookie/Cookie.php";
include $lotusHome . "/runtime/Cookie/CookieConfig.php";
/* 
 * 加载Cookie类文件
 */

/*
 * 开始使用Cookie
 * php.ini需要修改为output_buffering = On
 */
//构造设置cookie的参数

$parameters = array(
	"name" => "newproj",
	"value" => "hello",
	"expire" => time()+3600
);

$cookie = new LtCookie();
$cookie->conf->secretKey = "lotusphp";
$cookie->setCookie($parameters);
print_r($cookie->getCookie($parameters['name']));