<?php
class LtDbSqlAdapterPgsql extends LtDbSqlAdapter
{
	public function limit($limit, $offset)
	{
		return " LIMIT $limit OFFSET $offset";
	}
	public function setCharset($charset)
	{
		return "SET CLIENT_ENCODING TO '$charset'";
	}
	public function setSchema($schema)
	{
		return "SET SEARCH_PATH TO '$schema';";
	}

	public function showSchemas($database)
	{

	}
	public function getSchemas($queryResult)
	{
		
	}
	public function showTables($schema)
	{
//$sql=<<<EOD
//SELECT table_name, table_schema FROM information_schema.tables
//WHERE table_schema=:schema
//EOD;
	}
	public function getTables($queryResult)
	{
		
	}
	public function showFields($table)
	{
		//return "SELECT a.attnum, a.attname AS field, t.typname AS type, format_type(a.atttypid, a.atttypmod) AS complete_type, "
		 //. "a.attnotnull AS isnotnull, "
		 //. "( SELECT 't' "
		 //. 'FROM pg_index '
		 //. "WHERE c.oid = pg_index.indrelid "
		 //. "AND pg_index.indkey[0] = a.attnum "
		 //. "AND pg_index.indisprimary = 't') AS pri, "
		 //. "(SELECT pg_attrdef.adsrc "
		 //. 'FROM pg_attrdef '
		 //. "WHERE c.oid = pg_attrdef.adrelid "
		 //. "AND pg_attrdef.adnum=a.attnum) AS default "
		 //. "FROM pg_attribute a, pg_class c, pg_type t "
		 //. "WHERE c.relname = '{$table}' "
		 //. 'AND a.attnum > 0 '
		 //. "AND a.attrelid = c.oid "
		 //. "AND a.atttypid = t.oid "
		 //. 'ORDER BY a.attnum ';
//---------------------------------------------------------------------------
//		$sql=<<<EOD
//SELECT a.attname, LOWER(format_type(a.atttypid, a.atttypmod)) AS type, d.adsrc, a.attnotnull, a.atthasdef
//FROM pg_attribute a LEFT JOIN pg_attrdef d ON a.attrelid = d.adrelid AND a.attnum = d.adnum
//WHERE a.attnum > 0 AND NOT a.attisdropped
//	AND a.attrelid = (SELECT oid FROM pg_catalog.pg_class WHERE relname=:table
//		AND relnamespace = (SELECT oid FROM pg_catalog.pg_namespace WHERE nspname = :schema))
//ORDER BY a.attnum
//EOD;
	}
	public function getFields($queryResult)
	{
		
	}
}