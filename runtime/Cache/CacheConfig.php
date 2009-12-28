<?php
class LtCacheConfig
{
	/**
	 * 缓存引擎可选值: phps, file, eaccelerator, xcache
	 * 通过扩展可以实现其它缓存存储引擎
	 * 默认值 phps , 使用php的序列化机制将数据保存在文件中,
	 * 
	 * @var string 
	 */
	public $adapter = "phps";
	/**
	 * 缓存引擎用到的配置, 如不需要可忽略.
	 * 默认的phps需要设置存放缓存文件的根目录
	 * 
	 * @var array('option'=>'value', ...)
	 */
	public $options = array("cache_file_root" => "/tmp/LtCache");
}
