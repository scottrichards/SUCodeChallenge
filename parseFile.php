<?php 
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
?>
