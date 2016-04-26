<?php
if( !defined('WP_UNINSTALL_PLUGIN') ) exit;
global $wpdb;

$table_names = array( 'plg_sp_polls', 'plg_sp_answs', 'plg_sp_ip' );
if( count( $table_names ) > 0 ) {
	foreach( $table_names as $table_name ) {
		$table = $wpdb->prefix . $table_name;
		$wpdb->query( "DROP TABLE IF EXISTS $table" );
	}
}