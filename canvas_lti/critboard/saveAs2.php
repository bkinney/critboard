<?php
header('Content-Disposition: attachment; filename="Critboard:' . date("j-m-y")  . '.html"');
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Saved Critboard <?php echo date('j-m-y') ?></title>

 
<link href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/themes/redmond/jquery-ui.css" rel="stylesheet" type="text/css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.15/jquery-ui.min.js"></script>
<link href="https://apps.ats.udel.edu/canvas/critboard/critboard.css" rel="stylesheet" type="text/css">
  <script>
  function fixHeight(str){
	var div = $(str);
	//div.addClass("tab");
	div.find("li[data-score=1]").appendTo("div.ul");
	var newh =10+  div.find("li.last").position().top - div.position().top;

	div.css("height",newh+"px").css("background-color","gray").css("padding","1em 0 0 0");
}
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
function openFull(src,thumb,type){
	
		//alert(tag);
		var li = $("img[src='"+thumb+"']:visible,img[src*=img_error]:visible").parents("li");
		active = li;
		//var c = active.children("img:first").attr("class");
		var arr = li.nextAll(".label");
		var n = arr.length;
		
		var value = $(arr[n-1]).prev().data("score");
		var value = arr.data("score");
		$("input[name=score]").prop('checked', false);
		$("input[name=score][value=" + value + "]").prop('checked', true);
		//
		if(type=="video"){
			var vid = $('<video height="100%" width="100%" controls></video>');
			vid.attr("src",src);
			vid.attr("poster",thumb);
			vid.data("thumb",thumb);
				  $("#dialog").html(vid);
				 // $("#dialog img:first").attr('src',src).data("thumb",thumb).hide();
				 //$("#dialog video:first").attr("width","100%").attr("height","100%");
				 $("#dialog").dialog("open");
				
			  
  			return;
		}else {//all others- applications have a doctored thumb
		//this happens if the media is not a video
		$("#dialog").html(active.html());
		$("#dialog a:last").remove();
		$("#dialog img:first").attr("width","100%").attr("height","100%").data("thumb",thumb);
		if(type=="image"){
			$("#dialog img:first").attr('src',src);//show the full image here
			
		}
		}
		
		//if(c != undefined){
			//$("#dialog img:first").attr('class',c);
		//}
		
		$("#dialog").dialog("open");

}
  </script>
</head>

<body>
<div id="dialog">
</div>
<?php echo $_POST['critlist'] ?>
</body>
</html>
