<?php
class ConfigExpression
{
	private $_expression;
	
	public function __construct($string)
	{
		$this->_expression = (string) $string;
	}
	
	public function __toString()
	{
		return $this->_expression;
	}
}