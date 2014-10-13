<?php
/**
 * Plugin Name: Policy Recon Question
 * Plugin URI: http:policyrecon.com
 * Description: Custom post types for PolicyRecon.com
 * Version: 1.0.0
 * Author: DEF
 * License: A "Slug" license name e.g. GPL2
 * Text Domain: precon-question
 */

add_action( 'init', 'precon_question_init' );
add_action( 'save_post', 'precon_q_save_question', 10, 2 );


function precon_question_init() {
	$labels = array(
		'name'               => _x( 'question', 'post type general name', 'precon-question' ),
		'singular_name'      => _x( 'question', 'post type singular name', 'precon-question' ),
		'menu_name'          => _x( 'Questions', 'admin menu', 'precon-question' ),
		'name_admin_bar'     => _x( 'Question', 'add new on admin bar', 'precon-question' ),
		'add_new'            => _x( 'Add New', 'question', 'precon-question' ),
		'add_new_item'       => __( 'Add New Question', 'precon-question' ),
		'new_item'           => __( 'New Question', 'precon-question' ),
		'edit_item'          => __( 'Edit Question', 'precon-question' ),
		'view_item'          => __( 'View Question', 'precon-question' ),
		'all_items'          => __( 'All Questions', 'precon-question' ),
		'search_items'       => __( 'Search Questions', 'precon-question' ),
		'parent_item_colon'  => __( 'Parent Questions:', 'precon-question' ),
		'not_found'          => __( 'No Questions found.', 'precon-question' ),
		'not_found_in_trash' => __( 'No Questions found in Trash.', 'precon-question' )
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'Question' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor', 'thumbnail', 'tags', 'page-attributes'),
		'register_meta_box_cb' => 'add_questions_metaboxes',
		'taxonomies' => array( 'post_tag', 'category'), 
	);

	register_post_type( 'question', $args );
}

function add_questions_metaboxes() {
    add_meta_box('House', 'House Analysis', 'precon_house_box', 'question', 'normal', 'default');
 	add_meta_box('Expert', 'Expert Analytics', 'precon_expert_box', 'question', 'normal', 'default');
  	add_meta_box('Community', 'Community Analytics', 'precon_community_box', 'question', 'normal', 'default');
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

function precon_q_save_question( $post_id, $post ) {

	if ( !wp_verify_nonce( $_POST['house_box_nonce'], plugin_basename( __FILE__ ) ) )
		return $post_id;

	if ( !wp_verify_nonce( $_POST['expert_box_nonce'], plugin_basename( __FILE__ ) ) )
		return $post_id;

	if ( !wp_verify_nonce( $_POST['community_box_nonce'], plugin_basename( __FILE__ ) ) )
		return $post_id;

	if ( !current_user_can( 'edit_post', $post_id ) )
		return $post_id;

	$meta_value = get_post_meta( $post_id, 'House', true );
	$new_meta_value = stripslashes( $_POST['house'] );

	if ( $new_meta_value && '' == $meta_value )
		add_post_meta( $post_id, 'House', $new_meta_value, true );

	elseif ( $new_meta_value != $meta_value )
		update_post_meta( $post_id, 'House', $new_meta_value );

	elseif ( '' == $new_meta_value && $meta_value )
		delete_post_meta( $post_id, 'House', $meta_value );



	$meta_value = get_post_meta( $post_id, 'Expert', true );
	$new_meta_value = stripslashes( $_POST['expert'] );

	if ( $new_meta_value && '' == $meta_value )
		add_post_meta( $post_id, 'Expert', $new_meta_value, true );

	elseif ( $new_meta_value != $meta_value )
		update_post_meta( $post_id, 'Expert', $new_meta_value );

	elseif ( '' == $new_meta_value && $meta_value )
		delete_post_meta( $post_id, 'Expert', $meta_value );



	$meta_value = get_post_meta( $post_id, 'Community', true );
	$new_meta_value = stripslashes( $_POST['community'] );

	if ( $new_meta_value && '' == $meta_value )
		add_post_meta( $post_id, 'Community', $new_meta_value, true );

	elseif ( $new_meta_value != $meta_value )
		update_post_meta( $post_id, 'Community', $new_meta_value );

	elseif ( '' == $new_meta_value && $meta_value )
		delete_post_meta( $post_id, 'Community', $meta_value );
}
