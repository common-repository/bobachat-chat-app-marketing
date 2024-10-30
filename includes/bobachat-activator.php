<?php

/**
 * Fired during plugin activation
 *
 * @link       https://bobachat.app/
 * @since      1.0.2
 *
 * @package    Bobachat
 * @subpackage Bobachat/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.2
 * @package    Bobachat
 * @subpackage Bobachat/includes
 * @author     Bobachat <dev@bobachat.app>
 */
class Bobachat_Activator {

	/**
	 * @since    1.0.2
	 */
	public static function activate() {
		$bobachat_uniq_key = get_option( 'bobachat_uniq_key' );
		if (!empty($bobachat_uniq_key)) {
			$bobachat_env = plugin_dir_path( dirname( __FILE__ ) ).'admin'.DIRECTORY_SEPARATOR . 'env' . DIRECTORY_SEPARATOR . 'prod.env';
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/bobachat-env.php';
			(new Bobachat_Env($bobachat_env))->load();
			
			$siteUrl = get_bloginfo('url');

			$bodyGet = wp_json_encode( array(
				'domain' => $siteUrl,
				'key' => $bobachat_uniq_key,
				));

			$urlGet = getenv('BOBACHAT_URL').'/sfi/wordpress/get';

			$response =  wp_remote_post( $urlGet, [
				'headers'   =>  array('Content-Type' => 'application/json'),
				'body'       => $bodyGet,
			]);

			$integration = json_decode($response['body']);

			if (!empty($integration -> id)){
				$body = wp_json_encode( array(
					'domain' => $siteUrl,
					'status' => 'Connected',
					'key' => get_option( 'bobachat_uniq_key' ),
				));
				$url = getenv('BOBACHAT_URL').'/sfi/wordpress/set';
				wp_remote_post( $url, [
					'headers'   => array('Content-Type' => 'application/json'),
					'body'       => $body,
				]);
			}
		}
	}

}
