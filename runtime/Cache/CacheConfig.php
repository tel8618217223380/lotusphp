<?php
class LtCacheConfig
{
	/**
	 * ���������ѡֵ: phps, file, eaccelerator, xcache
	 * ͨ����չ����ʵ����������洢����
	 * Ĭ��ֵ phps , ʹ��php�����л����ƽ����ݱ������ļ���, 
	 */
	public $adapter = "phps";
	/**
	 * ���������õ�������, �粻��Ҫ�ɺ���.
	 * Ĭ�ϵ�phps��Ҫ���ô�Ż����ļ��ĸ�Ŀ¼
	 */
	public $options = array("cache_file_root" => "/tmp/LtCache");
}
