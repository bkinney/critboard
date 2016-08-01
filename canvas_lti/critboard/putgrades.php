<?php

	
	
	if(array_key_exists('csv',$_REQUEST)){//download
		header("content-disposition:attachment;filename=rubric_scores_" . $_REQUEST['aid'] . ".csv");
		header("content-type:text/csv");
		
	
			echo $_REQUEST['csv'];
		
		exit();
	}
	session_start();
	include $_SESSION['canvasphp']."sitepaths.php";
	//print_r($_SESSION);
	
$testing=false;
$include=null;
$tokentype="context";//this shouldn't matter
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 1);
function get_val($from,$varname){
	
	if(array_key_exists($varname,$from)){
		return $from[$varname];
	}else{
		return false;
	}
}
	
	require_once($_SESSION['canvasphp']."all_purpose.php");
	if(get_val($_GET,'endpoint')){
		$endpoint = str_replace('https://'.$domain,'', $_GET['endpoint']);
		
		$queueStatus = $api->get_canvas($endpoint,false);
		echo '<p>status: ' . $queueStatus['workflow_state'];
		echo '</p><p>percent completed: ' . $queueStatus['completion'] . '</p>';
		if($queueStatus['completion']*1<100){
			echo '<a href="#" onclick="$(\'#success\').load(\'putgrades.php?endpoint='. $endpoint.'\')">Check again</a>';
		}
		exit();
	}

	
	$endpoint='/api/v1/courses/' . $_SESSION['_basic_lti_context']['custom_canvas_course_id'] .'/assignments/' . $_POST['aid'] . '/submissions/update_grades';
	$args = array();
		foreach($_POST['grades'] as $grade){
			//grade 
			
	//echo "Student,ID,SIS User ID,SIS Login ID,Section,peer" . $projectid . "\r\n";
	//echo ",,,,," . $_GET['maxscore'] . "\r\n";
	
		$args['grade_data'][$grade[0]]['posted_grade']=$grade[1];
		$args['grade_data'][$grade[0]]['text_comment']=$grade[2];
		//$args .= '&grade_data['.$grade[0].'][text_comment]='.urlencode($grade[2]);
		//echo $endpoint;
	
		}
		$result = $api->post_canvas($endpoint,"POST",$args);
		//put_canvas("/api/v1/courses/301991/assignments/4612095/submissions/1273346?submission[posted_grade]=8&comment[text_comment]=a: 5 b: 3 :");
			if(!$result['errors']){
				
				echo 'Your grades have been queued for posting. If you have a large student roster, it may take a few minutes to complete. You can safely close your browser without interrupting the update.  <a href="#" onclick="$(\'#success\').load(\'putgrades.php?endpoint=' . $result['url'].'\')">Check status</a>';
		
}else{//close if result
  echo $result['errors'];
 //echo $_SESSION['tokentype'];
}
	?>