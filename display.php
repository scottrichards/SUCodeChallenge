<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Display Tag Data with PHP</title>
<style type="text/css">
#main {
	padding : 5px;
	margin : 5px;
	background-color: #ffffff;
	float : left;
}

#filter {
	padding: 5px;
	border: thin solid #CCC;
	background-color:#eee;
	font-size:.8em;
}

#sidePane {
	margin: 5px;
	float : left;
	
}

#instructions{
	font-size:.8em;
	margin-top:5px;
	max-width:250px;
}

.logo-primary {
	height : 66px;
	width : 290px;
	background-image: url(images/sulogo.png);
	background-repeat:no-repeat;
	float:left;
}

.nav-home {
	background-image: url(images/home.gif);
	background-repeat:no-repeat;
	float:right;
}

header {
/*padding: 5px; */
}

body {
	font-family: Verdana, Geneva, sans-serif;
	background-color: #f1f1ee;
}
.centered {
	text-align: center;
}

.clearFloat {
	clear : both;
}

</style>
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
<header>
<img src="images/sulogo.png" width="213" height="48" alt="StumbleUpon" style="float:left"/>
<a href="index.html"><img src="images/home.gif" style="float:right" /></a>
<h2 class="clearFloat centered">Display  Tag Data using PHP</h2>
</header>
<div id="sidePane">
<div id="filter">
  <fieldset><legend>Filter</legend>
  <form id="form1" name="form1" method="get" action="">
    <label>Site:
      <?php
      // include the pull down menu to select site
      require_once "selectMenu.php";
      ?>
    </label>
    <br />
    <label>Rating:
      <select name="rating" id="rating" onchange="updateRatings()">
        <option value="all">All</option>
        <option value="0">None</option>
        <option value="1">Thumbs Up</option>
        <option value="-1">Thumbs Down</option>
      </select>
      <br />
   
    </label>
    <p>
      Count:
      <label>
        <input name="count" type="radio" id="tagCount" value="tagCount" checked="checked" />
        Total Tags
      </label>
      <label>
        <input type="radio" name="count" value="people" id="peopleCount" />
        People
      </label>
    </p>
    <p>
      <label>Filter by  tag counts:
      <input name="minimum" type="text" size="5" /></label>
    Minimum</p>
    <input type="submit" name="button" id="button" value="Reload" />
  </form>
  </fieldset>
</div>
</div>
<div id="main">
<?php 

//define('DEBUG',true);

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

	function outputTableHeader($rating) {
		print '<table id="tableId" border="1" cellspacing="0">'."\n"; 
		$ratingColHeader = ($rating == NULL || $rating== "all") ? "<td>rating</td>": "";
		print "<tr>$ratingColHeader<td>tag</td><td>count</td></tr>\n";
	}

	function generateTable($rating = "all",$count="allTags",$minimum=0, $siteName="all") {
		$fileURL = "http://33.33.33.33/preakness/stumbleupon/sql/jsonData.php";
		$fileURL.= "?rating=" . $rating . "&count=" . $count . "&minimum=" . $minimum . "&siteName=" . $siteName;
		if (DEBUG) print "fileURL: $fileURL\n<br />";
		$result = json_decode(file_get_contents($fileURL));
		print "Site: ";
		print ($siteName == "all") ? "All" : $siteName;
		print "\n<br />";
		print "Rating: " . ratingString($rating) . "<br />\n";
		print "Count: ";
		print ($count == "people") ? "People" : "All Tags"; 
		print "<br />\n";
		print "Minimum: ";
		print ($minimum . 0) ? "$minimum tags<br />\n" : "None<br />\n";
		outputTableHeader($rating);
		foreach($result as $row) {
			print "<tr>\n";
			$ratingCol = ($rating == NULL || $rating == "all") ? "<td>" . $row->rating . "</td>" : "";
			print "$ratingCol<td>" . $row->tag . "</td><td>" . $row->count. "</td>";
			print "</tr>\n";
		}
		print "</table>\n";
	}

	if (!isset($_GET["count"]))
		$count = "allTags";
	else 
		$count = $_GET["count"];
	$minimum = 0;
	if (isset($_GET["minimum"]))
		$minimum = $_GET["minimum"];
	$rating = "all";
	if (isset($_GET["rating"]))
		$rating = $_GET["rating"];	
	if (isset($_GET["siteNameSelectMenu"]))
		$siteName = $_GET["siteNameSelectMenu"];
	else
		$siteName = "all";		
		
	generateTable($rating,$count,$minimum,$siteName);
?>
</div>
</body>
</html>