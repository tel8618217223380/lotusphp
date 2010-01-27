<?php
class LtTemplateView
{
	public $layoutDir;

	public $templateDir;

	public $compiledDir;

	public $layout;

	public $template;

	public $compiled;

	public function __construct()
	{
		//
	}

	public function render()
	{
		if (isset($this -> layout) && strlen($this -> layout))
		{
			include $this -> template(true);
		} 
		else
		{
			include $this -> template();
		} 
	} 

	public function template($islayout = false)
	{
		if ($islayout)
		{
			$tplfile = $this -> layoutDir . $this -> layout . '.php';
			$objfile = $this -> compiledDir . '/layout/' . $this -> layout . '.php';
		} 
		else
		{
			$tplfile = $this -> templateDir . $this -> template . '.php';
			$objfile = $this -> compiledDir . $this -> template . '.php';
		} 
		$iscompile = true; 
		// if (file_exists($objfile)) //性能
		if (is_file($objfile))
		{
			if (@filemtime($tplfile) <= @filemtime($objfile))
			{
				$iscompile = false;
			} 
		} 
		if ($iscompile)
		{
			$str = file_get_contents($tplfile);
			if (!$str)
			{
				trigger_error('Template file Not found or have no access!', E_USER_ERROR);
			} 
			$str = $this -> parse($str);
			file_put_contents($objfile, $str);
		} 
		return $objfile;
	} 

	/**
	 * 读模板进行替换后写入编译目录
	 * 所有变量用{}
	 * 
	 * @param string $tplfile ：模板文件名
	 * @param string $objfile ：编译后的文件名
	 * @return 
	 */
	protected function parse($str)
	{ 
 		$str = str_replace(array('<?php exit?>', '<?php exit;?>'), array('', ''), $str); 
		// 删除行首尾空白
		$str = preg_replace("/([\r\n]+)[\t ]+/s", "\\1", $str);
		$str = preg_replace("/[\t ]+([\r\n]+)/s", "\\1", $str); 
		// 删除 html 注释 <!--  -->
		$str = preg_replace("/\<\!\-\-\s*\{(.+?)\}\s*\-\-\>/s", "{\\1}", $str);
		$str = preg_replace("/\<\!\-\-\s*\-\-\>/s", "", $str); 
		// 删除 javascript 单行注释//
		$str = preg_replace("/\/\/[a-zA-Z0-9_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*[\r\n]/", "", $str); 
		// 删除 javascript 和 css 多行注释 /*有问题啊*/
		$str = preg_replace("/\/\*[^\/]*\*\//s", "", $str); 
		// --
		$str = preg_replace("/\{template\s+([a-z0-9_]+)\}/is", "<?php include template('\\1'); ?>", $str);
		$str = preg_replace("/\{template\s+(.+)\}/", "<?php include template(\\1); ?>", $str);
		$str = preg_replace("/\{include\s+(.+)\}/", "<?php include \\1; ?>", $str);
		$str = preg_replace("/\{php\s+(.+)\}/", "<?php \\1?>", $str); 
		// --
		$str = preg_replace("/\{if\s+(.+?)\}/", "<?php if(\\1) { ?>", $str);
		$str = preg_replace("/\{else\}/", "<?php } else { ?>", $str);
		$str = preg_replace("/\{elseif\s+(.+?)\}/", "<?php } elseif (\\1) { ?>", $str);
		$str = preg_replace("/\{\/if\}/", "<?php } ?>", $str); 
		// --
		$str = preg_replace("/\{loop\s+(\S+)\s+(\S+)\}/", "<?php if(is_array(\\1)) foreach(\\1 as \\2) { ?>", $str);
		$str = preg_replace("/\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}/", "<?php if(is_array(\\1)) foreach(\\1 as \\2=>\\3) { ?>", $str);
		$str = preg_replace("/\{\/loop\}/", "<?php } ?>", $str); 
		// url生成
		$str = preg_replace("/\{url\(([^}]+)\)\}/", "<?php echo C('LtUrl')->generate(\\1);?>", $str); 
		// 函数
		$str = preg_replace("/\{([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\s*\(([^{}]*)\))\}/", "<?php echo \\1;?>", $str);
		$str = preg_replace("/\{\\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))\}/", "<?php echo \$\\1;?>", $str); 
		// 变量
		$str = preg_replace("/(\\\$[a-zA-Z0-9_\[\]\'\"\$\x7f-\xff]+)\.([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/s", "\\1['\\2']", $str);
		$str = preg_replace("/\{(\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}/", "<?php echo \\1;?>", $str);
		$str = preg_replace("/\{(\\$[a-zA-Z0-9_\[\]\'\"\$\x7f-\xff]+)\}/es", "\$this->addquote('<?php echo \\1;?>')", $str); 
		// 类->属性  类->方法
		$str = preg_replace("/\{(\\\$[a-zA-Z0-9_\[\]\'\"\$\x7f-\xff]+\-\>[a-zA-Z_\x7f-\xff][\$\'\"\,\[\]\(\)a-zA-Z0-9_\x7f-\xff]+)\}/", "<?php echo \\1;?>", $str); 
		// 常量
		$str = preg_replace("/\{([A-Z_\x7f-\xff][A-Z0-9_\x7f-\xff]*)\}/s", "<?php echo \\1;?>", $str); 
		// 合并相邻php标记
		$str = preg_replace("/\?\>[\r\n\t ]*\<\?php[\r\n\t ]*/s", "", $str); 
		// 多个合并成一个 第二参数考虑 \\1
		$str = preg_replace("/([\r\n])+/", "\r\n", $str); 
		// 删除第一行
		$str = preg_replace("/^[\r\n]+/", "", $str); 
		// write
		$str = trim($str);
		return $str;
	} 
	/**
	 * 变量加上单引号
	 */
	protected function addquote($var)
	{
		return str_replace("\\\"", "\"", preg_replace("/\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\]/s", "['\\1']", $var));
	} 
} 
