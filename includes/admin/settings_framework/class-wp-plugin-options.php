<?php
/**
 * The admin-specific functionality of the plugin.
 * @link http://wordpress.org/plugins/cf7-international-sms-integration
 * @package CF7ISMS
 * @subpackage CF7ISMS/Admin
 * @since 3.0
 */
if ( ! defined( 'WPINC' ) ) { die; }

class CF7_International_SMS_Admin_Settings_Options {

    public function __construct() {
    	add_filter('cf7_isms_settings_pages',array($this,'settings_pages'));
		add_filter('cf7_isms_settings_section',array($this,'settings_section'));
		add_filter('cf7_isms_settings_fields',array($this,'settings_fields'));
    }
	public function settings_pages($page){
        $page[] = array('id'=>'general','slug'=>'general','title'=>__('General',CF7_ISMS_TXT));
		$page[] = array('id'=>'gateway','slug'=>'gateway','title'=>__('SMS Gateway',CF7_ISMS_TXT));
        
		$page[] = array('id'=>'smslogs','slug'=>'smslogs','title'=>__('SMS Logs',CF7_ISMS_TXT));
        $page = apply_filters('cf7_isms_addon_pages',$page);
        //$page[] = array('id'=>'addonssettings','slug'=>'addonssettings','title'=>__('Add-ons Options',CF7_ISMS_TXT));
		//$page[] = array('id'=>'addons','slug'=>'cf7_isms_addons','title'=>__('Add-ons',CF7_ISMS_TXT));
        
		return $page;
	}
	public function settings_section($section){
		$section['general'][] = array( 'id'=>'general', 'title'=> __('General',CF7_ISMS_TXT)); 
        $section['gateway'][] = array( 'id'=>'gateway', 'title'=> __('',CF7_ISMS_TXT));
        $section['smslogs'][] = array( 'id'=>'smslogs', 'title'=>'');
		//$section['general'][] = array( 'id'=>'advanced', 'title'=> __('Advanced Fields',CF7_ISMS_TXT));
        //$section['general'][] = array( 'id'=>'wpfields', 'title'=> __('WP Fields',CF7_ISMS_TXT));
        //$section['addons'][] = array( 'id'=>'addons', 'title'=>'');
		/*$addonSettings = array(
            'addon_sample' => array( 'id'=>'addonssettings', 'title'=>__('No Addons Activated / Installed.',CF7_ISMS_TXT))
        );
		
        $addonSettings = apply_filters('cf7_isms_addon_sections',$addonSettings);

		if(count($addonSettings) > 1) 			
			unset($addonSettings['addon_sample']);
		$section['addonssettings']  = $addonSettings;*/

        return $section;
	}
    
	public function settings_fields($fields){
        global $fields;
        include(CF7_ISMS_SETTINGS.'fields.php'); 
        
		/*$addonSettings = array('addon_sample' => array());
		$addonSettings = apply_filters('cf7_isms_addon_fields',$addonSettings);
		unset($addonSettings['addon_sample']);
		$fields['addonssettings'] = $addonSettings;*/
	
		return $fields;
	}
}
return new CF7_International_SMS_Admin_Settings_Options;