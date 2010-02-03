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
		$db->group = "group_8";
		$db->node = "node_8";
		$db->init();

		//用DbHandle直接操作数据库
		$dbh = $db->getDbHandle();
		$dbh->query("DROP TABLE IF EXISTS user_account");
		$dbh->query("CREATE TABLE user_account (
			id INT NOT NULL auto_increment,
			username VARCHAR( 20 ) NOT NULL ,
			PRIMARY KEY ( id ) 
		)");

		//使用Table Gateway查询引擎
		$tg = $db->getTableGateway("user_account");
		$id = $tg->insert(array("id" => 1, "username" => "lotusphp"));
		$data = $tg->fetch($id);


		$this->code = 200;
		$this->message = "Welcome LotusPHP";
		$this->data["username"] = $data['username'];
	}
}
