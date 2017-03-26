<?php

class AdminModule extends AbstractModule{

	public function controller($calledModule, $arguments = array()){

		if(!System::get()->getModule()->getUserModule()->hasLevel(1)){
			//return System::get()->getModule("user")->renderHtml("signin");
		}
		
		$modules = System::get()->getModule()->getSettingModule()->getSetting("admin_menu",SettingModule::DEFAULT_ADMIN_MENU_SETTING)['visible'];
		$modules = array_map(function($module){
			return strtolower($module);
		}, $modules);

		$this->data['module'] = $this->getModuleName();

		if(in_array($calledModule, $modules)){
			$this->data['module'] = $calledModule;
			$method = "index";
			if(!empty($arguments[0])){
				$method = $arguments[0];
				$arguments = array_slice($arguments, 1);
			}
			$this->data['content'] = System::get()->getModule($calledModule)->controller($method, $arguments);
		}else{
			$this->data['content'] = parent::controller($calledModule, $arguments);
		}

		$this->data['modules'] = $modules;

		return $this->renderHtml('menu');
	}

	public function index(){
		return "<h2>Welcome to the Adminmenu</h2>";
	}
}

?>