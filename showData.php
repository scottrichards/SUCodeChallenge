<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Show Site Views</title>
</head>

<body>
<pre>
<?php
// create a connection to mysql
$connection = mysql_connect("localhost","root");
// select the stumbleupon database
mysql_select_db("stumbleupon",$connection); 
// retrieve all the site views
$result = mysql_query("SELECT * from siteViews",$connection);
while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
	foreach ($row as $attribute) {
		print "{$attribute} ";
	}
	print "\n";
}

?>
</pre>
</body>
</html>