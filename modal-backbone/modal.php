<?php
/*
 * Plugin Name: Backbone Modal Sample
 * Description: Just a sample of how I implemented a modal similar to the Media modal using Backbone.
 * Author: Jer Brand / @aut0poietic
 * Version: 1.0.0
 * Author URI: http://irresponsibleart.com
 * Text Domain: backbone_modal
 * Domain Path: /languages/
 */

/**
 * Main plugin class, namespace [Author Handle]_[Plugin Text Domain]_ to avoid conflicts.
 */
class aut0poietic_backbone_modal_Plugin {

	/**
	 * Static Singleton
	 * @action plugins_loaded
	 * @return aut0poietic_backbone_modal_Plugin
	 * @static
	 */
	public static function init() {
		static $instance = false;
		if ( ! $instance ) {
			load_plugin_textdomain( 'backbone_modal', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
			$instance = new aut0poietic_backbone_modal_Plugin;
		}

		return $instance;
	}

	/**
	 * Constructor.  Adds a hook to display a panel with supporting CSS and JS on the edit page/post screen,
	 * as well as an AJAX hook to retrieve templates.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', $this->marshal( 'add_meta_box' ) );
		add_action( 'admin_enqueue_scripts', $this->marshal( 'add_scripts' ) );
		// Adding the templates to the post.php and post-new.php only as that's the only place
		// our meta box is added in this plugin.  You can also use the wp-footer action if you need
		// the templates globally.
		add_action( 'admin_footer-post-new.php', $this->marshal( 'add_templates' ) );
		add_action( 'admin_footer-post.php', $this->marshal( 'add_templates' ) );

	}

	/**
	 * Dumps the contents of template-data.php into the foot of the document.
	 * WordPress itself function-wraps the script tags rather than including them directly
	 * ( example: https://github.com/WordPress/WordPress/blob/master/wp-includes/media-template.php )
	 * but this isn't necessary for this example.
	 */
	public function add_templates() {
		include 'template-data.php';
	}

	/**
	 * Adds a small panel to right-most column of the edit/create post interface used to open the modal dialog.
	 */
	public function add_meta_box() {
		add_meta_box(
			'backbone_modal',
			__( 'Backbone Modal Sample', 'backbone_modal' ),
			$this->marshal( 'metabox_content' ),
			'post',
			'side',
			'core'
		);
	}

	/**
	 * Supplies the internal content for the panel. In this case, a simple button used as the primary target for
	 * the backbone application.
	 *
	 * @param $post Post a WordPress post object
	 */
	public function metabox_content( $post ) {
		print sprintf(
			'<input type="button" class="button button-primary " id="open-backbone_modal" value="%1$s">',
			__( 'Open Backbone Modal', 'backbone_modal' )
		);
	}

	/**
	 * Enqueue the script and styles necessary to for the modal.
	 *
	 * @param $hook string script-name of the current page.
	 *
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
			wp_enqueue_script( 'backbone_modal', $base . 'js/modal.js', array(
				'jquery',
				'backbone',
				'underscore',
				'wp-util'
			) );
			wp_localize_script( 'backbone_modal', 'aut0poietic_backbone_modal_l10n',
				array(
					'replace_message' => __( 'This is dummy content. You should add something here.', 'backbone_modal' )
				) );
			wp_enqueue_style( 'backbone_modal', $base . 'css/modal.css' );
		}
	}

	/**
	 * AJAX method that returns an HTML-fragment containing the various templates used by Backbone
	 * to construct the UI.
	 * @internal Obviously, this is part of a particular Backbone pattern that I enjoy using.
	 *           Feel free to remove this method ( and the associated action hook ) if you assemble the
	 *           UI using direct DOM manipulation, jQuery objects, or HTML strings.
	 */
	public function get_template_data() {
		include( 'template-data.php' );
		die(); // you must die from an ajax call
	}

	/**
	 * @param $method_name
	 *
	 * @return callable An array-wrapped PHP callable suitable for calling class methods when working with
	 * WordPress add_action/add_filter.
	 */
	public function marshal( $method_name ) {
		return array( &$this, $method_name );
	}
}

/**
 * Instantiates the plugin singleton during plugins_loaded action.
 */
add_action( 'plugins_loaded', array( 'aut0poietic_backbone_modal_Plugin', 'init' ) );