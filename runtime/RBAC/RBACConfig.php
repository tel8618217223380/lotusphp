<?php
class LtRBACConfig
{
	public $role = array('Administrator' => "����Ա", 'User' => "�û�", 'Guest' => "����");
	
	// accessControl
	public $acl = array('Administrator' => array('*'), 'User'=> array("Index/Index", "User/*"), 'Guest' => array("Index/Index", "User/View"), 'Demo' => array("*/*"));
}
