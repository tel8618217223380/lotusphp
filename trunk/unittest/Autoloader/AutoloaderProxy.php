<?php
/**
 * 用这个类把LtAutoloader的protected属性和方法暴露出来测试
 */
class AutoloaderProxy extends LtAutoloader
{
	public function __get($prop)
	{
		if (isset($this->$prop))
		{
			return $this->$prop;
		}
	}

	public function __call($method, $arg)
	{
		$this->$method($arg);
	}
}
