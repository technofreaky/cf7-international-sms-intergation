<?php
/**
* functionality of the plugin.
* @author     Varun Sridharan <varunsridharan23@gmail.com>
*/
if ( ! defined( 'WPINC' ) ) { die; }

class Contact_Form_7_International_Sms_Integration_Functions {

	public function __construct() {
		add_action( 'wpcf7_before_send_mail', array($this, 'configure_send_sms' ) );
	}



	public function get_cf7_tagS_To_String($value,$form){
		if(function_exists('wpcf7_mail_replace_tags')) {
			$return = wpcf7_mail_replace_tags($value); 
		} elseif(method_exists($form, 'replace_mail_tags')) {
			$return = $form->replace_mail_tags($value); 
		} else {
			return;
		}
		return $return;
	}
	/**
	* Send SMS on contact form submission
	*
	* @param object $form Contact form to send  
	* @return void
	* @author James Inman
	*/
	public function configure_send_sms( $form ) {
		$options = get_option( 'wpcf7_international_sms_' . (method_exists($form, 'id') ? $form->id() : $form->id)) ;
		$sendToAdmin = false;
		$sendToVisitor = false;
		$adminNumber = '';
		$adminMessage = ''; 
		$visitorNumber = '';
		$visitorMessage = '';

		if(isset($options['phone']) && $options['phone'] != '' && isset($options['message']) && $options['message'] != ''){
			$adminNumber = $this->get_cf7_tagS_To_String($options['phone'],$form);
			$adminMessage = $this->get_cf7_tagS_To_String($options['message'],$form);
			$sendToAdmin = true; 
		}


		if(isset($options['visitorNumber']) && $options['visitorNumber'] != '' && 
		   isset($options['visitorMessage']) && $options['visitorMessage'] != ''){ 
			
			$visitorNumber = $this->get_cf7_tagS_To_String($options['visitorNumber'],$form);
			$visitorMessage = $this->get_cf7_tagS_To_String($options['visitorMessage'],$form);
			$sendToVisitor = true; 
		}

		if($sendToAdmin){
			$ADMINSEND = $this->send_sms($adminNumber,$adminMessage);
			if($ADMINSEND){
				$save_db = array();
				$send_res = $ADMINSEND['body'];
				$save_db['response'] = $send_res;
				$save_db['formID'] = method_exists($form, 'id') ? $form->id() : $form->id;
				$save_db['formNAME'] = method_exists($form, 'name') ? $form->name() : $form->name;
				$save_db['datetime'] = date("Y-m-d H:i:s");
				$save_db['message'] = $adminMessage;
				$save_db['to'] = $adminNumber;  		
				$save_db['type'] = 'admin';
				$save_db['ID'] = time().rand(0,1000);
				$this->save_history($save_db);
			}
		}


		if($sendToVisitor){
			$visitorSEND = $this->send_sms($visitorNumber,$visitorMessage);
			if($visitorSEND){
				$save_db = array();
				$send_res = $visitorSEND['body'];
				$save_db['response'] = $send_res;
				$save_db['formID'] = method_exists($form, 'id') ? $form->id() : $form->id;
				$save_db['formNAME'] = method_exists($form, 'name') ? $form->name() : $form->name;
				$save_db['datetime'] = date("Y-m-d H:i:s");
				$save_db['message'] = $visitorMessage;
				$save_db['to'] = $visitorNumber;  		
				$save_db['type'] = 'visitor';
				$save_db['ID'] = time().rand(0,1000);
				$this->save_history($save_db);
			}		
		}
	}	


	public function send_sms($phone,$message){
		$message = urlencode($message); 
		$link = get_option(CF7SI_DB_SLUG.'api_urls','');
		if(!empty($link)){
			$link = str_replace(array('{MOBILENUMBER}','{MESSAGE}'),array($phone,$message),$link);
			$response = wp_remote_get($link);
			return $response;
		}
		return false;
	}
	
	public function save_history($data){
		$array = get_option( 'wpcf7is_history'); 
		//$array = array();
		if(empty($array)){$array = array(); } 
		$array[$data['ID']] = $data;
		//if(count($array) == 100){
		//	$array = array();$data['ID']
		//	$array[$data['ID']] = $data;
		//} else {
		//	$array[$data['ID']] = $data; 
		//}
		update_option('wpcf7is_history',$array);
	}
}