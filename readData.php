<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>StumbleUpon Dashboard</title>
<style type="text/css">
#main {
	margin: 10px;
	background-color: #ffffff;
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

<script type="text/javascript">
function doAction(action) {
	console.log(location);
	window.location = "http://33.33.33.33/preakness/stumbleupon/readData.php?action=" + action; 
}
</script>
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
<form id="form1" name="form1" method="get" action="">  
  <label>Action:
    <select name="action" id="action">
      <option value="create" selected="selected">Create</option>
      <option value="import">Import</option>
      <option value="delete">Delete</option>
    </select>
  </label>
  <input type="submit" name="button" id="button" value="Do It" />
</form>
<?php 

	define('EXECUTE_SQL_STATEMENTS',true);		// for debugging turn this off to prevent execution of sql statements
	$uniqueId = 100;
	
	function showError($errorMsg = NULL)
	{
		if ($errorMsg)
			print "$errorMsg\n";
		die("Error " . mysql_errno() . " : " . mysql_error( ));
	}

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
		if (! ($connection = @ mysql_connect("localhost","root")))
			die ("could not connect to mysql localhost");
		
		// select the stumbleupon database
		if (! (@mysql_select_db("stumbleupon",$connection)))
			showError("Could not select the stumbleupon database, make sure it exists");
			
		foreach ($sqlStatements as $sqlStatement) {
			if (EXECUTE_SQL_STATEMENTS) {
				if (!($result = @ mysql_query("$sqlStatement",$connection)))
					showError("mysql_query: $sqlStatement\n<br />" );
				print "result: $result\n";
			}
		} 
	}
	
	function importFile($filePath,$siteName) {
		global $uniqueId;
		print "Import Data from file: $filePath\n<br />";
		$fp = fopen($filePath,'r') or die("can't open file, $filePath");
		define('NUM_SITE_VIEW_COLS_TO_READ',7);		// number of columns of data to read from csv file to be inserted into siteViews table
		
		print '<table border="1">'."\n"; 
		print "<tr><td>rating</td><td>timestamp</td><td>age</td><td>gender</td><td>city</td><td>state</td><td>country</td><td>tags</td></tr>\n";
		
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
	
	/* For every file in the directory $dirPath look for a filename ending
	* in .csv and import all the ones we find
	*/
	function iterateThroughDirectory($dirPath) {
		foreach (new DirectoryIterator($dirPath) as $file) {
			$filePathParts = explode('/',$file->getPathname());
			$fileName = $filePathParts[1];
			$fileParts = explode('.',$fileName);
			if ($fileParts[count($fileParts) - 1] == "csv") {
				$siteName = substr($fileName,0,count($fileName) - 5);
				print "Imported: " . $file->getPathname() . " siteName: " . $siteName . "\n<br />";
				importFile($file->getPathname(),$siteName);
			}
		}
	}
	
	function delete() {
		$sqlStatements = array();
		$sqlStatements[] = "DROP TABLE IF EXISTS tags";
		$sqlStatements[] = "DROP TABLE IF EXISTS siteViews";
		executeSqlStatements($sqlStatements);
	}

	function create() {
		$sqlStatements = array();
		$sqlStatements[] = "CREATE DATABASE IF NOT EXISTS stumbleupon";
		$sqlStatements[] = "USE stumbleupon";
		$sqlStatements[] = "CREATE TABLE IF NOT EXISTS `siteViews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siteName` varchar(40) NOT NULL,
	`rating` int(5) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `age` int(5) NOT NULL,
  `gender` int(5) NOT NULL,
  `city` varchar(30) NOT NULL,
  `state` varchar(30) NOT NULL,
  `country` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2250";
		$sqlStatements[] = "CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
	`viewId` int(11) NOT NULL,
  `siteName` varchar(40) NOT NULL,
	`rating` int(5) NOT NULL,
  `tag` varchar(30) NOT NULL,
  `count` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2250";
		executeSqlStatements($sqlStatements);
	}
	
	

	$action = @ $_GET["action"];
	print "action = $action\n<br />";
	switch ($action) {
		case "import" :
			iterateThroughDirectory("smallData");
			break;
		case "delete" :	
			delete();
			break;
		case "create" :
			create();
			break;
	}
?>
</div>
</body>
</html>