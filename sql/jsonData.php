<?php

require_once "config.php";

// prevent caching !
header("Expires: 0"); 
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); // Add some IE-specific options
header("Cache-Control: post-check=0, pre-check=0", false); // For HTTP/1.0
header("Pragma: no-cache");
// end of caching

// specify content type to be json
header('Content-type: application/json');

define('DEBUG',false);		// set this to true to print out debug statements

	/* executeSqlStatements
	*
	* executes an array of sql statements
	*/
	function executeSqlStatements($sqlStatements) {
		
		print"SQL View STATEMENTS:\n</br>";
		foreach ($sqlStatements as $sqlStatement) {
			print "$sqlStatement\n</br>";
		}
		// create a connection to mysql
		$connection = mysql_connect("localhost","root");
		// select the stumbleupon database
		mysql_select_db("stumbleupon",$connection);
		foreach ($sqlStatements as $sqlStatement) {
			if (EXECUTE_SQL_STATEMENTS) {
				$result = mysql_query("$sqlStatement",$connection);
				print "result: $result\n";
			}
		} 
	}
	
	function ratingString($rating = "All") {
	
		switch($rating) {
			case -1 : return "Thumbs Down";
								break;
			case 0  : return "No Rating";
								break;
			case 1  : return "Thumbs Up";
								break;
		}
		// trick, to avoid warning when no parameter is passed it was not -1 - 1 then return default
		return $rating;
	}
	
	function getFieldData($connection){
		// select just one row 
		$result = mysql_query("SELECT * FROM " . SQL_TAGS_TABLE . " LIMIT 1", $connection);
		$numFields = mysql_num_fields($result);		// number of fields in the table
		$fieldNames = array();
		for ($i=0;i<$numFields;$i++) {
			$info = mysql_fetch_field($result);
			$fieldNames[] = $info->name;
		}
		return $fieldNames;
	}
	
	function generateJSON($rating = NULL,$count,$minimum = NULL) {
		// create a connection to mysql
		$connection = mysql_connect(SQL_SERVER,SQL_USER);
		// select the stumbleupon database
		mysql_select_db(SQL_DATABASE,$connection);
		$whereClause = "";
		if (isset($rating) && $rating != "all") {
			$whereClause = " WHERE rating='" . $rating . "'";
		}
		$havingClause = "";
		// to get the # of individual people you execute the following SQL statement COUNT(count) instead of SUM(COUNT)
		$countStr = ($count == "people") ? "COUNT(count)" : "SUM(count)";
		if (isset($minimum) && $minimum > 0) {
			
			$havingClause = " HAVING " . $countStr . " >= " . $minimum;
		}
		if (DEBUG) print "havingClause: $havingClause</br>\n";
		
	 $sqlStatement = "SELECT rating, tag, " . $countStr . " FROM " . SQL_TAGS_TABLE . $whereClause . " GROUP BY tag, rating" . $havingClause;
/*		
		if ($count == "people")
			$sqlStatement = "SELECT rating, tag, " . $countStr . " FROM " . SQL_TAGS_TABLE . $whereClause . " GROUP BY tag, rating" . $havingClause; 
		else
			$sqlStatement = "SELECT rating, tag, SUM(count) FROM " . SQL_TAGS_TABLE . $whereClause . " GROUP BY tag, rating" . $havingClause; 
*/
		if (DEBUG) print "sqlStatement: $sqlStatement</br>\n";
		if (DEBUG) print "Rating: " . ratingString($rating) . "</br>\n";
	
		$ratingColHeader = ($rating == NULL || $rating== "all") ? "<td>rating</td>": "";
		
		// get the array of fields names from the table so we can iterate through them
//		$nameFields = getFieldData($connection);
		
		$result = mysql_query($sqlStatement,$connection);
		$jsonRows = array();
		while ($row = mysql_fetch_array($result,MYSQL_BOTH)) {
			if (DEBUG) print "rating: " . $row["rating"] . ", tag: " . $row["tag"] . ", count: " . $row[$countStr] . "\n";
			$jsonRow = array();
			$jsonRow['rating'] = $row["rating"];
			$jsonRow['tag'] = $row["tag"];
			$jsonRow['count'] = $row[$countStr];
/*			if ($count == "people")		// if count is set to people then do a count instead of SUM
				$jsonRow['count'] = $row["COUNT(count)"];
			else
				$jsonRow['count'] = $row["SUM(count)"];
	*/
			$jsonRows[] = $jsonRow;
		}
		return $jsonRows;
	}
		
	if (!isset($_GET["count"]))
		$_GET["count"] = "allTags";
	$minimum = 0;
	if (isset($_GET["minimum"]))
		$minimum = $_GET["minimum"];
	$json = json_encode(generateJSON($_GET["rating"],$_GET["count"],$minimum));
	print "$json";
?>