<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://bobachat.app/
 * @since      1.0.5
 *
 * @package    Bobachat
 * @subpackage Bobachat/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Bobachat
 * @subpackage Bobachat/public
 * @author     Bobachat <dev@bobachat.app>
 */
class Bobachat_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Bobachat_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Bobachat_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bobachat-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Bobachat_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Bobachat_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bobachat-public.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 *
	 * @since    1.0.5
	 */
	
	public function add_bobachat_script_header() {
		// Ignore admin, feed, robots or trackbacks
		if ( is_admin() || is_feed() || is_robots() || is_trackback() ) {
			return;
		}
		$cdn = get_option('bobachat_subscriptionFormCdn');
		if(!empty($cdn)) {
			wp_enqueue_script( 'bobachatcdn', "$cdn", array('jquery'), '1.1.0', true );
		} else {
			 $bobachat_uniq_key = get_option('bobachat_uniq_key');
			 $siteUrl = get_bloginfo('url');
			if (!empty($bobachat_uniq_key)) {
			  $args = array(
				'headers' => $this->headers,
				'timeout' => 120,
			  );
			  $body = wp_json_encode( array(
				'domain' => $siteUrl,
				'key' => $bobachat_uniq_key,
			  ));
			  $url = 'https://oeqo2cipca.execute-api.us-east-1.amazonaws.com/prod/sfi/wordpress/get';
			  $response =  wp_remote_post( $url, [
				  'headers'   =>  $this->headers,
				  'body'       => $body,
			  ]);
			  $integration = json_decode($response['body']);
				if(!empty($integration)) {
					update_option('bobachat_subscriptionFormCdn', $integration -> subscriptionFormCdn);
					$cdnUrl = $integration -> subscriptionFormCdn;
					wp_enqueue_script( 'bobachatcdn', $cdnUrl, array('jquery'), '1.1.0', true );
				}
			}
		}	
	}
}
