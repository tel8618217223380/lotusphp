<?php
/**
 * Database Table abstract
 *
 * @todo pretty join support
 */
class DbTable
{

	/**
	 * The created field name
	 *
	 * @var string
	 */
	public $createdColumn;

	/**
	 * The database handle
	 *
	 * @var DbAdapter
	 */
	public $db;

	/**
	 * The group name
	 *
	 * @var string
	 */
	public $group;

	/**
	 * The modified field name
	 *
	 * @var string
	 */
	public $modifiedColumn;

	/**
	 * The schema name
	 *
	 * @var string
	 */
	public $schema = null;

	/**
	 * The table name
	 *
	 * @var string
	 */
	public $tableName;

	/**
	 * The fields array
	 *
	 * @var array
	 */
	protected $_fields;

	/**
	 * The primary key
	 *
	 * @var string
	 */
	protected $_primaryKey;

	/**
	 * Build table's field list
	 *
	 * @return array
	 */
	protected function _buildFieldList()
	{
		if (!$this->_fields)
		{
			$this->_fields = $this->db->showFields($this->tableName);
		}
		if (!$this->_primaryKey) 
		{
			foreach($this->_fields as $field)
			{
				if ($field['primary'] == 1)
				{
					$this->_primaryKey = $field['name'];
					break;
				}
			}
		}
	}

	/**
	 * The constructor function
	 *
	 */
	public function __construct()
	{
		$this->createdColumn = isset($this->createdColumn) ? $this->createdColumn : 'created';
		$this->modifiedColumn = isset($this->modifiedColumn) ? $this->modifiedColumn : 'modified';
		if (get_parent_class($this))
		{
			$this->db = $this->getAdapterInstance();
			$this->db->setGroup($this->group);
			$this->db->setSchema($this->schema);
		}
	}

	/**
	 * A shortcut to SELECT COUNT(*) FROM table WHERE condition
	 *
	 * @param array $args
	 * @return integer
	 * @example count(array('expression' => 'id < :id', 'value' => array('id' => 10)));
	 */
	public function count($args = null)
	{
		$selectTemplate = 'SELECT COUNT(*) AS total FROM %s%s';
		$where = isset($args['where']['expression']) ? ' WHERE ' . $args['where']['expression'] : '';
		$bind = isset($args['where']['value']) ? $args['where']['value'] : array();
		$join = isset($args['join']) ? ' ' . $args['join'] : '';
		$sql = sprintf($selectTemplate, $this->tableName, $join . $where);
		$queryResult = $this->db->query($sql, $bind, true);
		$rows = $queryResult['rows'];
		return $rows[0]['total'];
	}

	/**
	 * Delete a row by primary key
	 *
	 * @param string $primaryKeyId
	 * @return string
	 * @example delete(10);
	 */
	public function delete($primaryKeyId)
	{
		$this->_buildFieldList();
		$where['expression'] = $this->_primaryKey . '=:' . $this->_primaryKey;
		$where['value'][$this->_primaryKey] = $primaryKeyId;
		return $this->deleteRows($where);
	}

	/**
	 * Delete many rows from table
	 * Please use this method carefully!
	 *
	 * @param array $args
	 * @return integer
	 * @example deleteRows(array('expression' => "id > :id", 'value' => array('id' => 2)));
	 */
	public function deleteRows($args = null)
	{
		$deleteTemplate = 'DELETE FROM %s%s';
		$where = isset($args['expression']) ? ' WHERE ' . $args['expression'] : '';
		$bind = isset($args['value']) ? $args['value'] : array();
		$sql = sprintf($deleteTemplate, $this->tableName, $where);
		$queryResult = $this->db->query($sql, $bind);
		return $queryResult['row_total'];
	}

	/**
	 * Fetch one row from table by primary key
	 *
	 * @param string $primaryKeyId
	 * @param array $args
	 * @param boolean $useSlave
	 * @return array
	 *
	 * @example fetch(10)
	 */
	public function fetch($primaryKeyId, $args = null, $useSlave = true)
	{
		$this->_buildFieldList();
		$fetchRowsArgs['where']['expression'] =  $this->tableName . '.' . $this->_primaryKey . '=:' . $this->_primaryKey;
		$fetchRowsArgs['where']['value'][$this->_primaryKey] = $primaryKeyId;
		$fetchRowsArgs['fields'] = isset($args['fields']) ? $args['fields'] : null;
		$fetchRowsArgs['join'] = isset($args['join']) ? $args['join'] : null;
		$fetchResult = $this->fetchRows($fetchRowsArgs, $useSlave);
		return $fetchResult ? $fetchResult[0] : $fetchResult;
	}

