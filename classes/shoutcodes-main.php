<?php

/**
 * Display Class of ShoutCodes.
 *
 * @since 1.0.0
 */
class ShoutCodes {

	private $settings_section;

	/**
	 * Constructor of class.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		add_action( 'init', array( $this, 'redirect_query' ), 1 );
		add_filter( 'favorite_actions', array( $this, 'shoutcodes_add_menu_favorite' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'add_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'admin_menu', array( $this, 'add_menu_pages' ) );

		add_action( 'admin_init', array( $this, 'process_import' ) );
		add_action( 'admin_notices', array( $this, 'success_message' ) );
		add_action( 'admin_notices', array( $this, 'deactivate_gocodes_notice' ) );
		add_action( 'admin_notices', array( $this, 'import_gocodes' ) );
		add_action( 'admin_init', array( $this, 'deactivate_gocodes' ) );

		include( ShoutCode_Plugin_Directory . '/classes/class.settings-api.php' );
		include( ShoutCode_Plugin_Directory . '/classes/shoutcodes-settings.php' );
		$this->settings_section = new ShoutCodes_Setting();

	}


	/**
	 * Add Menu pages.
	 *
	 * @since 1.0.0
	 */
	function add_menu_pages() {

		add_menu_page( 'ShoutCodes', 'ShoutCodes', 'manage_options', 'shoutcodes', array( $this, 'manage_pages' ), 'dashicons-admin-links', 50 );
		add_submenu_page( 'shoutcodes', 'ShoutCodes', 'ShoutCodes', 'manage_options', 'shoutcodes',  array( $this, 'manage_pages' ) );
		add_submenu_page( 'shoutcodes', 'Settings', 'Settings', 'manage_options', 'shoutcodes-settings', array( $this, 'manage_pages' ) );

	}

	/**
	 * Manage menu pages.
	 *
	 * @since 1.0.0
	 */
	function manage_pages() {

		$screen = get_current_screen();
		if ( 'toplevel_page_shoutcodes' == $screen->base ) {
			require ShoutCode_Plugin_Directory . '/classes/shoutcodes-manage.php';
		} elseif ( 'shoutcodes_page_shoutcodes-settings' == $screen->base ) {
			$this->settings_section->plugin_page();
		}

	}

	/**
	 * Add Admin Scripts.
	 *
	 * @since 1.0.0
	 */
	function admin_scripts() {
		wp_enqueue_script( 'shout-js', plugins_url( 'assets/js/admin-main.js',dirname( __FILE__ ) ), false );
		wp_enqueue_style( 'shout-styles', plugins_url( 'assets/css/style.css' , dirname( __FILE__ ) ), false );
	}

	/**
	 * Add Scripts.
	 *
	 * @since 1.0.0
	 */
	function add_scripts() {
		wp_enqueue_style( 'thickbox' );
		wp_enqueue_script( 'thickbox' );
	}


	/**
	 * Do redirection.
	 *
	 * @since 1.0.0
	 */
	function redirect_query() {
		global $wpdb, $table_prefix;
		$request = $_SERVER['REQUEST_URI'];
		if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
			$request = substr( $_SERVER['PHP_SELF'], 1 );
			if ( isset( $_SERVER['QUERY_STRING'] ) and  '' != $_SERVER['QUERY_STRING'] ) {
				$request .= '?' . $_SERVER['QUERY_STRING'];
			}
		}
		if ( isset( $_GET['shoutcodes'] ) ) {
			$request = '/go/' . wp_unslash( $_GET['shoutcodes'] ) . '/';
		}
		// $url_trigger = get_option( 'shoutcodes_URL_trigger' );
		// $nofollow = get_option( 'shoutcodes_nofollow' );
		$_options    = get_option( 'shoutcodes_main', array() );
		$url_trigger = array_key_exists( 'url_trigger', $_options ) ? $_options['url_trigger'] : '';
		$nofollow    = array_key_exists( 'no_follow', $_options ) ? $_options['no_follow'] : '';
		if ( '' == $url_trigger ) {
			$url_trigger = 'go';
		}

