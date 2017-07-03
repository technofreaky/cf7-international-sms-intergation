<?php
/**
 * Common Plugin Functions
 * 
 * @link http://wordpress.org/plugins/cf7-international-sms-integration
 * @package CF7ISMS
 * @subpackage CF7ISMS/core
 * @since 1.0
 */
if ( ! defined( 'WPINC' ) ) { die; }


global $cf7_isms_db_settins_values, $cf7_isms_vars;
$cf7_isms_db_settins_values = array();
$cf7_isms_vars = array();

add_action('cf7_isms_loaded','cf7_isms_get_settings_from_db',1);

if(!function_exists('cf7_isms_vars')){
    function cf7_isms_vars($key,$values = false){
        global $cf7_isms_vars;
        if(isset($cf7_isms_vars[$key])){ 
            return $cf7_isms_vars[$key]; 
        }
        return $values;
    }
}

if(!function_exists('cf7_isms_add_vars')){
    function cf7_isms_add_vars($key,$values){
        global $cf7_isms_vars;
        if(! isset($cf7_isms_vars[$key])){ 
            $cf7_isms_vars[$key] = $values; 
            return true; 
        }
        return false;
    }
}

if(!function_exists('cf7_isms_remove_vars')){
    function cf7_isms_remove_vars($key){
        global $cf7_isms_vars;
        if(isset($cf7_isms_vars[$key])){ 
            unset($cf7_isms_vars[$key]);
            return true; 
        }
        return false;
    }
}

if(!function_exists('cf7_isms_option')){
	function cf7_isms_option($key = '',$default = false){
		global $cf7_isms_db_settins_values;
		if($key == ''){return $cf7_isms_db_settins_values;}
		if(isset($cf7_isms_db_settins_values[CF7_ISMS_DB.$key])){
			return $cf7_isms_db_settins_values[CF7_ISMS_DB.$key];
		} 
		
		return $default;
	}
}

if(!function_exists('cf7_isms_get_settings_from_db')){
	/**
	 * Retrives All Plugin Options From DB
	 */
	function cf7_isms_get_settings_from_db(){
		global $cf7_isms_db_settins_values;
		$section = array();
		$section = apply_filters('cf7_isms_settings_section',$section);  
		$values = array();
		foreach($section as $settings){
			foreach($settings as $set){
				$db_val = get_option(CF7_ISMS_DB.$set['id']);
				if(is_array($db_val)){ unset($db_val['section_id']); $values = array_merge($db_val,$values); }
			}
		}        
		$cf7_isms_db_settins_values = $values;
	}
}

if(!function_exists('cf7_isms_is_request')){
    /**
	 * What type of request is this?
	 * string $type ajax, frontend or admin
	 * @return bool
	 */
    function cf7_isms_is_request( $type ) {
        switch ( $type ) {
            case 'admin' :
                return is_admin();
            case 'ajax' :
                return defined( 'DOING_AJAX' );
            case 'cron' :
                return defined( 'DOING_CRON' );
            case 'frontend' :
                return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
        }
    }
}

if(!function_exists('cf7_isms_current_screen')){
    /**
     * Gets Current Screen ID from wordpress
     * @return string [Current Screen ID]
     */
    function cf7_isms_current_screen(){
       $screen =  get_current_screen();
       return $screen->id;
    }
}

if(!function_exists('cf7_isms_is_screen')){
    function cf7_isms_is_screen($check_screen = '',$current_screen = ''){
        if(empty($check_screen)) {$check_screen = cf7_isms_get_screen_ids(); }
        if(empty($current_screen)) {$current_screen = cf7_isms_current_screen(); }
        
        if(is_array($check_screen)){
            if(in_array($current_screen , $check_screen)){
                return true;
            }
        }
        
        if(is_string($check_screen)){
            if($check_screen == $current_screen){
                return true;
            }
        }
        return false;
    }
}

