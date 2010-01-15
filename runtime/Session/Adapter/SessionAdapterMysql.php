<?php

class LtSessionAdapterMysql implements LtSessionAdapter
{
	public $options;
	public $lifeTime;
	private $dbHandle;
	private $table;

	public function __construct()
	{
	}

	public function init()
	{
		if (isset($this->options['life_time']))
		{
			$this->lifeTime = $this->options['life_time'];
		}
		else
		{
			$this->lifeTime = get_cfg_var("session.gc_maxlifetime");
		}

		$this->table = $this->options['table'];

		$host = $this->options['host'];
		$user = $this->options['user'];
		$password = $this->options['password'];
		$dbname = $this->options['dbname'];

		$dbHandle = @mysql_connect($host, $user, $password);
		$dbSel = @mysql_select_db($dbname, $dbHandle);

		if (!$dbHandle || !$dbSel)
		{
			return false;
		}
		$this->dbHandle = $dbHandle;

		session_set_save_handler(array(&$this, 'open'), array(&$this, 'close'), array(&$this, 'read'), array(&$this, 'write'), array(&$this, 'destroy'), array(&$this, 'gc'));
	}

	public function runOnce()
	{
		$sql = "CREATE TABLE `lotus_sessions` (
 `session_id` VARCHAR(255) BINARY NOT NULL DEFAULT '',
 `session_expires` INT(10) UNSIGNED NOT NULL DEFAULT '0',
 `session_data` TEXT,
 PRIMARY KEY  (`session_id`)
 );";
	}

	function open($savePath, $sessName)
	{
		return true;
	}

	function close()
	{
		$this->gc(ini_get('session.gc_maxlifetime'));
		return @mysql_close($this->dbHandle);
	}

	function read($sessID)
	{
		$res = mysql_query("SELECT session_data AS d FROM $this->table
                           WHERE session_id = '$sessID'
                           AND session_expires > " . time(), $this->dbHandle);
		if ($row = mysql_fetch_assoc($res))
		{
			return $row['d'];
		}
		else
		{
			return "";
		}
	}

	function write($sessID, $sessData)
	{
		$newExp = time() + $this->lifeTime;
		$res = mysql_query("SELECT * FROM $this->table
                           WHERE session_id = '$sessID'", $this->dbHandle);
		if (mysql_num_rows($res))
		{
			mysql_query("UPDATE $this->table
                        SET session_expires = '$newExp',
                        session_data = '$sessData'
                        WHERE session_id = '$sessID'", $this->dbHandle);
			if (mysql_affected_rows($this->dbHandle))
			{
				return true;
			}
		}
		else
		{
			mysql_query("INSERT INTO $this->table (
                        session_id,
                        session_expires,
                        session_data)
                        VALUES(
                        '$sessID',
                        '$newExp',
                        '$sessData')", $this->dbHandle);
			if (mysql_affected_rows($this->dbHandle))
			{
				return true;
			}
		}
		return false;
	}

	function destroy($sessID)
	{
		mysql_query("DELETE FROM $this->table WHERE session_id = '$sessID'", $this->dbHandle);
		if (mysql_affected_rows($this->dbHandle))
		{
			return true;
		}
		return false;
	}

	function gc($sessMaxLifeTime)
	{
		mysql_query("DELETE FROM $this->table WHERE session_expires < " . time(), $this->dbHandle);
		return mysql_affected_rows($this->dbHandle);
	}
}
