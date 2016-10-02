<?php

class directoryCore {
	
	public $vars = array();
	public $expire = '';
	public $adminroot = '/';

	
	function __construct(){	

	}
	
	public function setAdminRoot($ar){
		$this->adminroot = $ar;
	}
	
	public function AdminRoot(){
		return $this->adminroot;
	}
	
	public function setExpire($expire){
		$this->expire = $expire;
	}

	public function getExpire(){
		return $this->expire;
	}
	
	// put this inside an call action on init?	
	public function setVars($vars){
		$this->vars = $vars;
	}
	
	public function getVars(){
		return $this->vars;
	}
	
	public function prepVars($vars = null){
		if(!$vars) return null;
		foreach($vars as $k=>$v){
			if($v != ''){
				$q = $this->getQuestion($k);
				if(is_array($q['value']) && !is_array($v)){
					$preppedVars[$k][] = $v;
				} else {
					$preppedVars[$k] = $v;
				}
			}

		}
		//echo '<pre>Prepped vars: <br />'; print_r($preppedVars); echo '</pre>';
		return $preppedVars ? $preppedVars : null;
	}
	
	public function getVarNames(){
		foreach($this->getVars() as $var){
			$names[] = $var['name'];
		}
		return $names;
	}

	public function getVarNameFromVal($val = null,$varname = null){
		if(!$val || !$varname) return false;

		$foundkey = null;

		foreach($this->getVars() as $var){

			if($var['name'] == $varname){
				if($var['value'] && is_array($var['value'])){
					foreach($var['value'] as $k=>$v){
						if($v['slug'] == $val) $foundkey = $k;
					}	
				}
				

			}
		}

		return $foundkey;

	}
	
	
	public function getVals($id){
		$vals = null;
		foreach($this->getVarNames() as $name){
			$q = $this->getQuestion($name);
			if($q['taxonomy']){
				$terms = wp_get_post_terms($id,$q['taxonomy']);
				foreach($terms as $term) $vals[$name][] = $term->slug;				 
			} else {
				$v = get_post_meta($id,$name,true);;
				if($q['fieldtype'] == 'date') $v = formatDate($v,$q);
				$vals[$name] = $v;
			}
		}
		return $vals;
	}
	
	public function addQuestion($args){
		array_push($this->vars, $args);
	}
	
	public function getGroup($groupname){
		if(is_array($this->vars)){
			foreach($this->vars as $var){
				if($var['group'] == $groupname){
					$group[] = $var;
				}
			}
		}
		return $group ? $group : false;
	}

