<?php
class LtSessionConfig
{
	/**
	 * ��ѡֵ: file, sqlite, mysql,mysqli,apc,eaccelerator,memcache,xcache
	 * 
	 * @var string 
	 */
	public $adapter = "file";
	/**
	 * �õ�������, �粻��Ҫ�ɺ���.
	 * 
	 * @var array('option'=>'value', ...)
	 */
	public $options = array("session_save_path" => "/tmp/LtSession");
}
