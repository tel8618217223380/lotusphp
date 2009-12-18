<?php
/**
 * Sqlite 预定义了类 SQLiteDatabase 本实现没有使用。
 * 这里使用的全部是过程函数。
 * 无论是函数还是类，本实现只支持sqlite的2.x系列版本。
 * php5.3新增扩展sqlite3用来支持3.x版本。
 * PDO则同时支持2.x和3.x版本。
 */
class LtDbConnectionAdapterSqlite extends LtDbConnectionAdapter
{
	public function beginTransaction()
	{
		$error = '';
		if (!sqlite_exec($this -> connResource, 'BEGIN TRANSACTION', $error))
		{ 
			// 错误处理
		} 
	} 

	public function commit()
	{
		$error = '';
		if (!sqlite_exec($this -> connResource, 'COMMIT TRANSACTION', $error))
		{ 
			// 错误处理
		} 
	} 

	public function rollBack()
	{
		$error = '';
		if (!sqlite_exec($this -> connResource, 'ROLLBACK TRANSACTION', $error))
		{ 
			// 错误处理
		} 
	} 

	public function connect($connConf)
	{
		if (isset($connConf['pconnect']) && true == $connConf['pconnect'])
		{
			$func = 'sqlite_popen';
		} 
		else
		{
			$func = 'sqlite_open';
		} 
		$error = '';

		$connConf["host"] = '\\' == substr($connConf["host"], -1) || '/' == substr($connConf["host"], -1) ? $connConf["host"] : $connConf["host"] . DIRECTORY_SEPARATOR;

		$connResource = $func($connConf["host"] . $connConf["dbname"], 0666, $error);
		if (!$connResource)
		{ 
			// 错误处理
			return false;
		} 
		else
		{
			return $connResource;
		} 
	} 

	public function query($sql, $bind = null, $type = '')
	{
		$sql = trim($sql);
		if (empty($sql))
		{
			return null;
		} 
		$error = '';
		$func = $type == 'UNBUFFERED' ? 'sqlite_unbuffered_query' : 'sqlite_query';
		if (preg_match("/^\s*SELECT/i", $sql))
		{
			$result = $func($this -> connResource, $sql, SQLITE_ASSOC, $error);
		} 
		else
		{
			$result = sqlite_exec($this -> connResource, $sql, $error);
		} 
		if ($error)
		{ 
			// 错误处理
		} 
		if (is_resource($result))
		{
			return sqlite_fetch_all($result, SQLITE_ASSOC); 
			// $rows = array();
			// while($row = sqlite_fetch_array($result, SQLITE_ASSOC))
			// {
			// $rows[] = $row;
			// }
			// return $rows;
		} 
		else
		{
			return $result;
		} 
	} 

	public function affectedRows()
	{
		return sqlite_changes($this->connResource);
	}

	public function fetchAll($resultRet)
	{
		return sqlite_fetch_all($resultRet, SQLITE_ASSOC); 
	}

	public function foundRows($resultRet)
	{
		return sqlite_num_rows($resultRet);
	}

	public function isResultSet($resultRet)
	{
		return is_resource($resultRet);
	}

	public function lastInsertId()
	{
		return sqlite_last_insert_rowid($this -> connResource);
	} 
} 
