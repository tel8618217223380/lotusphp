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
	 * ����ֵ���Ծͽ�
	 * SELECT
	 * INSERT_ONE
	 * INSERT_MANY
	 * DELETE(����UPDATE)
	 * OTHER
	 * ���ࣺ
	 * 1 DDL�����ݶ�����䣩
	 * 2 DML�����ݲ�����䣩
	 * 2.1 SELECT
	 * 2.2 INSERT_ONE
	 * 2.3 INSERT_MANY/DELETE/UPDATE/REPLACE
	 * 3 ��������ص�
	 * 3.1 USE
	 * 3.2 SET NAMES
	 * 3.4 ...
	 * ����Ч��ʶ���û���������SQL�Ǹ���ģ��þ�����
	 * 1. ���ؽ��
	 * 2. �ܲ�����Salve
	 * 3. �Ƿ�ı䵱ǰ���ӵĻỰ��������USE DB�ı��˵�ǰĬ�ϵ�DB��
	 */
	/**
	 * 
	 * @param string $sql һ����ѯ���
	 * @return string unknow, rs, int, count, conn, bool
	 */
	public function detectQueryType($sql)
	{
		$ret = 'unknow';
		if (preg_match("/^\s*SELECT|^\s*EXPLAIN|^\s*SHOW|^\s*DESCRIBE/i", $sql))
		{
			$ret = 'rs'; //RecorderSet ��¼�� ���� NULL
		}
		else if (preg_match("/^\s*INSERT/i", $sql))
		{
			$ret = 'int'; //�Զ������ֶ�ֵ
		}
		else if (preg_match("/^\s*UPDATE|^\s*DELETE|^\s*REPLACE/i", $sql))
		{
			$ret = 'count'; //Ӱ�����
		}
		else if (preg_match("/^\s*USE/i", $sql))
		{
			$ret = 'conn'; //�ı�����
		}
		else
		{ 
			// SET, CREATE, DROP, ALTER
			$ret = 'bool'; //�����ɹ�����ʧ��
		}
		return $ret;
	}
}
