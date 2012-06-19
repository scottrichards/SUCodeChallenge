<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Display Tag Data using Ajax</title>
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

window.onload=filter;

var sortColumnName = 'tag';
var sortOrder = 'ASC';

// remove the specified html node from the dom by navigating up to its parent node
function removeElement(node)
{
	node.parentNode.removeChild(node);
}

/* returns the <img /> tag based on the rating -1 = thumbs down, 0 = no rating, 1 = thumbs up
*/
function returnRatingImage(rating)
{
	if (rating == 1)
	 return '<img src="images/thumbsUp.gif" width="25" height="25" />';
	if (rating == -1)
	 return '<img src="images/thumbsDown.gif" width="25" height="25" />';
	else 
		return'0'; 
}

/* returns the link to appropriate triangle image to represent the sort state of the column headers
 * sortOrder -> "ASC" = Ascending, "DESC" = Descending
 * sortOn -> true if the column is controling the sort order
 */
function returnSortImage(sortOrder,sortOn)
{
	if (sortOn) {
		if (sortOrder == 'ASC')
			return "images/upArrowSolid.gif";
		else
			return "images/downArrowSolid.gif";
	} else {
		if (sortOrder == 'ASC')
			return "images/upArrow.gif";
		else
			return "images/downArrow.gif";
	}
}

/* sort the column based on the specified columnName
 * updates sorted column name, and sort order (if it is the current sorting column)
 * then we regenerate json data based on updated SQL query appending "ORDER BY sortColumnName sortOrder;" 
 */
function sortColumn(columnName){
	if (columnName == sortColumnName) {
		if (sortOrder == 'ASC')
			sortOrder = 'DESC';
		else 
			sortOrder = 'ASC';
	} else
		sortColumnName = columnName;
	filter();
}

/* updates the image in the table column header to reflect the current sorting order state
 * columnName -> name of the column to update the arrow image 
 */
function updateArrowImage(columnName)
{
	var sortColId = columnName + 'Col';
	imgElem = document.getElementById(sortColId);
	imgElem.setAttribute("src",returnSortImage(sortOrder,columnName == sortColumnName));
}

/* updates the image in all of the table column headers to reflect the current sorting order state
 */
function updateArrowImages()
{
	updateArrowImage('rating');
	updateArrowImage('tag');
	updateArrowImage('count');
}

/* generates an html table with list of ratings, tags, and their counts 
 * responseText -> JSON object returned from SQL query contains data as follows:
 
 [{"rating":"-1","tag":"yoga","count":"4"},{"rating":"1","tag":"yoga","count":"2"},{"rating":"-1","tag":"zen-stories","count":"1"}]
 */
function generateTable(responseText)
{
	// this is the div with id 'main' where we want to insert the table
	var main = document.getElementById("main");
	// make sure the return value is converted to JSON object
	jsonObj = JSON.parse(responseText);
	var numRows = jsonObj.length;
	main.innerHTML = '\n<br />';	// rewrite innerHTML so we delete everything
	var tableHTML = '<table id="tableId" border="1" cellpadding="2" cellspacing="0">\n';
	tableHTML += '<tr><th>Rating<img id="ratingCol" src="images/upArrow.gif" onclick="sortColumn(\'rating\')"/></th><th>Tag<img id="tagCol" src="images/upArrowSolid.gif" onclick="sortColumn(\'tag\')"/></th><th>Count<img id="countCol" src="images/upArrow.gif" onclick="sortColumn(\'count\')"/></th></tr>'; 
	for (i=0;i<jsonObj.length;i++) {
		tableHTML += '<tr><td>' + returnRatingImage(jsonObj[i].rating) + '</td><td>' + jsonObj[i].tag + '</td><td>' + jsonObj[i].count + '</td></tr>';
//		console.log("Tag: " + jsonObj[i].tag + "Count: " + jsonObj[i].count);
	}
	tableHTML += "</table>";
	main.innerHTML += tableHTML;
	updateArrowImages();	// after we regenerate html we need to update state of arrows
}

/* returns the url path to the currently running file minus its filename
 */
function getCurrentURLPath(){
	var url = location.href;
	var urlPath = url.substr(0,url.lastIndexOf('/'));
	return urlPath;
}

/* make an xml http request that does SQL query based on the filter criteria 
 * then generate an html table with resulting data 
 */
function filter() {
	if (window.XMLHttpRequest) {
		/* Handler to process xml http responses */
		function processResult() {
			// request is complete and successful
			if (xmlHttp.readyState == 4 && xmlHttp.status==200) {
//			console.log(xmlHttp.responseText);
				generateTable(xmlHttp.responseText);	// output the table
			} else {
				if (xmlHttp.readyState == 4 && xmlHttp.status!=200) {	// request is complete but failed
					console.log("readyState: " + xmlHttp.readyState + " status: " + xmlHttp.status);
					main.innerHTML = "Error could not retrieve data from: " + url;
				}
			}
		}
		
		xmlHttp = new XMLHttpRequest();
		var url = getCurrentURLPath();	// get url path to current file
		url += '/sql/jsonData.php';		// this php file does SQL qeuery and returns data formatted in JSON
		url += "?rating=" + document.forms["filterform"].elements["rating"].value;
	  url += "&count=" + ((document.forms["filterform"].elements["tagCount"].checked) ? "tagCount" : "people");
		url += "&siteName=" + document.forms["filterform"].elements["siteNameSelectMenu"].value;
		url += "&orderBy=" + sortColumnName;
		url += "&sortOrder=" + sortOrder;
		if (document.forms["filterform"].elements["minimum"].value > 0 )
			url += "&minimum=" + document.forms["filterform"].elements["minimum"].value;
		console.log("url: " + url);
		xmlHttp.open('GET', url, true); 
		xmlHttp.onreadystatechange = processResult;		// handle result of xmlhttp request
		xmlHttp.send( null);
	}
}
</script>
</head>

<body>
<header>
<img src="images/sulogo.png" width="213" height="48" alt="StumbleUpon" style="float:left"/>
<a href="index.html"><img src="images/home.gif" style="float:right" /></a>
<h2 class="clearFloat centered">Display &amp; Filter Tag Data using AJAX</h2>
</header>
<div id="sidePane">
  <div id="filter">
  <form id="filterform" name="filterform" method="get" action=""><fieldset><legend>Filter</legend><table width="200" border="0" cellspacing="1">
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
      <td>Minimum:</td>
      <td><input name="minimum" type="range" value="0" min="0" max="100" step="1" size="5" onchange="filter()"/></td>
    </tr>
  </table>
  </fieldset>
  </form>
  </div>
  <div id="instructions">
    <p>Use the &quot;Site&quot; pull down menu to select the site to display tags from or All to display tags for all sites.</p>
    <p>Use the &quot;Rating&quot; menu to filter by the specified rating.</p>
    <p>Use &quot;Count&quot; to show the total number of tags or number of people for the specified rating.</p>
    <p>Use &quot;Miminimum&quot; to only display results equal or greater than the specified minimum.</p>
    <p>The solid triangle in the table header indicates which column the data is sorted by. Click on the solid triangle to change the sort order from ascending to descending. Click on the other triangles in the table column headers to sort the data by that column. </p>
  </div>
</div>
<div id="main">
</div>
<footer></footer>
</body>
</html>