		if ( strpos( '/' . $request, '/' . $url_trigger . '/' ) ) {
			$shoutcodes_key = explode( $url_trigger . '/', $request );
			$shoutcodes_key = $shoutcodes_key[1];
			$shoutcodes_key = str_replace( '/', '', $shoutcodes_key );
			$table_name     = $wpdb->prefix . 'shoutcodes';
			$shoutcodes_key = esc_sql( $shoutcodes_key );
			// $shoutcodes_db = $wpdb->get_row( "SELECT id, target, key1, docount FROM $table_name WHERE key1 = '$shoutcodes_key'", OBJECT );
			$shoutcodes_db = $wpdb->get_row( $wpdb->prepare( " SELECT id, target, key1, docount, hitcount FROM $table_name WHERE key1 = %s ", $shoutcodes_key ), OBJECT );
			$shoutcodes_target = $shoutcodes_db->target;
			if (  '' != $shoutcodes_target ) {
				if (  1 == $shoutcodes_db->docount  ) {
					$wpdb->update( $table_name, array(
						'hitcount'  => $shoutcodes_db->hitcount + 1,
							),
						array(
							'id' => $shoutcodes_db->id,
						)
					);
				}
				if ( '' != $nofollow ) { header( 'X-Robots-Tag: noindex, nofollow', true ); }
				wp_redirect( $shoutcodes_target, 301 );
				exit;
			} else {
				$badambkey = get_option( 'siteurl' );
				wp_redirect( $badambkey, 301 );
				exit;
			}
		}
	}


	/**
	 * Add ShoutCodes to fav.
	 *
	 * @since 1.0.0
	 */
	function shoutcodes_add_menu_favorite( $actions ) {
		$actions[ shoutcodes_URL ] = array( 'shoutcodess', 'manage_options' );
		return $actions;
	}

	/**
	 * Import Data from GoCodes.
	 *
	 * @since 1.0.0
	 */
	function process_import() {

		if ( isset( $_POST['import_gocodes'] ) ) {
			global $wpdb;
			$table_name = $wpdb->prefix . 'shoutcodes';
			$old_table = $wpdb->prefix . 'wsc_gocodes';
			$sql = 'INSERT INTO ' . $table_name . ' ( target, key1, docount, hitcount )
              SELECT target, key1, docount, hitcount
							FROM ' . $old_table;
			$wpdb->query( $sql );
		}
	}

	/**
	 * Success message when import done.
	 *
	 * @return [type] [description]
	 */
	function success_message() {
		if ( isset( $_POST['import_gocodes'] ) ) {
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php esc_html_e( 'GoCodes Data Import Successfully!', 'shoutcodes' ); ?></p>
			</div>
			<?php
		}
	}

	/**
	 * Deactivate notice after import.
	 *
	 * @since 1.0.0
	 */
	function deactivate_gocodes_notice() {
		if ( is_plugin_active( 'gocodes/gocodes.php' ) &&	 isset( $_POST['import_gocodes'] ) ) {
				?>
				<div class="notice notice-success is-dismissible">
					<p>Do you want to deactivate GoCodes <a class='button-primary' href="<?php echo get_admin_url() ?>plugins.php?disabled_gocodes=yes">Yes</a> <a class='button-primary' href="<?php echo get_admin_url() ?>plugins.php?disabled_gocodes=no">No</a></p>
				</div>
				<?php
		}
	}

	/**
	 * Deactivate gocode plugin.
	 *
	 * @since 1.0.0
	 */
	function deactivate_gocodes() {
		// if ( is_plugin_active('gocodes/gocodes.php') && ! get_option( 'deactivate_gocodes_notice' ) ) {
		if ( isset( $_GET['disabled_gocodes'] ) && 'yes' == $_GET['disabled_gocodes'] ) {
			deactivate_plugins( 'gocodes/gocodes.php' );
			update_option( 'deactivate_gocodes_notice', 'yes' );
		}
			// else if ( isset( $_GET['disabled_gocodes'] ) && $_GET['disabled_gocodes'] == 'no' ) {
			// update_option( 'deactivate_gocodes_notice', 'yes' );
			// }
		// }
	}

	/**
	 * Import gocodes data
	 *
	 * @since 1.0.0
	 */
	function import_gocodes() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'wsc_gocodes';
		// GoCodes Available.
		if (  $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE  %s', $table_name ) )  && get_transient( 'wpb-shoutcodes-admin-notice' ) ) {
			?>
			<div class="notice notice-success is-dismissible">
				<form  method="post">
					<p>Do you want to import settings from Gocodes? 	<input type="submit" name="import_gocodes" value="Yes" class=" button-primary"> <a href="<?php echo get_admin_url() ?>" class="button-primary">No</a></p>
				</form>
			</div>
			<?php
			delete_transient( 'wpb-shoutcodes-admin-notice' );
		}
	}


}
