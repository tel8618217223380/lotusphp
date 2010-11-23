<?php
$lotusHome = "D:/lotus/trunk/runtime/";
include($lotusHome . "Lotus.php");
$lotus = new Lotus;
$lotus->option["proj_dir"] = dirname(dirname(dirname(__FILE__)));
$lotus->option["app_name"] = "frontend";
$lotus->init();