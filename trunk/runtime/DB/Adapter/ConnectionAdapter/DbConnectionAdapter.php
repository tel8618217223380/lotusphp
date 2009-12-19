<?php
abstract class LtDbConnectionAdapter
{
	public $connResource;

	/**
	 * Connect to db and execute sql query
	 */
	abstract public function connect($connConf);
	abstract public function exec($sql);
	abstract public function query($sql);
	abstract public function lastInsertId();
}