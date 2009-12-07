<?php
class JsonView extends AbstractView
{
	public function render()
	{
		echo json_encode($this->result);
	}
}