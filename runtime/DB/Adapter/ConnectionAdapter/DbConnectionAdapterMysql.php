<?php
class LtDbConnectionAdapterMysql extends LtDbConnectionAdapter
{
	public function connect($connConf)
	{
		return mysql_connect($connConf["host"] . ":" . $connConf["port"], $connConf["username"], $connConf["password"]);
	}

	public function exec($sql)
	{
		return mysql_query($sql, $this->connResource) ? mysql_affected_rows($this->connResource) : false;
	}

	public function query($sql)
	{
		$result = mysql_query($sql, $this->connResource);
		$rows = array();
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

	public function escape($sql)
	{
		return mysql_escape_string($sql);
	}
}