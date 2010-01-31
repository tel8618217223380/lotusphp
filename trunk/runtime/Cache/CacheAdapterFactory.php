<?php
class LtCacheAdapterFactory
{
	public function getConnectionAdapter($adapter)
	{	
		$adapterClassName = "LtCacheAdapter" . ucfirst($adapter);
		if(!class_exists($adapterClassName))
		{
			trigger_error('Invalid adapter');
		}
		return new $adapterClassName;
	}
}