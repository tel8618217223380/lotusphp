# 系统需求 #
  1. 只支持php 5（lotusphp所有组件都要求php5环境）
  1. 需要GD库支持

# 用法 #
## 运行lotus自带的例子 ##
  1. 在http://code.google.com/p/lotusphp/downloads/list?q=Captcha下载最新版本，解压后请放到相应的网站目录
  1. 运行example\Captcha\simplest.php，通过Web访问http://localhost/lotusphp/example/Captcha/simplest.php
  1. 屏幕上显示验证图片说明运行成功
  * 看一下Captcha\simplest.php的源码能帮助你理解这个示例，他们非常简单，还有中文注释

## 示例1：最简单的用法 ##
```
<?php
/*
 * 加载Captcha类文件
 */
include "/Captcha所在目录/runtime/Captcha/Captcha.php";
include "/Captcha所在目录/runtime/Captcha/CaptchaConfig.php";
/*
 * 加载Captcha类文件
 */

/*
 * 开始使用Captcha
 */
$captcha = new LtCaptcha;
$captcha->conf->secretKey = "lotusphp";
$seed = uniqid();
$captcha->generateImage($seed);
```

怎么样,是不是看到验证码图片了.

## 示例2：在生产环境获取更好的性能 ##

## 示例3：和lotusphp框架的其它组件一起工作 ##
# Lotus Captcha注意事项 #


# 扩展Captcha类 #
```
```


# 延伸阅读：我们为什么要做Captcha #


## Lotus Captcha如何解决这些问题 ##

## 常见问题 ##


# 鸣谢 #