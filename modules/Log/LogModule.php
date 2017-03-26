<?php

class LogModule extends AbstractModule{
	public function error($module, $msg){

		if(empty($msg)){
			$msg = $module;
			$module = $this->getModuleNameLower();
		}

		error_log("ERROR: " . $module . ": " . $msg);
	}

	public function debug($module, $msg = false){

		if(empty($msg)){
			$msg = $module;
			$module = $this->getModuleNameLower();
		}

		error_log("DEBUG: " . $module . ": " . $msg);
	}

	public function logEntry($module, $msg){
		$this->set(false, array(
			"module" => $module,
			"msg" => $msg
		));
	}
}

?>