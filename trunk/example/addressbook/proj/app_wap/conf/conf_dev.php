<?php
$projHome = substr(__FILE__, 0, strpos(__FILE__, "app_wap"));
$config = include($projHome . "/conf/conf_dev.php");
foreach(glob(dirname(__FILE__) . '/dev/*.php') as $confFile)
{
	if (__FILE__ != $confFile)
	{
		include($confFile);
	}
}

return $config;