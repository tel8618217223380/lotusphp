# lotus component 开发应该遵循的原则 #
总的目标:所有组件都有一样的原则,互相不知道其他组件的存在
  1. 不和其他组件耦合,开发某个组件的时候,不能假定(或要求)其他组件存在
  1. 类名唯一
  1. 尽量回避static属性和方法(也有例外,ObjectUtil类就有两个static方法),要允许用户继承,须实例化才能运行.
  1. 提供init()方法,初始化必要的资源
  1. 使用简单,少用getter/setter方法
  1. 配置单独放在xxxConfig类里,xxx->conf = new xxxConfig;
  1. 命名空间,组件类都以Lt开头,如class LtCache
  1. component class本身并不实现singleton模式（就是说component不提供getInstance()之类的方法），更不能依赖singleton，用户可以用ObjectUtil::singleton()来实现所有类的singleton模式:ObjectUtil::singleton("ComponentName")
  1. 若组件由多个类组成,不处理各类之间的包含关系,不用include/require,文件包含由用户自行解决,例如使用lotusphp的autoloader,或者直接include/require进来
  1. 组件的php文件只有class定义,没有自动执行的语句.当这些文件被include时,除了定义这些类,不会发生其他动作(比如输出字串,发送header,创建文件,变量赋值等等)
  1. 不依赖绝对路径,可配置
  1. 涉及界面输出的地方不能写死,允许用户自定义,比如分页类输出的html是什么风格,应该让用户自定义
  1. 提供simplest, work with lotus两种示例