	public function getQuestion($name){
		if(is_array($this->vars)){
			foreach($this->vars as $var){
				if($var['name'] == $name){
					return $var;
				}
			}
		}
		return false;
	}
	
	
	public function printQuestion($name,$value = null,$format = null,$add_blank = false,$use_dependency = true){
		//$output .= '<p>'.$name.''.print_r($value).'</p>';
		if($question = $this->getQuestion($name)){
			
			$value ? $value : ($question['value'] ? $question['value'] : '');

			//*=========================
			//
			// Needed this for something, can't remember what...
			//
			// $value = unserialize($value) ? unserialize($value) : $value;
			//
			//===========================

			

			$question['fieldtype'] = $format ? $format : $question['fieldtype'];
				
			switch($question['fieldtype']){
				
				case 'text':
					$output .= $question['label'] ? '<label>'.$question['label'].'</label>' : '';
					$output .= $question['instructions'] ? '<p class="instructions">'.$question['instructions'].'</p>' : '';
					$output .= '<input type="text" ';
					$output .= $question['class'] ? 'class="'.$question['class'].'" ' : '';
					$output .= $question['name'] ? 'name="'.$question['name'].'" ' : '';
					$output .= $question['name'] ? 'ng-model="inputs.'.$question['name'].'" ' : '';
					$output .= $question['placeholder'] ? 'placeholder="'.$question['placeholder'].'" ' : '';
					$output .= $question['label'] ? 'label="'.$question['label'].'" ' : '';
				
					
					if($question['required']) $question['required'] = array_filter(explode(':', $question['required']));
					if(count($question['required']) > 1){
						$output .= 'reqdep="'.$question['required'][0].'" ';
						$output .= 'req="'.$question['required'][1].'" ';						
					} else {
						$output .= 'req="'.$question['required'][0].'" ';							
					}
					if($use_dependency) $output .= $question['dependency'] ? 'dependency="'.$question['dependency'].'" ' : '';
					$output .= 'value="'.$value.'" ';
					$output .= '/>';
				break;

				case 'textarea':
					$output .= $question['label'] ? '<label>'.$question['label'].'</label>' : '';
					$output .= $question['instructions'] ? '<p class="instructions">'.$question['instructions'].'</p>' : '';
					$output .= '<textarea ';
					$output .= $question['name'] ? 'name="'.$question['name'].'" ' : '';
					$output .= $question['name'] ? 'ng-model="inputs.'.$question['name'].'" ' : '';
					$output .= $question['placeholder'] ? 'placeholder="'.$question['placeholder'].'" ' : '';
					if($use_dependency) $output .= $question['dependency'] ? 'dependency="'.$question['dependency'].'" ' : '';
					$output .= $question['required'] ? 'req="'.$question['required'].'" ' : '';
					$output .= '>'.$value.'</textarea>';
				break;

				case 'richtext':
					$output .= $question['limit'] ? '<span class="limited" data-limit="'.$question['limit'].'">' : '<span class="unlimited">';
					$output .= $question['label'] ? '<label>'.$question['label'].'</label>' : '';
					$output .= $question['instructions'] ? '<p class="instructions">'.$question['instructions'].'</p>' : '';
					
					ob_start();
					$editor_args = array(
						'media_buttons' => false,
						'teeny' => true,
						'wpautop' => false,
						'editor_height' => 600,
						'quicktags' => false
					);
					wp_editor($value,$question['name'],$editor_args);
					$output .= ob_get_clean();

					$output .= '</span>';
				break;

				
				case 'date':
					$output .= $question['label'] ? '<label>'.$question['label'].'</label>' : '';
					$output .= $question['instructions'] ? '<p class="instructions">'.$question['instructions'].'</p>' : '';

					$class = 'datepicker ';
					$class .= $question['class'] ? $question['class'] : '';

					$output .= '<input type="text" class="'.$class.'" ';
					$output .= $question['name'] ? 'name="'.$question['name'].'" ' : '';
					$output .= $question['name'] ? 'ng-model="inputs.'.$question['name'].'" ' : '';
					$output .= $question['placeholder'] ? 'placeholder="'.$question['placeholder'].'" ' : '';
					if($use_dependency) $output .= $question['dependency'] ? 'dependency="'.$question['dependency'].'" ' : '';
					$output .= $question['required'] ? 'req="'.$question['required'].'" ' : '';
					$output .= 'value="'.$value.'" ';
					$output .= '/>';
				break;

				case 'email':
					$output .= $question['label'] ? '<label>'.$question['label'].'</label>' : '';
					$output .= $question['instructions'] ? '<p class="instructions">'.$question['instructions'].'</p>' : '';
					$output .= '<input type="email" ';
					$output .= $question['name'] ? 'name="'.$question['name'].'" ' : '';
					$output .= $question['name'] ? 'ng-model="inputs.'.$question['name'].'" ' : '';
					$output .= $question['placeholder'] ? 'placeholder="'.$question['placeholder'].'" ' : '';
					if($use_dependency) $output .= $question['dependency'] ? 'dependency="'.$question['dependency'].'" ' : '';
					$output .= $question['required'] ? 'req="'.$question['required'].'" ' : '';
					$output .= 'value="'.$value.'" ';
					$output .= '/>';
				break;
				
				case 'link':
					$output .= $question['label'] ? '<label>'.$question['label'].'</label>' : '';
					$output .= $question['instructions'] ? '<p class="instructions">'.$question['instructions'].'</p>' : '';
					$output .= '<input type="text" ';
					$output .= $question['name'] ? 'name="'.$question['name'].'" ' : '';
					$output .= $question['name'] ? 'ng-model="inputs.'.$question['name'].'" ' : '';
					if($use_dependency) $output .= $question['dependency'] ? 'dependency="'.$question['dependency'].'" ' : '';
					$output .= $question['placeholder'] ? 'placeholder="'.$question['placeholder'].'" ' : '';
					$output .= $question['required'] ? 'req="'.$question['required'].'" ' : '';
					$output .= 'value="'.$value.'" ';
					$output .= '/>';
				break;
				
				case 'password':
					$output .= $question['label'] ? '<label>'.$question['label'].'</label>' : '';
					$output .= $question['instructions'] ? '<p class="instructions">'.$question['instructions'].'</p>' : '';
					$output .= '<input type="password" ';
					$output .= $question['name'] ? 'name="'.$question['name'].'" ' : '';
					$output .= $question['name'] ? 'ng-model="inputs.'.$question['name'].'" ' : '';
					$output .= $question['required'] ? 'req="'.$question['required'].'" ' : '';
					$output .= 'value="'.$value.'" ';
					$output .= '/>';
				break;
				
				case 'dropdown':

					$output .= $question['label'] ? '<label>'.$question['label'].'</label>' : '';
					$output .= $question['instructions'] ? '<p class="instructions">'.$question['instructions'].'</p>' : '';
					$output .= '<select ';
					$output .= $question['name'] ? 'name="'.$question['name'].'" ' : '';
					$output .= $question['name'] ? 'ng-model="inputs.'.$question['name'].'" ' : '';
					$output .= $question['label'] ? 'label="'.$question['label'].'" ' : '';
					$output .= $question['placeholder'] ? 'placeholder="'.$question['placeholder'].'" ' : '';
					if($use_dependency) $output .= $question['dependency'] ? 'dependency="'.$question['dependency'].'" ' : '';
					$output .= $question['force'] ? 'force="'.$question['force'].'" ' : '';
					$output .= $question['required'] ? 'req="'.$question['required'].'" ' : '';
					$output .= '>';
					if($question['addblank'] || $add_blank){
						$output .= '<option value="">'.(!is_bool($add_blank) ? $add_blank : $question['label']).'</option>';
					}
					foreach($question['value'] as $k=>$v){
						$v = is_array($v) ? $v : (array)$v;
						$value = is_array($value) ? $value : (array)$value;
						$output .= '<option value="'.$v['slug'].'" termid="'.$v['term_id'].'"';
						if($value[0] == $v['slug']){
							$output .= 'SELECTED ';
						}
						$output .= '>'.$k.'</option>';
						if(isset($v['children'])) foreach ($v['children'] as $k=>$v){
							$v = is_array($v) ? $v : (array)$v;
							$value = is_array($value) ? $value : (array)$value;
							$output .= '<option value="'.$v['slug'].'" termid="'.$v['term_id'].'"';
							if($value[0] == $v['slug']){
								$output .= 'SELECTED ';
							}
							$output .= '>- '.$k.'</option>';
								if(isset($v['children'])) foreach ($v['children'] as $kk=>$vv){
									$vv = is_array($vv) ? $vv : (array)$vv;
									$value = is_array($value) ? $value : (array)$value;
									$output .= '<option value="'.$vv['slug'].'" termid="'.$vv['term_id'].'"';
									if($value[0] == $vv['slug']){
										$output .= 'SELECTED ';
									}
									$output .= '>- - '.$kk.'</option>';
								}

						}
					}
					$output .= '</select>';
				break;
				
				case 'check':
					$l = 0;
					foreach($question['value'] as $k=>$v){
						if(strlen($k) > $l) $l = strlen($k);
					}
					$cols = round(40/$l, 0, PHP_ROUND_HALF_DOWN);
					$cols = $cols < 4 ? $cols : 4;
					
					$output .= $question['label'] ? '<label>'.$question['label'].'</label>' : '';
					$output .= $question['instructions'] ? '<p class="instructions">'.$question['instructions'].'</p>' : '';
					$output .= '<fieldset class="cc'.$cols.' ">';
					foreach($question['value'] as $k=>$v){
						$v = is_array($v) ? $v : (array)$v;
					$output .= '<p>';
						if($question['select_parent'] !== 'false'){
							$output .= '<input type="checkbox" value="'.$v['slug'].'" ';
							$output .= $question['name'] ? 'name="'.$question['name'].'[]" ' : '';
							$output .= $question['name'] ? 'ng-model="inputs.'.$question['name'].'[]" ' : '';
							$output .= $question['dependency'] ? 'dependency="'.$question['dependency'].'" ' : '';
							$output .= $question['force'] ? 'force="'.$question['force'].'" ' : '';
							$output .= $question['required'] ? 'req="'.$question['required'].'" ' : '';
							if($value && in_array($v['slug'], $value)){
								$output .= 'CHECKED ';
							}
							$output .= '/>';
						}
						$output .= '<label>'.$k.'</label>';
						$output .= '</p>';

						if(isset($v['children'])) foreach($v['children'] as $k=>$v){
							$v = is_array($v) ? $v : (array)$v;
							$output .= '<p class="inset1">';
							$output .= '<input type="checkbox" value="'.$v['slug'].'" ';
							$output .= $question['name'] ? 'name="'.$question['name'].'[]" ' : '';
							$output .= $question['name'] ? 'ng-model="inputs.'.$question['name'].'[]" ' : '';
							$output .= $question['dependency'] ? 'dependency="'.$question['dependency'].'" ' : '';
							$output .= $question['force'] ? 'force="'.$question['force'].'" ' : '';
							$output .= $question['required'] ? 'req="'.$question['required'].'" ' : '';
							if($value && in_array($v['slug'], $value)){
							//if($v == $value){
								$output .= 'CHECKED ';
							}
							$output .= '/><label>'.$k.'</label>';
							$output .= '</p>';


							if(isset($v['children'])) foreach($v['children'] as $k=>$v){
							$v = is_array($v) ? $v : (array)$v;
								$output .= '<p class="inset2">';
								$output .= '<input type="checkbox" value="'.$v['slug'].'" ';
								$output .= $question['name'] ? 'name="'.$question['name'].'[]" ' : '';
								$output .= $question['name'] ? 'ng-model="inputs.'.$question['name'].'[]" ' : '';
								$output .= $question['force'] ? 'force="'.$question['force'].'" ' : '';
								$output .= $question['dependency'] ? 'dependency="'.$question['dependency'].'" ' : '';
								$output .= $question['required'] ? 'req="'.$question['required'].'" ' : '';
								if($value && in_array($v['slug'], $value)){
								//if($v == $value){
									$output .= 'CHECKED ';
								}
								$output .= '/><label>'.$k.'</label>';
								$output .= '</p>';
							}


						}


					}
					$output .= '</fieldset>';
				break;
				

				
				case 'radio':
					$l = 0;
					foreach($question['value'] as $k=>$v){
						if(strlen($k) > $l) $l = strlen($k);
					}
					$cols = round(40/$l, 0, PHP_ROUND_HALF_DOWN);
					$cols = $cols < 4 ? $cols : 4;

					$output .= $question['label'] ? '<label>'.$question['label'].'</label>' : '';
					$output .= $question['instructions'] ? '<p class="instructions">'.$question['instructions'].'</p>' : '';
					$output .= '<fieldset class="cc'.$cols.' ">';
					foreach($question['value'] as $k=>$v){
						$output .= '<input type="radio" value="'.$v.' ';
						$output .= $question['dependency'] ? 'dependency="'.$question['dependency'].'" ' : '';
						$output .= $question['name'] ? 'name="'.$question['name'].'[]" ' : '';
						$output .= $question['name'] ? 'ng-model="inputs.'.$question['name'].'[]" ' : '';
					$output .= $question['required'] ? 'req="'.$question['required'].'" ' : '';
						if(in_array($v, $value)){
							$output .= 'CHECKED ';
						}
						$output .= '/><label>'.$k.'</label>';
					}
					$output .= '</fieldset>';
				break;

				case 'file':
					$output .= $question['label'] ? '<label>'.$question['label'].'</label>' : '';
					$output .= $question['instructions'] ? '<p class="instructions">'.$question['instructions'].'</p>' : '';
					$output .= '<input type="file" class="fileupload"';
					$output .= $question['name'] ? 'name="'.$question['name'].'" ' : '';
					$output .= $question['name'] ? 'ng-model="inputs.'.$question['name'].'" ' : '';
					if($use_dependency) $output .= $question['dependency'] ? 'dependency="'.$question['dependency'].'" ' : '';
					$output .= $question['required'] ? 'req="'.$question['required'].'" ' : '';
					$output .= '/>';
				break;

				case 'image':
					$output .= $question['label'] ? '<label>'.$question['label'].'</label>' : '';
					$output .= $question['instructions'] ? '<p class="instructions">'.$question['instructions'].'</p>' : '';
					$output .= '<input type="file" class="imageupload"';
					if($use_dependency) $output .= $question['dependency'] ? 'dependency="'.$question['dependency'].'" ' : '';
					$output .= $question['name'] ? 'name="'.$question['name'].'" ' : '';
					$output .= $question['name'] ? 'ng-model="inputs.'.$question['name'].'" ' : '';
					$output .= $question['required'] ? 'req="'.$question['required'].'" ' : '';
					$output .= '/>';
				break;

				
				case 'hidden':
					$output .= '<input type="hidden" ';
					$output .= $question['name'] ? 'name="'.$question['name'].'" ' : '';
					$output .= $question['name'] ? 'ng-model="inputs.'.$question['name'].'" ' : '';
					$output .= 'value="'.$value.'" ';
					$output .= '/>';
				break;
				
				
			}
			
			$el_class = $question['extra_class'] ? $question['extra_class'] : '';
			echo '<div class="question '.$el_class.'">'.$output.'</div>';

		} else {

			echo 'Question not found';

		}
	}
	
