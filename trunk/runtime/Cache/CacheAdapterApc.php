<?php
class LtCacheAdapterApc implements LtCacheAdapter
{
	public function add($key, $value)
	{
		return apc_add($key, $value);
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