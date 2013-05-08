<?php
	/*
	 * Plugin Name: IFrame Modal Sample
	 * Description: A sample modal implementation using an iframe &amp; wp-ajax for content.
	 * Author: Jer Brand / @aut0poietic
	 * Version: 1.0.0
	 * Author URI: http://irresponsibleart.com
	 * Text Domain: iframe_modal
	 * Domain Path: /languages/
	 */

	/**
	 * Main plugin class, namespace [Author Handle]_[Plugin Text Domain]_ to avoid conflicts.
	 */
	class aut0poietic_iframe_modal_Plugin
	{
		/**
		 * Static Singleton
		 * @action plugins_loaded
		 * @return aut0poietic_iframe_modal_Plugin
		 * @static
		 */
		public static function init() {
			static $instance = false;
			if ( !$instance ) {
				load_plugin_textdomain( 'iframe_modal' , false , dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
				$instance = new aut0poietic_iframe_modal_Plugin ;
			}
			return $instance;
		}

		/**
		 * Constructor.  Adds a hook to display a panel with supporting CSS and JS on the edit page/post screen,
		 * as well as an AJAX hook to retrieve the content of the iframe.
		 */
		public function aut0poietic_iframe_modal_Plugin()
		{
			if ( is_admin() ) {
				add_action( 'add_meta_boxes' , $this->marshal( 'add_meta_box' ) );
				add_action( 'admin_enqueue_scripts' , $this->marshal( 'add_scripts' ) );
				add_action( 'wp_ajax_modal_frame_content' , $this->marshal( 'modal_frame_content' ) );
			}
		}

		/**
		 * Adds a small panel to right-most column of the edit/create post interface used to open the modal dialog.
		 */
		public function add_meta_box() {
			add_meta_box(
				'iframe_modal' ,
				__( 'IFrame Modal Sample' , 'iframe_modal' ) ,
				$this->marshal( 'metabox_content' ) ,
				'post' ,
				'side' ,
				'core'
			);
		}

		/**
		 * Supplies the internal content for the panel. In this case, a simple button with an additional data-content-url parameter
		 * used to set the destination of the iframe.
		 * @param $post WordPress post object
		 */
		public function metabox_content( $post ) {
			print sprintf(
				'<input type="button" class="button button-primary " id="open-iframe_modal" value="%1$s" data-content-url="%3$s%2$d">' ,
				__( 'Open IFrame Modal' , 'iframe_modal' ) ,
				$post->ID ,
				admin_url( 'admin-ajax.php?action=modal_frame_content&post_id=' ) );
		}

		/**
		 * Enqueue the script necessary to open the modal as well as the styles of the modal.
		 * @param $hook string script-name of the current page.
		 * @internal I considered using the WordPress supplied "MediaModal" styles, but rejected them because
		 *           a) They're only available in WordPress 3.5+,
		 *           b) The code is subject to change ( I'd be surprised if it didn't ), and
		 *           b) They are Media Modal specific and have a certain amount of "baggage" as they're the basis for
		 *              javascript hooks and actions.
		 * Obviously YMMV.
		 */
		public function add_scripts( $hook ) {
			if ( $hook === 'post.php' || $hook === 'post-new.php' ) {
				$base = plugin_dir_url( __FILE__ );
				wp_enqueue_script( 'iframe_modal' , $base . 'js/modal.js' , array( 'jquery' ) );
				wp_enqueue_style( 'iframe_modal' , $base . 'css/modal.css' );
				wp_localize_script(
					'iframe_modal' ,
					'aut0poietic_iframe_modal_l10n',
					array(
						"close_label" => __( 'Close Dialog' , 'iframe_modal' )
					)
				);
			}
		}

		/**
		 * AJAX method that adds the required styles and scripts, then includes the modal content.
		 * @internal wp_localize_script() works here as well, but there's another opportunity to localize the iframe's
		 * content in the include file itself.
		 */
		public function modal_frame_content() {
			wp_enqueue_style( 'iframe_modal-content' , plugin_dir_url( __FILE__ ) . 'css/modal-content.css' );
			wp_enqueue_script( 'iframe_modal-content' , plugin_dir_url( __FILE__ ) . 'js/modal-content.js' , array( 'jquery' ) );
			include( 'modal-content.php' );
			die(); // you must die from an ajax call
		}

		/**
		 * @param $method_name
		 * @return callable An array-wrapped PHP callable suitable for calling class methods when working with
		 * WordPress add_action/add_filter.
		 */
		public function marshal( $method_name ) {
			return array( &$this , $method_name );
		}
	}

	/**
	 * Instantiates the plugin singleton during plugins_loaded action.
	 */
	add_action( 'plugins_loaded' , array( 'aut0poietic_iframe_modal_Plugin' , 'init' ) );