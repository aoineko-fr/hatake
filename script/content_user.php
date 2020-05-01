<?php

	switch($action)
	{		
		case "edit":
			$id = GetVar("id");
			$html = SqlDisplayEntry("user", $id, $edit, $page);
			break;
			
		case "add":
			$html = SqlDisplayEntry("user", -1, $edit, $page);
			break;
			
		case "view":
		default:
			$html = "<div style='float:right;'><a href='?task=do_disconnect'><img src='pub/img/interface/bust-in-silhouette_1f464.png' style='height:24px;' /></a></div>\n";
			$html .= SqlDisplayEntry("user", $userId, TRUE, $page);
			if($g_UserAdmin)
				$html .= "<br />\n". SqlDisplayTable("user", $edit);
	}
	print($html);

?>
