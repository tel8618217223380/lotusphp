<?php
class LtValidator
{
	public $conf;

	public function __construct()
	{
		$this->conf = new LtValidatorConfig();
	}

	protected function _ban($value, $ruleValue)
	{
		return !preg_match($ruleValue, $value);
	}

	protected function _mask($value, $ruleValue)
	{
		return preg_match($ruleValue, $value);
	}

	protected function _equal_to($value, $ruleValue)
	{
		return $value === $ruleValue;
	}

	protected function _max_length($value, $ruleValue)
	{
		return mb_strlen($value) <= $ruleValue;
	}

	protected function _min_length($value, $ruleValue)
	{
		return mb_strlen($value) >= $ruleValue;
	}

	protected function _max_value($value, $ruleValue)
	{
		return $value <= $ruleValue;
	}

	protected function _min_value($value, $ruleValue)
	{
		return $value >= $ruleValue;
	}

	protected function _min_selected($value, $ruleValue)
	{
		return count($value) >= $ruleValue;
	}

	protected function _max_selected($value, $ruleValue)
	{
		return count($value) <= $ruleValue;
	}

	protected function _required($value, $ruleValue)
	{
		if (false == $ruleValue)
		{
			return true;
		}
		else
		{
			return is_array($value) && count($value) || strlen($value);
		}
	}

	/**
	 * Validate an element
	 * 
	 * @param mixed $value
	 * @param array $dtd
	 * @return array 
	 */
	public function validate($value, $dtd)
	{
		$errorMessages = array();
		$label = $dtd['label'];

		if (is_array($dtd['rules']) && count($dtd['rules']))
		{
			$rules = $dtd['rules'];
			$message = isset($dtd['message']) ? $dtd['message'] : array();
			foreach ($rules as $key => $val)
			{
				$validateFunction = '_' . $key;
				if ((is_bool($rules[$key]) || 0 < strlen($rules[$key])) && !$this->$validateFunction($value, $rules[$key]))
				{
					$errorMessages[$key] = sprintf((isset($message[$key]) && strlen($message[$key]) ? $message[$key] : $this->conf->errorMessage[$key]), $label, $rules[$key]);
				}
			}
		}
		return $errorMessages;
	}
}
