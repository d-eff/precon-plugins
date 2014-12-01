<?php
//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
    exit();

global $wpdb;
$table_name = $wpdb->prefix . 'preconforecasts';
$wpdb->query( "DROP TABLE $table_name" );
