# 系统需求 #
  1. 只支持php 5（lotusphp所有组件都要求php5环境）

# 用法 #

## 运行lotus自带的例子 ##
  1. svn checkout最新版本
  1. 运行example\Router\index.php
  1. 设置不同的`$config['router.routing_table']`配置，输入url测试，看是否能正确识别出module、action和其它参数


## 示例1：STANDARD模式的用法 ##
```
﻿<?php
/**
 * 加载基本类文件
 */
$lotusHome = substr(__FILE__, 0, strpos(__FILE__, "example")).'/';
include $lotusHome . "runtime/Config.php";
include $lotusHome . "runtime/Store.php";
include $lotusHome . "runtime/StoreMemory.php";
include $lotusHome . "runtime/Router/Router.php";

$router = new LtRouter;
$config['router.routing_table'] = array(
	// URL中的变量名字
	'pattern' => ":module-:action",
	// 默认的module和action的名字
	'default' => array('module' => 'default', 'action' => 'index'),
	// 对URL中的变量名字进行正则匹配，满足条件才注册此变量
	'reqs' => array('module' => '[a-zA-Z0-9\.\-_]+', 
				'action' => '[a-zA-Z0-9\.\-_]+'),
	// 标识变量的前缀，对应URL中变量名字
	'varprefix' => ':',
	// URL中变量的分隔符号
	'delimiter' => '-',
	// 后缀，常用来将URL模拟成单个文件
	'postfix' => '.html',
	// REWRITE STANDARD PATH_INFO三种模式，不分大小写
	'protocol' => 'STANDARD',  
	);
// 加载配置
$router->configHandle->addConfig($config);
// 调用init
$router->init();

// 注册好的变量放入$_GET，没有通过正则匹配的变量会删除
print_r($_GET);
```

## 示例2：PATH\_INFO模式用法 ##
```
// 修改配置内容为
$config['router.routing_table'] = array(
	// URL中的变量名字
	'pattern' => ":module-:action-*",
	// 默认的module和action的名字
	'default' => array('module' => 'default', 'action' => 'index'),
	// 对URL中的变量名字进行正则匹配，满足条件才注册此变量
	'reqs' => array('module' => '[a-zA-Z0-9\.\-_]+', 
			'action' => '[a-zA-Z0-9\.\-_]+'),
			'id' => '[0-9]+'), // id只能是数字
			'page' => '[0-9]+'), // page只能是数字
	// 标识变量的前缀，对应URL中变量名字
	'varprefix' => ':',
	// URL中变量的分隔符号
	'delimiter' => '-',
	// 后缀，常用来将URL模拟成单个文件
	'postfix' => '.html',
	// REWRITE STANDARD PATH_INFO三种模式，不分大小写
	'protocol' => 'PATH_INFO',  
	);
```

## 示例3：REWRITE模式用法 ##

```
// 美化url为一个文件
$config['router.routing_table'] = array(
	// URL中的变量名字
	'pattern' => ":module-:action-*",
	// 默认的module和action的名字
	'default' => array('module' => 'default', 'action' => 'index'),
	// 对URL中的变量名字进行正则匹配，满足条件才注册此变量
	'reqs' => array('module' => '[a-zA-Z0-9\.\-_]+', 
				'action' => '[a-zA-Z0-9\.\-_]+'),
	// 标识变量的前缀，对应URL中变量名字
	'varprefix' => ':',
	// URL中变量的分隔符号
	'delimiter' => '-',
	// 后缀，常用来将URL模拟成单个文件
	'postfix' => '.html',
	// REWRITE STANDARD PATH_INFO三种模式，不分大小写
	'protocol' => 'REWRITE',  
	);
// 美化url为目录
$config['router.routing_table'] = array(
	// URL中的变量名字
	'pattern' => ":module/:action/*",
	// 默认的module和action的名字
	'default' => array('module' => 'default', 'action' => 'index'),
	// 对URL中的变量名字进行正则匹配，满足条件才注册此变量
	'reqs' => array('module' => '[a-zA-Z0-9\.\-_]+', 
				'action' => '[a-zA-Z0-9\.\-_]+'),
	// 标识变量的前缀，对应URL中变量名字
	'varprefix' => ':',
	// URL中变量的分隔符号
	'delimiter' => '/',
	// 后缀，常用来将URL模拟成单个文件
	'postfix' => '',
	// REWRITE STANDARD PATH_INFO三种模式，不分大小写
	'protocol' => 'REWRITE',  
	);
```

  * 此模式需要服务器配合使用，如果apache启用.htaccess文件支持，参考下边修改apache的配置

```
Options Indexes FollowSymLinks ExecCGI
AllowOverride All
```

  * 可以将.htaccess文件放在框架入口文件同一目录，内容如下：

```
# .htaccess文件内容
# 如果访问的文件或者目录不存在，rewrite到index.php文件处理
RewriteEngine On

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

RewriteRule . index.php
```

  * 对于nginx参考下面的修改配置

```
location /lotusphp/example/addressbook/web/
{
	if (!-e $request_filename) 
	{
		rewrite ^/(.*)$ /lotusphp/example/addressbook/web/index.php last;
	}
}   
location /lotusphp/example/Router/
{   
	if (!-e $request_filename)
	{
		rewrite ^/(.*)$ /lotusphp/example/Router/index.php last;
	}
}
```
# Lotus Router注意事项 #
  * 建议使用url组件输出，不要直接写链接地址。
  * url组件和router共用同一个配置。

# 扩展Router类 #
```
```


# 延伸阅读：我们为什么要做Router #
  * 开发环境通常使用标准链接访问，生产环境通常会启用url重写，只需要简单的修改一下配置文件，程序代码不需要更改就可以适应不同的服务器环境。

## Lotus Router如何解决这些问题 ##

## 常见问题 ##


# 鸣谢 #