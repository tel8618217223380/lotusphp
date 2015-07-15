# 系统需求 #
  1. 只支持php 5（lotusphp所有组件都要求php5环境）
  1. 不要求Web服务器，可运行于命令行下

# 用法 #
## 运行lotus自带的例子 ##
  1. 在http://code.google.com/p/lotusphp/downloads/list?q=Validator下载最新版本，解压后放到任意目录（如果想通过Web访问，请放到相应的网站目录）
  1. 运行example\Validator\simplest.php，通过Web访问http://localhost/lotusphp/example/Validator/simplest.php，或者通过命令行访问都可以
  1. 屏幕上打印“Array ( [max\_length](max_length.md) => Username is longer than 4 [mask](mask.md) => 户名只能由数字或字组成 [ban](ban.md) => 用户名不能包含脏话 ) ”说明运行成功
  * 看一下Validator\simplest.php的源码能帮助你理解这个示例，他们非常简单，还有中文注释

## 示例1：最简单的用法 ##
```
<?php
//加载Validator类文件
include "/Validator所在目录/runtime/Validator/Validator.php";
include "/Validator所在目录/runtime/Validator/ValidatorConfig.php";
include "/Validator所在目录/runtime/Validator/ValidatorDtd.php";
//构造验证规则
$dtd = new LtValidatorDtd("用户名",
    array(
        "max_length" => 4,
        "mask" => "/^[a-z0-9]+$/i",
        "ban" => "/fuck/",
    ),
    array(
        //"max_length" 使用默认的错误消息，在$this->conf->errorMessage里
        "mask" => "用户名只能由数字或字组成",
        "ban" => "用户名不能包含脏话"
    )
);

//初始化Validator，执行验证

$validator = new LtValidator;
$username = "fuck my life";
$result = $validator->validate($username, $dtd);
print_r($result);
```

## 示例2：在生产环境获取更好的性能 ##
```
if ( !apc_fetch('validator_cache_key') )
{
    apc_add( 'validator_cache_key', $dtd );
    //apc_store( 'validator_cache_key', $dtd ); apc_sotre也可以
}

......

$result = $validator->validate($username,apc_fetch('validator_cache_key'));
```
关于APC,在Autoloader里已经详细介绍了.当验证项较多的时候,可以采用APC来提高效率.

## 示例3：和lotusphp框架的其它组件一起工作 ##
# Lotus Validator支持的验证方法 #
## 综合示例 ##
```
/*
 * 构造验证规则
 */
$dtd['username'] = new LtValidatorDtd("用户名",
    array(
        "max_length" => 8,
        "min_length" => 4,
        "mask" => "/^0.*$/i",
        "ban" => "/fuck/",
        "required" => true
    ),
    array(
        "max_length" =>  "%s最多只能有%s个字符",
        "min_length" =>  "%s最少必须有%s个字符",
        "mask" => "%s只能由0开头",
        "ban" => "%s不能包含脏话",
        'required' => '%s不能为空',
    )
);

$dtd['password'] = new LtValidatorDtd("密码",
    array(
        "max_length" => 8,
        "min_length" => 1,
        "required" => true
    ),
    array(
        "max_length" =>  "%s最多只能有%s个字符",
        "min_length" =>  "%s最少必须有%s个字符",
        'required' => '%s不能为空'
    )
);

$dtd['password_confirm'] = new LtValidatorDtd("验证密码",
    array(
        "required" => true,
        "equal_to" => $_REQUEST['password']
    ),
    array(
        "equal_to" => "两次输入的密码不一致"
    )
);

$dtd['age'] = new LtValidatorDtd("年龄",
    array(
        "max_value" => 99,
        "min_value" => 10,
        "mask" => "/^[1-9]*$/i",
        "required" => true
    ),
    array(
        "max_value" =>  "%s不能大于%s岁",
        "min_value" =>  "%s不能小于%s岁",
        "mask" => "%s只能由1-9开头",
        'required' => '%s不能为空'
    )
);

$dtd['interest'] = new LtValidatorDtd("兴趣",
    array(
        "max_selected" => 2,
        "min_selected" => 1,
        "required" => true
    ),
    array(
        "max_selected" =>  "%s最多只能选%s个",
        "min_selected" =>  "%s最少只能选%s个",
        'required' => '%s不能为空'
    )
);


/*
 * 初始化Validator，执行验证
 */
$validator = new LtValidator;
$username = "fuck";
$age = 0;
$password = "";
$result = array();
$result['username'] = $validator->validate($username,$dtd['username']);
$result['age'] = $validator->validate($age,$dtd['age']);
$result['password'] = $validator->validate($password,$dtd['password']);
print_r($result);
```

