<?php
//remove all references to sitepaths, this has to be set in the including page
//require_once("/www/git/canvas/sitepaths.php");
	 
/*	 $secret = array("table"=>"blti_keys","key_column"=>"oauth_consumer_key","secret_column"=>"secret","context_column"=>"context_id");*/
include "../all_purpose.php";
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 1);


	

	if($_REQUEST['assignment_id']){
		if($_SESSION['isInstructor'] || $isAdmin){
		  //console.log("instructor");
			$aid = $_REQUEST['assignment_id'];
			$uri = "/api/v1/courses/". $context->info['custom_canvas_course_id'] ."/assignments/".$_REQUEST['assignment_id']."/submissions?include[]=user";
			$debug = $uri;
			$submissions = $api->get_canvas($uri,true);
			$image_list = array();
			$users = array();
			//submission['attachments'][n]['url']
			foreach($submissions as $submission){
				  $sid=$submission['user_id'];
				foreach($submission['attachments'] as $attachment){
					$sname = str_replace("'","\'",$submission['user']['sortable_name']);
					$image_list[]=array(
					'src'=>$attachment['url'],
					'display_name'=>$attachment['display_name'],
					'sname'=>$sname,
					'content-type'=>$attachment['content-type'],//'video/mp4'
					'thumb'=>$attachment['thumbnail_url'],
					'preview'=>empty($attachment['preview_url']) ? $attachment['url'] : 'https://' . $domain . $attachment['preview_url'],
					'sid'=>$sid);
					$sname = str_replace("'","\'",$submission['user']['sortable_name']);
					$users[$sid] = '"' .$sname . '",'  . $submission['user']['login_id'];
					//$users[$sid] = '"' . $submission['user']['sortable_name']. '",'  . $submission['user']['login_id'];
			
				}
			}
		}else{
			
			$uri = "/api/v1/courses/". $context->info['custom_canvas_course_id'] . "/gradebook_history/feed?course_id=". $context->info['custom_canvas_course_id'] . "&assignment_id=" . $_REQUEST['assignment_id'];
			$submissions = $api->get_canvas($uri,true);
			//echo "<pre>" . print_r($submissions) . "</pre>";
			$allgrades = array();
			function sortByCC($a, $b) {
   				 return $b['grade']*1>$a['grade']*1;
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


?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Critboard</title>


<link href="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/themes/redmond/jquery-ui.css" rel="stylesheet" type="text/css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.15/jquery-ui.min.js"></script>
<?php echo $keepalive ?>
 <style>
.logout{
	background-color: rgb(252, 229, 140);
padding: 2;
text-align: right;
font-family:Arial, Helvetica, sans-serif;
font-size:70%;
color:#559;name=
border-bottom: double;
margin-bottom: 5px;
 }
 .logout button{
	margin:0 1px 0 5px;

 }
</style>
 
 <style>
/* html{
	 overflow:auto;
 }*/
 #studentview{
	display:flex;
	flex-wrap:wrap;
	flex-direction:row;
	
 }
 #studentview div{margin-right:20px; text-align:center;width:45%}
 
  body{min-height:100%;overflow:auto}
  .placeholder{height:133px;width:5px;background-color:white; padding:0 5px;}
 #saveme{
	 height:100%;
	 background-color: gray;
 }
 #saveme h3{
	 color: white;
  width: 650px;
  padding: 40px;
  font-size: 200%;
 }
 #footer {
background-color: #ffe;
text-align: center;
border-top: thick solid #039;
padding: 0;
width: 100%;
position:fixed;
bottom:0px;

}
#dialog{
	max-width:90%;
	
}
#dialog img{
	max-width:100%;
}
#pform {
	margin:0;
}
#pform input[type=button]{margin-left:5em}
 #btns{display:none}
 #success{
	
margin-bottom: 30px;
text-align: center;
font-family: arial, helvetica, sans-serif;
border: double;
padding: 10px;
display:none;
 }
 #unload{
	background-color: rgb(252, 229, 140);
