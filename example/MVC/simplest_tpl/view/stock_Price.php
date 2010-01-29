<fieldset>
	<legend>Stock price of <em><?php echo $this->data['companyName'] ?></em></legend>
	Last Trade: <?php echo $this->data['stockPrice'][0] ?>
	<br />
	52wk Range: <?php echo $this->data['stockPrice'][1] . '-' . $this->data['stockPrice'][2] ?>
</fieldset>
