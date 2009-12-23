<?php
class LtAutoloaderConfig
{
	public $cacheFileRoot = "/tmp/LtAutoloader/";
	public $cacheTtl = 1;
	public $loadFunction = true;
	public $allowFileExtension = array("php", "inc");
	public $skipDirNames = array(".", "..", ".svn");
}