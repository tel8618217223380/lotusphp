<?php
class LtStoreMemory implements LtStore
{
	protected $stack;

	public function add($key, $value, $ttl = 0)
	{
		$this->stack[$key] = $value;
		return true;
	}

	public function del($key)
	{
		if (isset($this->stack[$key]))
		{
			unset($this->stack[$key]);
			return true;
		}
		else
		{
			return false;
		}
	}

	public function get($key, $doNotModifiedSince = null)
	{
		return isset($this->stack[$key]) ? $this->stack[$key] : false;
	}

	public function update($key, $value, $ttl = 0)
	{
		$this->stack[$key] = $value;
		return true;
	}
}
