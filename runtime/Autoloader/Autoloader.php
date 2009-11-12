<?php
class LtAutoloader
{
	public $classFileMapping;

	public function loadClass($className)
	{
		if (isset($this->classFileMapping[$className]))
		{
			include $this->classFileMapping[$className];
		}
		else 
		{
			$key = strtolower($className);
			if (isset($this->classFileMapping[$key]))
			{
				include $this->classFileMapping[$key];
			}
		}
	}

	public function scanDir($directories)
	{
		if (is_string($directories))
		{
			$directories = array($directories);
		}
		$mapping = array("class" => array(), "function" => array());
		$i = 0;
		while (isset($directories[$i]) && $files = scandir($directories[$i]))
		{
			$currentDir = realpath($directories[$i]);
			foreach ($files as $file)
			{
				$currentFile = $currentDir . DIRECTORY_SEPARATOR . $file;
				if (is_file($currentFile))
				{
					$extension = pathinfo($file, PATHINFO_EXTENSION);
					if (in_array($extension, array("php", "inc")))// we will judge the file extension, we only accept 'php' and 'inc' file.
					{
						if(preg_match_all('~^\s*(?:abstract\s+|final\s+)?(?:class|interface)\s+(\w+).*~mi', trim(file_get_contents($currentFile)), $classes) > 0)
						{
							foreach($classes[1] as $key => $class)
							{
								$mapping["class"][strtolower($class)] = $currentFile;
							}
						}
						else
						{
							$mapping["function"][] = $currentFile;
						}
					}
				}
				else if(!in_array($file, array(".", "..", ".svn")))// if $currentFile is a directory, pass through the next loop.
				{
					$directories[] = $directories[$i] . DIRECTORY_SEPARATOR . $file;
				}
			}
			$i ++;
		}
		return $mapping;
	}

	public function init($mapping)
	{
		$i = 0;
		while(isset($mapping["function"][$i]))
		{
			include $mapping["function"][$i];
			$i ++;
		}
		$this->classFileMapping = $mapping["class"];
		spl_autoload_register(array($this, "loadClass"));
	}
}