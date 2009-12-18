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

	public function exec($sql)
	{
		$this->connResource->query($sql);
		return $this->connResource->affected_rows;
	}

	public function query($sql)
	{
		$rows = array();
		$result = $this->connResource->query($sql);
		while($row = $result->fetch_assoc())
		{
			$rows[] = $row;
		}
		return $rows;
	}

	public function lastInsertid()
	{
		return $this->connResource->insert_id;
	}
}