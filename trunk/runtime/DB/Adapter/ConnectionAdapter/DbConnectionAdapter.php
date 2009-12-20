<?php
abstract class LtDbConnectionAdapter
{
	public $connResource;

	/**
	 * Connect to db
	 * @param $connConf array
	 * --key--          --default value--     --optional value--
	 * host             localhost             some ip, hostname
	 * port             3306
	 * username         root
	 * password         N/A
	 * adapter          mysql                 mysql,mysqli,pdo_mysql,sqlite,pdo_sqlite
	 * charset          utf-8
	 * pconnect         true                  true | false
	 * connection_ttl   30                    any seconds
	 * @return resource, database connection resource id
	 */
	abstract public function connect($connConf);
	abstract public function exec($sql);
	abstract public function query($sql);
	abstract public function lastInsertId();
	abstract public function escape($sql);
}