<?php
	
add_action("wp_ajax_jsapi", "jsapi");
add_action("wp_ajax_nopriv_jsapi", "jsapi");

function jsapi(){

	$type = $_POST['type'];
	global $$type;
	
	if($_POST['method'] == 'getquestion'){
		if(!$_POST['name']) return false;
		
		$result['question'] = $$type->getQuestion($_POST['name']);
		
		if($_POST['value'] && $result['question']){ // have a matching question and param supplied
			if(is_array($result['question']['value'])){ // question is defined as multichoice
				// Multichoice search (not necessarily a taxonomy)
				$result['value'] = $$type->taxArraySearch($_POST['value'],$result['question']['value']);
				echo 'jsapi: '.$result['value']; 
			} else {
				$result['value'] = $_POST['value'];
			}
		}
		

		echo json_encode($result);
		exit();
	}
	
	if($_POST['method'] == 'time2str'){
		if(!$_POST['date']) return false;
		echo json_encode(time2str($_POST['date']));	
		exit();
	}

}