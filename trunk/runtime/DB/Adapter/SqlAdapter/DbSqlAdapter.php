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

	/**
	 * 返回值可以就叫
	 * SELECT
	 * INSERT_ONE
	 * INSERT_MANY
	 * DELETE(包括UPDATE)
	 * OTHER
	 * 分类：
	 * 1 DDL（数据定义语句）
	 * 2 DML（数据操作语句）
	 * 2.1 SELECT
	 * 2.2 INSERT_ONE
	 * 2.3 INSERT_MANY/DELETE/UPDATE/REPLACE
	 * 3 跟连接相关的
	 * 3.1 USE
	 * 3.2 SET NAMES
	 * 3.4 ...
	 * 能有效的识别用户传入的这个SQL是干嘛的，好决定：
	 * 1. 返回结果
	 * 2. 能不能用Salve
	 * 3. 是否改变当前连接的会话参数（如USE DB改变了当前默认的DB）
	 */
	/**
	 * 
	 * @param string $sql 一条查询语句
	 * @return string unknow, rs, int, count, conn, bool
	 */
	public function detectQueryType($sql)
	{
		$ret = 'unknow';
		if (preg_match("/^\s*SELECT|^\s*EXPLAIN|^\s*SHOW|^\s*DESCRIBE/i", $sql))
		{
			$ret = 'rs'; //RecorderSet 记录集 或者 NULL
		}
		else if (preg_match("/^\s*INSERT/i", $sql))
		{
			$ret = 'int'; //自动增长字段值
		}
		else if (preg_match("/^\s*UPDATE|^\s*DELETE|^\s*REPLACE/i", $sql))
		{
			$ret = 'count'; //影响计数
		}
		else if (preg_match("/^\s*USE/i", $sql))
		{
			$ret = 'conn'; //改变连接
		}
		else
		{ 
			// SET, CREATE, DROP, ALTER
			$ret = 'bool'; //操作成功或者失败
		}
		return $ret;
	}
}
