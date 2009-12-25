<?php
/**
 * 用这个类把LtAutoloader的protected属性和方法暴露出来测试
 */
class LtAutoloaderProxy extends LtAutoloader
{
	public function __get($prop)
	{
		if (isset($this->$prop))
		{
			return $this->$prop;
		}
	}

	public function var2array($var)
	{
		return parent::var2array($var);
	}

	public function preparePath($path)
	{
		return parent::preparePath($path);
	}

	public function addClass($className, $file)
	{
		return parent::addClass($className, $file);
	}

	public function addFunction($functionName, $file)
	{
		return parent::addFunction($functionName, $file);
	}

	public function parseLibNames($src)
	{
		return parent::parseLibNames($src);
	}

	public function isAllowedFile($filename)
	{
		return parent::isAllowedFile($filename);
	}

	public function isSkippedDir($dir)
	{
		return parent::isSkippedDir($dir);
	}

	public function scanDirs($dir)
	{
		return parent::scanDirs($dir);
	}
}
