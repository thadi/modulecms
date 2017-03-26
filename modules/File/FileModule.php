<?php

class FileModule extends AbstractModule{

	public function css($module, $file = false){
		if(!empty($file)){
			$file = preg_replace('/\.css/', '', $file);
			return System::get()->getModule($module)->renderCss($file);
		}else{
			$file = preg_replace('/\.css/', '', $module);
			return $this->render($file, System::rootDir(), 'css');
		}
	}
}


?>