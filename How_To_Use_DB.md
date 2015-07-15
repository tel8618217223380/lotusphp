# 系统需求 #
  1. 只支持php 5（lotusphp所有组件都要求php5环境）
  1. 不要求Web服务器，可运行于命令行下
  1. 使用数据库组件必须安装这pdo扩展，并根据使用的数据库类型安装pdo\_mysql或pdo\_pgsql扩展。

# 用法 #
## 运行lotus自带的例子 ##
  1. 在http://code.google.com/p/lotusphp/downloads/list?q=DB下载最新版本，解压后放到任意目录（如果想通过Web访问，请放到相应的网站目录）
  1. 首先修改example\DB\simplest.php里22,23行的username,password,修改成你本地可以创建数据库的用户名和密码即可.
  1. 运行example\DB\simplest.php，通过Web访问http://localhost/lotusphp/example/DB/simplest.php，或者通过命令行访问都可以
  1. 屏幕上打印类似"Array ( [row\_total](row_total.md) => 0 [rows](rows.md) => Array ( ) ) Array ( [user\_id](user_id.md) => 1 [username](username.md) => chin [age](age.md) => 30 [created](created.md) => 1258644384 [modified](modified.md) => 1258644384 ) Array ( [0](0.md) => Array ( [user\_id](user_id.md) => 2 [username](username.md) => kiwiphp ) [1](1.md) => Array ( [user\_id](user_id.md) => 3 [username](username.md) => lotus ) ) "说明运行成功
  * 看一下simplest.php的源码能帮助你理解这个示例，他们非常简单，还有中文注释

## 示例1：最简单的用法 ##
```
<?php
/*
 * 加载Db类文件
 * 加载的类很多，且需要注意先后顺序，推荐使用LtAutoloader自动加载
 */
include "/DB所在目录/runtime/DB/DbConfigBuilder.php";
include "/DB所在目录/runtime/DB/Db.php";
include "/DB所在目录/runtime/DB/Adapter/DbAdapter.php";
include "/DB所在目录/runtime/DB/Adapter/DbAdapterPdo.php";
include "/DB所在目录/runtime/DB/Adapter/DbAdapterPdoMysql.php";
include "/DB所在目录/runtime/DB/QueryEngine/DbTable.php";
/*
 * 配置数据库连接
 * 关键是给LtDbStaticData::$servers变量赋一个数组，这个数组维度比较复杂 ，所以用DbConfigBuilder构建不容易出错
 * 如果你用别的方式（例如从ini或者yaml读取配置）构造一个同样的数组然后赋值给LtDbStaticData::$servers，效果是一样的
 */
$dbConfigBuilder = new LtDbConfigBuilder;
$dbConfigBuilder->addSingleHost(array(
    "host" => "localhost",
    "username" => "root",
    "password" => "root",
    "dbname" => "lotus_db_test",
    "adapter" => "pdoMysql",
    "charset" => "UTF-8",
));
LtDbStaticData::$servers = $dbConfigBuilder->getServers();

/*
 * 直接执行执行SQL
 */
$dba = LtDb::factory("pdoMysql");
$result = $dba->query("SELECT NOW();");
print_r($result);
```

## 示例2：在生产环境获取更好的性能 ##
## DB基础操作 ##
增删查改操作,以simplest.php例子为主

### Insert ###
insert()方法接收一个数组参数，如插入成功，返回被插入的这条记录的主键值。
```
/*
 * 使用Table Gateway模式操作数据表
 * 使用simplest里建立的lotus_db_test库，user表
 */
$userTDG = LtDb::newDbTable("user");

//插入新记录，并取得自增的ID
$userId = $userTDG->insert(array(
    "username" => "chin",
    "age" => "40",
));
//可以看看$userId,就是刚刚插入的值

//再来插入几条
$userTDG->insert(array(
    "username" => "kiwiphp",
    "age" => "3",
));
$userTDG->insert(array(
    "username" => "lotus",
    "age" => "1",
));

```
这里有三个特殊字段需要注意:

created, modified
如果表有字段名为created (或 modified)的字段, 且传入的参数并未指定它的值，lotusphp 将会将当前系统时间戳作为该字段的默认值，如果传入参数指定了它的值，lotusphp将使用该值。
primary key
不管他是由数据库系统自动生成的（例如MySQL的AUTO INCREMENT字段或PostgreSQL的Serial字段）还是由参数数组传入的主键，它的值将被返回。
some\_does\_not\_exists\_column在user表中不是一个有效的字段，那么，它将被忽略。

### Select ###

通过主键取出一条记录
```
print_r($userTDG->fetch($userId));
//$userId为刚才insert中的,如1,2,3

返回结果
Array
(
    [user_id] => 1
    [username] => chin
    [age] => 40
    [created] => 1259720130
    [modified] => 1259720130
)

```

