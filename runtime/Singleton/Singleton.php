<?php
class LtSingleton
{
	static public function getInstance($className)
	{
		static $instances;
		if (!isset($instances[$className]))
		{
			$instances[$className] = new $className;
		}
		return $instances[$className];
	}
}