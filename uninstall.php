<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://github.com/sensahin
 * @since      1.0.0
 *
 * @package    Wp_Ai_Content_Generator
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}


global $wpdb;
$wpaicg_all_plugins = get_plugins();
$wpaicgPlugins = 0;
foreach($wpaicg_all_plugins as $key=>$wpaicg_all_plugin){
    if(strpos($key, 'gpt3-ai-content-generator') !== false){
        $wpaicgPlugins++;
    }
}
if($wpaicgPlugins == 1){
    $wpaicgTable = $wpdb->prefix . 'wpaicg';
    $wpdb->query( "TRUNCATE TABLE $wpaicgTable" );

    $wpaicgTable2 = $wpdb->prefix . 'wpaicg_log';
    $wpdb->query( "TRUNCATE TABLE $wpaicgTable2" );

    $wpdb->query( "DROP TABLE IF EXISTS $wpaicgTable" );
}
