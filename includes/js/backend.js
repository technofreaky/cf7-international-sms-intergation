var http_reffer = '';
var addons_html = '';
jQuery(document).ready(function () {

    jQuery(".cf7_isms_delete_log").click(function(){
        if(jQuery(this).hasClass('clicked')){return;}
        
        var smsid = jQuery(this).attr('data-smsid');
        var action = 'cf7_isms_delete_log';
        
        jQuery(this).css('opacity','0.5');
        jQuery(this).addClass('clicked');
        var ref = jQuery(this);
        
        jQuery.ajax({
            url:ajaxurl,
            data:{action:action,smsid:smsid},
            method:"POST",
        }).done(function(resp){ 
            jQuery("#cf7_isms_log_ajax_response").remove();
            if(resp.success){
                var html = '<div id="cf7_isms_log_ajax_response" class="cf7_isms_log_ajax_response"> <span class="cf7_isms_ajax_success cf7_isms_log_success"> ';
                html += resp.data;
                html += '</span></div>';
                jQuery('table.smslogs').before(html);
                ref.parent().parent().parent().parent().fadeOut(function(){
                    jQuery(this).remove();
                })
            } else {
                var html = '<div id="cf7_isms_log_ajax_response" class="cf7_isms_log_ajax_response"> <span class="cf7_isms_ajax_error cf7_isms_log_error"> ';
                html += resp.data;
                html += '</span></div>';
                jQuery('table.smslogs').before(html);
                
                ref.css('opacity','1');
                ref.removeClass('clicked');
            }
            
            setTimeout(function(){
                jQuery("#cf7_isms_log_ajax_response").fadeOut(function(){jQuery(this).remove();})
            },3000);
        })
        
    })
    
    
    
    

    if (jQuery('div.cf7_isms_addon_listing').size() > 0) {
        jQuery('p.submit').remove();
    }

    if (jQuery('.cf7_isms_settings_submenu').size() > 0) {
        var id = window.location.hash;
        jQuery('.cf7_isms_settings_submenu a').removeClass('current');
        jQuery('.cf7_isms_settings_submenu a[href="' + id + '" ]').addClass('current');
        if (id == '') {
            jQuery('.cf7_isms_settings_submenu a:first').addClass('current');
            id = jQuery('.cf7_isms_settings_submenu a:first').attr('href');
        }
        http_reffer = jQuery('input[name=_wp_http_referer').val();

        cf7_isms_settings_showHash(id);
    }
    
    jQuery('.cf7_isms_settings_submenu a').click(function () {
        var id = jQuery(this).attr('href');
        jQuery('.cf7_isms_settings_submenu a').removeClass('current');
        jQuery(this).addClass('current');
        cf7_isms_settings_showHash(id);
        jQuery('input[name=_wp_http_referer').val(http_reffer + id)
    });

    jQuery('.cf7_isms_addon_listing').on('click', '.wc-pbp-activate-now', function () {
        cf7_isms_active_deactive_addon(jQuery(this), '.wc-pbp-deactivate-now');
    });
    
    jQuery('.cf7_isms_addon_listing').on('click', '.wc-pbp-deactivate-now', function () {
        cf7_isms_active_deactive_addon(jQuery(this), '.wc-pbp-activate-now')
    });

    addons_html = jQuery('.cf7_isms_addon_listing').clone();

    jQuery('ul.cf7_isms_addons_category li a:first').addClass('current');

    jQuery('ul.cf7_isms_addons_category li a').each(function () {
        var category = jQuery(this).attr('data-category');
        var catCount = jQuery('.wc-pbp-addon-' + category).size();
        jQuery(this).append(' <span class="catCount"> (' + catCount + ') </span>');
    });

    jQuery('ul.cf7_isms_addons_category li a').click(function () {
        var cat = jQuery(this).attr('data-category');
        var NewDis = 'div.wc-pbp-addon-' + cat;
        jQuery('ul.cf7_isms_addons_category li a').removeClass('current');
        jQuery(this).addClass('current');
        jQuery('.cf7_isms_addon_listing').html(addons_html.find(NewDis).clone());
    });

    jQuery('div.addons-search-form input.wp-filter-search').keyup(function () {
        var val = jQuery(this).val();
        var html_source = addons_html.clone();
        if (val == '') {
            jQuery('.cf7_isms_addon_listing').html(html_source);
            jQuery('.wc-pbp-addon-all').show();
        } else {
            html_source = jQuery(html_source).find(".plugin-card:contains('" + val + "')").not().remove();
            jQuery('.cf7_isms_addon_listing').html(html_source);
        }
    })

});

function cf7_isms_settings_showHash(id) {
    jQuery('div.cf7_isms_settings_content').hide();
    id = id.replace('#', '#settings_');
    jQuery(id).show();
}

function cf7_isms_active_deactive_addon(ref, oppo) {
    if (typeof (oppo) === 'undefined') oppo = '.wc-pbp-deactivate-now';
    var clicked = ref;
    var slug = ref.parent().attr('data-pluginslug');
    var parent_div = '.plugin-card-' + slug;
    var height = jQuery(parent_div).innerHeight();
    var width = jQuery(parent_div).innerWidth();
    jQuery(parent_div + ' .cf7_isms_ajax_overlay').css('height', height + 'px').css('width', width + 'px').fadeIn();
    clicked.attr('disabled', 'disable');
    var link = clicked.attr('href');
    jQuery.ajax({
        method: 'GET',
        url: link,
    }).done(function (response) {
        var status = response.success;
        jQuery(parent_div + ' .cf7_isms_ajax_overlay').fadeOut();
        clicked.removeAttr('disabled');
        if (status) {
            clicked.hide();
            jQuery(parent_div).find(oppo).fadeIn();
        }

        jQuery(parent_div).find('.cf7_isms_ajax_response').hide().html(response.data.msg).fadeIn(function () {
            setTimeout(function () {
                jQuery(parent_div).find('.cf7_isms_ajax_response').fadeOut();
            }, 5000);
        });

        jQuery.ajax({
            method: 'GET',
            url: ajaxurl + '?action=cf7_isms_get_addons_html',
        }).done(function (response) {
            addons_html = jQuery(response);
        });
    });

}