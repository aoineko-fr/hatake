<?php

/***/
function SqlDisplayTableValue($table, $cell_val, $col_idx, $col_name, $col_type, $editable = FALSE, $parentTable = NULL, $parentId = -1)
{
	global $g_SQLTableList, $g_SQLTablePrefix, $g_AdminOnlyField, $g_UserAdmin;
	
	$html = "";
	
	//=============================================================== EDIT
	if($editable)
	{
		$readyOnly = (!$g_UserAdmin && in_array("$table.$col_name", $g_AdminOnlyField)) ? "disabled" : "";
		
		if($col_idx == "0") //------------------------------------------------- ID
		{
			$html = "<span class='grayed'>$cell_val</span>";				
		}
		else if($col_name == $parentTable) //---------------------------------- PARENT TABLE INDEX
		{
			$result = SqlQuery("SELECT `id`,`name` FROM `$g_SQLTablePrefix$col_name`");
			$fetchResult = SqlFetchAllResult($result, MYSQLI_ASSOC);	
			$html = "<select class='edit grayed' name='$col_name' id='$col_name' style='width:100%;' readonly >";
			foreach($fetchResult as $opt)
			{
				if($parentId == $opt['id'])
					$html .= "<option value='$parentId' selected>${opt['name']}</option>";
			}
			$html .= "</select>";
		}
		else if(in_array($col_name, $g_SQLTableList)) //----------------------- TABLE INDEX
		{
			$result = SqlQuery("SELECT `id`,`name` FROM `$g_SQLTablePrefix$col_name`");
			$fetchResult = SqlFetchAllResult($result, MYSQLI_ASSOC);	
			$html = "<select class='edit' name='$col_name' id='$col_name' style='width:100%;' $readyOnly >";
			foreach($fetchResult as $opt)
			{
				$id = $opt['id'];
				$name = $opt['name'];
				$selected = ($id == $cell_val) ? "selected " : "";
				$html .= "<option value='$id' $selected>$name</option>";
			}
			$html .= "</select>";
		}
		else if(strpos($col_type, "enum") !== FALSE) //------------------------ ENUM
		{
			$tmp = str_replace("enum(", "", $col_type);
			$tmp = str_replace("'", "", $tmp);
			$tmp = str_replace(")", "", $tmp);
			$options = explode(",", $tmp);
			$html = "<select class='edit' name='$col_name' id='$col_name' style='width:100%;' $readyOnly >";
			foreach($options as $opt)
			{
				$select = ($opt == $cell_val) ? "selected " : "";
				$html .= "<option value='$opt' $select>".Loc("$table.$col_name.$opt")."</option>";
			}
			$html .= "</select>";
		}
		else if(strpos($col_type, "set") !== FALSE) //------------------------- SET
		{
			$tmp = str_replace("set(", "", $col_type);
			$tmp = str_replace("'", "", $tmp);
			$tmp = str_replace(")", "", $tmp);
			$options = explode(",", $tmp);
			$html = "<div style='diplay:flex; flex-flow:column; column-count:2;' />";
			foreach($options as $opt)
			{
				$selected = (strpos($cell_val, $opt) !== FALSE) ? "checked" : "";
				$html .= "<div><input type='checkbox' name='$col_name.$opt' id='$col_name.$opt' $selected $readyOnly />";
				$html .= "<label for='$col_name.$opt'>".Loc("$table.$col_name.$opt")."</label></div>";
			}
			$html .= "</div>";
		}
		else if($col_type == "tinyint(1)") //---------------------------------- BOOLEAN
		{
			$checked = ($cell_val == "1") ? "checked" : "";
			$html = "<input type='checkbox' name='$col_name' id='$col_name' $checked $readyOnly />";
		}
		else if($col_type == "point") //--------------------------------------- POINT
		{
			$x = 0;
			$y = 0;
			if($cell_val)
			{
				$res = unpack("lSRID/CByteOrder/lTypeInfo/dX/dY", $cell_val);
				$x = $res['X'];
				$y = $res['Y'];
			}
			$html  = "<input class='edit' type='number' step='0.01' name='$col_name.x' id='$col_name.x' value='$x' style='width:5em;' $readyOnly />&nbsp;•&nbsp;";
			$html .= "<input class='edit' type='number' step='0.01' name='$col_name.y' id='$col_name.y' value='$y' style='width:5em;' $readyOnly />";
		}
		else if($col_type == "char(7)") //------------------------------------- COLOR
		{
			$html = "<input class='edit' type='color' name='$col_name' id='$col_name' value='$cell_val' $readyOnly />";
		}
		else if($col_type == "longblob") //------------------------------------ IMAGE FILE
		{
			$html = "<div><input class='edit' type='file' name='$col_name' id='$col_name' accept='image/*' $readyOnly /><br/>";
			if($cell_val)
				$html .= "<img src='$cell_val' style='height:256px;' />";
			$html .= "</div>";
		}
		else if($col_type == "text") //---------------------------------------- TEXT
		{
			$html = "<textarea class='edit' name='$col_name' id='$col_name' rows='8' cols='50' dir='ltr' $readyOnly />$cell_val</textarea>";
		}
		else if($col_type == "float") //--------------------------------------- FLOAT
		{
			$html = "<input class='edit' type='number' step='0.01' name='$col_name' id='$col_name' value='$cell_val' style='width:5em;' $readyOnly />";
		}
		else if(strpos($col_type, "int") !== FALSE) //------------------------- INTEGER
		{
			$html = "<input class='edit' type='number' name='$col_name' id='$col_name' value='$cell_val' style='width:5em;' $readyOnly />";
		}
		else if($col_type == "date") //---------------------------------------- DATE
		{
			$html = "<input class='edit' type='date' name='$col_name' id='$col_name' value='$cell_val' style='width:100%;' $readyOnly />";			
		}
		else if($col_type == "datetime") //------------------------------------ DATETIME
		{
			$html = "<input class='edit' type='date' name='$col_name' id='$col_name' value='".substr($cell_val, 0, 10)."' style='width:100%;' $readyOnly />";			
		}
		else if($col_type == "char(33)") //------------------------------------ PASSWORD
		{
			$html = "<input class='edit' type='password' name='$col_name' id='$col_name' value='' style='width:100%;' $readyOnly />";
		}			
		else //---------------------------------------------------------------- DEFAULT
		{
			$html = "<input class='edit' type='text' name='$col_name' id='$col_name' value='$cell_val' style='width:100%;' $readyOnly />";
		}
	}
	//=============================================================== VIEW
	else if($cell_val)
	{
		if($col_idx == "0") //------------------------------------------------- ID
		{
			$html = "<span class='grayed'>$cell_val</span>";				
		}
		else if(in_array($col_name, $g_SQLTableList)) //----------------------- TABLE INDEX
		{
			$result = SqlQuery("SELECT `name` FROM `$g_SQLTablePrefix$col_name` WHERE `id` = '$cell_val'");
			$fetchResult = SqlFetchAllResult($result, MYSQLI_ASSOC);
			$html = RemoveBreak($fetchResult[0]['name']);
		}
		else if($col_type == "char(7)") //------------------------------------- COLOR
		{
			$html = "<div class='colorbox' style='background:$cell_val;'></div>";
		}
		else if($col_type == "point") //--------------------------------------- POINT
		{
			$res = unpack("lSRID/CByteOrder/lTypeInfo/dX/dY", $cell_val);
			if($res['X'] == $res['Y'])
				$html = $res['X'];
			else
				$html = $res['X'] ."&nbsp;•&nbsp;". $res['Y'];
		}
		else if(strpos($col_type, "enum") !== FALSE) //------------------------ ENUM
		{
			$html = RemoveBreak(Loc("$table.$col_name.$cell_val"));
		}
		else if(strpos($col_type, "set") !== FALSE) //------------------------- SET
		{
			$options = explode(",", $cell_val);
			$html = "";
			foreach($options as $opt_idx => $opt_val)
			{
				if($opt_idx > 0)
					$html .= ",&nbsp;";
				$html .= RemoveBreak(Loc("$table.$col_name.$opt_val"));
			}
		}
		else if($col_type == "date") //---------------------------------------- DATE
		{
			$date = explode("-", $cell_val); // Y-m-d
			$html = RemoveBreak(utf8_encode(strftime(Loc("date.format.short"), mktime(0, 0, 0, $date[1], $date[2], $date[0]))));
		}
		else if($col_type == "datetime") //------------------------------------ DATETIME
		{
			$date = explode("-", substr($cell_val, 0, 10)); // Y-m-d H:M:S
			$html = RemoveBreak(utf8_encode(strftime(Loc("date.format.med"), mktime(0, 0, 0, $date[1], $date[2], $date[0]))));
		}
		else if($col_type == "char(33)") //------------------------------------ PASSWORD
		{
			$html = "•••••";
		}
		else if($col_type == "longblob") //------------------------------------ IMAGE FILE
		{
			$html = "<img src='$cell_val' style='height:64px;' />";
		}
		else if($col_type == "text") //---------------------------------------- TEXT
		{
			$html = "<div style='min-width:256px;'>$cell_val</div>";
		}
		//--------------------------------------------------------------------- DEFAULT
		else
		{
			$html = RemoveBreak($cell_val);
		}
	}
	return $html;
}

