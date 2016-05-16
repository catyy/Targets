<?php
session_start();
// include ERPLY API class
include("EAPI.class.php");


// Initialise class
$api = new EAPI();

// Configuration settings
//$api->clientCode = "787";
//$api->username = "RobinDev";
//$api->password = "87affefe3";

$api->clientCode = "20501";
$api->username = "apitestuser";
$api->password = "B3KõMlBÜwLäHfCvG";

$api->url = "https://".$api->clientCode.".erply.com/api/";

$months = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");

