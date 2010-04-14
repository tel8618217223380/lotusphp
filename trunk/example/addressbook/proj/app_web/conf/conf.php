<?php
$projHome = substr(__FILE__, 0, strpos(__FILE__, "app_web"));
$config = include($projHome . "/conf/conf.php");

foreach(glob(dirname(__FILE__) . '/standard/*.php') as $confFile)
{
	if (__FILE__ != $confFile)
	{
		include($confFile);
	}
}

return $config;