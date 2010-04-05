<?php
$projHome = substr(__FILE__, 0, strpos(__FILE__, "app_name1"));
$app1Config = include($projHome . "/dev/conf_dev.php");
$config = array();

foreach(glob(dirname(__FILE__) . '/dev/*.php') as $confFile)
{
	if (__FILE__ != $confFile)
	{
		include($confFile);
	}
}

return $config;
