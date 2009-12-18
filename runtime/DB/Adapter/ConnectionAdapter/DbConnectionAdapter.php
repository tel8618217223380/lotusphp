<?php
abstract class LtDbConnectionAdapter
{
	public $connResource;

	/**
	 * Trancaction methods
	 */
	abstract public function beginTransaction();
	abstract public function commit();
	abstract public function rollBack();

	/**
	 * Connect to db and execute sql query
	 */
	abstract public function connect($connConf);
	abstract public function query($sql);
	abstract public function lastInsertId();
}