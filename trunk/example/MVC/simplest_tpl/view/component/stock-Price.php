<fieldset>
	<legend>Stock price of <em><?php echo $data['companyName'];?></em></legend>
	Last Trade: <?php echo $data['stockPrice'][0];?>
	<br />
	52wk Range: <?php echo $data['stockPrice'][1];?>-<?php echo $data['stockPrice'][2];?>
</fieldset>
{include 'test-test'}
