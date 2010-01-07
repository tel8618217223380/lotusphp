<?php
class LtErrorHandler
{
	public function debug($errno, $errstr, $errfile, $errline)
	{
		echo "\n" . $errstr . " in $errfile : $errline";
		print_r(debug_backtrace());
	}
}
$errorHandler = new LtErrorHandler;
set_error_handler(array($errorHandler, "debug"));