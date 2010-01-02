<?php
interface LtDbConnectionAdapter
{
	public function connect($connConf);
	public function exec($sql, $connResource);
	public function query($sql, $connResource);
	public function lastInsertId($connResource);
	public function escape($sql, $connResource);
}