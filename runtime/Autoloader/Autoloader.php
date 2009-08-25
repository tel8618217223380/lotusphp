<?php
class Autoloader
{
	public $classFileMapping;

	public function loadClass($className)
	{
		if (isset($this->classFileMapping[$className]))
		{
			require $this->classFileMapping[$className];
		}
	}
}