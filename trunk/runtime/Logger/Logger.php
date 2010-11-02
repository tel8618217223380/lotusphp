<?php
class LtLogger
{
	public $conf = array(
		"separator" => "\t",
		"log_file" => ""
	);

	private $fileHandle;

	protected function getFileHandle()
	{
		if (null === $this->fileHandle)
		{
			if (empty($this->conf["log_file"]))
			{
				trigger_error("no log file spcified.");
			}
			mkdir(dirname($this->conf["log_file"]), 0777, true);
			$this->fileHandle = fopen($this->conf["log_file"], "a");
		}
		return $this->fileHandle;
	}

	public function log($logData)
	{
		if ("" == $logData || array() == $logData)
		{
			return false;
		}
		if (is_array($logData))
		{
			$logData = implode($this->conf["separator"], $logData);
		}
		$logData = date("Y-m-dTH:i:s") . $this->conf["separator"] . $logData. "\n";
		fwrite($this->getFileHandle(), $logData);
	}
}