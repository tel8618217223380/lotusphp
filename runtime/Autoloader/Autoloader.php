<?php
/**
 * Autoloader
 * @author Jianxiang Qin <TalkativeDoggy@gmail.com>
 * @license http://opensource.org/licenses/BSD-3-Clause New BSD License
 * @version svn:$Id$
 * @package Lotusphp\Autoloader
 */

/**
 * 自动加载类和函数
 * 
 * 按需加载类，每次只加载用到的类。
 * 
 *     函数库文件不是按需加载！若支持加载函数，则所有定义函数的文件都会加载。
 * 
 * 代码中用到一个类或者函数的时候，不需要使用include/require来包含类库文件或者函数库文件。
 * 
 * 基于Autoloader组件的代码中将不用使用include/require。
 * 
 * Autoloader缓存的是绝对路径，能让Opcode Cache有效缓存文件。
 * 
 *     Autoloader要求类的名字唯一， 不在意类文件的路径和文件名。目前不支持命名空间
 * 
 * 传统的include/require通常存在以下问题。
 * <ul>
 * <li>目录名和文件名变化引起程序代码变化。</li>
 * <li>Windows和Linux对文件路径的大小写和目录分隔符号的处理不同导致代码在不同平台迁移时出现问题。</li>
 * <li>include_path相对路径的性能低（显著地低）。</li>
 * <li>为了保证不重复包含，使用include_once和require_once导致效率低（不是显著的低）。</li>
 * </ul>
 * @author Jianxiang Qin <TalkativeDoggy@gmail.com> Yi Zhao <zhao5908@gmail.com>
 * @category runtime
 * @package Lotusphp\Autoloader
 */
class LtAutoloader
{
	/** 
	 * @var bool true|false 是否自动加载定义了函数的文件。
	 * false 只自动加载定义了class或者interface的文件。
	 * true （默认） 自动加载定义了函数的文件。
	 */
	public $isLoadFunction = true;
	
	/**
	 * @var array 要扫描的文件类型
	 * 若该属性设置为array("php","inc","php3")，
	 * 则扩展名为"php","inc","php3"的文件会被扫描，
	 * 其它扩展名的文件会被忽略
	 */
	public $allowFileExtension = array('php', 'inc');
	
	/**
	 * @var array 不扫描的目录
	 * 若该属性设置为array(".svn", ".setting")，
	 * 则所有名为".setting"的目录也会被忽略
	 */
	public $skipDirNames = array('.svn');

	/** @var LtStoreFile 存储句柄默认使用 @link LtStoreFile */
	public $storeHandle;
	
	/** @var array 指定需要自动加载的目录列表 */
	public $autoloadPath;
	
	/** @var bool 开发模式下 true 每次都会扫描目录列表 生产环境下 false 只扫描一次 */
	public $devMode = true;
	
	/** @var array 定义了函数的文件地图 */
	private $functionFileMapping;
	
	/** @var array 定义了类的文件地图 */
	private $classFileMapping;
	
	/** @var int 所有扫描文件中最晚修改时间 */
	private $lastFileTime;
	
	/** @var int 当前扫描文件的修改时间 */
	private $currFileTime;
	
	/** @var boolean 是否更新fileMap */
	private $failFileMap;
	
	/** @var array function files */
	private $functionFiles;

	/**
	 * 递归扫描指定的目录列表，根据@see LtAutoloader::$isLoadFunction是否加载全部的函数定义文件。
	 * 注册自动加载函数，按需加载类文件。
	 * @return void
	 */
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
		if ($this->isLoadFunction)
		{
			$this->loadFunction();
		}
		spl_autoload_register(array($this, "loadClass"));
	}

	/**
	 * Autoloader扫描项目，若某个php文件中定义了函数，则此文件的绝对路径被缓存，
	 * 每次执行LtAutoloader->init()方法时，自动include所有定义了函数的php文件。
	 * @return void 
	 */
	protected function loadFunction()
	{
		if ($functionFiles = $this->storeHandle->get(".functions"))
		{
			foreach ($functionFiles as $functionFile)
			{
				include_once($functionFile);
			}
		}
	}

	/**
	 * 被注册的自动加载函数
	 * @param string $className
	 * @return void 
	 */
	protected function loadClass($className)
	{
		if ($classFile = $this->storeHandle->get(strtolower($className)))
		{
			include($classFile);
		}
	}

	/**
	 * 将目录分隔符号统一成linux目录分隔符号/
	 * @param string $path
	 * @return boolean
	 */
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
	 * 将字符串和多维数组转换成一维数组
	 * @param array|string
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
	 * 递归扫描目录包含子目录，保存自动加载的文件地图。
	 * @param array $dirs one-dimensional
	 * @return void 
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
				if (in_array($file, array(".", "..")) || in_array($file, $this->skipDirNames))
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

    /**
     * 分析出字符串中的类，接口，函数。 
     * @param string $src
     * @return array
     */
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

	/**
	 * 保存类名、接口名和对应的文件绝对路径。 
	 * @param string $className
	 * @param string $file
	 * @return boolean
	 */
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

	/**
	 * 保存函数名和对应的文件绝对路径
	 * @param string $functionName
	 * @param string $file
	 * @return boolean
	 */
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

	/**
	 * 将文件添加到自动加载的FileMap，
	 * 添加之前会判断自从上次扫描后有没有修改，若没有修改则无需重复添加，
	 * 若修改过，则分析文件内容，根据内容中包含的类、接口，函数添加到FileMap
	 * @param string $file
	 * @return boolean
	 */
	protected function addFileMap($file)
	{
		if (!in_array(pathinfo($file, PATHINFO_EXTENSION), $this->allowFileExtension))
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
