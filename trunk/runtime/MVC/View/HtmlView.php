<?php
/**
 * Render the view as html.
 */
class HtmlView extends AbstractView
{
	public $layoutDir;

	public $templateDir;

	public $layout;

	public $template;

	public function render()
	{
		if (isset($this->layout) && strlen($this->layout))
		{
			include($this->layoutDir . $this->layout . '.php');
		}
		else
		{
			include($this->templateDir . $this->template . '.php');
		}
	}
}