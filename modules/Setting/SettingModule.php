<?php

class SettingModule extends AbstractModule{

	const DEFAULT_ADMIN_MENU_SETTING = array(
		"visible" => array("Log", "Setting", "User")
	);
	
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

	public function getSetting($id, $default = array()){
		$value = $this->getById($id);
		if(empty($value)){
			$this->set($id, $default);
			$value = $default;
		}
		return $value;
	}
}

?>