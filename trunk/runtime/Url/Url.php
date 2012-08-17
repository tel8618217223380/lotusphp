<?php
class LtUrl
{
	public $configHandle;
	public $baseUrl = ''; // 例如 $baseUrl=http://www.example.com
    public $withPath = true; // 默认包含相对路径
	
    // module action 的默认值
	private $default = array('module'=>'default', 'action'=>'index');
	private $delimiter = '-';// 分隔符
	private $postfix = '.html';  // 后缀
	private $protocol = 'STANDARD'; // REWRITE PATH_INFO STANDARD

	public function __construct()
	{
		if (! $this->configHandle instanceof LtConfig)
		{
			if (class_exists("LtObjectUtil", false))
			{
				$this->configHandle = LtObjectUtil::singleton("LtConfig");
			}
			else
			{
				$this->configHandle = new LtConfig;
			}
		}
	}
	public function init()
	{
		$routingTable = $this->configHandle->get("router.routing_table");

		if (!empty($routingTable))
		{
			if (isset($routingTable['default']))
			{
				$this->default = $routingTable['default'];
			}
			if (isset($routingTable['delimiter']))
			{
				$this->delimiter = $routingTable['delimiter'];
			}
			if (isset($routingTable['postfix']))
			{
				$this->postfix = $routingTable['postfix'];
			}
			if (isset($routingTable['protocol']))
			{
				$this->protocol = $routingTable['protocol'];
			}
		}

		$this->protocol = strtoupper($this->protocol);
	}
	
	public function getLink($module, $action, $args = array(), $baseUrl = null)
	{
		return $this->generate($module, $action, $args, $baseUrl, 'STANDARD');
	}

	public function generate($module, $action, $args = array(), $baseUrl = null, $protocol = null)
	{
		if ($baseUrl)
		{
			$this->baseUrl = $baseUrl;
		}
		$protocol = $protocol ? strtoupper($protocol) : $this->protocol;
		$url = '';
		switch ($protocol)
		{
			case 'REWRITE':
				$url = $this->withPath ? rtrim(pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME), '\\/') . '/' : '';
				$url .= $module . $this->delimiter . $action;
				$url .= $this->build_url($args);
				break;
			case 'PATH_INFO':
				$url = $this->withPath ? $_SERVER['SCRIPT_NAME'] . '/' : '';
				$old_delimiter = $this->delimiter;
				$this->delimiter = '/';
				$url .= $module . '/' . $action;
				$url .= $this->build_url($args);
				$this->delimiter = $old_delimiter;
				break;
			default :
				$url = $this->withPath ? $_SERVER['PHP_SELF'] . '?' : '?';
				$old_delimiter = $this->delimiter;
				$this->delimiter = '';
				$old_postfix = $this->postfix;
				$this->postfix = '';
				if (!is_array($args))
				{
					$args = array();
				}
				$arr = array_merge(array('module' => $module, 'action' => $action), $args);
				$url .= $this->standard_build_url($arr);
				$this->delimiter = $old_delimiter;
				$this->postfix = $old_postfix;
				break;
		}
		return $this->baseUrl . $url;
	}

	private function standard_build_url($arr)
	{
		$url = '';
		foreach ($arr AS $key=>$value)
		{
			$url .= rawurlencode($key) . '=' . rawurlencode($value) . '&';
		}
		return rtrim($url, '&');
	}

	private function build_url($arr)
	{
		$url = '';
		if (!empty($arr) && is_array($arr))
		{
			foreach ($arr AS $key=>$value)
			{
				$key = str_replace($this->delimiter, '%FF', $key);
				$value = rawurlencode($value);
				$value = str_replace(rawurlencode($this->delimiter), '%FF', $value);
				$url .= $key . $this->delimiter . $value . $this->delimiter;
			}
			$url = $this->delimiter . substr($url, 0, -1);
		}
		return $url . $this->postfix;
	}
}
