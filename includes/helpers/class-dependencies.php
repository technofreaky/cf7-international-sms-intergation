<?php
/**
 * Dependency Checker
 *
 * Checks if required Dependency plugin is enabled
 *
 * @link http://wordpress.org/plugins/cf7-international-sms-integration
 * @package CF7ISMS
 * @subpackage CF7ISMS/core
 * @since 1.0
 */

if ( ! class_exists( 'CF7_International_SMS_Dependencies' ) ){
    class CF7_International_SMS_Dependencies {
		
        private static $active_plugins;
		
        public static function init() {
            self::$active_plugins = (array) get_option( 'active_plugins', array() );
            if ( is_multisite() )
                self::$active_plugins = array_merge( self::$active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
        }
		
        public static function active_check($pluginToCheck = '') {
            if ( ! self::$active_plugins ) 
				self::init();
            return in_array($pluginToCheck, self::$active_plugins) || array_key_exists($pluginToCheck, self::$active_plugins);
        }
    }
}
/**
 * WC Detection
 */
if(! function_exists('CF7_International_SMS_Dependencies')){
    function CF7_International_SMS_Dependencies($pluginToCheck = 'woocommerce/woocommerce.php') {
        return CF7_International_SMS_Dependencies::active_check($pluginToCheck);
    }
}