<?php
/**
 * Plugin Name: Policy Recon Countries
 * Plugin URI: http:policyrecon.com
 * Description: Custom post types for PolicyRecon.com
 * Version: 1.0.0
 * Author: DEF
 * License: A "Slug" license name e.g. GPL2
 * Text Domain: precon-Country
 */

add_action( 'init', 'precon_country_init' );
//add_action( 'save_post', 'precon_q_save_Country', 10, 2 );
add_action( 'save_post_country', 'precon_country_save_meta', 10, 2 );


function precon_country_init() {
	$labels = array(
		'name'               => _x( 'Country', 'post type general name', 'precon-ountry' ),
		'singular_name'      => _x( 'Country', 'post type singular name', 'precon-Country' ),
		'menu_name'          => _x( 'Countries', 'admin menu', 'precon-Country' ),
		'name_admin_bar'     => _x( 'Countries', 'add new on admin bar', 'precon-Country' ),
		'add_new'            => _x( 'Add New', 'Country', 'precon-Country' ),
		'add_new_item'       => __( 'Add New Country', 'precon-Country' ),
		'new_item'           => __( 'New Country', 'precon-Country' ),
		'edit_item'          => __( 'Edit Country', 'precon-Country' ),
		'view_item'          => __( 'View Country', 'precon-Country' ),
		'all_items'          => __( 'All Countries', 'precon-Country' ),
		'search_items'       => __( 'Search Countries', 'precon-Country' ),
		'parent_item_colon'  => __( 'Parent Countries:', 'precon-Country' ),
		'not_found'          => __( 'No Countries found.', 'precon-Country' ),
		'not_found_in_trash' => __( 'No Countries found in Trash.', 'precon-Country' )
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'Country' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'menu_icon'			 => 'dashicons-admin-site',
		'supports'           => array( 'title', 'editor', 'thumbnail', 'tags', 'page-attributes'),
		'register_meta_box_cb' => 'add_country_metaboxes',
		'taxonomies' => array( 'post_tag', 'category'), 
	);

	register_post_type( 'Country', $args );
}

function add_country_metaboxes() {
   add_meta_box('events', 'Events to Watch', 'precon_event_box', 'Country', 'normal', 'default');
   add_meta_box('key_risks', 'Key Risks', 'precon_key_risk_box', 'Country', 'normal', 'default');
   add_meta_box('policy_risks', 'Policy Risks', 'precon_policy_risk_box', 'Country', 'normal', 'default');

}


function precon_event_box( $object, $box ) { ?>
	<p>
		<label for="events">Events to Watch</label>
		<br />
		<textarea name="events" id="events" cols="60" rows="4" tabindex="30" style="width: 97%;"><?php echo esc_html( get_post_meta( $object->ID, 'events', true ), 1 ); ?></textarea>
		<input type="hidden" name="events_box_nonce" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
	</p>
<?php }
function precon_key_risk_box( $object, $box ) { ?>
	<p>
		<label for="key_risks">Key Risks</label>
		<br />
		<textarea name="key_risks" id="key_risks" cols="60" rows="4" tabindex="30" style="width: 97%;"><?php echo esc_html( get_post_meta( $object->ID, 'key_risks', true ), 1 ); ?></textarea>
		<input type="hidden" name="key_risks_nonce" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
	</p>
<?php }
function precon_policy_risk_box( $object, $box ) { ?>
	<p>
		<label for="policy_risks">Policy Risks</label>
		<br />
		<textarea name="policy_risks" id="policy_risks" cols="60" rows="4" tabindex="30" style="width: 97%;"><?php echo esc_html( get_post_meta( $object->ID, 'policy_risks', true ), 1 ); ?></textarea>
		<input type="hidden" name="policy_risks_nonce" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
	</p>
<?php }

function precon_country_save_meta( $post_id, $post ) {
	if ( !current_user_can( 'edit_post', $post_id ) )
			return $post_id;

	if ( !wp_verify_nonce( $_POST['events_box_nonce'], plugin_basename( __FILE__ ) ) ) {
		return $post_id;
	} else {
		$meta_value = get_post_meta( $post_id, 'events', true );
		$new_meta_value = stripslashes( $_POST['events'] );

		if ( $new_meta_value && '' == $meta_value )
			add_post_meta( $post_id, 'events', $new_meta_value, true );

		elseif ( $new_meta_value != $meta_value )
			update_post_meta( $post_id, 'events', $new_meta_value );

		elseif ( '' == $new_meta_value && $meta_value )
			delete_post_meta( $post_id, 'events', $meta_value );
	}
	

	if ( !wp_verify_nonce( $_POST['key_risks_nonce'], plugin_basename( __FILE__ ) ) ) {
		return $post_id;
	} else {
		$meta_value = get_post_meta( $post_id, 'key_risks', true );
		$new_meta_value = stripslashes( $_POST['key_risks'] );

		if ( $new_meta_value && '' == $meta_value )
			add_post_meta( $post_id, 'key_risks', $new_meta_value, true );

		elseif ( $new_meta_value != $meta_value )
			update_post_meta( $post_id, 'key_risks', $new_meta_value );

		elseif ( '' == $new_meta_value && $meta_value )
			delete_post_meta( $post_id, 'key_risks', $meta_value );
	}
		

	if ( !wp_verify_nonce( $_POST['policy_risks_nonce'], plugin_basename( __FILE__ ) ) ) {
		return $post_id;
	} else {
		$meta_value = get_post_meta( $post_id, 'policy_risks', true );
		$new_meta_value = stripslashes( $_POST['policy_risks'] );

		if ( $new_meta_value && '' == $meta_value )
			add_post_meta( $post_id, 'policy_risks', $new_meta_value, true );

		elseif ( $new_meta_value != $meta_value )
			update_post_meta( $post_id, 'policy_risks', $new_meta_value );

		elseif ( '' == $new_meta_value && $meta_value )
			delete_post_meta( $post_id, 'policy_risks', $meta_value );
	}


}



