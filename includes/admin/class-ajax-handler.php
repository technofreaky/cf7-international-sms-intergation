<?php
/**
 * The admin-specific functionality of the plugin.
 * @link http://wordpress.org/plugins/cf7-international-sms-integration
 * @package CF7ISMS
 * @subpackage CF7ISMS/Admin
 * @since 3.0
 */
if ( ! defined( 'WPINC' ) ) { die; }

class CF7_International_SMS_Admin_Ajax_Handler {
    
    public function __construct() { 
		add_action( 'wp_ajax_nopriv_cf7_isms_addon_custom_css',array($this,'render_addon_css'));
		add_action( 'wp_ajax_cf7_isms_addon_custom_css',array($this,'render_addon_css'));

        add_action( 'wp_ajax_nopriv_cf7_isms_addon_custom_js',array($this,'render_addon_js'));
		add_action( 'wp_ajax_cf7_isms_addon_custom_js',array($this,'render_addon_js'));
        
        add_action( 'wp_ajax_cf7_isms_view_fullsms',array($this,'render_fullinfo'));
        add_action( 'wp_ajax_cf7_isms_send_from_order_editpage',array($this,'trigger_send_order_custom_sms'));
        add_action( 'wp_ajax_cf7_isms_delete_log',array($this,'delete_sms_log'));
        add_action( 'wp_ajax_cf7_isms_view_http',array($this,'view_http_request'));
        add_action( 'wp_ajax_cf7_isms_resend_status_sms',array($this,'resend_order_sms'));
    }
    
    public function resend_order_sms(){
        if(isset($_REQUEST['order_id'])){
            if(isset($_REQUEST['cf7_isms_resend_sms'])){
                if(!empty($_REQUEST['cf7_isms_resend_sms'])){
                    $status = str_replace('wc-','',$_REQUEST['cf7_isms_resend_sms']);
                    new WooCommerce_Order_Status_SMS_Handler($_REQUEST['order_id'],null,$status);
                    new WooCommerce_Order_All_Status_SMS_Handler($_REQUEST['order_id'],null,$status);
                    wp_send_json_success(__("SMS Sent Successfully",CF7_ISMS_TXT));
                } else {
                    wp_send_json_error(__("Invalid Order Status SMS Selected",CF7_ISMS_TXT));
                }
            } else {
                wp_send_json_error(__("Invalid Order Status SMS Selected",CF7_ISMS_TXT));
            }
        } else {
            wp_send_json_error(__("Invalid Order ID",CF7_ISMS_TXT));
        }
        
    }
    
    public function view_http_request(){
        if(isset($_REQUEST['smsid'])){
            global $cf7ismslog;
            $smsid = $_REQUEST['smsid'];
            $log = $cf7ismslog->get_http_request($smsid);
            if($log){
                
                echo '<pre>';
                    print_r($log);
                echo '</pre>';
            } else {
                echo '<h3>'.__("Requested Data Not Avaiable",CF7_ISMS_TXT).'</h3>';
            }
        } else {
            echo '<h3>'.__("Invalid SMS ID",CF7_ISMS_TXT).'</h3>';
        }
        
        wp_die();
    }
    
    public function delete_sms_log(){
        if(isset($_REQUEST['smsid'])){
            global $cf7ismslog;
            $status = $cf7ismslog->delete_log($_REQUEST['smsid']);
            if($status){
                $text = __("Log Deleted (%s)",CF7_ISMS_TXT);
                $text = sprintf($text,$_REQUEST['smsid']);
                wp_send_json_success($text);
            } else {
                $text = __('Log Already Deleted (%s)',CF7_ISMS_TXT);
                $text = sprintf($text,$_REQUEST['smsid']);
                wp_send_json_error($text);
            }
        } else {
            $text = __('SMS ID Dose Not Exists',CF7_ISMS_TXT);
            wp_send_json_error($text);
        }
        
        wp_die();
    }
    
	
    public function render_fullinfo(){
        if(isset($_REQUEST['smsid'])){
            global $cf7ismslog;
            $sms_log = $cf7ismslog->get_log($_REQUEST['smsid']);
            
            require(CF7_ISMS_ADMIN.'views/ajax-full-log-view.php');
        }
        wp_die();
    }
    
	public function render_addon_css(){ 
        header('Content-Type: text/css');
		do_action('cf7_isms_addon_styles');
		wp_die();
	}
    
	public function render_addon_js(){ 
        header('Content-Type: text/javascript'); 
		do_action('cf7_isms_addon_scripts'); 
		wp_die();
	}
    
    public function trigger_send_order_custom_sms(){
        new CF7_International_SMS_Custom_Order_SMS;
        wp_die();
    }
}