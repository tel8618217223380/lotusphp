<?php
class LtCacheAdapterXcache implements LtCacheAdapter
{
	protected $keyPrefix;

	public function connect($hostConf)
	{
		if(isset($hostConf["key_prefix"]))
		{
			$this->keyPrefix = $hotConf["key_prefix"];
		}
		return true;
	}

	public function add($key, $value, $ttl=0)
	{
		return xcache_set($this->getRealKey($key), $value, $ttl);
	}
	
	public function del($key)
	{
		return xcache_unset($this->getRealKey($key));
	}
	
	public function get($key)
	{
		return xcache_get($this->getRealKey($key));
	}

	public function update($key, $value, $ttl = 0)
	{
		if ($this->del($this->getRealKey($key)))
		{
			return $this->add($this->getRealKey($key), $value, $ttl);
		}
		else
		{
			return false;
		}
	}

	protected function getRealKey($key)
	{
		if ($this->keyPrefix)
		{
			return $this->keyPrefix . "-" . $key;
		}
		else
		{
			return $key;
		}
	}
}