	public function printGroup($groupname,$vals = null){
		$group = $this->getGroup($groupname);
		if($group){
			foreach($group as $question) $this->printQuestion($question['name'],$vals[$question['name']]);
		}
	}
	
	public function printDetail($name,$value = null){
		
		
		$q = $this->getQuestion($name);
		
		
		if($q && is_array($value)){
			
			if(is_array($q['value'])){
				$foundkeys = array();
				foreach($value as $vitem){
					 if(is_array($vitem)) foreach($vitem as $i){
					
						foreach($q['value'] as $s=>$t){
							$t = is_array($t) ? $t : (array)$t;
							if($t['slug'] == $i){
								$foundkeys[] = $s;
							}
							if($t['children']) foreach($t['children'] as $f=>$g){
								if($g['slug'] == $i){
									$foundkeys[] = $f;
								}
							}
						}

					} else {
						
						foreach($q['value'] as $s=>$t){
							$t = is_array($t) ? $t : (array)$t;
							if($t['slug'] == $vitem){
								$foundkeys[] = $s;
							}
							if($t['children']) foreach($t['children'] as $f=>$g){
								$g = is_array($g) ? $g : (array)$g;
								if($g['slug'] == $vitem){
									$foundkeys[] = $f;
								}
							}
						}
						
					}

				}
				
				$value = implode(',&nbsp;', $foundkeys);
			} else {
				$value = $value[$name];
			}			
			
		}
		

		// create output
		$output .= '<tr>';
		$output .= '<td style="width:50%">'.($q ? $q['label'] : $name).'</td>';
		$output .= '<td>'.(is_array($value) ? implode(', ', $value) : $value).'</td>';
		$output .= '</tr>';
		
		echo $output;
	}
	

	
	
