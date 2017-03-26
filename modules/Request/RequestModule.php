<?php

class RequestModule extends AbstractModule{

	protected $calledModule = null;
	protected $calledMethod = null;
	protected $calledArguments = null;

	public function handleRequest($path){
		$this->calledModule = false;
		$this->calledMethod = false;
		$this->calledArguments = array();
		if(count($path > 0) && !empty($path[0])){
			$this->calledModule = $path[0];
			$this->calledMethod = "index";
			if(count($path) > 1 && !empty($path[1])){
				$this->calledMethod = $path[1];
				if(count($path) > 2 && !empty($path[2])){
					$this->calledArguments = array_slice($path, 2);
				}
			}
			$module = System::get()->getModule($this->calledModule);
			return $module->controller($this->calledMethod, $this->calledArguments);
		}else{
			return $this->renderHtml("404");
		}
	}

	public function getHost(){
		return $_SERVER['SERVER_NAME'];
	}

	public function getProtocol(){
		return parse_url($_SERVER['REQUEST_URI'], PHP_URL_SCHEME);
	}

	public function getPath(){
		return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
	}

	public function getUri($withParams = true){
		if(!empty($withParams)){
			return $_SERVER['REQUEST_URI'];
		}else{
			return explode("?", $_SERVER['REQUEST_URI'])[0];
		}
	}

	public function curl($url, $options = array()){
		$defaultOptions = array(
			"method" => "GET",
			"data" => false,
			"json" => false
		);

		$options = array_merge($defaultOptions, $options);

		$c = curl_init();
		curl_setopt($c, CURLOPT_URL, $url);
		curl_setopt($c, CURLOPT_CUSTOMREQUEST, $options["method"]);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		if(!empty($options['data'])){
			if(!empty($options['json'])){
				$data = json_encode($options['data']);
			}else{
				$data = (is_array($options['data'])) ? http_build_query($options['data']) : $options['data'];	
			}
			curl_setopt($c, CURLOPT_POSTFIELDS, $data);
		}
		$out = curl_exec($c);
		curl_close($c);
		if(!empty($options['json'])){
			$out = json_decode($out, true);
		}
		return $out;
	}

	public function getParameter($key){
		if(!empty($_REQUEST[$key])){
			return $_REQUEST[$key];
		}
		return false;
	}

	public function forward($uri){
		header('Location: ' . $uri);
	}

	public function renderRequestData(){
		$this->data['moduleName'] = $this->calledModule;
		$this->data['methodName'] = $this->calledMethod;
		$this->data['arguments'] = $this->calledArguments;
		return $this->renderHtml("requestData");
	}
}

?>