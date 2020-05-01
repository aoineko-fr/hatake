<?php

define("SETTING_LOCAL", 1);

$setting = SETTING_LOCAL;

if($setting == SETTING_LOCAL)
{
	$server         = "localhost/hatake/";
	$sqlHost		= "127.0.0.1";
	$sqlUser		= "root";
	$sqlPassword	= "root";
	$sqlDB			= "hatake";
	$sqlTablePrefix	= "hatake_";
	$needLogin      = true;
	$selfRegister   = true;
}


$g_AdminOnlyField = array("user.admin");
$g_AdminOnlyTable = array("project");

?>