<?php
class LtAutoloader
{
	protected $fileMapping;
	protected $dirs;

	public function __construct($dirArray = null)
	{
		if ($dirArray)
		{
			$this->dirs = $dirArray;
      $this->scanDirs();
			$this->init();
		}
	}

	public function getFileMapping()
	{
		return $this->fileMapping;
	}

	public function setFileMapping($fileMapping)
	{
		$this->fileMapping = $fileMapping;
	}

	public function init()
	{
		$i = 0;
		while(isset($this->fileMapping["function"][$i]))
		{
			include $this->fileMapping["function"][$i];
			$i ++;
		}
		spl_autoload_register(array($this, "loadClass"));
	}

	public function loadClass($className)
	{
		if (isset($this->fileMapping["class"][$className]))
		{
			include $this->fileMapping["class"][$className];
		}
		else 
		{
			$key = strtolower($className);
			if (isset($this->fileMapping["class"][$key]))
			{
				include $this->fileMapping["class"][$key];
			}
		}
	}

	protected function scanOneDir($dir)
	{
  	$files = scandir($dir);
		$currentDir = realpath($dir);
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
							$this->fileMapping["class"][strtolower($class)] = $currentFile;
						}
					}
					else
					{
						$this->fileMapping["function"][] = $currentFile;
					}
				}
			}
			else if(is_dir($file))
			{
				if (in_array($file, array(".", "..", ".svn")))
				{
					continue;
				}
				$this->dirs[] = $dir . DIRECTORY_SEPARATOR . $file;// if $currentFile is a directory, pass through the next loop.
			}
			else
			{
				trigger_error("$currentFile is not a file or a directory.");
			}
		}
	}

	protected function scanDirs()
	{
		$this->fileMapping = array("class" => array(), "function" => array());
		$i = 0;
		while (isset($this->dirs[$i]))
		{
			$this->scanOneDir($this->dirs[$i]);
			$i ++;
		}
	}
}
