var wplc_ajaxurl = wplc_ajaxurl;
var chat_status = 3;
var cid = wplc_cid;
var wplc_poll_delay = 1500;

if (typeof wplc_action2 !== "undefined" && wplc_action2 !== "") { 

    var data = {
        action: 'wplc_admin_long_poll_chat',
        security: wplc_ajax_nonce,
        cid: cid,
        chat_status: chat_status,
        action_2: wplc_action2,
        wplc_extra_data: wplc_extra_data
    };
} else {
    var data = {
        action: 'wplc_admin_long_poll_chat',
        security: wplc_ajax_nonce,
        cid: cid,
        chat_status: chat_status,
        wplc_extra_data: wplc_extra_data
    };

}
var wplc_run = true;
var wplc_had_error = false;
var wplc_display_name = wplc_name;
var wplc_enable_ding = wplc_enable_ding;
var wplc_user_email_address = wplc_user_email;

 jQuery(document).ready(function(){
    //Parse existing data
    if(typeof niftyFormatParser !== "undefined"){
        var htmlToParse = jQuery(".admin_chat_box_inner").html();
        jQuery(".admin_chat_box_inner").html(niftyFormatParser(htmlToParse));
    }
    
});

function wplc_call_to_server_admin_chat(data) {
jQuery.ajax({
    url: wplc_ajaxurl,
    data: data,
    security: wplc_ajax_nonce,
    type: "POST",
    success: function (response) {
        wplc_poll_delay = 1500;
        if (response) {
            if (response === "0") { if (window.console) { console.log('WP Live Chat Support Return Error'); } wplc_run = false; return; }

            response = JSON.parse(response);
            

            jQuery.event.trigger({type: "wplc_admin_chat_loop",response:response});


            if (response['action'] === "wplc_ma_agant_already_answered") {
                jQuery(".end_chat_div").empty();
                jQuery('#admin_chat_box').empty().append("<h2>This chat has already been answered. Please close the chat window</h2>");
                wplc_run = false;
            }

            if (response['action'] === "wplc_update_chat_status") {
                data['chat_status'] = response['chat_status'];
                wplc_display_chat_status_update(response['chat_status'], cid);
            }
            if (response['action'] === "wplc_new_chat_message") {
                jQuery("#wplc_user_typing").fadeOut("slow").remove();
                current_len = jQuery("#admin_chat_box_area_" + cid).html().length;
                if(typeof niftyFormatParser !== "undefined"){
                    jQuery("#admin_chat_box_area_" + cid).append(niftyFormatParser(response['chat_message']));
                }else{
                    jQuery("#admin_chat_box_area_" + cid).append(response['chat_message']);
                }
                new_length = jQuery("#admin_chat_box_area_" + cid).html().length;
                if (current_len < new_length) {
                    if (typeof wplc_enable_ding !== 'undefined' && wplc_enable_ding === "1") {
                        new Audio(wplc_ding_file).play()                               
                    }
                }
                var height = jQuery('#admin_chat_box_area_' + cid)[0].scrollHeight;
                jQuery('#admin_chat_box_area_' + cid).scrollTop(height);

            }
            if (response['action'] === "wplc_user_open_chat") {
                data['action_2'] = "";
                window.location.replace(wplc_url);
            }

        }
    },
    error: function (jqXHR, exception) {
        wplc_poll_delay = 5000;
        if (jqXHR.status == 404) {
            wplc_display_error('Error: Page not found [404]');
            wplc_run = false;
        } else if (jqXHR.status == 500) {
            wplc_display_error('Error: Internal server error [500]');
            wplc_display_error('Retrying in 5 seconds...');
            wplc_run = true;
        } else if (exception === 'parsererror') {
            wplc_display_error('Error: JSON error');
            wplc_run = false;
        } else if (exception === 'abort') {
            wplc_display_error('Error: Ajax request aborted');
            wplc_run = false;
        } else {
            wplc_display_error('Error: Uncaught Error' + jqXHR.responseText);
            wplc_display_error('Retrying in 5 seconds...');
            wplc_run = true;
        }
    },
    complete: function (response) {
        if (wplc_run) {
            setTimeout(function () {
                wplc_call_to_server_admin_chat(data);
            }, wplc_poll_delay);
        }
    },
    timeout: 120000
    
    });
};
function wplc_display_error(error) {
    if (window.console) { console.log(error); }

    jQuery("#admin_chat_box_area_" + cid).append("<small>" + error + "</small><br>");
    var height = jQuery('#admin_chat_box_area_' + cid)[0].scrollHeight;
    jQuery('#admin_chat_box_area_' + cid).scrollTop(height);
}
function wplc_display_chat_status_update(new_chat_status, cid) {
if (new_chat_status === "0") {
} else {
    if (chat_status !== new_chat_status) {
        previous_chat_status = chat_status;
        chat_status = new_chat_status;

        if ((previous_chat_status === "2" && chat_status === "3") || (previous_chat_status === "5" && chat_status === "3")) {
            jQuery("#admin_chat_box_area_" + cid).append("<em>"+wplc_string1+"</em><br />");
            var height = jQuery('#admin_chat_box_area_' + cid)[0].scrollHeight;
            jQuery('#admin_chat_box_area_' + cid).scrollTop(height);

        } else if (chat_status == "10" && previous_chat_status == "3") {
            jQuery("#admin_chat_box_area_" + cid).append("<em>"+wplc_string2+"</em><br />");
            var height = jQuery('#admin_chat_box_area_' + cid)[0].scrollHeight;
            jQuery('#admin_chat_box_area_' + cid).scrollTop(height);
        }
        else if (chat_status === "3" && previous_chat_status === "10") {
            jQuery("#admin_chat_box_area_" + cid).append("<em>"+wplc_string3+"</em><br />");
            var height = jQuery('#admin_chat_box_area_' + cid)[0].scrollHeight;
            jQuery('#admin_chat_box_area_' + cid).scrollTop(height);
        }
        else if (chat_status === "1" || chat_status === "8") {
            wplc_run = false;
            jQuery("#admin_chat_box_area_" + cid).append("<em>"+wplc_string4+"</em><br />");
            var height = jQuery('#admin_chat_box_area_' + cid)[0].scrollHeight;
            jQuery('#admin_chat_box_area_' + cid).scrollTop(height);
            document.getElementById('wplc_admin_chatmsg').disabled = true;
        }
    }
}
}
var wplc_searchTimeout;
var wplc_is_typing = false;



