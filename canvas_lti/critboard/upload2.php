<?php
  $li = $_POST['critlist'];
  $arr = explode('src="',$li);
  array_shift($arr);
  //print_r($arr);
  
  foreach($arr as $img){
    
    $newarr = explode('" data-full="',$img);
   // echo $newarr[0];
    $thumb = $newarr[0];
   $arrr=explode('"',$newarr[1]);
   $src = $arrr[0];
  // echo $src;
    $li = str_replace($thumb,$src,$li);
    
  }
if(empty($_POST['folder'])){
header('Content-Disposition: attachment; filename="Critboard:' . date("j-m-y")  . '.html"');
}else{




	include "/www/canvas/canvasapi.php";
	function canvas_upload_critboard($data,$api){
		$folderpath = "/critboard/".$_POST['folder'];
		$handle2 = fopen("/home/bkinney/writable/temp.html","w");
		if(!$handle2)echo "failed to open";
		$file = array();
		$file['size']= fwrite($handle2,$data);
		
		$file['tmp_name']="/home/bkinney/writable/temp.html"; 
		$file['name']='temp.html';
		$file['type']="text/html";
		$courseid = $_REQUEST['courseid'];
		//print_r($file);
		$uri='/api/v1/courses/'.$courseid .'/files?name='.date("j-m-y-g:i") . '.html&parent_folder_path='.$folderpath;
		
		fclose($handle2);
		
		return $api->upload($uri,$file);
		
	}
//ob_start();//start buffering content
}
$html='
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Saved Critboard ' . date("j-m-y") . '</title>


<link href="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/themes/redmond/jquery-ui.css" rel="stylesheet" type="text/css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.15/jquery-ui.min.js"></script>
<link href="//apps.ats.udel.edu/canvas/critboard/critboard.css" rel="stylesheet" type="text/css">
  <script>
  $(function() {
    
  
 
	
	$("#dialog").dialog({
		autoOpen:false,
		width:800,
		height:600
		
	});
	$("#critboard").tabs({
  show: function( event, ui ) {
	  //alert("open");
	  fixHeight("#tab" +ui.index);
	  }
	  });
	
	
});

  function fixHeight(str){
	if(!str){
		str = $("li.ui-state-active a").attr("href");
	}
	var div = $(str);
	//div.addClass("tab");
	div.find("li[data-score=1]").appendTo("div.ul");
	var newh = div.find("li:last").position().top - div.position().top;

	div.css("height",newh+"px").css("background-color","gray").css("padding","1em 0 0 0");
}
function openFull(src,thumb,type){
	
		//alert(tag);
		var li = $("img[src="+thumb+"]:visible,img[src^=img_error]:visible").parents("li");
		active = li;
		//var c = active.children("img:first").attr("class");
		var arr = li.nextAll(".label");
		var n = arr.length;
		
		var value = $(arr[n-1]).prev().data("score");
		var value = arr.data("score");
		$("input[name=score]").prop("checked", false);
		$("input[name=score][value=" + value + "]").prop("checked", true);
		//
		if(type=="video"){
			var vid = $("<video height=\"100%\" width=\"100%\" controls></video>");
			vid.attr("src",src);
			vid.attr("poster",thumb);
			vid.data("thumb",thumb);
				  $("#dialog").html(vid);
				
				 $("#dialog").dialog("open");
				
			  
  			return;
		}else {//all others- applications have a doctored thumb
		//this happens if the media is not a video
		$("#dialog").html(active.html());
		$("#dialog a:last").remove();
		$("#dialog img:first").attr("width","100%").attr("height","100%").data("thumb",thumb);
		if(type=="image"){
			$("#dialog img:first").attr("src",src);//show the full image here
			
		}
		}
		
		//if(c != undefined){
			//$("#dialog img:first").attr("class",c);
		//}
		
		$("#dialog").dialog("open");

}
  </script>
     
</head>

<body onload="fixHeight(false)"><div id="dialog"></div>'

. $li .
'</body>
</html>';

session_start();
	if(!isset($_SESSION['domain'])){
		die("your session has ended. Please launch Critboard again in a new window, and then try again.");
	}
	$token = $_SESSION['token_arr'][$_POST['tokentype']];
$api = $api = new CanvasAPI($token,$_SESSION['domain'],$_SESSION['_basic_lti_context']['custom_canvas_user_id']);
if($api->ready){
	
echo  canvas_upload_critboard($html,$api);
}else{

	myecho($api->error);
	myecho(print_r($_SESSION));
}

?>