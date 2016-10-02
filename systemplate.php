<?php

function register_systemplate(){

	$type = 'systemplate'; 
	$label = 'systemplates';
	$single_label ='systemplate';

	register_post_type($type, array(
        'labels' => array(
            'name' => ucfirst($label),
            'singular_name' => ucfirst($single_label),
            'add_new' => 'Add new '.$single_label,
            'edit_item' => 'Edit '.$single_label,
            'new_item' => 'New '.$single_label,
            'view_item' => 'View '.$single_label,
            'search_items' => 'Search '.$label,
            'not_found' => 'No '.$label.' found',
            'not_found_in_trash' => 'No '.$label.' found in Trash'
        ),
        'public' => true,
		'show_ui' => true,
		'capability_type' => 'post',
		'hierarchical' => true,
		//'rewrite' => array( 'slug' => 'systemplate', 'with_front' => false ),
		'query_var' => true,
        'supports' => array(
            'title',
            'editor',
            'excerpt',
            'thumbnail',
            'custom-fields',
            'page-attributes'
        )
	));
		
}
		
add_action( 'init', 'register_systemplate' );


// Remove 'systemplate' from URL


function remove_cpt_slug( $post_link, $post, $leavename ) {

    if ( 'systemplate' != $post->post_type || 'publish' != $post->post_status ) { return $post_link; }

    $post_link = str_replace( '/' . $post->post_type . '/', '/', $post_link );
    return $post_link;
}

add_filter( 'post_type_link', 'remove_cpt_slug', 10, 3 );

function custom_parse_request_tricksy( $query ) {

    // Only mod the main query
    if ( ! $query->is_main_query() )return;

    // Only mod our very specific rewrite rule match
    if ( 2 != count( $query->query ) || ! isset( $query->query['page'] ) ) { return; }

    // 'name' will be set if post permalinks are just post_name, otherwise the page rule will match
    if ( ! empty( $query->query['name'] ) ) {
        $query->set( 'post_type', array( 'post', 'systemplate', 'page' ) );
    }
}
add_action( 'pre_get_posts', 'custom_parse_request_tricksy' );
