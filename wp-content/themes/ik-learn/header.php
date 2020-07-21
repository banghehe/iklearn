<?php
	$route = get_route();
	if(isset($route[1])) {
		switch($route[1]) {
			case 'elearner': $active_menu = 68;
				break;
			case 'collegiate': $active_menu = 67;
				break;
			case 'medical': $active_menu = 66;
				break;
			case 'intermediate': $active_menu = 65;
				break;
			case 'elementary': $active_menu = 64;
				break;
		}
	}

	$current_user = wp_get_current_user();
    $is_user_logged_in = is_user_logged_in();
    if($is_user_logged_in){
        $u_time_zone = get_user_meta($current_user->ID, 'user_timezone', true);
        $u_time_zone = empty($u_time_zone)? 0 : $u_time_zone;
        $u_time_zone_index = get_user_meta($current_user->ID, 'time_zone_index', true);
        $u_time_zone_index = empty($u_time_zone_index)? 0 : $u_time_zone_index;
        $u_time_zone_name = get_user_meta($current_user->ID, 'time_zone_name', true);
        $timezone_name = empty($u_time_zone_name)? convert_timezone_to_name($u_time_zone_index) : $u_time_zone_name;
    }else{
        $u_time_zone = $u_time_zone_index = 0;
        $timezone_name = convert_timezone_to_name($u_time_zone_index);
    }
    $my_timezone_index = $u_time_zone_index;
    $my_city = convert_timezone_to_location($u_time_zone_index);
    $dt = new DateTime('now', new DateTimezone($timezone_name));

    $dt_yesterday = new DateTime('now', new DateTimezone($timezone_name));
    $dt_yesterday->add(DateInterval::createFromDateString('yesterday'));

    $dt_tomorrow = new DateTime('now', new DateTimezone($timezone_name));
    $dt_tomorrow->add(DateInterval::createFromDateString('tomorrow'));

    $date = new DateTime('now', new DateTimezone(get_option( 'timezone_string' )));

	$cart_items = get_cart_items();

	$locale_code = explode('_', get_locale());

	$lang = pll_current_language() ;
    $active_day = MWDB::get_tutoring_date();
    $count_notification = MWDB::get_count_quick_notification();
    add_filter( 'the_editor', 'set_my_mce_editor_placeholder' );

    function set_my_mce_editor_placeholder( $textarea_html ){

        // Optional, check for specific post type to add this (remove // to uncomment and use)
        // if( 'my_custom_post_type' !== get_post_type() ) return $plugins;
        $placeholder = __( 'Message' );

        $textarea_html = preg_replace( '/<textarea/', "<textarea placeholder=\"{$placeholder}\"", $textarea_html );

        return $textarea_html;
    }
?>
<!DOCTYPE html>

<!--[if lt IE 7]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8 lt-ie7"><![endif]-->
<!--[if (IE 7)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8"><![endif]-->
<!--[if (IE 8)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!-->
<html <?php language_attributes(); ?> class="no-js">
<!--<![endif]-->

<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
        <?php wp_title('ddddd'); ?>
    </title>
    <meta name="MobileOptimized" content="320">
    <meta name="viewport" content="width=device-width,height=device-height, initial-scale=1" />           
    <link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/library/images/apple-touch-icon.png">
    <link rel="icon" href="<?php echo get_template_directory_uri(); ?>/favicon.png">
    <!--[if IE]>
	    <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico">
    <![endif]-->
    <meta name="msapplication-TileColor" content="#f01d4f">
    <meta name="msapplication-TileImage" content="<?php echo get_template_directory_uri(); ?>/library/images/win8-tile-icon.png">
    <meta name="theme-color" content="#121212">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

    <?php // wordpress head functions ?>
    <?php wp_head(); ?>    
    <?php // end of wordpress head ?>

    <?php if(!is_admin_panel()) : ?>
    <script src="<?php echo get_template_directory_uri(); ?>/library/js/iklearn.js"></script>
    <?php endif ?>
    <script>
        var home_url = "<?php echo local_home_url(); ?>",
            LANG_CODE = "<?php echo $locale_code[0] ?>",
            isuserloggedin = <?php echo $is_user_logged_in ? 1 : 0 ?>;
    </script>
    <?php if(is_admin_panel()) : ?>
    <style type="text/css">
        a.sign-up-link {
            pointer-events: none !important;
            cursor: default !important;

            color: #999 !important;
        }
    </style>
    <?php endif ?>

    <?php if(isset($active_menu)) : ?>
    <style type="text/css">
        #main-nav nav .main-menu li#menu-item-<?php echo $active_menu ?> a {
            color: #FFF;
        }
    </style>
    <?php endif ?>
    <script>
        var active_day = <?php echo $active_day ?>;
    </script>
</head>

<body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebPage">
    <div class="modal fade modal-signup" id="my-account-modal" role="dialog" >
        <div class="modal-dialog modal-lg modal-signup">
            <div class="modal-content modal-content-signup">
                <div class="title-div">
                    <div class="icon-close-classes-created">
                        <?php if ($is_user_logged_in) { ?>
                        <button type="button" id="btn-my-timezone" class="btn-my-schedule">
                            <span id="mycity-name"><?php echo $my_city ?></span>
                            <span id="mytime-clock" data-hour="24" data-minute="0">2:35 PM</span>
                            <img class="ic-my-schedule" src="<?php echo get_template_directory_uri(); ?>/library/images/icon_TimeZone_Selector.png">
                        </button>
                        <?php } ?>

                        <button type="button" id="menu-quick-notification">
                            <?php if($count_notification == 0){ ?>
                            <img class="ic-close7" src="<?php echo get_template_directory_uri(); ?>/library/images/07_Top_Trigger.png">
                            <?php }else{ ?>
                            <img class="ic-close7 active" src="<?php echo get_template_directory_uri(); ?>/library/images/08_Top_Trigger_NOTIFICATION.png">    
                            <?php } ?>
                        </button>

                        <ul id="open-menu-quicknotifi" style="display: none;">
                            <li>
                                <button type="button" id="quick-notification-btn">
                                    <img src="<?php echo get_template_directory_uri(); ?>/library/images/10_Menu_Notification.png">
                                    Quick Notification
                                </button>
                            </li>
                            <li>
                                <button type="button" id="btn-my-schedule">
                                    <img src="<?php echo get_template_directory_uri(); ?>/library/images/01_icon_Schedule_Starter.png">
                                    Schedule Starter
                                </button>
                            </li>
                            <li class="last">
                                <button type="button" id="close-modal">
                                    <img src="<?php echo get_template_directory_uri(); ?>/library/images/09_Menu_Closed.png">
                                    Close
                                </button>
                            </li>
                        </ul>

                        <ul id="my-timezone" style="display: none;">
                            <li data-value="" data-city="London" <?php if($timezone_index == '0' ) echo 'class="active"'; ?>>Select Time Zone</li>
                            <li class="my-timezone<?php if($my_timezone_index == '1' ) echo ' active'; ?>" data-index="1" data-value="-5" data-name="America/New_York" data-city="New York">
                                <span class="name-city" id="name-city1">New York</span>
                                <span class="name-clock" id="name-clock1"></span>
                            </li>
                            <li class="my-timezone<?php if($my_timezone_index == '2' ) echo ' active'; ?>" data-index="2" data-value="-6" data-name="America/Chicago" data-city="Minneapolis">
                                <span class="name-city" id="name-city2">Minneapolis</span>
                                <span class="name-clock" id="name-clock2"></span>
                            </li>
                            <li class="my-timezone<?php if($my_timezone_index == '3' ) echo ' active'; ?>" data-index="3" data-value="-5" data-name="America/Denver" data-city="Colorado">
                                <span class="name-city" id="name-city3">Colorado</span>
                                <span class="name-clock" id="name-clock3"></span>
                            </li>
                            <li class="my-timezone <?php if($my_timezone_index == '4' ) echo ' active'; ?>" data-index="4" data-value="-7" data-name="America/Los_Angeles" data-city="San Francisco">
                                <span class="name-city" id="name-city4">San Francisco</span>
                                <span class="name-clock" id="name-clock4"></span>
                            </li>
                            <li class="my-timezone <?php if($my_timezone_index == '5' ) echo ' active'; ?>" data-index="5" data-value="-10" data-name="Pacific/Honolulu" data-city="Hawaii">
                                <span class="name-city" id="name-city5">Hawaii</span>
                                <span class="name-clock" id="name-clock5"></span>
                            </li>
                            <li class="my-timezone<?php if($my_timezone_index == '6' ) echo ' active'; ?>" data-index="6" data-value="+10" data-name="Pacific/Guam" data-city="Guam">
                                <span class="name-city" id="name-city6">Guam</span>
                                <span class="name-clock" id="name-clock6"></span>
                            </li>
                            <li class="my-timezone" data-index="7" data-value="+9" data-name="Asia/Tokyo" data-city="Tokyo">
                                <span class="name-city" id="name-city7">Tokyo</span>
                                <span class="name-clock" id="name-clock7"></span>
                            </li>
                            <li class="my-timezone<?php if($my_timezone_index == '7' ) echo ' active'; ?>" data-index="8" data-value="+9" data-name="Asia/Seoul" data-city="Seoul" <?php if($my_timezone_index == '8' ) echo 'class="active"'; ?>>
                                <span class="name-city" id="name-city8">Seoul</span>
                                <span class="name-clock" id="name-clock8"></span>
                            </li>
                            <li class="my-timezone<?php if($my_timezone_index == '9' ) echo ' active'; ?>" data-index="9" data-value="+8" data-name="Asia/Shanghai" data-city="Beijing">
                                <span class="name-city" id="name-city9">Beijing</span>
                                <span class="name-clock" id="name-clock9"></span>
                            </li>
                            <li class="my-timezone<?php if($my_timezone_index == '10' ) echo ' active'; ?>" data-index="10" data-value="+8" data-name="Asia/Shanghai" data-city="Xianyang">
                                <span class="name-city" id="name-city10">Xianyang</span>
                                <span class="name-clock" id="name-clock10"></span>
                            </li>
                            <li class="my-timezone<?php if($my_timezone_index == '11' ) echo ' active'; ?>" data-index="11" data-value="+7" data-name="Asia/Ho_Chi_Minh" data-city="Hanoi">
                                <span class="name-city" id="name-city11">Hanoi</span>
                                <span class="name-clock" id="name-clock11"></span>
                            </li>
                            <li class="my-timezone<?php if($my_timezone_index == '12' ) echo ' active'; ?>" data-index="12" data-value="+7" data-name="Asia/Bangkok" data-city="Bangkok">
                                <span class="name-city" id="name-city12">Bangkok</span>
                                <span class="name-clock" id="name-clock12"></span>
                            </li>
                            <li class="my-timezone<?php if($my_timezone_index == '13' ) echo ' active'; ?>" data-index="13" data-value="+7" data-name="Asia/Rangoon" data-city="Myanmar">
                                <span class="name-city" id="name-city13">Myanmar</span>
                                <span class="name-clock" id="name-clock13"></span>
                            </li>
                            <li class="my-timezone <?php if($my_timezone_index == '14' ) echo ' active'; ?>" data-index="14" data-value="+6" data-name="Asia/Dhaka" data-city="Bangladesh">
                                <span class="name-city" id="name-city14">Bangladesh</span>
                                <span class="name-clock" id="name-clock14"></span>
                            </li>
                            <li class="my-timezone<?php if($my_timezone_index == '15' ) echo ' active'; ?>" data-index="15" data-value="+5" data-name="Asia/Colombo" data-city="Sri Lanka">
                                <span class="name-city" id="name-city15">Sri Lanka</span>
                                <span class="name-clock" id="name-clock15"></span>
                            </li>
                            <li class="my-timezone<?php if($my_timezone_index == '16' ) echo ' active'; ?>" data-index="16" data-value="+5" data-name="Asia/Kolkata" data-city="New Delhi">
                                <span class="name-city" id="name-city16">New Delhi</span>
                                <span class="name-clock" id="name-clock16"></span>
                            </li>
                            <li class="my-timezone<?php if($my_timezone_index == '17' ) echo ' active'; ?>" data-index="17" data-value="+5" data-name="Asia/Kolkata" data-city="Mumbai">
                                <span class="name-city" id="name-city17">Mumbai</span>
                                <span class="name-clock" id="name-clock17"></span>
                            </li>
                            <li class="my-timezone<?php if($my_timezone_index == '18' ) echo ' active'; ?>" data-index="18" data-value="0" data-name="Europe/London" data-city="London">
                                <span class="name-city" id="name-city18">London</span>
                                <span class="name-clock" id="name-clock18"></span>
                            </li>
                            <li class="my-timezone<?php if($my_timezone_index == '19' ) echo ' active'; ?>" data-index="19" data-value="+5" data-name="Australia/Sydney" data-city="Sydney">
                                <span class="name-city" id="name-city19">Sydney</span>
                                <span class="name-clock" id="name-clock19"></span>
                            </li>
                        </ul>
                    </div>
                    <img id="menu_Taggle" src="<?php echo get_template_directory_uri(); ?>/library/images/icon_Menu_Trigger.png">
                    <span class="modal-title text-uppercase">
                        <a href="#">
                            <img data-dismiss="modal" src="<?php echo get_template_directory_uri(); ?>/library/images/ikTeach_Logo.png">
                        </a>
                    </span>
                </div>
                <hr class="line-modal">
                <div class="bg-overload"></div>
                <div id="open-list-quicknotifi" style="display: none;">
                    <div class="add-list-quicknotifi">
                                
                    </div>
                </div>
                <div class="modal-body-signup">
                    <div class="section-right">
                        <div class="tab-content">
                            <!-- Login -->
                            <div id="login-user" class="style-form tab-pane fade in active">
                                <h3>Login</h3>
                                <div class="col-md-12">
                                    <form action="<?php echo locale_home_url() ?>/?r=login" name="loginform" method="post">
                                        <div class="row">
                                            <div class="row md-login-r">
                                                <div class="col-xs-12 col-sm-6 col-md-6">
                                                    <div class="form-group">
                                                        <label for="username">
                                                            <?php _e('Username (e-mail address)', 'iii-dictionary') ?>
                                                        </label>
                                                        <input type="text" class="form-control border-ras" id="username-login" name="log" value="" style="border-radius:0px;">
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-sm-6 col-md-6">
                                                    <div class="form-group">
                                                        <label for="password">
                                                            <?php _e('Password', 'iii-dictionary') ?>
                                                        </label>
                                                        <input type="password" class="form-control border-ras" id="password-login" name="pwd" value="">
                                                    </div>
                                                </div>
                                                <div class="clearfix" style="margin-bottom: 20px;"></div>
                                                <div class="col-sm-6 col-md-6">
                                                    <div class="form-group">
                                                        <button type="button" id="btn-login" class="btn-orange border-btn" name="wp-submit">
                                                            <?php _e('Login', 'iii-dictionary') ?>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 col-md-6">
                                                    <div class="form-group">
                                                        <button type="button" class="btn-cancel-grey sign-up border-btn">
                                                            <?php _e('Create Account', 'iii-dictionary') ?>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                                <div class="forgot-pass">
                                                    <div class="form-group forgot-password-form">
                                                        <a class="forgot-password-a lblForgot">
                                                            <?php _e('Forgot password?', 'iii-dictionary') ?>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                        <input name="redirect_to" value="<?php echo locale_home_url() ?>" type="hidden">
                                    </form>
                                </div>
                            </div>
                            <!-- Login -->

                            <!-- Lost Password -->
                            <div id="lost-password" class="hidden style-form tab-pane fade in">
                                <h3>Lost Password</h3>
                                <div class="">
                                    <form name="lostpasswordform" id="lostpassword-form" action="<?php echo esc_url(network_site_url('?r=login&action=forgotpassword')); ?>" method="post">
                                        <div class="row" style="margin-bottom: 20px;">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="user_login">
                                                        <?php _e('Email Address for Receiving a New Password', 'iii-dictionary') ?>
                                                    </label>

                                                    <input type="text" name="user_login" id="user_login_password" class="form-control border-ras" value="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <button type="submit" name="wp-submit" class="btn-orange border-btn">
                                                        <?php esc_attr_e('Receive New Password', 'iii-dictionary') ?>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <button type="button" name="wp-submit" name="cancel" class="btn-cancel-grey border-btn close-modal-account">
                                                        <?php _e('Cancel', 'iii-dictionary') ?>
                                                    </button>
                                                </div>

                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!-- Lost Password -->
                            <!-- Create Basic Account -->
                            <div id="create-account" class="tab-pane fade">
                                <h3>Create Basic Account <span id="create-overview">overview</span></h3>
                                <form method="post" id="createAccount" action="" name="registerform" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-sm-10 col-md-10 refreshclass">
                                            <div class="form-group">
                                                <input id="user_login_signup" class="form-control" name="user_login" type="text" value="" required>
                                                <span class="placeholder"><?php _e('Email Address', 'iii-dictionary') ?>:</span>
                                                <span id="checked-availability" class="not-check-available"><span></span></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-2 col-md-2">
                                            <button class="btn-dark-blue border-btn check-availability" id="check-availability" style="background: #FFA523;" type="button" name="wp-submit">Availability</button>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-sm-5 col-md-5 mt-top-14 refreshclass">
                                            <div class="form-group">
                                                <input id="user_password_signup" class="form-control border-ras" name="user_password" type="text" value="" required>
                                                <span class="placeholder"><?php _e('Password', 'iii-dictionary') ?>:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-5 col-md-5 mt-top-14 refreshclass">
                                            <div class="form-group">
                                                <input id="confirm_password" class="form-control border-ras" name="confirm_password" type="text" value="" required>
                                                <span class="placeholder"><?php _e('Confirm Password', 'iii-dictionary') ?>:</span>
                                            </div>
                                        </div>

                                        <div class="col-sm-2 col-md-2 mt-top-14 gender-pc">
                                            <div id="gender-pc">
                                                <div class="form-group">
                                                    <div class="border-ras select-style" id="gender">
                                                        <select id="birth_g_pc" class="select-box-it form-control" name="birth_g_pc">
                                                            <option value="">Gender</option>
                                                            <option value="Male">Male</option>
                                                            <option value="Female">Female</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-sm-6 col-md-6 mt-top-14 refreshclass">
                                            <div class="form-group">
                                                <input id="first_name_signup" class="form-control" name="first_name" type="text" value="" required>
                                                <span class="placeholder"><?php _e('First Name', 'iii-dictionary') ?>:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-6 mt-top-14 refreshclass">
                                            <div class="form-group">
                                                <input id="last_name_signup" class="form-control" name="last_name" type="text" value="" required>
                                                <span class="placeholder"><?php _e('Last Name', 'iii-dictionary') ?>:</span>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-sm-12 col-md-12 mt-top-9">
                                            <div class="form-group">
                                                <label class="create-label mt-bottom-11">
                                                    <?php _e('Date of Birth', 'iii-dictionary') ?>
                                                </label>
                                                <div class="row tiny-gutter">
                                                    <div class="col-xs-12 col-sm-4 col-md-4 border-ras select-style" id="month">
                                                        <select id="birth_m" class="select-box-it form-control" name="birth-m">
                                                            <option value="">(Month)</option>
                                                            <?php for ($i = 1; $i <= 12; $i++) : ?>
                                                                <?php $pad_str = str_pad($i, 2, '0', STR_PAD_LEFT) ?>
                                                                    <option value="<?php echo $pad_str ?>">
                                                                        <?php echo $pad_str ?>
                                                                    </option>
                                                                    <?php endfor ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-4 col-md-4 border-ras select-style" id="date">
                                                        <select id="birth_d" class="select-box-it form-control" name="birth-d">
                                                            <option value="">(Day)</option>
                                                            <?php for ($i = 1; $i <= 31; $i++) : ?>
                                                                <?php $pad_str = str_pad($i, 2, '0', STR_PAD_LEFT) ?>
                                                                    <option value="<?php echo $pad_str ?>">
                                                                        <?php echo $pad_str ?>
                                                                    </option>
                                                                    <?php endfor ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-4 col-md-4 refreshclass year-mb">
                                                        <input id="birth_y" class="form-control" name="birth-y" type="text" value="" required>
                                                        <span class="placeholder"><?php _e('Year', 'iii-dictionary') ?>:</span>
                                                    </div>

                                                    <div class="col-xs-12 col-sm-4 col-md-4 gender-mb">
                                                        <div id="gender-mb">
                                                            <div class="form-group">
                                                                <div class="border-ras select-style" id="gender">
                                                                    <select id="birth-g_mb" class="select-box-it form-control" name="birth-g_mb">
                                                                        <option value="">Gender</option>
                                                                        <option value="Male">Male</option>
                                                                        <option value="Female">Female</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-sm-12 col-md-12 refreshclass">
                                            <label class="create-label mt-top-10">
                                                <?php _e('Language', 'iii-dictionary') ?>
                                            </label>
                                            <div class="form__boolean mt-bottom-10 clearfix" id="checkBoxSearch" style="margin-top: 0">
                                                <div class="col-md-2 col-xs-4 cb-type2">
                                                    <label>
                                                        <input type="checkbox" class="radio_buttons class_cb_search option-input-2 radio" value="en" data-lang="en" name="cb-lang" /> English
                                                    </label>
                                                </div>
                                                <div class="col-md-2 col-xs-4 cb-type2">
                                                    <label>
                                                        <input type="checkbox" class="radio_buttons class_cb_search option-input-2 radio" value="ja" data-lang="ja" name="cb-lang" /> Japanese
                                                    </label>
                                                </div>
                                                <div class="col-md-2 col-xs-4 cb-type2">
                                                    <label>
                                                        <input type="checkbox" class="radio_buttons class_cb_search option-input-2 radio" value="ko" data-lang="ko" name="cb-lang" /> Korean
                                                    </label>
                                                </div>
                                                <div class="col-md-2 col-xs-4 cb-type2">
                                                    <label>
                                                        <input type="checkbox" class="radio_buttons class_cb_search option-input-2 radio" value="zh" data-lang="zh" name="cb-lang" /> Chinese
                                                    </label>
                                                </div>
                                                <div class="col-md-2 col-xs-4 cb-type2">
                                                    <label>
                                                        <input type="checkbox" class="radio_buttons class_cb_search option-input-2 radio" value="zh-tw" data-lang="zh-tw" name="cb-lang" /> Traditional Chinese
                                                    </label>
                                                </div>
                                                <div class="col-md-2 col-xs-4 cb-type2">
                                                    <label>
                                                        <input type="checkbox" class="radio_buttons class_cb_search option-input-2 radio" value="vi" data-lang="vi" name="cb-lang" /> Vietnamese
                                                    </label>
                                                </div>
                                                <div class="col-md-2 col-xs-4 cb-type2">
                                                    <label>
                                                        <input type="checkbox" class="radio_buttons class_cb_search option-input-2 radio" value="ot" data-lang="ot" name="cb-lang" /> Others
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-12 col-md-12 profile-pic refreshclass mt-top-14" style="clear: both;">
                                            <label class="create-label img-profile">Profile Picture (optional)</label>
                                            <div class="row">
                                                <div class="col-sm-4 col-md-4 mt-top-9">
                                                    <div class="form-group">
                                                        <img id="user-upload-avatar" src="<?php echo get_template_directory_uri(); ?>/library/images/Icon_Image_Person.png" alt="Profile Picture" style="display: inline-block; margin-right: 14px;">
                                                        <input class="form-control input-file" type="file" id="input-avatar" value="">
                                                        <button class="btn-dark-blue border-btn" style="background: #cecece; display: inline-block; width: 82%" type="button" name="upload" onclick="document.getElementById('input-avatar').click();">
                                                            <?php _e('Browse', 'iii-dictionary') ?>
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="col-sm-8 col-md-8 mt-top-9">
                                                    <div class="form-group">
                                                        <input class="form-control input-path" id="profile-avatar" type="text">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-sm-6 col-md-6 col-xs-6 mt-top-mb-12">
                                            <label class="create-label mt-top-10">
                                                <?php _e('My Time Zone', 'iii-dictionary') ?>
                                            </label>
                                            <div class="form-group border-ras select-style user-timezone mt-top-8">
                                                <select class="select-box-it form-control" name="time_zone" id="user-time-zone">
                                                    <option value="0" data-value="0" data-name="Europe/London" data-city="London">Select Time Zone</option>
                                                    <option value="1" data-value="-5" data-city="New York" data-name="America/New_York">New York</option>
                                                    <option value="2" data-value="-6" data-city="Minneapolis" data-name="America/Chicago">Minneapolis</option>
                                                    <option value="3" data-value="-5" data-city="Colorado" data-name="America/Denver">Colorado</option>
                                                    <option value="4" data-value="-7" data-city="San Francisco" data-name="America/Los_Angeles">San Francisco</option>
                                                    <option value="5" data-value="-10" data-city="Hawaii" data-name="Pacific/Honolulu">Hawaii</option>
                                                    <option value="6" data-value="+10" data-city="Guam" data-name="Pacific/Guam">Guam</option>
                                                    <option value="7" data-value="+9" data-city="Tokyo" data-name="Asia/Tokyo">Tokyo</option>
                                                    <option value="8" data-value="+9" data-city="Seoul" data-name="Asia/Seoul">Seoul</option>
                                                    <option value="9" data-value="+8" data-city="Beijing" data-name="Asia/Shanghai">Beijing</option>
                                                    <option value="10" data-value="+8" data-city="Xianyang" data-name="Asia/Shanghai">Xianyang</option>
                                                    <option value="11" data-value="+7" data-city="Hanoi" data-name="Asia/Ho_Chi_Minh">Hanoi</option>
                                                    <option value="12" data-value="+7" data-city="Bangkok" data-name="Asia/Bangkok">Bangkok</option>
                                                    <option value="13" data-value="+7" data-city="Myanmar" data-name="Asia/Rangoon">Myanmar</option>
                                                    <option value="14" data-value="+6" data-city="Bangladesh" data-name="Asia/Dhaka">Bangladesh</option>
                                                    <option value="15" data-value="+5" data-city="Sri Lanka" data-name="Asia/Colombo">Sri Lanka</option>
                                                    <option value="16" data-value="+5" data-city="New Delhi" data-name="Asia/Kolkata">New Delhi</option>
                                                    <option value="17" data-value="+5" data-city="Mumbai" data-name="Asia/Kolkata">Mumbai</option>
                                                    <option value="18" data-value="0" data-city="London" data-name="Europe/London">London</option>
                                                    <option value="19" data-value="+5" data-city="Sydney" data-name="Australia/Sydney">Sydney</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-xs-12 col-sm-6 col-md-6 mt-bottom-24">
                                            <div class="form-group">
                                                <button class="btn-dark-blue border-btn" id="create-acc" style="background: #65C762; margin-top: 20px;" type="button" name="wp-submit">
                                                    <?php _e('Create Account', 'iii-dictionary') ?>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 col-md-6 mt-bottom-24">
                                            <div class="form-group">
                                                <button class="btn-dark-blue cancel-btn border-btn close-modal-account" style="background: #CECECE; margin-top: 20px !important;" type="button" name="cancel">
                                                    <?php _e('Cancel', 'iii-dictionary') ?>
                                                </button>

                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </form>
                            </div>
                            <!-- Create Basic Account -->  

                            <!-- Profile -->
                            <div id="profile" class="tab-pane fade">
                                <h3>Profile</h3>
                                <form method="post" id="myProfile" action="" name="registerform" enctype="multipart/form-data">
                                    <div class="row profile-pic">
                                        <div class="col-sm-1 col-md-1">
                                            <div class="form-group">
                                                <?php
                                                $user_avatar = ik_get_user_avatar($current_user->ID);

                                                if (!empty($user_avatar)) :
                                                    ?>
                                                <img id="profile-user-avatar" src="<?php echo $user_avatar ?>" alt="<?php echo $current_user->display_name ?>">
                                                <?php
                                                else :
                                                    ?>
                                                    <img id="profile-user-avatar" src="<?php echo get_template_directory_uri(); ?>/library/images/Profile_Image.png" alt="Profile Picture">
                                                <?php
                                                endif
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-10 col-md-10">
                                            <div class="form-group">
                                                <label>My Name</label>
                                                <span class="color-black" id="profile-my-name"><?php
                                                    if ($is_user_logged_in) {
                                                        $display_name = get_user_meta($current_user->ID, 'display_name', true);
                                                        if (!empty($display_name) && $display_name != '')
                                                            echo $display_name;
                                                        else
                                                            _e('N/A', 'iii-dictionary');
                                                    } else
                                                        _e('N/A', 'iii-dictionary');
                                                    ?>
                                                </span>
                                            </div>
                                        </div>
                                        <hr>
                                    </div>
                                    <div class="row line-profile">
                                        <div class="col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label>
                                                    <?php _e('Points Balance', 'iii-dictionary') ?>
                                                </label>
                                                <span class="color-yellow" id="profile-point-balance"><?php
                                                    if ($is_user_logged_in)
                                                        _e(ik_get_user_points($current_user->ID));
                                                    else
                                                        _e('N/A', 'iii-dictionary');
                                                    ?> (USD)
                                                </span>
                                            </div>
                                            <hr>
                                        </div>
                                        <div class="col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label>
                                                    <?php _e('Points Earned', 'iii-dictionary') ?>
                                                </label>
                                                <span class="color-yellow" id="profile-point-earned"><?php
                                                    if ($is_user_logged_in)
                                                        _e(ik_get_user_earned($current_user->ID));
                                                    else
                                                        _e('N/A', 'iii-dictionary');
                                                    ?> (USD)
                                                </span>
                                            </div>
                                            <hr>
                                        </div>
                                    </div>
                                    <div class="row line-profile">
                                        <div class="col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label>
                                                    <?php _e('Email Address (for login)', 'iii-dictionary') ?>
                                                </label>
                                                <span class="color-black" id="profile-user-email"><?php
                                                    if ($is_user_logged_in)
                                                        echo $current_user->user_email;
                                                    else
                                                        _e('N/A', 'iii-dictionary');
                                                    ?>                        
                                                </span>
                                            </div>
                                            <hr>
                                        </div>
                                        <div class="col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label>
                                                    <?php _e('Date of Birth (month/date/year)', 'iii-dictionary') ?>
                                                </label>
                                                <span class="color-black" id="profile-date-birth">
                                                    <?php
                                                    if ($is_user_logged_in) {
                                                        $date_of_birth = get_user_meta($current_user->ID, 'date_of_birth', true);
                                                        if (!empty($date_of_birth) && $date_of_birth != '')
                                                            echo $date_of_birth;
                                                        else
                                                            _e('N/A', 'iii-dictionary');
                                                    }else {
                                                        _e('N/A', 'iii-dictionary');
                                                    }
                                                    ?>
                                                </span>
                                            </div>
                                            <hr>
                                        </div>

                                    </div>
                                    <div class="row line-profile">

                                        <div class="col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label>
                                                    <?php _e('Language', 'iii-dictionary') ?>
                                                </label>
                                                <span class="color-black" id="profile-language">
                                                    <?php
                                                    if ($is_user_logged_in) {
                                                        $language_type = get_user_meta($current_user->ID, 'language_type', true);
                                                        if (!empty($language_type) && $language_type != '') {

                                                            $langs = array(
                                                                'en' => 'English',
                                                                'ja' => '日本語',
                                                                'ko' => '한국어',                                    
                                                                'zh' => '中文',
                                                                'zh-tw' => '中國',
                                                                'vi' => 'Tiếng Việt',
                                                                'ot' => 'Others'
                                                            );
                                                            $languages_t = explode(',', $language_type);
                                                            if (count($languages_t) > 0) {
                                                                $n = count($languages_t) - 1;
                                                                for ($i = 0; $i < count($languages_t); $i++) {
                                                                    $key = $languages_t[$i];
                                                                    echo $langs[$key];
                                                                    if (count($languages_t) > 1 && $i < $n)
                                                                        echo ', ';
                                                                }
                                                            }
                                                        } else
                                                            _e('N/A', 'iii-dictionary');
                                                    }else {
                                                        _e('N/A', 'iii-dictionary');
                                                    }
                                                    ?>                                               
                                                </span>
                                            </div>
                                            <hr>
                                        </div>
                                        <div class="col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label>
                                                    <?php _e('Mobile Phone Number', 'iii-dictionary') ?>
                                                </label>
                                                <span class="color-black" id="profile-mobile-phone">
                                                    <?php
                                                    if ($is_user_logged_in) {
                                                        $mobile_number = get_user_meta($current_user->ID, 'mobile_number', true);
                                                        if (!empty($mobile_number) && $mobile_number != '')
                                                            echo $mobile_number;
                                                        else
                                                            _e('N/A', 'iii-dictionary');
                                                    }else {
                                                        _e('N/A', 'iii-dictionary');
                                                    }
                                                    ?>
                                                </span>
                                            </div>
                                            <hr>
                                        </div>
                                    </div>
                                    <div class="row line-profile">
                                        <div class="col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label>
                                                    <?php _e('Last School Attended', 'iii-dictionary') ?>
                                                </label>
                                                <span class="color-black" id="profile-last-attended">
                                                    <?php
                                                    if ($is_user_logged_in) {
                                                        $last_school = get_user_meta($current_user->ID, 'last_school', true);
                                                        if (!empty($last_school) && $last_school != '')
                                                            echo $last_school;
                                                        else
                                                            _e('N/A', 'iii-dictionary');
                                                    }else {
                                                        _e('N/A', 'iii-dictionary');
                                                    }
                                                    ?>
                                                </span>
                                            </div>
                                            <hr>
                                        </div>
                                        <div class="col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label>
                                                    <?php _e('Skype ID', 'iii-dictionary') ?>
                                                </label>
                                                <span class="color-black" id="profile-skype-id">
                                                    <?php
                                                    if ($is_user_logged_in) {
                                                        $skype_id = get_user_meta($current_user->ID, 'skype_id', true);
                                                        if (!empty($skype_id) && $skype_id != '')
                                                            echo $skype_id;
                                                        else
                                                            _e('N/A', 'iii-dictionary');
                                                    }else {
                                                        _e('N/A', 'iii-dictionary');
                                                    }
                                                    ?>
                                                </span>
                                            </div>
                                            <hr>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!-- Profile --> 

                            <!-- Update My Account -->
                            <div id="updateinfo" class="tab-pane fade">
                                <?php
                                    $update_username = '';
                                    $update_user_password = '';
                                    $update_birth_g = '';
                                    $update_first_name = '';
                                    $update_last_name = '';
                                    $update_birth_m = '';
                                    $update_birth_d = '';
                                    $update_birth_y = '';
                                    $update_language = array();
                                    $profile_value = '';
                                    $update_mobile_number = '';
                                    $update_profession = '';
                                    $update_last_school = '';
                                    $update_previous_school = '';
                                    $update_skype = '';
                                    $desc_tell_update = '';
                                    $subject_type_update = array();
                                    $update_school_name = '';
                                    $update_teaching_link = '';
                                    $update_teaching_subject = '';
                                    $update_years = '';
                                    $update_school_attend = '';
                                    $update_gpa = '';
                                    $update_grade = '';
                                    $update_major = '';
                                    $update_school_name1 = '';
                                    $update_school_name2 = '';
                                    $update_school_link1 = '';
                                    $update_school_link2 = '';
                                    $update_any_other = '';
                                    $update_description = '';
                                    $update_student_link = '';
                                    $time_zone = '';
                                    $time_zone_index = '';
                                    if ($is_user_logged_in) {
                                        $update_username = $current_user->user_email;
                                        $update_first_name = get_user_meta($current_user->ID, 'first_name', true);
                                        $update_last_name = get_user_meta($current_user->ID, 'last_name', true);
                                        $update_user_password = get_user_meta($current_user->ID, 'user_password', true);
                                        $update_birth_g = get_user_meta($current_user->ID, 'gender', true);
                                        $date_of_birth = get_user_meta($current_user->ID, 'date_of_birth', true);
                                        $time_zone = get_user_meta($current_user->ID, 'user_timezone', true);
                                        $time_zone_index = get_user_meta($current_user->ID, 'time_zone_index', true);
                                        if($date_of_birth != ''){
                                        $arr_birth = explode('/', $date_of_birth);
                                        $update_birth_m = isset($arr_birth[0])?$arr_birth[0]:'';
                                        $update_birth_d = isset($arr_birth[1])?$arr_birth[1]:'';
                                        $update_birth_y = isset($arr_birth[2])?$arr_birth[2]:'';
                                        }
                                        $language_type = get_user_meta($current_user->ID, 'language_type', true);
                                        if($language_type != '') $update_language = explode(',', $language_type);

                                        $profile_value = get_user_meta($current_user->ID, 'ik_user_avatar', true);

                                        $update_mobile_number = get_user_meta($current_user->ID, 'mobile_number', true);
                                        $update_profession = get_user_meta($current_user->ID, 'user_profession', true);
                                        $update_last_school = get_user_meta($current_user->ID, 'last_school', true);
                                        $update_previous_school = get_user_meta($current_user->ID, 'previous_school', true);
                                        $update_skype = get_user_meta($current_user->ID, 'skype_id', true);
                                        $desc_tell_update = get_user_meta($current_user->ID, 'desc_tell_me', true);
                                        $subject_type = get_user_meta($current_user->ID, 'subject_type', true);
                                        if($subject_type != '') $subject_type_update = explode(',', $subject_type);
                                        $update_school_name = get_user_meta($current_user->ID, 'school_name', true);
                                        $update_teaching_link = get_user_meta($current_user->ID, 'teaching_link', true);
                                        $update_teaching_subject = get_user_meta($current_user->ID, 'teaching_subject', true);
                                        $update_student_link = get_user_meta($current_user->ID, 'student_link', true);
                                        $update_years = get_user_meta($current_user->ID, 'user_years', true);
                                        $update_school_attend = get_user_meta($current_user->ID, 'school_attend', true);
                                        $update_gpa = get_user_meta($current_user->ID, 'user_gpa', true);
                                        $update_grade = get_user_meta($current_user->ID, 'user_grade', true);
                                        $update_major = get_user_meta($current_user->ID, 'user_major', true);
                                        $update_school_name1 = get_user_meta($current_user->ID, 'school_name1', true);
                                        $update_school_name2 = get_user_meta($current_user->ID, 'school_name2', true);
                                        $update_school_link1 = get_user_meta($current_user->ID, 'school_link1', true);
                                        $update_school_link2 = get_user_meta($current_user->ID, 'school_link2', true);
                                        $update_any_other = get_user_meta($current_user->ID, 'any_other', true);
                                        $update_description = get_user_meta($current_user->ID, 'subject_description', true);
                                    }
                                ?>
                                <h3>Update My Account</h3>
                                <form method="post" id="myUpdate" action="" name="updateAccount" enctype="multipart/form-data">
                                    <h4>Basic Account Info:</h4>
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12">
                                            <div class="form-group">
                                                <input id="update_username" class="form-control" name="update_username" type="text" value="<?php echo $update_username ?>" readonly="">
                                                <span class="placeholder"><?php _e('Email Address', 'iii-dictionary') ?>:</span>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-sm-5 col-md-5 mt-top-14">
                                            <div class="form-group">
                                                <input id="update_password" class="form-control border-ras" name="update_password" type="text" value="<?php echo $update_user_password ?>" required>
                                                <span class="placeholder"><?php _e('Password', 'iii-dictionary') ?>:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-5 col-md-5 mt-top-14 mt-top-mb-24">
                                            <div class="form-group">
                                                <input id="update_confirmpass" class="form-control border-ras" name="update_confirmpass" type="text" value="<?php echo $update_user_password ?>" required>
                                                <span class="placeholder"><?php _e('Confirm Password', 'iii-dictionary') ?>:</span>
                                            </div>
                                        </div>

                                        <div class="col-sm-2 col-md-2 mt-top-14 gender-pc">
                                            <div id="update-gender-pc">                                                
                                                <div class="form-group">
                                                    <div class="border-ras select-style" id="gender_up">
                                                        <?php if($update_birth_g != ''){ ?>
                                                        <input type="text" class="form-control" name="update_birth_g_pc" value="<?php echo $update_birth_g; ?>" id="update_birth_g_pc" readonly="">
                                                        <?php }else{ ?>
                                                        <select id="update_birth_g_pc" class="select-box-it form-control" name="update_birth_g_pc">
                                                            <option value="">Gender</option>
                                                            <option value="Male">Male</option>
                                                            <option value="Female">Female</option>
                                                        </select>
                                                        <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-sm-6 col-md-6 mt-top-14">
                                            <div class="form-group">
                                                <input id="update_first_name" class="form-control" name="update_first_name" type="text" value="<?php echo $update_first_name ?>" required>
                                                <span class="placeholder"><?php _e('First Name', 'iii-dictionary') ?>:</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-6 mt-top-14 mt-top-mb-24">
                                            <div class="form-group">
                                                <input id="update_last_name" class="form-control" name="update_last_name" type="text" value="<?php echo $update_last_name ?>" required>
                                                <span class="placeholder"><?php _e('Last Name', 'iii-dictionary') ?>:</span>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-sm-12 col-md-12 mt-top-9">
                                            <div class="form-group">
                                                <label class="create-label mt-bottom-11">
                                                    <?php _e('Date of Birth', 'iii-dictionary') ?>
                                                </label>
                                                <div class="row tiny-gutter">
                                                    <div class="col-xs-12 col-sm-4 col-md-4 border-ras select-style" id="update_month">
                                                        <select id="update_birth_m" class="select-box-it form-control" name="update-birth-m">
                                                            <option value="">(Month)</option>
                                                            <?php 
                                                            for ($i = 1; $i <= 12; $i++) : 
                                                                $pad_str = str_pad($i, 2, '0', STR_PAD_LEFT);
                                                                if($pad_str == $update_birth_m)
                                                                    $sel_um = 'selected="selected"';
                                                                else
                                                                    $sel_um = ''; 
                                                            ?>
                                                            <option value="<?php echo $pad_str ?>" <?php echo $sel_um ?>>
                                                                <?php echo $pad_str ?>
                                                            </option>
                                                            <?php endfor ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-4 col-md-4 border-ras select-style" id="update_date">
                                                        <select id="update_birth_d" class="select-box-it form-control" name="update-birth-d">
                                                            <option value="">(Day)</option>
                                                            <?php 
                                                            for ($i = 1; $i <= 31; $i++): 
                                                                $pad_str = str_pad($i, 2, '0', STR_PAD_LEFT);
                                                                if($pad_str == $update_birth_d)
                                                                    $sel_ud = 'selected="selected"';
                                                                else
                                                                    $sel_ud = ''; 
                                                            ?>
                                                            <option value="<?php echo $pad_str ?>" <?php echo $sel_ud ?>>
                                                                <?php echo $pad_str ?>
                                                            </option>
                                                            <?php endfor ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-4 col-md-4 year-mb">
                                                        <input id="update_birth_y" class="form-control" name="update-birth-y" type="text" value="<?php echo $update_birth_y ?>" required>
                                                        <span class="placeholder"><?php _e('Year', 'iii-dictionary') ?>:</span>
                                                    </div>

                                                    <div class="col-xs-12 col-sm-4 col-md-4 gender-mb">
                                                        <div id="update-gender-mb">
                                                            <div class="form-group">
                                                                <div class="border-ras select-style" id="update_gender">
                                                                    <?php if($update_birth_g != ''){ ?>
                                                                    <input readonly="" type="text" name="update_birth_g_mb" class="form-control" value="<?php echo $update_birth_g; ?>" id="update_birth_g_mb">
                                                                    <?php }else{ ?>
                                                                    <select id="update_birth_g_mb" class="select-box-it form-control" name="update_birth_g_mb">
                                                                        <option value="">Gender</option>
                                                                        <option value="Male">Male</option>
                                                                        <option value="Female">Female</option>
                                                                    </select>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-sm-12 col-md-12">
                                            <label class="create-label mt-top-10">
                                                <?php _e('Language', 'iii-dictionary') ?>
                                            </label>
                                            <div class="form__boolean mt-bottom-10 clearfix" id="checkBoxSearch" style="margin-top: 0">
                                                <div class="col-md-2 col-xs-4 cb-type2">
                                                    <label>
                                                        <input type="checkbox" class="radio_buttons option-input-2 radio" value="en" <?php if(count($update_language)> 0 && in_array("en", $update_language)) echo 'checked="checked"'; ?> name="update-cb-lang"/> English
                                                    </label>
                                                </div>
                                                <div class="col-md-2 col-xs-4 cb-type2">
                                                    <label>
                                                        <input type="checkbox" class="radio_buttons option-input-2 radio" value="ja" <?php if(count($update_language)> 0 && in_array("ja", $update_language)) echo 'checked="checked"'; ?> name="update-cb-lang"/> Japanese
                                                    </label>
                                                </div>
                                                <div class="col-md-2 col-xs-4 cb-type2">
                                                    <label>
                                                        <input type="checkbox" class="radio_buttons option-input-2 radio" value="ko" <?php if(count($update_language)> 0 && in_array("ko", $update_language)) echo 'checked="checked"'; ?> name="update-cb-lang"/> Korean
                                                    </label>
                                                </div>
                                                <div class="col-md-2 col-xs-4 cb-type2">
                                                    <label>
                                                        <input type="checkbox" class="radio_buttons option-input-2 radio" value="zh" <?php if(count($update_language)> 0 && in_array("zh", $update_language)) echo 'checked="checked"'; ?> name="update-cb-lang"/> Chinese
                                                    </label>
                                                </div>
                                                <div class="col-md-2 col-xs-4 cb-type2">
                                                    <label>
                                                        <input type="checkbox" class="radio_buttons option-input-2 radio" value="zh-tw" <?php if(count($update_language)> 0 && in_array("zh-tw", $update_language)) echo 'checked="checked"'; ?> name="update-cb-lang"/> Traditional Chinese
                                                    </label>
                                                </div>
                                                <div class="col-md-2 col-xs-4 cb-type2">
                                                    <label>
                                                        <input type="checkbox" class="radio_buttons option-input-2 radio" value="vi" <?php if(count($update_language)> 0 && in_array("vi", $update_language)) echo 'checked="checked"'; ?> name="update-cb-lang"/> Vietnamese
                                                    </label>
                                                </div>
                                                <div class="col-md-2 col-xs-4 cb-type2">
                                                    <label>
                                                        <input type="checkbox" class="radio_buttons option-input-2 radio" value="ot" <?php if(count($update_language)> 0 && in_array("ot", $update_language)) echo 'checked="checked"'; ?> name="update-cb-lang"/> Others
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-12 col-md-12 profile-pic mt-top-14" style="clear: both;">
                                            <label class="create-label img-profile">Profile Picture (optional)</label>
                                            <div class="row">
                                                <div class="col-sm-4 col-md-4 mt-top-9">
                                                    <div class="form-group">
                                                        <img id="user-upload-img" src="<?php echo get_template_directory_uri(); ?>/library/images/Icon_Image_Person.png" alt="Profile Picture" style="display: inline-block; margin-right: 14px;">
                                                        <input class="form-control input-file" type="file" id="input-image" value="">
                                                        <button class="btn-dark-blue border-btn" style="background: #cecece; display: inline-block; width: 82%" type="button" name="upload" onclick="document.getElementById('input-image').click();">
                                                            <?php _e('Browse', 'iii-dictionary') ?>
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="col-sm-8 col-md-8 mt-top-9">
                                                    <div class="form-group">
                                                        <input class="form-control input-path" id="profile-value" type="text" value="<?php echo $profile_value ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-sm-6 col-md-6 col-xs-6 mt-top-mb-12">
                                            <label class="create-label mt-top-10">
                                                <?php _e('My Time Zone', 'iii-dictionary') ?>
                                            </label>
                                            <div class="form-group border-ras select-style user-timezone mt-top-8">
                                                <select class="select-box-it form-control" name="time_zone" id="update-time-zone">
                                                    <option value="0" data-value="0" data-name="Europe/London" data-city="London" <?php if($time_zone_index == '0' ) echo 'selected="selected"'; ?>>Select Time Zone</option>
                                                    <option value="1" data-value="-5" data-city="New York" data-name="America/New_York" <?php if($time_zone_index == '1' ) echo 'selected="selected"'; ?>>New York</option>
                                                    <option value="2" data-value="-6" data-city="Minneapolis" data-name="America/Chicago" <?php if($time_zone_index == '2' ) echo 'selected="selected"'; ?>>Minneapolis</option>
                                                    <option value="3" data-value="-5" data-city="Colorado" data-name="America/Denver" <?php if($time_zone_index == '3' ) echo 'selected="selected"'; ?>>Colorado</option>
                                                    <option value="4" data-value="-7" data-city="San Francisco" data-name="America/Los_Angeles" <?php if($time_zone_index == '4' ) echo 'selected="selected"'; ?>>San Francisco</option>
                                                    <option value="5" data-value="-10" data-city="Hawaii" data-name="Pacific/Honolulu" <?php if($time_zone_index == '5' ) echo 'selected="selected"'; ?>>Hawaii</option>
                                                    <option value="6" data-value="+10" data-city="Guam" data-name="Pacific/Guam" <?php if($time_zone_index == '6' ) echo 'selected="selected"'; ?>>Guam</option>
                                                    <option value="7" data-value="+9" data-city="Tokyo" data-name="Asia/Tokyo" <?php if($time_zone_index == '7' ) echo 'selected="selected"'; ?>>Tokyo</option>
                                                    <option value="8" data-value="+9" data-city="Seoul" data-name="Asia/Seoul" <?php if($time_zone_index == '8' ) echo 'selected="selected"'; ?>>Seoul</option>
                                                    <option value="9" data-value="+8" data-city="Beijing" data-name="Asia/Shanghai" <?php if($time_zone_index == '9' ) echo 'selected="selected"'; ?>>Beijing</option>
                                                    <option value="10" data-value="+8" data-city="Xianyang" data-name="Asia/Shanghai" <?php if($time_zone_index == '10' ) echo 'selected="selected"'; ?>>Xianyang</option>
                                                    <option value="11" data-value="+7" data-city="Hanoi" data-name="Asia/Ho_Chi_Minh" <?php if($time_zone_index == '11' ) echo 'selected="selected"'; ?>>Hanoi</option>
                                                    <option value="12" data-value="+7" data-city="Bangkok" data-name="Asia/Bangkok" <?php if($time_zone_index == '12' ) echo 'selected="selected"'; ?>>Bangkok</option>
                                                    <option value="13" data-value="+7" data-city="Myanmar" data-name="Asia/Rangoon" <?php if($time_zone_index == '13' ) echo 'selected="selected"'; ?>>Myanmar</option>
                                                    <option value="14" data-value="+6" data-city="Bangladesh" data-name="Asia/Dhaka" <?php if($time_zone_index == '14' ) echo 'selected="selected"'; ?>>Bangladesh</option>
                                                    <option value="15" data-value="+5" data-city="Sri Lanka" data-name="Asia/Colombo" <?php if($time_zone_index == '15' ) echo 'selected="selected"'; ?>>Sri Lanka</option>
                                                    <option value="16" data-value="+5" data-city="New Delhi" data-name="Asia/Kolkata" <?php if($time_zone_index == '16' ) echo 'selected="selected"'; ?>>New Delhi</option>
                                                    <option value="17" data-value="+5" data-city="Mumbai" data-name="Asia/Kolkata" <?php if($time_zone_index == '17' ) echo 'selected="selected"'; ?>>Mumbai</option>
                                                    <option value="18" data-value="0" data-city="London" data-name="Europe/London" <?php if($time_zone_index == '18' ) echo 'selected="selected"'; ?>>London</option>
                                                    <option value="19" data-value="+5" data-city="Sydney" data-name="Australia/Sydney" <?php if($time_zone_index == '19' ) echo 'selected="selected"'; ?>>Sydney</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <?php 
                                            if ($is_user_logged_in && (is_mw_qualified_teacher($current_user->ID) || is_mw_registered_teacher($current_user->ID)))
                                                $style = 'style="display: none;"';
                                            else
                                                $style = 'style="display: none;"';
                                        ?>
                                        <div id="tutor-regis-update" class="col-md-12" <?php echo $style ?>>
                                            <h4>Teacher and Tutor Account Info:</h4>
                                            <div id="info-update">
                                                <div class="row">
                                                    <div class="col-sm-6 col-md-6 col-xs-12">
                                                        <div class="form-group">
                                                            <input type="text" class="form-control" name="mobile_number" value="<?php echo $update_mobile_number ?>" id="mobile-number-update">
                                                            <span class="placeholder"><?php _e('Mobile Number', 'iii-dictionary') ?>:</span>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-6 col-md-6 col-xs-12 mt-top-mb">
                                                        <div class="form-group">
                                                            <input type="text" class="form-control" name="profession" value="<?php echo $update_profession ?>" id="profession-update">
                                                            <span class="placeholder"><?php _e('Profession', 'iii-dictionary') ?>:</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 col-md-6 col-xs-12 mt-top-14">
                                                        <div class="form-group">
                                                            <input type="text" class="form-control" name="last_school" value="<?php echo $update_last_school ?>" id="last-school-update">
                                                            <span class="placeholder"><?php _e('Last School Attended', 'iii-dictionary') ?>:</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 col-md-6 col-xs-12 mt-top-14">
                                                        <div class="form-group">
                                                            <input type="text" class="form-control" name="previous_school" value="<?php echo $update_previous_school ?>" id="previous-school-update">
                                                            <span class="placeholder"><?php _e('School Taught (if any)', 'iii-dictionary') ?>:</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 col-md-6 col-xs-12 mt-top-14">
                                                        <div class="form-group">
                                                            <input type="text" class="form-control" name="skype" value="<?php echo $update_skype ?>" id="skype-update">
                                                            <span class="placeholder"><?php _e('Skype ID (if any)', 'iii-dictionary') ?>:</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <label class="mt-top-10 mt-bottom-12">Tell me why you like Tutoring and Teaching</label>
                                            

                                            <label class="mt-top-12">Subjects you Interested in Tutor (check to all applied)</label>
                                            <div class="row">
                                                <div class="form__boolean chk-subject-type mt-bottom-10 clearfix" id="checkBoxSearch" style="margin-top: 0">
                                                    <div class="col-sm-4 col-md-3 col-xs-4 cb-type3">
                                                        <label>
                                                            <input type="checkbox" class="radio_buttons option-input-2 radio" value="english_writting" <?php if(count($subject_type_update)> 0 && in_array("english_writting", $update_language)) echo 'checked="checked"'; ?> name="subject_type_update"/> English Writting
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-4 col-md-3 col-xs-4 cb-type3">
                                                        <label>
                                                            <input type="checkbox" class="radio_buttons option-input-2 radio" value="english_conversation" <?php if(count($subject_type_update)> 0 && in_array("english_conversation", $update_language)) echo 'checked="checked"'; ?> name="subject_type_update"/> English Conversation
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-4 col-md-3 col-xs-4 cb-type3">
                                                        <label>
                                                            <input type="checkbox" class="radio_buttons option-input-2 radio" value="math_elementary" <?php if(count($subject_type_update)> 0 && in_array("math_elementary", $update_language)) echo 'checked="checked"'; ?> name="subject_type_update"/> Math (upto elementary)
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-3 col-md-3 col-xs-4 cb-type3">
                                                        <label>
                                                            <input type="checkbox" class="radio_buttons option-input-2 radio" value="math_any_level" <?php if(count($subject_type_update)> 0 && in_array("math_any_level", $update_language)) echo 'checked="checked"'; ?> name="subject_type_update"/> Math (any level)
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-2 col-md-2 col-xs-4 cb-type3">
                                                        <label>
                                                            <input type="checkbox" class="radio_buttons option-input-2 radio" value="other" <?php if(count($subject_type_update)> 0 && in_array("other", $update_language)) echo 'checked="checked"'; ?> name="subject_type_update"/> Others
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-7 col-md-10 col-xs-12 mt-top-14">
                                                        <div class="form-group">
                                                            <input type="text" class="form-control" name="description" value="<?php echo $update_description ?>" id="description-update">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <label class="mt-top-9 mt-bottom-7">Teaching Experience at School</label>
                                            <div class="row mt-top-9">
                                                <div class="col-sm-6 col-md-6 col-xs-12">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="school_name" value="<?php echo $update_school_name ?>" id="school-name-update">
                                                        <span class="placeholder"><?php _e('School Name', 'iii-dictionary') ?>:</span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 col-md-6 col-xs-12 mt-top-mb">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="teaching_link" value="<?php echo $update_teaching_link ?>" id="teaching-link-update">
                                                        <span class="placeholder"><?php _e('Link (if any)', 'iii-dictionary') ?>:</span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 col-md-6 col-xs-12 mt-top-14">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="teaching_subject" value="<?php echo $update_teaching_subject ?>" id="subject-update">
                                                        <span class="placeholder"><?php _e('Subject', 'iii-dictionary') ?>:</span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 col-md-6 col-xs-12 mt-top-14">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="years" value="<?php echo $update_years ?>" id="years-update">
                                                        <span class="placeholder"><?php _e('Years', 'iii-dictionary') ?>:</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <label class="mt-top-9 mt-bottom-7">Teaching Experience at Student</label>
                                            <div class="row mt-top-9">
                                                <div class="col-sm-6 col-md-6 col-xs-12">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="school_attend" value="<?php echo $update_school_attend ?>" id="school-attend-update">
                                                        <span class="placeholder"><?php _e('Attending', 'iii-dictionary') ?>:</span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 col-md-6 col-xs-12 mt-top-mb">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="student_link" value="<?php echo $update_student_link ?>" id="student-link-update">
                                                        <span class="placeholder"><?php _e('Link (if any)', 'iii-dictionary') ?>:</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-top-14">
                                                <div class="col-sm-4 col-md-4 cb-type4">
                                                    <div class="form-group border-ras select-style">
                                                        <select class="select-box-it form-control" name="birth-m" id="grade-update">
                                                            <option value="1" <?php if($update_grade=='1' ) echo 'selected="selected"'; ?>>Freshman</option>
                                                            <option value="2" <?php if($update_grade=='2' ) echo 'selected="selected"'; ?>>Sophomore</option>
                                                            <option value="3" <?php if($update_grade=='3' ) echo 'selected="selected"'; ?>>Junior</option>
                                                            <option value="4" <?php if($update_grade=='4' ) echo 'selected="selected"'; ?>>Senior</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-md-4 cb-type4">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="gpa" value="<?php echo $update_gpa ?>" id="gpa-update">
                                                        <span class="placeholder"><?php _e('GPA', 'iii-dictionary') ?>:</span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-md-4 cb-type4 mt-top-mb">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="major" value="<?php echo $update_major ?>" id="major-update">
                                                        <span class="placeholder"><?php _e('Major', 'iii-dictionary') ?>:</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <label class="mt-top-9 mt-bottom-7">Educational Background</label>
                                            <div class="row mt-top-9">
                                                <div class="col-sm-6 col-md-6 col-xs-12">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="school_name1" value="<?php echo $update_school_name1 ?>" id="school-name1-update">
                                                        <span class="placeholder"><?php _e('School Name 1', 'iii-dictionary') ?>:</span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 col-md-6 col-xs-12 mt-top-mb">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="school_link1" value="<?php echo $update_school_link1 ?>" id="school-link1-update">
                                                        <span class="placeholder"><?php _e('Link (if any)', 'iii-dictionary') ?>:</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-top-14">
                                                <div class="col-sm-6 col-md-6 col-xs-12">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="school_name2" value="<?php echo $update_school_name2 ?>" id="school-name2-update">
                                                        <span class="placeholder"><?php _e('School Name 2', 'iii-dictionary') ?>:</span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 col-md-6 col-xs-12 mt-top-mb">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="school_link2" value="<?php echo $update_school_link2 ?>" id="school-link2-update">
                                                        <span class="placeholder"><?php _e('Link (if any)', 'iii-dictionary') ?>:</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-top-14">
                                                <div class="col-sm-12 col-md-12 col-xs-12">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="any_other" value="<?php echo $update_any_other ?>" id="any-other-update">
                                                        <span class="placeholder"><?php _e('Others', 'iii-dictionary') ?>:</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-top-14">
                                        <div class="col-sm-6 col-md-6 col-xs-12 mt-top-4">
                                            <div class="form-group">
                                                <button id="update-teacher" class="btn-dark-blue border-btn" style="background: #65C762;" type="button" name="send-tutor">
                                                    <?php _e('Update', 'iii-dictionary') ?>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-6 col-xs-12 mt-top-4">
                                            <div class="form-group">
                                                <button id="cancel-update-teacher" class="btn-dark-blue border-btn cancel-update-teacher" data-id="sub-update-info" data-tab="updateinfo" style="background: #CECECE;" type="button" name="cancel">
                                                    <?php _e('Cancel', 'iii-dictionary') ?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" value="" id="chk-tutor-teacher">
                                    <input type="hidden" value="" id="chk-user-gender">
                                </form>
                            </div>
                            <!-- Update My Account -->

                            <!-- Subscription & Points -->
                            <div id="subscription" class="tab-pane fade">
                                <h3>Subscription & Points</h3>
                                <div class="subscription">
                                    <h3>Subscription Status & Purchase History</h3>
                                    <div id="tab-subs-purchase" class="tab-style">
                                        <ul class="nav nav-tabs">
                                            <li class="active tab-subs-purchase" id="subscription-status"><a data-toggle="tab" href="#tab-subs">Subscription Status</a></li>
                                            <li class="tab-subs-purchase" id="purchase-history"><a data-toggle="tab" href="#tab-purchase">Purchase History</a></li>
                                        </ul>
                                        <?php
                                            $current_user_id = get_current_user_id();
                                            $current_page2 = max(1, get_query_var('page'));
                                            $filter2 = get_page_filter_session();

                                            if (empty($filter2) && !isset($_POST['filter'])) {
                                                $filter2['orderby'] = 'ct.name';
                                                $filter2['order-dir'] = 'asc';
                                                $filter2['items_per_page'] = 9999;
                                                $filter2['offset'] = $filter2['items_per_page'] * ($current_page2 - 1);
                                            } else {
                                                $filter2['items_per_page'] = 99999999;
                                                if (isset($_REAL_POST['filter']['orderby'])) {
                                                    $filter2['orderby'] = $_REAL_POST['filter']['orderby'];
                                                    $filter2['order-dir'] = $_REAL_POST['filter']['order-dir'];
                                                }
                                                $filter2['offset'] = $filter2['items_per_page'] * ($current_page2 - 1);
                                            }

                                            set_page_filter_session($filter2);
                                            $user_subscriptions = MWDB::get_user_subscriptions($current_user_id, $filter2);
                                            $total_pages2 = ceil($user_subscriptions->total / $filter2['items_per_page']);
                                            $pagination2 = paginate_links(array(
                                                'format' => '?page=%#%',
                                                'current' => $current_page2,
                                                'total' => $total_pages2
                                            ));
                                        ?>
                                        <div class="tab-content">
                                            <div id="tab-subs" class="tab-pane fade in active">
                                                <div style="max-height: 450px; overflow-y: auto; overflow-x:hidden;">
                                                    <table class="table table-condensed table-subscription" id="user-subscriptions">
                                                        <thead>
                                                            <tr>
                                                                <th>
                                                                    <?php _e('Type', 'iii-dictionary') ?><span class="sorting-indicator"></span>
                                                                </th>
                                                                <th>
                                                                    <?php _e('Students', 'iii-dictionary') ?>
                                                                </th>
                                                                <th>
                                                                    <?php _e('Dictionary', 'iii-dictionary') ?>
                                                                </th>     
                                                                <th>
                                                                    <?php _e('Sub. End', 'iii-dictionary') ?> <span class="sorting-indicator"></span>
                                                                </th>
                                                                <th>
                                                                    <?php _e('Class (group)', 'iii-dictionary') ?>
                                                                </th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>

                                                        <tfoot>
                                                            <tr>
                                                                <td colspan="8">
                                                                    <?php echo $pagination2 ?>
                                                                </td>
                                                            </tr>
                                                        </tfoot>

                                                        <tbody>
                                                            <?php 
                                                            if (empty($user_subscriptions->items)) : 
                                                            ?>
                                                            <tr>
                                                                <td colspan="6">
                                                                    <?php _e('You haven\'t subscribed yet.', 'iii-dictionary') ?>
                                                                </td>
                                                            </tr>
                                                            <?php 
                                                            else : 
                                                                foreach ($user_subscriptions->items as $code) :
                                                                $date_a = date("Y-m-d");
                                                                if (ik_date_format($code->expired_on) < $date_a) {
                                                            ?>
                                                            <tr> 
                                                                <td class="note" style="width: 30%;">
                                                                    <?php 
                                                                    if (!$code->inherit){
                                                                        echo $code->type;
                                                                        echo in_array($code->typeid, array(SUB_SAT_PREPARATION, SUB_MATH_SAT_I_PREP, SUB_MATH_SAT_II_PREP)) ? ' - ' . $code->sat_class : ''; 
                                                                        ?>
                                                                        <div class="detail-note">
                                                                            <?php 
                                                                            echo $code->type;
                                                                            echo in_array($code->typeid, array(SUB_SAT_PREPARATION, SUB_MATH_SAT_I_PREP, SUB_MATH_SAT_II_PREP)) ? ' - ' . $code->sat_class : ''; 
                                                                            ?>
                                                                        </div>
                                                                    <?php 
                                                                    }else{
                                                                        echo $code->type 
                                                                    ?>
                                                                        <div class="detail-note">
                                                                            <?php echo $code->type ?>
                                                                        </div>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                </td>
                                                                <td style="width: 10%;">
                                                                    <?php 
                                                                    echo in_array($code->typeid, array(SUB_TEACHER_TOOL, SUB_TEACHER_TOOL_MATH, SUB_GROUP)) ? $code->number_of_students : 'N/A'; 
                                                                    ?>
                                                                </td>
                                                                <td style="width: 10%;">
                                                                    <?php echo $code->dictionary ?>
                                                                </td>
                                                                <td style="width: 10%;">
                                                                    <?php echo ik_date_format($code->expired_on) ?>
                                                                </td>
                                                                <td style="width: 30%;" class="note">
                                                                    <?php 
                                                                    echo is_null($code->group_name) ? 'N/A' : $code->group_name; 
                                                                    ?>
                                                                    <div class="detail-note">
                                                                        <?php 
                                                                        echo is_null($code->group_name) ? 'N/A' : $code->group_name; 
                                                                        ?>
                                                                    </div>
                                                                </td>
                                                                <?php
                                                                    $date1 = new DateTime();
                                                                    $date2 = new DateTime($code->expired_on);
                                                                    $interval = $date1->diff($date2);
                                                                    $months_left = $interval->d > 0 ? $interval->m + 1 : $interval->m;
                                                                    $checked_out_state = '';
                                                                    foreach ($cart_items as $item) {
                                                                        if ($item->sub_id == $code->id) {
                                                                            $checked_out_state = ' disabled';
                                                                        }
                                                                    }
                                                                ?>
                                                                <td style="width: 10%;" data-subid="<?php echo $code->id ?>" data-type="<?php echo $code->typeid ?>" data-did="<?php echo $code->dictionary_id ?>" data-size="<?php echo $code->number_of_students ?>" data-months="<?php echo $months_left ?>" <?php echo!is_null($code->group_name) ? ' data-group="' . $code->group_name . '"' : '' ?> data-sat-class=" <?php echo $code->sat_class ?>" data-sat-class-id="<?php echo $code->sat_class_id ?>" data-gid="<?php echo $code->group_id ?>">

                                                                    <?php if (!$code->inherit){ ?>
                                                                    <a data-toggle="tab" href="#"><img src="<?php echo get_template_directory_uri(); ?>/library/images/Icon_Detail_Grey_1.png" class="" alt="setting my account" style="width: 23px; margin-right: 5px;"></a>
                                                                    <a data-toggle="tab" href="#"><img src="<?php echo get_template_directory_uri(); ?>/library/images/Icon_Subscription.png" class="" alt="setting my account" style="width: 23px;"></a>
                                                                    <?php } ?>
                                                                </td>
                                                            </tr>
                                                            <?php }else{ ?>
                                                            <tr>
                                                                <td>
                                                                    <?php 
                                                                    if (!$code->inherit){
                                                                        echo $code->type;
                                                                        echo in_array($code->typeid, array(SUB_SAT_PREPARATION, SUB_MATH_SAT_I_PREP, SUB_MATH_SAT_II_PREP)) ? ' - ' . $code->sat_class : '';
                                                                    }else{ 
                                                                        echo $code->type; 
                                                                    }
                                                                    ?>
                                                                </td>
                                                                <td>
                                                                    <?php 
                                                                    echo in_array($code->typeid, array(SUB_TEACHER_TOOL, SUB_TEACHER_TOOL_MATH, SUB_GROUP)) ? $code->number_of_students : 'N/A';
                                                                    ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo $code->dictionary ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo ik_date_format($code->expired_on) ?>
                                                                </td>
                                                                <td>
                                                                    <?php 
                                                                    echo is_null($code->group_name) ? 'N/A' : $code->group_name; 
                                                                    ?>
                                                                </td>

                                                                <?php
                                                                    $date1 = new DateTime();
                                                                    $date2 = new DateTime($code->expired_on);
                                                                    $interval = $date1->diff($date2);
                                                                    $months_left = $interval->d > 0 ? $interval->m + 1 : $interval->m;
                                                                    $checked_out_state = '';
                                                                    foreach ($cart_items as $item) {
                                                                        if ($item->sub_id == $code->id) {
                                                                            $checked_out_state = ' disabled';
                                                                        }
                                                                    }
                                                                ?>
                                                                <td data-subid="<?php echo $code->id ?>" data-type="<?php echo $code->typeid ?>" data-did="<?php echo $code->dictionary_id ?>" data-size="<?php echo $code->number_of_students ?>" data-months="<?php echo $months_left ?>" <?php echo!is_null($code->group_name) ? ' data-group="' . $code->group_name . '"' : '' ?> data-sat-class="<?php echo $code->sat_class ?>" data-sat-class-id="<?php echo $code->sat_class_id ?>" data-gid="<?php echo $code->group_id ?>">

                                                                    <?php 
                                                                    if (!$code->inherit){
                                                                        if (!in_array($code->typeid, array(SUB_GROUP))){  
                                                                    ?>
                                                                    <a data-toggle="tab" href="#"><img src="<?php echo get_template_directory_uri(); ?>/library/images/Icon_Detail_Grey_1.png" class="" alt="setting my account" style="width: 23px; margin-right: 5px;"></a>
                                                                                                                
                                                                    <a data-toggle="tab" href="#"><img src="<?php echo get_template_directory_uri(); ?>/library/images/Icon_Subscription.png" class="" alt="setting my account" style="width: 23px;"></a>
                                                                    <?php 
                                                                       }
                                                                    }
                                                                    ?>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                                    }
                                                                endforeach;
                                                            endif; 
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div style="clear: both;"></div>
                                                <div class="detail-subs">
                                                    <a data-toggle="tab" href="#"><img src="<?php echo get_template_directory_uri(); ?>/library/images/Icon_Detail_Grey_1.png" class="" alt="setting my account" style="width: 23px; margin-right: 5px;">Detail</a>
                                                    <a data-toggle="tab" href="#"><img src="<?php echo get_template_directory_uri(); ?>/library/images/Icon_Subscription.png" class="" alt="setting my account" style="width: 23px;">Subscription</a>
                                                </div>
                                            </div>
                                            <div id="tab-purchase" class="tab-pane fade">
                                                <div style="max-height: 450px; overflow: auto;">
                                                    <table class="table table-condensed table-subscription">
                                                        <thead>
                                                            <tr>
                                                                <th>
                                                                    <?php _e('Purchase Item', 'iii-dictionary') ?>
                                                                </th>
                                                                <th>
                                                                    <?php _e('Activation Code', 'iii-dictionary') ?>
                                                                </th>
                                                                <th>
                                                                    <?php _e('Payment Method', 'iii-dictionary') ?>
                                                                </th>
                                                                <th>
                                                                    <?php _e('Paid Amount', 'iii-dictionary') ?>
                                                                </th>
                                                                <th>
                                                                    <?php _e('Purchased On', 'iii-dictionary') ?>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <?php
                                                        $purchased_history = MWDB::get_user_purchase_history($current_user_id);
                                                        ?>
                                                        <tbody>
                                                        <?php if (empty($purchased_history)) : ?>
                                                            <tr>
                                                                <td colspan="5">
                                                                    <?php _e('No history', 'iii-dictionary') ?>
                                                                </td>
                                                            </tr>
                                                        <?php
                                                        else :
                                                            foreach ($purchased_history as $item) :
                                                        ?>
                                                            <tr>
                                                                <td>
                                                                    <?php echo $item->purchased_item_name ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo!empty($item->encoded_code) ? $item->encoded_code : 'NULL'; ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo $item->payment_method ?>
                                                                </td>
                                                                <td>$
                                                                    <?php echo $item->amount ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo ik_date_format($item->purchased_on, 'm/d/Y H:m:i') ?>
                                                                </td>
                                                            </tr>
                                                        <?php
                                                            endforeach;
                                                        endif
                                                        ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <form method="post" action="">
                                                <input type="hidden" name="dictionary-id" id="dictionary-id" value="">
                                                <input type="hidden" name="starting-date" id="starting-date-txt" value="">
                                                <input type="hidden" name="assoc-group" id="assoc-group" value="">
                                                <input type="hidden" name="group-name" id="group-name" value="">
                                                <input type="hidden" name="group-pass" id="group-pass" value="">
                                                <input type="hidden" id="activation-code" name="activation-code" value="">
                                            </form>
                                            <div class="activation">
                                                <h3>Activation Code</h3>
                                                <div class="form-group col-md-6" style="padding-left: 0px !important;">
                                                    <label for="credit-code">Enter a Credit Code <span style="font-style: italic;">(if you have any)</span></label>
                                                    <input class="form-control border-ras" id="credit-code" name="credit-code">
                                                </div>
                                                <div class="form-group col-md-6" style="padding-right: 0px !important;">
                                                    <button class="btn-dark-blue border-btn" id="val-credit-code" style="background: #f7b555; margin-top: 25px;margin-bottom: 50px;" type="button" data-loading-text="<?php _e('Checking...', 'iii-dictionary') ?>" data-error-text="<?php _e('Please enter a credit code', 'iii-dictionary') ?>">
                                                        <?php _e('Apply', 'iii-dictionary') ?>
                                                    </button>
                                                    <span data-toggle="popover" data-placement="bottom" data-container="body" data-html="true" data-max-width="420px"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Subscription & Points -->

                            <div id="tutoring-main" class="tab-pane fade">
                                <div class="student-center">
                                    <div class="row">
                                        <div class="col-sm-6 col-md-6 col-xs-6">
                                            <p class="mt-bottom-12 student-center-title">Student Information Center</p>
                                            <div class="new-request-list">SCHEDULE</div>
                                        </div>
                                        <div class="col-sm-6 col-md-6 col-xs-6 text-right">
                                            <button type="button" id="btn-tutor">
                                                Find Tutor
                                            </button>
                                            <button type="button" id="btn-schedule">
                                                Schedule
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="tutoring-main">
                                    <div id="tab-sub-tutoring" class="tab-style2">
                                        <div class="tab-content">
                                            <div id="tab-tutor-content" class="tab-pane fade in active">
                                                <input value="" type="hidden" id="active-day-tutor">
                                                <input value="" type="hidden" id="today-tutor">
                                                <div class="form-group border-ras select-style" id="custom-timezone" data-type="" data-day="">
                                                    <?php
                                                    $timezone_index = '';
                                                    if ($is_user_logged_in) {
                                                    $timezone_index = get_user_meta($current_user->ID, 'time_zone_index', true);
                                                    }
                                                    ?>
                                                    <span class="placeholder-timezone">                 
                                                        Time Zone:
                                                    </span>
                                                    <span id="time-clock" data-hour="24" data-minute="0">2:35 PM</span>
                                                    <select class="select-box-it form-control" name="time_zone" id="select-timezone">
                                                        <option disabled="" value="0" data-value="" data-city="London" <?php if($timezone_index == '0' ) echo 'selected="selected"'; ?>>Select Time Zone</option>
                                                        <option value="1" data-value="-5" data-name="America/New_York" data-city="New York" <?php if($timezone_index == '1' ) echo 'selected="selected"'; ?>>New York</option>
                                                        <option value="2" data-value="-6" data-name="America/Chicago" data-city="Minneapolis" <?php if($timezone_index == '2' ) echo 'selected="selected"'; ?>>Minneapolis</option>
                                                        <option value="3" data-value="-5" data-name="America/Denver" data-city="Colorado" <?php if($timezone_index == '3' ) echo 'selected="selected"'; ?>>Colorado</option>
                                                        <option value="4" data-value="-7" data-name="America/Los_Angeles" data-city="San Francisco" <?php if($timezone_index == '4' ) echo 'selected="selected"'; ?>>San Francisco</option>
                                                        <option value="5" data-value="-10" data-name="Pacific/Honolulu" data-city="Hawaii" <?php if($timezone_index == '5' ) echo 'selected="selected"'; ?>>Hawaii</option>
                                                        <option value="6" data-value="+10" data-name="Pacific/Guam" data-city="Guam" <?php if($timezone_index == '6' ) echo 'selected="selected"'; ?>>Guam</option>
                                                        <option value="7" data-value="+9" data-name="Asia/Tokyo" data-city="Tokyo" <?php if($timezone_index == '7' ) echo 'selected="selected"'; ?>>Tokyo</option>
                                                        <option value="8" data-value="+9" data-name="Asia/Seoul" data-city="Seoul" <?php if($timezone_index == '8' ) echo 'selected="selected"'; ?>>Seoul</option>
                                                        <option value="9" data-value="+8" data-name="Asia/Shanghai" data-city="Beijing" <?php if($timezone_index == '9' ) echo 'selected="selected"'; ?>>Beijing</option>
                                                        <option value="10" data-value="+8" data-name="Asia/Shanghai" data-city="Xianyang" <?php if($timezone_index == '10' ) echo 'selected="selected"'; ?>>Xianyang</option>
                                                        <option value="11" data-value="+7" data-name="Asia/Ho_Chi_Minh" data-city="Hanoi" <?php if($timezone_index == '11' ) echo 'selected="selected"'; ?>>Hanoi</option>
                                                        <option value="12" data-value="+7" data-name="Asia/Bangkok" data-city="Bangkok" <?php if($timezone_index == '12' ) echo 'selected="selected"'; ?>>Bangkok</option>
                                                        <option value="13" data-value="+7" data-name="Asia/Rangoon" data-city="Myanmar" <?php if($timezone_index == '13' ) echo 'selected="selected"'; ?>>Myanmar</option>
                                                        <option value="14" data-value="+6" data-name="Asia/Dhaka" data-city="Bangladesh" <?php if($timezone_index == '14' ) echo 'selected="selected"'; ?>>Bangladesh</option>
                                                        <option value="15" data-value="+5" data-name="Asia/Colombo" data-city="Sri Lanka" <?php if($timezone_index == '15' ) echo 'selected="selected"'; ?>>Sri Lanka</option>
                                                        <option value="16" data-value="+5" data-name="Asia/Kolkata" data-city="New Delhi" <?php if($timezone_index == '16' ) echo 'selected="selected"'; ?>>New Delhi</option>
                                                        <option value="17" data-value="+5" data-name="Asia/Kolkata" data-city="Mumbai" <?php if($timezone_index == '17' ) echo 'selected="selected"'; ?>>Mumbai</option>
                                                        <option value="18" data-value="0" data-name="Europe/London" data-city="London" <?php if($timezone_index == '18' ) echo 'selected="selected"'; ?>>London</option>
                                                        <option value="19" data-value="+5" data-name="Australia/Sydney" data-city="Sydney" <?php if($timezone_index == '19' ) echo 'selected="selected"'; ?>>Sydney</option>
                                                    </select>
                                                </div>
                                                <div class="section-tutor-main">
                                                    <div class="border-selectall color-border">
                                                        <button type="button" class="btn-sub-tab active" name="available_now" id="btn-available-now">
                                                            <img src="<?php echo get_template_directory_uri(); ?>/library/images/04_Available_Now_Selected.png" alt="">Available Now
                                                        </button>
                                                        <button type="button" class="btn-sub-tab" name="find_tutoring" id="btn-find-tutoring">
                                                            <img src="<?php echo get_template_directory_uri(); ?>/library/images/icon_Find_off.png" alt="">Find
                                                        </button>
                                                        <button type="button" class="btn-sub-tab" name="list_favorite" id="btn-list-favorite">
                                                            <img src="<?php echo get_template_directory_uri(); ?>/library/images/icon_L_Favorite.png" alt="">Favorites
                                                        </button>
                                                        <button type="button" class="btn-sub-tab" name="list_review" id="btn-list-review">
                                                            <img src="<?php echo get_template_directory_uri(); ?>/library/images/icon_L_Review.png" alt="">Review
                                                        </button>
                                                        <button type="button" class="btn-sub-tab" name="list_tutoring" id="btn-list-tutoring">
                                                            <img src="<?php echo get_template_directory_uri(); ?>/library/images/icon_L_list.png" alt="">All
                                                        </button>
                                                    </div>

                                                    <div class="color-border toggle-btn" style="display: none;">
                                                        <input type="checkbox" name="cb_show_available" id="cb-show-available" class="hidden check-toggle">
                                                        <img  style="height: 18px; display: inline-block;" src="<?php echo get_template_directory_uri(); ?>/library/images/icon_Toggle_Switch_OFF.png" alt="Toggle_on" class="inactive-img" onclick="document.getElementById('cb-show-available').click();">
                                                        <div class="lable-toggle inactive">Show Only Available Tutors</div>
                                                    </div>

                                                    <div class="frm-available-now">
                                                        <div class="row">
                                                            <div class="col-sm-12 col-md-12">
                                                                <div class="find-page-title">Tutor and Subject</div>
                                                            </div>
                                                        </div>
                                                        <div class="row">                                                           
                                                            <div class="col-sm-6 col-md-6 col-xs-6">
                                                                <div class="find-general-border">
                                                                <label class="find-label">Subject for tutoring</label>
                                                                <div class="form-group border-ras select-style">
                                                                    <select class="find-select select-box-it  form-control" name="available_subject" id="select-available-subject">
                                                                        <option class="boo" value="0" data-name="">Subject</option>
                                                                        <option value="all" data-name="Any Subjects">Any Subjects</option>
                                                                        <option value="english_subject|all" data-name="English Only">English Only</option>
                                                                        <option value="math_subject|all" data-name="Math Only">Math Only</option>
                                                                        <option value="science_subject|all" data-name="Science Only">Science Only </option>
                                                                        <option value="other_preference|others" data-name="Other Subjects Only">Other Subjects Only</option>
                                                                        <option value="english_subject|english_conversation" data-name="English: Conversation for Foreign Students">English: Conversation for Foreign Students</option>
                                                                        <option value="english_subject|english_grammar" data-name="Enlgish: Grammar">Enlgish: Grammar</option>
                                                                        <option value="english_subject|english_writting" data-name="English Writting">English Writting</option>
                                                                        <option value="english_subject|english_reading_comprehension" data-name="English: Reading Comprehension">English: Reading Comprehension</option>
                                                                        <option value="english_subject|others" data-name="English: Others">English: Others</option>
                                                                        <option value="math_subject|elemenatary_school_math" data-name="Math: Elementary">Math: Elementary</option>
                                                                        <option value="math_subject|middle_school_math" data-name="Math: Middle School">Math: Middle School</option>
                                                                        <option value="math_subject|high_school_math" data-name="Math: High School">Math: High School</option>
                                                                        <option value="math_subject|advanced_math" data-name="Math: Advanced">Math: Advanced</option>
                                                                        <option value="math_subject|others" data-name="Math: Others">Math: Others</option>
                                                                        <option value="science_subject|science_middle_school" data-name="Science: Elementary/Middle School">Science: Elementary/Middle School</option>
                                                                        <option value="science_subject|physics_high_school" data-name="Science: High School">Science: High School</option>
                                                                        <option value="science_subject|chemistry_high_school" data-name="Science: Chemistry for High School">Science: Chemistry for High School</option>
                                                                        <option value="science_subject|others" data-name="Science: Others">Science: Others</option>
                                                                    </select>
                                                                </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6 col-md-6 search-tutorname">
                                                            <div class="find-general-border">
                                                                <label class="find-label">Tutor Name:</label>
                                                                <div class="form-group">
                                                                    <input id="search-find-tutoring" name="search-ready-les" class="find-name form-control search-tit " placeholder="Type name here...">
                                                                </div>
                                                                </div>
                                                            </div>
                                                            
                                                        </div>
                                                        <!-- <div class="row">
                                                        <div class="col-sm-3 col-md-3 col-xs-12 cb-type2">
                                                                <label>
                                                                    <input type="checkbox" class="radio_tutor_search class_cb_search option-input-2 radio" value="favorite" data-lang="en" name="type_search" /> Favorites
                                                                </label>
                                                                <label>
                                                                    <input type="checkbox" class="radio_tutor_search class_cb_search option-input-2 radio" value="rating" data-lang="en" name="type_search" /> Rating
                                                                </label>
                                                            </div>
                                                        </div> -->
                                                        <div class ="row">
                                                            <div class="col-sm-4 col-md-4">
                                                            <div class="find-general-border">
                                                                <label class="find-label">Tutoring Type</label>
                                                                <div class=" max-100 form-group select-style">
                                                            <select class="find-select select-box-it  form-control" name="available_subject" id="select-available-subject">
                                                                <option class="boo" value="0" data-name="">Tutoring type</option>
                                                                <option value="one_tutoring" data-name="1 on 1 Tutoring">1 On 1 Tutoring</option>
                                                                <option value="group_tutoring" data-name="Group Tutoring">Group Tutoring</option>
                                                            </select>
                                                            </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4 col-md-4">
                                                            <div class="find-general-border">
                                                            <label class="find-label">Price</label>
                                                            <div class="max-100 form-group select-style">
                                                            <select class="find-select select-box-it  form-control" name="available_subject" id="select-available-price">
                                                                <option class="boo" value="0" data-name="">Price</option>
                                                                <option value="one_to_ten" data-name="$1 - $10 (30 min)">$1 - $10 (30 min)</option>
                                                                <option value="eleven_to_twenty" data-name="$11 - $20 (30 min)">$11 - $20 (30 min)</option>
                                                                <option value="twetyone_to_thirty" data-name="$21 - $30 (30 min)">$21 - $30 (30 min)</option>
                                                                <option value="thirtyone_to_fourty" data-name="$31 - $40 (30 min)">$31 - $40 (30 min)</option>
                                                                <option value="fourtyone_to_fifty" data-name="$41 - $50 (30 min)">$41 - $50 (30 min)</option>
                                                                <option value="morethan_fifty" data-name="> $50 (30 min)">> $50 (30 min)</option>

                                                            </select>
                                                            </div>  
                                                            </div>
                                                        </div>
                                                        
                                                    
                                                    <div class="col-sm-4 col-md-4">
                                                        <div class="find-general-border">
                                                            <label class="find-label">Option :</label>
                                                            <div class="max-100 form-group select-style">
                                                            <select class="find-select select-box-it  form-control" name="available_subject" id="select-available-option">
                                                                <option class="boo" value="0" data-name="">Option</option>
                                                                <option value="all" data-name="Rating & Favorite">Rating & Favorite</option>
                                                                <option value="rating" data-name="Rating">Rating</option>
                                                                <option value="favorite" data-name="Favorite">Favorite</option>
                                                            </select>
                                                            </div>                                                       
                                                        </div>
                                                    </div>
                                                </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <label class="mt-top-10 mt-bottom-12">Date:</label>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-4 col-md-4 col-xs-4">
                                                                <div class="form-group border-ras select-style">
                                                                    <select class="select-box-it form-control" name="available_month" id="select-available-month">
                                                                        <option value="0">Select Month</option>
                                                                        <?php 
                                                                        for ($j = 1; $j < 13; $j++) {
                                                                            if($j < 10)
                                                                                $jt = '0'.$j;
                                                                            else
                                                                                $jt = $j;
                                                                        ?>
                                                                        <option value="<?php echo $jt; ?>"><?php echo date('M', mktime(0,0,0,$j)) ?></option>
                                                                        <?php
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-4 col-md-4 col-xs-4">
                                                                <div class="form-group border-ras select-style">
                                                                    <select class="select-box-it form-control" name="available_day" id="select-available-day">
                                                                        <option value="0">Select Day</option>
                                                                        <?php 
                                                                        for ($i = 1; $i < 32; $i++) {
                                                                            if($i < 10)
                                                                                $it = '0'.$i;
                                                                            else
                                                                                $it = $i;
                                                                        ?>
                                                                        <option value="<?php echo $it; ?>"><?php echo $i ?></option>
                                                                        <?php
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-4 col-md-4 col-xs-4">
                                                                <div class="form-group border-ras select-style">
                                                                    <select class="select-box-it form-control" name="available_time" id="select-available-time">
                                                                        <option value="0" data-time="" data-time-view="">Select Time</option>
                                                                        <?php 
                                                                        $dt_hour = (int)$dt->format('H');
                                                                        $type = 'am';
                                                                        for ($l = $dt_hour; $l < 24; $l++) {
                                                                            if ($l > 11){
                                                                                $k = $l - 12;                      
                                                                                $type = 'pm'; 
                                                                            }else{
                                                                                $k = $l;
                                                                            }
                                                                            $ks = $id = $k + 1;
                                                                            if($k == 0) $k = 12;
                                                                            $kl = $k;
                                                                            if($k < 10) $k = '0'.$k;
                                                                            if($id < 10) $id = '0'.$id;
                                                                        ?>
                                                                        <option data-time="<?php echo $kl.':00:'.$type.' ~ '.$kl.':30:'.$type ?>" data-time-view="<?php echo $kl.':00'.$type.'-'.$kl.':30'.$type ?>" value="<?php echo $kl.':00'.$type; ?>"><?php echo $k.':00'.' '.$type.' - '.$k.':30'.' '.$type ?></option>
                                                                        <option data-time="<?php echo $kl.':30:'.$type.' ~ '.$ks.':00:'.$type ?>" data-time-view="<?php echo $kl.':30'.$type.'-'.$ks.':00'.$type ?>" value="<?php echo $kl.':30'.$type; ?>"><?php echo $k.':30'.' '.$type.' - '.$id.':00'.' '.$type ?></option>
                                                                        <?php
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-4 col-md-4 col-xs-4 available-year-mb">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control border-ras" name="available_year" value="<?php echo $dt->format('Y') ?>" id="available_year">
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-4 col-md-4 col-xs-4">
                                                                <button class="btn-dark-blue border-btn btn-available-reset" type="button" name="available_reset">
                                                                    <?php _e('Reset', 'iii-dictionary') ?>
                                                                </button>
                                                            </div>
                                                            <div class="col-sm-4 col-md-4 col-xs-4 available-search-mb">
                                                                <button class="btn-dark-blue border-btn" id="btn-available-search" type="button" name="available_search">
                                                                    <?php _e('Search Now', 'iii-dictionary') ?>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="tutoring-table">
                                                        <table id="table-detail-tutor">
                                                            <tbody>
                                                                <tr class="tr-detail">
                                                                    <td>
                                                                        <p class="schedule-detail">Your Scheduling detail:</p>
																		<p class="subject-selected-detail">
																			<span>Subject:</span>
																			<span id="selected-subject" class="not-selected active">Not selected yet</span>
																		</p>
                                                                        <p class="date-detail">
																			<span>Date:</span>
																			<span id="selected-date"></span>
																		</p>
                                                                        <p class="tutor-detail">
																			<span>Tutor:</span>
																			<span id="selected-tutor" class="not-selected">Not selected yet</span>
																		</p>
                                                                        <img class="close-detail" src="<?php echo get_template_directory_uri(); ?>/library/images/icon_CONFIRM-CLOSE.png" alt="">
                                                                        <button class="btn-dark-blue border-btn btn-schedule-now" type="button" name="schedule_now" id="btn-schedule-now">Schedule Now</button>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                        <table>
                                                            <tbody id="table-list-tutor">
                                                                
                                                            </tbody>
                                                        </table>
                                                        <div class="slide-resume">
                                                            
                                                        </div>
                                                    </div>                                                    
                                                </div>

                                                <div class="main-status-request" style="display: none;">
                                                    <div class="border-selectall color-border">
                                                        <button type="button" class="btn-sub-status active" name="list_status" id="btn-status-all" data-status="all">
                                                            Show All
                                                        </button>
                                                        <button type="button" class="btn-sub-status" name="list_waiting" id="btn-status-waiting" data-status="waiting">
                                                            Waiting
                                                        </button>
                                                        <button type="button" class="btn-sub-status" name="list_confirmed" id="btn-status-confirmed" data-status="confirmed">
                                                            Confirmed
                                                        </button>
                                                        <button type="button" class="btn-sub-status" name="list_canceled" id="btn-status-canceled" data-status="canceled">
                                                            Canceled
                                                        </button>
                                                        <button type="button" class="btn-sub-status" name="list_Finished" id="btn-status-finished" data-status="finished">
                                                            Finished
                                                        </button>
                                                    </div>
                                                    <div class="tutoring-table">
                                                        <table>
                                                            <tbody id="table-status-request">
                                                                
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="main-view-request" style="display: none;">
                                                    <div class="row">
                                                        <div class="col-md-11">
                                                            <p class="name-request-vew">
                                                                <img class="img-new-request" src="<?php echo get_template_directory_uri(); ?>/library/images/icon_Status_Confirmed.png">
                                                                <span>CONFIRMED</span>
                                                            </p>
                                                        </div>
                                                        <div class="col-md-1 no-padding text-right">
                                                            <img class="goto-main-schedule" src="<?php echo get_template_directory_uri(); ?>/library/images/icon_Tutor_GoBack.png" data-type="schedule">
                                                        </div>
                                                    </div>
                                                    <p class="time-request">
                                                        <span class="current-view-day"><?php echo $dt->format('F d, Y')?></span>
                                                        <span class="stuff-view-day">(<?php echo $dt->format('D') ?>)</span>
                                                        <span class="time-current-view"></span>
                                                    </p>
                                                    <p class="location-request">
                                                        <img src="<?php echo get_template_directory_uri(); ?>/library/images/icon_Status_Location.png">
                                                        <span class="label-timezone">Time Zone:</span>
                                                        <span class="name-timezone">New Work</span>
                                                    </p>
                                                    <p class="tutor-request">
                                                        <img src="<?php echo get_template_directory_uri(); ?>/library/images/icon_Status_Tutor.png">
                                                        Tutor: <span>Vincent Burke</span>
                                                    </p>
                                                    <p class="subject-request">
                                                        <img src="<?php echo get_template_directory_uri(); ?>/library/images/icon_Status_Subject.png">
                                                        <span>English Conversation</span>
                                                    </p>
                                                    <p class="points-request">
                                                        <img src="<?php echo get_template_directory_uri(); ?>/library/images/icon_Status_Points.png">
                                                        <span>37 Points($) used</span>
                                                    </p>
                                                    <p class="message-sent">Message Sent:</p>
                                                    <p class="title-request">Test</p>
                                                    <p class="more-request clearfix">
                                                        <span class="by">by <span>Peter Chung</span></span>     
                                                        <img class="btn-edit-desc" src="<?php echo get_template_directory_uri(); ?>/library/images/icon_Tutor_Edit.png">
                                                        <span class="create-time">Jan 26, 2018 (5:30)</span>
                                                    </p>
                                                    <p class="description-request">Test</p>
                                                    
                                                    <div class="row">
                                                        <div class="col-sm-6 col-md-6 col-xs-12">
                                                            <div class="form-group">
                                                                <button class="btn-dark-blue border-btn btn-view-request" type="button" name="send-tutor">
                                                                    <?php _e('Start Tutor!', 'iii-dictionary') ?>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6 col-md-6 col-xs-12">
                                                            <div class="form-group">
                                                                <button class="btn-dark-blue border-btn btn-reschedule-request" type="button" name="send-tutor">
                                                                    <?php _e('Cancel & Reschedule', 'iii-dictionary') ?>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <p class="time-current-request"></p>
                                                </div>

                                                <div class="writting-review" style="display: none;">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <p class="head-title-resum">WRITE A REVIEW</p>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-xs-5 col-sm-9 col-md-9">
                                                            <div class="form-group">
                                                                <input type="text" class="form-control border-ras" name="subject" value="" id="write-review-subject">
                                                                <span class="placeholder"><?php _e('Title', 'iii-dictionary') ?>:</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-7 col-sm-3 col-md-3 cb-type-star">
                                                            <label>
                                                                <input id="star1" type="checkbox" class="star_buttons option-input-star radio" value="1" data-star="1" name="star">
                                                            </label>
                                                            <label>
                                                                <input id="star2" type="checkbox" class="star_buttons option-input-star radio" value="1" data-star="2" name="star">
                                                            </label>
                                                            <label>
                                                                <input id="star3" type="checkbox" class="star_buttons option-input-star radio" value="1" data-star="3" name="star">
                                                            </label>
                                                            <label>
                                                                <input id="star4" type="checkbox" class="star_buttons option-input-star radio" value="1" data-star="4" name="star">
                                                            </label>
                                                            <label>
                                                                <input id="star5" type="checkbox" class="star_buttons option-input-star radio" value="1" data-star="5" name="star">
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">                                     
                                                            
                                                            <!-- <div id="character_count"></div> -->
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-6 col-md-6">
                                                            <button type="button" class="btn-orange2 btn-green border-ras" name="submit_review" id="btn-submit-review">Submit Review</button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row header-title-newschedule">
                                                    <div class="col-md-11">
                                                        <p class="name-request">
                                                            <img class="img-new-request" src="<?php echo get_template_directory_uri(); ?>/library/images/icon_Request.png">
                                                            <span>REQUEST</span>
                                                        </p>
                                                    </div>
                                                    <div class="col-md-1 no-padding text-right">
                                                        <img class="goto-main-schedule" src="<?php echo get_template_directory_uri(); ?>/library/images/icon_Tutor_GoBack.png" data-day="">
                                                    </div>
                                                </div>

                                                <div class="main-my-schedule" style="display: none;">
                                                    <div class="box-schedule-left">
                                                        <div class="border-datepicker">
                                                            <div id="sandbox-container-tutor"></div>
                                                            <div class="upcoming-schedule">
                                                                <h4>Upcoming Schedules</h4>
                                                                <div class="upcoming-main style-scrollbar">
                                                                    <ul id="upcoming-schedule">
                                                                        <li>
                                                                            <span class="time-upcoming">Dec 14, 2018 / 8:30am - 9:30am</span>
                                                                            <span>English business conversation</span>
                                                                        </li>
                                                                        <li>
                                                                            <span class="time-upcoming">Dec 14, 2018 / 8:30am - 9:30am</span>
                                                                            <span>English business conversation</span>
                                                                        </li>
                                                                        <li>
                                                                            <span class="time-upcoming">Dec 14, 2018 / 8:30am - 9:30am</span>
                                                                            <span>English business conversation</span>
                                                                        </li>
                                                                        <li>
                                                                            <span class="time-upcoming">Dec 14, 2018 / 8:30am - 9:30am</span>
                                                                            <span>English business conversation</span>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                            <button type="button" class="btn-open-upcoming" id="btn-open-upcoming">
                                                                <img src="<?php echo get_template_directory_uri(); ?>/library/images/tutoring_05_Open_Upcomings.png" alt="">
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="box-schedule-right">
                                                        <div class="header-schedule clearfix">
                                                            <div class="col-xs-2 col-sm-2 col-md-2 no-padding-l">
                                                                <img class="schedule-left-btn" src="<?php echo get_template_directory_uri(); ?>/library/images/Chalendar_icon_Left_circle.png" data-day="<?php echo $dt_yesterday->format('Y-m-d') ?>" data-type="schedule">
                                                                <img class="schedule-right-btn" src="<?php echo get_template_directory_uri(); ?>/library/images/Chalendar_icon_Rightt_circle.png"  data-day="<?php echo $dt_tomorrow->format('Y-m-d') ?>" data-type="schedule">
                                                            </div>
                                                            <div class="col-xs-7 col-sm-7 col-md-7 no-padding">
                                                                <span class="current-stuff">
                                                                    <span class="current-day"><?php echo $dt->format('F d') ?></span>
                                                                    <span class="stuff-day">(<?php echo $dt->format('D') ?>)</span>
                                                                </span>
                                                            </div>                                                      
                                                            <div class="col-xs-3 col-sm-3 col-md-3 text-right no-padding-r">
                                                                <button type="button" class="btn-orange2 border-btn"  id="menu-schedule-btn" data-day="<?php echo $dt->format('Y-m-d') ?>" data-type="menu">
                                                                    <img src="<?php echo get_template_directory_uri(); ?>/library/images/TimeIcon_Menu_Dropdown.png">
                                                                </button>
                                                                <ul id="open-menu-schedule">
                                                                    <li>
                                                                        <button type="button" id="all-schedule-btn" data-status="all">
                                                                            <img src="<?php echo get_template_directory_uri(); ?>/library/images/TimeIcon_Summary.png">
                                                                            All
                                                                        </button>
                                                                    </li>
                                                                    <li>
                                                                        <button type="button" id="scheduled-btn" data-status="waiting">
                                                                            <img src="<?php echo get_template_directory_uri(); ?>/library/images/TimeIcon_Scheduled.png">
                                                                            Scheduled
                                                                        </button>
                                                                    </li>
                                                                    <li>
                                                                        <button type="button" id="completed-btn" data-status="confirmed">
                                                                            <img src="<?php echo get_template_directory_uri(); ?>/library/images/TimeIcon_Completed.png">
                                                                            Completed
                                                                        </button>
                                                                    </li>
                                                                    <li>
                                                                        <button type="button" id="expired-btn" data-status="canceled">
                                                                            <img src="<?php echo get_template_directory_uri(); ?>/library/images/TimeIcon_Expired.png">
                                                                            Canceled
                                                                        </button>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div> 

                                                        <div id="list-schedule-status" class="border-selectall color-border">
                                                            <button type="button" class="list-schedule-status all-status-btn active" id="all-status-btn" data-status="all">
                                                                <img src="<?php echo get_template_directory_uri(); ?>/library/images/TimeIcon_Summary.png">
                                                                All
                                                            </button>
                                                            <span class="line-schedule-status">|</span>
                                                            <button type="button" class="list-schedule-status scheduled-status-btn" id="scheduled-status-btn" data-status="waiting">
                                                                <img src="<?php echo get_template_directory_uri(); ?>/library/images/TimeIcon_Scheduled_disable.png">
                                                                Scheduled
                                                            </button>
                                                            <span class="line-schedule-status">|</span>
                                                            <button type="button" class="list-schedule-status completed-status-btn" id="completed-status-btn" data-status="confirmed">
                                                                <img src="<?php echo get_template_directory_uri(); ?>/library/images/TimeIcon_Completed_disable.png">
                                                                Completed
                                                            </button>
                                                            <span class="line-schedule-status">|</span>
                                                            <button type="button" class="list-schedule-status expired-status-btn" id="expired-status-btn" data-status="canceled">
                                                                <img src="<?php echo get_template_directory_uri(); ?>/library/images/TimeIcon_Expired_disable.png">
                                                                Canceled
                                                            </button>
                                                        </div>  

                                                        <div class="body-my-scheduled style-scrollbar" id="body-my-scheduled">
                                                            <table class="table-status-schedule">
                                                                <tbody id="table-status-schedule">
                                                                    
                                                                </tbody>
                                                            </table>
															<div class="main-view-status" style="display: none;">
																<div class="row">
																	<div class="col-md-11">
																		<p class="name-status-schedule">
																			<img id="icon-status-schedule" src="<?php echo get_template_directory_uri(); ?>/library/images/TimeIcon_Completed.png">
																			<span></span>
																		</p>
																	</div>
																	<div class="col-md-1 text-right">
																		<img class="close-status-schedule" src="<?php echo get_template_directory_uri(); ?>/library/images/03_Close_Icon.png">
																	</div>
																</div>
																<p class="date-status-schedule">
																	<span class="label-status-schedule">Date:</span>
																	<span id="date-schedule"></span>
																</p>
																<p class="current-status-schedule">
																	<span class="label-status-schedule">Status:</span>
																	<span id="current-status"></span>
																</p>
																<p class="name-tutor-schedule">
																	<span class="label-status-schedule">Tutor:</span>
																	<span id="name-tutor-detail"></span>
																</p>
																<p class="point-status-schedule">
																	<span class="label-status-schedule">Points:</span>
																	<span id="point-schedule"></span>
																</p>
																<p class="review-status-schedule">
																	<span class="label-status-schedule">Review:</span>
																	<span id="review-schedule" class="review-schedule"></span>
																</p>
                                                                <p class="cancel-this-schedule">
                                                                    <span class="label-status-schedule">Cancel:</span>
                                                                    <span class="this-cancel">Cancel This Schedule?<span id="cancel-now">Cancel it now</span></span>
                                                                    <ul id="open-menu-cancel">
                                                                        <li>
                                                                            <button type="button" id="yes-cancel-it">
                                                                                <img src="<?php echo get_template_directory_uri(); ?>/library/images/00_Icon_Cancel_it.png">
                                                                                Yes. Cancel it
                                                                            </button>
                                                                        </li>
                                                                        <li>
                                                                            <button type="button" id="no-cancel-it">
                                                                                <img src="<?php echo get_template_directory_uri(); ?>/library/images/02_Icon_Dont_Cancel_it.png">
                                                                                No. Don’t Cancel it
                                                                            </button>
                                                                        </li>
                                                                    </ul>
                                                                </p>
																<p class="note-status-schedule">
																	<span class="label-status-schedule">Note:</span>
																</p>
																<div id="desc-class2" class="edit-description">
																	<span class="editor-top-left"></span>
																	<span class="editor-top-right"></span>
																	<span class="editor-bottom-left"></span>
																	<span class="editor-bottom-right"></span>
																	<?php
																	$editor_settings = array(
																		'wpautop' => false,
																		'media_buttons' => false,
																		'quicktags' => false,
																		'editor_height' => 50,
																		'textarea_rows' => 3,
																		'tinymce' => array(
																			'toolbar1' => 'bold,italic,strikethrough,image,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,spellchecker,fullscreen,wp_adv'
																		)
																	);
																	?>
																	<?php wp_editor('', 'note_status_schedule', $editor_settings); ?>
																	<div class="clear-both"></div>
																</div>
																<div class="row">
																	<div class="col-sm-12 col-md-12 col-xs-12">
																		<div class="form-group">
																			<button class="btn-dark-blue border-btn btn-status-schedule" type="button" name="send-tutor">
																				<?php _e('Save Note', 'iii-dictionary') ?>
																			</button>
																		</div>
																	</div>
																</div>
															</div>
                                                            <table class="table table-condensed table-tutoring">
                                                                <tbody id="table-list-schedule" class="table-list-schedule">
                                                                </tbody>
                                                            </table>
                                                            <ul id="tutoring-scheduled">
                                                                
                                                            </ul>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                        <div class="boxshadow">
                                                                
                                                        </div>
                                                     </div>
                                                     <div class="clearfix"></div>
                                                </div>

                                                <div class="main-new-request" style="display: none;">
                                                    <p class="time-request">
                                                        <span class="current-request-day"><?php echo $dt->format('F d') ?></span>
                                                        <span class="stuff-request-day">(<?php echo $dt->format('D') ?>)</span>
                                                        <span class="time-current"></span>
                                                    </p>
                                                    <p class="step-1">
                                                        <img src="<?php echo get_template_directory_uri(); ?>/library/images/icon_01.png">
                                                        Messages width Subject(s) you are looking for
                                                    </p>
                                                    <div class="row">                                                    
                                                        <div class="col-sm-6 col-md-6 col-xs-12">
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" name="title" value="" id="search-title">
                                                                <span class="placeholder"><?php _e('Title', 'iii-dictionary') ?>:</span>
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-6 col-md-6 col-xs-12 mt-top-mb-12">
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" name="title" value="<?php echo convert_timezone_to_location($u_time_zone_index) ?>" id="request-time-zone" readonly="" data-index="<?php echo $u_time_zone_index ?>" data-value="<?php echo $u_time_zone ?>">
                                                                <span class="placeholder-timezone"><?php _e('Time Zone: ', 'iii-dictionary') ?></span>
                                                            </div>
                                                        </div>                                                        
                                                    </div>

                                                    <div class="row mt-top-14 mt-bottom-5">          
                                                        <div class="col-sm-12 col-md-12 col-xs-12">
                                                            <div id="desc-class2" class="mt-bottom-10">
                                                                <span class="editor-top-left"></span>
                                                                <span class="editor-top-right"></span>
                                                                <span class="editor-bottom-left"></span>
                                                                <span class="editor-bottom-right"></span>
                                                                <?php
                                                                $editor_settings = array(
                                                                    'wpautop' => false,
                                                                    'media_buttons' => false,
                                                                    'quicktags' => false,
                                                                    'editor_height' => 50,
                                                                    'textarea_rows' => 3,
                                                                    'tinymce' => array(
                                                                        'toolbar1' => 'bold,italic,strikethrough,image,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,spellchecker,fullscreen,wp_adv'
                                                                    )
                                                                );
                                                                ?>
                                                                <?php wp_editor('', 'description_request', $editor_settings); ?>
                                                                <div class="clear-both"></div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row mt-bottom-13">
                                                        <div class="chk-subject-type mt-bottom-10 clearfix" id="checkBoxSearch" style="margin-top: 0">
                                                            <div class="cb-type3">
                                                                <label>
                                                                    <input type="radio" class="radio_buttons_search option-input-2 radio_buttons_request" value="english_writting" data-subject="english_writting" name="subject_type_search"/>
                                                                    English Writing
                                                                </label>
                                                            </div>
                                                            <div class="cb-type3">
                                                                <label>
                                                                    <input type="radio" class="radio_buttons_search required option-input-2 radio_buttons_request" value="english_conversation" data-subject="english_conversation" name="subject_type_search"/>
                                                                    English Conversation
                                                                </label>
                                                            </div>
                                                            <div class="cb-type3">
                                                                <label>
                                                                    <input type="radio"  class="radio_buttons_search required option-input-2 radio_buttons_request" value="math_elementary" data-subject="math_elementary" name="subject_type_search"/>
                                                                    Math (upto Elementary)
                                                                </label>
                                                            </div>
                                                            <div class="cb-type3">
                                                                <label>
                                                                    <input type="radio" class="radio_buttons_search required option-input-2 radio_buttons_request" value="math_any_level" data-subject="math_any_level" name="subject_type_search"/>
                                                                    Math (any level)
                                                                </label>
                                                            </div>
                                                            <div class="cb-type3">
                                                                <label>
                                                                    <input type="radio" class="radio_buttons_search required option-input-2 radio_buttons_request" value="other" data-subject="other" name="subject_type_search"/>
                                                                    Others
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-sm-6 col-md-6 col-xs-12">
                                                            <div class="form-group">
                                                                <button id="btn-find-tutor" class="btn-dark-blue border-btn" style="background: #58AEC7;" type="button" name="send-tutor">
                                                                    <?php _e('Search Tutors', 'iii-dictionary') ?>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <p class="step-2">
                                                        <img src="<?php echo get_template_directory_uri(); ?>/library/images/icon_02.png">
                                                        Select a Tutor from the list and send a request
                                                    </p>
                                                    <div class="border-selectall color-border">
                                                        <button type="button" class="btn-sub-tab" name="list-tutoring" id="btn-search-tutoring">
                                                            <img src="<?php echo get_template_directory_uri(); ?>/library/images/icon_L_list_Selected.png" alt="">List
                                                        </button>
                                                        <button type="button" class="btn-sub-tab" name="list-review" id="btn-search-review">
                                                            <img src="<?php echo get_template_directory_uri(); ?>/library/images/icon_L_Review.png" alt="">Review
                                                        </button>
                                                        <button type="button" class="btn-sub-tab" name="list-favorite" id="btn-search-favorite">
                                                            <img src="<?php echo get_template_directory_uri(); ?>/library/images/icon_L_Favorite.png" alt="">Favorites
                                                        </button>
                                                        <button type="button" class="btn-sub-tab" name="from-class" id="btn-search-fromclass">
                                                            <img src="<?php echo get_template_directory_uri(); ?>/library/images/icon_L_FromClass.png" alt="">From Class
                                                        </button>
                                                    </div>

                                                    <div class="tutoring-table">
                                                        <table>
                                                            <tbody id="table-search-tutor">
                                                                <tr>
                                                                    <td colspan="3" class="no-list">
                                                                         <img src="<?php echo get_template_directory_uri(); ?>/library/images/icon_No_Schedule.png" alt="">Currently, there are no list
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <p class="step-3">
                                                        <img src="<?php echo get_template_directory_uri(); ?>/library/images/icon_03.png">
                                                        Credits required for this Tutoring
                                                    </p>
                                                    <p class="used-points">
                                                        <?php 
                                                            $pst = mw_get_option('price_schedule_tutoring');
                                                            $pst = $pst*30/100;
                                                        ?>
                                                        <img src="<?php echo get_template_directory_uri(); ?>/library/images/icon_Points_Used.png">
                                                        <span class="number-points"><?php echo $pst ?> Points($)</span>
                                                        will be used for this Tutoring
                                                    </p>
                                                    <p class="total-points">
                                                        <?php
                                                        if($is_user_logged_in){
                                                            $user_points = get_user_meta($current_user->ID, 'user_points', true);
                                                            $user_points = empty($user_points) ? 0 : $user_points;
                                                        }else{
                                                            $user_points = 0;
                                                        }
                                                        ?>
                                                        <img src="<?php echo get_template_directory_uri(); ?>/library/images/Icon_Total_Point.png">
                                                        You have total of <span class="total-num-points"><?php echo $user_points ?> Points($)</span> Remaining. To Purchase more Points, <a href="">Click here.</a>
                                                    </p>
                                                    <div class="row">
                                                        <div class="col-sm-6 col-md-6 col-xs-12">
                                                            <div class="form-group">
                                                                <button id="btn-sent-request" class="btn-dark-blue border-btn" style="background: #65C762;" type="button" name="send-tutor">
                                                                    <?php _e('Review and Send Request', 'iii-dictionary') ?>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>                                                
                                            </div>
                                            <div id="tab-myclass" class="tab-pane fade">
                                                
                                            </div>
                                            <div id="tab-mymessage" class="tab-pane fade">
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>                 
                        </div>
                    </div>

                    <div class="modal modal-red-brown" id="top-my-schedules" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;z-index: 3000; top: 62px;">
                        <div class="modal-dialog">
                            <div class="modal-contents" style="margin-top: 0;">
                                <div class="modal-body">
                                    <div class="row color-border">
                                        <div class="col-sm-6 col-md-6 col-xs-6">
                                            <p class="name-request-vew">
                                                <span>SCHEDULES STARTER</span>
                                            </p>
                                        </div>
                                        <div class="col-sm-6 col-md-6 col-xs-6">
                                            <p class="go-to-calendar">
                                                <span class="goto-calendar">
                                                    <span>Go to Calendar</span>
                                                    <img src="<?php echo get_template_directory_uri(); ?>/library/images/icon_Goto-Schedule.png" alt="">
                                                </span>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="slide-my-schedule">
                                        <?php 
                                        $schedules = MWDB::get_my_schedules();
                                        if(count($schedules) > 0){
                                            foreach ($schedules as $k1 => $value) {
                                        ?>
                                            <div class="item" data-fromhour="<?php echo $value['fromhour'] ?>" data-fromminute="<?php echo $value['fromminute'] ?>" data-tohour="<?php echo $value['tohour'] ?>" data-tominute="<?php echo $value['tominute'] ?>" data-day="<?php echo $value['day'] ?>" data-type="<?php echo $value['totype'] ?>">
                                                <div class="description-detail">
                                                    <p class="subject-detail">
                                                        <span class="name-subject"><?php echo $value['private_subject'] ?></span>
                                                    </p>
                                                    <p class="my-time-request">
                                                        <span class="label-timezone">Date:</span>
                                                        <span class="my-current-day"><?php echo $value['date'] ?></span>
                                                        <span class="my-stuff-day"><?php echo $value['stuff'] ?>/</span>
                                                        <span class="my-time-current"><?php echo $value['time_view'] ?></span>
                                                    </p>
                                                    <p class="name-detail">
                                                        <span class="label-tutor">Tutor:</span>
                                                        <span class="name-tutor"><?php echo $value['tutor_name'] ?></span>
                                                    </p>
                                                    <p class="points-detail">
                                                        <span class="label-points">Points:</span>
                                                        <span class="name-points"><?php echo $value['total'] ?> Points($)</span>
                                                    </p>
                                                </div>
                                                <?php if($value['type_slide'] == 'current'){ ?>
                                                    <button id="btn-start-now<?php echo $value['id'] ?>" class="btn-dark-blue btn-start-now active" type="button" name="start-now" data-id="<?php echo $value['id'] ?>" data-student-id="<?php echo $value['id_user'] ?>" data-teacher-id="<?php echo $value['tutor_id'] ?>">
                                                            <?php _e('Initiate Now!', 'iii-dictionary') ?>
                                                    </button>
                                                <?php }else{ ?>
                                                    <button id="btn-cancel-schedule<?php echo $value['id'] ?>" class="btn-dark-blue btn-cancel-schedule active" type="button" name="cancel-schedule" data-id="<?php echo $value['id'] ?>" data-student-id="<?php echo $value['id_user'] ?>" data-teacher-id="<?php echo $value['tutor_id'] ?>">
                                                        <img src="<?php echo get_template_directory_uri(); ?>/library/images/icon_Most-Current-Arrow.png" alt=""> <?php _e('Most Current', 'iii-dictionary') ?>
                                                    </button>
                                                <?php } ?>
                                                <button class="cancel-now" id="cancel-now<?php echo $value['id'] ?>" data-id="<?php echo $value['id'] ?>">
                                                    <img src="<?php echo get_template_directory_uri(); ?>/library/images/close_white.png">
                                                </button>
                                            </div>
                                        <?php
                                            }
                                        }else{
                                        ?>
                                            <div class="item no-detail-schedule" data-tohour="" data-tominute="" data-day="" data-type="">
                                                <div class="description-detail">
                                                    <p class="subject-detail">
                                                        <span class="name-subject">Currently there's no schedules</span>
                                                    </p>
                                                    <p class="my-time-request">
                                                        <span class="label-timezone">Date:</span>
                                                        <span class="my-current-day">N/A</span>
                                                        <span class="my-stuff-day"></span>
                                                        <span class="my-time-current"></span>
                                                    </p>
                                                    <p class="name-detail">
                                                        <span class="label-tutor">Tutor:</span>
                                                        <span class="name-tutor">N/A</span>
                                                    </p>
                                                    <p class="points-detail">
                                                        <span class="label-points">Points:</span>
                                                        <span class="name-points">0 Points($)</span>
                                                    </p>
                                                </div>
                                                <button id="btn-cancel-schedule" class="btn-dark-blue btn-cancel-schedule no-active" type="button" name="cancel-schedule" data-id="" data-student-id="" data-teacher-id="" tabindex="0">
                                                    <?php _e('Initiate Now!', 'iii-dictionary') ?>
                                                </button>
                                            </div>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <ul id="open-menu-cancel0" class="open-menu-cancel" data-id="" style="display: none;">
                        <li>
                            <button type="button" class="yes-cancel-it" data-id="">
                                <img src="<?php echo get_template_directory_uri(); ?>/library/images/00_Icon_Cancel_it.png">
                                Yes. Cancel it
                            </button>
                        </li>
                        <li>
                            <button type="button" class="no-cancel-it" data-id="">
                                <img src="<?php echo get_template_directory_uri(); ?>/library/images/02_Icon_Dont_Cancel_it.png">
                                No. Don’t Cancel it
                            </button>
                        </li>
                    </ul>

                    <div class="warp-menu">
                        <div id="menu-account-nav" class="menu-mb">
                            <div class="slide-menu-bg"></div>
                            <div class="section-left">
                                <ul id="menu-left-myaccount" class="nav nav-tabs">
                                    <li class="active" id="account"><a data-toggle="tab" href="#hom"><img src="<?php echo get_template_directory_uri(); ?>/library/images/IconMenu_Profile.png" class="" alt="setting my account" style="width: 24px;margin:26px 0px 20px"></a>
                                    </li>
                                    <li id="itutoring"><a data-toggle="tab" href="#"><img src="<?php echo get_template_directory_uri(); ?>/library/images/IconMenu_Tutoring.png" class="" alt="setting my account" style="width: 24px;margin:15px 0px"></a>
                                    </li>
                                    <li><a data-toggle="tab" href="#"><img src="<?php echo get_template_directory_uri(); ?>/library/images/IconMenu_ClassManager.png" class="" alt="setting my account" style="width: 24px;margin:15px 0px"></a>
                                    </li>
                                    <li><a data-toggle="tab" href="#"><img src="<?php echo get_template_directory_uri(); ?>/library/images/IconMenu_Message.png" class="" alt="setting my account" style="width: 24px;margin:15px 0px"></a>
                                    </li>
                                    <li><a data-toggle="tab" href="#"><img src="<?php echo get_template_directory_uri(); ?>/library/images/IconMenu_Download.png" class="" alt="setting my account" style="width: 24px;margin:15px 0px 0"></a>
                                    </li>
                                </ul>
                            </div>

                            <div id="mySidenav" class="sidenav">
                                <ul class="nav nav-tabs none-block">
                                    <li><a class="header-menu-left" data-toggle="tab" id="myacc">My Account</a>
                                        <ul class="sub-menu-left" id="sub-myacc">
                                            <?php if (!$is_user_logged_in) { ?>
                                            <li id="sub-createacc" class="active"><a class="redirect-create" data-toggle="tab" href="#create-account">Create Basic Account</a></li>
                                            <?php } ?>
                                            <li id="sub-profile"><a class="redirect-create" data-toggle="tab" href="#profile">Profile</a></li>
                                            <li id="sub-update-info"><a class="redirect-create" data-toggle="tab" href="#updateinfo">Update My Account</a></li>
                                            <li><a class="redirect-create" data-toggle="tab" href="#subscription" id="status-history">Subscription & Points</a></li>
                                        </ul>
                                    </li>
                                    <li><a class="header-menu-left padd-adjus redirect-create" id="mtutoring" data-toggle="tab">Tutoring</a>
                                        <ul class="sub-menu-left" id="sub-tutoring">
                                            <li id="sub-findingtutor"><a class="redirect-create" data-toggle="tab" href="#tutoring-main">Find a Tutor</a></li>
                                            <li id="sub-schedule-li"><a class="redirect-create" data-toggle="tab" href="#tutoring-main">Schedule</a></li>
                                            <li id="sub-status"><a class="redirect-create" data-toggle="tab" href="#tutoring-main">Status</a></li>    
                                        </ul>
                                    </li>
                                    <li><a class="header-menu-left padd-adjus" id="class-manager">My Class</a></li>
                                    <li><a class="header-menu-left padd-adjus redirect-create" href="#"> Message</a></li>
                                    <li><a class="header-menu-left padd-adjus redirect-create" href="#">Downloads</a></li>
                                    
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <!-- End Menu My Account -->
                </div>
            </div>
        </div>
    </div>
    <div class="modal modal-red-brown" id="top-popup-message" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;z-index: 3000;">
        <div class="modal-dialog">
            <div class="modal-contents" style="margin-top: 0;">
                <div class="modal-body">
                    <div id="popup-message">

                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal modal-red-brown" id="duration-tutoring-request" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;z-index: 2999;">
        <div class="modal-dialog">
            <div class="modal-contents" style="margin-top: 0;">
                <div class="modal-body">
                    <div class="form-group">
                        <label><span id="from-time">7:00 am ~</span></label>
                        <div class="border-ras select-style time-duration">
                            <select id="time-duration" class="select-box-it form-control" name="time_duration">
                                <option value="">Select Time</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn-select-time">Select</button>
                        <button type="button" class="btn-cancel-time">Cancel</button>
                    </div>  
                </div>
            </div>
        </div>
    </div>
    <div id="container">
        <header class="header" itemscope itemtype="http://schema.org/WPHeader">
            <div class="top-nav"></div>
            <div class="main-nav-block"></div>
            <div class="container" style="position: relative">
                <div id="sub-logo">
                    <a href="https://iktutor.com" rel="nofollow" title="Innovative Knowledge">
                        <img src="<?php echo get_template_directory_uri(); ?>/library/images/ikLearn_Dark_LOGO.png" alt="">
					</a>
                </div>
                <?php 
                $URL = $_SERVER['REQUEST_URI'];
                $segment = explode('/',$URL);
                $segment_ensat = explode('=',$URL); 
                $ensat_exit = isset($segment_ensat[1])?isset($segment_ensat[1]):"";
                ?>
                <div id="header-tutor" class="cs-button-tutor">
                    <a style="text-decoration: none;" id="show-find-tutor">TUTOR</a>
                </div>

                <div id="header-sat" class="cs-button-sat <?php if(isset($segment_ensat[1]) && $segment_ensat[1]=='ensat') echo 'active' ?>">
                    <a style="text-decoration: none;" href="<?php echo site_home_url(); ?>/?r=ensat">SAT</a>
                </div>

                <div id="header-math" class="cs-button-math">
                    <a style="text-decoration: none;" href="<?php echo site_math_url();?>">MATH</a>
                </div>

                <div id="header-english" class="cs-button-english <?php if($segment[2] == 'home' || (isset($route[0]) && $route[0] != 'ensat')) echo 'active'?>">
                    <a style="text-decoration: none;" href="<?php echo site_home_url(); ?>">ENGLISH</a>
                </div>

                <?php if(defined('IK_TEST_SERVER')) : ?>
                <div style="position: absolute;left: 240px;top: 5px">
                    <h2 style="margin: 0px;color: #fff;font-style: italic;text-shadow: 1px 1px #000">Test Site</h2>
                </div>
                <?php endif ?>
                <?php
                if($segment[2] == 'home'){
                        MWHtml::sel_lang_switcher();
                }elseif(isset($segment_ensat[1]) && $segment_ensat[1]=='ensat'){
                        MWHtml::sel_lang_switcher(2);
                }
                else{
                        MWHtml::sel_lang_switcher(1);
                }
                ?>
                <?php if ($is_user_logged_in){ ?>
                <ul id="user-nav" class="css-pad-log">
                <?php }else{ ?>
                <ul id="user-nav" class="css-pad-nolog">
                <?php } ?>
                    <?php 
                    if ($is_user_logged_in) : 
                        $ru_display_name = get_user_meta($current_user->ID, 'display_name', true);
                        $ru_first_name = get_user_meta($current_user->ID, 'first_name', true);
                        $ru_last_name = get_user_meta($current_user->ID, 'last_name', true);
                        $row_user = get_user_by('id', $current_user->ID);
                        if (!empty($ru_display_name) && $ru_display_name != '')
                            $ruser_name = $display_name;
                        else if((!empty($ru_first_name) && $ru_first_name != '') || (!empty($ru_last_name) && $ru_last_name != ''))
                            $ruser_name = $ru_first_name.' '.$ru_last_name;
                        else
                            $ruser_name = $row_user->display_name;
                    ?>
                    <li><a class="display-name" href="<?php echo locale_home_url() ?>/?r=my-account">[<?php echo $ruser_name; ?>]</a></li>
                    <?php endif ?>
                    <li><a class="shopping-cart" href="<?php echo locale_home_url() ?>/?r=payments" title="<?php _e('Shopping Cart', 'iii-dictionary') ?>"><span class="icon-cart3"></span>(<?php echo count($cart_items) ?>)</a></li>
                    <?php if (!$is_user_logged_in) : ?>
                    <li class="css-li-login">
                        <a id="show_login" title="<?php _e('Login', 'iii-dictionary') ?>">
                        <?php _e('Login', 'iii-dictionary') ?><span class="login-icon"></span></a>
                    </li>
                    <li class="">
                        <a class="sign-up-link" id="show_signup" title="<?php _e('Sign-up', 'iii-dictionary') ?>">
                            <?php _e('Sign-up', 'iii-dictionary') ?><span class="signup-icon"></span></a>
                    </li>
                    <?php else : ?>
                    <li class="css-li-logout">
                        <a class="logout-link" href="<?php echo wp_logout_url(home_url()) ?>" title="<?php _e('Logout', 'iii-dictionary') ?>">
                            <?php _e('Logout', 'iii-dictionary') ?><span class="logout-icon"></span></a>
                    </li>
                    <?php endif ?>
                    <li id="icon-home-hidden">
                        <a href="<?php echo site_home_url(); ?>/home" title="<?php _e('Home', 'iii-dictionary') ?>">
                            <?php _e('Home', 'iii-dictionary') ?><span class="home-icon"></span></a>
                    </li>
                </ul>

                <div id="btn-main-menu" class="btn-menu-collapse"></div>

                <div id="main-nav" class="row">
                    <div class="menu-new-head">
                        <div class="btn-menu-sat"><a href="<?php echo site_home_url().'/?r=ensat'?>" style="color:#bd5454">SAT</a></div>
                        <div class="btn-menu-math"><a href="<?php echo site_math_url()?>" style="color:#449f7e">MATH</a></div>
                        <div class="btn-menu-english"><a href="<?php echo site_home_url()?>" style="color:#488aca">ENGLISH</a></div>
                    </div>
                    <nav class="navbar navbar-default">
                        <?php 
                            wp_nav_menu(array(
                                'container' => false,                           // remove nav container
                                'container_class' => '',                 // class of container (should you choose to use it)
                                'menu' => 'Dictionary Menu',  // nav name
                                'menu_class' => 'main-menu nav navbar-nav',               // adding custom nav class
                                'theme_location' => 'dictionary-nav',                 // where it's located in the theme
                                'before' => '',                                 // before the menu
                                'after' => '',                                  // after the menu
                                'link_before' => '',                            // before each link
                                'link_after' => '',                             // after each link
                                'depth' => 0,                                   // limit the depth of the nav
                                'fallback_cb' => ''                             // fallback function (if there is one)
    						)); 
                        ?>
                    </nav>

                    <!--<div id="btn-sub-menu" class="btn-menu-collapse"></div>-->

                    <nav class="navbar navbar-default" id="sub-user-nav">
                        <?php 
                            wp_nav_menu(array(
                                'container' => false,                           // remove nav container
                                'container_class' => '',                 // class of container (should you choose to use it)
                                'menu' => 'Function Menu',  // nav name
                                'menu_class' => 'user-nav nav navbar-nav',               // adding custom nav class
                                'theme_location' => 'user-nav-ensat',                 // where it's located in the theme
                                'before' => '',                                 // before the menu
                                'after' => '',                                  // after the menu
                                'link_before' => '',                            // before each link
                                'link_after' => '',                             // after each link
                                'depth' => 0,                                   // limit the depth of the nav
                                'fallback_cb' => '',                             // fallback function (if there is one)
                                'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>'
    						)); 
                        ?>
                    </nav>
                    <!-- <li><a href="http://ikteach.com/en" target="_blank" >'.__('Teacher','iii-dictionary').'</a></li> -->

                    <nav class="navbar navbar-default" id="lang-switcher-nav">
                        <?php 
                            wp_nav_menu(array(
                                'container' => false,                           // remove nav container
                                'container_class' => '',                 // class of container (should you choose to use it)
                                'menu_class' => 'menu-lang-switcher nav navbar-nav',               // adding custom nav class
                                'theme_location' => 'lang-switcher-nav',                 // where it's located in the theme
                                'before' => '',                                 // before the menu
                                'after' => '',                                  // after the menu
                                'link_before' => '',                            // before each link
                                'link_after' => '',                             // after each link
                                'depth' => 0,                                   // limit the depth of the nav
                                'fallback_cb' => ''                             // fallback function (if there is one)
    						)); 
                        ?>
                    </nav>
                </div>

                <!-- Menu horizotal    -->
                <nav class="navbar navbar-default" id="menu-horizontal-english">
                    <?php 
                        if(isset($segment_ensat[1]) && $segment_ensat[1]=='ensat'){
                            wp_nav_menu(array(
                                'container' => false,                           // remove nav container
                                'container_class' => '',                 // class of container (should you choose to use it)
                                'menu' => 'Dictionary Menu',  // nav name
                                'menu_class' => 'main-menu ensat-english nav navbar-nav',               // adding custom nav class
                                'theme_location' => 'ensat-nav-english',                 // where it's located in the theme
                                'before' => '',                                 // before the menu
                                'after' => '',                                  // after the menu
                                'link_before' => '',                            // before each link
                                'link_after' => '',                             // after each link
                                'depth' => 0,                                   // limit the depth of the nav
                                'fallback_cb' => ''                             // fallback function (if there is one)
                            ));
                        }else{
                            wp_nav_menu(array(
                                'container' => false,                           // remove nav container
                                'container_class' => '',                 // class of container (should you choose to use it)
                                'menu' => 'Dictionary Menu',  // nav name
                                'menu_class' => 'main-menu nav navbar-nav',               // adding custom nav class
                                'theme_location' => 'dictionary-nav',                 // where it's located in the theme
                                'before' => '',                                 // before the menu
                                'after' => '',                                  // after the menu
                                'link_before' => '',                            // before each link
                                'link_after' => '',                             // after each link
                                'depth' => 0,                                   // limit the depth of the nav
                                'fallback_cb' => ''                             // fallback function (if there is one)
                            )); 
                        }
                    ?>
                </nav>
                <!-- Sub Menu horizotal    -->
                <nav class="navbar navbar-default" id="sub-menu-horizontal-english">
                    <?php 
                        if(isset($segment_ensat[1]) && $segment_ensat[1]=='ensat'){
                            wp_nav_menu(array(
                                'container' => false,                           // remove nav container
                                'container_class' => '',                 // class of container (should you choose to use it)
                                'menu' => 'Function Menu',  // nav name
                                'menu_class' => 'user-nav nav navbar-nav',               // adding custom nav class
                                'theme_location' => 'uensat-nav-english',                 // where it's located in the theme
                                'before' => '',                                 // before the menu
                                'after' => '',                                  // after the menu
                                'link_before' => '',                            // before each link
                                'link_after' => '',                             // after each link
                                'depth' => 0,                                   // limit the depth of the nav
                                'fallback_cb' => '',                             // fallback function (if there is one)
                                'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>'
                            )); 
                        }else{
                            wp_nav_menu(array(
                                'container' => false,                           // remove nav container
                                'container_class' => '',                 // class of container (should you choose to use it)
                                'menu' => 'Function Menu',  // nav name
                                'menu_class' => 'user-nav nav navbar-nav',               // adding custom nav class
                                'theme_location' => 'user-nav-ensat',                 // where it's located in the theme
                                'before' => '',                                 // before the menu
                                'after' => '',                                  // after the menu
                                'link_before' => '',                            // before each link
                                'link_after' => '',                             // after each link
                                'depth' => 0,                                   // limit the depth of the nav
                                'fallback_cb' => '',                             // fallback function (if there is one)
                                'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>'
                            )); 
                        }
                    ?>
                </nav>
            </div>
            <div class="">
                <?php get_template_part('ajax', 'auth'); ?>
            </div>
        </header>
        <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/library/moment/min/moment.min.js"></script>
        <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/library/moment/min/moment-with-locales.min.js"></script>
        <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/library/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
        <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/bootstrap-datetimepicker/css/bootstrap-datetimepicker.css" />
        <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/library/moment/min/moment-timezone-with-data.js"></script>
        <link href="<?php echo get_template_directory_uri(); ?>/library/slick/slick.css" rel="stylesheet">
        <link href="<?php echo get_template_directory_uri(); ?>/library/slick/slick-theme.css" rel="stylesheet">
        <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/library/slick/slick.min.js"></script>
        <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/library/js/detect-zoom.js"></script>
        <!-- <link href="<?php echo get_template_directory_uri(); ?>/library/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet" >
        <script src="<?php echo get_template_directory_uri(); ?>/library/bootstrap-datepicker/js/bootstrap-datepicker.js"></script> -->
        <script type="text/javascript">
            (function ($) {
                $(function () {
                    var availability_checking = false;
                    var redirect = '<?php echo isset($redirect)?$redirect:"" ?>';
                    var interval, intervalmy, intervalcity;
                    var arr_tutor = [];
                    var ref = '<?php echo $_REQUEST['ref'] ?>';
                    var route = '<?php echo isset($route[0])?$route[0]:'' ?>';
                    $("#check-availability").click(function (e) {
                        e.preventDefault();
                        if (availability_checking) {
                            return;
                        }
                        var tthis = $(this);
                        var viewport = getViewport(); 
                        var check_yes = $("#checked-availability").hasClass('yes-availability'); 
                        var check_no = $("#checked-availability").hasClass('not-availability');
                        var check_mb = $("#checked-availability").hasClass('available-mb');
                        if(viewport.width < 650){
                            if(!check_mb){
                                $("#checked-availability").addClass('available-mb');
                            }
                        }else{
                            if(check_mb){
                                $("#checked-availability").removeClass('available-mb');
                            }
                        }
                        if(check_yes){
                            $("#checked-availability").removeClass('yes-availability');
                        }
                        if(check_no){
                            $("#checked-availability").removeClass('not-availability');
                        }
                        $("#checked-availability span").text('');
                        var user_login = $("#user_login_signup").val().trim();
                        if (user_login != "") {
                            //tthis.popover("destroy");
                            availability_checking = true;
                            //tthis.find(".icon-loading").fadeIn();
                            $.getJSON(home_url + "/?r=ajax/availability/user", {user_login: user_login}, function (data) {
                                if (isValidEmail(user_login)) {
                                    if (data [0] == 0) {                                            
                                        $("#checked-availability").addClass('not-availability');
                                        $("#checked-availability span").text('Not Available');    
                                    } else {
                                        $("#checked-availability").addClass('yes-availability');
                                        $("#checked-availability span").text('Available');
                                    }
                                } else {
                                    if(check_yes){
                                        $("#checked-availability").removeClass('yes-availability');
                                    }
                                    if(check_no){
                                        $("#checked-availability").removeClass('not-availability');
                                    }
                                    $("#checked-availability span").text('');
                                }
                                availability_checking = false;
                            });
                        }
                    });

                    $('.logout-link').click(function (e) {
                        localStorage.clear();
                    });

                    $('.close-modal-account').click(function () {
                        $("#my-account-modal").modal('hide');
                        $(".sub-menu-left li").not("#sub-myacc li").removeClass("active");
                        if(ref == "notepad"){
                            window.history.replaceState(null, null, window.location.pathname);
                        } 
                    });

                    $(".view-my-account").click(function () {
                        $("#my-account-modal").modal('show');
                        var name = $(".display-name").text();
                        if (name !== '') {
                            $("#sub-createacc").removeClass("active");
                            $("#sub-profile").addClass("active");
                            $("#create-account").removeClass("active");
                            $("#create-account").removeClass("in");
                            $("#login-user").removeClass("active");
                            $("#login-user").removeClass("in");
                            $("#profile").addClass("active");
                            $("#profile").addClass("in");
                            $("#tutoring-main").removeClass("active");
                            $("#tutoring-main").removeClass("in");
                            $("#subscription").removeClass("active");
                            $("#subscription").removeClass("in");
                            $("#updateinfo").removeClass("active");
                            $("#updateinfo").removeClass("in");

                            $("#sub-myacc").css("display", "block");
                            $("#sub-myacc").addClass("opensub");
                            $("#sub-tutoring").css("display", "none");
                            $("#sub-tutoring").removeClass("opensub");
                            $("#sub-profile").addClass("active");
                        }else{
                            $("#login-user").removeClass("hidden");
                            $("#login-user").addClass("active");
                            $("#login-user").addClass("in");
                            $("#lost-password").removeClass("active");
                            $("#lost-password").removeClass("in");
                            $("#lost-password").addClass("hidden");
                            $("#create-account").removeClass("active");
                            $("#create-account").removeClass("in");
                            $("#tutoring-main").removeClass("active");
                            $("#tutoring-main").removeClass("in");
                            $("#subscription").removeClass("active");
                            $("#subscription").removeClass("in");
                            $("#updateinfo").removeClass("active");
                            $("#updateinfo").removeClass("in");
                        }
                    });

                    $("#show_signup").click(function () {
                        $("#my-account-modal").modal('show');
                        $("#login-user").removeClass("active");
                        $("#login-user").removeClass("in");
                        $("#lost-password").removeClass("active");
                        $("#lost-password").removeClass("in");
                        $("#create-account").addClass("active");
                        $("#create-account").addClass("in");

                        var img = '<?php echo get_template_directory_uri() ?>/library/images/Profile_Image.png';
                        $("#user-upload-avatar").attr('src',img);

                        $('span.placeholder').each(function () {
                            var text = $(this).text();
                            var font = $(this).css("font");
                            if(text == 'Year:'){
                                var offset = 11;
                            }
                            else{
                                if (document.documentMode || /Edge/.test(navigator.userAgent)) {
                                    //console.log("Edge");
                                    var offset = getDistancePlace(text, "Edge");
                                }else if (navigator.userAgent.search("Firefox") >= 0){
                                    var offset = getDistancePlace(text, "Firefox");
                                    //console.log("Firefox");
                                }else{
                                    var offset = 18;
                                }
                            }
                            var left = (getTextWidth(text,font) + offset);
                            $(this).prev().css("padding-left",left+"px");
                        });
                    });

                    if(ref == 'notepad'){
                        $("#my-account-modal").modal('show');
                        $("#login-user").removeClass("active");
                        $("#login-user").removeClass("in");
                        $("#lost-password").removeClass("active");
                        $("#lost-password").removeClass("in");
                        $("#create-account").addClass("active");
                        $("#create-account").addClass("in");

                        var img = '<?php echo get_template_directory_uri() ?>/library/images/Profile_Image.png';
                        $("#user-upload-avatar").attr('src',img);

                        $('span.placeholder').each(function () {
                            var text = $(this).text();
                            var font = $(this).css("font");
                            if(text == 'Year:'){
                                var offset = 11;
                            }
                            else{
                                if (document.documentMode || /Edge/.test(navigator.userAgent)) {
                                    //console.log("Edge");
                                    var offset = getDistancePlace(text, "Edge");
                                }else if (navigator.userAgent.search("Firefox") >= 0){
                                    var offset = getDistancePlace(text, "Firefox");
                                    //console.log("Firefox");
                                }else{
                                    var offset = 18;
                                }
                            }
                            var left = (getTextWidth(text,font) + offset);
                            $(this).prev().css("padding-left",left+"px");
                        });
                    }

                    $("#show-find-tutor").click(function () {
                        $("#my-account-modal").modal('show');
                        var name = $(".display-name").text();
                        if (name !== '') {
                            $("#sub-createacc").removeClass("active");
                            $("#sub-profile").addClass("active");
                            $("#create-account").removeClass("active");
                            $("#create-account").removeClass("in");
                            $("#login-user").removeClass("active");
                            $("#login-user").removeClass("in");
                            $("#profile").removeClass("active");
                            $("#profile").removeClass("in");
                            $("#tutoring-main").addClass("active");
                            $("#tutoring-main").addClass("in");
                            $("#subscription").removeClass("active");
                            $("#subscription").removeClass("in");
                            $("#updateinfo").removeClass("active");
                            $("#updateinfo").removeClass("in");

                            $("#sub-myacc").css("display", "none");
                            $("#sub-myacc").removeClass("opensub");
                            $("#sub-tutoring").css("display", "none");
                            $("#sub-tutoring").removeClass("opensub");
                            $("#sub-findingtutor").addClass("active");

                            var path = '<?php echo get_template_directory_uri() ?>/library/images/';  
                            $('.writting-review').css("display","none");
                            $('.toggle-btn').css("display","none");
                            
                            var available_time = $("#mytime-clock").attr("data-available-time");
                            $("#select-available-time").selectBoxIt('selectOption',available_time.toString()).data("selectBox-selectBoxIt");
                            $("#select-available-time").data("selectBox-selectBoxIt").refresh();

                            if(!$('#btn-find-tutoring').hasClass('active')){
                                $('.btn-sub-tab').removeClass('active');
                                $('#btn-find-tutoring').addClass('active');
                                $('#btn-find-tutoring').find('img').attr('src',path + 'icon_Find.png');
                                $('#btn-list-review').find('img').attr('src',path + 'icon_L_Review.png');
                                $('#btn-list-favorite').find('img').attr('src',path + 'icon_L_Favorite.png');
                                $('#btn-list-tutoring').find('img').attr('src',path + 'icon_L_list.png');
                                $('#btn-available-now').find('img').attr('src',path + '04_Available_Now.png');
                            }

                            $('.radio_tutor_search').attr('checked',false);
                            $('.frm-available-now').css("display","block");
                            $('#table-detail-tutor').css("display","none");
                            $(".slide-resume").css('visibility','hidden');
                            var tr = '<tr><td class="no-results"><img src="' + path + 'icon_Not_Available.png" alt="">Currently, there are no results.</td></tr>';
                            $('#table-list-tutor').html(tr);
                        }else{
                            $("#login-user").removeClass("hidden");
                            $("#login-user").addClass("active");
                            $("#login-user").addClass("in");
                            $("#lost-password").removeClass("active");
                            $("#lost-password").removeClass("in");
                            $("#lost-password").addClass("hidden");
                            $("#create-account").removeClass("active");
                            $("#create-account").removeClass("in");
                            $("#tutoring-main").removeClass("active");
                            $("#tutoring-main").removeClass("in");
                            $("#subscription").removeClass("active");
                            $("#subscription").removeClass("in");
                            $("#updateinfo").removeClass("active");
                            $("#updateinfo").removeClass("in");
                        }
                    });

                    $("#show_login").click(function () {
                        $("#my-account-modal").modal('show');
                        $("#login-user").removeClass("hidden");
                        $("#login-user").addClass("active");
                        $("#login-user").addClass("in");
                        $("#lost-password").removeClass("active");
                        $("#lost-password").removeClass("in");
                        $("#lost-password").addClass("hidden");
                        $("#create-account").removeClass("active");
                        $("#create-account").removeClass("in");
                    });

                    $(".sign-up").click(function () {
                        $("#login-user").removeClass("active");
                        $("#login-user").removeClass("in");
                        $("#lost-password").removeClass("active");
                        $("#lost-password").removeClass("in");
                        $("#create-account").addClass("active");
                        $("#create-account").addClass("in");

                        var img = '<?php echo get_template_directory_uri() ?>/library/images/icon_Tutor_ID.png';
                        $("#user-upload-avatar").attr('src',img);

                        $('span.placeholder').each(function () {
                            var text = $(this).text();
                            var font = $(this).css("font");
                            if(text == 'Year:'){
                                var offset = 11;
                            }
                            else{
                                if (document.documentMode || /Edge/.test(navigator.userAgent)) {
                                    //console.log("Edge");
                                    var offset = getDistancePlace(text, "Edge");
                                }else if (navigator.userAgent.search("Firefox") >= 0){
                                    var offset = getDistancePlace(text, "Firefox");
                                    //console.log("Firefox");
                                }else{
                                    var offset = 18;
                                }
                            }
                            var left = (getTextWidth(text,font) + offset);
                            $(this).prev().css("padding-left",left+"px");
                        });
                    });

                    $('#my-account-modal').on('show.bs.modal', function (e)
                    {
                        if($('#create-class').hasClass('active'))
                            $('#close-modal').attr('data-tab','create-class');
                    });

                    $("#account").click(function () {
                        $("#my-timezone").css("display","none");
                        var name = $(".display-name").text();
                        var viewport = getViewport();  
                        if(viewport.width < 650){
                            var check = $("#menu-account-nav").hasClass("open");
                        }else{
                            if($('body').hasClass('open-myschedule')){
                                var check = $("#menu-account-nav").hasClass("open");
                            }else{
                                var check = $("#mySidenav").hasClass("open");
                            }
                        }
                        if (name !== '') {
                            $("#sub-myacc").css("display", "block");
                            $("#sub-myacc").addClass("opensub");
                            $("#sub-tutoring").css("display", "none");
                            $("#sub-tutoring").removeClass("opensub");

                            if (check) {
                                //closeNav();
                                $('#menu-left-myaccount li:nth-child(2)').css("margin-top", "88px");
                                $('#menu-left-myaccount li:nth-child(3)').css("margin-top", "2px");
                                $('#menu-left-myaccount li:nth-child(4)').css("margin-top", "4px");
                                $('#menu-left-myaccount li:nth-child(5)').css("margin-top", "8px");
                            } else {
                                openNav();
                                $('#menu-left-myaccount li:nth-child(2)').css("margin-top", "88px");
                                $('#menu-left-myaccount li:nth-child(3)').css("margin-top", "2px");
                                $('#menu-left-myaccount li:nth-child(4)').css("margin-top", "4px");
                                $('#menu-left-myaccount li:nth-child(5)').css("margin-top", "8px");
                            }
                        }
                    });                    

                    $("#itutoring").click(function () {
                        $("#my-timezone").css("display","none");
                        var name = $(".display-name").text();
                        var viewport = getViewport();  
                        if(viewport.width < 650){
                            var check = $("#menu-account-nav").hasClass("open");
                        }else{
                            if($('body').hasClass('open-myschedule')){
                                var check = $("#menu-account-nav").hasClass("open");
                            }else{
                                var check = $("#mySidenav").hasClass("open");
                            }
                        }
                        if (name !== '') {
                            $("#sub-tutoring").css("display", "block");
                            $("#sub-tutoring").addClass("opensub");
                            $("#sub-myacc").css("display", "none");
                            $("#sub-myacc").removeClass("opensub");
                            
                            if (check) {
                                //closeNav();
                                $('#menu-left-myaccount li:nth-child(2)').css("margin-top", "-7px");
                                $('#menu-left-myaccount li:nth-child(3)').css("margin-top", "96px");
                                $('#menu-left-myaccount li:nth-child(4)').css("margin-top", "4px");
                                $('#menu-left-myaccount li:nth-child(5)').css("margin-top", "8px");
                            } else {
                                openNav();
                                $('#menu-left-myaccount li:nth-child(2)').css("margin-top", "-7px");
                                $('#menu-left-myaccount li:nth-child(3)').css("margin-top", "96px");
                                $('#menu-left-myaccount li:nth-child(4)').css("margin-top", "4px");
                                $('#menu-left-myaccount li:nth-child(5)').css("margin-top", "8px");
                            }
                        }
                    });

                    $("#menu_Taggle ,#btn-schedule, #btn-tutor").click(function () {
                        $("#my-timezone").css("display","none");
                        $("#open-menu-quicknotifi").css("display","none");
                        var name = $(".display-name").text();
                        var viewport = getViewport();  
                        if(viewport.width < 650){
                            var check = $("#menu-account-nav").hasClass("open");
                        }else{
                            if($('body').hasClass('open-myschedule')){
                                var check = $("#menu-account-nav").hasClass("open");
                            }else{
                                var check = $("#mySidenav").hasClass("open");
                            }
                        }
                        if (name !== '') {
                            if (check) {
                                closeNav();
                            } else {
                                openNav();
                                if ($("#sub-tutoring").hasClass("opensub")) {                                    
                                    $("#sub-myacc").css("display", "none");
                                    $("#sub-myacc").removeClass("opensub");
                                    $('#menu-left-myaccount li:nth-child(2)').css("margin-top", "-7px");
                                    $('#menu-left-myaccount li:nth-child(3)').css("margin-top", "96px");
                                    $('#menu-left-myaccount li:nth-child(4)').css("margin-top", "4px");
                                    $('#menu-left-myaccount li:nth-child(5)').css("margin-top", "8px");
                                }else{
                                    $("#sub-tutoring").css("display", "none");
                                    $("#sub-tutoring").removeClass("opensub");
                                    $('#menu-left-myaccount li:nth-child(2)').css("margin-top", "88px");
                                    $('#menu-left-myaccount li:nth-child(3)').css("margin-top", "2px");
                                    $('#menu-left-myaccount li:nth-child(4)').css("margin-top", "4px");
                                    $('#menu-left-myaccount li:nth-child(5)').css("margin-top", "8px");
                                }
                            }
                        }
                    });

                    $("#mySidenav a").click(function () {
                        closeNav();
                        $('#menu-left-myaccount li:nth-child(2)').css("margin-top", "0px");
                    });

                    $("#mtutoring").click(function () {
                        $("#sub-tutoring").css("display", "block");
                        $("#sub-tutoring").addClass("opensub");
                        $("#sub-myacc").css("display", "none");
                        $("#sub-myacc").removeClass("opensub");

                        var viewport = getViewport();  
                        if(viewport.width < 650){
                            var check = $("#menu-account-nav").hasClass("open");
                        }else{
                            if($('body').hasClass('open-myschedule')){
                                var check = $("#menu-account-nav").hasClass("open");
                            }else{
                                var check = $("#mySidenav").hasClass("open");
                            }
                        }

                        if (check) {
                            closeNav();
                            $('#menu-left-myaccount li:nth-child(2)').css("margin-top", "0px");
                        } else {
                            openNav();
                            $('#menu-left-myaccount li:nth-child(2)').css("margin-top", "-7px");
                            $('#menu-left-myaccount li:nth-child(3)').css("margin-top", "96px");
                            $('#menu-left-myaccount li:nth-child(4)').css("margin-top", "4px");
                            $('#menu-left-myaccount li:nth-child(5)').css("margin-top", "8px");
                        }
                    });

                    $("#myacc").click(function () {
                        $("#sub-myacc").css("display", "block");
                        $("#sub-myacc").addClass("opensub");
                        $("#sub-tutoring").css("display", "none");
                        $("#sub-tutoring").removeClass("opensub");

                        var viewport = getViewport();  
                        if(viewport.width < 650){
                            var check = $("#menu-account-nav").hasClass("open");
                        }else{
                            if($('body').hasClass('open-myschedule')){
                                var check = $("#menu-account-nav").hasClass("open");
                            }else{
                                var check = $("#mySidenav").hasClass("open");
                            }
                        }

                        if (check) {
                            closeNav();
                            $('#menu-left-myaccount li:nth-child(2)').css("margin-top", "0px");
                        } else {
                            openNav();
                            $('#menu-left-myaccount li:nth-child(2)').css("margin-top", "88px");
                            $('#menu-left-myaccount li:nth-child(3)').css("margin-top", "2px");
                            $('#menu-left-myaccount li:nth-child(4)').css("margin-top", "4px");
                            $('#menu-left-myaccount li:nth-child(5)').css("margin-top", "8px");
                        }
                    });                    
                    
                    $("#sub-myacc li").click(function () {
                        $(".sub-menu-left li").not("#sub-myacc li").removeClass("active");
                    });

                    $("#sub-tutoring li").click(function () {
                        $(".sub-menu-left li").not("#sub-tutoring li").removeClass("active");
                    });

                    $(".forgot-pass").click(function () {
                        $("#login-user").addClass("hidden");
                        $("#create-account").removeClass("active");
                        $("#create-account").removeClass("in");
                        $("#login-user").removeClass("active");
                        $("#login-user").removeClass("in");
                        $("#lost-password").removeClass("hidden");
                        $("#lost-password").addClass("active");
                        $("#lost-password").addClass("in");
                    });

                    $('#btn-login').click(function () {
                        var username = $('#username-login').val();
                        var password = $('#password-login').val();

                        if(route == '0' || route == 'login' || route == 'signup')
                            var redirect = '<?php echo locale_home_url(); ?>';
                        else
                            var redirect = '';

                        $.post(home_url + "/?r=ajax/login_account", {
                            user_name: username,
                            user_password: password
                         }, function (data) {
                            if ($.trim(data) == '1') {
                                if (redirect == '')
                                    window.location.reload();
                                else
                                    document.location.href = redirect;
                            } else {
                                $('#popup-message').html('<p class="text-used">' + data + '</p><button id="got-it" type="button" class="btn-orange form-control nopadding-r border-btn">OK</button>');
                                $('#top-popup-message').css("display", "block");
                            }
                        });
                    });

                    $('#create-acc').click(function () {
                        $('#create-acc').attr("disabled", true);
                        var user_name = $('#user_login_signup').val();
                        var user_password = $('#user_password_signup').val();
                        var confirm_password = $('#confirm_password').val();
                        var first_name = $('#first_name_signup').val();
                        var last_name = $('#last_name_signup').val();
                        var birth_m = $("#birth_mSelectBoxItText").attr("data-val");
                        var birth_d = $("#birth_dSelectBoxItText").attr("data-val");
                        var birth_y = $('#birth_y').val();
                        var profile_avatar = $('#profile-avatar').val();
                        var time_zone = $('#user-time-zone :selected').attr("data-value");
                        var time_zone_index = $("#user-time-zoneSelectBoxItText").attr("data-val");
                        var cb_lang = [];
                        $('input[name="cb-lang"]:checked').each(function () {
                            var val = this.value;
                            if(val == '') var val = $(this).attr('data-lang');
                            cb_lang.push(val);
                        });
                        var viewport = getViewport();
                        if(viewport.width < 650){
                            var gender = $("#birth-g_mbSelectBoxItText").attr("data-val");
                        }else{
                            var gender = $("#birth_g_pcSelectBoxItText").attr("data-val");
                        }
                        $.post(home_url + "/?r=ajax/create_account", {
                            user_name: user_name,
                            user_password: user_password,
                            confirm_password: confirm_password,
                            first_name: first_name,
                            last_name: last_name,
                            birth_m: birth_m,
                            birth_d: birth_d,
                            birth_y: birth_y,
                            cb_lang: cb_lang,
                            profile_avatar: profile_avatar,
                            gender: gender,
                            time_zone: time_zone,
                            time_zone_index: time_zone_index
                        }, function (data) {
                            $('#create-acc').attr("disabled", false);
                            if ($.trim(data) == '1') {
                                $('#popup-message').html('<p class="text-used">Basic Account has been created successfully</p><button id="got-home" type="button" class="btn-orange form-control nopadding-r border-btn">Got it</button>');
                                $('#top-popup-message').css("display", "block");                                    
                            } else {
                                $('#popup-message').html('<p class="text-used">' + data + '</p><button id="got-it" type="button" class="btn-orange form-control nopadding-r border-btn">OK</button>');
                                $('#top-popup-message').css("display", "block");
                            }
                        });
                    });

                    $('.cancel-update-teacher').click(function () {
                        var id = $(this).attr('data-id');
                        var tab = $(this).attr('data-tab');
                        $('#profile').addClass('active in');
                        $('#sub-profile').addClass('active');
                        $("#" + tab).removeClass("active in");
                        $("#" + id).removeClass("active");
                    });

                    $('#update-teacher').click(function () {
                        $("#update-teacher").attr("disabled", true);
                        var check_student = $('#chk-tutor-teacher').val();
                        var check_gender = $('#chk-user-gender').val();

                        //var user_email = $('#update_username').val();
                        var new_password = $('#update_password').val();
                        var retype_new_password = $('#update_confirmpass').val();
                        var first_name = $('#update_first_name').val();
                        var last_name = $('#update_last_name').val();
                        var birth_y = $('#update_birth_y').val();

                        var time_zone = $('#update-time-zone :selected').attr("data-value");
                        var name = $('#update-time-zone :selected').attr("data-name");
                        var time_zone_index = $("#update-time-zoneSelectBoxItText").attr("data-val");
                        var city = $('#update-time-zone :selected').attr("data-city");

                        var mobile_number = $('#mobile-number-update').val();
                        var last_school = $('#last-school-update').val();
                        var previous_school = $('#previous-school-update').val();
                        var skype_id = $('#skype-update').val();
                        var user_profession = $('#profession-update').val();

                        var subject_description = $('#description-update').val();
                        var school_name = $('#school-name-update').val();
                        var teaching_link = $('#teaching-link-update').val();

                        var teaching_subject = $('#subject-update').val();
                        var student_link = $('#student-link-update').val();
                        var user_years = $('#years-update').val();
                        var school_attend = $('#school-attend-update').val();
                        var user_gpa = $('#gpa-update').val();
                        var user_major = $('#major-update').val();

                        var school_name1 = $('#school-name1-update').val();
                        var school_name2 = $('#school-name2-update').val();
                        var school_link1 = $('#school-link1-update').val();
                        var school_link2 = $('#school-link2-update').val();
                        var any_other = $('#any-other-update').val();
                        var user_grade = $("#grade-updateSelectBoxItText").attr("data-val");
                        var desc_tell_me = $('#desc_tell_update_ifr').contents().find('#tinymce').text();

                        var birth_m = $("#update_birth_mSelectBoxItText").attr("data-val");
                        var birth_d = $("#update_birth_dSelectBoxItText").attr("data-val");
                        var gender = '';
                        if(check_gender == '' || $.trim(check_gender) == ''){
                            var viewport = getViewport();
                            if(viewport.width < 650){
                                var gender = $("#update_birth_g_mbSelectBoxItText").attr("data-val");
                            }else{
                                var gender = $("#update_birth_g_pcSelectBoxItText").attr("data-val");
                            }
                        }else{
                            gender = check_gender;
                        }

                        var cb_lang = [];
                        var subject_type = [];
                        var profile_avatar = $('#profile-value').val();
                        $('input[name="update-cb-lang"]:checked').each(function () {
                            cb_lang.push(this.value);
                        });
                        $('input[name="subject_type_update"]:checked').each(function () {
                            var val = this.value;
                            if(val == '') var val = $(this).attr('data-subject');
                            subject_type.push(val);
                        });

                        var msg = '';
                        var form_valid = true;
                        if(check_student == 1){                                
                            if(mobile_number == '' || $.trim(mobile_number) == ''){
                                msg += 'Please enter Mobile Number</br>';
                                form_valid = false;
                            }
                            if(last_school == '' || $.trim(last_school) == ''){
                                msg += 'Please enter Last School Attended</br>';
                                form_valid = false;
                            }
                            if(user_profession == '' || $.trim(user_profession) == ''){
                                msg += 'Please enter Profession</br>';
                                form_valid = false;
                            }
                            if(desc_tell_me == '' || $.trim(desc_tell_me) == ''){
                                msg += 'Please enter Short description</br>';
                                form_valid = false;
                            }
                            if(subject_type.length == 0){
                                msg += 'Please check the box of Subjects you Interested</br>';
                                form_valid = false;
                            }
                            if($.trim(any_other) == '' || $.trim(school_name2) == ''){                
                                if(school_name1 == '' || $.trim(school_name1) == ''){
                                    msg += 'Please enter School Name 1</br>';
                                    form_valid = false;
                                }
                            }
                            if((school_name2 == '' && $.trim(school_name1) == '') || ($.trim(school_name2) == '' && $.trim(school_name1) == '')){
                                msg += 'Please enter School Name 2</br>';
                                form_valid = false;
                            }
                        
                            if((any_other == '' && $.trim(school_name1) == '' && $.trim(school_name2) == '') || ($.trim(any_other) == '' && $.trim(school_name1) == '' && $.trim(school_name2) == '')){
                                msg += 'Please enter Others</br>';
                                form_valid = false;
                            }
                        }
                        
                        if(check_student == 0){
                            $.post(home_url + "/?r=ajax/update_info", {
                                new_password: new_password,
                                retype_new_password: retype_new_password,
                                first_name: first_name,
                                last_name: last_name,
                                birth_y: birth_y,
                                birth_m: birth_m,
                                birth_d: birth_d,
                                cb_lang: cb_lang,
                                profile_avatar: profile_avatar,
                                time_zone: time_zone,
                                time_zone_index: time_zone_index,
                                time_zone_name: name,
                                gender: gender,
                                type: "update"
                            }, function (data) {
                                $("#update-teacher").attr("disabled", false);
                                if ($.trim(data) == '1') {
                                    $('#popup-message').html('<p class="text-used">Account Info Updated!<br/>Your account has been updated successfully.</p><button id="got-profile" type="button" class="btn-orange form-control nopadding-r border-btn" data-id="sub-update-info" data-tab="updateinfo">Got it</button>');
                                    $('#top-popup-message').css("display", "block");

                                    initDateTimePicker(name, time_zone_index, time_zone, city, 'update');
                                } else {
                                    $('#popup-message').html('<p class="text-used">' + data + '</p><button id="got-it" type="button" class="btn-orange form-control nopadding-r border-btn">OK</button>');
                                    $('#top-popup-message').css("display", "block");
                                }
                            });
                        }else{   
                            if(form_valid){
                                $.post(home_url + "/?r=ajax/update_info", {
                                    new_password: new_password,
                                    retype_new_password: retype_new_password,
                                    first_name: first_name,
                                    last_name: last_name,
                                    birth_y: birth_y,
                                    birth_m: birth_m,
                                    birth_d: birth_d,
                                    cb_lang: cb_lang,
                                    gender: gender,
                                    profile_avatar: profile_avatar,
                                    subject_type: subject_type,
                                    desc_tell_me: desc_tell_me,
                                    user_grade: user_grade,
                                    any_other: any_other,
                                    school_link2: school_link2,
                                    school_link1: school_link1,
                                    school_name2: school_name2,
                                    school_name1: school_name1,
                                    user_major: user_major,
                                    user_gpa: user_gpa,
                                    school_attend: school_attend,
                                    user_years: user_years,
                                    student_link: student_link,
                                    teaching_subject: teaching_subject,
                                    teaching_link: teaching_link,
                                    school_name: school_name,
                                    subject_description: subject_description,
                                    last_school: last_school,
                                    previous_school: previous_school,
                                    skype_id: skype_id,
                                    user_profession: user_profession,
                                    mobile_number: mobile_number,
                                    time_zone: time_zone,
                                    time_zone_index: time_zone_index,
                                    time_zone_name: name,
                                    type: "update"
                                }, function (data) {
                                    $("#update-teacher").attr("disabled", false);
                                    if ($.trim(data) == '1') {                                        
                                        $('#popup-message').html('<p class="text-used">Account Info Updated!<br/>Your account has been updated successfully.</p><button id="got-profile" type="button" class="btn-orange form-control nopadding-r border-btn" data-id="sub-update-info" data-tab="updateinfo">Got it</button>');
                                        $('#top-popup-message').css("display", "block");

                                        initDateTimePicker(name, time_zone_index, time_zone, city, 'update');
                                    } else {
                                        $('#popup-message').html('<p class="text-used">' + data + msg +'</p><button id="got-it" type="button" class="btn-orange form-control nopadding-r border-btn">OK</button>');
                                        $('#top-popup-message').css("display", "block");
                                    }
                                });
                            }else{
                                $('#popup-message').html('<p class="text-used">' + msg +'</p><button id="got-it" type="button" class="btn-orange form-control nopadding-r border-btn">OK</button>');
                                $('#top-popup-message').css("display", "block");
                            }   
                        }                      
                    });

                    $("#input-avatar").change(function () {
                        var file_data = $('#input-avatar').prop('files')[0];
                        var type = file_data.type;
                        var match = ["image/gif", "image/png", "image/jpg", ];
                        var form_data = new FormData();
                        form_data.append('file', file_data);
                        $.ajax({
                            url: home_url + "/?r=ajax/upload_avatar",
                            dataType: 'text',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: form_data,
                            type: 'post',
                            success: function (res) {
                                if ($.trim(res) != '0') {
                                    $("#profile-avatar").val($.trim(res));
                                    $("#user-upload-avatar").attr('src',$.trim(res));
                                } else {
                                    $('#popup-message').html('<p class="text-used">Error: There was an error uploading your file</p><button id="got-it" type="button" class="btn-orange form-control nopadding-r border-btn">OK</button>');
                                    $('#top-popup-message').css("display", "block");
                                }
                            }
                        });
                    });

                    $("#input-image").change(function () {
                        var file_data = $('#input-image').prop('files')[0];
                        var type = file_data.type;
                        var form_data = new FormData();
                        form_data.append('file', file_data);
                        $.ajax({
                            url: home_url + "/?r=ajax/upload_avatar",
                            dataType: 'text',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: form_data,
                            type: 'post',
                            success: function (res) {
                                if ($.trim(res) != '0') {
                                    $("#profile-value").val($.trim(res));
                                    $("#user-upload-img").attr('src',$.trim(res));
                                } else {
                                    $('#popup-message').html('<p class="text-used">Error: There was an error uploading your file</p><button id="got-it" type="button" class="btn-orange form-control nopadding-r border-btn">OK</button>');
                                    $('#top-popup-message').css("display", "block");
                                }
                            }
                        });
                    });

                    $("#sub-update-info a").click(function (e) {
                        get_update_info();                                                      
                        $('span.placeholder').each(function () {
                            var text = $(this).text();
                            var font = $(this).css("font");                                
                            if(text == 'Year:'){
                                var offset = 11;
                            }
                            else{
                                if (document.documentMode || /Edge/.test(navigator.userAgent)) {
                                    //console.log("Edge");
                                    var offset = getDistancePlace(text, "Edge");
                                }else if (navigator.userAgent.search("Firefox") >= 0){
                                    var offset = getDistancePlace(text, "Firefox");
                                    //console.log("Firefox");
                                }else{
                                    var offset = 18;
                                }
                            }
                            var left = (getTextWidth(text,font) + offset);
                            $(this).prev().css("padding-left",left+"px");
                        }); 
                    });

                    $('#sub-createacc').click(function () {
                        $(".refreshclass input").val('');
                        $(".radio_buttons").attr('checked', false);

                        var viewport = getViewport();
                        if(viewport.width < 650){
                            $("#birth_g_mb").selectBoxIt('selectOption','0').data("selectBox-selectBoxIt");
                            $("#birth_g_mb").data("selectBox-selectBoxIt").refresh();
                        }else{
                            $("#birth_g_pc").selectBoxIt('selectOption','0').data("selectBox-selectBoxIt");
                            $("#birth_g_pc").data("selectBox-selectBoxIt").refresh();
                        }
                        if($("#checked-availability").hasClass('yes-availability')){
                            $("#checked-availability").removeClass('yes-availability');
                        }
                        if($("#checked-availability").hasClass('not-availability')){
                            $("#checked-availability").removeClass('not-availability');
                        }
                        $("#checked-availability span").text('');
                    });

                    $("#sub-createacc a").click(function (e) {
                        var img = '<?php echo get_template_directory_uri() ?>/library/images/icon_Tutor_ID.png';
                        $("#user-upload-avatar").attr('src',img);

                        $('span.placeholder').each(function () {
                            var text = $(this).text();
                            var font = $(this).css("font");
                            if(text == 'Year:'){
                                var offset = 11;
                            }
                            else{
                                if (document.documentMode || /Edge/.test(navigator.userAgent)) {
                                    //console.log("Edge");
                                    var offset = getDistancePlace(text, "Edge");
                                }else if (navigator.userAgent.search("Firefox") >= 0){
                                    var offset = getDistancePlace(text, "Firefox");
                                    //console.log("Firefox");
                                }else{
                                    var offset = 18;
                                }
                            }
                            var left = (getTextWidth(text,font) + offset);
                            $(this).prev().css("padding-left",left+"px");
                        }); 
                    });

                    $("#sub-profile a").click(function (e) {
                        get_profile_info();
                    });

                    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
                        $("#my-timezone").css("display","none");
                        if ($(this).hasClass('disabled')){
                            e.preventDefault();
                            return false;
                        }
                    });

                    $("#got-it, #got-cancel").live("click", function () {
                        $('#popup-message').html();
                        $('#top-popup-message').css("display", "none");
                    });

                    $("#got-home").live("click", function () {
                        $('#popup-message').html();
                        $('#top-popup-message').css("display", "none");

                        if (redirect == '')
                            window.location.reload();
                        else
                            document.location.href = redirect;
                    });

                    $("#got-profile").live("click", function () {
                        get_profile_info();
                        $('#popup-message').html();
                        $('#top-popup-message').css("display", "none");
                        var id = $(this).attr('data-id');
                        var tab = $(this).attr('data-tab');
                        $('#profile').addClass('active in');
                        $('#sub-profile').addClass('active');
                        $("#" + tab).removeClass("active in");
                        $("#" + id).removeClass("active");
                    });

                    $('#create-overview').click(function(){
                        $('#popup-message').html('<h3>Overview of Create Basic Account</h3><p class="text-used">Create a class and have students join. With a class name and password, a student can join your class from the list of classes displayed on the student site. You need to select what you will tutor. You may also offer a ready-made lesson in your classroom. You will earn the portion of sales (See the percentage below).</p><ul class="text-percent"><li>There is no cost of setting up a class</li><li>Earn money by offering tutoring, your lessons, and ready-made lessons</li><li>Flexible scheduling for your tutoring</li><li>Hundreds of ready-made worksheets you can use or create your own</li></ul><button id="got-it" type="button" class="btn-orange btn-create-lesson form-control nopadding-r border-btn">Got it</button>');
                        $('#top-popup-message').css("display", "block");                          
                    });

                    $('#tutor-overview').click(function(){
                        $('#popup-message').html('<h3>Student Information Center</h3><p class="text-used">Get one-on-one tutoring with a qualified tutor of your choice. Schedule a time that is convenient for you.</p><ul class="text-percent"><li>  Search through the list of available tutors to see their reviews and bio.</li><li> Schedule tutoring session using the calendar function</li></ul><button id="got-it" type="button" class="btn-orange btn-create-lesson form-control nopadding-r border-btn">Got it</button>');
                        $('#top-popup-message').css("display", "block");                          
                    });

                    $('#btn-sent-tutor').click(function(){
                        var stt = $("#rdo-agreed2").is(":checked");
                        var mobile_number = $('#mobile-number').val();
                        var last_school = $('#last-school').val();
                        var previous_school = $('#previous-school').val();
                        var skype_id = $('#skype-id').val();
                        var user_profession = $('#user-profession').val();

                        var subject_description = $('#subject-description').val();
                        var school_name = $('#school-name').val();
                        var teaching_link = $('#teaching-link').val();

                        var teaching_subject = $('#teaching-subject').val();
                        var student_link = $('#student-link').val();
                        var user_years = $('#user-years').val();
                        var school_attend = $('#school_attend').val();
                        var user_gpa = $('#user-gpa').val();
                        var user_major = $('#user-major').val();

                        var school_name1 = $('#school-name1').val();
                        var school_name2 = $('#school-name2').val();
                        var school_link1 = $('#school-link1').val();
                        var school_link2 = $('#school-link2').val();
                        var any_other = $('#any-other').val();
                        var user_grade = $("#user-gradeSelectBoxItText").attr("data-val");
                        var desc_tell_me = $('#desc_tell_me_ifr').contents().find('#tinymce').text();
                        var subject_type = [];
                        $('input[name="subject_type"]:checked').each(function () {
                            var val = this.value;
                            if(val == '') var val = $(this).attr('data-subject');
                            subject_type.push(val);
                        });
                        var msg = '';                                
                        var form_valid = true;
                        if(mobile_number == '' || $.trim(mobile_number) == ''){
                            msg += 'Please enter Mobile Number</br>';
                            form_valid = false;
                        }
                        if(last_school == '' || $.trim(last_school) == ''){
                            msg += 'Please enter Last School Attended</br>';
                            form_valid = false;
                        }
                        if(user_profession == '' || $.trim(user_profession) == ''){
                            msg += 'Please enter Profession</br>';
                            form_valid = false;
                        }
                        if(desc_tell_me == '' || $.trim(desc_tell_me) == ''){
                            msg += 'Please enter Short description</br>';
                            form_valid = false;
                        }
                        if(subject_type.length == 0){
                            msg += 'Please check the box of Subjects you Interested</br>';
                            form_valid = false;
                        }            
                        if($.trim(any_other) == '' || $.trim(school_name2) == ''){                
                            if(school_name1 == '' || $.trim(school_name1) == ''){
                                msg += 'Please enter School Name 1</br>';
                                form_valid = false;
                            }
                        }
                        if((school_name2 == '' && $.trim(school_name1) == '') || ($.trim(school_name2) == '' && $.trim(school_name1) == '')){
                            msg += 'Please enter School Name 2</br>';
                            form_valid = false;
                        }
                    
                        if((any_other == '' && $.trim(school_name1) == '' && $.trim(school_name2) == '') || ($.trim(any_other) == '' && $.trim(school_name1) == '' && $.trim(school_name2) == '')){
                            msg += 'Please enter Others</br>';
                            form_valid = false;
                        }
                        if (stt == false) {
                            msg += 'Please read and check the box of Terms and Conditions</br>';
                            form_valid = false;
                        }
                        if(form_valid){
                            $.post(home_url + "/?r=ajax/update_info", {                                   
                                subject_type: subject_type,
                                desc_tell_me: desc_tell_me,
                                user_grade: user_grade,
                                any_other: any_other,
                                school_link2: school_link2,
                                school_link1: school_link1,
                                school_name2: school_name2,
                                school_name1: school_name1,
                                user_major: user_major,
                                user_gpa: user_gpa,
                                school_attend: school_attend,
                                user_years: user_years,
                                student_link: student_link,
                                teaching_subject: teaching_subject,
                                teaching_link: teaching_link,
                                school_name: school_name,
                                subject_description: subject_description,
                                last_school: last_school,
                                previous_school: previous_school,
                                skype_id: skype_id,
                                user_profession: user_profession,
                                mobile_number: mobile_number,
                                type: "create"
                            }, function (data) {
                                if ($.trim(data) == '1') {
                                    $('#popup-message').html('<p class="text-used">Teacher & Tutor Account has been created successfully</p><button id="got-profile" type="button" class="btn-orange form-control nopadding-r border-btn" data-id="tutor-regist" data-tab="tutor-regis-tab">Got it</button>');
                                    $('#top-popup-message').css("display", "block");
                                }                                         
                            });
                        }else{
                            $('#popup-message').html('<p class="text-used">' + msg + '</p><button id="got-it" type="button" class="btn-orange form-control nopadding-r border-btn">OK</button>');
                            $('#top-popup-message').css("display", "block");
                        }
                    });

                    $('#sub-findingtutor').click(function(){
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                        $('.new-request-list').text('Find a tutor');
                        $('#btn-available-now').addClass('active');
                        $(".main-my-schedule").css("display","none");
                        $(".main-new-request").css("display","none"); 
                        $(".main-view-request").css("display","none");
                        $(".main-status-request").css("display","none");
                        $(".section-tutor-main").css("display","block");
                        $('.writting-review').css("display","none");
                        $('.header-title-newschedule').css("display","none");
                        $('.frm-available-now').css("display","none"); 
                        $('#custom-timezone').css("display","none");

                        $('.radio_tutor_search').attr('checked',false);

                        if($(".main-my-schedule").hasClass('active-tab-schedule')){
                            $(".main-my-schedule").removeClass("active-tab-schedule");
                        }

                        var viewport = getViewport();
                        if(viewport.width < 925){
                            $('#btn-open-calendar').css("display","none");
                            if(viewport.width < 650){
                                $('#tab-tutor-content .border-selectall').find('.col-md-6').css('width','66.2%');
                            }else{
                                $('#tab-tutor-content .border-selectall').find('.col-md-6').css('width','51.2%');
                            }
                        }else{
                            $('#tab-tutor-content .border-selectall').find('.col-md-6').css('width','67.2%');
                        }

                        $('.btn-sub-tab').removeClass('active');
                        if(!$('#btn-available-now').hasClass('active')){
                            $('#btn-available-now').addClass('active');
                            $('#btn-available-now').find('img').attr('src',path + '04_Available_Now_Selected.png');
                            $('#btn-list-review').find('img').attr('src',path + 'icon_L_Review.png');
                            $('#btn-list-favorite').find('img').attr('src',path + 'icon_L_Favorite.png');
                            $('#btn-list-tutoring').find('img').attr('src',path + 'icon_L_list.png');
                            $('#btn-find-tutoring').find('img').attr('src',path + 'icon_Find_off.png');
                        }

                        get_tutor_user('available');
                    });


                    $('#tab-tutoring').click(function(){
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                        $('.new-request-list').text('Find a tutor');
                        $('.writting-review').css("display","none");
                        $(".main-my-schedule").css("display","none");
                        $(".main-new-request").css("display","none");
                        $(".main-view-request").css("display","none");
                        $(".main-status-request").css("display","none");
                        $('.header-title-newschedule').css("display","none");
                        $('.frm-available-now').css("display","none"); 
                        $('#custom-timezone').css("display","none");

                        if($(".main-my-schedule").hasClass('active-tab-schedule')){
                            $(".main-my-schedule").removeClass("active-tab-schedule");
                        }

                        if(!$(this).hasClass('active')){
                            $(this).find('img').attr('src',path + 'icon_M_Tutoring_Selected.png');
                            $('#tab-my-class').find('img').attr('src',path + 'icon_M_MyClass.png');
                            $('#tab-my-message').find('img').attr('src',path + 'icon_M_MyMessage.png');
                            $('.btn-sub-tab').removeClass('active');
                            if(!$('#btn-available-now').hasClass('active')){
                                $('#btn-available-now').addClass('active');
                                $('#btn-available-now').find('img').attr('src',path + '04_Available_Now_Selected.png');
                                $('#btn-list-review').find('img').attr('src',path + 'icon_L_Review.png');
                                $('#btn-list-favorite').find('img').attr('src',path + 'icon_L_Favorite.png');
                                $('#btn-list-tutoring').find('img').attr('src',path + 'icon_L_list.png');
                                $('#btn-find-tutoring').find('img').attr('src',path + 'icon_Find_off.png');
                            }
                            get_tutor_user('available');
                        }                        
                    });

                    $('#tab-my-class').click(function(){
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                        if(!$(this).hasClass('active')){
                            $(this).find('img').attr('src',path + 'icon_M_MyClass_Selected.png');
                            $('#tab-tutoring').find('img').attr('src',path + 'icon_M_Tutoring.png');
                            $('#tab-my-message').find('img').attr('src',path + 'icon_M_MyMessage.png');
                        }
                    });

                    $('#tab-my-message').click(function(){
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                        if(!$(this).hasClass('active')){
                            $(this).find('img').attr('src',path + 'icon_M_MyMessage_Selected.png');
                            $('#tab-my-class').find('img').attr('src',path + 'icon_M_MyClass.png');
                            $('#tab-tutoring').find('img').attr('src',path + 'icon_M_Tutoring.png');
                        }
                    });

                    $('#btn-find-tutoring').click(function(){
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';  
                        $('.writting-review').css("display","none");
                        $('.toggle-btn').css("display","none");
                        
                        var available_time = $("#mytime-clock").attr("data-available-time");
                        $("#select-available-time").selectBoxIt('selectOption',available_time.toString()).data("selectBox-selectBoxIt");
                        $("#select-available-time").data("selectBox-selectBoxIt").refresh();

                        if(!$(this).hasClass('active')){
                            $('.btn-sub-tab').removeClass('active');
                            $(this).addClass('active');
                            $(this).find('img').attr('src',path + 'icon_Find.png');
                            $('#btn-list-review').find('img').attr('src',path + 'icon_L_Review.png');
                            $('#btn-list-favorite').find('img').attr('src',path + 'icon_L_Favorite.png');
                            $('#btn-list-tutoring').find('img').attr('src',path + 'icon_L_list.png');
                            $('#btn-available-now').find('img').attr('src',path + '04_Available_Now.png');
                        }

                        $('.radio_tutor_search').attr('checked',false);
                        $('.frm-available-now').css("display","block");
						$('#table-detail-tutor').css("display","none");
						$(".slide-resume").css('visibility','hidden');
                        $('#table-list-tutor').html('');
                        var tr = '<tr><td class="no-results"><img src="' + path + 'icon_Not_Available.png" alt="">Currently, there are no results.</td></tr>';
                        $('#table-list-tutor').html(tr);
                        
                        /*
                        var date = $('#today-tutor').val();
                        var hour = $("#time-clock").attr("data-hour");
                        var minute = $("#time-clock").attr("data-minute");
                        var type = $("#time-clock").attr("data-type");
                        if(parseInt(minute) > 29){
                            var time = hour+':30'+type;
                        }else{
                            var time = hour+':00'+type;
                        }

                        $('#table-detail-tutor').css('display','none');
                        $('.slide-resume').css('visibility','hidden');
                        get_tutor_user('fromclass','table-list-tutor','tutor','','','','',time,date);
                        */
                    });

                    $('#btn-tutor').click(function(){
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                       
                        $('.new-request-list').text('Find a tutor');
                        $(".main-my-schedule").css("display","none");
                        $(".section-tutor-main").css("display","block");
                        $(".main-new-request").css("display","none");
                        $(".main-view-request").css("display","none");
                        $(".main-status-request").css("display","none");
                        $('.writting-review').css("display","none");
                        $('.header-title-newschedule').css("display","none");
                        $('.frm-available-now').css("display","none"); 
                        $('#custom-timezone').css("display","none");

                        $('.header-schedule').removeClass('active');
                        $('#list-schedule-status').css("display","none");
                        $('#table-status-schedule').html('');
                        $("#open-menu-schedule").css("display","none");
						$(".main-view-status").css("display","none");

                        if($(".main-my-schedule").hasClass('active-tab-schedule')){
                            $(".main-my-schedule").removeClass("active-tab-schedule");
                        }

                        var viewport = getViewport();
                        if(viewport.width < 925){
                            $('#btn-open-calendar').css("display","none");
                            if(viewport.width < 650){
                                $('#tab-tutor-content .border-selectall').find('.col-md-6').css('width','66.2%');
                            }else{
                                $('#tab-tutor-content .border-selectall').find('.col-md-6').css('width','51.2%');
                            }
                        }else{
                            $('#tab-tutor-content .border-selectall').find('.col-md-6').css('width','67.2%');
                        }

                        if(!$('#btn-available-now').hasClass('active')){
                            $('.btn-sub-tab').removeClass('active');
                            $('#btn-available-now').addClass('active');
                            $('#btn-available-now').find('img').attr('src',path + '04_Available_Now_Selected.png');
                            $('#btn-list-review').find('img').attr('src',path + 'icon_L_Review.png');
                            $('#btn-list-favorite').find('img').attr('src',path + 'icon_L_Favorite.png');
                            $('#btn-list-tutoring').find('img').attr('src',path + 'icon_L_list.png');
                            $('#btn-find-tutoring').find('img').attr('src',path + 'icon_Find_off.png');
                        }
                        get_tutor_user('available');
                    });

                    $('#btn-list-tutoring').click(function(){         
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';  
                        $('.writting-review').css("display","none");    
                        $('.frm-available-now').css("display","none"); 
                        $('.toggle-btn').css("display","block");  
                        $('#cb-show-available').attr("data-type","list");       
                        
                        if(!$(this).hasClass('active')){
                            $('.btn-sub-tab').removeClass('active');
                            $(this).addClass('active');
                            $(this).find('img').attr('src',path + 'icon_L_list_Selected.png');
                            $('#btn-list-review').find('img').attr('src',path + 'icon_L_Review.png');
                            $('#btn-list-favorite').find('img').attr('src',path + 'icon_L_Favorite.png');
                            $('#btn-available-now').find('img').attr('src',path + '04_Available_Now.png');
                            $('#btn-find-tutoring').find('img').attr('src',path + 'icon_Find_off.png');
                        }
                        var check = $('#cb-show-available').is(":checked");
                        if(check == false)
                            var type = '';
                        else
                            var type = 'available';

                        get_tutor_user('list','table-list-tutor','tutor','','','','','','','','','',type);
                    });

                    $('#btn-list-review').click(function(){  
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';   
                        $('.writting-review').css("display","none");   
                        $('.frm-available-now').css("display","none");  
                        $('.toggle-btn').css("display","block");    
                        $('#cb-show-available').attr("data-type","review");           
                        
                        if(!$(this).hasClass('active')){
                            $('.btn-sub-tab').removeClass('active');
                            $(this).addClass('active');
                            $(this).find('img').attr('src',path + 'icon_L_Review_Selected.png');
                            $('#btn-list-tutoring').find('img').attr('src',path + 'icon_L_list.png');
                            $('#btn-list-favorite').find('img').attr('src',path + 'icon_L_Favorite.png');
                            $('#btn-available-now').find('img').attr('src',path + '04_Available_Now.png');
                            $('#btn-find-tutoring').find('img').attr('src',path + 'icon_Find_off.png');
                        }
                        var check = $('#cb-show-available').is(":checked");
                        if(check == false)
                            var type = '';
                        else
                            var type = 'available';

                        get_tutor_user('review','table-list-tutor','tutor','','','','','','','','','',type);
                    });

                    $('#btn-list-favorite').click(function(){  
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                        $('.writting-review').css("display","none");      
                        $('.frm-available-now').css("display","none");  
                        $('.toggle-btn').css("display","block");   
                        $('#cb-show-available').attr("data-type","favorite");           
                        
                        if(!$(this).hasClass('active')){
                            $('.btn-sub-tab').removeClass('active');
                            $(this).addClass('active');
                            $(this).find('img').attr('src',path + 'icon_L_Favorite_Selected.png');
                            $('#btn-list-review').find('img').attr('src',path + 'icon_L_Review.png');
                            $('#btn-list-tutoring').find('img').attr('src',path + 'icon_L_list.png');
                            $('#btn-available-now').find('img').attr('src',path + '04_Available_Now.png');
                            $('#btn-find-tutoring').find('img').attr('src',path + 'icon_Find_off.png');
                        }
                        var check = $('#cb-show-available').is(":checked");
                        if(check == false)
                            var type = '';
                        else
                            var type = 'available';

                        get_tutor_user('favorite','table-list-tutor','tutor','','','','','','','','','',type);
                    });

                    $('#btn-available-now').click(function(){ 
                        $('.writting-review').css("display","none");      
                        $('.frm-available-now').css("display","none");
                        $('.toggle-btn').css("display","none");
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                        if(!$(this).hasClass('active')){
                            $('.btn-sub-tab').removeClass('active');
                            $(this).addClass('active');
                            $(this).find('img').attr('src',path + '04_Available_Now_Selected.png');
                            $('#btn-list-review').find('img').attr('src',path + 'icon_L_Review.png');
                            $('#btn-list-favorite').find('img').attr('src',path + 'icon_L_Favorite.png');
                            $('#btn-list-tutoring').find('img').attr('src',path + 'icon_L_list.png');
                            $('#btn-find-tutoring').find('img').attr('src',path + 'icon_Find_off.png');
                        }
                        get_tutor_user('available','table-list-tutor','tutor','','','','','','');
                    });

                    $('.btn-available-reset').click(function(){
                        $(".main-my-schedule").css("display","none");
                        $(".main-new-request").css("display","none");
                        $(".main-view-request").css("display","none");
                        $(".main-status-request").css("display","none");
                        $('.writting-review').css("display","none");
                        $('.header-title-newschedule').css("display","none");

                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';

                        var year = $('#available_year').val();
                        var search = $('#search-find-tutoring').val('');
                        var day = $("#select-available-daySelectBoxItText").attr("data-val");
                        var month = $("#select-available-monthSelectBoxItText").attr("data-val");
                        var time = $("#select-available-timeSelectBoxItText").attr("data-val");
                        var subject_type = $("#select-available-subjectSelectBoxItText").attr("data-val");

                        $(".radio_tutor_search").attr('checked', false);

                        if(month != 0){
                            $("#select-available-month").selectBoxIt('selectOption','0').data("selectBox-selectBoxIt");
                            $("#select-available-month").data("selectBox-selectBoxIt").refresh();
                        }

                        if(day != 0){
                            $("#select-available-day").selectBoxIt('selectOption','0').data("selectBox-selectBoxIt");
                            $("#select-available-day").data("selectBox-selectBoxIt").refresh();
                        }

                        if(time != 0){
                            $("#select-available-time").selectBoxIt('selectOption','0').data("selectBox-selectBoxIt");
                            $("#select-available-time").data("selectBox-selectBoxIt").refresh();
                        }

                        if(subject_type != 0){
                            $("#select-available-subject").selectBoxIt('selectOption','0').data("selectBox-selectBoxIt");
                            $("#select-available-subject").data("selectBox-selectBoxIt").refresh();
                        }
                        var tr = '<tr><td class="no-results"><img src="' + path + 'icon_Not_Available.png" alt="">Currently, there are no results.</td></tr>';
                        $('#table-list-tutor').html(tr);
                        //get_tutor_user('fromclass','table-list-tutor','tutor');
                    });
                    
                    $('#btn-available-search').click(function(){
                        $(".main-my-schedule").css("display","none");
                        $(".main-new-request").css("display","none");
                        $(".main-view-request").css("display","none");
                        $(".main-status-request").css("display","none");
                        $('.writting-review').css("display","none");
                        $('.header-title-newschedule').css("display","none");

                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';

                        var search = $('#search-find-tutoring').val();
                        var year = $('#available_year').val();
                        var day = $("#select-available-daySelectBoxItText").attr("data-val");
                        var month = $("#select-available-monthSelectBoxItText").attr("data-val");
                        var time = $("#select-available-timeSelectBoxItText").attr("data-val");
                        var time_view = $('#select-available-time :selected').attr("data-time-view");
                        var stime = $('#select-available-time :selected').attr("data-time");
						var subject_name = $('#select-available-subject :selected').attr("data-name");
                        var subject_type = $("#select-available-subjectSelectBoxItText").attr("data-val");
                        var type_search = [];

                        var option = $('#select-available-option :selected').attr("value");
                        if(option == "all"){
                            type_search.push("rating");
                            type_search.push("favorite");
                        }else{
                            type_search.push(option);
                        }
                        // $('#select-available-subject :selected').each(function () {
                        //     var val = this.value;
                        //     type_search.push(val);
                        // });

                        if(month != 0 && day != 0 && year != 0)
                            var date = year + '-' + month + '-' + day;
                        else
                            var date = '';

                        if(time == 0) time = '';
                        if(search != '' || date != '' || time != '' || subject_type != 0 || type_search.length > 0){
                            if(subject_type == 0){
                                $('#popup-message').html('<p class="text-used">Please select subject</p><button id="got-it" type="button" class="btn-orange form-control nopadding-r border-btn">OK</button>');
                                $('#top-popup-message').css("display", "block");
                            }else{
                                $('#table-list-tutor').html('');
                                get_tutor_user('fromclass', 'table-list-tutor', 'tutor', search, '', '', subject_type, time, date, type_search,stime, time_view, '', subject_name);
                            }
                        }else{
                            var tr = '<tr><td class="no-results"><img src="' + path + 'icon_Not_Available.png" alt="">Currently, there are no results.</td></tr>';
                            $('#table-list-tutor').html(tr);
                        }
                    });

                    $('#btn-submit-review').click(function(){
                        var id = $(this).attr('data-id');
						var userid = $(this).attr('data-userid');
                        var review_id = $(this).attr('data-review-id');
                        var subject = $('#write-review-subject').val();
                        var message = $('#message-review_ifr').contents().find('#tinymce').text();
                        var star = 0;
                        $('input[name="star"]:checked').each(function () {
                            var val = this.value;
                            star = star + parseInt(val);
                        });
                        if(star == 0){
                            $('#popup-message').html('<p class="text-used">Please check the box of Star-Rating</p><button id="got-it" type="button" class="btn-orange form-control nopadding-r border-btn">OK</button>');
                            $('#top-popup-message').css("display", "block");
                        }else{
                            $.post(home_url + "/?r=ajax/save_review", {id:id, review_id:review_id, userid: userid, subject:subject, message:message, star:star}, function (data) {
                                if ($.trim(data) == '1'){ 
                                    $('#post_subject2').val('');
                                    tinyMCE.activeEditor.setContent('');
                                    $('.star_buttons').attr('checked',false);  
                                    $('.writting-review').css("display","none");    
                                    get_resume(review_id,'review');
                                }
                            });
                        }
                        $(this).attr("disabled", false);
                    });

                    $('.star_buttons').click(function(){
                        var star = $(this).val();
                        var cnt = $(this).attr('data-star');
						
						$(this).attr("disabled", false);
						if(cnt > 1){
							for(i=0; i<=5; i++){
								if (i < cnt){
									$("#star" + i).prop('checked', true);
								} else {
									if(i == cnt){
										//var chk = true;
										$("#star" + i).prop('checked', true);
									}else{
										$("#star" + i).prop('checked', false);
									}
								}
							}
						}else{
							$("#star1").prop('checked', true);
							for(i=2; i<=5; i++){
								$("#star" + i).prop('checked', false);
							}
						}
                    });

                    $('.radio_buttons_tutor').live('click', function () {
                        $('.radio_buttons_tutor').attr('checked',false);
                        $(this).attr('checked',true);
                    });

                    $('.radio_buttons_request').live('click', function () {
                        $('.radio_buttons_request').attr('checked',false);
                        $(this).attr('checked',true);
                    });

                    $('#btn-select-tutor').live('click', function () {
                        var id = $(this).attr('data-id');
                        var name = $(this).attr('data-name');
                        var subject = $(this).attr('data-subject');
						var subject_choose = $(this).attr('data-subject-choose');
                        var day = $(this).attr('data-day');
                        var time = $(this).attr('data-time');
                        var time_view = $(this).attr('data-time-view');
                        var price_tutoring = $(this).attr('data-price-tutoring');
                        if($('#selected-tutor').hasClass('active')){
							$('#selected-tutor').removeClass('active');
                            $('#btn-schedule-now').removeClass('active');
                            $('#selected-tutor').text('Not selected yet');
							//$('#selected-subject').removeClass('active');
							//$('#selected-subject').text('Not selected yet');
                            $('#btn-schedule-now').attr('data-tutor-id','');
                        }else{
                            $('#selected-tutor').addClass('active');
                            $('#btn-schedule-now').addClass('active');
                            $('#selected-tutor').text(name);
							//$('#selected-subject').addClass('active');
							/*if(subject_choose == ''){
								$('#selected-subject').text(subject);
								$('#btn-schedule-now').attr('data-subject',subject);
							}else{
								$('#selected-subject').text(subject_choose);
								$('#btn-schedule-now').attr('data-subject',subject_choose);
							}*/
                            $('#btn-schedule-now').attr('data-name',name);
                            $('#btn-schedule-now').attr('data-tutor-id',id);
                            $('#btn-schedule-now').attr('data-total',price_tutoring);
                            if(time != ''){
                                var today = new Date(day.replace("-", ","));                            
                                var weekday = new Array(7);
                                    weekday[0] =  "Sun";
                                    weekday[1] = "Mon";
                                    weekday[2] = "Tue";
                                    weekday[3] = "Wed";
                                    weekday[4] = "Thur";
                                    weekday[5] = "Fri";
                                    weekday[6] = "Sat";                                
                                var n = weekday[today.getDay()];
                                var month_text = getMonthtoText(today.getMonth()+1);
                                $('#selected-date').text(month_text + ' ' + today.getDate() +'('+n+')'+time_view);
                                $('#btn-schedule-now').attr('data-day',day);
                                $('#btn-schedule-now').attr('data-time',time);
                            }
                        }
                    });

                    $('#btn-schedule-now').click(function(){
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                        var choose_tutor = $(this).attr("data-tutor-id");
						var subject = $(this).attr("data-subject");
                        var ptype = $(this).attr("data-ptype");
                        var table = $(this).attr("data-table");
                        var time = $(this).attr("data-time");
                        var day = $(this).attr("data-day");
                        var price_tutoring = $(this).attr('data-price-tutoring');
                        var year = $('#available_year').val();
                        var month = $("#select-available-monthSelectBoxItText").attr("data-val");
                        
                        if(month != 0 && day != 0 && year != 0)
                            var date = year + '-' + month + '-' + day;
                        else
                            var date = '';
                            
                        var current_day = $('#today-tutor').val();
                        var CurrentDate = new Date(current_day);
                        var GivenDate = new Date(date);
                        
                        if($(this).hasClass('active') && choose_tutor != ''){
                            var total = $(this).attr("data-total");
                            if(GivenDate < CurrentDate){
                                $(this).removeClass('active');
                            }else{
                                $.post(home_url + "/?r=ajax/get_schedule_now", {  
                                    id: choose_tutor,
                                    day: day,
                                    time: time
                                }, function (datas) {
                                    datas = JSON.parse(datas);
                                    if (datas.user_points && datas.exit == 0) {
                                        $('#popup-message').html('<p class="text-used"><span class="popup-total"><strong>' + total + ' Points($)</strong> will be used for this schedule.</span><br/><span class="popup-points">You have total of <strong>' + datas.user_points + ' Points($)</strong> Remaining. To Purchase more Points, <a href="">Click here</a>.</span"></p><button id="got-accept" data-total="' + total + '" data-id="" data-ptype="'+ptype+'" data-table="'+table+'" type="button" class="btn-got-accept">Accept</button><button id="got-canceled" data-id="" type="button" class="btn-got-canceled">Cancel</button>');
                                        $('#top-popup-message').css("display", "block");
                                    }else{
                                        $('#popup-message').html('<p class="text-used">You already have a schedule on this date. Please select a different date.</p><button id="got-it" type="button" class="btn-got-accept">OK</button><button id="got-canceled" data-id="" type="button" class="btn-got-canceled">Cancel</button>');
                                        $('#top-popup-message').css("display", "block");
                                    }
                                });
                            }
                        }
                    });
                    
                    $('.close-detail').live('click', function () {
                        var table = $(this).attr('data-table');
                        var ptype = $(this).attr('data-ptype');
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                        $('.writting-review').css("display","none");

                        if($(".main-my-schedule").hasClass('active-tab-schedule')){
                            $(".main-my-schedule").removeClass("active-tab-schedule");
                        }

                        if(table == 'table-status-request'){
                            if($('.btn-sub-status').hasClass('active')){
                                var stype = $('.btn-sub-status').attr("data-type");        
                            }else{
                                stype = 'all';
                            }
                            get_status_request(stype);

                            $(".main-my-schedule").css("display","none");
                            $(".section-tutor-main").css("display","none");
                            $(".main-new-request").css("display","none"); 
                            $(".main-view-request").css("display","none");
                            $('.header-title-newschedule').css("display","none");
                            $(".main-status-request").css("display","block");
                        }else{
                            
                            if(table == 'table-list-tutor'){
                                var retype = 'tutor';
                            }else{
                                var retype = 'findtutor';
                            }
                            if(ptype == "fromclass"){
                                var search = $('#search-find-tutoring').val();
                                var year = $('#available_year').val();
                                var day = $("#select-available-daySelectBoxItText").attr("data-val");
                                var month = $("#select-available-monthSelectBoxItText").attr("data-val");
                                var time = $("#select-available-timeSelectBoxItText").attr("data-val");
                                var time_view = $('#select-available-time :selected').attr("data-time-view");
                                var stime = $('#select-available-time :selected').attr("data-time");
                                var subject_type = $("#select-available-subjectSelectBoxItText").attr("data-val");
                                var type_search = [];

                                $('input[name="type_search"]:checked').each(function () {
                                    var val = this.value;
                                    type_search.push(val);
                                });

                                if(month != 0 && day != 0 && year != 0)
                                    var date = year + '-' + month + '-' + day;
                                else
                                    var date = '';

                                if(time == 0) time = '';
                                if(search != '' || date != '' || time != '' || subject_type != 0 || type_search.length > 0){
                                    get_tutor_user('fromclass','table-list-tutor','tutor',search,'','',subject_type,time,date,type_search, stime, time_view);
                                }else{
                                    var tr = '<tr><td class="no-results"><img src="' + path + 'icon_Not_Available.png" alt="">Currently, there are no results.</td></tr>';
                                    $('#table-list-tutor').html(tr);
                                }
                                $('.frm-available-now').css("display","block");
                            }else{
                                get_tutor_user(ptype,table,retype);   
                                $('.frm-available-now').css("display","none");
                            }

                            $(".main-my-schedule").css("display","none");
                            $(".section-tutor-main").css("display","block");
                            $(".main-new-request").css("display","none"); 
                            $(".main-view-request").css("display","none");
                            $(".main-status-request").css("display","none");
                            $('.header-title-newschedule').css("display","none");
                        }
                    });

                    $('.book-mark').live('click', function (e) {
                        if(!$(this).hasClass('active')){
                            e.preventDefault();
                            $(this).addClass('active');
                            var id = $(this).attr('data-id');
                            var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                            $.post(home_url + "/?r=ajax/save_book_mark", {userid: id}, function (data) {
                                if ($.trim(data) == '1'){   
                                    $('#book-mark' + id).attr('src',path+'icon_Favorite_BookMark.png');
                                    $('#book-mark' + id).removeClass('active');                              
                                    /*$('#popup-message').html('<p class="text-used">Favorite Marked this Tutor successfully.</p><button id="got-it" type="button" class="btn-orange form-control nopadding-r border-btn">OK</button>');
                                    $('#top-popup-message').css("display", "block");*/
                                }else{
                                    $('#book-mark' + id).attr('src',path+'Icon_Favorite_Unselected.png');
                                    $('#book-mark' + id).removeClass('active');                              
                                    /*$('#popup-message').html('<p class="text-used">Unmark Favorite this Tutor successfully.</p><button id="got-it" type="button" class="btn-orange form-control nopadding-r border-btn">OK</button>');
                                    $('#top-popup-message').css("display", "block");*/
                                }
                            });
                        }
                    });

                    $('#tutor-sent-message').live('click', function () {
                        var id = $(this).attr('data-id');
                    });

                    $('.find-card-more').live('click', function (e) {
                        e.preventDefault();

                        var id = $(this).attr('data-id');
                        var ptype = $(this).attr('data-type');
                        var table = $(this).attr('data-table');
                        var slide_index = $(this).attr('data-slide-index');
                        var day = $(this).attr('data-day');
                        var time = $(this).attr('data-time');
                        var time_view = $(this).attr('data-time-view');
						var subject_name = $('#select-available-subject :selected').attr("data-name");
						
                        $('.writting-review').css("display","none");
                        $('.frm-available-now').css("display","none");

                        $('.slide-resume').slick('slickGoTo', parseInt(slide_index));

                        if($('#selected-tutor').hasClass('active')){
                            $('#selected-tutor').removeClass('active');
                            $('#btn-schedule-now').removeClass('active');
                            $('#selected-tutor').text('Not selected yet');
                            $('#btn-schedule-now').attr('data-tutor-id','');
                        }
						
						if(subject_name != ''){
							$('#selected-subject').text(subject_name);
							$('#btn-schedule-now').attr('data-subject',subject_name);
						}
						
                        if(time != ''){
                            var today = new Date(day.replace("-", ","));                            
                            var weekday = new Array(7);
                                weekday[0] =  "Sun";
                                weekday[1] = "Mon";
                                weekday[2] = "Tue";
                                weekday[3] = "Wed";
                                weekday[4] = "Thur";
                                weekday[5] = "Fri";
                                weekday[6] = "Sat";                                
                            var n = weekday[today.getDay()];
                            var month_text = getMonthtoText(today.getMonth()+1);
                            $('#selected-date').text(month_text + ' ' + today.getDate() +'('+n+')'+time_view);
                            $('#btn-schedule-now').attr('data-day',day);
                            $('#btn-schedule-now').attr('data-time',time);
                            $('#btn-schedule-now').attr('data-time-view',time_view);
                        }

                        get_resume(id,'resume',ptype,table,"",time_view);
                    });

                    $('.find-card-select-btn').live('click', function (e) {
                        e.preventDefault();

                        var id = $(this).attr('data-id');
                        var ptype = $(this).attr('data-type');
                        var table = $(this).attr('data-table');
                        var slide_index = $(this).attr('data-slide-index');
                        var day = $(this).attr('data-day');
                        var time = $(this).attr('data-time');
                        var time_view = $(this).attr('data-time-view');
						var subject_name = $('#select-available-subject :selected').attr("data-name");
						
                        $('.writting-review').css("display","none");
                        $('.frm-available-now').css("display","none");

                        $('.slide-resume').slick('slickGoTo', parseInt(slide_index));

                        if($('#selected-tutor').hasClass('active')){
                            $('#selected-tutor').removeClass('active');
                            $('#btn-schedule-now').removeClass('active');
                            $('#selected-tutor').text('Not selected yet');
                            $('#btn-schedule-now').attr('data-tutor-id','');
                        }
						
						if(subject_name != ''){
							$('#selected-subject').text(subject_name);
							$('#btn-schedule-now').attr('data-subject',subject_name);
						}
						
                        if(time != ''){
                            var today = new Date(day.replace("-", ","));                            
                            var weekday = new Array(7);
                                weekday[0] =  "Sun";
                                weekday[1] = "Mon";
                                weekday[2] = "Tue";
                                weekday[3] = "Wed";
                                weekday[4] = "Thur";
                                weekday[5] = "Fri";
                                weekday[6] = "Sat";                                
                            var n = weekday[today.getDay()];
                            var month_text = getMonthtoText(today.getMonth()+1);
                            $('#selected-date').text(month_text + ' ' + today.getDate() +'('+n+')'+time_view);
                            $('#btn-schedule-now').attr('data-day',day);
                            $('#btn-schedule-now').attr('data-time',time);
                            $('#btn-schedule-now').attr('data-time-view',time_view);
                        }

                        get_resume(id,'resume',ptype,table,"",time_view);
                    });

                    $('.slide-resume .slick-arrow').live('click', function () {
                        if($('.writting-review').hasClass("active")){
                            $('.writting-review').removeClass("active");
                            $('.writting-review').css("display","none");
                        }  

                        $('.tr-info').css('display','block');
                        $('.tr-review').css('display','none');
                        $('.view-review').attr('src','<?php echo get_template_directory_uri(); ?>/library/images/iconM_Review_OFF.png');
                        $('.view-resume').attr('src','<?php echo get_template_directory_uri(); ?>/library/images/iconM_Resume_ON-O.png');
                        $('.view-write-review').attr('src','<?php echo get_template_directory_uri(); ?>/library/images/iconM_Write_Review_OFF.png');

                        if($('#selected-tutor').hasClass('active')){
                            $('#selected-tutor').removeClass('active');
                            $('#btn-schedule-now').removeClass('active');
                            $('#selected-tutor').text('Not selected yet');
                            $('#btn-schedule-now').attr('data-tutor-id','');
                        }
                    });

                    $('.view-tutor-detail').live('click', function () {
                        var id = $(this).attr('data-id');
                        var ptype = '';
                        var table = 'table-status-request';
                        $('.writting-review').css("display","none");

                        if($('#btn-find-tutoring').hasClass('active')){
                            $('#btn-find-tutoring').removeClass('active');
                            $('#open-find-tutoring').css("display","none");
                            $('.radio_tutor_search').attr('checked',false);                            
                        }

                        get_resume(id,'resume',ptype,table);
                    });

                    $('.view-resume').live('click', function () {
                        var id = $(this).attr('data-id');
                        var ptype = $(this).attr('data-ptype');
                        var table = $(this).attr('data-table');
                        $('.writting-review').css("display","none");

                        if($('#btn-find-tutoring').hasClass('active')){
                            $('#btn-find-tutoring').removeClass('active');
                            $('#open-find-tutoring').css("display","none");  
                            $('.radio_tutor_search').attr('checked',false);                          
                        }

                        get_resume(id,'resume',ptype,table);
                    });

                    $('.view-review').live('click', function () {
                        var id = $(this).attr('data-id');
                        var ptype = $(this).attr('data-ptype');
                        var table = $(this).attr('data-table');
                        $('.writting-review').css("display","none");  
					    
                        get_resume(id,'review',ptype,table);
                    });

                    $('.view-write-review').live('click', function () {
                        var tutor_id = $(this).attr('data-id');
                        var review_id = $(this).attr('data-review-id');
                        var userid = $(this).attr('data-userid');
                        var star = $(this).attr('data-star');
                        var subject = $(this).attr('data-subject');
                        var message = $(this).attr('data-message');
                        var ptype = $(this).attr('data-ptype');
                        var table = $(this).attr('data-table');

                        $('#write-review-subject').val(subject);
                        tinymce.get('message-review').setContent(message);
                        $('#btn-submit-review').attr('data-id',tutor_id); 
						$('#btn-submit-review').attr('data-userid',userid); 
                        $('#btn-submit-review').attr('data-review-id',review_id); 
                        $('#btn-submit-review').attr('data-ptype',ptype);

                    
                        if(star == 0){
                            $('.star_buttons').attr('checked',false);                            
                        }else{
                            $('.star_buttons').attr('checked',false);
                            $('input[name="star"]').each(function (i) {
                                if(i < star){
                                    $(this).attr('checked',true);
                                }
                            });
                        }                      

                        $('span.placeholder').each(function () {
                            var text = $(this).text();
                            var font = $(this).css("font");                                
                            if (document.documentMode || /Edge/.test(navigator.userAgent)) {
                                //console.log("Edge");
                                var offset = 14;
                            }else if (navigator.userAgent.search("Firefox") >= 0){
                                var offset = 12;
                                //console.log("Firefox");
                            }else{
                                var offset = 11;
                            }
                            var left = (getTextWidth(text,font) + offset);
                            $(this).prev().css("padding-left",left+"px");
                        });
                        get_resume(review_id,'write_review',ptype,table);
                    });

                    $("#btn-schedule").click(function(){   
                        var day = $('#today-tutor').val();
                        $('.new-request-list').text('SCHEDULE');      
                        //$('#custom-timezone').css("display","block");
                        //$("#menu-schedule-btn").text('Summary');
                        $("#menu-schedule-btn").attr("data-type","summary");  
                        $('#custom-timezone').attr("data-type","");
                        $('#custom-timezone').attr("data-day","");
                        
                        $('.header-schedule').removeClass('active');
                        $('#list-schedule-status').css("display","none");
                        $('#table-status-schedule').html('');
                        $("#open-menu-schedule").css("display","none");
						$(".main-view-status").css("display","none");

                        if($('#body-my-scheduled').hasClass('status-schedule')){
                            $('#body-my-scheduled').removeClass('status-schedule')
                        }

                        $('.radio_tutor_search').attr('checked',false);

                        var today = new Date(day.replace("-", ","));
                        var weekday = new Array(7);
                            weekday[0] =  "Sun";
                            weekday[1] = "Mon";
                            weekday[2] = "Tue";
                            weekday[3] = "Wed";
                            weekday[4] = "Thur";
                            weekday[5] = "Fri";
                            weekday[6] = "Sat";                                
                        var n = weekday[today.getDay()];
                        var month_text = getMonthtoText(today.getMonth()+1);
                        $('.current-day').text(month_text + ' ' + today.getDate());
                        $('.stuff-day').text(' (' + n + ')');

                        var prev_d = new Date(day.replace("-", ","));
                        prev_d.setDate(prev_d.getDate() - 1);
                        var dd = prev_d.getDate();
                        var mm = prev_d.getMonth()+1; //January is 0!
                        var yyyy = prev_d.getFullYear();
                        if(dd < 10) {
                            dd = "0"+dd;
                        }
                        if(mm < 10) {
                            mm = "0"+mm;
                        }

                        var next_d = new Date(day.replace("-", ","));
                        next_d.setDate(next_d.getDate() + 1); 
                        var dd_n = next_d.getDate();
                        var mm_n = next_d.getMonth()+1; //January is 0!
                        var yyyy_n = next_d.getFullYear();
                        if(dd_n < 10) {
                            dd_n = "0"+dd_n;
                        }
                        if(mm_n < 10) {
                            mm_n = "0"+mm_n;
                        }
                        
                        $("#menu-schedule-btn").attr('data-day',day);
                        $(".schedule-left-btn").attr('data-day',yyyy+'-'+mm+'-'+dd);
                        $(".schedule-right-btn").attr('data-day',yyyy_n+'-'+mm_n+'-'+dd_n);

                        $('.datepicker-days td.day').each(function () {
                            var full_date = $(this).attr('data-day');
                            var st = full_date.split("/");
                            var formattedDate = st[2] + "-" + st[0] + "-" + st[1];
                            
                            if($(this).hasClass('active disabled')){
                                $(this).removeClass('active disabled');
                                $(this).attr('data-action','selectDay');
                            }

                            if(day == formattedDate){
                                $(this).addClass('active disabled');
                                $(this).attr('data-action','disabled');
                            }
                        });

                        get_list_schedule();

                        get_scheduled_day(day, 'schedule', true);

                        if($(".main-my-schedule").hasClass('active-reschedule')){
                            $(".main-my-schedule").removeClass('active-reschedule');
                        }

                        if(!$(".main-my-schedule").hasClass('active-tab-schedule')){
                            $(".main-my-schedule").addClass("active-tab-schedule");
                        }

                        if($(".main-new-request").hasClass('active')){
                            $(".main-new-request").removeClass('active');
                        }

                        $(".main-my-schedule").css("display","block");
                        $(".section-tutor-main").css("display","none");
                        $(".main-new-request").css("display","none"); 
                        $(".main-view-request").css("display","none");
                        $(".main-status-request").css("display","none");
                        $(".writting-review").css("display","none");
                        $('.header-title-newschedule').css("display","none");

                        var viewport = getViewport();
                        if(viewport.width < 925){
                            $('.box-schedule-left').css("display","block");
                            $('#btn-open-calendar').css("display","block");
                            if(viewport.width < 650){
                                $('#tab-tutor-content .border-selectall').find('.col-md-6').css('width','53.8%');
                            }else{
                                $('#tab-tutor-content .border-selectall').find('.col-md-6').css('width','42.88%');
                            }
                        }else{
                            $('#tab-tutor-content .border-selectall').find('.col-md-6').css('width','67.2%');
                        }
                    });

                    $(".current-stuff").click(function(){   
                        var day = $("#menu-schedule-btn").attr('data-day');
                        $('.new-request-list').text('SCHEDULE');      
                        //$('#custom-timezone').css("display","block");
                        //$("#menu-schedule-btn").text('Summary');
                        $("#menu-schedule-btn").attr("data-type","summary");  
                        $('#custom-timezone').attr("data-type","");
                        $('#custom-timezone').attr("data-day","");
                        
                        $('.header-schedule').removeClass('active');
                        $('#list-schedule-status').css("display","none");
                        $('#table-status-schedule').html('');
                        $("#open-menu-schedule").css("display","none");
                        $(".main-view-status").css("display","none");

                        if($('#body-my-scheduled').hasClass('status-schedule')){
                            $('#body-my-scheduled').removeClass('status-schedule')
                        }

                        $('.radio_tutor_search').attr('checked',false);

                        var today = new Date(day.replace("-", ","));
                        var weekday = new Array(7);
                            weekday[0] =  "Sun";
                            weekday[1] = "Mon";
                            weekday[2] = "Tue";
                            weekday[3] = "Wed";
                            weekday[4] = "Thur";
                            weekday[5] = "Fri";
                            weekday[6] = "Sat";                                
                        var n = weekday[today.getDay()];
                        var month_text = getMonthtoText(today.getMonth()+1);
                        $('.current-day').text(month_text + ' ' + today.getDate());
                        $('.stuff-day').text(' (' + n + ')');

                        var prev_d = new Date(day.replace("-", ","));
                        prev_d.setDate(prev_d.getDate() - 1);
                        var dd = prev_d.getDate();
                        var mm = prev_d.getMonth()+1; //January is 0!
                        var yyyy = prev_d.getFullYear();
                        if(dd < 10) {
                            dd = "0"+dd;
                        }
                        if(mm < 10) {
                            mm = "0"+mm;
                        }

                        var next_d = new Date(day.replace("-", ","));
                        next_d.setDate(next_d.getDate() + 1); 
                        var dd_n = next_d.getDate();
                        var mm_n = next_d.getMonth()+1; //January is 0!
                        var yyyy_n = next_d.getFullYear();
                        if(dd_n < 10) {
                            dd_n = "0"+dd_n;
                        }
                        if(mm_n < 10) {
                            mm_n = "0"+mm_n;
                        }
                        
                        $("#menu-schedule-btn").attr('data-day',day);
                        $(".schedule-left-btn").attr('data-day',yyyy+'-'+mm+'-'+dd);
                        $(".schedule-right-btn").attr('data-day',yyyy_n+'-'+mm_n+'-'+dd_n);

                        $('.datepicker-days td.day').each(function () {
                            var full_date = $(this).attr('data-day');
                            var st = full_date.split("/");
                            var formattedDate = st[2] + "-" + st[0] + "-" + st[1];
                            
                            if($(this).hasClass('active disabled')){
                                $(this).removeClass('active disabled');
                                $(this).attr('data-action','selectDay');
                            }

                            if(day == formattedDate){
                                $(this).addClass('active disabled');
                                $(this).attr('data-action','disabled');
                            }
                        });

                        get_list_schedule();

                        get_scheduled_day(day);

                        if($(".main-my-schedule").hasClass('active-reschedule')){
                            $(".main-my-schedule").removeClass('active-reschedule');
                        }

                        if(!$(".main-my-schedule").hasClass('active-tab-schedule')){
                            $(".main-my-schedule").addClass("active-tab-schedule");
                        }

                        if($(".main-new-request").hasClass('active')){
                            $(".main-new-request").removeClass('active');
                        }

                        $(".main-my-schedule").css("display","block");
                        $(".section-tutor-main").css("display","none");
                        $(".main-new-request").css("display","none"); 
                        $(".main-view-request").css("display","none");
                        $(".main-status-request").css("display","none");
                        $(".writting-review").css("display","none");
                        $('.header-title-newschedule').css("display","none");

                        var viewport = getViewport();
                        if(viewport.width < 925){
                            $('.box-schedule-left').css("display","block");
                            $('#btn-open-calendar').css("display","block");
                            if(viewport.width < 650){
                                $('#tab-tutor-content .border-selectall').find('.col-md-6').css('width','53.8%');
                            }else{
                                $('#tab-tutor-content .border-selectall').find('.col-md-6').css('width','42.88%');
                            }
                        }else{
                            $('#tab-tutor-content .border-selectall').find('.col-md-6').css('width','67.2%');
                        }
                    });

                    $(".goto-main-schedule").click(function(){   
                        var day = $(this).attr("data-day");
                        var type = $(this).attr("data-type");
                        var tab = $(this).attr("data-tab");

                        $('#custom-timezone').attr("data-type","");
                        $('#custom-timezone').attr("data-day","");

                        if($(".main-my-schedule").hasClass('active-reschedule')){
                            $(".main-my-schedule").removeClass('active-reschedule');
                        }

                        if($(".main-new-request").hasClass('active')){
                            $(".main-new-request").removeClass('active');
                        }

                        if(type == 'schedule'){
                            //$("#menu-schedule-btn").text('Summary');
                            $("#menu-schedule-btn").attr("data-type","summary");
                            $('.new-request-list').text('SCHEDULE');                         
                            get_list_schedule();

                            get_scheduled_day(day);

                            if(!$(".main-my-schedule").hasClass('active-tab-schedule')){
                                $(".main-my-schedule").addClass("active-tab-schedule");
                            }

                            $(".main-my-schedule").css("display","block");
                            $(".main-status-request").css("display","none");
                        }else{
                            if($('.btn-sub-status').hasClass('active')){
                                var stype = $('.btn-sub-status').attr("data-type");        
                            }else{
                                stype = 'all';
                            }
                            get_status_request(stype);

                            if($(".main-my-schedule").hasClass('active-tab-schedule')){
                                $(".main-my-schedule").removeClass("active-tab-schedule");
                            }

                            $(".main-my-schedule").css("display","none");
                            $(".main-status-request").css("display","block");
                        }
                        $(".section-tutor-main").css("display","none");
                        $(".main-new-request").css("display","none"); 
                        $(".main-view-request").css("display","none"); 
                        $(".writting-review").css("display","none"); 
                        $('.header-title-newschedule').css("display","none");                      
                    });

                    $('.btn-select-time').click(function(){   
                        var user_points = $(this).attr('data-points');                     
                        var fromtime = $(this).attr("data-fromtime");                        
                        var totime = $(this).attr("data-totime");
                        var day = $(this).attr("data-day");
                        var time = $(this).attr("data-time");
                        var time_duration = $("#time-durationSelectBoxItText").attr("data-val");
                        var pst = "<?php echo mw_get_option('price_schedule_tutoring') ?>";
                        pst = parseInt(pst);

                        if(time_duration != ''){
                            $('#duration-tutoring-request').css("display", "none");
                            $('.name-request').find('span').text('REQUEST');

                            var viewport = getViewport();
                            if(viewport.width < 925){
                                if(!$('#custom-timezone').hasClass('request-page')){
                                    $('#custom-timezone').addClass('request-page');
                                }
                            }else{
                                if($('#custom-timezone').hasClass('request-page')){
                                    $('#custom-timezone').removeClass('request-page');
                                }
                            }

                            var ftime = fromtime.split(':');
                            var totime = time_duration.split(':');
                            var hftime = parseInt(ftime[0]);
                            var httime = parseInt(totime[0]);
                            if(hftime == 12 && ftime[2] == "am"){
                                var hfrtime = 0;
                            }else{
                                var hfrtime = hftime;
                            }
                            if(httime == 12 && totime[2] == "am"){
                                var htotime = 0;
                            }else{
                                var htotime = httime;
                            }                                                        
                            var hour = (htotime - hfrtime) * 60 + (parseInt(totime[1]) - parseInt(ftime[1]));
                            var total = hour * pst/100;

                            if(hftime > 12) hftime = hftime - 12;
                            if(httime > 12) httime = httime - 12;
							
							var time_sc1 = hftime+':'+ftime[1]+':'+ftime[2].toLowerCase();
                            var time_sc2 = httime+':'+totime[1]+':'+totime[2].toLowerCase();
							
                            var time = hftime+':'+ftime[1]+ftime[2] +'-'+ httime+':'+totime[1]+totime[2];

                            $('#btn-sent-request').attr("data-id","");
                            $('#btn-sent-request').attr("data-fromtime",fromtime);
                            $('#btn-sent-request').attr("data-totime",time_duration);
                            $('#btn-sent-request').attr("data-day",day);
                            $('#btn-sent-request').attr("data-time",time_sc1 + ' ~ ' + time_sc2);
                            $('#btn-sent-request').attr("data-total-time",hour);
                            $('#btn-sent-request').attr("data-total",total);
                            $('#btn-sent-request').attr("data-time-view",time);
                            $('#custom-timezone').attr("data-type","request");
                            $('#custom-timezone').attr("data-day",day);
                            if($(".main-my-schedule").hasClass('active-reschedule')){
                                $(".main-my-schedule").css("display","block");
                                $('.number-points').text('Already Paid (' + total + ' Points)');

                                if(!$(".main-my-schedule").hasClass('active-tab-schedule')){
                                    $(".main-my-schedule").addClass("active-tab-schedule");
                                }
                            }else{
                                if($(".main-my-schedule").hasClass('active-tab-schedule')){
                                    $(".main-my-schedule").removeClass("active-tab-schedule");
                                }

                                $(".main-my-schedule").css("display","none");
                                $('.number-points').text(total + ' Points($)');
                            }                            
                            $('.total-num-points').text(user_points + ' Points($)');

                            var today = new Date(day.replace("-", ","));                            
                            var weekday = new Array(7);
                                weekday[0] =  "Sun";
                                weekday[1] = "Mon";
                                weekday[2] = "Tue";
                                weekday[3] = "Wed";
                                weekday[4] = "Thur";
                                weekday[5] = "Fri";
                                weekday[6] = "Sat";                                
                            var n = weekday[today.getDay()];
                            var month_text = getMonthtoText(today.getMonth()+1);
                            $('.current-request-day').text(month_text + ' ' + today.getDate());
                            $('.stuff-request-day').text(' (' + n + ')');
                            $('.time-current').text(time);   
                            $('#btn-sent-request').attr("data-current-day",month_text + ' ' + today.getDate()+','+today.getFullYear());
                            $('#btn-sent-request').attr("data-stuff",n);                         

                            $('span.placeholder').each(function () {
                                var text = $(this).text();
                                var font = $(this).css("font");                                
                                if (document.documentMode || /Edge/.test(navigator.userAgent)) {
                                    //console.log("Edge");
                                    var offset = 28;
                                }else if (navigator.userAgent.search("Firefox") >= 0){
                                    var offset = 26;
                                    //console.log("Firefox");
                                }else{
                                    var offset = 25;
                                }
                                var left = (getTextWidth(text,font) + offset);
                                $(this).prev().css("padding-left",left+"px");
                            });

                            $('#search-title').val('');
                            tinyMCE.activeEditor.setContent('');
                            $('.radio_buttons_search').attr('checked',false);

                            //$("#request-time-zone").selectBoxIt('selectOption','0').data("selectBox-selectBoxIt");
                            //$("#request-time-zone").data("selectBox-selectBoxIt").refresh();
                            $('#table-search-tutor').html("");
                            var tr = '<tr><td colspan="3" class="no-list"><img src="<?php echo get_template_directory_uri(); ?>/library/images/icon_No_Schedule.png" alt="">Currently, there are no list</td></tr>';
                            $('#table-search-tutor').append(tr);
                            
                            $(".main-view-request").css("display","none");
                            $(".main-status-request").css("display","none");
                            $(".section-tutor-main").css("display","none");
                            $(".writting-review").css("display","none");                
                            $(".main-new-request").css("display","block");                             
                            $('.header-title-newschedule').css("display","block");
                        }else{                            
                            $('#popup-message').html('<p class="text-used">Please choose Time</p><button id="got-it" type="button" class="btn-orange form-control nopadding-r border-btn">OK</button>');
                            $('#top-popup-message').css("display", "block");
                        }                        
                    });

                    $('.btn-cancel-time').click(function(){
                        $('#duration-tutoring-request').css("display", "none");
                    });
					
                    $(".btn-new-request").live("click",function(){ 
                        var user_points = $(this).attr('data-points');
                        var fromtime = $(this).attr("data-fromtime");
                        var totime = $(this).attr("data-totime");
                        var day = $(this).attr("data-day");
                        var time = $(this).attr("data-time"); 
                        var request_index = $(this).attr("data-index");
                        var tr_id =  $(this).attr("data-id");
                        var cnt = $('#'+tr_id).index();  
                        var request_half = $(this).attr('data-half');
                        var name = $('#select-timezone :selected').attr("data-name");
                        var today = $('#today-tutor').val();

                        var CurrentDate = new Date(today);
                        var GivenDate = new Date(day); 
                        //alert(dmDate);
                        var str = fromtime.split(':');
                        var hr = parseInt(str[0]);

                        var hrtoday = $("#mytime-clock").attr("data-hour");
                        var mttoday = $("#mytime-clock").attr("data-minute");

                        var scurr = parseInt(hrtoday) * 60; // + parseInt(mttoday);
                        var egive = hr * 60 + parseInt(str[1]);
                        
                        if((today == day && egive >= scurr) || GivenDate > CurrentDate){  
                            var ftime = fromtime.split(':');
                            var totime = totime.split(':');
                            var hftime = parseInt(ftime[0]);
                            var httime = parseInt(totime[0]);
                            if(hftime == 12 && ftime[2] == "am"){
                                var hfrtime = 0;
                            }else{
                                var hfrtime = hftime;
                            }
                            if(httime == 12 && totime[2] == "am"){
                                var htotime = 0;
                            }else{
                                var htotime = httime;
                            }                                                        

                            if(hftime > 12) hftime = hftime - 12;
                            if(httime > 12) httime = httime - 12;
                            
                            var time_sc1 = hftime+':'+ftime[1]+':'+ftime[2].toLowerCase();
                            var time_sc2 = httime+':'+totime[1]+':'+totime[2].toLowerCase();
                            
                            var time = hftime+':'+ftime[1]+ftime[2];    
                            var str_day = day.split('-');
                            var dd = str_day[2];
                            var mm = str_day[1];

                            var path = '<?php echo get_template_directory_uri() ?>/library/images/';  
                            $('.new-request-list').text('Find a tutor');
                            $(".main-my-schedule").css("display","none");
                            $(".section-tutor-main").css("display","block");
                            $(".main-new-request").css("display","none");
                            $(".main-view-request").css("display","none");
                            $(".main-status-request").css("display","none");
                            $('.writting-review').css("display","none");
                            $('.header-title-newschedule').css("display","none");
                            $('.frm-available-now').css("display","none"); 
                            $('#custom-timezone').css("display","none");

                            $('.header-schedule').removeClass('active');
                            $('#list-schedule-status').css("display","none");
                            $('#table-status-schedule').html('');
                            $("#open-menu-schedule").css("display","none");
                            $(".main-view-status").css("display","none");

                            if($(".main-my-schedule").hasClass('active-tab-schedule')){
                                $(".main-my-schedule").removeClass("active-tab-schedule");
                            }

                            var viewport = getViewport();
                            if(viewport.width < 925){
                                $('#btn-open-calendar').css("display","none");
                                if(viewport.width < 650){
                                    $('#tab-tutor-content .border-selectall').find('.col-md-6').css('width','66.2%');
                                }else{
                                    $('#tab-tutor-content .border-selectall').find('.col-md-6').css('width','51.2%');
                                }
                            }else{
                                $('#tab-tutor-content .border-selectall').find('.col-md-6').css('width','67.2%');
                            }

                            $('.writting-review').css("display","none");
                            $('.toggle-btn').css("display","none");
                            $("#table-list-schedule").html('');
                            $("#tutoring-scheduled").html('');
                            
                            //var available_time = $("#mytime-clock").attr("data-available-time");
                            $("#select-available-month").selectBoxIt('selectOption',mm.toString()).data("selectBox-selectBoxIt");
                            $("#select-available-month").data("selectBox-selectBoxIt").refresh();

                            $("#select-available-day").selectBoxIt('selectOption',dd.toString()).data("selectBox-selectBoxIt");
                            $("#select-available-day").data("selectBox-selectBoxIt").refresh();

                            $("#select-available-time").selectBoxIt('selectOption',time.toString()).data("selectBox-selectBoxIt");
                            $("#select-available-time").data("selectBox-selectBoxIt").refresh();

                            if(!$('#btn-find-tutoring').hasClass('active')){
                                $('.btn-sub-tab').removeClass('active');
                                $('#btn-find-tutoring').addClass('active');
                                $('#btn-find-tutoring').find('img').attr('src',path + 'icon_Find.png');
                                $('#btn-list-review').find('img').attr('src',path + 'icon_L_Review.png');
                                $('#btn-list-favorite').find('img').attr('src',path + 'icon_L_Favorite.png');
                                $('#btn-list-tutoring').find('img').attr('src',path + 'icon_L_list.png');
                                $('#btn-available-now').find('img').attr('src',path + '04_Available_Now.png');
                            }

                            $('.radio_tutor_search').attr('checked',false);
                            $('.frm-available-now').css("display","block");
                            $('#table-detail-tutor').css("display","none");
                            $(".slide-resume").css('visibility','hidden');
                            var tr = '<tr><td class="no-results"><img src="' + path + 'icon_Not_Available.png" alt="">Currently, there are no results.</td></tr>';
                            $('#table-list-tutor').html(tr);
                        }                     
                    });
					
                    $('#btn-find-tutor').click(function(){
                        var search = $('#search-title').val();
                        var time_zone = $("#request-time-zoneSelectBoxItText").attr("data-val");
                        var description = $('#description_request_ifr').contents().find('#tinymce').text();
                        var subject_type = [];
                        $('input[name="subject_type_search"]:checked').each(function () {
                            var val = this.value;
                            if(val == '') var val = $(this).attr('data-subject');
                            subject_type.push(val);
                        });
                       
                        get_tutor_user('list', 'table-search-tutor', 'findtutor', '', '', '', subject_type);
                    });

                    $('#btn-search-tutoring').click(function(){         
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/'; 
                        $('.writting-review').css("display","none");                                     
                        if(!$(this).hasClass('active')){
                            $('.btn-sub-tab').removeClass('active');
                            $(this).addClass('active');
                            $(this).find('img').attr('src',path + 'icon_L_list_Selected.png');
                            $('#btn-search-review').find('img').attr('src',path + 'icon_L_Review.png');
                            $('#btn-search-favorite').find('img').attr('src',path + 'icon_L_Favorite.png');
                            $('#btn-search-fromclass').find('img').attr('src',path + 'icon_L_FromClass.png');
                        }
                        get_tutor_user('list', 'table-search-tutor', 'findtutor');
                    });

                    $('#btn-search-review').click(function(){  
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/'; 
                        $('.writting-review').css("display","none");                   
                        
                        if(!$(this).hasClass('active')){
                            $('.btn-sub-tab').removeClass('active');
                            $(this).addClass('active');
                            $(this).find('img').attr('src',path + 'icon_L_Review_Selected.png');
                            $('#btn-search-tutoring').find('img').attr('src',path + 'icon_L_list.png');
                            $('#btn-search-favorite').find('img').attr('src',path + 'icon_L_Favorite.png');
                            $('#btn-search-fromclass').find('img').attr('src',path + 'icon_L_FromClass.png');
                        }
                        get_tutor_user('review', 'table-search-tutor', 'findtutor');
                    });

                    $('#btn-search-favorite').click(function(){  
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                        $('.writting-review').css("display","none");                      
                        
                        if(!$(this).hasClass('active')){
                            $('.btn-sub-tab').removeClass('active');
                            $(this).addClass('active');
                            $(this).find('img').attr('src',path + 'icon_L_Favorite_Selected.png');
                            $('#btn-search-review').find('img').attr('src',path + 'icon_L_Review.png');
                            $('#btn-search-tutoring').find('img').attr('src',path + 'icon_L_list.png');
                            $('#btn-search-fromclass').find('img').attr('src',path + 'icon_L_FromClass.png');
                        }
                        get_tutor_user('favorite', 'table-search-tutor', 'findtutor');
                    });

                    $('#btn-search-fromclass').click(function(){ 
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                        $('.writting-review').css("display","none");                     
                        
                        if(!$(this).hasClass('active')){
                            $('.btn-sub-tab').removeClass('active');
                            $(this).addClass('active');
                            $(this).find('img').attr('src',path + 'icon_L_FromClass_Selected.png');
                            $('#btn-search-review').find('img').attr('src',path + 'icon_L_Review.png');
                            $('#btn-search-favorite').find('img').attr('src',path + 'icon_L_Favorite.png');
                            $('#btn-search-tutoring').find('img').attr('src',path + 'icon_L_list.png');
                        }
                        get_tutor_user('fromclass', 'table-search-tutor', 'findtutor');
                    });

                    $('#btn-sent-request').click(function(){
                        var id = $(this).attr("data-id");
                        var fromtime = $(this).attr("data-fromtime");
                        var totime = $(this).attr("data-totime");
                        var day = $(this).attr("data-day");
                        var time = $(this).attr("data-time"); 
                        var total_time = $(this).attr("data-total-time");
                        var total = $(this).attr("data-total");
                        var time_view = $(this).attr("data-time-view");
                        var stuff = $(this).attr("data-stuff");
                        var current_day = $(this).attr("data-current-day");
                        var uid = '<?php if ($is_user_logged_in) echo $current_user->ID; else echo 0; ?>';

                        var d = day.split('-');
                        var events = d[1]+"/"+d[2]+"/"+d[0];

                        var title = $('#search-title').val();
                        var time_zone = $('#request-time-zone').attr("data-value");
                        var time_zone_index = $("#request-time-zone").attr("data-index");
                        var description = $('#description_request_ifr').contents().find('#tinymce').text();
                        var subject_type = '';
                        $('input[name="subject_type_search"]:checked').each(function () {
                            var val = this.value;
                            if(val == '') var val = $(this).attr('data-subject');
                            subject_type = val;
                        });
                        var choose_tutor = '';
                        var tutor_name = '';
                        $('input[name="choose_tutor"]:checked').each(function () {
                            var val = this.value;
                            if(val == '') var val = $(this).attr('data-id');
                            choose_tutor = val;
                            tutor_name = $(this).attr('data-name');
                        });

                        var msg = '';                                
                        var form_valid = true;

                        if(title == '' || $.trim(title) == ''){
                            msg += 'Please enter Title</br>';
                            form_valid = false;
                        }

                        if(time_zone == '0' || $.trim(time_zone) == '0'){
                            msg += 'Please choose Time Zone</br>';
                            form_valid = false;
                        }

                        if(subject_type == ''){
                            msg += 'Please check the box of Subject</br>';
                            form_valid = false;
                        }

                        if(description == '' || $.trim(description) == ''){
                            msg += 'Please enter Message</br>';
                            form_valid = false;
                        }

                        /*if($.trim(description).length > 200){
                            msg += 'The Message is less than 200 characters</br>';
                            form_valid = false;
                        }*/

                        if(choose_tutor == ''){
                            msg += 'Please check the box of Tutor</br>';
                            form_valid = false;
                        }

                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';

                        if(form_valid){
                            active_day.push(day);
                            $.post(home_url + "/?r=ajax/save_tutoring_plan", {  
                                id: id,                                 
                                title: title,
                                time_zone: time_zone,
                                time_zone_index: time_zone_index,
                                subject_type: subject_type,
                                description: description,
                                choose_tutor: choose_tutor,
                                day: day,
                                time: time,
                                total: total,
                                total_time: total_time,
                            }, function (datas) {
                                if ($.trim(datas) != '0') { 
                                    $('#custom-timezone').attr("data-type","");
                                    $('#custom-timezone').attr("data-day","");

                                    $('.datepicker-days td.day').each(function () {
                                        var full_date = $(this).attr('data-day');
                                        var st = full_date.split("/");
                                        var formattedDate = st[2] + "-" + st[0] + "-" + st[1];
                                        if(day == formattedDate && !$(this).hasClass('activeClass')){
                                            $(this).addClass('activeClass');
                                        }
                                    });   

                                    $.get(home_url + "/?r=ajax/get_my_schedules", {timezone: time_zone}, function (data) {
                                        //console.log(data);
                                        data = JSON.parse(data);
                                        if (data.length > 0) {
                                            $('.slide-my-schedule').html('');
                                            $('.slide-my-schedule').removeClass('slick-initialized');
                                            $('.slide-my-schedule').removeClass('slick-slider');
                                            $.each(data, function (i, v) {
                                                var html_slide = '';
                                                html_slide += '<div class="item" data-fromhour="'+v.fromhour+'" data-fromminute="'+v.fromminute+'" data-tohour="'+v.tohour+'" data-tominute="'+v.tominute+'" data-day="'+v.day+'" data-type="'+v.totype+'">';
                                                    html_slide += '<div class="description-detail">';
                                                        html_slide += '<p class="subject-detail">';
                                                            html_slide += '<span class="name-subject">'+v.private_subject+'</span>';
                                                        html_slide += '</p>';
                                                        html_slide += '<p class="my-time-request">';
                                                            html_slide += '<span class="label-timezone">Date:</span>';
                                                            html_slide += '<span class="my-current-day">'+v.date+'</span>';
                                                            html_slide += '<span class="my-stuff-day">'+v.stuff+'/</span>';
                                                            html_slide += '<span class="my-time-current">'+v.time_view+'</span>';
                                                        html_slide += '</p>';
                                                        html_slide += '<p class="name-detail">';
                                                            html_slide += '<span class="label-tutor">Tutor:</span>';
                                                            html_slide += '<span class="name-tutor">'+v.tutor_name+'</span>';
                                                        html_slide += '</p>';
                                                        html_slide += '<p class="points-detail">';
                                                            html_slide += '<span class="label-points">Points:</span>';
                                                            html_slide += '<span class="name-points">'+v.total+' Points($)</span>';
                                                        html_slide += '</p>';
                                                    html_slide += '</div>';
                                                    if(v.type_slide == 'current'){
                                                        html_slide += '<button id="btn-start-now'+v.id+'" class="btn-dark-blue btn-start-now active" type="button" name="start-now" data-id="'+v.id+'" data-student-id="'+v.id_user+'" data-teacher-id="'+v.tutor_id+'" tabindex="0">Initiate Now!</button>';
                                                    }else{
                                                        html_slide += '<button id="btn-cancel-schedule'+v.id+'" class="btn-dark-blue btn-cancel-schedule active" type="button" name="cancel-schedule" data-id="'+v.id+'" data-student-id="'+v.id_user+'" data-teacher-id="'+v.tutor_id+'" tabindex="0"><img src="'+path+'icon_Most-Current-Arrow.png" alt=""> Most Current</button>';
                                                    }
                                                    html_slide += '<button class="cancel-now" id="cancel-now'+v.id+'" data-id="'+v.id+'">';
                                                        html_slide += '<img src="'+path+'close_white.png">';
                                                    html_slide += '</button>';
                                                html_slide += '</div>';

                                                $('.slide-my-schedule').append(html_slide);
                                            });
                                            if(data.length > 1)
                                                $('.slide-my-schedule').not('.slick-initialized').slick(getSliderSettings(true,1));
                                            else
                                                $('.slide-my-schedule').not('.slick-initialized').slick(getSliderSettings(true,0));
                                        }else{
                                            $('.slide-my-schedule').html('');
                                            $('.slide-my-schedule').removeClass('slick-initialized');
                                            $('.slide-my-schedule').removeClass('slick-slider');
                                            var html_slide = '';
                                            html_slide += '<div class="item no-detail-schedule">';
                                                html_slide += '<div class="description-detail">';
                                                    html_slide += '<p class="subject-detail">';
                                                        html_slide += '<span class="name-subject">Currently there\'s no schedules</span>';
                                                    html_slide += '</p>';
                                                    html_slide += '<p class="my-time-request">';
                                                        html_slide += '<span class="label-timezone">Date:</span>';
                                                        html_slide += '<span class="my-current-day">N/A</span>';
                                                        html_slide += '<span class="my-stuff-day"></span>';
                                                        html_slide += '<span class="my-time-current"></span>';
                                                    html_slide += '</p>';
                                                    html_slide += '<p class="name-detail">';
                                                        html_slide += '<span class="label-tutor">Tutor:</span>';
                                                        html_slide += '<span class="name-tutor">N/A</span>';
                                                    html_slide += '</p>';
                                                    html_slide += '<p class="points-detail">';
                                                        html_slide += '<span class="label-points">Points:</span>';
                                                        html_slide += '<span class="name-points">0 Points($)</span>';
                                                    html_slide += '</p>';
                                                html_slide += '</div>';
                                                html_slide += '<button id="btn-cancel-schedule" class="btn-dark-blue btn-cancel-schedule no-active" type="button" name="cancel-schedule" data-id="" data-student-id="" data-teacher-id="" tabindex="0"><img src="'+path+'icon_Most-Current-Arrow.png" alt=""> Most Current</button>';
                                            html_slide += '</div>';

                                            $('.slide-my-schedule').append(html_slide);
                                            $('.slide-my-schedule').not('.slick-initialized').slick(getSliderSettings(true,0));
                                        }
                                    });                                
                                    /*$('#sandbox-container-tutor').datepicker('update');
                                    $('#sandbox-container-tutor').datepicker({
                                        todayHighlight: true,
                                        templates: {
                                            leftArrow: '<img src="<?php echo get_template_directory_uri(); ?>/library/images/Chalendar_icon_Left.png" height="15">',
                                            rightArrow: '<img src="<?php echo get_template_directory_uri(); ?>/library/images/Chalendar_icon_Right.png" height="15">'
                                        },
                                        beforeShowDay: function(date){
                                            var d = date;
                                            var curr_date = d.getDate();
                                            var curr_month = d.getMonth() + 1; //Months are zero based
                                            if(curr_month < 10) {
                                                curr_month = "0"+curr_month;
                                            }
                                            var curr_year = d.getFullYear();
                                            var formattedDate = curr_year + "-" + curr_month + "-" + curr_date;
                                            if($.inArray(formattedDate, active_day) != -1){
                                                return {
                                                   classes: 'activeClass'
                                                };
                                            }
                                            return;
                                        }
                                    });*/

                                    $('#search-title').val('');
                                    tinyMCE.activeEditor.setContent('');
                                    $('.radio_buttons_search').attr('checked',false);  
                                    $('.main-new-request').css("display","none"); 
                                    $(".main-view-request").css("display","none");
                                    $(".main-status-request").css("display","none");
                                    $(".section-tutor-main").css("display","none"); 
                                    $(".writting-review").css("display","none");
                                    $('.header-title-newschedule').css("display","none");
                                    if($(".main-my-schedule").hasClass('active-reschedule')){
                                        $(".main-my-schedule").removeClass('active-reschedule');
                                    }
                                    if($(".main-new-request").hasClass('active')){
                                        $(".main-new-request").removeClass('active');
                                    }
                                    $(".main-my-schedule").css("display","block");   

                                    if(!$(".main-my-schedule").hasClass('active-tab-schedule')){
                                        $(".main-my-schedule").addClass("active-tab-schedule");
                                    }                           
                                    get_scheduled_day(day);
                                }else{
                                    $('#popup-message').html('<p class="text-used">User point is not enough to create a new request.</p><button id="got-it" type="button" class="btn-orange form-control nopadding-r border-btn">OK</button>');
                                    $('#top-popup-message').css("display", "block");
                                }                                         
                            });
                        }else{
                            $('#popup-message').html('<p class="text-used">' + msg + '</p><button id="got-it" type="button" class="btn-orange form-control nopadding-r border-btn">OK</button>');
                            $('#top-popup-message').css("display", "block");
                        }
                    });

                    $("#sub-schedule-li").click(function(){
                        $('.new-request-list').text('SCHEDULE');  
                        //$('#custom-timezone').css("display","block");
                        //$("#menu-schedule-btn").text('Summary');
                        $("#menu-schedule-btn").attr("data-type","summary");
                        $('#custom-timezone').attr("data-type","");
                        $('#custom-timezone').attr("data-day","");

                        $('.header-schedule').removeClass('active');
                        $('#list-schedule-status').css("display","none");
                        $('#table-status-schedule').html('');
                        $("#open-menu-schedule").css("display","none");
						$(".main-view-status").css("display","none");

                        if($('#body-my-scheduled').hasClass('status-schedule')){
                            $('#body-my-scheduled').removeClass('status-schedule')
                        }

                        var day = $('#today-tutor').val();
                        var viewport = getViewport();
                        if(viewport.width < 925){
                            $('.box-schedule-left').css("display","block");
                            $('#btn-open-calendar').css("display","block");
                            if(viewport.width < 650){
                                $('#tab-tutor-content .border-selectall').find('.col-md-4').css('width','42.88%');
                            }else{
                                $('#tab-tutor-content .border-selectall').find('.col-md-4').css('width','35.88%');
                            }
                        }else{
                            $('#tab-tutor-content .border-selectall').find('.col-md-4').css('width','62.65%');
                        }

                        if($(".main-my-schedule").hasClass('active-reschedule')){
                            $(".main-my-schedule").removeClass('active-reschedule');
                        }

                        if($(".main-new-request").hasClass('active')){
                            $(".main-new-request").removeClass('active');
                        }

                        if(!$(".main-my-schedule").hasClass('active-tab-schedule')){
                            $(".main-my-schedule").addClass("active-tab-schedule");
                        }

                        var today = new Date(day.replace("-", ","));
                        var weekday = new Array(7);
                            weekday[0] =  "Sun";
                            weekday[1] = "Mon";
                            weekday[2] = "Tue";
                            weekday[3] = "Wed";
                            weekday[4] = "Thur";
                            weekday[5] = "Fri";
                            weekday[6] = "Sat";                                
                        var n = weekday[today.getDay()];
                        var month_text = getMonthtoText(today.getMonth()+1);
                        $('.current-day').text(month_text + ' ' + today.getDate());
                        $('.stuff-day').text(' (' + n + ')');

                        var prev_d = new Date(day.replace("-", ","));
                        prev_d.setDate(prev_d.getDate() - 1);
                        var dd = prev_d.getDate();
                        var mm = prev_d.getMonth()+1; //January is 0!
                        var yyyy = prev_d.getFullYear();
                        if(dd < 10) {
                            dd = "0"+dd;
                        }
                        if(mm < 10) {
                            mm = "0"+mm;
                        }

                        var next_d = new Date(day.replace("-", ","));
                        next_d.setDate(next_d.getDate() + 1); 
                        var dd_n = next_d.getDate();
                        var mm_n = next_d.getMonth()+1; //January is 0!
                        var yyyy_n = next_d.getFullYear();
                        if(dd_n < 10) {
                            dd_n = "0"+dd_n;
                        }
                        if(mm_n < 10) {
                            mm_n = "0"+mm_n;
                        }
                        
                        $("#menu-schedule-btn").attr('data-day',day);
                        $(".schedule-left-btn").attr('data-day',yyyy+'-'+mm+'-'+dd);
                        $(".schedule-right-btn").attr('data-day',yyyy_n+'-'+mm_n+'-'+dd_n);

                        $('.datepicker-days td.day').each(function () {
                            var full_date = $(this).attr('data-day');
                            var st = full_date.split("/");
                            var formattedDate = st[2] + "-" + st[0] + "-" + st[1];
                            
                            if($(this).hasClass('active disabled')){
                                $(this).removeClass('active disabled');
                                $(this).attr('data-action','selectDay');
                            }

                            if(day == formattedDate){
                                $(this).addClass('active disabled');
                                $(this).attr('data-action','disabled');
                            }
                        });

                        get_list_schedule();

                        get_scheduled_day(day, 'schedule', true);

                        $(".main-my-schedule").css("display","block");
                        $(".section-tutor-main").css("display","none");
                        $('.main-new-request').css("display","none"); 
                        $(".main-view-request").css("display","none");
                        $(".main-status-request").css("display","none");
                        $(".writting-review").css("display","none");
                        $('.header-title-newschedule').css("display","none");
                    });

                    $("#got-schedule").live("click",function(){
                        $('.new-request-list').text('SCHEDULE');
                        $('#popup-message').html();
                        $('#top-popup-message').css("display", "none");

                        if(!$(".main-my-schedule").hasClass('active-tab-schedule')){
                            $(".main-my-schedule").addClass("active-tab-schedule");
                        }

                        var day = $(this).attr('data-day');

                        get_list_schedule();

                        get_scheduled_day(day, 'schedule', true);

                        $(".main-my-schedule").css("display","block");
                        $(".section-tutor-main").css("display","none");
                        $('.main-new-request').css("display","none"); 
                        $(".main-view-request").css("display","none");
                        $(".main-status-request").css("display","none");
                        $(".writting-review").css("display","none");
                        $('.header-title-newschedule').css("display","none");
                    });

                    $('.view-detail-schedule').live("click",function(){
                        /*var id = $(this).attr("data-id");
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                        if($('#btn-my-schedule').hasClass('active')){
                            $('#top-my-schedules').css({"display":"none"});
                            $('body').removeClass('open-myschedule');
                            $('#btn-my-schedule').removeClass('active');
                            $('#btn-my-schedule').find('img').attr('src',path + '01_icon_Schedule_Starter.png');
                        }else{
                            $('#top-my-schedules').css({"display":"block"});
                            $('body').addClass('open-myschedule');
                            $('#btn-my-schedule').addClass('active');
                            $('#btn-my-schedule').find('img').attr('src',path + 'icon_CONFIRM-CLOSE.png');

                            //$('.btn-cancel-schedule').attr('data-id',id);
                        }*/
						var cl = $(this).attr("data-class");
						var subject = $(this).attr("data-subject");
						var private_subject = $(this).attr("data-private-subject");
						var student_name = $(this).attr("data-student-name");
						var tutor_name = $(this).attr("data-tutor-name");
						var date = $(this).attr("data-date");
						var stuff = $(this).attr("data-stuff");
						var message = $(this).attr("data-message");
						var note = $(this).attr("data-note");
						var time = $(this).attr("data-time");
						var time_view = $(this).attr("data-time-view");
						var fromtime = $(this).attr("data-fromtime");
						var totime = $(this).attr("data-totime");
						var total = $(this).attr("data-total");
						var total_time = $(this).attr("data-total-time");
						var day = $(this).attr("data-day");
						var confirmed = $(this).attr("data-confirmed");
						var canceled = $(this).attr("data-canceled");
						var id = $(this).attr("data-id");
						var student_id = $(this).attr("data-student-id");
						var teacher_id = $(this).attr("data-teacher-id");
						var status = $(this).attr("data-status");
                        var accepted = $(this).attr("data-accepted");
						var icon = $(this).attr("data-icon");
						var create_on = $(this).attr("data-create-on");
						var created = $(this).attr("data-created");
						var path = '<?php echo get_template_directory_uri() ?>/library/images/';
						
						var review_schedule = 'Session Not Completed Yet';
						var point_schedule = ' <span class="spent"></span>';
						
						if($.trim(note) == 'null') note = '';
						
						if($.trim(cl) == 'accepted'){
							var current_status = 'Completed';
							var type_status = 'confirmed';
							point_schedule = ' <span class="spent">(spent)</span>';
							review_schedule = '<a href="https://notepad.iktutor.com/en/?sid='+id+'&user_id='+student_id+'&teacher_id='+teacher_id+'" target="_blank">Review Session Again</a>';
							
							if(!$('#completed-status-btn').hasClass('active')){
								$('.list-schedule-status').removeClass('active');
								$('#completed-status-btn').addClass('active');
								$('#completed-status-btn').find('img').attr('src',path + 'TimeIcon_Completed.png');
								$('#all-status-btn').find('img').attr('src',path + 'TimeIcon_Summary_disable.png');
								$('#scheduled-status-btn').find('img').attr('src',path + 'TimeIcon_Scheduled_disable.png');
								$('#expired-status-btn').find('img').attr('src',path + 'TimeIcon_Expired_disable.png');
							}
							
							if(!$('.btn-status-schedule').hasClass('active')){
								$('.btn-status-schedule').addClass('active');
							}

                            $('.cancel-this-schedule').css("display","none");
						}else if($.trim(cl) == 'canceled'){
							if(accepted == 2)
                                var current_status = 'Canceled by Tutor';
                            else
                                var current_status = 'Canceled';

							var type_status = 'canceled';
							
							if(!$('#expired-status-btn').hasClass('active')){
								$('.list-schedule-status').removeClass('active');
								$('#expired-status-btn').addClass('active');
								$('#expired-status-btn').find('img').attr('src',path + 'TimeIcon_Expired.png');
								$('#all-status-btn').find('img').attr('src',path + 'TimeIcon_Summary_disable.png');
								$('#completed-status-btn').find('img').attr('src',path + 'TimeIcon_Completed_disable.png');
								$('#scheduled-status-btn').find('img').attr('src',path + 'TimeIcon_Scheduled_disable.png');
							}
							
							if(!$('.btn-status-schedule').hasClass('active')){
								$('.btn-status-schedule').addClass('active');
							}

                            $('.cancel-this-schedule').css("display","none");
						}else{
                            if(accepted == 1)
                                var current_status = 'Confirmed';
                            else if(accepted == 2)
                                var current_status = 'Canceled by Tutor';
                            else
                                var current_status = 'Waiting for Confirmation';

							var type_status = 'waiting';
							
							if(!$('#scheduled-status-btn').hasClass('active')){
								$('.list-schedule-status').removeClass('active');
								$('#scheduled-status-btn').addClass('active');
								$('#scheduled-status-btn').find('img').attr('src',path + 'TimeIcon_Scheduled.png');
								$('#all-status-btn').find('img').attr('src',path + 'TimeIcon_Summary_disable.png');
								$('#completed-status-btn').find('img').attr('src',path + 'TimeIcon_Completed_disable.png');
								$('#expired-status-btn').find('img').attr('src',path + 'TimeIcon_Expired_disable.png');
							}
							
							if(!$('.btn-status-schedule').hasClass('active')){
								$('.btn-status-schedule').addClass('active');
							}

                            $('.cancel-this-schedule').css("display","block");     
						}
						
						if(note == ''){
							$('.btn-status-schedule').text('Save Note');
						}else{
							$('.btn-status-schedule').text('Edit Note');
							tinymce.get('note_status_schedule').setContent(note);
						}
						
						$('.name-status-schedule').find('img').attr('src',path + icon);
						if($.trim(subject) == 'null')
							$('.name-status-schedule').find('span').text(private_subject);
						else
							$('.name-status-schedule').find('span').text(subject);
						$('#date-schedule').text(date + stuff + ' ' + time_view);
						$('#current-status').text(current_status);
						$('#name-tutor-detail').text(tutor_name);
						$('#point-schedule').html(total + 'Points($)' + point_schedule);
						$('#review-schedule').addClass($.trim(cl));
						$('#review-schedule').html(review_schedule);
						$('.close-status-schedule').attr('data-status',type_status);
						$('.btn-status-schedule').attr('data-id',id);
                        $('#yes-cancel-it').attr('data-id',id);
                        $('#no-cancel-it').attr('data-id',id);
						
						$('#list-schedule-status').css("display","block");
						$('#table-status-schedule').html('');
						$('#table-list-schedule').html('');
						$('#tutoring-scheduled').html('');
						$("#open-menu-schedule").css("display","none");
                        $("#open-menu-cancel").css("display","none");
						$(".header-schedule").addClass('active');
						$(".body-my-scheduled").addClass('status-schedule');
						$(".main-view-status").css("display","block");
                    });

                    $('#btn-my-schedule').click(function(e){
                        $('#open-menu-quicknotifi').css({"display":"none"});
                        $('#open-list-quicknotifi').css({"display":"none"});
                        $("#my-timezone").css("display","none");

                        e.preventDefault();
                        var id = $(this).attr("data-id");
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';

                        if($("#open-menu-cancel0").hasClass('active')){
                            $("#open-menu-cancel0").css("display","none");
                            $("#open-menu-cancel0").removeClass('active');
                        }

                        if($(this).hasClass('active')){
                            $('#top-my-schedules').css({"display":"none"});
                            $('body').removeClass('open-myschedule');
                            $(this).removeClass('active');
                            $(this).find('img').attr('src',path + '01_icon_Schedule_Starter.png');
                        }else{
                            $.get(home_url + "/?r=ajax/get_my_schedules", {timezone: ''}, function (data) {
                                //console.log(data);
                                data = JSON.parse(data);
                                if (data.length > 0) {
                                    $('.slide-my-schedule').html('');
                                    $('.slide-my-schedule').removeClass('slick-initialized');
                                    $('.slide-my-schedule').removeClass('slick-slider');
                                    $.each(data, function (i, v) {
                                        var html_slide = '';
                                        html_slide += '<div class="item" data-fromhour="'+v.fromhour+'" data-fromminute="'+v.fromminute+'" data-tohour="'+v.tohour+'" data-tominute="'+v.tominute+'" data-day="'+v.day+'" data-type="'+v.totype+'">';
                                            html_slide += '<div class="description-detail">';
                                                html_slide += '<p class="subject-detail">';
                                                    html_slide += '<span class="name-subject">'+v.private_subject+'</span>';
                                                html_slide += '</p>';
                                                html_slide += '<p class="my-time-request">';
                                                    html_slide += '<span class="label-timezone">Date:</span>';
                                                    html_slide += '<span class="my-current-day">'+v.date+'</span>';
                                                    html_slide += '<span class="my-stuff-day">'+v.stuff+'/</span>';
                                                    html_slide += '<span class="my-time-current">'+v.time_view+'</span>';
                                                html_slide += '</p>';
                                                html_slide += '<p class="name-detail">';
                                                    html_slide += '<span class="label-tutor">Tutor:</span>';
                                                    html_slide += '<span class="name-tutor">'+v.tutor_name+'</span>';
                                                html_slide += '</p>';
                                                html_slide += '<p class="points-detail">';
                                                    html_slide += '<span class="label-points">Points:</span>';
                                                    html_slide += '<span class="name-points">'+v.total+' Points($)</span>';
                                                html_slide += '</p>';
                                            html_slide += '</div>';
                                            if(v.type_slide == 'current'){
                                                html_slide += '<button id="btn-start-now'+v.id+'" class="btn-dark-blue btn-start-now active" type="button" name="start-now" data-id="'+v.id+'" data-student-id="'+v.id_user+'" data-teacher-id="'+v.tutor_id+'" tabindex="0">Initiate Now!</button>';
                                            }else{
                                                html_slide += '<button id="btn-cancel-schedule'+v.id+'" class="btn-dark-blue btn-cancel-schedule active" type="button" name="cancel-schedule" data-id="'+v.id+'" data-student-id="'+v.id_user+'" data-teacher-id="'+v.tutor_id+'" tabindex="0"><img src="'+path+'icon_Most-Current-Arrow.png" alt=""> Most Current</button>';
                                            }
                                            html_slide += '<button class="cancel-now" id="cancel-now'+v.id+'" data-id="'+v.id+'">';
                                                html_slide += '<img src="'+path+'close_white.png">';
                                            html_slide += '</button>';
                                        html_slide += '</div>';

                                        $('.slide-my-schedule').append(html_slide);
                                    });
                                    if(data.length > 1)
                                        $('.slide-my-schedule').not('.slick-initialized').slick(getSliderSettings(true,1));
                                    else
                                        $('.slide-my-schedule').not('.slick-initialized').slick(getSliderSettings(true,0));
                                }else{
                                    $('.slide-my-schedule').html('');
                                    $('.slide-my-schedule').removeClass('slick-initialized');
                                    $('.slide-my-schedule').removeClass('slick-slider');
                                    var html_slide = '';
                                    html_slide += '<div class="item no-detail-schedule">';
                                        html_slide += '<div class="description-detail">';
                                            html_slide += '<p class="subject-detail">';
                                                html_slide += '<span class="name-subject">Currently there\'s no schedules</span>';
                                            html_slide += '</p>';
                                            html_slide += '<p class="my-time-request">';
                                                html_slide += '<span class="label-timezone">Date:</span>';
                                                html_slide += '<span class="my-current-day">N/A</span>';
                                                html_slide += '<span class="my-stuff-day"></span>';
                                                html_slide += '<span class="my-time-current"></span>';
                                            html_slide += '</p>';
                                            html_slide += '<p class="name-detail">';
                                                html_slide += '<span class="label-tutor">Tutor:</span>';
                                                html_slide += '<span class="name-tutor">N/A</span>';
                                            html_slide += '</p>';
                                            html_slide += '<p class="points-detail">';
                                                html_slide += '<span class="label-points">Points:</span>';
                                                html_slide += '<span class="name-points">0 Points($)</span>';
                                            html_slide += '</p>';
                                        html_slide += '</div>';
                                        html_slide += '<button id="btn-cancel-schedule" class="btn-dark-blue btn-cancel-schedule no-active" type="button" name="cancel-schedule" data-id="" data-student-id="" data-teacher-id="" tabindex="0"><img src="'+path+'icon_Most-Current-Arrow.png" alt=""> Most Current</button>';
                                    html_slide += '</div>';

                                    $('.slide-my-schedule').append(html_slide);
                                    $('.slide-my-schedule').not('.slick-initialized').slick(getSliderSettings(true,0));
                                }
                            });

                            $('#top-my-schedules').css({"display":"block"});
                            $('body').addClass('open-myschedule');
                            $(this).addClass('active');
                            //$(this).find('img').attr('src',path + 'icon_CONFIRM-CLOSE.png');

                            //$('.btn-cancel-schedule').attr('data-id',id);
                        }
                        
                    });

                    $('.goto-calendar').click(function(){
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                        if($('#btn-my-schedule').hasClass('active')){
                            $('#top-my-schedules').css({"display":"none"});
                            $('body').removeClass('open-myschedule');
                            $('#btn-my-schedule').removeClass('active');
                            $('#btn-my-schedule').find('img').attr('src',path + '01_icon_Schedule_Starter.png');
                        }

                        $("#create-account").removeClass("active");
                        $("#create-account").removeClass("in");
                        $("#login-user").removeClass("active");
                        $("#login-user").removeClass("in");
                        $("#updateinfo").removeClass("active");
                        $("#updateinfo").removeClass("in");
                        $("#subscription").removeClass("active");
                        $("#subscription").removeClass("in");
                        $("#profile").removeClass("active");
                        $("#profile").removeClass("in");
                        $("#tutoring-main").addClass("active");
                        $("#tutoring-main").addClass("in");

                        $('.new-request-list').text('SCHEDULE');  
                        //$('#custom-timezone').css("display","block");
                        //$("#menu-schedule-btn").text('Summary');
                        $("#menu-schedule-btn").attr("data-type","summary");
                        $('#custom-timezone').attr("data-type","");
                        $('#custom-timezone').attr("data-day","");

                        var day = $('#menu-schedule-btn').attr('data-day');
                        var viewport = getViewport();
                        if(viewport.width < 925){
                            $('.box-schedule-left').css("display","block");
                            $('#btn-open-calendar').css("display","block");
                            if(viewport.width < 650){
                                $('#tab-tutor-content .border-selectall').find('.col-md-4').css('width','42.88%');
                            }else{
                                $('#tab-tutor-content .border-selectall').find('.col-md-4').css('width','35.88%');
                            }
                        }else{
                            $('#tab-tutor-content .border-selectall').find('.col-md-4').css('width','62.65%');
                        }

                        if($(".main-my-schedule").hasClass('active-reschedule')){
                            $(".main-my-schedule").removeClass('active-reschedule');
                        }

                        if($(".main-new-request").hasClass('active')){
                            $(".main-new-request").removeClass('active');
                        }

                        if(!$(".main-my-schedule").hasClass('active-tab-schedule')){
                            $(".main-my-schedule").addClass("active-tab-schedule");
                        }

                        get_list_schedule();

                        get_scheduled_day(day, 'schedule', true);

                        $(".main-my-schedule").css("display","block");
                        $(".section-tutor-main").css("display","none");
                        $('.main-new-request').css("display","none"); 
                        $(".main-view-request").css("display","none");
                        $(".main-status-request").css("display","none");
                        $(".writting-review").css("display","none");
                        $('.header-title-newschedule').css("display","none");
                    });

                    $('.btn-cancel-schedule').live("click",function(){
                        var id = $(this).attr("data-id");
                        $('.slide-my-schedule').slick('slickGoTo', 0);
                        /*if($(this).hasClass('active')){
                            $('#btn-start-now'+id).addClass('future-active');
                            $(this).removeClass('active');
                        }
                        
                        $.post(home_url + "/?r=ajax/cancel_confirm_tutoring", {                                   
                            id: id,
                            canceled: canceled
                        }, function (data) {
                            if ($.trim(data) == '1') {
                                $('#top-my-schedules').css("display", "none");
                                $('body').removeClass('open-myschedule');
                            }                                         
                        });*/
                    });

                    $('.btn-start-now').live('click', function () {
                        var id = $(this).attr("data-id");
                        var student_id = $(this).attr("data-student-id");
                        var teacher_id = $(this).attr("data-teacher-id");
                        //$('#top-my-schedules').css("display", "none");
                        //$('body').removeClass('open-myschedule');
                        if($(this).hasClass('active')){
                            $('<a href="https://notepad.iktutor.com/en/?sid='+id+'&user_id='+student_id+'&teacher_id='+teacher_id+'" target="_blank">Link</a>')[0].click();
                        }
                    });

                    $(".slide-my-schedule").on('swipe', function(event, slick, direction){
                        /*$('.btn-cancel-schedule').removeClass('active');
                        $('.btn-start-now').each(function () {
                            if(!$('.btn-start-now').hasClass('active')){
                                $('.btn-start-now').hasClass('active');
                            }
                        });*/
                        if($("#open-menu-cancel0").hasClass('active')){
                            $("#open-menu-cancel0").css("display","none");
                            $("#open-menu-cancel0").removeClass('active');
                        }
                    });

                    $('.slick-my-schedule').live('click', function () {
                        /*$('.btn-cancel-schedule').removeClass('active');
                        $('.btn-start-now').each(function () {
                            if(!$('.btn-start-now').hasClass('active')){
                                $('.btn-start-now').hasClass('active');
                            }
                        });*/
                        if($("#open-menu-cancel0").hasClass('active')){
                            $("#open-menu-cancel0").css("display","none");
                            $("#open-menu-cancel0").removeClass('active');
                        }
                    });

                    $(".check-toggle").live("click", function () {
                        var check = $(this).is(":checked");
                        var type = $(this).attr("data-type");
                        $(this).parent().find("div.lable-toggle").removeClass('inactive');
                        $(this).parent().find("div.lable-toggle").removeClass('active');
                        if (check == false) {
                            $(this).parent().find("img").attr("src", "<?php echo get_template_directory_uri(); ?>/library/images/icon_Toggle_Switch_OFF.png");
                            $(this).attr("checked", false);
                            $(this).parent().find("div.lable-toggle").addClass('inactive');
                            get_tutor_user(type,'table-list-tutor','tutor','','','','','','','','','','');
                        } else {
                            $(this).parent().find("img").attr("src", "<?php echo get_template_directory_uri(); ?>/library/images/icon_Toggle_Switch_ON.png");
                            $(this).attr("checked", true);
                            $(this).parent().find("div.lable-toggle").addClass('active');
                            get_tutor_user(type,'table-list-tutor','tutor','','','','','','','','','','available');
                        }
                    });

                    $('.goto-close-schedule').click(function(){
                        $('#top-my-schedules').css("display", "none");
                        $('body').removeClass('open-myschedule');
                    });

                    /*$('.btn-schedule-now').live("click",function(){
                        var id = $(this).attr("data-id");
                        $.post(home_url + "/?r=ajax/get_schedule_now", {  
                            id: id
                        }, function (data) {
                            data = JSON.parse(data);
                            if (data.id) {
                                $('#popup-message').html('<p class="text-used"><span class="popup-total"><strong>' + data.total + ' Points($)</strong> will be used for this schedule.</span><br/><span class="popup-points">You have total of <strong>' + data.user_points + ' Points($)</strong> Remaining. To Purchase more Points, <a href="">Click here</a>.</span"></p><button id="got-accept" data-total="' + data.total + '" data-id="' + data.id + '" type="button" class="btn-got-accept">Accept</button><button id="got-canceled" data-id="' + data.id + '" type="button" class="btn-got-canceled">Cancel</button>');
                                $('#top-popup-message').css("display", "block");
                            }
                        });
                    });*/

                    $('#got-accept').live("click",function(){
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                        var choose_tutor = $('#btn-schedule-now').attr("data-tutor-id");
                        var time_zone = $('#request-time-zone').attr("data-value");
                        var time_zone_index = $("#request-time-zone").attr("data-index");
                        var day = $('#btn-schedule-now').attr("data-day");
                        var time = $('#btn-schedule-now').attr("data-time"); 
                        var time_view = $('#btn-schedule-now').attr("data-time-view"); 
                        var total_time = $('#btn-schedule-now').attr("data-total-time");
                        var total = $('#btn-schedule-now').attr("data-total");
                        var description = '';
                        var id = '';
                        var subject_type = $('#btn-schedule-now').attr("data-subject");
                        var tutor_name = $('#btn-schedule-now').attr("data-name");
                        var uid = '<?php if ($is_user_logged_in) echo $current_user->ID; else echo 0; ?>';
                        var table = $(this).attr('data-table');
                        var ptype = $(this).attr('data-ptype');  
                                                    
                        var today = new Date(day.replace("-", ","));                            
                        var weekday = new Array(7);
                            weekday[0] =  "Sun";
                            weekday[1] = "Mon";
                            weekday[2] = "Tue";
                            weekday[3] = "Wed";
                            weekday[4] = "Thur";
                            weekday[5] = "Fri";
                            weekday[6] = "Sat";                                
                        var n = weekday[today.getDay()];
                        var month_text = getMonthtoText(today.getMonth()+1);

                        $.post(home_url + "/?r=ajax/save_tutoring_plan", {  
                            id: id,                                 
                            title: subject_type,
                            time_zone: time_zone,
                            time_zone_index: time_zone_index,
                            subject_type: subject_type,
                            description: description,
                            choose_tutor: choose_tutor,
                            day: day,
                            time: time,
                            total: total,
                            total_time: total_time,
                        }, function (data) {
                            if ($.trim(data) != '0') { 
                                $('#top-popup-message').css("display", "none");

                                $('.datepicker-days td.day').each(function () {
                                    var full_date = $(this).attr('data-day');
                                    var st = full_date.split("/");
                                    var formattedDate = st[2] + "-" + st[0] + "-" + st[1];
                                    
                                    if($(this).hasClass('active disabled')){
                                        $(this).removeClass('active disabled');
                                        $(this).attr('data-action','selectDay');
                                    }

                                    if(day == formattedDate){
                                        $(this).addClass('active');
                                        $(this).attr('data-action','selectDay');
                                    }
                                });
                                
                                var name = $('#select-timezone :selected').attr("data-name");
                                var city = $('#select-timezone :selected').attr("data-city");
                                var timezone = $('#select-timezone :selected').attr("data-value");
                                var index = $("#select-timezoneSelectBoxItText").attr("data-val");
                                
                                $.post(home_url + "/?r=ajax/get_tutoring_date_active", {                                   
									timezone: timezone,
									name: name,
									index: index
								}, function (data) {
									$('#active-day-tutor').val(data);
									initCalendar('update', data);
								});

                                if(table == 'table-list-tutor'){
                                    var retype = 'tutor';
                                }else{
                                    var retype = 'findtutor';
                                }
                                if(ptype == "fromclass"){
                                    var search = $('#search-find-tutoring').val();
                                    var year = $('#available_year').val();
                                    var day1 = $("#select-available-daySelectBoxItText").attr("data-val");
                                    var month = $("#select-available-monthSelectBoxItText").attr("data-val");
                                    var time1 = $("#select-available-timeSelectBoxItText").attr("data-val");
                                    var time_view1 = $('#select-available-time :selected').attr("data-time-view");
                                    var stime = $('#select-available-time :selected').attr("data-time");
                                    var subject_type = $("#select-available-subjectSelectBoxItText").attr("data-val");
                                    var type_search = [];

                                    $('input[name="type_search"]:checked').each(function () {
                                        var val = this.value;
                                        type_search.push(val);
                                    });

                                    if(month != 0 && day1 != 0 && year != 0)
                                        var date = year + '-' + month + '-' + day;
                                    else
                                        var date = '';

                                    if(time1 == 0) time1 = '';
                                    if(search != '' || date != '' || time1 != '' || subject_type != 0 || type_search.length > 0){
                                        get_tutor_user('fromclass','table-list-tutor','tutor',search,'','',subject_type,time1,date,type_search, stime, time_view1);
                                    }else{
                                        var tr = '<tr><td class="no-results"><img src="' + path + 'icon_Not_Available.png" alt="">Currently, there are no results.</td></tr>';
                                        $('#table-list-tutor').html(tr);
                                    }
                                    $('.frm-available-now').css("display","block");
                                }else{
                                    get_tutor_user(ptype,table,retype);   
                                    $('.frm-available-now').css("display","none");
                                }

                                $(".main-my-schedule").css("display","none");
                                $(".section-tutor-main").css("display","block");
                                $(".main-new-request").css("display","none"); 
                                $(".main-view-request").css("display","none");
                                $(".main-status-request").css("display","none");
                                $('.header-title-newschedule').css("display","none");
                            }
                        });
                    });

                    $('#got-canceled').live("click",function(){
                        $('#top-popup-message').css("display", "none");
                    });
					
					$('.view-status-scheduled').live("click",function(){
                        var cl = $(this).attr("data-class");
                        var subject = $(this).attr("data-subject");
                        var private_subject = $(this).attr("data-private-subject");
                        var student_name = $(this).attr("data-student-name");
                        var tutor_name = $(this).attr("data-tutor-name");
                        var date = $(this).attr("data-date");
                        var stuff = $(this).attr("data-stuff");
                        var message = $(this).attr("data-message");
						var note = $(this).attr("data-note");
                        var time = $(this).attr("data-time");
                        var time_view = $(this).attr("data-time-view");
                        var fromtime = $(this).attr("data-fromtime");
                        var totime = $(this).attr("data-totime");
                        var total = $(this).attr("data-total");
                        var total_time = $(this).attr("data-total-time");
                        var day = $(this).attr("data-day");
                        var location = $(this).attr("data-location");
                        var confirmed = $(this).attr("data-confirmed");
                        var canceled = $(this).attr("data-canceled");
                        var id = $(this).attr("data-id");
						var student_id = $(this).attr("data-student-id");
						var teacher_id = $(this).attr("data-teacher-id");
                        var status = $(this).attr("data-status");
                        var accepted = $(this).attr("data-accepted");
                        var icon = $(this).attr("data-icon");
                        var create_on = $(this).attr("data-create-on");
                        var created = $(this).attr("data-created");
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';
						
						var review_schedule = 'Session Not Completed Yet';
						var point_schedule = ' <span class="spent"></span>';
						
						if($.trim(note) == 'null') note = '';
						
						if($.trim(cl) == 'accepted'){
							var current_status = 'Completed';
							var type_status = 'confirmed';
							point_schedule = ' <span class="spent">(spent)</span>';
							review_schedule = '<a href="https://notepad.iktutor.com/en/?sid='+id+'&user_id='+student_id+'&teacher_id='+teacher_id+'" target="_blank">Review Session Again</a>';
							
							if(!$('#completed-status-btn').hasClass('active')){
								$('.list-schedule-status').removeClass('active');
								$('#completed-status-btn').addClass('active');
								$('#completed-status-btn').find('img').attr('src',path + 'TimeIcon_Completed.png');
								$('#all-status-btn').find('img').attr('src',path + 'TimeIcon_Summary_disable.png');
								$('#scheduled-status-btn').find('img').attr('src',path + 'TimeIcon_Scheduled_disable.png');
								$('#expired-status-btn').find('img').attr('src',path + 'TimeIcon_Expired_disable.png');
							}
							
							if(!$('.btn-status-schedule').hasClass('active')){
								$('.btn-status-schedule').addClass('active');
							}

                            $('.cancel-this-schedule').css("display","none");
						}else if($.trim(cl) == 'canceled'){
                            if(accepted == 2)
							    var current_status = 'Canceled by Tutor';
                            else
                                var current_status = 'Canceled';

							var type_status = 'canceled';
							
							if(!$('#expired-status-btn').hasClass('active')){
								$('.list-schedule-status').removeClass('active');
								$('#expired-status-btn').addClass('active');
								$('#expired-status-btn').find('img').attr('src',path + 'TimeIcon_Expired.png');
								$('#all-status-btn').find('img').attr('src',path + 'TimeIcon_Summary_disable.png');
								$('#completed-status-btn').find('img').attr('src',path + 'TimeIcon_Completed_disable.png');
								$('#scheduled-status-btn').find('img').attr('src',path + 'TimeIcon_Scheduled_disable.png');
							}

                            $('.cancel-this-schedule').css("display","none");
						}else{
                            if(accepted == 1)
                                var current_status = 'Confirmed';
                            else if(accepted == 2)
                                var current_status = 'Canceled by Tutor';
                            else
                                var current_status = 'Waiting for Confirmation';

							var type_status = 'waiting';
							
							if(!$('#scheduled-status-btn').hasClass('active')){
								$('.list-schedule-status').removeClass('active');
								$('#scheduled-status-btn').addClass('active');
								$('#scheduled-status-btn').find('img').attr('src',path + 'TimeIcon_Scheduled.png');
								$('#all-status-btn').find('img').attr('src',path + 'TimeIcon_Summary_disable.png');
								$('#completed-status-btn').find('img').attr('src',path + 'TimeIcon_Completed_disable.png');
								$('#expired-status-btn').find('img').attr('src',path + 'TimeIcon_Expired_disable.png');
							}

                            $('.cancel-this-schedule').css("display","block");
						}
						
						if(note == ''){
							$('.btn-status-schedule').text('Save Note');
							tinymce.get('note_status_schedule').setContent('');
						}else{
							$('.btn-status-schedule').text('Edit Note');
							tinymce.get('note_status_schedule').setContent(note);
						}
						
						$('.name-status-schedule').find('img').attr('src',path + icon);
						if($.trim(subject) == 'null')
							$('.name-status-schedule').find('span').text(private_subject);
						else
							$('.name-status-schedule').find('span').text(subject);
						$('#date-schedule').text(date + stuff + ' ' + time_view);
						$('#current-status').text(current_status);
						$('#name-tutor-detail').text(tutor_name);
						$('#point-schedule').html(total + 'Points($)' + point_schedule);
						$('#review-schedule').addClass($.trim(cl));
						$('#review-schedule').html(review_schedule);
						$('.close-status-schedule').attr('data-status',type_status);
						$('.btn-status-schedule').attr('data-id',id);
                        $('#yes-cancel-it').attr('data-id',id);
                        $('#no-cancel-it').attr('data-id',id);
						
						$('#list-schedule-status').css("display","block");
						$('#table-status-schedule').html('');
						$("#open-menu-schedule").css("display","none");
                        $("#open-menu-cancel").css("display","none");
						$(".main-view-status").css("display","block");
					});
					
					$('.close-status-schedule').click(function(){
						$("#open-menu-schedule li").removeClass('active');
						$("#scheduled-btn").parent().addClass('active');
						$(".main-view-status").css("display","none");

						if(!$('#body-my-scheduled').hasClass('status-schedule')){
							$('#body-my-scheduled').addClass('status-schedule')
						}
						
						if($('.btn-status-schedule').hasClass('active')){
							$('.btn-status-schedule').removeClass('active');
						}
						var status = $(this).attr("data-status");
						get_status_schedule(status);
					});
					
					$('.btn-status-schedule').click(function(){
						var id = $(this).attr("data-id");
						if($(this).hasClass('active')){
							var description = $('#note_status_schedule_ifr').contents().find('#tinymce').text();
							if($.trim(description) != ''){
								$.post(home_url + "/?r=ajax/save_tutoring_desc", {                                   
									id: id,
									description: description,
									type: "note"
								}, function (data) {
									if ($.trim(data) == '1') {
										$('.btn-status-schedule').text('Edit Note');
									}                                         
								});
							}
						}
					});

                    $('.view-detail-status').live("click",function(){
                        var cl = $(this).attr("data-class");
                        var subject = $(this).attr("data-subject");
                        var private_subject = $(this).attr("data-private-subject");
                        var student_name = $(this).attr("data-student-name");
                        var tutor_name = $(this).attr("data-tutor-name");
                        var date = $(this).attr("data-date");
                        var stuff = $(this).attr("data-stuff");
                        var message = $(this).attr("data-message");
                        var time = $(this).attr("data-time");
                        var time_view = $(this).attr("data-time-view");
                        var fromtime = $(this).attr("data-fromtime");
                        var totime = $(this).attr("data-totime");
                        var total = $(this).attr("data-total");
                        var total_time = $(this).attr("data-total-time");
                        var day = $(this).attr("data-day");
                        var location = $(this).attr("data-location");
                        var confirmed = $(this).attr("data-confirmed");
                        var canceled = $(this).attr("data-canceled");
                        var id = $(this).attr("data-id");
                        var status = $(this).attr("data-status");
                        var icon = $(this).attr("data-icon");
                        var create_on = $(this).attr("data-create-on");
                        var created = $(this).attr("data-created");
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                        $('.name-request-vew').removeClass('accepted');
                        $('.name-request-vew').removeClass('canceled');
                        $('.name-request-vew').removeClass('wait');

                        $('.btn-view-request').removeClass('accepted');
                        $('.btn-view-request').removeClass('canceled');
                        $('.btn-view-request').removeClass('wait');

                        $('.btn-reschedule-request').removeClass('accepted');
                        $('.btn-reschedule-request').removeClass('canceled');
                        $('.btn-reschedule-request').removeClass('wait');

                        $(".btn-edit-desc").removeClass('active');
                        $(".btn-edit-desc").attr('src',path + 'icon_Tutor_Edit.png');

                        $('.name-request-vew').addClass(cl);
                        $('.btn-view-request').addClass(cl);
                        $('.btn-reschedule-request').addClass(cl);  
                        $('.goto-main-schedule').attr("data-day",day);
                        $('.goto-main-schedule').attr("data-type",'status');
                        $('.goto-main-schedule').attr("data-tab",'main-status-request');  
                        $('#custom-timezone').attr("data-type","view");
						$('#custom-timezone').attr("data-id",id);
                        $('#custom-timezone').attr("data-day",day);
                        $('#custom-timezone').attr("data-created",created); 
						$('#custom-timezone').attr("data-id",id);
                        if($.trim(cl) == "accepted"){
                            $('.name-request-vew').find('img').attr('src',path+'icon_Status_Confirmed.png');
                            $('.name-request-vew').find('span').text('CONFIRMED');
                            $('.btn-view-request').text('Start Tutor!');
                            $('.btn-view-request').attr("data-id",id);
                            $('.btn-view-request').attr("data-canceled",canceled);
                            $('.btn-reschedule-request').text('Cancel & Reschedule');
                            $('.btn-reschedule-request').attr("data-id",id);
                            $('.btn-reschedule-request').attr("data-canceled",canceled);
                            $('.btn-reschedule-request').attr("data-day",day);
                            $('.time-current-request').css('display','block');
                            $('.btn-reschedule-request').css('display','block');
                        }else if($.trim(cl) == "canceled"){
                            $('.name-request-vew').find('img').attr('src',path+'icon_Status_Canceled.png');
                            $('.name-request-vew').find('span').text('CANCELED');
                            $('.btn-view-request').text('Reschedule');
                            $('.btn-view-request').attr("data-id",id);
                            $('.btn-view-request').attr("data-fromtime",fromtime);
                            $('.btn-view-request').attr("data-totime",totime);
                            $('.btn-view-request').attr("data-day",day);
                            $('.btn-view-request').attr("data-total-time",total_time);
                            $('.btn-view-request').attr("data-canceled",canceled);
                            $('.btn-view-request').attr("data-total",total);
                            $('.btn-reschedule-request').text('Cancel');
                            $('.btn-reschedule-request').attr("data-id",id);
                            $('.btn-reschedule-request').attr("data-day",day);
                            $('.time-current-request').css('display','none');
                            $('.btn-reschedule-request').css('display','block');
                        }else{
                            $('.name-request-vew').find('img').attr('src',path+'icon_Status_Waiting.png');
                            $('.name-request-vew').find('span').text('WAITING FOR ACCEPTANCE');
                            $('.btn-view-request').text('Cancel the Schedule');
                            $('.btn-view-request').attr("data-id",id);
                            $('.btn-view-request').attr("data-canceled",canceled);
                            $('.btn-view-request').attr("data-day",day);
                            $('.btn-reschedule-request').css('display','none');
                            $('.time-current-request').css('display','none');
                        }

                        $('.current-view-day').text(date);
                        $('.stuff-view-day').text(stuff);
                        $('.time-current-view').text(time_view);

                        $('.tutor-request').find('span').text(tutor_name);
                        $('.subject-request').find('img').attr('src',path+icon);
                        $('.subject-request').find('span').text(subject);
                        $('.location-request').find('span.name-timezone').text(location);
                        $('.points-request').find('span').text(total + ' Points($) used');
                        $('.title-request').text(private_subject);
                        $('.description-request').text(message);
                        $('.btn-edit-desc').attr("data-message",message);
                        $('.btn-edit-desc').attr("data-id",id);
                        $('.by').find('span').text(student_name);
                        $('.create-time').text(create_on);

                        if($(".main-my-schedule").hasClass('active-tab-schedule')){
                            $(".main-my-schedule").removeClass("active-tab-schedule");
                        }

                        $(".main-my-schedule").css("display","none");
                        $(".section-tutor-main").css("display","none");
                        $('.main-new-request').css("display","none"); 
                        $(".edit-description").css("display","none");
                        $(".main-status-request").css("display","none");
                        $(".writting-review").css("display","none");
                        $(".main-view-request").css("display","block"); 
                        $(".description-request").css("display","block");
                        $('.header-title-newschedule').css("display","none");
                    });

                    $(".btn-edit-desc").click(function(){
                        var message = $(this).attr("data-message");
                        var id = $(this).attr("data-id");
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';

                        if($.trim(message) == 'null') message = '';                        
                        if($(this).hasClass('active')){
                            var description = $('#edit_description_request_ifr').contents().find('#tinymce').text();
                            if($.trim(description) != ''){
                                $.post(home_url + "/?r=ajax/save_tutoring_desc", {                                   
                                    id: id,
                                    description: description
                                }, function (data) {
                                    if ($.trim(data) == '1') {
                                        $(".btn-edit-desc").attr('src',path + 'icon_Tutor_Edit.png');
                                        $(".btn-edit-desc").removeClass('active');
                                        $(".btn-edit-desc").attr("data-message",description);
                                        $('.description-request').text(description);
                                        $(".description-request").css("display","block");
                                        $(".edit-description").css("display","none");
                                    }                                         
                                });
                            }else{
                                $(".btn-edit-desc").attr('src',path + 'icon_Tutor_Edit.png');
                                $(".btn-edit-desc").removeClass('active');
                                $(".btn-edit-desc").attr("data-message",message);
                                $('.description-request').text(message);
                                $(".description-request").css("display","block");
                                $(".edit-description").css("display","none");
                                // $('#popup-message').html('<p class="text-used">Please enter Message</p><button id="got-it" type="button" class="btn-orange form-control nopadding-r border-btn">OK</button>');
                                // $('#top-popup-message').css("display", "block");

                            }
                        }else{
                            $(this).addClass('active');
                            $(this).attr('src',path + 'Checked_Icon.png');
                            tinymce.get('edit_description_request').setContent(message);
                            $(".description-request").css("display","none");
                            $(".edit-description").css("display","block");
                        }
                    });

                    $(".btn-view-request").click(function(){
                        var id = $(this).attr("data-id");
                        var canceled = $(this).attr("data-canceled");                        
                        if($(this).hasClass('accepted')){
                            $('.time-current-request').css('display','block');
                        }else if($(this).hasClass('canceled')){
                            $('.name-request').find('span').text('RESCHEDULE');
                            var fromtime = $(this).attr("data-fromtime");
                            var time_duration = $(this).attr("data-totime");
                            var total = $(this).attr("data-total");
                            var total_time = $(this).attr("data-total-time");
                            var day = $(this).attr("data-day");
                            var total = $(this).attr("data-total");

                            var viewport = getViewport();
                            if(viewport.width < 925){
                                if(!$('#custom-timezone').hasClass('request-page')){
                                    $('#custom-timezone').addClass('request-page');
                                }
                            }else{
                                if($('#custom-timezone').hasClass('request-page')){
                                    $('#custom-timezone').removeClass('request-page');
                                }
                            }

                            var ftime = fromtime.split(':');
                            var totime = time_duration.split(':');
                            var hftime = parseInt(ftime[0]);
                            var httime = parseInt(totime[0]);

                            if(hftime > 12) hftime = hftime - 12;
                            if(httime > 12) httime = httime - 12;
							
							var time_sc1 = hftime+':'+ftime[1]+':'+ftime[2].toLowerCase();
                            var time_sc2 = httime+':'+totime[1]+':'+totime[2].toLowerCase();
							
                            var time = hftime+':'+ftime[1]+ftime[2] +' - '+ httime+':'+totime[1]+totime[2];

                            $('#btn-sent-request').attr("data-id",id);
                            $('#btn-sent-request').attr("data-fromtime",fromtime);
                            $('#btn-sent-request').attr("data-totime",time_duration);
                            $('#btn-sent-request').attr("data-day",day);                            
                            $('#btn-sent-request').attr("data-time",time_sc1 + ' ~ ' + time_sc2);
                            $('#btn-sent-request').attr("data-total-time",total_time);
                            $('#btn-sent-request').attr("data-total",total);
                            $('.number-points').text('Already Paid (' + total + ' Points)');
                            $('#custom-timezone').attr("data-type","request");
                            $('#custom-timezone').attr("data-day",day);

                            var today = new Date(day.replace("-", ","));                            
                            var weekday = new Array(7);
                                weekday[0] =  "Sun";
                                weekday[1] = "Mon";
                                weekday[2] = "Tue";
                                weekday[3] = "Wed";
                                weekday[4] = "Thur";
                                weekday[5] = "Fri";
                                weekday[6] = "Sat";                                
                            var n = weekday[today.getDay()];
                            var month_text = getMonthtoText(today.getMonth()+1);
                            $('.current-request-day').text(month_text + ' ' + today.getDate());
                            $('.stuff-request-day').text(' (' + n + ')');
                            $('.time-current').text(time);                            

                            $('span.placeholder').each(function () {
                                var text = $(this).text();
                                var font = $(this).css("font");                                
                                if (document.documentMode || /Edge/.test(navigator.userAgent)) {
                                    //console.log("Edge");
                                    var offset = 28;
                                }else if (navigator.userAgent.search("Firefox") >= 0){
                                    var offset = 26;
                                    //console.log("Firefox");
                                }else{
                                    var offset = 25;
                                }
                                var left = (getTextWidth(text,font) + offset);
                                $(this).prev().css("padding-left",left+"px");
                            });

                            $('#search-title').val('');
                            tinymce.get('description_request').setContent('');
                            $('.radio_buttons_search').attr('checked',false);
							
                            $('#table-search-tutor').html("");
                            var tr = '<tr><td colspan="3" class="no-list"><img src="<?php echo get_template_directory_uri(); ?>/library/images/icon_No_Schedule.png" alt="">Currently, there are no list</td></tr>';
                            $('#table-search-tutor').append(tr);

                            $(".main-view-request").css("display","none");
                            $(".main-status-request").css("display","none");
                            $(".section-tutor-main").css("display","none");
                            $(".writting-review").css("display","none");
                            $(".main-my-schedule").css("display","block");
                            $(".main-my-schedule").addClass("active-reschedule");
                            $(".main-new-request").addClass("active");
                            $(".main-new-request").css("display","block"); 
                            $('.header-title-newschedule').css("display","block");

                            if(!$(".main-my-schedule").hasClass('active-tab-schedule')){
                                $(".main-my-schedule").addClass("active-tab-schedule");
                            }
                        }else{
                            var day = $(this).attr("data-day");
                            $.post(home_url + "/?r=ajax/cancel_confirm_tutoring", {                                   
                                id: id,
                                canceled: canceled
                            }, function (data) {
                                if ($.trim(data) == '1') {
                                    $('#popup-message').html('<p class="text-used">This new request successfully canceled!</p><button id="got-schedule" data-day="' + day + '" type="button" class="btn-orange form-control nopadding-r border-btn">OK</button>');
                                    $('#top-popup-message').css("display", "block");
                                }                                         
                            });
                        }                        
                    });

                    $(".btn-reschedule-request").click(function(){                                                
                        if($(this).hasClass('accepted')){
                            var id = $(this).attr("data-id");
                            var canceled = $(this).attr("data-canceled");
                            var day = $(this).attr("data-day");
                            $.post(home_url + "/?r=ajax/cancel_confirm_tutoring", {                                   
                                id: id,
                                canceled: canceled
                            }, function (data) {
                                if ($.trim(data) == '1') {
                                    $('#popup-message').html('<p class="text-used">This new request successfully canceled!</p><button id="got-schedule" data-day="' + day + '" type="button" class="btn-orange form-control nopadding-r border-btn">OK</button>');
                                    $('#top-popup-message').css("display", "block");
                                }                                         
                            });
                        }else if($(this).hasClass('canceled')){
                            var id = $(this).attr("data-id");
                            var day = $(this).attr("data-day");
                            $('.new-request-list').text('SCHEDULE');                         
                            get_list_schedule();

                            get_scheduled_day(day);

                            $(".main-my-schedule").css("display","block");
                            $(".section-tutor-main").css("display","none");
                            $(".main-new-request").css("display","none"); 
                            $(".main-view-request").css("display","none");
                            $(".main-status-request").css("display","none");
                            $(".writting-review").css("display","none");
                            $('.header-title-newschedule').css("display","none");

                            if(!$(".main-my-schedule").hasClass('active-tab-schedule')){
                                $(".main-my-schedule").addClass("active-tab-schedule");
                            }
                        }
                    })

                    $("#btn-status").click(function(){   
                        $('.new-request-list').text('STATUS');   
                        var type = 'all';                      
                        $('.btn-sub-status').removeClass('active');
                        $('#btn-status-all').addClass('active');

                        $('#custom-timezone').attr("data-type","status");
                        $('#custom-timezone').attr("data-status","all");

                        $('.header-schedule').removeClass('active');
                        $('#list-schedule-status').css("display","none");
                        $('#table-status-schedule').html('');
                        $("#open-menu-schedule").css("display","none");
						$(".main-view-status").css("display","none");

                        if($(".main-my-schedule").hasClass('active-tab-schedule')){
                            $(".main-my-schedule").removeClass("active-tab-schedule");
                        }

                        if($('#body-my-scheduled').hasClass('status-schedule')){
                            $('#body-my-scheduled').removeClass('status-schedule')
                        }

                        var viewport = getViewport();
                        if(viewport.width < 925){
                            $('#btn-open-calendar').css("display","none");
                            if(viewport.width < 650){
                                $('#tab-tutor-content .border-selectall').find('.col-md-4').css('width','55.88%');
                            }else{
                                $('#tab-tutor-content .border-selectall').find('.col-md-4').css('width','43.88%');
                            }
                        }else{
                            $('#tab-tutor-content .border-selectall').find('.col-md-4').css('width','62.65%');
                        }
                        get_status_request(type);

                        $(".main-status-request").css("display","block");
                        $(".section-tutor-main").css("display","none");
                        $(".main-new-request").css("display","none"); 
                        $(".main-view-request").css("display","none");
                        $(".main-my-schedule").css("display","none");
                        $(".writting-review").css("display","none");
                        $('.header-title-newschedule').css("display","none");
                    });

                    $("#sub-status").click(function(){
                        $('.new-request-list').text('STATUS');
                        var type = 'all';                      
                        $('.btn-sub-status').removeClass('active');
                        $('#btn-status-all').addClass('active');

                        $('.header-schedule').removeClass('active');
                        $('#list-schedule-status').css("display","none");
                        $('#table-status-schedule').html('');
                        $("#open-menu-schedule").css("display","none");
						$(".main-view-status").css("display","none");

                        $('#custom-timezone').attr("data-type","status");
                        $('#custom-timezone').attr("data-status","all");

                        if($(".main-my-schedule").hasClass('active-tab-schedule')){
                            $(".main-my-schedule").removeClass("active-tab-schedule");
                        }

                        if($('#body-my-scheduled').hasClass('status-schedule')){
                            $('#body-my-scheduled').removeClass('status-schedule')
                        }

                        var viewport = getViewport();
                        if(viewport.width < 925){
                            $('#btn-open-calendar').css("display","none");
                            if(viewport.width < 650){
                                $('#tab-tutor-content .border-selectall').find('.col-md-4').css('width','55.88%');
                            }else{
                                $('#tab-tutor-content .border-selectall').find('.col-md-4').css('width','43.88%');
                            }
                        }else{
                            $('#tab-tutor-content .border-selectall').find('.col-md-4').css('width','62.65%');
                        }
                        get_status_request(type);

                        $(".main-status-request").css("display","block");
                        $(".section-tutor-main").css("display","none");
                        $('.main-new-request').css("display","none"); 
                        $(".main-view-request").css("display","none");
                        $(".main-my-schedule").css("display","none");
                        $(".writting-review").css("display","none");
                        $('.header-title-newschedule').css("display","none");
                    });

                    $('#btn-status-all').click(function(){
                        $('.writting-review').css("display","none"); 
                        $('#custom-timezone').attr("data-type","status");
                        $('#custom-timezone').attr("data-status","all");
                                                    
                        if(!$(this).hasClass('active')){
                            $('.btn-sub-status').removeClass('active');
                            $(this).addClass('active');        
                        }
                        get_status_request('all');
                    });

                    $('#btn-status-waiting').click(function(){             
                        $('.writting-review').css("display","none");  
                        $('#custom-timezone').attr("data-type","status");
                        $('#custom-timezone').attr("data-status","waiting");
                           
                        if(!$(this).hasClass('active')){
                            $('.btn-sub-status').removeClass('active');
                            $(this).addClass('active');        
                            get_status_request('waiting');
                        }
                    });

                    $('#btn-status-confirmed').click(function(){  
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/'; 
                        $('.writting-review').css("display","none");
                        $('#custom-timezone').attr("data-type","status");
                        $('#custom-timezone').attr("data-status","confirmed");
            
                        if(!$(this).hasClass('active')){
                            $('.btn-sub-status').removeClass('active');
                            $(this).addClass('active');
                            get_status_request('confirmed');
                        }
                    });

                    $('#btn-status-canceled').click(function(){  
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                        $('.writting-review').css("display","none");     
                        $('#custom-timezone').attr("data-type","status");
                        $('#custom-timezone').attr("data-status","canceled");

                        if(!$(this).hasClass('active')){
                            $('.btn-sub-status').removeClass('active');
                            $(this).addClass('active');
                            get_status_request('canceled');
                        }
                    });

                    $('#btn-status-finished').click(function(){  
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                        $('.writting-review').css("display","none");   
                        $('#custom-timezone').attr("data-type","status");
                        $('#custom-timezone').attr("data-status","finished");

                        if(!$(this).hasClass('active')){
                            $('.btn-sub-status').removeClass('active');
                            $(this).addClass('active');
                            //get_status_request('canceled');
                        }
                    });

                    $('#btn-open-calendar').click(function(){  
                        if($(this).hasClass('active')){                            
                            $(this).removeClass('active');
                            $('.box-schedule-left').css('display','block');
                        }else{
                            $(this).addClass('active');
                            $('.box-schedule-left').css('display','none');
                        }
                    });
                    
                    $('#btn-open-upcoming').click(function(){                          
                        if($(this).hasClass('active')){                            
                            $(this).removeClass('active');
                            $(this).css('right','0');
                            $('.upcoming-schedule').css({'display':'none','right':'-286px'});
                        }else{
                            $(this).addClass('active');
                            $('.upcoming-schedule').css({'display':'block','right':'0px'});
                            $(this).css('right','285px');
                        }
                    });

                    $("#chk-schedule-btn").click(function(){ 
                        var day = $(this).attr("data-day");

                        var today = new Date(day.replace("-", ","));                            
                        var weekday = new Array(7);
                            weekday[0] =  "Sun";
                            weekday[1] = "Mon";
                            weekday[2] = "Tue";
                            weekday[3] = "Wed";
                            weekday[4] = "Thur";
                            weekday[5] = "Fri";
                            weekday[6] = "Sat";                                
                        var n = weekday[today.getDay()];
                        var month_text = getMonthtoText(today.getMonth()+1);
                        $('.current-day').text(month_text + ' ' + today.getDate());
                        $('.stuff-day').text(' (' + n + ')');                        

                        var prev_d = new Date(day.replace("-", ","));
                        prev_d.setDate(prev_d.getDate() - 1);
                        var dd = prev_d.getDate();
                        var mm = prev_d.getMonth()+1; //January is 0!
                        var yyyy = prev_d.getFullYear();
                        if(dd < 10) {
                            dd = "0"+dd;
                        }
                        if(mm < 10) {
                            mm = "0"+mm;
                        }

                        var next_d = new Date(day.replace("-", ","));
                        next_d.setDate(next_d.getDate() + 1);
                        var dd_n = next_d.getDate();
                        var mm_n = next_d.getMonth()+1; //January is 0!
                        var yyyy_n = next_d.getFullYear();
                        if(dd_n < 10) {
                            dd_n = "0"+dd_n;
                        }
                        if(mm_n < 10) {
                            mm_n = "0"+mm_n;
                        }
                        
                        $("#menu-schedule-btn").attr('data-day',day);
                        $(".schedule-left-btn").attr('data-day',yyyy+'-'+mm+'-'+dd);
                        $(".schedule-right-btn").attr('data-day',yyyy_n+'-'+mm_n+'-'+dd_n);                           
                        get_list_schedule();

                        get_scheduled_day(day);

                        $(".main-my-schedule").removeClass("hidden");
                        $(".body-my-scheduled").removeClass("hidden");
                        $(".sethide-detail").not(".main-my-schedule").addClass("hidden");
                    });

                    $("#accept-confirm-btn, #accept-confirm-my-btn").click(function(){
                        var confirmed = $(this).attr("data-confirmed");
                        var id = $(this).attr("data-id");
                        var status_request = 'Confirmed';
                        var icon_status = 'icon_Status_Confirmed.png';
                        $.get(home_url + "/?r=ajax/accept_confirm_tutoring", {id: id,confirmed:confirmed}, function () {
                            $('.status-my').attr('src','<?php echo get_template_directory_uri(); ?>/library/images/' + icon_status);
                            $('#label-status-my').text(status_request);

                            $('.status-request').attr('src','<?php echo get_template_directory_uri(); ?>/library/images/' + icon_status);
                            $('#label-status-request').text(status_request);
                            $('#accept-confirm-btn').text('Start Tutoring');
                            $('#accept-confirm-my-btn').text('Start Tutoring');
                        });
                    });
                    
                    $('#select-available-day').change(function(){
                        var day = $("#select-available-daySelectBoxItText").attr("data-val");
                        var year = $('#available_year').val();
                        var month = $("#select-available-monthSelectBoxItText").attr("data-val");
                        
                        if(month != 0 && day != 0 && year != 0)
                            var date = year + '-' + month + '-' + day;
                        else
                            var date = '';
                            
                        var current_day = $('#today-tutor').val();
                        var CurrentDate = new Date(current_day);
                        var GivenDate = new Date(date);
                        if(GivenDate > CurrentDate){
                            var type = 'am'; 
                        	var html = '<option value="0">Select Time</option>';
                        	for (var i = 0; i < 24; i++) {                            
                        	    var id = i;
                        	    if (i > 11){
                        	        var j = i - 12;                                    
                        	        type = 'pm'; 
                        	    }else{
                        	        var j = i;
                        	    }
                        
                        	    if(j == 0) id = j = 12;
                        
                        	    var kl = (parseInt(id) + 1);
                        	    if(kl > 12){
                        	        var ks  = kl - 12;
                        	    }else{
                        	        var ks = kl;
                        	    }
                        
                        	    if(ks < 10) 
                        	        var kll = '0'+ks;
                        	    else
                        	        var kll = ks;
                        
                        	    if(j < 10) 
                        	        var jk = '0'+j;
                        	    else
                        	        var jk = j;
                        	    
                        	    html += '<option data-time="'+j+':00:'+type+' ~ '+j+':30:'+type+'" data-time-view="'+j+':00'+type+'-'+j+':30'+type+'" value="'+j+':00'+type+'">'+jk+':00 '+type+' - '+jk+':30 '+type+'</option>';
                        	    html += '<option data-time="'+j+':30:'+type+' ~ '+ks+':00:'+type+'" data-time-view="'+j+':30'+type+'-'+ks+':00'+type+'" value="'+j+':30'+type+'">'+jk+':30 '+type+' - '+kll+':00 '+type+'</option>';
                        	}
                        	$('#select-available-time').html(html).data("selectBox-selectBoxIt").refresh();
                        }else{
                            if(current_day == date){
                                var current_hour = $("#mytime-clock").attr("data-hour");
                                var current_minute = $("#mytime-clock").attr("data-minute");
                                var available_time = $("#mytime-clock").attr("data-available-time");
                                
                                var type = 'am'; 
                                var html = '<option value="0">Select Time</option>';
                            	for (var i = current_hour; i < 24; i++) {                            
                            	    var id = i;
                            	    if (i > 11){
                            	        var j = i - 12;                                    
                            	        type = 'pm'; 
                            	    }else{
                            	        var j = i;
                            	    }
                            
                            	    if(j == 0) id = j = 12;
                            
                            	    var kl = (parseInt(id) + 1);
                            	    if(kl > 12){
                            	        var ks  = kl - 12;
                            	    }else{
                            	        var ks = kl;
                            	    }
                            
                            	    if(ks < 10) 
                            	        var kll = '0'+ks;
                            	    else
                            	        var kll = ks;
                            
                            	    if(j < 10) 
                            	        var jk = '0'+j;
                            	    else
                            	        var jk = j;
                            	    
                            	    if(current_minute > 29 && i == current_hour){
                            	        var sel_time = j+':30'+type;
                            	        if(sel_time == available_time)
                            	            var sel = 'selected="selected"';
                            	        else
                            	            var sel = '';
                            	        html += '<option '+sel+' data-time="'+j+':30:'+type+' ~ '+ks+':00:'+type+'" data-time-view="'+j+':30'+type+'-'+ks+':00'+type+'" value="'+j+':30'+type+'">'+jk+':30 '+type+' - '+kll+':00 '+type+'</option>';
                            	    }else{
                            	        var sel_time1 = j+':00'+type;
                            	        if(sel_time1 == available_time)
                            	            var sel1 = 'selected="selected"';
                            	        else
                            	            var sel1 = '';
                            	            
                            	        var sel_time2 = j+':30'+type;
                            	        if(sel_time2 == available_time)
                            	            var sel2 = 'selected="selected"';
                            	        else
                            	            var sel2 = '';
                            	        html += '<option '+sel1+' data-time="'+j+':00:'+type+' ~ '+j+':30:'+type+'" data-time-view="'+j+':00'+type+'-'+j+':30'+type+'" value="'+j+':00'+type+'">'+jk+':00 '+type+' - '+jk+':30 '+type+'</option>';
                            	        html += '<option '+sel2+' data-time="'+j+':30:'+type+' ~ '+ks+':00:'+type+'" data-time-view="'+j+':30'+type+'-'+ks+':00'+type+'" value="'+j+':30'+type+'">'+jk+':30 '+type+' - '+kll+':00 '+type+'</option>';
                            	    }
                            	}
                            	$('#select-available-time').html(html).data("selectBox-selectBoxIt").refresh();
                            }
                        }
                    });

                    $('#select-timezone').change(function(){
                        var timezone = $('#select-timezone :selected').attr("data-value");
                        var name = $('#select-timezone :selected').attr("data-name");
                        var index = $("#select-timezoneSelectBoxItText").attr("data-val");
                        var city = $('#select-timezone :selected').attr("data-city");
                        var ptype = $('#custom-timezone').attr("data-type");

                        $.post(home_url + "/?r=ajax/update_timezone", {                                   
                            timezone: timezone,
                            name: name,
                            index: index
                        }, function (data) {});

                        initDateTimePicker(name, index, timezone, city);
                        clearInterval(interval);
                        clearInterval(intervalmy);
                        clearInterval(intervalcity);
                        initTimeClock(name);
                        initMyTimeClock(name);
                        $('#mycity-name').text(city);
						
                        if(ptype == 'status'){
                            var status = $('#custom-timezone').attr("data-status");
                            if(status != 'finished'){
                                get_status_request(status);
                            }
                        }else if(ptype == 'view'){
							var id = $('#custom-timezone').attr("data-id");
							$.post(home_url + "/?r=ajax/get_view_by_timezone", {  
								id: id,
								timezone: timezone,
								name: name,
								index: index
							}, function (data) {
								var result = $.parseJSON(data);
								if(result.date){
									$('.current-view-day').text(result.date);
									$('.stuff-view-day').text(result.stuff);
									$('.time-current-view').text(result.time_view);
									$('.create-time').text(result.create_on);
									$('.btn-reschedule-request').attr("data-day",result.day);
									$('.btn-view-request').attr("data-day",result.day);
									$('.goto-main-schedule').attr("data-day",result.day);
									$('#custom-timezone').attr("data-day",result.day);  
									$('#custom-timezone').attr("data-created",result.created);
								}
							});
						}
                    });

                    /*$('#sandbox-container-tutor').datepicker({
                        todayHighlight: true,
                        templates: {
                            leftArrow: '<img src="<?php echo get_template_directory_uri(); ?>/library/images/Chalendar_icon_Left.png" height="15">',
                            rightArrow: '<img src="<?php echo get_template_directory_uri(); ?>/library/images/Chalendar_icon_Right.png" height="15">'
                        },
                        beforeShowDay: function(date){
                            var d = date;
                            var curr_date = d.getDate();
                            var curr_month = d.getMonth() + 1; //Months are zero based
                            if(curr_month < 10) {
                                curr_month = "0"+curr_month;
                            }
                            var curr_year = d.getFullYear();
                            var formattedDate = curr_year + "-" + curr_month + "-" + curr_date;
                            if($.inArray(formattedDate, active_day) != -1){
                                return {
                                   classes: 'activeClass'
                                };
                            }
                            return;
                        }
                    });*/
                    var date_utc = moment.utc().format('YYYY-MM-DD HH:mm:ss');
                    var timezone_name = '<?php echo $timezone_name ?>';
                    var tdDate = moment.tz(timezone_name).format('YYYY-MM-DD');
                    $('#today-tutor').val(tdDate);
                    $("#select-available-month").selectBoxIt('selectOption',moment.tz(timezone_name).format('MM')).data("selectBox-selectBoxIt");
                    $("#select-available-month").data("selectBox-selectBoxIt").refresh();

                    $("#select-available-day").selectBoxIt('selectOption',moment.tz(timezone_name).format('DD')).data("selectBox-selectBoxIt");
                    $("#select-available-day").data("selectBox-selectBoxIt").refresh();
                    
                    $("#available_year").val(moment.tz(timezone_name).format('YYYY'));
                    
                    if(moment.tz(timezone_name).format('m') < 30)
                        var available_time = moment.tz(timezone_name).format('h')+':00'+moment.tz(timezone_name).format('a');
                    else
                        var available_time = moment.tz(timezone_name).format('h')+':30'+moment.tz(timezone_name).format('a');
                    $("#select-available-time").selectBoxIt('selectOption',available_time.toString()).data("selectBox-selectBoxIt");
                    $("#select-available-time").data("selectBox-selectBoxIt").refresh();
                    
                    console.log('Timzone UTC: '+date_utc+' | to '+timezone_name+': '+tdDate);
                    $('#sandbox-container-tutor').datetimepicker({
                        useCurrent: false,
                        inline: true,
                        sideBySide: true,
                        viewMode: 'days',
                        timeZone: timezone_name,
                        defaultDate: 'now',
                        format: 'MM/DD/YYYY',
                        icons: {
                            previous: '<img src="<?php echo get_template_directory_uri(); ?>/library/images/Chalendar_icon_Left.png" height="15">',
                            next: '<img src="<?php echo get_template_directory_uri(); ?>/library/images/Chalendar_icon_Right.png" height="15">'
                        }
                    });

                    $('.picker-switch').click(function() { return false; });

                    initCalendar();    
                    initTimeClock(timezone_name); 
                    initMyTimeClock(timezone_name); 

                    $(".cancel-now").live("click", function(){
                        var id = $(this).attr("data-id");

                        $(".yes-cancel-it").attr("data-id",id);
                        $(".no-cancel-it").attr("data-id",id);

                        if($("#open-menu-cancel0").hasClass('active')){
                            $("#open-menu-cancel0").css("display","none");
                            $("#open-menu-cancel0").removeClass('active');
                        }else{
                            $("#open-menu-cancel0").css("display","block");
                            $("#open-menu-cancel0").addClass('active');
                        }
                    });

                    $(".yes-cancel-it").live("click",function(){
                        var id = $(this).attr("data-id");
                        var name = $('#select-timezone :selected').attr("data-name");
                        var city = $('#select-timezone :selected').attr("data-city");
                        var timezone = $('#select-timezone :selected').attr("data-value");
                        var index = $("#select-timezoneSelectBoxItText").attr("data-val");
                        var day = $("#menu-schedule-btn").attr("data-day");
                        var status = 1;
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                        if($("#open-menu-cancel0").hasClass('active')){
                            $("#open-menu-cancel0").css("display","none");
                            $("#open-menu-cancel0").removeClass('active');
                        }
                        $.post(home_url + "/?r=ajax/canceled_schedule_now", {
                            id: id,
                            status: status
                        }, function (data) {
                            $('#menu-quick-notification').find('img').attr('src',path + '08_Top_Trigger_NOTIFICATION.png');
                            if(!$('#menu-quick-notification').find('img').hasClass('active')){
                                $('#menu-quick-notification').find('img').addClass('active');
                            }

                            get_scheduled_day(day, 'schedule', true);

                            $.post(home_url + "/?r=ajax/get_tutoring_date_active", {                                   
                                timezone: timezone,
                                name: name,
                                index: index
                            }, function (data) {
                                $('#active-day-tutor').val(data);
                                initCalendar('update', data);
                            });

                            $.get(home_url + "/?r=ajax/get_my_schedules", {timezone: ''}, function (data) {
                                //console.log(data);
                                data = JSON.parse(data);
                                if (data.length > 0) {
                                    $('.slide-my-schedule').html('');
                                    $('.slide-my-schedule').removeClass('slick-initialized');
                                    $('.slide-my-schedule').removeClass('slick-slider');

                                    $.each(data, function (i, v) {
                                        var html_slide = '';
                                        html_slide += '<div class="item" data-fromhour="'+v.fromhour+'" data-fromminute="'+v.fromminute+'" data-tohour="'+v.tohour+'" data-tominute="'+v.tominute+'" data-day="'+v.day+'" data-type="'+v.totype+'">';
                                            html_slide += '<div class="description-detail">';
                                                html_slide += '<p class="subject-detail">';
                                                    html_slide += '<span class="name-subject">'+v.private_subject+'</span>';
                                                html_slide += '</p>';
                                                html_slide += '<p class="my-time-request">';
                                                    html_slide += '<span class="label-timezone">Date:</span>';
                                                    html_slide += '<span class="my-current-day">'+v.date+'</span>';
                                                    html_slide += '<span class="my-stuff-day">'+v.stuff+'/</span>';
                                                    html_slide += '<span class="my-time-current">'+v.time_view+'</span>';
                                                html_slide += '</p>';
                                                html_slide += '<p class="name-detail">';
                                                    html_slide += '<span class="label-tutor">Tutor:</span>';
                                                    html_slide += '<span class="name-tutor">'+v.tutor_name+'</span>';
                                                html_slide += '</p>';
                                                html_slide += '<p class="points-detail">';
                                                    html_slide += '<span class="label-points">Points:</span>';
                                                    html_slide += '<span class="name-points">'+v.total+' Points($)</span>';
                                                html_slide += '</p>';
                                            html_slide += '</div>';
                                            if(v.type_slide == 'current'){
                                                html_slide += '<button id="btn-start-now'+v.id+'" class="btn-dark-blue btn-start-now active" type="button" name="start-now" data-id="'+v.id+'" data-student-id="'+v.id_user+'" data-teacher-id="'+v.tutor_id+'" tabindex="0">Initiate Now!</button>';
                                            }else{
                                                html_slide += '<button id="btn-cancel-schedule'+v.id+'" class="btn-dark-blue btn-cancel-schedule active" type="button" name="cancel-schedule" data-id="'+v.id+'" data-student-id="'+v.id_user+'" data-teacher-id="'+v.tutor_id+'" tabindex="0"><img src="'+path+'icon_Most-Current-Arrow.png" alt=""> Most Current</button>';
                                            }
                                            html_slide += '<button class="cancel-now" id="cancel-now'+v.id+'" data-id="'+v.id+'">';
                                                html_slide += '<img src="'+path+'close_white.png">';
                                            html_slide += '</button>';
                                        html_slide += '</div>';

                                        $('.slide-my-schedule').append(html_slide);
                                    });
                                    if(data.length > 1)
                                        $('.slide-my-schedule').not('.slick-initialized').slick(getSliderSettings(true,1));
                                    else
                                        $('.slide-my-schedule').not('.slick-initialized').slick(getSliderSettings(true,0));
                                }else{
                                    $('.slide-my-schedule').html('');
                                    $('.slide-my-schedule').removeClass('slick-initialized');
                                    $('.slide-my-schedule').removeClass('slick-slider');
                                    var html_slide = '';
                                    html_slide += '<div class="item no-detail-schedule">';
                                        html_slide += '<div class="description-detail">';
                                            html_slide += '<p class="subject-detail">';
                                                html_slide += '<span class="name-subject">Currently there\'s no schedules</span>';
                                            html_slide += '</p>';
                                            html_slide += '<p class="my-time-request">';
                                                html_slide += '<span class="label-timezone">Date:</span>';
                                                html_slide += '<span class="my-current-day">N/A</span>';
                                                html_slide += '<span class="my-stuff-day"></span>';
                                                html_slide += '<span class="my-time-current"></span>';
                                            html_slide += '</p>';
                                            html_slide += '<p class="name-detail">';
                                                html_slide += '<span class="label-tutor">Tutor:</span>';
                                                html_slide += '<span class="name-tutor">N/A</span>';
                                            html_slide += '</p>';
                                            html_slide += '<p class="points-detail">';
                                                html_slide += '<span class="label-points">Points:</span>';
                                                html_slide += '<span class="name-points">0 Points($)</span>';
                                            html_slide += '</p>';
                                        html_slide += '</div>';
                                        html_slide += '<button id="btn-cancel-schedule" class="btn-dark-blue btn-cancel-schedule no-active" type="button" name="cancel-schedule" data-id="" data-student-id="" data-teacher-id="" tabindex="0"><img src="'+path+'icon_Most-Current-Arrow.png" alt=""> Most Current</button>';
                                    html_slide += '</div>';

                                    $('.slide-my-schedule').append(html_slide);
                                    $('.slide-my-schedule').not('.slick-initialized').slick(getSliderSettings(true,0));
                                }
                            });
                        });
                    });

                    $(".no-cancel-it").live("click",function(){
                        var id = $(this).attr("data-id");
                        var name = $('#select-timezone :selected').attr("data-name");
                        var city = $('#select-timezone :selected').attr("data-city");
                        var timezone = $('#select-timezone :selected').attr("data-value");
                        var index = $("#select-timezoneSelectBoxItText").attr("data-val");
                        var day = $("#menu-schedule-btn").attr("data-day");
                        var status = 2;
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';

                        if($("#open-menu-cancel0").hasClass('active')){
                            $("#open-menu-cancel0").css("display","none");
                            $("#open-menu-cancel0").removeClass('active');
                        }
                        $.post(home_url + "/?r=ajax/canceled_schedule_now", {
                            id: id,
                            status: status
                        }, function (data) {
                            $('#menu-quick-notification').find('img').attr('src',path + '08_Top_Trigger_NOTIFICATION.png');
                            if(!$('#menu-quick-notification').find('img').hasClass('active')){
                                $('#menu-quick-notification').find('img').addClass('active');
                            }

                            get_scheduled_day(day, 'schedule', true);

                            $.post(home_url + "/?r=ajax/get_tutoring_date_active", {                                   
                                timezone: timezone,
                                name: name,
                                index: index
                            }, function (data) {
                                $('#active-day-tutor').val(data);
                                initCalendar('update', data);
                            });

                            $.get(home_url + "/?r=ajax/get_my_schedules", {timezone: ''}, function (data) {
                                //console.log(data);
                                data = JSON.parse(data);
                                if (data.length > 0) {
                                    $('.slide-my-schedule').html('');
                                    $('.slide-my-schedule').removeClass('slick-initialized');
                                    $('.slide-my-schedule').removeClass('slick-slider');
                                    $.each(data, function (i, v) {
                                        var html_slide = '';
                                        html_slide += '<div class="item" data-fromhour="'+v.fromhour+'" data-fromminute="'+v.fromminute+'" data-tohour="'+v.tohour+'" data-tominute="'+v.tominute+'" data-day="'+v.day+'" data-type="'+v.totype+'">';
                                            html_slide += '<div class="description-detail">';
                                                html_slide += '<p class="subject-detail">';
                                                    html_slide += '<span class="name-subject">'+v.private_subject+'</span>';
                                                html_slide += '</p>';
                                                html_slide += '<p class="my-time-request">';
                                                    html_slide += '<span class="label-timezone">Date:</span>';
                                                    html_slide += '<span class="my-current-day">'+v.date+'</span>';
                                                    html_slide += '<span class="my-stuff-day">'+v.stuff+'/</span>';
                                                    html_slide += '<span class="my-time-current">'+v.time_view+'</span>';
                                                html_slide += '</p>';
                                                html_slide += '<p class="name-detail">';
                                                    html_slide += '<span class="label-tutor">Tutor:</span>';
                                                    html_slide += '<span class="name-tutor">'+v.tutor_name+'</span>';
                                                html_slide += '</p>';
                                                html_slide += '<p class="points-detail">';
                                                    html_slide += '<span class="label-points">Points:</span>';
                                                    html_slide += '<span class="name-points">'+v.total+' Points($)</span>';
                                                html_slide += '</p>';
                                            html_slide += '</div>';
                                            if(v.type_slide == 'current'){
                                                html_slide += '<button id="btn-start-now'+v.id+'" class="btn-dark-blue btn-start-now active" type="button" name="start-now" data-id="'+v.id+'" data-student-id="'+v.id_user+'" data-teacher-id="'+v.tutor_id+'" tabindex="0">Initiate Now!</button>';
                                            }else{
                                                html_slide += '<button id="btn-cancel-schedule'+v.id+'" class="btn-dark-blue btn-cancel-schedule active" type="button" name="cancel-schedule" data-id="'+v.id+'" data-student-id="'+v.id_user+'" data-teacher-id="'+v.tutor_id+'" tabindex="0"><img src="'+path+'icon_Most-Current-Arrow.png" alt=""> Most Current</button>';
                                            }
                                            html_slide += '<button class="cancel-now" id="cancel-now'+v.id+'" data-id="'+v.id+'">';
                                                html_slide += '<img src="'+path+'close_white.png">';
                                            html_slide += '</button>';
                                        html_slide += '</div>';

                                        $('.slide-my-schedule').append(html_slide);
                                    });
                                    if(data.length > 1)
                                        $('.slide-my-schedule').not('.slick-initialized').slick(getSliderSettings(true,1));
                                    else
                                        $('.slide-my-schedule').not('.slick-initialized').slick(getSliderSettings(true,0));
                                }else{
                                    $('.slide-my-schedule').html('');
                                    $('.slide-my-schedule').removeClass('slick-initialized');
                                    $('.slide-my-schedule').removeClass('slick-slider');
                                    var html_slide = '';
                                    html_slide += '<div class="item no-detail-schedule">';
                                        html_slide += '<div class="description-detail">';
                                            html_slide += '<p class="subject-detail">';
                                                html_slide += '<span class="name-subject">Currently there\'s no schedules</span>';
                                            html_slide += '</p>';
                                            html_slide += '<p class="my-time-request">';
                                                html_slide += '<span class="label-timezone">Date:</span>';
                                                html_slide += '<span class="my-current-day">N/A</span>';
                                                html_slide += '<span class="my-stuff-day"></span>';
                                                html_slide += '<span class="my-time-current"></span>';
                                            html_slide += '</p>';
                                            html_slide += '<p class="name-detail">';
                                                html_slide += '<span class="label-tutor">Tutor:</span>';
                                                html_slide += '<span class="name-tutor">N/A</span>';
                                            html_slide += '</p>';
                                            html_slide += '<p class="points-detail">';
                                                html_slide += '<span class="label-points">Points:</span>';
                                                html_slide += '<span class="name-points">0 Points($)</span>';
                                            html_slide += '</p>';
                                        html_slide += '</div>';
                                        html_slide += '<button id="btn-cancel-schedule" class="btn-dark-blue btn-cancel-schedule no-active" type="button" name="cancel-schedule" data-id="" data-student-id="" data-teacher-id="" tabindex="0"><img src="'+path+'icon_Most-Current-Arrow.png" alt=""> Most Current</button>';
                                    html_slide += '</div>';

                                    $('.slide-my-schedule').append(html_slide);
                                    $('.slide-my-schedule').not('.slick-initialized').slick(getSliderSettings(true,0));
                                }
                            });
                        });
                    });

                    $("#cancel-now").click(function(e){
                        e.stopPropagation();
                        $("#open-menu-cancel").toggle();
                    });

                    $("#yes-cancel-it").click(function(e){
                        e.stopPropagation();
                        var id = $(this).attr("data-id");
                        var name = $('#select-timezone :selected').attr("data-name");
                        var city = $('#select-timezone :selected').attr("data-city");
                        var timezone = $('#select-timezone :selected').attr("data-value");
                        var index = $("#select-timezoneSelectBoxItText").attr("data-val");
                        var day = $("#menu-schedule-btn").attr("data-day");
                        var status = 1;
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                        
                        var today = new Date(day.replace("-", ","));
                        var weekday = new Array(7);
                            weekday[0] =  "Sun";
                            weekday[1] = "Mon";
                            weekday[2] = "Tue";
                            weekday[3] = "Wed";
                            weekday[4] = "Thur";
                            weekday[5] = "Fri";
                            weekday[6] = "Sat";                                
                        var n = weekday[today.getDay()];
                        var month_text = getMonthtoText(today.getMonth()+1);
                        $('.current-day').text(month_text + ' ' + today.getDate());
                        $('.stuff-day').text(' (' + n + ')');

                        var prev_d = new Date(day.replace("-", ","));
                        prev_d.setDate(prev_d.getDate() - 1);
                        var dd = prev_d.getDate();
                        var mm = prev_d.getMonth()+1; //January is 0!
                        var yyyy = prev_d.getFullYear();
                        if(dd < 10) {
                            dd = "0"+dd;
                        }
                        if(mm < 10) {
                            mm = "0"+mm;
                        }

                        var next_d = new Date(day.replace("-", ","));
                        next_d.setDate(next_d.getDate() + 1); 
                        var dd_n = next_d.getDate();
                        var mm_n = next_d.getMonth()+1; //January is 0!
                        var yyyy_n = next_d.getFullYear();
                        if(dd_n < 10) {
                            dd_n = "0"+dd_n;
                        }
                        if(mm_n < 10) {
                            mm_n = "0"+mm_n;
                        }

                        $("#menu-schedule-btn").attr('data-day',day);
                        $(".schedule-left-btn").attr('data-day',yyyy+'-'+mm+'-'+dd);
                        $(".schedule-right-btn").attr('data-day',yyyy_n+'-'+mm_n+'-'+dd_n);

                        $('.datepicker-days td.day').each(function () {
                            var full_date = $(this).attr('data-day');
                            var st = full_date.split("/");
                            var formattedDate = st[2] + "-" + st[0] + "-" + st[1];
                            
                            if($(this).hasClass('active disabled')){
                                $(this).removeClass('active disabled');
                                $(this).attr('data-action','selectDay');
                            }

                            if(day == formattedDate){
                                $(this).addClass('active disabled');
                                $(this).attr('data-action','disabled');
                            }
                        });

                        $.post(home_url + "/?r=ajax/canceled_schedule_now", {
                            id: id,
                            status: status
                        }, function (data) {
                            $('#menu-quick-notification').find('img').attr('src',path + '08_Top_Trigger_NOTIFICATION.png');
                            if(!$('#menu-quick-notification').find('img').hasClass('active')){
                                $('#menu-quick-notification').find('img').addClass('active');
                            }

                            $("#open-menu-cancel").css("display","none");
                            $('.header-schedule').removeClass('active');
                            $('#list-schedule-status').css("display","none");
                            $('#table-status-schedule').html('');
                            $("#open-menu-schedule").css("display","none");
                            $(".main-view-status").css("display","none");

                            if($('#body-my-scheduled').hasClass('status-schedule')){
                                $('#body-my-scheduled').removeClass('status-schedule')
                            }

                            $('.radio_tutor_search').attr('checked',false);

                            var viewport = getViewport();
                            if(viewport.width < 925){
                                $('.box-schedule-left').css("display","block");
                                $('#btn-open-calendar').css("display","block");
                                if(viewport.width < 650){
                                    $('#tab-tutor-content .border-selectall').find('.col-md-6').css('width','53.8%');
                                }else{
                                    $('#tab-tutor-content .border-selectall').find('.col-md-6').css('width','42.88%');
                                }
                            }else{
                                $('#tab-tutor-content .border-selectall').find('.col-md-6').css('width','67.2%');
                            }

                            if($(".main-my-schedule").hasClass('active-reschedule')){
                                $(".main-my-schedule").removeClass('active-reschedule');
                            }

                            if(!$(".main-my-schedule").hasClass('active-tab-schedule')){
                                $(".main-my-schedule").addClass("active-tab-schedule");
                            }

                            if($(".main-new-request").hasClass('active')){
                                $(".main-new-request").removeClass('active');
                            }

                            get_list_schedule();

                            get_scheduled_day(day, 'schedule', true);

                            $.post(home_url + "/?r=ajax/get_tutoring_date_active", {                                   
                                timezone: timezone,
                                name: name,
                                index: index
                            }, function (data) {
                                $('#active-day-tutor').val(data);
                                initCalendar('update', data);
                            });

                            $(".main-my-schedule").css("display","block");
                            $(".section-tutor-main").css("display","none");
                            $(".main-new-request").css("display","none"); 
                            $(".main-view-request").css("display","none");
                            $(".main-status-request").css("display","none");
                            $(".writting-review").css("display","none");
                            $('.header-title-newschedule').css("display","none");
                        });
                    });

                    $("#no-cancel-it").click(function(e){
                        e.stopPropagation();
                        var id = $(this).attr("data-id");
                        var status = 2;
                        var name = $('#select-timezone :selected').attr("data-name");
                        var city = $('#select-timezone :selected').attr("data-city");
                        var timezone = $('#select-timezone :selected').attr("data-value");
                        var index = $("#select-timezoneSelectBoxItText").attr("data-val");
                        var day = $("#menu-schedule-btn").attr("data-day");
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                        $("#open-menu-cancel").css("display","none");

                        var today = new Date(day.replace("-", ","));
                        var weekday = new Array(7);
                            weekday[0] =  "Sun";
                            weekday[1] = "Mon";
                            weekday[2] = "Tue";
                            weekday[3] = "Wed";
                            weekday[4] = "Thur";
                            weekday[5] = "Fri";
                            weekday[6] = "Sat";                                
                        var n = weekday[today.getDay()];
                        var month_text = getMonthtoText(today.getMonth()+1);
                        $('.current-day').text(month_text + ' ' + today.getDate());
                        $('.stuff-day').text(' (' + n + ')');

                        var prev_d = new Date(day.replace("-", ","));
                        prev_d.setDate(prev_d.getDate() - 1);
                        var dd = prev_d.getDate();
                        var mm = prev_d.getMonth()+1; //January is 0!
                        var yyyy = prev_d.getFullYear();
                        if(dd < 10) {
                            dd = "0"+dd;
                        }
                        if(mm < 10) {
                            mm = "0"+mm;
                        }

                        var next_d = new Date(day.replace("-", ","));
                        next_d.setDate(next_d.getDate() + 1); 
                        var dd_n = next_d.getDate();
                        var mm_n = next_d.getMonth()+1; //January is 0!
                        var yyyy_n = next_d.getFullYear();
                        if(dd_n < 10) {
                            dd_n = "0"+dd_n;
                        }
                        if(mm_n < 10) {
                            mm_n = "0"+mm_n;
                        }

                        $("#menu-schedule-btn").attr('data-day',day);
                        $(".schedule-left-btn").attr('data-day',yyyy+'-'+mm+'-'+dd);
                        $(".schedule-right-btn").attr('data-day',yyyy_n+'-'+mm_n+'-'+dd_n);

                        $('.datepicker-days td.day').each(function () {
                            var full_date = $(this).attr('data-day');
                            var st = full_date.split("/");
                            var formattedDate = st[2] + "-" + st[0] + "-" + st[1];
                            
                            if($(this).hasClass('active disabled')){
                                $(this).removeClass('active disabled');
                                $(this).attr('data-action','selectDay');
                            }

                            if(day == formattedDate){
                                $(this).addClass('active disabled');
                                $(this).attr('data-action','disabled');
                            }
                        });

                        $.post(home_url + "/?r=ajax/canceled_schedule_now", {
                            id: id,
                            status: status
                        }, function (data) {
                            $('#menu-quick-notification').find('img').attr('src',path + '08_Top_Trigger_NOTIFICATION.png');
                            if(!$('#menu-quick-notification').find('img').hasClass('active')){
                                $('#menu-quick-notification').find('img').addClass('active');
                            }

                            $("#open-menu-cancel").css("display","none");
                            $('.header-schedule').removeClass('active');
                            $('#list-schedule-status').css("display","none");
                            $('#table-status-schedule').html('');
                            $("#open-menu-schedule").css("display","none");
                            $(".main-view-status").css("display","none");

                            if($('#body-my-scheduled').hasClass('status-schedule')){
                                $('#body-my-scheduled').removeClass('status-schedule')
                            }

                            $('.radio_tutor_search').attr('checked',false);

                            var viewport = getViewport();
                            if(viewport.width < 925){
                                $('.box-schedule-left').css("display","block");
                                $('#btn-open-calendar').css("display","block");
                                if(viewport.width < 650){
                                    $('#tab-tutor-content .border-selectall').find('.col-md-6').css('width','53.8%');
                                }else{
                                    $('#tab-tutor-content .border-selectall').find('.col-md-6').css('width','42.88%');
                                }
                            }else{
                                $('#tab-tutor-content .border-selectall').find('.col-md-6').css('width','67.2%');
                            }

                            if($(".main-my-schedule").hasClass('active-reschedule')){
                                $(".main-my-schedule").removeClass('active-reschedule');
                            }

                            if(!$(".main-my-schedule").hasClass('active-tab-schedule')){
                                $(".main-my-schedule").addClass("active-tab-schedule");
                            }

                            if($(".main-new-request").hasClass('active')){
                                $(".main-new-request").removeClass('active');
                            }

                            get_list_schedule();

                            get_scheduled_day(day, 'schedule', true);

                            $.post(home_url + "/?r=ajax/get_tutoring_date_active", {                                   
                                timezone: timezone,
                                name: name,
                                index: index
                            }, function (data) {
                                $('#active-day-tutor').val(data);
                                initCalendar('update', data);
                            });

                            $(".main-my-schedule").css("display","block");
                            $(".section-tutor-main").css("display","none");
                            $(".main-new-request").css("display","none"); 
                            $(".main-view-request").css("display","none");
                            $(".main-status-request").css("display","none");
                            $(".writting-review").css("display","none");
                            $('.header-title-newschedule').css("display","none");
                        });
                    });

                    $("#menu-schedule-btn").click(function(){
                        $("#open-menu-schedule").toggle();
                    });

                    $("#all-schedule-btn").click(function(){
                        $("#open-menu-schedule li").removeClass('active');
                        $(this).parent().addClass('active');
                        $('.header-schedule').addClass('active');
                        $('#list-schedule-status').css("display","block");
                        $("#open-menu-schedule").css("display","none");
						$(".main-view-status").css("display","none");

                        if(!$('#body-my-scheduled').hasClass('status-schedule')){
                            $('#body-my-scheduled').addClass('status-schedule')
                        }

                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                        if(!$('#all-status-btn').hasClass('active')){
                            $('.list-schedule-status').removeClass('active');
                            $('#all-status-btn').addClass('active');
                            $('#all-status-btn').find('img').attr('src',path + 'TimeIcon_Summary.png');
                            $('#scheduled-status-btn').find('img').attr('src',path + 'TimeIcon_Scheduled_disable.png');
                            $('#completed-status-btn').find('img').attr('src',path + 'TimeIcon_Completed_disable.png');
                            $('#expired-status-btn').find('img').attr('src',path + 'TimeIcon_Expired_disable.png');
                        }

                        var status = $(this).attr("data-status");
                        get_status_schedule(status);
                    });

                    $("#scheduled-btn").click(function(){
                        $("#open-menu-schedule li").removeClass('active');
                        $(this).parent().addClass('active');
                        $('#list-schedule-status').css("display","block");
                        $("#open-menu-schedule").css("display","none");
						$(".main-view-status").css("display","none");

                        if(!$('#body-my-scheduled').hasClass('status-schedule')){
                            $('#body-my-scheduled').addClass('status-schedule')
                        }

                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                        if(!$('#scheduled-status-btn').hasClass('active')){
                            $('.list-schedule-status').removeClass('active');
                            $('#scheduled-status-btn').addClass('active');
                            $('#scheduled-status-btn').find('img').attr('src',path + 'TimeIcon_Scheduled.png');
                            $('#all-status-btn').find('img').attr('src',path + 'TimeIcon_Summary_disable.png');
                            $('#completed-status-btn').find('img').attr('src',path + 'TimeIcon_Completed_disable.png');
                            $('#expired-status-btn').find('img').attr('src',path + 'TimeIcon_Expired_disable.png');
                        }

                        var status = $(this).attr("data-status");
                        get_status_schedule(status);
                    });

                    $("#completed-btn").click(function(){
                        $("#open-menu-schedule li").removeClass('active');
                        $(this).parent().addClass('active');
                        $('.header-schedule').addClass('active');
                        $('#list-schedule-status').css("display","block");
                        $("#open-menu-schedule").css("display","none");
						$(".main-view-status").css("display","none");

                        if(!$('#body-my-scheduled').hasClass('status-schedule')){
                            $('#body-my-scheduled').addClass('status-schedule')
                        }

                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                        if(!$('#completed-status-btn').hasClass('active')){
                            $('.list-schedule-status').removeClass('active');
                            $('#completed-status-btn').addClass('active');
                            $('#completed-status-btn').find('img').attr('src',path + 'TimeIcon_Completed.png');
                            $('#all-status-btn').find('img').attr('src',path + 'TimeIcon_Summary_disable.png');
                            $('#scheduled-status-btn').find('img').attr('src',path + 'TimeIcon_Scheduled_disable.png');
                            $('#expired-status-btn').find('img').attr('src',path + 'TimeIcon_Expired_disable.png');
                        }

                        var status = $(this).attr("data-status");
                        get_status_schedule(status);
                    });

                    $("#expired-btn").click(function(){
                        $("#open-menu-schedule li").removeClass('active');
                        $(this).parent().addClass('active');
                        $('.header-schedule').addClass('active');
                        $('#list-schedule-status').css("display","block");
                        $("#open-menu-schedule").css("display","none");
                        $(".main-view-status").css("display","none");

                        if(!$('#body-my-scheduled').hasClass('status-schedule')){
                            $('#body-my-scheduled').addClass('status-schedule')
                        }

                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                        if(!$('#expired-status-btn').hasClass('active')){
                            $('.list-schedule-status').removeClass('active');
                            $('#expired-status-btn').addClass('active');
                            $('#expired-status-btn').find('img').attr('src',path + 'TimeIcon_Expired.png');
                            $('#all-status-btn').find('img').attr('src',path + 'TimeIcon_Summary_disable.png');
                            $('#completed-status-btn').find('img').attr('src',path + 'TimeIcon_Completed_disable.png');
                            $('#scheduled-status-btn').find('img').attr('src',path + 'TimeIcon_Scheduled_disable.png');
                        }

                        var status = $(this).attr("data-status");
                        get_status_schedule(status);
                    });

                    $("#all-status-btn").click(function(){
                        $("#open-menu-schedule li").removeClass('active');
                        $("#all-schedule-btn").parent().addClass('active');
						$(".main-view-status").css("display","none");
                        $("#open-menu-schedule").css("display","none");

                        if(!$('#body-my-scheduled').hasClass('status-schedule')){
                            $('#body-my-scheduled').addClass('status-schedule')
                        }

                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                        if(!$(this).hasClass('active')){
                            $('.list-schedule-status').removeClass('active');
                            $(this).addClass('active');
                            $(this).find('img').attr('src',path + 'TimeIcon_Summary.png');
                            $('#scheduled-status-btn').find('img').attr('src',path + 'TimeIcon_Scheduled_disable.png');
                            $('#completed-status-btn').find('img').attr('src',path + 'TimeIcon_Completed_disable.png');
                            $('#expired-status-btn').find('img').attr('src',path + 'TimeIcon_Expired_disable.png');
                        }

                        var status = $(this).attr("data-status");
                        get_status_schedule(status);
                    });

                    $("#scheduled-status-btn").click(function(){
                        $("#open-menu-schedule li").removeClass('active');
                        $("#scheduled-btn").parent().addClass('active');
						$(".main-view-status").css("display","none");
                        $("#open-menu-schedule").css("display","none");

                        if(!$('#body-my-scheduled').hasClass('status-schedule')){
                            $('#body-my-scheduled').addClass('status-schedule')
                        }

                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                        if(!$(this).hasClass('active')){
                            $('.list-schedule-status').removeClass('active');
                            $(this).addClass('active');
                            $(this).find('img').attr('src',path + 'TimeIcon_Scheduled.png');
                            $('#all-status-btn').find('img').attr('src',path + 'TimeIcon_Summary_disable.png');
                            $('#completed-status-btn').find('img').attr('src',path + 'TimeIcon_Completed_disable.png');
                            $('#expired-status-btn').find('img').attr('src',path + 'TimeIcon_Expired_disable.png');
                        }

                        var status = $(this).attr("data-status");
                        get_status_schedule(status);
                    });

                    $("#completed-status-btn").click(function(){
                        $("#open-menu-schedule li").removeClass('active');
                        $("#completed-btn").parent().addClass('active');
						$(".main-view-status").css("display","none");
                        $("#open-menu-schedule").css("display","none");

                        if(!$('#body-my-scheduled').hasClass('status-schedule')){
                            $('#body-my-scheduled').addClass('status-schedule')
                        }

                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                        if(!$(this).hasClass('active')){
                            $('.list-schedule-status').removeClass('active');
                            $(this).addClass('active');
                            $(this).find('img').attr('src',path + 'TimeIcon_Completed.png');
                            $('#all-status-btn').find('img').attr('src',path + 'TimeIcon_Summary_disable.png');
                            $('#scheduled-status-btn').find('img').attr('src',path + 'TimeIcon_Scheduled_disable.png');
                            $('#expired-status-btn').find('img').attr('src',path + 'TimeIcon_Expired_disable.png');
                        }

                        var status = $(this).attr("data-status");
                        get_status_schedule(status);
                    });

                    $("#expired-status-btn").click(function(){
                        $("#open-menu-schedule li").removeClass('active');
                        $("#expired-btn").parent().addClass('active');
						$(".main-view-status").css("display","none");
                        $("#open-menu-schedule").css("display","none");

                        if(!$('#body-my-scheduled').hasClass('status-schedule')){
                            $('#body-my-scheduled').addClass('status-schedule')
                        }

                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                        if(!$(this).hasClass('active')){
                            $('.list-schedule-status').removeClass('active');
                            $(this).addClass('active');
                            $(this).find('img').attr('src',path + 'TimeIcon_Expired.png');
                            $('#all-status-btn').find('img').attr('src',path + 'TimeIcon_Summary_disable.png');
                            $('#completed-status-btn').find('img').attr('src',path + 'TimeIcon_Completed_disable.png');
                            $('#scheduled-status-btn').find('img').attr('src',path + 'TimeIcon_Scheduled_disable.png');
                        }

                        var status = $(this).attr("data-status");
                        get_status_schedule(status);
                    });

                    $("#menu-quick-notification").click(function(e){
                        e.stopPropagation();
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                        $("#my-timezone").css("display","none");
                        $("#open-list-quicknotifi").css("display","none");

                        if($("#open-list-quicknotifi").hasClass('active')){
                            $("#open-menu-quicknotifi").css("display","none");
                            $("#open-list-quicknotifi").removeClass('active');
                        }else if($('#btn-my-schedule').hasClass('active')){
                            if($('body').hasClass('open-myschedule')){
                                $('body').removeClass('open-myschedule');
                            }
                            $('#btn-my-schedule').removeClass('active');
                            $("#open-menu-quicknotifi").css("display","none");
                            $('#top-my-schedules').css({"display":"none"});
                            $('#btn-my-schedule').find('img').attr('src',path + '01_icon_Schedule_Starter.png');
                        }else{
                            $("#open-menu-quicknotifi").toggle();
                        }
                    });

                    $("#quick-notification-btn").click(function(e){
                        e.stopPropagation();
                        $("#open-menu-quicknotifi").css("display","none");

                        getQuickNotification(true);
                    });

                    $('#close-modal').click( function (e)
                    {
                        e.stopPropagation();
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                        $('#my-account-modal').modal("hide");//location.reload();         

                        $("#open-menu-quicknotifi").css("display","none");
                        $("#open-list-quicknotifi").css("display","none");

                        if($("#open-list-quicknotifi").hasClass('active')){
                            $("#open-list-quicknotifi").removeClass('active');
                        } 

                        if($('body').hasClass('open-myschedule')){
                            $('body').removeClass('open-myschedule');
                        }

                        if($('#btn-my-schedule').hasClass('active')){
                            $('#btn-my-schedule').removeClass('active');
                        }
                        
                        $('#top-my-schedules').css({"display":"none"});
                        $('#btn-my-schedule').find('img').attr('src',path + '01_icon_Schedule_Starter.png');

                        if(ref == "notepad"){
                            window.history.replaceState(null, null, window.location.pathname);
                        }

                        $("#sub-myacc").css("display", "none");
                        $("#sub-myacc").removeClass("opensub");
                        $("#sub-tutoring").css("display", "none");
                        $("#sub-tutoring").removeClass("opensub");
                        $(".sub-menu-left li").removeClass("active");

                        closeNav();
                    });

                    $(".close-quicknotifi").live("click",function(e){
                        e.stopPropagation();
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';

                        $("#open-menu-quicknotifi").css("display","none");

                        var tid = $(this).attr("data-tid");
                        $.post(home_url + "/?r=ajax/remove_quick_notification", {
                            id: tid
                        }, function (data) {
                            $('#quicknotifi'+tid).remove();
                            if ($.trim(data) == '0') {
                                var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                                $('#menu-quick-notification').find('img').attr('src',path + '07_Top_Trigger.png');
                                if($('#menu-quick-notification').find('img').hasClass('active')){
                                    $('#menu-quick-notification').find('img').removeClass('active');
                                }

                                if($("#open-list-quicknotifi").hasClass('active')){
                                    $("#open-list-quicknotifi").removeClass('active');
                                }
                            }
                        });
                    });

                    $(".modal-content-signup").click( function (e){
                        if (e.target !== this){
                            return;
                        }
                        $("#open-menu-quicknotifi").css("display","none");
                        $("#open-list-quicknotifi").css("display","none");
                        $("#my-timezone").css("display","none");

                        if($("#open-list-quicknotifi").hasClass('active')){
                            $("#open-list-quicknotifi").removeClass('active');
                        }
                        $("#open-menu-cancel").css("display","none");
                    });

                    $(".modal-body-signup").click( function (e){
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';

                        $("#open-menu-quicknotifi").css("display","none");
                        $("#open-list-quicknotifi").css("display","none");
                        $("#my-timezone").css("display","none");
                        $("#open-menu-cancel").css("display","none");

                        if($("#open-list-quicknotifi").hasClass('active')){
                            $("#open-list-quicknotifi").removeClass('active');
                        }
                    });

                    $(".title-div").click( function (e){
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                        if (e.target !== this){
                            return;
                        }
                        $("#open-menu-quicknotifi").css("display","none");
                        $("#open-list-quicknotifi").css("display","none");
                        $("#my-timezone").css("display","none");
                        $("#open-menu-cancel").css("display","none");

                        if($("#open-list-quicknotifi").hasClass('active')){
                            $("#open-list-quicknotifi").removeClass('active');
                        }

                        if($('body').hasClass('open-myschedule')){
                            $('body').removeClass('open-myschedule');
                        }

                        if($('#btn-my-schedule').hasClass('active')){
                            $('#btn-my-schedule').removeClass('active');
                        }
                        
                        $('#top-my-schedules').css({"display":"none"});
                        $('#btn-my-schedule').find('img').attr('src',path + '01_icon_Schedule_Starter.png');
                    });

                    /*$("#top-my-schedules .modal-body").click( function (e){
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                        if (e.target !== this){
                            return;
                        }
                        
                        if($('body').hasClass('open-myschedule')){
                            $('body').removeClass('open-myschedule');
                        }

                        if($('#btn-my-schedule').hasClass('active')){
                            $('#btn-my-schedule').removeClass('active');
                        }
                        
                        $('#top-my-schedules').css({"display":"none"});
                        $('#btn-my-schedule').find('img').attr('src',path + '01_icon_Schedule_Starter.png');
                    });*/

                    $(".bg-overload").click( function (e){
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                        
                        if($('body').hasClass('open-myschedule')){
                            $('body').removeClass('open-myschedule');
                        }

                        if($('#btn-my-schedule').hasClass('active')){
                            $('#btn-my-schedule').removeClass('active');
                        }
                        
                        $('#top-my-schedules').css({"display":"none"});
                        $('#btn-my-schedule').find('img').attr('src',path + '01_icon_Schedule_Starter.png');
                    });

                    $('.view-detail-quicknotifi').live("click",function(e){
                        e.stopPropagation();
                        
                        $("#open-menu-quicknotifi").css("display","none");
                        $("#open-list-quicknotifi").css("display","none");

                        if($("#open-list-quicknotifi").hasClass('active')){
                            $("#open-list-quicknotifi").removeClass('active');
                        } 

                        var cl = $(this).attr("data-class");
                        var subject = $(this).attr("data-subject");
                        var private_subject = $(this).attr("data-private-subject");
                        var student_name = $(this).attr("data-student-name");
                        var tutor_name = $(this).attr("data-tutor-name");
                        var date = $(this).attr("data-date");
                        var stuff = $(this).attr("data-stuff");
                        var message = $(this).attr("data-message");
                        var note = $(this).attr("data-note");
                        var time = $(this).attr("data-time");
                        var time_view = $(this).attr("data-time-view");
                        var fromtime = $(this).attr("data-fromtime");
                        var totime = $(this).attr("data-totime");
                        var total = $(this).attr("data-total");
                        var total_time = $(this).attr("data-total-time");
                        var day = $(this).attr("data-day");
                        var confirmed = $(this).attr("data-confirmed");
                        var canceled = $(this).attr("data-canceled");
                        var id = $(this).attr("data-id");
                        var student_id = $(this).attr("data-student-id");
                        var teacher_id = $(this).attr("data-teacher-id");
                        var status = $(this).attr("data-status");
                        var accepted = $(this).attr("data-accepted");
                        var icon = $(this).attr("data-icon");
                        var create_on = $(this).attr("data-create-on");
                        var created = $(this).attr("data-created");
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                        
                        var review_schedule = 'Session Not Completed Yet';
                        var point_schedule = ' <span class="spent"></span>';
                        
                        if($.trim(note) == 'null') note = '';
                        
                        if($.trim(cl) == 'accepted'){
                            var current_status = 'Completed';
                            var type_status = 'confirmed';
                            point_schedule = ' <span class="spent">(spent)</span>';
                            review_schedule = '<a href="https://notepad.iktutor.com/en/?sid='+id+'&user_id='+student_id+'&teacher_id='+teacher_id+'" target="_blank">Review Session Again</a>';
                            
                            if(!$('#completed-status-btn').hasClass('active')){
                                $('.list-schedule-status').removeClass('active');
                                $('#completed-status-btn').addClass('active');
                                $('#completed-status-btn').find('img').attr('src',path + 'TimeIcon_Completed.png');
                                $('#all-status-btn').find('img').attr('src',path + 'TimeIcon_Summary_disable.png');
                                $('#scheduled-status-btn').find('img').attr('src',path + 'TimeIcon_Scheduled_disable.png');
                                $('#expired-status-btn').find('img').attr('src',path + 'TimeIcon_Expired_disable.png');
                            }
                            
                            if(!$('.btn-status-schedule').hasClass('active')){
                                $('.btn-status-schedule').addClass('active');
                            }

                            $('.cancel-this-schedule').css("display","none");
                        }else if($.trim(cl) == 'canceled'){
                            if(accepted == 2)
                                var current_status = 'Canceled by Tutor';
                            else
                                var current_status = 'Canceled';

                            var type_status = 'canceled';
                            
                            if(!$('#expired-status-btn').hasClass('active')){
                                $('.list-schedule-status').removeClass('active');
                                $('#expired-status-btn').addClass('active');
                                $('#expired-status-btn').find('img').attr('src',path + 'TimeIcon_Expired.png');
                                $('#all-status-btn').find('img').attr('src',path + 'TimeIcon_Summary_disable.png');
                                $('#completed-status-btn').find('img').attr('src',path + 'TimeIcon_Completed_disable.png');
                                $('#scheduled-status-btn').find('img').attr('src',path + 'TimeIcon_Scheduled_disable.png');
                            }
                            
                            if(!$('.btn-status-schedule').hasClass('active')){
                                $('.btn-status-schedule').addClass('active');
                            }

                            $('.cancel-this-schedule').css("display","none");
                        }else{
                            if(accepted == 1)
                                var current_status = 'Confirmed';
                            else if(accepted == 2)
                                var current_status = 'Canceled by Tutor';
                            else
                                var current_status = 'Waiting for Confirmation';

                            var type_status = 'waiting';
                            
                            if(!$('#scheduled-status-btn').hasClass('active')){
                                $('.list-schedule-status').removeClass('active');
                                $('#scheduled-status-btn').addClass('active');
                                $('#scheduled-status-btn').find('img').attr('src',path + 'TimeIcon_Scheduled.png');
                                $('#all-status-btn').find('img').attr('src',path + 'TimeIcon_Summary_disable.png');
                                $('#completed-status-btn').find('img').attr('src',path + 'TimeIcon_Completed_disable.png');
                                $('#expired-status-btn').find('img').attr('src',path + 'TimeIcon_Expired_disable.png');
                            }
                            
                            if(!$('.btn-status-schedule').hasClass('active')){
                                $('.btn-status-schedule').addClass('active');
                            }

                            $('.cancel-this-schedule').css("display","block");     
                        }
                        
                        if(note == ''){
                            $('.btn-status-schedule').text('Save Note');
                        }else{
                            $('.btn-status-schedule').text('Edit Note');
                            tinymce.get('note_status_schedule').setContent(note);
                        }
                        
                        $('.name-status-schedule').find('img').attr('src',path + icon);
                        if($.trim(subject) == 'null')
                            $('.name-status-schedule').find('span').text(private_subject);
                        else
                            $('.name-status-schedule').find('span').text(subject);

                        $('#date-schedule').text(date + stuff + ' ' + time_view);
                        $('#current-status').text(current_status);
                        $('#name-tutor-detail').text(tutor_name);
                        $('#point-schedule').html(total + 'Points($)' + point_schedule);
                        $('#review-schedule').addClass($.trim(cl));
                        $('#review-schedule').html(review_schedule);
                        $('.close-status-schedule').attr('data-status',type_status);
                        $('.btn-status-schedule').attr('data-id',id);
                        $('#yes-cancel-it').attr('data-id',id);
                        $('#no-cancel-it').attr('data-id',id);
                        
                        $(".main-my-schedule").css("display","block");
                        $(".section-tutor-main").css("display","none");
                        $('.main-new-request').css("display","none"); 
                        $(".main-view-request").css("display","none");
                        $(".main-status-request").css("display","none");
                        $(".writting-review").css("display","none");
                        $('.header-title-newschedule').css("display","none");

                        $('#list-schedule-status').css("display","block");
                        $('#table-status-schedule').html('');
                        $('#table-list-schedule').html('');
                        $('#tutoring-scheduled').html('');
                        $("#open-menu-schedule").css("display","none");
                        $("#open-menu-cancel").css("display","none");
                        $(".header-schedule").addClass('active');
                        $(".body-my-scheduled").addClass('status-schedule');
                        $(".main-view-status").css("display","block");

                        $("#create-account").removeClass("active");
                        $("#create-account").removeClass("in");
                        $("#login-user").removeClass("active");
                        $("#login-user").removeClass("in");
                        $("#updateinfo").removeClass("active");
                        $("#updateinfo").removeClass("in");
                        $("#subscription").removeClass("active");
                        $("#subscription").removeClass("in");
                        $("#profile").removeClass("active");
                        $("#profile").removeClass("in");
                        $("#tutoring-main").addClass("active");
                        $("#tutoring-main").addClass("in");
                    });

                    $(".schedule-left-btn").click(function(){
						$('.header-schedule').removeClass('active');
						$('#list-schedule-status').css("display","none");
						$('#table-status-schedule').html('');
						$("#open-menu-schedule").css("display","none");
						$(".main-view-status").css("display","none");

						if($("#body-my-scheduled").hasClass('status-schedule')){
							$("#body-my-scheduled").removeClass("status-schedule");
						}
                        $("#body-my-scheduled").animate({scrollTop: 0});
				
                        var day = $(this).attr("data-day");
                        var type = $(this).attr("data-type");

                        var today = new Date(day.replace("-", ","));
                        var weekday = new Array(7);
                            weekday[0] =  "Sun";
                            weekday[1] = "Mon";
                            weekday[2] = "Tue";
                            weekday[3] = "Wed";
                            weekday[4] = "Thur";
                            weekday[5] = "Fri";
                            weekday[6] = "Sat";                                
                        var n = weekday[today.getDay()];
                        var month_text = getMonthtoText(today.getMonth()+1);
                        $('.current-day').text(month_text + ' ' + today.getDate());
                        $('.stuff-day').text(' (' + n + ')');

                        var prev_d = new Date(day.replace("-", ","));
                        prev_d.setDate(prev_d.getDate() - 1);
                        var dd = prev_d.getDate();
                        var mm = prev_d.getMonth()+1; //January is 0!
                        var yyyy = prev_d.getFullYear();
                        if(dd < 10) {
                            dd = "0"+dd;
                        }
                        if(mm < 10) {
                            mm = "0"+mm;
                        }

                        var next_d = new Date(day.replace("-", ","));
                        next_d.setDate(next_d.getDate() + 1); 
                        var dd_n = next_d.getDate();
                        var mm_n = next_d.getMonth()+1; //January is 0!
                        var yyyy_n = next_d.getFullYear();
                        if(dd_n < 10) {
                            dd_n = "0"+dd_n;
                        }
                        if(mm_n < 10) {
                            mm_n = "0"+mm_n;
                        }
                        
                        $("#menu-schedule-btn").attr('data-day',day);
                        $(this).attr('data-day',yyyy+'-'+mm+'-'+dd);
                        $(".schedule-right-btn").attr('data-day',yyyy_n+'-'+mm_n+'-'+dd_n);

                        $('.datepicker-days td.day').each(function () {
                            var full_date = $(this).attr('data-day');
                            var st = full_date.split("/");
                            var formattedDate = st[2] + "-" + st[0] + "-" + st[1];
                            
                            if($(this).hasClass('active disabled')){
                                $(this).removeClass('active disabled');
                                $(this).attr('data-action','selectDay');
                            }

                            if(day == formattedDate){
                                $(this).addClass('active disabled');
                                $(this).attr('data-action','disabled');
                            }
                        });

                        get_list_schedule('schedule');
                        get_scheduled_day(day,type);
                    });

                    $(".schedule-right-btn").click(function(){
						$('.header-schedule').removeClass('active');
						$('#list-schedule-status').css("display","none");
						$('#table-status-schedule').html('');
						$("#open-menu-schedule").css("display","none");
						$(".main-view-status").css("display","none");

						if($("#body-my-scheduled").hasClass('status-schedule')){
							$("#body-my-scheduled").removeClass("status-schedule");
						}
                        $("#body-my-scheduled").animate({scrollTop: 0});

                        var day = $(this).attr("data-day");
                        var type = $(this).attr("data-type");
                                                    
                        var today = new Date(day.replace("-", ","));
                        var weekday = new Array(7);
                            weekday[0] =  "Sun";
                            weekday[1] = "Mon";
                            weekday[2] = "Tue";
                            weekday[3] = "Wed";
                            weekday[4] = "Thur";
                            weekday[5] = "Fri";
                            weekday[6] = "Sat";                                
                        var n = weekday[today.getDay()];
                        var month_text = getMonthtoText(today.getMonth()+1);
                        $('.current-day').text(month_text + ' ' + today.getDate());
                        $('.stuff-day').text(' (' + n + ')');

                        var next_d = new Date(day.replace("-", ","));
                        next_d.setDate(next_d.getDate() + 1);
                        var dd = next_d.getDate();
                        var mm = next_d.getMonth()+1; //January is 0!
                        var yyyy = next_d.getFullYear();
                        if(dd < 10) {
                            dd = "0"+dd;
                        }
                        if(mm < 10) {
                            mm = "0"+mm;
                        }

                        var prev_d = new Date(day.replace("-", ","));
                        prev_d.setDate(prev_d.getDate() - 1);
                        var dd_n = prev_d.getDate();
                        var mm_n = prev_d.getMonth()+1; //January is 0!
                        var yyyy_n = prev_d.getFullYear();
                        if(dd_n < 10) {
                            dd_n = "0"+dd_n;
                        }
                        if(mm_n < 10) {
                            mm_n = "0"+mm_n;
                        }
                        
                        $("#menu-schedule-btn").attr('data-day',day);
                        $(this).attr('data-day',yyyy+'-'+mm+'-'+dd);
                        $(".schedule-left-btn").attr('data-day',yyyy_n+'-'+mm_n+'-'+dd_n);
                        
                        $('.datepicker-days td.day').each(function () {
                            var full_date = $(this).attr('data-day');
                            var st = full_date.split("/");
                            var formattedDate = st[2] + "-" + st[0] + "-" + st[1];
                            
                            if($(this).hasClass('active disabled')){
                                $(this).removeClass('active disabled');
                                $(this).attr('data-action','selectDay');
                            }

                            if(day == formattedDate){
                                $(this).addClass('active disabled');
                                $(this).attr('data-action','disabled');
                            }
                        });
						
                        get_list_schedule('schedule');
                        get_scheduled_day(day,type);
                    });

                    $("#btn-my-timezone").click(function () {
                        $('#my-timezone').toggle();
                        $("#open-menu-quicknotifi").css("display","none");

                    });

                    $('.my-timezone').click(function(){
                        if($(this).hasClass('active')){
                            $('#my-timezone').toggle();
                        }else{
                            var timezone = $(this).attr("data-value");
                            var name = $(this).attr("data-name");
                            var index = $(this).attr("data-index");
                            var city = $(this).attr("data-city");
                            var path = '<?php echo get_template_directory_uri() ?>/library/images/';

                            $('.my-timezone').removeClass('active');
                            $(this).addClass('active');
                            $('#mycity-name').text(city);

                            $("#select-timezone").selectBoxIt('selectOption',index.toString()).data("selectBox-selectBoxIt");
                            $("#select-timezone").data("selectBox-selectBoxIt").refresh();

                            $('#my-timezone').toggle();

                            $.post(home_url + "/?r=ajax/update_timezone", {                                   
                                timezone: timezone,
                                name: name,
                                index: index
                            }, function (data) {});

                            initDateTimePicker(name, index, timezone, city);
                            clearInterval(interval);
                            clearInterval(intervalmy);
                            clearInterval(intervalcity);
                            initTimeClock(name);
                            initMyTimeClock(name);

                            var available_time = $("#mytime-clock").attr("data-available-time");
                            $("#select-available-time").selectBoxIt('selectOption',available_time.toString()).data("selectBox-selectBoxIt");
                            $("#select-available-time").data("selectBox-selectBoxIt").refresh();

                            $.get(home_url + "/?r=ajax/get_my_schedules", {timezone: timezone}, function (data) {
                                //console.log(data);
                                data = JSON.parse(data);
                                if (data.length > 0) {
                                    $('.slide-my-schedule').html('');
                                    $('.slide-my-schedule').removeClass('slick-initialized');
                                    $('.slide-my-schedule').removeClass('slick-slider');
                                    $.each(data, function (i, v) {
                                        var html_slide = '';
                                        html_slide += '<div class="item" data-fromhour="'+v.fromhour+'" data-fromminute="'+v.fromminute+'" data-tohour="'+v.tohour+'" data-tominute="'+v.tominute+'" data-day="'+v.day+'" data-type="'+v.totype+'">';
                                            html_slide += '<div class="description-detail">';
                                                html_slide += '<p class="subject-detail">';
                                                    html_slide += '<span class="name-subject">'+v.private_subject+'</span>';
                                                html_slide += '</p>';
                                                html_slide += '<p class="my-time-request">';
                                                    html_slide += '<span class="label-timezone">Date:</span>';
                                                    html_slide += '<span class="my-current-day">'+v.date+'</span>';
                                                    html_slide += '<span class="my-stuff-day">'+v.stuff+'/</span>';
                                                    html_slide += '<span class="my-time-current">'+v.time_view+'</span>';
                                                html_slide += '</p>';
                                                html_slide += '<p class="name-detail">';
                                                    html_slide += '<span class="label-tutor">Tutor:</span>';
                                                    html_slide += '<span class="name-tutor">'+v.tutor_name+'</span>';
                                                html_slide += '</p>';
                                                html_slide += '<p class="points-detail">';
                                                    html_slide += '<span class="label-points">Points:</span>';
                                                    html_slide += '<span class="name-points">'+v.total+' Points($)</span>';
                                                html_slide += '</p>';
                                            html_slide += '</div>';
                                            if(v.type_slide == 'current'){
                                                html_slide += '<button id="btn-start-now'+v.id+'" class="btn-dark-blue btn-start-now active" type="button" name="start-now" data-id="'+v.id+'" data-student-id="'+v.id_user+'" data-teacher-id="'+v.tutor_id+'" tabindex="0">Initiate Now!</button>';
                                            }else{
                                                html_slide += '<button id="btn-cancel-schedule'+v.id+'" class="btn-dark-blue btn-cancel-schedule active" type="button" name="cancel-schedule" data-id="'+v.id+'" data-student-id="'+v.id_user+'" data-teacher-id="'+v.tutor_id+'" tabindex="0"><img src="'+path+'icon_Most-Current-Arrow.png" alt=""> Most Current</button>';
                                            }
                                            html_slide += '<button class="cancel-now" id="cancel-now'+v.id+'" data-id="'+v.id+'">';
                                                html_slide += '<img src="'+path+'close_white.png">';
                                            html_slide += '</button>';
                                        html_slide += '</div>';

                                        $('.slide-my-schedule').append(html_slide);
                                    });
                                    if(data.length > 1)
                                        $('.slide-my-schedule').not('.slick-initialized').slick(getSliderSettings(true,1));
                                    else
                                        $('.slide-my-schedule').not('.slick-initialized').slick(getSliderSettings(true,0));
                                }else{
                                    $('.slide-my-schedule').html('');
                                    $('.slide-my-schedule').removeClass('slick-initialized');
                                    $('.slide-my-schedule').removeClass('slick-slider');
                                    var html_slide = '';
                                    html_slide += '<div class="item no-detail-schedule">';
                                        html_slide += '<div class="description-detail">';
                                            html_slide += '<p class="subject-detail">';
                                                html_slide += '<span class="name-subject">Currently there\'s no schedules</span>';
                                            html_slide += '</p>';
                                            html_slide += '<p class="my-time-request">';
                                                html_slide += '<span class="label-timezone">Date:</span>';
                                                html_slide += '<span class="my-current-day">N/A</span>';
                                                html_slide += '<span class="my-stuff-day"></span>';
                                                html_slide += '<span class="my-time-current"></span>';
                                            html_slide += '</p>';
                                            html_slide += '<p class="name-detail">';
                                                html_slide += '<span class="label-tutor">Tutor:</span>';
                                                html_slide += '<span class="name-tutor">N/A</span>';
                                            html_slide += '</p>';
                                            html_slide += '<p class="points-detail">';
                                                html_slide += '<span class="label-points">Points:</span>';
                                                html_slide += '<span class="name-points">0 Points($)</span>';
                                            html_slide += '</p>';
                                        html_slide += '</div>';
                                        html_slide += '<button id="btn-cancel-schedule" class="btn-dark-blue btn-cancel-schedule no-active" type="button" name="cancel-schedule" data-id="" data-student-id="" data-teacher-id="" tabindex="0"><img src="'+path+'icon_Most-Current-Arrow.png" alt=""> Most Current</button>';
                                    html_slide += '</div>';

                                    $('.slide-my-schedule').append(html_slide);
                                    $('.slide-my-schedule').not('.slick-initialized').slick(getSliderSettings(true,0));
                                }
                            });
                        }
                    });

                    $(".slide-resume").slick(getSliderSettings(false));

                    $(".slide-my-schedule").slick(getSliderSettings(true));

                    $(".slide-resume").on('swipe', function(event, slick, direction){
                        if($('.writting-review').hasClass("active")){
                            $('.writting-review').removeClass("active");
                            $('.writting-review').css("display","none");
                        }  

                        $('.tr-info').css('display','block');
                        $('.tr-review').css('display','none');
                        $('.view-review').attr('src','<?php echo get_template_directory_uri(); ?>/library/images/iconM_Review_OFF.png');
                        $('.view-resume').attr('src','<?php echo get_template_directory_uri(); ?>/library/images/iconM_Resume_ON-O.png');
                        $('.view-write-review').attr('src','<?php echo get_template_directory_uri(); ?>/library/images/iconM_Write_Review_OFF.png');

                        if($('#selected-tutor').hasClass('active')){
                            $('#selected-tutor').removeClass('active');
                            $('#btn-schedule-now').removeClass('active');
                            $('#selected-tutor').text('Not selected yet');
                            $('#btn-schedule-now').attr('data-tutor-id','');
                        }
                    });

                    // Event custom scrollbar
                    $('.style-scrollbar').mCustomScrollbar({theme:"dark-thick"});

                    function initMyTimeClock(timezone_name){
                        var date_utc = moment.utc().format('YYYY-MM-DD');
                        var date_my = moment.tz(timezone_name).format('YYYY-MM-DD');
                        var ct = 0;
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';

                        let mytime = moment.tz(timezone_name).format('hh:mm A');
                        if (document.getElementById('mytime-clock')) {
                            document.getElementById('mytime-clock').innerHTML = mytime;
                        }

                        if(moment.tz(timezone_name).format('m') < 30)
                            var available_time = moment.tz(timezone_name).format('h')+':00'+moment.tz(timezone_name).format('a');
                        else
                            var available_time = moment.tz(timezone_name).format('h')+':30'+moment.tz(timezone_name).format('a');
                            
                        $('#mytime-clock').attr('data-hour',moment.tz(timezone_name).format('H'));
                        $('#mytime-clock').attr('data-minute',moment.tz(timezone_name).format('m'));
                        $('#mytime-clock').attr('data-type',moment.tz(timezone_name).format('a'));
                        $('#mytime-clock').attr('data-available-time',available_time);
                        
                        var html1 = '<option value="0">Select Time</option>';
                        var current_hour1 =  parseInt(moment.tz(timezone_name).format('H'));
                        var current_minute1 = parseInt(moment.tz(timezone_name).format('m'));
                        var type = 'am';
                        for (var i = current_hour1; i < 24; i++) {                            
                            var id = i;
                            if (i > 11){
                                var j = i - 12;                                    
                                type = 'pm'; 
                            }else{
                                var j = i;
                            }

                            if(j == 0) id = j = 12;

                            var kl = (parseInt(id) + 1);
                            if(kl > 12){
                                var ks  = kl - 12;
                            }else{
                                var ks = kl;
                            }

                            if(ks < 10) 
                                var kll = '0'+ks;
                            else
                                var kll = ks;

                            if(j < 10) 
                                var jk = '0'+j;
                            else
                                var jk = j;
                            
                            if(current_minute1 > 29 && i == current_hour1){
                    	        html1 += '<option data-time="'+j+':30:'+type+' ~ '+ks+':00:'+type+'" data-time-view="'+j+':30'+type+'-'+ks+':00'+type+'" value="'+j+':30'+type+'">'+jk+':30 '+type+' - '+kll+':00 '+type+'</option>';
                    	    }else{
                    	        html1 += '<option data-time="'+j+':00:'+type+' ~ '+j+':30:'+type+'" data-time-view="'+j+':00'+type+'-'+j+':30'+type+'" value="'+j+':00'+type+'">'+jk+':00 '+type+' - '+jk+':30 '+type+'</option>';
                    	        html1 += '<option data-time="'+j+':30:'+type+' ~ '+ks+':00:'+type+'" data-time-view="'+j+':30'+type+'-'+ks+':00'+type+'" value="'+j+':30'+type+'">'+jk+':30 '+type+' - '+kll+':00 '+type+'</option>';
                    	    }
                        }
                        $('#select-available-time').html(html1).data("selectBox-selectBoxIt").refresh();
                        
                        var current_date = $('#today-tutor').val();
                        var html2 = '<option value="0">Select Day</option>';
                        var current_day = parseInt(current_date.split('-')[2]);
                        for (var jd = current_day; jd < 32; jd++) {
                            if(current_day == jd)
                                var seld = 'selected="selected"';
                            else
                                var seld = '';

                            if(jd < 10)
                                var it = '0'+jd;
                            else
                                var it = jd;

                            html2 += '<option '+seld+' value="'+it+'">'+it+'</option>';
                        }
                        $('#select-available-day').html(html2).data("selectBox-selectBoxIt").refresh();

                        intervalmy = setInterval(() => {
                            mytime = moment.tz(timezone_name).format('hh:mm A');
                            if (document.getElementById('mytime-clock')) {
                                document.getElementById('mytime-clock').innerHTML = mytime;
                            }
                            
                            if(moment.tz(timezone_name).format('m') < 30)
                                var available_time_sel = moment.tz(timezone_name).format('h')+':00'+moment.tz(timezone_name).format('a');
                            else
                                var available_time_sel = moment.tz(timezone_name).format('h')+':30'+moment.tz(timezone_name).format('a');
                                    
                            $('#mytime-clock').attr('data-hour',moment.tz(timezone_name).format('H'));
                            $('#mytime-clock').attr('data-minute',moment.tz(timezone_name).format('m'));
                            $('#mytime-clock').attr('data-type',moment.tz(timezone_name).format('a'));
                            $('#mytime-clock').attr('data-available-time',available_time_sel);
                            
                            ct++;
                            if(ct == 60){
                                ct = 0;
                                var schedule_now = false;
                                var iSlide = 0;
                                     
                                $('.slide-my-schedule .slick-slide').each(function () {
                                    var index = $(this).attr('data-slick-index');
                                    var hour =  $(this).find('.item').attr('data-tohour');
                                    var minute =  $(this).find('.item').attr('data-tominute');
                                    var type =  $(this).find('.item').attr('data-type');
                                    var day =  $(this).find('.item').attr('data-day');

                                    var chour =  moment.tz(timezone_name).format('H');
                                    var cminute =  moment.tz(timezone_name).format('m');
                                    var ctype =  moment.tz(timezone_name).format('a');
                                    var today = $('#today-tutor').val();
                                  
                                    if(today == day && parseInt(chour) >= parseInt(hour) && parseInt(cminute) >= parseInt(minute)){
                                        $('#menu-quick-notification').find('img').attr('src',path + '08_Top_Trigger_NOTIFICATION.png');
                                        if(!$('#menu-quick-notification').find('img').hasClass('active')){
                                            $('#menu-quick-notification').find('img').addClass('active');
                                        }

                                        $('.slide-my-schedule').slick('slickRemove',index).slick('refresh');

                                        get_scheduled_day(today,'schedule');
                                    }
                                    iSlide++;
                                });
                                if(iSlide == 0){
                                    $.get(home_url + "/?r=ajax/get_my_schedules", {timezone: ""}, function (data) {
                                        //console.log(data);
                                        data = JSON.parse(data);
                                        if (data.length > 0) {
                                            $('.slide-my-schedule').html('');
                                            $('.slide-my-schedule').removeClass('slick-initialized');
                                            $('.slide-my-schedule').removeClass('slick-slider');
                                            $.each(data, function (i, v) {
                                                var html_slide = '';
                                                html_slide += '<div class="item" data-fromhour="'+v.fromhour+'" data-fromminute="'+v.fromminute+'" data-tohour="'+v.tohour+'" data-tominute="'+v.tominute+'" data-day="'+v.day+'" data-type="'+v.totype+'">';
                                                    html_slide += '<div class="description-detail">';
                                                        html_slide += '<p class="subject-detail">';
                                                            html_slide += '<span class="name-subject">'+v.private_subject+'</span>';
                                                        html_slide += '</p>';
                                                        html_slide += '<p class="my-time-request">';
                                                            html_slide += '<span class="label-timezone">Date:</span>';
                                                            html_slide += '<span class="my-current-day">'+v.date+'</span>';
                                                            html_slide += '<span class="my-stuff-day">'+v.stuff+'/</span>';
                                                            html_slide += '<span class="my-time-current">'+v.time_view+'</span>';
                                                        html_slide += '</p>';
                                                        html_slide += '<p class="name-detail">';
                                                            html_slide += '<span class="label-tutor">Tutor:</span>';
                                                            html_slide += '<span class="name-tutor">'+v.tutor_name+'</span>';
                                                        html_slide += '</p>';
                                                        html_slide += '<p class="points-detail">';
                                                            html_slide += '<span class="label-points">Points:</span>';
                                                            html_slide += '<span class="name-points">'+v.total+' Points($)</span>';
                                                        html_slide += '</p>';
                                                    html_slide += '</div>';
                                                    if(v.type_slide == 'current'){
                                                        html_slide += '<button id="btn-start-now'+v.id+'" class="btn-dark-blue btn-start-now active" type="button" name="start-now" data-id="'+v.id+'" data-student-id="'+v.id_user+'" data-teacher-id="'+v.tutor_id+'" tabindex="0">Initiate Now!</button>';
                                                    }else{
                                                        html_slide += '<button id="btn-cancel-schedule'+v.id+'" class="btn-dark-blue btn-cancel-schedule active" type="button" name="cancel-schedule" data-id="'+v.id+'" data-student-id="'+v.id_user+'" data-teacher-id="'+v.tutor_id+'" tabindex="0"><img src="'+path+'icon_Most-Current-Arrow.png" alt=""> Most Current</button>';
                                                    }
                                                    html_slide += '<button class="cancel-now" id="cancel-now'+v.id+'" data-id="'+v.id+'">';
                                                        html_slide += '<img src="'+path+'close_white.png">';
                                                    html_slide += '</button>';
                                                html_slide += '</div>';

                                                $('.slide-my-schedule').append(html_slide);
                                            });
                                            if(data.length > 1)
                                                $('.slide-my-schedule').not('.slick-initialized').slick(getSliderSettings(true,1));
                                            else
                                                $('.slide-my-schedule').not('.slick-initialized').slick(getSliderSettings(true,0));
                                        }else{
                                            $('.slide-my-schedule').html('');
                                            $('.slide-my-schedule').removeClass('slick-initialized');
                                            $('.slide-my-schedule').removeClass('slick-slider');
                                            var html_slide = '';
                                            html_slide += '<div class="item no-detail-schedule">';
                                                html_slide += '<div class="description-detail">';
                                                    html_slide += '<p class="subject-detail">';
                                                        html_slide += '<span class="name-subject">Currently there\'s no schedules</span>';
                                                    html_slide += '</p>';
                                                    html_slide += '<p class="my-time-request">';
                                                        html_slide += '<span class="label-timezone">Date:</span>';
                                                        html_slide += '<span class="my-current-day">N/A</span>';
                                                        html_slide += '<span class="my-stuff-day"></span>';
                                                        html_slide += '<span class="my-time-current"></span>';
                                                    html_slide += '</p>';
                                                    html_slide += '<p class="name-detail">';
                                                        html_slide += '<span class="label-tutor">Tutor:</span>';
                                                        html_slide += '<span class="name-tutor">N/A</span>';
                                                    html_slide += '</p>';
                                                    html_slide += '<p class="points-detail">';
                                                        html_slide += '<span class="label-points">Points:</span>';
                                                        html_slide += '<span class="name-points">0 Points($)</span>';
                                                    html_slide += '</p>';
                                                html_slide += '</div>';
                                                html_slide += '<button id="btn-cancel-schedule" class="btn-dark-blue btn-cancel-schedule no-active" type="button" name="cancel-schedule" data-id="" data-student-id="" data-teacher-id="" tabindex="0"><img src="'+path+'icon_Most-Current-Arrow.png" alt=""> Most Current</button>';
                                            html_slide += '</div>';

                                            $('.slide-my-schedule').append(html_slide);
                                            $('.slide-my-schedule').not('.slick-initialized').slick(getSliderSettings(true,0));
                                        }
                                    });
                                }

                                var current_hour =  parseInt(moment.tz(timezone_name).format('H'));
                                var current_minute = parseInt(moment.tz(timezone_name).format('m'));
                                var year = $('#available_year').val();
                                var day = $("#select-available-daySelectBoxItText").attr("data-val");
                                var month = $("#select-available-monthSelectBoxItText").attr("data-val");
                                if(month != 0 && day != 0 && year != 0)
                                    var date = year + '-' + month + '-' + day;
                                else
                                    var date = '';
                                var type = 'am';
                                var current_day = $('#today-tutor').val();
                                //console.log(parseInt('00'));
                                    
                                if(date == current_day && (current_minute == 0 || current_minute == 30)){
                                    var html = '<option value="0">Select Time</option>';
                                    for (var i = current_hour; i < 24; i++) {                            
                                        var id = i;
                                        if (i > 11){
                                            var j = i - 12;                                    
                                            type = 'pm'; 
                                        }else{
                                            var j = i;
                                        }

                                        if(j == 0) id = j = 12;

                                        var kl = (parseInt(id) + 1);
                                        if(kl > 12){
                                            var ks  = kl - 12;
                                        }else{
                                            var ks = kl;
                                        }

                                        if(ks < 10) 
                                            var kll = '0'+ks;
                                        else
                                            var kll = ks;

                                        if(j < 10) 
                                            var jk = '0'+j;
                                        else
                                            var jk = j;
                                        
                                        if(current_minute > 29 && i == current_hour){
                                	        var sel_time = j+':30'+type;
                                	        if(sel_time == available_time_sel)
                                	            var sel = 'selected="selected"';
                                	        else
                                	            var sel = '';
                                	        html += '<option '+sel+' data-time="'+j+':30:'+type+' ~ '+ks+':00:'+type+'" data-time-view="'+j+':30'+type+'-'+ks+':00'+type+'" value="'+j+':30'+type+'">'+jk+':30 '+type+' - '+kll+':00 '+type+'</option>';
                                	    }else{
                                	        var sel_time1 = j+':00'+type;
                                	        if(sel_time1 == available_time_sel)
                                	            var sel1 = 'selected="selected"';
                                	        else
                                	            var sel1 = '';
                                	            
                                	        var sel_time2 = j+':30'+type;
                                	        if(sel_time2 == available_time_sel)
                                	            var sel2 = 'selected="selected"';
                                	        else
                                	            var sel2 = '';
                                	        html += '<option '+sel1+' data-time="'+j+':00:'+type+' ~ '+j+':30:'+type+'" data-time-view="'+j+':00'+type+'-'+j+':30'+type+'" value="'+j+':00'+type+'">'+jk+':00 '+type+' - '+jk+':30 '+type+'</option>';
                                	        html += '<option '+sel2+' data-time="'+j+':30:'+type+' ~ '+ks+':00:'+type+'" data-time-view="'+j+':30'+type+'-'+ks+':00'+type+'" value="'+j+':30'+type+'">'+jk+':30 '+type+' - '+kll+':00 '+type+'</option>';
                                	    }
                                    }
                                    $('#select-available-time').html(html).data("selectBox-selectBoxIt").refresh();
                                }
                            }

                            if(ct%10 == 0){
                                if($('#tutoring-scheduled').hasClass('active') && $('body').hasClass('modal-open')){
                                    var day = $('#menu-schedule-btn').attr('data-day');
                                    //getAcceptTutor(day);
                                    get_scheduled_day(day,'schedule', false, false);
                                }

                                var displayName = $(".display-name").text();
                                if (displayName !== '' && $('body').hasClass('modal-open')) {
                                    getQuickNotification(false);
                                }
                            }

                            //get_list_schedule('schedule', current_hour);
                        }, 1000);

                        $('.my-timezone').each(function () {
                            var name = $(this).attr('data-name');
                            var index = $(this).attr('data-index');
                            var city = $(this).attr('data-city');

                            var date_city = moment.tz(name).format('YYYY-MM-DD');
                            var CurrentDate = new Date(date_my);
                            var GivenDate = new Date(date_city);
                            if(GivenDate > CurrentDate){
                                var txt = city + ' (tomorrow)';
                            }else{
                                var txt = '';
                            }
                            $('#name-clock'+index).text(txt);

                            let citytime = moment.tz(name).format('hh:mm A');
                            document.getElementById('name-clock'+index).innerHTML = citytime;
                            $('#name-clock'+index).attr('data-hour',moment.tz(name).format('H'));
                            $('#name-clock'+index).attr('data-minute',moment.tz(name).format('m'));
                            $('#name-clock'+index).attr('data-type',moment.tz(name).format('a'));

                            intervalcity = setInterval(() => {
                                let citytime = moment.tz(name).format('hh:mm A');
                                document.getElementById('name-clock'+index).innerHTML = citytime;
                                $('#name-clock'+index).attr('data-hour',moment.tz(name).format('H'));
                                $('#name-clock'+index).attr('data-minute',moment.tz(name).format('m'));
                                $('#name-clock'+index).attr('data-type',moment.tz(name).format('a'));
                            }, 1000);
                        });
                    }

                    function initTimeClock(timezone_name){
                        var date_utc = moment.utc().format('YYYY-MM-DD HH:mm:ss');

                        let time = moment.tz(timezone_name).format('h:mm a');
                        if (document.getElementById('time-clock')) {
                            document.getElementById('time-clock').innerHTML = time;
                        }
                        $('#time-clock').attr('data-hour',moment.tz(timezone_name).format('H'));
                        $('#time-clock').attr('data-minute',moment.tz(timezone_name).format('m'));
                        $('#time-clock').attr('data-type',moment.tz(timezone_name).format('a'));
                          
                        interval = setInterval(() => {
                            time = moment.tz(timezone_name).format('h:mm a');
                            if (document.getElementById('time-clock')) {
                                document.getElementById('time-clock').innerHTML = time;
                            }
                            $('#time-clock').attr('data-hour',moment.tz(timezone_name).format('H'));
                            $('#time-clock').attr('data-minute',moment.tz(timezone_name).format('m'));
                            $('#time-clock').attr('data-type',moment.tz(timezone_name).format('a'));
                        }, 1000);

                        var text = $('#select-timezoneSelectBoxItText').text();
                        var font = $('#select-timezoneSelectBoxItText').css("font");
                        if (document.documentMode || /Edge/.test(navigator.userAgent)) {
                            //console.log("Edge");
                            var offset = getDistancePlace(text, "Edge");
                        }else if (navigator.userAgent.search("Firefox") >= 0){
                            var offset = getDistancePlace(text, "Firefox");
                            //console.log("Firefox");
                        }else{
                            var offset = getDistancePlace(text, "Chrome");
                        }

                        var left = (getTextWidth(text,font) + offset);
                        $('#time-clock').css("right",left+"px");
                    }

                    function initCalendar(type = '', activeDay = ''){
                        // if(type != ''){
                        //     $.ajax({
                        //         url: home_url + "/?r=ajax/get_tutoring_date_active",
                        //         type: 'get',

                        //         success: function (data) {
                        //             $('#active-day-tutor').val(data);
                        //         }
                        //     });
                        // }
                        //var activeDay = $('#active-day-tutor').val();
                        if(activeDay == ''){
                            activeDay = active_day;
                        }else{
                            activeDay = $.parseJSON(activeDay);
                        }
                        
                        $('.datepicker-days td.day').each(function () {
                            var full_date = $(this).attr('data-day');
                            var st = full_date.split("/");
                            var formattedDate = st[2] + "-" + st[0] + "-" + st[1];
                            
                            if($(this).hasClass('activeClass')){
                                $(this).removeClass('activeClass');
                            }
                            
                            if($.inArray(formattedDate, activeDay) != -1){
                                //console.log('Sau: '+activeDay);
                                if(!$(this).hasClass('activeClass')){
                                    $(this).addClass('activeClass');
                                }
                            }
                            if($(this).hasClass('active')){
                                $(this).removeClass('active');
                            }
                        });       

                        $('#sandbox-container-tutor').on("dp.update", function (e) {
                            e.preventDefault();
                            var active_Day = $('#active-day-tutor').val();
                            if(active_Day != ''){
                                activeDay = $.parseJSON(active_Day);
                            }
                            //console.log('update');
                            $('.datepicker-days td.day').each(function () {
                                var full_date = $(this).attr('data-day');
                                var st = full_date.split("/");
                                var formattedDate = st[2] + "-" + st[0] + "-" + st[1];

                                if($(this).hasClass('activeClass')){
                                    $(this).removeClass('activeClass');
                                }

                                if($.inArray(formattedDate, activeDay) != -1){
                                    if(!$(this).hasClass('activeClass')){
                                        $(this).addClass('activeClass');
                                    }
                                }
                            });    
                        });

                        $('#sandbox-container-tutor').on("dp.change", function (e) {
                            e.preventDefault();
                            var active_Day = $('#active-day-tutor').val();
                            if(active_Day != ''){
                                activeDay = $.parseJSON(active_Day);
                            }
                            //console.log('change');
                            $('.datepicker-days td.day').each(function () {
                                var full_date = $(this).attr('data-day');
                                var st = full_date.split("/");
                                var formattedDate = st[2] + "-" + st[0] + "-" + st[1];

                                if($(this).hasClass('activeClass')){
                                    $(this).removeClass('activeClass');
                                }

                                if($.inArray(formattedDate, activeDay) != -1){
                                    if(!$(this).hasClass('activeClass')){
                                        $(this).addClass('activeClass');
                                    }
                                }
                            });    
                        });          

                        if(type == ''){
                            $(".datepicker .datepicker-days").on('click', 'td.day', function (e) {
                                e.preventDefault();
                                if(!$(this).hasClass('disabled')){
                                    $("#body-my-scheduled").animate({scrollTop: 0});

                                    $('.datepicker-days td.day').removeClass('active disabled');
                                    $('.datepicker-days td.day').attr('data-action','selectDay');

                                    $(this).addClass('active disabled');
                                    $(this).attr('data-action','disabled');
                                    var active_Day = $('#active-day-tutor').val();
                                    if(active_Day != ''){
                                        activeDay = $.parseJSON(active_Day);
                                        $('.datepicker-days td.day').removeClass('activeClass');    
                                        //console.log(activeDay);
                                    }
                                    $('.datepicker-days td.day').each(function () {
                                        var full_date = $(this).attr('data-day');
                                        var st = full_date.split("/");
                                        var formattedDate = st[2] + "-" + st[0] + "-" + st[1];
                                        
                                        if($(this).hasClass('activeClass')){
                                            $(this).removeClass('activeClass');
                                        }
                                        
                                        if($.inArray(formattedDate, activeDay) != -1){
                                            if(!$(this).hasClass('activeClass')){
                                                $(this).addClass('activeClass');
                                            }
                                        }
                                    });
                                    
                                    var full_date = $(this).attr('data-day');
                                    var action = $(this).attr('data-action');
                                    //$("#menu-schedule-btn").text('Summary');
                                    $("#menu-schedule-btn").attr("data-type","summary");
                                    var get_date = $(this).text();
                                    var date1 = $(".picker-switch").text();
                                    var st = date1.split(" ");
                                    var month_text = st[0];       
                                    var day = new Date(full_date.replace("/", ","));
                                    var weekday = new Array(7);
                                        weekday[0] =  "Sun";
                                        weekday[1] = "Mon";
                                        weekday[2] = "Tue";
                                        weekday[3] = "Wed";
                                        weekday[4] = "Thur";
                                        weekday[5] = "Fri";
                                        weekday[6] = "Sat";                                
                                    var n = weekday[day.getDay()];
                                    const monthNames = ["January", "February", "March", "April", "May", "June",
                                        "July", "August", "September", "October", "November", "December"];                         
                                    $('.current-day').text(monthNames[full_date.split("/")[0]-1] + ' ' + day.getDate());
                                    $('.stuff-day').text(' (' + n + ')');

                                    var prev = new Date(full_date.replace("/", ","));
                                    prev.setDate(prev.getDate() - 1);

                                    var dd = prev.getDate();
                                    var mm = prev.getMonth()+1; //January is 0!
                                    var yyyy = prev.getFullYear();
                                    if(dd < 10) {
                                        dd = "0"+dd;
                                    }
                                    if(mm < 10) {
                                        mm = "0"+mm;
                                    }

                                    var next = new Date(full_date.replace("/", ","));
                                    next.setDate(next.getDate() + 1);
                                    var dd_n = next.getDate();
                                    var mm_n = next.getMonth()+1; //January is 0!
                                    var yyyy_n = next.getFullYear();
                                    if(dd_n < 10) {
                                        dd_n = "0"+dd_n;
                                    }
                                    if(mm_n < 10) {
                                        mm_n = "0"+mm_n;
                                    }

                                    var current = full_date.split("/");
                                    var formattedDate = current[2] + "-" + current[0] + "-" + current[1];
                                    
                                    $("#menu-schedule-btn").attr('data-day',formattedDate);
                                    $(".schedule-left-btn").attr('data-day',yyyy+'-'+mm+'-'+dd);
                                    $(".schedule-right-btn").attr('data-day',yyyy_n+'-'+mm_n+'-'+dd_n);
    								
    								$('.header-schedule').removeClass('active');
    								$('#list-schedule-status').css("display","none");
    								$('#table-status-schedule').html('');
    								$("#open-menu-schedule").css("display","none");
    								$(".main-view-status").css("display","none");

    								if($(".main-my-schedule").hasClass('active-tab-schedule')){
    									$(".main-my-schedule").removeClass("active-tab-schedule");
    								}

    								if($("#body-my-scheduled").hasClass('status-schedule')){
    									$("#body-my-scheduled").removeClass("status-schedule");
    								}
    								
                                    if($(this).hasClass('today')){
                                        var th = $('#time-clock').attr('data-hour');
                                        get_list_schedule('schedule', th);
                                    }else{
                                        get_list_schedule('schedule');
                                    }
                                    get_scheduled_day(formattedDate,'schedule');
                                }else{
                                    var full_date = $(this).attr('data-day');
                                    var current = full_date.split("/");
                                    var formattedDate = current[2] + "-" + current[0] + "-" + current[1];

                                    if($(this).hasClass('today')){
                                        var th = $('#time-clock').attr('data-hour');
                                        get_list_schedule('schedule', th);
                                    }else{
                                        get_list_schedule('schedule');
                                    }
                                    get_scheduled_day(formattedDate,'schedule');
                                }
                            });
                        }  
                    }

                    function getMonthtoText(m = 0){
                        switch(m) {
                            case 1 :
                            var month = "January"; 
                            break;
                            
                            case 2 : 
                            var month = "February"; 
                            break;
                            
                            case 3 : 
                            var month = "March"; 
                            break;
                            
                            case 4 : 
                            var month = "April"; 
                            break;
                            
                            case 5 : 
                            var month = "May"; 
                            break;
                            
                            case 6 : 
                            var month = "June"; 
                            break;
                            
                            case 7 : 
                            var month = "July"; 
                            break;
                            
                            case 8 : 
                            var month = "August"; 
                            break;
                            
                            case 9 : 
                            var month = "September"; 
                            break;
                            
                            case 10 : 
                            var month = "October"; 
                            break;
                            
                            case 11 : 
                            var month = "November"; 
                            break;
                            
                            case 12 : 
                            var month = "December"; 
                            break;

                            default:
                               var month = "January"; 
                        }
                        return month
                    }

                    function get_scheduled_day(day = '', type = 'schedule', reload = false, realtime = true){
                        if(type == 'schedule'){
                            //$('.table-tutoring').removeClass('hide');
                            $('#tutoring-scheduled').removeClass('list-summary');
                            var fl = ' class="icon-status"';
                            var icon_arrow = '';
                        }else{
                            //$('.table-tutoring').addClass('hide');
                            $('#tutoring-scheduled').addClass('list-summary');
                            var fl = ' class="icon-status"';
                            var icon_arrow = '';
                        }

                        var ul_scheduled = $("#tutoring-scheduled");
                        var ul_upcoming = $("#upcoming-schedule");

                        if(realtime){
                            ul_scheduled.html('');
                        }

                        if(reload){
                           ul_upcoming.html('');
                        }
                        $.get(home_url + "/?r=ajax/get_scheduled_day", {day: day}, function (data) {
                            //console.log(data);
                            data = JSON.parse(data);
                            $('.btn-new-request').attr('data-points',data.points);
                            if (data.scheduled.length > 0) {
                                if(!$('#tutoring-scheduled').hasClass('active')){
                                    $('#tutoring-scheduled').addClass('active');
                                }

                                $.each(data.scheduled, function (i, v) {                                   
                                    var height = 0;
                                    var index_st = $('#'+$.trim(v.start_id)).index();
                                    var index_ed = $('#'+$.trim(v.end_id)).index();

                                    $('#'+$.trim(v.start_id)).addClass('start-time');                                    

                                    var top = index_st * 48 + 8 + index_ed; 
                                    height = 1;//index_ed - index_st;

                                    if(v.confirmed == 1 && v.canceled == 0){
                                        var cl = ' accepted';   
                                        var confirmed = 'edf9eb';  
										var icon = 'TimeIcon_Completed.png';										
                                    }else if(v.canceled == 1 && v.confirmed == 0){
                                        var cl = ' canceled';  
                                        var confirmed = 'f4f7f7';  
										var icon = 'TimeIcon_Expired.png';	
                                    }else{
                                        var cl = ' wait';
                                        var confirmed = 'e7feff';
										var icon = 'TimeIcon_Scheduled.png';
                                    }

                                    if(type == 'schedule'){                                        
                                        var h = height * 48;                                        
                                        if(height > 1){
                                            top = top - height + 1;
                                            h = h + height - 1;
                                        }
                                        if(v.end_id == '12_00_am') top = top + 11;
                                        if(top < 0) top = top - 8;
                                        var style = 'style="top: ' + top + 'px; background: #' + confirmed +';height: ' + h + 'px;"';    
                                        var class_type = cl;    
                                        var attr_style = 'top: ' + top + 'px; background: #' + confirmed +';height: ' + h + 'px;';                         
                                    }else{
                                        var style = ' style="top: ' + (i+1) + 'px; height: 78px;"';
                                        var attr_style = 'top: ' + (i+1) + 'px; height:78px;';   
                                        var class_type = cl;//' summary' + cl;
                                    }

                                    if(v.accepted == 1){
                                        var private_subject = 'Confirmed';
                                    }else{
                                        if(v.subject == "")
                                            var private_subject = v.private_subject;
                                        else
                                            var private_subject = v.subject;
                                    }

                                    //var icon = getIconTutoring(v.subject);
                                    if(realtime){
                                        var li = '<li id="view-detail-schedule' + v.id + '" class="view-detail-schedule' + class_type + '" ' + style + ' data-id="' + v.id + '" data-teacher-id="' + v.tutor_id + '" data-student-id="' + v.id_user + '" data-subject="' + v.subject + '" data-private-subject="' + v.private_subject + '" data-message="' + v.short_message + '"  data-note="' + v.note + '" data-student-name="' + v.student_name + '" data-time="' + v.time + '" data-location="' + v.location + '" data-status="' + v.status + '" data-accepted="' + v.accepted + '" data-confirmed="' + v.confirmed + '" data-date="' + v.date_view + '" data-icon="' + icon + '" data-class="' + cl + '" data-total="' + v.total + '" data-time-view="' + v.time_view2 + '" data-create-on="' + v.create_on + '" data-tutor-name="' + v.tutor_name + '" data-stuff="' + v.stuff + '" data-fromtime="' + v.fromtime + '" data-totime="' + v.totime + '" data-day="' + v.day + '" data-total-time="' + v.total_time + '" data-canceled="' + v.canceled + '" data-created="' + v.created + '"><span ' + fl + '>';
                                        li += '<span class="time-scheduled">' + v.time + '</span>';
                                        li += '<span class="subject-scheduled">' + private_subject + '</span>';
                                        li += '</span>' + icon_arrow + '</li>';
                                        ul_scheduled.append(li);
                                    }else{
                                        var ck_class = $('#view-detail-schedule'+v.id).attr('data-class');
                                        if(ck_class != cl || v.accepted == 1){
                                            if($('#view-detail-schedule'+ v.id).hasClass('accepted')){
                                                $('#view-detail-schedule'+ v.id).removeClass('accepted');
                                            }
                                            if($('#view-detail-schedule'+ v.id).hasClass('canceled')){
                                                $('#view-detail-schedule'+ v.id).removeClass('canceled');
                                            }
                                            if($('#view-detail-schedule'+ v.id).hasClass('wait')){
                                                $('#view-detail-schedule'+ v.id).removeClass('wait');
                                            }
                                            $('#view-detail-schedule'+ v.id).addClass($.trim(cl));
                                            $('#view-detail-schedule'+ v.id).removeAttr("style");
                                            $('#view-detail-schedule'+ v.id).attr("style", attr_style);
                                            $('#view-detail-schedule'+ v.id).attr("data-class", cl);
                                            $('#view-detail-schedule'+ v.id).find('span.subject-scheduled').text(private_subject);
                                            $('#view-detail-schedule'+ v.id).attr('data-accepted',v.accepted);
                                        }
                                    }
                                });
                            }else{
                                if($('#tutoring-scheduled').hasClass('active')){
                                    $('#tutoring-scheduled').removeClass('active');
                                }
                            }

                            if (data.confirmed.length > 0) {
                                $.each(data.confirmed, function (i, v) {
                                    if(v.confirmed == 1){
                                        var cl = ' accepted'; 
                                        var icon = 'TimeIcon_Completed.png';                                        
                                    }else if(v.canceled == 1){
                                        var cl = ' canceled';  
                                        var icon = 'TimeIcon_Expired.png';  
                                    }else{
                                        var cl = ' wait';
                                        var icon = 'TimeIcon_Scheduled.png';
                                    }

                                    var li = '<li class="view-detail-schedule ' + cl + '" data-id="' + v.id + '" data-teacher-id="' + v.tutor_id + '" data-student-id="' + v.id_user + '" data-subject="' + v.subject + '" data-private-subject="' + v.private_subject + '" data-message="' + v.short_message + '" data-note="' + v.note + '" data-student-name="' + v.student_name + '" data-time="' + v.time + '" data-location="' + v.location + '" data-status="' + v.status + '" data-confirmed="' + v.confirmed + '" data-date="' + v.date_view + '" data-icon="' + icon + '" data-class="' + cl + '" data-total="' + v.total + '" data-time-view="' + v.time_view2 + '" data-create-on="' + v.create_on + '" data-tutor-name="' + v.tutor_name + '" data-stuff="' + v.stuff + '" data-fromtime="' + v.fromtime + '" data-totime="' + v.totime + '" data-day="' + v.day + '" data-total-time="' + v.total_time + '" data-canceled="' + v.canceled + '" data-created="' + v.created + '"><span class="time-upcoming">' + v.date_view + v.stuff + ' ' + v.time_view2 + '</span><span>' + v.private_subject + '</span></li>';
                                    if(reload){
                                        ul_upcoming.append(li);
                                    }
                                });
                                if(data.confirmed.length < 5){
                                    for(var k = data.confirmed.length; k < 5; k++){
                                        var li = '<li><span class="time-upcoming no-time-schedule">No Schedule</span><span>&nbsp&nbsp</span></li>';
                                        if(reload){
                                            ul_upcoming.append(li);
                                        }
                                    }
                                }
                            }else{
                                for(var k = 0; k < 5; k++){
                                    var li = '<li><span class="time-upcoming no-time-schedule">No Schedule</span><span>&nbsp&nbsp</span></li>';
                                    if(reload){
                                        ul_upcoming.append(li);
                                    }
                                }
                            }
                        });
                    }

                    function getAcceptTutor(day = ''){
                        $.get(home_url + "/?r=ajax/get_accepted_tutor", {day: day}, function (data) {
                            //console.log(data);
                            data = JSON.parse(data);
                            //$('.btn-new-request').attr('data-points',data.points);
                            if (data.accepted.length > 0) {
                                var path = '<?php echo get_template_directory_uri() ?>/library/images/';
                                $('#menu-quick-notification').find('img').attr('src',path + '08_Top_Trigger_NOTIFICATION.png');
                                if(!$('#menu-quick-notification').find('img').hasClass('active')){
                                    $('#menu-quick-notification').find('img').addClass('active');
                                }

                                $.each(data.accepted, function (i, v) {                                   
                                    $('#view-detail-schedule'+ v.id).find('span.subject-scheduled').text(v.subject);
                                });
                            }
                        });
                    }

                    function getQuickNotification(realtime = false){
                        if(realtime){
                            $(".add-list-quicknotifi").html('');
                            $(".add-list-quicknotifi").removeClass('slick-initialized');
                            $(".add-list-quicknotifi").removeClass('slick-slider');
                        }
                        var path = '<?php echo get_template_directory_uri() ?>/library/images/';

                        $.get(home_url + "/?r=ajax/get_quick_notification", {day: ''}, function (data) {
                            //console.log(data);
                            data = JSON.parse(data);
                            if (data.notifications.length > 0) {
                                $('#menu-quick-notification').find('img').attr('src',path + '08_Top_Trigger_NOTIFICATION.png');
                                if(!$('#menu-quick-notification').find('img').hasClass('active')){
                                    $('#menu-quick-notification').find('img').addClass('active');
                                }

                                if(realtime){
                                    $("#open-list-quicknotifi").css("display","block");
                                    if(!$("#open-list-quicknotifi").hasClass('active')){
                                        $("#open-list-quicknotifi").addClass('active');
                                    }

                                    $.each(data.notifications, function (i, v) {  
                                        //var icon = getIconTutoring(v.subject);   
                                        if(v.confirmed == 1){
                                            var cl = ' accepted'; 
                                            var icon = 'TimeIcon_Completed.png'; 
                                            var status_notifi = '';   
                                            var icon_quicknotifi = '';
                                        }else if(v.canceled == 1){
                                            var cl = ' canceled';  
                                            var icon = 'TimeIcon_Expired.png';
                                            var status_notifi = ' has been Expired.';
                                            var icon_quicknotifi = '05_warn_Expired.png';
                                        }else{
                                            var cl = ' wait';
                                            var icon = 'TimeIcon_Scheduled.png';
                                            var status_notifi = ' is coming up soon.';
                                            var icon_quicknotifi = '06_warn_Comingup.png';
                                        }
                                        
                                        if(v.accepted == 1){
                                            var icon_quicknotifi = '13_Confirmed.png';
                                            var status_notifi = ' is Confirmed by Tutor.';
                                        }

                                        if(v.accepted == 2){
                                            var icon_quicknotifi = '04_warn_Canceled.png';
                                            var status_notifi = ' has been Canceled by Tutor.';
                                        }

                                        if(v.subject == "")
                                            var private_subject = v.private_subject;
                                        else
                                            var private_subject = v.subject;

                                        var li = '<div id="quicknotifi' + v.tid + '" class="item">';
                                                li += '<div class="open-list-quicknotifi">';  
                                                    li += '<img class="icon-quicknotifi" src="' + path + icon_quicknotifi + '">';
                                                    li += '<span>';
                                                        li += '<span class="info-quicknotifi">'+ v.date_view + v.stuff +' ' + v.time_view2 + ' - ' + private_subject + ' ' + v.short_message + '</span>';
                                                        li += '<span class="status-quicknotifi">'+ status_notifi +'</span>';
                                                    li += '</span>';
                                                    li += '<button type="button" data-tid="' + v.tid + '" class="close-quicknotifi">';
                                                        li += '<img src="' + path + '12_Tab_Close.png">';
                                                    li += '</button>';
                                                    li += '<button type="button" class="view-detail-quicknotifi' + cl + '" data-id="' + v.id + '" data-teacher-id="' + v.tutor_id + '" data-student-id="' + v.id_user + '" data-subject="' + v.subject + '" data-private-subject="' + v.private_subject + '" data-message="' + v.short_message + '" data-note="' + v.note + '" data-student-name="' + v.student_name + '" data-time="' + v.time + '" data-location="' + v.location + '" data-status="' + v.status + '" data-accepted="' + v.accepted + '" data-confirmed="' + v.confirmed + '" data-date="' + v.date_view + '" data-icon="' + icon + '" data-class="' + cl + '" data-total="' + v.total + '" data-time-view="' + v.time_view2 + '" data-create-on="' + v.create_on + '" data-tutor-name="' + v.tutor_name + '" data-stuff="' + v.stuff + '" data-fromtime="' + v.fromtime + '" data-totime="' + v.totime + '" data-day="' + v.day + '" data-total-time="' + v.total_time + '" data-canceled="' + v.canceled + '" data-created="' + v.created + '">';
                                                        li += '<img src="' + path + '11_Tab_Detail.png">';
                                                    li += '</button>';
                                                li += '</div>';
                                            li += '</div>';
                                        $(".add-list-quicknotifi").append(li);
                                    });

                                    $(".add-list-quicknotifi").not('.slick-initialized').slick(getSliderVerticalSettings(data.notifications.length));
                                }
                            }else{
                                $('#menu-quick-notification').find('img').attr('src',path + '07_Top_Trigger.png');
                                if($('#menu-quick-notification').find('img').hasClass('active')){
                                    $('#menu-quick-notification').find('img').removeClass('active');
                                } 
                            }
                        });
                    }

                    function get_status_schedule(type = 'all'){
                        if($('#tutoring-scheduled').hasClass('active')){
                            $('#tutoring-scheduled').removeClass('active');
                        }

                        $("#table-list-schedule").html('');
						$("#tutoring-scheduled").html('');
                        var tbody_request = $("#table-status-schedule");
                        tbody_request.html('');
                        $.get(home_url + "/?r=ajax/get_request_status", {type: type}, function (data) {
                            //console.log(data);
                            data = JSON.parse(data);
                            if (data.status.length > 0) {
                                $.each(data.status, function (i, v) {  
                                    //var icon = getIconTutoring(v.subject);   
                                    if(v.confirmed == 1){
                                        var cl = ' accepted'; 
										var icon = 'TimeIcon_Completed.png';										
                                    }else if(v.canceled == 1){
                                        var cl = ' canceled';  
										var icon = 'TimeIcon_Expired.png';	
                                    }else{
                                        var cl = ' wait';
										var icon = 'TimeIcon_Scheduled.png';
                                    }  
									
									if(v.accepted == 1){
                                        var private_subject = 'Confirmed';
                                    }else{
                                        if(v.subject == "")
                                            var private_subject = v.private_subject;
                                        else
                                            var private_subject = v.subject;
                                    }
									
                                    var tr = '<tr class="tr-status">';
                                        tr += '<td class="view-status-scheduled' + cl + '" data-id="' + v.id + '" data-teacher-id="' + v.tutor_id + '" data-student-id="' + v.id_user + '" data-subject="' + v.subject + '" data-private-subject="' + v.private_subject + '" data-message="' + v.short_message + '" data-note="' + v.note + '" data-student-name="' + v.student_name + '" data-time="' + v.time + '" data-location="' + v.location + '" data-status="' + v.status + '" data-accepted="' + v.accepted + '" data-confirmed="' + v.confirmed + '" data-date="' + v.date_view + '" data-icon="' + icon + '" data-class="' + cl + '" data-total="' + v.total + '" data-time-view="' + v.time_view2 + '" data-create-on="' + v.create_on + '" data-tutor-name="' + v.tutor_name + '" data-stuff="' + v.stuff + '" data-fromtime="' + v.fromtime + '" data-totime="' + v.totime + '" data-day="' + v.day + '" data-total-time="' + v.total_time + '" data-canceled="' + v.canceled + '" data-created="' + v.created + '"><span>' + private_subject + '</span></td>'; 
                                        tr += '<td class="time-request-status">' + v.date + v.stuff +' '+ v.time_view + '</td>';
                                        tr += '</tr>';
                                    tbody_request.append(tr);
                                });
                            }
                        });
                    }

                    function get_status_request(type = 'all'){
                        var tbody_request = $("#table-status-request");
                        tbody_request.html('');
                        $.get(home_url + "/?r=ajax/get_request_status", {type: type}, function (data) {
                            //console.log(data);
                            data = JSON.parse(data);
                            if (data.status.length > 0) {
                                $.each(data.status, function (i, v) {  
                                    var icon = getIconTutoring(v.subject);   
                                    if(v.confirmed == 1){
                                        var cl = ' accepted';                
                                    }else if(v.canceled == 1){
                                        var cl = ' canceled';                
                                    }else{
                                        var cl = ' wait';
                                    }      
										
									if(v.subject == "")
										var private_subject = v.private_subject;
									else
										var private_subject = v.subject;
									
                                    var tr = '<tr class="tr-status">';
                                        tr += '<td class="view-detail-status' + cl + '" data-id="' + v.id + '" data-subject="' + v.subject + '" data-private-subject="' + v.private_subject + '" data-message="' + v.short_message + '" data-note="' + v.note + '" data-student-name="' + v.student_name + '" data-time="' + v.time + '" data-location="' + v.location + '" data-status="' + v.status + '" data-accepted="' + v.accepted + '" data-confirmed="' + v.confirmed + '" data-date="' + v.date + '" data-icon="' + icon + '" data-class="' + cl + '" data-total="' + v.total + '" data-time-view="' + v.time_view + '" data-create-on="' + v.create_on + '" data-tutor-name="' + v.tutor_name + '" data-stuff="' + v.stuff + '" data-fromtime="' + v.fromtime + '" data-totime="' + v.totime + '" data-day="' + v.day + '" data-total-time="' + v.total_time + '" data-canceled="' + v.canceled + '" data-created="' + v.created + '"><span>' + private_subject + '</span></td>'; 
                                        tr += '<td class="by-tutor">by <span class="view-tutor-detail" data-id="' + v.tutor_id + '">' + v.tutor_name + '</span></td>';
                                        tr += '<td class="time-request-status">' + v.date + v.stuff +' '+ v.time_view + '</td>';
                                        tr += '</tr>';
                                    tbody_request.append(tr);
                                });
                            }
                        });
                    }

                    function getIconTutoring(type = ''){
                        var icon = '';
                        switch($.trim(type)) {
                            case 'English':
                            case 'English Writting':
                            case 'English Conversation':
                                icon = 'icon_Status_Subject.png';
                                break;
                            case 'Math (upto elementary)':
                            case 'Math (any level)':
                            case 'Math':
                                icon = 'icon_MATH.png';
                                break;
                            case 'History':
                                icon = 'Type_History.png';
                                break;
                            case 'Art and Design':
                                icon = 'Type_Art_n_Design.png';
                                break;
                            case 'Science':
                                icon = 'Type_Science.png';
                                break;
                            case 'Music':
                                icon = 'Type_Music.png';
                                break;
                            case 'Other':
                            case 'Others':
                                icon = 'Type_Others.png';
                                break;
                            default:
                                icon = 'Type_Others.png';
                        }
                        return icon;
                    }

                    function get_list_schedule(stype = 'schedule', time = 0){
                        var tbody_schedule = $("#table-list-schedule");
                        var date = $('#menu-schedule-btn').attr('data-day');
                        tbody_schedule.html("");
                        var type = 'am';
                        var formattedDate = $('#today-tutor').val();
                        var minute = 0;

                        if(formattedDate == date && time == 0){
                            time = $('#time-clock').attr('data-hour');
                        }

                        if(formattedDate == date){
                            minute = $('#time-clock').attr('data-minute');
                        }

                        var html = '<option value="0">Select Time</option>';
                        for (var i = time; i < 24; i++) {                            
                            var id = i;
                            if (i > 11){
                                var j = i - 12;                                    
                                type = 'pm'; 
                            }else{
                                var j = i;
                            }

                            if(j == 0) id = j = 12;

                            var kl = (parseInt(id) + 1);
                            if(kl > 12){
                                var ks  = kl - 12;
                            }else{
                                var ks = kl;
                            }

                            if(ks < 10) 
                                var kll = '0'+ks;
                            else
                                var kll = ks;

                            if(j < 10) 
                                var jk = '0'+j;
                            else
                                var jk = j;

                            html += '<option data-time="'+j+':00:'+type+' ~ '+j+':30:'+type+'" data-time-view="'+j+':00'+type+'-'+j+':30'+type+'" value="'+j+':00'+type+'">'+jk+':00 '+type+' - '+jk+':30 '+type+'</option>';
                            html += '<option data-time="'+j+':30:'+type+' ~ '+ks+':00:'+type+'" data-time-view="'+j+':30'+type+'-'+ks+':00'+type+'" value="'+j+':30'+type+'">'+jk+':30 '+type+' - '+kll+':00 '+type+'</option>';
                            
                            if(stype == 'summary'){    
                                var tr_am = '';
                            }else{
                                var tr_am = '<tr id="' + id + '_00_' + type + '" class="schedule-time" data-index="' + i + '" data-half="0">';
                                tr_am += '<td class="time-type">' + j + type + '</td>'; 
                                tr_am += '<td><button class="btn-new-request" data-fromtime="' + id + ':00:' + type + '" data-totime="' + id + ':30:' + type + '" data-day="' + date + '" data-time="' + id + ':00' + type + ' - ' + id + ':30' + type + '" data-id="' + id + '_00_' + type + '" data-index="' + i + '" data-half="0"><img src="<?php echo get_template_directory_uri(); ?>/library/images/TimeIcon_No-Scheduled.png">No Schedule</button></td>';
                                tr_am += '</tr>';

                                var tr_half = '<tr id="' + id + '_30_' + type + '"  class="schedule-time" data-index="' + i + '" data-half="30">';
                                tr_half += '<td class="time-type"></td>'; 
                                tr_half += '<td class="half-time"><button class="btn-new-request" data-fromtime="' + id + ':30:' + type + '" data-totime="' + (id + 1) + ':00:' + type + '" data-day="' + date + '" data-time="' + id + ':30' + type + ' - ' + (id + 1) + ':30' + type + '" data-id="' + id + '_30_' + type + '" data-index="' + i + '" data-half="30"><img src="<?php echo get_template_directory_uri(); ?>/library/images/TimeIcon_No-Scheduled.png">No Schedule</button></td>';
                                tr_half += '</tr>';
                            }
                            if(time == i && minute > 0 && minute < 30){
                                //tbody_schedule.append(tr_am);
                                tbody_schedule.append(tr_am);
                                tbody_schedule.append(tr_half);
                            }else if(time == i && minute > 30){
                                tbody_schedule.append(tr_half);
                            }else{
                                tbody_schedule.append(tr_am);
                                tbody_schedule.append(tr_half);
                            }
                        }
                        $('#select-available-time').html(html).data("selectBox-selectBoxIt").refresh();

                        if(stype == 'summary'){ 
                            var tr_24 = '';
                                tbody_schedule.append(tr_24);
                        }else{
                            var tr_24 = '<tr id="24_00_pm">';
                                tr_24 += '<td class="time-type"></td>'; 
                                tr_24 += '<td></td>';
                                tr_24 += '</tr>';
                                tbody_schedule.append(tr_24);
                        }
                    }

                    function get_resume(id = '', type = 'resume', ptype = 'list', table="table-list-tutor", animate="", time_view = ""){
                        var tbody_request = $("#"+table);
                        var uid = '<?php if ($is_user_logged_in) echo $current_user->ID; else echo 0; ?>';
                        var day = $('#today-tutor').val();
                        var hour = $('#mytime-clock').attr('data-hour');
                        var minute = $('#mytime-clock').attr('data-minute');
                        var time_type = $('#mytime-clock').attr('data-type');
                        var today = new Date(day.replace("-", ","));
                        var weekday = new Array(7);
                            weekday[0] =  "Sun";
                            weekday[1] = "Mon";
                            weekday[2] = "Tue";
                            weekday[3] = "Wed";
                            weekday[4] = "Thur";
                            weekday[5] = "Fri";
                            weekday[6] = "Sat";                                
                        var n = weekday[today.getDay()];
                        var month_text = getMonthtoText(today.getMonth()+1);

                        if(hour > 12) hour = parseInt(hour) - 12;
                        if(hour == 0) hour = 12;
                        
                        if(minute > 29){
                            minute = 30;
                            var hour1 = parseInt(hour) + 1;
                            if(hour1 > 12) hour1 = hour1 - 12;
                            var totime = hour1+':00'+time_type;
                            var time_sc2 = hour1+':00'+':'+time_type;
                            var time_v2 = hour1+':00'+time_type;
                        }
                        else{
                            minute = '00';
                            var totime = hour+':30'+time_type;
                            var time_sc2 = hour+':30'+':'+time_type;
                        }
                        
                        var time = hour+':'+minute+time_type +'-'+ totime;
                        var time_sc1 = hour+':'+minute+':'+time_type;
                        var time_v1 = hour+':'+minute+time_type;
                        if(time_view == ""){
                            $('#selected-date').text(month_text + ' ' + today.getDate() + ' (' + n + ')' + time);
                            $('#btn-schedule-now').attr("data-day",day);
                            $('#btn-schedule-now').attr("data-time",time_sc1 + ' ~ ' + time_sc2);
                            $('#btn-schedule-now').attr("data-time-view",time_v1 + '-' + time_v2);
                        }
                        $('.close-detail').attr('data-ptype',ptype);
                        $('.close-detail').attr('data-table',table);

                        $('#table-detail-tutor').css('display','table');
                        $('#btn-schedule-now').attr('data-id',id);
                        $('#btn-schedule-now').attr('data-tutor-id','');
                        $('#btn-schedule-now').attr("data-total-time",30);
                        $('#btn-schedule-now').attr("data-total",15);
                        $('#btn-schedule-now').attr('data-ptype',ptype);
                        $('#btn-schedule-now').attr('data-table',table);
                        tbody_request.html("");
                        $(".slide-resume").css('visibility','visible');

                        if(type == 'review'){
							updateReview(id);
                            $('#tr-tutor' + id).css('display','block');
                            $('#tr-info' + id).css('display','none');
                            $('#tr-review' + id).css('display','block');
                            $('.writting-review').css("display","none");
                            if($('.writting-review').hasClass("active")){
                                $('.writting-review').removeClass("active");
                            }

                            $('#view-review' + id).attr('src','<?php echo get_template_directory_uri(); ?>/library/images/iconM_Review_ON-O.png');
                            $('#view-resume' + id).attr('src','<?php echo get_template_directory_uri(); ?>/library/images/iconM_Resume_OFF.png');
                            $('#view-write-review' + id).attr('src','<?php echo get_template_directory_uri(); ?>/library/images/iconM_Write_Review_OFF.png');
                        }else if(type == 'write_review'){
                            $('#tr-tutor' + id).css('display','block');
                            $('#tr-info' + id).css('display','none');
                            $('#tr-review' + id).css('display','none');
                            if(!$('.writting-review').hasClass("active")){
                                $('.writting-review').addClass("active");
                                $('.writting-review').css("display","block");
                            }

                            $('#view-review' + id).attr('src','<?php echo get_template_directory_uri(); ?>/library/images/iconM_Review_OFF.png');
                            $('#view-resume' + id).attr('src','<?php echo get_template_directory_uri(); ?>/library/images/iconM_Resume_OFF.png');
                            $('#view-write-review' + id).attr('src','<?php echo get_template_directory_uri(); ?>/library/images/iconM_Write_Review_ON-O.png');
                        }else{
                            if($('.writting-review').hasClass("active")){
                                $('.writting-review').removeClass("active");
                            }  
                            $('.writting-review').css("display","none");
                            $('#tr-tutor' + id).css('display','block');
                            $('#tr-info' + id).css('display','block');
                            $('#tr-review' + id).css('display','none');

                            $('#view-review' + id).attr('src','<?php echo get_template_directory_uri(); ?>/library/images/iconM_Review_OFF.png');
                            $('#view-resume' + id).attr('src','<?php echo get_template_directory_uri(); ?>/library/images/iconM_Resume_ON-O.png');
                            $('#view-write-review' + id).attr('src','<?php echo get_template_directory_uri(); ?>/library/images/iconM_Write_Review_OFF.png');
                        }  
                    }

                    function getTextWidth(text, font) {
                        var canvas = getTextWidth.canvas ||
                            (getTextWidth.canvas = document.createElement("canvas"));
                        var context = canvas.getContext("2d");
                        context.font = font;
                        var metrics = context.measureText(text);
                        return metrics.width;
                    };

                    function openNav() {
                        var viewport = getViewport();  
                        if(viewport.width < 650){
                            $("#menu-account-nav").removeClass("open");
                            $("#menu-account-nav").removeClass("close");
                            $("#menu-account-nav").addClass("open");
                        }else{
                            if($('body').hasClass('open-myschedule')){
                                $("#menu-account-nav").removeClass("open");
                                $("#menu-account-nav").removeClass("close");
                                $("#menu-account-nav").addClass("open");
                            }else{
                                $("#mySidenav").removeClass("open");
                                $("#mySidenav").removeClass("close");
                                $("#mySidenav").addClass("open");
                            }
                        }
                    }

                    function closeNav() {
                        var viewport = getViewport();  
                        if(viewport.width < 650){
                            $("#menu-account-nav").removeClass("open");
                            $("#menu-account-nav").removeClass("close");
                            $("#menu-account-nav").addClass("close");                                
                        }else{                 
                            if($('body').hasClass('open-myschedule')){
                                $("#menu-account-nav").removeClass("open");
                                $("#menu-account-nav").removeClass("close");
                                $("#menu-account-nav").addClass("close");
                            }else{
                                $("#mySidenav").removeClass("open");
                                $("#mySidenav").removeClass("close");
                                $("#mySidenav").addClass("close");
                            }         
                        }
                        $('#menu-left-myaccount li').css("margin-top", "0px");
                    }

                    function isValidEmail(emailText) {
                        var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
                        return pattern.test(emailText);
                    }

                    function get_update_info(){
                        $chk_teacher = 0;
                        $.get(home_url + "/?r=ajax/get_user_info", {userid: ''}, function (data) {
                            //console.log(data);
                            data = JSON.parse(data);
                            /*if(data.chk_teacher == 1)
                                $('#tutor-regis-update').css("display","block");
                            else
                                $('#tutor-regis-update').css("display","none");*/

                            $('#tutor-regis-update').css("display","none");
                            $('#chk-tutor-teacher').val($chk_teacher);
                            $('#chk-user-gender').val(data.gender);
                            $('#update_username').val(data.user_email);
                            $('#update_password').val(data.user_password);
                            $('#update_confirmpass').val(data.user_password);
                            $('#update_first_name').val(data.first_name);
                            $('#update_last_name').val(data.last_name);
                            $('#update_birth_y').val(data.birth_y);
                            
                            $('#mobile-number-update').val(data.mobile_number);
                            $('#last-school-update').val(data.last_school);
                            $('#previous-school-update').val(data.previous_school);
                            $('#skype-update').val(data.skype_id);
                            $('#profession-update').val(data.user_profession);

                            $('#description-update').val(data.subject_description);
                            $('#school-name-update').val(data.school_name);
                            $('#teaching-link-update').val(data.teaching_link);

                            $('#subject-update').val(data.teaching_subject);
                            $('#student-link-update').val(data.student_link);
                            $('#years-update').val(data.user_years);
                            $('#school-attend-update').val(data.school_attend);
                            $('#gpa-update').val(data.user_gpa);
                            $('#major-update').val(data.user_major);

                            $('#school-name1-update').val(data.school_name1);
                            $('#school-name2-update').val(data.school_name2);
                            $('#school-link1-update').val(data.school_link1);
                            $('#school-link2-update').val(data.school_link2);
                            $('#any-other-update').val(data.any_other);
                            $('#profile-value').val(data.profile_value); 
                            $("#user-upload-img").attr('src',data.user_avatar);

                            tinyMCE.activeEditor.setContent(data.desc_tell_me);

                            $("#update-time-zone").selectBoxIt('selectOption',data.time_zone_index.toString()).data("selectBox-selectBoxIt");
                            $("#update-time-zone").data("selectBox-selectBoxIt").refresh();

                            if(data.birth_m != ''){
                                $("#update_birth_m").selectBoxIt('selectOption',data.birth_m.toString()).data("selectBox-selectBoxIt");
                                $("#update_birth_m").data("selectBox-selectBoxIt").refresh();
                            }
                            if(data.birth_d != ''){
                                $("#update_birth_d").selectBoxIt('selectOption',data.birth_d.toString()).data("selectBox-selectBoxIt");
                                $("#update_birth_d").data("selectBox-selectBoxIt").refresh();
                            }
                            if(data.user_grade != ''){
                                $("#grade-update").selectBoxIt('selectOption',data.user_grade.toString()).data("selectBox-selectBoxIt");
                                $("#grade-update").data("selectBox-selectBoxIt").refresh();
                            }

                            var viewport = getViewport();
                            if(viewport.width < 650){
                                if(data.gender != ''){
                                    $('#update_gender').html('');
                                    $('#update_gender').html('<input readonly="" type="text" name="update_birth_g_mb" class="form-control" value="' + data.gender + '" id="update_birth_g_mb">');
                                }
                            }else{
                                if(data.gender != ''){
                                    $('#gender_up').html('');
                                    $('#gender_up').html('<input type="text" class="form-control" name="update_birth_g_pc" value="' + data.gender + '" id="update_birth_g_pc" readonly="">');
                                }
                            }
                            $('input[name="update-cb-lang"]').each(function () {
                                if( $.inArray(this.value, data.cb_lang) >= 0 ) {
                                    $(this).attr("checked",true);
                                }
                            });
                            $('input[name="subject_type_update"]').each(function () {
                                if( $.inArray(this.value, data.subject_type) >= 0 ) {
                                    $(this).attr("checked",true);
                                }
                            });
                        });
                    }

                    function get_profile_info(){
                        $.get(home_url + "/?r=ajax/get_user_profile", {userid: ''}, function (data) {
                            //console.log(data);
                            data = JSON.parse(data);                                

                            $('#profile-user-avatar').attr("src",data.user_avatar);
                            $('#profile-my-name').text(data.user_name);
                            $('#profile-point-balance').text(data.user_points);
                            $('#profile-point-earned').text(data.user_earned);
                            $('#profile-english-writting').text(data.english_writting);
                            $('#profile-english-conversation').text(data.english_conversation);
                            $('#profile-math-up').text(data.math_up);
                            $('#profile-math-conversation').text(data.math_conversation);
                            $('#profile-user-email').text(data.uemail);
                            $('#profile-date-birth').text(data.dbirth);
                            $('#profile-language').text(data.langs);
                            $('#profile-mobile-phone').text(data.user_mobile_number);
                            $('#profile-last-attended').text(data.user_last_school);
                            $('#profile-last-tought').text(data.user_previous_school);
                            $('#profile-skype-id').text(data.user_skype_id);
                            $('#profile-profession').text(data.u_profession);
                            $('#chk-user-gender').val(data.gender);
                        });
                    }

                    function initDateTimePicker(timezone_name = 'Europe/London', timezone_index = 18, time_zone = 0, location_time = 'London', type = 'schedule'){
                        var ptype = $('#custom-timezone').attr("data-type");
                        var pday = $('#custom-timezone').attr("data-day");
                        if($('.datepicker-days').find('.day').hasClass('active')){
                            var active_day = $('.datepicker-days').find('.active').attr('data-day');
                        }else{
                            if(ptype != '' && pday != ''){
                                var active_day = pday;
                            }else{
                                var active_day = '';
                            }
                        }

                        $('#request-time-zone').attr('data-index',timezone_index);
                        $('#request-time-zone').attr('data-value',time_zone);
                        $('#request-time-zone').val(location_time);

                        if(type != 'schedule'){
                            $("#select-timezone").selectBoxIt('selectOption',timezone_index.toString()).data("selectBox-selectBoxIt");
                            $("#select-timezone").data("selectBox-selectBoxIt").refresh();
                        }

                        var date_utc = moment.utc().format('YYYY-MM-DD HH:mm:ss');
                        var dpDate = moment.tz(timezone_name).format('YYYY-MM-DD HH:mm:ss');
                        var mDate = moment.utc(dpDate).format('YYYY-MM-DD');
                            
                        $('#today-tutor').val(mDate);
                        $("#select-available-month").selectBoxIt('selectOption',moment.utc(dpDate).format('MM')).data("selectBox-selectBoxIt");
                        $("#select-available-month").data("selectBox-selectBoxIt").refresh();

                        $("#select-available-day").selectBoxIt('selectOption',moment.utc(dpDate).format('DD')).data("selectBox-selectBoxIt");
                        $("#select-available-day").data("selectBox-selectBoxIt").refresh();
                        
                        $("#available_year").val(moment.utc(dpDate).format('YYYY'));
                        
                        if(moment.tz(timezone_name).format('m') < 30)
                            var available_time = moment.tz(timezone_name).format('h')+':00'+moment.tz(timezone_name).format('a');
                        else
                            var available_time = moment.tz(timezone_name).format('h')+':30'+moment.tz(timezone_name).format('a');
                        $("#select-available-time").selectBoxIt('selectOption',available_time.toString()).data("selectBox-selectBoxIt");
                        $("#select-available-time").data("selectBox-selectBoxIt").refresh();

                        //$('#sandbox-container-tutor').data("DateTimePicker").clear();
                        var picker = $('#sandbox-container-tutor').data("DateTimePicker");
                        if (picker) {
                            picker.clear();
                            picker.destroy();
                        }
                        $('#sandbox-container-tutor').datetimepicker({
                            inline: true,
                            sideBySide: true,
                            viewMode: 'days',
                            timeZone: timezone_name,
                            defaultDate: new Date(mDate),
                            format: "MM/DD/YYYY",
                            icons: {
                                previous: '<img src="<?php echo get_template_directory_uri(); ?>/library/images/Chalendar_icon_Left.png" height="15">',
                                next: '<img src="<?php echo get_template_directory_uri(); ?>/library/images/Chalendar_icon_Right.png" height="15">'
                            }
                        });

                        $.post(home_url + "/?r=ajax/get_tutoring_date_active", {                                   
                            timezone: time_zone,
                            name: timezone_name,
                            index: timezone_index
                        }, function (data) {
                            $('#active-day-tutor').val(data);
                            initCalendar('update', data);
                        });

                        var weekday = new Array(7);
                            weekday[0] =  "Sun";
                            weekday[1] = "Mon";
                            weekday[2] = "Tue";
                            weekday[3] = "Wed";
                            weekday[4] = "Thur";
                            weekday[5] = "Fri";
                            weekday[6] = "Sat";
						/*
                        if(ptype == 'view'){
                            var created = $('#custom-timezone').attr("data-created");
                           
                            var ctDate = moment.tz(created,timezone_name).format('YYYY-MM-DD HH:mm:ss');
                            var ctDay = new Date(ctDate);
                            var month_ct = getMonthtoText(ctDay.getMonth()+1);
                            var text = month_ct + ctDay.getDate() + ', ' + ctDay.getFullYear() + '(' + ctDay.getHours() + ':' + ct.getMinutes() + ')';
                            $('.more-request').find('.create-time').text(text);
                        }*/

                        if(active_day == ''){
                            var day = new Date(dpDate);                                
                            var n = weekday[day.getDay()];
                            var month_text = getMonthtoText(day.getMonth()+1);

                            $('.current-day').text(month_text + ' ' + day.getDate());
                            $('.stuff-day').text(' (' + n + ')');

                            $('.current-request-day').text(month_text+ ' ' + day.getDate() +', '+ day.getFullYear());
                            $('.stuff-request-day').text(' (' + n + ') ');

                            $('#request-time-zone').val(location_time);

                            var dd_s = day.getDate();
                            var mm_s = day.getMonth()+1; //January is 0!
                            var yyyy_s = day.getFullYear();
                            if(dd_s < 10) {
                              dd_s = "0"+dd_s;
                            }
                            if(mm_s < 10) {
                              mm_s = "0"+mm_s;
                            }

                            var prev = new Date(dpDate);
                            prev.setDate(prev.getDate() - 1);

                            var dd = prev.getDate();
                            var mm = prev.getMonth()+1; //January is 0!
                            var yyyy = prev.getFullYear();
                            if(dd < 10) {
                              dd = "0"+dd;
                            }
                            if(mm < 10) {
                              mm = "0"+mm;
                            }

                            var next = new Date(dpDate);
                            next.setDate(next.getDate() + 1);
                            var dd_n = next.getDate();
                            var mm_n = next.getMonth()+1; //January is 0!
                            var yyyy_n = next.getFullYear();
                            if(dd_n < 10) {
                              dd_n = "0"+dd_n;
                            }
                            if(mm_n < 10) {
                              mm_n = "0"+mm_n;
                            }

                            $("#menu-schedule-btn").attr('data-day',yyyy_s+'-'+mm_s+'-'+dd_s);
                            $('#btn-sent-request').attr('data-day',yyyy_s+'-'+mm_s+'-'+dd_s);
                            $(".schedule-left-btn").attr('data-day',yyyy+'-'+mm+'-'+dd);
                            $(".schedule-right-btn").attr('data-day',yyyy_n+'-'+mm_n+'-'+dd_n);
							
							$('.header-schedule').removeClass('active');
							$('#list-schedule-status').css("display","none");
							$('#table-status-schedule').html('');
							$("#open-menu-schedule").css("display","none");
							$(".main-view-status").css("display","none");

							if($("#body-my-scheduled").hasClass('status-schedule')){
								$("#body-my-scheduled").removeClass("status-schedule");
							}

                            get_list_schedule('schedule');
                            get_scheduled_day(yyyy_s+'-'+mm_s+'-'+dd_s,'schedule', true);

                            console.log('Timzone UTC: '+date_utc+' | to '+timezone_name+ ' ' +time_zone +': '+dpDate+'->'+mDate+'|'+yyyy_s+'-'+mm_s+'-'+dd_s);
                        }else{
                            var gday = $('#menu-schedule-btn').attr('data-day');
                            var formattedDate = gday + ' ' + moment.utc().format('HH:mm:ss');
                           
                            var dmDate = moment.tz(formattedDate,timezone_name).format('YYYY-MM-DD HH:mm:ss');
                            var mmDay = new Date(dmDate);
                            var gn = weekday[mmDay.getDay()];
                            var gmonth_text = getMonthtoText(mmDay.getMonth()+1);

                            $('.current-day').text(gmonth_text + ' ' + mmDay.getDate());
                            $('.stuff-day').text(' (' + gn + ')');

                            $('.current-view-day').text(gmonth_text+ ' ' + mmDay.getDate() +', '+mmDay.getFullYear());
                            $('.stuff-view-day').text(' (' + gn + ') ');

                            $('.current-request-day').text(gmonth_text+ ' ' + mmDay.getDate() +', '+mmDay.getFullYear());
                            $('.stuff-request-day').text(' (' + gn + ') ');

                            $('#request-time-zone').val(location_time);

                            var dd_s = mmDay.getDate();
                            var mm_s = mmDay.getMonth()+1; //January is 0!
                            var yyyy_s = mmDay.getFullYear();
                            if(dd_s < 10) {
                              dd_s = "0"+dd_s;
                            }
                            if(mm_s < 10) {
                              mm_s = "0"+mm_s;
                            }

                            var active_mday = yyyy_s+'-'+mm_s+'-'+dd_s;

                            $('.datepicker-days td.day').each(function () {
                                var full_date = $(this).attr('data-day');
                                var st = full_date.split("/");
                                var formattedDate = st[2] + "-" + st[0] + "-" + st[1];
                                if(active_mday == formattedDate){
                                    $(this).addClass('active');
                                }
                            });

                            var prev = new Date(dmDate);
                            prev.setDate(prev.getDate() - 1);

                            var dd = prev.getDate();
                            var mm = prev.getMonth()+1; //January is 0!
                            var yyyy = prev.getFullYear();
                            if(dd < 10) {
                              dd = "0"+dd;
                            }
                            if(mm < 10) {
                              mm = "0"+mm;
                            }

                            var next = new Date(dmDate);
                            next.setDate(next.getDate() + 1);
                            var dd_n = next.getDate();
                            var mm_n = next.getMonth()+1; //January is 0!
                            var yyyy_n = next.getFullYear();
                            if(dd_n < 10) {
                              dd_n = "0"+dd_n;
                            }
                            if(mm_n < 10) {
                              mm_n = "0"+mm_n;
                            }

                            $("#menu-schedule-btn").attr('data-day',yyyy_s+'-'+mm_s+'-'+dd_s);
                            $('#btn-sent-request').attr('data-day',yyyy_s+'-'+mm_s+'-'+dd_s);
                            $(".schedule-left-btn").attr('data-day',yyyy+'-'+mm+'-'+dd);
                            $(".schedule-right-btn").attr('data-day',yyyy_n+'-'+mm_n+'-'+dd_n);
							
							$('.header-schedule').removeClass('active');
							$('#list-schedule-status').css("display","none");
							$('#table-status-schedule').html('');
							$("#open-menu-schedule").css("display","none");
							$(".main-view-status").css("display","none");

							if($("#body-my-scheduled").hasClass('status-schedule')){
								$("#body-my-scheduled").removeClass("status-schedule");
							}

                            get_list_schedule('schedule');
                            get_scheduled_day(yyyy_s+'-'+mm_s+'-'+dd_s,'schedule', true);

                            console.log('Timzone UTC: '+date_utc+' | to '+timezone_name+ ' ' +time_zone +': '+dmDate+'->'+mDate+'|'+yyyy_s+'-'+mm_s+'-'+dd_s);
                        }
                        //console.log(yyyy_s+'-'+mm_s+'-'+dd_s);
                    }

                    function getSliderSettings(width, countSlide = 0){
                        if(countSlide == 0)
                            var count = <?php if(count($schedules) == 0) echo 0; else echo 1;?>;
                        else
                            var count = 1;
                        if(width){
                            return {
                                variableWidth: true,
                                arrows: true,
                                slidesToShow: count,
                                slidesToScroll: 1,
                                dots: false,
                                infinite: false,
                                prevArrow:"<img class='a-left slick-prev slick-my-schedule' src='<?php echo get_template_directory_uri(); ?>/library/images/icon_Arrow_Left.png'>",
                                nextArrow:"<img class='a-right slick-next slick-my-schedule' src='<?php echo get_template_directory_uri(); ?>/library/images/icon_Arrow_Right.png'>"
                            }
                        }else{
                            return {
                                arrows: true,
                                slidesToShow: 1,
                                slidesToScroll: 1,
                                dots: false,
                                infinite: false,
                                prevArrow:"<img class='a-left slick-prev' src='<?php echo get_template_directory_uri(); ?>/library/images/icon_Arrow_Left.png'>",
                                nextArrow:"<img class='a-right slick-next' src='<?php echo get_template_directory_uri(); ?>/library/images/icon_Arrow_Right.png'>"
                            } 
                        }
                    }

                    function getSliderVerticalSettings(countSlide = 0){
                        if(countSlide < 6)
                            var count = countSlide;
                        else
                            var count = 6;
                        return {
                            arrows: false,
                            vertical: true,
                            slidesToShow: count,
                            slidesToScroll: count,
                            dots: false,
                            infinite: false,
                            verticalSwiping: true,
                            prevArrow:"<img class='a-left slick-prev' src='<?php echo get_template_directory_uri(); ?>/library/images/icon_Arrow_Left.png'>",
                            nextArrow:"<img class='a-right slick-next' src='<?php echo get_template_directory_uri(); ?>/library/images/icon_Arrow_Right.png'>"
                        } 
                    }

                    function get_tutor_user(type = 'list', table = 'table-list-tutor', retype = 'tutor', search = '', time_zone = '', description = '', subject_type = '', time = '', date = '', type_search = '', stime = '', time_view = '', available = '', subject_name = ''){
                        var tbody_request = $("#"+table);
                        var uid = '<?php if ($is_user_logged_in) echo $current_user->ID; else echo 0; ?>';
                        tbody_request.html("");
                        $('#table-detail-tutor').css('display','none');
                        $(".slide-resume").html('');
                        $(".slide-resume").removeClass('slick-initialized');
                        $(".slide-resume").removeClass('slick-slider');
                        $(".slide-resume").css('visibility','hidden');
						if(subject_name != ''){
							$('#selected-subject').text(subject_name);
							$('#btn-schedule-now').attr('data-subject',subject_name);
						}

                        var post_data = {type:type, search:search, time_zone:time_zone, description:description, subject_type:subject_type, time:time, date:date, type_search: type_search, available: available};
                        console.log(post_data);
                        $.get(home_url + "/?r=ajax/get_users_tutor", post_data, function (data) {
                            console.log(data);
                            data = JSON.parse(data);
                            if (data.users.length > 0) {
                                if(type == 'fromclass' && stime != ''){
                                    var time_zone = $('#user-time-zone :selected').attr("data-value");
                                    var today = new Date(date.replace("-", ","));                            
                                    var weekday = new Array(7);
                                        weekday[0] =  "Sun";
                                        weekday[1] = "Mon";
                                        weekday[2] = "Tue";
                                        weekday[3] = "Wed";
                                        weekday[4] = "Thur";
                                        weekday[5] = "Fri";
                                        weekday[6] = "Sat";                                
                                    var n = weekday[today.getDay()];
                                    var month_text = getMonthtoText(today.getMonth()+1);
                                    $('#selected-date').text(month_text + ' ' + today.getDate() +'('+n+')'+time_view);
                                }
                                $.each(data.users, function (i, v) {  
                                    var img_star = '';
                                    var max_star = 5;
                                    arr_tutor.push(v.ID);

                                    if(v.star > 0){
                                        for(var l = 0; l < v.star; l++){
                                            img_star += '<img src="<?php echo get_template_directory_uri(); ?>/library/images/icon_Rating_ON.png" alt="">';
                                        }
                                        max_star = max_star - v.star;
                                    }
                                    for(var m = 0; m < max_star; m++){
                                        img_star += '<img src="<?php echo get_template_directory_uri(); ?>/library/images/icon_Rating_OFF.png" alt="">';
                                        
                                    } 
                                    if(type == 'fromclass'){
                                        $('#btn-available-search').attr("disabled", false);
                                    }   
                                    if( $.inArray(uid, v.user_favorites) >= 0 ) {
                                        var img_bookmark = 'icon_Favorite_BookMark.png';
                                    }else{
                                        var img_bookmark = 'Icon_Favorite_Unselected.png';
                                    }                       
                                      
                                    var tr = '<tr class="tr-tutor btn-resume" data-type="' + type + '" data-table="' + table + '" data-id="' + v.ID + '" data-time="'+stime+'" data-time-view="'+time_view+'" data-day="'+date+'" data-slide-index="' + i + '" data-subject="'+ subject_name +'" data-price-tutoring="' + v.price_tutoring + '" name="resume">'; 
                                    if(retype == 'findtutor'){
                                        tr+='<td><input type="radio" class="radio_buttons_tutor class_cb_search option-input-2 radio" value="' + v.ID + '" data-id="' + v.ID + '" data-name="' + v.display_name + '" name="choose_tutor"></td>';
                                    }                          
                                    
                                     tr+='<td class="avatar-tutor"><img src="' + v.user_avatar + '" alt="' + v.display_name + '"/><img id="book-mark' + v.ID + '" class="find-card-img-bookmark find-card-bookmarked" data-id="' + v.ID + '" src="<?php echo get_template_directory_uri(); ?>/library/images/' + img_bookmark + '" alt=""></td>'; 

                                    var subject = "";
                                    v.subject_type.forEach(function(item, index){
                                        if(index == v.subject_type.length - 1){
                                            subject += item;
                                        }else{
                                            subject += item + ", ";
                                        }
                                    })
                                    //DUMMY DATA
                                    tr+=`<td> <div class="row"><div class="col-sm-1 col-md-1" style="margin-bottom="15px";><img src="<?php echo get_template_directory_uri(); ?>/library/images/icon_Group.png" alt="" style="height:12px"></div><div class="col-sm-11 col-md-11"><p class="find-card-sibject"> ${subject}</p></div></div>
                                        <div><b>${v.display_name}</b></div><div><p class="find-card-marketing-tag">
                                        Lorem ipsum dolor sit amet</p></div><div><p class="icon-star">${img_star}<span class="find-card-star-count">(${v.cnt})<span>
                                        <span class="find-card-more" data-type="${type}" data-table="${table}" data-id="${v.ID}" data-time="${stime}" data-time-view="${time_view}" data-day="${date}" data-slide-index="${i}" data-subject="${subject_name}" data-price-tutoring="${v.price_tutoring}" name="resume"><u>+Read more</u></span></p></div>
                                    </td>`;
                                    tr+=`<td><div>
                                    <p style="text-align:center;"><span class="find-card-price">$${v.price_tutoring}</span><span class="find-card-time">&nbsp;/&nbsp;30 min</span></p></div>
                                    <div><button class="find-card-select-btn btn orange"  data-type="${type}" data-table="${table}" data-id="${v.ID}" data-time="${stime}" data-time-view="${time_view}" data-day="${date}" data-slide-index="${i}" data-subject="${subject_name}" data-price-tutoring="${v.price_tutoring}" name="resume">Select This Tutor</button></div>
                                    <div class="find-card-send-message"><img class="find-card-envelope" src="https://www.dropbox.com/s/lqa74sc4w9gtv3o/envelope-solid.svg?raw=1" style="width:13px"> Send a message</p></div>
                                    </td>`;
                                    tr+='</tr>';
                                    tbody_request.append(tr);  

                                    //Slide resume tutor
                                    var attr_data = 'data-id="" data-userid="" data-star="" data-subject="" data-message="" data-ptype="' + type + '"';
                                    if (v.reviews.length > 0) {
                                        $.each(v.reviews, function (index, value) {
                                            if(value.userid == uid){
                                                attr_data = 'data-id="' + value.id + '" data-userid="' + value.userid + '" data-star="' + value.star + '" data-subject="' + value.subject + '" data-message="' + value.message + '" data-ptype="' + type + '"';
                                            }
                                        });
                                    }

                                    var view = '<img src="<?php echo get_template_directory_uri(); ?>/library/images/iconM_Resume_ON-O.png" alt="" id="view-resume' + v.ID + '" class="btn-view view-resume" data-id="' + v.ID + '" data-ptype="' + type + '" data-table="' + table + '"><img id="view-review' + v.ID + '" class="btn-view view-review" data-id="' + v.ID + '" data-ptype="' + type + '" data-table="' + table + '" src="<?php echo get_template_directory_uri(); ?>/library/images/iconM_Review_OFF.png" alt="">';
                                    if(uid != v.ID){
                                        view += '<img id="view-write-review' + v.ID + '" class="btn-view view-write-review" data-review-id="' + v.ID + '" '+ attr_data +' data-table="' + table + '" src="<?php echo get_template_directory_uri(); ?>/library/images/iconM_Write_Review_OFF.png" alt="">';
                                    }
                                    var title_resum = 'RESUME';

                                    view += '<button type="button" data-id="' + v.ID + '" data-subject="' + v.user_subject + '"  data-subject-choose="'+ subject_name +'" data-name="' + v.display_name + '" data-ptype="' + type + '" data-time="'+stime+'" data-time-view="'+time_view+'" data-day="'+date+'" data-price-tutoring="' + v.price_tutoring + '" class="btn-orange2 nopadding-r border-btn" id="btn-select-tutor"><span>Select</span></button>';
                                    var div = '<div class="item">';
                                            div += '<div class="tr-tutor resume clearfix" id="tr-tutor' + v.ID + '">';
                                                div +='<div class="avatar-tutor"><img src="' + v.user_avatar + '" alt="' + v.display_name + '"/></div>'; 
                                                div +='<div class="item-name"><p class="name-tutor">' + v.display_name + '</p><p class="icon-star">' + img_star + '<span>('+v.cnt+')<span></p><p class="view-tutor">' + view + '</p></div>';
                                            div +='</div>';

                                            div +='<div class="tr-info clearfix" id="tr-info' + v.ID + '">';
                                                div += '<p class="head-title-resum">' + title_resum + '</p>';
                                                div += '<h4>Why I like teaching and tutoring:</h4>';
                                                div += '<p>' + v.desc_tell_me + '<p>';
                                                div += '<h4>Subjects I can teach:</h4>';
                                                div += '<ul>';
                                                if(v.subject_type.length > 0){
                                                    for(var i = 0; i < v.subject_type.length; i++){
                                                        div += '<li>'+ v.subject_type[i] +'</li>';
                                                    }
                                                }
                                                div += '</ul>';
                                                
                                                if(v.school_name != '' || v.teaching_link != '' || v.user_years != '' || v.teaching_subject != ''){
                                                    $class_teaching = '';
                                                }else{
                                                    $class_teaching = ' class="hidden"';
                                                }    
                                                div += '<h4' + $class_teaching + '>Teaching Experlence at School:</h4>';
                                                div += '<ul' + $class_teaching + '>';
                                                if(v.school_name != '' || v.teaching_link != ''){
                                                    div += '<li>School Name: ' + v.school_name + ' (' + v.teaching_link + ')</li>';
                                                }
                                                if(v.teaching_subject != ''){
                                                    div += '<li>Subject: ' + v.teaching_subject + '</li>';
                                                }
                                                if(v.user_years != ''){
                                                    div += '<li>Years: ' + v.user_years + '</li>';
                                                }
                                                div += '</ul>';

                                                if(v.school_attend != '' || v.user_grade != '' || v.user_gpa != '' || v.user_major != '' || v.student_link != ''){
                                                    $class_student = '';
                                                }else{
                                                    $class_student = ' class="hidden"';
                                                }
                                                div += '<h4' + $class_student + '>Teaching Experlence as a Student:</h4>';
                                                div += '<ul' + $class_student + '>';
                                                if(v.school_attend != ''){
                                                    div += '<li>Attending: ' + v.school_attend + ' (' + v.student_link + ')</li>';
                                                }
                                                if(v.user_grade == '1'){
                                                    div += '<li>Grade: Freshman</li>';
                                                }else if(v.user_grade == '2'){
                                                    div += '<li>Grade: Sophomore</li>';
                                                }else if(v.user_grade == '3'){
                                                    div += '<li>Grade: Junior</li>';
                                                }else if(v.user_grade == '4'){
                                                    div += '<li>Grade: Senior</li>';
                                                }
                                                if(v.user_gpa != ''){
                                                    div += '<li>GPA: ' + v.user_gpa + '</li>';
                                                }
                                                if(v.user_major != ''){
                                                    div += '<li>Major: ' + v.user_major + '</li>';
                                                }
                                                div += '</ul>';
                                                div += '<h4>Educational Background:</h4>';
                                                div += '<ul>';
                                                if(v.school_name1 != ''){
                                                    div += '<li>School Name: ' + v.school_name1 + ' (' + data.school_link1 + ')</li>';
                                                }
                                                if(v.school_name2 != ''){
                                                    div += '<li>School Name: ' + v.school_name2 + ' (' + data.school_link2 + ')</li>';
                                                }
                                                if(v.any_other != ''){
                                                    div += '<li>Others: ' + v.any_other + '</li>';
                                                }
                                                div += '</ul>';
                                            div += '</div>';

                                    if (v.reviews.length > 0) {
                                        div += '<div id="tr-review' + v.ID + '" style="display: none">';
                                        div += '<p class="head-title-resum">REVIEW</p>';
                                        $.each(v.reviews, function (ir, vr) {
                                            var img_star1 = '';
                                            var max_star1 = 5;

                                            if(vr.star > 0){
                                                for(var k = 0; k < vr.star; k++){
                                                    img_star1 += '<img src="<?php echo get_template_directory_uri(); ?>/library/images/icon_Rating_ON.png" alt="">';
                                                }
                                                max_star1 = max_star1 - vr.star;
                                            }

                                            for(var j = 0; j < max_star1; j++){
                                                img_star1 += '<img src="<?php echo get_template_directory_uri(); ?>/library/images/icon_Rating_OFF.png" alt="">';
                                            }

                                            if(ir == 0)
                                                var cl = 'first';
                                            else
                                                var cl = '';

                                            div += '<div class="tr-info ' + cl + ' clearfix">';
                                                div += '<p class="subject-review">' + vr.subject + '</p><p class="icon-star">' + img_star1 + '<span class="name-review">' + vr.review_name + '<span></p><p class="view-tutor-message">' + vr.message + '</p>';
                                            div += '</div>';
                                        });
                                        div += '</div>';
                                    }
                                    div +='</div>';
                                    $('.slide-resume').append(div);
                                });   

                                $(".slide-resume").not('.slick-initialized').slick(getSliderSettings());
                            }else{
                                //var tr = '<tr><td colspan="3" class="no-list"><img src="<?php echo get_template_directory_uri(); ?>/library/images/icon_No_Schedule.png" alt="">Currently, there are no list</td></tr>';

                                var tr = '<tr><td class="no-results"><img src="<?php echo get_template_directory_uri(); ?>/library/images/icon_Not_Available.png" alt="">Currently, there are no results.</td></tr>';
                                if(type == 'fromclass'){
                                    tbody_request.append(tr);  
                                }
                            } 
                            
                            return true;
                        });
                    }   
					
					function updateReview(tutor_id = 0){
						var userid = '<?php if ($is_user_logged_in) echo $current_user->ID; else echo 0; ?>';
						$.get(home_url + "/?r=ajax/get_users_reviews", {tutor_id: tutor_id}, function (data) {
							data = JSON.parse(data);
							if (data.reviews.length > 0) {
								var div = '<p class="head-title-resum">REVIEW</p>';
								$.each(data.reviews, function (ir, vr) {
									var img_star1 = '';
									var max_star1 = 5;

									if(vr.star > 0){
										for(var k = 0; k < vr.star; k++){
											img_star1 += '<img src="<?php echo get_template_directory_uri(); ?>/library/images/icon_Rating_ON.png" alt="">';
										}
										max_star1 = max_star1 - vr.star;
									}

									for(var j = 0; j < max_star1; j++){
										img_star1 += '<img src="<?php echo get_template_directory_uri(); ?>/library/images/icon_Rating_OFF.png" alt="">';
									}
									
									if(userid == vr.userid){
										$('#view-write-review' + tutor_id).attr('data-id',vr.id);
										$('#view-write-review' + tutor_id).attr('data-review-id',vr.review_id);
										$('#view-write-review' + tutor_id).attr('data-userid',vr.userid);
										$('#view-write-review' + tutor_id).attr('data-star',vr.star);
										$('#view-write-review' + tutor_id).attr('data-subject',vr.subject);
										$('#view-write-review' + tutor_id).attr('data-message',vr.message);
									}

									if(ir == 0)
										var cl = 'first';
									else
										var cl = '';

									div += '<div class="tr-info ' + cl + ' clearfix">';
										div += '<p class="subject-review">' + vr.subject + '</p><p class="icon-star">' + img_star1 + '<span class="name-review">' + vr.review_name + '<span></p><p class="view-tutor-message">' + vr.message + '</p>';
									div += '</div>';
								});
								$('#tr-review' + tutor_id).html(div);
							}
						});
					}

                    function getDistancePlace(text = '', type = ''){
                        var distance = '';
                        switch($.trim(text)) {
                            case 'Mobile Number:':
                                if(type == 'Firefox')
                                    distance = 29;
                                else
                                    distance = 35;
                                break;                               
                            case 'Last School Attended:':
                                if(type == 'Firefox')
                                    distance = 31;
                                else
                                    distance = 37;
                                break;
                            case 'School Taught (if any):':
                                if(type == 'Firefox')
                                    distance = 30;
                                else
                                    distance = 35;
                                break;
                            case 'Skype ID (if any):':
                                if(type == 'Firefox')
                                    distance = 28;
                                else 
                                    distance = 30;
                                break;
                            case 'Profession:':
                                distance = 25;
                                break;
                            case 'Description:':
                                if(type == 'Firefox')
                                    distance = 28;
                                else
                                    distance = 30;
                                break;
                            case 'School Name:':
                                if(type == 'Firefox')
                                    distance = 26;
                                else
                                    distance = 29;
                                break;
                            case 'Link (if any):':
                                if(type == 'Firefox')
                                    distance = 25;
                                else
                                    distance = 28;
                                break;
                            case 'Subject:':
                                if(type == 'Firefox')
                                    distance = 22;
                                else
                                    distance = 24;
                                break;
                            case 'Years:':
                                distance = 20;
                                break;
                            case 'Attending:':
                                if(type == 'Firefox')
                                    distance = 26;
                                else
                                    distance = 29;
                                break;
                            case 'GPA:':
                                distance = 18;
                                break;
                            case 'Major:':
                                if(type == 'Firefox')
                                    distance = 22;
                                else
                                    distance = 23;
                                break;
                            case 'School Name 1:':
                                if(type == 'Firefox')
                                    distance = 26;
                                else
                                    distance = 30;
                                break;
                            case 'School Name 2:':
                                if(type == 'Firefox')
                                    distance = 26;
                                else
                                    distance = 30;
                                break;
                            case 'Others:':
                                if(type == 'Firefox')
                                    distance = 24;
                                else
                                    distance = 26;
                                break;
                            case 'Email Address:':
                                if(type == 'Firefox')
                                    distance = 26;
                                else
                                    distance = 26;
                                break;
                            case 'Password:':
                                distance = 24;
                                break;
                            case 'Confirm Password:':
                                if(type == 'Firefox')
                                    distance = 30;
                                else
                                    distance = 34;
                                break;
                            case 'First Name:':
                                if(type == 'Firefox')
                                    distance = 24;
                                else
                                    distance = 26;
                                break;
                            case 'Last Name:':
                                distance = 25;
                                break;
                            case 'Year:':
                                distance = 0;
                                break;
                            case 'San Francisco':
                                if(type == 'Firefox')
                                    distance = -12;
                                else
                                    distance = -12;
                                break;
                            case 'New York':
                                if(type == 'Firefox')
                                    distance = 37;
                                else
                                    distance = 37;
                                break;
                            case 'Minneapolis':
                                if(type == 'Firefox')
                                    distance = 6;
                                else
                                    distance = 6;
                                break;
                            case 'Colorado':
                                if(type == 'Firefox')
                                    distance = 41;
                                else
                                    distance = 41;
                                break;
                            case 'Hawaii':
                                if(type == 'Firefox')
                                    distance = 64;
                                else
                                    distance = 64;
                                break;
                            case 'Guam':
                                if(type == 'Firefox')
                                    distance = 72;
                                else
                                    distance = 72;
                                break;
                            case 'Tokyo':
                                if(type == 'Firefox')
                                    distance = 74;
                                else
                                    distance = 74;
                                break;
                            case 'Seoul':
                                if(type == 'Firefox')
                                    distance = 77;
                                else
                                    distance = 77;
                                break;
                            case 'Beijing':
                                if(type == 'Firefox')
                                    distance = 63;
                                else
                                    distance = 63;
                                break;
                            case 'Xianyang':
                                if(type == 'Firefox')
                                    distance = 38;
                                else
                                    distance = 38;
                                break;
                            case 'Hanoi':
                                if(type == 'Firefox')
                                    distance = 74;
                                else
                                    distance = 74;
                                break;
                            case 'Bangkok':
                                if(type == 'Firefox')
                                    distance = 44;
                                else
                                    distance = 44;
                                break;
                            case 'Myanmar':
                                if(type == 'Firefox')
                                    distance = 36;
                                else
                                    distance = 36;
                                break;
                            case 'Bangladesh':
                                if(type == 'Firefox')
                                    distance = 12;
                                else
                                    distance = 12;
                                break;
                            case 'Sri Lanka':
                                if(type == 'Firefox')
                                    distance = 34;
                                else
                                    distance = 34;
                                break;
                            case 'New Delhi':
                                if(type == 'Firefox')
                                    distance = 20;
                                else
                                    distance = 20;
                                break;
                            case 'Mumbai':
                                if(type == 'Firefox')
                                    distance = 48;
                                else
                                    distance = 48;
                                break;
                            case 'London':
                                if(type == 'Firefox')
                                    distance = 55;
                                else
                                    distance = 55;
                                break;
                            case 'Sydney':
                                if(type == 'Firefox')
                                    distance = 60;
                                else
                                    distance = 60;
                                break;
                            case 'Select Time Zone':
                                if(type == 'Firefox')
                                    distance = -40;
                                else
                                    distance = -40;
                                break;
                            default:
                                distance = 30;
                        }
                        return distance;
                    }
                });
            })(jQuery);
        </script>