<?php
/**
 * StoreMemory
 * @author Jianxiang Qin <TalkativeDoggy@gmail.com>
 * @license http://opensource.org/licenses/BSD-3-Clause New BSD License
 * @version svn:$Id$
 */

/**
 * LtStore Memory
 * @author Jianxiang Qin <TalkativeDoggy@gmail.com>
 * @category runtime
 * @package Lotusphp\Store
 */
class LtStoreMemory implements LtStore
{
	/**
	 * 存储配置
	 * @var array
	 */
	protected $stack;

	/**
	 * add
	 * @param string $key
	 * @param string|array|object $value
	 * @return boolean
	 */
	public function add($key, $value)
	{
		if (isset($this->stack[$key]))
		{
			return false;
		}
		else
		{
			$this->stack[$key] = $value;
			return true;
		}
	}

	/**
	 * del
	 * @param string $key
	 * @return boolean
	 */
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

	/**
	 * get
	 * @param string $key
	 * @return string|array|object
	 */
	public function get($key)
	{
		return isset($this->stack[$key]) ? $this->stack[$key] : false;
	}

	/**
	 * key不存在返回false
	 * 
	 * @return bool 
	 */
	/**
	 * update
	 * @param string $key
	 * @param string|array|object $value
	 * @return boolean
	 */
	public function update($key, $value)
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
