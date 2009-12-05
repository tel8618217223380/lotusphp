<?php
include("./captcha.inc.php");

//绘制验证码图片
$captcha->generateImage($_GET["seed"]);