<?php

class UserModule extends AbstractModule{
	
	public function view($id = false){
		$this->data['actions'] = array(
				array(
						"name" => "new",
						"type" => "link",
						"action" => "edit"
				)
		);
		return parent::view($id);
	}
	
	public function login($username, $password){
		$user = $this->getById($username);
		
		if(empty($user) || $user['password'] != $password){
			return $this->renderHtml("signin");
		}
		System::get()->getModule()->getSessionModule()->startSession();
		System::get()->getModule()->getSessionModule()->setValue("username", $username);
	}
	
	public function logout($username){
		System::get()->getModule()->getSessionModule()->endSession();
	}
	
	public function register($username, $password, $level = 3){
		
		if(!empty($this->getById($username))){
			return $this->renderHtml("signup");
		}
		
		$user = array(
			"name" => $username,
			"password" => $password,
			"level" => $level
		);
		$this->set($username, $user);
	}
	
	public function signin(){
		$username = System::get()->getModule()->getRequestModule()->getParameter("username");
		$password = md5(System::get()->getModule()->getRequestModule()->getParameter("password"));
		
		if(empty($username) || empty($password)){
			return $this->renderHtml("signin");
		}
		
		return $this->login($username, $password);
	}
	
	public function signup(){
		$username = System::get()->getModule()->getRequestModule()->getParameter("username");
		$password= md5(System::get()->getModule()->getRequestModule()->getParameter("password"));
		
		if(empty($username) || empty($password)){
			return $this->renderHtml("signup");
		}
		
		return $this->register($username, $password);
	}
	
	public function isLoggedIn(){
		return System::get()->getModule()->getSessionModule()->sessionRunning();
	}
	
	public function hasLevel($level){
		if(!$this->isLoggedIn()){
			return false;
		}
		$user = $this->getById(System::get()->getModule()->getSessionModule()->getValue("username"));
		if(empty($user['level']) || $user['level'] > $level){
			return false;
		}
		return true;
	}
}

?>