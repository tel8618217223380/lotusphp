<?php
/*
 * 加载MVC类文件
 * 加载的类很多，且需要注意先后顺序，推荐使用LtAutoloader自动加载
 */
$lotusHome = dirname(dirname(dirname(__FILE__)));
include $lotusHome . "/runtime/MVC/Dispatcher.php";
include $lotusHome . "/runtime/MVC/Action.php";
include $lotusHome . "/runtime/MVC/Component.php";
include $lotusHome . "/runtime/MVC/Context.php";
include $lotusHome . "/runtime/MVC/View/View.php";
include $lotusHome . "/runtime/MVC/View/AbstractView.php";
include $lotusHome . "/runtime/MVC/View/HtmlView.php";
include $lotusHome . "/runtime/MVC/View/JsonView.php";

/*
 * 实例化
 */
$dispatcher = new LtDispatcher();
$dispatcher->appDir = "./app/simplest/";
$dispatcher->dispatchAction("User", "Signin");