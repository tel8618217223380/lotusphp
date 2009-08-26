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

	public function scanDir($directories)
	{
		$mapping = array();
		$i = 0;
		while (isset($directories[$i]) && $files = scandir($directories[$i]))
		{
			foreach ($files as $file)
			{
				$currentFile = $directories[$i] . $file;
				if (is_file($currentFile))
				{
					$extension = pathinfo($file, PATHINFO_EXTENSION);
					if ($extension == 'php' || $extension == 'inc')// we will judge the file extension, we only accept 'php' and 'inc' file.
					{
						if(preg_match_all('~^\s*(?:abstract\s+|final\s+)?(?:class|interface)\s+(\w+).*~mi', trim(file_get_contents($currentFile)), $classes) > 0)
						{
							foreach($classes[1] as $key => $class)
							{
								$mapping["class"][$class] = $currentFile;
							}
						}
						else
						{
							$mapping["non-class"] = $currentFile;
						}
					}
				}
				else if('.' != $file && '..' != $file && '.svn' != $file)// if $currentFile is a directory, pass through the next loop.
				{
					$directories[] = $currentFile . DIRECTORY_SEPARATOR;
				}
			}
			$i ++;
		}
		return $mapping;
	}
}