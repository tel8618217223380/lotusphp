<?php
class LtCacheAdapterEAccelerator extends LtCacheAdapter
{
	public function add($key, $value, $ttl=0)
	{
		return eaccelerator_put($key, $value, $ttl);
	}

	public function del($key)
	{
		return eaccelerator_rm($key);
	}

	public function get($key)
	{
		return eaccelerator_get($key);
	}
}