<?php
	
	function getGroupUsers($group_id = null){
		global $user;
		if(!$group_id){
			$meta_group_id = get_user_meta($user->ID,'group_id',true);
			$group_id = $meta_group_id != '' ? $meta_group_id : $user->ID;
		}
		
		//queries for group ID and user ID
		$idquery	 = new WP_User_Query(array('include' => array($group_id)));
		$groupquery	 = new WP_User_Query(array('meta_key' => 'group_id', 'meta_value' => $group_id));
				
		//merge queries
		$mergedquery = new WP_User_Query();
		$mergedquery->results = array_merge($idquery->results,$groupquery->results);
		
		//update merged query post count
		$mergedquery->count_total = $groupquery->count_total + $idquery->count_total;
		
		//return result;
		return $mergedquery;
		
	}
	

	
