<?php
/**
 * ��ȡ��Ŀ��������
 */

$projHome = substr(__FILE__, 0, strpos(__FILE__, "app_name1"));
$config = include($projHome . "/dev/conf_dev.php");

/**
 * ����ģʽ���ȶ�ȡstandard����,
 * Ȼ���ȡdev����,������standard�Ĳ�������
 */
include(dirname(__FILE__) . '/conf.php');

/**
 * ��ȡdev����
 */
foreach(glob(dirname(__FILE__) . '/dev/*.php') as $confFile)
{
	if (__FILE__ != $confFile)
	{
		include($confFile);
	}
}

return $config;
