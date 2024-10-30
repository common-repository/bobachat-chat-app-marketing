<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://bobachat.app/
 * @since             1.0.3
 * @package           Bobachat-Widget
 *
 * @wordpress-plugin
 * Plugin Name:       Bobachat - Chat marketing for Telegram & Facebook
 * Description:       Allow your users to connect with you over Telegram, Whatsapp, and other chat apps. It's Mailchimp for chat.
 * Version:           1.0.5
 * Author:            Bobachat
 * Author URI:        https://bobachat.app/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bobachat
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'BOBACHAT_VERSION', '1.0.3' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/bobachat-activator.php
 */
function activate_bobachat() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/bobachat-activator.php';
	Bobachat_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/bobachat-deactivator.php
 */
function deactivate_bobachat() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/bobachat-deactivator.php';
	Bobachat_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_bobachat' );
register_deactivation_hook( __FILE__, 'deactivate_bobachat' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/bobachat.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_bobachat() {

	$plugin = new Bobachat();
	$plugin->run();

}
run_bobachat();
