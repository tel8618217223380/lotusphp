<?php
class LtObjectUtil
{
	static $instances;
	static public function singleton($className)
	{
		if (empty($className))
		{
			trigger_error('empty class name');
			return false;
		}
		if (class_exists($className))
		{
			$key = strtolower($className);
			if (!isset(self::$instances[$key]))
			{
				self::$instances[$key] = new $className;
			}
			return self::$instances[$key];
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
