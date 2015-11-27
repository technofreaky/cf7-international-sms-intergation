<?php
/**
 * functionality of the plugin.
 * @author     Varun Sridharan <varunsridharan23@gmail.com>
 */
if ( ! defined( 'WPINC' ) ) { die; }

class Contact_Form_7_International_Sms_Integration_Functions {
	
	public function __construct() {
		add_action( 'wpcf7_before_send_mail', array($this, 'send_sms' ) );
  	}
	

  /**
   * Send SMS on contact form submission
   *
   * @param object $form Contact form to send  
   * @return void
   * @author James Inman
   */
  public function send_sms( $form ) {
	$options = get_option( 'wpcf7_international_sms_' . (method_exists($form, 'id') ? $form->id() : $form->id)) ;
	if(isset( $options['phone'] ) && $options['phone'] != '' && isset( $options['message'] ) && $options['message'] != '' ) { 

	// Contact Form 7 > 3.9 
	if(function_exists('wpcf7_mail_replace_tags')) {
	  $message = wpcf7_mail_replace_tags($options['message'], array());
	  $phone = wpcf7_mail_replace_tags($options['phone'], array());
	} elseif(method_exists($form, 'replace_mail_tags')) {
	  $message = $form->replace_mail_tags($options['message']);
	  $phone = $form->replace_mail_tags($options['phone']);
	} else {
	  return;
	}

	  $message = urlencode($message); 
	  $link = get_option(CF7SI_DB_SLUG.'api_urls','');
		
		if(!empty($link)){
			$save_db = array();
			$link = str_replace(array('{MOBILENUMBER}','{MESSAGE}'),array($phone,$message),$link);
			$response = wp_remote_get( $link);
			$send_res = $response['body'];
			$save_db['response'] = $send_res;
			$save_db['formID'] = method_exists($form, 'id') ? $form->id() : $form->id;
			$save_db['formNAME'] = method_exists($form, 'name') ? $form->name() : $form->name;
			$save_db['datetime'] = date("Y-m-d H:i:s");
			$save_db['message'] = $message;
			$save_db['to'] = $phone;  
			$this->save_history($save_db);
		}
	   
	}
  }	
	
  public function save_history($data){
	  $array = get_option( 'wpcf7is_history'); 
	  if(empty($array)){$array = array(); } 
	  if(count($array) == 100){
		  $array = array();
		  $array[] = $data;
	  } else {
		$array[] = $data; 
	  }
	  update_option('wpcf7is_history',$array);
  }
}