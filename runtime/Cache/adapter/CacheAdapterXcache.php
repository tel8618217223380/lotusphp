<?php
class LtCacheAdapterXcache extends LtCacheAdapter
{
	public function add($key, $value)
	{
		return xcache_set($key, $value);
	}
	
	public function del($key)
	{
		return xcache_unset($key);
	}
	
	public function get($key)
	{
		return xcache_get($key);
	}
}