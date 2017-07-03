<?php
/**
 * Dependency Checker
 *
 * Checks if required Dependency plugin is enabled
 *
 * @link http://wordpress.org/plugins/cf7-international-sms-integration
 * @package CF7ISMS
 * @subpackage CF7ISMS/FrontEnd
 * @since 1.0
 */
if ( ! defined( 'WPINC' ) ) { die; }

class CF7_International_SMS_Functions {

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
        add_action( 'wp_enqueue_scripts', array($this,'enqueue_styles') );
        add_action( 'wp_enqueue_scripts', array($this,'enqueue_scripts') );
        
        add_action( 'wpcf7_before_send_mail', array($this, 'configure_send_sms' ) );
    }
 
    public function configure_send_sms($form){
        new CF7SMS_Handler($form);
    }
    
	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() { 
		wp_enqueue_style(CF7_ISMS_NAME.'frontend_style', CF7_ISMS_CSS. 'frontend.css', array(), CF7_ISMS_V, 'all' );
	}
    
	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() { 
		wp_enqueue_script(CF7_ISMS_NAME.'frontend_script', CF7_ISMS_JS.'frontend.js', array( 'jquery' ), CF7_ISMS_V, false );
	}

}