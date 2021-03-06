<?php

class AbstractModule {

	protected $data = array();

	/**
	 * Default method if no method is specified (via URL-Path)
	 * The default behavior of the index-Method is to call the view-Method
	 * @return string
	 */
	public function index(){
		return $this->view();
	}

	/**
	 * Startingpoint fot every request
	 * Every module decides for itself how to handle a request on its module
	 * The default behavior is to call the requested method (if it exists)  with the given arguments
	 * @param string $methodName
	 * @param array $arguments
	 * @return mixed|string
	 */
	public function controller($methodName, $arguments = array()){
		if(method_exists($this, $methodName)){
			return call_user_func_array(array($this, $methodName), $arguments);
		}else{
			$this->data['moduleName'] = $this->getModuleName();
			$this->data['methodName'] = $methodName;
			$this->data['arguments'] = $arguments;
			return $this->renderHtml("methodNotImplemented");
		}
	}

	/**
	 * Returns the name of the current module
	 * e.g. "Abstract", "Log"
	 * @return string
	 */
	public function getModuleName(){
		$className = get_class($this);
		return preg_replace('/Module$/', '', $className);
	}

	/**
	 * Returns the name of the current module in lowercase
	 * @return string
	 */
	public function getModuleNameLower(){
		return strtolower($this->getModuleName());
	}

	/**
	 * Returns the path to the directory of the current module
	 * @return string
	 */
	public function getDir(){
		$module = $this->getModuleName();
		return System::get()->moduleDir() . $module;
	}

	/**
	 * Returns the path to the html-Folder of the current module
	 * @return string
	 */
	public function getHtmlDir(){
		return $this->getDir() . "/html";
	}

	/**
	 * Returns the path to the css-Folder of the current module
	 * @return string
	 */
	public function getCssDir(){
		return $this->getDir() . "/css";
	}

	/**
	 * Returns the path to the js-Folder of the current module
	 * @return string
	 */
	public function getJsDir(){
		return $this->getDir() . "/js";
	}

	/**
	 * Returns the path to the files-Folder of the current module
	 * @return string
	 */
	public function getFileDir(){
		return $this->getDir() . "/file";
	}

	/**
	 * Returns the content of the requested file
	 * Every module can overwrite this behavior to meet its needs
	 * @param string $fileName
	 * @return string
	 */
	public function renderFile($fileName){
		return $this->render($fileName, $this->getFileDir());
	}

	/**
	 * Returns the content of the requested js-file
	 * Every module can overwrite this behavior to meet its needs
	 * @param string $jsName
	 * @return string
	 */
	public function renderJs($jsName){
		return $this->render($jsName, $this->getJsDir(), 'js');
	}

	/**
	 * Returns the content of the requested css.file
	 * Every module can overwrite this behavior to meet its needs
	 * @param string $cssName
	 * @return string
	 */
	public function renderCss($cssName){
		return $this->render($cssName, $this->getCssDir(), 'css');
	}

	/**
	 * Returns the content of the requested html-file
	 * Every module can overwrite this behavior to meet its needs
	 * @param string $htmlName
	 * @return string
	 */
	public function renderHtml($htmlName){
		return $this->render($htmlName, $this->getHtmlDir(), 'html');
	}

	/**
	 * Returns the content of the requested file
	 * If the file is not found in the specified $path (of a module),
	 * the method looks in the abstract-module as a fallback
	 * @param string $fileName
	 * @param string $path
	 * @param string $extension
	 * @return string
	 */
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
			System::get()->getModule()->getLogModule()->error($this->getModuleName(), "file not found: " . $path);
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
		System::get()->getModule()->getPersistenzModule()->delData($this->getModuleNameLower(), $id);
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
		return System::get()->getModule()->getPersistenzModule()->setData($this->getModuleName(), $id, $data);
	}

	public function get($query = array()){
		return System::get()->getModule()->getPersistenzModule()->getData($this->getModuleName(), $query);
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
