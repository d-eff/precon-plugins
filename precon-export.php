<?php
/**
 * Plugin Name: Policy Recon Forecast Export
 * Plugin URI: http:policyrecon.com
 * Description: Custom post types for PolicyRecon.com
 * Version: 1.0.0
 * Author: DEF
 * License: GPL
 * Text Domain: precon-Export
*/

add_action('admin_menu', 'precon_forecast_menu');
add_action( 'admin_init', 'precon_export_run' );

function precon_forecast_menu() {
	add_management_page('Forecast Export', 'Forecast Export', 'administrator', 'precon_forecast_export', 'precon_forecast_export_display');
}

function precon_forecast_export_display() {
	?>	
	<div>
		<div>
			<h3>Export Policy Recon Forecast Data</h3>
			<p>In an easy to process csv format!</p>
		</div>
		<div>
			<a id="precon-fire-export" style="padding: 10px 20px; display: inline-block; margin: 5px 0; color: #EEE; background-color: #444; font-size: 14px; text-decoration: none;" href="/wp-admin/tools.php?page=precon_forecast_export&precon_forecast_export" >Export Forecasts</a>
		</div>
	</div>

<?php
};

function precon_export_run() {
	if ( ! is_super_admin() ) {
		return;
	}

	if ( ! isset( $_GET['precon_forecast_export'] ) ) {
		return;
	}

	$filename = 'forecast_data_' . time() . '.csv';

	$header_row = array(
		0 => 'ID',
		1 => 'Forecast Title',
		2 => 'Key',
		3 => 'Values',
	);

	$data_rows = array();

	global $wpdb;
	$results = $wpdb->get_results('select p.id, p.post_title, m.meta_key, m.meta_value from wp_fsko_posts p inner join wp_fsko_preconforecasts f on p.id=f.postid inner join wp_fsko_postmeta m on p.id=m.post_id where m.meta_key like \'historicalVotesAdmin\' OR m.meta_key like \'historicalVotesExpert\' OR m.meta_key like \'historicalVotesSub\'');

	$postlist = get_posts(array(
					'orderby'          => 'title',
					'order'            => 'ASC',
					'post_type'        => 'forecast',
					'posts_per_page'   => -1,
					));
				foreach($postlist as $post) {
					setup_postdata($post);
					$pid = $post->ID; 

					$row = array();
					$row[] = $pid;
					$row[] = $post->post_title;
					$row[] = "House";
					$votes = get_post_meta($pid, 'historicalVotesAdmin', true);
					foreach ($votes as $key => $value) {
						$row[] = $key . ' ' . $value;
					}
					$row[] = date('m/d/y', current_time('timestamp', $gmt = 0)) . ' ' . get_post_meta($pid, 'runningAverageAdmin', true);
					$data_rows[] = $row;

					$row = array();
					$row[] = $pid;
					$row[] = $post->post_title;
					$row[] = "Experts";
					$votes = get_post_meta($pid, 'historicalVotesExpert', true);
					foreach ($votes as $key => $value) {
						$row[] = $key . ' ' . $value;
					}
					$row[] = date('m/d/y', current_time('timestamp', $gmt = 0)) . ' ' . get_post_meta($pid, 'runningAverageExpert', true);
					$data_rows[] = $row;

					$row = array();
					$row[] = $pid;
					$row[] = $post->post_title;
					$row[] = "Subscribers";
					$votes = get_post_meta($pid, 'historicalVotesSub', true);
					foreach ($votes as $key => $value) {
						$row[] = $key . ' ' . $value;
					}
					$row[] = date('m/d/y', current_time('timestamp', $gmt = 0)) . ' ' . get_post_meta($pid, 'runningAverageSub', true);
					$data_rows[] = $row;
					wp_reset_postdata(); 	
				}
			

	// foreach ( $results as $res ) {
	// 	$row = array();
	// 	$row[] = $res->id;
	// 	$row[] = $res->post_title;
	// 	$mkey = $res->meta_key;

	// 	switch ($mkey) {
	// 		case 'historicalVotesAdmin':
	// 			$mkey = 'House';
	// 			break;
			
	// 		case 'historicalVotesExpert':
	// 			$mkey = 'Expert';
	// 			break;

	// 		case 'historicalVotesSub':
	// 			$mkey = 'Subscriber';
	// 			break;
	// 	}

	// 	$row[] = $mkey;
	// 	$vals = unserialize($res->meta_value);
	// 	foreach ($vals as $key => $value) {
	// 		$row[] = $key . ' ' . $value;	
	// 	}

	// 	$data_rows[] = $row;
	// }

	header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
	header( 'Content-Description: File Transfer' );
	header( 'Content-type: text/csv' );
	header( "Content-Disposition: attachment; filename={$filename}" );
	header( 'Expires: 0' );
	header( 'Pragma: public' );

	$fh = @fopen( 'php://output', 'w' );

	fprintf( $fh, chr(0xEF) . chr(0xBB) . chr(0xBF) );

	fputcsv( $fh, $header_row );

	foreach ( $data_rows as $data_row ) {
		fputcsv( $fh, $data_row );
	}

	fclose( $fh );
	
	die();
}
