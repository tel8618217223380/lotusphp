<?php
interface LtCacheAdapter
{
	public function add($key, $value, $ttl=0);
	public function del($key);
	public function get($key);
}