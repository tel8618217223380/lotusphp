<?php
/**
 * DB sql expression
 * @author Jianxiang Qin <TalkativeDoggy@gmail.com>
 * @license http://opensource.org/licenses/BSD-3-Clause New BSD License
 * @version svn:$Id$
 */

/**
 * db sql expression
 * @author Jianxiang Qin <TalkativeDoggy@gmail.com>
 * @category runtime
 * @package   Lotusphp\DB
 */
class LtDbSqlExpression
{
	/** @var string expression */
	private $_expression;
	
	/**
	 * construct
	 * @param string $string
	 */
	public function __construct($string)
	{
		$this->_expression = (string) $string;
	}
	
	/**
	 * to string
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->_expression;
	}
}
