<?php
class LtAutoloader
{
	public $conf = array(
	/**
	 * 是否自动加载定义了函数的文件
	 *
	 * 可选项： 
	 * # true   自动加载 
	 * # false  跳过函数，只自动加载定义了class或者interface的文件
	 */
		"load_function" => true,

	/**
	 * 要扫描的文件类型
	 *
	 * 若该属性设置为array("php","inc","php3")， 
	 * 则扩展名为"php","inc","php3"的文件会被扫描， 
	 * 其它扩展名的文件会被忽略
	 */
		"allow_file_extension" => array("php", "inc"),

	/**
	 * 不扫描的目录
	 *
	 * 若该属性设置为array(".svn", ".setting")， 
	 * 则所有名为".setting"的目录也会被忽略
	 */
		"skip_dir_names" => array(".svn"),
	);

	public $storeHandle;
	public $autoloadPath;
	public $devMode = true;
	
	private $functionFileMapping;
	private $classFileMapping;
	private $lastFileTime;
	private $currFileTime;
	private $failFileMap;
	private $functionFiles;

	public function init()
	{
		if (!is_object($this->storeHandle))
		{
			$this->storeHandle = new LtStoreFile;
			$prefix = sprintf("%u", crc32(serialize($this->autoloadPath)));
			$this->storeHandle->prefix = 'Lotus-' . $prefix;
			$this->storeHandle->useSerialize = true;
			$this->storeHandle->storeDir = '/tmp/LtAutoloader';
			$this->storeHandle->init();
		}
		$this->lastFileTime = intval($this->storeHandle->get('.last_file_time'));
		// Whether scanning directory
		if ($this->devMode || $this->lastFileTime == 0)
		{
			$this->storeHandle->add(".functions", array(), 0);
			$autoloadPath = $this->preparePath($this->autoloadPath);
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
		if ($this->conf["load_function"])
		{
			$this->loadFunction();
		}
		spl_autoload_register(array($this, "loadClass"));
	}

	public function loadFunction()
	{
		if ($functionFiles = $this->storeHandle->get(".functions"))
		{
			foreach ($functionFiles as $functionFile)
			{
				include_once($functionFile);
			}
		}
	}

	public function loadClass($className)
	{
		if ($classFile = $this->storeHandle->get(strtolower($className)))
		{
			include($classFile);
		}
	}

	protected function convertPath($path)
	{
		$path = str_replace("\\", "/", $path);
		if (!is_readable($path))
		{
			trigger_error("Directory is not exists/readable: {$path}");
			return false;
		}
		$path = rtrim(realpath($path), '\\/');
		if ("WINNT" != PHP_OS && preg_match("/\s/i", $path))
		{
			trigger_error("Directory contains space/tab/newline is not supported: {$path}");
			return false;
		}
		return $path;
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
			$var = array($var);
		}
		$i = 0;
		while (isset($var[$i]))
		{
			if (!is_array($var[$i]) && $path = $this->convertPath($var[$i]))
			{
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
		// 如果函数文件已经删除，将它从自动加载函数列表中剔除
		$this->functionFiles = $this->storeHandle->get(".functions");
		if (is_array($this->functionFiles))
		{
			foreach ($this->functionFiles as $key => $functionFile)
			{
				if ( ! is_file($functionFile) )
				{
					unset($this->functionFiles[$key]);
				}
			}
		}
		else
		{
			$this->functionFiles = array();
		}
		
		$this->currFileTime = $this->lastFileTime;
		$this->failFileMap = false;
		$i = 0;
		while (isset($dirs[$i]))
		{
			$dir = $dirs[$i];
			$files = scandir($dir);
			foreach ($files as $file)
			{
				if (in_array($file, array(".", "..")) || in_array($file, $this->conf["skip_dir_names"]))
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

		if ($this->lastFileTime == 0)
		{
			$this->storeHandle->add('.last_file_time', $this->currFileTime);
		}
		if ($this->failFileMap == false)
		{
			$this->storeHandle->update('.last_file_time', $this->currFileTime);
		}
		$this->functionFiles = array();
		$this->classFileMapping = array();
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
		$existedClassFile = $this->storeHandle->get($key);
		if (is_file($existedClassFile) && $existedClassFile != $file)
		{
			trigger_error("duplicate class [$className] found in:\n$existedClassFile\n$file\n");
			return false;
		}
		else
		{
			if (isset($this->classFileMapping[$key]))
			{
				$existedClassFile = $this->classFileMapping[$key];
				trigger_error("duplicate class [$key] found in:\n$existedClassFile\n$file\n");
				return false;
			}
			$this->classFileMapping[$key] = $file;
			$this->storeHandle->add($key, $file);
			$this->storeHandle->update($key, $file);
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
			if ( ! is_array($this->functionFiles) )
			{
				$this->functionFiles = array();
			}
			$this->functionFiles = array_unique( array_merge($this->functionFiles , array($file) ), SORT_STRING);
			$this->storeHandle->update('.functions', $this->functionFiles);
			return true;
		}
	}

	protected function addFileMap($file)
	{
		if (!in_array(pathinfo($file, PATHINFO_EXTENSION), $this->conf["allow_file_extension"]))
		{
			return false;
		}
		$libNames = array();
		$currFileTime = filemtime($file);
		if ($this->lastFileTime < $currFileTime)
		{
			$this->currFileTime = max($currFileTime, $this->currFileTime);
			$libNames = $this->parseLibNames(trim(file_get_contents($file)));
			// 如果文件不再有函数定义，将它从自动加载函数文件中剔除。
			if (! array_key_exists('function', $libNames) )
			{
				if (is_array($this->functionFiles))
				{
					if ($_key = array_search($file, $this->functionFiles))
					{
						unset($this->functionFiles[$_key]);
					}
				}
			}
			foreach ($libNames as $libType => $libArray)
			{
				// 同一文件内的类、接口、函数重复定义
				$_lib = array_count_values($libArray);
				foreach ($_lib as $_k => $_v)
				{
					if ($_v > 1)
					{
						trigger_error("duplicate $libType [$_k] found in:$file\n");
						$this->failFileMap = true;
					}
				}
				
				$method = "function" == $libType ? "addFunction" : "addClass";
				foreach ($libArray as $libName)
				{
					if(! $this->$method($libName, $file))
					{
						$this->failFileMap = true;
					}
				}
			}
		}
		return true;
	}
}
