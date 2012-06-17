<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>StumbleUpon Dashboard</title>
<style type="text/css">
#main {
	margin: 10px;
	
}

.logo-primary {
	height : 66px;
	width : 290px;
	background-image: url(images/sulogo.png);
	background-repeat:no-repeat;
}

header {
margin: 10px;
}

body,td,th {
	font-family: Verdana, Geneva, sans-serif;
	background-color: #f1f1ee;
}
.centered {
	text-align: center;
}

.center{
	
}
</style>
</head>

<body>
<header>
<img src="images/sulogo.png" width="291" height="66" alt="StumbleUpon" /> 
<h2 class="centered">Dashboard</h2>
<h3 class="centered">Import Data</h3>
</header>
<div id="nav">
<a href="index.html">Home</a>
</div>
<div  id="main">
<?php 

	define('EXECUTE_SQL_STATEMENTS',true);		// for debugging turn this off to prevent execution of sql statements

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
	
	function importFile($siteName) {
		$fileName = "data/" . $siteName . ".csv";
		print "Import Data from file: $fileName\n<br />";
		$fp = fopen($fileName,'r') or die("can't open file, $fileName");
		define('NUM_SITE_VIEW_COLS_TO_READ',7);		// number of columns of data to read from csv file to be inserted into siteViews table
		
		print '<table border="1">'."\n"; 
		print "<tr><td>rating</td><td>timestamp</td><td>age</td><td>gender</td><td>city</td><td>state</td><td>country</td><td>tags</td></tr>\n";
		$uniqueId = 100;
		$columnNames = array('id', 'siteName', 'rating', 'timestamp', 'age', 'gender', 'city', 'state', 'country');
		$sqlStatements = array();
		$tagsSqlStatements = array();
		while ($csv_line = fgetcsv($fp)) {
			$sqlStatement = "INSERT INTO siteViews SET $columnNames[0]=$uniqueId, $columnNames[1]=\"$siteName\"";
			// read up to where the tags start, to create sql insert statements for the siteViews
			for ($i = 0; $i < NUM_SITE_VIEW_COLS_TO_READ; $i++) {
				print '<td>'.$csv_line[$i].'</td>'."\n";
				$columnIndex = $i + 2;
	//			print ", $columnNames[$columnIndex]=$csv_line[$i]\n";
				$sqlStatement .= ", $columnNames[$columnIndex]=\"$csv_line[$i]\"";
		
			}
			// read the tag information create SQL for the tags table
			for ($i=NUM_SITE_VIEW_COLS_TO_READ; $i<count($csv_line); $i++) {
				$fields = explode(':',$csv_line[$i]);
				if (is_array($fields) && count($fields) > 1) {
					$tagSqlStatement = "INSERT INTO tags SET viewId=$uniqueId, siteName=\"$siteName\", rating=\"$csv_line[0]\"";
					$tagSqlStatement .= ", tag=\"$fields[0]\", count=\"$fields[1]\"";
					$tagSqlStatements[] = $tagSqlStatement;
				}
			}
			$uniqueId += 1;
			print "</tr>\n";
			$sqlStatements[] = $sqlStatement;
		} 
		print "</table>\n"; 
		print "</body>\n</html>\n";
		fclose($fp) or die("can't close file");
		executeSqlStatements($sqlStatements);
		executeSqlStatements($tagSqlStatements);
	}
	
	importFile("hubspotsmall.com");
?>
</body>
</html>