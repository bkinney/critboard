<?php
 if($_REQUEST['folder']){
	$image_list = scandir($_REQUEST['folder']); 
 }else{
	 $secret = array("table"=>"key_token_view","key_column"=>"oauth_consumer_key","secret_column"=>"secret","context_column"=>"context_id");
/*error_reporting(E_ALL);
ini_set('display_errors', 1);*/
include "../canvas_dance_include.php";
	
/*	session_start();

	$token = $_SESSION['token'];
	$domain = 'udel.instructure.com';
	include "/www/canvas/canvasapi.php";
	
	$api = new CanvasAPI($token,$domain);
	*/
	if($_REQUEST['grades']){
		foreach($_REQUEST['grades'] as $grade){
			$api->post_canvas($grade,"PUT",null);
		}
	}else if($_REQUEST['assignment_id']){
		if($isAdmin || $context->isInstructor()){
			$uri = "/api/v1/courses/". $context->info['custom_canvas_course_id'] ."/assignments/".$_REQUEST['assignment_id']."/submissions";
			$submissions = $api->get_canvas($uri,true);
			$image_list = array();
			//submission['attachments'][n]['url']
			foreach($submissions as $submission){
				$sid = $submission['user_id'];
				foreach($submission['attachments'] as $attachment){
					
					$image_list[]=$attachment['url'];
			
				}
			}
		}else{
			$uri = "/api/v1/courses/". $context->info['custom_canvas_course_id'] . "/gradebook_history/feed?course_id=". $context->info['custom_canvas_course_id'] . "&assignment_id=" . $_REQUEST['assignment_id'];
			$submissions = $api->get_canvas($uri,true);
			//echo "<pre>" . print_r($submissions) . "</pre>";
			$allgrades = array();
			function sortByCC($a, $b) {
   				 return strcmp($b['grade'],$a['grade']);
			}
			foreach($submissions as $submission){
				if($submission['user_name']== "Test Student") continue;
				$name=$submission['user_name'];
				$id = $submission['user_id'];
				$grade = $submission['current_grade'];
				$allgrades[$id]['grade']=$grade;
				$allgrades[$id]['name']=$name;
				
			}
			uasort($allgrades,"sortByCC");
		}
	}
 }

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Critboard</title>


<link href="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/themes/redmond/jquery-ui.css" rel="stylesheet" type="text/css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.15/jquery-ui.min.js"></script>
 <style>
.sortable img{
	
	margin:5px 5px 0 5px;
	max-width:200px;
	max-height:133px;
	overflow:hidden;
}
p{
	text-align:right;
	width:50px;
}
.sortable a{
	cursor:pointer;
	font-size:small;
	margin:0;
	display:none;
}
.sortable li:hover a{
	background:url(Full_Screen.png)  no-repeat 5px;
width:25px;
	};
#critboard{
	height:90%;	
}
#sortable li.label{display:none};

ul.sortable li a{
background:url(/test/Full_Screen.png)  no-repeat 5px;
width:25px;

color:#248ef4;	
}


  .sortable{ list-style-type: none; margin: 0; padding: 0;
   
    }
  .sortable li { margin: 3px 3px 3px 0; padding: 1px; float: left; 
  max-width: 220px;
   max-height: 400px; }
   .sortable li.label{
		max-width:100%;
		width:100%; 
		background-image: url(/test/shelf.png);
		height: 20px;
		padding-top: 9px;  
		text-align: center;
	}
   
  </style>
  <script>
  $(function() {
    
  
 	//$( "#sortable" ).sortable();
	//$("#critboard").load("images.php","",function(){
		
    //$( "#sortable" ).disableSelection();
	//});
	$("#dialog").dialog({
		autoOpen:false,
		width:'auto',
		height:'auto'
	});
	
});
var active = null;
function openFull(img){
	var li = $("img[src='"+img+"']:visible").parent();
	active = li;
	var arr = li.prevUntil(".label");
	var n = arr.length;
	
	var value = $(arr[n-1]).prev().data("score");
	
	$("input[name=score]").prop('checked', false);
	$("input[name=score][value=" + value + "]").prop('checked', true);
	$("#dialog img:first").attr('src',img);
	$("#dialog").dialog("open");
}
function setCat(input){
	
	var label=("li[data-score="+input+"]:visible");
	active.detach().insertAfter(label);
}
function saveCrit(){
	var list = $("#critboard").html();
	$("#critlist").val(list);
	$("#save").submit();
}
function setLabels(input){
	var labels = $(input).val().split(",");
	$("#sortable li.label").each(function(index, element) {
		if(labels.length==0 && index>0){
			$(this).remove();
		}else{
        	$(this).text(labels.shift());
		}
    });
	for(i=0;i<labels.length;i++){
		$("#sortable li.label:last").after('<li class="label">' + labels[i] + '</li>');
	}
	
}
/*function newRubric(input){
	var labels = $("#labels").val().split(",");
	$("#critboard").prepend("<ul></ul>");
	for(i=0;i<labels.length;i++){
		var clone = $("#sortable").clone(true);
		var tabid= "tab" +i;
		var listid="sortable" + i;
		clone.attr("id",listid);
		$("#critboard ul:first").append('<li><a href="#' + 'tab'+i + '">' + labels[i] + '</a></li>');
		$("#critboard").append('<div id="'+tabid+'" title="' + labels[i]+'" ></div>');
		$("#"+tabid).html(clone);
		$("#"+listid).sortable();
	}
	$("#sortable").hide();
	$("#critboard").tabs({heightStyle:"fill"});
}*/
function fixHeight(str){
	var div = $(str);
	
	var newh = div.find("li:last").position().top + 100;
	div.css("height",newh+"px").css("background-image","url(/test/wood3.png)");
}
function newRubric(input){
	var labels = $("#labels").val().split(",");
	$("#critboard").prepend("<ul></ul>");
	for(i=0;i<labels.length;i++){
		var clone = $("#sortable").clone();
		var tabid= "tab" +i;
		var listid="sortable" + i;
		clone.attr("id",listid);
		$("#critboard ul:first").append('<li><a href="#' + 'tab'+i + '">' + labels[i] + '</a></li>');
		$("#critboard").append('<div id="'+tabid+'" title="' + labels[i]+'" ></div>');
		$("#"+tabid).html(clone);
		//fixHeight("#"+tabid);
		$("#"+listid).sortable({
			change: function(event,ui){
				var tab = ui.item.parent().parent().attr("id");
				//alert(tab);
				fixHeight("#"+tab);
			}
		});
	}
	$("#sortable").hide();
	$("#critboard").tabs({
	  show: function( event, ui ) {
		  //alert("open");
		  fixHeight("#tab" +ui.index);
		  }
	});
}
var grades = new Array();

