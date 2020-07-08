<?php
global $wpdb;
$route = get_route();
$client = $_GET['client'];
$curr_mode = empty($_GET['mode']) ? 'practice' : $_GET['mode'];
update_user_subscription();
$active_tab = empty($route[1]) ? 'vocab' : $route[1];
$class_type_obj = MWDB::get_group_class_type_by('slug', $active_tab);
$class_type = $class_type_obj->id;
$is_sat_class_subscribed = is_sat_class_subscribed($class_type);
unset($_SESSION['subscription']);
 if (in_array($class_type, array(1, 2, 3, 4, 5, 6, 7))) {
        $is_sat_english_subscribed_package = is_sat_class_subscribed(51);
    }
update_user_subscription();
$task = isset($_POST['task']) ? $_POST['task'] : '';
$gid = empty($_GET['gid']) ? 0 : $_GET['gid'];
$mid = empty($_GET['mid']) ? 0 : $_GET['mid'];
$hid = empty($_GET['hid']) ? 0 : $_GET['hid'];
$sid = empty($_GET['sid']) ? 0 : $_GET['sid'];
$wrid = empty($_GET['wrid']) ? 0 : $_GET['wrid'];
$hgid = empty($_GET['hgid']) ? 0 : $_GET['hgid']; //check id group joined
$lvid = empty($_GET['lvid']) ? 0 : $_GET['lvid'];
$lvgrid = empty($_GET['lvgrid']) ? 0 : $_GET['lvgrid'];
$current_user = wp_get_current_user();
$gname = '';
$current_user_id = get_current_user_id();
$uref = rawurlencode(base64_encode(home_url() . $_SERVER['REQUEST_URI']));
$is_math_panel = is_math_panel();
$_page_title = __('My Subscription', 'iii-dictionary');
$_averge = 0;

if (isset($_POST['add-tutoring-plan'])) {

$_SESSION['tutoring'] = $_POST; 
//echo '<pre>';
//print_r($_POST);
//die;
//    ik_add_to_cart($_POST);
//    wp_redirect(home_url_ssl() . '/?r=payments');
//    exit;
}

set_page_filter_session($filter);
$filter['offset'] = 0;
$filter['items_per_page'] = 99999999;
$groups = MWDB::get_groups($filter, $filter['offset'], $filter['items_per_page']);
$total_pages = ceil($groups->total / $filter['items_per_page']);
// get private message id
if ($mid) {
    
    $header_title = __('Received Message', 'iii-dictionary');
    $send_to_lbl = __('Sender Username', 'iii-dictionary');
    $cur_message = MWDB::get_received_private_message($mid);

    if ($cur_message->status == MESSAGE_STATUS_UNREAD) {
        // if this is an unread message, set it to read
        ik_update_message_status($mid, MESSAGE_STATUS_READ);
    }

    if (!$cur_message->sender_id) {
        $recipient = 'Support';
        $recipient_id = 0;
    } else {
        $recipient = $cur_message->sender_login;
        $recipient_id = $cur_message->sender_id;
    }
    $time = $cur_message->received_on;
    $subject = $cur_message->subject;
    $message = '<blockquote><p class="quoted-from">' . $recipient . ' wrote:</p>' . $cur_message->message . '</blockquote><p></p>';
}
// user click Start button, join user to the group.
if (isset($_POST['submit-message'])) {
    $form_valid = true;
    $recipient_id = $_REAL_POST['recipient-id'];
    $reply_prefix = 'RE: ';
    $subject = $_REAL_POST['subject'];
    $message = $_REAL_POST['message'];

    if ($recipient_id == '') {
        $recipient = $_POST['recipient'];
        $recipient_obj = get_user_by('login', $recipient);
        if ($recipient_obj) {
            $recipient_id = $recipient_obj->ID;
        } else {
            ik_enqueue_messages(__('Invalid recipient.', 'iii-dictionary'), 'error');
            $form_valid = false;
        }
    }

    if (ik_send_private_message($recipient_id, $reply_prefix . $subject, $message)) {
        wp_redirect(locale_home_url() . '/?r=online-learning');
        exit;
    }
}
if (isset($_POST['submit-new-message'])) {
    $form_valid = true;
    $recipient_id = $_REAL_POST['recipient-id'];
    $reply_prefix = '';
    $subject = $_REAL_POST['subject'];
    $message = $_REAL_POST['new-message'];

    if (!wp_check_password($_POST['sender-password'], $current_user->user_pass, $current_user->ID)) {
        ik_enqueue_messages(__('Password not match.', 'iii-dictionary'), 'error');
        $form_valid = false;
    }

    if ($recipient_id == '') {
        $recipient = $_POST['recipient'];
        $recipient_obj = get_user_by('login', $recipient);
        if ($recipient_obj) {
            $recipient_id = $recipient_obj->ID;
        } else {
            ik_enqueue_messages(__('Invalid recipient.', 'iii-dictionary'), 'error');
            $form_valid = false;
        }
    }

    if (empty($subject)) {
        ik_enqueue_messages(__('You must enter a subject.', 'iii-dictionary'), 'error');
        $form_valid = false;
    }

    if (empty($message)) {
        ik_enqueue_messages(__('Please complete message fields.', 'iii-dictionary'), 'error');
        $form_valid = false;
    }

    if ($form_valid && ik_send_private_message($recipient_id, $reply_prefix . $subject, $message)) {
        wp_redirect(locale_home_url() . '/?r=online-learning');
        exit;
    }
}
if(isset($_POST['submit-send-message']))
{
    $form_valid = true;
    $recipient_id = $_REAL_POST['recipient-id'];
    $reply_prefix = $mid && strpos($_REAL_POST['subject'], 'RE: ') === false ? 'RE: ' : '';
    $subject = $_REAL_POST['subject'];
    $message = $_REAL_POST['message'];

    if(!wp_check_password($_POST['sender-password'], $current_user->user_pass, $current_user->ID)) {
            ik_enqueue_messages(__('Password not match.', 'iii-dictionary'), 'error');
            $form_valid = false;
    }

    if($recipient_id == '') {
            $recipient = $_POST['recipient'];
            $recipient_obj = get_user_by('login', $recipient);
            if($recipient_obj) {
                    $recipient_id = $recipient_obj->ID;
            }
            else {
                    ik_enqueue_messages(__('Invalid recipient.', 'iii-dictionary'), 'error');
                    $form_valid = false;
            }
    }

    if(empty($subject)) {
            ik_enqueue_messages(__('You must enter a subject.', 'iii-dictionary'), 'error');
            $form_valid = false;
    }

    if(empty($message)) {
            ik_enqueue_messages(__('Please complete message fields.', 'iii-dictionary'), 'error');
            $form_valid = false;
    }

    if($form_valid && ik_send_private_message($recipient_id, $reply_prefix . $subject, $message)) {
            wp_redirect(locale_home_url() . '/?r=online-learning&check');
            exit;
    }
}
if (!empty($_POST['jid'])) {
    $class_type_id = $_POST['cltid'];
    $is_sat_class_subscribed = is_sat_class_subscribed($class_type_id);
    if ($is_sat_class_subscribed) {
        $g = MWDB::get_group($_POST['jid'], 'id');

        if (MWDB::join_group($_POST['jid']) != 1) {
            wp_redirect(locale_home_url() . '/?r=homework-status');
            exit;
        }
    }
}
if (isset($_REQUEST['message']) && isset($_REQUEST['group_id_post'])) {
    $data = array(
        'group_id' => $_REQUEST['group_id_post'],
        'posted_by' => $current_user_id,
        'message' => $_REQUEST['message'],
        'posted_on' => date('Y-m-d H:i:s', time())
    );

    if (MWDB::insert_group_message($data)) {
        wp_redirect(locale_home_url() . '/?r=group-messages&g=' . $_REQUEST['group_id_post']);
        exit;
    }
}
// get history tutoring 
$histutor = MWDB::get_histori_tutoring($current_user_id);
//var_dump($histutor);die;
// user want to join group
if (isset($_POST['join'])) {
    $gname = esc_html($_POST['gname']);
    $gpass = esc_html($_POST['gpass']);
    if (MWDB::join_group($gname, $gpass, $current_user_id)) {

        wp_redirect(locale_home_url() . '/?r=online-learning');
        exit;
    }
    session_destroy();
    update_user_subscription();
}

// page content
$current_page = max(1, get_query_var('page'));
$filter['items_per_page'] = 20;
$filter['offset'] = $filter['items_per_page'] * ($current_page - 1);

$filter['offset'] = 0;
$filter['items_per_page'] = 99999999;
//if (!$is_math_panel) {
//    //old 18/1 thay đổi thành 2 bên english và math sẽ view ra những nhóm private teacher tạo nên sẽ giống ở cả 2 bên
//    $user_groups = MWDB::get_user_joined_english_groups($current_user_id, $filter['offset'], $filter['items_per_page'], true);
//} else {
//    $user_groups = MWDB::get_user_joined_math_groups($current_user_id, $filter['offset'], $filter['items_per_page'], true);
//}

$user_groups = MWDB::get_user_joined_group_private($current_user_id);
$user_ikmath_groups = MWDB::get_ikmath_groups($current_user_id, $filter['offset'], $filter['items_per_page'], true);
//    var_dump(empty($user_ikmath_groups->items));die;
//$total_pages = ceil($user_groups->total / $filter['items_per_page']);
if (!$gid) {
    
} else {
    // user want to re do the homework
    if (isset($_POST['retry'])) {
        if (MWDB::delete_homework_result($_POST['rid'])) {
            $url = MWHtml::get_practice_page_url($_POST['aid']) . '&mode=homework&sid=' . $_POST['sid'] . '&ref=' . $uref;
            wp_redirect($url);
            exit;
        }
    }

    // user want to request grading from teacher
    if (isset($_POST['request-grading'])) {
        $hrid = $_POST['hrid']; // homework result id
        $hid = $_POST['hid']; // homework id
        // request grading
        if (ik_request_worksheet_grading($hrid, $hid, $current_user_id)) {
            wp_redirect(locale_home_url() . '/?r=online-learning&gid=' . $gid);
            exit;
        }
    }

    $filter['homework_result'] = true;
    $filter['user_id'] = get_current_user_id();
    $filter['is_active'] = 1;
    $group = MWDB::get_group($gid, 'id');
    $homeworks = MWDB::get_group_homeworks($gid, $filter, $filter['offset'], $filter['items_per_page']);
    $total_pages = ceil($homeworks->total / $filter['items_per_page']);
    //calculate the average score
//    var_dump($homeworks->items);die;
    if (!empty($homeworks->items)) {
        $_averge = average_test_homework($homeworks->items);
    }
}
$homeworksnotgroup = MWDB::get_homeworks_not_group(get_current_user_id(), $filter['offset'], 20);

$pagination = paginate_links(array(
    'format' => '?page=%#%',
    'current' => $current_page,
    'total' => $total_pages
        ));
$sub_msg = __('Please subscribe ', 'iii-dictionary');
$sub_msg_sat = __('Your subscription has expired. Please subscribe to SAT I Preparation to start.', 'iii-dictionary');
ik_enqueue_js_messages('login_req_h', __('Login Required', 'iii-dictionary'));
ik_enqueue_js_messages('login_req_err', __('Please login in order to continue to use this function.', 'iii-dictionary'));
ik_enqueue_js_messages('login_req_lbl', __('Login', 'iii-dictionary'));

ik_enqueue_js_messages('sub_req_h', __('Subscription Required', 'iii-dictionary'));
ik_enqueue_js_messages('sub_req_err', $sub_msg);
ik_enqueue_js_messages('sub_req_err_sat', $sub_msg_sat);
ik_enqueue_js_messages('sub_req_lbl', __('OK', 'iii-dictionary'));
$teacher_tool_price = mw_get_option('teacher-tool-price');
$self_study_price = mw_get_option('self-study-price');
$self_study_price_math = mw_get_option('math-self-study-price');
$dictionary_price = mw_get_option('dictionary-price');
?>
<?php if (!$is_math_panel) : ?>
    <?php get_dict_header($_page_title) ?>
<?php else : ?>
    <?php get_math_header($_page_title, 'red-brown') ?>
<?php endif ?>
<?php get_dict_page_title($_page_title, '', '', array(), get_info_tab_cloud_url('Popup_info_11.jpg')) ?>
<script>
    jQuery('#online-learning .article-header').css('background', '#ffffff');
    jQuery('#online-learning .entry-content').css('background', '#ffffff');
    if ((window.matchMedia('screen and (min-width: 1200px)').matches)) {
        jQuery('#main').removeClass('container');
        jQuery('#online-learning .article-header .row').attr('style', 'width:1050px; margin:auto !important');
        jQuery('#online-learning .entry-content .row:first').attr('style', 'width:1050px; margin:auto !important');
    }
    if ((window.matchMedia('screen and (max-width: 480px)').matches)) {    
        jQuery('#online-learning .article-header').attr('style', 'height: 0px;padding-top: 0px !important; background: #fff !important;border-bottom: 1px solid #fff;');
    }
    jQuery('#online-learning #page-tabs-container').css('background', '#ffffff');
    jQuery('#online-learning .entry-content').css('color', 'black');
    jQuery('.main-article header .page-title').css('color', 'black');
    jQuery('.cs-select').css('color', 'black');
    jQuery('#page-info-tab').hide();
    jQuery('#span-title').html('Select the activity below to restart your worksheet, or check the graded results.');
    
    if ((window.matchMedia('screen and (max-width: 450px)').matches)) {
        jQuery('#span-title').css('bottom', '-12px');
        jQuery('.container-acc-login-signup-online').css('margin-top', '10px');
    }
//    jQuery('#page-tabs-container').remove();
</script>
<div id="div-all-select">
    <div id="div-select">
        <section>
            <select class="cs-select set-selected cs-skin-border">
                <option value="homeworkagm">Homework from Teacher</option>
                <?php if (!$is_math_panel) { ?>
                    <option value="homeworkfrom">Critical English Subjects</option>
                <?php } else { ?>
                    <option value="homeworkfrom">Critical Math Subjects</option>
                    <option value="ikmath_courses">ikMath Courses</option>
                    <option value="tutoring_plan">Tutoring Plan</option>                    
                <?php } ?>
                <option value="sattest">SAT Preparation and Practice Test</option>
            </select>
        </section>
    </div>

    <script>
        (function () {
            [].slice.call(document.querySelectorAll('select.cs-select')).forEach(function (el) {
                new SelectFx(el);
            });
        })();
    </script>
    <div id="div-text"><p id="p-text">SELECT</p><span id="span-icon" class="span-icon-down"></span></div>
