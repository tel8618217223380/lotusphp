<?php
class LtDbSqlAdapterMysql extends LtDbSqlAdapter
{
	public function limit($limit, $offset)
	{
		return " LIMIT $limit OFFSET $offset";
	}
	public function setCharset($charset)
	{
		return "SET NAMES " . str_replace('-', '', $charset);
	}
	public function setSchema($schema)
	{
		return "USE $schema";
	}

	public function showSchemas($database)
	{
		return "SHOW DATABASES";
	}
	public function getSchemas($queryResult)
	{
		
	}
	public function showTables($schema)
	{
		return "SHOW TABLES";
	}
	public function getTables($queryResult)
	{
		
	}
	public function showFields($table)
	{
		return "DESCRIBE $table";
	}
	public function getFields($queryResult)
	{
		
	}
}