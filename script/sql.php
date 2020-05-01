<?php

require_once("script/settings.php");
require_once("script/sql_operation.php");
require_once("script/sql_display.php");

//--------------------------------------------------
// S Q L
//--------------------------------------------------

$g_SQL = NULL;
$g_SQLTableList = NULL;
$g_SQLTablePrefix = "";

/***/
function SqlFetchAllResult($result, $resulttype = MYSQLI_NUM)
{
	if(!is_object($result))
		return NULL;
	
	if (method_exists('mysqli_result', 'fetch_all')) # Compatibility layer with PHP < 5.3
		$res = $result->fetch_all($resulttype);
	else
		for ($res = array(); $tmp = $result->fetch_array($resulttype);) $res[] = $tmp;

	AddLog("SQL Feched Result: " .print_r($res, TRUE));

	return $res;
}

/***/
function SqlIsTableExist($prefix, $table)
{
	return SqlQuery("DESCRIBE `$prefix$table`");
}

/***/
function SqlQueryFromFile($filename)
{
	global $g_SQL, $g_SQLTablePrefix;
	
	AddLog("Create the data-base tables");
	$queryFile = file_get_contents($filename);
	$query = str_replace("@PREFIX@", $g_SQLTablePrefix, $queryFile);
	$result = SqlQuery($query, true);
	
	return $result;
}


/** */
function SqlConnect($host, $user, $password, $db, $sqlTablePrefix = "")
{
	global $g_SQL, $g_SQLTableList, $g_SQLTablePrefix;
	
	AddLog("Connect to SQL server $host");

	$g_SQL = mysqli_connect($host, $user, $password, $db);

	if (!$g_SQL)
	{
		AddLog("Error: Unable to connect to mySQL server $host");
		AddLog("Error: #" . mysqli_connect_errno() . " " . mysqli_connect_error());
		exit;
	}

	AddLog("Succed: Connected to $host");
	AddLog("Host information: " . mysqli_get_host_info($g_SQL));
	
	$g_SQLTablePrefix = $sqlTablePrefix;
	AddLog("Table prefix: $g_SQLTablePrefix");

	if(!SqlIsTableExist($g_SQLTablePrefix, "project"))
		SqlQueryFromFile("script/create_db.sql");

	// Retreive table list
	$result = SqlQuery("SHOW tables LIKE '${g_SQLTablePrefix}%'");
	$fetchResult = SqlFetchAllResult($result, MYSQLI_NUM);
	//var_dump($fetchResult);
	foreach($fetchResult as $table)
	{
		$g_SQLTableList[] = substr($table[0], strlen($g_SQLTablePrefix));
	}
}

/** */
function SqlDisconnect()
{
	global $g_SQL;

	mysqli_close($g_SQL);
}

/** Handle SQL query and result */
function SqlQuery($query, $multi=false)
{
	global $g_SQL;

	if($multi)
	{
		AddLog("SQL multi-query: <b>$query</b>");
		$result = $g_SQL->multi_query($query);
	}
	else
	{
		AddLog("SQL query: <b>$query</b>");
		$result = $g_SQL->query($query);
	}
	
	AddLog("SQL error: ". $g_SQL->error);

	if(is_object($result))
	{
		AddLog("SQL result: " .print_r($result, TRUE));
		return $result;
	}
	
	if($result)
		AddLog("Query result: Succeed!");
	else
		AddLog("Query result: Failed!");
	
	return $result;
}

/** Execture SHOW query */
function SqlQueryShow($like)
{
	$result = SqlQuery("SHOW tables LIKE '$like%'");
	$fetchResult = SqlFetchAllResult($result, MYSQLI_NUM);
	return $fetchResult;
}

/** Execture SELECT query */
function SqlQuerySelect($selector, $table, $filter = NULL, $value = -1)
{
	if($filter)
		$result = SqlQuery("SELECT $selector FROM `$table` WHERE `$filter` = '$value'");
	else
		$result = SqlQuery("SELECT $selector FROM `$table`");
	$fetchResult = SqlFetchAllResult($result, MYSQLI_NUM);
	return $fetchResult;
}


/**  */
function SqlGetTableList()
{
	global $g_SQLTableList;
	return $g_SQLTableList;
}

/** Build HTML table from a SQL table */
function SqlGetTableHeader($baseTable)
{
	global $g_SQL, $g_SQLTablePrefix;

	// Get columns information
	$result = SqlQuery("SHOW FIELDS FROM `$g_SQLTablePrefix$baseTable`");
	if(!$result)
		return "Error! Can't build <table> from $baseTable";
	$header = SqlFetchAllResult($result, MYSQLI_ASSOC);
	//var_dump($header);

	return $header;
}

/** Build HTML table from a SQL table */
function SqlGetTableContent($baseTable, $filter = NULL, $value = 0)
{
	global $g_SQL, $g_SQLTablePrefix;
	
	$result = NULL;
	if(is_null($filter))
		$result = SqlQuery("SELECT * FROM `$g_SQLTablePrefix$baseTable`");
	else
		$result = SqlQuery("SELECT * FROM `$g_SQLTablePrefix$baseTable` WHERE `$filter` = '$value'");
	
	if(!$result)
		return "Error! Can't build <table> from $baseTable";
	$content = SqlFetchAllResult($result, MYSQLI_NUM);
	//var_dump($content);

	return $content;
}

/***/
function SqlRemoveEntry($table, $id)
{
	global $g_SQLTablePrefix;
	$result = SqlQuery("DELETE FROM `$g_SQLTablePrefix$table` WHERE `id` = $id");
	return $result;
}

?>