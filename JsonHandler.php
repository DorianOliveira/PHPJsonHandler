<?php

namespace JsonHandler;

use ReflectionClass;
class JsonHandler
{
    const HEADER = 'Content-Type: application/json';
	public $arrayData;
	private $mainKey;
	
	public function AddItem($key, $value, $item_key = '')
	{
		
		if($this -> GetMainKey() != '')
		{
			if($item_key === '' || $item_key === null)
			{
				$this -> arrayData[$this -> GetMainKey()][$key] = $value;
			}
			else
			{
				$this -> arrayData[$this -> GetMainKey()][$item_key][$key] = $value;		
			}
				
		}
		else
		{
			if($item_key == '')
				$this -> arrayData[$key] = $value;
			else
				$this -> arrayData[$item_key][$key] = $value;
		}
		
		$this -> CreateDefaultValues();
	}	
	public function GetArray()
	{
		return $this -> arrayData;
	}
    public  function PrintJson()
    {
		header(self :: HEADER);
		echo json_encode($this -> arrayData);
    } 
	public function SetMainKey($key)
	{
		if($key != '' && $key != null)
			$this -> mainKey = $key;
	}
	public function GetMainKey()
	{
		return $this -> mainKey;
	}
	private function CreateDefaultValues()
	{
		$count = count($this -> arrayData[$this -> GetMainKey()]);
		$this -> arrayData['length'] = $count;
	}
	public function CreateFromArray($array, $key = '')
    {
        $result = $array;
		$this -> SetMainKey($key);
		
		$result = array($this -> GetMainKey() => $array);
		$this -> arrayData = $result;
		$this -> mainKey = $key;
		$this -> CreateDefaultValues();
	}
	public function CreateFromObject($obj, $fields=array(), $key = 'result', $optionsMethods = array(), $includeObjectVars = false)
	{
		$this -> SetMainKey($key);
		$array_json = JsonHandler :: CreateArrayProperties($obj, $fields, $optionsMethods, $includeObjectVars);
		
		if($this -> GetMainKey() != '')
			$array_data = array($this -> GetMainKey()  => $array_json);
		else
			$array_data = $array_json;
		$this -> arrayData = $array_data;
		$this -> CreateDefaultValues();
	}
	public function CreateFromListObject($list_item, $fields=array(), $key='result', $optionsMethods = array(), $includeObjectVars = false)
	{
		
		$listJson = $this -> CreateArrayObject($list_item, $fields, $key, $optionsMethods, $includeObjectVars);
		if($this -> GetMainKey() != '' && $this -> GetMainKey() != null)
		{
			$this -> arrayData[$this -> GetMainKey()] = $listJson;
		}
		else {
			$this -> arrayData = $listJson;
		}
		$this -> CreateDefaultValues();
	}
	private function CreateArrayObject($array, $fields=array(), $key='result', $optionsMethods = array(), $includeObjectVars = false)
	{
		$listJson = array();
		$counter_item = 1;
		$this -> SetMainKey($key);
		foreach($array as $item_key => $item_value)
		{
			if(is_array($item_value) && !empty($item_value))
			{
				$listJson[$item_key] = $this -> CreateArrayObject($item_value, $fields, $key, $optionsMethods, $includeObjectVars);
			}
			else if(gettype($item_value) == 'object')
			{
				$listJson[$item_key] = JsonHandler :: CreateArrayProperties($item_value, $fields, $optionsMethods, $includeObjectVars);
			}
			else {
				$listJson[$item_key] = $item_value;
			}
				
		}
		return $listJson;
	}
	private function CreateArrayProperties($obj, $fields = array(), $optionsMethods = array(), $include_object_vars = false)
	{
		
		$object_vars = get_object_vars($obj);
		$reflection = new ReflectionClass($obj);
		$props = $reflection -> getProperties();
		
		$array_json = array();
		if($include_object_vars)
		{
			foreach($object_vars as $variable_key => $variable_value)
			{
				$array_json[$variable_key] = $variable_value;
			}
		}
		
	    foreach($props as $prop)
	    {   
	    	$valid_property = true;
	    	if(is_array($fields) && count($fields) > 0)
	    	{
	    		if(!in_array($prop -> getName(), $fields))
	    			$valid_property = false;
			}
			
	    	if($valid_property)
	    	{
				$prop -> setAccessible(true);
				$type = gettype($prop -> getValue($obj));
				
				$value_object = null;
				if($type == 'object')
					$value_object = $this -> CreateArrayProperties($prop -> getValue($obj), array());
				else
					$value_object = $prop -> getValue($obj);
				
				$array_json[$prop -> getName()] = $value_object;
			}
			
			if(is_array($optionsMethods) && count($optionsMethods) > 0)
			{
				foreach($optionsMethods as $option)
				{
					$method = $option['method'];
					$key = $option['key'];
					$values = $option['values'];
					$method_to_invoke = $reflection -> getMethod($method);
					$method_to_invoke -> invokeArgs($obj, $values);
				}
			}
		}
		
		return $array_json;
		
	}
}