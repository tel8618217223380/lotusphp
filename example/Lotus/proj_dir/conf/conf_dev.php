<?php
/**
 * ����ģʽ���ȶ�ȡstandard����,
 * Ȼ���ȡdev����,������standard�Ĳ�������
 */
include(dirname(__FILE__) .'/conf.php');

$config = array();

foreach(glob(dirname(__FILE__) . '/dev/*.php') as $confFile)
{
	if (__FILE__ != $confFile)
	{
		include($confFile);
	}
}

return $config;
