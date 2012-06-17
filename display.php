<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Display Data</title>
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
<?php 

define('DEBUG',true);

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
		print '<table id="tableId" border="1">'."\n"; 
		$ratingColHeader = ($rating == NULL || $rating== "all") ? "<td>rating</td>": "";
		print "<tr>$ratingColHeader<td>tag</td><td>count</td></tr>\n";
	}

	function generateTable($rating = "all",$count="allTags",$minimum=0) {
		$fileURL = "http://33.33.33.33/preakness/stumbleupon/sql/jsonData.php";
		$fileURL.= "?rating=" . $rating . "&count=" . $count . "&minimum=" . $minimum;
		if (DEBUG) print "fileURL: $fileURL\n</br>";
		$result = json_decode(file_get_contents($fileURL));
		print "Rating: " . ratingString($rating) . "</br>\n";
		outputTableHeader($rating);
		foreach($result as $row) {
			print "<tr>\n";
			$ratingCol = ($rating == NULL || $rating == "all") ? "<td>" . $row->rating . "</td>" : "";
			print "$ratingCol<td>" . $row->tag . "</td><td>" . $row->count. "</td>";
			print "</tr>\n";
		}
		print "</table>\n";
	}

	generateTable($_GET["rating"],$_GET["count"],$_GET["minimum"]);
?>
</body>
</html>