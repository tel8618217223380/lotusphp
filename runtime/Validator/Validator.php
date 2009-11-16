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
		$label = $dtd->label;

		if (is_array($dtd->rules) && count($dtd->rules))
		{
			$messages = isset($dtd->messages) ? $dtd->messages : array();
			foreach ($dtd->rules as $key => $val)
			{
				$validateFunction = '_' . $key;
				if ((is_bool($dtd->rules[$key]) || 0 < strlen($dtd->rules[$key])) && !$this->$validateFunction($value, $dtd->rules[$key]))
				{
					$errorMessages[$key] = sprintf((isset($messages[$key]) && strlen($messages[$key]) ? $messages[$key] : $this->conf->errorMessage[$key]), $label, $dtd->rules[$key]);
				}
			}
		}
		return $errorMessages;
	}
}
