<?php
header('Content-Type: image/jpeg');


  $im = imagecreate(400,300);
$bg = imagecolorallocate($im,255,255,255);
$text_color = imagecolorallocate($im, 0,0,0);
imagestring($im,3,15,50,"Critboard can not display this file.",$text_color);
imagestring($im,3,15,100,"Click to view or download",$text_color);
imagestring($im, 5, 15, 140,  $_GET['preview'], $text_color);



imagepng($im);
?>