	public function canAccess($args){
		
		global $user, $usermeta;
		$usertype = $user->roles[0];
		global $$usertype;
				
		if(!$user) $redirect = true;
		
		if($args['roles']) {
			$roles = explode(',', $args['roles']);
			if(!in_array($user->roles[0], $roles)) $redirect = true;
		}
		
		if($args['id']) {
			$ids = explode(',', $args['id']);
			if(!in_array($user->ID, $ids)) $redirect = true;
		}
		
		if($args['group_id']) {
			if($args['group_id'] != $user->ID && $args['group_id'] != $usermeta['group_id']) $redirect = true;
		}
		
		if($user->roles[0] == 'administrator') $redirect = false;
		
		if($redirect){ // access denied
			if($user){ // no user logged in
				if(rtrim($_SERVER['REQUEST_URI'],'/') != $$usertype->AdminRoot()){ // avoids redirect loop if usertype redirect doesn't specify access permission
					header("Location: ".$$usertype->AdminRoot());
				}
			} else {
				header("Location: ".DIRECTORY_LOGINPATH);
			}
		}
		
	}
	
	public function makeFolder($path){
		
		$folder = $_SERVER['DOCUMENT_ROOT'].ltrim($path,'\\');
		
		if (!file_exists($folder)) {
			$old = umask(0);
			$result = mkdir($folder,0777,true) ? $folder : false; // Only give permission to read and write, not execute
			umask($old);
			return $result;
		}
	}
	
	
	public function uploadFiles($post_id = NULL){
		
		/*
		
			Check $_FILES actually contains file data
			Iterate through $_FILES
			Check each file type, decide if image or other
			redirect to uploadImage() or uploadFile()
			Pass $_FILES[$key] into each
			
		*/
		

		

		if($_FILES){
			$uploads = false;
			foreach($_FILES as $k=>$v){
				if($_FILES[$k]['tmp_name'] && $_FILES[$k]['tmp_name'] != ''){
					if (getimagesize($_FILES[$k]['tmp_name'])) {
						$upload = $this->uploadImage($k, false, $post_id, true);
					} else {
						$upload = $this->uploadFile($k, true, $post_id);
					}
				}
				$uploads[] = $upload;	
			}
			return $uploads;

		}		
		
	}
	
