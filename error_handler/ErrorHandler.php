<?php
/**
 * LtErrorHandler
 * @author Jianxiang Qin <TalkativeDoggy@gmail.com>
 * @license http://opensource.org/licenses/BSD-3-Clause New BSD License
 * @version svn:$Id$
 */

/**
 * error handler
 * @author Jianxiang Qin <TalkativeDoggy@gmail.com>
 * @category error_handler
 * @package   Lotusphp\ErrorHandler
 */
class LtErrorHandler
{
	/**
	 * debug
	 * @param int $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param int $errline
	 */
	public function debug($errno, $errstr, $errfile, $errline)
	{
		echo "\n" . $errstr . " in $errfile : $errline";
		print_r(debug_backtrace());
	}
}

$errorHandler = new LtErrorHandler;
set_error_handler(array($errorHandler, "debug"));