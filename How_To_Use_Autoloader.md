# 系统需求 #
  1. 只支持php 5（lotusphp所有组件都要求php5环境）
  1. 不要求Web服务器，可运行于命令行下

# 用法 #
## 运行lotus自带的例子 ##
  1. 在http://code.google.com/p/lotusphp/downloads/list?q=Autoloader下载最新版本，解压后放到任意目录（如果想通过Web访问，请放到相应的网站目录）
  1. 运行example\Autoloader\simplest.php，通过Web访问http://localhost/lotusphp/example/Autoloader/simplest.php，或者通过命令行访问都可以
  1. 屏幕上打印"Hello, this is sayHello() method of HelloWorld class"说明运行成功,仔细看看两个Array有什么不同,是不是发现这句话之前的那个Array没有HelloWorld.php,这就是按需加载.您可以试着再加个GoodBye.php里的class看看有什么不同.
  * 看一下Autoloader\simplest.php和HelloWorld.php的源码能帮助你理解这个示例，他们非常简单，还有中文注释

## 示例1：最简单的用法 ##
```
<?php
//加载Autoloader类文件
include "/Autoloader所在目录/runtime/Autoloader/Autoloader.php";

//指定需要自动加载的目录,可以指定多个目录,目录路径最好不要包含空格
$directories = array("目录1","目录2");
$autoloader = new LtAutoloader($directories);
//这里就开始自动加载您刚指定目录中的以.php,.inc结尾的文件

/*
 * 初始化完成，开始享受Autoloader的便利
 */
$class = new ClassA;//只要ClassA的文件在前面你指定的"目录1","目录2"或其子目录中,就能自动加载进来了.
```

## 示例2：在生产环境获取更好的性能 ##
黑话解释
  1. **生产环境** 本文档中所说的生产环境是指正式对外提供服务的环境，他他们通常在机房里
> 请参照示例1那一节写的,$autoloader = new LtAutoloader($directories);这个扫描目录是很费时间的,在开发环境,类库文件可能经常增加或者改名(文件名,类名),生产环境有哪些类库文件基本是不变的这样，我们可以把Autoloader返回的结果存到opcode cache(如APC)里,autoloader的性能会好很多倍.<br>注:本示例不适用于命令行下.(apc是web server管理的一块内存,所以多个http请求都可以共用这个内存;cli模式下，每次运行的pid都不一样，所以你存了apc,下次也取不到,同一个cli周期存取apc是可以的,但这样没有任何加速的意义)<br>
<pre><code>$cacheKey = "autoloader_cache_key";<br>
if ($cachedFileMapping = apc_fetch($cacheKey))//若从apc中获取到了class file mapping，则不要扫描目录了<br>
{<br>
    $autoloader = new LtAutoloader;<br>
    $autoloader-&gt;setFileMapping($autoloader);<br>
    $autoloader-&gt;init();<br>
}<br>
else//若apc中没有class file mapping，则扫描目录获得之，并存入apc<br>
{<br>
    $directories = array("Classes");<br>
    $autoloader = new LtAutoloader($directories);<br>
    $fileMapping = $autoloader-&gt;getFileMapping();<br>
    apc_add($cacheKey, $fileMapping);<br>
}<br>
</code></pre></li></ul>

<h2>示例3：和lotusphp框架的其它组件一起工作 ##

# 扩展Autoloader类 #
> 就是说你想定制属于自己的autoloader，可以extends它，例如：
```
class MyAutoloader extends LtAutoloader
{
    /*
     * 覆盖LtAutoloader->init()方法
     * 不加载非class文件
     */
    public function init()
    {
        spl_autoload_register(array($this, "loadClass"));
    }
}

/*
 * 试用MyAutoloader
 */
$directories = array("Classes", "function");
$myAutoloader = new MyAutoloader($directories);

/*
 * 看看有哪些文件被包含进来了
 * function目录下的文件全都没包含进来
 */
print_r(get_included_files());
```
# 利用Lotus Autoloader加载第三方类库 #
我们以ZendFramework-1.9.2为例,一般大家最容易想到的就是把Zend的全路径告诉给Autoloader,然后会被自动加载的.恭喜你,这么做就成功了一半,不过ZF的一些类用了require\_once(相对路径)来加载他依赖的类，所以我们还需要一小步来搞定这个问题,以下两种方案供您选择.<br>
方案一：<br>
注释ZendFramework里所有的require_once,这样所用到的类就会靠LtAutoloader加载.<br>
方案二：<br>
set_include_path( get_include_path() . "; ZendFramework library所在的路径" );这样所用到的类是靠Zend_Loader::include来加载.</li></ul>

两种方案的优缺点:<br>
<blockquote>方案一的优点是没了require\_once,相对要快;缺点是修改了Zend的源码<br>
方案二的优点是不用改zend的源码;缺点是有require_once(比require慢),且是相对路径（比绝对路径慢）</blockquote>

两种方案您可以任选一种,我们建议在生产环境:推荐方案一,开发环境:推荐方案二
```

set_include_path( get_include_path() . ";D:/ZendFramework-1.9.2/library/" );

$directories = array("D:/ZendFramework-1.9.2/library/");
$autoloader = new LtAutoloader($directories);

$params = array ('host'     => 'localhost',
                 'username' => 'root',
                 'password' => 'root',
                 'dbname'   => 'mysql');

$db = Zend_Db::factory('Pdo_Mysql', $params);
Zend_Db_Table::setDefaultAdapter($db);
$result = $db->query('SELECT * FROM user');
$rows = $result->fetchAll();
Zend_Debug::dump($rows);
```

