<?php
/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @link http://wordpress.org/plugins/cf7-international-sms-integration
 * @package CF7ISMS
 * @subpackage CF7ISMS/core
 * @since 1.0
 */
class CF7_International_SMS_Deactivator {
	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

	}

	public static function dependency_deactivate(){ 
		if ( is_plugin_active(CF7_ISMS_FILE) ) {
			add_action('update_option_active_plugins', array(__CLASS__,'deactivate_dependent'));
		}
	}
	
	public static function deactivate_dependent(){
		deactivate_plugins(CF7_ISMS_FILE);
	}

}