jQuery(document).ready(function () {

    var wplc_image = admin_pic;

    jQuery("#nifty_file_input").on("change", function(){

        var file = this.files[0]; //Last file in array
        niftyShareFile(file,'#nifty_attach_fail_icon', '#nifty_attach_success_icon', '#nifty_attach_uploading_icon',  "#nifty_select_file");

    });
    


    jQuery("#wplc_admin_chatmsg").focus();


    wplc_call_to_server_admin_chat(data);
    
    if (typeof wplc_action2 !== "undefined" && wplc_action2 !== "") { return; }

    if (jQuery('#wplc_admin_cid').length) {
        var wplc_cid = jQuery("#wplc_admin_cid").val();
        var height = jQuery('#admin_chat_box_area_' + wplc_cid)[0].scrollHeight;
        jQuery('#admin_chat_box_area_' + wplc_cid).scrollTop(height);
    }






    jQuery("body").on("keydown","#wplc_admin_chatmsg", function(e) {
        if (e.which <= 90 && e.which >= 48) {
            if (wplc_is_typing) { 
                wplc_renew_typing();
                return; /* user already typing */
            }
            wplc_is_typing = true;
            
            wplc_searchTimeout = setTimeout(wplc_clear_typing, 3000);
            wplc_usertyping('admin',Math.floor(Date.now() / 1000),cid);
        }
    });

    jQuery("body").on("click", "#wplc_admin_send_msg", function() {
        if (wplc_is_typing) { wplc_clear_typing(); }
    });

    function wplc_renew_typing() {
        clearTimeout(wplc_searchTimeout);
        wplc_searchTimeout = setTimeout(wplc_clear_typing, 3000);
    }
    function wplc_clear_typing() {
        wplc_is_typing = false;
        clearTimeout(wplc_searchTimeout);
        wplc_usertyping('admin',0,cid);
    }
    function wplc_usertyping(wplc_typing_user,wplc_typing_type,wplc_typing_cid) {
        if (typeof cid !== "undefined" && cid !== null) { 
            var data = {
                    action: 'wplc_typing',
                    security: wplc_ajax_nonce,
                    user: wplc_typing_user,
                    type: wplc_typing_type,
                    cid: wplc_typing_cid,
                    wplc_extra_data:wplc_extra_data
            };
        } else {
           /* no cid? */
        }
        jQuery.post(wplc_ajaxurl, data, function(response) {

        });
    }

    


    jQuery(".wplc_admin_accept").on("click", function () {
        wplc_title_alerts3 = setTimeout(function () {
            document.title = "WP Live Chat Support";
        }, 2500);
        var cid = jQuery(this).attr("cid");

        var data = {
            action: 'wplc_admin_accept_chat',
            cid: cid,
            security: wplc_ajax_nonce
        };
        jQuery.post(wplc_ajaxurl, data, function (response) {
            wplc_refresh_chat_boxes[cid] = setInterval(function () {
                wpcl_admin_update_chat_box(cid);
            }, 3000);
            jQuery("#admin_chat_box_" + cid).show();
        });
    });

    jQuery("#wplc_admin_chatmsg").keyup(function (event) {
        if (event.keyCode == 13) {
            jQuery("#wplc_admin_send_msg").click();
        }
    });

    jQuery("#wplc_admin_close_chat").on("click", function () {
        var wplc_cid = jQuery("#wplc_admin_cid").val();
        var data = {
            action: 'wplc_admin_close_chat',
            security: wplc_ajax_nonce,
            cid: wplc_cid,
            wplc_extra_data: wplc_extra_data

        };
        jQuery.post(wplc_ajaxurl, data, function (response) {
            
            window.close();
        });

    });

    function wplc_strip(str) {
        str=str.replace(/<br>/gi, "\n");
        str=str.replace(/<p.*>/gi, "\n");
        str=str.replace(/<a.*href="(.*?)".*>(.*?)<\/a>/gi, " $2 ($1) ");
        str=str.replace(/<(?:.|\s)*?>/g, "");

        str=str.replace('iframe', "");    
        str=str.replace('src', "");    
        str=str.replace('href', "");  
        str=str.replace('<', "");  
        str=str.replace('>', "");  

        return str;
    }

    jQuery("#wplc_admin_send_msg").on("click", function () {
        var wplc_cid = jQuery("#wplc_admin_cid").val();
        var wplc_chat = wplc_strip(document.getElementById('wplc_admin_chatmsg').value);
        var wplc_name = "a" + "d" + "m" + "i" + "n";

        if(typeof wplc_name_override  !== "undefined"){
            wplc_name = wplc_name_override ;
        }
        
        jQuery("#wplc_admin_chatmsg").val('');

        /*Nifty Format Parser*/
        var wplc_chat_parsed = wplc_chat;
        if(typeof niftyFormatParser !== "undefined"){
            //PRO
            wplc_chat_parsed = niftyFormatParser(wplc_chat_parsed);
        }

        if (wplc_display_name == 'display') {
            jQuery("#admin_chat_box_area_" + wplc_cid).append("<span class='wplc-admin-message'>" + wplc_image + " <strong>" + wplc_name + "</strong>:<hr/ style='margin-bottom: 0px;'>" + wplc_chat_parsed + "</span><br /><div class='wplc-clear-float-message'></div>");
        } else {
            jQuery("#admin_chat_box_area_" + wplc_cid).append("<span class='wplc-admin-message'>" + wplc_chat_parsed + "</span><br /><div class='wplc-clear-float-message'></div>");
        }
        var height = jQuery('#admin_chat_box_area_' + wplc_cid)[0].scrollHeight;
        jQuery('#admin_chat_box_area_' + wplc_cid).scrollTop(height);


        var data = {
            action: 'wplc_admin_send_msg',
            security: wplc_ajax_nonce,
            cid: wplc_cid,
            msg: wplc_chat,
            wplc_extra_data:wplc_extra_data
        };
        jQuery.post(wplc_ajaxurl, data, function (response) {

        });


    });







});

