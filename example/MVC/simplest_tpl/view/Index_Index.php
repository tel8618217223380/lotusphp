The <em>index</em> module, <em>Index</em> action
<h1>{$this->code} - {$this->message}</h1>

<form>
<input type="text" name="username" value="{if (isset($this->data['username']))}
{$this->data['username']}{/if}" />
</form>

<pre>
<?php print_r($this->data);?>
</pre>