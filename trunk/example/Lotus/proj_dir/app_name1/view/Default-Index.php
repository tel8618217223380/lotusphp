<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Welcome LotusPHP</title>
<meta name="generator" content="lotusphp" />
</head>
<body>
<h1>{$this->message}</h1>
<pre>
LotusPHP开始工作啦！
code: {$this->code}{CR}{LF}
message: {$this->message}{CR}{LF}
username from mysql: {$this->data[username]}{CR}{LF}
username from sqlite: {$this->data[user_name]} created:{date('Y-m-d H:i:s',$this->data['created'])}
</pre>
</body>
</html>