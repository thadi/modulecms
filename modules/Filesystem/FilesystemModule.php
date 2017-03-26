<?php

class FilesystemModule extends AbstractModule{

	public function getFiles($dir, $extension = false){
		if(!file_exists($dir)){
			return array();
		}
		$files = scandir($dir);
		$files = array_filter($files, function($file){
			return !empty($file) && ($file != '.' && $file != '..');
		});
		if(empty($files)){
			return array();
		}
		if(empty($extension)){
			return $files;
		}
		return array_filter($files, function($file) use ($extension){
			return preg_match('/\.' . $extension . '$/', $file);
		});
	}
}

?>