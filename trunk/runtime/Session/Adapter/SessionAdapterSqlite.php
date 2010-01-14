<?php
class LtSessionAdapterSqlite implements LtSessionAdapter
{
	public $options;
	public $lifetime = 1800; //Session 生命周期（秒）
	private $db;
	private $table;

	public function __construct()
	{ 
		if (isset($this->options['lifetime']))
		{
			$this->lifetime = $this->options['lifetime'];
		}
		$this->db = Database::getInstance();
		$this->table = DB_PRE . 'session';
		session_set_save_handler(array(&$this, 'open'), array(&$this, 'close'), array(&$this, 'read'), array(&$this, 'write'), array(&$this, 'destroy'), array(&$this, 'gc'));
	}

	public function init()
	{
		$this->__construct();
	}

	public function open($save_path, $session_name)
	{
		return true;
	}

	public function close()
	{
		return $this->gc($this->lifetime);
	}

	public function read($id)
	{ 
		$r = $this->db->get_one("SELECT data FROM $this->table WHERE sessionid='$id'");
		return $r['data'];
	}

	public function write($id, $data)
	{
	// SQLITE 中 REPLACE 是 INSERT 的别名.
		$this->db->query("DELETE FROM $this->table WHERE sessionid='$id'");
		return $this->db->query("INSERT INTO $this->table (sessionid, data) VALUES('$id', '" . $data . "')");
	}

	public function destroy($id)
	{
		return $this->db->query("DELETE FROM $this->table WHERE sessionid='$id'");
	}

	public function gc($maxlifetime)
	{
		$expiretime = REQUEST_TIME - $maxlifetime;
		return $this->db->query("DELETE FROM $this->table WHERE lastvisit < $expiretime");
	}
}
