<?php
/**
 * Class for registering settings and sections and for display of the settings form(s).
 * For detailed instructions see: https://github.com/keesiemeijer/WP-Settings
 *
 * @link http://wordpress.org/plugins/cf7-international-sms-integration
 * @package CF7ISMS
 * @subpackage CF7ISMS/WordPress/Settings
 * @since 3.0
 * @version 2.0
 * @author keesiemeijer
 */
if ( ! defined( 'WPINC' ) ) { die; }
class CF7_International_SMS_Settings_Framework {
    private $page_hook = '';
    public $settings;
    private $settings_page;
    private $settings_section;
    private $settings_fields;
    private $create_function;
    private $settings_key;
    private $settings_values;
    private $pageName;
	
    function __construct($page_hook = '',$pageName = '') {
        $this->settings_section = array();
        $this->settings_fields = array();
        $this->create_function = array();
       
		$this->page_hook = $page_hook;
        $this->pageName = $pageName;
        
        
        
        if(empty($pageName)){
            $this->pageName = __('International SMS',CF7_ISMS_TXT);
        } else {
            cf7_isms_add_vars('settings_page',$this->page_hook);
        }
        
        if(empty($page_hook)) {
            add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        }
        add_action( 'admin_init', array( $this, 'admin_init' ) );
    }
    
    function admin_menu() { 
		$this->page_hook = add_submenu_page('wpcf7',
											$this->pageName,
											$this->pageName,
											'wpcf7_read_contact_forms',
                                            CF7_ISMS_SLUG.'-settings',
                                            array( $this, 'admin_page' ) );
        
        cf7_isms_add_vars('settings_page',$this->page_hook);
	}
    
    private function add_settings_pages(){
		$pages = array();
		$pages = apply_filters('cf7_isms_settings_pages',$pages);
        $this->settings_page = $pages;
    }
    
    private function add_settings_section(){
        $section =  array();
        $section = apply_filters('cf7_isms_settings_section',$section);
        $this->settings_section = $section;
    }
    
    private function add_settings_fields(){
        global $fields;
        $fields =  array();
		$fields = apply_filters('cf7_isms_settings_fields',$fields);
        $this->settings_field = $fields;
    }
	
    private function create_callback_function(){
        $sec = $this->settings_section;
        
        foreach($sec as $sk => $s){
            if(is_array($s)){
                $c = count($s);
                $a = 0;
                while($a < $c){
                    if(isset($s[$a]['validate_callback'])){
                        $this->create_function[] =  $s[$a]['id'];
                        $s[$a]['validate_callback'] = '';
                        $file = addslashes(PUGIN_SETTINGS.'validate-'.$s[$a]['id'].'.php');
                         $s[$a]['validate_callback'] = create_function('$fields', 'do_action("cf7_isms_settings_validate",$fields); do_action("cf7_isms_settings_validate_'.$s[$a]['id'].'",$fields);');
                    }
                    $a++;
                }
            }
            
            $this->settings_section[$sk] = $s; 
        }
    } 

    function admin_init(){ 
         $this->add_settings_pages(); 
        $this->add_settings_section();
        $this->create_callback_function();
        
		$this->settings = new CF7_International_SMS_WP_Settings();
        $this->add_settings_fields();
        $this->settings->add_pages($this->settings_page);
        $sections = $this->settings_section;
        
        foreach ($sections as $page_id => $section_value){
            $pages = $this->settings->add_sections($page_id,$section_value);
        }
        
        $fields = $this->settings_field;
        foreach($fields as $page_id => $section_fields){
            foreach($section_fields as $section_id => $sfields){
                if(is_array($sfields)){
                    foreach($sfields as $f){
                        $pages = $this->settings->add_field($page_id,$section_id,$f);
                    }
                
                } else {
                    $pages = $this->settings->add_field($page_id,$section_id,$sfields);
                }
                
            } 
        }
		
		$this->settings->init($pages, CF7_ISMS_DB );
    }

    public function admin_page(){
		echo '<div class="wrap cf7_isms_settings">';
		settings_errors();
		$this->settings->render_header();
		//echo $this->settings->debug;
		$this->settings->render_form();
		echo '</div>';
	}
 }