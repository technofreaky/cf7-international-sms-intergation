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

class CF7SMS_Handler extends Varun_Sridharan_SMS_Handler {

    public $order_status = '';
    public $form_options = '';
    public $form = '';
    
    public function __construct($form){
        parent::__construct(); 
        $this->process_order_sms($form);
    }
    
    public function process_order_sms($form){
        $this->form = $form;
        $this->form_options = get_option( 'wpcf7_international_sms_' . (method_exists($form, 'id') ? $form->id() : $form->id)) ;
        if(isset($this->form_options['status'])){
            $this->send_order_sms($form);
        }
    }
    
    public function get_sender_id(){
        $sender_id = $this->form_options['sender_id'];
        
        if($sender_id == '' || $sender_id == 'Default' || $sender_id === false){
            $sender_id = parent::get_sender_id();
        }
        
        return $sender_id;
    }
    
    public function get_admin_numbers(){
        $existing_numbers = parent::get_admin_numbers();
        $new_numbers = $this->form_options['phone'];  
        $new_numbers = $this->split_numbers($new_numbers);
        $existing_numbers = array_merge($new_numbers,$existing_numbers);
        return array_unique($existing_numbers);
    }
    
    public function get_customer_template(){
        $template = $this->form_options['visitorMessage'];
        return $template;
    }
    
    public function get_admin_template(){
        $template = $this->form_options['message']; 
        return $template;
    } 
        
    public function get_form_attr($type){
        return method_exists($this->form, $type) ? $this->form->$type() : $this->form->$type;
    }
    
    public function send_order_sms(){
        $admin_sms_data = $buyer_sms_data = array();
        
        $admin_phone_number = $this->get_admin_numbers();
        $admin_phone_number = $this->get_cf7_tagS_To_String($admin_phone_number);
        $admin_content = $this->get_admin_template();
        $admin_content = $this->get_cf7_tagS_To_String($admin_content);
        
        $customer_phone_number = $this->form_options['visitorNumber'];
        $customer_phone_number = $this->get_cf7_tagS_To_String($customer_phone_number);
        $customer_content = $this->get_customer_template();
        $customer_content = $this->get_cf7_tagS_To_String($customer_content);
        
        $status_customer_sms = $this->is_enabled();
        $status_admin_sms =  $this->is_admin_enabled();
        $sender_id = $this->get_sender_id();
        
        $form_id = $this->get_form_attr('id');
        $form_name = $this->get_form_attr('name');
        $form_title = $this->get_form_attr('title');
        
        

        if($this->is_enabled()){
            $response = $this->send_sms($sender_id,$customer_phone_number,$customer_content);
            $status = 'success'; 
            if(! $response){ $response = $this->sms_api_error; $status = 'error';}
            $response = $this->get_sms_errors($response);
            $smsid = $this->create_log($form_id,$form_name,$form_title,$customer_phone_number,$response,$customer_content,$sender_id,$status,'customer'); 
        }
         
        if($this->is_admin_enabled()){
            if(is_array($admin_phone_number)){
                foreach($admin_phone_number as $number){
                    $response = $this->send_sms($sender_id,$number,$admin_content); 
                    $status = 'success';
                    if(! $response){ $response = $this->sms_api_error; $status = 'error';}
                    $response = $this->get_sms_errors($response);
                    $smsid = $this->create_log($form_id,$form_name,$form_title,$number,$response,$admin_content,$sender_id,$status,'admin'); 
                } 
            }
        }
        
    }

    public function create_log($order_id,$form_name,$form_title,$number,$response,$admin_message,$sender_id,$status,$smsfor){
        global $cf7ismslog;
        $defaults = array( 
            'rorder' => $order_id,
            'form_name' => $form_name,
            'form_title' => $form_title,
            'sentby' => 'system',
            'smsto' => $number,
            'response' => $response,
            'message' => $admin_message,
            'senderid' => $sender_id,
            'status' => $status,
            'smsreason' => 'formsubmitted',
            'smsfor' => $smsfor,
        );

        $smsid = $cf7ismslog->add_log($defaults,$this->response);
        return $smsid;
    }

    public function get_cf7_tagS_To_String($value){
		if(function_exists('wpcf7_mail_replace_tags')) {
			$return = wpcf7_mail_replace_tags($value); 
		} elseif(method_exists($this->form, 'replace_mail_tags')) {
			$return = $this->form->replace_mail_tags($value); 
		} else {
			return;
		}
		return $return;
	}
 
}