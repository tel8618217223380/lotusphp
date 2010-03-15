<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "common.inc.php";

$operation = $_REQUEST["operation"];

/**
 * Lotus组件初始化三步曲
 */ 
// 1. 实例化
$cookie = new LtCookie; 
// 2. 设置属性
$cookie->conf->secretKey = "VHfgk!@c=_"; 
// 3. 调init()方法
$cookie->init();
/**
 * 初始化完毕，测试其效果
 */

switch ($operation)
{
	case "set":
		$cookie->setCookie('newproj', 'hello', time() + 3600);
		$cookie->setCookie('test', array('a', 'b', 'c', 'd'), time() + 3600);
		break;
	case "get":
		$cookie->getCookie('newproj');
		$cookie->getCookie('test');
		break;
	case "del":
		$cookie->delCookie('newproj');
		$cookie->delCookie('test');
		break;
}
