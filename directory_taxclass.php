<?php

class taxdef extends directoryCore {
	
	public $type;
	public $label;
	public $single_label;
	public $items;
	public $terms;

	function __construct($type,$label,$single_label,$items,$terms){
		$this->type = $type;
		$this->label = $label;
		$this->single_label = $single_label;
		$this->items = $items;
		$this->terms = $terms;
		
		if( ! taxonomy_exists( $this->type ) )
	    {
	        $this->addTaxonomy();
	        
			if(!get_option('_dir_taxonomies_imported')) {
	        	$this->addTerms();
				add_option('_dir_taxonomies_imported',1);
			}
	        	
	    }
	    
	}
		
	public function addTaxonomy(){
						
		register_taxonomy($this->type,$this->items,array(
			'hierarchical' => true,
			'labels' => array(
				'name' => _x( ucfirst($this->label), 'taxonomy general name' ),
				'singular_name' => _x( ucfirst($this->single_label), 'taxonomy singular name' ),
				'search_items' =>  __( 'Search '.$this->label ),
				'all_items' => __( 'All '.$this->label ),
				'parent_item' => __( 'Parent '.$this->single_label ),
				'parent_item_colon' => __( 'Parent '.$this->single_label.':' ),
				'edit_item' => __( 'Edit '.$this->single_label ), 
				'update_item' => __( 'Update '.$this->single_label ),
				'add_new_item' => __( 'Add new '.$this->single_label ),
				'new_item_name' => __( 'New '.$this->single_label.' name' ),
				'menu_name' => __( ucfirst($this->single_label) )
			),
			'show_ui' => true,
			'query_var' => true
		));
			
	}
	
	public function addTerms($terms = NULL,$parent = 0){
		
		
			$args = NULL;
			
			// start with terms array from taxdef
			if(!$terms) $terms = $this->terms; // Could be improved?
	
			foreach($terms as $k=>$v){
				
				if(!is_array($v)) {
					$args = array('slug' => $v,
								  'parent' => $parent
								 );
					if(!term_exists($k,$this->type)) $term_reg = wp_insert_term($k,$this->type,$args);
				} else {
					if(!term_exists($k,$this->type)){
						$term_reg = wp_insert_term($k,$this->type);
					} else {
						$term_reg = term_exists($k,$this->type);
					}
					$this->addTerms($v,$term_reg['term_id']);
				}
	
			}
			
		
		//}
		
	}
	
	public function getTerms(){
		

		if(taxonomy_exists($this->type)){
			return get_terms($this->type,array( 'hide_empty' => 0 ));
		} else {
			return array('taxonomy not created','true');
		}
		
	}
	
	
	public function taxTree(){
		


		$taxterms = get_terms($this->type,array( 'hide_empty' => 0, 'parent' => 0 ));


		if(count($taxterms > 0)){
			foreach($taxterms as $term){
				$varr = array('id' => $term->term_id,'slug' => $term->slug);
				// recursive
				

				
				
				$children = get_term_children($term->term_id,$this->type);
				if(count($children) > 0){
					foreach($children as $child){
						$cterm = get_term($child,$this->type);
						$varr['children'][$cterm->name] = array('id' => $child,'slug' => $cterm->slug);
					}
				}
				// end recursive
				$taxtree[$term->name] = $varr;
			}
		}
		
		//echo '<pre>'; print_r($taxtree); echo '</pre>';
		
		return $taxtree;

		
		
	}
	
	public function getTermChildren($parent){
		
		$args = array(
		    'parent' => $parent,
		    'taxonomy' => $this->type,
		    'hide_empty' => 0,
		    'hierarchical' => true,
		    );
		$children = get_categories( $args );
		
/*
		echo '<pre>';
		echo 'Count children = '.count($children).'<br />';
		print_r($children);
		echo '</pre>';
*/
		return $children;
		
	}


	public function taxTreeRecursive(){
		
		$terms = get_terms($this->type, array('hide_empty' => false));
		
		$condterms = array();
		
		foreach($terms as $term){
			$condterm['term_id'] = $term->term_id;
			$condterm['parent'] = $term->parent;
			$condterm['slug'] = $term->slug;
			$condterm['name'] = $term->name;
			$condterms[$term->name] = $condterm;
		}
		

		for($i=0; $i<count($terms); $i++){
			unset($terms[$i]->term_group);
			unset($terms[$i]->term_taxonomy_id);
			unset($terms[$i]->description);
			unset($terms[$i]->count);
			unset($terms[$i]->taxonomy);
			$terms[$terms[$i]->name] = $terms[$i];
			unset($terms[$i]);
		}

		
		$categoryHierarchy = array();
		//$this->sort_terms_hierarchicaly($termsArr, $categoryHierarchy);
		$this->sort_terms_hierarchicaly_original($terms, $categoryHierarchy);
		
		return $categoryHierarchy;
		
	}
	
	 public function sort_terms_hierarchicaly(&$terms, &$into, $parentId = 0){
	    foreach ($terms as $i => $term) {
	        if ($term['parent'] == $parentId) {
	            $into[$term['name']] = $term;
	            unset($terms[$i]);
	        }
	    }
	
	    foreach ($into as $topTerm) {
	        $topTerm['children'] = array();
	        $this->sort_terms_hierarchicaly($terms, $topTerm['children'], $topTerm['term_id']);
	    }
	}
	
	
	public function sort_terms_hierarchicaly_original(Array &$cats, Array &$into, $parentId = 0)
{
    foreach ($cats as $i => $cat) {
        if ($cat->parent == $parentId) {
            $into[$cat->name] = $cat;
            unset($cats[$i]);
        }
    }

    foreach ($into as $topCat) {
        $topCat->children = array();
        $this->sort_terms_hierarchicaly_original($cats, $topCat->children, $topCat->term_id);
    }
}
	
		
		//Below code to call function As my main Custom Taxonomy is Location please replace with yours
		

	
}