if(!function_exists('cf7_isms_get_screen_ids')){
    /**
     * Returns Predefined Screen IDS
     * @return [Array] 
     */
    function cf7_isms_get_screen_ids(){
        $screen_ids = array();
        //$screen_ids[] = 'woocommerce_page_cf7-international-sms-integration-settings';
        $screen_ids[] = cf7_isms_vars('settings_page');
        return $screen_ids;
    }
}

if(!function_exists('cf7_isms_dependency_message')){
	function cf7_isms_dependency_message(){
		$text = __( CF7_ISMS_NAME . ' requires <b> Contact Form 7 </b> To Be Installed..  <br/> <i>Plugin Deactivated</i> ', CF7_ISMS_TXT);
		return $text;
	}
}

if(!function_exists('cf7_isms_get_template')){
	function cf7_isms_get_template($name,$args = array(),$template_base = '',$remote_template = ''){
        if(empty($template_base)){$template_base = CF7_ISMS_PATH.'/templates/';}
        if(empty($remote_template)){$remote_template = 'woocommerce/';}
		wc_get_template( $name, $args ,$remote_template,  $template_base);
	}
}

if(!function_exists('cf7_isms_settings_products_json')){
    function cf7_isms_settings_products_json($ids){
        $json_ids    = array();
        if(!empty($ids)){
            $ids = explode(',',$ids);
            foreach ( $ids as $product_id ) {
                $product = wc_get_product( $product_id );
                $json_ids[ $product_id ] = wp_kses_post( $product->get_formatted_name() );
            }   
        }
        return $json_ids;
    }
}

if(!function_exists('cf7_isms_settings_get_categories')){
    function cf7_isms_settings_get_categories($tax='product_cat'){
        $args = array();
        $args['hide_empty'] = false;
        $args['number'] = 0; 
        $args['pad_counts'] = true; 
        $args['update_term_meta_cache'] = false;
        $terms = get_terms($tax,$args);
        $output = array();
        
        foreach($terms as $term){
            $output[$term->term_id] = $term->name .' ('.$term->count.') ';
        }
        
        return $output; 
    }
}

if(!function_exists('cf7_isms_settings_page_link')){
    function cf7_isms_settings_page_link($tab = '',$section = ''){
        $settings_url = admin_url('admin.php?page='.CF7_ISMS_SLUG.'-settings');
        if(!empty($tab)){$settings_url .= '&tab='.$tab;}
        if(!empty($section)){$settings_url .= '#'.$section;}
        return $settings_url;
    }   
}

if(!function_exists('cf7_isms_get_settings_sample')){
	/**
	 * Retunrs the sample array of the settings framework
	 * @param [string] [$type = 'page' | 'section' | 'field'] [[Description]]
	 */
	function cf7_isms_get_settings_sample($type = 'page'){
		$return = array();
		
		if($type == 'page'){
			$return = array( 
				'id'=>'settings_general', 
				'slug'=>'general', 
				'title'=>__('General',CF7_ISMS_TXT),
				'multiform' => 'false / true',
				'submit' => array( 
					'text' => __('Save Changes',CF7_ISMS_TXT), 
					'type' => 'primary / secondary / delete', 
					'name' => 'submit'
				)
			);
			
		} else if($type == 'section'){
			$return['page_id'][] = array(
				'id'=>'general',
				'title'=>'general', 
				'desc' => 'general',
				'submit' => array(
					'text' => __('Save Changes',CF7_ISMS_TXT), 
					'type' => 'primary / secondary / delete', 
					'name' => 'submit'
				)
			);
		} else if($type == 'field'){
			$return['page_id']['section_id'][] = array(
				'id' => '',
				'type' => 'text, textarea, checkbox, multicheckbox, radio, select, field_row, extra',
				'label' => '',
				'options' => 'Only required for type radio, select, multicheckbox [KEY Value Pair]',
				'desc' => '',
				'size' => '',
				'default' => '',
				'attr' => "Key Value Pair",
				'before' => 'Content before the field label',
				'after' => 'Content after the field label',
				'content' => 'Content used for type extra' ,
				'text_type' => "Set the type for text input field (e.g. 'hidden' )",
			);
		}
	}
}