	public function uploadImage ($file_field = null, $random_name = false, $post_id = NULL, $check_image = false) {
		
		/*
		
			Check file extension permitted
			Check MIME type permitted
			Chech file size does not exceed limit
			Check file is a valid image
			
		*/
		
	   
		//Config Section    
		//Define upload path
		//Set max file size in bytes
		$max_size = 1000000;
		//Set default file extension whitelist
		$whitelist_ext = array('jpg','png','gif');
		//Set default file type whitelist
		$whitelist_type = array('image/jpeg', 'image/png','image/gif');
		
		//The Validation
		// Create an array to hold any output
		$out = array('error'=>null);
		           
		if (!$file_field) {
		$out['error'][] = "Please specify a valid form field name";           
		}
		   
		if (count($out['error'])>0) {
		return $out;
		}
		
		//Make sure that there is a file
		if((!empty($_FILES[$file_field])) && ($_FILES[$file_field]['error'] == 0)) {
			
		     
			// Get filename
			$file_info = pathinfo($_FILES[$file_field]['name']);
			$name = $file_info['filename'];
			$ext = $file_info['extension'];
			           
			//Check file has the right extension           
			if (!in_array($ext, $whitelist_ext)) {
			  $out['error'][] = "Invalid file Extension";
			}
			           
			//Check that the file is of the right type
			if (!in_array($_FILES[$file_field]["type"], $whitelist_type)) {
			  $out['error'][] = "Invalid file Type";
			}
			           
			//Check that the file is not too big
			if ($_FILES[$file_field]["size"] > $max_size) {
			  $out['error'][] = "File is too big";
			}
			           
			//If $check image is set as true
			if ($check_image) {
			  if (!getimagesize($_FILES[$file_field]['tmp_name'])) {
			    $out['error'][] = "Uploaded file is not a valid image";
			  }
			}
			
			
			if(get_class($this) == 'itemdef' && !$post_id){
			    $out['error'][] = "Entity is an item but no post id provided";				
			}
			

			
			if(!$out['error']){ // Any errors?	
				
				
				///////////////////////////////////////////
				//
				// WordPress upload to media library
				//
				///////////////////////////////////////////
				
				/*
				
					Work out if image upload for a user or item
					Get question for user or item to validate
					If item, pass post_id into media_handle_upload
					
				*/
		
								
				if($q = $this->getQuestion($file_field)){
					//die('file matches an expected field');
					$attachment = media_handle_upload($file_field,((get_class($this) == 'itemdef' && $post_id) ? $post_id : 0));	// silent error - needs better handlling		
					if(is_wp_error($attachment)) {
						$out['error'][] = "Image was not uploaded to media library";
					} else {
						$out['varname'] = $file_field;
						$out['attachment_id'] = $attachment;
						$out['original_filename'] = $name.'.'.$ext;
					}
					
				} else {
					$out['error'][] = "File field does not match item or user vars";
				}
				
				///////////////////////////////////////////
				//
				// END WordPress upload to media library
				//
				///////////////////////////////////////////

				
			}		
		     
		} else {			
			$out['error'][] = "No file uploaded";
		}      
		
		return $out;
		
	}


