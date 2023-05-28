<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Fired during plugin deactivation
 *
 * @link       https://aipower.org
 * @since      1.0.0
 *
 * @package    Wp_Ai_Content_Generator
 * @subpackage Wp_Ai_Content_Generator/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Wp_Ai_Content_Generator
 * @subpackage Wp_Ai_Content_Generator/includes
 * @author     Senol Sahin <senols@gmail.com>
 */
class Wp_Ai_Content_Generator_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */

	public static function deactivate() {
		wp_clear_scheduled_hook( 'wpaicg_remove_chat_tokens_limited' );
		wp_clear_scheduled_hook( 'wpaicg_remove_promptbase_tokens_limited' );
		wp_clear_scheduled_hook( 'wpaicg_remove_image_tokens_limited' );
		wp_clear_scheduled_hook( 'wpaicg_remove_forms_tokens_limited' );
	}
}



