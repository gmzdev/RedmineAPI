<?php

class RedmineAPI{
	//server url
	var $site;
	//remine api key
	var $key;
	//redmine resourcer
	var $resource;
	//request format
	var $request_format = 'xml';

	public function __construct(){
	
	}
	
	public function login(){
		//to be filled out
	}
	
	public function logout(){
		//to be filled out
	}
	
	public function search(){
	
	}
	
	//get projects 
	public function getProjects($data = array()){
		$request = new Curl();
		$resource = 'projects';
		$params = array(
			'key' => $this->key
		);
		
		if(is_array($data)){
			$params = array_merge($data,$params);
		}
		
		$url = $this->site.$resource.'.'.$this->request_format;
		$result = $request->get($url,$params);
		
		if (!$request->error()){
			if ($this->request_format == 'xml'){
			
				return $this->parseXML($result->body, $resource);
				
			}
			if ($this->request_format == 'json'){

				return $this->parseJSON($result->body, $resource);
			}
		}else{
			return $request->error();
		}	
	}
	
	public function getProject($project_name){
		$request = new Curl();
		$resource = 'projects';
		$params = array(
			'key' => $this->key
		);
		
		$url = $this->site.$resource.'/'.$project_name.'.'.$this->request_format;
		$result = $request->get($url,$params);
		if (!$request->error()){
			if ($this->request_format == 'xml'){
			
				//return $this->parseXML($result->body, 'project');
				$xml = simplexml_load_string($result->body);
				return $this->objectsIntoArray($xml);
				
			}
			if ($this->request_format == 'json'){

				return $this->parseJSON($result->body, 'project');
				//$json = json_decode($result->body);
				//return $this->objectsIntoArray($json);
			}
		}else{
			return $request->error();
		}	
	}
	
	public function getIssues($data = array()){
		$request = new Curl();
		$resource = 'issues';
		$params = array(
			'key' => $this->key
		);
		
		if(is_array($data)){
			$params = array_merge($data,$params);
		}
		$url = $this->site.$resource.'.'.$this->request_format;
		$result = $request->get($url,$params);
		if (!$request->error()){
			if ($this->request_format == 'xml'){
			
				//return $this->parseXML($result->body, 'project');
				//$xml = simplexml_load_string($result->body);
				//return $this->objectsIntoArray($xml);
				return $this->parseXML($result->body, $resource);
				
			}
			if ($this->request_format == 'json'){

				return $this->parseJSON($result->body, $resource);
			}
		}else{
			return $request->error();
		}
	}
	
	public function getMyIssues($data = array()){
		$request = new Curl();
		$resource = 'issues';
		$params = array(
			'assigned_to_id' => 'me',
			'key' => $this->key
		);
		
		if(is_array($data)){
			$params = array_merge($data,$params);
		}
		$url = $this->site.$resource.'.'.$this->request_format;
		$result = $request->get($url,$params);
		if (!$request->error()){
			if ($this->request_format == 'xml'){
			
				//return $this->parseXML($result->body, 'project');
				//$xml = simplexml_load_string($result->body);
				//return $this->objectsIntoArray($xml);
				return $this->parseXML($result->body, $resource);
				
			}
			if ($this->request_format == 'json'){

				return $this->parseJSON($result->body, $resource);
			}
		}else{
			return $request->error();
		}
	}
	
	public function getIssue($issue_id){
		$request = new Curl();
		$resource = 'issues';
		$params = array(
			'key' => $this->key
		);
		$url = $this->site.$resource.'/'.$issue_id.$this->request_format;
		$result = $request->get($url,$params);
		if (!$request->error()){
			if ($this->request_format == 'xml'){
			
				//return $this->parseXML($result->body, 'project');
				//$xml = simplexml_load_string($result->body);
				//return $this->objectsIntoArray($xml);
				return $this->parseXML($result->body, 'issue');
				
			}
			if ($this->request_format == 'json'){

				return $this->parseJSON($result->body, 'issue');
			}
		}else{
			return $request->error();
		}
	}
	
		//parse result xml to array
	private function parseXML($result, $resource){
		//$xml = new SimpleXMLElement($result);
		$xml = simplexml_load_string($result);
		if ($xml->getName() == $resource) {
			// multiple
			$result = array ();
			foreach ($xml->children() as $child) {
				foreach ((array) $child as $k => $v) {
					$k = str_replace ('-', '_', $k);
					if (is_object($v) || is_array($v)) {
				    	$v = $this->objectsIntoArray($v); // recursive call
					}
					if (isset ($v['nil']) && $v['nil'] == 'true') {
						continue;
					} else {
						$res[$k] = $v;
					}
				}
				$result[] = $res;
			}
			return $result;
		} elseif ($xml->getName() == 'errors') {
			// parse error message
			$this->error = $xml->error;
			$this->errno = $this->response_code;
			return false;
		}
	}
	
	//parse object to array
	private function objectsIntoArray($arrObjData, $arrSkipIndices = array())
	{
		 $arrData = array();
		
		 // if input is object, convert into array
		 if (is_object($arrObjData)) {
		     $arrObjData = get_object_vars($arrObjData);
		 }
		
		 if (is_array($arrObjData)) {
		     foreach ($arrObjData as $index => $value) {
		         if (is_object($value) || is_array($value)) {
		             $value = $this->objectsIntoArray($value, $arrSkipIndices); // recursive call
		         }
		         if (in_array($index, $arrSkipIndices)) {
		             continue;
		         }
		         $arrData[$index] = $value;
		     }
		 }
		 return $arrData;
	}
	
	//parse json to array
	private function parseJSON($result, $resource){
		$json = json_decode($result, true);
		
		$result = array();
		foreach($json[$resource] as $p => $r){
			if(is_array($r)){
				foreach ($r as $k => $v) {
						$k = str_replace ('-', '_', $k);
						if (isset ($v['nil']) && $v['nil'] == 'true') {
							continue;
						} else {
							$res[$k] = $v;
						}
						$result[$p] = $res;
				}
			}else{
				$result[$p] = $r;
				continue;
			}
			
		}
		
		return $result;
		//return $json;
	
	}
	
	
	//curl functions
	private function request(){
	
	}
	
	private function response(){
	
	}

}

?>
