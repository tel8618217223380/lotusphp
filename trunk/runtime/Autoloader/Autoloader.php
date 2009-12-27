<?php
class LtAutoloader
{
	public $storeHandle;
	public $namespace;
	public $autoloadPath;
	public $conf;

	public function __construct()
	{
		$this->conf = new LtAutoloaderConfig();
	}

	public function init()
	{
		if (!is_object($this->storeHandle))
		{
			$this->storeHandle = new LtAutoloaderStore();
		}
		else
		{
			$this->namespace = md5(serialize($this->autoloadPath));
			$this->storeHandle->namespaceMapping[$this->namespace] = crc32($this->namespace);
		}
		// Whether scanning directory
		if (0 == $this->storeHandle->get(".class_total", $this->namespace) && 0 == $this->storeHandle->get(".function_total", $this->namespace))
		{
			$this->storeHandle->add(".class_total", 0, 0, $this->namespace);
			$this->storeHandle->add(".function_total", 0, 0, $this->namespace);
			$this->storeHandle->add(".functions", array(), 0, $this->namespace);
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
		if ($functionFiles = $this->storeHandle->get(".functions", $this->namespace))
		{
			foreach ($functionFiles as $functionFile)
			{
				include($functionFile);
			}
		}
	}

	public function loadClass($className)
	{
		if ($classFile = $this->storeHandle->get(strtolower($className), $this->namespace))
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
		$libNames = array("class" => array(), "function" => array());
		$classes = $functions = array();
		if (preg_match_all('~^\s*(?:abstract\s+|final\s+)?(?:class|interface)\s+(\w+).*~mi', $src, $classes) > 0)
		{
			foreach($classes[1] as $class)
			{
				$libNames["class"][] = $class;
			}
		}
		else if (preg_match_all('~^\s*(?:function)\s+(\w+).*~mi', $src, $functions) > 0)
		{
			foreach($functions[1] as $function)
			{
				$libNames["function"][] = $function;
			}
		}
		else
		{ 
			// No classes and No function ignore
		}
		return $libNames;
	}

	protected function addClass($className, $file)
	{
		$key = strtolower($className);
		if ($this->storeHandle->get($key, $this->namespace))
		{
			trigger_error("dumplicate class name : $className");
			return false;
		}
		else
		{
			$this->storeHandle->add($key, $file, 0, $this->namespace);
			$this->storeHandle->update(".class_total", $this->storeHandle->get(".class_total", $this->namespace) + 1, 0, $this->namespace);
			return true;
		}
	}

	protected function addFunction($functionName, $file)
	{
		$functionName = strtolower($functionName);
		$foundFunctions = $this->storeHandle->get(".functions", $this->namespace);
		if ($foundFunctions && array_key_exists($functionName, $foundFunctions))
		{
			trigger_error("dumplicate function name: $functionName");
			return false;
		}
		else
		{
			$foundFunctions[$functionName] = $file;
			$this->storeHandle->update(".functions", $foundFunctions, 0, $this->namespace);
			$this->storeHandle->update(".function_total", $this->storeHandle->get(".function_total", $this->namespace) + 1, 0, $this->namespace);
			return true;
		}
	}

	protected function addFileMap($file)
	{
		if (in_array(pathinfo($file, PATHINFO_EXTENSION), $this->conf->allowFileExtension))
		{
			$src = trim(file_get_contents($file));
			$libNames = $this->parseLibNames($src);
			foreach ($libNames["class"] as $class)
			{
				$this->addClass($class, $file);
			}
			foreach ($libNames["function"] as $function)
			{
				$this->addFunction($function, $file);
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
		$this->stack[$key = $this->getRealKey($namespace, $key)] = $value;
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
