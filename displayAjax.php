<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>StumbleUpon Dashboard</title>
<style type="text/css">
#main {
	padding : 5px;
	margin : 5px;
	background-color: #ffffff;
	float : left;
}

#filter {
	padding: 5px;
	margin: 5px;
	border: thin solid #666;
	background-color:#eee;
	float : left;
	font-size:.8em;
}

.logo-primary {
	height : 66px;
	width : 290px;
	background-image: url(images/sulogo.png);
	background-repeat:no-repeat;
	float:left;
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

</style>
<script type="text/javascript">

window.onload=filter;

// remove the specified html node from the dom by navigating up to its parent node
function removeElement(node)
{
	node.parentNode.removeChild(node);
}

function returnRatingImage(rating)
{
	if (rating == 1)
	 return '<img src="images/thumbsUp.gif" width="25" height="25" />';
	if (rating == -1)
	 return '<img src="images/thumbsDown.gif" width="25" height="25" />';
	else 
		return'0'; 
}

function generateTable(responseText)
{
	// this is the div with id 'main' where we want to insert the table
	var main = document.getElementById("main");
	// make sure the return value is converted to JSON object
	jsonObj = JSON.parse(responseText);
	var numRows = jsonObj.length;
	main.innerHTML = '\n<br />';
	var tableHTML = '<table id="tableId" border="1" cellpadding="2" cellspacing="0">\n';
	tableHTML += '<tr><th>Rating</th><th>Tag</th><th>Count</th></tr>'; 
	for (i=0;i<jsonObj.length;i++) {
		tableHTML += '<tr><td>' + returnRatingImage(jsonObj[i].rating) + '</td><td>' + jsonObj[i].tag + '</td><td>' + jsonObj[i].count + '</td></tr>';
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
//			console.log(xmlHttp.responseText);
				generateTable(xmlHttp.responseText);
			} else {
				if (xmlHttp.readyState == 4 && xmlHttp.status!=200) {
					console.log("readyState: " + xmlHttp.readyState + " status: " + xmlHttp.status);
					main.innerHTML = "Error could not retrieve data from: " + url;
				}
			}
		}
		
		xmlHttp = new XMLHttpRequest();
		// ToDo Remove hard coded URL
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
<img src="images/sulogo.png" width="213" height="48" alt="StumbleUpon" /> 
<h2 class="centered">Display &amp; Filter Tag Data using AJAX</h2>
</header>
<div id="filter">
<form id="filterform" name="filterform" method="get" action=""><table width="200" border="0" cellspacing="1">
  <tr>
    <td>Site:</td>
    <td><?php
    // include the pull down menu to select site
    require_once "selectMenu.php";
    ?></td>
  </tr>
  <tr>
    <td>Rating:</td>
    <td><select name="rating" id="rating" onchange="filter()">
      <option value="all">All</option>
      <option value="0">None</option>
      <option value="1">Thumbs Up</option>
      <option value="-1">Thumbs Down</option>
    </select></td>
  </tr>
  <tr>
    <td>Count:</td>
    <td><label>
      <input name="count" type="radio" id="tagCount" value="tagCount" checked="checked" onchange="filter()" />
      Total Tags
    </label>
    <br />
    <label>
      <input type="radio" name="count" value="people" id="peopleCount" onchange="filter()"/>
      People
    </label></td>
  </tr>
  <tr>
    <td>Count:</td>
    <td><label>
      <input name="minimum" type="text" size="5" onblur="filter()"/>Minimum</label></td>
  </tr>
</table>
</form>
</div>
<div id="main">
</div>
<footer></footer>
</body>
</html>
