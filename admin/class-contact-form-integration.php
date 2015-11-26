<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wordpress.org/plugins/woocommerce-role-based-price/
 *
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    @TODO
 * @subpackage @TODO
 * @author     Varun Sridharan <varunsridharan23@gmail.com>
 */
if ( ! defined( 'WPINC' ) ) { die; }

class Contact_Form_7_International_Sms_Integration_Plugin_Integration extends Contact_Form_7_International_Sms_Integration_Admin {
	
    /**
	 * Initialize the class and set its properties.
	 * @since      0.1
	 */
	public function __construct() {
		add_filter( 'wpcf7_editor_panels' , array($this, 'new_menu'),99);
		add_action( 'wpcf7_after_save', array( &$this, 'save_form' ) );
	}

	public function new_menu ($panels) {
		$panels['cf7si-sms-panel'] = array(
				'title' => __('Interntional SMS',CF7SI_TXT),
				'callback' => array($this, 'add_panel')
		);
		return $panels;
	}
	
	public function add_panel($form) { 
		if ( wpcf7_admin_has_edit_cap() ) {
		  $options = get_option( 'wpcf7_international_sms_' . (method_exists($form, 'id') ? $form->id() : $form->id) );
		  if( empty( $options ) || !is_array( $options ) ) {
			$options = array( 'phone' => '', 'message' => '' );
		  }
		  $options['form'] = $form;
          $data =  $options; 
		  include(CF7SI()->get_vars('PATH').'template/cf7-template.php'); 
		}
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
	 
}
?>