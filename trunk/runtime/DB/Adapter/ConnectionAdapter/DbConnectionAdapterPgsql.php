<?php
class LtDbConnectionAdapterPgsql extends LtDbConnectionAdapter
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
		if (isset($connConf['pconnect']) && true == $connConf['pconnect'])
		{
			$func = 'pg_pconnect';
		}
		else
		{
			$func = 'pg_connect';
		}
		return $func("host=$connConf[host] port=$connConf[port] dbname=$connConf[dbname] user=$connConf[username] password=$connConf[password]");
	}

	public function exec($sql)
	{
		$result = pg_query($this -> connResource, $sql);
		return pg_affected_rows($result);
	}

	public function query($sql)
	{
		$result = pg_query($this -> connResource, $sql);
		return pg_fetch_all($result);
	}

	// SELECT CURRVAL(
	// pg_get_serial_sequence('my_tbl_name','id_col_name'));"
	// ------------------------------------------------------
	// CREATE FUNCTION last_insert_id() RETURNS bigint AS $$
	// SELECT lastval();
	// $$ LANGUAGE SQL VOLATILE;
	public function lastInsertId()
	{
		$result = pg_query($this -> connResource, "SELECT lastval()");
		$row = pg_fetch_array($result, 0, PGSQL_NUM);
		return $row[0];
	}
}
