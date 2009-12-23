<?php
class LtAutoloader
{
	public $fileMapping;
	public $dirs;

	public function __construct()
	{
		$this->conf = new LtAutoloaderConfig();
		if (func_num_args() > 0)
		{
			$args = func_get_args();
			$this -> prepareDirs($args);
			$this -> dirs = array_filter($this -> dirs);
			if (!empty($this -> dirs))
			{
				$this -> scanDirs();
				$this -> startAutoload();
			}
		}
	}

	public function loadClass($className)
	{
		if (isset($this -> fileMapping["class"][$className]))
		{
			include $this -> fileMapping["class"][$className];
		}
		else
		{
			$key = strtolower($className);
			if (isset($this -> fileMapping["class"][$key]))
			{
				include $this -> fileMapping["class"][$key];
			}
		}
	}

	public function startAutoload()
	{
		$i = 0;
		while (isset($this -> fileMapping["function"][$i]))
		{
			include $this -> fileMapping["function"][$i];
			$i ++;
		}
		spl_autoload_register(array($this, "loadClass"));
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
			//if (preg_match("/\s/i", $dir) || !is_dir($dir))
			//{
			//throw new Exception("Directory is invalid: {$dir}");
			//}
			// 删除最后的目录分隔符号
			// $dirs = preg_replace("/[\\\\|\/]*$/",'',$dirs);
			$dir = rtrim($dirs, '\\\\\/') . DIRECTORY_SEPARATOR;

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

	protected function getLibNamesFromContent($file)
	{
		$libNames = array("class" => array(), "function" => array());
		if (preg_match_all('~^\s*(?:abstract\s+|final\s+)?(?:class|interface)\s+(\w+).*~mi', trim(file_get_contents($currentFile)), $classes) > 0)
		{
			foreach($classes[1] as $key => $class)
			{
				$libNames["class"] = $class;
			}
		}
		else if (preg_match_all('~^\s*(?:function)\s+(\w+).*~mi', trim(file_get_contents($currentFile)), $functions) > 0)
		{
			foreach($functions[1] as $key => $function)
			{
				$libNames["function"] = $function;
			}
		}
		else
		{
			//既没有定义类，也没有定义函数
		}
		return ;
	}

	protected function addClass($name, $file)
	{
		$name = strtolower($name);
		if (isset($this->fileMapping["class"][$name]))
		{
			trigger_error("dumplicate class name");
		}
		else
		{
			$this->fileMapping["class"][$name] = $file;
		}
	}

	protected function addFunction($name, $file)
	{
		static $foundFunctions = array();
		$name = strtolower($name);
		if (in_array($name, $foundFunctions))
		{
			trigger_error("dumplicate class name");
		}
		else
		{
			$foundFunctions[] = $name;
			$this->fileMapping["function_file"] = $file;
		}
	}

	protected function scanOneDir($dir)
	{
		$files = scandir($dir);
		foreach ($files as $file)
		{
			$currentFile = $dir . DIRECTORY_SEPARATOR . $file;
			if (is_file($currentFile) && $this->isAllowedFile($currentFile))
			{
				$libNames = $libgetLibNamesFromContent($file);
				foreach ($libNames["class"] as $className)
				{
					$this->addClass($className, $currentFile);
				}
				foreach ($libNames["function"] as $funcName)
				{
					$this->addFunction($funcName, $currentFile);
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
	}

	protected function scanDirs()
	{
		$this -> fileMapping = array("class" => array(), "function" => array());
		$i = 0;
		while (isset($this -> dirs[$i]))
		{
			$this -> scanOneDir($this -> dirs[$i]);
			$i ++;
		}
	}
}
