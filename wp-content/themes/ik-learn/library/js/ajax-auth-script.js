jQuery(document).ready(function ($) {
    // Display form from link inside a popup
    /*$('#pop_login, #pop_signup').live('click', function (e) {
        formToFadeOut = $('form#register');
        formtoFadeIn = $('form#login');
        if ($(this).attr('id') == 'pop_signup') {
            formToFadeOut = $('form#login');
            formtoFadeIn = $('form#register');
        }
        formToFadeOut.fadeOut(500, function () {
            formtoFadeIn.fadeIn();
        })
        return false;
    });
    // Close popup
    $(document).on('click', ' .close', function () {
        $('form#login, form#register').fadeOut(500, function () {
        });
        return false;
    });
    $(document).mouseup(function (e)
    {
        var container = $('form#login, form#register');

        if (!container.is(e.target) && container.has(e.target).length === 0)
        {
            container.hide();
        }
    });
*/
    // Show the login/signup popup on click
    /*jQuery('#show_login, #show_signup').on('click', function (e) {
        e.preventDefault();
        if (jQuery(this).attr('id') == 'show_login') {
            jQuery('form#login').fadeIn(500);
        } else {
            jQuery('form#register').fadeIn(500);
        }
    });*/

    // Perform AJAX login/register on form submit
    $('form#login, form#register').on('submit', function (e) {
        if (!$(this).valid())
            return false;
        $('p.status', this).show().text(ajax_auth_object.loadingmessage);
        action = 'ajaxlogin';
        username = $('form#login #username').val();
        password = $('form#login #password').val();
        email = '';
        language_type = '';
        birth_m = '';
        birth_d = '';
        birth_y = '';
        last_name = '';
        first_name = '';
        security = $('form#login #security').val();
            url=ajax_auth_object.ajaxurl;
        if(window.location.href.indexOf('math')!=-1){
        url=home_url+'/wp-admin/admin-ajax.php';
        }
        if ($(this).attr('id') == 'register') {
            action = 'ajaxregister';
            username = $('#signonname').val();
            password = $('#signonpassword').val();
            email = $('#signonname').val();
            security = $('#signonsecurity').val();
            language_type = $(".language_type option:selected").val();
            birth_m = $('#birth-m').val();
            birth_d = $('#birth-d').val();
            birth_y = $('#birth-y').val();
            last_name = $('#last_name').val();
            first_name = $('#first_name').val();
        }
        ctrl = $(this);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: url,
            data: {
                'action': action,
                'username': username,
                'password': password,
                'email': email,
                'security': security,
                'language-type': language_type,
                'birth-m': birth_m,
                'birth-d': birth_d,
                'birth-y': birth_y,
                'last-name': last_name,
                'first-name': first_name
            },
            success: function (data) {
//                console.log(data);
                $('p.status', ctrl).text(data.message);
                if (data.loggedin == true) {
                        document.location.href = ajax_auth_object.redirecturl;
                }
            }
        });
        e.preventDefault();
    });

    // Client side form validation
    if (jQuery("#register").length)
        jQuery("#register").validate(
                {
                    rules: {
                        password2: {equalTo: '#signonpassword'
                        }
                    }}
        );
    else if (jQuery("#login").length)
        jQuery("#login").validate();
});