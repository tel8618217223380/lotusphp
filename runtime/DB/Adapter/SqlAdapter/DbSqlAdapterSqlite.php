<?php
class LtDbSqlAdapterSqlite extends LtDbSqlAdapter
{
	public function setCharset($charset)
	{
		// return 'PRAGMA encoding = "' . $charset . '"';
	}
	public function setSchema($schema)
	{
		//return "USE $schema";
	}

	public function beginTransaction()
	{
		return 'BEGIN TRANSACTION';
	}

	public function commit()
	{
		return 'COMMIT TRANSACTION';
	}

	public function rollBack()
	{
		return 'ROLLBACK TRANSACTION';
	}

	public function showSchemas($database)
	{
		//return "SHOW DATABASES";
	}
	public function showTables($schema)
	{
		// 临时表及其索引不在 SQLITE_MASTER 表中而在 SQLITE_TEMP_MASTER 中出现
		return "SELECT name FROM sqlite_master WHERE type='table' UNION ALL SELECT name FROM sqlite_temp_master WHERE type='table' ORDER BY name";
	}
	public function showFields($table)
	{
		return "PRAGMA table_info('" . $table . "')";

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
	public function detectQueryType($sql)
	{
		
	}
}