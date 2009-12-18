<?php
class LtDbConnectionAdapterPdo extends LtDbConnectionAdapter
{
	public function beginTransaction()
	{
		return $this->connResource->beginTransaction();
	}

	public function commit()
	{
		return $this->connResource->commit();
	}

	public function rollBack()
	{
		return $this->connResource->rollBack();
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

	public function exec($sql)
	{
		return $this->connResource->exec($sql);
	}

	public function query($sql)
	{
		return $this->connResource->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	}

	public function lastInsertId()
	{
		return $this->connResource->lastInsertId();
	}
}