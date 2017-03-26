<?php

class AbstractModule {

	protected $data = array();

	public function index(){
		return $this->view();
	}

	public function controller($methodName, $arguments = array()){
		if(method_exists($this, $methodName)){
			return call_user_method_array($methodName, $this, $arguments);	
		}else{
			$this->data['moduleName'] = $this->getModuleName();
			$this->data['methodName'] = $methodName;
			$this->data['arguments'] = $arguments;
			return $this->renderHtml("methodNotImplemented");
		}
	}

	public function getModuleName(){
		$className = get_class($this);
		return preg_replace('/Module$/', '', $className);
	}

	public function getModuleNameLower(){
		return strtolower($this->getModuleName());
	}

	public function getDir(){
		$module = $this->getModuleName();
		return System::get()->moduleDir() . $module;
	}

	public function getHtmlDir(){
		return $this->getDir() . "/html";
	}

	public function getCssDir(){
		return $this->getDir() . "/css";
	}

	public function getJsDir(){
		return $this->getDir() . "/js";
	}

	public function getFileDir(){
		return $this->getDir() . "/file";
	}

	public function renderFile($fileName){
		return $this->render($fileName, $this->getFileDir());
	}

	public function renderJs($jsName){
		return $this->render($jsName, $this->getJsDir(), 'js');
	}

	public function renderCss($cssName){
		return $this->render($cssName, $this->getCssDir(), 'css');
	}

	public function renderHtml($htmlName){
		return $this->render($htmlName, $this->getHtmlDir(), 'html');
	}

	public function render($fileName, $path, $extension = false){
		$file = $fileName . (!empty($extension) ? '.' . $extension : '');
		$path = $path . '/' . $file;
		if(file_exists($path)){
			ob_start();
			include($path);
			$content = ob_get_clean();
			return $content;
		}else{
			$dirFn = "getFileDir";
			if($extension){
				$dirFn = "get" . ucfirst($extension) . "Dir";	
			}
			$altPath = System::get()->getModule()->getAbstractModule()->$dirFn() . '/' . $file; 
			if(file_exists($altPath)){
				return $this->render($fileName, System::get()->getModule()->getAbstractModule()->$dirFn(), $extension);
			}
			System::get()->getModule("log")->error($this->getModuleName(), "file not found: " . $path);
			return '';
		}
	}

	public function view($id = false){

		$defaultQuery = System::get()->getModule()->getSettingModule()->getSetting("grid_query", array(
			"size" => 20,
			"from" => 0,
		));

		$requestModule = System::get()->getModule()->getRequestModule();
		if($requestModule->getParameter("size")){
			$size = $requestModule->getParameter("size");
			$size = $size > 0 ? $size : 20;
			$defaultQuery['size'] = $size;
		}
		if($requestModule->getParameter("page")){
			$page = $requestModule->getParameter("page");
			$page = $page > 0 ? $page : 1;
			$defaultQuery['from'] = ($page - 1) * $defaultQuery['size'];
		}

		$query = $defaultQuery;
		if(!empty($id)){
			$query = System::get()->getModule()->getPersistenzModule()->getIdQuery($id);
		}

		$data = $this->get($query);

		$max_page = ceil($data['hits']['total'] / $query['size']);
		$cur_page = floor($query['from'] / $query['size']) + 1;	
		$this->data['info'] = array(
			"total_hits" => $data['hits']['total'],
			"cur_page" => $cur_page,
			"max_page" => $max_page,
			"size" => $query['size']
		);
		if($cur_page > 1){
			$this->data['info']['prev_page'] = System::get()->getModule()->getUriModule()->getCurrentUri(array(
				"page" => ($cur_page - 1)
			));
		}
		if($cur_page < $max_page){
			$this->data['info']['next_page'] = System::get()->getModule()->getUriModule()->getCurrentUri(array(
				"page" => ($cur_page + 1)
			));	
		}

		$this->data["entries"] = array();
		if(!empty($data["hits"]["hits"])){
			$this->data["entries"] = $this->extractEntries($data["hits"]["hits"]);
		}
		return $this->renderHtml("view");
	}

	public function edit($id = false){
		$this->data['edit'] = !empty($id);
		$this->data['data'] = "";
		$this->data['id'] = "";
		if(!empty($id)){
			$this->data['id'] = $id;
			$this->data['data'] = $this->getById($id);
			$this->data['data'] = json_encode($this->data['data']);
		}
		return $this->renderHtml('edit');
	}

	public function save(){
		$requestModule = System::get()->getModule()->getRequestModule();
		$id = $requestModule->getParameter("id");
		$data = $requestModule->getParameter("data");

		$data = json_decode($data, true);

		$this->set($id, $data);
		$requestModule->forward(System::get()->getModule()->getUriModule()->getAdminUri($this->getModuleNameLower()));
	}

	public function del($id = false){
		System::get()->getModule()->getPersistenzModule()->del($this->getModuleNameLower(), $id);
		System::get()->getModule()->getRequestModule()->forward($this->getAdminActionUri());
	}

	public function extractEntries($data){
		return array_map(function($entry){
			return array("id" => $entry['_id'], "data" => json_encode($entry['_source']));
		}, $data);
	}

	public function curl($url, $options = array()){
		return System::get()->getModule()->getRequestModule()->curl($url, $options);
	}

	public function log($msg){
		System::get()->getModule()->getLogModule()->logEntry($this->getModuleName(), $msg);
	}

	public function debug($msg){
		System::get()->getModule()->getLogModule()->debug($this->getModuleName(), $msg);
	}

	public function set($id = false, $data = array()){
		return System::get()->getModule()->getPersistenzModule()->set($this->getModuleName(), $id, $data);
	}

	public function get($query = array()){
		return System::get()->getModule()->getPersistenzModule()->get($this->getModuleName(), $query);
	}

	public function getById($id){
		$query = System::get()->getModule()->getPersistenzModule()->getIdQuery($id);
		$result = $this->get($query);
		if(!empty($result["hits"]["hits"][0])){
			return $result["hits"]["hits"][0]['_source'];
		}
		return array();
	}

	public function getActionUri($action = false, $arguments = array()){
		return System::get()->getModule()->getUriModule()->getModuleUri($this->getModuleNameLower(), $action, $arguments);
	}
	public function getAdminActionUri($action = false, $arguments = array()){
		return System::get()->getModule()->getUriModule()->getAdminUri($this->getModuleNameLower(), $action, $arguments);	
	}
}

?>