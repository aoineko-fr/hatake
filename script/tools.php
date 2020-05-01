<?php

require_once("script/settings.php");
require_once("script/localization.php");

//--------------------------------------------------
// G L O B A L   D E F I N E S
//--------------------------------------------------
define("FROM_URL",    1);
define("FROM_POST",   2);
define("FROM_COOKIE", 3);
define("FROM_FILES",  4);

define("VER_MAJ", 0);
define("VER_MED", 0);
define("VER_MIN", 2);

//--------------------------------------------------
// V E R S I O N
//--------------------------------------------------

/** Get version string */
function GetVersion()
{
	return VER_MAJ.".".VER_MED.".".VER_MIN;
}

/** Get core system credit */
function GetCredit()
{
	return "<b>Hatake ".VER_MAJ.".".VER_MED.".".VER_MIN."</b> by Guillaume Blanchard (Aoineko) under GPL (2020).<br/>"
	."Moon phase use Solaris by Samir Shah (http://rayofsolaris.net) Copyright 2012";
}

//--------------------------------------------------
// D E B U G   L O G
//--------------------------------------------------
$g_Log = "";

/** */
function AddLog($text)
{
	global $g_Log;
	$g_Log .= "> $text\n";
}

/** */
function AddLogCategory($text)
{
	global $g_Log;

	$first  = "";
	$second = "";
	for($i = 0; $i < 80; $i++)
	{
		$first  .= "&nbsp;";
		$second .= "_";
	}
	$first  .= " _";
	$second .= "/ ";
	for($i = 0; $i < strlen($text); $i++)
	{
		$first .= "_";
	}
	$second .= $text;
	$first  .=  "_";
	$second .= " \\_";
	
	$g_Log .= "$first\n";
	$g_Log .= "$second\n";
}

/** */
function GetLog()
{
	global $g_Log;
	$str = str_replace("\n", "<br/>\n", $g_Log);
	$str = str_replace(" ", "&nbsp;", $str);
	return $str;
}

//--------------------------------------------------
// L O C A L I Z A T I O N 
//--------------------------------------------------

/** Get localized texte */
function Loc($entry)
{
	global $g_Loc, $g_DefaultLang, $g_Lang;
	
	$cur_lang = $g_Lang;
	
	if(!isset($g_Loc[$cur_lang]))
		$cur_lang = $g_DefaultLang;
	
	if(!isset($g_Loc[$cur_lang][$entry]))
		$cur_lang = $g_DefaultLang;
	
	if(!isset($g_Loc[$cur_lang][$entry]))
		return "#$entry";

	return $g_Loc[$cur_lang][$entry];
}

function LocExists($entry)
{
	return Loc($entry)[0] != '#';
}

/** */
function RemoveBreak($str)
{
	return str_replace(array(" ", "-"), array("&nbsp;", "&#8209;"), $str);
}

//--------------------------------------------------
// F O R M   V A R I A B L E S   H A N D L E I N G
//--------------------------------------------------

/**
 * Get PHP variable
 *
 * @param $source	Variable source (URL, POST, etc.)
 * @param $param	Variable name
 * @param $default	Default variable value
 */
function GetVarFrom($source, $param, $default = "")
{
	if($source == FROM_URL)
		return GetVarFromURL($param, $default);
	else if($source == FROM_POST)
		return GetVarFromPost($param, $default);
	else if($source == FROM_COOKIE)
		return GetVarFromCookie($param, $default);
	else if($source == FROM_FILES)
		return GetVarFromFile($param, $default);
	else
		die("Unknow variable source!");
}

/**
 * Get variable from URL
 *
 * @param $param	Variable name
 * @param $default	Default variable value
 */
function GetVarFromURL($param, $default = "")
{
	if(isset($_GET[$param]))
		return $_GET[$param];
	else
		return $default;
}

/**
 * Get variable from POST
 *
 * @param $param	Variable name
 * @param $default	Default variable value
 */
function GetVarFromPost($param, $default = "")
{
	if(isset($_POST[$param]))
		return $_POST[$param];
	else
		return $default;
}

/**
 * Get variable from Cookie
 *
 * @param $param	Variable name
 * @param $default	Default variable value
 */
function GetVarFromCookie($param, $default = "")
{
	if(isset($_COOKIE[$param]))
		return $_COOKIE[$param];
	else
		return $default;
}

/**
 * Get variable from file
 *
 * @param $param	Variable name
 * @param $default	Default variable value
 */
function GetVarFromFile($param, $default = "")
{
	if(isset($_FILES[$param]))
		return $_FILES[$param]["name"];
	else
		return $default;
}

/**
 * Get variable from HTTP header
 *
 * @param $param	Variable name
 * @param $default	Default variable value
 */
