<?php
class LtDbConnectionAdapterMysqli extends LtDbConnectionAdapter
{
	public function beginTransaction()
	{
		
	}

	public function commit()
	{
		
	}

	public function rollBack()
	{
		
	}

	public function connect($connConf)
	{
		return new mysqli($connConf["host"], $connConf["username"], $connConf["password"], $connConf["dbname"], $connConf["port"]);
	}

	public function query($sql)
	{
		$rows = array();
		$result = $this->connResource->query($sql);
		if (is_object($result))
		{
			while($row = $result->fetch_assoc())
			{
				$rows[] = $row;
			}
			return $rows;
		}
		else
		{
			return $result;
		}
	}
}