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

//Create Forecast Post Type
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
		'rewrite'            => array( 'slug' => 'forecasts' ),
		'capability_type'    => array('precon_forecast', 'precon_forecasts'),
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

//
//Add editing permissions for Admins
//
add_action('admin_init','precon_forecast_add_role_caps',999);
function precon_forecast_add_role_caps() {
		//role array, so we can easily add other roles
		$roles = array('administrator');
		
		// Loop through each role and assign capabilities
		foreach($roles as $the_role) { 

		     $role = get_role($the_role);
			
	             $role->add_cap( 'read' );
	             $role->add_cap( 'read_precon_forecast');
	             $role->add_cap( 'read_private_precon_forecasts' );
	             $role->add_cap( 'edit_precon_forecast' );
	             $role->add_cap( 'edit_precon_forecasts' );
	             $role->add_cap( 'edit_others_precon_forecasts' );
	             $role->add_cap( 'edit_published_precon_forecasts' );
	             $role->add_cap( 'publish_precon_forecasts' );
	             $role->add_cap( 'delete_others_precon_forecasts' );
	             $role->add_cap( 'delete_private_precon_forecasts' );
	             $role->add_cap( 'delete_published_precon_forecasts' );
		}
}

//Add Metaboxes
function add_forecasts_metaboxes() {
    add_meta_box('House', 'House Analysis', 'precon_house_box', 'forecast', 'normal', 'default');
 	add_meta_box('Expert', 'Expert Analytics', 'precon_expert_box', 'forecast', 'normal', 'default');
  	add_meta_box('Community', 'Community Analytics', 'precon_community_box', 'forecast', 'normal', 'default');
}

//Callback for Metaboxes
function precon_house_box( $object, $box ) { ?>
	<p>
		<label for="house">House Analysis</label>
		<br />
		<textarea name="house" id="house" cols="60" rows="4" tabindex="30" style="width: 97%;"><?php echo esc_html( get_post_meta( $object->ID, 'House', true ), 1 ); ?></textarea>
		<input type="hidden" name="house_box_nonce" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
	</p>
<?php }
function precon_expert_box( $object, $box ) { ?>
	<p>
		<label for="expert">Expert Analysis</label>
		<br />
		<textarea name="expert" id="expert" cols="60" rows="4" tabindex="30" style="width: 97%;"><?php echo esc_html( get_post_meta( $object->ID, 'Expert', true ), 1 ); ?></textarea>
		<input type="hidden" name="expert_box_nonce" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
	</p>
<?php }
function precon_community_box( $object, $box ) { ?>
	<p>
		<label for="community">Community Analysis</label>
		<br />
		<textarea name="community" id="community" cols="60" rows="4" tabindex="30" style="width: 97%;"><?php echo esc_html( get_post_meta( $object->ID, 'Community', true ), 1 ); ?></textarea>
		<input type="hidden" name="community_box_nonce" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
	</p>
<?php }

//Callback for Saving Forecast
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

//Process data and output to D3
function getData($tid, $suffix) {
	$vaName = 'voteArray' . $suffix;
	$vc = 'voteCount' . $suffix;
	$vote_arr = get_post_meta($tid, $vaName, true);
	$vote_count = get_post_meta($tid, $vc, true);

	if(!empty($vote_count) && !empty($vote_arr)) {

		foreach($vote_arr as $key => $value) {
			echo $value / $vote_count[$key] . " ";
		}
	}
}

//Enqueue Forecast Scripts
function add_forecast_scripts() {
	wp_enqueue_script(
		'forecast',
		plugins_url( '/precon-forecast.js' , __FILE__ )
	);
	wp_enqueue_script(
		'd3',
		'//d3js.org/d3.v3.min.js'
	);
}
add_action( 'init', 'add_forecast_scripts' );

//
//Forms
//
function notlogged_form() {
	echo 
	'<div class="widgetWrap"><h4 class="widgetTitle">Forecasts</h4>
	<p class="voteInstr">To submit your own forecasts, please log in or register as a user.</p>
	</div>';
}

