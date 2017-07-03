<?php
/**
 * Plugin's Admin code
 *
 * @link http://wordpress.org/plugins/cf7-international-sms-integration
 * @package CF7ISMS
 * @subpackage CF7ISMS/Admin
 * @since 1.0
 */
if ( ! defined( 'WPINC' ) ) { die; }

class CF7_International_SMS_Admin {

    /**
	 * Initialize the class and set its properties.
	 * @since      0.1
	 */
	public function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ),99);
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'admin_init', array( $this, 'admin_init' ));

        add_filter( 'plugin_row_meta', array($this, 'plugin_row_links' ), 10, 2 );
        add_filter( 'plugin_action_links_'.CF7_ISMS_FILE, array($this,'plugin_action_links'),10,10);  
	}
 
    
    /**
     * Inits Admin Sttings
     */
    public function admin_init(){
        new CF7_International_SMS_Admin_Ajax_Handler;
        new CF7_International_SMS_Addons;
    }
    public function init_admin_notices(){
        $displayCallBack = array( CF7_International_SMS_Admin_Notices::getInstance(), 'displayNotices' );
        $dismissCallBack = array( CF7_International_SMS_Admin_Notices::getInstance(), 'ajaxDismissNotice' );
        
        if ( ! has_action( 'admin_notices', $displayCallBack ) ) { add_action( 'admin_notices', $displayCallBack ); }
            
        if ( ! has_action( 'admin_notices', $dismissCallBack ) ) {
            add_action( 'wp_ajax_' . CF7_International_SMS_Admin_Notices::KILL_STICKY_NTC_AJAX_ACTION, $dismissCallBack );
        }        
    }
    
    
    
    /**
	 * Register the stylesheets for the admin area.
	 */
	public function enqueue_styles() {  
        $pages = cf7_isms_get_screen_ids();
        $current_screen = cf7_isms_current_screen();
        
        $addon_url = admin_url('admin-ajax.php?action=cf7_isms_addon_custom_css');
        wp_register_style(CF7_ISMS_SLUG.'_backend_style',CF7_ISMS_CSS.'backend.css' , array(), CF7_ISMS_V, 'all' );  
        wp_register_style(CF7_ISMS_SLUG.'_addons_style',$addon_url , array(), CF7_ISMS_V, 'all' );  

        
        if(in_array($current_screen ,$pages) || $current_screen == 'shop_order') {
            wp_enqueue_style(CF7_ISMS_SLUG.'_backend_style');  
            wp_enqueue_style(CF7_ISMS_SLUG.'_addons_style');  
        }
        
        do_action('cf7_isms_admin_styles',$current_screen,$pages);
	}
    
    /**
	 * Register the JavaScript for the admin area.
	 */
	public function enqueue_scripts() {
        $pages = cf7_isms_get_screen_ids();
        $current_screen = cf7_isms_current_screen();
        
        $addon_url = admin_url('admin-ajax.php?action=cf7_isms_addon_custom_js');
        
        wp_register_script(CF7_ISMS_SLUG.'_backend_script', CF7_ISMS_JS.'backend.js', array('jquery'), CF7_ISMS_V, false ); 
        wp_register_script(CF7_ISMS_SLUG.'_addons_script', $addon_url, array('jquery'), CF7_ISMS_V, false ); 
        
        wp_register_script(CF7_ISMS_SLUG.'_order_edit_page', CF7_ISMS_JS.'order-edit.js', array('jquery'), CF7_ISMS_V, false ); 
        
        
        if(in_array($current_screen ,$pages) || $current_screen == 'shop_order') {
            wp_enqueue_script(CF7_ISMS_SLUG.'_backend_script' ); 
            wp_enqueue_script(CF7_ISMS_SLUG.'_addons_script' ); 
        } 
        
        if($current_screen == 'shop_order'){
            wp_enqueue_script(CF7_ISMS_SLUG.'_order_edit_page');
        }
        
        do_action('cf7_isms_admin_scripts',$current_screen,$pages); 
 	}
 
    /**
	 * Adds Some Plugin Options
	 * @param  array  $plugin_meta
	 * @param  string $plugin_file
	 * @since 0.11
	 * @return array
	 */
    public function plugin_action_links($action,$file,$plugin_meta,$status){
        $menu_link = admin_url('admin.php?page=cf7-international-sms-integration-settings');
        $actions[] = sprintf('<a href="%s">%s</a>', $menu_link, __('Settings',CF7_ISMS_TXT) );
        $actions[] = sprintf('<a href="%s">%s</a>', 'http://vssupport.ticksy.com', __('Contact Author',CF7_ISMS_TXT) );
        $action = array_merge($actions,$action);
        return $action;
    }
    
    /**
	 * Adds Some Plugin Options
	 * @param  array  $plugin_meta
	 * @param  string $plugin_file
	 * @since 0.11
	 * @return array
	 */
	public function plugin_row_links( $plugin_meta, $plugin_file ) {
		if ( CF7_ISMS_FILE == $plugin_file ) {
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', 'http://vssupport.ticksy.com', __('Report Issue',CF7_ISMS_TXT) );
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', 'https://codecanyon.net/item/cf7-international-sms-integration/19471296', __('View on CodeCanyon',CF7_ISMS_TXT) );
		}
		return $plugin_meta;
	}	    
}