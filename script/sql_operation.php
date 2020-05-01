<?php


/***/
function SqlIsValidValueFromHeader($colName, $colType)
{
	if($colType == "tinyint(1)") //------------------------------------------- BOOLEAN
		return TRUE;
	else if(strpos($colType, "set") !== FALSE) //----------------------------- SET
		return TRUE;	
	else if($colType == "point") //--------------------------------------- POINT
	{
		$x = GetVar($colName."_x", false);
		$y = GetVar($colName."_y", false);
		return $x !== false;
	}		
	
	return GetVar($colName);
}

/***/
function SqlGetEmptyValueFromType($colType)
{
	if($colType == "point") //-------------------------------------------- POINT
		$value = "GeomFromText(NULL)";
	else if($colType == "tinyint(1)") //---------------------------------- BOOLEAN
		$value = "'0'";
	else
		$value = "NULL";
	
	return $value;
}

/***/
function SqlGetValueFromHeader($colName, $colType)
{
	if(!GetVar($colName, false))
	{
		$value =  SqlGetEmptyValueFromType($colType);
	}
	else
	{
		if(strpos($colType, "set") !== FALSE) //------------------------------- SET
		{
			$tmp = str_replace("set(", "", $colType);
			$tmp = str_replace("'", "", $tmp);
			$tmp = str_replace(")", "", $tmp);
			$options = explode(",", $tmp);
			$value = "'";
			foreach($options as $opt)
			{
				$opt_val = GetVar($colName."_".$opt);
				if($opt_val)
				{
					if($value != "'")
						$value .= ",";
					$value .= $opt;
				}
			}
			$value .= "'";
		}
		else if($colType == "tinyint(1)") //----------------------------------- BOOLEAN
		{
			$value = (GetVar($colName) == "on") ? "'1'" : "'0'";
		}
		else if($colType == "point") //---------------------------------------- POINT
		{
			$x = GetVar($colName."_x");
			$y = GetVar($colName."_y");
			$value = "GeomFromText('POINT($x $y)',0)";
		}
		else if($colType == "longblob") //------------------------------------- IMAGE FILE
		{
			$imageType = $_FILES[$colName]["type"];
			$imageBase64 = base64_encode(file_get_contents($_FILES[$colName]['tmp_name']));
			$value = "'data:$imageType;base64,$imageBase64'";	
		}
		else if($colType == "char(33)") //------------------------------------- PASSWORD
		{
			$value = "'".md5(GetVar($colName))."'";
		}	
		else //---------------------------------------------------------------- DEFAULT
		{
			$value = "'".addslashes(GetVar($colName))."'";
		}
	}

	return $value;
}


/** */
function SqlUpdateEntryFromHeader($baseTable)
{
	global $g_SQLTablePrefix;
	
	// Get columns information
	$result = SqlQuery("SHOW FIELDS FROM `$g_SQLTablePrefix$baseTable`");
	if(!$result)
		return "Error! Can't build <table> from $baseTable";
	$header = SqlFetchAllResult($result, MYSQLI_ASSOC);
	//var_dump($header);

	// Build query string
	$id = GetVar("id");
	$query = "UPDATE `$g_SQLTablePrefix$baseTable` SET ";
	
	$paramNum = 0;
	foreach($header as $col_idx => $col_val)
	{
		if($col_idx > 0)
		{
			$colName = $col_val["Field"];
			$colType = $col_val["Type"];

			if(SqlIsValidValueFromHeader($colName, $colType))
			{
				$value = SqlGetValueFromHeader($colName, $colType);
				if($paramNum > 0)
					$query .= ", ";
				$query .= "`$colName` = $value";
				$paramNum++;
			}
		}
	}

	$query .= " WHERE `id` = '$id';";
	$result = SqlQuery($query);	
}

/**
 * Insert an entry into SQL table from HTTP Header.
 *
 * @param	$baseTable	The table where to insert entry
 */
function SqlInsertEntryFromHeader($baseTable)
{
	global $g_SQLTablePrefix;
	
	// Get columns information
	$result = SqlQuery("SHOW FIELDS FROM `$g_SQLTablePrefix$baseTable`");
	if(!$result)
		return "Error! Can't build <table> from $baseTable";
	$header = SqlFetchAllResult($result, MYSQLI_ASSOC);
	//var_dump($header);

	// Build query string
	$query = "INSERT INTO `$g_SQLTablePrefix$baseTable` (";
	
	foreach($header as $col_idx => $col_val)
	{
		if($col_idx > 0)
		{
			$colName = $col_val["Field"];
			$query .= "`$colName`";
			if($col_idx != count($header) - 1)
				$query .= ", ";
		}
	}
	$query .= ") VALUES (";
	
	foreach($header as $col_idx => $col_val)
	{
		if($col_idx > 0)
		{
			$colName = $col_val["Field"];
			$colType = $col_val["Type"];
			
			$query .= SqlGetValueFromHeader($colName, $colType);
			if($col_idx != count($header) - 1)
				$query .= ", ";
		}
	}

	$query .= ");";
	$result = SqlQuery($query);	
}

