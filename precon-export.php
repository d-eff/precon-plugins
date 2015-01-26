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
		2 => 'User',
		3 => 'Values',
	);

	$data_rows = array();

	$postlist = get_posts(array(
					'orderby'          => 'title',
					'order'            => 'ASC',
					'post_type'        => 'forecast',
					'posts_per_page'   => -1,
				));
	foreach($postlist as $post) {
		setup_postdata($post);
		$pid = $post->ID; 
		$votes = get_post_meta($pid, 'votersExpert', true);
		$exp = get_post_meta($pid, 'votersExpiryExpert', true);
		$date = current_time('timestamp', $gmt = 0);
		
		foreach ($votes as $key => $value) {
			$row = array();
		 	$user = get_user_by('id', $key);
			$row[] = $pid;
			$row[] = $post->post_title;
		 	$row[] = $user->first_name . ' ' . $user->last_name;
		 	$row[] = $value;
		 	$adjDate = date('m/d/y', $date - (86400 * (10 - intval($exp[$key]))));
		 	$row[] = $adjDate;

		 	$data_rows[] = $row;
		}
		wp_reset_postdata(); 
	}

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
