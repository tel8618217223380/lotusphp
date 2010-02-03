<h1>{$code} - {$message}</h1>

<form>
<input type="text" name="username" value="{$data[username]}" />
</form>

<div style="clear:both"></div>

<?php
$dispatcher = new LtDispatcher;
$dispatcher->viewDir = "./simplest_app/view/";
$this->context->companyName = 'DELL';
$dispatcher->dispatchComponent("stock", "Price", $this->context);
$this->data = array_merge($this->data,$dispatcher->data);
?>

<div style="margin:0 auto;width:300px;">
{component stock Price}
</div>
