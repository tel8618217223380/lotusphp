<?php
/**
 * Store
 * @author Jianxiang Qin <TalkativeDoggy@gmail.com>
 * @license http://opensource.org/licenses/BSD-3-Clause New BSD License
 * @version svn:$Id$
 */

/**
 * LtStore Interface
 * @author Jianxiang Qin <TalkativeDoggy@gmail.com>
 * @category runtime
 * @package Lotusphp\Store
 */
Interface LtStore
{
	/**
	 * add
	 * @param string $key
	 * @param string|array $value
	 */
	public function add($key, $value);
	/**
	 * del
	 * @param string $key
	 */
	public function del($key);
	/**
	 * get
	 * @param string $key
	 */
	public function get($key);
	/**
	 * update
	 * @param string $key
	 * @param string|array $value
	 */
	public function update($key, $value);
}