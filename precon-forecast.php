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

global $precon_db_version;
$precon_db_version = '1.0';

//
// Install function. Creates DB and sets up cron job.
//
function precon_forecast_install() {
	global $wpdb;
	global $precon_db_version;

	$table_name = $wpdb->prefix . 'preconforecasts';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		postid mediumint(9) unsigned NOT NULL UNIQUE default '0',
		UNIQUE KEY id (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	add_option( 'precon_db_version', $precon_db_version );

    $timeoffset = strtotime('midnight')+(4.9*HOUR_IN_SECONDS);
    if($timeoffset < time()) $timeoffset+(24*HOUR_IN_SECONDS);
    wp_schedule_event($timeoffset, 'daily', 'preconforecastcronhookact');
}
register_activation_hook( __FILE__, 'precon_forecast_install' );

add_action('preconforecastcronhookact', 'precon_forecast_cron_hook');

register_deactivation_hook( __FILE__, 'precon_forecast_deactivation' );

/**
 * On deactivation, remove all functions from the scheduled action hook.
 */
function precon_forecast_deactivation() {
	wp_clear_scheduled_hook( 'preconforecastcronhookact' );

	global $wpdb;
	global $precon_db_version;

	$table_name = $wpdb->prefix . 'preconforecasts';
	
	$sql = "DROP TABLE $table_name;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}

function precon_forecast_cron_hook() {
	global $wpdb;

	$table_name = $wpdb->prefix . 'preconforecasts';

	$forecasts = $wpdb->get_results("
		SELECT postid
		FROM $table_name;
	");

	if($forecasts){
		foreach ($forecasts as $value) {
			$post = get_post($value);
			$pid = intval($post->postid);
			$date = get_post_meta($pid, 'currentDate', true);	

			$runningAverageAdmin = get_post_meta($pid, 'runningAverageAdmin', true);
			$runningAverageExpert = get_post_meta($pid, 'runningAverageExpert', true);
			$runningAverageSub = get_post_meta($pid, 'runningAverageSub', true);

			$historicalVotesAdmin = get_post_meta($pid, 'historicalVotesAdmin', true);
			$historicalVotesExpert = get_post_meta($pid, 'historicalVotesExpert', true);
			$historicalVotesSub = get_post_meta($pid, 'historicalVotesSub', true);

			$historicalVotesAdmin[$date] = intval($runningAverageAdmin);
			$historicalVotesExpert[$date] = intval($runningAverageExpert);
			$historicalVotesSub[$date] = intval($runningAverageSub);

			update_post_meta($pid, 'historicalVotesAdmin', $historicalVotesAdmin);
			update_post_meta($pid, 'historicalVotesExpert', $historicalVotesExpert);
			update_post_meta($pid, 'historicalVotesSub', $historicalVotesSub);

			$votersAdmin = get_post_meta($pid, 'votersAdmin', true);
			$votersExpert = get_post_meta($pid, 'votersExpert', true);
			$votersSub = get_post_meta($pid, 'votersSub', true);

			$votersExpiryAdmin = get_post_meta($pid, 'votersExpiryAdmin', true);
			$votersExpiryExpert = get_post_meta($pid, 'votersExpiryExpert', true);
			$votersExpirySub = get_post_meta($pid, 'votersExpirySub', true);

			$dailyTotalAd = intval(get_post_meta($pid, 'dailyTotalAdmin', true));
			$dailyTotalEx = intval(get_post_meta($pid, 'dailyTotalExpert', true));
			$dailyTotalSub = intval(get_post_meta($pid, 'dailyTotalSub', true));

			$updateFlag = false;

			foreach ($votersExpiryAdmin as $key => $votersExp) {
				intval($votersExpiryAdmin[$key])--;
				if(intval($votersExpiryAdmin[$key]) <= 0) {
					$vote = intval($votersAdmin[$key]);
					unset($votersAdmin[$key]);
					$dailyTotalAd -= $vote;
					unset($votersExpiryAdmin[$key]);
					$updateFlag = true;
				}
			}
			if($updateFlag) {
				update_post_meta($pid, 'dailyTotalAdmin', $dailyTotalAd);
				$runningAverageAd = $dailyTotalAd/count($votersAdmin);
				update_post_meta($pid, 'runningAverageAdmin', $runningAverageAd);
				update_post_meta($pid, 'votersExpiryAdmin', $votersExpiryAdmin);
				update_post_meta($pid, 'votersAdmin', $votersAdmin);
				$updateFlag = false;
			}

			foreach ($votersExpiryExpert as $key => $votersExp) {
				intval($votersExpiryExpert[$key])--;
				if(intval($votersExpiryExpert[$key]) <= 0) {
					$vote = intval($votersExpert[$key]);
					unset($votersExpert[$key]);
					$dailyTotalEx -= $vote;
					unset($votersExpiryExpert[$key]);
			
				}
			}
			if($updateFlag) {
				update_post_meta($pid, 'dailyTotalExpert', $dailyTotalEx);
				$runningAverageEx = $dailyTotalEx/count($votersExpert);
				update_post_meta($pid, 'runningAverageExpert', $runningAverageEx);
				update_post_meta($pid, 'votersExpiryExpert', $votersExpiryExpert);		
				update_post_meta($pid, 'votersExpert', $votersExpert);
				$updateFlag = false;
			}

			foreach ($votersExpirySub as $key => $votersExp) {
				intval($votersExpirySub[$key])--;
				if(intval($votersExpirySub[$key]) <= 0) {
					$vote = intval($votersSub[$key]);
					unset($votersSub[$key]);
					$dailyTotalSub -= $vote;
					unset($votersExpirySub[$key]);
					$updateFlag = true;
				}
			}
			if($updateFlag) {
				update_post_meta($pid, 'dailyTotalSub', $dailyTotalSub);
				$runningAverageSub = $dailyTotalSub/count($votersSub);
				update_post_meta($pid, 'runningAverageSub', $runningAverageSub);
				update_post_meta($pid, 'votersExpirySub', $votersExpirySub);
				update_post_meta($pid, 'votersSub', $votersSub);
				$updateFlag = false;
			}
		
			$newDate = date('m/d/y', current_time('timestamp', $gmt = 0));
			if($newDate == $date) {
				$dateTime = new DateTime('tomorrow');
				$date = $dateTime->format('m/d/y');
			} else {
				$date = $newDate;
			}
			update_post_meta($pid, 'currentDate', $date);

		}
	}

}

add_action( 'init', 'precon_forecast_init' );
add_action( 'save_post_forecast', 'precon_q_save_forecast', 99, 2 );
//
//Create Forecast Post Type
//
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
		'register_meta_box_cb' => 'precon_add_forecasts_metaboxes',
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
	             $role->add_cap( 'delete_precon_forecast' );
	             $role->add_cap( 'delete_others_precon_forecasts' );
	             $role->add_cap( 'delete_private_precon_forecasts' );
	             $role->add_cap( 'delete_published_precon_forecasts' );
		}
}

//Add Metaboxes
function precon_add_forecasts_metaboxes() {
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
		
	</p>
<?php }
function precon_expert_box( $object, $box ) { ?>
	<p>
		<label for="expert">Expert Analysis</label>
		<br />
		<textarea name="expert" id="expert" cols="60" rows="4" tabindex="30" style="width: 97%;"><?php echo esc_html( get_post_meta( $object->ID, 'Expert', true ), 1 ); ?></textarea>
		
	</p>
<?php }
function precon_community_box( $object, $box ) { ?>
	<p>
		<label for="community">Community Analysis</label>
		<br />
		<textarea name="community" id="community" cols="60" rows="4" tabindex="30" style="width: 97%;"><?php echo esc_html( get_post_meta( $object->ID, 'Community', true ), 1 ); ?></textarea>
		
<?php }

//Callback for Saving Forecast
function precon_q_save_forecast( $post_id, $post ) {

	if ( !current_user_can( 'edit_post', $post_id ) )
		return $post_id;

   	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $post_id;
    }

    if(isset($_POST)) {
		$meta_value = get_post_meta( $post_id, 'House', true );
		$new_meta_value = stripslashes( $_POST['house'] );

		if ( $new_meta_value && !isset($meta_value) ) {
			add_post_meta( $post_id, 'House', $new_meta_value, true );
		} elseif ( $new_meta_value != $meta_value ) {
			update_post_meta( $post_id, 'House', $new_meta_value );
		} elseif ( '' == $new_meta_value && $meta_value ) {
			delete_post_meta( $post_id, 'House', $meta_value );
		}		

		$meta_value = get_post_meta( $post_id, 'Expert', true );
		$new_meta_value = stripslashes( $_POST['expert'] );

		if ( $new_meta_value && !isset($meta_value) ) {
			add_post_meta( $post_id, 'Expert', $new_meta_value, true );
		} elseif ( $new_meta_value != $meta_value ) {
			update_post_meta( $post_id, 'Expert', $new_meta_value );
		} elseif ( '' == $new_meta_value && $meta_value ) {
			delete_post_meta( $post_id, 'Expert', $meta_value );
		}

		$meta_value = get_post_meta( $post_id, 'Community', true );
		$new_meta_value = stripslashes( $_POST['community'] );

		if ( $new_meta_value && !isset($meta_value) ) {
			add_post_meta( $post_id, 'Community', $new_meta_value, true );
		} elseif ( $new_meta_value != $meta_value ) {
			update_post_meta( $post_id, 'Community', $new_meta_value );
		} elseif ( '' == $new_meta_value && $meta_value ) {
			delete_post_meta( $post_id, 'Community', $meta_value );
		}
	}



	//Initialize voting stuff
	$voters = get_post_meta($post_id, 'votersExpert', true);
	
	//If we don't have a voters list, we can assume we're unitialized
	if(empty($voters)){
		//initialize post meta
		$votersAdmin = array();
		$votersExpert = array();
		$votersSub = array();
		add_post_meta($post_id, 'votersAdmin', $votersAdmin, true);
		add_post_meta($post_id, 'votersExpert', $votersExpert, true);
		add_post_meta($post_id, 'votersSub', $votersSub, true);

		$votersExpiryAdmin = array();
		$votersExpiryExpert = array();
		$votersExpirySub = array();
		add_post_meta($post_id, 'votersExpiryAdmin', $votersExpiryAdmin, true);
		add_post_meta($post_id, 'votersExpiryExpert', $votersExpiryExpert, true);
		add_post_meta($post_id, 'votersExpirySub', $votersExpirySub, true);

		$dailyTotalAdmin = 0;
		$dailyTotalExpert = 0;
		$dailyTotalSub = 0;
		add_post_meta($post_id, 'dailyTotalAdmin', $dailyTotalAdmin, true);
		add_post_meta($post_id, 'dailyTotalExpert', $dailyTotalExpert, true);
		add_post_meta($post_id, 'dailyTotalSub', $dailyTotalSub, true);

		$runningAverageAdmin = 0;
		$runningAverageExpert = 0;
		$runningAverageSub = 0;
		add_post_meta($post_id, 'runningAverageAdmin', $runningAverageAdmin, true);
		add_post_meta($post_id, 'runningAverageExpert', $runningAverageExpert, true);
		add_post_meta($post_id, 'runningAverageSub', $runningAverageSub, true);

		$historicalVotesAdmin = array();
		$historicalVotesExpert = array();
		$historicalVotesSub = array();
		add_post_meta($post_id, 'historicalVotesAdmin', $historicalVotesAdmin, true);
		add_post_meta($post_id, 'historicalVotesExpert', $historicalVotesExpert, true);
		add_post_meta($post_id, 'historicalVotesSub', $historicalVotesSub, true);

		add_post_meta($post_id, 'currentDate', date('m/d/y', current_time('timestamp', $gmt = 0)), true);

		//add to update list
		global $wpdb;
		$table_name = $wpdb->prefix . 'preconforecasts';
		$wpdb->replace($table_name, array('postid' => $post_id), '%s');
	}

}

