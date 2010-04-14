<?php
@set_magic_quotes_runtime(0);
date_default_timezone_set('Etc/GMT-8');
$lotusHome = substr(dirname(__FILE__), 0, strpos(__FILE__, "example"));

include $lotusHome . 'runtime/Lotus.php';
/**
 * ��ʼ��Lotus��
 */
$lotus = new Lotus();
/**
 * ��ĿĿ¼
 */
$lotus->option['proj_dir'] = dirname(dirname(__FILE__)) . '/proj/';
/**
 * ��ʱĿ¼,Ĭ����proj_dir/tmp/
 * ����ģʽ�µ�Autoloader �� MVC��ģ������ �� �ļ�����Cache
 */
//$lotus->option['app_tmp'] = $_SERVER['DOCUMENT_ROOT'].'/tmp/addressbook/';
$lotus->option['app_tmp'] = '/tmp/addressbook/';

/**
 * �Ƿ��Զ����غ����ļ�, Ĭ��ΪAutoloaderConfig.php������
 */
$lotus->option['load_function'] = true;
/**
 * Ӧ�����ƶ���ĿĿ¼�µ���Ŀ¼����
 */
$lotus->option['app_name'] = 'app_web';
$lotus->option['runtime_filemap'] = true;

/**
 * Ĭ�� true
 */
//$lotus->mvcMode = true;

/**
 * Ĭ�� false;
 */
//$lotus->devMode = true;


$lotus->init();
/**
 * ʹ��xdebug��������
 */
if (function_exists('xdebug_time_index') && function_exists('xdebug_peak_memory_usage'))
{
	echo "\n<script type=\"text/javascript\">\n";
	echo 'document.getElementById("debug_info").innerHTML = "';
	echo xdebug_time_index();
	echo ' - ';
	echo format_size(xdebug_peak_memory_usage());
	echo "\";\n</script>\n";
}
