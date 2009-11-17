<?php
/**
 * Database adapter PDO class
 */
abstract class DbAdapterPdo extends DbAdapter
{
	/**
	 * The PDO construct options
	 *
	 * @var array
	 */
	protected $_options = array(PDO::ATTR_PERSISTENT => true);

	/**
	 * Initiate a transaction
	 *
	 * @return boolean
	 */
	protected function _beginTransaction()
	{
		return $this->_connection->beginTransaction();
	}

	/**
	 * Create a PDO object and connects to the database.
	 *
	 * @param array $config
	 * @return resource
	 */
	protected function _connect($config)
	{
		$dsn = $this->_dsn($config);
		try
		{
			$connection = new PDO($dsn, $config['username'], $config['password'], $this->_options);
			$this->setCharset($config['charset'], $connection);
			return $connection;
		}
		catch (PDOException $exception)
		{
			return false;
		}
	}

	/**
	 * Commit a transaction
	 *
	 * @return boolean
	 */
	public function commit()
	{
		return $this->_connection->commit();
	}

	/**
	 * Get the last inserted ID.
	 *
	 * @param string $tableName
	 * @param string $primaryKey
	 * @return integer
	 */
	public function lastInsertId($tableName = null, $primaryKey = null)
	{
		return $this->_connection->lastInsertId();
	}

	/**
	 * Prepare an SQL statement.
	 *
	 * @param resource $connection
	 * @param string $sql
	 * @return PDOStatement
	 */
	public function prepare($sql)
	{
		return $this->_connection->prepare($sql);
	}

	/**
	 * Roll back a transaction
	 *
	 * @return boolean
	 */
	public function rollBack()
	{
		return $this->_connection->rollBack();
	}
}
