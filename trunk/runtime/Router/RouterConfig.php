<?php
// Options +FollowSymLinks
// IndexIgnore */*
// ##  Turn on the RewriteEngine
// RewriteEngine On
// ##  Rules
// RewriteCond %{REQUEST_FILENAME} !-f
// RewriteCond %{REQUEST_FILENAME} !-d
// RewriteRule . index.php
class LtRouterConfig
{
	public $module = 'default';
	public $action = 'index';
}
