<?php 
/**
 * Plugin Main File
 *
 * @link http://wordpress.org/plugins/cf7-international-sms-integration
 * @package CF7ISMS
 * @subpackage CF7ISMS/core
 * @since 1.0
 */
if ( ! defined( 'WPINC' ) ) { die; }
 
class CF7_International_SMS {
	public $version = '1.2';
	public $plugin_vars = array();
	
	protected static $_instance = null; # Required Plugin Class Instance
    protected static $functions = null; # Required Plugin Class Instance
	protected static $admin = null;     # Required Plugin Class Instance
	protected static $settings = null;  # Required Plugin Class Instance

    /**
     * Creates or returns an instance of this class.
     */
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
        $this->define_constant();
        $this->load_required_files();
        add_action('init',array($this,'init_class'));
        add_action('plugins_loaded', array( $this, 'after_plugins_loaded' ));
        add_filter('load_textdomain_mofile',  array( $this, 'load_plugin_mo_files' ), 10, 2);
    }
	
	/**
	 * Throw error on object clone.
	 *
	 * Cloning instances of the class is forbidden.
	 *
	 * @since 1.0
	 * @return void
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cloning instances of the class is forbidden.', CF7_ISMS_TXT), CF7_ISMS_V );
	}	

	/**
	 * Disable unserializing of the class
	 *
	 * Unserializing instances of the class is forbidden.
	 *
	 * @since 1.0
	 * @return void
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of the class is forbidden.',CF7_ISMS_TXT), CF7_ISMS_V);
	}

    /**
     * Loads Required Plugins For Plugin
     */
    private function load_required_files(){
       $this->load_files(CF7_ISMS_SETTINGS.'class-wp-*.php');
       $this->load_files(CF7_ISMS_INC.'abstract/*.php');
        $this->load_files(CF7_ISMS_INC.'class-*.php');
       $this->load_files(CF7_ISMS_INC.'order_status/class-order-sms.php');
        $this->load_files(CF7_ISMS_INC.'order_status/class-*.php');
	   
        
       if(cf7_isms_is_request('admin')){
           $this->load_files(CF7_ISMS_ADMIN.'class-*.php');
       } 

        do_action('cf7_isms_before_addons_load'); 
    }
    
	public function load_addons(){ 
		$addons = cf7_isms_get_active_addons();
		if(!empty($addons)){
			foreach($addons as $addon){
				if(apply_filters('cf7_isms_load_addon',true,$addon)){
					do_action('cf7_isms_before_'.$addon.'_addon_load');
					$this->load_addon($addon);
					do_action('cf7_isms_after_'.$addon.'_addon_load');
				}
			}
		}
	}
    
	public function load_addon($file){
		if(file_exists(CF7_ISMS_ADDON.$file)){
			$this->load_files(CF7_ISMS_ADDON.$file);
		} else if(file_exists($file = apply_filters('cf7_isms_addon_file_location',$file))) {
			$this->load_files($file);
		} else {
            if(has_action('cf7_isms_addon_'.$file.'_load')){
                do_action('cf7_isms_addon_'.$file.'_load');    
            } else {
                
                cf7_isms_deactivate_addon($file);
            }
			
		}
	}
   
    /**
     * Inits loaded Class
     */
    public function init_class(){ 
        do_action('cf7_isms_loaded');
        do_action('cf7_isms_before_init');
        self::$functions = new CF7_International_SMS_Functions;
		self::$settings = new CF7_International_SMS_Settings_Framework; 
        
        
        if(cf7_isms_is_request('admin')){
            self::$admin = new CF7_International_SMS_Admin;
            
        }
        do_action('cf7_isms_after_init');
        
    } 
    
    
	# Returns Plugin's Functions Instance
	public function func(){
		return self::$functions;
	}
	
	# Returns Plugin's Settings Instance
	public function settings(){
		return self::$settings;
	}
	
	# Returns Plugin's Admin Instance
	public function admin(){
		return self::$admin;
	}
    
    /**
     * Loads Files Based On Give Path & regex
     */
    protected function load_files($path,$type = 'require'){
        foreach( glob( $path ) as $files ){
            if($type == 'require'){ require_once( $files ); } 
			else if($type == 'include'){ include_once( $files ); }
        } 
    }
    
    /**
     * Set Plugin Text Domain
     */
    public function after_plugins_loaded(){
        load_plugin_textdomain(CF7_ISMS_TXT, false, CF7_ISMS_LANGUAGE_PATH );
    }
    
    /**
     * load translated mo file based on wp settings
     */
    public function load_plugin_mo_files($mofile, $domain) {
        if (CF7_ISMS_TXT === $domain)
            return CF7_ISMS_LANGUAGE_PATH.'/'.get_locale().'.mo';

        return $mofile;
    }
    
    /**
     * Define Required Constant
     */
    private function define_constant(){
        $this->define('CF7_ISMS_NAME', 'CF7 International SMS'); # Plugin Name
        $this->define('CF7_ISMS_SLUG', 'cf7-international-sms-integration'); # Plugin Slug
        $this->define('CF7_ISMS_TXT',  'cf7-international-sms-integration'); #plugin lang Domain
		$this->define('CF7_ISMS_DB', 'cf7_isms_');
		$this->define('CF7_ISMS_V',$this->version); # Plugin Version
		
		$this->define('CF7_ISMS_LANGUAGE_PATH',CF7_ISMS_PATH.'languages'); # Plugin Language Folder
		$this->define('CF7_ISMS_ADMIN',CF7_ISMS_INC.'admin/'); # Plugin Admin Folder
		$this->define('CF7_ISMS_SETTINGS',CF7_ISMS_ADMIN.'settings_framework/'); # Plugin Settings Folder
		$this->define('CF7_ISMS_ADDON',CF7_ISMS_PATH.'addons/');
        
		$this->define('CF7_ISMS_URL',plugins_url('', __FILE__ ).'/');  # Plugin URL
		$this->define('CF7_ISMS_CSS',CF7_ISMS_URL.'includes/css/'); # Plugin CSS URL
		$this->define('CF7_ISMS_IMG',CF7_ISMS_URL.'includes/img/'); # Plugin IMG URL
		$this->define('CF7_ISMS_JS',CF7_ISMS_URL.'includes/js/'); # Plugin JS URL
        
        
        $this->define('CF7_ISMS_ADDON_URL',CF7_ISMS_URL.'addons/');  # Plugin URL
		$this->define('CF7_ISMS_ADDON_CSS',CF7_ISMS_ADDON_URL.'includes/css/'); # Plugin CSS URL
		$this->define('CF7_ISMS_ADDON_IMG',CF7_ISMS_ADDON_URL.'includes/img/'); # Plugin IMG URL
		$this->define('CF7_ISMS_ADDON_JS',CF7_ISMS_ADDON_URL.'includes/js/'); # Plugin JS URL
    }
	
    /**
	 * Define constant if not already set
	 * @param  string $name
	 * @param  string|bool $value
	 */
    protected function define($key,$value){
        if(!defined($key)){
            define($key,$value);
        }
    }
    
}