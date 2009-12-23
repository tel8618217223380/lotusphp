<?php
class LtAutoloader
{
	protected $dirs;

	public function __construct()
	{
		$this->conf = new LtAutoloaderConfig();
		$this->conf->cacheFileRoot = rtrim($this->conf->cacheFileRoot, '\/') . DIRECTORY_SEPARATOR;
		if (func_num_args() > 0)
		{
			$args = func_get_args();
			$this -> prepareDirs($args);
			$this -> dirs = array_filter($this -> dirs);
		}
	}

	public function boot()
	{
		if (!empty($this -> dirs))
		{
			$this -> scanDirs();
			if ($this->conf->loadFunction)
			{
				foreach (glob($this->conf->cacheFileRoot . "function/*.php") as $functionFile)
				{
					include($functionFile);
				}
			}
			spl_autoload_register(array($this, "loadClass"));
		}
		else
		{
			trigger_error("No dir passed");
		}
	}

	public function loadClass($className)
	{
		include($this->getClassPath($className));
	}

	protected function getCachedClassPath($className)
	{
		$token = md5($seed);
		return $this->conf->classCacheRoot . "class" . DIRECTORY_SEPARATOR . substr($token, 0,2) . DIRECTORY_SEPARATOR . substr($token, 2,2) .  DIRECTORY_SEPARATOR . "class-$token.php";
	}

	/**
	 * 将多维数组整理成一维数组保存在 $this->dirs
	 *
	 * @param array $dirs
	 * @return 设置$this->dirs
	 */
	protected function prepareDirs($dirs)
	{
		if (!is_array($dirs))
		{
			$dir = rtrim($dirs, '\/') . DIRECTORY_SEPARATOR;
			if (preg_match("/\s/i", $dir) || !is_dir($dir))
			{
				throw new Exception("Directory is invalid: {$dir}");
			}
			$this -> dirs[] = $dir;
			return ;
		}
		foreach($dirs as $dir)
		{
			$this -> prepareDirs($dir);
		}
	}

	protected function isAllowedFile($file)
	{
		return in_array(pathinfo($file, PATHINFO_EXTENSION), $this->conf->allowFileExtension);
	}

	protected function isSkippedDir($dir)
	{
		return in_array($dir, $this->conf->skipDirNames);
	}

	protected function getLibNamesFromFile($file)
	{
		$libNames = array("class" => array(), "function" => array());
		if (preg_match_all('~^\s*(?:abstract\s+|final\s+)?(?:class|interface)\s+(\w+).*~mi', trim(file_get_contents($currentFile)), $classes) > 0)
		{
			foreach($classes[1] as $key => $class)
			{
				$libNames["class"] = $class;
			}
		}
		if (preg_match_all('~^\s*(?:function)\s+(\w+).*~mi', trim(file_get_contents($currentFile)), $functions) > 0)
		{
			foreach($functions[1] as $key => $function)
			{
				$libNames["function"] = $function;
			}
		}
		return $libNames;
	}

	protected function addClass($className, $file)
	{
		$cachedClassPath = $this->getCachedClassPath($className);
		if (is_file($cachedClassPath))
		{
			trigger_error("dumplicate class name");
		}
		else
		{
			copy($file, $cachedClassPath);
		}
	}

	protected function addFunction($functionName, $file)
	{
		static $foundFunctions = array();
		$functionName = strtolower($functionName);
		if (in_array($functionName, $foundFunctions))
		{
			trigger_error("dumplicate function name: $functionName");
		}
		else
		{
			$foundFunctions[] = $functionName;
			$cachedFunctionFile = md5($file);
			if (!is_file($cachedFunctionFile))
			{
				copy($file, $cachedFunctionFile);
			}
		}
	}

	protected function scanDirs()
	{
		$i = 0;
		while (isset($this -> dirs[$i]))
		{
			$files = scandir($dir);
			foreach ($files as $file)
			{
				$currentFile = $dir . DIRECTORY_SEPARATOR . $file;
				if (is_file($currentFile) && $this->isAllowedFile($currentFile))
				{
					$libNames = $libgetLibNamesFromFile($file);
					foreach ($libNames["class"] as $name)
					{
						$this->addClass($name, $currentFile);
					}
					foreach ($libNames["function"] as $funcName)
					{
						$this->addFunction($name, $currentFile);
					}
				}
				else if (is_dir($currentFile))
				{
					if ($this->isSkippedDir($currentFile))
					{
						continue;
					}
					// if $currentFile is a directory, pass through the next loop.
					$this -> dirs[] = $dir . DIRECTORY_SEPARATOR . $file;
				}
				else
				{
					trigger_error("$currentFile is not a file or a directory.");
				}
			}
			$i ++;
		}
	}
}