	/**
	 * Fetch many rows from table
	 *
	 * @param array $args
	 * @param boolean $useSlave
	 * @return array
	 *
	 * @example fetchRows(array('where' => array('expression' => "id > :id", 'value' => array('id' => 2))));
	 */
	public function fetchRows($args = null, $useSlave = true)
	{
		$selectTemplate = 'SELECT %s FROM %s%s';
		$fields = isset($args['fields']) ? $args['fields'] : '*';
		$where = isset($args['where']['expression']) ? ' WHERE ' . $args['where']['expression'] : '';
		$bind = isset($args['where']['value']) ? $args['where']['value'] : array();
		$join = isset($args['join']) ? ' ' . $args['join'] : '';
		$orderby = isset($args['orderby']) ? ' ORDER BY ' . $args['orderby'] : '';
		$groupby = isset($args['groupby']) ? ' GROUP BY ' . $args['groupby'] : '';
		$sql = sprintf($selectTemplate, $fields, $this->tableName, $join . $where . $groupby . $orderby);
		if (isset($args['limit']))
		{
			$offset = isset($args['offset']) ? $args['offset'] : 0;
			$sql = $this->db->limit($sql, $args['limit'], $offset);
		}
		$queryResult = $this->db->query($sql, $bind, $useSlave);
		return $queryResult['rows'];
	}

	/**
	 * Get adapter instance by driver name
	 *
	 * @return resource
	 */
	public function getAdapterInstance()
	{
		$nodeArray = array_keys(Config::$app["db_server"][$this->group]);
		$masterIndexArray = array_keys(Config::$app["db_server"][$this->group][$nodeArray[0]]['master']);
		$config = array_merge(Db::$defaultConfig, Config::$app["db_server"][$this->group][$nodeArray[0]]['master'][$masterIndexArray[0]]);
		return Db::factory($config['adapter']);
	}

	/**
	 * Insert one row into table, then return the inserted row's pk
	 *
	 * @param array $args
	 * @return string
	 * @example insert(array('name' => 'lily', 'age' => '12'));
	 */
	public function insert($args = null)
	{
		$this->_buildFieldList();
		$insertTemplate = 'INSERT INTO %s (%s) VALUES (%s)';
		$fields = array();
		$placeHolders = array();
		foreach($args as $field => $value)
		{
			if (isset($this->_fields[$field]))
			{
				$fields[] = $field;
				$placeholders[] = ":$field";
				$values[$field] = $value;
			}
		}
		if (isset($this->_fields[$this->createdColumn]) && !isset($args[$this->createdColumn]))
		{
			$fields[] = $this->createdColumn;
			$placeholders[] = ':' . $this->createdColumn;
			$values[$this->createdColumn] = time();
		}
		if (isset($this->_fields[$this->modifiedColumn]) && !isset($args[$this->modifiedColumn]))
		{
			$fields[] = $this->modifiedColumn;
			$placeholders[] = ':' . $this->modifiedColumn;
			$values[$this->modifiedColumn] = time();
		}
		$sql = sprintf($insertTemplate, $this->tableName, implode(",", $fields), implode(",", $placeholders));
		$bind = $values;
		$queryResult = $this->db->query($sql, $bind);
		return isset($args[$this->_primaryKey]) ? $args[$this->_primaryKey] : $this->db->lastInsertId($this->tableName, $this->_primaryKey);
	}

	/**
	 * Update one row  by primary key
	 *
	 * @param string $primaryKeyId
	 * @param array $args
	 * @return integer
	 * @example update(1, array('name' => 'lily', 'age' => '18'));
	 */
	public function update($primaryKeyId, $args = null)
	{
		$this->_buildFieldList();
		$where['expression'] = $this->_primaryKey . '=:' . $this->_primaryKey;
		$where['value'][$this->_primaryKey] = $primaryKeyId;
		return $this->updateRows($where, $args);
	}

	/**
	 * Update manay rows
	 * Please use this method carefully!
	 *
	 * @param array $where
	 * @param array $args
	 * @return integer
	 * @example updateRows(array('expression' => "id > :id", 'value' => array('id' => 2)), array('name' => 'kiwi', 'age' => '1'));
	 */
	public function updateRows($where, $args = null)
	{
		$this->_buildFieldList();
		$updateTemplate = 'UPDATE %s SET %s%s';
		$fields = array();
		$bindParameters = array();
		$placeholderStyle = isset($where['value']) && array_key_exists(0, $where['value']) ? 'questionMark' : 'named';
		foreach($args as $field => $value)
		{
			if (isset($this->_fields[$field]))
			{
				if ($args[$field] instanceof DbExpression)
				{
					$fields[] = "$field=" . $args[$field]->__toString();
				}
				else
				{
					if ('named' == $placeholderStyle)
					{
						$fields[] = "$field=:$field";
						$bindParameters[$field] = $args[$field];
					}
					else
					{
						$fields[] = "$field=?";
						$bindParameters[] = $args[$field];
					}
				}
			}
		}
		if (isset($this->_fields[$this->modifiedColumn]) && !isset($args[$this->modifiedColumn]))
		{
			if ('named' == $placeholderStyle)
			{
				$fields[] = $this->modifiedColumn . '=:' . $this->modifiedColumn;
				$bindParameters[$this->modifiedColumn] = time();
			}
			else
			{
				$fields[] = $this->modifiedColumn . '=?';
				$bindParameters[] = time();
			}
		}
		$whereCause = isset($where['expression']) ? ' WHERE ' . $where['expression'] : '';
		$bind = isset($where['value']) ? array_merge($bindParameters, $where['value']) : $bindParameters;
		$sql = sprintf($updateTemplate, $this->tableName, implode(",", $fields), $whereCause);
		$queryResult = $this->db->query($sql, $bind);
		return $queryResult['row_total'];
	}
}
