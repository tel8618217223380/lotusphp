# 系统需求 #
  1. 只支持php 5（lotusphp所有组件都要求php5环境）

# 用法 #
## 运行lotus自带的例子 ##
  1. 在http://code.google.com/p/lotusphp/downloads/list?q=Url下载最新版本，解压后请放到相应的网站目录
  1. 运行example\Url\simplest.php，通过Web访问http://localhost/lotusphp/example/Url/simplest.php
  1. 屏幕上显示"Index/Add"说明运行成功
  * 看一下Url\simplest.php的源码能帮助你理解这个示例，他们非常简单，还有中文注释

## 示例1：最简单的用法 ##
```
<?php
/*
 * 加载Url类文件
 */
include "/Url所在目录/runtime/Url/Url.php";
include "/Url所在目录/runtime/Url/UrlConfig.php";
/*
 * 加载Url类文件
 */

/*
 * 开始使用Url
 */
$url = new LtUrl;
$url->conf->patern = "rewrite";
echo $url->generate("Index", "Add");
```

## 示例2：在生产环境获取更好的性能 ##

## 示例3：和lotusphp框架的其它组件一起工作 ##
# Lotus Url注意事项 #


# 扩展Url类 #
```
```


# 延伸阅读：我们为什么要做Url #


## Lotus Url如何解决这些问题 ##

## 常见问题 ##


# 鸣谢 #