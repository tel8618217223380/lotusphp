<?php
class LtCacheConfig
{
	/**
	 * Ĭ�ϵĻ���������phps , ʹ��php�����л����ƽ����ݱ������ļ���, 
	 * �������� file, eaccelerator, xcache,
	 * ͨ����չ����ʵ����������洢����
	 */
	public $adapter = "phps";
	/**
	 * ���������õ�������
	 * Ĭ�ϵ�phps��Ҫ���ô�Ż����ļ��ĸ�Ŀ¼
	 */
	public $options = array("cache_file_root" => "/tmp/LtCache");
}
