jQuery(document).ready(function(){
    jQuery("#cf7ismsviewshortcodes").click(function(){
        jQuery("#cf7ismsshortcodes").slideToggle();
    });
    
    
    jQuery("#cf7_isms_send_sms_button").click(function(){
        var data = jQuery("#cf7_isms_custom_order_sms_send :input").serialize(); 
        var ref = jQuery(this);
        jQuery('div.cf7_isms_from_order_send_sms_result').html(''); 
        //jQuery(this).after('<span class="spinner is-active"></span>');
        //jQuery(this).attr('disabled','disabled');
        jQuery.ajax({
            url:ajaxurl + '?action=cf7_isms_send_from_order_editpage',
            data:data,
            method:'POST',
        }).done(function(res){
            if(res.success){
                var html = '<div id="cf7_isms_log_ajax_response" class="cf7_isms_log_ajax_response"> <span class="cf7_isms_ajax_success cf7_isms_log_success" style="font-weight:normal"> ';
                html += res.data;
                html += '</span></div>';
                jQuery('div.cf7_isms_from_order_send_sms_result').html(html); 
            } else {
                var html = '<div id="cf7_isms_log_ajax_response" class="cf7_isms_log_ajax_response"> <span class="cf7_isms_ajax_error cf7_isms_log_error" style="font-weight:normal"> ';
                html += res.data;
                html += '</span></div>';
                jQuery('div.cf7_isms_from_order_send_sms_result').html(html); 
            }
            
            //ref.parent().find('span.spinner').remove();
            //ref.removeAttr('disabled');
        })
    });
    
    

    jQuery("#cf7_isms_resend_orderstatus").click(function(){
        var data = jQuery("#cf7_isms_resend_form_fields :input").serialize(); 
        var refs = jQuery(this);
        jQuery('div.cf7_isms_from_order_send_sms_result').html(''); 
        jQuery(this).after('<span class="spinner is-active"></span>');
        jQuery(this).attr('disabled','disabled');
        jQuery.ajax({
            url:ajaxurl + '?action=cf7_isms_resend_status_sms',
            data:data,
            method:'POST',
        }).done(function(res){
            if(res.success){
                var html = '<div id="cf7_isms_log_ajax_response" class="cf7_isms_log_ajax_response"> <span class="cf7_isms_ajax_success cf7_isms_log_success" style="font-weight:normal"> ';
                html += res.data;
                html += '</span></div>';
                jQuery('div.cf7_isms_from_order_send_sms_result').html(html); 
            } else {
                var html = '<div id="cf7_isms_log_ajax_response" class="cf7_isms_log_ajax_response"> <span class="cf7_isms_ajax_error cf7_isms_log_error" style="font-weight:normal"> ';
                html += res.data;
                html += '</span></div>';
                jQuery('div.cf7_isms_from_order_send_sms_result').html(html); 
            }
            
            refs.parent().find('span.spinner').remove();
            refs.removeAttr('disabled');
        })
    })
});