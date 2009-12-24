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
		$connConf["host"] = '\\' == substr($connConf["host"], -1) || '/' == substr($connConf["host"], -1) ? $connConf["host"] : $connConf["host"] . DIRECTORY_SEPARATOR;
		$error = '';
		$connResource = $func($connConf["host"] . $connConf["dbname"], 0666, $error);
		if (!$connResource)
		{
			trigger_error($error, E_USER_ERROR);
		} 
		else
		{
			return $connResource;
		} 
	} 

	public function exec($sql)
	{
		sqlite_exec($this->connResource, $sql); 
		// echo '<pre>';
		// print_r(debug_backtrace());
		// debug_print_backtrace();
		// echo '</pre>';
		// delete from table 结果为0，原因未知。
		return sqlite_changes($this->connResource);
	} 

	public function query($sql)
	{
		$result = sqlite_query($this->connResource, $sql, SQLITE_ASSOC);
		return sqlite_fetch_all($result, SQLITE_ASSOC);
	} 

	public function lastInsertId()
	{
		return sqlite_last_insert_rowid($this->connResource);
	} 

	public function escape($string)
	{
		return sqlite_escape_string($string);
	} 
} 
