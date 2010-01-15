<?php

class LtSessionSqlite
{
	public $lifeTime;
	private $dbHandle;
	private $table;

	public function __construct()
	{
		if (isset($this->options['life_time']))
		{
			$this->lifeTime = $this->options['life_time'];
		}
		else
		{
			$this->lifeTime = get_cfg_var("session.gc_maxlifetime");
		}

		$this->table = 'lotus_session';
		$dbHandle = sqlite_open('/tmp/LtSession/session_sqlite2.db', 0666);

		if (!$dbHandle)
		{
			return false;
		}
		$this->dbHandle = $dbHandle;
		$sql = "SELECT name FROM sqlite_master WHERE type='table' UNION ALL SELECT name FROM sqlite_temp_master WHERE type='table' AND name='" . $this->table."'";
		$res = sqlite_query($sql, $this->dbHandle);
		$row = sqlite_fetch_array($res, SQLITE_ASSOC);
		if (empty($row))
		{
			$this->runOnce();
		}
	}

	public function runOnce()
	{
		$sql = "CREATE TABLE $this->table (
			[session_id] VARCHAR(255)  NOT NULL PRIMARY KEY,
			[session_expires] INTEGER DEFAULT '0' NOT NULL,
			[session_data] TEXT  NULL
		)";
		return sqlite_exec($sql, $this->dbHandle);
	}

	function open($savePath, $sessName)
	{
		return true;
	}

	function close()
	{
		$this->gc(ini_get('session.gc_maxlifetime'));
		return @sqlite_close($this->dbHandle);
	}

	function read($sessID)
	{
		$res = sqlite_query("SELECT session_data AS d FROM $this->table
                           WHERE session_id = '$sessID'
                           AND session_expires > " . time(), $this->dbHandle);
		if ($row = sqlite_fetch_array($res, SQLITE_ASSOC))
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
		$res = sqlite_query("SELECT * FROM $this->table
                           WHERE session_id = '$sessID'", $this->dbHandle);
		if (sqlite_num_rows($res))
		{
			sqlite_exec("UPDATE $this->table
                        SET session_expires = '$newExp',
                        session_data = '$sessData'
                        WHERE session_id = '$sessID'", $this->dbHandle);
			if (sqlite_changes($this->dbHandle))
			{
				return true;
			}
		}
		else
		{
			sqlite_exec("INSERT INTO $this->table (
                        session_id,
                        session_expires,
                        session_data)
                        VALUES(
                        '$sessID',
                        '$newExp',
                        '$sessData')", $this->dbHandle);
			if (sqlite_changes($this->dbHandle))
			{
				return true;
			}
		}
		return false;
	}

	function destroy($sessID)
	{
		sqlite_exec("DELETE FROM $this->table WHERE session_id = '$sessID'", $this->dbHandle);
		if (sqlite_changes($this->dbHandle))
		{
			return true;
		}
		return false;
	}

	function gc($sessMaxLifeTime)
	{
		sqlite_exec("DELETE FROM $this->table WHERE session_expires < " . time(), $this->dbHandle);
		return sqlite_changes($this->dbHandle);
	}
}
