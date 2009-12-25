<?php
class LtCache
{
	protected $cacheHandle;

	public $namespaceMapping;

	public function __construct()
	{
		$this->conf = new LtCacheConfig;
	}

	public function init()
	{
		$adapterClassName = "LtCacheAdapter" . ucfirst($this->conf->adapter);
		$this->cacheHandle = new $adapterClassName;
		$this->cacheHandle->options = $this->conf->options;
	}

	public function add($namespace, $key, $value, $ttl = 0)
	{
		return $this->cacheHandle->add($this->getRealKey($namespace, $key), $value, $ttl);
	}

	public function del($namespace, $key)
	{
		return $this->cacheHandle->del($this->getRealKey($namespace, $key));
	}

	public function get($namespace, $key)
	{
		return $this->cacheHandle->get($this->getRealKey($namespace, $key));
	}

	public function update($namespace, $key, $value, $ttl = 0)
	{
		$realKey = $this->getRealKey($namespace, $key);
		if ($result = $this->cacheHandle->del($realKey))
		{
			return $this->cacheHandle->add($realKey, $value, $ttl);
		}
		return $result;
	}

	protected function getRealKey($namespace, $key)
	{
		$keyPrefix = "";
		if ($this->namespaceMapping)
		{
			if (isset($this->namespaceMapping[$namespace]))
			{
				$keyPrefix = $this->namespaceMapping[$namespace];
			}
			else
			{
				trigger_error("Invalid namespace: $namespace, please register it first");
			}
		}
		else
		{
			$keyPrefix = sprintf("%u", crc32($namespace));
		}
	}
}