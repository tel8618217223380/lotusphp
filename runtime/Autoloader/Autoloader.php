<?php
class LtAutoloader
{
	public $storeHandle;
	public $storeKeyPrefix;
	public $autoloadPath;
	protected $conf;

	public function __construct()
	{
		$this->conf = new LtAutoloaderConfig();
		$this->storeKeyPrefix = '';
	}

	public function init()
	{
		if (empty($this->storeHandle))
		{
			$this->storeHandle = new LtAutoloaderStore();
		} 
		// Whether scanning directory
		if (0 == $this->storeHandle->get($this->storeKeyPrefix . ".class_total") && 0 == $this->storeHandle->get($this->storeKeyPrefix . ".function_total"))
		{
			$autoloadPath = $this->var2array($this->autoloadPath);
			$this->autoloadPath = $this->preparePath($autoloadPath);
			$this->scanDirs($this->autoloadPath);
		} 
		// Whether loading function files
		if ($functionFiles = $this->storeHandle->get($this->storeKeyPrefix . ".funcations"))
		{
			foreach ($functionFiles as $functionFile)
			{
				include($functionFile);
			}
		}
		spl_autoload_register(array($this, "loadClass"));
	}

	public function loadClass($className)
	{
		if (empty($this->storeHandle))
		{
			$this->storeHandle = new LtAutoloaderStore();
		}
		if ($classFile = $this->storeHandle->get($this->storeKeyPrefix . strtolower($className)))
		{
			include($classFile);
		}
	}

	/**
	 * The string or an Multidimensional array into a one-dimensional array
	 * 
	 * @param array $ or string
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

	protected function isAllowedFile($file)
	{
		return in_array(pathinfo($file, PATHINFO_EXTENSION), $this->conf->allowFileExtension);
	}

	protected function isSkippedDir($dir)
	{
		return in_array($dir, array(".", "..")) || in_array($dir, $this->conf->skipDirNames);
	}

	protected function parseLibNames($src)
	{
		$libNames = array("class" => array(), "function" => array());
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
			// 没有类也没有函数的文件忽略
		}
		return $libNames;
	}

	protected function addClass($className, $file)
	{
		if (empty($this->storeHandle))
		{
			$this->storeHandle = new LtAutoloaderStore();
		}
		$key = $this->storeKeyPrefix . strtolower($className);
		if ($this->storeHandle->get($key))
		{
			trigger_error("dumplicate class name : $className");
		}
		else
		{
			$this->storeHandle->add($key, $file);
			$classTotalKey = $this->storeKeyPrefix . ".class_total";
			$classTotal = $this->storeHandle->get($classTotalKey);
			$this->storeHandle->del($classTotalKey);
			$this->storeHandle->add($classTotalKey, $classTotal + 1);
		}
	}

	protected function addFunction($functionName, $file)
	{
		if (empty($this->storeHandle))
		{
			$this->storeHandle = new LtAutoloaderStore();
		}
		$functionName = strtolower($functionName);
		$foundFunctions = $this->storeHandle->get($this->storeKeyPrefix . ".funcations");
		if (array_key_exists($functionName, $foundFunctions))
		{
			trigger_error("dumplicate function name: $functionName");
		}
		else
		{
			$foundFunctions[$functionName] = $file;
			$this->storeHandle->del($this->storeKeyPrefix . ".funcations");
			$this->storeHandle->add($this->storeKeyPrefix . ".funcations", $foundFunctions);
			$functionTotalKey = $this->storeKeyPrefix . ".function_total";
			$functionTotal = $this->storeHandle->get($functionTotalKey);
			$this->storeHandle->del($functionTotalKey);
			$this->storeHandle->add($functionTotalKey, $functionTotal + 1);
		}
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
			if (is_file($dir))
			{
				if ($this->isAllowedFile($dir))
				{
					$src = trim(file_get_contents($dir));
					$libNames = $this->parseLibNames($src);
					foreach ($libNames["class"] as $class)
					{
						$this->addClass($class, $dir);
					}
					foreach ($libNames["function"] as $function)
					{
						$this->addFunction($function, $dir);
					}
				}
				unset($dirs[$i]);
				$i ++;
				continue;
			}
			$files = scandir($dir);
			foreach ($files as $file)
			{
				if ($this->isSkippedDir($file))
				{
					continue;
				}
				$currentFile = $dir . DIRECTORY_SEPARATOR . $file;
				if (is_file($currentFile))
				{
					if ($this->isAllowedFile($currentFile))
					{
						$src = trim(file_get_contents($currentFile));
						$libNames = $this->parseLibNames($src);
						foreach ($libNames["class"] as $class)
						{
							$this->addClass($class, $currentFile);
						}
						foreach ($libNames["function"] as $function)
						{
							$this->addFunction($function, $currentFile);
						}
					}
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
}

class LtAutoloaderStore
{
	public $fileMapping = array(".class_total" => 0, ".function_total" => 0, ".funcations" => array());

	public function add($key, $value)
	{
		$this->fileMapping[$key] = $value;
	}

	public function del($key)
	{
		unset($this->fileMapping[$key]);
	}

	public function get($key)
	{
		return isset($this->fileMapping[$key]) ? $this->fileMapping[$key] : false;
	}
}