通过条件取出多条记录
```
$condition["where"]["expression"] = "age < 10";
//Where子句 包括两个部分: 表达式 和 值, 其规则，机制和 PDO的预处理语句是一样的。
$condition["fields"] = "user_id, username";
//fields参数用于指定获取哪些字段。默认是获取所有字段，等于执行SELECT * ...。 
print_r($userTDG->fetchRows($condition));

返回结果
Array
(
    [0] => Array
        (
            [user_id] => 2
            [username] => kiwiphp
        )
 
    [1] => Array
        (
            [user_id] => 3
            [username] => lotus
        )
 
)
```


### 注意 ###

PDO 不支持 "WHERE column IN (:placeholder)"
"WHERE column LIKE :placeholder" 支持，但 "WHERE column LIKE %:placeholder%" 不支持。
占位符的数目必须和后面绑定的参数数目一样
且占位符的名字不能重复（例如："where :id > 10 and :id < 100" 是不可以的）

当只读查询 (fetch, fetchRows, count) 被执行时, lotusphp 将会自动选择任意可用的一台Slave服务器执行查询。在实际生产中，为了避免主从复制延迟带来的影响，也可能需要强制从Master服务器查询，这三个方法都支持强制读Master。

### Limit & Offset ###
常见的数据库系统都有专属的SQL语法，例如，如果你想从一个表中取出前10条记录，你可以这样写:

|DBMS|  |SQL|
|:---|:-|:--|
|DB2 |  |select **from table fetch first 10 rows only**|
|Informix|  |select first 10 **from table**|
|Microsoft SQL Server and Access|  |select top 10 **from table**|
|MySQL and PostgreSQL|  |select **from table limit 10**|
|Oracle|  |select **from (select** from table) where rownum <= 10|

```
$condition["limit"] = 1;
$condition["offset"] = 0;
print_r($userTDG->fetchRows($condition));

返回结果
Array
(
    [0] => Array
        (
            [user_id] => 1
            [username] => chin
            [age] => 40
            [created] => 1259720130
            [modified] => 1259720130
        )
 
)
```
当"offset"等于0时，offset参数可以忽略。

### Order By ###

```
$condition['orderby'] = "user_id DESC";
print_r($userTDG->fetchRows($condition));
Array
(
    [0] => Array
        (
            [user_id] => 3
            [username] => lotus
            [age] => 1
            [created] => 1259720130
            [modified] => 1259720130
        )
 
    [1] => Array
        (
            [user_id] => 2
            [username] => kiwiphp
            [age] => 3
            [created] => 1259720130
            [modified] => 1259720130
        )
 
    [2] => Array
        (
            [user_id] => 1
            [username] => chin
            [age] => 40
            [created] => 1259720130
            [modified] => 1259720130
        )
 
)

```

### 统计共计获取多少行记录 ###

如果你刚刚为fetchRows()组成了一个条件数组, 你可以直接将这个数组传递给count()方法来统计有多少满足条件的记录, 使用count()方法时，将忽略limit, offset, order by, group by, fields 参数, 只使用 where, join 参数.
```
echo $userTDG->count();
//返回结果为3
```

### Update ###

根据主键更新一条记录
```
$userId = 1;
$userTDG->update($userId, array(
    "age" => 41
));


//更新多条
$condition["where"]["expression"] = "age < 10";
$condition["fields"] = "user_id, username";
$userTDG->updateRows($condition["where"], array(
	"age" => 9
));

print_r($userTDG->fetchRows());

返回结果

Array
(
    [0] => Array
        (
            [user_id] => 1
            [username] => chin
            [age] => 41
            [created] => 1259720130
            [modified] => 1259720130
        )
 
    [1] => Array
        (
            [user_id] => 2
            [username] => kiwiphp
            [age] => 9
            [created] => 1259720130
            [modified] => 1259722639
        )
 
    [2] => Array
        (
            [user_id] => 3
            [username] => lotus
            [age] => 9
            [created] => 1259720130
            [modified] => 1259722639
        )
 
)
```

### Delete ###

根据主键删除一条记录
```
//根据主键删除
$userId = 1;
$userTDG->delete($userId);

//删除多条
$condition["where"]["expression"] = "age < 10";
$userTDG->deleteRows($condition["where"]);
print_r($userTDG->fetchRows());

返回结果
Array
(
)
刚好删完数据也学完了增删改查操作

```

# 扩展DB类 #

# 延伸阅读：我们为什么要做DB #


## Lotus DB如何解决这些问题 ##

## 常见问题 ##


# 鸣谢 #