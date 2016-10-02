<?php

class userdef extends directoryCore {
	
	public $role;
	public $label;

	function __construct($role,$label){
		$this->role = $role;
		$this->label = $label;
		
		$this->register_role();
		//add_filter( 'editable_roles', 'exclude_role' );
	    
	}


	
	public function register_role(){
		
		$caps = array(	'delete_posts' => true,
				'delete_published_posts' => true,
				'edit_posts' => true,
				'edit_published_posts' => true,
				'publish_posts' => true,
				'read' => true,
				'upload_files' => true
				);
				
					
		add_role( $this->role, $this->label, $caps );
		
	}
	
	function exclude_role($roles) {

	    if (isset($roles['author'])) {
	      unset($roles['author']);
	    }

		if (isset($roles['editor'])) {
	      unset($roles['editor']);
	    }

		if (isset($roles['subscriber'])) {
	      unset($roles['subscriber']);
	    }

		if (isset($roles['contributor'])) {
	      unset($roles['contributor']);
	    }

	    return $roles;
	    
	}
	
	public function getVals($user_id = null){

		global $user, $usermeta;
		if(!$user && (!$user_id || $user_id == 0)) {
			return false;
		}
		
		if($user_id){
			foreach(get_user_meta($user_id) as $k=>$v){
				$vals[$k] = unserialize($v[0]) ? unserialize($v[0]) : $v[0];
			}
			$vals['user'] = get_user_by('id',$user_id);
			
		} else {
			foreach($usermeta as $k=>$v){
				$vals[$k] = $v[0];
			}
			$vals['user'] = $user;
		}
		return $vals;
		
	}
	
	
	
	public function getUsers($params = null){
		
		$args = array();
		$args['role'] = $this->role;
		if($params['id']) $args['include'] = $params['id'];
		if($params['login']) $args['user_login'] = $params['login'];
		if($params['email']) $args['user_email'] = $params['email'];
		

		// Set up meta query for search against role specific parameters
		$varnames = $this->getVarNames();

		
		// Set up basic args - needs to be expanded to cover other search params eg. name, email etc.
		if($params['orderby']){
			if(in_array($params['orderby'], $varnames)){
				$args['meta_key'] = $params['orderby'];
				$args['orderby'] = 'meta_value';
			} else {
				$args['orderby'] = $params['orderby'];				
			}			
		} else {
			$args['orderby'] = 'name';
		}

		$args['order'] = $params['order'] ? $params['order'] : 'ASC';
		
		if(is_array($params)) foreach($params as $k=>$v){
			if(in_array($k, $varnames)){
				$args['meta_query'][] = array('key' => $k,'value' => $v,'compare' => '=');
			}
		}

		$users = new WP_User_Query($args);

		for($i=0; $i<count($users->results); $i++){
			$thisuser = $users->results[$i];
			$cleanusermeta = array();
			foreach(get_user_meta($thisuser->ID) as $k=>$v){
				$cleanusermeta[$k] = unserialize($v[0]) ? unserialize($v[0])[0] : $v[0];
			}
			$thisuser->meta = $cleanusermeta;
			$usersarr[] = $thisuser;
		}
		$users->results = $usersarr;
		
		return $users;
		
	}
	
			
}