/***/
function ImportFieldData($inValue, $colName, $colType)
{
	if(!$inValue)
	{
		$value =  SqlGetEmptyValueFromType($colType);
	}
	else
	{
		$value =  SqlGetEmptyValueFromType($colType);
		if(strpos($colType, "enum") !== false) //----------------------------- ENUM
		{
			$tmp = str_replace("enum(", "", $colType);
			$tmp = str_replace("'", "", $tmp);
			$tmp = str_replace(")", "", $tmp);
			$options = explode(",", $tmp);
			foreach($options as $opt)
			{
				if(stripos($inValue, $opt) !== false)
				{
					$value = $opt;
					break;
				}
			}
		}
		else if(strpos($colType, "set") !== false) //-------------------------- SET
		{
			$tmp = str_replace("set(", "", $colType);
			$tmp = str_replace("'", "", $tmp);
			$tmp = str_replace(")", "", $tmp);
			$options = explode(",", $tmp);
			$found = 0;
			foreach($options as $opt)
			{
				if(stripos($inValue, $opt) !== false)
				{
					if($found == 0)
						$value .= "'";
					else
						$value .= ",";
					$value .= $opt;
					$found++;
				}
			}
			if($found > 0)
				$value .= "'";
		}
		else if($colType == "tinyint(1)") //----------------------------------- BOOLEAN
		{
			$value = $inValue ? "'1'" : "'0'";
		}
		else if($colType == "point") //---------------------------------------- POINT
		{
			$num = ExtractNumbers($inValue);
			if(is_array($num) && is_array($num[0]))
			{
				$x = $num[0][0];
				if(isset($num[0][1]))
					$y = $num[0][1];
				else 
					$y = $x;
				$value = "GeomFromText('POINT($x $y)',0)";
			}
		}	
		else if($colType == "date") //----------------------------------------- DATE
		{
			
		}
		else if($colType == "datetime") //------------------------------------- DATETIME
		{
			
		}
		else //---------------------------------------------------------------- DEFAULT
		{
			$value = "'".addslashes($inValue)."'";
		}
	}

	return $value;	
}

/**
 * Insert entries into SQL table from CSV array.
 *
 * @param	$baseTable	The table where to insert entry
 */
function SqlInsertEntriesFromHeader($baseTable)
{
	global $g_SQLTablePrefix;
	
	$csvArray = unserialize(base64_decode(GetVar("csv_array")));
	AddLog("SqlInsertEntriesFromHeader");
	AddLog(print_r($csvArray, true));
	
	// Get columns information
	$result = SqlQuery("SHOW FIELDS FROM `$g_SQLTablePrefix$baseTable`");
	if(!$result)
		return "Error! Can't build <table> from $baseTable";
	$header = SqlFetchAllResult($result, MYSQLI_ASSOC);
	//var_dump($header);

	// Save matching table to Cookie
	$matchTable = array();
	foreach($header as $colVal)
	{
		$colName = $colVal["Field"];
		$matchTable[$colName] = GetVar($colName);
	}
	SaveCookie("csv_match_$baseTable", base64_encode(serialize($matchTable)));

	foreach($csvArray as $csvRow)
	{
		// Build query string
		$query = "INSERT INTO `$g_SQLTablePrefix$baseTable` (";
		
		foreach($header as $colIdx => $colVal)
		{
			if($colIdx > 0)
			{
				$colName = $colVal["Field"];
				if($colIdx > 1)
					$query .= ", ";
				$query .= "`$colName`";
			}
		}
		$query .= ") VALUES (";
		
		foreach($header as $colIdx => $colVal)
		{
			if($colIdx > 0)
			{
				$colName = $colVal["Field"];
				$colType = $colVal["Type"];
				
				if($colIdx > 1)
					$query .= ", ";


				
				$match = GetVar($colName);
				if($match != "empty" && isset($csvRow[$match]))
					$query .= ImportFieldData($csvRow[$match], $colName, $colType);
				else
					$query .= SqlGetEmptyValueFromType($colType);
			}
		}

		$query .= ");";
		$result = SqlQuery($query);
	}
}

?>