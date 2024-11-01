<?php

/**
 * ReviePress Setting section.
 *
 * @package ReviewPress
 */
if ( ! class_exists( 'ShoutCodes_Setting' ) ) :
	class ShoutCodes_Setting {

		/**
		 * Private member.
		 */
		private $settings_api;

		/**
		 * Constructor of class.
		 */
		 function __construct() {
			 $this->settings_api = new ShoutCodes_Settings_API ;
			 add_action( 'admin_init', array( $this, 'admin_init' ) );

		 }


		/**
		 * Call in costructor.
		 */
		function admin_init() {

			// Set the settings.
			$this->settings_api->set_sections( $this->get_settings_sections() );
			$this->settings_api->set_fields( $this->get_settings_fields() );

			// Initialize settings.
			$this->settings_api->admin_init();
		}

		/**
		 * Generate setting section
		 */
		function get_settings_sections() {
			$sections = array(
			array(
			   'id'    => 'shoutcodes_main',
			   'title' => __( 'Main Settings', 'shoutcodes' ),
			),
			array(
			   'id'    => 'shoutcodes_import',
			   'title' => __( 'Import', 'shoutcodes' ),
			)
			);
			return $sections;
		}

		 /**
		  * Returns all the settings fields
		  *
		  * @return array settings fields
		  *
		  * @since 1.0.0
		  */
		function get_settings_fields() {

			// $post_type = get_post_types( array( 'public' => true, 'show_in_nav_menus' => true ) );
			$settings_fields = array(
			  'shoutcodes_main' => array(
				 array(
					'name'    => 'url_trigger',
					'label'   => __( 'URL Trigger', 'shoutcodes' ),
					'desc'    => __( 'Change the /go/ part of your redirects to something else. Enter without slashes.', 'shoutcodes' ),
					'type'    => 'text',
					'default' => 'go'
				 ),
				 array(
					'name'        => 'no_follow',
					'label'       => __( 'Nofollow', 'shoutcodes' ),
					'type'        => 'checkbox',
					'field-class' => 'hidden',
					'desc'        => __( 'Adds a nofollow into the redirection sequence.', 'shoutcodes' ),
				 )
			  ),
			  'shoutcodes_import' => array(
				 array(
					 'name'        => 'import_gocodes',
					 'label'       => __( 'Import GoCodes', 'shoutcodes' ),
					 'type'        => 'html',
					 'field-class' => 'hidden',
					//  'desc'        => __( 'Import GoCodes Data.', 'shoutcodes' ),
					//  'desc'        => '<form method="POST"> <input type="submit" value="Import GoCodes Data" name="import_gocodes" class=" button button-primary" /></form>',
						'desc'      =>  '<a href="'. get_admin_url() .'/admin.php?page=shoutcodes-settings&import=shortcode" class=" button button-primary"  >Import GoCodes</a>'
				 ),

			  ),

				 );

				 return $settings_fields;
		}

		/**
		 * Generate spread the word section.
		 *
		 * @since 1.0.0
		 */
		function plugin_page() {

				echo '<div id="" class="wrap"><h2 class="opt-title"><span id="icon-options-general" class="analytics-options"><img src="" alt=""></span>
			 ShoutCodes Settings</h2></div>';

			echo "<div class='wpbr-wrap'><div class='wpbr-tabsWrapper'>";
			echo '<div class="wpbr-button-container top">
						<div class="setting-notification">'.
							__( 'Settings have changed, you should save them!' , 'shoutcodes' )
						.'</div>
                  <input type="submit" class="wpbrmedia-settings-submit button button-primary button-big" value="'.esc_html__( 'Save Settings','shoutcodes' ).'" id="wpbr_save_setting_top">
                  </div>';
			echo '<div id="shoutcodes-settings" class="">';

			$this->settings_api->show_navigation();
			$this->settings_api->show_forms();

			echo '</div>';
			echo '<div class="wpbr-button-container bottom">
                  <div class="wpbr-social-links alignleft">
                  <a href="https://twitter.com/wpbrigade" class="twitter" target="_blank"><span class="dashicons dashicons-twitter"></span></a>
                  <a href="https://www.facebook.com/WPBrigade" class="facebook" target="_blank"><span class="dashicons dashicons-facebook"></span></a>
                  <a href="https://profiles.wordpress.org/WPBrigade/" class="wordpress" target="_blank"><span class="dashicons dashicons-wordpress"></span></a>
                  <a href="http://wpbrigade.com/feed/" class="rss" target="_blank"><span class="dashicons dashicons-rss"></span></a>
                  </div>
                  <input type="submit" class="wpbrmedia-settings-submit button button-primary button-big" value="'.esc_html__( 'Save Settings','shoutcodes' ).'" id="wpbr_save_setting_bottom">
                  </div>';
			echo '</div>';

			?>
           <div class="metabox-holder wpbr-sidebar">
              <div class="sidebar postbox">
				 <h2><?php esc_html_e( 'Spread the Word' , 'shoutcodes' )?></h2>
            <ul>
					<li>
						<a href="http://twitter.com/share?text=This is Best WordPress Affilates  Plugin&url=http://wordpress.org&hashtags=ShoutCodes,WordPress" data-count="none"  class="button twitter" target="_blank" title="Post to Twitter Now"><?php esc_html_e( 'Share on Twitter' , 'shoutcodes' )?><span class="dashicons dashicons-twitter"></span></a>
					</li>

					<li>
						<a href="https://www.facebook.com/sharer/sharer.php?u=https://wordpress.org" class="button facebook" target="_blank" title="Post to Facebook Now"><?php esc_html_e( 'Share on Facebook' , 'shoutcodes' )?><span class="dashicons dashicons-facebook"></span>
						</a>
					</li>

					<li>
						<a href="#" class="button wordpress" target="_blank" title="Rate on Wordpress.org"><?php esc_html_e( 'Rate on Wordpress.org' , 'shoutcodes' )?><span class="dashicons dashicons-wordpress"></span>
						</a>
					</li>
					<li>
						<a href="http://wpbrigade.com/feed/" class="button rss" target="_blank" title="Subscribe to our Feeds"><?php esc_html_e( 'Subscribe to our Feeds' , 'shoutcodes' )?><span class="dashicons dashicons-rss"></span>
						</a>
					</li>
				</ul>
              </div>
			  </div>

				<?php
		}

			   /**
				* Get all the pages
				*
				* @return array page names with key value pairs
				*
				* @since 1.0.0
				*/
		function get_pages() {
			$pages = get_pages();
			$pages_options = array();
			if ( $pages ) {
				foreach ( $pages as $page ) {
					$pages_options[ $page->ID ] = $page->post_title;
				}
			}

			return $pages_options;
		}



	} // End of class.


endif;
