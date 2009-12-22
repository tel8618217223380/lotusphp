<?php
class LtAutoloader
{
	public $fileMapping;
	protected $dirs;

	public function __construct()
	{
		if (func_num_args() > 0)
		{
			$args = func_get_args();
			$this -> prepareDirs($args);
			$this -> dirs = array_filter($this -> dirs);
			if (!empty($this -> dirs))
			{
				$this -> scanDirs();
				$this -> init();
			} 
		} 
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
			$dirs = rtrim($dirs, '\\\\\/');
			$this -> dirs[] = $dirs;
			return ;
		} 
		foreach($dirs as $dir)
		{
			$this -> prepareDirs($dir);
		} 
	} 

	public function init()
	{
		$i = 0;
		while (isset($this -> fileMapping["function"][$i]))
		{
			include $this -> fileMapping["function"][$i];
			$i ++;
		} 
		spl_autoload_register(array($this, "loadClass"));
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

	protected function scanOneDir($dir)
	{
		if (preg_match("/\s/i", $dir) || !is_dir($dir))
		{
			throw new Exception("Directory is invalid: {$dir}");
		} 
		$files = scandir($dir);
		foreach ($files as $file)
		{
			$currentFile = $dir . DIRECTORY_SEPARATOR . $file;
			if (is_file($currentFile))
			{
				$extension = pathinfo($file, PATHINFO_EXTENSION); 
				// we will judge the file extension, we only accept 'php' and 'inc' file.
				if (in_array($extension, array("php", "inc")))
				{
					if (preg_match_all('~^\s*(?:abstract\s+|final\s+)?(?:class|interface)\s+(\w+).*~mi', trim(file_get_contents($currentFile)), $classes) > 0)
					{
						foreach($classes[1] as $key => $class)
						{
							$class = strtolower($class);
							if (array_key_exists($class, $this -> fileMapping["class"]))
							{
								throw new Exception("Name repetition: {$class}");
							} 
							$this -> fileMapping["class"][$class] = $currentFile;
						} 
					} 
					else
					{
						$this -> fileMapping["function"][] = $currentFile;
					} 
				} 
			} 
			else if (is_dir($currentFile))
			{
				if (in_array($file, array(".", "..", ".svn")))
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
			$this -> dirs[$i] = realpath($this -> dirs[$i]);
			$this -> scanOneDir($this -> dirs[$i]);
			$i ++;
		} 
	} 
} 