function GetVar($param, $default = "")
{
	if(isset($_REQUEST[$param]))
		return $_REQUEST[$param];
	else if(isset($_COOKIE[$param]))
		return $_COOKIE[$param];
	else if(isset($_FILES[$param]))
		return $_FILES[$param]["name"];
	else
		return $default;
}

/**
 * Save cookie variable
 *
 * @param $name		Variable name
 * @param $value	Variable value
 */
function SaveCookie($name, $value)
{
	$expires = mktime(0, 0, 0, date("n"), date("j"), date("Y")+1);
	$res = setcookie($name, $value, $expires);
	AddLog("Save cookie '$name'='$value'. Expires ". date("Y-m-j", $expires) .". Result:$res");
	return $res;
}

/**
 * Delete a cookie
 *
 * @param $name		Variable name
 */
function DeleteCookie($name)
{
	// Delete from current session
	if(isset($_COOKIE[$name]))
		unset($_COOKIE[$name]);
	
	// Delete from browser cache
	$res = setcookie($name, "", 1);
	AddLog("Delete cookie '$name'. Result:$res");
	return $res;
}

//--------------------------------------------------
// L O G I N
//--------------------------------------------------

/**
 * Retreive login and password information
 */
function Login()
{
	AddLog("Login");

	$login = GetVar("login");
	$password = GetVar("password");
	$pw_md5 = md5($password);
	SaveCookie("login", $login);
	SaveCookie("pw_md5", $pw_md5);
}

/**
 * Clear cookie data to remove login information
 */
function Logout()
{
	AddLog("Logout");
	
	DeleteCookie("login");
	DeleteCookie("pw_md5");
}

//--------------------------------------------------
// C S V
//--------------------------------------------------

/**
 * This function allows you to import a CSV file and export it into a PHP array
 *
 * @author Bastien Malahieude
 *
 * @param string $file      The file you want to import the data from
 * @param string $delimiter The CSV file delimiter
 * @param string $enclosure The type of enclosure used in the CSV file
 *
 * @return array            The array containing the CSV infos
 */
function ImportCsvToArray($file, $delimiter = ";", $enclosure ="\"")
{

    // Let's get the content of the file and store it in the string
    $csv_string = file_get_contents($file);

    // Get all the lines of the CSV string
    $lines = explode("\n", $csv_string);

    // The first line of the CSV file is the headers that we will use as the keys
    $head = str_getcsv(array_shift($lines), $delimiter, $enclosure);

    $array = array();

    // For all the lines within the CSV
    foreach ($lines as $line)
	{
        // Sometimes CSV files have an empty line at the end, we try not to add it in the array
        if(empty($line))
		{
			continue;
		}
		
        // Get the CSV data of the line
        $csv = str_getcsv($line, $delimiter, $enclosure);

        // Combine the header and the lines data
        $array[] = array_combine($head, $csv);

    }

    // Returning the array
    return $array;
}

/**
 * Exports an associative array into a CSV file using PHP.
 *
 * @author Bastien Malahieude
 * @see https://stackoverflow.com/questions/21988581/write-utf-8-characters-to-file-with-fputcsv-in-php
 *
 * @param array     $data       The table you want to export in CSV
 * @param string    $filename   The name of the file you want to export
 * @param string    $delimiter  The CSV delimiter you wish to use. The default ";" is used for a compatibility with microsoft excel
 * @param string    $enclosure  The type of enclosure used in the CSV file, by default it will be a quote "
 */
function ExportArrayToCsv($data, $filename, $delimiter = ";", $enclosure = "\"")
{
    // Tells to the browser that a file is returned, with its name : $filename.csv
    header("Content-Disposition: attachment; filename=$filename");
    // Tells to the browser that the content is a csv file
    header("Content-Type: text/csv");

    // I open PHP memory as a file
    $fp = fopen("php://output", 'w');

    // Insert the UTF-8 BOM in the file
    fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

    // I add the array keys as CSV headers
    fputcsv($fp, array_keys($data[0]), $delimiter, $enclosure);

    // Add all the data in the file
    foreach ($data as $fields)
	{
        fputcsv($fp, $fields, $delimiter, $enclosure);
    }

    // Close the file
    fclose($fp);

    // Stop the script
    die();
}

/***/
function GetCsvFromHeader()
{
	$csvDelimiter = GetVar("csv_delimiter");
	$csvEnclosure = GetVar("csv_enclosure");
	return ImportCsvToArray($_FILES["csv_file"]['tmp_name'], $csvDelimiter, $csvEnclosure);
}

//--------------------------------------------------
// M I S C
//--------------------------------------------------

/***/
function ExtractNumbers($str)
{
	preg_match_all('!\d+[\.\,]*\d*!', $str, $matches);
	return $matches;
}


?>