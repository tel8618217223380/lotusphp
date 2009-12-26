<?php
class LtCacheAdapterXcache implements LtCacheAdapter
{
	public function add($key, $value, $ttl=0)
	{
		return xcache_set($key, $value, $ttl);
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