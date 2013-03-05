<?php namespace psm\html;
if(!defined('psm\INDEX_FILE') || \psm\INDEX_FILE!==TRUE) {if(headers_sent()) {echo '<header><meta http-equiv="refresh" content="0;url=../"></header>';}
	else {header('HTTP/1.0 301 Moved Permanently'); header('Location: ../');} die("<font size=+2>Access Denied!!</font>");}
abstract class tplFile {

//	protected $tags = array();
	protected $blocks = array();

	protected static $cachedFiles = array();


	public static function LoadFile($modName, $theme, $filename) {
		$theme = \psm\Utils\Utils_Files::SanFilename($theme);
		$filename = str_replace('..', '', $filename);
		if(empty($modName))  return NULL;
		if(empty($theme))    return NULL;
		if(empty($filename)) return NULL;
		// portal html only
		if(empty($modName)) {
			$paths = \psm\Paths::getLocal('portal html').DIR_SEP.$theme;
		// search in portal and mod html
		} else {
			$paths = array();
			foreach(\psm\Paths::getLocal('html', $modName) as $p)
				$paths[] = $p.DIR_SEP.$theme;
		}
		// find file
		$filefound = \psm\Utils\Utils_Files::findFile($filename.'.html.php', $paths);
		if(!$filefound)
			die('<p>File not found! '.$filename.'.html.php'.'</p>');
		include_once($filefound);
		$clss = '\wa\html\html_'.$filename;
		if(!class_exists($clss))
			die('<p>Class not found! '.$clss.'</p>');
		return new $clss();
	}


//	protected function __construct() {
//		parent::__construct();
//	}


	public function addBlock($blockName, &$data, $top=FALSE) {
		if($data == NULL) return;
		$rendered = Engine::renderObject($data);
		// attempt loading block function
		if(!isset($this->blocks[$blockName]))
			$this->blocks[$blockName] = $this->getFunc($blockName);
		if($this->blocks[$blockName] == NULL)
			$this->blocks[$blockName] = '';
		// add to block
		if($top)
			$this->blocks[$blockName] = $rendered . $this->blocks[$blockName];
		else
			$this->blocks[$blockName] .= $rendered;
	}
	public function getBlock($blockName='block') {
		// return cached block
		if(isset($this->blocks[$blockName]))
			return $this->blocks[$blockName];
		// load block function
		$this->blocks[$blockName] = $this->getFunc($blockName);
		// block function not found
		if($this->blocks[$blockName] == NULL) {
			unset($this->blocks[$blockName]);
			return NULL;
		}
		// return loaded block
		return $this->blocks[$blockName];
	}
	protected function getFunc($blockName) {
		$func = '_'.$blockName;
		if(!method_exists($this, $func))
			return null;
		return $this->$func();
	}


	public function getFilePath() {
		return __FILE__;
	}


	public function &getTags() {
		return $this->tags;
	}
	public function &getTag($tag) {
		if(!isset($this->tags[$tag]))
			return null;
		return $this->tags[$tag];
	}
	public function addTag($tag, $value) {
		$this->tags[$tag] = $value;
	}


}
?>