	public function uploadFile ($file_field = null, $random_name = false, $post_id = NULL) {
	   
		//Config Section    
		//Define upload path
		$path = $this->uploadPath($post_id);
		$this->makeFolder($path);
		//Set max file size in bytes
		$max_size = 1000000;
		//Set default file extension whitelist
		$whitelist_ext = array('txt','rtf','doc','docx','pdf');
		//Set default file type whitelist
		$whitelist_type = array('text/plain','application/rtf','text/rtf','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/pdf');
		
		//The Validation
		// Create an array to hold any output
		$out = array('error'=>null);
		           
		if (!$file_field) {
		$out['error'][] = "Please specify a valid form field name";           
		}

		if (!$path) {
		$out['error'][] = "Upload path not defined (possibly function not called by known class object)";           
		}
		   
		if (count($out['error'])>0) {
		return $out;
		}
		
		//Make sure that there is a file
		if((!empty($_FILES[$file_field])) && ($_FILES[$file_field]['error'] == 0)) {
		     
			// Get filename
			$file_info = pathinfo($_FILES[$file_field]['name']);
			$name = $file_info['filename'];
			$ext = $file_info['extension'];
			           
			//Check file has the right extension           
			if (!in_array($ext, $whitelist_ext)) {
			  $out['error'][] = "Invalid file Extension";
			}
			           
			//Check that the file is of the right type
			if (!in_array($_FILES[$file_field]["type"], $whitelist_type)) {
			  $out['error'][] = "Invalid file Type";
			}
			           
			//Check that the file is not too big
			if ($_FILES[$file_field]["size"] > $max_size) {
			  $out['error'][] = "File is too big";
			}
			
			//Create full filename including path
			if ($random_name) {
				// Generate random filename
				$tmp = str_replace(array('.',' '), array('',''), microtime());
				               
				if (!$tmp || $tmp == '') {
				$out['error'][] = "File must have a name";
				}     
				$newname = $tmp.'.'.$ext;                                
			} else {
			    $newname = $name.'.'.$ext;
			}
			           
			// Check if file already exists on server
			// Disabled as happy to overwrite
/*
			if (file_exists($path.$newname)) {
			  $out['error'][] = "A file with this name already exists";
			}
*/
			
			if (count($out['error'])>0) {
			  //The file has not correctly validated
			  return $out;
			} 
			
			//Check field name matches object vars
			if($q = $this->getQuestion($file_field)){
				if (move_uploaded_file($_FILES[$file_field]['tmp_name'], $_SERVER['DOCUMENT_ROOT'].$path.$newname)) {
				  //Success
				  $out['varname'] = $file_field;
				  $out['filepath'] = $path;
				  $out['filename'] = $newname;
				  $out['original_filename'] = $name.'.'.$ext;
				  return $out;
				} else {
				  $out['error'][] = "Server Error!";
				}
			}
		     
		} else {
			
			$out['error'][] = "No file uploaded";
			return $out;
			
		}      
	}
	
