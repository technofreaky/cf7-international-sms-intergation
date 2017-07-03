<?php
/**
 * Check the version of WordPress
 *
 * @link http://wordpress.org/plugins/cf7-international-sms-integration
 * @package CF7ISMS
 * @subpackage CF7ISMS/core
 * @since 1.0
 */
class CF7_International_SMS_Version_Check {
    static $version;
    /**
     * The primary sanity check, automatically disable the plugin on activation if it doesn't meet minimum requirements
     *
     * @since  1.0.0
     */
    public static function activation_check( $version ) {
        self::$version = $version;
        if ( ! self::compatible_version() ) {
            deactivate_plugins(CF7_ISMS_FILE);
            wp_die( __( CF7_ISMS_NAME . ' requires WordPress ' . self::$version . ' or higher!', CF7_ISMS_TXT ) );
        } 
    }
	
    /**
     * The backup sanity check, in case the plugin is activated in a weird way, or the versions change after activation
     *
     * @since  1.0.0
     */
    public function check_version() {
        if ( ! self::compatible_version() ) {
            if ( is_plugin_active(CF7_ISMS_FILE) ) {
                deactivate_plugins(CF7_ISMS_FILE);
                add_action( 'admin_notices', array( $this, 'disabled_notice' ) );
                if ( isset( $_GET['activate'] ) ) {
                    unset( $_GET['activate'] );
                }
            } 
        } 
    }
	
    /**
     * Text to display in the notice
     *
     * @since  1.0.0
     */
    public function disabled_notice() {
       echo '<strong>' . esc_html__( CF7_ISMS_NAME . ' requires WordPress ' . self::$version . ' or higher!', CF7_ISMS_TXT ) . '</strong>';
    } 
	
    /**
     * Check current version against $prefix_version_check
     *
     * @since  1.0.0
     */
    public static function compatible_version() {
        if ( version_compare( $GLOBALS['wp_version'], self::$version, '<' ) ) {
             return false;
         }
        return true;
    }
}