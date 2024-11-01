<?php

/**
 * Manage ShourCodes.
 *
 * @since 1.0.0
 */
class ShoutCodes_Manage {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		$this->managemenu();
	}

	/**
	 * Manage all shourcodes.\
	 *
	 * @since 1.0.0
	 */
	function managemenu() {
		echo '<div class="wrap">';

		if ( ! isset( $_GET['editamb'] ) ) {

			// Add Redirect.
			if ( isset( $_GET['saveamb'] ) && 'yes' == $_GET['saveamb'] ) {
				$ambkey     = isset( $_POST['Key'] ) ? sanitize_key( wp_unslash( $_POST['Key'] ) )  : '';
				$ambtarget  = isset( $_POST['Target'] ) ? esc_url_raw( wp_unslash( $_POST['Target'] ) ) : '' ;
				$ambkey     = preg_replace( '[^a-zA-Z0-9._-]', '', $ambkey );
				$ambdocount = isset( $_POST['DoCount'] ) ? wp_unslash( $_POST['DoCount'] ) : '';

				if ( 'on' == $ambdocount ) {
					$ambdocount = 1;
				} else {
					$ambdocount = 0;
				}
				if ( '' != $ambkey && '' != $ambtarget && 'http://' != $ambtarget ) {
					global $wpdb;
					$table_name = $wpdb->prefix . 'shoutcodes';
					$wpdb->insert( $table_name, array(
						'target'  => $ambtarget,
						'key1'    => $ambkey,
						'docount' => $ambdocount,
					) );
					echo '<div id="message" class="updated fade"><p>Redirect added successfully.</p></div>';
				} else { echo '<div id="message" class="updated fade"><p>Could not add redirect. You did not properly fill-out both fields!</p></div>';
				}
			}

			// Delete Redirect.
			if ( isset( $_GET['deleteamb'] ) && '' != $_GET['deleteamb'] ) {
				$ambid = sanitize_text_field( wp_unslash( $_GET['deleteamb'] ) );
				echo '<div id="message" class="updated fade"><p>Are you sure you want to delete the redirect? <a href="' . shoutcodes_URL . '&deleteambconf=yes&ambid=' . $ambid . '">Yes</a> &nbsp; <a href="' . shoutcodes_URL . '">No!</a></p></div>';
			}
			if ( isset( $_GET['deleteambconf'] ) && '' != $_GET['deleteambconf'] ) {
				$ambid = sanitize_text_field( wp_unslash( $_GET['ambid'] ) );
				global $wpdb, $table_prefix;
				$table_name = $wpdb->prefix . 'shoutcodes';
				$wpdb->delete( $table_name, array( 'id' => $ambid ) );
				echo '<div id="message" class="updated fade"><p>Redirect removed successfully.</p></div>';
			}

			// Uninstall plugin.
			if ( isset( $_GET['uninstallamb'] ) && 'yes' == $_GET['uninstallamb'] ) {
				echo '<div id="message" class="updated fade"><p><strong>Are you sure you want to delete the shoutcodess database entries? You will lose all of your redireccts!</strong><br/><a href="' . shoutcodes_URL . '&uninstallamb=yes&confirm=yes">Yes, delete.</a> &nbsp; <a href="index.php">NO!</a></p></div>';
				if ( 'yes' == $_GET['uninstallamb'] && 'yes'== $_GET['confirm'] ) {
					global $wpdb, $table_prefix;
					$table_name = $wpdb->prefix . 'shoutcodes';
					$uninstallamb = 'DROP TABLE ' . $table_name;
					$results = $wpdb->query( $uninstallamb );
					echo '<div id="message" class="updated fade"><p>shoutcodess has removed its database entries. Now deactivate the plugin.</p></div>';
					return;
				}
			}

			// Update Redirect
			if ( isset( $_GET['editambconf'] ) && 'yes' == $_GET['editambconf'] && isset( $_POST['id'] ) ) {
				$ambpostid     = sanitize_text_field( wp_unslash( $_POST['id'] ) );
				$ambpostkey    = sanitize_key( wp_unslash( $_POST['Key'] ) );
				$ambposttarget = esc_url_raw( wp_unslash( $_POST['Target'] ) );

				$ambpostdocount = isset( $_POST['DoCount'] ) ? sanitize_text_field( wp_unslash( $_POST['DoCount'] ) ) : '';
				$ambpostkey = preg_replace( '[^a-zA-Z0-9._-]', '', $ambpostkey );
				if ( 'on' == $ambpostdocount ) { $ambpostdocount = 1;
				} else { $ambpostdocount = 0; }
				if ( $ambpostkey != '' && $ambposttarget != '' && $ambposttarget != 'http://' ) {
					global $wpdb;
					$table_name = $wpdb->prefix . 'shoutcodes';
					$wpdb->update( $table_name, array(
							'target'  => $ambposttarget,
							'key1'    => $ambpostkey,
							'docount' => $ambpostdocount,
						),
							array(
								'id' => $ambpostid
							 )
					);
					echo '<div id="message" class="updated fade"><p>Redirect saved successfully.</p></div>';
				} else { echo '<div id="message" class="updated fade"><p>Could not update redirect. You did not properly fill-out a field!</p></div>'; }
			}

			// Reset Redirect Counter
			if ( isset( $_GET['ambresetcount'] ) && $_GET['ambresetcount'] == 'yes' ) {
				$ambid = sanitize_text_field( wp_unslash( $_GET['ambid'] ) );
				echo '<div id="message" class="updated fade"><p>Are you sure you want to reset the hit count for the redirect? <a href="' . shoutcodes_URL . '&ambresetcountconf=yes&ambid=' . $ambid . '">Yes</a> &nbsp; <a href="' . shoutcodes_URL . '">No!</a></p></div>';
			}

			if ( isset( $_GET['ambresetcountconf'] ) && $_GET['ambresetcountconf'] == 'yes' ) {
				$ambid = $_GET['ambid'];
				global $wpdb;
				$table_name = $wpdb->prefix . 'shoutcodes';
				$wpdb->update( $table_name, array(
						'hitcount'  => 0,
					),
						array(
							'id' => $ambid
						 )
				);
			}

			// Form
			echo '<h2>Add ShoutCodes</h2>';
			echo '<div>';
			echo '<form method="post" action="' . shoutcodes_URL . '&saveamb=yes">';
			echo '<table class="form-table">';
			echo '<tr class="form-field form-required">';
			echo '<th scope="row" valign="top"><label for="Key">Redirection Key</label></th>';
			echo '<td><input type="text" name="Key" value="" />';
			echo ' <br />The text after /go/ that triggers the redirect (e.g. yourblog.com/go/thekey/).</td>';
			echo '</tr>';
			echo '<tr class="form-field form-required">';
			echo '<th scope="row" valign="top"><label for="Target">Target URL</label></th>';
			echo '<td><input type="text" name="Target" value="http://" />';
			echo ' <br />The URL you wish to redirect to. "http://" is required.</td>';
			echo '</tr>';
			echo '<tr class="form-field form-required">';
			echo '<th scope="row" valign="top"><label for="DoCount">Count hits?</label></th>';
			echo ' <td><input type="checkbox" name="DoCount" style="width:1em" /> Yes, track the number of times this redirect is used.</td>';
			echo '</tr>';
			echo '</table>';
			echo ' <p class="submit"><input type="submit" name="Submit" class="button-primary" value="Add Redirect" /></p>';
			echo '</form>';
			echo '<br/>';
			echo '</div>';

			// List
			echo '<h2>Manage ShoutCodes</h2><br />';
			echo '<div class="subsubsub" style="margin-top:-10px;"><strong>Sort by:</strong> <a href="' . shoutcodes_URL . '">Date Added</a> | <a href="' . shoutcodes_URL . '&sortby=key">Key</a> | <a href="' . shoutcodes_URL . '&sortby=hits">Hits</a></div>';
			echo '<div>';
			echo '<table class="widefat">';
			echo '<thead>';
			echo '<tr>';
			echo '<th scope="col"><div style="text-align: center">Key</div></th>';
			echo '<th scope="col"><div style="text-align: center">Target</div></th>';
			echo '<th scope="col"><div style="text-align: center">Hits</div></th>';
			echo '<th scope="col"></th>';
			echo '<th scope="col"></th>';
			echo '</tr>';
			echo '</thead>';
			echo '<tbody id="the-list">';

			global $wpdb, $table_prefix;
			$table_name = $wpdb->prefix . 'shoutcodes';
			// $trigger = get_option( 'shoutcodes_URL_trigger' );
			$_options = get_option('shoutcodes_main', array() );
			$trigger = array_key_exists( 'url_trigger', $_options ) ? $_options['url_trigger'] : '';
			if ( $trigger == '' ) { $trigger = 'go'; }
			$sortby = isset( $_GET['sortby'] ) ? sanitize_text_field( wp_unslash( $_GET['sortby'] ) ) : '';
			if ( 'key' == $sortby) {
				$sort = 'key1 ASC';
			} elseif ( 'hits' == $sortby ) {
				$sort = 'hitcount DESC';
			} else {
				$sort = 'id DESC';
			}
			$shoutcodess = $wpdb->get_results( $wpdb->prepare( "SELECT id, target, key1, docount, hitcount FROM {$table_name} WHERE key1 != '' ORDER BY %s", $sort ), OBJECT );

			$basewpurl = get_option( 'siteurl' );
			if ( $shoutcodess ) :
				foreach ( $shoutcodess as $shoutcodes ) :
					if ( 1 != $shoutcodes->docount ) { $shoutcodes->hitcount = ''; }
					echo '<tr class="alternate"> <td><strong>' . $shoutcodes->key1 . '</strong><br /><small>' . $basewpurl . '/' . $trigger . '/' . $shoutcodes->key1 . '/</small></td> <td>' . $this->truncate( $shoutcodes->target ) . '</td> <td style="text-align: center">' . $shoutcodes->hitcount . '</td> <td><a href="' . shoutcodes_URL . '&editamb=' . $shoutcodes->id . '">Edit</a></td> <td><a href="' . shoutcodes_URL . '&deleteamb=' . $shoutcodes->id . '" class="delete">Delete</a></td> </tr>';
				endforeach;
			  else :
					echo "<tr><td colspan='3'>Not Found</td></tr>";
			  endif;

				echo '</tbody>';
				echo '</table>';
				echo '</div>';

		}

		if ( isset( $_GET['editamb'] ) && $_GET['editamb'] != '' ) {
			$ambid = sanitize_text_field( wp_unslash( $_GET['editamb'] ) );
			global $wpdb;
			$table_name = $wpdb->prefix . 'shoutcodes';

			$editquery = $wpdb->prepare( "SELECT id, target, key1, docount, hitcount FROM {$table_name} WHERE id=%s", $ambid );
			$shoutcodes = $wpdb->get_row( $editquery, OBJECT );
			echo '<div class="wrap">';
			echo '<h2>Edit shoutcodes</h2>';
			echo '<div>';
			echo '<form method="post" action="' . shoutcodes_URL . '&editambconf=yes">';
			echo '<table class="form-table">';
			echo '<table class="form-table">';
			echo '<tr class="form-field form-required">';
			echo '<th scope="row" valign="top"><label for="Key">Redirection Key</label></th>';
			echo '<td><input type="text" name="Key" value="' . $shoutcodes->key1 . '" />';
			echo '<br />The text after /go/ that triggers the redirect (e.g. yourblog.com/go/thekey/).</td>';
			echo '</tr>';
			echo '<tr class="form-field form-required">';
			echo '<th scope="row" valign="top"><label for="Target">Target URL</label></th>';
			echo '<td><input type="text" name="Target" value="' . $shoutcodes->target . '" />';
			echo '<br />The URL you wish to redirect to. "http://" is required.</td>';
			echo '</tr>';
			echo '<tr class="form-field form-required">';
			echo '<th scope="row" valign="top"><label for="DoCount">Count hits?</label></th>';
			echo ' <td><input type="checkbox" name="DoCount"';
			if ( $shoutcodes->docount == 1 ) { echo 'checked="checked"';
			} echo '/> Yes, track the number of times this redirect is used. &nbsp;&nbsp; <a href="' . shoutcodes_URL . '&ambresetcount=yes&ambid=' . $ambid . '">Reset count</a></td>';
			echo '</tr>';
			echo '</table>';
			echo ' <input type="hidden" name="id" value="' . $ambid . '" />';
			echo ' <p class="submit"><input type="submit" name="Submit" class="button" value="Edit Redirect" /></p>';
			echo '</form>';
			echo '</div>';
			echo '</div>';
		}
		echo '</div>';
	}

	/**
	 * Beautify the link
	 *
	 * @since 1.0.0
	 */
	function truncate( $text ) {
		if ( strlen( $text ) > 79 ) {
			$text = $text . ' ';
			$text = substr( $text,0,80 );
			$text = $text . '...';
			return $text;
		} else {
			return $text;
		}
	}

}

new ShoutCodes_Manage();
