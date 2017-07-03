<br/>
<table class="widefat striped fixed">
    <thead>
        <tr>
            <th colspan="2"><h4 style="margin:0; text-align:center;"><?php _e("General Information",CF7_ISMS_TXT); ?></h4></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th><?php _e("SMS ID",CF7_ISMS_TXT); ?></th>
            <td><?php echo $sms_log['smsid']; ?></td>
        </tr>
        
        <tr>
            <th><?php _e("Sent Date",CF7_ISMS_TXT); ?></th>
            <td><?php echo  date_i18n( get_option( 'date_format' ).' '.get_option( 'time_format' ),$sms_log['datetime']); ?></td>
        </tr>
        
        <tr>
            <th><?php _e("Related CF7 Form",CF7_ISMS_TXT); ?></th>
            <td> <?php $edit_link = admin_url( 'admin.php?page=wpcf7&post=' . absint($sms_log['rorder']) ) ; echo '<a href="'.$edit_link.'">#'.$sms_log['rorder'].' | '.$sms_log['form_title'].'</a>'; ?></td>
        </tr>
        
        <tr>
            <th><?php _e("SMS For",CF7_ISMS_TXT); ?></th>
            <td> <?php 
                    if(isset($sms_log['smsfor'])){
                        if($sms_log['smsfor'] == 'customer'){echo '<span class="cf7isms_badge cf7isms_customer_badge">Customer</span>';  } 
                        else if($sms_log['smsfor'] == 'admin') { echo '<span class="cf7isms_badge cf7isms_admin_badge">Admin</span>'; } 
                        else {echo '<span class="cf7isms_badge cf7isms_unknown_badge">Unknown</span>'; }    
                    } else {
                        echo '<span class="cf7isms_badge cf7isms_unknown_badge">Unknown</span>';
                    }
                ?></td>
        </tr>
        
        <tr>
            <th><?php _e("Sent By",CF7_ISMS_TXT); ?></th>
            <td><?php 
                if(is_string($sms_log['sentby'])){
                    echo $sms_log['sentby'];
                } else if(is_int($sms_log['sentby'])){
                    $user = get_user_by('id',$sms_log['sentby']);
                    if ( ! empty( $user ) ) { 
                        $link = get_edit_user_link( $sms_log['sentby'] );
                        echo '<a href="'.$link.'"> '.$user->data->display_name.' <small>('.$user->data->user_login.')</small> </a>';
                        echo '<br/> <small> ID : #'. $sms_log['sentby'].' </small> ';
                        echo '<br/> <small> Email : '.$user->data->user_email.' </small> ';
                    }
                }
                ?></td>
        </tr>
        
        
        <tr>
            <th><?php _e("SMS Trigger Reason",CF7_ISMS_TXT); ?></th>
            <td> <?php   
                    if($sms_log['smsreason'] == 'system'){
                        echo _e("Sent By System",CF7_ISMS_TXT); 
                    } else if($sms_log['smsreason'] == 'formsubmitted'){
                        echo '(#'.$sms_log['form_title'].') ';_e("Form Submitted",CF7_ISMS_TXT);
                    } else if($sms_log['smsreason'] == 'orderstatuschange'){
                        echo _e("Order Status Changed.",CF7_ISMS_TXT);
                    } else {
                        echo _e("Unknown",CF7_ISMS_TXT);
                    }
                
                ?></td>
        </tr>
        
         
    </tbody>
    
</table>

<br/>
<hr/>
<br/>


<table class="widefat striped fixed">
    <thead>
        <tr>
            <th colspan="2"><h4 style="margin:0; text-align:center;"><?php _e("SMS Information",CF7_ISMS_TXT); ?></h4></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th><?php _e("SMS To",CF7_ISMS_TXT); ?></th>
            <td><?php echo $sms_log['smsto']; ?></td>
        </tr>
        
        <tr>
            <th><?php _e("Sender ID",CF7_ISMS_TXT); ?></th>
            <td><?php echo $sms_log['senderid']; ?></td>
        </tr>
        
        <tr>
            <th><?php _e("message",CF7_ISMS_TXT); ?></th>
            <td><?php echo  $sms_log['message']; ?></td>
        </tr>
         
         
    </tbody>
    
</table>






<br/>
<hr/>
<br/>


<table class="widefat striped fixed">
    <thead>
        <tr>
            <th colspan="2"><h4 style="margin:0; text-align:center;"><?php _e("API Response",CF7_ISMS_TXT); ?></h4></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th><?php _e("API Response",CF7_ISMS_TXT); ?></th>
            <td><?php 
                
                if(is_array($sms_log['response'])){
                    $return = '<ul style="list-style:inside;">';
                    foreach($sms_log['response'] as $log){
                        if(!empty($log))
                            $return .= '<li>'.$log.'</li>';
                    }
                    echo $return;
                } else {
                    echo $sms_log['response'];     
                }
                
                
                ?></td>
        </tr>
        
         
    </tbody>
    
</table>

<br/>
<br/>