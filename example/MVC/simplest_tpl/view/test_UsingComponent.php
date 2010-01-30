<h3>About IBM</h3>
<div style="width: 75%; float: left;">International Business Machines Corporation, abbreviated IBM and nicknamed "Big Blue" (for its official corporate color), is a multinational computer technology and consulting corporation headquartered in Armonk, New York, United States. The company is one of the few information technology companies with a continuous history dating back to the 19th century. IBM manufactures and sells computer hardware and software, and offers infrastructure services, hosting services, and consulting services in areas ranging from mainframe computers to nanotechnology.</div>

<div style="width: 25%; float: right;">
<?php
$dispatcher = new LtDispatcher;
$dispatcher->viewDir = "./simplest_tpl/view/";
$this->context->companyName = 'IBM';
$dispatcher->dispatchComponent("stock", "Price", $this->context);
$this->data = array_merge($this->data,$dispatcher->data);
?>
{component stock Price}
</div>

<div style="clear:both"></div>
<div style="margin:0 auto;width:300px;">
<p>一次dispatcher可以多次使用了<br />要注意多个dispatcher之间变量冲突</p>
{component stock Price}
</div>
