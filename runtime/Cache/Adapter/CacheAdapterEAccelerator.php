<?php
class LtCacheAdapterEAccelerator implements LtCacheAdapter
{
	protected $keyPrefix;

	public function connect($hostConf)
	{
		if(isset($hostConf["key_prefix"]))
		{
			$this->keyPrefix = $hostConf["key_prefix"];
		}
		return true;
	}

	public function add($key, $value, $ttl=0)
	{
		return eaccelerator_put($this->getRealKey($key), $value, $ttl);
	}

	public function del($key)
	{
		return eaccelerator_rm($this->getRealKey($key));
	}

	public function get($key)
	{
		$value = eaccelerator_get($this->getRealKey($key));
		if (!empty($value))
		{
			return $value;
		}
		else
		{
			return false;
		}
	}

	public function update($key, $value, $ttl = 0)
	{
		return eaccelerator_put($this->getRealKey($key), $value, $ttl);
// 直接更新
//		if ($this->del($this->getRealKey($key)))
//		{
//			return $this->add($this->getRealKey($key), $value, $ttl);
//		}
//		else
//		{
//			return false;
//		}
	}

	protected function getRealKey($key)
	{
		if (!empty($this->keyPrefix))
		{
			return $this->keyPrefix . "-" . $key;
		}
		else
		{
			return $key;
		}
	}
}