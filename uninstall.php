<?php
if( !defined('WP_UNINSTALL_PLUGIN') ) exit;
require dirname(__FILE__)."/alex_helpers.php";
global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS $wpdb->table_name");
$wpdb->query("DROP TABLE IF EXISTS $wpdb->table2_name");
$wpdb->query("DROP TABLE IF EXISTS $wpdb->table3_name");