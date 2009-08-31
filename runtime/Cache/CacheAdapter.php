<?php
interface LtCacheAdapter
{
	public function add($key, $value);
	
	public function del($key);
	
	public function get($key);
}