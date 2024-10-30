<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://bobachat.app/
 * @since      1.0.0
 *
 * @package    Bobachat
 * @subpackage Bobachat/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Bobachat
 * @subpackage Bobachat/admin
 * @author     Bobachat <dev@bobachat.app>
 */
class Bobachat_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bobachat-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'bootstrap-css', plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bobachat-admin.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Add our custom menu
	 *
	 * @since    1.0.0
	 */
	public function my_admin_menu() {
		add_menu_page( "Bobachat", 'Bobachat', 'manage_options', 'bobachat.php', array($this, 'myplugin_admin_page'), plugins_url( 'img/bobachat.png', __FILE__ ), 250);
	}

	public function myplugin_admin_page() {
		$this->initiate_api();
		$bobachat_uniq_key = get_option( 'bobachat_uniq_key' );
		if (empty($bobachat_uniq_key)) {
			update_option('bobachat_uniq_key', uniqid('bobachat_', true));
		}
		$integration = $this->bobachat_api->getIntegration();
		if(!empty($integration)) {
			update_option('bobachat_subscriptionFormCdn', $integration -> subscriptionFormCdn);
		}
		if ( !current_user_can( 'administrator' ) ) {
			echo '<p>' . __( 'Sorry, you are not allowed to access this page.', 'bobachat' ) . '</p>';
			return;
		}
		require_once 'partials/bobachat-admin-display.php';
	}

	/**
	 * Register custom fields for plugin
	 *
	 * @since    1.0.0
	 */
	public function register_subscription_form_setting() {
		register_setting( 'bobachat_custom_setting', 'bobachat_uniq_key');
		register_setting( 'bobachat_custom_setting', 'bobachat_subscriptionFormCdn');
	}
    private function env(string $file)
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'env' . DIRECTORY_SEPARATOR . $file;
    }

	public function initiate_api() {
		(new Bobachat_Env($this->env(BOBACHAT_ENV.'.env')))->load();
		if (empty($this->bobachat_api)) {
			$this->bobachat_api = new Bobachat_Api();
		}
		return $this->bobachat_api;
	}
}
