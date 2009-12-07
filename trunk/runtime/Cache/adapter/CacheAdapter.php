<?php
abstract class LtCacheAdapter
{
	public $options;

	abstract public function add($key, $value);
	
	abstract public function del($key);
	
	abstract public function get($key);
}