padding: 2;
text-align: right;

border-bottom: double;
margin-bottom: 5px;
 }
.sortable img, .sortable object,.sortable embed, .sortable video{
	
	margin:5px 5px 0 5px;
	max-width:200px;
	max-height:133px;
	overflow:hidden;
}
#critboard p{
	text-align:right;
	width:50px;
}
ul.sortable{padding-left:0; margin-top:0;list-style-type:none;height:100%}/*overflow:auto;*/
.sortable li a.hash{
	cursor:pointer;
	width:25px;
	margin:0;
	display:block;
	background:none;
}
.sortable li:hover a{
	background:url(https://apps.ats.udel.edu/test/Full_Screen.png)  no-repeat 5px;

	}
	#sortable{background-color:gray;overflow:visible;}
#sortable  li a.hash{
	 width:0;
 }

#sortable li.label{display:none}

/*#critboard div .ui-tabs .ui-tabs-panel{background:gray; padding:0}

.ui-widget-content{background:none}*/
  .sortable{ list-style-type: none; margin: 0; padding: 0;
   
    }
  .sortable li { margin: 3px 3px 20px 0; padding: 3px; float: left;
  width: 220px;
  height: 150px; }
   .sortable li.label{
		max-width:100%;
		width:100%;
		background-image: url(https://apps.ats.udel.edu/test/shelf.png);
		height: 10px;
		padding:0;
		text-align: center;
	}
	.sortable li.label.last{height:15px}
   .r90{
    -ms-transform: rotate(90deg); /* IE 9 */
    -webkit-transform: rotate(90deg); /* Chrome, Safari, Opera */
    transform: rotate(90deg);
}
  .r180{
    -ms-transform: rotate(180deg); /* IE 9 */
    -webkit-transform: rotate(180deg); /* Chrome, Safari, Opera */
    transform: rotate(180deg);
}
  .r270{
    -ms-transform: rotate(270deg); /* IE 9 */
    -webkit-transform: rotate(270deg); /* Chrome, Safari, Opera */
    transform: rotate(270deg);
}
  </style>
  <script>
  function findMax(){
    
    var maxh = 0;
    var maxw = 0;
    $("#sortable").find("img").each(function(){
      maxh = Math.max($(this).height(),maxh);
      maxw = Math.max($(this).width(),maxw);
    });
    console.log(maxh + "," + maxw);
    $(".sortable li:not(.label)").css("width",maxw).css("height",maxh);
  stretchSortable();
  }
  function stretchSortable(){
     var div = $("#sortable");
    if(div.find("img").length){
     
      var newh =134+  div.find("img:last").position().top - div.position().top;
     
      console.log(newh);
  	  div.css("height",newh+"px").css("background-color","gray").css("padding","1em 0 0 0");
    }
  }
  $(document).ready(function(e) {

    // parent.postMessage(JSON.stringify({subject: 'lti.frameResize', height: "1000px"}), '*');
  
 	//$( "#sortable" ).sortable();
	//$("#critboard").load("images.php","",function(){
		
    //$( "#sortable" ).disableSelection();
	//});
$("#dialog").dialog({
		autoOpen:false,
		width:800,
		height:600,
		modal:true,
		title:'<form id="pform"><input name="score" onchange="setCat(5)" type="radio" value="5">top<input name="score" onchange="setCat(3)" type="radio" value="3">middle<input name="score" onchange="setCat(1)" type="radio" value="1">bottom<input type="button" value="delete" onclick="deleteImage()"> <a href="#" onclick="rotateImage()">rotate</a></form>'
		
		
	});

	
	
});
var active = null;
function addTab(str){
	//update template in case images have been deleted
		var clone = $("#sortable").clone();
clone.show();
	$("#critboard").tabs("option","panelTemplate",'<div><ul class="sortable">' + clone.html() + '</ul></div>');
	var label = str ? label : $("#labels").val();
	if(!label){
		alert("please enter a category label");
		return;
	}
	if(label.indexOf(",")!=-1){
		alert("please add extra tabs one at a time (new tab names may not contain commas).");
		return;
	}
	var tabname = "#tab" + $("#critboard div").length;
	$("#critboard").tabs("add",tabname,label);
	$("#labels").val("");
	
	$(tabname).attr("title",label);
	$(tabname).children("ul:first").sortable({
		
		
			change: function(event,ui){
				var tab = ui.item.parent().parent().attr("id");
				//alert(tab);
				fixHeight("#"+tab);
			}
		});
	fixHeight(tabname);
	
}
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
		$("#dialog img:first").attr("width","100%").data("thumb",thumb);
		if(type=="image"){
			$("#dialog img:first").attr('src',src);//show the full image here
			
		}
		}
		
		//if(c != undefined){
			//$("#dialog img:first").attr('class',c);
		//}
		
		$("#dialog").dialog("open");

}
function setCat(input){
	
	var label=("li[data-score="+input+"]:visible");
	active.detach().insertBefore(label);
	fixHeight(active.parents("div")[0]);
	$("#dialog").dialog("close");
}
function saveCrit(toCanvas){
	var list = $("#saveme").html();
	var helper = "<?php echo $canvashtml?>critboard/upload2.php";
	if(toCanvas){
		var obj = new Object();
		//$("#save").attr("action","upload2.php").attr("target","_blank");
		obj.folder="<?php echo $_REQUEST['assignment_id'] ?>";
		obj.critlist = list;
		obj.courseid = "<?php echo $context->info['custom_canvas_course_id'] ?>";
		obj.tokentype="<?php echo $tokentype ?>";
			console.log(obj.folder);
		console.log(obj.courseid);
		$("#success").text("loading...");
		$("#success").load(helper,obj).show();
	}else{
		$("#critlist").val(list);
		$("#save").submit();
	}
}
/*function setLabels(input){
	
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
	
}*/
function fixHeight(str){
	var div = $(str);
	//div.addClass("tab");
	div.find("li[data-score=1]").appendTo("div.ul");
	var newh =10+  div.find("li.last").position().top - div.position().top;

	div.css("height",newh+"px").css("background-color","gray").css("padding","1em 0 0 0");
}
function toggleGradeExport(){
	var button = $("input[value='Compile Grades']");
	button.show();
	var button2 = $("input[value='Export CSV']");
	button2.show();
	console.log($("#tab0 ul li img").length);
	$("#tab0 ul li img").each(function(index, element) {
		
        var sid=$(this).attr("data-sid");
		
		if($(this).parent().siblings().has("img[data-sid='"+sid + "']").length>0){
			console.log($(this).parent().siblings().has("img[data-sid='"+sid + "']").html());
			button.hide();
			return sid;
		}
	
    });
}
function newRubric(input){
	$("#labels").attr("placeholder","single tab label");
	var labels = $("#labels").val().split(",");
	if(labels[0]==""){
		alert("please enter a category label");
		return;
	}
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
			
			forceHelperSize:true,
			
			change: function(event,ui){
				
				var tab = ui.item.parent().parent().attr("id");
				//alert(tab);
				fixHeight("#"+tab);
			},
			stop:function(event,ui){
				var tab = ui.item.parent().parent().attr("id");
				//alert(tab);
				fixHeight("#"+tab);
			}
		});
	}
	clone =$("#sortable").clone();
	$("#sortable").hide();
	$("#critboard").tabs({
  show: function( event, ui ) {
	  //alert("open");
	  fixHeight("#tab" +ui.index);
	  
	  },
	  panelTemplate:'<div><ul class="sortable">' + clone.html() + '</ul></div>'
});//{heightStyle:"fill"}
	//fixHeight("#tab0");
	$("#labels").val("");
	$("#tabsettings").attr("value","Add Tab").attr("onclick","addTab()");
	$("#btns").show();
	toggleGradeExport();
	
}
var grades;
var rubrics="";
var users = new Array();
<?php
//write out the users array in a format useful to js
foreach($users as $key => $value){
	echo "users[".$key . "]='".$value . "';
	";
}

