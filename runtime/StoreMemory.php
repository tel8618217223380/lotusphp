<?php
class LtStoreMemory implements LtStore
{
	protected $stack;

	public function add($key, $value, $ttl = 0)
	{
		if (isset($this->stack[$key]))
		{
			if (0 == $this->stack[$key]['ttl'] || time() < $this->stack[$key]['ttl'])
			{
				return false;
			}
		}
		$this->stack[$key]['value'] = $value;
		$this->stack[$key]['ttl'] = (0 == $ttl) ? 0 : (time() + $ttl);
		$this->stack[$key]['modified'] = time();
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
		if (!isset($this->stack[$key]))
		{
			return false;
		}
		else
		{
			if (0 != $this->stack[$key]['ttl'] && time() > $this->stack[$key]['ttl'])
			{
				unset($this->stack[$key]);
				return false;
			}
			else
			{
				if ($doNotModifiedSince && $this->stack[$key]['modified'] < $doNotModifiedSince)
				{
					return false;
				}
				else
				{
					return $this->stack[$key]['value'];
				}
			}
		}
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
			$this->stack[$key]['value'] = $value;
			$this->stack[$key]['ttl'] = (0 == $ttl) ? 0 : (time() + $ttl);
			$this->stack[$key]['modified'] = time();
			return true;
		}
	}
}
