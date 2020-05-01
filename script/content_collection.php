<?php

switch($action)
{
	case "edit":
		$id = GetVar("id");
		$html = SqlDisplayEntry($table, $id, $edit, $page, $parentTable, $parentId);
		break;
		
	case "add":
		$html = SqlDisplayEntry($table, -1, $edit, $page, $parentTable, $parentId);
		break;
		
	case "import":
		$html = SqlDisplayFieldMatch("variety", $csvFile);
		break;
		
	case "view":
	default:
		$html = SqlDisplayTable("variety", $edit, "variety_period", "variety");
}

print($html);

?>
