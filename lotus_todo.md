## Lotus ToDo List ##
  1. Router和Url组件是一对，一个根据routr规则parse进来的url，一个根据route规则生成出去的url，共用同一个route rule
  1. Lotus存储三剑客可进一步抽象：多条操作（DB，Search）和单条操作（DB，Cache）
  1. ObjectUtil整合到lotus里面去，只有第一次singleton("LtCaptcha")时才初始化LtCaptcha组件
  1. DB的master/slave, queryType做成类常量
  1. DbHandle->query()增加queryType参数，lotus暂时parse不出来的queryType允许用户自己指定
  1. DbConnectionManager：在insert()和startTransaction()时将该缓存资源锁定，用完才释放，防止多线程操作产生脏数据
  1. Autoloader的tool借鉴Autoloader->parseLibNames()，用tokenizer查找include/require语句
  1. 将configHandle->get()的结果缓存为局部变量