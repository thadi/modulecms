<?php

class PersistenzModule extends AbstractModule{

	public function set($module, $id = false, $data = array()){
		$module = strtolower($module);
		$queryUrl = 'localhost:9200/' . System::get()->projectName() . '/' . $module;
		$method = "POST";
		if(!empty($id)){
			$method = "PUT";
			$queryUrl .= '/' . $id;
		}
		return $this->curl($queryUrl, array(
			"method" => $method,
			"data" => $data,
			"json" => true
		));
	}

	public function get($module, $query = array()){
		if(is_array($module)){
			$module = $this->getModuleName();
		}
		$module = strtolower($module);
		$queryUrl = 'localhost:9200/' . System::get()->projectName() . '/' . $module . '/_search';
		$method = "POST";
		if(empty($query)){
			$method = "GET";
		}
		return $this->curl($queryUrl, array(
			"method" => $method,
			"data" => $query,
			"json" => true
		));
	}

	public function del($module, $id = false){
		$module = strtolower($module);
		$queryUrl = 'localhost:9200/' . System::get()->projectName() . '/' . $module;
		if(!empty($id)){
			$queryUrl .= '/' . $id;
		}
		$method = "DELETE";
		return $this->curl($queryUrl, array(
			"method" => $method,
			"json" => true
		));	
	}

	public function getIdQuery($id){
		return array(
			"query" => array(
				"match" => array(
					"_id" => $id
				)
			)
		);
	}
}


?>