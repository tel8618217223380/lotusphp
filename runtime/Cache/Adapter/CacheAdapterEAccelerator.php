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
		$value = serialize($value); //eAccelerator doesn't serialize object
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
			return unserialize($value);
		}
		else
		{
			return false;
		}
	}

	public function update($key, $value, $ttl = 0)
	{
		$value = serialize($value);
		return eaccelerator_put($this->getRealKey($key), $value, $ttl);
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