/** Build HTML table from a SQL table */
function SqlDisplayTable($baseTable, $editable = TRUE, $subTable = NULL, $subID = "")
{
	global $g_SQL, $page, $g_AdminOnlyTable, $g_UserAdmin;

	if(!$g_UserAdmin && in_array($baseTable, $g_AdminOnlyTable))
		$editable = false;
	
	AddLog("DisplaySQLTable for table: $baseTable");
	$html = "";
	$html .= "<table class='sql' border='0' cellpadding='0' cellspacing='0' >\n";
	
	// Get table header information
	$header = SqlGetTableHeader($baseTable);
	$subHeader = isset($subTable) ? SqlGetTableHeader($subTable) : NULL;

	// Display Table Header
	{
		$rowSpan = isset($subTable) ? 2 : 1;
		$html .= "\t<tr class='header'>\n";
		if($editable)
		{
			$html .= "\t\t<th rowspan='$rowSpan'></th>\n";
		}
		foreach($header as $col_idx => $col_val)
		{
			$colName = $col_val["Field"];
			$class = ($col_idx == 0) ? "dev grayed" : "";
			$help = LocExists("$baseTable.$colName.info") ? "title='". Loc("$baseTable.$colName.info") ."' style='cursor:help;'" : "";
			$html .= "\t\t<th class='$class' rowspan='$rowSpan' $help>". Loc("$baseTable.$colName") ."</th>\n";
		}
		if(isset($subHeader))
		{
			$help = LocExists("$baseTable.$subTable.info") ? "title='". Loc("$baseTable.$subTable.info") ."' style='cursor:help;'" : "";
			$html .= "\t\t<th colspan='". intval(count($subHeader) + 1) ."' $help>". Loc("$baseTable.$subTable") ."</th>\n";
			$html .= "\t</tr><tr class='sub-header'>\n";
			if($editable)
			{
				$html .= "\t\t<th></th>\n";
			}
			foreach($subHeader as $col_idx => $col_val)
			{
				$subColName = $subHeader[$col_idx]["Field"];
				$class = (($col_idx == 0) || ($subColName == $baseTable)) ? "dev grayed" : "";
				$help = LocExists("$subTable.$subColName.info") ? "title='". Loc("$subTable.$subColName.info") ."' style='cursor:help;'" : "";
				$html .= "\t\t<th class='$class' $help>". Loc("$subTable.$subColName") ."</th>\n";
			}
		}
		$html .= "\t</tr>\n";
	}

	// Get table content
	$content = SqlGetTableContent($baseTable);
	
	// Display Table content
	foreach($content as $row_idx => $row_tab)
	{
		$rowClass = (($row_idx % 2) == 0) ? "even" : "odd";
		$html .= "\t<tr class='$rowClass'>\n";
		
		$subContent = isset($subTable) ? SqlGetTableContent($subTable, $subID, $row_tab[0]) : NULL;		
		$rowSpan = 1;
		if(isset($subContent))
		{
			$rowSpan = max(count($subContent)+1, 1);
			if($editable)
				$rowSpan++;
		}
		
		if($editable)
		{
			$html .= "\t\t<td rowspan='$rowSpan'><div style='display:flex; flex-flow:row; width:100%;'>\n"
			      .  "<a href='?page=$page&table=$baseTable&action=edit&id=${row_tab[0]}'><img src='pub/img/interface/black-nib_2712.png' style='height:16px;' /></a>&nbsp;\n"
				  .  "<a href='?page=$page&table=$baseTable&task=do_remove&id=${row_tab[0]}' onclick='return confirm(\"". Loc("system.confirm") ."\");' >"
				  .  "<img src='pub/img/interface/cross-mark_274c.png' style='height:16px;' /></a>"
				  .  "</div></td>\n";
		}
		foreach($row_tab as $col_idx => $value)
		{
			$col_name    = $header[$col_idx]["Field"];
			$col_type    = $header[$col_idx]["Type"];
			$col_default = $header[$col_idx]["Default"];
			
			$class = $col_idx == 0 ? "dev grayed" : "";			
			$html .= "\t\t<td class='$class' rowspan='$rowSpan'>";
			$html .= SqlDisplayTableValue($baseTable, ($value != NULL) ? $value : $col_default, $col_idx, $col_name, $col_type, FALSE);
			$html .= "</td>\n";
		}
		// Sub-tables
		if(isset($subContent))
		{
			foreach($subContent as $subRowIdx => $subRow)
			{
				$html .= "\t<tr class='$rowClass'>\n";
				if($editable)
				{
					$html .= "\t\t<td><div style='display:flex; flex-flow:row; width:100%;'>"
					        ."<a href='?page=$page&table=$subTable&parent=$baseTable&parentid=${row_tab[0]}&action=edit&id=${subRow[0]}'><img src='pub/img/interface/black-nib_2712.png' style='height:16px;' /></a>&nbsp;"
					        ."<a href='?page=$page&table=$subTable&task=do_remove&id=${subRow[0]}' onclick='return confirm(\"". Loc("system.confirm") ."\");' ><img src='pub/img/interface/cross-mark_274c.png' style='height:16px;' /></a>"
					        ."</div></td>\n";
				}
				foreach($subRow as $subColIdx => $subValue)
				{
					$subColName    = $subHeader[$subColIdx]["Field"];
					$subColType    = $subHeader[$subColIdx]["Type"];
					$subColDefault = $subHeader[$subColIdx]["Default"];
					
					$class = (($subColIdx == 0) || ($subColName == $baseTable)) ? "dev grayed" : "";
					$html .= "\t\t<td class='$class'>";
					$html .= SqlDisplayTableValue($subTable, ($subValue != NULL) ? $subValue : $subColDefault, $subColIdx, $subColName, $subColType, FALSE);
					$html .= "</td>\n";
				}
				$html .= "\t</tr>\n";
			}
			if($editable)
			{
				$html .= "\t<tr class='$rowClass'>\n";
				$html .= "\t\t<td><a href='?page=$page&table=$subTable&parent=$baseTable&parentid=${row_tab[0]}&action=add'><img src='pub/img/interface/heavy-plus-sign_2795.png' style='height:16px;' /></a></td>";
				foreach($subHeader as $subColIdx => $subValue)
				{
					$subColName = $subHeader[$subColIdx]["Field"];
					$class = (($subColIdx == 0) || ($subColName == $baseTable)) ? "dev grayed" : "";
					$html .= "\t\t<td class='$class'>...</td>\n";
				}
				$html .= "\t</tr>\n";
			}
			else
			{
				foreach($subHeader as $subColIdx => $subValue)
				{
					$subColName = $subHeader[$subColIdx]["Field"];
					$class = (($subColIdx == 0) || ($subColName == $baseTable)) ? "dev grayed" : "";
					$html .= "\t\t<td class='$class'></td>\n";
				}
			}
		}


		$html .= "\t</tr>\n";
	}
	
	if($editable)
	{
		if(!isset($row_idx))
			$row_idx = 0;
		$rowClass = ((($row_idx + 1) % 2) == 0) ? "even" : "odd";
		$html .= "\t<tr class='$rowClass'>\n";
		$html .= "\t\t<td><a href='?page=$page&table=$baseTable&action=add'><img src='pub/img/interface/heavy-plus-sign_2795.png' style='height:16px;' /></a></td>";
		foreach($header as $col_idx => $col_val)
		{
			$class = ($col_idx == 0) ? "dev grayed" : "";
			$html .= "\t\t<td class='$class'>...</td>\n";
		}
		if(isset($subContent))
		{
			$html .= "\t\t<td>...</td>\n";
			foreach($subHeader as $subColIdx => $subValue)
			{
				$subColName = $subHeader[$subColIdx]["Field"];
				$class = (($subColIdx == 0) || ($subColName == $baseTable)) ? "dev grayed" : "";
				$html .= "\t\t<td class='$class'>...</td>\n";
			}
		}

		$html .= "\t</tr>\n";
	}
	
	$html .= "</table>\n";

	return $html;
}


