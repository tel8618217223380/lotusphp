<?php
class LtUrl
{
	public $conf;

	public function __construct()
	{
		$this->conf = new LtUrlConfig;
	}

	public function link($string)
	{
		echo "\tline " . $string . "\n";
		print_r($this->conf);
	}

	public function generate($module, $action, $args = null, $options = null)
	{
		$pattern = isset($options['pattern']) ? $options['pattern'] : $this->conf->patern;
		switch ($pattern)
		{
			case 'standard':
			default:// URL format: ?module=module_name&action=action_name&key=value
				$url = $this->conf->bootstrapFile . "?module=$module&action=$action";
				if ($args)
				{
					foreach($args as $key => $value)
					{
						if (isset($options['encoded']) && true == $options['encoded'])
						{
							$value = urlencode($value);
						}
						$url .= "&$key=$value";
					}
				}
				break;
			case 'rewrite':// URL formar: module_name/action_name?key=value
				$url = "$module/$action";
				if ($args)
				{
					$queryString = '';
					foreach($args as $key => $value)
					{
						if (isset($options['encoded']) && true == $options['encoded'])
						{
							$value = urlencode($value);
						}
						$queryString .= "&$key=$value";
					}
					$url .= '?' . substr($queryString, 1);
				}
				break;
			case 'path_info':// URL formar: module_name/action_name/key/value
				$url = $this->conf->bootstrapFile . "/$module/$action";
				if ($args)
				{
					foreach($args as $key => $value)
					{
						if (isset($options['encoded']) && true == $options['encoded'])
						{
							$value = urlencode($value);
						}
						$url .= "/$key/$value";
					}
				}
				break;
		}
		return $this->conf->prefix . $url;
	}
}