<?php
class LtRBACConfig
{
	public $role = array('Administrator' => "管理员", 'User' => "用户", 'Guest' => "来宾");
	
	// accessControl
	public $acl = array('Administrator' => array('*'), 'User'=> array("Index/Index", "User/*"), 'Guest' => array("Index/Index", "User/View"), 'Demo' => array("*/*"));
}