/** Build HTML table from a SQL table entry */
function SqlDisplayEntry($baseTable, $id = -1, $editable = TRUE, $page = "", $parentTable = NULL, $parentId = -1)
{
	global $g_SQL, $g_SQLTablePrefix, $page, $g_AdminOnlyTable, $g_UserAdmin;

	if(!$g_UserAdmin && in_array($baseTable, $g_AdminOnlyTable))
		$editable = false;

	$task = ($id == -1) ? "do_add" : "do_edit";
	
	AddLog("DisplaySQLTable for table: $baseTable @ $id");
	$html = "<!- Generated code ->\n";
	
	// Get columns information
	$header = SqlGetTableHeader($baseTable);
	
	// Get current date for update
	$row_tab;
	if($id >= 0)
	{
		$result = SqlQuery("SELECT * FROM `$g_SQLTablePrefix$baseTable` WHERE `id` = '$id'");
		if(!$result)
			return "Error! Can't build <table> from $baseTable";
		$row_tab = SqlFetchAllResult($result, MYSQLI_NUM);
		//var_dump($row_tab);
	}

	if($editable)
	{
		$html .= "<form name='$task' action='index.php' method='post' enctype='multipart/form-data'>\n";
		$html .= "\t<input type='hidden' name='page' value='$page' />\n";
		$html .= "\t<input type='hidden' name='table' value='$baseTable' />\n";
		$html .= "\t<input type='hidden' name='task' value='$task' />\n";
		$html .= "\t<input type='hidden' name='id' value='$id' />\n";
	}
	
	$html .= "\t<table class='sql' border='0' cellpadding='0' cellspacing='0'>\n";
	
	foreach($header as $col_idx => $col_val)
	{
		$col_name    = $col_val["Field"];
		$col_type    = $col_val["Type"];
		$col_default = $col_val["Default"];
		$cell_val = ($id >= 0) ? $cell_val = $row_tab[0][$col_idx] : "";
		
		// Row fields
		$rowClass = (($col_idx % 2) == 0) ? "even" : "odd";
		$html .= "\t\t<tr class='$rowClass'>\n";
		$help = LocExists("$baseTable.$col_name.info") ? "title='". Loc("$baseTable.$col_name.info") ."' style='cursor:help;'" : "";
		$html .= "\t\t\t<td $help>". Loc("$baseTable.$col_name") ."</td>\n";
		// Row values
		$html .= "\t\t\t<td>". SqlDisplayTableValue($baseTable, ($cell_val != NULL) ? $cell_val : $col_default, $col_idx, $col_name, $col_type, $editable, $parentTable, $parentId) ."</td>\n";
		$html .= "\t\t</tr>\n";
	}

	$html .= "\t</table>\n";

	if($editable)
	{
		$html .= "\t<div class='save' style='display:flex; flex-flow:row;'>\n";
		$html .= "\t\t<a href='index.php?page=$page'><img src='pub/img/interface/negative-squared-cross-mark_274e.png' style='height:24px;' /></a>\n";
		$html .= "\t\t<input type='image' src='pub/img/interface/white-heavy-check-mark_2705.png' alt='Submit' style='height:24px;' >\n";
		$html .= "\t</div>\n";
		$html .= "</form>\n";
	}

	return $html;
}

