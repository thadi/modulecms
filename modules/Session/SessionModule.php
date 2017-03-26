<?php

class SessionModule extends AbstractModule{
	
	public function startSession(){
		session_abort();
		session_start();
		$this->setValue("session", true);
	}
	
	public function endSession(){
		session_abort();
	}
	
	public function sessionRunning(){
		return $this->getValue("session");
	}
	
	public function setValue($key, $value){
		$_SESSION[$key] = $value;
	}
	
	public function getValue($key){
		if(!empty($_SESSION[$key])){
			return $_SESSION[$key];
		}
		return false;
	}
}

?>