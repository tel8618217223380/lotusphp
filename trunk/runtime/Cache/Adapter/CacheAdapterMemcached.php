<?php
class LtCacheAdapterMemcached implements LtCacheAdapter
{
	protected $keyPrefix;
	protected $connectionResource;

	public function connect($hostConf)
	{
		if(isset($hostConf["key_prefix"]))
		{
			$this->keyPrefix = $hotConf["key_prefix"];
		}
		$this->connectionResource = new Memcached();
		$this->connectionResource->addServer($hostConf["host"], $hostConf["port"]);
		return $this->connectionResource;
	}

	public function add($key, $value, $ttl=0)
	{
		return $this->connectionResource->add($this->getRealKey($key), $value, $ttl);
	}

	public function del($key)
	{
		return $this->connectionResource->delete($this->getRealKey($key));
	}

	public function get($key)
	{
		return $this->connectionResource->get($this->getRealKey($key));
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