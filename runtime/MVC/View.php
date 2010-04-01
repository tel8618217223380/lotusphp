<?php
/**
 * The View class
 */
class LtView
{
	public $configHandle;

	public $layoutDir;

	public $templateDir;

	public $layout;

	public $template;

	public function render()
	{
		if (!empty($this->layout))
		{
			include($this->layoutDir . $this->layout . '.php');
		}
		else
		{
			include($this->templateDir . $this->template . '.php');
		}
	}

	public function get()
	{
		$numargs = func_num_args();
		$argList = func_get_args();
		$out = $this->configHandle->get($argList[0]);
		for ($i = 1; $i < $numargs; $i++)
		{
			$out = $out[$argList[$i]];
		}
		return $out;
	}
}
