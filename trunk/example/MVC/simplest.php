<?php
/**
 * 加载MVC类文件
 * 加载的类很多，且需要注意先后顺序，推荐使用LtAutoloader自动加载
 */
$lotusHome = dirname(dirname(dirname(__FILE__)));
include $lotusHome . "/runtime/MVC/Dispatcher.php";
include $lotusHome . "/runtime/MVC/Action.php";
include $lotusHome . "/runtime/MVC/Component.php";
include $lotusHome . "/runtime/MVC/Context.php";
/**
 * 加载Action类文件
 */
$appDir = "./simplest_app/";
include $appDir . "action/UserSigninAction.php";

/*
 * 实例化
 */
$dispatcher = new LtDispatcher();
$dispatcher->appDir = "./simplest_app/";
$dispatcher->dispatchAction("User", "Signin");