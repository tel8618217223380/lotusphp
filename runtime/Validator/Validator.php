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
				// callback_user_function
				if ('callback_' == substr($key, 0, 9))
				{
					$method = substr($key, 9);
					// 定义了过程函数
					if (function_exists($method))
					{
						if (!$method($value, $dtd->rules[$key]))
						{
							$errorMessages[$key] = sprintf((isset($messages[$key]) && strlen($messages[$key]) ? $messages[$key] : $this->conf->errorMessages[$key]), $label, $dtd->rules[$key]);
						}
						continue;
					}
					// 定义了类方法
					$rc = new ReflectionClass($val);
					if ($rc->hasMethod($method))
					{
						$rcMethod = $rc->getMethod($method);
						if ($rcMethod->isStatic())
						{
							$ret = $rcMethod->invoke(null, $value, $dtd->rules[$key]);
						}
						else
						{
							// 非静态方法需要一个实例 有待考虑单例
							$rcInstance = $rc->newInstance();
							$ret = $rcMethod->invoke($rcInstance, $value, $dtd->rules[$key]);
						}
						if (!$ret)
						{
							$errorMessages[$key] = sprintf((isset($messages[$key]) && strlen($messages[$key]) ? $messages[$key] : $this->conf->errorMessages[$key]), $label, $dtd->rules[$key]);
						}
						continue;
					}
					continue;
				}
				// end callback_user_function
				$validateFunction = '_' . $key;
				if ((is_bool($dtd->rules[$key]) || 0 < strlen($dtd->rules[$key])) && !$this->$validateFunction($value, $dtd->rules[$key]))
				{
					$errorMessages[$key] = sprintf((isset($messages[$key]) && strlen($messages[$key]) ? $messages[$key] : $this->conf->errorMessages[$key]), $label, $dtd->rules[$key]);
				}
			}
		}
		return $errorMessages;
	}
}
