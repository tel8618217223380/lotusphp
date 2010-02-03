<?php
/**
 * 加载MVC类文件
加载的类很多，且需要注意先后顺序，推荐使用LtAutoloader自动加载
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
include $appDir . "action/IndexIndexAction.php";
include $appDir . "action/testUsingComponentAction.php";
include $appDir . "action/stockPriceComponent.php";
include $appDir . "action/testUsingBlankLayoutAction.php";
include $appDir . "action/testPassDataAction.php";
include $appDir . "action/testUsingTitleAction.php";
/**
 * 实例化
 */
$dispatcher = new LtDispatcher;
$dispatcher->viewDir = "./simplest_tpl/view/";
/**
 * 保存模板编译后的文件目录,
 * 如果不指定,默认在view同级目录生成LtTemplateView目录
 */
$dispatcher->viewTplDir = "/tmp/LtTemplateView/";

$module = isset($_GET['module']) ? $_GET['module'] : 'Index';
$action = isset($_GET['action']) ? $_GET['action'] : 'Index';
$dispatcher->dispatchAction($module, $action);
