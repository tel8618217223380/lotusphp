<?php
/*
 * ����Autoloader���ļ�
 */
$lotusHome = dirname(dirname(dirname(dirname(__FILE__))));
include $lotusHome . "/runtime/Autoloader/Autoloader.php";

/*
 * ��ɨ��Ŀ¼�õ���class file mapping���浽�ڴ���
 */
$cacheKey = "autoloader_cache_key";
if ($cachedFileMapping = apc_fetch($cacheKey))//����apc�л�ȡ����class file mapping����Ҫɨ��Ŀ¼��
{
	$autoloader = new LtAutoloader();
  $autoloader->setFileMapping($autoloader);
  $autoloader->init();
}
else//��apc��û��class file mapping����ɨ��Ŀ¼���֮��������apc
{
  $directories = array("Classes");
	$autoloader = new LtAutoloader($directories);
	$fileMapping = $autoloader->getFileMapping();
	apc_add($cacheKey, $fileMapping);
}

/*
 * ��ʼ����ϣ�����ʹ��
 */
$hello = new HelloWorld();
$hello->sayHello();