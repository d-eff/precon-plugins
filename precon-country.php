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
add_action( 'save_post_country', 'precon_country_save_meta', 10, 2 );


function precon_country_init() {
	$labels = array(
		'name'               => _x( 'Country', 'post type general name', 'precon-Country' ),
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

	register_post_type( 'country', $args );
}

function add_country_metaboxes() {
   add_meta_box('info_boxes', 'Additional Info', 'precon_country_boxes', 'Country', 'normal', 'default');

}

function precon_country_boxes() {
	global $post;
 
    $info_boxes = get_post_meta($post->ID, 'info_boxes', true);
 
    wp_nonce_field( 'info_meta_box_nonce', 'info_meta_box_nonce' );
?>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('#add_box').on('click', function(e) {
	       		var row = $('#infobox_sample').clone(true);  		
	        	row.removeClass('hidden');
	        	console.log(row);
	        	row.insertBefore('#infobox_sample');
	        	return false;
    		});
    		$('.remove_box').on('click', function(){
    			$(this).parent('.infobox').remove();
    			return false;
    		});
		});

	</script>
<?php if ($info_boxes):
	foreach($info_boxes as $box) {  ?>
	<p class="infobox">
		<input type="text" class="widefat" name="box_title[]" value="<?php if ($box['box_title'] != '') echo esc_attr( $box['box_title'] ); ?>" />
		<textarea name="box_content[]" id="events" cols="60" rows="4" tabindex="30" style="width: 97%;"><?php if($box['box_content'] != '') echo esc_attr( $box['box_content'] ); ?></textarea>
		<a href="#" class="remove_box">Remove section</a>
	</p>
<?php } else: ?>
	<p class="infobox">
		<input type="text" class="widefat" name="box_title[]" placeholder="Title"/>
		<textarea name="box_content[]" id="events" cols="60" rows="4" tabindex="30" style="width: 97%;"></textarea>
	</p>
<?php endif;
?>
	<p class="infobox hidden" id="infobox_sample">
		<input type="text" class="widefat" name="box_title[]" placeholder="Title"/>
		<textarea name="box_content[]" id="events" cols="60" rows="4" tabindex="30" style="width: 97%;"></textarea>
		<a href="#" class="remove_box">Remove section</a>
	</p>
	<a href="#" id="add_box">Add New Box</a>

<?php }

function precon_country_save_meta( $post_id ) {
	 if ( ! isset( $_POST['info_meta_box_nonce'] ) ||
        ! wp_verify_nonce( $_POST['info_meta_box_nonce'], 'info_meta_box_nonce' ) )
        return;
	
    if (!current_user_can('edit_post', $post_id))
        return;

    $old = get_post_meta($post_id, 'info_boxes', true);
    $new = array();
 
    $titles = $_POST['box_title'];
    $contents = $_POST['box_content'];
 
    $count = count( $titles );
 
    for ( $i = 0; $i < $count; $i++ ) {
        if ( $titles[$i] != '' ) :
            $new[$i]['box_title'] = stripslashes( strip_tags( $titles[$i] ) );
 
 
        if ( $contents[$i] != '' )
            $new[$i]['box_content'] = stripslashes( $contents[$i] ); 
        endif;
    }
 
    if ( !empty( $new ) && $new != $old )
        update_post_meta( $post_id, 'info_boxes', $new );
    elseif ( empty($new) && $old )
        delete_post_meta( $post_id, 'info_boxes', $old );

	//need to add support for changing post title?
	$title = $post->post_title;
	if($title != 'Auto Draft') {
		$newCat = wp_create_category($title, get_cat_ID('issue'));

		wp_set_post_categories($post_id, array($newCat), TRUE);

	}

}



