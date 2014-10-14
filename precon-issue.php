<?php
/**
 * Plugin Name: Policy Recon Issues
 * Plugin URI: http:policyrecon.com
 * Description: Custom post types for PolicyRecon.com
 * Version: 1.0.0
 * Author: DEF
 * License: GPL2
 * Text Domain: precon-issue
 */

add_action( 'init', 'precon_issue_init' );
//add_action( 'save_post', 'precon_q_save_issue', 10, 2 );


function precon_issue_init() {
	$labels = array(
		'name'               => _x( 'Issue', 'post type general name', 'precon-issue' ),
		'singular_name'      => _x( 'Issue', 'post type singular name', 'precon-issue' ),
		'menu_name'          => _x( 'Issues', 'admin menu', 'precon-issue' ),
		'name_admin_bar'     => _x( 'Issue', 'add new on admin bar', 'precon-issue' ),
		'add_new'            => _x( 'Add New', 'Issue', 'precon-issue' ),
		'add_new_item'       => __( 'Add New Issue', 'precon-issue' ),
		'new_item'           => __( 'New Issue', 'precon-issue' ),
		'edit_item'          => __( 'Edit Issue', 'precon-issue' ),
		'view_item'          => __( 'View Issue', 'precon-issue' ),
		'all_items'          => __( 'All Issues', 'precon-issue' ),
		'search_items'       => __( 'Search Issues', 'precon-issue' ),
		'parent_item_colon'  => __( 'Parent Issues:', 'precon-issue' ),
		'not_found'          => __( 'No Issues found.', 'precon-issue' ),
		'not_found_in_trash' => __( 'No Issues found in Trash.', 'precon-issue' )
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'issue' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'menu_icon'			 => 'dashicons-schedule',
		'supports'           => array( 'title', 'editor', 'thumbnail', 'tags', 'page-attributes'),
		'register_meta_box_cb' => 'add_issues_metaboxes',
		'taxonomies' => array( 'post_tag', 'category'), 
	);

	register_post_type( 'issue', $args );
}

function add_issues_metaboxes() {


}



