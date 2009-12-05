<?php
/**
 * Database adapter PDO sqlite class
 */
class LtDbAdapterPdoSqlite extends LtDbAdapterPdo {
	/**
	 * The default database configuration for sqlite
	 * 
	 * @var array 
	 */
	protected $_config = array('port' => '', 'username' => '', 'password' => '');

	/**
	 * The PDO construct options
	 * 
	 * @var array 
	 */
	protected $_options = array(PDO :: ATTR_PERSISTENT => false);

	/**
	 * Create a PDO DSN for the adapter
	 * 
	 * @param array $config 
	 * @return string 
	 */
	protected function _dsn($config) {
		// sqlite:/opt/databases/mydb.sq3
		// sqlite::memory:
		// sqlite2:/opt/databases/mydb.sq2
		// sqlite2::memory:
		$config['dbver'] = isset($config['dbver']) ? $config['dbver'] : 'sqlite';
		return $config['dbver'] . ':' . $config['host'] . $config['dbname'];
	} 

	/**
	 * Get current db configuration
	 * 
	 * @param string $group 
	 * @param string $node 
	 * @param string $role 
	 * @param string $host 
	 * @return array 
	 */
	protected function _getConfig($group, $node, $role = 'master', $host = null) {
		return $this -> _getBasicConfig($group, $node, $role, $host);
	} 

	/**
	 * Change current schema
	 * 
	 * @return void 
	 */
	protected function _useSchema() {
	} 

	/**
	 * Add an adapter-specific LIMIT clause to the SELECT statement.
	 * 
	 * @param string $sql 
	 * @param integer $limit 
	 * @param integer $offset 
	 * @return string 
	 */
	public function limit($sql, $limit, $offset) {
		if ($limit > 0) {
			$offset = 0 < $offset ? $offset : 0;
			$sql .= " LIMIT $limit OFFSET $offset";
		} 
		return $sql;
	} 

	/**
	 * Set encoding for a database connection.
	 * 
	 * @param string $encoding 
	 * @param resource $connection 
	 * @return void 
	 */
	public function setCharset($charset, $connection) {
		// $sql = 'PRAGMA encoding = "' . $charset . '"';
		// $connection -> exec($sql);
	} 

	/**
	 * Return the column descriptions for a table.
	 * 
	 * @param string $table 
	 * @return array 
	 */
	public function showFields($table) {
		$sql = "PRAGMA table_info('" . $table . "')";
		$queryResult = $this -> query($sql);
		$result = $queryResult['rows'];
		$fields = array();
		foreach ($result as $key => $value) {
			// 字段名
			$fields[$value['name']]['name'] = $value['name']; 
			// 字段类型
			$fulltype = $value['type'];
			$size = null;
			$precision = null;
			$scale = null;

			if (preg_match('/^([^\(]+)\(\s*(\d+)\s*,\s*(\d+)\s*\)$/',
					$fulltype, $matches)) {
				$type = $matches[1];
				$precision = $matches[2];
				$scale = $matches[3]; // aka precision
			} elseif (preg_match('/^([^\(]+)\(\s*(\d+)\s*\)$/',
					$fulltype, $matches)) {
				$type = $matches[1];
				$size = $matches[2];
			} else {
				$type = $fulltype;
			} 

			$fields[$value['name']]['type'] = $type;
			/**
			 * not null is 99, null is 0
			 */
			$fields[$value['name']]['notnull'] = (bool) ($value['notnull'] != 0);
			$fields[$value['name']]['default'] = $value['dflt_value'];
			$fields[$value['name']]['primary'] = (bool) ($value['pk'] == 1 && strtoupper($fulltype) == 'INTEGER');
		} 
		return $fields;
	} 
} 