if(!function_exists('cf7_isms_check_active_addon')){
	function cf7_isms_check_active_addon($slug){
		$addons = cf7_isms_get_active_addons();
		if(in_array($slug,$addons)){ return true; }
		return false;
	}
}

if(!function_exists('cf7_isms_get_active_addons')){
	/**
	 * Returns Active Addons List
	 * @return [[Type]] [[Description]]
	 */
	function cf7_isms_get_active_addons(){
		$addons = get_option(CF7_ISMS_DB.'active_addons',array()); 
		return $addons;
	}
}

if(!function_exists('cf7_isms_update_active_addons')){
	/**
	 * Returns Active Addons List
	 * @return [[Type]] [[Description]]
	 */
	function cf7_isms_update_active_addons($addons){
		update_option(CF7_ISMS_DB.'active_addons',$addons); 
		return true;
	}
}

if(!function_exists('cf7_isms_activate_addon')){
	function cf7_isms_activate_addon($slug){
		$active_list = cf7_isms_get_active_addons();
		if(!in_array($slug,$active_list)){
			$active_list[] = $slug;
			cf7_isms_update_active_addons($active_list);
			return true;
		}
		return false;
	}
}

if(!function_exists('cf7_isms_deactivate_addon')){
	function cf7_isms_deactivate_addon($slug){
		$active_list = cf7_isms_get_active_addons();
		if(in_array($slug,$active_list)){
			$key = array_search($slug, $active_list);
			unset($active_list[$key]);
			cf7_isms_update_active_addons($active_list);
			return true;
		}
		return false;
	}
}

if(!function_exists('cf7_isms_admin_notice')){
    function cf7_isms_admin_notice($msg , $type = 'updated'){
        $notice = ' <div class="'.$type.' settings-error notice is-dismissible" id="setting-error-settings_updated"> 
<p>'.$msg.'</p><button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
        return $notice;
    }
}

if(!function_exists('cf7_isms_remove_notice')){
    function cf7_isms_remove_notice($id){
        CF7_International_SMS_Admin_Notices::getInstance()->deleteNotice($id);
        return true;
    }
}

if(!function_exists('cf7_isms_notice')){
    function cf7_isms_notice( $message, $type = 'update',$args = array()) {
        $notice = '';
        $defaults = array('times' => 1,'screen' => array(),'users' => array(), 'wraper' => true,'id'=>'');    
        $args = wp_parse_args( $args, $defaults );
        extract($args);
        
        if($type == 'error'){
            $notice = new CF7_International_SMS_Admin_Error_Notice($message,$id,$times, $screen, $users);
        }
        
        if($type == 'update'){
            $notice = new CF7_International_SMS_Admin_Updated_Notice($message,$id,$times, $screen, $users);
        }
        
        if($type == 'upgrade'){
            $notice = new CF7_International_SMS_Admin_UpdateNag_Notice($message,$id,$times, $screen, $users);
        } 
        
        $msgID = $notice->getId();
        $message = str_replace('$msgID$',$msgID,$message);
        $notice->setContent($message);
        $notice->setWrapper($wraper);
        CF7_International_SMS_Admin_Notices::getInstance()->addNotice($notice);
    }
}

if(!function_exists('cf7_isms_admin_error')){
    function cf7_isms_admin_error( $message,$times = 1, $id, $screen = array(),$args = array()) {
        $args['id'] = $id;
        $args['times'] = $times;
        $args['screen'] = $screen;
        cf7_isms_notice($message,'error',$args);
    }
}

if(!function_exists('cf7_isms_admin_update')){
    function cf7_isms_admin_update( $message,$times = 1, $id, $screen = array(),$args = array()) {
        $args['id'] = $id;
        $args['times'] = $times;
        $args['screen'] = $screen;
        cf7_isms_notice($message,'update',$args);
    }
}

if(!function_exists('cf7_isms_admin_upgrade')){
    function cf7_isms_admin_upgrade( $message,$times = 1, $id, $screen = array(),$args = array()) {
        $args['id'] = $id;
        $args['times'] = $times;
        $args['screen'] = $screen;
        cf7_isms_notice($message,'upgrade',$args);
    }
}

