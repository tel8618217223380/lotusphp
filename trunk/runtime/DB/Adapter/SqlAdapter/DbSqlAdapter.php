<?php
abstract class LtDbSqlAdapter
{
	/**
	 * Return SQL statements
	 */
	abstract public function setCharset($charset);
	abstract public function setSchema($schema);

	abstract public function showSchemas($database);
	abstract public function showTables($schema);
	abstract public function showFields($table);

	abstract public function beginTransaction();
	abstract public function commit();
	abstract public function rollBack();

	abstract public function limit($limit, $offset);

	/**
	 * Retrive recordset
	 */
	abstract public function getSchemas($queryResult);
	abstract public function getTables($queryResult);
	abstract public function getFields($queryResult);
}