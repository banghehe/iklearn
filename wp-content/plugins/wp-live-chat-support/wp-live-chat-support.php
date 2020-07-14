<?php
/*
  Plugin Name: WP Live Chat Support
  Plugin URI: http://www.wp-livechat.com
  Description: The easiest to use website live chat plugin. Let your visitors chat with you and increase sales conversion rates with WP Live Chat Support. No third party connection required!
  Version: 6.2.11
  Author: WP-LiveChat
  Author URI: http://www.wp-livechat.com
  Text Domain: wplivechat
  Domain Path: /languages
 */

//error_reporting(E_ERROR);
global $wplc_version;
global $wplc_p_version;
global $wplc_tblname;
global $wpdb;
global $wplc_tblname_chats;
global $wplc_tblname_msgs;
global $wplc_tblname_offline_msgs;

$wplc_tblname_offline_msgs = $wpdb->prefix . "wplc_offline_messages";
$wplc_tblname_chats = $wpdb->prefix . "wplc_chat_sessions";
$wplc_tblname_msgs = $wpdb->prefix . "wplc_chat_msgs";
$wplc_version = "6.2.10";

define('WPLC_BASIC_PLUGIN_DIR', dirname(__FILE__));
define('WPLC_BASIC_PLUGIN_URL', plugins_url() . "/wp-live-chat-support/");
global $wplc_basic_plugin_url;
$wplc_basic_plugin_url = get_option('siteurl') . "/wp-content/plugins/wp-live-chat-support/";


global $wplc_pro_version;
$wplc_ver = str_replace('.', '', $wplc_pro_version);
$checker = intval($wplc_ver);

require_once (plugin_dir_path(__FILE__) . "ajax_new.php");
require_once (plugin_dir_path(__FILE__) . "functions.php");
require_once (plugin_dir_path(__FILE__) . "includes/deprecated.php");
require_once (plugin_dir_path(__FILE__) . "includes/surveys.php");
require_once (plugin_dir_path(__FILE__) . "modules/api/wplc-api.php");

add_action('wp_ajax_wplc_admin_set_transient', 'wplc_action_callback');
add_action('wp_ajax_wplc_admin_remove_transient', 'wplc_action_callback');
add_action('wp_ajax_wplc_hide_ftt', 'wplc_action_callback');
add_action('wp_ajax_nopriv_wplc_user_send_offline_message', 'wplc_action_callback');
add_action('wp_ajax_wplc_user_send_offline_message', 'wplc_action_callback');
add_action('wp_ajax_delete_offline_message', 'wplc_action_callback');
add_action('init', 'wplc_version_control');
add_action('wp_footer', 'wplc_display_box');
add_action('init', 'wplc_init');

require_once (plugin_dir_path(__FILE__) . 'includes/update_control.class.php');

add_action('admin_init', 'wplc_head_basic');
add_action('wp_enqueue_scripts', 'wplc_add_user_stylesheet');
add_action('admin_enqueue_scripts', 'wplc_add_admin_stylesheet');
add_action('admin_menu', 'wplc_admin_menu');
add_action('admin_head', 'wplc_superadmin_javascript');

register_activation_hook(__FILE__, 'wplc_activate');

function wplc_basic_check() {
    // check if basic exists if pro is installed
}

function wplc_init() {
    $plugin_dir = basename(dirname(__FILE__)) . "/languages/";
    load_plugin_textdomain('wplivechat', false, $plugin_dir);
}

function wplc_version_control() {

    global $wplc_version;


    $current_version = get_option("wplc_current_version");
    if (!isset($current_version) || $current_version != $wplc_version) {
        

        $admins = get_role('administrator');
        $admins->add_cap('wplc_ma_agent');

        $uid = get_current_user_id();
        update_user_meta($uid, 'wplc_ma_agent', 1);
        update_user_meta($uid, "wplc_chat_agent_online", time());

        /* add caps to admin */
        if (current_user_can('manage_options')) {
            global $user_ID;
            $user = new WP_User($user_ID);
            foreach ($user->roles as $urole) {
                if ($urole == "administrator") {
                    $admins = get_role('administrator');
                    $admins->add_cap('edit_wplc_quick_response');
                    $admins->add_cap('edit_wplc_quick_response');
                    $admins->add_cap('edit_other_wplc_quick_response');
                    $admins->add_cap('publish_wplc_quick_response');
                    $admins->add_cap('read_wplc_quick_response');
                    $admins->add_cap('read_private_wplc_quick_response');
                    $admins->add_cap('delete_wplc_quick_response');
                }
            }
        }

        $wplc_settings = get_option("WPLC_SETTINGS");


        if (!isset($wplc_settings['wplc_pro_na']) || (isset($wplc_settings['wplc_pro_na']) && $wplc_settings['wplc_pro_na'] == "")) {
            $wplc_settings["wplc_pro_na"] = __("Chat offline. Leave a message", "wplivechat");
        }
        if (!isset($wplc_settings['wplc_pro_intro']) || (isset($wplc_settings['wplc_pro_intro']) && $wplc_settings['wplc_pro_intro'] == "")) {
            $wplc_settings["wplc_pro_intro"] = __("Hello. Please input your details so that I may help you.", "wplivechat");
        }
        if (!isset($wplc_settings['wplc_pro_offline1']) || (isset($wplc_settings['wplc_pro_offline1']) && $wplc_settings['wplc_pro_offline1'] == "")) {
            $wplc_settings["wplc_pro_offline1"] = __("We are currently offline. Please leave a message and we'll get back to you shortly.", "wplivechat");
        }
        if (!isset($wplc_settings['wplc_pro_offline2']) || (isset($wplc_settings['wplc_pro_offline2']) && $wplc_settings['wplc_pro_offline2'] == "")) {
            $wplc_settings["wplc_pro_offline2"] = __("Sending message...", "wplivechat");
        }
        if (!isset($wplc_settings['wplc_pro_offline3']) || (isset($wplc_settings['wplc_pro_offline3']) && $wplc_settings['wplc_pro_offline3'] == "")) {
            $wplc_settings["wplc_pro_offline3"] = __("Thank you for your message. We will be in contact soon.", "wplivechat");
        }
        if (!isset($wplc_settings['wplc_pro_fst1']) || (isset($wplc_settings['wplc_pro_fst1']) && $wplc_settings['wplc_pro_fst1'] == "")) {
            $wplc_settings["wplc_pro_fst1"] = __("Questions?", "wplivechat");
        }
        if (!isset($wplc_settings['wplc_pro_fst2']) || (isset($wplc_settings['wplc_pro_fst2']) && $wplc_settings['wplc_pro_fst2'] == "")) {
            $wplc_settings["wplc_pro_fst2"] = __("Chat with us", "wplivechat");
        }
        if (!isset($wplc_settings['wplc_pro_fst3']) || (isset($wplc_settings['wplc_pro_fst3']) && $wplc_settings['wplc_pro_fst3'] == "")) {
            $wplc_settings["wplc_pro_fst3"] = __("Start live chat", "wplivechat");
        }
        if (!isset($wplc_settings['wplc_pro_sst1']) || (isset($wplc_settings['wplc_pro_sst1']) && $wplc_settings['wplc_pro_sst1'] == "")) {
            $wplc_settings["wplc_pro_sst1"] = __("Start Chat", "wplivechat");
        }
        if (!isset($wplc_settings['wplc_pro_sst1_survey']) || (isset($wplc_settings['wplc_pro_sst1_survey']) && $wplc_settings['wplc_pro_sst1_survey'] == "")) {
            $wplc_settings["wplc_pro_sst1_survey"] = __("Or chat to an agent now", "wplivechat");
        }
        if (!isset($wplc_settings['wplc_pro_sst1e_survey']) || (isset($wplc_settings['wplc_pro_sst1e_survey']) && $wplc_settings['wplc_pro_sst1e_survey'] == "")) {
            $wplc_settings["wplc_pro_sst1e_survey"] = __("Chat ended", "wplivechat");
        }
        if (!isset($wplc_settings['wplc_pro_sst2']) || (isset($wplc_settings['wplc_pro_sst2']) && $wplc_settings['wplc_pro_sst2'] == "")) {
            $wplc_settings["wplc_pro_sst2"] = __("Connecting. Please be patient...", "wplivechat");
        }
        if (!isset($wplc_settings['wplc_pro_tst1']) || (isset($wplc_settings['wplc_pro_tst1']) && $wplc_settings['wplc_pro_tst1'] == "")) {
            $wplc_settings["wplc_pro_tst1"] = __("Reactivating your previous chat...", "wplivechat");
        }
        if (!isset($wplc_settings['wplc_user_welcome_chat']) || (isset($wplc_settings['wplc_user_welcome_chat']) && $wplc_settings['wplc_user_welcome_chat'] == "")) {
            $wplc_settings["wplc_user_welcome_chat"] = __("Welcome. How may I help you?", "wplivechat");
        }
        if (!isset($wplc_settings['wplc_user_enter']) || (isset($wplc_settings['wplc_user_enter']) && $wplc_settings['wplc_user_enter'] == "")) {
            $wplc_settings["wplc_user_enter"] = __("Press ENTER to send your message", "wplivechat");
        }


        if (!isset($wplc_settings['wplc_powered_by_link'])) {
            $wplc_settings["wplc_powered_by_link"] = "0";
        }



        /* users who are updating will stay on the  existing theme */
        if (get_option("WPLC_FIRST_TIME")) {
            
        } else {
            if (!isset($wplc_settings['wplc_newtheme'])) {
                $wplc_settings["wplc_newtheme"] = "theme-2";
            }
        }


        if (!isset($wplc_settings['wplc_settings_color1'])) {
            $wplc_settings["wplc_settings_color1"] = "ED832F";
        }
        if (!isset($wplc_settings['wplc_settings_color2'])) {
            $wplc_settings["wplc_settings_color2"] = "FFFFFF";
        }
        if (!isset($wplc_settings['wplc_settings_color3'])) {
            $wplc_settings["wplc_settings_color3"] = "EEEEEE";
        }
        if (!isset($wplc_settings['wplc_settings_color4'])) {
            $wplc_settings["wplc_settings_color4"] = "666666";
        }



        if (!isset($wplc_settings['wplc_settings_align'])) {
            $wplc_settings["wplc_settings_align"] = 2;
        }

        if (!isset($wplc_settings['wplc_settings_enabled'])) {
            $wplc_settings["wplc_settings_enabled"] = 1;
        }

        if (!isset($wplc_settings['wplc_settings_fill'])) {
            $wplc_settings["wplc_settings_fill"] = "ed832f";
        }

        if (!isset($wplc_settings['wplc_settings_font'])) {
            $wplc_settings["wplc_settings_font"] = "FFFFFF";
        }
        wplc_handle_db();
        update_option("wplc_current_version", $wplc_version);


        if (!isset($wplc_settings['wplc_require_user_info'])) {
            $wplc_settings['wplc_require_user_info'] = "1";
        }
        if (!isset($wplc_settings['wplc_loggedin_user_info'])) {
            $wplc_settings['wplc_loggedin_user_info'] = "1";
        }
        if (!isset($wplc_settings['wplc_user_alternative_text'])) {
            $wplc_alt_text = __("Please click \'Start Chat\' to initiate a chat with an agent", "wplivechat");
            $wplc_settings['wplc_user_alternative_text'] = $wplc_alt_text;
        }
        if (!isset($wplc_settings['wplc_enabled_on_mobile'])) {
            $wplc_settings['wplc_enabled_on_mobile'] = "1";
        }
        if (!isset($wplc_settings['wplc_record_ip_address'])) {
            $wplc_settings['wplc_record_ip_address'] = "1";
        }
        if (!isset($wplc_settings['wplc_enable_msg_sound'])) {
            $wplc_settings['wplc_enable_msg_sound'] = "1";
        }
        if (!isset($wplc_settings['wplc_using_localization_plugin'])) {
            $wplc_settings['wplc_using_localization_plugin'] = 0;
        }


        update_option("WPLC_SETTINGS", $wplc_settings);

        do_action("wplc_update_hook");
    }
}

add_action("wplc_hook_set_transient", "wplc_hook_control_set_transient", 10);

function wplc_hook_control_set_transient() {
    $should_set_transient = apply_filters("wplc_filter_control_set_transient", true);
    if ($should_set_transient) {
        set_transient("wplc_is_admin_logged_in", "1", 70);
    }
}

add_action("wplc_hook_remove_transient", "wplc_hook_control_remove_transient", 10);

function wplc_hook_control_remove_transient() {
    delete_transient('wplc_is_admin_logged_in');
}

function wplc_action_callback() {
    global $wpdb;
    global $wplc_tblname_chats;
    $check = check_ajax_referer('wplc', 'security');

    if ($check == 1) {

        if ($_POST['action'] == 'delete_offline_message') {

            global $wplc_tblname_offline_msgs;

            $mid = sanitize_text_field($_POST['mid']);

            $sql = "DELETE FROM `$wplc_tblname_offline_msgs` WHERE `id` = '$mid'";
            $query = $wpdb->Query($sql);

            if ($query) {

                echo 1;
            }
        }

        if ($_POST['action'] == "wplc_user_send_offline_message") {
            if (function_exists('wplc_send_offline_msg')) {
                wplc_send_offline_msg($_POST['name'], $_POST['email'], $_POST['msg'], $_POST['cid']);
            }
            if (function_exists('wplc_store_offline_message')) {
                wplc_store_offline_message($_POST['name'], $_POST['email'], $_POST['msg']);
            }
            do_action("wplc_hook_offline_message", array(
                "cid" => $_POST['cid'],
                "name" => $_POST['name'],
                "email" => $_POST['email'],
                "url" => get_site_url(),
                "msg" => $_POST['msg']
                    )
            );
        }
        if ($_POST['action'] == "wplc_admin_set_transient") {
            do_action("wplc_hook_set_transient");
        }
        if ($_POST['action'] == "wplc_admin_remove_transient") {
            do_action("wplc_hook_remove_transient");
        }
        if ($_POST['action'] == 'wplc_hide_ftt') {
            update_option("WPLC_FIRST_TIME_TUTORIAL", true);
        }
        do_action("wplc_hook_action_callback");
    }
    die(); // this is required to return a proper result
}

function wplc_feedback_page_include() {
    include 'includes/feedback-page.php';
}

/**
 * Decide who gets to see the various main menus (left navigation)
 * @return array
 * @since  6.0.00
 * @author Nick Duncan <nick@codecabin.co.za>
 */
add_filter("wplc_ma_filter_menu_control", "wplc_filter_control_menu_control", 10, 1);

function wplc_filter_control_menu_control() {
    $array = array(
        0 => 'read', /* main menu */
        1 => 'read', /* settings */
        2 => 'read', /* history */
        3 => 'read', /* missed chats */
        4 => 'read', /* offline messages */
        5 => 'read', /* feedback */
        6 => 'read' /* surveys */
    );
    return $array;
}

function wplc_admin_menu() {

    $cap = apply_filters("wplc_ma_filter_menu_control", array());
    if (get_option("wplc_seen_surveys")) {
        $survey_new = "";
    } else {
        $survey_new = ' <span class="update-plugins"><span class="plugin-count">New</span></span>';
    }

    $wplc_current_user = get_current_user_id();

    /* If user is either an agent or an admin, access the page. */
    if (get_user_meta($wplc_current_user, 'wplc_ma_agent', true) || current_user_can("wplc_ma_agent")) {
        $wplc_mainpage = add_menu_page('WP Live Chat', __('Live Chat', 'wplivechat'), $cap[0], 'wplivechat-menu', 'wplc_admin_menu_layout', 'dashicons-format-chat');
        add_submenu_page('wplivechat-menu', __('Settings', 'wplivechat'), __('Settings', 'wplivechat'), $cap[1], 'wplivechat-menu-settings', 'wplc_admin_settings_layout');
        add_submenu_page('wplivechat-menu', __('Surveys', 'wplivechat'), __('Surveys', 'wplivechat') . $survey_new, $cap[2], 'wplivechat-menu-survey', 'wplc_admin_survey_layout');
    }

    //Only if pro is not active
    if (!function_exists("wplc_pro_reporting_page")) {
        add_submenu_page('wplivechat-menu', __('Reporting', 'wplivechat'), __('Reporting', 'edit_posts') . ' <span class="update-plugins"><span class="plugin-count">Pro</span></span>', $cap[0], 'wplc-basic-reporting', 'wplc_basic_reporting_page');
    }


    //Only if pro is not active
    if (!function_exists("wplc_pro_triggers_page")) {
        add_submenu_page('wplivechat-menu', __('Triggers', 'wplivechat'), __('Triggers', 'edit_posts') . ' <span class="update-plugins"><span class="plugin-count">Pro</span></span>', $cap[0], 'wplc-basic-triggers', 'wplc_basic_triggers_page');
    }

    /* only if user is both an agent and an admin that has the cap assigned, can they access these pages */
    if (get_user_meta($wplc_current_user, 'wplc_ma_agent', true) && current_user_can("wplc_ma_agent")) {

        add_submenu_page('wplivechat-menu', __('History', 'wplivechat'), __('History', 'wplivechat'), $cap[2], 'wplivechat-menu-history', 'wplc_admin_history_layout');
        add_submenu_page('wplivechat-menu', __('Missed Chats', 'wplivechat'), __('Missed Chats', 'wplivechat'), $cap[3], 'wplivechat-menu-missed-chats', 'wplc_admin_missed_chats');

        /* TO DO
          Add a hook here so that the other plugins can add to the menu
          Also make sure the function below is controled differently as the pro will not longer exist
         */

        if (function_exists("wplc_admin_menu_pro")) {
            global $wplc_pro_version;
            if (intval(str_replace(".", "", $wplc_pro_version)) <= 5100) {
                /* do nothing as they have the pro active and their version of the pro makes use of offline messages */
            } else {
                add_submenu_page('wplivechat-menu', __('Offline Messages', 'wplivechat'), __('Offline Messages', 'wplivechat'), $cap[4], 'wplivechat-menu-offline-messages', 'wplc_admin_offline_messages');
            }
        } else {
            add_submenu_page('wplivechat-menu', __('Offline Messages', 'wplivechat'), __('Offline Messages', 'wplivechat'), $cap[4], 'wplivechat-menu-offline-messages', 'wplc_admin_offline_messages');
        }
        do_action("wplc_hook_menu_mid", $cap);


        add_submenu_page('wplivechat-menu', __('Feedback', 'wplivechat'), __('Feedback', 'wplivechat'), $cap[5], 'wplivechat-menu-feedback-page', 'wplc_feedback_page_include');
        add_submenu_page('wplivechat-menu', __('Support', 'wplivechat'), __('Support', 'wplivechat'), 'manage_options', 'wplivechat-menu-support-page', 'wplc_support_menu');
        add_submenu_page('wplivechat-menu', __('Extensions', 'wplivechat'), __('Extensions', 'wplivechat'), 'manage_options', 'wplivechat-menu-extensions-page', 'wplc_extensions_menu');
    }
    do_action("wplc_hook_menu");
}

add_action("wplc_hook_menu", "wplc_hook_control_menu");

function wplc_hook_control_menu() {
    $check = apply_filters("wplc_filter_menu_api", 0);
    if ($check > 0) {
        add_submenu_page('wplivechat-menu', __('API Keys', 'wplivechat'), __('API Keys', 'wplivechat'), 'manage_options', 'wplivechat-menu-api-keys-page', 'wplc_api_keys_menu');
    }
}

function wplc_api_keys_menu() {
    $page_content = "<h1>" . __("Premium Extension API Keys", "wplivechat") . "</h3>";
    $page_content .= "<p>" . __("To find and manage your premium API keys, please visit your <a target='_BLANK' href='https://wp-livechat.com/my-account/'>my account</a> page.", "") . "</p>";

    $page_content .= "<hr />";
    $page_content = apply_filters("wplc_filter_api_page", $page_content);


    echo $page_content;
}

add_action("init", "wplc_load_user_js", 0);

function wplc_load_user_js() {

    if (!is_admin()) {
        if (in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'))) {
            return false;
        }


        if (function_exists('wplc_display_chat_contents')) {
            $display_contents = wplc_display_chat_contents();
        } else {
            $display_contents = 1;
        }

        if (function_exists('wplc_is_user_banned_basic')) {
            $user_banned = wplc_is_user_banned_basic();
        } else {
            $user_banned = 0;
        }

        $display_contents = apply_filters("wplc_filter_display_contents", $display_contents);

        if ($display_contents && $user_banned == 0) {

            /* do not show if pro is outdated */
            global $wplc_pro_version;
            if (isset($wplc_pro_version)) {
                $float_version = floatval($wplc_pro_version);
                if ($float_version < 4 || $wplc_pro_version == "4.1.0" || $wplc_pro_version == "4.1.1") {
                    return;
                }
            }

            $wplc_settings = get_option("WPLC_SETTINGS");
            if (!class_exists('Mobile_Detect')) {
                require_once (plugin_dir_path(__FILE__) . 'includes/Mobile_Detect.php');
            }
            $wplc_detect_device = new Mobile_Detect;
            $wplc_is_mobile = $wplc_detect_device->isMobile();

            if ($wplc_is_mobile && isset($wplc_settings['wplc_enabled_on_mobile']) && $wplc_settings['wplc_enabled_on_mobile'] == '0') {
                return;
            }

            if (function_exists("wplc_register_pro_version")) {
                if (function_exists('wplc_basic_hide_chat_when_offline')) {
                    $wplc_hide_chat = wplc_basic_hide_chat_when_offline();
                    if (!$wplc_hide_chat) {
                        if (function_exists("wplc_push_js_to_front_pro")) {
                            wplc_push_js_to_front_pro();
                        }
                    }
                } else {
                    if (function_exists("wplc_push_js_to_front_pro")) {
                        wplc_push_js_to_front_pro();
                    }
                }
            } else {
                wplc_push_js_to_front_basic();
            }
        }
    }
}

function wplc_push_js_to_front_basic() {
    global $wplc_is_mobile;
    global $wplc_version;
    wp_enqueue_script('jquery');

    $wplc_settings = get_option("WPLC_SETTINGS");

    if (isset($wplc_settings['wplc_display_to_loggedin_only']) && $wplc_settings['wplc_display_to_loggedin_only'] == 1) {
        /* Only show to users that are logged in */
        if (!is_user_logged_in()) {
            return;
        }
    }

    if ($wplc_settings["wplc_settings_enabled"] == 2) {
        return;
    }

    if (isset($wplc_settings['wplc_display_name']) && $wplc_settings['wplc_display_name'] == 1) {
        $wplc_display = 'display';
        wp_register_script('wplc-md5', plugins_url('/js/md5.js', __FILE__), array('wplc-user-script'), $wplc_version);
        wp_enqueue_script('wplc-md5');
    } else {
        $wplc_display = 'hide';
    }
    if (isset($wplc_settings['wplc_enable_msg_sound']) && intval($wplc_settings['wplc_enable_msg_sound']) == 1) {
        $wplc_ding = '1';
    } else {
        $wplc_ding = '0';
    }

    $ajax_nonce = wp_create_nonce("wplc");
    if (!function_exists("wplc_register_pro_version")) {
        $ajaxurl = is_math_panel() ? "http://math.dicprj.moe/wp-admin/admin-ajax.php" : admin_url('admin-ajax.php');
        $wplc_ajaxurl = $ajaxurl;
    }





    wp_register_script('wplc-user-script', plugins_url('/js/wplc_u.js', __FILE__), array('jquery'), $wplc_version);
    /**
     * No longer in use as of 6.2.11 as using the minified file causes issues on sites that are minified.
     * @deprecated 6.2.11
     */
    // wp_register_script('wplc-user-script', plugins_url('/js/wplc_u.min.js', __FILE__),array('jquery'),$wplc_version);

    wp_enqueue_script('wplc-user-script');

    if (isset($wplc_settings['wplc_newtheme'])) {
        $wplc_newtheme = $wplc_settings['wplc_newtheme'];
    } else {
        
    }
    if (isset($wplc_newtheme)) {
        if ($wplc_newtheme == 'theme-1') {
            wp_register_script('wplc-theme-classic', plugins_url('/js/themes/classic.js', __FILE__), array('wplc-user-script'), $wplc_version);
            wp_enqueue_script('wplc-theme-classic');
        } else if ($wplc_newtheme == 'theme-2') {
            wp_register_script('wplc-theme-modern', plugins_url('/js/themes/modern.js', __FILE__), array('wplc-user-script'), $wplc_version);
            wp_enqueue_script('wplc-theme-modern');
        }
    } else {
        wp_register_script('wplc-theme-classic', plugins_url('/js/themes/classic.js', __FILE__), array('wplc-user-script'), $wplc_version);
        wp_enqueue_script('wplc-theme-classic');
    }


    wp_register_script('wplc-user-jquery-cookie', plugins_url('/js/jquery-cookie.js', __FILE__), array('wplc-user-script'), false, false);
    wp_enqueue_script('wplc-user-jquery-cookie');

    $ajax_url = is_math_panel() ? "http://math.dicprj.moe/wp-admin/admin-ajax.php" : admin_url('admin-ajax.php');
    $home_ajax_url = $ajax_url;

    $wplc_ajax_url = apply_filters("wplc_filter_ajax_url", $ajax_url);
    wp_localize_script('wplc-admin-chat-js', 'wplc_ajaxurl', $wplc_ajax_url);
    wp_localize_script('wplc-ma-js', 'wplc_home_ajaxurl', $home_ajax_url);

    $wplc_detect_device = new Mobile_Detect;
    $wplc_is_mobile = $wplc_detect_device->isMobile() ? 'true' : 'false';
    wp_localize_script('wplc-user-script', 'wplc_is_mobile', $wplc_is_mobile);


    wp_localize_script('wplc-user-script', 'wplc_ajaxurl', $wplc_ajax_url);
    wp_localize_script('wplc-user-script', 'wplc_ajaxurl_site', is_math_panel() ? "http://math.dicprj.moe/wp-admin/admin-ajax.php" : admin_url('admin-ajax.php'));
    wp_localize_script('wplc-user-script', 'wplc_nonce', $ajax_nonce);
    wp_localize_script('wplc-user-script', 'wplc_plugin_url', plugins_url());
    wp_localize_script('wplc-user-script', 'wplc_display_name', $wplc_display);
    wp_localize_script('wplc-user-script', 'wplc_enable_ding', $wplc_ding);
    $wplc_run_override = "0";
    $wplc_run_override = apply_filters("wplc_filter_run_override", $wplc_run_override);
    wp_localize_script('wplc-user-script', 'wplc_filter_run_override', $wplc_run_override);

    if (!isset($wplc_settings['wplc_pro_offline1'])) {
        $wplc_settings["wplc_pro_offline1"] = __("We are currently offline. Please leave a message and we'll get back to you shortly.", "wplivechat");
    }
    if (!isset($wplc_settings['wplc_pro_offline2'])) {
        $wplc_settings["wplc_pro_offline2"] = __("Sending message...", "wplivechat");
    }
    if (!isset($wplc_settings['wplc_pro_offline3'])) {
        $wplc_settings["wplc_pro_offline3"] = __("Thank you for your message. We will be in contact soon.", "wplivechat");
    }


    wp_localize_script('wplc-user-script', 'wplc_offline_msg', stripslashes($wplc_settings['wplc_pro_offline2']));
    wp_localize_script('wplc-user-script', 'wplc_offline_msg3', stripslashes($wplc_settings['wplc_pro_offline3']));

    if (isset($wplc_settings['wplc_elem_trigger_id']) && trim($wplc_settings['wplc_elem_trigger_id']) !== "") {
        if (isset($wplc_settings['wplc_elem_trigger_action'])) {
            wp_localize_script('wplc-user-script', 'wplc_elem_trigger_action', stripslashes($wplc_settings['wplc_elem_trigger_action']));
        }
        if (isset($wplc_settings['wplc_elem_trigger_type'])) {
            wp_localize_script('wplc-user-script', 'wplc_elem_trigger_type', stripslashes($wplc_settings['wplc_elem_trigger_type']));
        }
        wp_localize_script('wplc-user-script', 'wplc_elem_trigger_id', stripslashes($wplc_settings['wplc_elem_trigger_id']));
    }

    $extra_data_array = array();
    $extra_data_array = apply_filters("wplc_filter_front_js_extra_data", $extra_data_array);
    wp_localize_script('wplc-user-script', 'wplc_extra_data', $extra_data_array);


    if (isset($_COOKIE['wplc_email']) && $_COOKIE['wplc_email'] != "") {
        $wplc_user_gravatar = sanitize_text_field(md5(strtolower(trim($_COOKIE['wplc_email']))));
    } else {
        $wplc_user_gravatar = "";
    }

    if ($wplc_user_gravatar != "") {
        $wplc_grav_image = "<img src='//www.gravatar.com/avatar/$wplc_user_gravatar?s=30' class='wplc-user-message-avatar' />";
    } else {
        $wplc_grav_image = "";
    }
    wp_localize_script('wplc-user-script', 'wplc_gravatar_image', $wplc_grav_image);

    $wplc_hide_chat = "";
    if (get_option('WPLC_HIDE_CHAT') == TRUE) {
        $wplc_hide_chat = "yes";
    } else {
        $wplc_hide_chat = null;
    }
    wp_localize_script('wplc-user-script', 'wplc_hide_chat', $wplc_hide_chat);


    wp_enqueue_script('jquery-ui-core', false, array('wplc-user-script'), false, false);
    wp_enqueue_script('jquery-ui-draggable', false, array('wplc-user-script'), false, false);

    do_action("wplc_hook_push_js_to_front");
}

