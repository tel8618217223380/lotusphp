<fieldset>
	<legend>Stock price of <em>{$data['companyName']}</em></legend>
	Last Trade: {$data['stockPrice'][0]}
	<br />
	52wk Range: {$data['stockPrice'][1]}-{$data['stockPrice'][2]}
</fieldset>
{include 'test-test'}
