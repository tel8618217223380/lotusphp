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
		$result = mysql_query($sql, $this->connResource);
		if (false === $result)//Query failed
		{
			return $result;
		}
		if (is_resource($result))//SELECT, SHOW, DESCRIBE
		{
			if (0 === mysql_num_rows($result))
			{
				return null;
			}
			while($row = mysql_fetch_assoc($result))
			{
				$rows[] = $row;
			}
			return $rows;
		}
		else if ("insert" == strtolower(trim(substr($sql, 0, 6))))//INSERT
		{
			return mysql_insert_id($this->connResource);
		}
		else if (in_array(strtolower(trim(substr($sql, 0, 6))), array("update", "delete", "replac")))//UPDATE, DELETE, REPLACE
		{
			return mysql_affected_rows($this->connResource);
		}
		else
		{
			return $result;
		}
	}
}