<?php
class LtObjectUtil
{
	static public function singleton($className)
	{
		static $instances;
		if (!isset($instances[$className]))
		{
			$instances[$className] = new $className;
		}
		return $instances[$className];
	}

	static public function setProperties($object, $propArray)
	{
		foreach ($object as $propName)
		{
			if (array_key_exists($propName, $propArray))
			{
				$object->$propName = $propArray[$propName];
			}
		}
	}
}
