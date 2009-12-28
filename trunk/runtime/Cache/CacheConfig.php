<?php
class LtCacheConfig
{
	/**
	 * 默认的缓存引擎是phps , 使用php的序列化机制将数据保存在文件中, 
	 * 还可以是 file, eaccelerator, xcache,
	 * 通过扩展可以实现其它缓存存储引擎
	 */
	public $adapter = "phps";
	/**
	 * 缓存引擎用到的配置
	 * 默认的phps需要设置存放缓存文件的根目录
	 */
	public $options = array("cache_file_root" => "/tmp/LtCache");
}
