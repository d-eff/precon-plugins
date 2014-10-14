<?php
/**
 * Plugin Name: Policy Recon Forecasts
 * Plugin URI: http:policyrecon.com
 * Description: Custom post types for PolicyRecon.com
 * Version: 1.0.0
 * Author: DEF
 * License: GPL2
 * Text Domain: precon-forecast
 */

add_action( 'init', 'precon_forecast_init' );
add_action( 'save_post_forecast', 'precon_q_save_forecast', 10, 2 );


function precon_forecast_init() {
	$labels = array(
		'name'               => _x( 'Forecast', 'post type general name', 'precon-forecast' ),
		'singular_name'      => _x( 'Forecast', 'post type singular name', 'precon-forecast' ),
		'menu_name'          => _x( 'Forecasts', 'admin menu', 'precon-forecast' ),
		'name_admin_bar'     => _x( 'Forecast', 'add new on admin bar', 'precon-forecast' ),
		'add_new'            => _x( 'Add New', 'Forecast', 'precon-forecast' ),
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
		'menu_icon'			 => 'dashicons-chart-line',
		'supports'           => array( 'title', 'editor', 'thumbnail', 'tags', 'page-attributes'),
		'register_meta_box_cb' => 'add_forecasts_metaboxes',
		'taxonomies' => array( 'post_tag', 'category'), 
	);

	register_post_type( 'forecast', $args );
}

function add_forecasts_metaboxes() {
    add_meta_box('House', 'House Analysis', 'precon_house_box', 'forecast', 'normal', 'default');
 	add_meta_box('Expert', 'Expert Analytics', 'precon_expert_box', 'forecast', 'normal', 'default');
  	add_meta_box('Community', 'Community Analytics', 'precon_community_box', 'forecast', 'normal', 'default');
}


function precon_house_box( $object, $box ) { ?>
	<p>
		<label for="house">House Analysis</label>
		<br />
		<textarea name="house" id="house" cols="60" rows="4" tabindex="30" style="width: 97%;"><?php echo wp_specialchars( get_post_meta( $object->ID, 'House', true ), 1 ); ?></textarea>
		<input type="hidden" name="house_box_nonce" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
	</p>
<?php }
function precon_expert_box( $object, $box ) { ?>
	<p>
		<label for="expert">Expert Analysis</label>
		<br />
		<textarea name="expert" id="expert" cols="60" rows="4" tabindex="30" style="width: 97%;"><?php echo wp_specialchars( get_post_meta( $object->ID, 'Expert', true ), 1 ); ?></textarea>
		<input type="hidden" name="expert_box_nonce" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
	</p>
<?php }
function precon_community_box( $object, $box ) { ?>
	<p>
		<label for="community">Community Analysis</label>
		<br />
		<textarea name="community" id="community" cols="60" rows="4" tabindex="30" style="width: 97%;"><?php echo wp_specialchars( get_post_meta( $object->ID, 'Community', true ), 1 ); ?></textarea>
		<input type="hidden" name="community_box_nonce" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
	</p>
<?php }

function precon_q_save_forecast( $post_id, $post ) {

	if ( !current_user_can( 'edit_post', $post_id ) )
		return $post_id;

	if ( isset($_POST['house_box_nonce']) && !wp_verify_nonce( $_POST['house_box_nonce'], plugin_basename( __FILE__ ) ) ) {
		return $post_id;
	} else {
		$meta_value = get_post_meta( $post_id, 'House', true );
		$new_meta_value = stripslashes( $_POST['house'] );

		if ( $new_meta_value && '' == $meta_value )
			add_post_meta( $post_id, 'House', $new_meta_value, true );

		elseif ( $new_meta_value != $meta_value )
			update_post_meta( $post_id, 'House', $new_meta_value );

		elseif ( '' == $new_meta_value && $meta_value )
			delete_post_meta( $post_id, 'House', $meta_value );		
	}

		
	if ( isset($_POST['expert_box_nonce']) && !wp_verify_nonce( $_POST['expert_box_nonce'], plugin_basename( __FILE__ ) ) ) {
		return $post_id; 
	} else {
		$meta_value = get_post_meta( $post_id, 'Expert', true );
		$new_meta_value = stripslashes( $_POST['expert'] );

		if ( $new_meta_value && '' == $meta_value )
			add_post_meta( $post_id, 'Expert', $new_meta_value, true );

		elseif ( $new_meta_value != $meta_value )
			update_post_meta( $post_id, 'Expert', $new_meta_value );

		elseif ( '' == $new_meta_value && $meta_value )
			delete_post_meta( $post_id, 'Expert', $meta_value );

	}
		

	if ( isset($_POST['community_box_nonce']) && !wp_verify_nonce( $_POST['community_box_nonce'], plugin_basename( __FILE__ ) ) ) {
		return $post_id;
	} else {
		$meta_value = get_post_meta( $post_id, 'Community', true );
		$new_meta_value = stripslashes( $_POST['community'] );

		if ( $new_meta_value && '' == $meta_value )
			add_post_meta( $post_id, 'Community', $new_meta_value, true );

		elseif ( $new_meta_value != $meta_value )
			update_post_meta( $post_id, 'Community', $new_meta_value );

		elseif ( '' == $new_meta_value && $meta_value )
			delete_post_meta( $post_id, 'Community', $meta_value );
	}
		

}
