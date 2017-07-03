<?php
/** 
 *
 * Addons Handler
 *
 * @link 
 * @package APRWC
 * @subpackage cf7isms/FrontEnd
 * @since 2.0
 */
if ( ! defined( 'WPINC' ) ) { die; }

abstract class CF7_International_SMS_Addon_Handler { 
    
	public function __construct() {
        add_filter('cf7_isms_addon_sections',array($this,'register_section'));
        add_filter('cf7_isms_addon_fields',array($this,'register_fields'));
        
        if(is_admin()){
            add_action('cf7_isms_admin_styles',array($this,'admin_style'));
            add_action('cf7_isms_admin_scripts',array($this,'admin_script'));
        }
            
        add_action('cf7_isms_addon_frontend_scripts',array($this,'frontend_script'));
        add_action('cf7_isms_addon_frontend_styles',array($this,'frontend_style'));
		add_action('cf7_isms_loaded',array($this,'init_class')); 
    }
    
    public function register_section($settings_section){return $settings_section;}
    public function register_fields($settings_fields){return $settings_fields;}
    
    public function init_class(){} 
    
    public function admin_style(){}
    public function admin_script(){}
    public function frontend_style(){}
    public function frontend_script(){}
    
    public function plugin_path($file = __DIR__){ return plugin_dir_path($file); }
    public function plugin_url($file = __FILE__){ return plugin_dir_url($file); } 
}