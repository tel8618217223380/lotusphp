<?php
/**
 * ������������ʲô��������ν�ģ�ֻҪ��ĩβ����return����
 */
$config = array();
include substr(dirname(__FILE__),0,strpos(__FILE__, "app_name1")).'/conf/common.conf.php';
/**
 * �Զ�ɨ��confĿ¼�µ�php�ļ�����֮��������������֧����Ŀ¼
 * ��δ���ȼ��ڣ�
 * include("conf/db.conf.php");
 * include("conf/validator.conf.php");
 */
foreach(glob(dirname(__FILE__) . '/conf/*.php') as $confFile)
{
	if (__FILE__ != $confFile)
	{
		include($confFile);
	}
}

/**
 * һ����Ҫ�������return���
 * �����return��include(conf.php)��ʱ���յ��ķ���ֵ������1
 * ����return��include(conf.php)�յ��ķ���ֵ�������� 
 * lotusphp��Ҫ�ķ���ֵ��һ������
 */
return $config;