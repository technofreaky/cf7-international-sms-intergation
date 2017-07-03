<?php
global $fields;

$fields['general']['general'][] = array(
    'id' => CF7_ISMS_DB.'enable_sms_notification', 
    'type'    => 'checkbox',
    'label' => __('Enable Customer SMS Notifications',CF7_ISMS_TXT),
    'desc' => __('If checked then sms notifications will be sent',CF7_ISMS_TXT), 
    'attr'    => array( ),
);


$fields['general']['general'][] = array(
    'id' => CF7_ISMS_DB.'enable_admin_sms_notification', 
    'type'    => 'checkbox',
    'label' => __('Enable Admin SMS Notifications',CF7_ISMS_TXT),
    'desc' => __('If checked then Admin sms notifications will be sent',CF7_ISMS_TXT), 
    'attr'    => array( ),
);

$fields['general']['general'][] = array(
    'id' => CF7_ISMS_DB.'enable_admin_sms_notification_numbers', 
    'type'    => 'text',
    'label' => __('Admin Mobile Numbers',CF7_ISMS_TXT),
    'desc' => __('Enter Multiple Numbers By <code>,</code>',CF7_ISMS_TXT), 
    'attr'    => array(
        'placeholder' => __("Eg : +9100000000,+660000000",CF7_ISMS_TXT),
    ),
);
 



 
$fields['gateway']['gateway'][] = array(
    'id' => CF7_ISMS_DB.'gateway_url',
    'multiple' => 'true',
    'type'    => 'text',
    'label' => __('SMS Gateway URL',CF7_ISMS_TXT),
    'desc' => __('Just Enter Only The Gateway URL',CF7_ISMS_TXT), 
    'attr'    => array( 
        'placeholder' => 'Eg : http://example.com/send-sms.php',
        'multiple' => 'multiple',
        'style' => 'width:50%;',
    ),
);


$fields['gateway']['gateway'][] = array(
    'id' => CF7_ISMS_DB.'gateway_method', 
    'type'    => 'select',
    
    'label' => __('API Processing Method',CF7_ISMS_TXT),
    'desc' => __('',CF7_ISMS_TXT), 
    'attr' => array('class' => 'wc-enhanced-select', 'style' => 'width:15%;'),
    'options' => array(
        'post' => __("POST",CF7_ISMS_TXT),
        'get' => __("GET",CF7_ISMS_TXT),
    ),
);


$fields['gateway']['gateway'][] = array(
    'id' => CF7_ISMS_DB.'gateway_url_attrs', 
    'type'    => 'textarea',
    'label' => __('SMS Gateway Attributes',CF7_ISMS_TXT),
    'desc' => __('Just Enter Only The Gateway URL Attributes by <code>,</code> and values by <code>=</code> 
        
        <h3 style="margin:10px 0;">Available Tags : </h3>
        <ul style="margin:0;">
            <li><strong> Message Content : </strong> <code> %message%</code></li>
            <li><strong> To Number : </strong>  <code>%tonumber%</code></li>
            <li><strong> Sender ID :  </strong> <code>%senderid%</code></li>
        </ul>
        
    ',CF7_ISMS_TXT), 
    'attr'    => array( 
        'placeholder' => 'Eg : to=000000000000,message=%message%,senderID=%senderid%', 
        'style' => 'width:50%;',
    ),
);


$fields['gateway']['gateway'][] = array(
    'id' => CF7_ISMS_DB.'gateway_url_encode', 
    'type'    => 'checkbox',
    'label' => __('Encode URL Attributes',CF7_ISMS_TXT),
    'desc' => __('Depending on your SMS Gateway , you may or may not need to encode the url attributes. if you encounter sending errors please try with and or without encoding ',CF7_ISMS_TXT), 
    'attr'    => array( ),
);


$fields['gateway']['gateway'][] = array(
    'id' => CF7_ISMS_DB.'gateway_available_senderids', 
    'type'    => 'textarea',
    'label' => __('Available Sender ID\'s',CF7_ISMS_TXT),
    'desc' => __('You Can enter multiple sender ids and will allow you to set sender for each and every order message. enter multiple sender ids by <code>,</code>',CF7_ISMS_TXT), 
    'attr'    => array( 
        'placeholder' => 'Eg : senderid1,senderid2,senderid3', 
        'style' => 'width:50%;',
    ),
); 
 

$fields['gateway']['gateway'][] = array(
    'id' => CF7_ISMS_DB.'gateway_default_sender_id', 
    'type'    => 'select',
    'attr' => array('class' => 'wc-enhanced-select', 'style' => 'width:20%;'),
    'label' => __('Default Sender ID',CF7_ISMS_TXT),
    'desc' => __('Choose any 1 sender id as default. it will be used when there is no override done for the message. please fill the above sender ids box to get the list',CF7_ISMS_TXT), 
    'options' => cf7_isms_get_sender_id(),
);