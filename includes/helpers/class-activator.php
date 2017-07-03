<?php 
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @link http://wordpress.org/plugins/cf7-international-sms-integration
 * @package CF7ISMS
 * @subpackage CF7ISMS/core
 * @since 1.0
 */
class CF7_International_SMS_Activator {
	
    public function __construct() {
    }
	
	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		require_once(CF7_ISMS_INC.'helpers/class-version-check.php');
		require_once(CF7_ISMS_INC.'helpers/class-dependencies.php');
		
		if(CF7_International_SMS_Dependencies(CF7_ISMS_DEPEN)){
			CF7_International_SMS_Version_Check::activation_check('3.7');	
		} else {
			if ( is_plugin_active(CF7_ISMS_FILE) ) { deactivate_plugins(CF7_ISMS_FILE);} 
			wp_die(cf7_isms_dependency_message());
		}
	} 
 
}