	public function uploadPath($post_id = NULL){
		
		//Config Section    
		//Upload pertains to a user
		if(get_class($this) == 'userdef'){
			global $user, $usermeta;
			$usertype = $user->roles[0];
			$path = DIRECTORY_UPLOADPATH.$usertype.'/'.$user->ID.'/';
		}

		if(get_class($this) == 'itemdef'){
			if($post_id) {
				$itemtype = $this->getItemType();
				$path = DIRECTORY_UPLOADPATH.$itemtype.'/'.$post_id.'/';
			} 
		}

		return $path ? $path : false;
		
	}
	
	public function getEntity(){

		if(get_class($this) == 'userdef'){
			global $user, $usermeta;
			$usertype = $user->roles[0];
			return $usertype;
		} else if(get_class($this) == 'itemdef'){
			return $this->getItemType();
		} else {
			return false;
		}

	}
	
	public function formAfter($params){
		
		/* PSEUDO
			
			Could have come from
			- a form processor function
			- login
			- a.n.other front end form
			
			Possible actions
			- hide/fadeout form
			- show a page element
			- refresh some page content
			- redirect
		
			Behaviour paramemeters
			- Submitted by AJAX or HTTP
			- Form action was success or fail
			
			Will need a syntax to carry all data in one field
			
			eg. formafter="hide,update:#contentarea,redirect:/myaccount"
			
			
		*/
		
		
		if(!$params['result']) return false;
		
		if($params['result'] == 'success'){
			if($params['subtype'] == 'AJAX' && $params['formafter']){
				
			}
			
			if($params['redirect']) {
				header('Location: '.$params['redirect']);
			} else {
				header('Location: '.$params['referer'].'?u='.$newuserID);
			}
			
			
		}
		
		if($params['result'] == 'fail'){
			
			
		}
		
		
	}
	
