<?php
$lotusHome = substr(__FILE__, 0, strpos(__FILE__, "unittest"));
require $lotusHome . "runtime/DB/Db.php";
require $lotusHome . "runtime/DB/DbConfig.php";
require $lotusHome . "runtime/DB/adapter/ConnectionAdapter/DbConnectionAdapter.php";
require $lotusHome . "runtime/DB/adapter/ConnectionAdapter/DbConnectionAdapterMysql.php";
require $lotusHome . "runtime/DB/adapter/ConnectionAdapter/DbConnectionAdapterMysqli.php";
require $lotusHome . "runtime/DB/adapter/ConnectionAdapter/DbConnectionAdapterPdo.php";
require $lotusHome . "runtime/DB/adapter/ConnectionAdapter/DbConnectionAdapterPgsql.php";
require $lotusHome . "runtime/DB/adapter/ConnectionAdapter/DbConnectionAdapterSqlite.php";
require $lotusHome . "runtime/DB/adapter/SqlAdapter/DbSqlAdapter.php";
require $lotusHome . "runtime/DB/adapter/SqlAdapter/DbSqlAdapterMysql.php";
require $lotusHome . "runtime/DB/adapter/SqlAdapter/DbSqlAdapterPgsql.php";
require $lotusHome . "runtime/DB/adapter/SqlAdapter/DbSqlAdapterSqlite.php";