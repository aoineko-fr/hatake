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
		
	case "view":
	default:
		$html = SqlDisplayTable("crop", $edit, "crop_event", "crop");
}

print($html);

?>
