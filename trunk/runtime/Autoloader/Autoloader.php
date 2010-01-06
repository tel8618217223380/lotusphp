<?php
class LtAutoloader
{
	static public $storeHandle;
	static public $namespace;
	public $autoloadPath;
	public $conf;

	public function __construct()
	{
		$this->conf = new LtAutoloaderConfig;
	}

	public function init()
	{
		if (!is_object(self::$storeHandle))
		{
			self::$storeHandle = new LtAutoloaderStore;
		}
		else
		{
			self::$namespace = md5(serialize($this->autoloadPath));
			self::$storeHandle->namespaceMapping[self::$namespace] = sprintf("%u", crc32(self::$namespace));
		}
		// Whether scanning directory
		if (0 == self::$storeHandle->get(".class_total", self::$namespace) && 0 == self::$storeHandle->get(".function_total", self::$namespace))
		{
			self::$storeHandle->add(".class_total", 0, 0, self::$namespace);
			self::$storeHandle->add(".function_total", 0, 0, self::$namespace);
			self::$storeHandle->add(".functions", array(), 0, self::$namespace);
			$autoloadPath = $this->var2array($this->autoloadPath);
			$this->autoloadPath = $this->preparePath($autoloadPath);
			$autoloadPath = $this->autoloadPath;
			foreach($autoloadPath as $key=>$path)
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
		if($this->conf->isLoadFunction)
		{
			$this->loadFunction();
		}
		spl_autoload_register(array($this, "loadClass"));
	}

	public function loadFunction()
	{
		if ($functionFiles = self::$storeHandle->get(".functions", self::$namespace))
		{
			foreach ($functionFiles as $functionFile)
			{
				include($functionFile);
			}
		}
	}

	public function loadClass($className)
	{
		if ($classFile = self::$storeHandle->get(strtolower($className), self::$namespace))
		{
			include($classFile);
		}
	}

	/**
	 * The string or an Multidimensional array into a one-dimensional array
	 * 
	 * @param array or string $var
	 * @return array one-dimensional array
	 */
	protected function var2array($var)
	{
		$ret = array();
		if (!is_array($var))
		{
			$ret = array($var);
		}
		else
		{
			$i = 0;
			while (isset($var[$i]))
			{
				if (!is_array($var[$i]))
				{
					$ret[] = $var[$i];
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

	protected function preparePath($path)
	{
		if (is_array($path))
		{
			foreach($path as $key => $dir)
			{
				$dir = rtrim(realpath($dir), '\/');
				if (preg_match("/\s/i", $dir))
				{
					trigger_error("Directory is invalid: {$dir}");
				}
				$path[$key] = $dir;
			}
		}
		else
		{
			$path = rtrim(realpath($path), '\/');
			if (preg_match("/\s/i", $path))
			{
				trigger_error("Directory is invalid: {$dir}");
			}
		}
		return $path;
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
			$dir = $this->preparePath($dirs[$i]);
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
		$tokenTotal = count($tokens);
		$found = null;
		$braceLevel = 0;
		for ($i = 0; $i < $tokenTotal; $i ++)
		{
			if (!isset($tokens[$i]))
			{
				trigger_error("invalid source");
			}
			if (0 == $braceLevel && is_array($tokens[$i]))
			{
				if (in_array($tokens[$i][0], array(T_CLASS, T_INTERFACE, T_FUNCTION)))
				{
					$found = token_name($tokens[$i][0]);
				}
				else if ($found && T_STRING == $tokens[$i][0])
				{
					$libNames[strtolower(substr($found, 2))][] = $tokens[$i][1];
				}
			}
			else if ("{" == $tokens[$i])
			{
				$braceLevel += 1;
			}
			else if ("}" == $tokens[$i])
			{
				$braceLevel -= 1;
			}
		}
		return $libNames;
	}

	protected function addClass($className, $file)
	{
		$key = strtolower($className);
		if (self::$storeHandle->get($key, self::$namespace))
		{
			trigger_error("duplicate class name : $className");
			return false;
		}
		else
		{
			self::$storeHandle->add($key, $file, 0, self::$namespace);
			self::$storeHandle->update(".class_total", self::$storeHandle->get(".class_total", self::$namespace) + 1, 0, self::$namespace);
			return true;
		}
	}

	protected function addFunction($functionName, $file)
	{
		$functionName = strtolower($functionName);
		$foundFunctions = self::$storeHandle->get(".functions", self::$namespace);
		if ($foundFunctions && array_key_exists($functionName, $foundFunctions))
		{
			trigger_error("duplicate function name: $functionName");
			return false;
		}
		else
		{
			$foundFunctions[$functionName] = $file;
			self::$storeHandle->update(".functions", $foundFunctions, 0, self::$namespace);
			self::$storeHandle->update(".function_total", self::$storeHandle->get(".function_total", self::$namespace) + 1, 0, self::$namespace);
			return true;
		}
	}

	protected function addFileMap($file)
	{
		if (in_array(pathinfo($file, PATHINFO_EXTENSION), $this->conf->allowFileExtension))
		{
			$src = trim(file_get_contents($file));
			$libNames = $this->parseLibNames($src);
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
		return false;
	}
}

class LtAutoloaderStore
{
	protected $stack;

	public function add($key, $value, $ttl, $namespace)
	{
		$this->stack[$this->getRealKey($namespace, $key)] = $value;
		return true;
	}

	public function del($key, $namespace)
	{
		$key = $this->getRealKey($namespace, $key);
		if(isset($this->stack[$key]))
		{
			unset($this->stack[$key]);
			return true;
		}
		else
		{
			return false;
		}
	}

	public function get($key, $namespace)
	{
		$key = $this->getRealKey($namespace, $key);
		return isset($this->stack[$key]) ? $this->stack[$key] : false;
	}

	public function update($key, $value, $ttl, $namespace)
	{
		$this->stack[$this->getRealKey($namespace, $key)] = $value;
		return true;
	}

	protected function getRealKey($namespace, $key)
	{
		return sprintf("%u", crc32($namespace)) . $key;
	}
}