?>
function addSubmission(sid){
	
	rubrics +=  users[sid]+ ',';
	

	var comment ="";
		var score=0;
	$("div[id^=tab]").find("img[data-sid="+sid+"]").each(function(index, element) {
		
		var subscore = $(this).closest("li").nextAll(".label").data("score");
		score+=Number(subscore);
		rubrics += subscore + ",";
        comment += $(this).closest("div").attr("title") + ": " + subscore + "  ";
    });
	rubrics += "\r";
	//console.log(rubrics);
	var stu = new Array();
	stu.push(sid);
	stu.push(score);
	stu.push(comment);
	grades.push(stu);
	
	return;
}
function compileGrades(bool){
	grades = new Array();
	var helper = "<?php echo $canvashtml ?>critboard/putgrades.php";
	
	
	rubrics = "name,sisid,";
	$("div[id^=tab]").each(function(index,element){
		rubrics += $(this).attr("title") + ",";
	});
	rubrics += "\r";
	$("#sortable img").each(function(index,element) {
       
		
			
            var sid = $(this).data("sid");
			//add submission formats data for both csv and grading endpoint, returns first row for rubrics
			addSubmission(sid);
			
        
    });
	//if(confirm(rubrics)){
	var obj = new Object();
	
	if(bool){
		$("#csvdata").val(rubrics);
		$("input[name=aid]").val("<?php echo $aid ?>");
		//alert(rubrics);
		$("#csv").submit();
		
	}else{
		obj.aid="<?php echo $aid ?>";
	obj.uid = "<?php echo $_REQUEST['custom_canvas_user_id']?>";
		obj.grades=grades;
		$("#success").text("Please wait").show().load(helper,obj);
	}
	//}
	
	
	
	
}
function exportRubrics(){
	
	
}
function deleteImage(ref){
	if(window.confirm("Delete this image from the critboard? This action will remove this image from all tabs, and can not be undone. Deleting will not remove the image from the assignment, only from this critique. You may only have one image per student if you wish to export grades to Canvas.")){
		
		var element = $("#dialog img,#dialog video,#dialog object");
		var src=element.data("thumb");
		
		$("li img[src='" + src + "']").parent().remove();
		$("#dialog").dialog("close");
		toggleGradeExport();
		
	}
}
function rotateImage(){
	if(window.confirm("Rotate this image 90º? This action will impact all tabs, but will not correct the orientation of the image as seen in other contexts, such as the speed grader.")){
		var src=$("#dialog img").data("thumb");
		var me = $("img[src='" + src + "'],#dialog img");
		var currclass= me.attr("class");
		//var me = $("img[src='" + src + "']");
		if(currclass!=undefined){
  		switch (currclass){
  			case "r90":
  				me.removeClass("r90").addClass("r180");
  				break;
  			case "r180":
  				me.removeClass("r180").addClass("r270");
  				break;
  			case "r270":
  				me.removeClass("r270");
				me.removeAttr("class");
  				break;
  			default:
  				me.addClass("r90");
  				break;
  		}
		}else{
		  
		  me.addClass("r90");
		}
		
		
	}
}
function endSession(){
	$("#unload").load("../logout.php",function(){
		//$(window).unbind('beforeunload');
		$("body").html("Your Critboard session has ended. Refresh your browser, or click the Canvas Critboard link again to re-activate this app");
		});
}
  </script>
