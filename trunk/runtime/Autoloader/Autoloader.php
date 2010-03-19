<?php
class LtAutoloader
{
	static public $storeHandle;
	public $autoloadPath;
	public $conf;
	protected $functionFileMapping;
	protected $fileStore;

	public function __construct()
	{
		$this->conf = new LtAutoloaderConfig;
	}

	public function init()
	{
		if (!is_object(self::$storeHandle))
		{
			self::$storeHandle = new LtStoreMemory;
			$this->fileStore = new LtStoreFile;
			$this->fileStore->setFileRoot($this->conf->mappingFileRoot);
		} 
		// Whether scanning directory
		if (0 == self::$storeHandle->get(".class_total") && 0 == self::$storeHandle->get(".function_total"))
		{
			self::$storeHandle->add(".class_total", 0);
			self::$storeHandle->add(".function_total", 0);
			self::$storeHandle->add(".functions", array(), 0);
			$this->autoloadPath = $this->preparePath($this->autoloadPath);
			$autoloadPath = $this->autoloadPath;
			foreach($autoloadPath as $key => $path)
			{
				if (is_file($path))
				{
					$this->addFileMap($path);
					unset($autoloadPath[$key]);
				}
			}
			$this->scanDirs($autoloadPath);
			unset($autoloadPath);
		} 
		// Whether loading function files
		if ($this->conf->isLoadFunction)
		{
			$this->loadFunction();
		}
		spl_autoload_register(array($this, "loadClass"));
	}

	public function loadFunction()
	{
		if ($functionFiles = self::$storeHandle->get(".functions"))
		{
			foreach ($functionFiles as $functionFile)
			{
				include($functionFile);
			}
		}
	}

	public function loadClass($className)
	{
		if ($classFile = self::$storeHandle->get(strtolower($className)))
		{
			include($classFile);
		}
	}

	/**
	 * The string or an Multidimensional array into a one-dimensional array
	 * 
	 * @param array $ or string $var
	 * @return array one-dimensional array
	 */
	protected function preparePath($var)
	{
		$ret = array();
		if (!is_array($var))
		{
			$path = rtrim(realpath($var), '\\/');
			if (preg_match("/\s/i", $path))
			{
				trigger_error("Directory is invalid: {$path}");
			}
			$ret = array($path);
		}
		else
		{
			$i = 0;
			while (isset($var[$i]))
			{
				if (!is_array($var[$i]))
				{
					$path = rtrim(realpath($var[$i]), '\\/');
					if (preg_match("/\s/i", $path))
					{
						trigger_error("Directory is invalid: {$path}");
					}
					$ret[] = $path;
				}
				else
				{
					foreach($var[$i] as $v)
					{
						$var[] = $v;
					}
				}
				unset($var[$i]);
				$i ++;
			}
		}
		return $ret;
	}

	/**
	 * Using iterative algorithm scanning subdirectories
	 * save autoloader filemap
	 * 
	 * @param array $dirs one-dimensional
	 * @return 
	 */
	protected function scanDirs($dirs)
	{
		$i = 0;
		while (isset($dirs[$i]))
		{
			$dir = rtrim(realpath($dirs[$i]), '\\/');
			if (preg_match("/\s/i", $dir))
			{
				trigger_error("Directory is invalid: {$dir}");
			}
			$files = scandir($dir);
			foreach ($files as $file)
			{
				if (in_array($file, array(".", "..")) || in_array($file, $this->conf->skipDirNames))
				{
					continue;
				}
				$currentFile = $dir . DIRECTORY_SEPARATOR . $file;
				if (is_file($currentFile))
				{
					$this->addFileMap($currentFile);
				}
				else if (is_dir($currentFile))
				{ 
					// if $currentFile is a directory, pass through the next loop.
					$dirs[] = $currentFile;
				}
				else
				{
					trigger_error("$currentFile is not a file or a directory.");
				}
			} //end foreach
			unset($dirs[$i]);
			$i ++;
		} //end while
	}

	protected function parseLibNames($src)
	{
		$libNames = array();
		$tokens = token_get_all($src);
		$level = 0;
		$found = false;
		$name = '';
		foreach ($tokens as $token)
		{
			if (is_string($token))
			{
				if ('{' == $token)
				{
					$level ++;
				}
				else if ('}' == $token)
				{
					$level --;
				}
			}
			else
			{
				list($id, $text) = $token;
				if (T_CURLY_OPEN == $id || T_DOLLAR_OPEN_CURLY_BRACES == $id)
				{
					$level ++;
				}
				if (0 < $level)
				{
					continue;
				}
				switch ($id)
				{
					case T_STRING:
						if ($found)
						{
							$libNames[strtolower($name)][] = $text;
							$found = false;
						}
						break;
					case T_CLASS:
					case T_INTERFACE:
					case T_FUNCTION:
						$found = true;
						$name = $text;
						break;
				}
			}
		}
		return $libNames;
	}

	protected function addClass($className, $file)
	{
		$key = strtolower($className);
		if ($existedClassFile = self::$storeHandle->get($key))
		{
			trigger_error("duplicate class [$className] found in:\n$existedClassFile\n$file\n");
			return false;
		}
		else
		{
			self::$storeHandle->add($key, $file);
			self::$storeHandle->update(".class_total", self::$storeHandle->get(".class_total") + 1);
			return true;
		}
	}

	protected function addFunction($functionName, $file)
	{
		$functionName = strtolower($functionName);
		if (isset($this->functionFileMapping[$functionName]))
		{
			$existedFunctionFile = $this->functionFileMapping[$functionName];
			trigger_error("duplicate function [$functionName] found in:\n$existedFunctionFile\n$file\n");
			return false;
		}
		else
		{
			$this->functionFileMapping[$functionName] = $file;
			self::$storeHandle->update(".functions", array_unique(array_values($this->functionFileMapping)));
			self::$storeHandle->update(".function_total", count($this->functionFileMapping));
			return true;
		}
	}

	protected function addFileMap($file)
	{
		if (!in_array(pathinfo($file, PATHINFO_EXTENSION), $this->conf->allowFileExtension))
		{
			return false;
		}
		$libNames = array();
		if ($this->fileStore instanceof LtStore)
		{
			$key = md5($file);
			$cacheFile = $this->fileStore->getCacheFile($key);
			if (is_file($cacheFile) && filemtime($cacheFile) > filemtime($file))
			{
				$libNames = unserialize($this->fileStore->get($key));
			}
			else
			{
				$libNames = $this->parseLibNames(trim(file_get_contents($file)));
				$this->fileStore->add($key, serialize($libNames));
			}
		}
		else
		{
			$libNames = $this->parseLibNames(trim(file_get_contents($file)));
		}

		foreach ($libNames as $libType => $libArray)
		{
			$method = "function" == $libType ? "addFunction" : "addClass";
			foreach ($libArray as $libName)
			{
				$this->$method($libName, $file);
			}
		}
		return true;
	}
}
