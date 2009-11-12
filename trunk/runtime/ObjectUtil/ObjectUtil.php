<?php
class LtObjectUtil
{
	static public function singleton($className)
	{
		static $instances;
		if (class_exists($className))
		{
			$key = strtolower($className);
			if (!isset($instances[$key]))
			{
				$instances[$key] = new $className;
			}
			return $instances[$key];
		}
		else
		{
			return false;
		}
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
