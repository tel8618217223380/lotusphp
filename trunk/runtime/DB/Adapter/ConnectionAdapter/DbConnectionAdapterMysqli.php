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
		
	}

	public function query($sql, $bind = null)
	{
		$rows = array();
		$result = mysql_query($sql, $this->connResource);
		while($row = mysql_fetch_assoc($result))
		{
			$rows[] = $row;
		}
		return $rows;
	}

	public function lastInsertId()
	{
		return mysql_insert_id($this->connResource);
	}
}