通过这个基本注册验证例子,我们已经把Validator支持的10个验证方法都用上了.(ban,mask,equal\_to,max\_length,min\_length,max\_value,min\_value,max\_selected,min\_selected,required)
下面分别讲讲这几个
### ban ###
通常用来验证字符串中不包含指定字符的,例如用户名不能包含xxx
<br>
输入只支持正则表达式,需要用//引起来.具体可以参考php手册里的preg_match函数<br>
<br>
<h3>mask</h3>
通常用来验证字符串中包含指定字符的,例如用户名只能以字母数字开头<br>
<br>
输入只支持正则表达式,需要用//引起来.具体可以参考php手册里的preg_match函数<br>
<br>
<h3>equal_to</h3>
通常用来验证两个值是否恒等,例如输入的两次密码是否相同<br>
<br>
输入可以是数字也可以是字符串<br>
<br>
<h3>max_length</h3>
通常用来验证输入的字符串是否超过了指定长度,例如用户名最多只能8个字母<br>
<br>
输入只能是数字<br>
<br>
<h3>min_length</h3>
通常用来验证输入的字符串是否少于指定长度,例如用户名最少4个字母<br>
<br>
输入只能是数字<br>
<br>
<h3>max_value</h3>
通常用来验证输入的数值是否大于指定的值,例如年龄最大为99岁<br>
<br>
输入只能是数字<br>
<br>
<h3>min_value</h3>
通常用来验证输入的数值是否小于指定的值,例如年龄最小为10岁<br>
<br>
输入只能是数字<br>
<br>
<h3>max_selected</h3>
通常用来验证选择的最多个数是否大于指定的值,例如最多只能选3个<br>
<br>
输入只能是数字<br>
<br>
<h3>min_selected</h3>
通常用来验证选择的最少个数是否小于指定的值,例如最少只能选1个<br>
<br>
输入只能是数字<br>
<br>
<h3>required</h3>
通常用来验证该验证选项是否为必填,例如用户名不能为空<br>
<br>
输入只能是布尔类型,默认是false<br>
<br>
<h1>扩展Validator类</h1>
<pre><code>class myValidator extends LtValidator<br>
{<br>
    protected function _username_can_be_used($value, $ruleValue = true)<br>
    {<br>
        /*此处定义您自己的数据查询,<br>
         * 如: $result = mysql_query("SELECT id FROM user WHERE username = '$value'") or die("Could not perform select query - " . mysql_error());<br>
         * $num_rows=mysql_num_rows($result);<br>
        */<br>
        $num_rows = 0;//这里仅供测试用<br>
        if ( 0 == $num_rows )<br>
        {<br>
            return true;<br>
        }<br>
    }<br>
}<br>
<br>
$dtd = new LtValidatorDtd("用户名",<br>
    array(<br>
        "ban" =&gt; "/fuck/",<br>
        "username_can_be_used" =&gt; "true",<br>
    ),<br>
    array(<br>
        "ban" =&gt; "用户名不能包含脏话",<br>
        "username_can_be_used" =&gt; "用户名重复"<br>
    )<br>
);<br>
<br>
<br>
$validator = new myValidator;<br>
$username = "覃健祥";<br>
$result = $validator-&gt;validate($username, $dtd);<br>
print_r($result);<br>
<br>
</code></pre>

<pre><code>$dtd = new LtValidatorDtd("IP",<br>
    array(<br>
        "is_ip" =&gt; true,<br>
    ),<br>
    array(<br>
        "is_ip" =&gt; "只能在0~254之间"<br>
    )<br>
);<br>
class myValidator extends LtValidator<br>
{<br>
    protected function _is_ip($value, $ruleValue = true)<br>
    {<br>
        $ips = explode(".", $value);<br>
        $flag = 0;<br>
        for($i=0;$i&lt;4;$i++)<br>
        {<br>
            if ( $ips[$i] &lt; 0 || $ips[$i] &gt; 254 )<br>
            {<br>
                $flag = 1;<br>
            }<br>
        }<br>
        if ( $flag == 1 )<br>
        {<br>
            return false;<br>
        }<br>
        else<br>
        {<br>
            return true;<br>
        }<br>
    }<br>
}<br>
<br>
$validator = new myValidator;<br>
$ip = "192.168.1.256";<br>
$result = $validator-&gt;validate($ip, $dtd);<br>
print_r($result);<br>
</code></pre>

通过这个示例我们可以看到,想让Validator具有什么样的功能都由您决定.基本上需要改动的很少.需要注意的是LtValidator的每个方法,return true是验证通过的意思.<br>
<br>
<h1>延伸阅读：我们为什么要做Validator</h1>


<h2>Lotus Validator如何解决这些问题</h2>

<h2>常见问题</h2>


<h1>鸣谢</h1>