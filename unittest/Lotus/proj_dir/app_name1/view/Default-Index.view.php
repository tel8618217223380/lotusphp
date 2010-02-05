<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Welcome LotusPHP</title>
<meta name="generator" content="lotusphp" />
</head>
<body>
<h1>{$message}</h1>
<pre>
LotusPHP Works!
code: {$code}{CR}{LF}
message: {$message}{CR}{LF}
username from mysql: {$data[username]}{CR}{LF}
username from sqlite: {$data[user_name]} created:{date('Y-m-d H:i:s',$this->data['created'])}
</pre>
</body>
</html>