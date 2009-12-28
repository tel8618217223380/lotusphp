<?php
class LtDbHandle
{
	public $conf;
	public $connectionAdapter;
	protected $sqlAdapter;

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
	 * @return false on failed
	 * SELECT, SHOW, DESECRIBE, EXPLAIN return rowset or NULL when no record found
	 * INSERT return the ID generated for an AUTO_INCREMENT column
	 * UPDATE, DELETE return affected count
	 * USE, DROP, ALTER, CREATE, SET etc, return affected count
	 * @todo 如果是读操作，自动去读slave服务器，除非设置了强制读master服务器（此功能行动到上级类去实现）
	 * @notice 每次只能执行一条SQL
	 */
	public function query($sql, $bind = null)
	{
		if(empty($sql))
		{
			// trigger_error('Empty the SQL statement', E_USER_WARNING);
			return null;
		}
		if (is_array($bind))
		{
			$sql = $this->bindParameter($sql, $bind);
		}
		if (preg_match("/^\s*SELECT|^\s*EXPLAIN|^\s*SHOW|^\s*DESCRIBE/i", $sql))//read query: SELECT, SHOW, DESCRIBE
		{
			$result = $this->connectionAdapter->query($sql);
			//if (0 === count($result))
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
				return $result;
			}
		}
	}

	public function init()
	{
		if (preg_match("/^pdo_/i", $this->conf["adapter"]))
		{
			$LtDbSqlAdapter = "LtDbSqlAdapter" . ucfirst(substr($this->conf["adapter"], 4));
			$LtDbConnectionAdapter = "LtDbConnectionAdapterPdo";
		}
		else
		{
			$LtDbSqlAdapter = "LtDbSqlAdapter" . ucfirst($this->conf["adapter"]);
			$LtDbConnectionAdapter = "LtDbConnectionAdapter" . ucfirst($this->conf["adapter"]);
		}
		/**
		 * Mysqli use mysql syntax
		 */
		if ("mysqli" == $this->conf["adapter"])
		{
			$LtDbSqlAdapter = "LtDbSqlAdapterMysql";
		}
		$this->sqlAdapter = new $LtDbSqlAdapter();
		$this->connectionAdapter = new $LtDbConnectionAdapter();
		if($this->connectionAdapter->connResource = $this->connectionAdapter->connect($this->conf))
		{
			$this->query($this->sqlAdapter->setCharset($this->conf["charset"]));
			if (!empty($this->conf["schema"]))//set default schema, for pgsql, oracle
			{
				$this->query($this->sqlAdapter->setSchema($this->conf["schema"]));
			}
		}
		else
		{
			//don't trigger_error() here, because the caller may catch this exception
			throw new Exception("Can not connect to db server");
		}
	}

	/**
	 * Generate complete sql from sql template (with placeholder) and parameter
	 * @param $sql
	 * @param $parameter
	 * @return string
	 * @todo 移动到DbHandler下面去，兼容各驱动的escape()方法
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