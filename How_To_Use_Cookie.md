# 系统需求 #
  1. 只支持php 5（lotusphp所有组件都要求php5环境）

# 用法 #
## 运行lotus自带的例子 ##
  1. 在http://code.google.com/p/lotusphp/downloads/list?q=Cookie下载最新版本，解压后请放到相应的网站目录
  1. 运行example\Cookie\simplest.php，通过Web访问http://localhost/lotusphp/example/Cookie/simplest.php
  1. 屏幕上显示"set cookie ...."刷新后显示"hello"说明运行成功
  * 看一下Cookie\simplest.php的源码能帮助你理解这个示例，他们非常简单，还有中文注释

## 示例1：最简单的用法 ##
```
<?php
/*
 * 加载Cookie类文件
 */
include "/Cookie所在目录/runtime/Cookie/Cookie.php";
include "/Cookie所在目录/runtime/Cookie/CookieConfig.php";
/*
 * 加载Cookie类文件
 */

/*
 * 开始使用Cookie
 * php.ini需要修改为output_buffering = On
 */
//构造设置cookie的参数
$parameters = array(
    "name" => "newproj",
    "value" => "hello",
    "expire" => time()+3600
);

$cookie = new LtCookie;
$cookie->conf->secretKey = "lotusphp";
$cookie->setCookie($parameters);

//此处可以新建一个文件看看cookie设置成功没
$cookie_name = "newproj";
print_r($cookie->getCookie($cookie_name));
```

## 示例2：在生产环境获取更好的性能 ##

## 示例3：和lotusphp框架的其它组件一起工作 ##
# Lotus Cookie注意事项 #


# 扩展Cookie类 #
```
```


# 延伸阅读：我们为什么要做Cookie #


## Lotus Cookie如何解决这些问题 ##

## 常见问题 ##


# 鸣谢 #