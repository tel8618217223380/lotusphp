<?php
/**
 * 这是一个最简单的示例，没有配置文件，没有MVC，不需要Web服务器
 * 适合用来开发服务器上定时运行的脚本，如数据迁移的脚本
 */
$lotusHome = dirname(dirname(dirname(__FILE__)));
include $lotusHome . "/runtime/Lotus.php";

/**
 * 初始化Lotus类
 */
$lotus = new Lotus();

/**
 * envMode的默认值是dev，即开发模式
 * envMode不等于dev的时候（如prod-生产环境，testing-测试环境），性能会有提高
 * $lotus->envMode = "prod";
 */
$lotus->boot();

/**
 * ========== 以下内容取自example/DB/simplest.php 演示了如何操作数据库 ==========
 */
/**
 * 配置数据库连接
 * 关键是给Db::$servers变量赋一个数组，这个数组维度比较复杂 ，所以用DbConfigBuilder构建不容易出错
 * 如果你用别的方式（例如从ini或者yaml读取配置）构造一个同样的数组然后赋值给Db::$servers，效果是一样的
 */
$dbConfigBuilder = new LtDbConfigBuilder();
$adapter = "pgsql";
$dbConfigBuilder->addSingleHost(array(
	"host" => "localhost",
	"port" => "5432",
	"username" => "postgres",
	"password" => "111111",
	"dbname" => "test",
	"schema" => "public",
	"adapter" => $adapter,
	//"adapter" => "pdo_mysql",//使用pdo_mysql扩展,目前只支持mysql和pdo_mysql,都能运行成功
	"charset" => "UTF-8",
));
LtDbStaticData::$servers = $dbConfigBuilder->getServers();

/**
 * 直接执行执行SQL
 * 由于mysql_query()的潜规则,每次只能执行一条SQL
 */
$dba = new LtDbHandler();

echo "\nUSE, DROP, CREATE应该返回受影响的行数（执行成功）或者false（执行失败）：\n";
var_dump($dba->query('DROP TABLE IF EXISTS "user"'));

var_dump($dba->query('CREATE TABLE "user"
(
  user_id serial NOT NULL,
  username character varying(20) NOT NULL,
  age integer NOT NULL DEFAULT 0,
  created integer NOT NULL DEFAULT 0,
  modified integer NOT NULL DEFAULT 0,
  CONSTRAINT user_pkey PRIMARY KEY (user_id)
)
WITH (
  OIDS=FALSE
);'));
echo "\n--------表信息--------\n";
var_dump($dba->query("
SELECT a.attnum, a.attname AS field, t.typname AS type, 
format_type(a.atttypid, a.atttypmod) AS complete_type, 
a.attnotnull AS isnotnull, 
( SELECT 't' FROM pg_index 
WHERE c.oid = pg_index.indrelid 
AND pg_index.indkey[0] = a.attnum 
AND pg_index.indisprimary = 't') AS pri, 
(SELECT pg_attrdef.adsrc FROM pg_attrdef 
WHERE c.oid = pg_attrdef.adrelid 
AND pg_attrdef.adnum=a.attnum) AS default 
FROM pg_attribute a, pg_class c, pg_type t 
WHERE c.relname = 'user' 
AND a.attnum > 0 
AND a.attrelid = c.oid 
AND a.atttypid = t.oid 
ORDER BY a.attnum" ));
echo "\n--------表信息--------\n";
$sql=<<<EOD
SELECT a.attname, LOWER(format_type(a.atttypid, a.atttypmod)) AS type, d.adsrc, a.attnotnull, a.atthasdef
FROM pg_attribute a LEFT JOIN pg_attrdef d ON a.attrelid = d.adrelid AND a.attnum = d.adnum
WHERE a.attnum > 0 AND NOT a.attisdropped
	AND a.attrelid = (SELECT oid FROM pg_catalog.pg_class WHERE relname='user'
		AND relnamespace = (SELECT oid FROM pg_catalog.pg_namespace WHERE nspname = 'public'))
ORDER BY a.attnum
EOD;
var_dump($dba->query($sql));

echo "\nINSERT应该返回自增ID：\n";
var_dump($dba->query("INSERT INTO \"user\" (username, age) VALUES ('" . $adapter . uniqid() . "', 4);"));

var_dump($dba->query("INSERT INTO \"user\" (username, age) VALUES ('" . $adapter . uniqid() . "', '5');"));

echo "\nSELECT应该返回查到的结果集：\n";
var_dump($dba->query("SELECT c.oid,case when n.nspname='public' then c.relname else n.nspname||'.'||c.relname end as relname 
				FROM pg_class c join pg_namespace n on (c.relnamespace=n.oid)
				WHERE c.relkind = 'r'
					AND n.nspname NOT IN ('information_schema','pg_catalog')
					AND n.nspname NOT LIKE 'pg_temp%'
					AND n.nspname NOT LIKE 'pg_toast%'
				ORDER BY relname"));
var_dump($dba->query('SELECT * FROM "user"'));
var_dump($dba->query('EXPLAIN SELECT * FROM "user"'));

echo "\nUPDATE,DELETE应该返回受影响的行数：\n";
var_dump($dba->query('UPDATE "user" SET age = 10'));
var_dump($dba->query('DELETE FROM "user"'));

echo "\nSELECT查不到结果应该返回null：\n";
var_dump($dba->query('SELECT * FROM "user"'));

