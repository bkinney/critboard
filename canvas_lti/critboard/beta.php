<?php
/* dependent files
../all_purpose, etc
common.php
upload.php
saveAs2.php
putgrades.php
image_error.php
*/
session_start();
include "/www/canvas/sitepaths.php";
 $me = $canvashtml . "critboard/index.php";
$tokentype="context";	 
error_reporting(E_ERROR & ~E_WARNING);
ini_set('display_errors', 1);
$testing = false;
$secret = array("table"=>"blti_keys","key_column"=>"oauth_consumer_key","secret_column"=>"secret","context_column"=>"context_id");
$include= $canvasphp . "critboard/common.php";
if($_GET['include']) $include = $canvasphp . $_GET['include'];
include $canvasphp . "all_purpose.php";
	
//echo $include;
exit();
?>