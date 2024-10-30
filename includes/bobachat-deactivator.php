<?php
/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.1
 * @package    Bobachat
 * @subpackage Bobachat/includes
 * @author     Bobachat <dev@bobachat.app>
 */
class Bobachat_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.1
	 */
	public static function deactivate() {
		$bobachat_uniq_key = get_option( 'bobachat_uniq_key' );
		if (!empty($bobachat_uniq_key)) {
		$bobachat_env = plugin_dir_path( dirname( __FILE__ ) ).'admin'.DIRECTORY_SEPARATOR . 'env' . DIRECTORY_SEPARATOR . 'prod.env';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/bobachat-env.php';
		(new Bobachat_Env($bobachat_env))->load();
		$args = array(
			'headers' => array('Content-Type' => 'application/json'),
			'timeout' => 120,
		);
		$siteUrl = get_bloginfo('url');
		$body = wp_json_encode( array(
			'domain' => $siteUrl,
			'status' => 'PluginInactive',
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
