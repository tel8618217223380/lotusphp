<?php
class LtCacheAdapterEAccelerator extends LtCacheAdapter
{
	public function add($key, $value)
	{
		return eaccelerator_put($key, $value);
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