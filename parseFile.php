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
<h3 class="centered">Parse CSV Data</h3>
</header>
<div id="nav">
<a href="index.html">Home</a>
</div>
<div  id="main">
<?php 
	define('FILE_NAME',"data/gigaomsmall.com.csv");

	function importFile($fileName) {
		print "Importing File: $fileName\n</br>";
		$fp = fopen('data/smallsample.com.csv','r') or die("can't open file");
		print "<html>\n<body>\n";
		print "Outputting Data:";
		print '<table border="1">'."\n"; 
		print "<tr><td>rating</td><td>timestamp</td><td>age</td><td>gender</td><td>city</td><td>state</td><td>country</td><td>tags</td></tr>\n";
		$uniqueId = 100;
		while ($csv_line = fgetcsv($fp)) {
			$j = count($csv_line);
			for ($i = 0; $i < $j; $i++) {
				print '<td>'.$csv_line[$i].'</td>'."\n";
			}
			$uniqueId++;
			print "</tr>\n";
		} 
		print "</table>\n"; 
		print "</body>\n</html>\n";
		fclose($fp) or die("can't close file"); 
	}
	
	importFile(FILE_NAME);
?>
</div>
<footer></footer>
</body>
</html>
