<?php
/**
 * The View class
 */
class LtView
{
	static public function factory($responseType)
	{
		if (!empty($responseType))
		{
			$viewClass = ucfirst($responseType) . "View";
			if (!class_exists($viewClass))
			{
				DebugHelper::debug('VIEW_CLASS_NOT_FOUND', array('class' => $viewClass));
			}
			else
			{
				return new $viewClass();
			}
		}
		else
		{
			DebugHelper::debug('VIEW_TYPE_NOT_SPECIFIED');
		}
	}
}
