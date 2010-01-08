<?php
class LtDbHandle
{
	public $group;
	public $node;
	public $role = "master";
	public $connectionAdapter;
	public $connectionResource;
	public $sqlAdapter;
	protected $connectionManager;

	public function __construct()
	{
		$this->connectionManager = new LtDbConnectionManager;
	}

	/**
	 * Trancaction methods
	 */
	public function beginTransaction()
	{
		return $this->connectionAdapter->exec($this->sqlAdapter->beginTransaction(), $this->connectionResource);
	}

	public function commit()
	{
		return $this->connectionAdapter->exec($this->sqlAdapter->commit(), $this->connectionResource);
	}

	public function rollBack()
	{
		return $this->connectionAdapter->exec($this->sqlAdapter->rollBack(), $this->connectionResource);
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
			$this->sqlAdapter = $this->getCurrentSqlAdapter();
			return;
			//trigger_error('Empty the SQL statement');
		}
		$this->sqlAdapter = $this->getCurrentSqlAdapter();
		$queryType = $this->sqlAdapter->detectQueryType($sql);
		switch ($queryType)
		{
			case "SELECT":
				$servers = LtDb::$storeHandle->get("servers", LtDb::$namespace);
				if (!$forceUseMaster && isset($servers[$this->group][$this->node]["slave"]))
				{
					$this->role = "slave";
				}
				$queryMethod = "select";
				break;
			case "INSERT":
				$this->role = "master";
				$queryMethod = "insert";
				break;
			case "CHANGE_ROWS":
				$this->role = "master";
				$queryMethod = "changeRows";
				break;
			case "SET_SESSION_VAR":
				$queryMethod = "setSessionVar";
				break;
			case "OTHER":
			default:
				$this->role = "master";
				$queryMethod = "other";
				break;
		}
		$adapters = $this->connectionManager->getConnection($this->group, $this->node, $this->role);
		$this->connectionAdapter = $adapters["connectionAdapter"];
		$this->connectionResource = $adapters["connectionResource"];
		if (is_array($bind) && 0 < count($bind))
		{
			$sql = $this->bindParameter($sql, $bind);
		}
		return $this->$queryMethod($sql, $this->connectionResource);
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
			if ($value instanceof LtDbSqlExpression)
			{
				$replacement[] = $value->__toString();
			}
			else
			{
				$replacement[] = "'" . $this->connectionAdapter->escape($value, $this->connectionResource) . "'";
			}
			$sql = str_replace(":$key", $newPlaceHolder, $sql);
		}
		return str_replace($find, $replacement, $sql);
	}

	protected function getCurrentSqlAdapter()
	{
		$factory = new LtDbFactory;
		$servers = LtDb::$storeHandle->get("servers", LtDb::$namespace);
		$host = key($servers[$this->group][$this->node][$this->role]);
		return $factory->getSqlAdapter($servers[$this->group][$this->node][$this->role][$host]["adapter"]);
	}

	protected function select($sql, $connResource)
	{
		$result = $this->connectionAdapter->query($sql, $connResource);
		if (empty($result))
		{
			return null;
		}
		else
		{
			return $result;
		}
	}

	protected function insert($sql, $connResource)
	{
		if($result = $this->connectionAdapter->exec($sql, $connResource))
		{
			return $this->connectionAdapter->lastInsertId($connResource);
		}
		else
		{
			return $result;
		}
	}

	protected function changeRows($sql, $connResource)
	{
		return $this->connectionAdapter->exec($sql, $connResource);
	}

	/**
	 * @todo 更新连接缓存
	 */
	protected function setSessionVar($sql, $connResource)
	{
		return false === $this->connectionAdapter->exec($sql, $connResource) ? false : true;
	}

	protected function other($sql, $connResource)
	{
		return false === $this->connectionAdapter->exec($sql, $connResource) ? false : true;
	}
}