<?php
$lotusHome = "D:/lotus/trunk/runtime/";
include($lotusHome . "Lotus.php");
$lotus = new Lotus;
$lotus->option["app_name"] = "frontend";
$lotus->init();