function house_form($amount) {
	echo 
	'<div class="widgetWrap"><h4 class="widgetTitle">Forecasts</h4>
	 <p class="voteInstr">Submit your forecast here. You can update your forecast during the day, and only your last submission will count for that day’s forecast.</p>
	 <form action="' . $_SERVER['REQUEST_URI'] . '" method="post" class="forecastForm">
   
   	<select name="amount">
   		<option value="--">--</option>
   		<option value="100">100%</option>
   		<option value="95">95%</option>
   		<option value="90">90%</option>
   		<option value="85">85%</option>
   		<option value="80">80%</option>
   		<option value="75">75%</option>
   		<option value="70">70%</option>
   		<option value="65">65%</option>
   		<option value="60">60%</option>
   		<option value="55">55%</option>
   		<option value="50">50%</option>
   		<option value="45">45%</option>
   		<option value="40">40%</option>
   		<option value="35">35%</option>
   		<option value="30">30%</option>
   		<option value="25">25%</option>
   		<option value="20">20%</option>
   		<option value="15">15%</option>
   		<option value="10">10%</option>
   		<option value="5">5%</option>
   		<option value="0">0%</option>
   	</select>
   		<input type="submit" name="submit" value="Submit" class="forecastFormButton"/>
   		<input type="hidden" name="votenonce" value="' . wp_create_nonce( 'votin' ) . '" />
    </form></div>';
}


//
//Form Validation
//
function house_validation($amount) {
	global $reg_errors;
	$reg_errors = new WP_Error;

	if(empty($amount)) {
		$reg_errors->add('field', 'Required field missing');
	}
}

//
// Forecast voting main function
//
function complete_voting($amount, $tid, $user_level, $intime, $UID) {
	//three different sets of values, for 3 user levels
	if($user_level > 2) {
		$suffix = 'admin';
	} elseif ($user_level > 0) {
		$suffix = 'auth';
	} else {
		$suffix = 'sub';
	}
	$vaName = 'voteArray' . $suffix;
	$vc = 'voteCount' . $suffix;
	$last = 'lastTime' . $suffix;

	$new_meta_value = intval(stripslashes( $amount ));

	$lastTime = intval(get_post_meta($tid, $last, true));
	$vote_arr = get_post_meta($tid, $vaName, true);
	$vote_count = get_post_meta($tid, $vc, true);
	$voters = get_post_meta($tid, 'voters', true);
	$interval = 86400;

	$timeStamp = current_time('timestamp');
	$difference = $timeStamp - $lastTime;

	//if there's no meta, the graph is unitialized
	if(empty($lastTime) || empty($vote_arr) || empty($vote_count)) {
		$lastTime = $intime;
		$vote_arr = array(strval($lastTime-$interval) => $new_meta_value,
								strval($lastTime) => $new_meta_value);
		$vote_count = array(strval($lastTime-$interval) => 1,
							strval($lastTime) => 1);
		$voters = array($UID => $new_meta_value);

		add_post_meta($tid, $voters, true);
		add_post_meta($tid, $last, $lastTime, true);
		add_post_meta($tid, $vaName, $vote_arr, true);
		add_post_meta($tid, $vc, $vote_count, true); 
	//if the current time is less than one day ahead
	} elseif ($difference < $interval) {
		$vote_mod = 0;
		$vc_mod = 0;
		if(array_key_exists($UID, $voters)) {
			$vc_mod = -1;
			$vote_mod = $voters[$UID] - $new_meta_value; 
		}
		$vote_arr[strval($lastTime)] = $vote_arr[strval($lastTime)] + $new_meta_value + $vote_mod;
		$vote_count[strval($lastTime)] = $vote_count[strval($lastTime)] + 1 + $vc_mod;
		$voters[$UID] = $new_meta_value;
		update_post_meta($tid, 'voters', $voters);
		update_post_meta($tid, $vaName, $vote_arr);
		update_post_meta($tid, $last, $lastTime);
		update_post_meta($tid, $vc, $vote_count);
	//if there's a gap of at least one day
	} elseif ($difference >= $interval) {
		for($x = $lastTime + $interval; $x < $timeStamp; $x += $interval) {
			$vote_arr[strval($x)] = $vote_arr[strval($lastTime)];
			$vote_count[strval($x)] = $vote_count[strval($lastTime)];
		}
		$lastTime = $timeStamp - ($difference % $interval);
		$vote_arr[strval($lastTime)] = $new_meta_value;
		$vote_count[strval($lastTime)] = 1;
		update_post_meta($tid, $vaName, $vote_arr);
		update_post_meta($tid, $last, $lastTime);
		update_post_meta($tid, $vc, $vote_count);
	}

	$_POST['amount'] = '--';

}

//
// Calls main voting function
//
function custom_vote_function($tid, $user_level, $intime, $UID) {
	global $amount;	

	if(!empty($_POST)) {

		if ( isset($_POST['submit']) && $_POST['amount'] != '--' ) {
			
				house_validation($_POST['amount']);
			
				$amount = $_POST['amount'];

				complete_voting($amount, $tid, $user_level, $intime, $UID);
		}
	}
	house_form($amount);	
}