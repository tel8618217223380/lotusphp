<?php
class LtDbHandle
{
	public $group;
	public $node;
	public $connectionAdapter;
	public $sqlAdapter;

	/**
	 * Trancaction methods
	 */
	public function beginTransaction()
	{
		return $this->connectionAdapter->exec($this->sqlAdapter->beginTransaction());
	}

	public function commit()
	{
		return $this->connectionAdapter->exec($this->sqlAdapter->commit());
	}

	public function rollBack()
	{
		return $this->connectionAdapter->exec($this->sqlAdapter->rollBack());
	}

	/**
	 * Execute an sql query
	 * @param $sql
	 * @param $bind
	 * @param $forceUseMaster
	 * @return false on query failed
	 *         --sql type--                         --return value--
	 *         SELECT, SHOW, DESECRIBE, EXPLAIN     rowset or NULL when no record found
	 *         INSERT                               the ID generated for an AUTO_INCREMENT column
	 *         UPDATE, DELETE, REPLACE              affected count
	 *         USE, DROP, ALTER, CREATE, SET etc    true
	 * @notice 每次只能执行一条SQL
	 *         不要通过此接口执行USE DATABASE, SET NAMES这样的语句
	 */
	public function query($sql, $bind = null, $forceUseMaster = false)
	{
		if(empty($sql))
		{
			// trigger_error('Empty the SQL statement', E_USER_WARNING);
			return null;
		}
		$connectionManager = new LtDbConnectionManager;
		if (is_array($bind) && 0 < count($bind))
		{
			$sql = $this->bindParameter($sql, $bind);
		}
		if (preg_match("/^\s*SELECT|^\s*EXPLAIN|^\s*SHOW|^\s*DESCRIBE/i", $sql))
		{//read query (use ): SELECT, EXPLAIN, SHOW, DESCRIBE
			if (!$forceUseMaster && isset(LtDbStaticData::$servers[$this->group][$this->node]["slave"]))
			{
				$adapters = $connectionManager->getAdapters($this->group, $this->node, "slave");
			}
			else
			{
				$adapters = $connectionManager->getAdapters($this->group, $this->node, "master");
			}
			$this->connectionAdapter = $adapters["connectionAdapter"];
			$this->sqlAdapter = $adapters["sqlAdapter"];
			$result = $this->connectionAdapter->query($sql);
			if (empty($result))
			{
				return null;
			}
			else
			{
				return $result;
			}			
		}
		else
		{
			$adapters = $connectionManager->getAdapters($this->group, $this->node, "master");
			$this->connectionAdapter = $adapters["connectionAdapter"];
			$this->sqlAdapter = $adapters["sqlAdapter"];
			$result = $this->connectionAdapter->exec($sql);
			if (preg_match("/^\s*INSERT/i", $sql))//INSERT
			{
				return $this->connectionAdapter->lastInsertId();
			}
			else if (preg_match("/^\s*UPDATE|^\s*DELETE|^\s*REPLACE/i", $sql))//UPDATE, DELETE, REPLACE
			{
				return $result;
			}
			else//USE, SET, CREATE, DROP, ALTER
			{
				return true;
			}
		}
	}

	/**
	 * Generate complete sql from sql template (with placeholder) and parameter
	 * @param $sql
	 * @param $parameter
	 * @return string
	 * @todo 兼容pgsql等其它数据库，pgsql的某些数据类型不接受单引号引起来的值
	 */
	public function bindParameter($sql, $parameter)
	{
		$delimiter = "\x01\x02\x03";
		foreach($parameter as $key => $value)
		{
			$newPlaceHolder = "$delimiter$key$delimiter";
			$find[] = $newPlaceHolder;
			$replacement[] = "'" . $this->connectionAdapter->escape($value) . "'";
			$sql = str_replace(":$key", $newPlaceHolder, $sql);
		}
		return str_replace($find, $replacement, $sql);
	}
}