/**
 * Display matching table between table and csv file header
 *
 * @param  $baseTable	The destination SQL table
 * @param  $csvArray	The source CSV array
 *
 * @return				The resulting HTML code
 */
function SqlDisplayFieldMatch($baseTable, $csvArray)
{
	global $g_SQL, $g_SQLTablePrefix, $page, $g_AdminOnlyTable, $g_UserAdmin;

	AddLog("DisplayFieldMatch for table: $baseTable");

	$matchData = GetVar("csv_match_$baseTable");
	$matchArray = unserialize(base64_decode($matchData));
	AddLog("Maching array from cookie:\n". print_r($matchArray, true));

	$html = "<!- Generated code ->\n";
	$html .= "<form name='do_add_csv' action='index.php' method='post' enctype='multipart/form-data'>\n";
	$html .= "\t<input type='hidden' name='page' value='$page' />\n";
	$html .= "\t<input type='hidden' name='table' value='$baseTable' />\n";
	$html .= "\t<input type='hidden' name='task' value='do_add_csv' />\n";
	$html .= "\t<input type='hidden' name='csv_array' value='". base64_encode(serialize($csvArray)) ."' />\n";
	
	// Get columns information
	$header = SqlGetTableHeader($baseTable);
	
	$html .= "\t<table class='sql' border='0' cellpadding='0' cellspacing='0'>\n";
	
	foreach($header as $colIdx => $colVal)
	{
		$colName = $colVal["Field"];
		
		// Row fields
		$rowClass = (($colIdx % 2) == 0) ? "even" : "odd";
		$html .= "\t\t<tr class='$rowClass'>\n";
		$help = LocExists("$baseTable.$colName.info") ? "title='". Loc("$baseTable.$colName.info") ."' style='cursor:help;'" : "";
		$html .= "\t\t\t<td $help>". Loc("$baseTable.$colName") ."</td>\n";
		
		// Row values
		$html .= "\t\t\t<td><select class='edit' name='$colName' id='$colName' style='width:100%;' >";
		$html .= "\t\t\t\t<option value='empty'></option>";
		if(isset($csvArray[0]))
		{
			foreach($csvArray[0] as $key => $val)
			{
				$selected = (is_array($matchArray) && isset($matchArray[$colName]) && ($matchArray[$colName] == $key)) ? "selected" : "";
				$html .= "\t\t\t\t<option value='$key' $selected>$key</option>";
			}
		}
		$html .= "\t\t\t</select></td>\n";
		$html .= "\t\t</tr>\n";
	}

	$html .= "\t</table>\n";
	$html .= "\t<div class='save' style='display:flex; flex-flow:row;'>\n";
	$html .= "\t\t<a href='index.php?page=$page'><img src='pub/img/interface/negative-squared-cross-mark_274e.png' style='height:24px;' /></a>\n";
	$html .= "\t\t<input type='image' src='pub/img/interface/white-heavy-check-mark_2705.png' alt='Submit' style='height:24px;' >\n";
	$html .= "\t</div>\n";
	$html .= "</form>\n";

	return $html;
}

?>