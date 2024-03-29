<?php
/**
 * Cache QueryEngine TableDataGateway
 * @author Jianxiang Qin <TalkativeDoggy@gmail.com>
 * @license http://opensource.org/licenses/BSD-3-Clause New BSD License
 * @version svn:$Id$
 */

/**
 * 缓存 查询引擎 表数据入口
 * @author Jianxiang Qin <TalkativeDoggy@gmail.com>
 * @category runtime
 * @package   Lotusphp\Cache\QueryEngine
 * @subpackage TableDataGateway
 */
class LtCacheTableDataGateway
{
	/** @var string table name */
	public $tableName;

	/** @var LtCacheAdapter 缓存适配器的一个实例句柄 */
	public $ch;

	/**
	 * add
	 * @param string $key
	 * @param string|array|object $value
	 * @param int $ttl
	 * @return boolean
	 */
	public function add($key, $value, $ttl = 0)
	{
		return $this->ch->add($key, $value, $ttl, $this->tableName);
	}

	/**
	 * del
	 * @param string $key
	 * @return boolean
	 */
	public function del($key)
	{
		return $this->ch->del($key, $this->tableName);
	}

	/**
	 * get
	 * @param string $key
	 * @return string|array|object
	 */
	public function get($key)
	{
		return $this->ch->get($key, $this->tableName);
	}

	/**
	 * update
	 * @param string $key
	 * @param string|array|object $value
	 * @param int $ttl
	 * @return boolean
	 */
	public function update($key, $value, $ttl = 0)
	{
		return $this->ch->update($key, $value, $ttl, $this->tableName);
	}
}