</head>

<body onLoad="findMax()">
<?php echo $header ?>

<div id="success"></div>
<?php
//if($_SESSION['isInstructor']) {
 if($_REQUEST['assignment_id']){
	
	if($_SESSION['isInstructor']==false && !$isAdmin){//
	echo '<h2>Critboard (Student View) for '.$submissions[0]['assignment_name'] . '</h2>';
	echo '<div id="studentview"><div><h3>All Grades</h3>';
		
		if(!count($allgrades))echo '<p>No grades have been submitted for this assignment</p>';
		foreach($allgrades as $student){
			echo "<p>" . $student['grade'] . "</p>";
		}
		echo "</div><div><h3>Saved Crits</h3>";
		
		$critfolders = $api->get_canvas('/api/v1/courses/'.$context->info['custom_canvas_course_id'].'/folders/by_path/critboard/' . $_REQUEST['assignment_id'],true);
		//echo $critfolders[2]['files_url'];
		
		$path = str_replace("https://" . $domain,"",$critfolders[2]['files_url']);
		$savedcrits = $api->get_canvas($path,true);
		if(!count($savedcrits))echo '<p>No crits have been saved for this assignment</p>';
		foreach($savedcrits as $crit){
			echo "<p><a target='crit' href='" . str_replace("download_frd=1&","",$crit['url']) . "'>" . $crit['display_name'] . "</a></p>";
		}
		echo "</div></div></body></html>";
		exit();
	}else{
		
		
		// echo '<pre>' .print_r($_SESSION['_basic_lti_context']).'</pre>';
	echo 'Rubric Elements: <input type="text" placeholder="comma delimited" id="labels"  size="50">
<input type="button" id="tabsettings" onclick ="newRubric()"; value="Create Rubric Tabs">
<span  id="btns"><input type="button" onclick ="compileGrades(false)"; value="Compile Grades">
<input type="button" onclick ="compileGrades(true)"; value="Export CSV">
<input type="button" onClick="saveCrit()" value="Download Crit">
<input type="button" onClick="saveCrit(true)" value="Save Crit" title="save these sortings as a Canvas file"></span>
<form id="save" action="saveAs.php" method="post">
  <input id="critlist" name="critlist" type="hidden">
 




</form>
<form id="csv" action="putgrades.php" method="post" enctype="multipart/form-data">
  <input id="csvdata" name="csv" type="hidden" value="">
 <input id="aiddata" name="aid" type="hidden">




</form>';
	}
}else{// no assignment chosen
//$debug="";
	function sortByDate($b,$a){
	//	global $debug;
	//$debug .= $a['due_at']. "| " . $b['due_at']."\r" ;
			
			return strcmp($a['due_at'],$b['due_at']);
		}
	$uri = "/api/v1/courses/". $context->info['custom_canvas_course_id'] ."/assignments";
	
	
	
	$assignments = $api->get_canvas($uri);
	function listA($assignments){
		foreach($assignments as $assignment){
			echo $assignment['name'] ."," . $assignment['due_at'] . "<br>";
		}
	}
	listA($assignments);
	echo "<hr>";
	//$assignments=array_reverse($assignmentsR);//hard to sort this, because of empty
	uasort($assignments,"sortByDate");
	listA($assignments);
		//print_r($assignments);
	$foundone=false;
	echo '<form action="' . $me . '" id="launchAssignment" method="get">
	Select an assignment: <select name="assignment_id">';
	foreach($assignments as $assignment){
		//print_r($assignment['submission_types']);
		if(in_array('online_upload',$assignment['submission_types'])){
			$foundone=true;
			echo '<option value="' .$assignment['id'] . '">' . $assignment['name'] . '</option>';
		}
	}
	echo '</select> <input type="submit" value="Go"> <input type="button" onclick="openFullScreen()" value="Open Full Screen"></form>';
	if(!$foundone) echo "<p>No appropriate assignments exist. Create an assignment that accepts file upload submissions.</p>";
}
?>
<script>
var wincount=0;
function openFullScreen(){
	var map = "map" + wincount++;
	console.log(map);
	window.open("",map,"fullscreen=yes, scrollbars=1");
	$("#launchAssignment").attr("target",map).submit().attr("target","_self");
	
}

