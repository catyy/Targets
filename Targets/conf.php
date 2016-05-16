<?php
session_start();
// include ERPLY API class
include("EAPI.class.php");


// Initialise class
$api = new EAPI();

// Configuration settings


$api->clientCode = "";
$api->username = "";
$api->password = "";

$api->url = "https://".$api->clientCode.".erply.com/api/";

$months = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");

