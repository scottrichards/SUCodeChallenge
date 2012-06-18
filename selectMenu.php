<?php

	require_once "sql/config.php";
	define ('DEBUG', false);
	
  function selectMenu($connection, $tableName, $attributeName, $pulldownName) {
		$sqlDistinctQuery = "SELECT DISTINCT $attributeName from $tableName";
		if (DEBUG) print "SQL: $sqlDistinctQuery\n<br />";
		
		$result = @ mysql_query($sqlDistinctQuery, $connection);
		if (DEBUG) print "Num Rows: " . mysql_num_rows($result) . "\n<br />";
		if (!$result) {
			print "Error with sql Statement: $sqlDistinctQuery\n";
			die;
		}
		
		// output select pull down menu with specified name	
		print "\n<select name=\"{$pulldownName}\">";
		// retreive each row from results of query
		print "\n\t<option value=\"all\">All</option>";
		while ($row = @ mysql_fetch_array($result)) {
				if (DEBUG) print "$row[$attributeName]\n<br />"; 
			print "\n\t<option value=\"{$row[$attributeName]}\">{$row[$attributeName]}</option>";
		}
		print "\n</select>";
	}

	// create a connection to mysql
	$connection = mysql_connect(SQL_SERVER,SQL_USER);
	// select the stumbleupon database
	mysql_select_db(SQL_DATABASE,$connection);
	selectMenu($connection,SQL_VIEWS_TABLE,"siteName","siteNameSelectMenu");
?>