<?php
abstract class LtDbConnectionAdapter
{
	public $connResource;

	abstract public function connect($connConf);
	abstract public function exec($sql);
	abstract public function query($sql);
	abstract public function lastInsertId();
	abstract public function escape($sql);
}