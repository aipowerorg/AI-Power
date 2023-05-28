<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Fired during plugin activation
 *
 * @link       https://aipower.org
 * @since      1.0.0
 *
 * @package    Wp_Ai_Content_Generator
 * @subpackage Wp_Ai_Content_Generator/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wp_Ai_Content_Generator
 * @subpackage Wp_Ai_Content_Generator/includes
 * @author     Senol Sahin <senols@gmail.com>
 */
class Wp_Ai_Content_Generator_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		self::createTable();
	}

	public static function createTable()
	{
		global $wpdb;

		$wpaicgTable = $wpdb->prefix . 'wpaicg';
		if($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s",$wpaicgTable)) != $wpaicgTable) {
			$charset_collate = $wpdb->get_charset_collate();
			$sql = "CREATE TABLE $wpaicgTable (
						ID mediumint(11) NOT NULL AUTO_INCREMENT,
						name text NOT NULL,
						temperature float NOT NULL,
						max_tokens float NOT NULL,
						top_p float NOT NULL,
						best_of float NOT NULL,
						frequency_penalty float NOT NULL,
						presence_penalty float NOT NULL,
						img_size text NOT NULL,
						api_key text NOT NULL,
						wpai_language VARCHAR(255) NOT NULL,
						wpai_add_img BOOLEAN NOT NULL,
						wpai_add_intro BOOLEAN NOT NULL,
						wpai_add_conclusion BOOLEAN NOT NULL,
						wpai_add_tagline BOOLEAN NOT NULL,
						wpai_add_faq BOOLEAN NOT NULL,
						wpai_add_keywords_bold BOOLEAN NOT NULL,
						wpai_number_of_heading INT NOT NULL,
						wpai_modify_headings BOOLEAN NOT NULL,
						wpai_heading_tag VARCHAR(10) NOT NULL,
						wpai_writing_style VARCHAR(255) NOT NULL,
						wpai_writing_tone VARCHAR(255) NOT NULL,
						wpai_target_url VARCHAR(255) NOT NULL,
						wpai_target_url_cta VARCHAR(255) NOT NULL,
						wpai_cta_pos VARCHAR(255) NOT NULL,
						added_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
						modified_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
						PRIMARY KEY  (ID)
					) $charset_collate;";
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
            $sampleData = [
                'name'						=> 'wpaicg_settings',
                'temperature' 				=> '0.7',
                'max_tokens' 				=> '1500',
                'top_p' 					=> '0.01',
                'best_of' 					=> '1',
                'frequency_penalty' 		=> '0.01',
                'presence_penalty' 			=> '0.01',
                'img_size' 					=> '512x512',
                'api_key' 					=> 'sk..',
                'wpai_language' 			=> 'en',
                'wpai_add_img' 				=> 'false',
                'wpai_add_intro' 			=> 'false',
                'wpai_add_conclusion' 		=> 'false',
                'wpai_add_tagline' 			=> 'false',
                'wpai_add_faq' 				=> 'false',
                'wpai_add_keywords_bold' 	=> 'false',
                'wpai_number_of_heading' 	=>  3,
                'wpai_modify_headings' 		=> 'false',
                'wpai_heading_tag' 			=> 'h1',
                'wpai_writing_style' 		=> 'infor',
                'wpai_writing_tone' 		=> 'formal',
                'wpai_cta_pos' 				=> 'beg',
                'added_date' 				=> date('Y-m-d H:i:s'),
                'modified_date'				=> date('Y-m-d H:i:s')

            ];

            $result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpaicgTable WHERE name = %s", 'wpaicg_settings' ) );

            if(!empty($result->name)){
                $wpdb->update(
                    $wpaicgTable,
                    $sampleData,
                    [
                        'name'			=> 'wpaicg_settings'
                    ],
                    [
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s'
                    ],
                    [
                        '%s'
                    ]
                );
            }else{
                $wpdb->insert(
                    $wpaicgTable,
                    $sampleData,
                    [
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s'
                    ]
                );
            }
		}
	}

}
