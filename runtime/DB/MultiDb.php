<?php
class LtMultiDb
{
	public $storeHandle;

	protected $group;
	protected $node;

	/**
	 * Get group, node
	 */
	protected function getGroup()
	{
		if (1 == count(LtDbStaticData::$servers))
		{
			$this->group = key(LtDbStaticData::$servers);
		}
		return $this->group;
	}

	protected function getNode()
	{
		if (NULL === $this->node)
		{
			$nodeArray = array_keys(LtDbStaticData::$servers[$this->getGroup()]);
			if (1 === count($nodeArray))
			{
				$this->node = $nodeArray[0];
			}
			else
			{
				DebugHelper::debug('DB_NODE_NOT_SPECIFIED', array('group' => $this->getGroup()));
			}
		}
		return $this->node;
	}
}