/* Handles Uploading and sharing a file within chat*/
function niftyShareFile(fileToUpload, failedID, successID, uploadingID, originalID){
    var formData = new FormData();

    formData.append('action', 'wplc_upload_file');
    formData.append('cid', cid);
    formData.append('file', fileToUpload);
    formData.append('timestamp', Date.now());
    formData.append('security', wplc_ajax_nonce );
    
    /*Handle jQuery Elements*/
    jQuery(uploadingID).show();
    jQuery(originalID).hide();
    jQuery(successID).hide();
    jQuery(failedID).hide();

    if(fileToUpload.name.indexOf(".php") === -1 && fileToUpload.name.indexOf(".html") === -1 && fileToUpload.name.indexOf(".asp") === -1){
        //Files allowed - continue
        if(fileToUpload.size < 4000000){ //Max size of 4MB
            jQuery.ajax({
                   url : wplc_home_ajaxurl,
                   type : 'POST',
                   data : formData,
                   cache: false,
                   processData: false, 
                   contentType: false, 
                   success : function(data) {    
                       if(parseInt(data) !== 0){
                           jQuery(uploadingID).hide();
                           jQuery(successID).show();
                           setTimeout(function(){
                              jQuery(successID).hide();
                              jQuery(originalID).show(); 
                           }, 2000);

                            //All good post the link to file            
                            var tag = (data.indexOf(".png") !== -1 || data.indexOf(".PNG") !== -1 || data.indexOf(".jpg") !== -1  || data.indexOf(".JPG") !== -1 || data.indexOf(".jpeg") !== -1 || data.indexOf(".gif") !== -1 || data.indexOf(".bmp")!== -1 ) ? "img" : "link";
                           
                            if(tag !== "img"){
                                tag = (data.indexOf(".mp4") !== -1 || data.indexOf(".mpeg4") !== -1 || data.indexOf(".webm") !== -1 || data.indexOf(".oog") !== -1 ) ? "vid" : "link"; //video now
                            }
                            jQuery("#wplc_admin_chatmsg").val(tag + ":" + data + ":" + tag); //Add to input field
                            jQuery("#wplc_admin_send_msg").trigger("click"); //Send message
                       }
                       else{
                           jQuery(uploadingID).hide();
                           jQuery(failedID).show();
                           setTimeout(function(){
                              jQuery(failedID).hide();
                              jQuery(originalID).show(); 
                           }, 2000);

                       }
                   },
                   error : function (){
                        jQuery(uploadingID).hide();
                        jQuery(failedID).show();
                        setTimeout(function(){
                           jQuery(failedID).hide();
                           jQuery(originalID).show(); 
                        }, 2000);
                   }
            });
        }else{
            alert("File limit is 4mb");
            jQuery(uploadingID).hide();
            jQuery(failedID).show();
            setTimeout(function(){
               jQuery(failedID).hide();
               jQuery(originalID).show(); 
            }, 2000);
        }
    } else{
        alert("File type not supported");
        jQuery(uploadingID).hide();
        jQuery(failedID).show();
        setTimeout(function(){
           jQuery(failedID).hide();
           jQuery(originalID).show(); 
        }, 2000);
    }
}