</div>
<div  style="padding: 0px !important; margin-top: 30px" class="col-sm-12 p-left5-percent">
    <div id="loadhomework" <?php
    if ($gid || ($hid && $sid) || $wrid || $lvid || $lvgrid) {
        echo 'style=" display: none; "';
    }
    ?> ></div>
         <?php if ($gid) { ?>
        <div id="loadhomework_group" >
            <?php
            // check subscribe group
            $rec = $wpdb->get_row("SELECT class_type_id FROM {$wpdb->prefix}dict_group_details WHERE group_id = {$gid}");
            $class_type_id = $rec->class_type_id;
            $is_sat_class_subscribed = is_sat_class_subscribed($class_type_id);
            ?>
            <form method="post" action="<?php echo locale_home_url() ?>/?r=online-learning<?php echo $gid ? '&amp;gid=' . $gid : '' ?><?php echo $gid ? '&amp;price=' . $gi : '' ?>">
                <div class="box-header box-header-custom">
                    <?php $url = $_SERVER['REQUEST_URI']; 
                    if(isset($_GET['sat'])) { 
                        $id=$_GET['sat'];
                    ?>
                        <a href="<?php echo locale_home_url() ?>/?r=online-learning&back-sat<?php echo $id?>" class="link-back css-back" style="height: 39px;"><span class="span-icon-left css-icon-back"></span><span class="txt-back">BACK</span></a>
                    <?php } else if(isset($_GET["back-ikmath"])) { ?>
                        <a href="<?php echo locale_home_url() .'/?r=online-learning&backik=1'.$id.'&back-ikmath='.$_GET["back-ikmath"] ?>" class="link-back css-back" style="height: 39px;"><span class="span-icon-left css-icon-back"></span><span class="txt-back">BACK</span></a>
                    <?php } else if(isset($_GET['eng-prac'])){ 
                        $id=$_GET['eng-prac'];
                        ?>
                        <a href="<?php echo locale_home_url() ?>/?r=online-learning&back-en-sat<?php echo $id?>" class="link-back css-back" style="height: 39px;"><span class="span-icon-left css-icon-back"></span><span class="txt-back">BACK</span></a>
                    <?php } else if(isset($_GET['gid'])){ ?>
                            <?php if(is_math_panel()) {  ?>
                                <?php if(strpos($url, 'sat1prep') !== false){ ?>
                                    <a href="<?php echo home_url() .'/?r=sat-preparation/sat1prep&client=math-sat1' ?>" class="link-back css-back" style="height: 39px;"><span class="span-icon-left css-icon-back"></span><span class="txt-back">BACK</span></a>
                                <?php } else if(strpos($url, 'sat1a') !== false){ ?>
                                    <a href="<?php echo home_url() .'/?r=sat-preparation/sat1a&client=math-sat1' ?>" class="link-back css-back" style="height: 39px;"><span class="span-icon-left css-icon-back"></span><span class="txt-back">BACK</span></a>
                                <?php } else if(strpos($url, 'sat1b') !== false){ ?>
                                    <a href="<?php echo home_url() .'/?r=sat-preparation/sat1b&client=math-sat1' ?>" class="link-back css-back" style="height: 39px;"><span class="span-icon-left css-icon-back"></span><span class="txt-back">BACK</span></a>
                                <?php } else if(strpos($url, 'sat1c') !== false){ ?>
                                    <a href="<?php echo home_url() .'/?r=sat-preparation/sat1c&client=math-sat1' ?>" class="link-back css-back" style="height: 39px;"><span class="span-icon-left css-icon-back"></span><span class="txt-back">BACK</span></a>
                                <?php } else if(strpos($url, 'sat1d') !== false){ ?>
                                    <a href="<?php echo home_url() .'/?r=sat-preparation/sat1d&client=math-sat1' ?>" class="link-back css-back" style="height: 39px;"><span class="span-icon-left css-icon-back"></span><span class="txt-back">BACK</span></a>
                                <?php } else if(strpos($url, 'sat1e') !== false){ ?>
                                    <a href="<?php echo home_url().'/?r=sat-preparation/sat1e&client=math-sat1' ?>" class="link-back css-back" style="height: 39px;"><span class="span-icon-left css-icon-back"></span><span class="txt-back">BACK</span></a>


                                <?php } else if(strpos($url, 'sat2prep') !== false){ ?>
                                    <a href="<?php echo home_url() .'/?r=sat-preparation/sat2prep&client=math-sat2'?>" class="link-back css-back" style="height: 39px;"><span class="span-icon-left css-icon-back"></span><span class="txt-back">BACK</span></a>
                                <?php } else if(strpos($url, 'sat2a') !== false){ ?>
                                    <a href="<?php echo home_url() .'/?r=sat-preparation/sat2a&client=math-sat2'?>" class="link-back css-back" style="height: 39px;"><span class="span-icon-left css-icon-back"></span><span class="txt-back">BACK</span></a>
                                <?php } else if(strpos($url, 'sat2b') !== false){ ?>
                                    <a href="<?php echo home_url() .'/?r=sat-preparation/sat2b&client=math-sat2' ?>" class="link-back css-back" style="height: 39px;"><span class="span-icon-left css-icon-back"></span><span class="txt-back">BACK</span></a>
                                <?php } else if(strpos($url, 'sat2c') !== false){ ?>
                                    <a href="<?php echo home_url() .'/?r=sat-preparation/sat2c&client=math-sat2'?>" class="link-back css-back" style="height: 39px;"><span class="span-icon-left css-icon-back"></span><span class="txt-back">BACK</span></a>
                                <?php } else if(strpos($url, 'sat2d') !== false){ ?>
                                    <a href="<?php echo home_url() .'/?r=sat-preparation/sat2d&client=math-sat2'?>" class="link-back css-back" style="height: 39px;"><span class="span-icon-left css-icon-back"></span><span class="txt-back">BACK</span></a>
                                <?php } else if(strpos($url, 'sat2e') !== false){ ?>
                                    <a href="<?php echo home_url() .'/?r=sat-preparation/sat2e&client=math-sat2'?>" class="link-back css-back" style="height: 39px;"><span class="span-icon-left css-icon-back"></span><span class="txt-back">BACK</span></a>
                                <?php } else { ?>
                                    <a href="<?php echo locale_home_url() ?>/?r=online-learning<?php echo $id?>" class="link-back css-back" style="height: 39px;"><span class="span-icon-left css-icon-back"></span><span class="txt-back">BACK</span></a>
                                <?php } ?>
                            <?php } else { ?>
                                <?php if(strpos($url, 'writing') !== false){ ?>
                                    <a href="<?php echo home_url() .'/?r=sat-preparation/writing'?>" class="link-back css-back" style="height: 39px;"><span class="span-icon-left css-icon-back"></span><span class="txt-back">BACK</span></a>
                                <?php } else if(strpos($url, 'sat1') !== false){ ?>
                                    <a href="<?php echo home_url() .'/?r=sat-preparation/sat1'?>" class="link-back css-back" style="height: 39px;"><span class="span-icon-left css-icon-back"></span><span class="txt-back">BACK</span></a>
                                <?php } else if(strpos($url, 'sat2') !== false){ ?>
                                    <a href="<?php echo home_url() .'/?r=sat-preparation/sat2'?>" class="link-back css-back" style="height: 39px;"><span class="span-icon-left css-icon-back"></span><span class="txt-back">BACK</span></a>
                                <?php } else if(strpos($url, 'sat3') !== false){ ?>
                                    <a href="<?php echo home_url() .'/?r=sat-preparation/sat3'?>" class="link-back css-back" style="height: 39px;"><span class="span-icon-left css-icon-back"></span><span class="txt-back">BACK</span></a>
                                <?php } else if(strpos($url, 'sat4') !== false){ ?>
                                    <a href="<?php echo home_url() .'/?r=sat-preparation/sat4'?>" class="link-back css-back" style="height: 39px;"><span class="span-icon-left css-icon-back"></span><span class="txt-back">BACK</span></a>
                                <?php } else if(strpos($url, 'sat5') !== false){ ?>
                                    <a href="<?php echo home_url() .'/?r=sat-preparation/sat5'?>" class="link-back css-back" style="height: 39px;"><span class="span-icon-left css-icon-back"></span><span class="txt-back">BACK</span></a>
                                <?php } else if(strpos($url, 'vocab') !== false){ ?>
                                    <a href="<?php echo home_url() .'/?r=sat-preparation'?>" class="link-back css-back" style="height: 39px;"><span class="span-icon-left css-icon-back"></span><span class="txt-back">BACK</span></a>
                                <?php } else {?>
                                    <a href="<?php echo locale_home_url() ?>/?r=online-learning<?php echo $id?>" class="link-back css-back" style="height: 39px;"><span class="span-icon-left css-icon-back"></span><span class="txt-back">BACK</span></a>
                                <?php } ?>
                            <?php } ?>
                    <?php } ?>
                    
                    <div class="col-xs-12" style="padding-left: 0px !important;padding-top: 10px">
                        <p  class="col-xs-12 p-left5-percent" style="color: #fcd971;float: left;padding-right: 5px;"><span  style="color: #ffcc62;font-size: 18px;" class="css-pad-left-4-p-destop"><?php echo $group->name ?></span></p>
                        <p class="col-xs-12 p-left5-percent css-pad-left-5-p-destop" ><?php _e('By:', 'iii-dictionary') ?> <span><?php echo $group->display_name ?></span></p>                        
                    </div>
                </div>
                <div class="homeworkcritical-online can-scroll" style="height: 500px">
                    <div style="width: 100%">
                        <table class="table table-striped table-condensed ik-table1 text-center vertical-middle scroll-fix-head" id="homeworkcritical">
                            <thead class="homeworkcritical">
                                <tr>
                                    <?php if (!empty($homeworks->items)) {
                                        $check_css = 0;
                                        foreach ($homeworks->items as $hw) :
                                            if(($hw->is_view)==1) {
                                                $check_css = 1;
                                            }
                                        endforeach;
                                    }
                                    ?>      
                                    <?php if($check_css == 1) { ?>
                                        <th class="text-color-custom-1 p-left5-percent css-mobile-th1"><?php _e('Homework Name', 'iii-dictionary') ?></th>
                                    <?php }else {?>
                                        <th class="text-color-custom-1 p-left5-percent css-mobile-th5"><?php _e('Homework Name', 'iii-dictionary') ?></th>
                                    <?php }?>
                                    <th class="text-color-custom-1 css-mobile-th2-new" ><?php _e('Due date', 'iii-dictionary') ?></th>
                                    <th class="text-color-custom-1" style="width: 17% !important;padding-left: 5%"><?php _e('Grading', 'iii-dictionary') ?></th>
                                    <th class="text-color-custom-1" style="width: 10%;"><?php _e('Score', 'iii-dictionary') ?></th>
                                    <th class="text-color-custom-1" style="width: 5%"><?php _e('Status', 'iii-dictionary') ?></th>
                                    <th class="text-color-custom-1" ></th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr><td colspan="6"><?php echo $pagination ?></td></tr>
                            </tfoot>
                            <tbody style="max-height: 466px">
                                <?php
                                if (!empty($homeworks->items)) :
                                    foreach ($homeworks->items as $hw) :
                                        ?>
                                        <tr>
                                            <td class="p-left5-percent" style="width: 54%"><?php echo $hw->sheet_name ?></td>
                                            <td style="width: 21% !important"><?php echo $hw->deadline == '0000-00-00' ? 'No deadline' : ik_date_format($hw->deadline) ?></td>
                                            <?php
                                            if (is_null($hw->finished)) {
                                                $txt = __('New', 'iii-dictionary');
                                                $td_class = ' text-primary';
                                            } else if (!$hw->finished) {
                                                $txt = __('Unfinished', 'iii-dictionary');
                                                $td_class = ' text-warning2';
                                            } else {
                                                if ($hw->deadline != '0000-00-00' && $hw->submitted_on > $hw->deadline) {
                                                    $txt = __('Over Due', 'iii-dictionary');
                                                    $td_class = ' text-danger';
                                                } else {
                                                    $txt = __('Finished', 'iii-dictionary');
                                                    $td_class = ' text-success';
                                                }
                                            }
                                            ?>
                                            <td style="width: 18% !important;color: #0065bb !important"><?php if($hw->for_practice ==1){echo "Practice";}else{echo "Test";}?></td>
                                            <td style="color:#256CAA;">
                                                <?php
                                                $s = $_GET["sat"];
                                                    $g = $_GET["gid"];
                                                    $practice_url = MWHtml::get_practice_page_url($hw->assignment_id) . '&amp;hid=' . $hw->hid.'&amp;sid=' . $hw->sheet_id.'&amp;sat='.$s.'&amp;gid='.$g.'&ismode='.$hw->for_practice;
                                                    $homework_url = MWHtml::get_practice_page_url($hw->assignment_id) . '&amp;mode=homework&amp;hid=' . $hw->hid.'&amp;sid=' . $hw->sheet_id.'&amp;sat='.$s.'&amp;gid='.$g.'&ismode='.$hw->for_practice;
                                                ?>
                                                <?php if(is_math_panel()) { ?>
                                                    <?php if (ik_validate_date($hw->finished_on) || ik_validate_date($hw->accepted_on) || ik_validate_date($hw->requested_on) || $hw->assignment_id ==4) { ?>
                                                        <?php if(!empty($hw->score)){ ?>
                                                            <button type="button" data-practice-url="<?php echo $practice_url ?>" data-mode="<?php echo $hw->for_practice;?>" class="btn btn-default btn-tiny grey btn-a-link view-result-none-writing" style="color: #256CAA" data-status="<?php  echo $txt?>" id_g="<?php  echo $g?>" id="<?php  echo  $hw->hid  ?>"> <?php _e($hw->score.'%', 'iii-dictionary') ?></button>
                                                        <?php }else { ?>
                                                            <button type="button" data-practice-url="<?php echo $practice_url ?>" data-mode="<?php echo $hw->for_practice;?>" class="btn btn-default btn-tiny grey btn-a-link view-result-none-writing" style="color: #256CAA" data-status="<?php  echo $txt?>" id_g="<?php  echo $g?>" id="<?php  echo  $hw->hid  ?>"> <?php _e('0%', 'iii-dictionary') ?></button>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        <button type="button" data-practice-url="<?php echo $practice_url ?>" data-mode="<?php echo $hw->for_practice;?>" class="btn btn-default btn-tiny grey btn-a-link view-result-none-writing" style="color: #256CAA" data-status="<?php  echo $txt?>" id_g="<?php  echo $g?>" id="<?php  echo  $hw->hid  ?>"> <?php _e('-%', 'iii-dictionary') ?></button>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <?php if($hw->assignment_id ==4) {?>
                                                        <button type="button" data-practice-url="<?php echo $practice_url ?>" data-mode="<?php echo $hw->for_practice;?>" class="btn btn-default btn-tiny grey btn-a-link view-result-writing" style="color: #256CAA" data-status="<?php  echo $txt?>" id_g="<?php  echo $g?>" id="<?php  echo  $hw->hid  ?>"> <?php _e('-%', 'iii-dictionary') ?></button>
                                                    <?php } else { ?>
                                                        <?php if (ik_validate_date($hw->finished_on) || ik_validate_date($hw->accepted_on) || ik_validate_date($hw->requested_on)) { ?>
                                                            <?php if(!empty($hw->score)){ ?>
                                                                <button type="button" data-practice-url="<?php echo $practice_url ?>" data-mode="<?php echo $hw->for_practice;?>" class="btn btn-default btn-tiny grey btn-a-link view-result-none-writing" style="color: #256CAA" data-status="<?php  echo $txt?>" id_g="<?php  echo $g?>" id="<?php  echo  $hw->hid  ?>"> <?php _e($hw->score.'%', 'iii-dictionary') ?></button>
                                                            <?php }else { ?>
                                                                <button type="button" data-practice-url="<?php echo $practice_url ?>" data-mode="<?php echo $hw->for_practice;?>" class="btn btn-default btn-tiny grey btn-a-link view-result-none-writing" style="color: #256CAA" data-status="<?php  echo $txt?>" id_g="<?php  echo $g?>" id="<?php  echo  $hw->hid  ?>"> <?php _e('0%', 'iii-dictionary') ?></button>
                                                            <?php } ?>
                                                        <?php } else { ?>
                                                            <button type="button" data-practice-url="<?php echo $practice_url ?>" data-mode="<?php echo $hw->for_practice;?>" class="btn btn-default btn-tiny grey btn-a-link view-result-none-writing" style="color: #256CAA" data-status="<?php  echo $txt?>" id_g="<?php  echo $g?>" id="<?php  echo  $hw->hid  ?>"> <?php _e('-%', 'iii-dictionary') ?></button>
                                                        <?php } ?>
                                                    <?php } ?>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <?php
                                                    $s = $_GET["sat"];
                                                    $g = $_GET["gid"];
                                                    $url = $_SERVER['REQUEST_URI'];
                                                    if(strpos($url, '&ikcourse') !== false){
                                                    // check url have string ikcoure handing when finish worksheet "Next-Assignment" link back page ikmath course    
                                                        $last_url = substr($url, -8);
                                                        $array = split('&', $last_url);
                                                        $get_id_page = $array[1];
                                                        $practice_url = MWHtml::get_practice_page_url($hw->assignment_id) . '&amp;hid=' . $hw->hid.'&amp;sid=' . $hw->sheet_id.'&amp;sat='.$s.'&amp;gid='.$g.'&ismode='.$hw->for_practice.'&page-back='.$get_id_page;
                                                        $homework_url = MWHtml::get_practice_page_url($hw->assignment_id) . '&amp;mode=homework&amp;hid=' . $hw->hid.'&amp;sid=' . $hw->sheet_id.'&amp;sat='.$s.'&amp;gid='.$g.'&ismode='.$hw->for_practice.'&page-back='.$get_id_page;
                                                    } else if(!empty($_GET["back-ikmath"])){
                                                        $practice_url = MWHtml::get_practice_page_url($hw->assignment_id) . '&amp;hid=' . $hw->hid.'&amp;sid=' . $hw->sheet_id.'&amp;sat='.$s.'&amp;gid='.$g.'&ismode='.$hw->for_practice.'&amp;back-ikmath='.$_GET["back-ikmath"];
                                                        $homework_url = MWHtml::get_practice_page_url($hw->assignment_id) . '&amp;mode=homework&amp;hid=' . $hw->hid.'&amp;sid=' . $hw->sheet_id.'&amp;sat='.$s.'&amp;gid='.$g.'&ismode='.$hw->for_practice.'&amp;back-ikmath='.$_GET["back-ikmath"];
                                                    }else {
                                                        $practice_url = MWHtml::get_practice_page_url($hw->assignment_id) . '&amp;hid=' . $hw->hid.'&amp;sid=' . $hw->sheet_id.'&amp;sat='.$s.'&amp;gid='.$g.'&ismode='.$hw->for_practice;
                                                        $homework_url = MWHtml::get_practice_page_url($hw->assignment_id) . '&amp;mode=homework&amp;hid=' . $hw->hid.'&amp;sid=' . $hw->sheet_id.'&amp;sat='.$s.'&amp;gid='.$g.'&ismode='.$hw->for_practice;
                                                    }
                                                    if($is_sat_class_subscribed)
                                                        $annoying = 0;
                                                    else
                                                        $annoying = 1;
                                                    if ($hw->assignment_id != ASSIGNMENT_REPORT) :   //$is_sat_class_subscribed
                                                        ?>
                                                        <a href="<?php echo $practice_url ?>" data-annoying="<?php echo $annoying ?>" gname="<?php echo $group->name ?>" data-homework-url="<?php echo $homework_url ?>" data-for-practice="<?php echo $hw->for_practice ?>" data-startnew="<?php echo is_null($hw->finished) ? 1 : 0 ?>" class="btn btn-default btn-block btn-tiny grey goto-homework btn-a-link css-link"
                                                           <?php     
                                                            if ($class_type_id==1){ echo 'data-sat-class="Grammar Review" data-subscription-type="3" data-type="1"'; }
                                                            else if($class_type_id==2){ echo 'data-sat-class="Writing Practice" data-subscription-type="3" data-type="2"'; }
                                                            else if($class_type_id>2 && $class_type_id<8){ echo 'data-sat-class="SAT practice Test" data-subscription-type="3" data-type="3"'; }
                                                            else if($class_type_id==9){ echo 'data-sat-class="SAT I Preparation" data-subscription-type="7" data-type="9"'; }
                                                            else if($class_type_id>9 && $class_type_id<15){ echo 'data-sat-class="SAT I Simulated Test (New SAT Test)" data-subscription-type="7" data-type="10"'; }
                                                            else if($class_type_id==15){ echo 'data-sat-class="SAT II Preparation" data-subscription-type="8" data-type="15"'; }
                                                            else if($class_type_id>15 && $class_type_id<21){ echo 'data-sat-class="SAT II Simulated Test" data-subscription-type="8" data-type="16"'; }
                                                            else if($class_type_id==22) { echo 'data-sat-class="" data-subscription-type="22" data-type="22"'; }
                                                            else if($class_type_id==27) { echo 'data-sat-class="" data-subscription-type="27" data-type="27"'; }
                                                            else if($class_type_id>37 && $class_type_id<51){ echo 'data-sat-class="IK Math Classes" data-subscription-type="12" data-type="38"'; }
                                                            if(($hw->is_view)==0) {
                                                            ?>
                                                                ><?php _e('NEW', 'iii-dictionary') ?>
                                                            
                                                            <?php } else {
                                                                if(!empty($hw->practice_id)) {
                                                                ?>
                                                                    ><?php _e('FINISHED', 'iii-dictionary') ?>
                                                                <?php } else { ?>
                                                                    ><?php _e('CONTINUE', 'iii-dictionary') ?>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </a>
                                                        <?php
                                                    else :
                                                        $rp_url = $hw->for_practice ? $practice_url : $homework_url;
                                                        ?>
                                                        <a href="<?php echo $practice_url ?>" data-annoying="<?php echo $annoying ?>" class="btn btn-default btn-block btn-tiny grey btn-a-link css-link"
                                                           <?php 
                                                            if ($class_type_id==1){ echo 'data-sat-class="Grammar Review" data-subscription-type="3" data-type="1"'; }
                                                            else if($class_type_id==2){ echo 'data-sat-class="Writing Practice" data-subscription-type="3" data-type="2"'; }
                                                            else if($class_type_id>2 && $class_type_id<8){ echo 'data-sat-class="SAT practice Test" data-subscription-type="3" data-type="3"'; }
                                                            else if($class_type_id==9){ echo 'data-sat-class="SAT I Preparation" data-subscription-type="7" data-type="9"'; }
                                                            else if($class_type_id>9 && $class_type_id<15){ echo 'data-sat-class="SAT I Simulated Test (New SAT Test)" data-subscription-type="7" data-type="10"'; }
                                                            else if($class_type_id==15){ echo 'data-sat-class="SAT II Preparation" data-subscription-type="8" data-type="15"'; }
                                                            else if($class_type_id>15 && $class_type_id<21){ echo 'data-sat-class="SAT II Simulated Test" data-subscription-type="8" data-type="16"'; }
                                                            else if($class_type_id==22) { echo 'data-sat-class="" data-subscription-type="22" data-type="22"'; }
                                                            else if($class_type_id==27) { echo 'data-sat-class="" data-subscription-type="27" data-type="27"'; }
                                                            else if($class_type_id>37 && $class_type_id<51){ echo 'data-sat-class="IK Math Classes" data-subscription-type="12" data-type="38"'; }                                              
                                                            if(($hw->is_view)==0) {
                                                            ?>
                                                                ><?php _e('NEW', 'iii-dictionary') ?>
                                                            
                                                            <?php } else {
                                                                if(!empty($hw->practice_id)) {
                                                                ?>
                                                                    ><?php _e('FINISHED', 'iii-dictionary') ?>
                                                                <?php } else { ?>
                                                                    ><?php _e('CONTINUE', 'iii-dictionary') ?>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </a>
                                                    <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php
                                    endforeach;
                                    if (count($homeworks->items) < 13) {
                                        for ($i = count($homeworks->items); $i < 13; $i++) {
                                        ?>
                                        <tr ><td style="height : 35px;width: 1%" colspan="7" ></td></tr>
                                        <?php
                                        }
                                    }
                                ?>
                                <?php
                                else :
                                    ?>
                                    <tr>
                                        <td colspan="7"><?php _e('No homework assigned to this Group yet', 'iii-dictionary') ?></td>
                                    </tr>
                                    <?php for ($i = 0; $i < 13; $i++) { ?>
                                        <tr ><td style="height : 35px; width: 1%" colspan="7" ></td></tr>
                                    <?php } ?>
                                <?php endif ?>
                            </tbody>
<!--                            <tbody class="background-838383">
                                <tr><td colspan="6" class="td-average"><?php printf(__('Average : %d %s', 'iii-dictionary'), $_averge, '%') ?></td></tr>
                            </tbody>-->
                        </table>
                    </div>
                </div>
                <input type="hidden" id="uref" value="<?php echo $uref ?>">

                <?php
                ik_enqueue_js_messages('homework_writing', __('This writing assignment is currently being graded. You can only restart once grading is complete.', 'iii-dictionary'));
                ik_enqueue_js_messages('homework_assigned', __('This is the Homework assigned by your teacher. You can view the score from the Student’s Box.', 'iii-dictionary'));
                ik_enqueue_js_messages('test_inst', __('This is the Test assigned by your teacher. The score will be displayed at Homework Status panel.', 'iii-dictionary'));
                ik_enqueue_js_messages('practice_inst', __('This is Practice Worksheet sent by your teacher', 'iii-dictionary'));
                ik_enqueue_js_messages('unfinished_homework', __('You have more than 2 unfinished homeworks. Please complete it before starting another one.', 'iii-dictionary'));
                ik_enqueue_js_messages('point_err', sprintf(__('Your current points is <strong>%d</strong> pts. You don\'t have enough points to request grading for this homework', 'iii-dictionary'), ik_get_user_points($current_user_id)));
                ?>

                <div id="request-grading-dialog" class="modal fade modal-white ik-modal1 ik-modal-transparent" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog magin-top-1"></a>
                                <h3 style="color: #FBD582"><?php _e('Request Grading', 'iii-dictionary') ?></h3>
                            </div>
                            <div class="modal-body" style="padding-bottom: 0px !important;padding-top: 4%;">
                                <?php _e('Your request has been sent already.', 'iii-dictionary') ?>
                                <p class="text-danger" id="request-grading-err"></p>
                            </div>
                            <div class="modal-footer">
                                <div class="row" style="padding-top: 2%; padding-bottom: 3%;">
                                    <div class="col-sm-6" style="width:100%">
                                        <button class="btn btn-block orange css-btn-request1 btn-close-request"><?php _e('OK', 'iii-dictionary') ?></button>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="hrid" id="hrid">
                            <input type="hidden" name="hid" id="hid">
                        </div>
                    </div>
                </div>
                <input type="hidden" name="task" id="task" value="">
                <input type="hidden" name="gid" id="gid" value="">
                <input type="hidden" id="unfinished_homework" value="<?php echo is_homework_unfinished() ? 1 : 0 ?>">
            </form>
        </div>
        <?php } else if ($lvid) { ?>
            <div id="loadhomework_group" >
            <?php
            $worksheet = MWDB::get_homework_by_group_id_critical($_REQUEST['lvid']);
//            $obj = json_decode($worksheet);
//            echo '<pre>';
//            print_r($obj[0]->name);
            // check subscribe group
            $rec = $wpdb->get_row("SELECT class_type_id FROM {$wpdb->prefix}dict_group_details WHERE group_id = {$gid}");
            $class_type_id = $rec->class_type_id;
            $is_sat_class_subscribed = is_sat_class_subscribed($class_type_id);
            if(!empty($_GET["lvid"])){
                $get_title_group = MWDB::get_info_title_group($_GET["lvid"]);
//                var_dump($get_title_group);die;
            }
            ?>
            <form method="post" action="<?php echo locale_home_url() ?>/?r=online-learning<?php echo $gid ? '&amp;gid=' . $gid : '' ?><?php echo $gid ? '&amp;price=' . $gi : '' ?>">
                <div class="box-header box-header-custom" >
                    <?php if(is_math_panel()) {?>
                        <a href="<?php echo locale_home_url() ?>/?r=online-learning&crit-math&bid=1" class="link-back css-back" style="height: 39px;"><span class="span-icon-left css-icon-back" id="link-back-critical"></span><span class="txt-back">BACK</span></a>
                    <?php } else { ?>
                        <a href="<?php echo locale_home_url() ?>/?r=online-learning&crit-english&bid=1" class="link-back css-back" style="height: 39px;"><span class="span-icon-left css-icon-back" id="link-back-critical"></span><span class="txt-back">BACK</span></a>
                    <?php } ?>
                    <div class="col-xs-12" style="padding-left: 0px !important;padding-top: 10px">
                        <p  class="col-xs-12 p-left5-percent" style="color: #fcd971;float: left;padding-right: 5px;"><span  style="color: #ffcc62;font-size: 18px;" class="css-pad-left-4-p-destop"><?php echo $get_title_group[0]->name ?></span></p>
                        <p class="col-xs-12 p-left5-percent css-pad-left-5-p-destop" ><?php _e('By:', 'iii-dictionary') ?> <span><?php echo $get_title_group[0]->display_name ?></span></p>                        
                    </div>
                </div>
                <div class="homeworkcritical-online can-scroll" style="height: 500px">
                    <div style="width: 100%">
                        <table class="table table-striped table-condensed ik-table1 text-center vertical-middle scroll-fix-head" id="homeworkcritical">
                            <thead class="homeworkcritical">
                                <tr>
                                    <th class="text-color-custom-1 p-left5-percent" style="width: 33%; text-align: left"><?php _e('List of Worksheet', 'iii-dictionary') ?></th>
                                    <th class="text-color-custom-1 css-mobile-width1"><?php _e('Due date', 'iii-dictionary') ?></th>
                                    <th class="text-color-custom-1" style="width: 5% !important"><?php _e('Score', 'iii-dictionary') ?></th>
                                    <th class="text-color-custom-1 css-mobile-th3"><?php _e('Status', 'iii-dictionary') ?></th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr><td colspan="6"><?php echo $pagination ?></td></tr>
                            </tfoot>
                            <tbody style="max-height: 468px;"> 
                                <?php
                                if (!empty($worksheet)) :
                                    foreach($worksheet as $value):
                                        ?>
                                        <tr>
                                            <td class="p-left5-percent" style="width: 52%;text-align: left !important"><?php if(empty($value->name)) echo $value->sheet_name; else echo $value->name;?></td>
                                            <td style="width: 20%"><?php echo $value->deadline == '0000-00-00' ? 'No deadline' : ik_date_format($value->deadline) ?></td>
                                            <?php
                                                $get_stg = MWDB::get_something_in_group($value->group_id);
                                            ?>
                                            <?php if(($value->assignment_id==ASSIGNMENT_REPORT) || ($value->assignment_id==ASSIGNMENT_WRITING)) { ?>
                                                <td class="css-score-view">
                                                    <button type="button" data-practice-url="<?php echo $practice_url ?>" class="btn btn-default btn-tiny grey btn-a-link view-result-writing" style="color: #256CAA" data-status="<?php  echo $txt?>" id="<?php  echo  $value->hid  ?>"> <?php echo $value->score ?>%</button>
                                            <?php }else { ?>
                                                <td class="css-score-view ">
                                                    <button type="button" data-practice-url="<?php echo $practice_url ?>" class="btn btn-default btn-tiny grey btn-a-link view-result-none-writing" style="color: #256CAA" data-status="<?php  echo $txt?>" id="<?php  echo  $value->hid  ?>"> <?php echo $value->score.'%' ?></button>
                                            <?php } ?>   
                                               
                                            </td>
                                            <td style="width: 16%;padding-right: 2%;">
                                                <?php 
                                                if (empty($get_stg->step_of_user) && $item->price != 0 || !is_user_logged_in()) { ?>
                                                <a href="#" data-name="<?php echo $item->name ?>" data-free=<?php echo ( $item->price == 0 ) ? '1' : '0' ?> data-jcid="<?php echo $item->id ?>" class="bold-font btn btn-default btn-block btn-tiny grey btn-a-link join-class-lang-btn"><?php _e('JOIN', 'iii-dictionary') ?></a>
                                                    <?php
                                                        
                                                } else if($value->group_id ==161) { ?> 
                                                <a href="<?php echo locale_home_url()?>/?r=writing-report&hid=1018" class="bold-font btn btn-default btn-block btn-tiny css-color-006fbd btn-a-link prevent-detail-btn-<?php echo $is_math ? 'math' : 'english' ?> "><?php _e('START', 'iii-dictionary') ?></a>
                                                <?php }else {  
                                                    if($get_stg->step['assg'] =="") {
                                                        $get_stg->step['assg'] = $value->assignment_id;
                                                    }
                                                    if(!empty($_GET["lvid"])) {
                                                        $practice_url = MWHtml::get_practice_page_url($get_stg->step['assg']) . '&amp;ismode=1&amp;hid=' . $value->id.'&amp;pr=' . $item->price.'&amp;lvid=' .$_GET["lvid"];
                                                    $homework_url = MWHtml::get_practice_page_url($get_stg->step['assg']) . '&amp;ismode=0&hid=' . $value->id.'&amp;pr=' . $item->price.'&amp;lvid=' .$_GET["lvid"];
                                                    }else{
                                                        $practice_url = MWHtml::get_practice_page_url($get_stg->step['assg']) . '&amp;ismode=1&amp;hid=' . $value->id.'&amp;pr=' . $item->price;
                                                        $homework_url = MWHtml::get_practice_page_url($get_stg->step['assg']) . '&amp;ismode=0&hid=' . $value->id.'&amp;pr=' . $item->price;
                                                    }
                                                    if($value->for_practice) {
                                                        $rp_url = $practice_url;
                                                    } else {
                                                        $rp_url = $homework_url;
                                                    }
                                                    $rp_url = !empty($uref) ? $rp_url . '&ref=' . $uref : $rp_url;
                                                    ?>
                                                    <a href="<?php echo $rp_url ?>" class="bold-font btn btn-default btn-block btn-tiny css-color-006fbd btn-a-link css-link prevent-detail-btn-<?php echo $is_math ? 'math' : 'english' ?> "><?php _e('START', 'iii-dictionary') ?></a>
                                                <?php } ?>
                                            </td>
                                            <td></td>
                                        </tr>
                                        <?php
                                    endforeach;
                                    if (count($worksheet) < 13) {
                                        for ($i = count($worksheet); $i < 13; $i++) {
                                        ?>
                                        <tr ><td style="height : 35px;width: 1%" colspan="7" ></td></tr>
                                        <?php
                                        }
                                    }
                                ?>
                                <?php
                                else :
                                    ?>
                                    <tr>
<!--                                        <td colspan="7">No homework assigned to this Group yet</td>-->
                                        <td colspan="7" style="height: 35px;"></td>
                                    </tr>
                                    <?php for ($i = 0; $i < 13; $i++) { ?>
                                        <tr ><td style="height : 35px; width: 1%" colspan="7" ></td></tr>
                                    <?php } ?>
                                <?php endif ?>
                            </tbody>
<!--                            <tbody class="background-838383">
                                <tr><td colspan="6" class="td-average"><?php printf(__('Average : %d %s', 'iii-dictionary'), $_averge, '%') ?></td></tr>
                            </tbody>-->
                        </table>
                    </div>
                </div>
                <input type="hidden" id="uref" value="<?php echo $uref ?>">

                <?php
                ik_enqueue_js_messages('test_inst', __('This is the Test assigned by your teacher. The score will be displayed at Homework Status panel.', 'iii-dictionary'));
                ik_enqueue_js_messages('practice_inst', __('This is Practice Worksheet sent by your teacher', 'iii-dictionary'));
                ik_enqueue_js_messages('unfinished_homework', __('You have more than 2 unfinished homeworks. Please complete it before starting another one.', 'iii-dictionary'));
                ik_enqueue_js_messages('point_err', sprintf(__('Your current points is <strong>%d</strong> pts. You don\'t have enough points to request grading for this homework', 'iii-dictionary'), ik_get_user_points($current_user_id)));
                ?>

                <div id="request-grading-dialog" class="modal fade modal-red-brown" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
                                <h3><?php _e('Request Grading', 'iii-dictionary') ?></h3>
                            </div>
                            <div class="modal-body">
                                <?php printf(__('This grading and editing your writing costs %s points.', 'iii-dictionary'), '<strong id="grading-cost"></strong>') ?>
                                <a href="<?php echo locale_home_url() ?>/?r=manage-subscription" ><?php _e('Need Points?', 'iii-dictionary') ?></a>
                                <p class="text-danger" id="request-grading-err"></p>
                            </div>
                            <div class="modal-footer">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <button name="request-grading" class="btn btn-block orange"><span class="icon-check"></span><?php _e('OK', 'iii-dictionary') ?></button>
                                    </div>
                                    <div class="col-sm-6">
                                        <a href="#" data-dismiss="modal" class="btn btn-block grey"><span class="icon-cancel"></span><?php _e('Cancel', 'iii-dictionary') ?></a>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="hrid" id="hrid">
                            <input type="hidden" name="hid" id="hid">
                        </div>
                    </div>
                </div>
                <input type="hidden" name="task" id="task" value="">
                <input type="hidden" name="gid" id="gid" value="">
                <input type="hidden" id="unfinished_homework" value="<?php echo is_homework_unfinished() ? 1 : 0 ?>">
            </form>
        </div>
        <?php } else if ($lvgrid) { ?>
            <div id="loadhomework_group_from_homework" >
            <?php
            $obj = MWDB::get_list_worksheet_group_from_homework($_REQUEST['lvgrid']);
            //$obj = json_decode($worksheet);
//            echo '<pre>';
//            print_r($obj[0]->name);
            // check subscribe group
            $rec = $wpdb->get_row("SELECT class_type_id FROM {$wpdb->prefix}dict_group_details WHERE group_id = {$gid}");
            $class_type_id = $rec->class_type_id;
            $is_sat_class_subscribed = is_sat_class_subscribed($class_type_id);
            ?>
            <form method="post" action="<?php echo locale_home_url() ?>/?r=online-learning<?php echo $gid ? '&amp;gid=' . $gid : '' ?><?php echo $gid ? '&amp;price=' . $gi : '' ?>">
                <div class="box-header box-header-custom" style="height: 48px;">
                    <a href="<?php echo locale_home_url() ?>/?r=online-learning" class="link-back css-back" ><span class="span-icon-left css-icon-back"></span><span class="txt-back">BACK</span></a>
                    <div class="col-xs-12" style="padding-left: 0px !important;padding-top: 10px;width: 75%">
                        <p  class="col-xs-12 p-left5-percent" style="color: #fcd971;float: left;padding-right: 5px;"> 
                            <span  style="color: #ffcc62;font-size: 20px;"><?php echo $obj[0]->name ?></span>
                        </p>
                    </div>
                </div>
                <div class="homeworkcritical-online can-scroll" style="height: 500px">
                    <div style="width: 100%">
                        <table class="table table-striped table-condensed ik-table1 text-center vertical-middle scroll-fix-head" id="homeworkcritical">
                            <thead class="homeworkcritical">
                                <tr>
                                    <th class="text-color-custom-1 p-left5-percent" style="width: 33%; text-align: left"><?php _e('List of Worksheet', 'iii-dictionary') ?></th>
                                    <th class="text-color-custom-1" style="width: 17%"><?php _e('Due date', 'iii-dictionary') ?></th>
                                    <th class="text-color-custom-1" style="width: 5% !important"><?php _e('Score', 'iii-dictionary') ?></th>
                                    <th class="text-color-custom-1" style="width: 15%"><?php _e('Status', 'iii-dictionary') ?></th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr><td colspan="6"><?php echo $pagination ?></td></tr>
                            </tfoot>
                            <tbody>
                                <?php
                                if (!empty($obj)) :
                                    foreach($obj as $value):
                                        ?>
                                        <tr>
                                            <td class="p-left5-percent" style="width: 52%;text-align: left !important"><?php echo $value->sheet_name?></td>
                                            <td style="width: 20%"><?php echo $value->deadline == '0000-00-00' ? 'No deadline' : ik_date_format($value->deadline) ?></td>
                                            <td style="width: 12% !important;color: #5280AC;font-weight: bold">
                                                <?php if (!is_null($value->attempted_on) && $value->assignment_id != ASSIGNMENT_REPORT) : ?>
                                                   <?php echo $value->score.'%' ?>
                                                <?php else : ?>
                                                    <?php echo $value->score ?>%
                                                <?php endif ?>
                                            </td>
                                            <?php
                                            if ($value->finished == '0') {
                                                if($value->finished_question == 0) {
                                                    $txt = __('New', 'iii-dictionary');
                                                ?>
                                                    <td><a href="<?php echo locale_home_url() . '/?r=online-learning' ?>"><strong style="text-decoration: underline;" class="<?php echo $td_class ?>" btn-view-sheet><?php echo $txt ?></strong></a></td>
                                                <?php
                                                }else{ 
                                                    $txt = __('Unfinished', 'iii-dictionary');
                                                ?>
                                                    <td><a href="<?php echo locale_home_url() . '/?r=online-learning' ?>"><strong style="text-decoration: underline;"  class="<?php echo $td_class ?>" btn-view-sheet><?php echo $txt ?></strong></a></td>
                                                <?php
                                                }
                                            } else {
                                                $txt = __('Finished', 'iii-dictionary');
                                                ?>
                                                    <!-- Is class Math so always is result Numberic Answer-->
                                                    <td><a href="<?php echo locale_home_url() . '/?r=online-learning' ?>"><strong style="text-decoration: underline;" data-id ="<?php echo $value->sid ?>" class="<?php echo $td_class ?>view-result-none-writing" btn-view-sheet><?php echo $txt ?></strong></a></td>
                                                <?php   
                                            }
                                            ?>
                                            
                                            <td></td>
                                        </tr>
                                        <?php
                                    endforeach;
                                    if (count($obj) < 13) {
                                        for ($i = count($obj); $i < 13; $i++) {
                                        ?>
                                        <tr ><td style="height : 35px;width: 1%" colspan="7" ></td></tr>
                                        <?php
                                        }
                                    }
                                ?>
                                <?php
                                else :
                                    ?>
                                    <tr>
<!--                                        <td colspan="7">No homework assigned to this Group yet</td>-->
                                        <td colspan="7" style="height: 35px;"></td>
                                    </tr>
                                    <?php for ($i = 0; $i < 13; $i++) { ?>
                                        <tr ><td style="height : 35px; width: 1%" colspan="7" ></td></tr>
                                    <?php } ?>
                                <?php endif ?>
                            </tbody>
                            <tbody class="background-838383">
                                <tr><td colspan="6" class="td-average"><?php printf(__('Average : %d %s', 'iii-dictionary'), $_averge, '%') ?></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <input type="hidden" id="uref" value="<?php echo $uref ?>">

                <?php
                ik_enqueue_js_messages('test_inst', __('This is the Test assigned by your teacher. The score will be displayed at Homework Status panel.', 'iii-dictionary'));
                ik_enqueue_js_messages('practice_inst', __('This is Practice Worksheet sent by your teacher', 'iii-dictionary'));
                ik_enqueue_js_messages('unfinished_homework', __('You have more than 2 unfinished homeworks. Please complete it before starting another one.', 'iii-dictionary'));
                ik_enqueue_js_messages('point_err', sprintf(__('Your current points is <strong>%d</strong> pts. You don\'t have enough points to request grading for this homework', 'iii-dictionary'), ik_get_user_points($current_user_id)));
                ?>

                <div id="request-grading-dialog" class="modal fade modal-red-brown" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
                                <h3><?php _e('Request Grading', 'iii-dictionary') ?></h3>
                            </div>
                            <div class="modal-body">
                                <?php printf(__('This grading and editing your writing costs %s points.', 'iii-dictionary'), '<strong id="grading-cost"></strong>') ?>
                                <a href="<?php echo locale_home_url() ?>/?r=manage-subscription"><?php _e('Need Points?', 'iii-dictionary') ?></a>
                                <p class="text-danger" id="request-grading-err"></p>
                            </div>
                            <div class="modal-footer">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <button name="request-grading" class="btn btn-block orange"><span class="icon-check"></span><?php _e('OK', 'iii-dictionary') ?></button>
                                    </div>
                                    <div class="col-sm-6">
                                        <a href="#" data-dismiss="modal" class="btn btn-block grey"><span class="icon-cancel"></span><?php _e('Cancel', 'iii-dictionary') ?></a>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="hrid" id="hrid">
                            <input type="hidden" name="hid" id="hid">
                        </div>
                    </div>
                </div>
                <input type="hidden" name="task" id="task" value="">
                <input type="hidden" name="gid" id="gid" value="">
                <input type="hidden" id="unfinished_homework" value="<?php echo is_homework_unfinished() ? 1 : 0 ?>">
            </form>
        </div> 
        <?php } else if ($wrid) {
        ?>
        <div class="boder-black " id="view-result-wrid">
            <div class="modal-header custom-header" style="padding: 1px 0px 1px 5% !important;">
                <a style="top: 1.3%;" href="<?php echo locale_home_url() ?>/?r=online-learning" class="link-back"><?php _e('Back', 'iii-dictionary') ?><span class="span-icon-left"></span></a>
                <h3 style="margin-top: 10px; color: #fff"><?php _e('View Result', 'iii-dictionary') ?></h3>
            </div>
            <div style="margin: 20px 5%; max-height: 550px" class="scroll-list-1"  >
                <div style="color: #000">
                    <?php
                    $homework_result_wr = MWDB::get_homework_results($wrid);
                    $questions = json_decode($homework_result_wr[0]->questions, true);
                    $answers = json_decode($homework_result_wr[0]->answers, true);
                    $teacher_comments = json_decode($homework_result[0]->teacher_comments, true);
                    ?>
                    <h3 style="color: black;margin-bottom: 3%"><?php echo $homework_result_wr[0]->sheet_name ?></h3>
                    <div style="border-bottom: 1px solid #ccc;margin-bottom: 20px"></div>
                    <?php if (!empty($homework_result_wr[0]->dictionary)) : ?>
                        <div class="col-sm-4 col-md-4">
                            <span class="span-purchase-red"></span><label class="font-dialog"><?php _e('Dictionary:', 'iii-dictionary') ?> <span class="bold"><?php echo $homework_result_wr[0]->dictionary ?></span></label>
                        </div>
                    <?php endif ?>
                    <div class="col-sm-4 col-md-4">
                        <span class="span-purchase-red"></span><label class="font-dialog"><?php _e('Level:', 'iii-dictionary') ?> <span class="bold"><?php echo $homework_result_wr[0]->grade ?></span></label>
                    </div>
                    <div class="col-sm-4">
                        <span class="span-purchase-red"></span><label class="font-dialog"><?php _e('Completed Date:', 'iii-dictionary') ?> <span class="bold"><?php echo ik_date_format($homework_result_wr[0]->submitted_on) ?></span></label>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-sm-4 col-md-4">
                        <span class="span-purchase-red"></span><label class="font-dialog"><?php _e('Score:', 'iii-dictionary') ?> <span class="bold">
                                <?php if ($homework_result_wr[0]->assignment_id == ASSIGNMENT_WRITING) : ?>
                                    <?php printf(__('%d %%', 'iii-dictionary'), $homework_result_wr[0]->score) ?>
                                <?php else : ?>
                                    <?php printf(__('%d correct, %d %%', 'iii-dictionary'), $homework_result_wr[0]->correct_answers_count, $homework_result[0]->score) ?>
                                <?php endif ?>
                            </span></label>
                    </div>
                    <div class="col-sm-5 col-md-8">
                        <span class="span-purchase-red"></span><label class="font-dialog"><?php _e('Last Attempt:', 'iii-dictionary') ?> <span class="bold"><?php echo ik_date_format($homework_result_wr[0]->attempted_on) ?></span></label>
                    </div>
                    <div class="clearfix"></div>
                    <div style="border-bottom: 1px solid #ccc;margin-top: 15px; margin-bottom: 15px"></div>
                </div>
                <div class="row">
                    <?php switch ($homework_result_wr[0]->assignment_id) {

                        case ASSIGNMENT_SPELLING:
                            ?>

                            <div class="col-sm-12">
                                <h2 class="title-border"><?php _e('Missed Word', 'iii-dictionary') ?></h2>
                            </div>
            <?php foreach ($answers as $key => $item) : $n = substr($key, 1) ?>

                                <div class="col-sm-12">
                                    <div class="box box-gray-dialog form-group">
                                        <strong class="text-primary"><?php echo __('Question', 'iii-dictionary') . ' ' . ($n + 1) ?></strong>
                                        <div>
                                            <audio controls>
                                                <source src="<?php echo $item['question'] ?>" type="audio/mpeg">
                                            </audio>
                                        </div>
                                        <strong class="heading3 text-success"><?php _e('Correct Answer', 'iii-dictionary') ?></strong>
                                        <div style="color: #fff">
                <?php echo $questions[$n] ?>
                                        </div>
                                        <strong class="heading3 text-success"><?php _e('Your Answer', 'iii-dictionary') ?></strong>
                                        <div style="color: #<?php echo $answers['q' . $key]['score'] ? 'fff' : 'FF5959' ?>">
                <?php echo $item['selected'] ?>
                                        </div>
                                    </div>
                                </div>

                            <?php
                            endforeach;
                            break; // end spelling case

                        case ASSIGNMENT_VOCAB_GRAMMAR:
                        case ASSIGNMENT_READING:

                            if ($homework_result_wr[0]->assignment_id == ASSIGNMENT_READING) :
                                ?>
                                <div class="col-sm-12">
                                    <strong class="font-gray-italic"><?php _e('Article', 'iii-dictionary') ?></strong>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group box-gray-dialog">
                                        <div class="scroll-list" style="max-height: 300px; ">
                                <?php echo $homework_result_wr[0]->passages ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif ?>
                            <div class="col-sm-12">
                                <h5 class="font-gray-italic"><?php _e('Question & Answers', 'iii-dictionary') ?></h5>
                            </div>
            <?php foreach ($questions['question'] as $key => $item) : ?>

                                <div class="col-sm-12">
                                    <div class="box-gray-dialog form-group" style="background: #e3f7eb">
                                        <div class="heading3 text-primary col-xs-3 bold"><?php echo __('Question:', 'iii-dictionary') . ' ' . ($key + 1) ?></div>
                                        <div class="scroll-list col-xs-9" style="max-height: 150px; color: black">
                <?php echo $item ?>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="heading3 text-success col-xs-3 bold" style="color: #9b7514"><?php _e('Correct Answer:', 'iii-dictionary') ?></div>
                                        <div class="col-xs-9" style="color: #9b7514">
                <?php echo $questions['c_answer'][$key] ?>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="heading3 text-success col-xs-3 bold" style="color: #747b74"><?php _e('Your Answer:', 'iii-dictionary') ?></div>
                                        <div class="col-xs-9" style="color: #747b74">
                <?php echo $answers['q' . $key]['selected'] ?>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>

                            <?php
                            endforeach;
                            break; // end vocabulary and reading case

                        case ASSIGNMENT_WRITING:

                            if (!empty($questions['question'])) :
                                ?>
                                <div class="col-sm-12">
                                    <h2 class="title-border"><?php _e('Grading Results', 'iii-dictionary') ?></h2>
                                </div>
                <?php foreach ($questions['question'] as $key => $question) : ?>

                                    <div class="col-sm-12">
                                        <strong class="heading3 text-primary"><?php _e('Subject', 'iii-dictionary') ?></strong>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group box">
                                            <div class="scroll-list" style="max-height: 150px; color: #fff">
                    <?php echo nl2br($question) ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <strong class="heading3 text-success"><?php _e('Your Essay', 'iii-dictionary') ?></strong>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group box box-sapphire" style="word-wrap: break-word">
                    <?php echo $answers['q' . $key] ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <strong class="heading3 text-danger"><?php _e('Teacher\'s Comments', 'iii-dictionary') ?></strong>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group" style="color: #fff">
                    <?php echo empty($teacher_comments['q' . $key]) ? 'No comments' : $teacher_comments['q' . $key] ?>
                                        </div>
                                    </div>
                <?php endforeach;
            else :
                ?>

                                <div class="col-sm-12 form-group">
                                    <div class="box box-sapphire">
                                        <strong class="heading3 text-success"><?php _e('All of your answers is correct', 'iii-dictionary') ?></strong>
                                    </div>
                                </div>

                            <?php
                            endif;
                            break; // end writing case

                        case MATH_ASSIGNMENT_SINGLE_DIGIT:
                        case MATH_ASSIGNMENT_TWO_DIGIT_MUL:
                        case MATH_ASSIGNMENT_SINGLE_DIGIT_DIV:
                        case MATH_ASSIGNMENT_TWO_DIGIT_DIV:
                            ?>

                            <div class="col-sm-12">
                                <div class="box box-red form-group">
                                    <strong class="heading3 text-primary"><?php echo __('Question', 'iii-dictionary') ?></strong>
                                    <div style="max-height: 150px; color: #fff">
                                        <?php echo $questions['op1'] . ' ' . $questions['sign'] . ' ' . $questions['op2'] ?> =
                                    </div>
                                    <strong class="heading3 text-success"><?php _e('Correct Answer', 'iii-dictionary') ?></strong>
                                    <div style="color: #fff">
                            <?php echo $questions['step']['s' . count($questions['step'])] ?>
                                    </div>
                                    <strong class="heading3 text-success"><?php _e('Your Answer', 'iii-dictionary') ?></strong>
                                    <div style="color: #<?php echo $homework_result_wr[0]->score ? 'fff' : 'FF5959' ?>">
                            <?php echo implode('', $answers['s' . count($questions['step'])]) ?>
                                    </div>
                                </div>
                            </div>

            <?php
            break; // end math addition, substraction, multiplication and division case

        case MATH_ASSIGNMENT_FLASHCARD:
        case MATH_ASSIGNMENT_FRACTION:

            foreach ($questions['q'] as $key => $item) :
                ?>

                                <div class="col-sm-12">
                                    <div class="box box-red form-group">
                                        <strong class="heading3 text-primary"><?php echo __('Question', 'iii-dictionary') . ' ' . substr($key, 1) ?></strong>
                                        <div style="max-height: 150px; color: #fff">
                                            <?php echo $item['op1'] . ' ' . $item['op'] . ' ' . $item['op2'] ?> =
                                        </div>
                                        <strong class="heading3 text-success"><?php _e('Correct Answer', 'iii-dictionary') ?></strong>
                                        <div style="color: #fff">
                <?php echo $item['answer'] . ' ' . $item['note'] ?>
                                        </div>
                                        <strong class="heading3 text-success"><?php _e('Your Answer', 'iii-dictionary') ?></strong>
                                        <div style="color: #fff">
                                <?php if ($homework_result_wr[0]->assignment_id == MATH_ASSIGNMENT_FLASHCARD) : ?>
                                    <?php echo implode('/', (array) $answers[$key]) . ' ' . $item['note'] ?>
                                <?php else : ?>
                    <?php echo count($answers[$key]) == 3 ? $answers[$key][0] . ' ' . $answers[$key][1] . '/' . $answers[$key][2] : implode('/', $answers[$key]) ?>
                <?php endif ?>
                                        </div>
                                    </div>
                                </div>

            <?php
            endforeach;
            break; // end math flashcard and fraction case

        case MATH_ASSIGNMENT_WORD_PROB:
            ?>

                            <div class="col-sm-12">
                                <div class="box box-red form-group">
                                    <strong class="heading3 text-success"><?php _e('Correct Answer', 'iii-dictionary') ?></strong>
                                    <div style="color: #fff">
                            <?php echo $questions['answer'] ?>
                                    </div>
                                    <strong class="heading3 text-success"><?php _e('Your Answer', 'iii-dictionary') ?></strong>
                                    <div style="color: #<?php echo $homework_result_wr[0]->score ? 'fff' : 'FF5959' ?>">
            <?php echo $answers ?>
                                    </div>
                                </div>
                            </div>

                                        <?php
                                        break; // end math word problem case

                                    case MATH_ASSIGNMENT_QUESTION_BOX:
                                    case MATH_ASSIGNMENT_EQUATION:
                                        foreach ($questions['q'] as $key => $item) : if ($item['answer'] != '') :
                                                ?>

                                    <div class="col-sm-12">
                                        <div class="box box-red form-group">
                                            <strong class="heading3 text-primary">
                                    <?php
                                    echo $homework_result_wr[0]->assignment_id == MATH_ASSIGNMENT_EQUATION ?
                                            __('Question', 'iii-dictionary') :
                                            __('Step', 'iii-dictionary');
                                    echo ' ' . substr($key, 1)
                                    ?></strong>
                                            <strong class="heading3 text-success"><?php _e('Correct Answer', 'iii-dictionary') ?></strong>
                                            <div style="color: #fff"><?php echo $item['answer'] ?></div>
                                            <strong class="heading3 text-success"><?php _e('Your Answer', 'iii-dictionary') ?></strong>
                                            <div style="color: #fff"><?php echo $answers[$key] ?></div>
                                        </div>
                                    </div>

                <?php
                endif;
            endforeach;
            break; // end math question box case

        case MATH_ASSIGNMENT_EQUATION:
            ?>

            <?php break; // end euqation case
    }
    ?>
                </div>
            </div>
        </div>
<?php } ?>
    <div  id="div_hw">
        <di  id="homework_critical">
            <div class="homeworkcritical-online can-scroll " style="height: 500px ! important;"> 
                <?php MWHtml::load_homework_group($user_groups, true); ?>
            </div>
        </di>
    </div>
    <script>
        var a1 = jQuery('#homework_critical').html();
        jQuery("#div_hw").html('');
        jQuery(document).ready(function () {
            if (jQuery('.cs-select option:selected').val() == 'homeworkagm') {
                jQuery('#loadhomework').html(a1);
            }
        });
    </script>
    <div  id="div_hw1">
        <div id="homeworkgroup" style="overflow: hidden;">
            <div class="homeworkcritical-online can-scroll" style="height:500px;" >
                <?php MWHtml::load_homework(); ?>
            </div>
        </div>
    </div>
    <div  id="div_hw2">
        <div id="homeworksat" style="overflow: hidden;">
<?php
if (!$is_math_panel) {
    MWHtml::load_homework_sat();
} else {
    ?>
                <div >
                    <h3 style="margin: 0 0 10px 0;"><?php _e('SAT I Preparation', 'iii-dictionary'); ?></h3>
            <?php MWHtml::load_homework_math_sat(); ?>
                </div>
                <div style="padding-top: 35px">
                    <h3 style="margin: 0 0 10px 0;"><?php _e('SAT II Preparation', 'iii-dictionary'); ?></h3>
    <?php MWHtml::load_homework_math_sat_ii(); ?>
                </div>
<?php } ?>
        </div>
    </div>
<script>
    var a2 = jQuery('#homeworkgroup').html();
        jQuery("#div_hw1").html('');
</script>
 
<script>
    var a3 = jQuery('#homeworksat').html();
    jQuery("#div_hw2").html('');
    jQuery('.main-article header').css('padding-top', '15px');
</script>
    <div  id="div_hw3">
        <form action="<?php echo $form_action ?>" method="post" id="main-form">
            <div id="homeworkcourse" style="overflow: hidden;">
                <div class="homeworkcritical-online can-scroll" style="height:477px;" >
                    <?php MWHtml::load_ikmaths_course(); ?>
                </div>
            </div>
        </form>
    </div>
    <div class="modal fade modal-purple modal-large" id="class-detail-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-custom-first">
            <div class="modal-content boder-black">
                <div class="modal-header custom-header">
                    <span style="right: 5%;padding-top: 5%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                    <h3 class="modal-title" id="myModalLabel"><?php _e('Class Detail', 'iii-dictionary') ?></h3>
                </div>
                <div class="modal-body body-custom" style="max-height: calc(67vh - 210px);overflow-y: auto;"></div>
            </div>
        </div>
    </div>
    <div  id="div_hw5">
        <div id="homeworktutoringplan" style="overflow: hidden;">
            <div class="homeworkcritical-online can-scroll" style="height:477px;" >
                <?php MWHtml::load_tutoringplan(); ?>
            </div>
        </div>
    </div>
  
<div class="modal fade modal-purple modal-large" id="view-score-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3 class="modal-title" id="myModalLabel"><?php _e('View Score', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-body body-custom">
                <table class="table-custom-2 table table-striped table-condensed ik-table1 ik-table-break-all text-center table-custom-color text-table-black" id="table-score">
                    <thead style="background-color: #fff !important;color: #000 !important;"><tr>
                            <th><?php _e('Worksheet Name', 'iii-dictionary') ?></th>
                            <th><?php _e('Score', 'iii-dictionary') ?></th>
                            <th><?php _e('Result', 'iii-dictionary') ?></th>
                            <th><?php _e('Completed Date', 'iii-dictionary') ?></th>
                        </tr></thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
    <script>
        var annoying = <?php echo $is_sat_class_subscribed || $is_sat_english_subscribed_package ? 'false' : 'true' ?>;
        var a4 = jQuery('#homeworkcourse').html();
        jQuery("#div_hw3").html('');
        jQuery('.main-article header').css('padding-top', '15px');
        var annoying = <?php echo $is_sat_class_subscribed || $is_sat_english_subscribed_package ? 'false' : 'true' ?>;
        var a5 = jQuery('#homeworktutoringplan').html();
        jQuery("#div_hw5").html('');
    </script>
    
<!--Message Center? -->    
<div style="padding-top: 35px" > 
<h3 style="margin: 0 0 10px 0;"><?php _e('Message Center', 'iii-dictionary'); ?></h3>
<?php if ($mid) { ?>
            <div class="boder-black">
                <div class="box-header box-header-custom">
                    <a href="<?php echo locale_home_url() ?>/?r=online-learning" class="link-close"><span class="span-icon-left-msg"></span></a>
                    <div class="col-xs-12" style="padding-left: 0px !important;padding-top: 10px">
                        <p class="font-header"><?php _e('Group Name:', 'iii-dictionary') ?> <span  style="color: #ffcc62;font-size: 18px;"><?php echo $group->name ?></span></p>
                    </div>
                </div>
                <div class="clearfix"></div>
                <ul class="nav nav-tabs" id="menu-msg-private">
                    <li id="li-private-msg-pr-1" class="active"><a data-toggle="tab" class="a-custom-color" href="#private-received"><?php _e('Received Message', 'iii-dictionary'); ?></a></li>
                    <li id="li-private-msg-pr-2"><a data-toggle="tab" class="a-custom-color" href="#private-received-new"><?php _e('Create a New Message', 'iii-dictionary'); ?></a></li>
                </ul>
                <div class="tab-content can-scroll">
                    <div id="private-received" class="tab-pane fade active in ">
                        <form method="post" action="<?php echo locale_home_url() ?>/?r=online-learning" id="main-form">
                            <div class="row" style="margin: 0 5% 0 5% !important;">

                                <div class="clearfix"></div>
                                <div class="col-sm-12">
                                    <p class="subject-type"><?php echo $subject ?></p>     
                                </div>

                                <div class="clearfix"></div>
                                <div style="color:#838383 " class="col-sm-12 user-sent">
                                    <label style="font-weight: bold"><?php echo $recipient ?><span style="font-weight: normal;font-size: 15px;"><?php echo ' - ' . ik_date_format($time, 'M d, Y H:i') ?></span></label>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label><?php _e('Message', 'iii-dictionary') ?></label>
    <?php if (!empty($cur_message->message)) : ?>
                                            <div class="box-gray-dialog"><?php echo $cur_message->message ?></div>
    <?php endif ?>
                                    </div>
                                </div>
                                <div class="col-sm-12">
    <?php
    $editor_settings = array(
        'wpautop' => false,
        'media_buttons' => false,
        'quicktags' => false,
        'textarea_rows' => 10,
        'tinymce' => array(
            'toolbar1' => 'bold,italic,strikethrough,image,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,spellchecker,fullscreen,wp_adv'
        )
    );
    ?>
                                    <div class="form-group borderccc">
    <?php wp_editor($message, 'message', $editor_settings); ?>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <button type="submit" name="submit-message" class="btn-custom"><?php echo __('Reply', 'iii-dictionary') ?></button>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="subject" value="<?php echo $subject ?>">
                            <input type="hidden" name="recipient-id" value="<?php echo $recipient_id ?>">
                        </form>
                    </div>
                    <!-- New message-->
                    <div id="private-received-new" class="tab-pane fade">
                        <div style="margin: 0 5% 0 5% !important;" id="new-msg">
                            <form method="post" action="<?php echo locale_home_url() ?>/?r=online-learning" id="main-form">
                                <div class="form-group new-msg-style col-xs-12">
                                    <label class="italic color4c4c4c"for="sender-email"><?php _e('My E-mail address:&nbsp;', 'iii-dictionary') ?></label><label class="color7d9b27 bold"><?php echo $current_user->user_email ?></label>
                                </div>
                                <div class="form-group new-msg-style col-xs-12">
                                    <label for="sender-password" class="label-custom"><?php _e('Your Password', 'iii-dictionary') ?></label>
                                    <input type="password" class="required width-100" style="font-family: Myriad_bold;" id="sender-password" name="sender-password" value="">
                                </div>
                                <div class="form-group new-msg-style col-xs-12">
                                    <label for="recipient" class="label-custom"><?php _e('Recipient Username', 'iii-dictionary') ?></label>
                                    <input type="text" class="required width-100" style="font-family: Myriad_bold;" id="recipient" name="recipient" value="">
                                </div>
                                <div class="form-group new-msg-style col-xs-12">
                                    <label for="subject" class="label-custom"><?php _e('Subject', 'iii-dictionary') ?></label>
                                    <input type="text" class="required width-100" style="font-family: Myriad_bold;" id="subject" name="subject" value="">
                                </div>
                                <div class="col-sm-12" style="margin-top: 3%">
    <?php
    $editor_settings = array(
        'wpautop' => false,
        'media_buttons' => false,
        'quicktags' => false,
        'textarea_rows' => 10,
        'tinymce' => array(
            'toolbar1' => 'bold,italic,strikethrough,image,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,spellchecker,fullscreen,wp_adv'
        )
    );
    ?>
                                    <div class="form-group borderccc">
    <?php wp_editor('', 'new-message', $editor_settings); ?>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <button type="submit" name="submit-new-message" class="btn-custom"><?php echo $mid ? __('Reply', 'iii-dictionary') : __('Send', 'iii-dictionary') ?></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
<?php }else { ?>

            <ul class="nav nav-tabs" id="menu-msg">
                
                    <li id="li-private-1"><a data-toggle="tab" class="a-custom-color" href="#menu-message-homemessage"><?php _e('Group Message', 'iii-dictionary'); ?></a></li>
                    
                        <li id="li-private-msg"><a data-toggle="tab" class="a-custom-color" href="#menu-message-first"><?php _e('Private Message', 'iii-dictionary'); ?></a></li>


                    <li id="li-private-in-msg"><a data-toggle="tab" id="click-received" class="a-custom-color" href="#menu-message-received" style="font-size: 13px;text-decoration: underline;"><?php _e('Received /', 'iii-dictionary'); ?></a></li>
                    <li id="li-private-out-msg"><a data-toggle="tab" class="a-custom-color" href="#menu-message-sent" style="font-size: 13px;text-decoration: underline;"><?php _e('Sent', 'iii-dictionary'); ?></a></li>
                    <li id="li-private-hidden1"><a data-toggle="tab" class="a-custom-color" href="#menu-message-received" ><?php _e('New Message: ', 'iii-dictionary'); ?><p id="text-new-msg"></p></a></li>
                    <li id="li-private-hidden2" class="active icon-tab-menu-msg"><a data-toggle="tab" class="icon-hidden" id="hide-msg" href="#menu-message-icon"></a></li>
                
            </ul>
            <div class="tab-content can-scroll">
                <div id="menu-message-homemessage" class="tab-pane fade ">
                    <div class="homeworkcritical-online can-scroll" style="height:500px;" >
            <?php MWHtml::load_group_message(); ?>
                    </div>
                </div>
                <div id="menu-message-first" class="tab-pane fade">
                </div>
                <div id="menu-message-received" class="tab-pane fade">
                    <div class="homeworkcritical-online can-scroll" style="height:500px;" >
                     <?php MWHtml::load_private_input_message(); ?>
                    </div>
                </div>
                <div id="menu-message-sent" class="tab-pane fade">
                    <div class="homeworkcritical-online can-scroll" style="height:500px;" >
                            <?php MWHtml::load_private_out_message(); ?>
                    </div>
                </div>
                <div id="menu-message-notice" class="tab-pane fade">
                </div>
                <div id="menu-message-icon" class="tab-pane fade in active">
                </div>
            </div>
<?php } ?>
    </div>
    <div class="padding-top-60px">
        <div class="col-xs-6 pad-left">
<?php if ($is_math_panel) { ?>
                <div class="col-xs-11 pad-left">
                    <h3 style="margin: 0 0 10px 0;"><?php _e('Math Practice with Tutor', 'iii-dictionary'); ?></h3>
                    <p style="margin: 0 0 10px 0;" class="color-8d8d8d"><?php _e('Request a tutor for improvement of your math.', 'iii-dictionary'); ?></p>
                    <div style="width:100%;border-bottom: 1px solid #c8c8c8;padding-top: 3.5%"></div>
                    <div style="padding-top: 6%"></div>
                    <p style="font-size: 20px;color: #aa882f;">BENEFITS from a Tutor</p>
                    <span class="span-custom"></span><p class="p-custom p-left8"><?php _e('Learn Comfortably from Your Home', 'iii-dictionary'); ?></p>
                    <span class="span-custom"></span><p class="p-custom p-left8"><?php _e('Immediate Access to Unlimited Library Resources', 'iii-dictionary'); ?></p>
                    <span class="span-custom"></span><p class="p-custom p-left8"><?php _e('Better Understanding with Real Time Whiteboard Method', 'iii-dictionary'); ?></p>
                    <a href="<?php echo locale_home_url(); ?>/?r=sat-preparation/emathk&client=math-emathk" class="btn-custom"><?php _e('Request a Tutoring Now', 'iii-dictionary'); ?></a>
                </div>
<?php } else { ?>
                <div class="col-xs-11 pad-left">
                    <h3 style="margin: 0 0 10px 0;"><?php _e('Start Essay Writing Practice', 'iii-dictionary'); ?></h3>
                    <p style="margin: 0 0 10px 0;" class="color-8d8d8d"><?php _e('Request a tutor to edit and improve your writting', 'iii-dictionary'); ?></p>
                    <div style="width:100%;border-bottom: 1px solid #c8c8c8;padding-top: 3.5%"></div>
                    <div style="padding-top: 6%"></div>
                    <p style="font-size: 20px;color: #aa882f;"><?php _e('BENEFITS from a Tutor', 'iii-dictionary'); ?></p>
                    <span class="span-custom"></span><p class="p-custom p-left8"><?php _e('Improve on Spelling and Grammar', 'iii-dictionary'); ?></p>
                    <span class="span-custom"></span><p class="p-custom p-left8"><?php _e('Developing a Thought Through Paragraph Writing', 'iii-dictionary'); ?></p>
                    <p style="font-size: 20px;color: #aa882f;"><?php _e('REQUESTING a TUTOR', 'iii-dictionary'); ?></p>
                    <span class="span-custom"></span><p class="p-custom p-left8"><?php _e('Select a subject from the list and click start to get started', 'iii-dictionary'); ?></p>
                    <span class="span-custom"></span><p class="p-custom p-left8"><?php _e('When you finished, tutor will check your writing', 'iii-dictionary'); ?></p>
                    <a href="<?php echo locale_home_url(); ?>/?r=sat-preparation/writing" class="btn-custom"><?php _e('Request a Tutoring Now', 'iii-dictionary'); ?></a>
                </div>
<?php } ?>
        </div>
        <div class="col-xs-6 pad-right">
            <div class="col-xs-12 pad-right">
                <form method="post" action="<?php echo locale_home_url() ?>/?r=online-learning">
                    <h3 style="margin: 0 0 10px 0;"><?php _e('Join the Groups(Classes)', 'iii-dictionary'); ?></h3>
                    <p style="margin: 0 0 10px 0;" class="color-8d8d8d"><?php _e('In order to Join the croups(classes) you must obtain the group name and the password from your teacher', 'iii-dictionary'); ?></p>
                    <div class="row" style="margin-bottom:10px">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="gname" class="p-custom"><?php _e('Group name', 'iii-dictionary') ?></label>
                                <input type="text" class="form-control" id="gname" name="gname" value="<?php echo $gname ?>">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="gpass" class="p-custom"><?php _e('Group password', 'iii-dictionary') ?></label>
                                <input type="password" class="form-control" id="gpass" name="gpass" value="">
                            </div>					
                        </div>
                        <div class="col-sm-12">
                            <div >
                                <button type="button" class="btn-custom" id="join-group"><?php _e('Join Now', 'iii-dictionary') ?></button>
                            </div>
                        </div>
                    </div>
                    <div id="join-group-dialog" class="modal fade modal-red-brown" aria-hidden="true">
                        <div class="modal-dialog modal-join-group">
                            <div class="modal-content">
                                <div class="modal-header custom-header">
                                    <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                                    <h3><?php _e('Joining the group', 'iii-dictionary') ?></h3>
                                </div>
                                <div class="modal-body body-custom">
                                    <h4 class="text-join-group" ><?php _e('Are you sure about joining to this group?', 'iii-dictionary') ?></h4>
                                    <p class="text-p-join-group"><?php _e('Are you sure you are joining to this group? If this group name is provided by your teacher, your name will show up in his/her class member list and it will cost the teacher for class membership. If this is a private group, it is free.', 'iii-dictionary') ?></p>
                                    <hr>
                                    <h4 class="text-join-group"><?php _e('Do you want to join group chat (chat board for this group)?', 'iii-dictionary') ?></h4>
                                    <div class="radio">															
                                        <input id="rdo-yes" type="checkbox" class="gCheckbox" name="joinchat" value="1" checked>
                                        <label for="rdo-yes" class="lab-checkbox"><?php _e('Yes', 'iii-dictionary') ?></label>
                                    </div>
                                    <div class="radio">
                                        <input id="rdo-no" type="checkbox" class="gCheckbox" name="joinchat" value="0">  
                                        <label for="rdo-no" class="lab-checkbox"><?php _e('No', 'iii-dictionary') ?></label>
                                    </div>
                                    <hr>
                                    <h4 class="text-join-group" id="text-join-group"></h4>
                                </div>
                                <div class="modal-footer footer-custom">			
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <button type="submit" name="join" class="btn-custom confirm"><?php _e('Yes, Join', 'iii-dictionary') ?></button>
                                        </div>
                                        <div class="col-sm-6">
                                            <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1 secondary cancel-450"><?php _e('Cancel', 'iii-dictionary') ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>  
            </div>
        </div>
    </div>
</div>


<div id="switch-mode-dialog" class="modal fade" aria-hidden="true">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 3% !important;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Message', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-body body-custom"></div>
            <div class="modal-footer footer-custom">
                <div class="row">
                        <div class="col-sm-6 width-100-js">
                            <div class="form-group">
                                 <a href="#" id="btn-practice" class="btn btn-block btn-custom">
                                <span class="icon-accept" style="display: none;"></span><?php _e('OK', 'iii-dictionary') ?></a>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1 secondary"><?php _e('Cancel', 'iii-dictionary') ?></a>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>

<div id="switch-mode-dialog-writing" class="modal fade" aria-hidden="true">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 3% !important;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Message', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-body body-custom">
                <span>This writing assignment is currently being graded. You can only restart once grading is complete.</span>
            </div>
            <div class="modal-footer footer-custom">
                <div class="row">
                        <div class="col-sm-6 width-100">
                            <div class="form-group">
                                 <a href="#" id="btn-practice-writing" class="btn btn-block btn-custom">
                                <span class="icon-accept-writing" style="display: none;"></span><?php _e('OK', 'iii-dictionary') ?></a>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>

<div id="require-modal" class="modal fade modal-purple" aria-hidden="true">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 4%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3 style="color:#fff;"><?php _e('Subscription Required', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-body body-custom"></div>
            <div class="modal-footer footer-custom">
                <button id="sub-modal" data-sat-class="" data-subscription-type="" data-type="" class="btn-custom choose-sub-btn" target="_blank" data-toggle="modal"></button>
            </div>
        </div>
    </div>
</div>
<div id="sub-modal-math-sat" class="modal fade modal-purple" aria-hidden="true">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 4%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3 style="color:#fff;"><?php _e('Subscription Required', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-body body-custom">
                <span>Your subscription has expired. Please subscribe to SAT I Preparation to start.</span>
            </div>
            <div class="modal-footer footer-custom">
                <button id="ok-modal-sub-sat" class="btn-custom choose-sub-btn" target="_blank" data-toggle="modal"><?php _e('OK', 'iii-dictionary') ?></button>
            </div>
        </div>
    </div>
</div>
<div id="sub-modal-math-sat-new" class="modal fade modal-purple" aria-hidden="true">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 4%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3 style="color:#fff;"><?php _e('Subscription Required', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-body body-custom">
            </div>
            <div class="modal-footer footer-custom">
                <button id="ok-modal-sub-sat-new" class="btn-custom choose-sub-btn" target="_blank" data-toggle="modal"><?php _e('OK', 'iii-dictionary') ?></button>
            </div>
        </div>
    </div>
</div>
<!-- Dialog show EVALUATION OK -->
<div id="evaluation_ok" class="modal fade modal-purple" aria-hidden="true">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content modal-content-custom">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3 style="color:#fff;"><?php _e('TUTOR EVALUATION', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-body body-custom">          
                <label style="color: #000; "><b>Thank you for your Evaluation!</b></label>
            </div>
        </div>
    </div>
</div>

<!-- Dialog show EVALUATION OK -->
<div id="evaluation_error" class="modal fade modal-purple" aria-hidden="true">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content modal-content-custom">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3 style="color:#fff;"><?php _e('TUTOR EVALUATION', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-body body-custom"> 
                <label><b>Error:</b>You must enter evaluation .</label>
            </div>
        </div>
    </div>
</div>
<!-- Dialog show member list -->
<div id="members-list-dialog" class="modal fade " aria-hidden="true">
    <div class="modal-dialog modal-custom-first" id="modal-member" style="background-color: #DFDFDF">        
        <div class="boder-black" style="margin-top: 120px;">
            <div class="modal-header custom-header" >
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3 style="padding-left: 0%"><?php _e('Members List', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-body " style="padding: 0px;min-height:315px;margin-top: 0px;font-size: 15px;overflow-y: scroll" >
                <table id="members-list-modal" class="table table-striped table-condensed ik-table1 ">
                    <thead>
                        <tr style="background: #aaaaaa;">
                            <th style="padding-left: 5%" class="text-color-custom-1"><?php _e('Account Name', 'iii-dictionary') ?></th>
                            <th class="text-color-custom-1"><?php _e('Joined Date', 'iii-dictionary') ?></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="tutor-evaluation" class="modal fade modal-red-brown">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('TUTOR EVALUATION', 'iii-dictionary') ?></h3>
            </div>
            <form method="post">
                <input type="hidden" name="sub-type" value="4">
                <input type="hidden" name="idchat" value="" id='idchat'>
                <div class="modal-body body-custom">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="font-dialog1" style="font-weight: bold"><?php _e("Please write your evaluation on the tutor and tutoring session in order for us to make improvements.", 'iii-dictionary') ?></label>
                                <textarea id="txt_evaluation" style="margin-top: 20px; width: 100%; border: 1px solid;" name='txt_evaluation' rows="5" cols="80"></textarea>                          
                            </div>
                        </div>
                       
                    </div>				
                </div>
                <div class="modal-footer footer-custom">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button type="button" name="add_tutor_evaluation"  class="btn-custom confirm add_tutor_evaluation" id='add_tutor_evaluation'><?php _e('Send', 'iii-dictionary') ?></button>
                             </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1 secondary"><?php _e('Cancel', 'iii-dictionary') ?></a>
                            </div>
                        </div>
                    </div>                    
                </div>			
            </form>
        </div>
    </div>
</div>

<div id="write-message" class="modal fade " aria-hidden="true">
    <div class="modal-dialog" id="modal-write-msg">
        <div class="modal-content boder-black"  style="padding: 0px">
            <div class="modal-header-write-msg" >
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3 class="h3-write-msg"><?php _e('Write a Post', 'iii-dictionary') ?></h3>
                <p class="p-write-msg" id="group-name">Group Name: </p>
                <p class="col-sm-4 p1-write-msg">List of the Members</p>
                <p class="col-sm-8 p2-write-msg">All Posts</p>
            </div>
            <div class="modal-body modal-body-write-msg"  >
                <form method="post" action="<?php echo locale_home_url() ?>/?r=online-learning" id="msg-form" >
                    <div class="modal-body-message"></div>
                    <div class="post-form">
                        <div class="form-group">
                            <label for="group-details"><?php _e('Write message', 'iii-dictionary') ?></label>
<?php
$editor_settings = array(
    'wpautop' => false,
    'media_buttons' => false,
    'quicktags' => false,
    'textarea_rows' => 10,
    'tinymce' => array(
        'toolbar1' => 'bold,italic,strikethrough,image,file,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,spellchecker,fullscreen'
    )
);

wp_editor('', 'message', $editor_settings);
?>
                        </div>
                        <div class="form-group">
                            <button type="button" name="reply" class="btn-custom post-reply"><?php _e('Post', 'iii-dictionary') ?></button>
                        </div>
                    </div>
                    <input class="hidden" id="group_id_post" name="group_id_post" value=""/>
                </form>
            </div>
        </div>
    </div>
</div>

<!--modal receive private message-->
<div id="received_msg" class="modal fade " aria-hidden="true">
    <div class="modal-dialog" style="background-color: white">
        <div class="boder-black" style="margin-top: 120px;">
            <div class="box-header box-header-custom">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <div class="col-xs-12" style="padding-left: 0px !important;padding-top: 10px; background: #838383">
                    <p class="font-header"><?php _e('Group Name:', 'iii-dictionary') ?> <span  style="color: #ffcc62;font-size: 18px;"><?php echo $group->name ?></span></p>
                </div>
            </div>
            <ul class="nav nav-tabs" id="menu-msg-private">
                <li id="li-private-sent" class="active"><a data-toggle="tab" class="a-custom-color " href="#private-receive"><?php _e('Received Message', 'iii-dictionary'); ?></a></li>
                <li id="li-private-new"><a data-toggle="tab" class="a-custom-color" href="#private-receive-new"><?php _e('Create a New Message', 'iii-dictionary'); ?></a></li>
            </ul>
            <div class="tab-content can-scroll">
                <div id="private-receive" class="tab-pane fade active in" style="height: 560px;">
                    <div class="box-white-dialog" id="box-msg" style="padding: 0px 20px 0px 20px;"></div>
                </div>
                <!-- New message-->
                <div id="private-receive-new" class="tab-pane fade " style="overflow: hidden; position: relative; width: 100%; height: 560px;">
                    <div style="margin: 0 5% 0 5% !important;" id="new-msg">
                        <form method="post" action="<?php echo locale_home_url() ?>/?r=online-learning" id="main-form">
                            <div class="form-group new-msg-style col-xs-12">
                                <label class="italic color4c4c4c"for="sender-email"><?php _e('My E-mail address:&nbsp;', 'iii-dictionary') ?></label><label class="color7d9b27 bold"><?php echo $current_user->user_email ?></label>
                            </div>
                            <div class="form-group new-msg-style col-xs-12">
                                <label for="sender-password" class="label-custom"><?php _e('Your Password', 'iii-dictionary') ?></label>
                                <input type="password" class="required width-100" style="font-family: Myriad_bold;" id="sender-password" name="sender-password" value="">
                            </div>
                            <div class="form-group new-msg-style col-xs-12">
                                <label for="recipient" class="label-custom"><?php _e('Recipient Username', 'iii-dictionary') ?></label>
                                <input type="text" class="required width-100" style="font-family: Myriad_bold;" id="recipient" name="recipient" value="">
                            </div>
                            <div class="form-group new-msg-style col-xs-12">
                                <label for="subject" class="label-custom"><?php _e('Subject', 'iii-dictionary') ?></label>
                                <input type="text" class="required width-100" style="font-family: Myriad_bold;" id="subject" name="subject" value="">
                            </div>
                            <div class="col-sm-12" style="margin-top: 3%">
<?php
$editor_settings = array(
    'wpautop' => false,
    'media_buttons' => false,
    'quicktags' => false,
    'textarea_rows' => 10,
    'tinymce' => array(
        'toolbar1' => 'bold,italic,strikethrough,image,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,spellchecker,fullscreen,wp_adv'
    )
);
?>
 <div class="form-group borderccc">
<?php wp_editor('', 'new-message', $editor_settings); ?>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <button type="submit" name="submit-reply-message" class="btn-custom"><?php _e('Reply', 'iii-dictionary') ?></button>
                                </div>
                            </div>
                            <input type="hidden" name="subject" value="<?php echo $subject ?>">
                            <input type="hidden" name="sender_id" value="<?php echo $sender_id ?>">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div> 
</div>

<!--modal send private message-->
<div id="read-msg" class="modal fade " aria-hidden="true">
    <div class="modal-dialog" style="background-color: white">
        <div class="boder-black" style="margin-top: 120px;">
            <div class="box-header box-header-custom">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <div class="col-xs-12" style="padding-left: 0px !important;padding-top: 10px; background: #838383">
                    <p class="font-header"><?php _e('Group Name:', 'iii-dictionary') ?> <span  style="color: #ffcc62;font-size: 18px;"><?php echo $group->name ?></span></p>
                </div>
            </div>
            <ul class="nav nav-tabs" id="menu-msg-private">
                <li id="li-private-sent" class="active"><a data-toggle="tab" class="a-custom-color " href="#private-sent"><?php _e('Sent Message', 'iii-dictionary'); ?></a></li>                
                <li id="li-private-new"><a data-toggle="tab" class="a-custom-color" href="#private-send-new"><?php _e('Create a New Message', 'iii-dictionary'); ?></a></li>
            </ul>
            <div class="tab-content can-scroll">
                <div id="private-sent" class="tab-pane fade active in" style="height: 580px;">
                    <div class="box-white-dialog" id="box_msg_sent" style="padding: 0px 20px 0px 20px;"></div>
                </div>
                <!-- New message-->
                <div id="private-send-new" class="tab-pane fade" style="height: 580px;">
                    <div style="margin: 0 5% 0 5% !important;" id="new-msg">
                        <form method="post" action="<?php echo locale_home_url() ?>/?r=online-learning" id="main-form">
                            <div class="form-group new-msg-style col-xs-12">
                                <label class="italic color4c4c4c"for="sender-email"><?php _e('My E-mail address:&nbsp;', 'iii-dictionary') ?></label><label class="color7d9b27 bold"><?php echo $current_user->user_email ?></label>
                            </div>
                            <div class="form-group new-msg-style col-xs-12">
                                <label for="sender-password" class="label-custom"><?php _e('Your Password', 'iii-dictionary') ?></label>
                                <input type="password" class="required width-100" style="font-family: Myriad_bold;" id="sender-password" name="sender-password" value="">
                            </div>
                            <div class="form-group new-msg-style col-xs-12">
                                <label for="recipient" class="label-custom"><?php _e('Recipient Username', 'iii-dictionary') ?></label>
                                <input type="text" class="required width-100" style="font-family: Myriad_bold;" id="recipient" name="recipient" value="">
                            </div>
                            <div class="form-group new-msg-style col-xs-12">
                                <label for="subject" class="label-custom"><?php _e('Subject', 'iii-dictionary') ?></label>
                                <input type="text" class="required width-100" style="font-family: Myriad_bold;" id="subject" name="subject" value="">
                            </div>
                            <div class="col-sm-12" style="margin-top: 3%">
<?php
$editor_settings = array(
    'wpautop' => false,
    'media_buttons' => false,
    'quicktags' => false,
    'textarea_rows' => 10,
    'tinymce' => array(
        'toolbar1' => 'bold,italic,strikethrough,image,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,spellchecker,fullscreen,wp_adv'
    )
);
?>
 <div class="form-group borderccc">
<?php wp_editor('', 'new-message1', $editor_settings); ?>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <button type="submit" name="submit-send-message" class="btn-custom"><?php _e('Send', 'iii-dictionary') ?></button>
                                </div>
                            </div>
                            <input type="hidden" name="subject" value="<?php echo $subject ?>">
                            <input type="hidden" name="recipient-id" value="<?php echo $recipient_id ?>">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div> 
</div>

<div id="snackbar"><?php _e('Post successful', 'iii-dictionary') ?></div>

<!-- Modal show homework group -->
<div id="modal-homework" class="modal fade" aria-hidden="true">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header" >
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3 style="padding-left: 0%"><?php _e('Homework Result', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-body body-custom-2">
                <table class="table table-custom-color table-custom-3">
                    <tr class="row">
                        <td class="col-xs-3"><div ><?php _e('Level:', 'iii-dictionary') ?></div></td>
                        <td class="col-xs-9"><div  id="level-hw"></div></td>
                    </tr>
                    <tr class="row">
                        <td class="col-xs-3"><div ><?php _e('Score:', 'iii-dictionary') ?></div></td>
                        <td class="col-xs-9"><div id="score-hw"></div></td>
                    </tr>
                    <tr class="row">
                        <td class="col-xs-3"><div ><?php _e('Last Attempt:', 'iii-dictionary') ?></div></td>
                        <td class="col-xs-9"><div id="la-hw"></div></td>
                    </tr>
                    <tr class="row">
                        <td class="col-xs-3"><div><?php _e('Completed Date:', 'iii-dictionary') ?></div></td>
                        <td class="col-xs-9"><div  id="cd-hw"></div></td>
                    </tr>
                    <tr class="row">
                        <td class="col-xs-3"><div ><?php _e('Lesson: ', 'iii-dictionary') ?></div></td>
                        <td class="col-xs-9"><div  id="l-hw"></div></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Modal show result homework math-->
<div id="modal-view-result-homework" class="modal fade modal-red-brown">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header" >
                <span style="right: 3%;margin-top: -3px !important;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <span ><h3 class="txt-wr"><?php _e('Worksheet - Score Result', 'iii-dictionary') ?></h3></span>
            </div>
            <div id="can-scroll" style="background: #fff;color: #000;padding-top: 3%">
            </div>
            <div class="modal-footer footer-custom" style="padding-left: 5%;padding-top: 0px;">
                <span class="text-food-view-rs">Restart the Worksheet. This will overwrite the previous score.</span>
                <input type="button" id="btn-ok-rs-homework" class="css-ok-rs" value="Restart the Worksheet">
                <input type="button" id="close-wd-homework" class="css-close-wd" value="Close The Window">
            </div>
            <div class="hidden" id="load-prevent-modal"></div>
        </div>
    </div>
</div>
<!-- Modal show result homework english-->
<div id="modal-view-result-writing" class="modal fade modal-red-brown">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header" >
                <span style="margin-top: -1%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <span ><h3 class="txt-wr"><?php _e('Writing Assignment - Results', 'iii-dictionary') ?></h3></span>
            </div>
            <div id="can-scroll" style="background: #fff;color: #000;padding-top: 3%">
            </div>
            <div class="modal-footer footer-custom" style="padding-left: 5%;padding-top: 0px;">
                <input type="button" style="width: 100% !important;margin-top: 10%" id="btn-ok-rs-homework-english" class="css-ok-rs" value="OK">
                <div style="margin-top: 20%;" class="line-result"></div>
                <span class="text-food-view-rs-eng">Would you like to evaluate your teacher? </span><span class="text-food-view-rs-eng1 show-evaluation-english"> Please Click Here</span>
            </div>
        </div>
    </div>
</div>
<div id="modal-enter-evaluation-english" class="modal fade modal-red-brown">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <span ><h3 class="txt-wr" style="color: #fff !important;"><?php _e('TEACHER EVALUATION', 'iii-dictionary') ?></h3></span>
            </div>
            <div style="background: #fff;color: #000;padding-top: 3%;padding-left: 5%;padding-right: 5%">
                <span class="txt-write-evaluation">Please Write Evaluation</span>
                <textarea rows="4" cols="50" class="css-area-evaluation txt-evaluation-sub"></textarea>
            </div>
            <div class="modal-footer footer-custom" style="padding-left: 5%">
                <input type="button" class="css-ok-rs add-evaluation-english" value="Save">
                <input type="button" class="css-close-wd" value="Cancel">
            </div>
        </div>
    </div>
</div>
<!-- sat-subscription-dialog -->
<div id="sat-subscription-dialog" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Purchase SAT Subscriptions', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo home_url_ssl() ?>/?r=payments">
                <input type="hidden" name="sub-type" id="sat-sub-type" value="0">
                <input type="hidden" name="sat-class" id="sat-class" value="">
                <input type="hidden" name="sub-id" value="0">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="font-dialog col-xs-12" style="border-bottom: 1px solid #d6d6d6;"><?php _e('Selected Class', 'iii-dictionary') ?></label>
                                <p class="selected-class col-xs-12" id="selected-class" ></p>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12" style="padding-top: 3%">
                            <div class="form-group">
                                <label class="font-dialog"><?php _e('Number of Months', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control" name="sat-months" id="sel-sat-months">
<?php for ($i = 1; $i <= 24; $i++) : ?>
                                        <option value="<?php echo $i ?>"><?php printf(_n('%s month', '%s months', $i, 'iii-dictionary'), $i) ?></option>
                        <?php endfor ?>
                                </select>
                            </div>
                        </div>
                        <?php
                        if (!is_math_panel()) :
                            $select_class_options = array(CLASS_SAT1 => __('SAT Test 1', 'iii-dictionary'), CLASS_SAT2 => __('SAT Test 2', 'iii-dictionary'), CLASS_SAT3 => __('SAT Test 3', 'iii-dictionary'),
                                CLASS_SAT4 => __('SAT Test 4', 'iii-dictionary'), CLASS_SAT5 => __('SAT Test 5', 'iii-dictionary'))
                            ?>

                            <div class="col-sm-12" id="sat-test-block" style="display: none">
                                <div class="form-group">
                                    <label class="font-dialog"><?php _e('Practice Test', 'iii-dictionary') ?></label>
                                    <select class="select-box-it form-control sel-sat-class">
                            <?php foreach ($select_class_options as $key => $value) : ?>
                                            <option value="<?php echo $key ?>"><?php echo $value ?></option>
                            <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
    <?php
else :
    $select1_class_options = array(CLASS_MATH_SAT1A => __('SAT 1A', 'iii-dictionary'), CLASS_MATH_SAT1B => __('SAT 1B', 'iii-dictionary'),
        CLASS_MATH_SAT1C => __('SAT 1C', 'iii-dictionary'), CLASS_MATH_SAT1D => __('SAT 1D', 'iii-dictionary'), CLASS_MATH_SAT1E => __('SAT 1E', 'iii-dictionary'));
    $select2_class_options = array(CLASS_MATH_SAT2A => __('SAT 2A', 'iii-dictionary'), CLASS_MATH_SAT2B => __('SAT 2B', 'iii-dictionary'),
        CLASS_MATH_SAT2C => __('SAT 2C', 'iii-dictionary'), CLASS_MATH_SAT2D => __('SAT 2D', 'iii-dictionary'), CLASS_MATH_SAT2E => __('SAT 2E', 'iii-dictionary'));
    $select3_class_options = array(CLASS_MATH_IK => __('Math Kindergarten', 'iii-dictionary'),
        CLASS_MATH_IK1 => __('Math Grade 1', 'iii-dictionary'),
        CLASS_MATH_IK2 => __('Math Grade 2', 'iii-dictionary'),
        CLASS_MATH_IK3 => __('Math Grade 3', 'iii-dictionary'),
        CLASS_MATH_IK4 => __('Math Grade 4', 'iii-dictionary'),
        CLASS_MATH_IK5 => __('Math Grade 5', 'iii-dictionary'),
        CLASS_MATH_IK6 => __('Math Grade 6', 'iii-dictionary'),
        CLASS_MATH_IK7 => __('Math Grade 7', 'iii-dictionary'),
        CLASS_MATH_IK8 => __('Math Grade 8', 'iii-dictionary'),
        CLASS_MATH_IK9 => __('Math Grade 9', 'iii-dictionary'),
        CLASS_MATH_IK10 => __('Math Grade 10', 'iii-dictionary'),
        CLASS_MATH_IK11 => __('Math Grade 11', 'iii-dictionary'),
        CLASS_MATH_IK12 => __('Math Grade 12', 'iii-dictionary'))
    ?>

                            <div class="col-xs-12" id="sat-test-i-block" style="display: none">
                                <div class="form-group">
                                    <label class="font-dialog"><?php _e('Simulated Test', 'iii-dictionary') ?></label>
                                    <select class="select-box-it form-control sel-sat-class">
                                        <?php foreach ($select1_class_options as $key => $value) : ?>
                                            <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12" id="sat-test-ii-block" style="display: none">
                                <div class="form-group">
                                    <label class="font-dialog"><?php _e('Simulated Test', 'iii-dictionary') ?></label>
                                    <select class="select-box-it form-control sel-sat-class">
                                    <?php foreach ($select2_class_options as $key => $value) : ?>
                                            <option value="<?php echo $key ?>"><?php echo $value ?></option>
    <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12" id="ik-test-class-block" style="display: none">
                                <div class="form-group">
                                    <label class="font-dialog"><?php _e('Simulated Test', 'iii-dictionary') ?></label>
                                    <select class="select-box-it form-control sel-sat-class" id="sel-sat-class">
    <?php foreach ($select3_class_options as $key => $value) : ?>
                                            <option value="<?php echo $key ?>"><?php echo $value ?></option>
    <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
<?php endif ?>

                        <div class="col-sm-12 padding-top-2">
                            <div class="box-gray-dialog" style="text-align: right">
<?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color708b23">$<span id="total-amount-sat" class="color708b23">0</span></span>
                            </div>
                        </div>
                    </div>				
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button type="submit" name="add-to-cart" class="btn-custom confirm"><?php _e('Check out', 'iii-dictionary') ?></button>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1 secondary"><?php _e('Cancel', 'iii-dictionary') ?></a>
                            </div>
                        </div>
                    </div>
                </div>			
            </form>
        </div>
    </div>
</div>

<!--------------------------Quản lý phần hiển thị modal để subscrible các class-------------------->

<div id="teacher-sub-details-dialog" class="modal fade modal-red-brown">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
			<h3><?php _e('Teacher\'s Homework Tool Subscription', 'iii-dictionary') ?></h3>
        </div>
        <div class="modal-body">
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<label><?php _e('Select existing group (Class Name)', 'iii-dictionary') ?></label>
						<select class="select-box-it" id="sel-group-teacher">
                                                    <option value=""><?php _e('Select one', 'iii-dictionary') ?></option>
                                                    <?php foreach($user_groups as $group) : if(is_null($group->expired_date)) : ?>
                                                        <option value="<?php echo $group->id ?>" data-size="<?php echo $group->size ?>"><?php echo $group->name ?></option>
                                                    <?php endif; endforeach ?>
						</select>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="col-sm-6">
                                    <div class="form-group">
                                        <label><?php _e('Or Create New Group', 'iii-dictionary') ?></label>
                                        <input type="text" class="form-control" id="teacher-gname" placeholder="<?php _e('Group name', 'iii-dictionary') ?>">
                                    </div>
				</div>
				<div class="col-sm-6">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <input type="text" class="form-control" id="teacher-gpass" placeholder="<?php _e('Group password', 'iii-dictionary') ?>">
                                    </div>
				</div>
				<div class="col-sm-12">
					<ol>
						<li><?php _e('One dictionary for all members in your group is included in the subscription.', 'iii-dictionary') ?></li>
						<li><?php printf(__('The price for Teacher\'s Homework Tool is %s per student per month.', 'iii-dictionary'), '<strong>' . $teacher_tool_price / 100 . '</strong>') ?></li>
					</ol>
				</div>
                                <div>
                                        <li><?php _e('Do you pay for this group license fee, or you collect group fee from your students?', 'iii-dictionary')?></li>
                                </div>
                                <div>
                                    <li style="float:left"><?php _e('Pay by myself', 'iii-dictionary')?></li>
                                    <input style="margin-left: 100px;" id="paymyseft" class="checkboxagree" type="checkbox" name="paymyseft" value="paymyseft" >
                                </div>
                                <div>
                                    <li style="float:left"><?php _e('collect from students', 'iii-dictionary')?></li>
                                    <input style="margin-left: 45px;" id="paystudent" class="checkboxagree" type="checkbox" name="paystudent" value="paystudent" >
                                </div>
                                <div style="text-align: center">
                                    <li><?php _e('How much from each student?', 'iii-dictionary')?></li>
                                    <input id="payeachstudent" type="text" name="payeachstudent" value="" >
                                </div>
			</div>
        </div>
        <div class="modal-footer">
			<div class="row">
				<div class="col-sm-4 pull-right">
					<button type="button" id="sub-continue" class="btn btn-default btn-block orange" data-loading-text="<?php _e('Checking...', 'iii-dictionary') ?>"><span class="icon-check"></span><?php _e('Continue', 'iii-dictionary') ?></button>
				</div>
			</div>
        </div>
      </div>
    </div>
</div>

<div id="additional-subscription-dialog" class="modal fade modal-red-brown">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
             <h3 id="addi-popup-title" data-ts-text="<?php _e('Teacher\'s Homework Tool Subscription', 'iii-dictionary') ?>" data-ds-text="<?php _e('Dictionary Subscription', 'iii-dictionary') ?>" data-ext-text="<?php _e('Purchase Additional Subscriptions', 'iii-dictionary') ?>"><?php _e('Purchase Additional Subscriptions', 'iii-dictionary') ?></h3>
        </div>
		<form method="post" action="<?php echo home_url_ssl() ?>/?r=payments">
			<input type="hidden" id="addi-sub-type" name="sub-type" value="">
			<input type="hidden" id="addi-gid" name="assoc-group" value="">
			<input type="hidden" id="addi-gname" name="group-name" value="">
			<input type="hidden" id="addi-gpass" name="group-pass" value="">
			<input type="hidden" id="sub-id" name="sub-id" value="0">
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-6" id="selected-group-label">
						<div class="form-group">
							<label><?php _e('Selected Group', 'iii-dictionary') ?></label>
							<p class="box" id="addi-selected-group" style="padding: 5px 15px"></p>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group<?php echo $is_math_panel ? ' hidden' : '' ?>">
							<label><?php _e('Select the type of dictionary', 'iii-dictionary') ?></label>
							<?php MWHtml::select_dictionaries('', false, 'dictionary', 'sel-dictionary', 'form-control', true) ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label id="num-of-student-lbl"><?php _e('Number of Students', 'iii-dictionary') ?></label>
							<?php $min_no_of_student = mw_get_option('min-students-subscription') ?>
							<input type="number" name="no-students" id="student_num" class="form-control" data-min="<?php echo $min_no_of_student ?>" value="<?php echo $min_no_of_student ?>">
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label id="num-of-months-lbl"><?php _e('Number of Months', 'iii-dictionary') ?></label>
							<select class="select-box-it form-control" name="teacher-tool-months" id="sel-teacher-tool">
								<?php for($i = 3; $i <= 24; $i++) : ?>
									<option value="<?php echo $i ?>"><?php printf(__('%s months', 'iii-dictionary'), $i) ?></option>
								<?php endfor ?>
							</select>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<div class="box" style="text-align: right">
							<?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency">$</span> <span id="total-amount">0</span>
						</div>
					</div>
				</div>				
			</div>
			<div class="modal-footer">
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<button type="submit" id="add-to-cart" name="add-to-cart" class="btn btn-block orange confirm"><span class="icon-cart"></span><?php _e('Check out', 'iii-dictionary') ?></button>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<a href="#" data-dismiss="modal" aria-hidden="true" class="btn btn-block grey secondary"><span class="icon-cancel"></span><?php _e('Cancel', 'iii-dictionary') ?></a>
						</div>
					</div>
				</div>
			</div>			
		</form>
      </div>
    </div>
</div>

<div id="purchase-points-dialog" class="modal fade modal-red-brown">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
             <h3><?php _e('Purchase Points', 'iii-dictionary') ?></h3>
        </div>
		<form method="post" action="<?php echo home_url_ssl() ?>/?r=payments">
			<input type="hidden" name="sub-type" value="4">
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label><?php _e('Number of Points', 'iii-dictionary') ?></label>
							<input type="number" class="form-control" name="no-of-points" id="no-of-points" min="1">
						</div>
					</div>
					<div class="col-sm-12">
						<div class="box" style="text-align: right">
							<?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency">$ <span id="total-amount-points">0</span></span>
						</div>
					</div>
				</div>				
			</div>
			<div class="modal-footer">
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<button type="submit" name="add-to-cart" class="btn btn-block orange confirm"><span class="icon-cart"></span><?php _e('Check out', 'iii-dictionary') ?></button>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<a href="#" data-dismiss="modal" aria-hidden="true" class="btn btn-block grey secondary"><span class="icon-cancel"></span><?php _e('Cancel', 'iii-dictionary') ?></a>
						</div>
					</div>
				</div>
			</div>			
		</form>
      </div>
    </div>
</div>
<div id="self-study-subscription-dialog" class="modal fade modal-red-brown">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
             <h3><?php _e('Student\'s Self-study Subscription', 'iii-dictionary') ?></h3>
        </div>
		<form method="post" action="<?php echo home_url_ssl() ?>/?r=payments">
			<input type="hidden" name="sub-type" value="<?php echo !$is_math_panel ? SUB_SELF_STUDY : SUB_SELF_STUDY_MATH ?>" id="self-study-sub">
			<?php $self_study_group = generate_self_study_group_name() ?>
			<input type="hidden" name="group-name" value="<?php echo $self_study_group ?>">
			<input type="hidden" name="group-pass" value="<?php echo $self_study_group ?>">
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-6 form-group">
						<label><?php _e('Default Group for this subscription', 'iii-dictionary') ?></label>
						<p class="box" style="padding: 5px 15px"><?php echo $self_study_group ?></p>
					</div>
					<div class="col-sm-6 form-group" id="ss-dict-block">
						<label><?php _e('Select the type of dictionary', 'iii-dictionary') ?></label>
						<?php MWHtml::select_dictionaries('', false, 'dictionary', 'sel-dictionary2', 'form-control', true) ?>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label><?php _e('Number of Students', 'iii-dictionary') ?></label>
							<input type="number" name="no-students" class="form-control" min="1" max="1" value="1" readonly>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label><?php _e('Number of Months', 'iii-dictionary') ?></label>
							<select class="select-box-it form-control" name="self-study-months" id="sel-self-study-months">
								<?php for($i = 1; $i <= 24; $i++) : ?>
									<option value="<?php echo $i ?>"><?php printf(_n('%s month', '%s months', $i, 'iii-dictionary'), $i) ?></option>
								<?php endfor ?>
							</select>
						</div>
					</div>
					<div class="col-sm-12">
						<div class="box" style="text-align: right">
							<?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency">$</span> <span class="currency" id="ss-total-amount">0</span>
						</div>
					</div>
				</div>				
			</div>
			<div class="modal-footer">
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<button type="submit" id="add-to-cart-ss" name="add-to-cart" class="btn btn-block orange"><span class="icon-cart"></span><?php _e('Check out', 'iii-dictionary') ?></button>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<a href="#" data-dismiss="modal" aria-hidden="true" class="btn btn-block grey"><span class="icon-cancel"></span><?php _e('Cancel', 'iii-dictionary') ?></a>
						</div>
					</div>
				</div>
			</div>			
		</form>
      </div>
    </div>
</div>
<div id="sat1-preparation-subscrible-modal" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Purchase SAT Subscriptions', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo home_url_ssl() ?>/?r=payments">
                <input type="hidden" name="sub-type" id="sat1-preparation-sub-type" value="7">
                <input type="hidden" name="sat-class" id="sat1-preparation-class" value="9">
                <input type="hidden" name="sub-id" value="0">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="font-dialog col-xs-12" style="border-bottom: 1px solid #d6d6d6;"><?php _e('Selected Class', 'iii-dictionary') ?></label>
                                <p class="selected-class col-xs-12" id="selected-class1" >SAT I Preparation - Preparation</p>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12" style="padding-top: 3%">
                            <div class="form-group">                                
                                <label class="font-dialog"><?php _e('Number of Months', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control" name="sat-months" id="select-sat1-preparation-months" >
                                    <?php for ($i = 1; $i <= 24; $i++) : ?>
                                        <option value="<?php echo $i ?>"><?php printf(_n('%s month', '%s months', $i, 'iii-dictionary'), $i) ?></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                        </div>   
                        <div class="col-sm-12" id="sat-test-block">
                            <div class="form-group">
                                <label class="font-dialog"><?php _e('Practice Test', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control sel-sat-class" id="select-sat1-preparation-class" disabled="disabled">
                                        <option value="">Sat 1 - PREPARATION</option>
                                </select>
                            </div>
                        </div>  
                        <div class="col-sm-12 padding-top-2">
                            <div class="box-gray-dialog" style="text-align: right">
                                <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color708b23">$<span id="total-amount-sat1-preparation" class="color708b23">1</span></span>
                            </div>
                        </div>
                    </div>				
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button type="submit" name="add-to-cart" class="btn-custom confirm"><?php _e('Check out', 'iii-dictionary') ?></button>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1 secondary"><?php _e('Cancel', 'iii-dictionary') ?></a>
                            </div>
                        </div>
                    </div>
                </div>			
            </form>
        </div>
    </div>
</div>
<div id="sat2-preparation-subscrible-modal" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Purchase SAT Subscriptions', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo home_url_ssl() ?>/?r=payments">
                <input type="hidden" name="sub-type" id="sat2-preparation-sub-type" value="8">
                <input type="hidden" name="sat-class" id="sat2-preparation-class" value="15">
                <input type="hidden" name="sub-id" value="0">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="font-dialog col-xs-12" style="border-bottom: 1px solid #d6d6d6;"><?php _e('Selected Class', 'iii-dictionary') ?></label>
                                <p class="selected-class col-xs-12" id="selected-class1" >SAT II Preparation - Preparation</p>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12" style="padding-top: 3%">
                            <div class="form-group">                                
                                <label class="font-dialog"><?php _e('Number of Months', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control" name="sat-months" id="select-sat2-preparation-months" >
                                    <?php for ($i = 1; $i <= 24; $i++) : ?>
                                        <option value="<?php echo $i ?>"><?php printf(_n('%s month', '%s months', $i, 'iii-dictionary'), $i) ?></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                        </div>   
                        <div class="col-sm-12" id="sat-test-block">
                            <div class="form-group">
                                <label class="font-dialog"><?php _e('Practice Test', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control sel-sat-class" id="select-sat2-preparation-class" disabled="disabled">
                                        <option value="">Sat 2 - PREPARATION</option>
                                </select>
                            </div>
                        </div>  
                        <div class="col-sm-12 padding-top-2">
                            <div class="box-gray-dialog" style="text-align: right">
                                <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color708b23">$<span id="total-amount-sat2-preparation" class="color708b23">1</span></span>
                            </div>
                        </div>
                    </div>				
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button type="submit" name="add-to-cart" class="btn-custom confirm"><?php _e('Check out', 'iii-dictionary') ?></button>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1 secondary"><?php _e('Cancel', 'iii-dictionary') ?></a>
                            </div>
                        </div>
                    </div>
                </div>			
            </form>
        </div>
    </div>
</div>
<div id="sat1-subscrible-modal" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Purchase SAT Subscriptions', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo home_url_ssl() ?>/?r=payments">
                <input type="hidden" name="sub-type" id="sat1-sub-type" value="7">
                <input type="hidden" name="sat-class" id="sat1-class" value="">
                <input type="hidden" name="sub-id" value="0">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="font-dialog col-xs-12" style="border-bottom: 1px solid #d6d6d6;"><?php _e('Selected Class', 'iii-dictionary') ?></label>
                                <p class="selected-class col-xs-12" id="class-sat1" >SAT I</p>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12" style="padding-top: 3%">
                            <div class="form-group">                                
                                <label class="font-dialog"><?php _e('Number of Months', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control" name="sat-months" id="select-sat1-months">
                                    <?php for ($i = 1; $i <= 24; $i++) : ?>
                                        <option value="<?php echo $i ?>"><?php printf(_n('%s month', '%s months', $i, 'iii-dictionary'), $i) ?></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                        </div>
                        <?php
                            $select1_class_options = array(CLASS_MATH_SAT1A => __('SAT 1A', 'iii-dictionary'), CLASS_MATH_SAT1B => __('SAT 1B', 'iii-dictionary'),
                            CLASS_MATH_SAT1C => __('SAT 1C', 'iii-dictionary'), CLASS_MATH_SAT1D => __('SAT 1D', 'iii-dictionary'), CLASS_MATH_SAT1E => __('SAT 1E', 'iii-dictionary'));
                        ?>

                            <div class="col-sm-12" id="sat-test-block">
                                <div class="form-group">
                                    <label class="font-dialog"><?php _e('Practice Test', 'iii-dictionary') ?></label>
                                    <select class="select-box-it form-control sel-sat-class" id="select-sat1-class">
                                        <?php foreach ($select1_class_options as $key => $value) : ?>
                                            <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>  
                        <div class="col-sm-12 padding-top-2">
                            <div class="box-gray-dialog" style="text-align: right">
                                <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color708b23">$<span id="total-amount-sat1-class" class="color708b23">1</span></span>
                            </div>
                        </div>
                    </div>				
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button type="submit" name="add-to-cart" class="btn-custom confirm"><?php _e('Check out', 'iii-dictionary') ?></button>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1 secondary"><?php _e('Cancel', 'iii-dictionary') ?></a>
                            </div>
                        </div>
                    </div>
                </div>			
            </form>
        </div>
    </div>
</div>
<div id="sat2-subscrible-modal" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Purchase SAT Subscriptions', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo home_url_ssl() ?>/?r=payments">
                <input type="hidden" name="sub-type" id="sat2-sub-type" value="7">
                <input type="hidden" name="sat-class" id="sat2-class" value="">
                <input type="hidden" name="sub-id" value="0">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="font-dialog col-xs-12" style="border-bottom: 1px solid #d6d6d6;"><?php _e('Selected Class', 'iii-dictionary') ?></label>
                                <p class="selected-class col-xs-12" id="class-sat2" >SAT I</p>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12" style="padding-top: 3%">
                            <div class="form-group">                                
                                <label class="font-dialog"><?php _e('Number of Months', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control" name="sat-months" id="select-sat2-months">
                                    <?php for ($i = 1; $i <= 24; $i++) : ?>
                                        <option value="<?php echo $i ?>"><?php printf(_n('%s month', '%s months', $i, 'iii-dictionary'), $i) ?></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                        </div>
                        <?php
                            $select2_class_options = array(CLASS_MATH_SAT2A => __('SAT 2A', 'iii-dictionary'), CLASS_MATH_SAT2B => __('SAT 2B', 'iii-dictionary'),
                            CLASS_MATH_SAT2C => __('SAT 2C', 'iii-dictionary'), CLASS_MATH_SAT2D => __('SAT 2D', 'iii-dictionary'), CLASS_MATH_SAT2E => __('SAT 2E', 'iii-dictionary'));
                        ?>

                            <div class="col-sm-12" id="sat-test-block">
                                <div class="form-group">
                                    <label class="font-dialog"><?php _e('Practice Test', 'iii-dictionary') ?></label>
                                    <select class="select-box-it form-control sel-sat-class" id="select-sat2-class">
                                        <?php foreach ($select2_class_options as $key => $value) : ?>
                                            <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>  
                        <div class="col-sm-12 padding-top-2">
                            <div class="box-gray-dialog" style="text-align: right">
                                <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color708b23">$<span id="total-amount-sat2-class" class="color708b23">1</span></span>
                            </div>
                        </div>
                    </div>				
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button type="submit" name="add-to-cart" class="btn-custom confirm"><?php _e('Check out', 'iii-dictionary') ?></button>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1 secondary"><?php _e('Cancel', 'iii-dictionary') ?></a>
                            </div>
                        </div>
                    </div>
                </div>			
            </form>
        </div>
    </div>
</div>

<!--modal show preview math homework-->
<!--<div class="modal fade " id="modal-homework-math">
    <div class="modal-dialog modal-lg"style="top : 40% !important">
        <div class="modal-content" style="margin-top: 0px;" id="load-prevent-modal">
                Load data ajax
        </div>
    </div>
</div>-->
<!--<input type="button" id="btn-update-home" value="UPDATE NEXT HOME">-->
<script>
    var _CMODE = "<?php echo $curr_mode ?>";
    var ypoints = <?php echo ik_get_user_points($current_user_id) ?>;
    var annoying = <?php echo $is_sat_class_subscribed ? 'false' : 'true' ?>;
    var user_id = <?php echo $current_user_id; ?>;
    var ASSIGNMENT_WRITING = <?php echo ASSIGNMENT_WRITING; ?>;
    var ttp = <?php echo (int)$teacher_tool_price ?>;
    var ssp = <?php echo (int)$self_study_price ?>;
    var ssp_math = <?php echo (int)$self_study_price_math ?>;
    var dp = <?php echo (int)$dictionary_price ?>;
    var sub_sat = <?php echo SUB_SAT_PREPARATION ?>;
    var sub_dic = <?php echo SUB_DICTIONARY ?>;
    var sub_teach = <?php echo SUB_TEACHER_TOOL ?>;
    var adp = <?php echo mw_get_option('all-dictionary-price') ?>;
    var sat1Pre = <?php echo mw_get_option('math-sat1-preparation') ?>;
    var sat2Pre = <?php echo mw_get_option('math-sat2-preparation') ?>;
    var student_multiplier = <?php echo STUDENT_MULTIPLIER ?>;
    var min_student = <?php echo mw_get_option('min-students-subscription') ?>;
    var satGp = <?php echo mw_get_option('sat-grammar-price') ?>;
    var satWp = <?php echo mw_get_option('sat-writing-price') ?>;
    var satStp = <?php echo mw_get_option('sat-test-price') ?>;
    var satMIP = <?php echo mw_get_option('math-sat1-price') ?>;
    var satMIIP = <?php echo mw_get_option('math-sat2-price') ?>;
    var satMIKP1 = <?php echo mw_get_option('math-ik-price1') ?>;
    var satMIKP2 = <?php echo mw_get_option('math-ik-price2') ?>;
    var satMIKP3 = <?php echo mw_get_option('math-ik-price3') ?>;
    var satMIKP4 = <?php echo mw_get_option('math-ik-price4') ?>;
    var satMIKP5 = <?php echo mw_get_option('math-ik-price5') ?>;
    var satMIKP6 = <?php echo mw_get_option('math-ik-price6') ?>;
    var satMIKP7 = <?php echo mw_get_option('math-ik-price7') ?>;
    var satMIKP8 = <?php echo mw_get_option('math-ik-price8') ?>;
    var satMIKP9 = <?php echo mw_get_option('math-ik-price9') ?>;
    var satMIKP10 = <?php echo mw_get_option('math-ik-price10') ?>;
    var satMIKP11 = <?php echo mw_get_option('math-ik-price11') ?>;
    var satMIKP12 = <?php echo mw_get_option('math-ik-price12') ?>;
    var satMIKP = <?php echo mw_get_option('math-ik-price') ?>;
    var ptsr = <?php echo mw_get_option('point-exchange-rate') ?>;
    var M_SINGLE = "<?php _e('month', 'iii-dictionary') ?>";
    var M_PLURAL = "<?php _e('months', 'iii-dictionary') ?>";
    var DICT_EMPTY_ERR = "<?php _e('Please select a Dictionary', 'iii-dictionary') ?>";
    var GRP_EMPTY_ERR = "<?php _e('Please select a group', 'iii-dictionary') ?>";
    var GRP_EXIST_ERR = "<?php _e('This group name is already taken. Please choose a different name.', 'iii-dictionary') ?>";
    var GRP_PW_ERR = "<?php _e('Group password cannot empty', 'iii-dictionary') ?>";
    var M_EMPTY_ERR = "<?php _e('Please select Number of Months', 'iii-dictionary') ?>";
    var NUMBER_INV = "<?php _e('Invalid number', 'iii-dictionary') ?>";
    var LBL_NO_USERS = "<?php _e('Number of Users', 'iii-dictionary') ?>";
    var LBL_NO_M = "<?php _e('Number of Months', 'iii-dictionary') ?>";
    var LBL_NO_STUDENTS = "<?php _e('Number of Students', 'iii-dictionary') ?>";
    var LBL_NO_STUDENTS_ADD = "<?php _e('Number of Students to Increase', 'iii-dictionary') ?>";
    var LBL_NO_M_REMAIN = "<?php _e('Number of Remaining Months', 'iii-dictionary') ?>";
    var LBL_NO_M_ADD = "<?php _e('Number of Months', 'iii-dictionary') ?>";
    var _ISMATH = <?php echo $is_math_panel ? 1 : 0 ?>;
    var _IM4 = <?php echo!empty($_SESSION['method_point']) ? $_SESSION['method_point'] : 0 ?>;
    (function ($) {
        $(function () {
                $("#received_msg").on("hidden.bs.modal", function (e) {
                    e.preventDefault();
                    var id = $('#id').val();
                    var status = $('#status').val();
                    if(status == 2) {
                        $.get(home_url + "/?r=ajax/change_status", {st_id: id}, function (data) {
                           location.reload();  
                        })
                    }   
                });
                $('#btn-ok-rs').click (function (){
                    $('#modal-view-result-writing').modal('hide');
                });
                
                $('.not-join').click(function (e) {
                var modal = $("#require-modal");
                
                    e.preventDefault();
                    var modal = $("#class-detail-modal");
                    count=$(this).next().html();
                    if($(count).filter("br").length > 13){
                        $('.modal-body').css('height','400px');
                        modal.find("#modal-body-detail").html($(this).next().html());
                        modal.modal();
                     modal.find('#modal-body-detail').css('height','350px')
                    }else{
                        modal.find("#modal-body-detail").html($(this).next().html());
                        modal.modal();
                    }
                });
//                $('#btn-update-home').click(function(){
//                    $.get(home_url+"/?r=ajax/update-id-home",function(data){
////                        alert(data);
//                    }); 
//                });
            }); // end jquery
       })(jQuery);
</script>

<?php if (!$is_math_panel) : ?>
    <?php get_dict_footer() ?>
<?php else : ?>
    <?php get_math_footer() ?>
<?php endif; ?>

