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
		return new PDO($dsn, $connConf['username'], $connConf['password'], $option);
	}

	public function query($sql, $bind = null)
	{
		$rows = array();
		$result = $this->connResource->query($sql);
		if (is_object($result))
		{
			return $result->fetchAll(PDO::FETCH_ASSOC);
		}
		else
		{
			return $result;
		}
	}
}