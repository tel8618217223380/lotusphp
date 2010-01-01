<?php
class LtDbHandle
{
	public $group;
	public $node;
	public $role = "master";
	public $connectionAdapter;
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
			trigger_error('Empty the SQL statement');
		}
		$this->sqlAdapter = $this->getCurrentSqlAdapter();
		$queryType = $this->sqlAdapter->detectQueryType($sql);
		switch ($queryType)
		{
			case "SELECT":
				if (!$forceUseMaster && isset(LtDbStaticData::$servers[$this->group][$this->node]["slave"]))
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
		$adapters = $this->connectionManager->getAdapters($this->group, $this->node, $this->role);
		$this->connectionAdapter = $adapters["connectionAdapter"];
		if (is_array($bind) && 0 < count($bind))
		{
			$sql = $this->bindParameter($sql, $bind);
		}
		return $this->$queryMethod($sql);
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

	protected function getCurrentSqlAdapter()
	{
		$factory = new LtDbFactory;
		$host = key(LtDbStaticData::$servers[$this->group][$this->node][$this->role]);
		return $factory->getSqlAdapter(LtDbStaticData::$servers[$this->group][$this->node][$this->role][$host]["adapter"]);
	}

	protected function select($sql)
	{
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

	protected function insert($sql)
	{
		if($result = $this->connectionAdapter->exec($sql))
		{
			return $this->connectionAdapter->lastInsertId();
		}
		else
		{
			return $result;
		}
	}

	protected function changeRows($sql)
	{
		return $this->connectionAdapter->exec($sql);
	}

	/**
	 * @todo 更新连接缓存
	 */
	protected function setSessionVar($sql)
	{
		return $this->connectionAdapter->exec($sql);
	}

	protected function other($sql)
	{
		return false == $this->connectionAdapter->exec($sql) ? false : true;
	}
}