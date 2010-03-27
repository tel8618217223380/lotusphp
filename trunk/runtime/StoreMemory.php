<?php
class LtStoreMemory implements LtStore
{
	protected $stack;

	public function add($key, $value, $ttl = 0)
	{
		if (isset($this->stack[$key]))
		{ 
			
		}
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

	/**
	 * key不存在 返回false
	 * 不管有没有过期,都更新数据
	 * 
	 * @return bool 
	 */
	public function update($key, $value, $ttl = 0)
	{
		if (!isset($this->stack[$key]))
		{
			return false;
		}
		else
		{
			$this->stack[$key] = $value;
			return true;
		}
	}
}
