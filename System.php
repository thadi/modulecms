<?php

class System{

	private static $sys = null;
	private $modules = array();
	private $modulesObj = null;

	/**
	 * Jumpin-Method to all available Modules
	 * Use $moduleName Paramter to get the Module by Name
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

	/**
	 * Returns all Modulenames.
	 * e.g. Log, Abstract, Setting
	 * @return array
	 */
	public function getModules(){
		return System::get()->getModule()->getFilesystemModule()->getFiles(System::get()->moduleDir());
	}

	/**
	 * Returns the name of the current Project (Foldername)
	 * @return string
	 */
	public function projectName(){
		return basename(__DIR__);
	}

	/**
	 * Return the Path to the Directory of the Project
	 * @return string
	 */
	public function rootDir(){
		return __DIR__;
	}
	
	/**
	 * Returns the Path to the Modules-Directory of the Project
	 * @return string
	 */
	public function moduleDir(){
		return __DIR__ . "/modules/";
	}

	/**
	 * Sets the ModulesObject (private Variable)
	 * @param Modules $modulesObj
	 */
	public function setModulesObj($modulesObj){
		$this->modulesObj = $modulesObj;
	}

	/**
	 * Generates the Module-Class
	 * Extracts all Modules in the Module-Folder and generates the corresponding Method
	 * to get the Module.
	 * Writes the Class to Modules.php in the Project-Folder
	 */
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

	/**
	 * Sets the sys Variable (Singlton)
	 * @param System $system
	 */
	public static function set($system){
		self::$sys = $system;
	}

	/**
	 * Returns the System-Instance
	 * @return System
	 */
	public static function get(){
		return self::$sys;
	}
}

?>