if(!function_exists('cf7_isms_remove_link')){
    function cf7_isms_remove_link($attributes = '',$msgID = '$msgID$', $text = 'Remove Notice') {
        if(!empty($msgID)){
            $removeKey = CF7_ISMS_DB.'MSG';
            $url = admin_url().'?'.$removeKey.'='.$msgID ;
            //$url = wp_nonce_url($url, 'WCQDREMOVEMSG');
            $url = urldecode($url);
            $tag = '<a '.$attributes.' href="'.$url.'">'.__($text,CF7_ISMS).'</a>';
            return $tag;
        }
    }
}

if(!function_exists('cf7_isms_get_ajax_overlay')){
	/**
	 * Prints WC PBP Ajax Loading Code
	 */
	function cf7_isms_get_ajax_overlay($echo = true){
		$return = '<div class="cf7_isms_ajax_overlay">
		<div class="cf7_isms_sk-folding-cube">
		<div class="cf7_isms_sk-cube1 cf7_isms_sk-cube"></div>
		<div class="cf7_isms_sk-cube2 cf7_isms_sk-cube"></div>
		<div class="cf7_isms_sk-cube4 cf7_isms_sk-cube"></div>
		<div class="cf7_isms_sk-cube3 cf7_isms_sk-cube"></div>
		</div>
		</div>';
		if($echo){echo $return;}
		else{return $return;}
	}
}

if(!function_exists("cf7_isms_get_sender_ids")){
    function cf7_isms_get_sender_id($true = true){
        $senderids = cf7_isms_option('gateway_available_senderids',array());
        if($true){
            if(empty($senderids)){return array();}
            $senderids = explode(',',$senderids); 
            $sender = array(''=> __('Default',CF7_ISMS_TXT));
            $senderids = array_merge($sender,$senderids);
            $senderids = array_combine($senderids,$senderids);
            return $senderids;
        }
        
        return $senderids;
    }
}

if(!function_exists('cf7_isms_wc_v')){
    function cf7_isms_wc_v($compare = '>=',$version = ''){
        $version = empty($version) ? WOOCOMMERCE_VERSION : $version; 
        if(version_compare( WOOCOMMERCE_VERSION, $version,$compare)){
            return true;
        } else {
            return false;
        }
    }
}

if(!function_exists("cf7_isms_order_shortcodes")){

    /**
     * Get sms order shortcodes
     *
     * @since 1.7
     *
     * @return array
     */
    function cf7_isms_order_shortcodes() {
        return apply_filters( 'cf7_isms_order_shortcodes', array(
            '[order_status]',
            '[order_id]',
            '[order_amount]',
            '[order_items]',
            '[billing_firstname]',
            '[billing_lastname]',
            '[billing_email]',
            '[billing_address1]',
            '[billing_address2]',
            '[billing_country]',
            '[billing_city]',
            '[billing_state]',
            '[billing_postcode]',
            '[shipping_address1]',
            '[shipping_address2]',
            '[shipping_country]',
            '[shipping_city]',
            '[shipping_state]',
            '[shipping_postcode]',
            '[payment_method]',
            '[customer_phone_number]',
            '[checkout_url]',
            '[checkout_url_force]',
        ) );
    }
}

