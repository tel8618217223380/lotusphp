<?php
abstract class LtDbSqlAdapter
{
	abstract public function limit($limit, $offset);
	abstract public function setCharset($charset);
	abstract public function setSchema($schema);

	abstract public function showSchemas($database);
	abstract public function getSchemas($queryResult);
	abstract public function showTables($schema);
	abstract public function getTables($queryResult);
	abstract public function showFields($table);
	abstract public function getFields($queryResult);
}