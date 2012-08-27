<?php
/**
 * The Session memcache
 * @author Yi Zhao <zhao5908@gmail.com>
 * @license http://opensource.org/licenses/BSD-3-Clause New BSD License
 * @version svn:$Id$
 */

/**
 * The Session memcache
 * @author Yi Zhao <zhao5908@gmail.com>
 * @category runtime
 * @package   Lotusphp\Session
 * @subpackage saveHandler
 */
class LtSessionMemcache
{
	/** @var string session save path */
	public $sessionSavePath;

	/**
	 * init
	 */
	public function init()
	{
		ini_set('session.save_handler', 'memcache');
		if(empty($this->sessionSavePath))
		{
			$this->sessionSavePath = 'tcp://127.0.0.1:11211';
		}
		ini_set('session.save_path', $this->sessionSavePath);
		session_start();
	}

}