if(!function_exists('cf7_isms_form_field')) {

	/**
	 * Outputs a checkout/address form field.
	 *
	 * @subpackage	Forms
	 * @param string $key
	 * @param mixed $args
	 * @param string $value (default: null)
	 */
    function cf7_isms_form_field( $key, $args, $value = null ) {
		$defaults = array(
			'type'              => 'text',
			'label'             => '',
			'description'       => '',
			'placeholder'       => '',
            'name'              => $key,
			'maxlength'         => false,
			'required'          => false,
			'autocomplete'      => false,
			'id'                => $key,
			'class'             => array(),
			'label_class'       => array(),
			'input_class'       => array(),
			'return'            => false,
			'options'           => array(),
			'custom_attributes' => array(),
			'validate'          => array(),
			'default'           => '',
            'value'             => null,
            'only_field' => false,
		);
        
        
        
		$args = wp_parse_args( $args, $defaults );
        if(is_null($value)){$value = $args['value'];}
		$args = apply_filters( 'cf7_isms_form_field_args', $args, $key, $value );
        $key = $args['name'];
            
		if ( $args['required'] ) {
			$args['class'][] = 'validate-required';
			$required = ' <abbr class="required" title="' . esc_attr__( 'required', CF7_ISMS_TXT ) . '">*</abbr>';
		} else {
			$required = '';
		}
        
		$args['maxlength'] = ( $args['maxlength'] ) ? 'maxlength="' . absint( $args['maxlength'] ) . '"' : '';
		$args['autocomplete'] = ( $args['autocomplete'] ) ? 'autocomplete="' . esc_attr( $args['autocomplete'] ) . '"' : '';
		if ( is_string( $args['label_class'] ) ) {
			$args['label_class'] = array( $args['label_class'] );
		}
		if ( is_null( $value ) ) {
			$value = $args['default'];
		}
        
		$custom_attributes = array();
		if ( ! empty( $args['custom_attributes'] ) && is_array( $args['custom_attributes'] ) ) {
			foreach ( $args['custom_attributes'] as $attribute => $attribute_value ) {
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
			}
		}
		if ( ! empty( $args['validate'] ) ) {
			foreach( $args['validate'] as $validate ) {
				$args['class'][] = 'validate-' . $validate;
			}
		}
		$field = '';
		$label_id = $args['id'];
		$field_container = '<div class="%1$s" id="%2$s">%3$s</div>';
		switch ( $args['type'] ) {
                
			case 'textarea' :
				$field .= '<textarea name="' . esc_attr( $key ) . '" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) .'" id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '" ' . $args['maxlength'] . ' ' . $args['autocomplete'] . ' ' . ( empty( $args['custom_attributes']['rows'] ) ? ' rows="2"' : '' ) . ( empty( $args['custom_attributes']['cols'] ) ? ' cols="5"' : '' ) . implode( ' ', $custom_attributes ) . '>'. esc_textarea( $value  ) .'</textarea>';
				break;
                
			case 'checkbox' :
				$field = '<label class="checkbox ' . implode( ' ', $args['label_class'] ) .'" ' . implode( ' ', $custom_attributes ) . '>
						<input type="' . esc_attr( $args['type'] ) . '" class="input-checkbox ' . esc_attr( implode( ' ', $args['input_class'] ) ) .'" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="1" '.checked( $value, 1, false ) .' /> '
						 . $args['label'] . $required . '</label>';
				break;
                
			case 'password' :
			case 'text' :
			case 'email' :
            case 'hidden' :
			case 'tel' :
			case 'number' :
				$field .= '<input type="' . esc_attr( $args['type'] ) . '" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) .'" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '" ' . $args['maxlength'] . ' ' . $args['autocomplete'] . ' value="' . esc_attr( $value ) . '" ' . implode( ' ', $custom_attributes ) . ' />';
				break;
                
			case 'select' :
				$options = $field = '';
				if ( ! empty( $args['options'] ) ) {
					foreach ( $args['options'] as $option_key => $option_text ) { 
                        if(is_array($option_text)){
                            $options .= '<optgroup label="'.$option_key.'"> ';
                            foreach($option_text as $opk => $opv){
                                if ( '' === $opk ) {
                                    if ( empty( $args['placeholder'] ) ) {
                                        $args['placeholder'] = $opk ? $opk : __( 'Choose an option', CF7_ISMS_TXT );
                                    }
                                    $custom_attributes[] = 'data-allow_clear="true"';
                                }       
                                
                                $options .= '<option value="'.esc_attr( $opk ).'"'.selected($value,$opk,false).'>'.esc_attr($opv).'</option>';
                            }
                            $options .= '</optgroup> ';
                        } else {
                            if ( '' === $option_key ) {
                                if ( empty( $args['placeholder'] ) ) {
                                    $args['placeholder'] = $option_text ? $option_text : __( 'Choose an option', CF7_ISMS_TXT );
                                }
                                $custom_attributes[] = 'data-allow_clear="true"';
                            }
                            $options .= '<option value="' . esc_attr( $option_key ) . '" '. selected( $value, $option_key, false ) . '>' . esc_attr( $option_text ) .'</option>';
                        }
					}
					$field .= '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" class="select '. esc_attr( implode( ' ', $args['input_class'] ) ) . '" ' . implode( ' ', $custom_attributes ) . ' data-placeholder="' . esc_attr( $args['placeholder'] ) . '" ' . $args['autocomplete'] . '>
							' . $options . '
						</select>';
				}
				break;
                
			case 'radio' :
				$label_id = current( array_keys( $args['options'] ) );
				if ( ! empty( $args['options'] ) ) {
					foreach ( $args['options'] as $option_key => $option_text ) {
						$field .= '<div class="input-radio-container" > <input type="radio" class="input-radio ' . esc_attr( implode( ' ', $args['input_class'] ) ) .'" value="' . esc_attr( $option_key ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '_' . esc_attr( $option_key ) . '"' . checked( $value, $option_key, false ) . ' />';
						$field .= '<label for="' . esc_attr( $args['id'] ) . '_' . esc_attr( $option_key ) . '" class="radio ' . implode( ' ', $args['label_class'] ) .'">' . $option_text . '</label> </div>';
					}
				}
				break;
                
            case 'checkbox_group' :
				$label_id = current( array_keys( $args['options'] ) );
				if ( ! empty( $args['options'] ) ) {
					foreach ( $args['options'] as $option_key => $option_text ) {
						$field .= '<div class="input-checkbox-container" > <input type="checkbox" class="input-checkbox ' . esc_attr( implode( ' ', $args['input_class'] ) ) .'" value="' . esc_attr( $option_key ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '_' . esc_attr( $option_key ) . '"' . checked( $value, $option_key, false ) . ' />';
						$field .= '<label for="' . esc_attr( $args['id'] ) . '_' . esc_attr( $option_key ) . '" class="checkbox ' . implode( ' ', $args['label_class'] ) .'">' . $option_text . '</label> </div>';
					}
				}
				break;
                
            case 'submit' :
            case 'button' :
            case 'reset' :
                $value = empty($value) ? $value = esc_attr( $args['label']) : $value;
                $args['label'] = '';
                $field .= '<button type="' . esc_attr( $args['type'] ) . '" class="button button-' . esc_attr( $args['type'] ) . '  ' . esc_attr( $args['type'] ) . ' ' . esc_attr( implode( ' ', $args['input_class'] ) ) .'" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" '.implode( ' ', $custom_attributes ).'>' . esc_attr( $value ) . '</button> ';
                break;            
		}
		if ( ! empty( $field ) ) {
			$field_html = '';
            
            if($args['only_field'] === false){
                if ( $args['label'] && 'checkbox' != $args['type'] ) {
                    $field_html .= '<div class="field-label"> <label for="' . esc_attr( $label_id ) . '" class="' . esc_attr( implode( ' ', $args['label_class'] ) ) .'">' . $args['label'] . $required . '</label></div>';
                }
                $field_html .= $field;
                if ( $args['description'] ) {
                    $field_html .= '<span class="description">' . esc_html( $args['description'] ) . '</span>';
                }
                $container_class = 'form-row ' . esc_attr( implode( ' ', $args['class'] ) );
                $container_id = esc_attr( $args['id'] ) . '_field';
                $after = ! empty( $args['clear'] ) ? '<div class="clear"></div>' : '';
                $field = sprintf( $field_container, $container_class, $container_id, $field_html ) . $after;
            }
		}
		$field = apply_filters( 'cf7_isms_form_field_' . $args['type'], $field, $key, $args, $value );
		if ( $args['return'] ) {
			return $field;
		} else {
			echo $field;
		}
	}
}