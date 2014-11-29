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
add_action( 'save_post_issue', 'precon_q_save_Issue', 10, 2 );


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
		'rewrite'            => array( 'slug' => 'issues' ),
		'capability_type'    => array('precon_issue', 'precon_issues'),
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'menu_icon'			 => 'dashicons-welcome-widgets-menus',
		'supports'           => array( 'title', 'editor', 'thumbnail', 'tags', 'page-attributes'),
		'register_meta_box_cb' => 'add_issue_metaboxes',
		'taxonomies' => array( 'post_tag', 'category'), 
	);

	register_post_type( 'issue', $args );
}

//
//Add editing permissions for Admins
//
add_action('admin_init','precon_issue_add_role_caps',999);
function precon_issue_add_role_caps() {
		//role array, so we can easily add other roles
		$roles = array('administrator');
		
		// Loop through each role and assign capabilities
		foreach($roles as $the_role) { 

		     $role = get_role($the_role);
			
	             $role->add_cap( 'read' );
	             $role->add_cap( 'read_precon_issue');
	             $role->add_cap( 'read_private_precon_issues' );
	             $role->add_cap( 'edit_precon_issue' );
	             $role->add_cap( 'edit_precon_issues' );
	             $role->add_cap( 'edit_others_precon_issues' );
	             $role->add_cap( 'edit_published_precon_issues' );
	             $role->add_cap( 'publish_precon_issues' );
	             $role->add_cap( 'delete_others_precon_issues' );
	             $role->add_cap( 'delete_private_precon_issues' );
	             $role->add_cap( 'delete_published_precon_issues' );
		}
}

function add_issue_metaboxes() {
   add_meta_box('summary_box', 'Summary', 'precon_issue_boxes', 'Issue', 'normal', 'default');

}

function precon_issue_boxes($object) { ?>

	<p class="infobox">
		<textarea name="excerpt" id="excerpt" cols="60" rows="4" tabindex="30" style="width: 97%;"><?php echo esc_html( get_post_meta( $object->ID, 'excerpt', true ), 1 ); ?></textarea>
		<input type="hidden" name="house_excerpt_nonce" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
	</p>

<?php }

function precon_q_save_issue($post_id, $post) {

	if ( !current_user_can( 'edit_post', $post_id ) )
		return $post_id;

	if ( isset($_POST['house_excerpt_nonce']) && !wp_verify_nonce( $_POST['house_excerpt_nonce'], plugin_basename( __FILE__ ) ) ) {
		return $post_id;
	} else {
		$meta_value = get_post_meta( $post_id, 'excerpt', true );
		$new_meta_value = stripslashes( $_POST['excerpt'] );

		if ( $new_meta_value && '' == $meta_value )
			add_post_meta( $post_id, 'excerpt', $new_meta_value, true );

		elseif ( $new_meta_value != $meta_value )
			update_post_meta( $post_id, 'excerpt', $new_meta_value );

		elseif ( '' == $new_meta_value && $meta_value )
			delete_post_meta( $post_id, 'excerpt', $meta_value );		
	}

	//need to add support for changing post title?
	$title = $post->post_title;
	if($title != 'Auto Draft') {
		$newCat = wp_create_category($title, get_cat_ID('issue'));

		wp_set_post_categories($post_id, array($newCat), TRUE);

	}
	
}