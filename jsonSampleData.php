<?php
// prevent caching !
header("Expires: 0"); 
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); // Add some IE-specific options
header("Cache-Control: post-check=0, pre-check=0", false); // For HTTP/1.0 header("Pragma: no-cache");


$menu = array();
$menu[] = array('type' => "appetizer",
								 'dish' => "Chicken Soup");
$menu[] = array('type' => "main",
								'dish' => "Garlic Beef");

header('Content-type: application/json');
//$json = new Services_JSON();
$json = json_encode($menu);
print "$json";
?>