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

class Varun_Sridharan_SMS_Handler {


    public function __construct(){  
        $this->response = null;
        $this->sms_api_error = null;
        $this->internal_errors = array();
    } 
    
    public function get_sms_errors($response){
        if(is_string($response) || is_null($response)){
            if(!empty($this->internal_errors)){
                $old_res = $response;
                $response = array_merge(array($old_res),$this->internal_errors);
            }
            
        } else if(is_array($response)){
            if(!empty($this->internal_errors))
                $response[] = $this->internal_errors;
        }
        
        return $response;
    }
     
    
	public function send_sms($sender_id = null,$to_number = null,$message = null) {
        $default_sender_id = $this->get_sender_id();
        $url = $this->get_host_url();
        $method = $this->get_processing_method();
        $attributes = $this->get_url_attributes(true);

        if(empty($sender_id) && empty($default_sender_id)){ $this->internal_errors['sender_id'] = __("No Sender ID Defined.",CF7_ISMS_TXT); }
        if(empty($sender_id)){ $sender_id = $default_sender_id; }
        if(empty($to_number)){ $this->internal_errors['invalid_sender_number'] = sprintf(__("Invalid Sender Number (%s)",CF7_ISMS_TXT),$to_number);}
        if(empty($message)){ $this->internal_errors['invalid_message'] = __("Invalid / Empty SMS Message",CF7_ISMS_TXT); }
        if(empty($url) || filter_var($url, FILTER_VALIDATE_URL) === FALSE ){ $this->internal_errors['invalid_url'] = sprintf(__("Invalid / Empty SMS Gateway URL (%s)",CF7_ISMS_TXT),$url);}
        if(empty($method)){ $this->internal_errors['invalid_processing_method'] = __("Invalid Processing Method Selected",CF7_ISMS_TXT); }
        if(empty($attributes)){ $this->internal_errors['invalid_url_attributes'] = __("No URL Attributes Entered",CF7_ISMS_TXT); }
        
        if(empty($this->internal_errors)){
            $encode_url = cf7_isms_option('gateway_url_encode','no');
            foreach($attributes as $id => $val){
                $search = array('%senderid%','%tonumber%','%message%');
                $replace = array($sender_id,$to_number,$message); 
                $attributes[$id] = str_replace($search,$replace,$val);
                $attributes[$id] = $attributes[$id];
                if($encode_url == 'yes'){
                    $attributes[$id] = urlencode($attributes[$id]);   
                }
            }

            $function = 'send_sms_'.$method.'_method';
            
            if(method_exists(__CLASS__,$function)){
                $response = $this->$function($url,$attributes);
                $this->response = $response;
                $status_code = wp_remote_retrieve_response_code($response);
                $status = $this->is_wp_error($response);
                $return = array();
                if($status_code !== 200){ $return[] = $status_code.' : '.$response['response']['message']; }
                if($status === false){ $return[] = wp_remote_retrieve_body($response); }
                 
                return $return;
                
            } else if (has_action('cf7_isms_'.$function)) {
                do_action('cf7_isms_'.$function);
            }             
        }
        return false;
    }
    
    
    public function send_sms_get_method($url,$attributes){ 
        $arrgs = add_query_arg($attributes, $url );
        $get_args = apply_filters("cf7_isms_get_api_args",array( 'timeout' => 120, 'httpversion' => '1.1' ));
        $response = wp_remote_get($arrgs, $get_args); 
        return $response;
    }
    public function send_sms_post_method($url,$attributes){
        $post_args = apply_filters("cf7_isms_post_api_args",array( 'method' => 'POST', 'timeout' => 45, 'redirection' => 5, 'httpversion' => '1.0', 'blocking' => true, 'headers' => array(), 'body' => $attributes, 'cookies' => array() ));
        $response = wp_remote_post( $url,  $post_args);        
        return $response; 
    }
    
    public function is_wp_error($response){
        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message(); 
            $this->sms_api_error = $error_message;
            return true;
        } 
        
        return false;
    }
    
    
    
    
    public function is_enabled(){
        $is_enabled = cf7_isms_option('enable_sms_notification',false);
        return $is_enabled;
    }
    
    public function is_admin_enabled(){
        $is_admin_enabled = cf7_isms_option("enable_admin_sms_notification",false);
        return $is_admin_enabled;
    }
    
    public function get_admin_numbers(){
        $numbers = cf7_isms_option('enable_admin_sms_notification_numbers',array());
        return $this->split_numbers($numbers);
    }
    
    public function split_numbers($numbers){
        if($numbers){
            if(is_string($numbers)) 
                return explode(',',$numbers);
        }   
        
        return array();
    }
    
    public function get_host_url(){
        $url = cf7_isms_option('gateway_url',false);
        return esc_url($url);
    }
    
    public function get_processing_method(){
        $method = cf7_isms_option("gateway_method",'GET');
        return $method;
    }
    
    public function get_url_attributes($force = false){
        $attributes = cf7_isms_option("gateway_url_attrs",'');
        
        if($force){
            $attributes = $this->split_attributes($attributes);
        }
        
        return $attributes;
    }
    
    public function split_attributes($attributes){
        $cp_attr = $attributes;
        $cp_attr = explode(',',$cp_attr);
        
        if(count($cp_attr) == 1){
            $cp_attr_new = explode('&',$cp_attr[0]); 
            if(count($cp_attr_new) > 1){
                $cp_attr = $cp_attr_new;
            }
        }
        
        $final_attr = array();
        foreach($cp_attr as $key => $value){
            $atr = explode('=',$value); 
            if(count($atr) == 2){
                $final_attr[$atr[0]] = $atr[1];
            } else{
                $final_attr[] = $atr[0];
            }
            
        }
        return $final_attr;
    }
    
    public function get_sender_id(){
        $sender_id = cf7_isms_option('gateway_default_sender_id',false); 
        return $sender_id;
    }
    
}