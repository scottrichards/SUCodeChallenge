<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Create Reports</title>
<script type="text/javascript">

document.addEventListener("DOMContentLoaded",docLoaded);

function docLoaded() {
	console.log(location.search);
	var urlParams = location.search;
	re=/rating\=(\w*)/;
	var matches = urlParams.match(re);
  if (matches.length > 1) {
	  console.log(matches[0]);
	}
	
}

</script>
</head>
<body>
<form id="form1" name="form1" method="get" action="">
  <label>Rating:
    <select name="rating" id="rating" onchange="updateRatings()">
      <option value="all">All</option>
      <option value="0">None</option>
      <option value="1">Thumbs Up</option>
      <option value="-1">Thumbs Down</option>
    </select>
  </label>
  <input type="submit" name="button" id="button" value="Reload" />
</form>
<?php 

	define('EXECUTE_SQL_STATEMENTS',true);		// for debugging turn this off to prevent execution of sql statements
	define('SQL_SERVER',"localhost");
	define('SQL_USER',"root");
	define('SQL_DATABASE',"stumbleupon");
	define('SQL_TAGS_TABLE',"tags");
		
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

	function generateTable($rating = NULL) {
	// create a connection to mysql
		$connection = mysql_connect(SQL_SERVER,SQL_USER);
		// select the stumbleupon database
		mysql_select_db(SQL_DATABASE,$connection);
		$whereClause = "";
		if (isset($rating) && $rating != "all") {
			$whereClause = " WHERE rating='" . $rating . "'";
		}
//		$sqlStatement = "SELECT * FROM " . SQL_TAGS_TABLE . $whereClause . " ORDER BY tag"; 
		$sqlStatement = "SELECT rating, tag, SUM(count) FROM " . SQL_TAGS_TABLE . $whereClause . " GROUP BY tag, rating"; 

		print "sqlStatement: $sqlStatement</br>\n";
		print "Rating: " . ratingString($rating) . "</br>\n";
		print '<table id="tableId" border="1">'."\n"; 
		$ratingColHeader = ($rating == NULL || $rating== "all") ? "<td>rating</td>": "";
//		print "<tr><td>viewId</td>$ratingColHeader<td>siteName</td><td>tag</td><td>count</td></tr>\n";
		print "<tr>$ratingColHeader<td>tag</td><td>count</td></tr>\n";

		$result = mysql_query($sqlStatement,$connection);

		while ($row = mysql_fetch_array($result,MYSQL_BOTH)) {
			print "<tr>\n";
			$ratingCol = ($rating == NULL || $rating == "all") ? "<td>" . $row["rating"] . "</td>" : "";
//			print "<td>" . $row["viewId"]. "</td>$ratingCol<td>" . $row["siteName"]. "</td><td>" . $row["tag"]. "</td><td>" . $row["count"]. "</td>";
			print "$ratingCol<td>" . $row["tag"]. "</td><td>" . $row["SUM(count)"]. "</td>";
	
			print "</tr>\n";
		}
		print "</table>\n"; 
		print "</body>\n</html>\n";
	}	
	
	generateTable($_GET["rating"]);
?>
</body>
</html>