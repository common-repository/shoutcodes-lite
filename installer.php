<?php

/**
 * Create Database Table.
 *
 * @since 1.0.1
 */
function shoutcodes_install() {
	set_transient( 'wpb-shoutcodes-admin-notice', true, 5 );
	global $wpdb;
	$table_name = $wpdb->prefix . 'shoutcodes';
	$shoutcodes_db_version = '1.0.0';

	// If table not exist create a table.
	if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE  %s', $table_name ) ) != $table_name ) {
		$sql = 'CREATE TABLE ' . $table_name . ' (
								id mediumint(11) NOT NULL AUTO_INCREMENT,
								target varchar(255) NOT NULL,
								key1 varchar(255) NOT NULL,
								docount int(1) NOT NULL,
								hitcount mediumint(15) NOT NULL,
								UNIQUE KEY id (id)
				);';
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		$demotarget = 'https://www.example.com/';
		$demokey    = 'example';

		$wpdb->insert( $table_name, array(
			'target' => $demotarget,
			'key1'   => $demokey,
		) );
		add_option( 'shoutcodes_db_version', $shoutcodes_db_version );

		$_option = array( 'url_trigger' => 'go' );
		update_option( 'shoutcodes_main', $_option );

	}

}
