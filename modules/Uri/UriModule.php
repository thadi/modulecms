<?php

class UriModule extends AbstractModule{

	public function getModuleUri($module, $action = false, $arguments = array()){
		$arguments = is_array($arguments) ? $arguments : array($arguments);
		return System::get()->getModule()->getRequestModule()->getProtocol() . 
			   "//" . System::get()->getModule()->getRequestModule()->getHost() . 
			   '/' . strtolower($module) .
			   (!empty($action) ? '/' . $action : '') . 
			   (!empty($arguments) ? '/' . join('/', $arguments) : '');
	}

	public function getAdminUri($module, $action = false, $arguments = array()){
		$arguments = is_array($arguments) ? $arguments : array($arguments);
		return System::get()->getModule()->getRequestModule()->getProtocol() . 
			   "//" . System::get()->getModule()->getRequestModule()->getHost() . 
			   '/' . 'admin' .
			   '/' . strtolower($module) .
			   (!empty($action) ? '/' . $action : '') .
			   (!empty($arguments) ? '/' . join('/', $arguments) : '');
	}

	public function getCurrentUri($getParams = array()){
		if(!empty($getParams)){
			$GET = $_GET;
			foreach($getParams as $param => $value){
				$GET[$param] = $value;
			}
			$currentUri = System::get()->getModule()->getRequestModule()->getUri(false);
			return $currentUri . "?" . http_build_query($GET);
		}
		return System::get()->getModule()->getRequestModule()->getUri(true);
	}
}

?>