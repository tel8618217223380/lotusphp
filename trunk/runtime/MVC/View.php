<?php
/**
 * The View class
 * @author Jianxiang Qin <TalkativeDoggy@gmail.com>
 * @license http://opensource.org/licenses/BSD-3-Clause New BSD License
 * @version svn:$Id$
 */

/**
 * The View class
 * @author Jianxiang Qin <TalkativeDoggy@gmail.com>
 * @category runtime
 * @package   Lotusphp\MVC
 */
class LtView
{
	/** @var LtConfig config handle */
	public $configHandle;
	
	/** @var string layout dir */
	public $layoutDir;

	/** @var string template dir */
	public $templateDir;

	/** @var string layout name*/
	public $layout;

	/** @var string template name such as module-action */
	public $template;

	/**
	 * render
	 */
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
}
