<?php

	//======== INCLUDES ========

	require_once("script/settings.php");
	require_once("script/localization.php");
	require_once("script/tools.php");
	require_once("script/sql.php");
	require_once("script/solaris/MoonPhase.php");

	//======== INITIALIZATION ========

	AddLog("\$_GET ". print_r($_GET, true));
	AddLog("\$_POST ". print_r($_POST, true));
	AddLog("\$_COOKIE ". print_r($_COOKIE, true));
	AddLog("\$_FILES ". print_r($_FILES, true));

	AddLogCategory("Initialization");

	date_default_timezone_set('UTC');

	$pageList = array(
		array("map",		"pub/img/interface/world-map_1f5fa.png"),
		array("crops",		"pub/img/vegetables/seedling_1f331.png"),
		array("collection",	"pub/img/interface/ledger_1f4d2.png"),
		array("schedule",	"pub/img/interface/spiral-calendar-pad_1f5d3.png"),
		array("project",	"pub/img/interface/hammer-and-wrench_1f6e0.png"),
		array("user",		"pub/img/interface/man-farmer_1f468-200d-1f33e.png"),
	);

	$csvFile = NULL;

	//======== SQL CONNECTION ========

	SqlConnect($sqlHost, $sqlUser, $sqlPassword, $sqlDB, $sqlTablePrefix);
	
	//======== SQL ACTION HANDLING ========


	$action      = GetVar("action", "view");
	$task        = GetVar("task");
	$table       = GetVar("table");
	$id          = GetVar("id", -1);
	$parentTable = GetVar("parent");
	$parentId    = GetVar("parentid", -1);

	if($task)
	{
		AddLogCategory("Task handling");
		AddLog("Current task: $task");
		switch($task)
		{
			case "do_edit":
				SqlUpdateEntryFromHeader($table);
				break;
				
			case "do_add":
				SqlInsertEntryFromHeader($table);
				break;

			case "do_remove":
				SqlRemoveEntry($table, $id);
				break;

			case "do_register":
				SqlInsertEntryFromHeader("user");
				// then do_login
			case "do_login":
				Login();
				break;

			case "do_disconnect":
				Logout();
				break;
				
			case "do_get_csv":
				$csvFile = GetCsvFromHeader();
				AddLog("Import CSV file\n". print_r($csvFile, true));				
				break;

			case "do_add_csv":
				SqlInsertEntriesFromHeader($table);
				break;

			case "do_export_csv":
				SendCsvToHeader();
				break;
		}
	}
	
	//======== RETREIVE PROJECT INFO ========

	AddLogCategory("Project info");

	$result = SqlQuery("SELECT * FROM `${g_SQLTablePrefix}project` WHERE `id`=1");
	if($result && $result->num_rows > 0) // Project found
	{
		$projectData = SqlFetchAllResult($result, MYSQLI_ASSOC);
		$g_Lang   = $projectData[0]['lang'];
		$name     = $projectData[0]['name'];
		$projImage = $projectData[0]['map_image'];
		$edit     = $projectData[0]['allow_edit'];
		$dev      = $projectData[0]['allow_dev'];
		$page     = GetVar("page", $pageList[2][0]);
	}
	else // Project need to be created
	{
		$g_Lang   = "fr";
		$name     = "";
		$page     = "project";
		$edit     = 0;
		$dev      = 0;
	}

	setlocale(LC_ALL, Loc("date.lang"));

	//======== LOGIN HANDLING ========
	
	$userId = -1;
	$g_UserAdmin = !$needLogin;
	if($needLogin)
	{	
		//var_dump($_REQUEST);
		AddLogCategory("Login check");

		if(in_array($task, array("do_login", "do_register")))
		{
			$login = GetVar("login");
			$pw_md5 = md5(GetVar("password"));
		}
		else
		{
			$login = GetVarFromCookie("login");
			$pw_md5 = GetVarFromCookie("pw_md5");
		}
		AddLog("Login/password-md5 provided: $login/$pw_md5");
		
		$loginSucceed = false;
		if($login && $pw_md5)
		{
			$result = SqlQuery("SELECT * FROM `${g_SQLTablePrefix}user` WHERE `login`='$login'");
			if($result && mysqli_num_rows($result) > 0)
			{
				$userTab = SqlFetchAllResult($result, MYSQLI_ASSOC);
				//var_dump($userTab);
				if($pw_md5 == $userTab[0]['password'])
				{
					$loginSucceed = true;
					$userId = $userTab[0]['id'];
					$g_UserAdmin = $userTab[0]['admin'];
					AddLog("Login succeed!");
				}
				else
					AddLog("Login failed! Invalid password '$pw_md5' for login '$login'");
			}
			else
				AddLog("Login failed! Login '$login' not found");
		}
		else
			AddLog("Login failed! No login and/or password provided");

		if(!$loginSucceed)
			$page = "login";
	}

	AddLogCategory("Page content");

	AddLog("Current action: $action");
	AddLog("Current table: $table");
	AddLog("Current id: $id");
	AddLog("Current parent table: $parentTable");
	AddLog("Current parent id: $parentId");

	AddLog("Current lang: $g_Lang");
	AddLog("Current edit: $edit");
	AddLog("Current page: $page");

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Hatake<?php if($name) print(" - $name"); ?></title>
        <meta charset="utf-8" />
		<meta name="Author" content="Guillaume Blanchard">
		<meta name="Copyright" content="&copy; 2020 Guillaume Blanchard, Under free GNU Public Licence">
		<link rel="stylesheet" href="pub/main.css">
		<link rel="icon" href="pub/img/favicon.ico">
		<script type="text/javascript" src="pub/main.js"></script>
	</head>
	<body onload="<?php if(!$dev) print("ToggleVisibilityByClass('dev');"); ?>" >
		<section id="main">
			<div id="menu" style="display:flex; flex-flow:column;" >
				<div id="menu_head"><?php require_once("script/menu.php"); ?></div>			
				<?php
					print "<div id='menu_$page'>\n";
					if(file_exists("script/menu_".$page.".php"))
						require_once("script/menu_".$page.".php");
					print "</div>\n";
				?>
				<div style="flex-grow:1; display:flex; flex-flow:column;" >
					<div id="" style="flex-grow:1;" ></div>
					<div id="credit" style="" ><?php print(GetCredit()); ?></div>
				</div>
			</div>
			<div id="tabs" style="display:flex; flex-flow:row;">
				<?php
					foreach ($pageList as $pageInfo)
					{
						$pageName = $pageInfo[0];
						$pageImg = $pageInfo[1];

						if(!$needLogin && $pageName == "user")
							continue;

						$selected = ($page == $pageName) ? "selected" : "";
						print("<div class='tab $selected' id='tab_$pageName'>\n");
						print("<a href='index.php?page=$pageName'><img src='$pageImg' style='height:14px; margin:2px 0.2em 0 2px;' />". Loc("page.$pageName") ."</a>\n");
						print("</div>\n");	
					}
				?>
				<div style="flex-grow:1;" ><img src="pub/img/interface/eye_1f441.png" style="height:16px; float:right; cursor:pointer;" onclick="ToggleVisibilityByClass('dev');" /></div>
			</div>
			<?php
				print "<div id='content' id='content_$page' >\n";
				if(file_exists("script/content_".$page.".php"))
					require_once("script/content_".$page.".php");
				print "<div id='log' class='dev'>". GetLog() ."</div>\n";
				print "</div>\n";
			?>
		</section>
	</body>
</html>

<?php SqlDisconnect(); ?>