//
//Process data and output to graphing lib
//
function precon_forecast_getData($tid, $suffix) {
	$runningAverageMetaName = 'runningAverage' . $suffix;
	$historyMetaName = 'historicalVotes' . $suffix;

	$runningAverage = get_post_meta($tid, $runningAverageMetaName, true);
	$history = get_post_meta($tid, $historyMetaName, true);

	$dates = '';
	$vals = '';
	if(!empty($history)) {
		foreach ($history as $key => $value) {
			$dates .= $key . ' ';
			$vals .= $value . ' ';
		}
	}

	$dates .= date('m/d/y', current_time('timestamp', $gmt = 0));
	$vals .= $runningAverage;
	echo $dates . '+';
	echo $vals;

}

//Enqueue Forecast Scripts
function add_forecast_scripts() {
	wp_enqueue_script(
		'forecast',
		plugins_url( '/precon-forecast.js' , __FILE__ )
	);
	wp_enqueue_script(
		'charts',
		plugins_url('/Chart.min.js', __FILE__ )
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

function house_form($amount, $UID, $suffix, $tid) {
	$votersMetaName = 'voters' . $suffix;
	$votersExpiryMetaName = 'votersExpiry' . $suffix;
	$vote = get_post_meta($tid, $votersMetaName, true);
	$voteExp = get_post_meta($tid, $votersExpiryMetaName, true);

	if(!empty($vote) && array_key_exists($UID, $vote)) {
		$current = 'Your current forecast: ' . $vote[$UID] . '<br> Days to expiry: ' . $voteExp[$UID];
	} else {
		$current = 'You do not currently have a forecast.';
	}

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
    </form>
    <p class="voteInstr">' . $current . '</p>
    </div>';
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
function complete_voting($amount, $tid, $suffix, $intime, $UID) {

	$votersMetaName = 'voters' . $suffix;
	$dailyTotalMetaName = 'dailyTotal' . $suffix;
	$runningAverageMetaName = 'runningAverage' . $suffix;
	$votersExpiryMetaName = 'votersExpiry' . $suffix;
	$new_vote = intval(stripslashes( $amount ));

	$voters = get_post_meta($tid, $votersMetaName, true);
	$votersExpiry = get_post_meta($tid, $votersExpiryMetaName, true);
	$dailyTotal = intval(get_post_meta($tid, $dailyTotalMetaName, true));
	
	if(array_key_exists($UID, $voters)) {
		$old_vote = $voters[$UID];
		$dailyTotal -= $old_vote;
	} 

	$voters[$UID] = $new_vote;
	$votersExpiry[$UID] = 10;
	$dailyTotal += $new_vote;

	$runningAverage = $dailyTotal/count($voters);
	
	update_post_meta($tid, $votersMetaName, $voters);
	update_post_meta($tid, $votersExpiryMetaName, $votersExpiry);
	update_post_meta($tid, $dailyTotalMetaName, $dailyTotal);
	update_post_meta($tid, $runningAverageMetaName, $runningAverage);

	$_POST['amount'] = '--';

}

//
// Calls main voting function
//
function custom_vote_function($tid, $user_level, $intime, $UID) {
	global $amount;	

	//three different sets of values, for 3 user levels
	if($user_level > 2) {
		$suffix = 'Admin';
	} elseif ($user_level > 0) {
		$suffix = 'Expert';
	} else {
		$suffix = 'Sub';
	}

	if(!empty($_POST)) {

		if ( isset($_POST['submit']) && $_POST['amount'] != '--' ) {
			
				house_validation($_POST['amount']);
			
				$amount = $_POST['amount'];

				complete_voting($amount, $tid, $suffix, $intime, $UID);
		}
	}
	house_form($amount, $UID, $suffix, $tid);	
}
