<?php
/**
 * The admin-specific functionality of the plugin.
 * @link http://wordpress.org/plugins/cf7-international-sms-integration
 * @package CF7ISMS
 * @subpackage CF7ISMS/Admin
 * @since 1.0
 */
if ( ! defined( 'WPINC' ) ) { die; }

class CF7_International_SMS_Admin_Handler {
    
    public function __construct() {
        add_filter( 'wpcf7_editor_panels' , array($this, 'new_menu'),99);
		add_action( 'wpcf7_after_save', array( &$this, 'save_form' ) );
        
    }
    /**
     * Save SMS options when contact form is saved
     *
     * @param object $cf Contact form
     * @return void
     * @author James Inman
     */
    public function save_form( $form ) {
        update_option( 'wpcf7_international_sms_' . (method_exists($form, 'id') ? $form->id() : $form->id), $_POST['wpcf7si-settings'] );
    }
    
	public function new_menu ($panels) {
		$panels['cf7si-sms-panel'] = array(
				'title' => __('CF7 Interntional SMS',CF7_ISMS_TXT),
				'callback' => array($this, 'add_panel')
		);
		return $panels;
	}
    
    
    public function get_fields($data){
        ob_start();
        $data['form']->suggest_mail_tags();
        $codes = ob_get_clean();
        $fields =  array(
            'general' => array(
                'name' => __("General SMS Notification Settings",CF7_ISMS_TXT),
                'info' => '',
                'fields' => array(
            
                    'wpcf7-sms-general-status' => array(
                        'only_field' => true,
                        'type' => 'checkbox','description' => '', 
                        'label' => __("Send SMS Notification ?",CF7_ISMS_TXT),
                        'id' => 'wpcf7-sms-general-status',
                        'name' => 'wpcf7si-settings[status]',
                        'value' => isset($data['status']) ? $data['status'] : null,
                    ),
            
                    'wpcf7-sms-general-sender' => array(
                        'only_field'         => true,
                        'type' => 'select',
                        'description' => __("If Default Selected Then Global Sender ID Will Be Used",CF7_ISMS_TXT),
                        'options' => cf7_isms_get_sender_id(),
                        'label' => __("Sender ID :",CF7_ISMS_TXT),
                        'id' => 'wpcf7-sms-general-sender',
                        'name' => 'wpcf7si-settings[sender_id]',
                        'value' => isset($data['sender_id']) ? $data['sender_id'] : null,
                    ),
                ),
                
            ),
            'visitor' => array(
                'name' => __("Visitor SMS Notifications",CF7_ISMS_TXT),
                'info' =>  __("In the following fields, you can use these tags:",CF7_ISMS_TXT).'<br/>'.$codes,
                'fields' => array(
                    'wpcf7-sms-visitor-recipient' => array(
                        'only_field' => true,
                        'type'              => 'text',
                        'label'             => __("To:",CF7_ISMS_TXT),
                        'description'       => __("<small>Use <b>CF7 Tags</b> To Get Visitor Mobile Number | Enter Numbers By <code>,</code> for multiple</small>",CF7_ISMS_TXT),
                        'name'              => 'wpcf7si-settings[visitorNumber]',
                        'id'                => 'wpcf7-sms-recipient',
                        'class'             => array(),
                        'label_class'       => array(),
                        'input_class'       => array('wide'),
                        'custom_attributes' => array('size' => 70),
                        'return'            => false,
                        'value'             => isset($data['visitorNumber']) ? $data['visitorNumber'] : '',
                    ), 

                    'wpcf7-sms-visitor-message' => array(
                        'only_field' => true,
                        'type'              => 'textarea',
                        'description' => '',
                        'label'             => __("Message body:",CF7_ISMS_TXT), 
                        'name'              => 'wpcf7si-settings[visitorMessage]',
                        'id'                => 'wpcf7-sms-message',
                        'class'             => array(),
                        'label_class'       => array(),
                        'input_class'       => array('wide'),
                        'return'            => false,
                        'custom_attributes' => array('style' =>'width:75%;min-height:100px;'),
                        'value'             => isset($data['visitorMessage']) ? $data['visitorMessage'] : '',
                    ), 
                )
        
            ),
            'admin' => array(
                'name' => __("Admin SMS Notifications",CF7_ISMS_TXT),
                'info' =>  __("In the following fields, you can use these tags:",CF7_ISMS_TXT).'<br/>'.$codes,
                'fields' => array(
                    'wpcf7-sms-admin-recipient' => array(
                        'only_field' => true,
                        'type'              => 'text',
                        'label'             => __("To:",CF7_ISMS_TXT),
                        'description'       => __("<small>Enter Numbers By <code>,</code> for multiple</small>",CF7_ISMS_TXT),
                        'name'              => 'wpcf7si-settings[phone]',
                        'id'                => 'wpcf7-sms-recipient',
                        'class'             => array(),
                        'label_class'       => array(),
                        'custom_attributes' => array('size' => 70),
                        'input_class'       => array('wide'),
                        'return'            => false,
                        'value'             => isset($data['phone']) ? $data['phone'] : '',
                    ), 
            
                    'wpcf7-sms-admin-message' => array(
                        'only_field' => true, 'description' => '',
                        'type'              => 'textarea',
                        'label'             => __("Message body:",CF7_ISMS_TXT), 
                        'name'              => 'wpcf7si-settings[message]',
                        'id'                => 'wpcf7-sms-message',
                        'class'             => array(),
                        'label_class'       => array(),
                        'input_class'       => array('large'),
                        'return'            => false, 
                        'custom_attributes' => array('style' =>'width:75%;min-height:100px;'),
                        'value'             => isset($data['message']) ? $data['message'] : '',
                    ), 
                )
            ),
        );
        
        $fields = apply_filters("cf7_isms_form_fields",$fields,$data);
        return $fields ;
    }
    
    
    public function add_panel($form) { 
        if ( wpcf7_admin_has_edit_cap() ) {
            $options = get_option( 'wpcf7_international_sms_' . (method_exists($form, 'id') ? $form->id() : $form->id) );
            if( empty( $options ) || !is_array( $options ) ) {
                $options = array( 'phone' => '', 'message' => '', 'visitorNumber' => '','visitorMessage' => '');
            }
            $options['form'] = $form;
            $data =  $options;
            $fields = $this->get_fields($data);
            include(CF7_ISMS_ADMIN.'views/cf7-metabox.php'); 
        }
	}
 
}

return new CF7_International_SMS_Admin_Handler;