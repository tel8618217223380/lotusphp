<?php
class DefaultIndexAction extends LtAction
{
	public function __construct()
	{
		parent::__construct();
		$this->responseType = 'tpl'; // 使用模板引擎
	}
	/**
	 * 一定要有这个execute()方法
	 */
	public function execute()
	{
		$db = new LtDb;
		LtDb::$configHandle = C("LtConfig");
		$db->group = "group_8";
		$db->node = "node_8";
		$db->init(); 
		// 用DbHandle直接操作数据库
		$dbh = $db->getDbHandle();
		$dbh->query("DROP TABLE IF EXISTS user_account");
		$dbh->query("CREATE TABLE user_account (
			id INT NOT NULL auto_increment,
			username VARCHAR( 20 ) NOT NULL ,
			PRIMARY KEY ( id ) 
		)"); 
		// 使用Table Gateway查询引擎
		$tg = $db->getTDG("user_account");
		$id = $tg->insert(array("id" => 1, "username" => "lotusphp"));
		$data = $tg->fetch($id); 
		// -------------------------------------------------------------
		$db1 = new LtDb;
		$db1->group = "group_1";
		$db1->node = "node_1";
		$db1->init(); 
		// 用DbHandle直接操作数据库
		$dbh1 = $db1->getDbHandle();
		$dbh1->query("CREATE TABLE [user] (
			[user_id] INTEGER  NOT NULL PRIMARY KEY,
			[user_name] VARCHAR(20)  NOT NULL,
			[created] INTEGER  NOT NULL,
			[modified] INTEGER  NOT NULL
		)"); 
		// 使用Table Gateway查询引擎
		$tg1 = $db1->getTDG("user");
		$id1 = $tg1->insert(array("user_id" => 1, "user_name" => "kiwiphp", 'created' => time(), 'modified' => time()));
		$data1 = $tg1->fetch($id1);
		$dbh1->query("DROP TABLE user"); 
		// ---------------------------------------------------------------
		$this->code = 200;
		$this->message = "Welcome LotusPHP";
		$this->data["username"] = $data['username'];
		$this->data['user_name'] = $data1['user_name'];
		$this->data['created'] = $data1['created'];
	}
}
