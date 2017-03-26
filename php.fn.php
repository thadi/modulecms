<?php

if(!function_exists("is_json")){
	function is_json($str){
		return json_decode($str) != null;
	}
}

?>