</script>

<hr/>

<div id="dialog"><img src=""/><!--<form><input name="score" onChange="setCat(5)" type="radio" value="5">top
<input name="score" onChange="setCat(3)" type="radio" value="3">middle
<input name="score" onChange="setCat(1)" type="radio" value="1">bottom
<input type="button" value="delete" onClick="deleteImage()"></form>-->
</div>
<div id="saveme">
<div id="critboard">
  

<?php if($_REQUEST['assignment_id']):?>
<ul id="sortable" class="sortable">
<li class="label" data-score="5">&nbsp;</li>
<?php else: ?>
<h3 >Images submitted to the assignment selected above will appear in this space</h3>
<?php endif ?>
<?php
//print_r($image_list);
if($image_list){
foreach ($image_list as $img){
	
		$path =str_replace("download_frd=1",'',$img['src']);
		$sid=$img['sid'];
		$thumb = $img['thumb'];
		$preview = $img['preview'];//this might be an api call or just the src all over again, depending on what is delivered.
		$preview = str_replace("download_frd=1","",$preview);
		$matches=explode("/",$img['content-type']);
		
		//print_r($matches);
		$content_type=$matches[0];
		
		if($content_type=="image" ){
			$dialog = 1;
			$tag = '<img data-sid="' . $sid . '" src="' . $thumb . '" data-full="' . $path . '" >';
		}else if($content_type=="video"){
			$dialog=1;
			
			if($thumb==''){
				$thumb='https://apps.ats.udel.edu/canvas/critboard/img_error.php?ran=Video with no thumbnail, view full may still work.&preview=' . $img['display_name'];
			}
			$size='width="128" height="96" ';
			$tag = '<img src="'.$thumb.'" data-sid="' . $sid . '" data-vidsrc="' . urlencode($path) . '" ' . $size . ' ></img>';
		}else if($content_type=="application" || $content_type=="binary"){
			$thumb='https://apps.ats.udel.edu/canvas/critboard/img_error.php?preview=' . $img['display_name'] . "&ran=" . rand(0,100) ;
			$tag = '<a class="error" href="' . $preview . '" target="_blank"><img data-sid="' . $sid . '" src="'. $thumb . '"></a>';//
			
			$dialog=1;
		}else if($content_type=="binary"){
			$thumb=$toolserver . $canvashtml . 'critboard/img_error.php?preview=' . $img['display_name'] ;
			$tag = '<a class="error" href="' . $img['preview']. '" target="_blank"><img data-sid="' . $sid . '" src="'. $thumb . '"></a>';//
			
			$dialog=1;
		
		}else{
			
		  $tag = '<pre>' . print_r($img). '</pre>';
		
		}
		//echo '<li>' . $tag . '<a title="open full" onclick="openFull(\'' . $path . '\')">&nbsp;</a></li>';
		echo '<li>';
		echo $tag;
	if($dialog){//this is happening all the time now
		echo '<a class="hash" title="open full" onclick="openFull(\'' . $path . '\', \'' . $thumb . '\', \'' . $content_type . '\')">&nbsp;</a></li>';
	}else{//just a plain vanilla link
		echo '<button title="delete" onclick="deleteImage(this)">X</button></li>';
	}
		
		
	
}
}else{
  //running into an issue with expired context tokens, or maybe it was that there was a token in the db that belonged to Mu? And I changed him to a student?
}
if($_REQUEST['assignment_id']) echo '<li class="label"  data-score="3">&nbsp;</li><li class="label last"  data-score="1">&nbsp;</li></ul>';
?>

</div>
</div>
<div id="footer"><a href="https://sites.google.com/a/udel.edu/critboard/bug-reports" target="bugreports">Bug Reports</a> | <a href="https://docs.google.com/a/udel.edu/forms/d/1iujOdz3jR9saeALKQn_skgLKhWoVkCAjZ3Fq203st8c/viewform" target="featurerequests">Feature Requests</a> | <a href="help.html" target="help">Help</a> </div>


</body>
</html>
