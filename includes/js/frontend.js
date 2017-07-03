jQuery(document).ready(function(){
    
    jQuery("#wfb-field-371116227-div").show();  //Reason for exchange 
    jQuery("#wfb-field-1557780079-div").hide(); // Others Field
    jQuery("#wfb-field-1569312045-div").hide();  // in Case of refund or refund.

    jQuery("select[name=warranty_request_type]").change(function(){
        var value = jQuery(this).val();
        if(value == 'refund'){
            jQuery("#wfb-field-371116227-div").hide();  //Reason for exchange 
            jQuery("#wfb-field-1557780079-div").hide(); // Others Field
            jQuery("#wfb-field-1569312045-div").show();  // in Case of refund or refund.
        }else if(value == 'replacement'){
            jQuery("#wfb-field-371116227-div").show();  //Reason for exchange 
            jQuery("#wfb-field-1557780079-div").hide(); // Others Field
            jQuery("#wfb-field-1569312045-div").hide();  // in Case of refund or refund.
        } else {
            jQuery("#wfb-field-371116227-div").hide();  //Reason for exchange 
            jQuery("#wfb-field-1557780079-div").hide(); // Others Field
            jQuery("#wfb-field-1569312045-div").hide();  // in Case of refund or refund
        }
    })
})