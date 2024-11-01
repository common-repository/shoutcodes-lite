<?php
/*
Plugin Name: ShoutCodes lite
Plugin URI: https://shoutcodes.com/link-management-plugin/
Description: The fastest & powerful affiliate link management plugin. Create branded cloaked URL for your domain name.
Author: denharsh
Author URI: https://Shoutcodes.com
Version: 1.0.1
Text Domain: shoutcodes
Domain Path: /languages/
*/

// Copyright (c) 2016 ShoutCodes. All rights reserved.
//
// Released under the GPL license
// http://www.opensource.org/licenses/gpl-license.php
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// **********************************************************************
define( 'shoutcodes_URL', admin_url( 'admin.php?page=shoutcodes' ) );
define( 'ShoutCode_Plugin_Directory', dirname( __FILE__ ) );

// Install the database at plugin-activation.
if ( is_admin() ) {
	include dirname( __FILE__ ) . '/installer.php';
}
register_activation_hook( __FILE__, 'shoutcodes_install' );


add_action( 'plugins_loaded', 'run_shoutcodes' );

/**
 * Load plugin all files.
 *
 * @since 1.0.0
 */
function run_shoutcodes() {

	require dirname( __FILE__ ) . '/classes/shoutcodes-main.php';

	new ShoutCodes();
}