function addSubmission(sid,grade,comment){
	var uri="<?php echo $uri ?>/" + sid + "?submission[posted_grade]="+grade+"&comment="+comment;
	grades.push(uri);
}
function compileComments(sid){
	$("img").filter('[data-sid="'+sid +'"]').each(function(index, element) {
		var comment ="";
		
        comment += $(this).closest("div").attr("title").text() + ": " + $(this).closest("li").prev(".label").data("score") + "  ";
    });
}
function compileGrades(){
	$("#sortable li.label").each(function(index,element) {
        var grade = $(element).text();
		$(element).nextUntil("li.label","li").each(function(i,e) {
			
            var sid = $(e).children("img").data("sid");
			var comment = compileComments(sid);
			addSubmission(sid,grade,comment);
        });
    });
	var obj = new Object();
	obj.grades=grades;
	$("#success").load("index.php",obj);
}
  </script>
</head>

<body>
<?php 

?>
<div id="success"></div>
<?php if($_REQUEST['assignment_id']){
	echo '<h2>Critboard (Student View) for '.$submissions[0]['assignment_name'] . '</h2><h3>All Grades</h3>';
	if($context->isInstructor()==false && !$isAdmin){
		foreach($allgrades as $student){
			echo "<p>" . $student['grade'] . "</p>";
		}
		echo "</body></html>";
		exit();
	}else{
	echo 'Rubric Elements: <input type="text" placeholder="comma delimited" id="labels"  size="50">
<input type="button" onclick ="newRubric()"; value="Create Rubric Tabs">
<input type="button" onClick="saveCrit()" value="Save Sequence">
<form id="save" action="saveAs.php" method="post">
  <input id="critlist" name="critlist" type="hidden">





</form>';
	}
}else{
	$uri = "/api/v1/courses/". $context->info['custom_canvas_course_id'] ."/assignments";
	
	$assignments = $api->get_canvas($uri);
	
	echo '<form action="' . $_SERVER['PHP_SELF'] . '">
	Select an assignment: <select name="assignment_id">';
	foreach($assignments as $assignment){
		//print_r($assignment['submission_types']);
		if(in_array('online_upload',$assignment['submission_types'])){
			echo '<option value="' .$assignment['id'] . '">' . $assignment['name'] . '</option>';
		}
	}
	echo '</select><input type="submit"></form>';
}
?>


<hr/>

<div id="dialog"></div>
<div id="critboard">
<ul id="sortable" class="sortable">
<?php if($_REQUEST['assignment_id']):?>
<li class="label" data-score="5">Top</li>
<li class="label"  data-score="3">Middle</li>

<?php endif ?>
<?php

foreach ($image_list as $img){
	
		$path =$_REQUEST['folder'] ? 'http://apps.ats.udel.edu/test/' . $_REQUEST["folder"] . '/' . $img : $img;
		//echo '<li><img src="' . $path . '" data-sid="' . $count . '" ><a title="open full" onclick="openFull(\'' . $path . '\')">&nbsp;</a></li>';
		echo '<li><img data-sid="' . $sid . '" src="' . $path . '"><a title="open full" onclick="openFull(\'' . $path . '\')">&nbsp;</a></li>';
	
}
if($_REQUEST['assignment_id']) echo '<li class="label"  data-score="1">Bottom</li>';
?>
</ul>
</div>

</body>
</html>
