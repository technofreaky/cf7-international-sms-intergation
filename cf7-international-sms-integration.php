<?php 
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://wordpress.org/plugins/cf7-international-sms-integration
 * @since             1.1
 * @package           CF7 International SMS
 *
 * @wordpress-plugin
 * Plugin Name:       Contact Form 7 International SMS
 * Plugin URI:        http://wordpress.org/plugins/cf7-international-sms-integration
 * Description:       Send SMS Notification to customer and also Supports Worlds 99% of SMS Gateway 
 * Version:           1.2
 * Author:            Varun Sridharan
 * Author URI:        http://varunsridharan.in
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cf7-international-sms-integration
 * Domain Path:       /languages
 */

if ( ! defined( 'WPINC' ) ) { die; }
 
define('CF7_ISMS_FILE',plugin_basename( __FILE__ ));
define('CF7_ISMS_PATH',plugin_dir_path( __FILE__ )); # Plugin DIR
define('CF7_ISMS_INC',CF7_ISMS_PATH.'includes/'); # Plugin INC Folder
define('CF7_ISMS_DEPEN','contact-form-7/wp-contact-form-7.php');

register_activation_hook( __FILE__, 'cf7_isms_activate_plugin' );
register_deactivation_hook( __FILE__, 'cf7_isms_deactivate_plugin' );
register_deactivation_hook( CF7_ISMS_DEPEN, 'cf7_isms_dependency_deactivate' );



/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function cf7_isms_activate_plugin() {
	require_once(CF7_ISMS_INC.'helpers/class-activator.php');
	CF7_International_SMS_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function cf7_isms_deactivate_plugin() {
	require_once(CF7_ISMS_INC.'helpers/class-deactivator.php');
	CF7_International_SMS_Deactivator::deactivate();
}


/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function cf7_isms_dependency_deactivate() {
	require_once(CF7_ISMS_INC.'helpers/class-deactivator.php');
	CF7_International_SMS_Deactivator::dependency_deactivate();
}



require_once(CF7_ISMS_INC.'functions.php');
require_once(CF7_ISMS_PATH.'bootstrap.php');

if(!function_exists('CF7_International_SMS')){
    function CF7_International_SMS(){
        return CF7_International_SMS::get_instance();
    }
}

CF7_International_SMS();