if (function_exists('wplc_pro_user_top_js')) {
    add_action('wp_head', 'wplc_pro_user_top_js');
} else {
    add_action('wp_head', 'wplc_user_top_js');
}

function wplc_user_top_js() {

    if (function_exists('wplc_display_chat_contents')) {
        $display_contents = wplc_display_chat_contents();
    } else {
        $display_contents = 1;
    }
    if ($display_contents >= 1) {
        /* echo "<!-- DEFINING DO NOT CACHE -->";
          if (!defined('DONOTCACHEPAGE')) {
          define('DONOTCACHEPAGE', true);
          }
          if (!defined('DONOTCACHEDB')) {
          define('DONOTCACHEDB', true);
          }
         */
        $ajax_nonce = wp_create_nonce("wplc");
        $wplc_settings = get_option("WPLC_SETTINGS");
        $ajax_url = is_math_panel() ? "http://math.dicprj.moe/wp-admin/admin-ajax.php" : admin_url('admin-ajax.php');
        $wplc_ajax_url = apply_filters("wplc_filter_ajax_url", $ajax_url);
        ?>

        <script type="text/javascript">
        <?php if (!function_exists("wplc_register_pro_version")) { ?>
                var wplc_ajaxurl = '<?php echo $wplc_ajax_url; ?>';
        <?php } ?>
            var wplc_nonce = '<?php echo $ajax_nonce; ?>';
        </script>




        <?php
        $wplc_settings = get_option('WPLC_SETTINGS');
        if (isset($wplc_settings['wplc_theme'])) {
            $wplc_theme = $wplc_settings['wplc_theme'];
        } else {
            $wplc_theme = "theme-default";
        }
        if (isset($wplc_theme)) {

            if ($wplc_theme == 'theme-6') {
                /* custom */

                if (isset($wplc_settings["wplc_settings_color1"])) {
                    $wplc_settings_color1 = $wplc_settings["wplc_settings_color1"];
                } else {
                    $wplc_settings_color1 = "ED832F";
                }
                if (isset($wplc_settings["wplc_settings_color2"])) {
                    $wplc_settings_color2 = $wplc_settings["wplc_settings_color2"];
                } else {
                    $wplc_settings_color2 = "FFFFFF";
                }
                if (isset($wplc_settings["wplc_settings_color3"])) {
                    $wplc_settings_color3 = $wplc_settings["wplc_settings_color3"];
                } else {
                    $wplc_settings_color3 = "EEEEEE";
                }
                if (isset($wplc_settings["wplc_settings_color4"])) {
                    $wplc_settings_color4 = $wplc_settings["wplc_settings_color4"];
                } else {
                    $wplc_settings_color4 = "666666";
                }
                ?>
                <style>
                    .wplc-color-1 { color: #<?php echo $wplc_settings_color1; ?> !important; }
                    .wplc-color-2 { color: #<?php echo $wplc_settings_color2; ?> !important; }
                    .wplc-color-3 { color: #<?php echo $wplc_settings_color3; ?> !important; }
                    .wplc-color-4 { color: #<?php echo $wplc_settings_color4; ?> !important; }
                    .wplc-color-bg-1 { background-color: #<?php echo $wplc_settings_color1; ?> !important; }
                    .wplc-color-bg-2 { background-color: #<?php echo $wplc_settings_color2; ?> !important; }
                    .wplc-color-bg-3 { background-color: #<?php echo $wplc_settings_color3; ?> !important; }
                    .wplc-color-bg-4 { background-color: #<?php echo $wplc_settings_color4; ?> !important; }
                    .wplc-color-border-1 { border-color: #<?php echo $wplc_settings_color1; ?> !important; }
                    .wplc-color-border-2 { border-color: #<?php echo $wplc_settings_color2; ?> !important; }
                    .wplc-color-border-3 { border-color: #<?php echo $wplc_settings_color3; ?> !important; }
                    .wplc-color-border-4 { border-color: #<?php echo $wplc_settings_color4; ?> !important; }
                    .wplc-color-border-1:before { border-color: transparent #<?php echo $wplc_settings_color1; ?> !important; }
                    .wplc-color-border-2:before { border-color: transparent #<?php echo $wplc_settings_color2; ?> !important; }
                    .wplc-color-border-3:before { border-color: transparent #<?php echo $wplc_settings_color3; ?> !important; }
                    .wplc-color-border-4:before { border-color: transparent #<?php echo $wplc_settings_color4; ?> !important; }
                </style>

                <?php
            }
        }
    }
}

/**
 * Detect if the user is using blocked in the live chat settings 'blocked IP' section
 * @return void
 * @since  6.0.00
 * @author  Nick Duncan <nick@codecabin.co.za>
 */
function wplc_hook_control_banned_users() {
    if (function_exists('wplc_is_user_banned_basic')) {
        $user_banned = wplc_is_user_banned_basic();
    } else {
        $user_banned = 0;
    }
    if ($user_banned) {
        remove_action("wplc_hook_output_box_body", "wplc_hook_control_show_chat_box");
        remove_action("wplc_hook_output_box_footer", "wplc_action_control_hook_output_box_footer");
    }
}

/**
 * Detect if the user is using a mobile phone or not and decides to show the chat box depending on the admins settings
 * @return void
 * @since  6.0.00
 * @author  Nick Duncan <nick@codecabin.co.za>
 */
function wplc_hook_control_check_mobile() {
    $wplc_settings = get_option("WPLC_SETTINGS");

    $draw_box = false;

    if (!class_exists('Mobile_Detect')) {
        require_once (plugin_dir_path(__FILE__) . 'includes/Mobile_Detect.php');
    }

    $wplc_detect_device = new Mobile_Detect;
    $wplc_is_mobile = $wplc_detect_device->isMobile();

    if ($wplc_is_mobile && !isset($wplc_settings['wplc_enabled_on_mobile']) && $wplc_settings['wplc_enabled_on_mobile'] != 1) {
        return "";
    }

    if (function_exists('wplc_basic_hide_chat_when_offline')) {
        $wplc_hide_chat = wplc_basic_hide_chat_when_offline();
        if (!$wplc_hide_chat) {
            $draw_box = true;
        }
    } else {
        $draw_box = true;
    }
    if (!$draw_box) {
        remove_action("wplc_hook_output_box_body", "wplc_hook_control_show_chat_box");
        remove_action("wplc_hook_output_box_footer", "wplc_action_control_hook_output_box_footer");
    }
}

add_action("wplc_hook_output_box_footer", "wplc_action_control_hook_output_box_footer", 10, 1);

function wplc_action_control_hook_output_box_footer() {
    /* nothing here */
}

/**
 * Decides whether or not to show the chat box based on the main setting in the settings page
 * @return void
 * @since  6.0.00
 */
function wplc_hook_control_is_chat_enabled() {
    $wplc_settings = get_option("WPLC_SETTINGS");
    if ($wplc_settings["wplc_settings_enabled"] == 2) {
        remove_action("wplc_hook_output_box_body", "wplc_hook_control_show_chat_box");
        remove_action("wplc_hook_output_box_footer", "wplc_action_control_hook_output_box_footer");
    }
}

/**
 * Backwards compatibility for the control of the chat box
 * @return string
 * @since  6.0.00
 * @author  Nick Duncan - nick@codecabin.co.za
 */
function wplc_hook_control_show_chat_box() {
    if (function_exists("wplc_pro_version_control")) {
        global $wplc_pro_version;
        if (intval(str_replace(".", "", $wplc_pro_version)) < 5100) {
            echo wplc_output_box_ajax();
        } else {
            echo wplc_output_box_ajax_new();
        }
    } else {
        echo wplc_output_box_ajax_new();
    }
}

/* basic */
add_action("wplc_hook_output_box_header", "wplc_hook_control_banned_users");
add_action("wplc_hook_output_box_header", "wplc_hook_control_check_mobile");
add_action("wplc_hook_output_box_header", "wplc_hook_control_is_chat_enabled");

add_action("wplc_hook_output_box_body", "wplc_hook_control_show_chat_box");

/**
 * Build the chat box
 * @return void
 * @since  6.0.00
 * @author  Nick Duncan <nick@codecabin.co.za>
 */
function wplc_output_box_5100() {
    wplc_string_check();
    do_action("wplc_hook_output_box_header");
    do_action("wplc_hook_output_box_body");
    do_action("wplc_hook_output_box_footer");
}

/**
 * Filter to control the top MAIN DIV of the chat box
 * @param  array   $wplc_settings Live chat settings array
 * @return string               
 * @since  6.0.00
 * @author  Nick Duncan <nick@codecabin.co.za>
 */
function wplc_filter_control_live_chat_box_html_main_div_top($wplc_settings, $logged_in, $wplc_using_locale) {
    $ret_msg = "";
    $wplc_class = "";
//   $wplc_settings = get_option("WPLC_SETTINGS");

    if ($wplc_settings["wplc_settings_align"] == 1) {
        $original_pos = "bottom_left";
    } else if ($wplc_settings["wplc_settings_align"] == 2) {
        $original_pos = "bottom_right";
    } else if ($wplc_settings["wplc_settings_align"] == 3) {
        $original_pos = "left";
        $wplc_class = "wplc_left";
    } else if ($wplc_settings["wplc_settings_align"] == 4) {
        $original_pos = "right";
        $wplc_class = "wplc_right";
    }


    $animations = wplc_return_animations_basic();
    if ($animations) {
        isset($animations['animation']) ? $wplc_animation = $animations['animation'] : $wplc_animation = 'animation-4';
        isset($animations['starting_point']) ? $wplc_starting_point = $animations['starting_point'] : $wplc_starting_point = 'display: none;';
        isset($animations['box_align']) ? $wplc_box_align = $animations['box_align'] : $wplc_box_align = '';
    } else {

        if ($wplc_settings["wplc_settings_align"] == 1) {
            $original_pos = "bottom_left";
            $wplc_box_align = "left:100px; bottom:0px;";
        } else if ($wplc_settings["wplc_settings_align"] == 2) {
            $original_pos = "bottom_right";
            $wplc_box_align = "right:100px; bottom:0px;";
        } else if ($wplc_settings["wplc_settings_align"] == 3) {
            $original_pos = "left";
            $wplc_box_align = "left:0; bottom:100px;";
            $wplc_class = "wplc_left";
        } else if ($wplc_settings["wplc_settings_align"] == 4) {
            $original_pos = "right";
            $wplc_box_align = "right:0; bottom:100px;";
            $wplc_class = "wplc_right";
        }
    }


    $wplc_extra_attr = apply_filters("wplc_filter_chat_header_extra_attr", "");
    if (isset($wplc_settings['wplc_newtheme'])) {
        $wplc_newtheme = $wplc_settings['wplc_newtheme'];
    } else {
        
    }
    if (isset($wplc_newtheme)) {
        if ($wplc_newtheme == 'theme-1') {
            $wplc_theme_type = "classic";
        } else if ($wplc_newtheme == 'theme-2') {
            $wplc_theme_type = "modern";
        } else {
            $wplc_theme_type = "modern";
        }
    }

    if (isset($wplc_settings['wplc_newtheme']) && $wplc_settings['wplc_newtheme'] == "theme-2") {
        $hovercard_content = "<div class='wplc_hovercard_content_left'>" . apply_filters("wplc_filter_modern_theme_hovercard_content_left", "") . "</div><div class='wplc_hovercard_content_right'>" . apply_filters("wplc_filter_live_chat_box_html_1st_layer", wplc_filter_control_live_chat_box_html_1st_layer($wplc_settings, $logged_in, $wplc_using_locale)) . "</div>";
        $hovercard_content = apply_filters("wplc_filter_hovercard_content", $hovercard_content);

        $ret_msg .= "<div id='wplc_hovercard' style='display:none' class='" . $wplc_theme_type . "'>";
        $ret_msg .= "<div id='wplc_hovercard_min' class='wplc_button_standard wplc-color-border-1 wplc-color-bg-1'>" . __("close", "wplivechat") . "</div>";
        $ret_msg .= "<div id='wplc_hovercard_content'>" . apply_filters("wplc_filter_live_chat_box_pre_layer1", "") . $hovercard_content . "</div>";
        $ret_msg .= "<div id='wplc_hovercard_bottom'>" . apply_filters("wplc_filter_hovercard_bottom_before", "") . apply_filters("wplc_filter_live_chat_box_hover_html_start_chat_button", "", $wplc_settings, $logged_in, $wplc_using_locale) . "</div>";
        $ret_msg .= "</div>";
    } else if (isset($wplc_settings['wplc_newtheme']) && $wplc_settings['wplc_newtheme'] == "theme-1") {
        $hovercard_content = "<div class='wplc_hovercard_content_right'>" . apply_filters("wplc_filter_live_chat_box_html_1st_layer", wplc_filter_control_live_chat_box_html_1st_layer($wplc_settings, $logged_in, $wplc_using_locale, "wplc-color-4")) . "</div>";
        $hovercard_content = apply_filters("wplc_filter_hovercard_content", $hovercard_content);

        $ret_msg .= "<div id='wplc_hovercard' style='display:none' class='" . $wplc_theme_type . "'>";
        $ret_msg .= "<div id='wplc_hovercard_min' class='wplc_button_standard wplc-color-border-1 wplc-color-bg-1'>" . __("close", "wplivechat") . "</div>";
        $ret_msg .= "<div id='wplc_hovercard_content'>" . apply_filters("wplc_filter_live_chat_box_pre_layer1", "") . $hovercard_content . "</div>";
        $ret_msg .= "<div id='wplc_hovercard_bottom'>" . apply_filters("wplc_filter_hovercard_bottom_before", "") . apply_filters("wplc_filter_live_chat_box_hover_html_start_chat_button", "", $wplc_settings, $logged_in, $wplc_using_locale) . "</div>";
        $ret_msg .= "</div>";
    }

    $ret_msg .= "<div id=\"wp-live-chat\" wplc_animation=\"" . $wplc_animation . "\" style=\"" . $wplc_starting_point . " " . $wplc_box_align . ";\" class=\"" . $wplc_theme_type . " " . $wplc_class . " wplc_close\" original_pos=\"" . $original_pos . "\" " . $wplc_extra_attr . " > ";
    return $ret_msg;
}

add_filter("wplc_filter_modern_theme_hovercard_content_left", "wplc_filter_control_modern_theme_hovercard_content_left", 10, 1);

function wplc_filter_control_modern_theme_hovercard_content_left($msg) {

    $msg .= "<div class='wplc_left_logo' style='background:url(" . plugins_url('images/iconmicro.png', __FILE__) . ") no-repeat; background-size: cover;'></div>";
    $msg = apply_filters("wplc_filter_microicon", $msg);
    return $msg;
}

/**
 * Filter to control the top HEADER DIV of the chat box
 * @param  array   $wplc_settings Live chat settings array
 * @return string               
 * @since  6.0.00
 * @author  Nick Duncan <nick@codecabin.co.za>
 */
function wplc_filter_control_live_chat_box_html_header_div_top($wplc_settings) {

    $ret_msg = "";

    $current_theme = isset($wplc_settings['wplc_newtheme']) ? $wplc_settings['wplc_newtheme'] : "";
    if ($current_theme === "theme-1") {
        $ret_msg .= apply_filters("wplc_filter_chat_header_above", "", $wplc_settings); //Ratings/Social Icon Filter
    }

    $ret_msg .= "<div id=\"wp-live-chat-header\" class='wplc-color-bg-1 wplc-color-2'>";
    $ret_msg .= apply_filters("wplc_filter_chat_header_under", "", $wplc_settings);
    return $ret_msg;
}

add_filter("wplc_filter_chat_header_under", "wplc_filter_control_chat_header_under", 1, 2);

function wplc_filter_control_chat_header_under($ret_msg, $wplc_settings) {
    if (isset($wplc_settings['wplc_newtheme']) && $wplc_settings['wplc_newtheme'] == "theme-2") {
        $ret_msg .= "<style>#wp-live-chat-header { background:url(" . plugins_url('images/chaticon.png', __FILE__) . ") no-repeat; background-size: cover; }</style>";
        if (function_exists("wplc_acbc_filter_control_chat_header_under")) {
            remove_filter("wplc_filter_chat_header_under", "wplc_acbc_filter_control_chat_header_under");
        }
    }
    return $ret_msg;
}

/**
 * Filter to control the user details section - custom fields coming soon
 * @param  array   $wplc_settings Live chat settings array
 * @return string               
 * @since  6.0.00
 * @author  Nick Duncan <nick@codecabin.co.za>
 */
function wplc_filter_control_live_chat_box_html_ask_user_detail($wplc_settings) {
    $ret_msg = "";
    if (isset($wplc_settings['wplc_loggedin_user_info']) && $wplc_settings['wplc_loggedin_user_info'] == 1) {
        $wplc_use_loggedin_user_details = 1;
    } else {
        $wplc_use_loggedin_user_details = 0;
    }

    $wplc_loggedin_user_name = "";
    $wplc_loggedin_user_email = "";

    if ($wplc_use_loggedin_user_details == 1) {
        global $current_user;

        if ($current_user->data != null) {
            //Logged in. Get name and email
            $wplc_loggedin_user_name = $current_user->user_nicename;
            $wplc_loggedin_user_email = $current_user->user_email;
        }
    } else {
        $wplc_loggedin_user_name = '';
        $wplc_loggedin_user_email = '';
    }

    if (isset($wplc_settings['wplc_require_user_info']) && $wplc_settings['wplc_require_user_info'] == 1) {
        $wplc_ask_user_details = 1;
    } else {
        $wplc_ask_user_details = 0;
    }

    if ($wplc_ask_user_details == 1) {
        //Ask the user to enter name and email

        $ret_msg .= "<input type=\"text\" name=\"wplc_name\" id=\"wplc_name\" value='" . $wplc_loggedin_user_name . "' placeholder=\"" . __("Name", "wplivechat") . "\" />";
        $ret_msg .= "<input type=\"text\" name=\"wplc_email\" id=\"wplc_email\" wplc_hide=\"0\" value=\"" . $wplc_loggedin_user_email . "\" placeholder=\"" . __("Email", "wplivechat") . "\"  />";
    } else {
        //Dont ask the user
        $ret_msg .= "<div style=\"padding: 7px; text-align: center;\">";
        if (isset($wplc_settings['wplc_user_alternative_text'])) {
            $ret_msg .= html_entity_decode(stripslashes($wplc_settings['wplc_user_alternative_text']));
        }
        $ret_msg .= '</div>';

        $wplc_random_user_number = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);
        //$wplc_loggedin_user_email = $wplc_random_user_number."@".$wplc_random_user_number.".com";
        if ($wplc_loggedin_user_name != '') {
            $wplc_lin = $wplc_loggedin_user_name;
        } else {
            $wplc_lin = 'user' . $wplc_random_user_number;
        }
        if ($wplc_loggedin_user_email != '' && $wplc_loggedin_user_email != null) {
            $wplc_lie = $wplc_loggedin_user_email;
        } else {
            $wplc_lie = $wplc_random_user_number . '@' . $wplc_random_user_number . '.com';
        }
        $ret_msg .= "<input type=\"hidden\" name=\"wplc_name\" id=\"wplc_name\" value=\"" . $wplc_lin . "\" />";
        $ret_msg .= "<input type=\"hidden\" name=\"wplc_email\" id=\"wplc_email\" wplc_hide=\"1\" value=\"" . $wplc_lie . "\" />";
    }
    return $ret_msg;
}

/**
 * Filter to control the start chat button
 * @param  array   $wplc_settings Live chat settings array
 * @return string               
 * @since  6.0.00
 * @author  Nick Duncan <nick@codecabin.co.za>
 */
function wplc_filter_control_live_chat_box_html_start_chat_button($wplc_settings, $wplc_using_locale) {
    $wplc_sst_1 = __('Start chat', 'wplivechat');
    if (!isset($wplc_settings['wplc_pro_sst1']) || $wplc_settings['wplc_pro_sst1'] == "") {
        $wplc_settings['wplc_pro_sst1'] = $wplc_sst_1;
    }
    $text = ($wplc_using_locale ? $wplc_sst_1 : stripslashes($wplc_settings['wplc_pro_sst1']));
    return "<button id=\"wplc_start_chat_btn\" type=\"button\" class='wplc-color-bg-1 wplc-color-2'>$text</button>";
}

/**
 * Filter to control the hover card start chat button
 * @param  array   $wplc_settings Live chat settings array
 * @return string               
 * @since  6.1.00
 * @author  Nick Duncan <nick@codecabin.co.za>
 */
add_filter("wplc_filter_live_chat_box_hover_html_start_chat_button", "wplc_filter_control_live_chat_box_html_hovercard_chat_button", 10, 4);

function wplc_filter_control_live_chat_box_html_hovercard_chat_button($content, $wplc_settings, $logged_in, $wplc_using_locale) {
    if ($logged_in) {
        $wplc_sst_1 = __('Start chat', 'wplivechat');

        if (!isset($wplc_settings['wplc_pro_sst1']) || $wplc_settings['wplc_pro_sst1'] == "") {
            $wplc_settings['wplc_pro_sst1'] = $wplc_sst_1;
        }
        $text = ($wplc_using_locale ? $wplc_sst_1 : stripslashes($wplc_settings['wplc_pro_sst1']));
        return "<button id=\"speeching_button\" type=\"button\"  class='wplc-color-bg-1 wplc-color-2'>$text</button>";
    } else {
        $wplc_sst_1 = __('Leave a message', 'wplivechat');
        return "<button id=\"speeching_button\" type=\"button\"  class='wplc-color-bg-1 wplc-color-2'>$wplc_sst_1</button>";
    }
}

/**
 * Filter to control the offline message button
 * @param  array   $wplc_settings Live chat settings array
 * @return string               
 * @since  6.0.00
 * @author  Nick Duncan <nick@codecabin.co.za>
 */
function wplc_filter_control_live_chat_box_html_send_offline_message_button($wplc_settings) {
    $wplc_settings = get_option('WPLC_SETTINGS');
    if (isset($wplc_settings['wplc_theme'])) {
        $wplc_theme = $wplc_settings['wplc_theme'];
    } else {
        
    }

    if (isset($wplc_theme)) {
        if ($wplc_theme == 'theme-1') {
            $wplc_settings_fill = "#DB0000";
            $wplc_settings_font = "#FFFFFF";
        } else if ($wplc_theme == 'theme-2') {
            $wplc_settings_fill = "#000000";
            $wplc_settings_font = "#FFFFFF";
        } else if ($wplc_theme == 'theme-3') {
            $wplc_settings_fill = "#DB30B3";
            $wplc_settings_font = "#FFFFFF";
        } else if ($wplc_theme == 'theme-4') {
            $wplc_settings_fill = "#1A14DB";
            $wplc_settings_font = "#F7FF0F";
        } else if ($wplc_theme == 'theme-5') {
            $wplc_settings_fill = "#3DCC13";
            $wplc_settings_font = "#FF0808";
        } else if ($wplc_theme == 'theme-6') {
            if ($wplc_settings["wplc_settings_fill"]) {
                $wplc_settings_fill = "#" . $wplc_settings["wplc_settings_fill"];
            } else {
                $wplc_settings_fill = "#ec832d";
            }
            if ($wplc_settings["wplc_settings_font"]) {
                $wplc_settings_font = "#" . $wplc_settings["wplc_settings_font"];
            } else {
                $wplc_settings_font = "#FFFFFF";
            }
        } else {
            if ($wplc_settings["wplc_settings_fill"]) {
                $wplc_settings_fill = "#" . $wplc_settings["wplc_settings_fill"];
            } else {
                $wplc_settings_fill = "#ec832d";
            }
            if ($wplc_settings["wplc_settings_font"]) {
                $wplc_settings_font = "#" . $wplc_settings["wplc_settings_font"];
            } else {
                $wplc_settings_font = "#FFFFFF";
            }
        }
    } else {
        if ($wplc_settings["wplc_settings_fill"]) {
            $wplc_settings_fill = "#" . $wplc_settings["wplc_settings_fill"];
        } else {
            $wplc_settings_fill = "#ec832d";
        }
        if ($wplc_settings["wplc_settings_font"]) {
            $wplc_settings_font = "#" . $wplc_settings["wplc_settings_font"];
        } else {
            $wplc_settings_font = "#FFFFFF";
        }
    }
    $ret_msg = "<input id=\"wplc_na_msg_btn\" type=\"button\" value=\"" . __("Send message", "wplivechat") . "\" style=\"background-color: " . $wplc_settings_fill . " !important; color: " . $wplc_settings_font . " !important;\"/>";
    return $ret_msg;
}

/**
 * Filter to control the 2nd layer of the chat window (online/offline)
 * @param  array   $wplc_settings Live chat settings array
 * @param  bool    $logged_in     Is the user logged in or not
 * @return string               
 * @since  6.0.00
 * @author  Nick Duncan <nick@codecabin.co.za>
 */
function wplc_filter_control_live_chat_box_html_2nd_layer($wplc_settings, $logged_in, $wplc_using_locale) {
    if ($logged_in) {
        $wplc_intro = __('Hello. Please input your details so that I may help you.', 'wplivechat');
        if (!isset($wplc_settings['wplc_pro_intro']) || $wplc_settings['wplc_pro_intro'] == "") {
            $wplc_settings['wplc_pro_intro'] = $wplc_intro;
        }
        $text = ($wplc_using_locale ? $wplc_intro : stripslashes($wplc_settings['wplc_pro_intro']));

        $ret_msg = "<div id=\"wp-live-chat-2-inner\">";
        $ret_msg .= " <div id=\"wp-live-chat-2-info\" class='wplc-color-bg-2 wplc-color-4'>";
        $ret_msg .= "   <strong>" . $text . "</strong>";
        $ret_msg .= " </div>";
        $ret_msg .= apply_filters("wplc_filter_live_chat_box_html_ask_user_details", wplc_filter_control_live_chat_box_html_ask_user_detail($wplc_settings));
        $ret_msg .= apply_filters("wplc_filter_live_chat_box_html_start_chat_button", wplc_filter_control_live_chat_box_html_start_chat_button($wplc_settings, $wplc_using_locale));
        $ret_msg .= "</div>";
    } else {
        /* admin not logged in, show offline messages */
        $wplc_offline = __("We are currently offline. Please leave a message and we'll get back to you shortly.", "wplivechat");
        $text = ($wplc_using_locale ? $wplc_offline : stripslashes($wplc_settings['wplc_pro_offline1']));

        $ret_msg = "<div id=\"wp-live-chat-2-info\">";
        $ret_msg .= $text;
        $ret_msg .= "</div>";
        $ret_msg .= "<div id=\"wplc_message_div\">";
        $ret_msg .= "<input type=\"text\" name=\"wplc_name\" id=\"wplc_name\" value=\"\" placeholder=\"" . __("Name", "wplivechat") . "\" />";
        $ret_msg .= "<input type=\"text\" name=\"wplc_email\" id=\"wplc_email\" value=\"\"  placeholder=\"" . __("Email", "wplivechat") . "\" />";
        $ret_msg .= "<textarea name=\"wplc_message\" id=\"wplc_message\" placeholder=\"" . __("Message", "wplivechat") . "\"></textarea>";

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip_address = $_SERVER['REMOTE_ADDR'];
        }

        if (isset($wplc_settings['wplc_record_ip_address']) && $wplc_settings['wplc_record_ip_address'] == 1) {
            $offline_ip_address = $ip_address;
        } else {
            $offline_ip_address = "";
        }

        $ret_msg .= "<input type=\"hidden\" name=\"wplc_ip_address\" id=\"wplc_ip_address\" value=\"" . $offline_ip_address . "\" />";
        $ret_msg .= "<input type=\"hidden\" name=\"wplc_domain_offline\" id=\"wplc_domain_offline\" value=\"" . get_option('siteurl') . "\" />";
        $ret_msg .= apply_filters("wplc_filter_live_chat_box_html_send_offline_message_button", wplc_filter_control_live_chat_box_html_send_offline_message_button($wplc_settings));
        $ret_msg .= "</div>";
    }
    return $ret_msg;
}

/**
 * Filter to control the 3rd layer of the chat window
 * @param  array $wplc_settings live chat settings array
 * @return string               
 * @since  6.0.00
 * @author  Nick Duncan <nick@codecabin.co.za>
 */
function wplc_filter_control_live_chat_box_html_3rd_layer($wplc_settings, $wplc_using_locale) {

    $wplc_sst_2 = __('Connecting. Please be patient...', 'wplivechat');
    if (!isset($wplc_settings['wplc_pro_sst2']) || $wplc_settings['wplc_pro_sst2'] == "") {
        $wplc_settings['wplc_pro_sst2'] = $wplc_sst_2;
    }
    $text = ($wplc_using_locale ? $wplc_sst_2 : stripslashes($wplc_settings['wplc_pro_sst2']));

    $ret_msg = "<p class=''wplc-color-4>" . $text . "</p>";
    return $ret_msg;
}

add_filter("wplc_filter_live_chat_box_above_main_div", "wplc_filter_control_live_chat_box_above_main_div", 10, 2);

function wplc_filter_control_live_chat_box_above_main_div($msg, $wplc_settings) {
    if (isset($wplc_settings['wplc_newtheme']) && $wplc_settings['wplc_newtheme'] == "theme-2") {
        $msg .= "<div id='wplc_chatbox_header' class='wplc-color-bg-2 wplc-color-4'></div>";
    }
    return $msg;
}

/**
 * Filter to control the 4th layer of the chat window
 * @param  array $wplc_settings live chat settings array
 * @return string               
 * @since  6.0.00
 * @author  Nick Duncan <nick@codecabin.co.za>
 */
function wplc_filter_control_live_chat_box_html_4th_layer($wplc_settings, $wplc_using_locale) {
    $wplc_enter = __('Connecting. Please be patient...', 'wplivechat');
    if (!isset($wplc_settings['wplc_user_enter']) || $wplc_settings['wplc_user_enter'] == "") {
        $wplc_settings['wplc_pro_sst2'] = $wplc_enter;
    }
    $text = ($wplc_using_locale ? $wplc_enter : stripslashes($wplc_settings['wplc_user_enter']));

    $wplc_welcome = __('Welcome. How may I help you?', 'wplivechat');
    if (!isset($wplc_settings['wplc_user_welcome_chat']) || $wplc_settings['wplc_user_welcome_chat'] == "") {
        $wplc_settings['wplc_user_welcome_chat'] = $wplc_welcome;
    }
    $text2 = ($wplc_using_locale ? $wplc_welcome : stripslashes($wplc_settings['wplc_user_welcome_chat']));







    $ret_msg = "<div id=\"wplc_sound_update\" style=\"height:0; width:0; display:none; border:0;\"></div>";

    $ret_msg .= apply_filters("wplc_filter_live_chat_box_above_main_div", "", $wplc_settings);


    $ret_msg .= "<div id=\"wplc_chatbox\">";
//  $ret_msg .= "<span class='wplc-admin-message'>";
//  $ret_msg .= $text2;
//  $ret_msg .= "</span>";
//  $ret_msg .= "<br />";
//  $ret_msg .= "<div class='wplc-clear-float-message'></div>";
    $ret_msg .= "</div>";

    $ret_msg .= "<div id='wplc_user_message_div'>";
    $ret_msg .= "<p style=\"text-align:center; font-size:11px;\" id='wplc_msg_notice'>" . $text . "</p>";

    //Editor Controls
    $ret_msg .= apply_filters("wplc_filter_chat_text_editor", "");

    $ret_msg .= "<p>";
    $ret_msg .= "<input type=\"text\" name=\"wplc_chatmsg\" id=\"wplc_chatmsg\" value=\"\" />";

    //Upload Controls
    $ret_msg .= apply_filters("wplc_filter_chat_upload", "");

    $ret_msg .= "<input type=\"hidden\" name=\"wplc_cid\" id=\"wplc_cid\" value=\"\" />";
    $ret_msg .= "<input id=\"wplc_send_msg\" type=\"button\" value=\"" . __("Send", "wplivechat") . "\" style=\"display:none;\" />";
    $ret_msg .= "</p>";

    $current_theme = isset($wplc_settings['wplc_newtheme']) ? $wplc_settings['wplc_newtheme'] : "";
    if ($current_theme === "theme-2") {
        $ret_msg .= apply_filters("wplc_filter_chat_4th_layer_below_input", "", $wplc_settings); //Ratings/Social Icon Filter
    }

    $ret_msg .= "</div>";
    $ret_msg .= "</div>";
    return $ret_msg;
}

/**
 * Filter to control the 1st layer of the chat window
 * @param  array $wplc_settings        live chat settings array
 * @param  bool  $logged_in            Is the admin logged in or not
 * @param  bool  $wplc_using_locale    Are they using a localization plugin
 * @return string               
 * @since  6.0.00
 * @author  Nick Duncan <nick@codecabin.co.za>
 */
function wplc_filter_control_live_chat_box_html_1st_layer($wplc_settings, $logged_in, $wplc_using_locale, $class_override = false) {

    $ret_msg = "<div id='wplc_first_message'>";
    if ($logged_in) {
        $wplc_fst_1 = __('Questions?', 'wplivechat');
        $wplc_fst_2 = __('Chat with us', 'wplivechat');
        if (isset($wplc_settings['wplc_newtheme']) && $wplc_settings['wplc_newtheme'] == "theme-2") {
            $coltheme = "wplc-color-4";
        } else {
            $coltheme = "wplc-color-2";
        }

        if ($class_override) {
            //Override color class
            $coltheme = $class_override;
        }

        $wplc_tl_msg = "<div class='$coltheme'><strong>" . ($wplc_using_locale ? $wplc_fst_1 : stripslashes($wplc_settings['wplc_pro_fst1'])) . "</strong> " . ( $wplc_using_locale ? $wplc_fst_2 : stripslashes($wplc_settings['wplc_pro_fst2'])) . "</div>";

        $ret_msg .= $wplc_tl_msg;
    } else {
        $wplc_na = __('Chat offline. Leave a message', 'wplivechat');
        $wplc_tl_msg = "<span class='wplc_offline'>" . ($wplc_using_locale ? $wplc_na : stripslashes($wplc_settings['wplc_pro_na'])) . "</span>";
        $ret_msg .= $wplc_tl_msg;
    }
    $ret_msg .= "</div>";



    return $ret_msg;
}

/**
 * Build the initiate teaser
 * @return void
 * @since  6.0.00
 * @author  Nick Duncan <nick@codecabin.co.za>
 */
add_filter('wplc_filter_list_chats_actions', 'wplc_initiate_chat_button', 12, 3);

function wplc_initiate_chat_button($actions, $result, $post_data) {


    if (intval($result->status) == 5) {
        $actions = "<a href=\"javascript:void(0);\" id=\"wplc_initiate_chat\" class=\"wplc_initiate_chat button button-secondary\">" . __("Initiate Chat", "wplivechat") . "</a>";
    }
    return $actions;
}

add_filter("wplc_loggedin_filter", "wplc_filter_control_loggedin", 10, 1);

function wplc_filter_control_loggedin($logged_in) {
    $wplc_is_admin_logged_in = get_transient("wplc_is_admin_logged_in");
    if (!function_exists("wplc_register_pro_version") && $wplc_is_admin_logged_in != 1) {
        $logged_in = false;
    } else {
        $logged_in = true;
    }
    $logged_in_checks = apply_filters("wplc_filter_is_admin_logged_in", array());
    /* if we are logged in ANYWHERE, set this to true */
    foreach ($logged_in_checks as $key => $val) {
        if ($val) {
            $logged_in = true;
            break;
        }
    }
    $logged_in_via_app = false;
    if (isset($logged_in_checks['app']) && $logged_in_checks['app'] == true) {
        $logged_in_via_app = true;
    }
    $logged_in = apply_filters("wplc_final_loggedin_control", $logged_in, $logged_in_via_app);
    /* admin is using the basic version and is logged in */
    if ($wplc_is_admin_logged_in) {
        $logged_in = true;
    }


    return $logged_in;
}

/**
 * The function that builds the chat box
 * @return void
 * @since  6.0.00
 * @author  Nick Duncan <nick@codecabin.co.za>
 */
function wplc_output_box_ajax_new() {


    $ret_msg = array();
    $logged_in = false;

    $wplc_settings = get_option("WPLC_SETTINGS");

    if (isset($wplc_settings['wplc_using_localization_plugin']) && $wplc_settings['wplc_using_localization_plugin'] == 1) {
        $wplc_using_locale = true;
    } else {
        $wplc_using_locale = false;
    }


    $logged_in = apply_filters("wplc_loggedin_filter", false);


    $wplc_settings = get_option('WPLC_SETTINGS');
    $ret_msg['cbox'] = apply_filters("wplc_theme_control", $wplc_settings, $logged_in, $wplc_using_locale);
    $ret_msg['online'] = $logged_in;

    global $wplc_pro_version;
    $wplc_ver = str_replace('.', '', $wplc_pro_version);
    $checker = intval($wplc_ver);

    if (function_exists("wplc_pro_version_control")) {
        if ($checker < 6000) {
            /* backwards compatibilitty for the old pro version */
            return json_encode($ret_msg['cbox']);
        } else {
            return json_encode($ret_msg);
        }
    } else {
        return json_encode($ret_msg);
    }
}

function wplc_return_default_theme($wplc_settings, $logged_in, $wplc_using_locale) {
    $ret_msg = apply_filters("wplc_filter_live_chat_box_html_main_div_top", wplc_filter_control_live_chat_box_html_main_div_top($wplc_settings, $logged_in, $wplc_using_locale));
    $ret_msg .= "<div class=\"wp-live-chat-wraper\">";
    $ret_msg .= apply_filters("wplc_filter_live_chat_box_html_header_div_top", wplc_filter_control_live_chat_box_html_header_div_top($wplc_settings));
    $ret_msg .= " <i id=\"wp-live-chat-minimize\" class=\"fa fa-minus wplc-color-bg-2 wplc-color-1\" style=\"display:none;\"></i>";
    $ret_msg .= " <i id=\"wp-live-chat-close\" class=\"fa fa-times\" style=\"display:none;\" ></i>";
    $ret_msg .= " <div id=\"wp-live-chat-1\" >";
    $ret_msg .= apply_filters("wplc_filter_live_chat_box_html_1st_layer", wplc_filter_control_live_chat_box_html_1st_layer($wplc_settings, $logged_in, $wplc_using_locale));
    $ret_msg .= " </div>";
    $ret_msg .= " </div>";
    $ret_msg .= " <div id=\"wp-live-chat-2\" style=\"display:none;\">";
    $ret_msg .= apply_filters("wplc_filter_live_chat_box_survey", "");
    $ret_msg .= apply_filters("wplc_filter_live_chat_box_html_2nd_layer", wplc_filter_control_live_chat_box_html_2nd_layer($wplc_settings, $logged_in, $wplc_using_locale));
    $ret_msg .= " </div>";
    $ret_msg .= " <div id=\"wp-live-chat-3\" style=\"display:none;\">";
    $ret_msg .= apply_filters("wplc_filter_live_chat_box_html_3rd_layer", wplc_filter_control_live_chat_box_html_3rd_layer($wplc_settings, $wplc_using_locale));
    $ret_msg .= " </div>";
    $ret_msg .= " <div id=\"wp-live-chat-react\" style=\"display:none;\">";
    $ret_msg .= "   <p>" . __("Reactivating your previous chat...", "wplivechat") . "</p>";
    $ret_msg .= " </div>";
    $ret_msg .= " <div id=\"wp-live-chat-4\" style=\"display:none;\">";
    $ret_msg .= apply_filters("wplc_filter_live_chat_box_html_4th_layer", wplc_filter_control_live_chat_box_html_4th_layer($wplc_settings, $wplc_using_locale));
    $ret_msg .= "</div>";
    $ret_msg .= " <div id=\"wplc-extra-div\" style=\"display:none;\">";
    $ret_msg .= apply_filters("wplc_filter_wplc_extra_div", "", $wplc_settings, $wplc_using_locale);
    $ret_msg .= "</div>";
    $ret_msg .= "</div>";
    return $ret_msg;
}

add_filter("wplc_theme_control", "wplc_theme_control_function", 10, 3);

function wplc_theme_control_function($wplc_settings, $logged_in, $wplc_using_locale) {

    if (!$wplc_settings) {
        return "";
    }
    if (isset($wplc_settings['wplc_newtheme'])) {
        $wplc_newtheme = $wplc_settings['wplc_newtheme'];
    } else {
        
    }

    $default_theme = wplc_return_default_theme($wplc_settings, $logged_in, $wplc_using_locale);
    if (isset($wplc_newtheme)) {







        if ($wplc_newtheme == 'theme-1') {
            $ret_msg = $default_theme;
        } else if ($wplc_newtheme == 'theme-2') {
            $ret_msg = apply_filters("wplc_filter_live_chat_box_html_main_div_top", wplc_filter_control_live_chat_box_html_main_div_top($wplc_settings, $logged_in, $wplc_using_locale));
            $ret_msg .= "<div class=\"wp-live-chat-wraper\">";
            $ret_msg .= apply_filters("wplc_filter_live_chat_box_html_header_div_top", wplc_filter_control_live_chat_box_html_header_div_top($wplc_settings));
            $ret_msg .= " </div>";
            $ret_msg .= " <div id=\"wp-live-chat-2\" style=\"display:none;\">";
            $ret_msg .= " <i id=\"wp-live-chat-minimize\" class=\"fa fa-minus wplc-color-bg-1 wplc-color-2\" style=\"display:none;\" ></i>";
            $ret_msg .= " <i id=\"wp-live-chat-close\" class=\"fa fa-times\" style=\"display:none;\" ></i>";
            $ret_msg .= " <div id=\"wplc-extra-div\" style=\"display:none;\">";
            $ret_msg .= apply_filters("wplc_filter_wplc_extra_div", "", $wplc_settings, $wplc_using_locale);
            $ret_msg .= "</div>";
            $ret_msg .= " <div id='wp-live-chat-inner-container'>";
            $ret_msg .= " <div id='wp-live-chat-inner'>";
            $ret_msg .= "   <div id=\"wp-live-chat-1\" >";
            $ret_msg .= apply_filters("wplc_filter_live_chat_box_html_1st_layer", wplc_filter_control_live_chat_box_html_1st_layer($wplc_settings, $logged_in, $wplc_using_locale));
            $ret_msg .= "   </div>";
            $ret_msg .= apply_filters("wplc_filter_live_chat_box_html_2nd_layer", wplc_filter_control_live_chat_box_html_2nd_layer($wplc_settings, $logged_in, $wplc_using_locale));
            $ret_msg .= " </div>";
            $ret_msg .= " <div id=\"wp-live-chat-react\" style=\"display:none;\">";
            $ret_msg .= "   <p>" . __("Reactivating your previous chat...", "wplivechat") . "</p>";
            $ret_msg .= " </div>";
            $ret_msg .= " </div>";
            $ret_msg .= "   <div id=\"wp-live-chat-3\" style=\"display:none;\">";
            $ret_msg .= apply_filters("wplc_filter_live_chat_box_html_3rd_layer", wplc_filter_control_live_chat_box_html_3rd_layer($wplc_settings, $wplc_using_locale));
            $ret_msg .= "   </div>";
            $ret_msg .= "   <div id=\"wp-live-chat-4\" style=\"display:none;\">";
            $ret_msg .= apply_filters("wplc_filter_live_chat_box_html_4th_layer", wplc_filter_control_live_chat_box_html_4th_layer($wplc_settings, $wplc_using_locale));
            $ret_msg .= "   </div>";
            $ret_msg .= " </div>";
            $ret_msg .= "</div>";
        } else {
            $ret_msg = $default_theme;
        }
    } else {
        $ret_msg = $default_theme;
    }

    return $ret_msg;
}

/**
 * THIS FUNCTION ONLY RUNS IF THE PRO VERSION IS LESS THAN 5.0.1
 * The new method is being handled with ajax
 * @return void
 */
function wplc_display_box() {

    global $wplc_pro_version;
    $wplc_ver = str_replace('.', '', $wplc_pro_version);
    $checker = intval($wplc_ver);

    if (function_exists("wplc_pro_version_control") && ($checker <= 501 || $checker == 4410)) {
        /* prior to latest pro version with ajax handling */

        if (function_exists('wplc_display_chat_contents')) {
            $display_contents = wplc_display_chat_contents();
        } else {
            $display_contents = 1;
        }

        if (function_exists('wplc_is_user_banned_basic')) {
            $user_banned = wplc_is_user_banned_basic();
        } else {
            $user_banned = 0;
        }
        if ($display_contents && $user_banned == 0) {
            $wplc_is_admin_logged_in = get_transient("wplc_is_admin_logged_in");
            if ($wplc_is_admin_logged_in != 1) {
                echo "<!-- wplc a-n-c -->";
            }

            /* do not show if pro is outdated */
            global $wplc_pro_version;
            if (isset($wplc_pro_version)) {
                $float_version = floatval($wplc_pro_version);
                if ($float_version < 4 || $wplc_pro_version == "4.1.0" || $wplc_pro_version == "4.1.1") {
                    return;
                }
            }

            if (function_exists("wplc_register_pro_version")) {
                $wplc_settings = get_option("WPLC_SETTINGS");
                if (!class_exists('Mobile_Detect')) {
                    require_once (plugin_dir_path(__FILE__) . 'includes/Mobile_Detect.php');
                }
                $wplc_detect_device = new Mobile_Detect;
                $wplc_is_mobile = $wplc_detect_device->isMobile();
                if ($wplc_is_mobile && !isset($wplc_settings['wplc_enabled_on_mobile']) && $wplc_settings['wplc_enabled_on_mobile'] != 1) {
                    return;
                }
                if (function_exists('wplc_basic_hide_chat_when_offline')) {
                    $wplc_hide_chat = wplc_basic_hide_chat_when_offline();
                    if (!$wplc_hide_chat) {
                        wplc_pro_draw_user_box();
                    }
                } else {
                    wplc_pro_draw_user_box();
                }
            } else {
                wplc_draw_user_box();
            }
        }
    }
}

function wplc_display_box_ajax() {

    if (function_exists('wplc_display_chat_contents')) {
        $display_contents = wplc_display_chat_contents();
    } else {
        $display_contents = 1;
    }

    if (function_exists('wplc_is_user_banned')) {
        $user_banned = wplc_is_user_banned();
    } else if (function_exists('wplc_is_user_banned')) {
        $user_banned = wplc_is_user_banned_basic();
    } else {
        $user_banned = 0;
    }
    if ($display_contents && $user_banned == 0) {
        $wplc_is_admin_logged_in = get_transient("wplc_is_admin_logged_in");
        if ($wplc_is_admin_logged_in != 1) {
            return "";
        }

        /* do not show if pro is outdated */
        global $wplc_pro_version;
        if (isset($wplc_pro_version)) {
            $float_version = floatval($wplc_pro_version);
            if ($float_version < 4 || $wplc_pro_version == "4.1.0" || $wplc_pro_version == "4.1.1") {
                return;
            }
        }

        if (function_exists("wplc_register_pro_version")) {
            $wplc_settings = get_option("WPLC_SETTINGS");
            if (!class_exists('Mobile_Detect')) {
                require_once (plugin_dir_path(__FILE__) . 'includes/Mobile_Detect.php');
            }
            $wplc_detect_device = new Mobile_Detect;
            $wplc_is_mobile = $wplc_detect_device->isMobile();
            if ($wplc_is_mobile && !isset($wplc_settings['wplc_enabled_on_mobile']) && $wplc_settings['wplc_enabled_on_mobile'] != 1) {
                return;
            }
            if (function_exists('wplc_basic_hide_chat_when_offline')) {
                $wplc_hide_chat = wplc_basic_hide_chat_when_offline();
                if (!$wplc_hide_chat) {
                    wplc_pro_draw_user_box();
                }
            } else {
                wplc_pro_draw_user_box();
            }
        } else {
            wplc_draw_user_box();
        }
    }
}

function wplc_admin_display_chat($cid) {
    global $wpdb;
    global $wplc_tblname_msgs;
    $results = $wpdb->get_results(
            "
        SELECT *
        FROM $wplc_tblname_msgs
        WHERE `chat_sess_id` = '$cid'
        ORDER BY `timestamp` DESC
        LIMIT 0, 100
        "
    );
    foreach ($results as $result) {
        $from = $result->msgfrom;
        $msg = stripslashes($result->msg);
        $msg_hist .= "$from: $msg<br />";
    }
    echo $msg_hist;
}

function wplc_admin_accept_chat($cid) {
    $user_ID = get_current_user_id();
    wplc_change_chat_status(sanitize_text_field($cid), 3, $user_ID);
    return true;
}

add_action('admin_head', 'wplc_update_chat_statuses');


add_action("wplc_hook_superadmin_head", "wplc_hook_control_superadmin_head", 10);

function wplc_hook_control_superadmin_head() {
    $wplc_current_user = get_current_user_id();

    if (get_user_meta($wplc_current_user, 'wplc_ma_agent', true)) {
        $ajax_nonce = wp_create_nonce("wplc");
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {


                var wplc_set_transient = null;

                wplc_set_transient = setInterval(function () {
                    wpcl_admin_set_transient();
                }, 60000);
                wpcl_admin_set_transient();
                function wpcl_admin_set_transient() {
                    var data = {
                        action: 'wplc_admin_set_transient',
                        security: '<?php echo $ajax_nonce; ?>'

                    };
                    jQuery.post(ajaxurl, data, function (response) {

                    });
                }

            });
        </script>
        <?php
    }
}

function wplc_superadmin_javascript() {

    if (isset($_GET['page']) && ($_GET['page'] == 'wplivechat-menu' || $_GET['page'] == 'wplivechat-menu-settings' || $_GET['page'] == 'wplivechat-menu-offline-messages')) {

        if (!isset($_GET['action'])) {
            if (function_exists("wplc_register_pro_version")) {
                wplc_pro_admin_javascript();
            } else {
                wplc_admin_javascript();
            }
            do_action("wplc_hook_admin_javascript");
        } // main page
        else if (isset($_GET['action'])) {
            if (function_exists("wplc_register_pro_version")) {
                wplc_return_pro_admin_chat_javascript(sanitize_text_field($_GET['cid']));
            } else {
                wplc_return_admin_chat_javascript(sanitize_text_field($_GET['cid']));
            }
            do_action("wplc_hook_admin_javascript_chat");
        }
    }


    do_action("wplc_hook_superadmin_head");
    ?>
    <script type="text/javascript">


        function wplc_desktop_notification() {
            if (typeof Notification !== 'undefined') {
                if (!Notification) {
                    return;
                }
                if (Notification.permission !== "granted")
                    Notification.requestPermission();

                var wplc_desktop_notification = new Notification('<?php _e('New chat received', 'wplivechat'); ?>', {
                    icon: wplc_notification_icon_url,
                    body: "<?php _e("A new chat has been received. Please go the 'Live Chat' page to accept the chat", "wplivechat"); ?>"
                });
                //Notification.close()
            }
        }

    </script>
    <?php
}

/**
 * Admin JS set up
 * @return void
 * @since  6.0.00
 * @author Nick Duncan <nick@codecabin.co.za>
 */
function wplc_admin_javascript() {
    $ajax_nonce = wp_create_nonce("wplc");
    global $wplc_version;

    $wplc_current_user = get_current_user_id();
    if (get_user_meta($wplc_current_user, 'wplc_ma_agent', true)) {

        wp_register_script('wplc-admin-js', plugins_url('js/wplc_u_admin.js', __FILE__), false, $wplc_version, false);
        wp_enqueue_script('wplc-admin-js');
        $not_icon = plugins_url('/images/wplc_notification_icon.png', __FILE__);

        $wplc_wav_file = plugins_url('/ring.wav', __FILE__);
        $wplc_wav_file = apply_filters("wplc_filter_wav_file", $wplc_wav_file);
        wp_localize_script('wplc-admin-js', 'wplc_wav_file', $wplc_wav_file);

        wp_localize_script('wplc-admin-js', 'wplc_ajax_nonce', $ajax_nonce);
        wp_localize_script('wplc-admin-js', 'wplc_notification_icon', $not_icon);

        $extra_data = apply_filters("wplc_filter_admin_javascript", array());
        wp_localize_script('wplc-admin-js', 'wplc_extra_data', $extra_data);

        $ajax_url = is_math_panel() ? "http://math.dicprj.moe/wp-admin/admin-ajax.php" : admin_url('admin-ajax.php');
        $wplc_ajax_url = apply_filters("wplc_filter_ajax_url", $ajax_url);
        wp_localize_script('wplc-admin-js', 'wplc_ajaxurl', $wplc_ajax_url);
        wp_localize_script('wplc-admin-js', 'wplc_ajaxurl_home', admin_url('admin-ajax.php'));

        $wpc_ma_js_strings = array(
            'remove_agent' => __('Remove', 'wplivechat'),
            'nonce' => wp_create_nonce("wplc"),
            'user_id' => get_current_user_id()
        );
        wp_localize_script('wplc-admin-js', 'wplc_admin_strings', $wpc_ma_js_strings);
    }
}

function wplc_admin_menu_layout() {
    do_action("wplc_hook_admin_menu_layout");
    if (function_exists("wplc_register_pro_version")) {
        global $wplc_pro_version;
        if (floatval($wplc_pro_version) < 4 || $wplc_pro_version == "4.1.0" || $wplc_pro_version == "4.1.1") {
            ?>
            <div class='error below-h1'>

                <p><?php _e("Dear Pro User", "wplivechat") ?><br /></p>
                <p><?php _e("You are using an outdated version of <strong>WP Live Chat Support Pro</strong>. Please", "wplivechat") ?> <a href="https://wp-livechat.com/get-updated-version/" target=\"_BLANK\"><?php _e("update to at least version", "wplivechat") ?> 4.0</a> <?php _e("to ensure all functionality is in working order", "wplivechat") ?>.</p>
                <p><strong><?php _e("You're live chat box on your website has been temporarily disabled until the Pro plugin has been updated. This is to ensure a smooth and hassle-free user experience for both yourself and your visitors.", "wplivechat") ?></strong></p>
                <p><?php _e("You can update your plugin <a href='./update-core.php'>here</a>, <a href='./plugins.php'>here</a> or <a href='https://wp-livechat.com/get-updated-version/' target='_BLANK'>here</a>.", "wplivechat") ?></strong></p>
                <p><?php _e("If you are having difficulty updating the plugin, please contact", "wplivechat") ?> nick@wp-livechat.com</p>

            </div>
            <?php
        }
        $wplc_ver = str_replace('.', '', $wplc_pro_version);
        $wplc_ver = intval($wplc_ver);
        if ($wplc_ver < 501) {
            ?>
            <div class='error below-h1'>

                <p><?php _e("Dear Pro User", "wplivechat") ?><br /></p>
                <p><?php _e("You are using an outdated version of <strong>WP Live Chat Support Pro</strong>.", "wplivechat") ?></p>
                <p>
                    <strong><?php _e("Please update to the latest version of WP Live Chat Support Pro", 'wplivechat'); ?>
                        <a href="https://wp-livechat.com/get-updated-version/" target=\"_BLANK\"> <?php _e("Version 5.0.1", "wplivechat"); ?></a>  
            <?php _e("to ensure everything is working correctly.", "wplivechat"); ?>
                    </strong>
                </p>
                <p><?php _e("You can update your plugin <a href='./update-core.php'>here</a>, <a href='./plugins.php'>here</a> or <a href='https://wp-livechat.com/get-updated-version/' target='_BLANK'>here</a>.", "wplivechat") ?></strong></p>
                <p><?php _e("If you are having difficulty updating the plugin, please contact", "wplivechat") ?> nick@wp-livechat.com</p>

            </div>
            <?php
        }
    }
    if (get_option("WPLC_FIRST_TIME") == true && !class_exists("APC_Object_Cache")) {

        update_option('WPLC_FIRST_TIME', false);
        include 'includes/welcome_page.php';
    } else {
        update_option('WPLC_FIRST_TIME', false);
        if (function_exists("wplc_register_pro_version")) {
            wplc_pro_admin_menu_layout_display();
        } else {
            wplc_admin_menu_layout_display();
        }
    }
}

function wplc_first_time_tutorial() {
    if (!get_option('WPLC_FIRST_TIME_TUTORIAL')) {

        global $wplc_basic_plugin_url;
        ?>


        <div id="wplcftt" style='margin-top:30px; margin-bottom:20px; width: 65%; background-color: #FFF; box-shadow: 1px 1px 3px #ccc; display:block; padding:10px; text-align:center; margin-left:auto; margin-right:auto;'>
            <img src='<?php echo $wplc_basic_plugin_url; ?>/images/wplc_notification_icon.png' width="130" align="center" />
            <h1 style='font-weight: 300; font-size: 50px; line-height: 50px;'><strong style="color: #ec822c;"><?php _e("Congratulations", "wplivechat"); ?></strong></h1>
            <h2><strong><?php _e("You are now accepting live chat requests on your site.", "wplivechat"); ?></strong></h2>
            <p><?php _e("The live chat box has automatically been enabled on your website.", "wplivechat"); ?></p>
            <p><?php _e("Chat notifications will start appearing once visitors send a request.", "wplivechat"); ?></p>
            <p><?php _e("You may <a href='?page=wplivechat-menu-settings' target='_BLANK'>modify your chat box settings here.", "wplivechat"); ?></a></p>
            <p><?php _e("Experiencing issues?", "wplivechat"); ?> <a href="https://wp-livechat.com/documentation/troubleshooting/" target="_BLANK" title=""><?php _e("Visit our troubleshooting section.", "wplivechat"); ?></a></p>

            <p><button id="wplc_close_ftt" class="button button-secondary"><?php _e("Hide", "wplivechat"); ?></button></p>

        </div>

    <?php
    }
}

/**
 * Control the content below the visitor count
 * @return void
 * @since  6.0.00
 * @author Nick Duncan <nick@codecabin.co.za>
 */
add_filter("wplc_filter_chat_dahsboard_visitors_online_bottom", "wplc_filter_control_chat_dashboard_visitors_online_bottom", 10);

function wplc_filter_control_chat_dashboard_visitors_online_bottom($text) {
    $text = "<hr />";
    $text .= "<p class='wplc-agent-info' id='wplc-agent-info'>";
    $text .= "  <span class='wplc_agents_online'>1</span>";
    $text .= "  <a href='javascript:void(0);'>" . __("Agent(s) online", "wplivechat") . "</a>";
    $text .= "</p>";
    return $text;
}

add_action("wplc_hook_chat_dashboard_bottom", "wplc_hook_control_chat_dashboard_bottom", 10);

/**
 * Decides whether or not to show the available extensions for this area.
 * @return void
 * @since  6.0.00
 * @author Nick Duncan <nick@codecabin.co.za>
 */
function wplc_hook_control_chat_dashboard_bottom() {
    echo "<p>";
    ?>
    <?php _e("With the Pro add-on of WP Live Chat Support, you can", "wplivechat"); ?> 
    <a href="http://www.wp-livechat.com/purchase-pro/?utm_source=plugin&utm_medium=link&utm_campaign=initiate1" title="<?php _e("see who's online and initiate chats", "wplivechat"); ?>" target=\"_BLANK\">
    <?php _e("initiate chats", "wplivechat"); ?>
    </a> <?php _e("with your online visitors with the click of a button.", "wplivechat"); ?> 
    <a href="http://www.wp-livechat.com/purchase-pro/?utm_source=plugin&utm_medium=link&utm_campaign=initiate2" title="<?php _e("Buy the Pro add-on now.", "wplivechat"); ?>" target=\"_BLANK\">
        <strong>
    <?php _e("Buy the Pro add-on now.", "wplivechat"); ?>
        </strong>
    </a>
    <?php
    echo "</p>";
}

function wplc_admin_menu_layout_display() {

    $wplc_current_user = get_current_user_id();

    if (get_user_meta($wplc_current_user, 'wplc_ma_agent', true)) {
        /*
          -- modified in in 6.0.04 --

          if(current_user_can('wplc_ma_agent') || current_user_can('manage_options')){
         */
        do_action("wplc_hook_admin_menu_layout_display_top");

        wplc_stats("chat_dashboard");

        global $wplc_basic_plugin_url;
        if (!isset($_GET['action'])) {
            ?>
            <style>
                #wplc-support-tabs a.wplc-support-link {
                    background: url('<?php echo $wplc_basic_plugin_url; ?>/images/support.png');
                    right: 0px;
                    top: 250px;
                    height: 108px;
                    width: 45px;
                    margin: 0;
                    padding: 0;
                    position: fixed;
                    z-index: 9999;
                    display:block;
                }
            </style>
            <div class='wplc_network_issue' style='display:none;'>

            </div>

            <div id="wplc-support-tabs">
                <a class="wplc-support-link wplc-rotate" href="?page=wplivechat-menu-support-page"></a>
            </div>
            <div class='wplc_page_title'>
                <img src='<?php echo WPLC_BASIC_PLUGIN_URL; ?>/images/wplc-logo.png' class='wplc-logo' />
            <?php wplc_first_time_tutorial(); ?>
            <?php do_action("wplc_hook_chat_dashboard_above"); ?>

                <p><?php _e("Please note: This window must be open in order to receive new chat notifications.", "wplivechat"); ?></p>
            </div>

            <div id="wplc_sound"></div>

            <div id="wplc_admin_chat_holder">
                <div id='wplc_admin_chat_info_new'>
                    <div class='wplc_chat_vis_count_box'>
            <?php do_action("wplc_hook_chat_dahsboard_visitors_online_top"); ?>
                        <span class='wplc_vis_online'>0</span>
                        <span style='text-transform:uppercase;'>
            <?php _e("Visitors online", "wplivechat"); ?>
                        </span>
            <?php echo apply_filters("wplc_filter_chat_dahsboard_visitors_online_bottom", ""); ?>


                    </div>

                </div>

                <div id="wplc_admin_chat_area_new">
                    <div style="display:block;padding:10px;">
                        <ul class='wplc_chat_ul_header'>
                            <li><span class='wplc_header_vh wplc_headerspan_v'><?php _e("Visitor", "wplivechat"); ?></span></li>
                            <li><span class='wplc_header_vh wplc_headerspan_t'><?php _e("Time", "wplivechat"); ?></span></li>
                            <li><span class='wplc_header_vh wplc_headerspan_nr'><?php _e("Type", "wplivechat"); ?></span></li>
                            <li><span class='wplc_header_vh wplc_headerspan_dev'><?php _e("Device", "wplivechat"); ?></span></li>
                            <li><span class='wplc_header_vh wplc_headerspan_d'><?php _e("Data", "wplivechat"); ?></span></li>
                            <li><span class='wplc_header_vh wplc_headerspan_s'><?php _e("Status", "wplivechat"); ?></span></li>
                            <li><span class='wplc_header_vh wplc_headerspan_a'><?php _e("Action", "wplivechat"); ?></span></li>
                        </ul>
                        <ul id='wplc_chat_ul'>
                        </ul>
                        <br />
                        <hr />
            <?php do_action("wplc_hook_chat_dashboard_bottom"); ?>    

                    </div>

                </div>
            </div>





            <?php
        } else {
            if (isset($_GET['aid'])) {
                $aid = $_GET['aid'];
            } else {
                $aid = null;
            }
            do_action("wplc_hook_admin_menu_layout_display_1", $_GET['action'], $_GET['cid'], $aid);

            if (!is_null($aid)) {
                do_action("wplc_hook_update_agent_id", $_GET['cid'], $aid);
            }

            do_action("wplc_hook_admin_menu_layout_display_1", $_GET['action'], $_GET['cid'], $aid);

            if ($_GET['action'] == 'ac') {
                do_action('wplc_hook_accept_chat', $_GET, $aid);
            }
            do_action("wplc_hook_admin_menu_layout_display", $_GET['action'], $_GET['cid'], $aid);
        }
    } else {
        ?>

        <h1><?php _e("Chat Dashboard", "wplivechat"); ?></h1>
        <div id="welcome-panel" class="welcome-panel">
            <div class="welcome-panel-content">
                <h2><?php _e("Oh no!", "wplivechat"); ?></h2>
                <p class="about-description">
        <?php
        _e(
                "You do not have access to this page as <strong>you are not a chat agent</strong>.", "wplivechat"
        );
        ?>
                </p>
                <p>Users with access to this page are as follows:</p>
                <p>
        <?php
        $user_array = get_users(array(
            'meta_key' => 'wplc_ma_agent',
        ));
        echo "<ol>";
        if ($user_array) {
            foreach ($user_array as $user) {
                echo "<li> " . $user->display_name . " (ID: " . $user->ID . ")</li>";
            }
        }
        echo "</ol>";
        ?>
                </p>
            </div>
        </div>
        <?php
    }
}

add_action("wplc_hook_change_status_on_answer", "wplc_hook_control_change_status_on_answer", 10, 1);

function wplc_hook_control_change_status_on_answer($get_data) {

    $user_ID = get_current_user_id();
    wplc_change_chat_status(sanitize_text_field($get_data['cid']), 3, $user_ID);
}

add_action('wplc_hook_accept_chat', 'wplc_hook_control_accept_chat', 10, 2);

function wplc_hook_control_accept_chat($get_data, $aid) {
    do_action("wplc_hook_change_status_on_answer", $get_data);


    do_action("wplc_hook_accept_chat_url", $get_data);

    if (function_exists("wplc_register_pro_version")) {
        wplc_pro_draw_chat_area(sanitize_text_field($get_data['cid']));
    } else {
        do_action("wplc_hook_draw_chat_area", $get_data);
    }
}

add_action("wplc_hook_chat_dashboard_bottom", "wplc_hook_control_app_chat_dashboard_bottom", 10);

function wplc_hook_control_app_chat_dashboard_bottom() {
    //echo "<p>Tired of logging in to accept chats? Use our <a href='https://wp-livechat.com/extensions/mobile-desktop-app-extension/?utm_source=plugin&utm_medium=plugin&utm_campaign=main_app' target='_BLANK'>Android app</a> or <a href='https://wp-livechat.com/extensions/mobile-desktop-app-extension/?utm_source=plugin&utm_medium=plugin&utm_campaign=main_desktop' target='_BLANK'>desktop app</a> to monitor visitors, accept and initiate chats.</p>";
}

add_action("wplc_hook_draw_chat_area", "wplc_hook_control_draw_chat_area", 10, 1);

function wplc_hook_control_draw_chat_area($get_data) {

    wplc_draw_chat_area(sanitize_text_field($get_data['cid']));
}

function wplc_draw_chat_area($cid) {

    global $wpdb;
    global $wplc_tblname_chats;
    $results = $wpdb->get_results(
            "
        SELECT *
        FROM $wplc_tblname_chats
        WHERE `id` = '$cid'
        LIMIT 1
        "
    );
    if ($results) {
        
    } else {
        $results[0] = null;
    } /* if chat ID doesnt exist, create the variable anyway to avoid an error. Hopefully the Chat ID exists on the server..! */
    $result = apply_filters("wplc_filter_chat_area_data", $results[0], $cid);
    ?>
    <style>

        .wplc-clear-float-message{
            clear: both;
        }

        .rating{
            background-color: lightgray;
            width: 80px;
            padding: 2px;
            border-radius: 4px;
            text-align: center;
            color: white;
            display: inline-block;
            float: right;
        }

        .rating-bad {
            background-color: #AF0B0B !important;
        }

        .rating-good {
            background-color: #368437 !important;
        }


    </style>
    <?php
    $user_data = maybe_unserialize($result->ip);
    $user_ip = $user_data['ip'];
    $browser = wplc_return_browser_string($user_data['user_agent']);
    $browser_image = wplc_return_browser_image($browser, "16");
    global $wplc_basic_plugin_url;
    if ($result->status == 1) {
        $status = __("Previous", "wplivechat");
    } else {
        $status = __("Active", "wplivechat");
    }

    if ($user_ip == "") {
        $user_ip = __('IP Address not recorded', 'wplivechat');
    } else {
        $user_ip = "<a href='http://www.ip-adress.com/ip_tracer/" . $user_ip . "' title='" . __('Whois for', 'wplivechat') . " " . $user_ip . "' target='_BLANK'>" . $user_ip . "</a>";
    }

    echo "<h2>$status " . __('Chat with', 'wplivechat') . " " . $result->name . "</h2>";
    echo "<style>#adminmenuwrap { display:none; } #adminmenuback { display:none; } #wpadminbar { display:none; } #wpfooter { display:none; } .update-nag { display:none; }</style>";

    echo "<div class=\"end_chat_div\"><a href=\"javascript:void(0);\" class=\"wplc_admin_close_chat button\" id=\"wplc_admin_close_chat\">" . __("End chat", "wplivechat") . "</a></div>";

    do_action("wplc_add_js_admin_chat_area", $cid);

    echo "<div id='admin_chat_box'>";

    $result->continue = true;

    do_action("wplc_hook_wplc_draw_chat_area", $result);

    if (!$result->continue) {
        return;
    }

    echo"<div class='admin_chat_box'><div class='admin_chat_box_inner' id='admin_chat_box_area_" . $result->id . "'>" . wplc_return_chat_messages($cid) . "</div><div class='admin_chat_box_inner_bottom'>" . wplc_return_chat_response_box($cid) . "</div>";



    echo "</div>";
    echo "<div class='admin_visitor_info'>";
    do_action("wplc_hook_admin_visitor_info_display_before", $cid);
    echo "  <div style='float:left; width:100px;'><img src=\"//www.gravatar.com/avatar/" . md5($result->email) . "\" class=\"admin_chat_img\" /></div>";
    echo "  <div style='float:left;'>";

    echo "      <div class='admin_visitor_info_box1'>";
    echo "          <span class='admin_chat_name'>" . $result->name . "</span>";
    echo "          <span class='admin_chat_email'>" . $result->email . "</span>";
    echo "      </div>";
    echo "  </div>";

    echo "  <div class='admin_visitor_advanced_info'>";
    echo "      <strong>" . __("Site Info", "wplivechat") . "</strong>";
    echo "      <hr />";
    echo "      <span class='part1'>" . __("Chat initiated on:", "wplivechat") . "</span> <span class='part2'>" . esc_url($result->url) . "</span>";
    echo "  </div>";

    echo "  <div class='admin_visitor_advanced_info'>";
    echo "      <strong>" . __("Advanced Info", "wplivechat") . "</strong>";
    echo "      <hr />";
    echo "      <span class='part1'>" . __("Browser:", "wplivechat") . "</span><span class='part2'> $browser <img src='" . $wplc_basic_plugin_url . "/images/$browser_image' alt='$browser' title='$browser' /><br />";
    echo "      <span class='part1'>" . __("IP Address:", "wplivechat") . "</span><span class='part2'> " . $user_ip;
    echo "  </div>";
    echo "<hr />";

    echo (apply_filters("wplc_filter_advanced_info", "", $result->id, $result->name));

    echo "  <div id=\"wplc_sound_update\"></div>";

    echo "<h3>" . __("Add-ons", "wplivechat") . "</h3>";
    do_action("wplc_hook_admin_visitor_info_display_after", $cid);
    echo "<a href='" . admin_url('admin.php?page=wplivechat-menu-extensions-page') . "' class='button button-primary' target='_BLANK'>" . __("Get more add-ons", "wplivechat") . "</a>";
    echo "</div>";



    if ($result->status != 1) {

        do_action("wplc_hook_admin_below_chat_box", $result);

        //echo wplc_return_admin_chat_javascript($_GET['cid']);
    }
}

add_action("wplc_hook_admin_below_chat_box", "wplc_hook_control_admin_below_chat_box", 10);

function wplc_hook_control_admin_below_chat_box() {
    echo "<div class='admin_chat_quick_controls'>";
    echo "  <p style=\"text-align:left; font-size:11px;\">" . __('Press ENTER to send your message', 'wplivechat') . "</p>";
    echo "  " . __("Assign Quick Response", "wplivechat") . " <select name='wplc_macros_select' class='wplc_macros_select' disabled><option>" . __('Select', 'wplivechat') . "</option></select> <a href='https://wp-livechat.com/purchase-pro/?utm_source=plugin&utm_medium=link&utm_campaign=quick_resposnes' title='" . __('Add Quick Responses to your Live Chat', 'wplivechat') . "' target='_BLANK'>" . __("Pro version only", "wplivechat") . "</a>";
    echo "  </div>";
    echo "</div>";
}

function wplc_return_chat_response_box($cid) {
    $ret = "<div class=\"chat_response_box\">";
    $ret .= "<input type='text' name='wplc_admin_chatmsg' id='wplc_admin_chatmsg' value='' placeholder='" . __("type here...", "wplivechat") . "' />";

    $ret .= apply_filters("wplc_filter_chat_upload", "");
    $ret .= (!function_exists("nifty_text_edit_div") ? apply_filters("wplc_filter_chat_text_editor_upsell", "") : apply_filters("wplc_filter_chat_text_editor", ""));

    $ret .= "<input id='wplc_admin_cid' type='hidden' value='$cid' />";
    $ret .= "<input id='wplc_admin_send_msg' type='button' value='" . __("Send", "wplivechat") . "' style=\"display:none;\" />";
    $ret .= "</div>";
    return $ret;
}

function wplc_return_admin_chat_javascript($cid) {
    $ajax_nonce = wp_create_nonce("wplc");
    global $wplc_version;

    wp_register_script('wplc-admin-chat-js', plugins_url('js/wplc_u_admin_chat.js', __FILE__), false, $wplc_version, false);
    wp_enqueue_script('wplc-admin-chat-js');


    if (function_exists("wplc_pro_get_admin_picture")) {
        $src = wplc_pro_get_admin_picture();
        if ($src) {
            $image = "<img src=" . $src . " width='20px' id='wp-live-chat-2-img'/>";
        } else {
            $image = "";
        }
    } else {
        $image = "";
    }
    $admin_pic = $image;
    wp_localize_script('wplc-admin-chat-js', 'wplc_ajax_nonce', $ajax_nonce);
    wp_localize_script('wplc-admin-chat-js', 'admin_pic', $admin_pic);
    $wplc_ding_file = plugins_url('/ding.mp3', __FILE__);
    wp_localize_script('wplc-admin-chat-js', 'wplc_ding_file', $wplc_ding_file);


    $extra_data = apply_filters("wplc_filter_admin_javascript", array());
    wp_localize_script('wplc-admin-chat-js', 'wplc_extra_data', $extra_data);





    $wplc_settings = get_option("WPLC_SETTINGS");

    if (isset($wplc_settings['wplc_display_name']) && $wplc_settings['wplc_display_name'] == 1) {
        $display_name = 'display';
    } else {
        $display_name = 'hide';
    }
    if (isset($wplc_settings['wplc_enable_msg_sound']) && intval($wplc_settings['wplc_enable_msg_sound']) == 1) {
        $enable_ding = '1';
    } else {
        $enable_ding = '0';
    }
    if (isset($_COOKIE['wplc_email']) && $_COOKIE['wplc_email'] != "") {
        $wplc_user_email_address = sanitize_text_field($_COOKIE['wplc_email']);
    } else {
        $wplc_user_email_address = "";
    }

    wp_localize_script('wplc-admin-chat-js', 'wplc_name', $display_name);
    wp_localize_script('wplc-admin-chat-js', 'wplc_enable_ding', $enable_ding);
    wp_localize_script('wplc-admin-chat-js', 'wplc_user_email', $wplc_user_email_address);

    $ajax_url = is_math_panel() ? "http://math.dicprj.moe/wp-admin/admin-ajax.php" : admin_url('admin-ajax.php');
    $home_ajax_url = $ajax_url;

    $wplc_ajax_url = apply_filters("wplc_filter_ajax_url", $ajax_url);
    wp_localize_script('wplc-admin-chat-js', 'wplc_ajaxurl', $wplc_ajax_url);
    wp_localize_script('wplc-admin-chat-js', 'wplc_home_ajaxurl', $home_ajax_url);



    $wplc_url = admin_url('admin.php?page=wplivechat-menu&action=ac&cid=' . $cid);
    wp_localize_script('wplc-admin-chat-js', 'wplc_url', $wplc_url);


    $wplc_string1 = __("User has opened the chat window", "wplivechat");
    $wplc_string2 = __("User has minimized the chat window", "wplivechat");
    $wplc_string3 = __("User has maximized the chat window", "wplivechat");
    $wplc_string4 = __("The chat has been ended", "wplivechat");
    wp_localize_script('wplc-admin-chat-js', 'wplc_string1', $wplc_string1);
    wp_localize_script('wplc-admin-chat-js', 'wplc_string2', $wplc_string2);
    wp_localize_script('wplc-admin-chat-js', 'wplc_string3', $wplc_string3);
    wp_localize_script('wplc-admin-chat-js', 'wplc_string4', $wplc_string4);
    wp_localize_script('wplc-admin-chat-js', 'wplc_cid', $cid);


    do_action("wplc_hook_admin_chatbox_javascript");
}

function wplc_activate() {
    wplc_handle_db();
    if (!get_option("WPLC_SETTINGS")) {
        $wplc_alt_text = __("Please click \'Start Chat\' to initiate a chat with an agent", "wplivechat");
        add_option('WPLC_SETTINGS', array(
            "wplc_settings_align" => "2",
            "wplc_settings_enabled" => "1",
            "wplc_powered_by_link" => "0",
            "wplc_settings_fill" => "ed832f",
            "wplc_settings_font" => "FFFFFF",
            "wplc_settings_color1" => "ED832F",
            "wplc_settings_color2" => "FFFFFF",
            "wplc_settings_color3" => "EEEEEE",
            "wplc_settings_color4" => "666666",
            "wplc_theme" => "theme-default",
            "wplc_newtheme" => "theme-2",
            "wplc_require_user_info" => '1',
            "wplc_loggedin_user_info" => '1',
            "wplc_user_alternative_text" => $wplc_alt_text,
            "wplc_enabled_on_mobile" => '1',
            "wplc_display_name" => '1',
            "wplc_record_ip_address" => '1',
            "wplc_pro_fst1" => __("Questions?", "wplivechat"),
            "wplc_pro_fst2" => __("Chat with us", "wplivechat"),
            "wplc_pro_fst3" => __("Start live chat", "wplivechat"),
            "wplc_pro_sst1" => __("Start Chat", "wplivechat"),
            "wplc_pro_sst1_survey" => __("Or chat to an agent now", "wplivechat"),
            "wplc_pro_sst1e_survey" => __("Chat ended", "wplivechat"),
            "wplc_pro_sst2" => __("Connecting. Please be patient...", "wplivechat"),
            "wplc_pro_tst1" => __("Reactivating your previous chat...", "wplivechat"),
            "wplc_pro_na" => __("Chat offline. Leave a message", "wplivechat"),
            "wplc_pro_intro" => __("Hello. Please input your details so that I may help you.", "wplivechat"),
            "wplc_pro_offline1" => __("We are currently offline. Please leave a message and we'll get back to you shortly.", "wplivechat"),
            "wplc_pro_offline2" => __("Sending message...", "wplivechat"),
            "wplc_pro_offline3" => __("Thank you for your message. We will be in contact soon.", "wplivechat"),
            "wplc_user_enter" => __("Press ENTER to send your message", "wplivechat"),
            "wplc_user_welcome_chat" => __("Welcome. How may I help you?", "wplivechat"),
        ));
    }






    $admins = get_role('administrator');
    $admins->add_cap('wplc_ma_agent');

    $uid = get_current_user_id();
    update_user_meta($uid, 'wplc_ma_agent', 1);
    update_user_meta($uid, "wplc_chat_agent_online", time());

    add_option("WPLC_HIDE_CHAT", "true");
    add_option("WPLC_FIRST_TIME", true);


    do_action("wplc_activate_hook");
}

function wplc_handle_db() {
    global $wpdb;
    global $wplc_version;
    global $wplc_tblname_chats;
    global $wplc_tblname_msgs;
    global $wplc_tblname_offline_msgs;

    $sql = "
        CREATE TABLE " . $wplc_tblname_chats . " (
          id int(11) NOT NULL AUTO_INCREMENT,
          timestamp datetime NOT NULL,
          name varchar(700) NOT NULL,
          email varchar(700) NOT NULL,
          ip varchar(700) NOT NULL,
          status int(11) NOT NULL,
          session varchar(100) NOT NULL,
          url varchar(700) NOT NULL,
          last_active_timestamp datetime NOT NULL,
          other LONGTEXT NOT NULL,
          PRIMARY KEY  (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
    ";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);




    $sql = '
        CREATE TABLE ' . $wplc_tblname_msgs . ' (
          id int(11) NOT NULL AUTO_INCREMENT,
          chat_sess_id int(11) NOT NULL,
          msgfrom varchar(150) CHARACTER SET utf8 NOT NULL,
          msg varchar(700) CHARACTER SET utf8 NOT NULL,
          timestamp datetime NOT NULL,
          status INT(3) NOT NULL,
          originates INT(3) NOT NULL,
          PRIMARY KEY  (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
    ';

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    @dbDelta($sql);

    /* check for previous versions containing 'from' instead of 'msgfrom' */
    $results = $wpdb->get_results("DESC $wplc_tblname_msgs");
    $founded = 0;
    foreach ($results as $row) {
        if ($row->Field == "from") {
            $founded++;
        }
    }

    if ($founded > 0) {
        $wpdb->query("ALTER TABLE " . $wplc_tblname_msgs . " CHANGE `from` `msgfrom` varchar(150)");
    }


    $sql2 = "
        CREATE TABLE " . $wplc_tblname_offline_msgs . " (
          id int(11) NOT NULL AUTO_INCREMENT,
          timestamp datetime NOT NULL,
          name varchar(700) NOT NULL,
          email varchar(700) NOT NULL,
          message varchar(700) NOT NULL,
          ip varchar(700) NOT NULL,
          user_agent varchar(700) NOT NULL,
          PRIMARY KEY  (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
    ";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    @dbDelta($sql2);

    add_option("wplc_db_version", $wplc_version);
    update_option("wplc_db_version", $wplc_version);
}

function wplc_add_user_stylesheet() {

    if (function_exists('wplc_display_chat_contents')) {
        $show_chat_contents = wplc_display_chat_contents();
    } else {
        $show_chat_contents = 1;
    }
    if ($show_chat_contents >= 1) {
        wp_register_style('wplc-font-awesome', plugins_url('/css/font-awesome.min.css', __FILE__));
        wp_enqueue_style('wplc-font-awesome');
        wp_register_style('wplc-style', plugins_url('/css/wplcstyle.css', __FILE__));
        wp_enqueue_style('wplc-style');


        global $wplc_pro_version;
        $wplc_ver = str_replace('.', '', $wplc_pro_version);
        $checker = intval($wplc_ver);

        if (function_exists("wplc_pro_version_control") && $checker < 6000) {
            /* old pro version backwards compatibility */
            wp_register_style('wplc-style-pro', plugins_url('/css/wplcstyle_old.css', __FILE__));
            wp_enqueue_style('wplc-style-pro');
        }



        $wplc_settings = get_option('WPLC_SETTINGS');
        if (isset($wplc_settings['wplc_theme'])) {
            $wplc_theme = $wplc_settings['wplc_theme'];
        } else {
            $wplc_theme = "theme-default";
        }

        if ((isset($wplc_theme) && $checker >= 6000 ) || (!function_exists("wplc_pro_version_control") )) {
            if ($wplc_theme == 'theme-default') {
                wp_register_style('wplc-theme-palette-default', plugins_url('/css/themes/theme-default.css', __FILE__));
                wp_enqueue_style('wplc-theme-palette-default');
            } else if ($wplc_theme == 'theme-1') {
                wp_register_style('wplc-theme-palette-1', plugins_url('/css/themes/theme-1.css', __FILE__));
                wp_enqueue_style('wplc-theme-palette-1');
            } else if ($wplc_theme == 'theme-2') {
                wp_register_style('wplc-theme-palette-2', plugins_url('/css/themes/theme-2.css', __FILE__));
                wp_enqueue_style('wplc-theme-palette-2');
            } else if ($wplc_theme == 'theme-3') {
                wp_register_style('wplc-theme-palette-3', plugins_url('/css/themes/theme-3.css', __FILE__));
                wp_enqueue_style('wplc-theme-palette-3');
            } else if ($wplc_theme == 'theme-4') {
                wp_register_style('wplc-theme-palette-4', plugins_url('/css/themes/theme-4.css', __FILE__));
                wp_enqueue_style('wplc-theme-palette-4');
            } else if ($wplc_theme == 'theme-5') {
                wp_register_style('wplc-theme-palette-5', plugins_url('/css/themes/theme-5.css', __FILE__));
                wp_enqueue_style('wplc-theme-palette-5');
            } else if ($wplc_theme == 'theme-6') {
                /* custom */
                /* handled elsewhere */
            }





            if (isset($wplc_settings['wplc_newtheme'])) {
                $wplc_newtheme = $wplc_settings['wplc_newtheme'];
            } else {
                
            }
            if (isset($wplc_newtheme)) {
                if ($wplc_newtheme == 'theme-1') {
                    wp_register_style('wplc-theme-classic', plugins_url('/css/themes/classic.css', __FILE__));
                    wp_enqueue_style('wplc-theme-classic');
                } else if ($wplc_newtheme == 'theme-2') {
                    wp_register_style('wplc-theme-modern', plugins_url('/css/themes/modern.css', __FILE__));
                    wp_enqueue_style('wplc-theme-modern');
                }
            }

            if ($wplc_settings["wplc_settings_align"] == 1) {
                wp_register_style('wplc-theme-position', plugins_url('/css/themes/position-bottom-left.css', __FILE__));
                wp_enqueue_style('wplc-theme-position');
            } else if ($wplc_settings["wplc_settings_align"] == 2) {
                wp_register_style('wplc-theme-position', plugins_url('/css/themes/position-bottom-right.css', __FILE__));
                wp_enqueue_style('wplc-theme-position');
            } else if ($wplc_settings["wplc_settings_align"] == 3) {
                wp_register_style('wplc-theme-position', plugins_url('/css/themes/position-left.css', __FILE__));
                wp_enqueue_style('wplc-theme-position');
            } else if ($wplc_settings["wplc_settings_align"] == 4) {
                wp_register_style('wplc-theme-position', plugins_url('/css/themes/position-right.css', __FILE__));
                wp_enqueue_style('wplc-theme-position');
            } else {
                wp_register_style('wplc-theme-position', plugins_url('/css/themes/position-bottom-right.css', __FILE__));
                wp_enqueue_style('wplc-theme-position');
            }
        }
    }
    if (function_exists('wplc_ce_activate')) {
        if (function_exists('wplc_ce_load_user_styles')) {
            wplc_ce_load_user_styles();
        }
    }
}

function wplc_add_admin_stylesheet() {
    if (isset($_GET['page']) && ($_GET['page'] == 'wplivechat-menu' || $_GET['page'] == 'wplivechat-menu-api-keys-page' || $_GET['page'] == 'wplivechat-menu-extensions-page' || $_GET['page'] == 'wplivechat-menu-settings' || $_GET['page'] == 'wplivechat-menu-offline-messages' || $_GET['page'] == 'wplivechat-menu-history')) {
        wp_register_style('wplc-admin-style', plugins_url('/css/jquery-ui.css', __FILE__));
        wp_enqueue_style('wplc-admin-style');
        wp_register_style('wplc-chat-style', plugins_url('/css/chat-style.css', __FILE__));
        wp_enqueue_style('wplc-chat-style');
        wp_register_style('wplc-font-awesome', plugins_url('/css/font-awesome.min.css', __FILE__));
        wp_enqueue_style('wplc-font-awesome');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-effects-core');
    }
    if (isset($_GET['page']) && $_GET['page'] == 'wplivechat-menu' && isset($_GET['action']) && $_GET['action'] == "ac") {
        wp_register_style('wplc-admin-chat-box-style', plugins_url('/css/admin-chat-box-style.css', __FILE__));
        wp_enqueue_style('wplc-admin-chat-box-style');
    }
    if (isset($_GET['page']) && $_GET['page'] == "wplivechat-menu-support-page") {
        wp_register_style('fontawesome', plugins_url('css/font-awesome.min.css', __FILE__));
        wp_enqueue_style('fontawesome');
        wp_register_style('wplc-support-page-css', plugins_url('css/support-css.css', __FILE__));
        wp_enqueue_style('wplc-support-page-css');
    }
}

if (isset($_GET['page']) && $_GET['page'] == 'wplivechat-menu-settings') {
    add_action('admin_print_scripts', 'wplc_admin_scripts_basic');
}

function wplc_admin_scripts_basic() {

    if (isset($_GET['page']) && $_GET['page'] == "wplivechat-menu-settings") {
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-tooltip');
        wp_register_script('my-wplc-color', plugins_url('js/jscolor.js', __FILE__), false, '1.4.1', false);
        wp_enqueue_script('my-wplc-color');
        wp_enqueue_script('jquery-ui-tabs');
        wp_register_script('my-wplc-tabs', plugins_url('js/wplc_tabs.js', __FILE__), array('jquery-ui-core'), '', true);
        wp_enqueue_script('my-wplc-tabs');
    }
}

function wplc_admin_settings_layout() {
    wplc_settings_page_basic();
}

add_action("wplc_hook_history_draw_area", "wplc_hook_control_history_draw_area", 10, 1);

/**
 * Display normal history page
 * @param  int   $cid Chat ID
 * @return void
 * @since  6.1.00
 * @author Nick Duncan <nick@codecabin.co.za>
 */
function wplc_hook_control_history_draw_area($cid) {
    wplc_draw_chat_area($cid);
}

/**
 * What to display for the chat history
 * @param  int   $cid Chat ID
 * @return void
 * @since  6.1.00
 * @author Nick Duncan <nick@codecabin.co.za>
 */
function wplc_admin_view_chat_history($cid) {
    do_action("wplc_hook_history_draw_area", $cid);
}

add_action('wplc_hook_admin_menu_layout_display', 'wplc_hook_control_history_get_control', 1, 3);

/**
 * Control history GET calls
 * @param  string $action The GET action
 * @param  int    $cid    The chat id
 * @param  int    $aid    AID
 * @return void
 * @since  6.1.00
 * @author Nick Duncan <nick@codecabin.co.za>
 */
function wplc_hook_control_history_get_control($action, $cid, $aid) {

    if ($action == 'history') {
        wplc_admin_view_chat_history(sanitize_text_field($cid));
    } else if ($action == 'download_history') {

        //Moved into the init hook as of version 6.0.01 due to different functionality
        //wplc_admin_download_history(sanitize_text_field($_GET['type']), sanitize_text_field($cid));
    }
}

add_action("wplc_hook_chat_history", "wplc_hook_control_chat_history");

function wplc_hook_control_chat_history() {


    global $wpdb;
    global $wplc_tblname_chats;

    if (isset($_GET['wplc_action']) && $_GET['wplc_action'] == 'remove_cid') {
        if (isset($_GET['cid'])) {
            if (isset($_GET['wplc_confirm'])) {
                //Confirmed - delete
                $delete_sql = "
                    DELETE FROM $wplc_tblname_chats
                    WHERE `id` = '" . intval($_GET['cid']) . "'
                    AND (`status` = 1 OR `status` = 8)                   
                    ";


                $wpdb->query($delete_sql);
                if ($wpdb->last_error) {
                    echo "<div class='update-nag' style='margin-top: 0px;margin-bottom: 5px;'>
                        " . __("Error: Could not delete chat", "wplivechat") . "<br>
                      </div>";
                } else {
                    echo "<div class='update-nag' style='margin-top: 0px;margin-bottom: 5px;border-color:#67d552;'>
                        " . __("Chat Deleted", "wplivechat") . "<br>
                      </div>";
                }
            } else {
                //Prompt
                echo "<div class='update-nag' style='margin-top: 0px;margin-bottom: 5px;'>
                        " . __("Are you sure you would like to delete this chat?", "wplivechat") . "<br>
                        <a class='button' href='?page=wplivechat-menu-history&wplc_action=remove_cid&cid=" . $_GET['cid'] . "&wplc_confirm=1''>" . __("Yes", "wplivechat") . "</a> <a class='button' href='?page=wplivechat-menu-history'>" . __("No", "wplivechat") . "</a>
                      </div>";
            }
        }
    }


    $results = $wpdb->get_results(
            "
        SELECT *
        FROM $wplc_tblname_chats
        WHERE `status` = 1 OR `status` = 8
        ORDER BY `timestamp` DESC
      "
    );
    echo "
       <form method=\"post\" >
        <input type=\"submit\" value=\"" . __('Delete History', 'wplivechat') . "\" class='button' name=\"wplc-delete-chat-history\" /><br /><br />
       </form>

      <table class=\"wp-list-table widefat fixed \" cellspacing=\"0\">
  <thead>
  <tr>
    <th scope='col' id='wplc_id_colum' class='manage-column column-id sortable desc'  style=''><span>" . __("Date", "wplivechat") . "</span></th>
                <th scope='col' id='wplc_name_colum' class='manage-column column-name_title sortable desc'  style=''><span>" . __("Name", "wplivechat") . "</span></th>
                <th scope='col' id='wplc_email_colum' class='manage-column column-email' style=\"\">" . __("Email", "wplivechat") . "</th>
                <th scope='col' id='wplc_url_colum' class='manage-column column-url' style=\"\">" . __("URL", "wplivechat") . "</th>
                <th scope='col' id='wplc_status_colum' class='manage-column column-status'  style=\"\">" . __("Status", "wplivechat") . "</th>
                <th scope='col' id='wplc_action_colum' class='manage-column column-action sortable desc'  style=\"\"><span>" . __("Action", "wplivechat") . "</span></th>
        </tr>
  </thead>
        <tbody id=\"the-list\" class='list:wp_list_text_link'>
        ";
    if (!$results) {
        echo "<tr><td></td><td>" . __("No chats available at the moment", "wplivechat") . "</td></tr>";
    } else {
        foreach ($results as $result) {
            unset($trstyle);
            unset($actions);


            $url = admin_url('admin.php?page=wplivechat-menu&action=history&cid=' . $result->id);
            $url2 = admin_url('admin.php?page=wplivechat-menu&action=download_history&type=csv&cid=' . $result->id);
            $url3 = "?page=wplivechat-menu-history&wplc_action=remove_cid&cid=" . $result->id;
            $actions = "
                <a href='$url' class='button' title='" . __('View Chat History', 'wplivechat') . "' target='_BLANK' id=''><i class='fa fa-eye'></i></a> <a href='$url2' class='button' title='" . __('Download Chat History', 'wplivechat') . "' target='_BLANK' id=''><i class='fa fa-download'></i></a> <a href='$url3' class='button'><i class='fa fa-trash-o'></i></a>      
                ";
            $trstyle = "style='height:30px;'";

            echo "<tr id=\"record_" . $result->id . "\" $trstyle>";
            echo "<td class='chat_id column-chat_d'>" . $result->timestamp . "</td>";
            echo "<td class='chat_name column_chat_name' id='chat_name_" . $result->id . "'><img src=\"//www.gravatar.com/avatar/" . md5($result->email) . "?s=40\" /> " . $result->name . "</td>";
            echo "<td class='chat_email column_chat_email' id='chat_email_" . $result->id . "'><a href='mailto:" . $result->email . "' title='Email " . ".$result->email." . "'>" . $result->email . "</a></td>";
            echo "<td class='chat_name column_chat_url' id='chat_url_" . $result->id . "'>" . esc_url($result->url) . "</td>";
            echo "<td class='chat_status column_chat_status' id='chat_status_" . $result->id . "'><strong>" . wplc_return_status($result->status) . "</strong></td>";
            echo "<td class='chat_action column-chat_action' id='chat_action_" . $result->id . "'>$actions</td>";
            echo "</tr>";
        }
    }
    echo "</table>";
}

function wplc_admin_history_layout() {
    wplc_stats("history");
    echo"<div class=\"wrap\"><div id=\"icon-edit\" class=\"icon32 icon32-posts-post\"><br></div><h2>" . __("WP Live Chat History", "wplivechat") . "</h2>";


    if (function_exists("wplc_ce_activate")) {
        wplc_ce_admin_display_history();
    } else if (function_exists("wplc_register_pro_version")) {
        wplc_pro_admin_display_history();
    } else {
        do_action("wplc_hook_chat_history");
    }
}

add_action("wplc_hook_chat_missed", "wplc_hook_control_missed_chats", 10);

function wplc_hook_control_missed_chats() {
    if (function_exists('wplc_admin_display_missed_chats')) {
        wplc_admin_display_missed_chats();
    }
}

function wplc_admin_missed_chats() {
    wplc_stats("missed");
    echo "<div class=\"wrap\"><div id=\"icon-edit\" class=\"icon32 icon32-posts-post\"><br></div><h2>" . __("WP Live Chat Missed Chats", "wplivechat") . "</h2>";
    do_action("wplc_hook_chat_missed");
}

add_action("wplc_hook_offline_messages_display", "wplc_hook_control_offline_messages_display", 10);

function wplc_hook_control_offline_messages_display() {
    if (function_exists("wplc_admin_display_offline_messages_new")) {
        wplc_admin_display_offline_messages_new();
    } else {
        if (function_exists("wplc_register_pro_version")) {
            if (function_exists('wplc_pro_admin_display_offline_messages')) {
                wplc_pro_admin_display_offline_messages();
            } else {
                echo "<div class='updated'><p>" . __('Please update to the latest version of WP Live Chat Support Pro to start recording any offline messages.', 'wplivechat') . "</p></div>";
            }
        } else {
            echo "<br /><br >" . _('This option is only available in the ', 'wplivechat') . "<a href=\"http://www.wp-livechat.com/purchase-pro/?utm_source=plugin&utm_medium=link&utm_campaign=history1\" title=\"" . __("Pro Add-on", "wplivechat") . "\" target=\"_BLANK\">" . __('Pro Add-on', 'wplivechat') . "</a> of WP Live Chat. <a href=\"http://www.wp-livechat.com/purchase-pro/?utm_source=plugin&utm_medium=link&utm_campaign=missed_chats2\" title=\"" . __("Pro Add-on", "wplivechat") . "\" target=\"_BLANK\"></a>";
        }
    }
}

/**
 * Control who should see the offline messages
 * @return void
 */
function wplc_admin_offline_messages() {
    wplc_stats("offline_messages");
    echo"<div class=\"wrap\"><div id=\"icon-edit\" class=\"icon32 icon32-posts-post\"><br></div><h2>" . __("WP Live Chat Offline Messages", "wplivechat") . "</h2>";
    do_action("wplc_hook_offline_messages_display");
}

/**
 * Output the offline messages in an HTML table
 * @return void
 */
function wplc_admin_display_offline_messages_new() {

    global $wpdb;
    global $wplc_tblname_offline_msgs;

    echo "
        <table class=\"wp-list-table widefat fixed \" cellspacing=\"0\">
            <thead>
                <tr>
                    <th class='manage-column column-id'><span>" . __("Date", "wplivechat") . "</span></th>
                    <th scope='col' id='wplc_name_colum' class='manage-column column-id'><span>" . __("Name", "wplivechat") . "</span></th>
                    <th scope='col' id='wplc_email_colum' class='manage-column column-id'>" . __("Email", "wplivechat") . "</th>
                    <th scope='col' id='wplc_message_colum' class='manage-column column-id'>" . __("Message", "wplivechat") . "</th>
                    <th scope='col' id='wplc_message_colum' class='manage-column column-id'>" . __("Actions", "wplivechat") . "</th>
                </tr>
            </thead>
            <tbody id=\"the-list\" class='list:wp_list_text_link'>";

    $sql = "SELECT * FROM $wplc_tblname_offline_msgs ORDER BY `timestamp` DESC";

    $results = $wpdb->get_results($sql);

    if (!$results) {
        echo "<tr><td></td><td>" . __("You have not received any offline messages.", "wplivechat") . "</td></tr>";
    } else {
        foreach ($results as $result) {
            echo "<tr id=\"record_" . $result->id . "\">";
            echo "<td class='chat_id column-chat_d'>" . $result->timestamp . "</td>";
            echo "<td class='chat_name column_chat_name' id='chat_name_" . $result->id . "'><img src=\"//www.gravatar.com/avatar/" . md5($result->email) . "?s=30\" /> " . $result->name . "</td>";
            echo "<td class='chat_email column_chat_email' id='chat_email_" . $result->id . "'><a href='mailto:" . $result->email . "' title='Email " . ".$result->email." . "'>" . $result->email . "</a></td>";
            echo "<td class='chat_name column_chat_url' id='chat_url_" . $result->id . "'>" . $result->message . "</td>";
            echo "<td class='chat_name column_chat_delete'><button class='button wplc_delete_message' title='" . __('Delete Message', 'wplivechat') . "' class='wplc_delete_message' mid='" . $result->id . "'><i class='fa fa-times'></i></button></td>";
            echo "</tr>";
        }
    }

    echo "
            </tbody>
        </table>";
}

function wplc_settings_page_basic() {





    if (function_exists("wplc_register_pro_version")) {
        wplc_settings_page_pro();
    } else {
        include 'includes/settings_page.php';
    }
}

function wplc_stats($sec) {
    $wplc_stats = get_option("wplc_stats");
    if ($wplc_stats) {
        if (isset($wplc_stats[$sec]["views"])) {
            $wplc_stats[$sec]["views"] = $wplc_stats[$sec]["views"] + 1;
            $wplc_stats[$sec]["last_accessed"] = date("Y-m-d H:i:s");
        } else {
            $wplc_stats[$sec]["views"] = 1;
            $wplc_stats[$sec]["last_accessed"] = date("Y-m-d H:i:s");
            $wplc_stats[$sec]["first_accessed"] = date("Y-m-d H:i:s");
        }
    } else {

        $wplc_stats[$sec]["views"] = 1;
        $wplc_stats[$sec]["last_accessed"] = date("Y-m-d H:i:s");
        $wplc_stats[$sec]["first_accessed"] = date("Y-m-d H:i:s");
    }
    update_option("wplc_stats", $wplc_stats);
}

add_action("wplc_hook_head", "wplc_hook_control_head");

function wplc_hook_control_head() {
    if (isset($_POST['wplc-delete-chat-history'])) {
        wplc_del_history();
    }
}

function wplc_del_history() {
    global $wpdb;
    global $wplc_tblname_chats;
    $wpdb->query("TRUNCATE TABLE $wplc_tblname_chats");
}

add_filter("wplc_filter_chat_header_extra_attr", "wplc_filter_control_chat_header_extra_attr", 10, 1);

function wplc_filter_control_chat_header_extra_attr($wplc_extra_attr) {
    $wplc_acbc_data = get_option("WPLC_SETTINGS");
    if (isset($wplc_acbc_data['wplc_auto_pop_up'])) {
        $extr_string = $wplc_acbc_data['wplc_auto_pop_up'];
        $wplc_extra_attr .= " wplc-auto-pop-up=\"" . $extr_string . "\"";
    }

    return $wplc_extra_attr;
}

function wplc_head_basic() {
    global $wpdb;

    do_action("wplc_hook_head");


    if (isset($_POST['wplc_save_settings'])) {
        do_action("wplc_hook_admin_settings_save");
        if (isset($_POST['wplc_settings_align'])) {
            $wplc_data['wplc_settings_align'] = esc_attr($_POST['wplc_settings_align']);
        }
        if (isset($_POST['wplc_environment'])) {
            $wplc_data['wplc_environment'] = esc_attr($_POST['wplc_environment']);
        }
        if (isset($_POST['wplc_settings_fill'])) {
            $wplc_data['wplc_settings_fill'] = esc_attr($_POST['wplc_settings_fill']);
        }
        if (isset($_POST['wplc_settings_font'])) {
            $wplc_data['wplc_settings_font'] = esc_attr($_POST['wplc_settings_font']);
        }

        if (isset($_POST['wplc_settings_color1'])) {
            $wplc_data['wplc_settings_color1'] = esc_attr($_POST['wplc_settings_color1']); /* backwards compatibility for pro */ $wplc_data['wplc_settings_fill'] = esc_attr($_POST['wplc_settings_color1']);
        }
        if (isset($_POST['wplc_settings_color2'])) {
            $wplc_data['wplc_settings_color2'] = esc_attr($_POST['wplc_settings_color2']); /* backwards compatibility for pro */ $wplc_data['wplc_settings_font'] = esc_attr($_POST['wplc_settings_color2']);
        }
        if (isset($_POST['wplc_settings_color3'])) {
            $wplc_data['wplc_settings_color3'] = esc_attr($_POST['wplc_settings_color3']);
        }
        if (isset($_POST['wplc_settings_color4'])) {
            $wplc_data['wplc_settings_color4'] = esc_attr($_POST['wplc_settings_color4']);
        }

        if (isset($_POST['wplc_settings_enabled'])) {
            $wplc_data['wplc_settings_enabled'] = esc_attr($_POST['wplc_settings_enabled']);
        }
        if (isset($_POST['wplc_powered_by_link'])) {
            $wplc_data['wplc_powered_by_link'] = esc_attr($_POST['wplc_powered_by_link']);
        }
        if (isset($_POST['wplc_auto_pop_up'])) {
            $wplc_data['wplc_auto_pop_up'] = esc_attr($_POST['wplc_auto_pop_up']);
        }
        if (isset($_POST['wplc_require_user_info'])) {
            $wplc_data['wplc_require_user_info'] = esc_attr($_POST['wplc_require_user_info']);
        } else {
            $wplc_data['wplc_require_user_info'] = "0";
        }
        if (isset($_POST['wplc_loggedin_user_info'])) {
            $wplc_data['wplc_loggedin_user_info'] = esc_attr($_POST['wplc_loggedin_user_info']);
        } else {
            $wplc_data['wplc_loggedin_user_info'] = "0";
        }
        if (isset($_POST['wplc_user_alternative_text']) && $_POST['wplc_user_alternative_text'] != '') {
            $wplc_data['wplc_user_alternative_text'] = esc_attr($_POST['wplc_user_alternative_text']);
        } else {
            $wplc_data['wplc_user_alternative_text'] = __("Please click 'Start Chat' to initiate a chat with an agent", "wplivechat");
        }
        if (isset($_POST['wplc_enabled_on_mobile'])) {
            $wplc_data['wplc_enabled_on_mobile'] = esc_attr($_POST['wplc_enabled_on_mobile']);
        } else {
            $wplc_data['wplc_enabled_on_mobile'] = "0";
        }
        if (isset($_POST['wplc_display_name'])) {
            $wplc_data['wplc_display_name'] = esc_attr($_POST['wplc_display_name']);
        }
        if (isset($_POST['wplc_display_to_loggedin_only'])) {
            $wplc_data['wplc_display_to_loggedin_only'] = esc_attr($_POST['wplc_display_to_loggedin_only']);
        }

        if (isset($_POST['wplc_record_ip_address'])) {
            $wplc_data['wplc_record_ip_address'] = esc_attr($_POST['wplc_record_ip_address']);
        } else {
            $wplc_data['wplc_record_ip_address'] = "0";
        }
        if (isset($_POST['wplc_enable_msg_sound'])) {
            $wplc_data['wplc_enable_msg_sound'] = esc_attr($_POST['wplc_enable_msg_sound']);
        } else {
            $wplc_data['wplc_enable_msg_sound'] = "0";
        }

        if (isset($_POST['wplc_pro_na'])) {
            $wplc_data['wplc_pro_na'] = esc_attr($_POST['wplc_pro_na']);
        }
        if (isset($_POST['wplc_hide_when_offline'])) {
            $wplc_data['wplc_hide_when_offline'] = esc_attr($_POST['wplc_hide_when_offline']);
        }
        if (isset($_POST['wplc_pro_chat_email_address'])) {
            $wplc_data['wplc_pro_chat_email_address'] = esc_attr($_POST['wplc_pro_chat_email_address']);
        }
        if (isset($_POST['wplc_pro_offline1'])) {
            $wplc_data['wplc_pro_offline1'] = esc_attr($_POST['wplc_pro_offline1']);
        }
        if (isset($_POST['wplc_pro_offline2'])) {
            $wplc_data['wplc_pro_offline2'] = esc_attr($_POST['wplc_pro_offline2']);
        }
        if (isset($_POST['wplc_pro_offline3'])) {
            $wplc_data['wplc_pro_offline3'] = esc_attr($_POST['wplc_pro_offline3']);
        }
        if (isset($_POST['wplc_using_localization_plugin'])) {
            $wplc_data['wplc_using_localization_plugin'] = esc_attr($_POST['wplc_using_localization_plugin']);
        }


        if (isset($_POST['wplc_pro_fst1'])) {
            $wplc_data['wplc_pro_fst1'] = esc_attr($_POST['wplc_pro_fst1']);
        }
        if (isset($_POST['wplc_pro_fst2'])) {
            $wplc_data['wplc_pro_fst2'] = esc_attr($_POST['wplc_pro_fst2']);
        }
        if (isset($_POST['wplc_pro_fst3'])) {
            $wplc_data['wplc_pro_fst3'] = esc_attr($_POST['wplc_pro_fst3']);
        }
        if (isset($_POST['wplc_pro_sst1'])) {
            $wplc_data['wplc_pro_sst1'] = esc_attr($_POST['wplc_pro_sst1']);
        }
        if (isset($_POST['wplc_pro_sst1_survey'])) {
            $wplc_data['wplc_pro_sst1_survey'] = esc_attr($_POST['wplc_pro_sst1_survey']);
        }
        if (isset($_POST['wplc_pro_sst1e_survey'])) {
            $wplc_data['wplc_pro_sst1e_survey'] = esc_attr($_POST['wplc_pro_sst1e_survey']);
        }
        if (isset($_POST['wplc_pro_sst2'])) {
            $wplc_data['wplc_pro_sst2'] = esc_attr($_POST['wplc_pro_sst2']);
        }
        if (isset($_POST['wplc_pro_tst1'])) {
            $wplc_data['wplc_pro_tst1'] = esc_attr($_POST['wplc_pro_tst1']);
        }
        if (isset($_POST['wplc_pro_intro'])) {
            $wplc_data['wplc_pro_intro'] = esc_attr($_POST['wplc_pro_intro']);
        }
        if (isset($_POST['wplc_user_enter'])) {
            $wplc_data['wplc_user_enter'] = esc_attr($_POST['wplc_user_enter']);
        }
        if (isset($_POST['wplc_user_welcome_chat'])) {
            $wplc_data['wplc_user_welcome_chat'] = esc_attr($_POST['wplc_user_welcome_chat']);
        }


        if (isset($_POST['wplc_animation'])) {
            $wplc_data['wplc_animation'] = esc_attr($_POST['wplc_animation']);
        }
        if (isset($_POST['wplc_theme'])) {
            $wplc_data['wplc_theme'] = esc_attr($_POST['wplc_theme']);
        }
        if (isset($_POST['wplc_newtheme'])) {
            $wplc_data['wplc_newtheme'] = esc_attr($_POST['wplc_newtheme']);
        }

        if (isset($_POST['wplc_elem_trigger_action'])) {
            $wplc_data['wplc_elem_trigger_action'] = esc_attr($_POST['wplc_elem_trigger_action']);
        } else {
            $wplc_data['wplc_elem_trigger_action'] = "0";
        }
        if (isset($_POST['wplc_elem_trigger_type'])) {
            $wplc_data['wplc_elem_trigger_type'] = esc_attr($_POST['wplc_elem_trigger_type']);
        } else {
            $wplc_data['wplc_elem_trigger_type'] = "0";
        }
        if (isset($_POST['wplc_elem_trigger_id'])) {
            $wplc_data['wplc_elem_trigger_id'] = esc_attr($_POST['wplc_elem_trigger_id']);
        } else {
            $wplc_data['wplc_elem_trigger_id'] = "";
        }

        if (isset($_POST['wplc_agent_select']) && $_POST['wplc_agent_select'] != "") {
            $user_array = get_users(array(
                'meta_key' => 'wplc_ma_agent',
            ));
            if ($user_array) {
                foreach ($user_array as $user) {
                    $uid = $user->ID;
                    $wplc_ma_user = new WP_User($uid);
                    $wplc_ma_user->remove_cap('wplc_ma_agent');
                    delete_user_meta($uid, "wplc_ma_agent");
                    delete_user_meta($uid, "wplc_chat_agent_online");
                }
            }


            $uid = intval($_POST['wplc_agent_select']);
            $wplc_ma_user = new WP_User($uid);
            $wplc_ma_user->add_cap('wplc_ma_agent');
            update_user_meta($uid, "wplc_ma_agent", 1);
            update_user_meta($uid, "wplc_chat_agent_online", time());
        }


        if (isset($_POST['wplc_ban_users_ip'])) {
            $wplc_banned_ip_addresses = explode('<br />', nl2br(sanitize_text_field($_POST['wplc_ban_users_ip'])));
            foreach ($wplc_banned_ip_addresses as $key => $value) {
                $data[$key] = trim($value);
            }
            $wplc_banned_ip_addresses = maybe_serialize($data);

            update_option('WPLC_BANNED_IP_ADDRESSES', $wplc_banned_ip_addresses);
        }

        update_option('WPLC_SETTINGS', $wplc_data);
        if (isset($_POST['wplc_hide_chat'])) {
            update_option("WPLC_HIDE_CHAT", true);
        } else {
            update_option("WPLC_HIDE_CHAT", false);
        }


        $wplc_advanced_settings = array();
        if (isset($_POST['wplc_iterations'])) {
            $wplc_advanced_settings['wplc_iterations'] = esc_attr($_POST['wplc_iterations']);
        }
        if (isset($_POST['wplc_delay_between_loops'])) {
            $wplc_advanced_settings['wplc_delay_between_loops'] = esc_attr($_POST['wplc_delay_between_loops']);
        }
        update_option("wplc_advanced_settings", $wplc_advanced_settings);


        update_option('wplc_mail_type', $_POST['wplc_mail_type']);
        update_option('wplc_mail_host', $_POST['wplc_mail_host']);
        update_option('wplc_mail_port', $_POST['wplc_mail_port']);
        update_option('wplc_mail_username', $_POST['wplc_mail_username']);
        update_option('wplc_mail_password', $_POST['wplc_mail_password']);




        echo "<div class='updated'>";
        _e("Your settings have been saved.", "wplivechat");
        echo "</div>";
    }
    if (isset($_POST['action']) && $_POST['action'] == "wplc_submit_find_us") {
        if (function_exists('curl_version')) {
            $request_url = "http://www.wp-livechat.com/apif/rec.php";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $request_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);
            curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_HOST']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($ch);
            curl_close($ch);
        }
        echo "<div class=\"updated\"><p>" . __("Thank You for your feedback!", "wplivechat") . "</p></div>";
    }
    if (isset($_POST['wplc_nl_send_feedback'])) {
        if (wp_mail("nick@wp-livechat.com", "Plugin feedback", "Name: " . $_POST['wplc_nl_feedback_name'] . "\n\r" . "Email: " . $_POST['wplc_nl_feedback_email'] . "\n\r" . "Website: " . $_POST['wplc_nl_feedback_website'] . "\n\r" . "Feedback:" . $_POST['wplc_nl_feedback_feedback'])) {
            echo "<div id=\"message\" class=\"updated\"><p>" . __("Thank you for your feedback. We will be in touch soon", "wplc") . "</p></div>";
        } else {

            if (function_exists('curl_version')) {
                $request_url = "http://www.wp-livechat.com/apif/rec_feedback.php";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $request_url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);
                curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_HOST']);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $output = curl_exec($ch);
                curl_close($ch);
                echo "<div id=\"message\" class=\"updated\"><p>" . __("Thank you for your feedback. We will be in touch soon", "wplc") . "</p></div>";
            } else {
                echo "<div id=\"message\" class=\"error\">";
                echo "<p>" . __("There was a problem sending your feedback. Please log your feedback on ", "wplc") . "<a href='https://wp-livechat.com//support/' target='_BLANK'>https://wp-livechat.com//support/</a></p>";
                echo "</div>";
            }
        }
    }
}

function wplc_logout() {
    delete_transient('wplc_is_admin_logged_in');
}

add_action('wp_logout', 'wplc_logout');

function wplc_get_home_path() {
    $home = get_option('home');
    $siteurl = get_option('siteurl');
    if (!empty($home) && 0 !== strcasecmp($home, $siteurl)) {
        $wp_path_rel_to_home = str_ireplace($home, '', $siteurl); /* $siteurl - $home */
        $pos = strripos(str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']), trailingslashit($wp_path_rel_to_home));
        $home_path = substr($_SERVER['SCRIPT_FILENAME'], 0, $pos);
        $home_path = trailingslashit($home_path);
    } else {
        $home_path = ABSPATH;
    }
    return str_replace('\\', '/', $home_path);
}

/* Error Checks */
if (isset($_GET['page']) && $_GET['page'] == 'wplivechat-menu-settings') {
    if (is_admin()) {
        $wplc_error_count = 0;
        $wplc_admin_warnings = "<div class='error'>";
        if (!function_exists('set_time_limit')) {
            $wplc_admin_warnings .= "
                <p>" . __("WPLC: set_time_limit() is not enabled on this server. You may experience issues while using WP Live Chat Support as a result of this. Please get in contact your host to get this function enabled.", "wplivechat") . "</p>
            ";
            $wplc_error_count++;
        }
        if (ini_get('safe_mode')) {
            $wplc_admin_warnings .= "
                <p>" . __("WPLC: Safe mode is enabled on this server. You may experience issues while using WP Live Chat Support as a result of this. Please contact your host to get safe mode disabled.", "wplivechat") . "</p>
            ";
            $wplc_error_count++;
        }
        $wplc_admin_warnings .= "</div>";
        if ($wplc_error_count > 0) {
            echo $wplc_admin_warnings;
        }
    }
}

function wplc_extensions_menu() {

    if (isset($_GET['type']) && $_GET['type'] == "additional") {
        $additional = "nav-tab-active";
        $normal = "";
    } else {
        $normal = "nav-tab-active";
        $additional = "";
    }
    ?>
    <h2 class="nav-tab-wrapper">
        <a href="admin.php?page=wplivechat-menu-extensions-page" title="<?php _e("Add-ons", "wplivechat"); ?>" class="nav-tab <?php echo $normal; ?>"><?php _e("Add-ons", "wplivechat"); ?></a><a href="admin.php?page=wplivechat-menu-extensions-page&type=additional" title="<?php _e("Suggested Plugins", "wplivechat"); ?>" class="nav-tab  <?php echo $additional; ?>"><?php _e("Suggested Plugins", "wplivechat"); ?></a>
        <span style='float: right; bottom:-5px; position: relative;'><img src='<?php echo plugins_url('/images/codecabin.png', __FILE__); ?>' style="height:15px;" /></span>
    </h2> 
    <div id="tab_container">


    <?php
    if (isset($_GET['type']) && $_GET['type'] == "additional") {
        ?>

            <div class="wplc-extension wplc-plugin">
                <h3 class="wplc-extension-title"><?php _e("Sola Support Tickets", "wplivechat"); ?></h3>
                <a href="https://wordpress.org/plugins/sola-support-tickets/" title="<?php _e("Sola Support Tickets", "wplivechat"); ?>" target="_BLANK">
                    <img width="320" src="<?php echo plugins_url('/images/plugin2.jpg', __FILE__); ?>" class="attachment-showcase wp-post-image" alt="<?php _e("Sola Support Tickets", "wplivechat"); ?>" title="<?php _e("Sola Support Tickets", "wplivechat"); ?>">
                </a>
                <p></p>
                <p><?php _e("The easiest to use Help Desk & Support Ticket plugin. Create a support help desk quickly and easily with Sola Support Tickets.", "wplivechat"); ?></p>
                <p></p>
                <a href="https://wordpress.org/plugins/sola-support-tickets/" title="<?php _e("Sola Support Tickets", "wplivechat"); ?>" class="button-secondary" target="_BLANK"><?php _e("Get this Plugin", "wplivechat"); ?></a>
            </div>

            <div class="wplc-extension wplc-plugin">
                <h3 class="wplc-extension-title"><?php _e("Nifty Newsletters", "wplivechat"); ?></h3>
                <a href="https://wordpress.org/plugins/sola-newsletters/" title="<?php _e("Nifty Newsletters", "wplivechat"); ?>" target="_BLANK">
                    <img width="320" src="<?php echo plugins_url('/images/plugin1.jpg', __FILE__); ?>" class="attachment-showcase wp-post-image" alt="<?php _e("Nifty Newsletters", "wplivechat"); ?>" title="<?php _e("Nifty Newsletters", "wplivechat"); ?>">
                </a>
                <p></p>
                <p><?php _e("Create and send newsletters, automatic post notifications and autoresponders that are modern and beautiful with Nifty Newsletters.", "wplivechat"); ?></p>
                <p></p>
                <a href="https://wordpress.org/plugins/sola-newsletters/" title="<?php _e("Nifty Newsletters", "wplivechat"); ?>" class="button-secondary" target="_BLANK"><?php _e("Get this Plugin", "wplivechat"); ?></a>
            </div>


    <?php
    } else {
        $filter1 = "all";
        $filter2 = "all";

        if (isset($_GET['filter1'])) {
            $filter1 = $_GET['filter1'];
        }
        if (isset($_GET['filter2'])) {
            $filter2 = $_GET['filter2'];
        }

        $style_strong = 'style="font-weight:bold;"';
        $style_normal = 'style="font-weight:normal;"';

        $filter1_all_style = $style_normal;
        $filter1_free_style = $style_normal;
        $filter1_paid_style = $style_normal;
        $filter2_both_style = $style_normal;
        $filter2_free_style = $style_normal;
        $filter2_pro_style = $style_normal;

        if ($filter1 == "all") {
            $filter1_all_style = $style_strong;
        } else if ($filter1 == "free") {
            $filter1_free_style = $style_strong;
        } else if ($filter1 == "paid") {
            $filter1_paid_style = $style_strong;
        } else {
            $filter1_all_style = $style_strong;
        }
        if ($filter2 == "all") {
            $filter2_both_style = $style_strong;
        } else if ($filter2 == "free") {
            $filter2_free_style = $style_strong;
        } else if ($filter2 == "pro") {
            $filter2_pro_style = $style_strong;
        } else {
            $filter2_both_style = $style_strong;
        }


        echo "<p><div style='display:block; overflow:auto; clear:both;'>";
        echo "<strong>" . __("Price:", "wplivechat") . "</strong> ";
        echo "<a href='admin.php?page=wplivechat-menu-extensions-page&filter1=all&filter2=" . $filter2 . "' $filter1_all_style>" . __("All", "wplivechat") . "</a> |";
        echo "<a href='admin.php?page=wplivechat-menu-extensions-page&filter1=free&filter2=" . $filter2 . "' $filter1_free_style>" . __("Free", "wplivechat") . "</a> |";
        echo "<a href='admin.php?page=wplivechat-menu-extensions-page&filter1=paid&filter2=" . $filter2 . "' $filter1_paid_style>" . __("Paid", "wplivechat") . "</a>";
        echo "</div></p>";
        echo "<p><div style='display:block; overflow:auto; clear:both;'>";
        echo "<strong>" . __("For:", "wplivechat") . "</strong> ";
        echo "<a href='admin.php?page=wplivechat-menu-extensions-page&filter2=all&filter1=" . $filter1 . "' $filter2_both_style>" . __("Both", "wplivechat") . "</a> |";
        echo "<a href='admin.php?page=wplivechat-menu-extensions-page&filter2=free&filter1=" . $filter1 . "' $filter2_free_style>" . __("Free version", "wplivechat") . "</a> |";
        echo "<a href='admin.php?page=wplivechat-menu-extensions-page&filter2=pro&filter1=" . $filter1 . "' $filter2_pro_style>" . __("Pro version", "wplivechat") . "</a>";
        echo "</div></p>";


        $response = wp_remote_post("https://ccplugins.co/api-wplc-extensions", array(
            'method' => 'POST',
            'body' => array(
                'action' => 'extensions',
                'filter1' => $filter1,
                'filter2' => $filter2
            )
                )
        );
        $data = json_decode($response['body']);

        global $wplc_version;
        $wplc_version = str_replace(",", "", $wplc_version);


        if ($data) {
            $output = "";
            foreach ($data as $extension) {
                if (!isset($extension->fromversion)) {
                    $extension->fromversion = 0;
                }
                if (intval($wplc_version) >= intval($extension->fromversion)) {


                    $output .= '<div class="wplc-extension">';
                    $output .= '<h3 class="wplc-extension-title">' . $extension->title . '</h3>';
                    $output .= '<a href="' . $extension->link . '" title="' . $extension->title . '" target="_BLANK">';
                    $output .= '<img width="320" height="200" src="' . $extension->image . '" class="attachment-showcase wp-post-image" alt="' . $extension->title . '" title="' . $extension->title . '">';
                    $output .= '</a>';
                    $output .= '<p></p>';
                    $output .= '<p><div class="wplc-extension-label-box">';
                    $output .= '</div></p>';
                    $output .= '<p>' . $extension->description . '</p>';
                    if ($extension->slug != false && is_plugin_active($extension->slug . "/" . $extension->slug . ".php")) {
                        $button = '<a href="javascriot:void(0);" title="" disabled class="button-secondary">' . __("Already installed", "wplivechat") . '</a>';
                    } else {
                        $button = '<a href="' . $extension->link . '" title="' . $extension->title . '" class="button-secondary" target="_BLANK">' . $extension->button_text . '</a>';
                    }
                    $output .= $button;
                    $output .= '</div>';
                }
            }
            echo $output;
        }
        ?>



    <?php } ?>






    </div>
    <?php
}

function wplc_support_menu() {
    wplc_stats("support");
    ?>   
    <h1><?php _e("WP Live Chat Support", "wplivechat"); ?></h1>
    <div class="wplc_row">
        <div class='wplc_row_col' style='background-color:#FFF;'>
            <h2><i class="fa fa-book fa-2x"></i> <?php _e("Documentation", "wplivechat"); ?></h2>
            <hr />
            <p><?php _e("Getting started? Read through some of these articles to help you along your way.", "wplivechat"); ?></p>
            <p><strong><?php _e("Documentation:", "wplivechat"); ?></strong></p>
            <ul>
                <li><a href='https://wp-livechat.com/documentation/minimum-system-requirements/' target='_BLANK' title='<?php _e("Minimum System Requirements", "wplivechat"); ?>'><?php _e("Minimum System Requirements", "wplivechat"); ?></a></li>
                <li><a href='https://wp-livechat.com/documentation/do-i-have-to-be-logged-into-the-dashboard-to-chat-with-visitors/' target='_BLANK' title='<?php _e("Do I have to be logged into the dashboard to chat with visitors?", "wplivechat"); ?>'><?php _e("Do I have to be logged into the dashboard to chat with visitors?", "wplivechat"); ?></a></li>
                <li><a href='https://wp-livechat.com/documentation/what-are-quick-responses/' target='_BLANK' title='<?php _e("What are Quick Responses?", "wplivechat"); ?>'><?php _e("What are Quick Responses?", "wplivechat"); ?></a></li>
                <li><a href='https://wp-livechat.com/documentation/troubleshooting/is-this-plugin-multi-site-friendly/' target='_BLANK' title='<?php _e("Can I use this plugin on my multi-site?", "wplivechat"); ?>'><?php _e("Can I use this plugin on my multi-site?", "wplivechat"); ?></a></li>
                <li><a href='https://wp-livechat.com/documentation/how-do-i-disable-apc-object-cache/' target='_BLANK' title='<?php _e("How do I disable APC Object Cache?", "wplivechat"); ?>'><?php _e("How do I disable APC Object Cache?", "wplivechat"); ?></a></li>
                <li><a href='https://wp-livechat.com/documentation/do-you-have-a-mobile-app/' target='_BLANK' title='<?php _e("Do you have a mobile app?", "wplivechat"); ?>'><?php _e("Do you have a mobile app?", "wplivechat"); ?></a></li>
                <li><a href='https://wp-livechat.com/documentation/how-do-i-check-for-javascript-errors-on-my-site/' target='_BLANK' title='<?php _e("How do I check for JavaScript errors on my site?", "wplivechat"); ?>'><?php _e("How do I check for JavaScript errors on my site?", "wplivechat"); ?></a></li>
            </ul>
        </div>
        <div class='wplc_row_col' style='background-color:#FFF;'>
            <h2><i class="fa fa-exclamation-circle fa-2x"></i> <?php _e("Troubleshooting", "wplivechat"); ?></h2>
            <hr />
            <p><?php _e("WP Live Chat Support  has a diverse and wide range of features which may, from time to time, run into conflicts with the thousands of themes and other plugins on the market.", "wplivechat"); ?></p>
            <p><strong><?php _e("Common issues:", "wplivechat"); ?></strong></p>
            <ul>
                <li><a href='https://wp-livechat.com/documentation/troubleshooting/the-chat-box-doesnt-show-up/' target='_BLANK' title='<?php _e("The chat box doesnt show up", "wplivechat"); ?>'><?php _e("The chat box doesnt show up", "wplivechat"); ?></a></li>
                <li><a href='https://wp-livechat.com/documentation/the-chat-window-disappears-when-i-logout-or-go-offline/' target='_BLANK' title='<?php _e("The chat window disappears when I logout or go offline", "wplivechat"); ?>'><?php _e("The chat window disappears when I logout or go offline", "wplivechat"); ?></a></li>
                <li><a href='https://wp-livechat.com/this-chat-has-already-been-answered-please-close-the-chat-window/' target='_BLANK' title='<?php _e("This chat has already been answered. Please close the chat window", "wplivechat"); ?>'><?php _e("This chat has already been answered. Please close the chat window", "wplivechat"); ?></a></li>
                <li><a href='https://wp-livechat.com/messages-only-show-when-i-refresh-the-chat-window/' target='_BLANK' title='<?php _e("Messages only show when I refresh the chat window", "wplivechat"); ?>'><?php _e("Messages only show when I refresh the chat window", "wplivechat"); ?></a></li>
                <li><a href='https://wp-livechat.com/documentation/im-not-getting-any-notifications-of-a-new-chat/' target='_BLANK' title='<?php _e("I'm not getting any notifications of a new chat", "wplivechat"); ?>'><?php _e("I'm not getting any notifications of a new chat", "wplivechat"); ?></a></li>
                <li><a href='https://wp-livechat.com/the-chat-window-never-goes-offline/' target='_BLANK' title='<?php _e("The chat window never goes offline", "wplivechat"); ?>'><?php _e("The chat window never goes offline", "wplivechat"); ?></a></li>
            </ul>
        </div>
        <div class='wplc_row_col' style='background-color:#FFF;'>
            <h2><i class="fa fa-bullhorn fa-2x"></i> <?php _e("Support", "wplivechat"); ?></h2>
            <hr />
            <p><?php _e("Still need help? Use one of these links below.", "wplivechat"); ?></p>
            <ul>
                <li><a href='https://wp-livechat.com/support/' target='_BLANK' title='<?php _e("Support desk", "wplivechat"); ?>'><?php _e("Support desk", "wplivechat"); ?></a></li>
                <li><a href='https://wp-livechat.com/contact-us/' target='_BLANK' title='<?php _e("Contact us", "wplivechat"); ?>'><?php _e("Contact us", "wplivechat"); ?></a></li>
            </ul>
        </div>
    </div>
    <?php
}

if (!function_exists("wplc_ic_initiate_chat_button")) {
    add_action('admin_enqueue_scripts', 'wp_button_pointers_load_scripts');
}

function wp_button_pointers_load_scripts($hook) {

    if ($hook != 'toplevel_page_wplivechat-menu') {
        return;
    }   // stop if we are not on the right page


    $pointer_localize_strings = array(
        "initiate" => "<h3>" . __("Initiate Chats", "wplivechat") . "</h3><p>" . __("With the Pro add-on of WP Live Chat Support, you can", "wplivechat") . " <a href='http://www.wp-livechat.com/purchase-pro/?utm_source=plugin&utm_medium=link&utm_campaign=initiate1_pointer' title='" . __("see who's online and initiate chats", "wplivechat") . "' target=\"_BLANK\">" . __("initiate chats", "wplivechat") . "</a> " . __("with your online visitors with the click of a button.", "wplivechat") . " <br /><br /><a href='http://www.wp-livechat.com/purchase-pro/?utm_source=plugin&utm_medium=link&utm_campaign=initiate2_pointer' title='" . __("Buy the Pro add-on now (once off payment).", "wplivechat") . "' target=\"_BLANK\"><strong>" . __("Buy the Pro add-on now (once off payment).", "wplivechat") . "</strong></a></p>",
        "chats" => "<h3>" . __("Multiple Chats", "wplivechat") . "</h3><p>" . __("With the Pro add-on of WP Live Chat Support, you can", "wplivechat") . " <a href='http://www.wp-livechat.com/purchase-pro/?utm_source=plugin&utm_medium=link&utm_campaign=morechats1_pointer' title='" . __("accept and handle multiple chats.", "wplivechat") . "' target=\"_BLANK\">" . __("accept and handle multiple chats.", "wplivechat") . "</a><br /><br /><a href='http://www.wp-livechat.com/purchase-pro/?utm_source=plugin&utm_medium=link&utm_campaign=morechats2_pointer' title='" . __("Buy the Pro add-on now (once off payment).", "wplivechat") . "' target=\"_BLANK\"><strong>" . __("Buy the Pro add-on now (once off payment).", "wplivechat") . "</strong></a></p>",
        "agent_info" => "<h3>" . __("Add unlimited agents", "wplivechat") . "</h3><p><a href='http://www.wp-livechat.com/purchase-pro/?utm_source=plugin&utm_medium=link&utm_campaign=unlimited_agents1_pointer' title='" . __("Add unlimited agents", "wplivechat") . "' target=\"_BLANK\">" . __("Add unlimited agents", "wplivechat") . "</a> " . __(" with the Pro add-on of WP Live Chat Support", "wplivechat") . " " . "<a href='http://www.wp-livechat.com/purchase-pro/?utm_source=plugin&utm_medium=link&utm_campaign=unlimited_agents2_pointer' target='_BLANK'>" . __("(once off payment).", "wplivechat") . "</a></p>"
    );


    wp_enqueue_style('wp-pointer');
    wp_enqueue_script('wp-pointer');
    wp_register_script('wplc-user-admin-pointer', plugins_url('/js/wplc-admin-pointers.js', __FILE__), array('wp-pointer'));
    wp_enqueue_script('wplc-user-admin-pointer');
    wp_localize_script('wplc-user-admin-pointer', 'pointer_localize_strings', $pointer_localize_strings);
}

function wplc_footer_mod($footer_text) {
    if (isset($_GET['page']) && ($_GET['page'] == 'wplivechat-menu' || $_GET['page'] == 'wplivechat-menu-extensions-page' || $_GET['page'] == 'wplivechat-menu-settings' || $_GET['page'] == 'wplivechat-menu-offline-messages' || $_GET['page'] == 'wplivechat-menu-history')) {
        $footer_text_mod = sprintf(__('Thank you for using <a href="%1$s" target="_blank">WP Live Chat Support</a>! Please <a href="%2$s" target="_blank">rate us</a> on <a href="%2$s" target="_blank">WordPress.org</a>', 'wplivechat'), 'https://wp-livechat.com/?utm_source=plugin&utm_medium=link&utm_campaign=footer', 'https://wordpress.org/support/view/plugin-reviews/wp-live-chat-support?filter=5#postform'
        );

        return str_replace('</span>', '', $footer_text) . ' | ' . $footer_text_mod . ' | ' . __('WP Live Chat Support is a product of', 'wplivechat') . ' <a target="_BLANK" href="http://codecabin.co.za/?utm_source=livechat&utm_medium=link&utm_campaign=footer" border="0"><img src="' . plugins_url('/images/codecabin.png', __FILE__) . '" style="height:10px;"/></span>';
    } else {
        return $footer_text;
    }
}

add_filter('admin_footer_text', 'wplc_footer_mod');


add_filter("wplc_filter_admin_long_poll_chat_loop_iteration", "wplc_filter_control_wplc_admin_long_poll_chat_iteration", 1, 3);

function wplc_filter_control_wplc_admin_long_poll_chat_iteration($array, $post_data, $i) {
    if (isset($post_data['action_2']) && $post_data['action_2'] == "wplc_long_poll_check_user_opened_chat") {
        $chat_status = wplc_return_chat_status(sanitize_text_field($post_data['cid']));
        if ($chat_status == 3) {
            $array['action'] = "wplc_user_open_chat";
        }
    } else {

        $new_chat_status = wplc_return_chat_status(sanitize_text_field($post_data['cid']));
        if ($new_chat_status != $post_data['chat_status']) {
            $array['chat_status'] = $new_chat_status;
            $array['action'] = "wplc_update_chat_status";
        }
        $new_chat_message = wplc_return_admin_chat_messages(sanitize_text_field($post_data['cid']));
        if ($new_chat_message) {

            $array['chat_message'] = $new_chat_message;
            $array['action'] = "wplc_new_chat_message";
        }
    }

    return $array;
}

add_action("wplc_hook_agents_settings", "wplc_hook_control_agents_settings", 10);

function wplc_hook_control_agents_settings() {
    $user_array = get_users(array(
        'meta_key' => 'wplc_ma_agent',
    ));

    echo "<h3>" . __('Current Users that are Chat Agents', 'wplivechat') . "</h3>";
    $wplc_agents = "<div class='wplc_agent_container'><ul>";

    if ($user_array) {
        foreach ($user_array as $user) {

            $wplc_agents .= "<li id=\"wplc_agent_li_" . $user->ID . "\">";
            $wplc_agents .= "<p><img src=\"//www.gravatar.com/avatar/" . md5($user->user_email) . "?s=80&d=mm\" /></p>";
            $check = get_user_meta($user->ID, "wplc_chat_agent_online");
            if ($check) {
                $wplc_agents .= "<span class='wplc_status_box wplc_type_returning'>" . __("Online", "wplivechat") . "</span>";
            }
            $default_agent_id = $user->ID;
            $default_agent_user = $user->display_name;
            $wplc_agents .= "<h3>" . $user->display_name . "</h3>";

            $wplc_agents .= "<small>" . $user->user_email . "</small>";




            $wplc_agents .= "</li>";
        }
    }
    echo $wplc_agents;
    $temp_line = "<select name='wplc_agent_select' id='wplc_agent_select'><option value=''>" . __("Select", "wplivechat") . "</option>";

    $blogusers = get_users(array('role' => 'administrator', 'fields' => array('display_name', 'id', 'user_email')));
    if ($blogusers) {
        foreach ($blogusers as $user) {
            $is_agent = get_user_meta(esc_html($user->id), 'wplc_ma_agent', true);
            $temp_line2 = '<option id="wplc_selected_agent_' . esc_html($user->id) . '" value="' . esc_html($user->id) . '">' . esc_html($user->display_name) . ' (' . __('Administrator', 'wplivechat') . ')</option>';
            if (!$is_agent) {
                $temp_line .= $temp_line2;
            }
        }
    }

    $blogusers = get_users(array('role' => 'editor', 'fields' => array('display_name', 'id', 'user_email')));
    if ($blogusers) {
        foreach ($blogusers as $user) {
            $is_agent = get_user_meta(esc_html($user->id), 'wplc_ma_agent', true);
            $temp_line2 = '<option id="wplc_selected_agent_' . esc_html($user->id) . '" value="' . esc_html($user->id) . '">' . esc_html($user->display_name) . ' (' . __('Editor', 'wplivechat') . ')</option>';
            if (!$is_agent) {
                $temp_line .= $temp_line2;
            }
        }
    }

    $blogusers = get_users(array('role' => 'author', 'fields' => array('display_name', 'id', 'user_email')));
    if ($blogusers) {
        foreach ($blogusers as $user) {
            $is_agent = get_user_meta(esc_html($user->id), 'wplc_ma_agent', true);
            $temp_line2 = '<option id="wplc_selected_agent_' . esc_html($user->id) . '" value="' . esc_html($user->id) . '">' . esc_html($user->display_name) . ' (' . __('Author', 'wplivechat') . ')</option>';
            if (!$is_agent) {
                $temp_line .= $temp_line2;
            }
        }
    }

    $temp_line .= "</select> ";
    ?>


    <li style='width:150px;' id='wplc_add_new_agent_box'>
        <p><i class='fa fa-plus-circle fa-4x' style='color:#ccc;' ></i></p>
        <h3><?php _e("Add New Agent", "wplivechat"); ?></h3>
        <p><button class='button button-secondary' id='wplc_add_agent' disabled style=><?php _e("Add Agent", "wplivechat"); ?></button></p>
        <p style='font-size:0.8em'><?php _e("Add as many agents as you need with the ", "wplivechat") ?> <a href="http://wp-livechat.com/purchase-pro/?utm_source=plugin&utm_medium=link&utm_campaign=multipleAgents" target="_BLANK"><?php _e("Pro version.", "wplivechat") ?></a></p>
    </li>
    </ul>
    <?php
    $agent_text = sprintf(__('Change the default chat agent from <strong>%1$s</strong> to ', 'wplivechat'), $default_agent_user
    );
    echo $agent_text . $temp_line;
    ?>

    </div>

    <hr/>
    <?php
}

function wplc_get_chat_data($cid) {
    global $wpdb;
    global $wplc_tblname_chats;
    $sql = "SELECT * FROM $wplc_tblname_chats WHERE `id` = '" . intval($cid) . "' LIMIT 1";
    $results = $wpdb->get_results($sql);
    if (isset($results[0])) {
        $result = $results[0];
    } else {
        $result = null;
    }
    $result = apply_filters("wplc_filter_get_chat_data", $result, $cid);
    return $result;
}

function wplc_get_chat_messages($cid) {
    global $wpdb;
    global $wplc_tblname_msgs;

    $results = $wpdb->get_results(
            "
        SELECT *
        FROM $wplc_tblname_msgs
        WHERE `chat_sess_id` = '$cid'
        ORDER BY `timestamp` ASC
        LIMIT 0, 100
        "
    );

    if (isset($results[0])) {
        
    } else {
        $results = null;
    }
    $results = apply_filters("wplc_filter_get_chat_messages", $results, $cid);

    if ($results == "null") {
        return false;
    } else {
        return $results;
    }
}

function wplc_build_api_check($page_content, $data) {
    $link = "#";
    $image = "https://ccplugins.co/api-wplc-extensions/images/add-on0.jpg";
    if ($data['string'] == "Multiple Agents") {
        $link = "";
        $image = "https://ccplugins.co/api-wplc-extensions/images/Agents-Small.jpg";
    }
    if ($data['string'] == "Cloud Server") {
        $link = "";
        $image = "https://ccplugins.co/api-wplc-extensions/images/Cloud-Small.jpg";
    }
    if ($data['string'] == "Advanced Chat Box Control") {
        $link = "https://wp-livechat.com/extensions/advanced-chat-control/";
        $image = "https://ccplugins.co/api-wplc-extensions/images/AdvancedChatBox-Small.jpg";
    }
    if ($data['string'] == "Choose When Online") {
        $link = "";
        $image = "https://ccplugins.co/api-wplc-extensions/images/ChooseOnline-Small.jpg";
    }
    if ($data['string'] == "Encryption") {
        $link = "";
        $image = "https://ccplugins.co/api-wplc-extensions/images/Encryption-Small.jpg";
    }
    if ($data['string'] == "Mobile and Desktop App") {
        $link = "";
        $image = "https://ccplugins.co/api-wplc-extensions/images/MobileDesktop-Small.jpg";
    }
    if ($data['string'] == "Initiate Chats") {
        $link = "";
        $image = "https://ccplugins.co/api-wplc-extensions/images/InitiateChat-Small.jpg";
    }
    if ($data['string'] == "Include Exclude Pages") {
        $link = "";
        $image = "https://ccplugins.co/api-wplc-extensions/images/IncludeAndExclude-Small.jpg";
    }


    if ($data['string'] == "WP Live Chat Support Pro") {
        $link = "";
        $image = "https://ccplugins.co/api-wplc-extensions/images/add-on0.jpg";
    }






    $page_content .= '<div class="wplc-extension" style="height:480px;">';
    $page_content .= '<a href="' . $link . '" title="' . $data['string'] . '" target="_BLANK" style=" text-decoration:none;"><h3 class="wplc-extension-title" style="text-decoration:none;">' . $data['string'] . '</h3></a>';
    $page_content .= '<img width="320" height="200" src="' . $image . '" alt="' . $data['string'] . '" title="' . $data['string'] . '">';
    $page_content .= '<p>' . __('API Key', 'wplivechat') . '<br />';
    $page_content .= "        <form name='" . $data['form_name'] . "' action='' method='POST'>";
    $page_content .= "            <input type='text' name='" . $data['option_name'] . "' id='" . $data['option_name'] . "' value='" . get_option($data['option_name']) . "' style='width: 250px;'/>";
    $page_content .= "            <input type='submit' name='" . $data['button'] . "' id='" . $data['button'] . "' value='" . __("Verify", "wplivechat") . "' />";
    $page_content .= "        </form>";
    $page_content .= '</p>';
    $page_content .= '<p>' . __('Status: ', 'wplivechat');
    if (isset($data['data']['status']) && $data['data']['status'] == "OK") {
        update_option($data['is_valid'], 1);
        $page_content .= "<span style='color: white; font-weight: bold; background-color: green; border-radius: 5px; padding: 3px;'>" . __('Valid', 'wplivechat') . '</span>';
        $page_content .= '<a href="https://wp-livechat.com/my-account/" title="' . __('Manage this extension', 'wplivechat') . '" class="button-secondary" target="_BLANK">' . __('Manage this extension', 'wplivechat') . '</a>';
    } else {
        update_option($data['is_valid'], 0);
        $page_content .= "<span style='color: white; font-weight: bold; background-color: red; border-radius: 5px; padding: 3px;'>" . __('Invalid', 'wplivechat') . '</span>';
        $page_content .= '<a href="https://wp-livechat.com/my-account/" title="' . __('Manage this extension', 'wplivechat') . '" class="button-secondary" target="_BLANK">' . __('Manage this extension', 'wplivechat') . '</a>';
    }
    $page_content .= '</p>';
    $page_content .= '<div style="dispaly:block; width:100%; height:100px; overflow:auto;">';
    if (isset($data['data']['domains']) && !empty($data['data']['domains'])) {
        $page_content .= '<span><strong>' . __("Linked Domains", "wplivechat") . '</strong></span><ol>';
        foreach ($data['data']['domains'] as $domain) {
            $page_content .= '<li>' . $domain . '</li>';
        }
        $page_content .= '</ol>';
    } else {
        $page_content .= '              <span>' . $data['data']['message'] . '</span>';
    }
    $page_content .= '</div>';

    $page_content .= '</div>';




    return $page_content;
}

add_filter("wplc_filter_relevant_extensions_main", "wplc_filter_control_relevant_extensions_main_proe");

function wplc_filter_control_relevant_extensions_main_proe($text) {
    if (function_exists("wplc_hook_control_intiate_check")) {
        return $text;
    }

    $rel_name = __("Pro Add-on", "wplivechat");
    $rel_image = "https://ccplugins.co/api-wplc-extensions/images/add-on0.jpg";
    $rel_link = "http://www.wp-livechat.com/purchase-pro/?utm_source=plugin&utm_medium=link&utm_campaign=relevant_pro";
    $text .= '<div class="wplc-extension relevant_extension">';
    $text .= '<a href="' . $rel_link . '" title="' . $rel_name . '" target="_BLANK" style="float:left;">';
    $text .= '<img src="' . $rel_image . '" class="attachment-showcase wp-post-image" alt="' . $rel_name . '" title="' . $rel_name . '" >';
    $text .= '</a>';
    $text .= '<div class="float:left; padding-left:10px;">';
    $text .= '<h3 class="wplc-extension-title">' . $rel_name . '</h3>';
    $text .= '<p>' . __("Get unlimited agents, initiate chats, advanced chat box control, encryption and more with the Pro add-on.", "wplivechat") . '</p>';
    $text .= '</div>';
    $text .= '<a href="' . $rel_link . '" title="' . $rel_name . '" class="button-secondary" target="_BLANK">' . __("Get this extension", "wplivechat") . '</a>';
    $text .= '</div>';

    return $text;
}

add_filter("wplc_filter_relevant_extensions_main", "wplc_filter_control_relevant_extensions_main_mobile");

function wplc_filter_control_relevant_extensions_main_mobile($text) {
    if (function_exists("wplc_mobile_check_if_logged_in")) {
        return $text;
    }

    $rel_name = __("Mobile & Desktop App", "wplivechat");
    $rel_image = "https://ccplugins.co/api-wplc-extensions/images/MobileDesktop-Icon.jpg";
    $rel_link = "https://wp-livechat.com/extensions/mobile-desktop-app-extension/?utm_source=plugin&amp;utm_medium=link&amp;utm_campaign=relevant_mobile";
    $text .= '<div class="wplc-extension relevant_extension">';
    $text .= '<a href="' . $rel_link . '" title="' . $rel_name . '" target="_BLANK" style="float:left;">';
    $text .= '<img src="' . $rel_image . '" class="attachment-showcase wp-post-image" alt="' . $rel_name . '" title="' . $rel_name . '" >';
    $text .= '</a>';
    $text .= '<div class="float:left; padding-left:10px;">';
    $text .= '<h3 class="wplc-extension-title">' . $rel_name . '</h3>';
    $text .= '<p>' . __("Answer chats directly from your mobile phone or desktop with our mobile app and desktop client", "wplivechat") . '</p>';
    $text .= '</div>';
    $text .= '<a href="' . $rel_link . '" title="' . $rel_name . '" class="button-secondary" target="_BLANK">' . __("Get this extension", "wplivechat") . '</a>';
    $text .= '</div>';

    return $text;
}

add_filter("wplc_filter_relevant_extensions_main", "wplc_filter_control_relevant_extensions_main_cloud");

function wplc_filter_control_relevant_extensions_main_cloud($text) {
    if (function_exists("wplc_cloud_filter_control_chat_messages")) {
        return $text;
    }

    $rel_name = __("Cloud Server", "wplivechat");
    $rel_image = "https://ccplugins.co/api-wplc-extensions/images/Cloud-Icon.jpg";
    $rel_link = "https://wp-livechat.com/extensions/cloud-server-extension/?utm_source=plugin&amp;utm_medium=link&amp;utm_campaign=relevant_cloud";
    $text .= '<div class="wplc-extension relevant_extension">';
    $text .= '<a href="' . $rel_link . '" title="' . $rel_name . '" target="_BLANK" style="float:left;">';
    $text .= '<img src="' . $rel_image . '" class="attachment-showcase wp-post-image" alt="' . $rel_name . '" title="' . $rel_name . '" >';
    $text .= '</a>';
    $text .= '<div class="float:left; padding-left:10px;">';
    $text .= '<h3 class="wplc-extension-title">' . $rel_name . '</h3>';
    $text .= '<p>' . __("Reduce the resources required by your server - use our cloud server to host your chats.", "wplivechat") . '</p>';
    $text .= '</div>';
    $text .= '<a href="' . $rel_link . '" title="' . $rel_name . '" class="button-secondary" target="_BLANK">' . __("Get this extension", "wplivechat") . '</a>';
    $text .= '</div>';

    return $text;
}

add_filter("wplc_filter_relevant_extensions_chatbox", "wplc_filter_control_relevant_extensions_chatbox_proe");

function wplc_filter_control_relevant_extensions_chatbox_proe($text) {
    if (function_exists("wplc_hook_control_intiate_check")) {
        return $text;
    }

    $rel_name = __("Pro Add-on", "wplivechat");
    $rel_image = "https://ccplugins.co/api-wplc-extensions/images/add-on0.jpg";
    $rel_link = "http://www.wp-livechat.com/purchase-pro/?utm_source=plugin&utm_medium=link&utm_campaign=relevant_pro_chatbox";
    $text .= '<div class="wplc-extension relevant_extension">';
    $text .= '<a href="' . $rel_link . '" title="' . $rel_name . '" target="_BLANK" style="float:left;">';
    $text .= '<img src="' . $rel_image . '" class="attachment-showcase wp-post-image" alt="' . $rel_name . '" title="' . $rel_name . '" >';
    $text .= '</a>';
    $text .= '<div class="float:left; padding-left:10px;">';
    $text .= '<h3 class="wplc-extension-title">' . $rel_name . '</h3>';
    $text .= '<p>' . __("Get unlimited agents, initiate chats, advanced chat box control, encryption and more with the Pro add-on.", "wplivechat") . '</p>';
    $text .= '</div>';
    $text .= '<a href="' . $rel_link . '" title="' . $rel_name . '" class="button-secondary" target="_BLANK">' . __("Get this extension", "wplivechat") . '</a>';
    $text .= '</div>';

    return $text;
}

/**
 * Add to the chat box settings page
 * @return void
 * @since  1.0.00
 * @author Nick Duncan <nick@codecabin.co.za>
 */
add_action('wplc_hook_admin_settings_chat_box_settings_after', 'wplc_hook_control_settings_page_relevant_extensions_acbc', 9);

function wplc_hook_control_settings_page_relevant_extensions_acbc() {
    $check = apply_filters("wplc_filter_relevant_extensions_chatbox", "");
    if ($check != "") {
        echo "<hr />";
        echo "<div style='padding:1%; width:98%; display:block; overflow:auto;'>";
        echo "<div class='display:block; font-weight:bold;'><strong>" . __("Relevant Extensions", 'wplivechat') . "</strong><br /><br /></div>";
        echo "";
        echo $check;
        echo "";
        echo "";
        echo "</div>";
    }
}

/**
 * Add to the chat box settings page
 * @return void
 * @since  1.0.00
 * @author Nick Duncan <nick@codecabin.co.za>
 */
add_action('wplc_hook_admin_settings_main_settings_after', 'wplc_hook_control_settings_page_relevant_extensions_main', 9);

function wplc_hook_control_settings_page_relevant_extensions_main() {
    $check = apply_filters("wplc_filter_relevant_extensions_main", "");
    if ($check != "") {
        echo "<hr />";
        echo "<div style='padding:1%; width:98%; display:block; overflow:auto;'>";
        echo "<div class='display:block; font-weight:bold;'><strong>" . __("Relevant Extensions", 'wplivechat') . "</strong><br /><br /></div>";
        echo "";
        echo $check;
        echo "";
        echo "";
        echo "</div>";
    }
}

add_filter("wplc_filter_hovercard_bottom_before", "wplc_filter_control_hovercard_bottom_before_pro", 5, 1);

function wplc_filter_control_hovercard_bottom_before_pro($content) {
    $wplc_settings = get_option("WPLC_SETTINGS");
    if (isset($wplc_settings["wplc_powered_by_link"]) && $wplc_settings["wplc_powered_by_link"] == 0) {
        
    } else if (!isset($wplc_settings["wplc_powered_by_link"])) {
        
    } else {
        $content .= "<a title='" . __("Powered By WP Live Chat Support", "wplivechat") . "' target='_blank' rel='nofollow' href='https://wp-livechat.com?utm_source=poweredby&utm_medium=click&utm_campaign=" . esc_html(site_url()) . "' class='wplc_powered_by_link'>" . __("Powered By WP Live Chat Support", "wplivechat") . "</a>"; //Empty Content to start
    }
    return $content;
}

add_action('init', 'wplc_admin_download_new_chat_history');

function wplc_admin_download_new_chat_history() {
    if (isset($_GET['action']) && $_GET['action'] == "download_history") {

        global $wpdb;

        $chat_id = $_GET['cid'];
        $fileName = 'live_chat_history_' . $chat_id . '.csv';

        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header('Content-Description: File Transfer');
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename={$fileName}");
        header("Expires: 0");
        header("Pragma: public");

        $fh = @fopen('php://output', 'w');

        global $wpdb;
        global $wplc_tblname_msgs;

        $results = $wpdb->get_results(
                "
	        SELECT *
	        FROM $wplc_tblname_msgs
	        WHERE `chat_sess_id` = '$chat_id'
	        ORDER BY `timestamp` ASC
	        LIMIT 0, 100
	        "
        );

        $fields[] = array(
            'id' => __('Chat ID', 'wplivechat'),
            'msgfrom' => __('From', 'wplivechat'),
            'msg' => __('Message', 'wplivechat'),
            'time' => __('Timestamp', 'wplivechat'),
            'orig' => __('Origin', 'wplivechat'),
        );

        foreach ($results as $result => $key) {
            if ($key->originates == 2) {
                $user = __('user', 'wplivechat');
            } else {
                $user = __('agent', 'wplivechat');
            }

            $fields[] = array(
                'id' => $key->chat_sess_id,
                'msgfrom' => $key->msgfrom,
                'msg' => apply_filters("wplc_filter_message_control_out", $key->msg),
                'time' => $key->timestamp,
                'orig' => $user,
            );
        }

        foreach ($fields as $field) {
            fputcsv($fh, $field, ",", '"');
        }
        // Close the file
        fclose($fh);
        // Make sure nothing else is sent, our file is done
        exit;
    }
}

function wplc_admin_download_history($type, $cid) {

    global $wpdb;
    global $wplc_tblname_msgs;

    $results = $wpdb->get_results(
            "
        SELECT *
        FROM $wplc_tblname_msgs
        WHERE `chat_sess_id` = '$cid'
        ORDER BY `timestamp` ASC
        LIMIT 0, 100
        "
    );

    $fields[] = array(
        'id' => __('Chat ID', 'wplivechat'),
        'msgfrom' => __('From', 'wplivechat'),
        'msg' => __('Message', 'wplivechat'),
        'time' => __('Timestamp', 'wplivechat'),
        'orig' => __('Origin', 'wplivechat'),
    );

    foreach ($results as $result => $key) {
        if ($key->originates == 2) {
            $user = __('user', 'wplivechat');
        } else {
            $user = __('agent', 'wplivechat');
        }

        $fields[] = array(
            'id' => $key->chat_sess_id,
            'msgfrom' => $key->msgfrom,
            'msg' => apply_filters("wplc_filter_message_control_out", $key->msg),
            'time' => $key->timestamp,
            'orig' => $user,
        );
    }

    ob_end_clean();

    wplc_convert_to_csv_new($fields, 'live_chat_history_' . $cid . '.csv', ',');

    exit();
}

function wplc_convert_to_csv_new($in, $out, $del) {

    $f = fopen('php://memory', 'w');

    foreach ($in as $line) {
        wplc_fputcsv_eol_new($f, $line, $del, "\r\n");
    }

    fseek($f, 0);

    header('Content-Type: application/csv');

    header('Content-Disposition: attachement; filename="' . $out . '";');

    fpassthru($f);
}

function wplc_fputcsv_eol_new($fp, $array, $del, $eol) {
    fputcsv($fp, $array, $del);
    if ("\n\r" != $eol && 0 === fseek($fp, -1, SEEK_CUR)) {
        fwrite($fp, $eol);
    }
}

function wplc_plugin_row_invalid_api() {
    echo '<tr class="active"><td>&nbsp;</td><td colspan="2" style="color:red;">
    &nbsp; &nbsp; ' . __('Your API Key is Invalid. You are not eligible for future updates. Please enter your API key <a href="admin.php?page=wplivechat-menu-api-keys-page">here</a>.', 'wplivechat') . '
    </td></tr>';
}

function wplc_basic_hide_chat_when_offline() {
    $wplc_settings = get_option("WPLC_SETTINGS");

    $hide_chat = 0;
    if (isset($wplc_settings['wplc_hide_when_offline']) && $wplc_settings['wplc_hide_when_offline'] == 1) {
        /* Hide the window when its offline */
        $logged_in = apply_filters("wplc_loggedin_filter", false);
        if (!$logged_in) {
            $hide_chat++;
        }
    } else {
        $hide_chat = 0;
    }
    return $hide_chat;
}

function wplc_string_check() {
    $wplc_settings = get_option("WPLC_SETTINGS");

    if (!isset($wplc_settings['wplc_pro_na']) || (isset($wplc_settings['wplc_pro_na']) && $wplc_settings['wplc_pro_na'] == "")) {
        $wplc_settings["wplc_pro_na"] = __("Chat offline. Leave a message", "wplivechat");
    }
    if (!isset($wplc_settings['wplc_pro_intro']) || (isset($wplc_settings['wplc_pro_intro']) && $wplc_settings['wplc_pro_intro'] == "")) {
        $wplc_settings["wplc_pro_intro"] = __("Hello. Please input your details so that I may help you.", "wplivechat");
    }
    if (!isset($wplc_settings['wplc_pro_offline1']) || (isset($wplc_settings['wplc_pro_offline1']) && $wplc_settings['wplc_pro_offline1'] == "")) {
        $wplc_settings["wplc_pro_offline1"] = __("We are currently offline. Please leave a message and we'll get back to you shortly.", "wplivechat");
    }
    if (!isset($wplc_settings['wplc_pro_offline2']) || (isset($wplc_settings['wplc_pro_offline2']) && $wplc_settings['wplc_pro_offline2'] == "")) {
        $wplc_settings["wplc_pro_offline2"] = __("Sending message...", "wplivechat");
    }
    if (!isset($wplc_settings['wplc_pro_offline3']) || (isset($wplc_settings['wplc_pro_offline3']) && $wplc_settings['wplc_pro_offline3'] == "")) {
        $wplc_settings["wplc_pro_offline3"] = __("Thank you for your message. We will be in contact soon.", "wplivechat");
    }
    if (!isset($wplc_settings['wplc_pro_fst1']) || (isset($wplc_settings['wplc_pro_fst1']) && $wplc_settings['wplc_pro_fst1'] == "")) {
        $wplc_settings["wplc_pro_fst1"] = __("Questions?", "wplivechat");
    }
    if (!isset($wplc_settings['wplc_pro_fst2']) || (isset($wplc_settings['wplc_pro_fst2']) && $wplc_settings['wplc_pro_fst2'] == "")) {
        $wplc_settings["wplc_pro_fst2"] = __("Chat with us", "wplivechat");
    }
    if (!isset($wplc_settings['wplc_pro_fst3']) || (isset($wplc_settings['wplc_pro_fst3']) && $wplc_settings['wplc_pro_fst3'] == "")) {
        $wplc_settings["wplc_pro_fst3"] = __("Start live chat", "wplivechat");
    }
    if (!isset($wplc_settings['wplc_pro_sst1']) || (isset($wplc_settings['wplc_pro_sst1']) && $wplc_settings['wplc_pro_sst1'] == "")) {
        $wplc_settings["wplc_pro_sst1"] = __("Start Chat", "wplivechat");
    }
    if (!isset($wplc_settings['wplc_pro_sst1_survey']) || (isset($wplc_settings['wplc_pro_sst1_survey']) && $wplc_settings['wplc_pro_sst1_survey'] == "")) {
        $wplc_settings["wplc_pro_sst1_survey"] = __("Or chat to an agent now", "wplivechat");
    }
    if (!isset($wplc_settings['wplc_pro_sst1e_survey']) || (isset($wplc_settings['wplc_pro_sst1e_survey']) && $wplc_settings['wplc_pro_sst1e_survey'] == "")) {
        $wplc_settings["wplc_pro_sst1e_survey"] = __("Chat ended", "wplivechat");
    }
    if (!isset($wplc_settings['wplc_pro_sst2']) || (isset($wplc_settings['wplc_pro_sst2']) && $wplc_settings['wplc_pro_sst2'] == "")) {
        $wplc_settings["wplc_pro_sst2"] = __("Connecting. Please be patient...", "wplivechat");
    }
    if (!isset($wplc_settings['wplc_pro_tst1']) || (isset($wplc_settings['wplc_pro_tst1']) && $wplc_settings['wplc_pro_tst1'] == "")) {
        $wplc_settings["wplc_pro_tst1"] = __("Reactivating your previous chat...", "wplivechat");
    }
    if (!isset($wplc_settings['wplc_user_welcome_chat']) || (isset($wplc_settings['wplc_user_welcome_chat']) && $wplc_settings['wplc_user_welcome_chat'] == "")) {
        $wplc_settings["wplc_user_welcome_chat"] = __("Welcome. How may I help you?", "wplivechat");
    }
    if (!isset($wplc_settings['wplc_user_enter']) || (isset($wplc_settings['wplc_user_enter']) && $wplc_settings['wplc_user_enter'] == "")) {
        $wplc_settings["wplc_user_enter"] = __("Press ENTER to send your message", "wplivechat");
    }

    update_option("WPLC_SETTINGS", $wplc_settings);
}

add_filter("wplc_filter_chat_text_editor_upsell", "nifty_text_edit_upsell", 1, 1);

function nifty_text_edit_upsell($msg) {
    if (!function_exists("nifty_text_edit_div") && !function_exists("wplc_pro_activate")) {
        //Only show this if in admin area and is not PRO
        $msg .= "<div id='nifty_text_editor_holder' class='wplc_faded_upsell'>";
        $msg .= "<i class='nifty_tedit_icon fa fa-bold' id='nifty_tedit_b'></i>";
        $msg .= "<i class='nifty_tedit_icon fa fa-italic' id='nifty_tedit_i'></i>";
        $msg .= "<i class='nifty_tedit_icon fa fa-underline' id='nifty_tedit_u'></i>";
        $msg .= "<i class='nifty_tedit_icon fa fa-strikethrough' id='nifty_tedit_strike'></i>";
        $msg .= "<i class='nifty_tedit_icon fa fa-square' id='nifty_tedit_mark'></i>";
        $msg .= "<i class='nifty_tedit_icon fa fa-subscript' id='nifty_tedit_sub'></i>";
        $msg .= "<i class='nifty_tedit_icon fa fa-superscript' id='nifty_tedit_sup'></i>";
        $msg .= "<i class='nifty_tedit_icon fa fa-link' id='nifty_tedit_link'></i>";
        $msg .= "<i class='nifty_tedit_icon'><a target='_BLANK' href='https://wp-livechat.com/purchase-pro/?utm_source=plugin&utm_medium=link&utm_campaign=text_editor'>Pro version only</a></i>";
        $msg .= "</div>";
    }
    return $msg;
}

add_filter("wplc_filter_advanced_info", "nifty_rating_advanced_info_upsell", 1, 3);

function nifty_rating_advanced_info_upsell($msg, $cid, $name) {
    if (!function_exists("nifty_rating_advanced_info_control") && is_admin() && !function_exists("wplc_pro_activate")) {
        $msg .= "<div class='admin_visitor_advanced_info admin_agent_rating wplc_faded_upsell'>
	                <strong>" . __("Experience Rating", "wplivechat") . "</strong>
	                <div class='rating'><a target='_BLANK' href='https://wp-livechat.com/purchase-pro/?utm_source=plugin&utm_medium=link&utm_campaign=experience_ratings'>Pro only</a></div>
	            </div>";
    }
    return $msg;
}

add_action("wplc_hook_admin_settings_main_settings_after", "wplc_hook_control_admin_settings_chat_box_settings_after", 2);

function wplc_hook_control_admin_settings_chat_box_settings_after() {

    $wplc_settings = get_option("WPLC_SETTINGS");
    if (isset($wplc_settings["wplc_environment"])) {
        $wplc_environment[intval($wplc_settings["wplc_environment"])] = "SELECTED";
    }
    ?>
    <table class='form-table' width='700'>
        <tr>
            <td width='400' valign='top'>
                <h4><?php _e("Advanced settings", "wplivechat") ?></h4>
            </td>
            <td valign='top'>
                &nbsp;
            </td>
        </tr>
    <?php do_action("wplc_advanced_settings_above_performance", $wplc_settings); ?>
        <tr>
            <td>
                <p><em><small><?php _e("Only change these settings if you are experiencing performance issues.", "wplivechat"); ?></small></em></p>
            </td>
            <td valign='top'>
                &nbsp;
            </td>
        </tr>
    </tr>
    <?php do_action("wplc_advanced_settings_settings"); ?>
    <tr>
        <td valign='top'>
    <?php _e("What type of environment are you on?", "wplivechat"); ?>
        </td>
        <td valign='top'>
            <select name='wplc_environment' id='wplc_environment'>
                <option value='1' <?php if (isset($wplc_environment[1])) {
        echo $wplc_environment[1];
    } ?>><?php _e("Shared hosting - low level plan", "wplivechat"); ?></option>
                <option value='2' <?php if (isset($wplc_environment[2])) {
        echo $wplc_environment[2];
    } ?>><?php _e("Shared hosting - normal plan", "wplivechat"); ?></option>
                <option value='3' <?php if (isset($wplc_environment[3])) {
        echo $wplc_environment[3];
    } ?>><?php _e("VPS", "wplivechat"); ?></option>
                <option value='4' <?php if (isset($wplc_environment[4])) {
        echo $wplc_environment[4];
    } ?>><?php _e("Dedicated server", "wplivechat"); ?></option>
            </select>  
        </td>
    </tr>
    <tr>
        <td valign='top'>
    <?php _e("Long poll setup", "wplivechat"); ?>: <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e("Only change these if you are an experienced developer or if you have received these figures from the Code Cabin Support team.", "wplivechat") ?>"></i>
        </td>
        <td valign='top'>
    <?php
    $wplc_advanced_settings = get_option("wplc_advanced_settings");
    ?>
            <table>
                <tr>
                    <td><?php _e("Iterations", "wplivechat"); ?></td>
                    <td><input id="wplc_iterations" name="wplc_iterations" type="number" max='200' min='10'  value="<?php if (isset($wplc_advanced_settings['wplc_iterations'])) {
        echo $wplc_advanced_settings['wplc_iterations'];
    } else {
        echo '55';
    } ?>" /></td>
                </tr>
                <tr>
                    <td><?php _e("Sleep between iterations", "wplivechat"); ?></td>
                    <td>
                        <input id="wplc_delay_between_loops" name="wplc_delay_between_loops" type="number" max='1000000' min='25000'  value="<?php if (isset($wplc_advanced_settings['wplc_delay_between_loops'])) {
        echo $wplc_advanced_settings['wplc_delay_between_loops'];
    } else {
        echo '500000';
    } ?>" />
                        <small><em><?php _e("microseconds", "wplivechat"); ?></em></small>
                    </td>
                </tr>
            </table>

        </td>
    </tr>

    </table>
    <?php
}

function wplc_basic_reporting_page() {

    $content = "<div class='wrap'>";
    $content .= "<h2>" . __('WP Live Chat Support Reporting', 'wp-livechat') . " (beta) </h2>";
    $content .= "<table class=\"wp-list-table widefat fixed form-table\" cellspacing=\"0\" style='width:98%'>";
    $content .= "<tr>";
    $content .= "<td style='width:35%; vertical-align:top;'>";
    $content .= "<img class='reporting_img_main' style='width:99%; height:auto; padding:2px; border:1px lightgray solid;box-shadow: 3px 3px 2px -2px #999;-webkit-box-shadow: 3px 3px 2px -2px #999;-moz-box-shadow: 3px 3px 2px -2px #999;-o-box-shadow: 3px 3px 2px -2px #999;' src='" . plugins_url('/images/reporting_sample.jpg', __FILE__) . "'>";
    $content .= "</td>";
    $content .= "<td style='vertical-align:top;'>";
    $content .= "<h3>" . __('WP Live Chat Support Reporting', 'wp-livechat') . "</h3>";
    $content .= "<p>" . __('View comprehensive reports regarding your chat and agent activity.', 'wp-livechat') . "</p>";


    $content .= "<br><p><strong>" . __('Reports', 'wp-livechat') . ":</strong></p>";
    $content .= "<ul style='list-style: inherit;margin-left: 22px;'>";
    $content .= "<li>" . __('Chat statistics', 'wp-livechat') . "</li>";
    $content .= "<li>" . __('Popular pages', 'wp-livechat') . "</li>";
    $content .= "<li>" . __('ROI reporting and tracking (identify which agents produce the most sales)', 'wp-livechat') . "</li>";
    $content .= "<li>" . __('User experience ratings (identify which agents produce the happiest customers)', 'wp-livechat') . "</li>";
    $content .= "</ul>";

    if (function_exists("wplc_pro_activate")) {
        global $wplc_pro_version;
        if (intval(str_replace(".", "", $wplc_pro_version)) < 6300) {
            $content .= "<p>In order to use reporting, please ensure you are using the latest Pro version (version 6.3.00 or newer).</p>";
            $content .= "<br><a title='Update Now' href='./update-core.php' style='width: 200px;height: 58px;text-align: center;line-height: 56px;font-size: 18px;' class='button button-primary'>" . __("Update now", "wplivechat") . "</a>";
        }
    } else {
        $content .= "<p>" . __('Get all this and more in the ', 'wp-livechat') . "<a href='https://www.wp-livechat.com/purchase-pro/?utm_source=plugin&utm_medium=link&utm_campaign=reporting' target='_BLANK'>" . __("Pro add-on", "wplivechat") . "</a></p>";
        $content .= "<br><a title='Upgrade Now' href='https://www.wp-livechat.com/purchase-pro/?utm_source=plugin&utm_medium=link&utm_campaign=reporting' style='width: 200px;height: 58px;text-align: center;line-height: 56px;font-size: 18px;' class='button button-primary'  target='_BLANK'>" . __("Upgrade Now", "wplivechat") . "</a>";
    }
    $content .= "</td>";
    $content .= "</tr>";
    $content .= "</table>";
    $content .= "</div>";
    echo $content;
}

function wplc_basic_triggers_page() {
    $content = "<div class='wrap'>";
    $content .= "<h2>" . __('WP Live Chat Support Triggers', 'wp-livechat') . " (beta) </h2>";
    $content .= "<script>
    				var isOn = true;
    				jQuery(function(){
    					jQuery(document).ready(function(){
    						setInterval(function(){
    							if(isOn){
    								jQuery('.trigger_img_main').fadeOut('fast', function(){
    									jQuery('.trigger_img_sec').fadeIn('slow');
    									jQuery('.trigger_img_sec').fadeIn('slow');
    								});
    							} else {
    								jQuery('.trigger_img_sec').fadeOut('fast');
    								jQuery('.trigger_img_sec').fadeOut('fast', function(){
    									jQuery('.trigger_img_main').fadeIn('slow');
    								});
    							}
    							isOn = !isOn;
    						}, 5000);
    					});
    				});
    			</script>";

    $content .= "<table class=\"wp-list-table widefat fixed form-table\" cellspacing=\"0\" style='width:98%'>";
    $content .= "<tr>";
    $content .= "<td style='width:35%; vertical-align:top;'>";
    $content .= "<img class='trigger_img_main' style='width:99%; height:auto; padding:2px; border:1px lightgray solid;box-shadow: 3px 3px 2px -2px #999;-webkit-box-shadow: 3px 3px 2px -2px #999;-moz-box-shadow: 3px 3px 2px -2px #999;-o-box-shadow: 3px 3px 2px -2px #999;' src='" . plugins_url('/images/trigger_sample.jpg', __FILE__) . "'>";
    $content .= "<img class='trigger_img_sec'style='display: none; width:49%; height:auto; border:1px lightgray solid;box-shadow: 3px 3px 2px -2px #999;-webkit-box-shadow: 3px 3px 2px -2px #999;-moz-box-shadow: 3px 3px 2px -2px #999;-o-box-shadow: 3px 3px 2px -2px #999;' src='" . plugins_url('/images/trigger_sample_front.jpg', __FILE__) . "'>";
    $content .= "</td>";
    $content .= "<td style='vertical-align:top;'>";
    $content .= "<h3>" . __('WP Live Chat Support Triggers', 'wp-livechat') . "</h3>";
    $content .= "<p>" . __('Create custom data triggers when users view a certain page, spend a certain amount of time on a page, scroll past a certain point or when their mouse leaves the window.', 'wp-livechat') . "</p>";


    $content .= "<br><p><strong>" . __('Trigger Types', 'wp-livechat') . ":</strong></p>";
    $content .= "<ul style='list-style: inherit;margin-left: 22px;'>";
    $content .= "<li>" . __('Page Trigger', 'wp-livechat') . "</li>";
    $content .= "<li>" . __('Time Trigger', 'wp-livechat') . "</li>";
    $content .= "<li>" . __('Scroll Trigger', 'wp-livechat') . "</li>";
    $content .= "<li>" . __('Page Leave Trigger', 'wp-livechat') . "</li>";
    $content .= "</ul>";

    if (function_exists("wplc_pro_activate")) {
        global $wplc_pro_version;
        if (intval(str_replace(".", "", $wplc_pro_version)) < 6200) {
            $content .= "<p>In order to use data triggers, please ensure you are using the latest Pro version (version 6.2.00 or newer).</p>";
            $content .= "<br><a title='Update Now' href='./update-core.php' style='width: 200px;height: 58px;text-align: center;line-height: 56px;font-size: 18px;' class='button button-primary'>" . __("Update now", "wplivechat") . "</a>";
        }
    } else {
        $content .= "<p>" . __('Get all this and more in the ', 'wp-livechat') . "<a href='https://www.wp-livechat.com/purchase-pro/?utm_source=plugin&utm_medium=link&utm_campaign=data_triggers' target='_BLANK'>" . __("Pro add-on", "wplivechat") . "</a></p>";
        $content .= "<br><a title='Upgrade Now' href='https://www.wp-livechat.com/purchase-pro/?utm_source=plugin&utm_medium=link&utm_campaign=data_triggers' style='width: 200px;height: 58px;text-align: center;line-height: 56px;font-size: 18px;' class='button button-primary' target='_BLANK'>" . __("Upgrade Now", "wplivechat") . "</a>";
    }
    $content .= "</td>";
    $content .= "</tr>";
    $content .= "</table>";
    $content .= "</div>";
    echo $content;
}
