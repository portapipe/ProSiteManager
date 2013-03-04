<?php namespace psm\Portal;
if(!defined('psm\INDEX_FILE') || \psm\INDEX_FILE!==TRUE) {if(headers_sent()) {echo '<header><meta http-equiv="refresh" content="0;url=../"></header>';} else {header('HTTP/1.0 301 Moved Permanently'); header('Location: ../');} die("<font size=+2>Access Denied!!</font>");}
class Module_Loader {

	// selected module;
	private static $moduleName = NULL;
	// module instances
	private static $modules = array();


	// load mods.txt modules list
	public static function &LoadModulesTxt($modsFile) {
		if(!\psm\Utils\Utils_Strings::endsWith($modsFile, '.txt'))
			$modsFile .= '.txt';
		// mods.txt file not found
		if(!file_exists($modsFile))
			die('Modules list file not found!');
		$data = file_get_contents($modsFile);
		$array = explode("\n", $data);
		foreach($array as $line) {
			$line = trim($line);
			if(empty($line)) continue;
			// already loaded
			if(isset(self::$modules[$line])) continue;
			// load module
			$mod = self::LoadModule($line);
			// failed to load
			if($mod == null) continue;
			// mod loaded successfully
			self::$modules[$line] = $mod;
		}
		return self::$modules;
	}
	// load a module
	private static function LoadModule($name) {
		// file mod/mod.php
		$file = \psm\Portal::getLocalPath('module', $name).DIR_SEP.$name.'.php';
		// module file not found
		if(!file_exists($file)) {
//TODO:
			echo '<p>Module file not found! '.$name.'</p>';
			return;
		}
		include $file;
		// class \mod\module_mod
		$clss = $name.'\module_'.$name;
		// class not found
		if(!class_exists($clss)) {
//TODO:
			echo '<p>Module class not found! '.$clss.'</p>';
			return;
		}
		return new $clss();
	}


	// selected module name
	public static function getModuleName() {
		if(!empty(self::$moduleName)) return self::$moduleName;
		// mod from url
		self::$moduleName = \psm\Utils\Vars::getVar('mod', 'str');
		if(!empty(self::$moduleName)) return self::$moduleName;
		// mod from define
		if(defined('psm\MODULE'))
			self::$moduleName = \psm\MODULE;
		if(!empty(self::$moduleName)) return self::$moduleName;
		// default mod
		self::$moduleName = \psm\DEFAULT_MODULE;
		if(!empty(self::$moduleName)) return self::$moduleName;
		return reset(self::$modules);
	}


	// selected module instance
	public static function getModule() {
		$name = self::getModuleName();
		if(isset(self::$modules[$name]))
			return self::$modules[$name];
		return NULL;
	}


}
?>