# 延伸阅读：我们为什么要做Autoloader #
黑话解释
  1. **类库文件** 本文档中所说的类库文件是指被包含（include/require）的公共文件，他们通常定义一些class, function

## 传统include/require的不足 ##
在没用Autoloader的时候，怎样加载类库文件？最容易想到的是用include/require来包含类库文件，这种文件包含通常会有如下问题：

  1. **目录名和文件名变化引起程序代码变化** <br>当类库文件目录名或者文件名需要更改的时候，所有include了这个文件的php文件也要随着修改，这加大了源代码目录结构重构的负担。<br><br>Windows和Linux对文件路径大小写和目录分隔符（斜线和反斜线）的处理不同，也使得PHP程序员需要花费相当一部分精力来应对文件名和文件路径问题。<br>
<ol><li><b>相对路径的性能问题</b> <br>我们不会把类库文件的绝对路径写死在代码里，于是采用相对路径。<br><br>一种做法是设置php.ini和include_path值，然后给include()传入一个相对路径，Zend Framework和雅虎就是这样做的，这种方案存在显而易见的性能问题，include_path的值越多，性能损失就越大。php引擎处理include_path的机制参见<a href='http://www.php.net/manual/en/ini.core.php#ini.include-path'>http://www.php.net/manual/en/ini.core.php#ini.include-path</a>。包含文件时使用绝对路径也能让APC，eAccelerator等Opcode Cache更有效地缓存他们。<br><br>另一种流行的方法是利用"FILE"魔术变量取得应用的根路径，include的时候使用基于“应用的根路径”的绝对路径,如include($appRoot . "conf/db.php")，这个方法很好的解决了相对路径带来的性能问题，CakePHP，Symfony等就是用的这种方案。<br>
</li><li><b>类库文件间相互依赖的问题</b> <br>类库文件之间存在依赖，为了保证运行时不出现“类定义找不到”的情况，类库文件会用将需要的更基础的类库包含进来，又为了保证不重复包含，通常要用include_once/require_once，Zend Framework就是这样做的。include_once比include慢。</li></ol>

当团队里不同水平，不同喜好的成员共同维护一份代码时，这些问题尤其严重。<br>
<br>
<h2>Lotus Autoloader如何解决这些问题</h2>
为了解决上面这些问题，我在kiwiphp/lotusphp中加入了autoloader。Lotus Autoloader是这样解决上述问题的：<br>
<ol><li>Autoloader会动态扫描需要自动加载的类库目录，生成array("类名" => "文件")数组，用了Autoloader的程序，完全不需要写include/require来加载类库，代码里不会出现类库文件的路径、文件名。<br>
</li><li>由于代码里已经不需要写类库文件的路径了，自然也不存在相对路径的问题。<br>
</li><li>php5的autoload()机制会解决这个依赖的问题，详细情况参见：<a href='http://php.net/manual/en/language.oop5.autoload.php'>http://php.net/manual/en/language.oop5.autoload.php</a></li></ol>

<h2>和其它Autoloader有什么不同</h2>
<ol><li>Zend Framework和QeePHP的Autoloader机制是通过类名翻译出文件路径，而Lotus Autoloader是查找一个 array("类名" => "文件")数组，原因是我们（Lotusphp开发者）希望类名不要跟文件路径耦合，这样PHP程序员写程序时不必关注类的路径，只要名字写对了，就可以加载，重构的时候调整了类文件的目录或者名字，也不影响调用他的程序。<br>另一个好处是这种方案兼容性较好，Zend Framework的组件也能被Lotus Autoloader自动加载。而Zend Framework和Qee则希望基于他们建立的应用程序命名更规范。<br>当然这种Autoload机制导致类库的class名字不能重复，在多年的实践中，我们发现，与“类名不要跟文件路径耦合”这个问题比起来，类名唯一引起的痛苦要小得多。</li></ol>

<h2>常见问题</h2>
<ol><li>Q:类名是不是必须得唯一?如果重名怎么办?<br>A:类名必须唯一,如果重名会被覆盖<br>
</li><li>Q:Autoloader会不会把所有的class都加载进来?<br>A:不会,只有在new HelloWorld的时候,才把HelloWorld所在的文件include进来,绝对的按需加载!<br>
</li><li>Q:怎么在Lotus里没看到autoload()函数?<br>A:Lotus Autoloader自动加载的函数实际是LtAutoloader->loadClass(),spl_autoload_register(array($this, "loadClass"));这个语句等于是告诉 php引擎:当你找autoload()的时候,就直接去找LtAutoloader->loadClass()吧.通过spl_autoload_register注册了自己的函数，PHP就不会调用autoload()函数，而会调用自定义的函数,也就是loadClass</li></ol>


<h1>鸣谢</h1>
<ol><li>Autoload的理念来自Symfony，我是用Symfony做应用的时候享受Autoload的好处才在kiwiphp中加入Autoloader的。<br>
</li><li>QeePHP的作者dualface等人让我知道了PHP5的autoload()机制，我才放弃了原本那个笨拙的办法：把所有类库打包成一个大文件，然后包含进来，我还为这个打包机制设计了黑白名单。详细讨论过程参见：<a href='http://www.phpchina.com/bbs/viewthread.php?tid=142775'>http://www.phpchina.com/bbs/viewthread.php?tid=142775</a>
</li><li>PHPChina的nightsailer提供的"class->file" mapping方案跟我最终采用的方案完全一样，刚开始我没看懂他的回复，等我自以为想到一个最合适的方案时，才发现之前nightsailer已经说过了：）