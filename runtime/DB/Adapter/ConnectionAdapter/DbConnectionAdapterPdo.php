<?php
class LtDbConnectionAdapterPdo extends LtDbConnectionAdapter
{
	public function beginTransaction()
	{
		
	}
	public function commit()
	{
		
	}
	public function rollBack()
	{
		
	}

	public function connect($connConf)
	{
		$option = array(PDO::ATTR_PERSISTENT => true);
		switch ($connConf['adapter'])
		{
			case "pdo_mysql":
				$dsn =  "mysql:host={$connConf['host']};dbname={$connConf['dbname']}";
				$option[PDO::ATTR_PERSISTENT] = false;
				break;
			case "pdo_sqlite":
				$dsn =  "{$connConf['sqlite_version']}:{$connConf['host']}{$connConf['dbname']}";
				break;
			case "pdo_pgsql":
				$dsn =  "pgsql:host={$connConf['host']} port={$connConf['port']} dbname={$connConf['dbname']} user={$connConf['username']} password={$connConf['password']}";
				break;
			case "odbc":
				$dsn =  "odbc:" . $connConf["host"];
				break;
		}
		try
		{
			$connection = new PDO($dsn, $connConf['username'], $connConf['password'], $option);
			return $connection;
		}
		catch (PDOException $exception)
		{
			return false;
		}
	}

	public function query($sql, $bind = null)
	{
		$stmt = $this->prepare($sql);
		$stmt->execute((array) $bind);
		if ('00000' != $stmt->errorCode())
		{
			if(function_exists('onDbQueryFailed'))
			{
				call_user_func('onDbQueryFailed', $stmt, $sql, $bind);
			}
			else
			{
				$errorInfo = $stmt->errorInfo();
				throw new Exception('SQL query exception (code: [' . $stmt->errorCode() . ']; sql: [' . $sql . ']; message:' . $errorInfo[2]);
			}
		}
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$data['row_total'] = $stmt->rowCount();
		$data['rows'] = $stmt->fetchAll();
		$stmt->closeCursor();
		return $data;
	}
}