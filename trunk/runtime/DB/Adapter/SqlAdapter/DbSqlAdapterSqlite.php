<?php
class LtDbSqlAdapterSqlite implements LtDbSqlAdapter
{
	public function setCharset($charset)
	{
		// return 'PRAGMA encoding = "' . $charset . '"';
		return '';
	}
	public function setSchema($schema)
	{
		return '';
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
		return '';
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
		if (preg_match("/^\s*SELECT|^\s*EXPLAIN|^\s*SHOW|^\s*DESCRIBE/i", $sql))
		{
			$ret = 'SELECT';
		}
		else if (preg_match("/^\s*INSERT/i", $sql))
		{
			$ret = 'INSERT';
		}
		else if (preg_match("/^\s*UPDATE|^\s*DELETE|^\s*REPLACE/i", $sql))
		{
			$ret = 'CHANGE_ROWS';
		}
		else if (preg_match("/^\s*USE|^\s*SET/i", $sql))
		{
			$ret = 'SET_SESSION_VAR';
		}
		else
		{
			$ret = 'OTHER';
		}
		return $ret;
	}
}