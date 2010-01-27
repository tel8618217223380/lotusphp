<?php
class LtUrl
{
	public $conf;
	public $routingTable;

	public function __construct()
	{
		$this->conf = new LtUrlConfig;
		$this->routingTable = $this->conf->routingTable;
		// unset($this->conf);
	}
	public function init()
	{
		//
	}

	public function generate($module, $action, $args = array())
	{
		$args['module'] = $module;
		$args['action'] = $action;
		return $this->reverseMatchingRoutingTable($args);
	}

	/**
	 * ����������ƥ��·�ɱ�, ����ƥ����url
	 * 
	 * @param array $params 
	 * @return string 
	 */
	public function reverseMatchingRoutingTable($args)
	{
		$ret = $this->routingTable['pattern'];
		$default = $this->routingTable['default'];
		$reqs = $this->routingTable['reqs'];
		$delimiter = $this->routingTable['delimiter'];
		$varprefix = $this->routingTable['varprefix'];
		$postfix = $this->routingTable['postfix'];

		$pattern = explode($delimiter, trim($this->routingTable['pattern'], $delimiter));

		foreach($pattern as $k => $v)
		{
			if ($v[0] == $varprefix)
			{ 
				// ����
				$varname = substr($v, 1); 
				// ƥ�����
				if (isset($args[$varname]))
				{
					$regex = "/^{$this->routingTable['reqs'][$varname]}\$/i";
					if (preg_match($regex, $args[$varname]))
					{
						$ret = str_replace($v, $args[$varname], $ret);
						unset($args[$varname]);
					}
				}
				else if (isset($default[$varname]))
				{
					$ret = str_replace($v, $default[$varname], $ret);
				}
			}
			else if ($v[0] == '*')
			{ 
				// ͨ���
				$tmp = '';
				foreach($args as $key => $value)
				{
					if (!isset($default[$key]))
					{
						$tmp .= $key . $delimiter . rawurlencode($value) . $delimiter;
					}
				}
				$tmp = rtrim($tmp, $delimiter);
				$ret = str_replace($v, $tmp, $ret);
				$ret = rtrim($ret, $delimiter);
			}
			else
			{ 
				// ��̬
			}
		}
		$protocol = strtoupper($this->routingTable['protocol']);
		if ('REWRITE' == $protocol)
		{
			$ret = $ret . $postfix;
		}
		else if ('PATH_INFO' == $protocol)
		{
			$ret = $_SERVER['SCRIPT_NAME'] . '/' . $ret . $postfix;
		}
		else
		{
			$ret = $ret . $postfix;
		}
		return $ret;
	}
}
