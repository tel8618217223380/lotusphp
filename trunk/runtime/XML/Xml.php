<?php
/**
 *返回码定义
 */
/* 扩展内部错误 */
define("INTERNAL_ERR", -1);
/* 当前模式下不允许执行该函数 */
define("WRONG_MODE", 0);
/* 成功 */
define("SUCESS", 1);

/**
 * 模式定义
 */
define("READMODE", 0);
define("WRITEMODE", 1);

class LtXml {
	/**
	 * 只支持 ISO-8859-1, UTF-8 和 US-ASCII三种编码
	 */
	private $_supportedEncoding = array("ISO-8859-1", "UTF-8", "US-ASCII");

	/**
	 * XMLParser 操作句柄
	 */
	private $_handler;

	/**
	 *     READMODE 0:读模式，encoding参数不生效，通过输入的string获取version和encoding（getString方法不可用） 
	 *     WRITEMODE 1:写模式，按照制定的encoding和array生成string（getArray方法不可用） 
	 */
	public $mode;

	/**
	 * 该 XML 对象的编码，ISO-8859-1, UTF-8（默认） 或 US-ASCII
	 */
	public $encoding;
	
	/**
	 * 该 XML 对象的版本，1.0（默认）
	 */
	public $version;

	public function init($mode = 0, $encoding = "UTF-8", $version = "1.0") {
		$this->mode = $mode;

		$this->encoding = $encoding;
		$this->version = $version;

		$this->_getParser($encoding);
	}

	public function getArray($xmlString) {
		if (WRITEMODE === $this->mode) {
			return WRONG_MODE;
		}

		if (0 === preg_match("/version=[\"|\']([1-9]\d*\.\d*)[\"|\']/", $xmlString, $res)) {
			return INTERNAL_ERR;
		}
		else {
			$this->version = $res[1];
		}

		if (0 === preg_match("/encoding=[\"|\'](.*?)[\"|\']/", $xmlString, $res)) {
			$this->encoding = "UTF-8";
		}
		else {
			$this->encoding = strtoupper($res[1]);
		}

		$_array = $this->_stringToArray($xmlString);
		if (NULL === $_array) {
			return INTERNAL_ERR;
		}
		$currentArray = NULL;
		$openingTags = array();
		$array = $this->_getArrayTemplate;

		foreach ($_array as $tag) {
			$tag["tag"] = strtolower($tag["tag"]);
			if (isset($tag["type"]) && "close" == $tag["type"]
					&& isset($tag["tag"]) && ! empty($tag["tag"])) {
				if ($openingTags[count($openingTags) - 1]["tag"] == $tag["tag"]) {
					unset($openingTags[count($openingTags) - 1]);
				}
				else {
					return -1;
				}
			}
			else if ((isset($tag["type"]) && "complete" == $tag["type"])
						|| (isset($tag["type"]) && "open" == $tag["type"])
						&& isset($tag["tag"]) && ! empty($tag["tag"])){
				$currentArray = $this->_getArrayTemplate();
				$currentArray["tag"] = $tag["tag"];
				$currentArray["cdata"] = $tag["value"];
				if (isset($tag["attributes"]) && is_array($tag["attributes"])) {
					foreach($tag["attributes"] as $k => $v) {
						$currentArray["attributes"][strtolower($k)] = $v;
					}
				}

				if (0 == count($openingTags)) {
					$openingTags[] = &$array;
					$openingTags[0] = $currentArray;
				}
				else {
					$subCount = count($openingTags[count($openingTags) - 1]["sub"]);
					$openingTags[count($openingTags) - 1]["sub"][$subCount] = $currentArray;
					$openingTags[count($openingTags)] = &$openingTags[count($openingTags) - 1]["sub"][$subCount];
				}

				if ("complete" == $tag["type"]) {
					unset($openingTags[count($openingTags) - 1]);
				}
			}
			else if (isset($tag["type"]) && "cdata" == $tag["type"]
					&& isset($tag["tag"]) && ! empty($tag["tag"])) {
				if ($tag["tag"] == $openingTags[count($openingTags) - 1]["tag"]) {
					$openingTags[count($openingTags) - 1]["cdata"] .= trim($tag["value"]);
				}
				else {
					return -2;
				}
			}
		}

		if (0 < count($openingTags)) {
			return -3;
		}

		return $array;
	}

	public function getString($xmlArray) {
		if (READMODE === $this->mode)
			return WRONG_MODE;
	}

	/**
	 * 生成一个xml节点
	 * @param string tag 标签名
	 * @param string cdata 数据
	 * @param array attr 属性列表
	 * @param array sub 子标签列表
	 */
	public function createTag($tag, $cdata = "", $attr = array(), $sub = array()) {
		$newTag = $this->_getArrayTemplate();
		if (! is_string($tag)) {
			return INTERNAL_ERR;
		}

		$newTag["tag"] = $tag;

		return $newTag;
	}

	private function _getParser($encoding) {
		if (in_array($encoding, $this->_supportedEncoding))
			$this->_handler = xml_parser_create($encoding);
		else
			$this->_handler = NULL;
	}

	private function _stringToArray($xmlString) {
		$res = xml_parse_into_struct($this->_handler, $xmlString, $array);
		if (1 === $res)
			return $array;
		else
			return NULL;
	}

	private function _convertEntity($string) {
		$patterns = array("/</", "/</", "/&/", "/'/", "/\"/");
		$replacement = array("&lt;", "&gt;", "&amp;", "&apos;", "&quot;");

		return preg_replace($patterns, $replacement, $string);
	}

	private function _getArrayTemplate() {
		return array("tag" => "", "attributes" => array(), "sub" => array(), "cdata" => "");
	}
}

