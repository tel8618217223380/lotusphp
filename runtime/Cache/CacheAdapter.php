<?php
abstract class CacheAdapter
{
	abstract public function add($key, $value);
	
	abstract public function del($key);
	
	abstract public function get($key);
}