	public function notify($data){
		
		if(!$data['notify']) return false;
		
		// if the notify field does not contain an email address, get the value of the field named by notify
		if(strstr($data['notify'], '@')) {
			$to = $data['notify'];
		} else {
			$to = isset($data[$data['notify']]) ? $data[$data['notify']] : 'no email';
		}
		
		$data['logo'] = get_bloginfo('template_url').'/img/logo_strap.png';
		
		// if the $to does not contain a valid email address
		if (!filter_var($to, FILTER_VALIDATE_EMAIL)) return false;
	
		
		// build email body using template
		$template = $data['notify_template'] ? $data['notify_template'] : 'default';
		$body = file_get_contents(__DIR__ . '/email_templates/'.$template.'.php');
		foreach($data as $k=>$v){
			$replace = is_array($v) ? $v[0] : $v;
			$body = preg_replace('/\['.$k.'\]/', $replace, $body);
		}

		//echo $body;
			
		// send email
		$this->sendEmail($to,NOTIFY_EMAIL,$data['notify_subject'],$body);
		return true;
	}
	
	public function sendEmail($toemail,$fromemail = APPLICATION_EMAIL,$subject,$body,$fromname = APPLICATION,$toname = null){
		
		$to = $toname ? $toname.' <'.$toemail.'>' : $toemail;
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: '.$fromname.' <'.$fromemail.'>'."\r\n";
		$option = '-f '.$fromemail;
		
		// swap in wp_mail to use Sendgrid
		mail($to, $subject, $body, $headers, $option);
		//wp_mail($to, $subject, $body, $headers, $option = null);
	}
	
	
	public function encrypt($payload) {	
		$iv = mcrypt_create_iv(IV_SIZE, MCRYPT_DEV_URANDOM);
		$crypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, CRYPTKEY, $payload, MCRYPT_MODE_CBC, $iv);
		$combo = $iv . $crypt;
		$garble = base64_encode($iv . $crypt);
		return $garble;
	}
	
	public function decrypt($garble) {
		$combo = base64_decode($garble);
		$iv = substr($combo, 0, IV_SIZE);
		$crypt = substr($combo, IV_SIZE, strlen($combo));
		$payload = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, CRYPTKEY, $crypt, MCRYPT_MODE_CBC, $iv);
		return $payload;
	}
	
	public function sort2d ($array, $index, $order='ASC', $natsort=TRUE, $case_sensitive=FALSE){
        if(is_array($array) && count($array)>0){
            foreach(array_keys($array) as $key)
                $temp[$key]=$array[$key][$index];
                if(!$natsort)($order=='ASC')? asort($temp) : arsort($temp);
                else {
                    ($case_sensitive)? natsort($temp) : natcasesort($temp);
                    if($order!='ASC') $temp=array_reverse($temp,TRUE);
                }
            foreach(array_keys($temp) as $key) (is_numeric($key))? $sorted[]=$array[$key] : $sorted[$key]=$array[$key];
            return $sorted;
        }
        return $array;
    }
    
    public function makeLink($url){
	    return 'http://'.preg_replace('@http:\/\/@', '', $url);
    }
    
    public function recursive_array_search($needle,$haystack) {
	 
	    foreach($haystack as $key=>$value) {
	        $current_key = $key;
	        if($needle == $value OR (is_array($value) && $this->recursive_array_search($needle,$value) != false)) {
	            return $current_key;
	        }
	    }
	    return false;
	}
	

	
	
	public function getCurrencySymbol($locale, $currency)
	{
	    // Create a NumberFormatter
	    $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
	
	    // Figure out what 0.00 looks like with the currency symbol
	    $withCurrency = $formatter->formatCurrency(0, $currency);
	
	    // Figure out what 0.00 looks like without the currency symbol
	    $formatter->setPattern(str_replace('¤', '', $formatter->getPattern()));
	    $withoutCurrency = $formatter->formatCurrency(0, $currency);
	
	    // Extract just the currency symbol from the first string
	    return str_replace($withoutCurrency, '', $withCurrency);    
	}
	


	
	
}