<?php
/**
 * 加载MVC类文件
 * 加载的类很多，且需要注意先后顺序，推荐使用LtAutoloader自动加载
 */
$lotusHome = substr(__FILE__, 0, strpos(__FILE__, "example"));
include $lotusHome . "/runtime/MVC/Dispatcher.php";
include $lotusHome . "/runtime/MVC/Action.php";
include $lotusHome . "/runtime/MVC/Component.php";
include $lotusHome . "/runtime/MVC/Context.php";
include $lotusHome . "/runtime/MVC/View.php";
include $lotusHome . "/runtime/MVC/TemplateView.php";

include $lotusHome . "/runtime/Validator/Validator.php";
include $lotusHome . "/runtime/Validator/ValidatorConfig.php";
include $lotusHome . "/runtime/Validator/ValidatorDtd.php";

/**
 * 加载Action类文件
 */
$appDir = "./simplest_tpl/";
include $appDir . "action/UserSigninAction.php";

/*
 * 实例化
 */
$dispatcher = new LtDispatcher;
$dispatcher->viewDir = "./simplest_tpl/view/";
$dispatcher->dispatchAction("User", "Signin");
