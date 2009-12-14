<?php
class LtCacheAdapterApc extends LtCacheAdapter
{
	public function add($key, $value, $ttl=0)
	{
		return apc_add($key, $value, $ttl);
	}
	
	public function del($key)
	{
		return apc_delete($key);
	}
	
	public function get($key)
	{
		return apc_fetch($key);
	}
}