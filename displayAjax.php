<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>StumbleUpon Dashboard</title>
<style type="text/css">
#main {
	padding: 10px;
	background-color: #ffffff;
}

#filter {
	padding: 10px;
	border: thin solid #666;
	background-color:#eee;
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

body {
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

function generateTable(responseText)
{
	var main = document.getElementById("main");
	main.innerHTML = responseText;
	jsonObj = JSON.parse(responseText);
	var numRows = jsonObj.length;
	main.innerHTML += '\n<br />';
	var tableHTML = '<table id="tableId" border="1" cellpadding="2" cellspacing="0">\n';
	tableHTML += '<tr><th>Rating</th><th>Tag</th><th>Count</th></tr>'; 
	for (i=0;i<jsonObj.length;i++) {
		tableHTML += '<tr><td>' + jsonObj[i].rating + '</td><td>' + jsonObj[i].tag + '</td><td>' + jsonObj[i].count + '</td></tr>';
		console.log("Tag: " + jsonObj[i].tag + "Count: " + jsonObj[i].count);
	}
	tableHTML += "</table>";
	main.innerHTML += tableHTML;
}

function filterFormObject() {
}

function filter() {
	if (window.XMLHttpRequest) {
		function processResult() {
			if (xmlHttp.readyState == 4 && xmlHttp.status==200) {
				generateTable(xmlHttp.responseText);
			} else {
				main.innerHTML = "Error could not retrieve data from: " + url;
			}
		}
		
		xmlHttp = new XMLHttpRequest();
		url = "http://33.33.33.33/preakness/stumbleupon/sql/jsonData.php";
		url += "?rating=" + document.forms["filterform"].elements["rating"].value;
	  url += "&count=" + ((document.forms["filterform"].elements["tagCount"].checked) ? "tagCount" : "people");
		url += "&siteName=" + document.forms["filterform"].elements["siteNameSelectMenu"].value;
		if (document.forms["filterform"].elements["minimum"].value > 0 )
			url += "&minimum=" + document.forms["filterform"].elements["minimum"].value;
		console.log("url: " + url);
		xmlHttp.open('GET', url, true); 
		xmlHttp.onreadystatechange = processResult;
		xmlHttp.send( null);
	}
}
</script>
</head>

<body>
<header>
<img src="images/sulogo.png" width="291" height="66" alt="StumbleUpon" /> 
<h2 class="centered">Dashboard</h2>
</header>
<div id="filter">
<form id="filterform" name="filterform" method="get" action="">
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
    <label>Count:
      <input name="minimum" type="text" size="5" /></label>
  Minimum </p>
  <input type="button" name="button" id="button" value="Filter" onclick="filter()"/>
</form>
</div>
<div id="main">
</div>
<footer></footer>
</body>
</html>
