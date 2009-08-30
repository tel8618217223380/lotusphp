<?php
/* 
 * Load rumtime class start
 */
$lotusHome = dirname(dirname(dirname(dirname(__FILE__))));
include $lotusHome . "/runtime/lotus.php";
/* 
 * Load rumtime class end
 */

$lotus = new Lotus;
$lotus->boot();