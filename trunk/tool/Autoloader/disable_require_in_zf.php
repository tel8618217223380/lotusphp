<?php
class LotusConvertor {
	public $sourcePath;
	public $destinationPath;
	public $msg = array();
	public $fileCount = 0;
	/**
	 * 指定原始文件目录
	 * @param unknown_type $path
	 * @return unknown_type
	 */
	public function setSourcePath($path){
		if(is_dir($path)){
			$this->sourcePath = $path;
			return true;
		}else{
			$this->msg[] = "$path is not a directory";
			return false;
		}
	}
	/**
	 * 处理后文件保存目录
	 * @param unknown_type $path
	 * @return unknown_type
	 */
	public function setDestinationPath($path){
		if(!is_dir($path)){
			mkdir($path,0777);
		}
		$this->destinationPath = $path;
	}
	/**
	 * 对文件进行处理,
	 * 已把代替规则抽离到别一个类实现
	 * @param unknown_type $filename 文件名
	 * @param unknown_type $type
	 * @return unknown_type
	 */
	public function convert($filename,$type='Zf'){
		$content = file_get_contents($filename);
		$class = 'Convert'.$type;
		$newContent = call_user_func(array($class,'convert'),$content);
		if(isset($this->destinationPath)){
			$filename = str_replace($this->sourcePath,$this->destinationPath,$filename);
		}
		$dir = dirname($filename);
		if(!is_dir($dir)){
			@mkdir($dir,0777);
		}
		@file_put_contents($filename,$newContent);
		$this->fileCount ++;
		$msg = "$filename has been converted...";
		return $msg;
	}
	/**
	 * 获得指定目录下的文件列表,包含子目录
	 * @param unknown_type $path 指定目录
	 * @param unknown_type $suffix 后缀名过滤
	 * @return array 文件列表数组
	 */
	public function getFileList($path = '',$suffix= array('php','inc')){
		if('' === $path){
			$path = $this->sourcePath;
		}
		static $fileList = array();
		if ($handle = opendir($path)) {
			while (false !== ($file = readdir($handle))) {
				if (!in_array($file,array('.','..','.svn'))) {
					$filename = $path."/".$file;
					if (is_dir($filename)) {
						$this->getFileList($filename,$suffix);
					} else{
						$ext = pathinfo($file, PATHINFO_EXTENSION);
						if(in_array($ext,$suffix)){
							$fileList[] = $filename;
						}
					}
				}
			}
		}
		return $fileList;
	}
	/**
	 * 入口
	 * @return unknown_type
	 */
	public function main(){
		if(!empty($_POST)){
			if(!isset($_POST['source_path'])){
				$this->msg[] = "Source path can not be empty";
				include 'tpl.html';
				exit;
			}else{
				$res = $this->setSourcePath($_POST['source_path']);
				if(!$res){
					include 'tpl.html';
					exit;
				}
				$this->msg[] = "Source path : ".$_POST['source_path'];
			}
			if(!isset($_POST['destination_path'])){
				$this->msg[] = 'Destination path is the same as Source path';
			}else{
				$this->setDestinationPath($_POST['destination_path']);
				$this->msg[] = "Destination path : ".$_POST['destination_path'];
			}
			if(!isset($_POST['type'])){
				$type = 'Zf';
			}else{
				$type = $_POST['type'];
			}
			$fileList = $this->getFileList();
			foreach($fileList as $filename){
				$this->msg[] = $this->convert($filename,$type);
			}
		}
		include 'tpl.html';
	}
}
/**
 * 装封过滤Zf的算法
 * 必须实现静态方法  convert()
 * @author Administrator
 *
 */
class ConvertZf {
	/**
	 *
	 * @param unknown_type $content 待过滤内容
	 * @return unknown_type 返回过滤后的内容
	 */
	static function convert($content){
		$newContent = str_replace('require_once','//require_once',$content);
		return $newContent;
	}
}


/**
 * script  process
 */
set_time_limit(200);
$convertor = new LotusConvertor();
$convertor->main();