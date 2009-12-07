<?php
Abstract class AbstractView
{
	public $context;
	public $result;
	abstract public function render();
}