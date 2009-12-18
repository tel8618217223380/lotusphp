<?php
class LtDbConnectionAdapterPgsql extends LtDbConnectionAdapter
{
	private $_result;

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
		return $func("host=$connConf['host'] port=$connConf['port'] user=$connConf['username'] password=$connConf['password']");
	} 

	public function exec($sql)
	{
		$this->_result = pg_query($this -> connResource, $sql);
		return pg_affected_rows($this -> connResource);
	} 

	public function query($sql)
	{
		$result = pg_query($this -> connResource, $sql);
		$rows = array();
		while ($row = pg_fetch_assoc($result))
		{
			$rows[] = $row;
		} 
		return $rows;
	} 
	/**
	 * MySql's "mysql_insert_id" receives the conection handler as argument 
	 * but PostgreSQL's "pg_last_oid" uses the result handler.
	 */
	public function lastInsertId()
	{
		return pg_last_oid($this->_result);
	} 
} 
