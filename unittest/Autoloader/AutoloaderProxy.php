<?php
/**
 * 用这个类把LtAutoloader的protected属性和方法暴露出来测试
 */
class LtAutoloaderProxy extends LtAutoloader
{
	public function __get($prop)
	{
		if (isset($this->$prop))
		{
			return $this->$prop;
		}
	}

	public function addClass($className, $file)
	{
		return parent::addClass($className, $file);
	}

	public function addFunction($functionName, $file)
	{
		return parent::addFunction($functionName, $file);
	}

	public function parseLibNames($src)
	{
		return parent::parseLibNames($src);
	}
}
