<?php
/**
 * Plugin Name: Policy Recon forecast
 * Plugin URI: http:policyrecon.com
 * Description: Custom post types for PolicyRecon.com
 * Version: 1.0.0
 * Author: DEF
 * License: A "Slug" license name e.g. GPL2
 * Text Domain: precon-forecast
 */

add_action( 'init', 'precon_forecast_init' );
//add_action( 'save_post', 'precon_q_save_forecast', 10, 2 );


function precon_forecast_init() {
	$labels = array(
		'name'               => _x( 'forecast', 'post type general name', 'precon-forecast' ),
		'singular_name'      => _x( 'forecast', 'post type singular name', 'precon-forecast' ),
		'menu_name'          => _x( 'Forecasts', 'admin menu', 'precon-forecast' ),
		'name_admin_bar'     => _x( 'Forecast', 'add new on admin bar', 'precon-forecast' ),
		'add_new'            => _x( 'Add New', 'forecast', 'precon-forecast' ),
		'add_new_item'       => __( 'Add New Forecast', 'precon-forecast' ),
		'new_item'           => __( 'New Forecast', 'precon-forecast' ),
		'edit_item'          => __( 'Edit Forecast', 'precon-forecast' ),
		'view_item'          => __( 'View Forecast', 'precon-forecast' ),
		'all_items'          => __( 'All Forecasts', 'precon-forecast' ),
		'search_items'       => __( 'Search Forecasts', 'precon-forecast' ),
		'parent_item_colon'  => __( 'Parent Forecasts:', 'precon-forecast' ),
		'not_found'          => __( 'No Forecasts found.', 'precon-forecast' ),
		'not_found_in_trash' => __( 'No Forecasts found in Trash.', 'precon-forecast' )
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'forecast' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor', 'thumbnail', 'tags', 'page-attributes'),
		'register_meta_box_cb' => 'add_forecasts_metaboxes',
		'taxonomies' => array( 'post_tag', 'category'), 
	);

	register_post_type( 'forecast', $args );
}



