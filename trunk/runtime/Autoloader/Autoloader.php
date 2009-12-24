<?php
class LtAutoloader
{
	public $storeHandle;
	protected $dirs;

	public function __construct()
	{
		$this -> conf = new LtAutoloaderConfig();
		$this -> storeHandle = new LtAutoloaderFakeCache();
		if (func_num_args() > 0)
		{
			$args = func_get_args();
			$this -> prepareDirs($args);
			// prepareDirs()已经处理存入$this->dirs的值不存在空值
			// 因此下面这条语句是没有必要的
			// $this -> dirs = array_filter($this -> dirs);
		}
	}

	public function boot()
	{
		if (!isset($this -> storeHandle -> keyPrefix))
		{
			$this -> storeHandle -> keyPrefix = '';
		}
		// 尚未扫描目录
		if (0 == $this -> storeHandle -> get($this -> storeHandle -> keyPrefix . ".class_total") && 0 == $this -> storeHandle -> get($this -> storeHandle -> keyPrefix . ".function_total"))
		{
			if (!empty($this -> dirs))
			{
				$this -> scanDirs();
			}
			else
			{
				trigger_error("No dir passed");
			}
		}
		if ($functionFiles = $this -> storeHandle -> get($this -> storeHandle -> keyPrefix . ".funcations"))
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
		if ($classFile = $this -> storeHandle -> get($this -> storeHandle -> keyPrefix . strtolower($className)))
		{
			include($classFile);
		}
	}

	/**
	 * 使用迭代算法
	 * 将多维数组整理成一维数组保存在 $this->dirs
	 *
	 * @param array $dirs
	 * @return 设置$this->dirs
	 */
	protected function prepareDirs($dirs)
	{
		$i = 0;
		while (isset($dirs[$i])) // iteration, Don't use foreach
		{
			if (!is_array($dirs[$i]))
			{
				$dir = rtrim($dirs[$i], '\/');
				if (preg_match("/\s/i", $dir) || !is_dir($dir))
				{
					throw new Exception("Directory is invalid: {$dir}");
				}
				$this -> dirs[] = $dir; // save
			}
			else
			{
				foreach($dirs[$i] as $dir)
				{
					$dirs[] = $dir; // next while
				}
			}
			unset($dirs[$i], $dir); // free
			$i ++;
		}
	}

	protected function isAllowedFile($file)
	{
		return in_array(pathinfo($file, PATHINFO_EXTENSION), $this -> conf -> allowFileExtension);
	}

	protected function isSkippedDir($dir)
	{
		return in_array($dir, array(".", "..")) || in_array($dir, $this -> conf -> skipDirNames);
	}

	protected function getLibNamesFromFile($src)
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
		$key = $this -> storeHandle -> keyPrefix . strtolower($className);
		if ($this -> storeHandle -> get($key))
		{
			trigger_error("dumplicate class name : $className");
		}
		else
		{
			$this -> storeHandle -> add($key, $file);
			$classTotalKey = $this -> storeHandle -> keyPrefix . ".class_total";
			$classTotal = $this -> storeHandle -> get($classTotalKey);
			$this -> storeHandle -> del($classTotalKey);
			$this -> storeHandle -> add($classTotalKey, $classTotal + 1);
		}
	}

	protected function addFunction($functionName, $file)
	{
		$functionName = strtolower($functionName);
		$foundFunctions = $this -> storeHandle -> get($this -> storeHandle -> keyPrefix . ".funcations");
		if (in_array($functionName, $foundFunctions))
		{
			trigger_error("dumplicate function name: $functionName");
		}
		else
		{
			$foundFunctions[] = $file;
			$this -> storeHandle -> del($this -> storeHandle -> keyPrefix . ".funcations");
			$this -> storeHandle -> add($this -> storeHandle -> keyPrefix . ".funcations", $foundFunctions);
			$functionTotalKey = $this -> storeHandle -> keyPrefix . ".function_total";
			$functionTotal = $this -> storeHandle -> get($functionTotalKey);
			$this -> storeHandle -> del($functionTotalKey);
			$this -> storeHandle -> add($functionTotalKey, $functionTotal + 1);
		}
	}
	/**
	 * 使用迭代算法扫描子目录
	 */
	protected function scanDirs()
	{
		$i = 0;
		while (isset($this -> dirs[$i])) // iteration, Don't use foreach
		{
			$dir = $this -> dirs[$i];
			$files = scandir($dir);
			foreach ($files as $file)
			{
				if ($this -> isSkippedDir($file))
				{
					continue;
				}
				$currentFile = $dir . DIRECTORY_SEPARATOR . $file;
				if (is_file($currentFile) && $this -> isAllowedFile($currentFile))
				{
					$src = trim(file_get_contents($currentFile));
					$libNames = $this -> getLibNamesFromFile($src);
					foreach ($libNames["class"] as $class)
					{
						// Use absolute paths
						$this -> addClass($class, realpath($currentFile));
					}
					foreach ($libNames["function"] as $function)
					{
						// Use absolute paths
						$this -> addFunction($function, realpath($currentFile));
					}
				}
				else if (is_dir($currentFile))
				{
					// if $currentFile is a directory, pass through the next loop.
					$this -> dirs[] = $currentFile;
				}
				else
				{
					trigger_error("$currentFile is not a file or a directory.");
				}
			} //end foreach
			$i ++;
		} //end while
	}
}

class LtAutoloaderFakeCache
{
	public $keyPrefix = '';
	public $fileMapping = array(".class_total" => 0, ".function_total" => 0, ".funcations" => array());

	public function add($key, $value)
	{
		$this -> fileMapping[$key] = $value;
	}

	public function del($key)
	{
		unset($this -> fileMapping[$key]);
	}

	public function get($key)
	{
		return isset($this -> fileMapping[$key]) ? $this -> fileMapping[$key] : false;
	}
}
