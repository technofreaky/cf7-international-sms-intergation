<?php
global $cf7ismslog;
$cf7ismslog = '';
class CF7_International_SMS_Log_Handler {
    protected static $_instance = null; # Required Plugin Class Instance
    
    public static function get_instance() {
        if ( null == self::$_instance ) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }
    
    /**
     * Class Constructor
     */
    public function __construct() {
        $this->logger_db_key = CF7_ISMS_DB.'sms_logs';
        $this->get_logs(); 
    }
    
    public function get_logs(){
        if(empty($this->current_logs)){
            $this->current_logs = get_option($this->logger_db_key,array()); 
        }
        
        return $this->current_logs;
    }
    
    public function get_log($id = ''){
        if(isset($this->current_logs[$id])){
            return $this->current_logs[$id];
        }
        return array();
    }
    
    public function clear_logs(){
        update_option($this->logger_db_key,array());
        $this->current_logs = null;
        $this->get_logs();
    }
    
    
    public function delete_log($smsid){
        if(isset($this->current_logs[$smsid])){
            unset($this->current_logs[$smsid]);
            update_option($this->logger_db_key,$this->current_logs);
            $this->current_logs = null;
            $this->get_logs();
            delete_option(CF7_ISMS_DB.'id_'.$smsid);
            return true;
        } 
        
        return false;
    }
    
    public function get_http_request($smsid){
        $request = get_option(CF7_ISMS_DB.'id_'.$smsid);
        return $request;
    }
    
    public function add_log($args = array(),$response_info = array()){
        //
        $defaults = array(
            'smsid' => 'CF7ISMS'.current_time( 'timestamp').rand(0,100),
            'datetime' => current_time( 'timestamp'),
            'rorder' => null,
            'sentby' => null,
            'smsto' => null,
            'response' => null,
            'senderid' => null,
            'message' => null,
            'status' => null,
            'smsreason' => 'system',
            'smsfor' => null,
        );
        
        $args = wp_parse_args($args,$defaults);
        
        if(!isset($this->current_logs[$args['smsid']])){
            $this->current_logs[$args['smsid']] = $args;
        } else {
            return false;
        }
        
        update_option($this->logger_db_key,$this->current_logs);
        $this->current_logs = null;
        $this->get_logs();
        
        add_option(CF7_ISMS_DB.'id_'.$args['smsid'],$response_info);
        return $args['smsid'];
    }
    
    
}

$cf7ismslog = CF7_International_SMS_Log_Handler::get_instance();

return CF7_International_SMS_Log_Handler::get_instance();