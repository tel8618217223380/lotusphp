<?php
class LtDbConnectionAdapterMysql extends LtDbConnectionAdapter
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
		return mysql_connect($connConf["host"] . ":" . $connConf["port"], $connConf["username"], $connConf["password"]);
	}

	public function query($sql)
	{
		return mysql_query($sql, $this->connResource);
	}

	public function affectedRows()
	{
		return mysql_affected_rows($this->connResource);
	}

	public function fetchAll($resultRet)
	{
		while($row = mysql_fetch_assoc($resultRet))
		{
			$rows[] = $row;
		}
		return $rows;
	}

	public function foundRows($resultRet)
	{
		return mysql_num_rows($resultRet);
	}

	public function isResultSet($resultRet)
	{
		return is_resource($resultRet);
	}

	public function lastInsertId()
	{
		return mysql_insert_id($this->connResource);
	}
}