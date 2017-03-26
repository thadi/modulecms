<?php

class System{

	private static $sys = null;
	private $modules = array();
	private $modulesObj = null;

	/**
	 * 
	 * @param string $moduleName
	 * @return Modules
	 */
	public function getModule($moduleName = false){
		if(empty($moduleName)){
			return $this->modulesObj;
		}
		
		if(!empty($this->modules[$moduleName])){
			return $this->modules[$moduleName];
		}
		$className = ucfirst($moduleName) . "Module";
		$this->modules[$moduleName] = new $className();
		return $this->modules[$moduleName];
	}

	public function getModules(){
		return System::get()->getModule("filesystem")->getFiles(System::get()->moduleDir());
	}

	public function projectName(){
		return basename(__DIR__);
	}

	public function rootDir(){
		return __DIR__;
	}

	public function moduleDir(){
		return __DIR__ . "/modules/";
	}

	public function setModulesObj($modulesObj){
		$this->modulesObj = $modulesObj;
	}
	
	public function updateModules(){
		$modules = $this->getModule("filesystem")->getFiles($this->moduleDir());
		$moduleFile = <<<'S'
<?php

class Modules{

	private $modules = array();
S;
		foreach($modules as $module){
			$moduleUpper = ucfirst($module);
			$moduleLower = strtolower($module);
			$moduleFile .= <<<S


    /**
	 * Returns the {$moduleUpper}Module
	 * @return {$moduleUpper}Module
	 */
	public function get{$moduleUpper}Module(){
		if(
S;
			$moduleFile .= '!empty($this->modules["' . $moduleLower . '"])){';
			$moduleFile .= <<<S

			return 
S;
			$moduleFile .= '$this->modules["' . $moduleLower . '"];';
			$moduleFile .= <<<S

		}
		
S;
			$moduleFile .= '$this->modules["' . $moduleLower . '"] = new ' . $moduleUpper . 'Module();';
			$moduleFile .= <<<S
			
		
S;
			$moduleFile .= 'return $this->modules["' . $moduleLower . '"];';
			$moduleFile .= <<<S

	}
S;
		}
		$moduleFile .= <<<S

}
?>
S;
		file_put_contents($this->rootDir() . "/Modules.php", $moduleFile);
	}
	
	public static function set($system){
		self::$sys = $system;
	}

	/**
	 * 
	 * @return System
	 */	
	public static function get(){
		return self::$sys;
	}
}

?>