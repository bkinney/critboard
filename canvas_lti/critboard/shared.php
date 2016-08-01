<?php
session_start();
include "/www/canvas/sitepaths.php";
 $me = $canvasphp . "shared.php";
$tokentype="context";	 
error_reporting(E_ERROR & ~E_WARNING);
ini_set('display_errors', 1);
$testing = false;
$secret = array("table"=>"blti_keys","key_column"=>"oauth_consumer_key","secret_column"=>"secret","context_column"=>"context_id");
$include= $canvasphp . "critboard/common.php";
include $canvasphp . "all_purpose.php";
	
echo $include;
exit();
?>
