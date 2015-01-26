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
		0 => 'id',
		1 => 'title',
		2 => 'value',
		3 => 'key',
	);

	$data_rows = array();

	global $wpdb, $bp;
	$results = $wpdb->get_results('select p.id, p.post_title, m.meta_value, m.meta_key from wp_fsko_posts p inner join wp_fsko_preconforecasts f on p.id=f.postid inner join wp_fsko_postmeta m on p.id=m.post_id where m.meta_key like \'historicalVotesAdmin\' OR m.meta_key like \'historicalVotesExpert\' OR m.meta_key like \'historicalVotesSub\'');

	foreach ( $results as $res ) {
		$row = array();
		$row[0] = $res->id;
		$row[1] = $res->post_title;
		$row[2] = $res->meta_value;
		$row[3] = $res->meta_key;

		$data_rows[] = $row;
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
