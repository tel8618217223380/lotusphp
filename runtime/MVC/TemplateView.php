<?php
class LtTemplateView
{
	public $layout;
	public $layoutDir;

	public $template;
	public $templateDir;
	public $compiledDir;

	public $autoCompile; // bool
	public $component; // bool

	public function __construct()
	{
		/**
		 * 自动编译通过对比文件修改时间确定是否编译,
		 * 当禁止自动编译时, 需要手工删除编译后的文件来重新编译.
		 * @todo 尚不支持component include自动编译
		 */
		$this->autoCompile = true;
		$this->component = false;
	}

	public function render()
	{
		if (!empty($this->layout))
		{
			include $this->template(true);
		}
		elseif ($this->component)
		{
			/**
			component在模板中写{component module action} 
			实现合并成一个文件, 不需要include
			*/
		}
		else
		{
			include $this->template();
		}
	}

	public function template($islayout = false)
	{
		if ($islayout)
		{
			$tplfile = $this->layoutDir . $this->layout . '.php';
			$objfile = $this->compiledDir . 'layout/' . $this->layout . '-' . $this->template . '.php';
		}
		else
		{
			$tplfile = $this->templateDir . $this->template . '.php';
			$objfile = $this->compiledDir . $this->template . '.php';
		}
		if (is_file($objfile))
		{
			if ($this->autoCompile)
			{
				if (@filemtime($tplfile) <= @filemtime($objfile))
				{
					$iscompile = false;
				}
				else
				{
					$iscompile = true;
				}
			}
			else
			{
				$iscompile = false;
			}
		}
		else
		{
			// 目标文件不存在,编译模板
			$iscompile = true;
		}
		if ($iscompile)
		{
			$dir = pathinfo($objfile, PATHINFO_DIRNAME);
			if (!is_dir($dir))
			{
				if (!@mkdir($dir, 0777, true))
				{
					trigger_error("Can not create $dir");
				}
			}
			$str = file_get_contents($tplfile);
			if (!$str)
			{
				trigger_error('Template file Not found or have no access!', E_USER_ERROR);
			}
			$str = $this->parse($str);
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
		$str = $this->removeComments($str);
		$str = $this->parseSubTpl($str); 
		$str = $this->parseComponent($str); 
		// --
		$str = str_replace("{LF}", "<?php echo \"\\n\"?>", $str);
		// if else elseif
		$str = preg_replace("/\{if\s+(.+?)\}/", "<?php if(\\1) { ?>", $str);
		$str = preg_replace("/\{else\}/", "<?php } else { ?>", $str);
		$str = preg_replace("/\{elseif\s+(.+?)\}/", "<?php } elseif (\\1) { ?>", $str);
		$str = preg_replace("/\{\/if\}/", "<?php } ?>", $str); 
		// loop
		$str = preg_replace("/\{loop\s+(\S+)\s+(\S+)\}/", "<?php if(is_array(\\1)) foreach(\\1 as \\2) { ?>", $str);
		$str = preg_replace("/\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}/", "<?php if(is_array(\\1)) foreach(\\1 as \\2=>\\3) { ?>", $str);
		$str = preg_replace("/\{\/loop\}/", "<?php } ?>", $str); 
		// url生成
		$str = preg_replace("/\{url\(([^}]+)\)\}/", "<?php echo C('LtUrl')->generate(\\1);?>", $str); 
		// config读取
		$str = preg_replace("/\{config\(([^}]+)\)\}/", "<?php echo C('LtConfig')->get(\\1);?>", $str); 
		// 函数
		$str = preg_replace("/\{([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\s*\(([^{}]*)\))\}/", "<?php echo \\1;?>", $str);
		$str = preg_replace("/\{\\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))\}/", "<?php echo \$\\1;?>", $str); 
		// 变量
		$str = preg_replace("/(\\\$[a-zA-Z0-9_\[\]\'\"\$\x7f-\xff]+)\.([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/s", "\\1['\\2']", $str); 
		// 内置变量 code message data
		$str = preg_replace("/\{\\\$([code|message|data][a-zA-Z0-9_\[\]\'\"\$\x7f-\xff]*)\}/es", "\$this->addquote('<?php if (isset(\$this->\\1)) echo \$this->\\1;?>')", $str);

		$str = preg_replace("/\{(\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}/", "<?php echo \\1;?>", $str);
		$str = preg_replace("/\{(\\$[a-zA-Z0-9_\[\]\'\"\$\x7f-\xff]+)\}/es", "\$this->addquote('<?php echo \\1;?>')", $str); 
		// 类->属性  类->方法
		$str = preg_replace("/\{(\\\$[a-zA-Z0-9_\[\]\'\"\$\x7f-\xff][+\-\>\$\'\"\,\[\]\(\)a-zA-Z0-9_\x7f-\xff]+)\}/es", "\$this->addquote('<?php echo \\1;?>')", $str); 
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
	 * 如果是数字就不加单引号, 如果已经加上单引号或者双引号保持不变
	 */
	protected function addquote($var)
	{
		preg_match_all("/\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\]/s", $var, $vars);
		foreach($vars[1] as $k => $v)
		{
			if (is_numeric($v))
			{
				$var = str_replace($vars[0][$k], "[$v]", $var);
			}
			else
			{
				$var = str_replace($vars[0][$k], "['$v']", $var);
			}
		}
		return str_replace("\\\"", "\"", $var);
	}

	/**
	 * 模板中第一行可以写exit函数防止浏览
	 * 删除行首尾空白, html javascript css注释
	 */
	protected function removeComments($str)
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
		return $str;
	}

	/**
	 * 解析多个{include path/file}合并成一个文件
	 * 
	 * @todo 实现修改子模板后自动重新编译
	 */
	protected function parseSubTpl($str)
	{
		$countSubTpl = preg_match_all("/\{include\s+(.+)\}/", $str, $tvar);
		while ($countSubTpl > 0)
		{
			foreach($tvar[1] as $k => $subfile)
			{
				eval("\$subfile = $subfile;");
				if (is_file($subfile))
				{
					$subTpl = file_get_contents($subfile);
				}
				else
				{
					$subTpl = 'SubTemplate not found:' . $subfile;
				}
				$str = str_replace($tvar[0][$k], $subTpl, $str);
			}
			$countSubTpl = preg_match_all("/\{include\s+(.+)\}/", $str, $tvar);
		}
		$str = $this->removeComments($str);
		return $str;
	}

	/**
	 * 解析多个{component module action}合并成一个文件
	 * 
	 * @todo 实现修改子模板后自动重新编译
	 */
	protected function parseComponent($str)
	{
		$countCom = preg_match_all("/\{component\s+([a-zA-Z0-9\.\-_]+)\s+([a-zA-Z0-9\.\-_]+)\}/", $str, $tvar);
		while ($countCom > 0)
		{
			$i = 0;
			while ($i < $countCom)
			{
				$comfile = $this->templateDir . $tvar[1][$i].'_'.$tvar[2][$i].'.php';
				if (is_file($comfile))
				{
					$subTpl = file_get_contents($comfile);
				}
				else
				{
					$subTpl = 'SubTemplate not found:' . $comfile;
				}
				$str = str_replace($tvar[0][$i], $subTpl, $str);
				$i++;
			}
			$countCom = preg_match_all("/\{component\s+([a-zA-Z0-9\.\-_]+)\s+([a-zA-Z0-9\.\-_]+)\}/", $str, $tvar);
		}
		$str = $this->removeComments($str);
		return $str;
	}
}
