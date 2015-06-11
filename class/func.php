<?
	require_once "error.php";
	global $DB; 
	if (!isset($DB))
		$DB = DataBase::getDB();
	
	function where($where, $field, $operator = "", $value = "")
	{
		$result = $where;
		
		if (($operator == "") and ($value == ""))
		{	
			if ($field != "(")
			{
				$result["query"] .= " ".$field." ";
				return $result;
			}
		}
		
		
		if (isset($where["query"]))
			$str = substr($where["query"], -6);
		
		if (isset($str))
			if (strpos($str, "{?}"))
			{
				$result["query"] .= " AND ";
			}
		
		if (($field == "(") or ($field == ")") )
		{
			$result["query"] .= $field;
			return $result;
		}
		
		if (!strpos($field, "("))
			$field = "`".$field."`";
		
		if (!isset($result["query"]))
			$result["query"] = "";
			
		$result["query"] .= $field." ".$operator." ";
		
		if ($operator == "IN")
		{
			$result["query"] .= "(";
			foreach ($value as $val)
			{
				$result["query"] .= "{?},";
				$result["params"][] = $val;
			}
			$result["query"] = substr($result["query"], 0, -1);
			$result["query"] .= ") ";
		}
		else
		{
			$result["query"] .= "{?}";
			$result["params"][] = $value;
		}
		//print_r($where);
		//echo substr($where["query"], -4);
		return $result;
	}
	
	function getQueryString($get, $arr_name = false)
	{
		$txt = "";
		foreach ($get as $key => $val)
		{
			if ($txt != "")
				$txt .= "&";
			
			if (($arr_name == false) or (is_array($val)) )
			{
				if (!is_array($val))
					$txt .= $key."=".$val;
				else
					$txt .= getQueryString($val, $key);
			}
			else
			{
				$txt .= $arr_name."%5B".$key."%5D=".$val;
			}
			
		}
		return $txt;
	}
	
	function setError($number)
	{
		global $error;
		
		$r["status"] = "error";
		$r["error_code"] = $number;
		$r["error"] = $error[$number];
		
		return $r;
	}
	
?>