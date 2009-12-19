<?php
class LtDbSqlAdapterMysql extends LtDbSqlAdapter
{
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
	public function showTables($schema)
	{
		return "SHOW TABLES";
	}
	public function showFields($table)
	{
		return "DESCRIBE $table";
	}

	public function beginTransaction()
	{
		return "START TRANSACTION";
	}
	public function commit()
	{
		return "COMMIT";
	}
	public function rollBack()
	{
		return "ROLLBACK";
	}

	public function limit($limit, $offset)
	{
		return " LIMIT $limit OFFSET $offset";
	}
	public function getSchemas($queryResult)
	{
		
	}
	public function getTables($queryResult)
	{
		
	}
	public function getFields($queryResult)
	{
		
	}
}