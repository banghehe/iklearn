<?php
$page_title_tag = __('Spelling Homework', 'iii-dictionary');
include IK_PLUGIN_DIR . '/library/formatter.php';
$mode = get_query_var('mode', 'practice');
$select_grade_sheets = $insql = $tmp = $js_homework_list = array();
$cur_sheet_index = $count = 0;
$sid = isset($_GET['sid']) ? $_GET['sid'] : 0;
$_return_url = base64_decode(rawurldecode($_GET['ref']));
// is teacher taking a test?
$self_study_price = mw_get_option('self-study-price');
$teacher_taking_test = in_array($sid, (array) $_SESSION['teacher_tests']);
$view = $_GET["hid"] ;
if($view > 0) {
    MWDB::update_user_is_view_homework($view);
}
$homework_sheets = MWDB::get_homework_sheets(ASSIGNMENT_SPELLING);
$sheet_list = $homework_sheets;

if ($mode == 'practice') {
    $page_title_tag = __('Spelling Practice', 'iii-dictionary');
    $practice_sheets = MWDB::get_practice_sheets(ASSIGNMENT_SPELLING);

    foreach ($homework_sheets as $key => $item) {
        if ($item->private) {
            $teacher_sheet_suggestion[$item->grade] = $item->grade;
        } else {
            $public_sheet_grade_suggestion[$item->grade] = $item->grade;
            $public_sheet_sheet_suggestion[$item->sheet_name] = $item->sheet_name;
        }
    }

    $sheet_list = $practice_sheets;
}

// check homework id
if (!empty($_GET['hid'])) {
    $homework_assignment = MWDB::get_homework_assignment_by_id($_GET['hid']);
    $sid = $homework_assignment->sheet_id;
}

// user didn't select a homework, redirect back to practice page
if ($mode == 'homework' && !$sid) {
    wp_redirect(locale_home_url() . '/?r=spelling-practice');
    exit;
}

// no sheet id provided, get first sheet in the list as init sheet
if (!$sid) {
    $sheet = $sheet_list[0];
}

// if sheet id is provided, check if teacher taking a test
if ($sid && $teacher_taking_test) {
    if ($teacher_taking_test) {
        $sheet = MWDB::get_sheet($sid, mw_get_option('teacher-test-group'));
    } else {
        $sheet = MWDB::get_sheet($sid);
    }
    $sheet_list = array($sheet);
} else {
    foreach ($sheet_list as $item) {
//        var_dump($sheet_list);
        $_disabled_js = ($item->homework_type_id == HOMEWORK_SUBSCRIBED && !get_ws_subscribed()) ? 'disabled' : '';
        if ($_disabled_js == '') {
            $_disabled_js = 'disabled';
            $class = 'classdisable';
        }  else {
            $class='';
        }
        if (is_mw_super_admin() || is_mw_admin()) {
            $_disabled = '';
        }

        $select_grade_sheets[$item->grade] .= '<option data-sheet-id="' . $item->sheet_id . '" class="' . $class . '"  value="' . $item->sheet_name . '"' . $_disabled . '>' . $item->sheet_name . '</option>';

        if ($sid && $sid == $item->sheet_id) {
            $sheet = $item;

            // we only need 1 sheet in homework mode if sheet id is provided
            if ($mode == 'homework') {
                $js_homework_list = array();
                $sheet_list = array($item);
                break;
            }
        }

        if ($mode == 'homework' && $sheet->homework_id != $item->homework_id) {
            $js_homework_list[] = '{hid: ' . $item->homework_id . ', sid: ' . $item->sheet_id . ', grade: "' . $item->grade . '", sheet_num: "' . $item->sheet_name . '"}';
        }
    }

}

// get next homework
//if (!empty($homework_assignment->next_homework_id)) {
//    $next_homework = MWDB::get_homework_assignment_by_id($homework_assignment->next_homework_id);
//    $sheet->next_homework_id = $homework_assignment->next_homework_id;
//    $sheet->next_sheet = empty($next_homework->name) ? $next_homework->sheet_name : $next_homework->name;
//    $sheet->next_assignment_id = $next_homework->assignment_id;
//}

$sheet_total = count($sheet_list);
// load user answers
if (!empty($sheet->answers)) {
    $user_answers = json_decode($sheet->answers, true);
} else {
    $user_answers = json_decode($sheet->practice_answers, true);
}

// generate javascript for default worksheet
$words = json_decode($sheet->questions);
if (is_null($words)) {
    $words = array();
}

$dict_table = get_dictionary_table($sheet->dictionary_id);

foreach ($words as $key => $v) {
    $insql[] = "'" . esc_sql($v) . "'";
}

$results = $wpdb->get_results('SELECT id, entry, sound, sound_url, definition FROM ' . $wpdb->prefix . $dict_table . ' WHERE entry IN (' . implode(',', $insql) . ')');

foreach ($results as $item) {
    $tmp[strtolower($item->entry)][] = array('id' => $item->id, 'entry' => $item->entry, 'sound' => $item->sound, 'sound_url' => $item->sound_url, 'definition' => $item->definition);
}

$word_total = count($tmp);
$_cur_word_index = 0;
if (isset($sheet->finished_question)) {
    $_cur_word_index = $sheet->finished_question + 1;
    if ($_cur_word_index == $word_total) {
        $_cur_word_index = 0;
    }
}

$jsvar = 'var words = [];';
foreach ($tmp as $items) {
    $a = array();
    foreach ($items as $item) {
        $a['entry'] = strtolower($item['entry']);
        $a['def'] .= WFormatter::_def($item['definition'], $sheet->dictionary_id, true);
        if (!isset($a['sound'])) {
            if (!is_null($item['sound_url'])) {
                $a['sound'] = $item['sound_url'];
            } else {
                $sound_url = WFormatter::_sound($item['sound'], $sheet->dictionary_id, true);
                $a['sound'] = $sound_url;
                if ($sound_url != '') {
                    $wpdb->update(
                            $wpdb->prefix . $dict_table, array('sound_url' => $sound_url), array('id' => $item['id'])
                    );
                }
            }
        }
    }
    $ans = !isset($user_answers['q' . $count]['selected']) ? $user_answers['q' . $count] : $user_answers['q' . $count]['selected'];
    $jsvar .= 'words[' . $count . '] = {entry: "' . $a['entry'] . '", sound: "' . $a['sound'] . '", def: "' . str_replace('"', '\"', $a['def']) . '", selected: "' . $ans . '"};';
    $count++;
}
?>
<?php get_dict_header($page_title_tag, 'green') ?>
<?php
$info_tab_url = get_info_tab_cloud_url('Popup_info_12.jpg');
if ($mode == 'homework') {
    $box_bg = ' box-test-mode';
    $disable_select = ' disabled';
    get_dict_page_title($page_title_tag, 'test-mode', '', array(), $info_tab_url);
} else {
    $box_bg = '';
    $disable_select = '';
    get_dict_page_title($page_title_tag, '', '', array(), $info_tab_url);
}
?>

<div class="row">
    <div class="col-md-12">
        <h3 class="med-font-size"><?php _e('Spell the following word', 'iii-dictionary') ?></h3>
    </div>										
</div>									
<div class="row"<?php echo $teacher_taking_test ? ' style="display: none"' : '' ?>>
    <div class="col-sm-3">
        <div class="form-group box small<?php echo $box_bg ?>">
            <label><?php _e('GRADE:', 'iii-dictionary') ?></label>
            <select class="select-box-it select-green" id="grade"<?php echo $disable_select ?>>
                <?php foreach ($select_grade_sheets as $g => $s) : ?>
                    <option value="<?php echo $g ?>"<?php echo $sid && $g == $sheet->grade ? ' selected' : '' ?>><?php echo $g ?></option>
                <?php endforeach ?>
            </select>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group box small<?php echo $box_bg ?>">
            <label><?php _e('LESSON:', 'iii-dictionary') ?></label>
            <select class="select-box-it select-green" id="sheet-num"<?php echo $disable_select ?>></select>
            <?php foreach ($select_grade_sheets as $g => $s) : ?>
                <select id="sheet-num-<?php echo $g ?>" style="display: none">
                    <?php echo $s ?>
                </select>
            <?php endforeach ?>
        </div>
    </div>										
    <div class="col-sm-3 form-group">
        <div class="loading">
            <span class="icon-loading"></span>&nbsp;<?php _e('Loading...', 'iii-dictionary') ?>
        </div>
        <div class="words-pagin txt-question" >
           <?php
            printf(__('Question %s of %s', 'iii-dictionary'), '<span id="word-num">' . ($_cur_word_index + 1) . '</span>', '<span id="word-total">' . $word_total . '</span>')
            ?>
            <!--<span class="css-color-fff" style="float: left">20 Words</span>-->
        </div>
<!--        if ($mode != 'homework') : ?>
            <button class="btn btn-default btn-block sky-blue" type="button" id="reset-counter"<?php echo $mode == 'homework' ? ' disabled' : '' ?>><?php _e('Go back to 1', 'iii-dictionary') ?></button>
         endif ?>-->

        <div class="form-group" id="sl-option">
            <select class="select-background select-box-it form-control" id="sel1">
                <?php for($i = 0; $i< $word_total ;$i++) { 
                    $j=$i+1;
                ?>
                    <option class="css-color-b0b0b0" value=<?php echo $i?>><?php echo "Page"." ".$j; ?></option>
                <?php } ?>
            </select>
         </div>
    </div>										
</div>

<div class="row">
    <div class="col-md-12">
        <input type="text" class="form-control answer-box bg-correct" id="answer"  value="" autocomplete="off" data-placement="top" data-trigger="focus">
    </div>
</div>
<div class="row">
    <div class="col-sm-4">
        <div class="form-group">
            <button type="button" id="start-btn" class="btn btn-block css-background-00a5bb css-color-fff" ><?php _e('Start', 'iii-dictionary') ?><span class="icon-start-new"></span></button>
        </div>
    </div>
    <?php if ($mode == 'practice') : ?>
        <div class="col-sm-4">
            <div class="form-group">
                <button type="button" id="submit-btn"  class="btn btn-block css-background-ff8a00 css-color-fff" ><?php _e('Submit', 'iii-dictionary') ?><span class="icon-submit-new"></span></button>
            </div>
        </div>
    <?php endif ?>
    <div class="col-sm-4">
        <div class="form-group">
            <button type="button" id="next-btn" class="btn btn-block css-background-ff8a00 css-color-fff"><?php _e('Next', 'iii-dictionary') ?><span class="icon-next-new"></span></button>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="box desc scroll-list">
            <div id="word-hints"><span class="word-hints"><?php _e('HINT (Click here !)', 'iii-dictionary') ?></span></div>
        </div>
    </div>
</div>
<input type="hidden" id="current-word" value="<?php echo $_cur_word_index ?>">
<input type="hidden" id="rid" value="<?php echo isset($sheet->homework_result_id) ? $sheet->homework_result_id : 0 ?>">

<?php if ($mode == 'homework') : ?>

    <?php if (empty($homework_sheets) && !$teacher_taking_test) : ?>

        <div id="nogroup-mode-dialog" class="modal fade modal-green" data-keyboard="false" data-backdrop="static" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <?php if (!is_student_in_group()) : ?>
                        <div class="modal-header">
                            <h3><?php _e('Join a Group', 'iii-dictionary') ?></h3>
                        </div>
                        <div class="modal-body">
                            <p><?php _e('You need to join a Group to use Homework mode.', 'iii-dictionary') ?><br>
                                <?php _e('To join a Group, go to Homework Status page', 'iii-dictionary') ?><br>
                                <?php _e('You can get Group name and Group password from a teacher.', 'iii-dictionary') ?></p>
                        </div>
                    <?php else : ?>
                        <div class="modal-header">
                            <h3><?php _e('No Homework', 'iii-dictionary') ?></h3>
                        </div>
                        <div class="modal-body">
                            <p><?php _e('You do not have any homework assignment now.', 'iii-dictionary') ?></p>
                        </div>
                    <?php endif ?>
                    <div class="modal-footer">
                        <div class="row">				
                            <div class="col-md-12">
                                <a href="<?php echo locale_home_url() ?>/?r=spelling-practice" class="btn btn-block grey secondary"><span class="icon-switch"></span><?php _e('Go back', 'iii-dictionary') ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>jQuery("#nogroup-mode-dialog").modal();centerModals();</script>

    <?php endif ?>

    <div id="submit-lesson-modal-dialog" class="modal fade modal-green" data-keyboard="true" aria-hidden="true"<?php echo $teacher_taking_test ? ' data-backdrop="static"' : '' ?>>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3><?php !$teacher_taking_test ? _e('The End of Homework', 'iii-dictionary') : _e('The End of Test', 'iii-dictionary') ?></h3>
                </div>
                <?php if (!$teacher_taking_test) : ?>
                    <div class="modal-body">
                        <?php _e('You have completed this homework.', 'iii-dictionary') ?><br>
                        <?php _e('If you want to leave a message to your teacher, type it in the box below.', 'iii-dictionary') ?><br>
                        <?php
                        if (empty($sheet->next_homework_id)) {
                            _e('Click OK to submit.', 'iii-dictionary');
                        }
                        ?>
                        <br>
                        <div class="form-group">
                            <textarea class="form-control" id="txt-feedback" placeholder="<?php _e('Leave feedback', 'iii-dictionary') ?>" style="resize: none"></textarea>
                        </div>
                        <?php if (!empty($sheet->next_homework_id)) : ?>
                            <div class="homework-notice">
                                <?php printf(__('Starting the next worksheet, %s?', 'iii-dictionary'), $sheet->next_sheet) ?>
                            </div>
                        <?php endif ?>
                        <hr>
                    </div>
                    <div class="modal-footer">
                        <div class="row">
                            <?php if (empty($sheet->next_homework_id)) : ?>
                                <div class="col-sm-12 form-group">
                                    <button type="button" class="btn btn-block orange confirm submit-lesson-btn" data-loading-text="<?php _e('Submitting...', 'iii-dictionary') ?>" data-ref="<?php echo $_return_url ?>"><span class="icon-accept"></span><?php _e('OK', 'iii-dictionary') ?></button>
                                </div>
                            <?php else : ?>
                                <div class="col-sm-6 form-group">
                                    <button type="button" class="btn btn-block orange confirm submit-lesson-btn" data-loading-text="<?php _e('Submitting...', 'iii-dictionary') ?>" data-ref="<?php echo MWHtml::get_practice_page_url($sheet->next_assignment_id) . '&amp;mode=homework&amp;hid=' . $sheet->next_homework_id . '&amp;ref=' . $_GET['ref'] ?>"><span class="icon-accept"></span><?php _e('Yes', 'iii-dictionary') ?></button>
                                </div>
                                <div class="col-sm-6 form-group">
                                    <button type="button" class="btn btn-block grey confirm submit-lesson-btn" data-loading-text="<?php _e('Submitting...', 'iii-dictionary') ?>" data-ref="<?php echo $_return_url ?>"><span class="icon-cancel"></span><?php _e('Quit', 'iii-dictionary') ?></button>
                                </div>
                            <?php endif ?>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="modal-body">
                        <?php _e('You have completed this test.', 'iii-dictionary') ?><br>
                        <?php _e('If you want to leave a message to the admin, type it in the box below.', 'iii-dictionary') ?><br>
                        <?php _e('Click OK to submit.', 'iii-dictionary') ?>
                        <hr>
                        <div class="form-group">
                            <textarea class="form-control" id="txt-feedback" placeholder="Leave feedback" style="resize: none"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <button type="button" class="btn btn-block orange confirm submit-lesson-btn" data-loading-text="<?php _e('Submitting...', 'iii-dictionary') ?>"><span class="icon-accept"></span><?php _e('OK', 'iii-dictionary') ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>

<?php else : ?>

    <div id="quit-practice-modal" class="modal fade modal-green" data-keyboard="true" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3><?php _e('The end of practice session', 'iii-dictionary') ?></h3>
                </div>
                <div class="modal-body">
                    <div class="homework-notice">
                        <?php if (!empty($sheet->next_homework_id)) : ?>
                            <?php printf(__('Starting the next worksheet, %s?', 'iii-dictionary'), $sheet->next_sheet) ?>
                        <?php else : ?>
                            <?php _e('The end of practice session', 'iii-dictionary') ?>
                        <?php endif ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <?php if (empty($sheet->next_homework_id)) : ?>
                            <div class="col-sm-12 form-group">
                                <a href="<?php echo $_return_url ?>" class="btn btn-block orange confirm"><span class="icon-accept"></span><?php _e('OK', 'iii-dictionary') ?></a>
                            </div>
                        <?php else : ?>
                            <div class="col-sm-6 form-group">
                                <a href="<?php echo locale_home_url() . '/?r=spelling-practice&amp;hid=' . $sheet->next_homework_id . '&amp;ref=' . $_GET['ref'] ?>" class="btn btn-block orange confirm"><span class="icon-accept"></span><?php _e('Yes', 'iii-dictionary') ?></a>
                            </div>
                            <div class="col-sm-6 form-group">
                                <a href="<?php echo $_return_url ?>" class="btn btn-block grey confirm"><span class="icon-cancel"></span><?php _e('Quit', 'iii-dictionary') ?></a>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!--modal-required subscribe-->
<div id="require-modal1" class="modal fade modal-white ik-modal1 ik-modal-transparent" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <a style="margin-top: 1%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></a>
                    <h3><?php _e('Save to Folder', 'iii-dictionary') ?></h3>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-6" style="width: 100% !important">
                            <div class="form-group">
                                <button type="button" id="ok-modal-req-sub" class="btn-custom btn-leave-group"><?php _e('OK', 'iii-dictionary') ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>

<?php endif ?>

<?php MWHtml::subscribe_dictionary_popup($sheet->dictionary_id) ?>

<!-- Student's Self-study Subscription -->
<div id="self-study-subscription" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Student\'s Self-study Subscription', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo locale_home_url() ?>/?r=payments">
                <input type="hidden" name="sub-type" value="<?php echo!$is_math_panel ? SUB_SELF_STUDY : SUB_SELF_STUDY_MATH ?>" id="self-study-sub">
                <?php $self_study_group = generate_self_study_group_name() ?>
                <input type="hidden" name="group-name" value="<?php echo $self_study_group ?>">
                <input type="hidden" name="sat-months" id="self-sat-months" value="1">
                <div class="modal-body">
                    <div class="row">
                        <div class=" form-group">

                            <label class="font-dialog col-xs-12" style="border-bottom: 1px solid #d6d6d6;"><?php _e('Default Group for this subscription', 'iii-dictionary') ?></label>
                            <p class="selected-class col-xs-12" style="padding: 5px 15px"><?php echo $self_study_group ?></p>
                        </div>
                        <div class="col-sm-12 form-group" id="ss-dict-block">
                            <label class="font-dialog"><?php _e('Select the type of dictionary', 'iii-dictionary') ?></label>
                            <?php MWHtml::select_dictionaries('', false, 'dictionary', 'sel-dictionary2', 'form-control', true) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="font-dialog"><?php _e('Number of Students', 'iii-dictionary') ?></label>
                                <input type="number" name="no-students" class="form-control" min="1" max="1" value="1" readonly>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="font-dialog"><?php _e('Number of Months', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control" name="self-study-months" id="sel-self-study-months1">
                                    <?php for ($i = 1; $i <= 24; $i++) : ?>
                                        <option value="<?php echo $i ?>"><?php printf(_n('%s month', '%s months', $i, 'iii-dictionary'), $i) ?></option>
                                    <?php endfor ?>
                                </select>   
                            </div>
                        </div>
                        <div class="col-sm-12 padding-top-2">
                            <div class="box-gray-dialog" style="text-align: right">
                                <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color708b23">$</span> <span class="currency color708b23" id="ss-total-amount">0</span>
                            </div>
                        </div>
                    </div>				
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button type="submit" id="add-to-cart-ss" name="add-to-cart" class="btn-custom"><?php _e('Check out', 'iii-dictionary') ?></button>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1"><?php _e('Cancel', 'iii-dictionary') ?></a>
                            </div>
                        </div>
                    </div>
                </div>			
            </form>
        </div>
    </div>
</div>

<script>
    var CMODE = "<?php echo $mode ?>";
    if (!isuserloggedin) 
        var DIS = "<?php echo $_disabled_js ?>";
    else
        var DIS = "";
    if (window.location.search.indexOf('&hid=') > -1) {
        var url = window.location.href;
        var str = url.split('&hid=');
        var csid = str[1];
    } else {
    }
    
    var pid = <?php echo is_null($sheet->pid) ? 0 : $sheet->pid // practice result id   ?>;
    var ptid = <?php echo is_numeric($_GET['hid']) ? $_GET['hid'] : 0 ?>;
<?php echo $jsvar; ?>
<?php if ($mode == 'homework') : ?>
        var homework_list = [<?php echo implode(',', $js_homework_list) ?>];
        var current_homework_id = <?php echo!empty($homework_assignment->id) ? $homework_assignment->id : 0 ?>;
<?php endif ?>
    if ((window.matchMedia('screen and (max-width: 480px)').matches)) {
            jQuery('.col-sm-offset-1 h1:first-child').attr('style', 'color: #599180;margin-bottom: 4%;padding-top: 6%;');
        }
    (function ($) {
        $(function () {
//            setup_question();
            //$("#answer").val(words[parseInt($("#current-word").val())].selected);

            $("#start-btn").click(function () {
                $("#submit-btn").removeClass("css-background-00a5bb");
                $("#next-btn").removeClass("css-background-00a5bb");
                $("#submit-btn").addClass("css-background-ff8a00");
                $("#next-btn").addClass("css-background-ff8a00");
                var existclass=$( "#sheet-num option:selected" ).hasClass('classdisable');
                if (DIS != ''&& existclass==false && window.location.href.indexOf("&pr=")<= -1) {
                    var modal = $("#require-modal1");
                    if (!isuserloggedin) {
                        modal.find("h3").text('Login Required');
                        modal.find(".modal-body").html('Please login in order to continue to use this function.', 'iii-dictionary');
                        modal.find(".modal-footer a").html('<span class="icon-issue2"></span> ' + 'Login');
                        modal.modal();
                    }/* else {
                        modal.find("h3").text('Subscription Required');
                        modal.find(".modal-body").html('Please subscribe Student Self-study to start');
                        modal.find(".modal-footer a").html('<span class="icon-issue2"></span> ' + 'Subscribe');
                    }*/
                    //modal.modal();
                } else {
                    $(this).html('<?php _e('Replay', 'iii-dictionary') ?><span class="icon-start-new"></span>');
                    $(this).addClass("css-background-ff8a00");
                    $(this).removeClass("css-background-00a5bb");
                    
                    $('#submit-btn').addClass("css-background-00a5bb");
                    $('#submit-btn').removeClass("css-background-ff8a00");
                    
                    $("#next-btn").addClass("css-background-ff8a00");
                    $("#next-btn").removeClass("css-background-00a5bb");
                    var current_word_index = $("#current-word").val();
                    var src_wav = words[current_word_index].sound;

                    if (supportAudioHtml5())
                        playHtml5(src_wav);
                    else if (supportAudioFlash())
                        playFlash(src_wav);
                    else
                        playRaw(src_wav);
                    $("#answer").focus();
                }
            });

            $("#next-btn").click(function () {
                $(this).removeClass("css-background-00a5bb");
                $(this).addClass("css-background-ff8a00");
                
                $('#start-btn').removeClass("css-background-ff8a00");
                $('#start-btn').addClass("css-background-00a5bb");
                
                $('#submit-btn').removeClass("css-background-00a5bb");
                $('#submit-btn').addClass("css-background-ff8a00");
                var existclass=$( "#sheet-num option:selected" ).hasClass('classdisable');
                            if (DIS != ''&& existclass==false && window.location.href.indexOf("&pr=")<= -1) {
                    var modal = $("#require-modal1");
                    if (!isuserloggedin) {
                        modal.find("h3").text('Login Required');
                        modal.find(".modal-body").html('Please login in order to continue to use this function.', 'iii-dictionary');
                        modal.find(".modal-footer a").html('<span class="icon-issue2"></span> ' + 'Login');
                    }/* else {
                        modal.find("h3").text('Subscription Required');
                        modal.find(".modal-body").html('Please subscribe Student Self-study to start');
                        modal.find(".modal-footer a").html('<span class="icon-issue2"></span> ' + 'Subscribe');
                    }*/
                    //modal.modal();
                } else {
                    $(this).removeClass("css-background-00a5bb");
                    $(this).addClass("css-background-ff8a00");
                    if (CMODE == "homework") {
                        var _selected = $("#answer").val().toLowerCase();
                        var tthis = $(this);
                        var _cur_word_i = $("#current-word").val();
                        var _a = {question: words[_cur_word_i].sound, selected: _selected};
                        words[_cur_word_i].selected = _selected;
                        _a.score = _selected == words[_cur_word_i].entry ? 1 : 0;
                        tthis.button("loading");
                        save_answer(JSON.stringify(_a));
                        setTimeout(function () {
                            tthis.button("reset")
                        }, 550);
                    }
                    $("#start-btn").html('<?php _e('Start', 'iii-dictionary') ?><span class="icon-start-new"></span>');
                    $("#submit-btn").button("reset");
                    var current_word_index = parseInt($("#current-word").val());
                    if (current_word_index == (words.length - 1)) {
                        current_word_index = -1;
                    }
                    if (current_word_index == (words.length - 2)) {
                        $("#submit-homework").prop("disabled", false);
                    }
                    setup_question();
                    $("#current-word").val(current_word_index + 1);
                    $("#word-hints").html('<span class="word-hints"><?php _e('HINT (Click here !)', 'iii-dictionary') ?></span>');
                    $("#word-num").html(current_word_index + 2);
//                    $("#answer").val(words[current_word_index + 1].selected);
                    $('#answer').val("");
                    $('#sel1').val(parseInt(current_word_index)+1).trigger('change');
                }
            });

            $("#submit-btn").click(function () {
                var existclass=$( "#sheet-num option:selected" ).hasClass('classdisable');
                            if (DIS != ''&& existclass==false && window.location.href.indexOf("&pr=")<= -1) {
                    var modal = $("#require-modal1");
                    if (!isuserloggedin) {
                        modal.find("h3").text('Login Required');
                        modal.find(".modal-body").html('Please login in order to continue to use this function.', 'iii-dictionary');
                        modal.find(".modal-footer a").html('<span class="icon-issue2"></span> ' + 'Login');
                        modal.modal();
                    }/* else {
                        modal.find("h3").text('Subscription Required');
                        modal.find(".modal-body").html('Please subscribe Student Self-study to start');
                        modal.find(".modal-footer a").html('<span class="icon-issue2"></span> ' + 'Subscribe');
                    }
                    modal.modal();*/
                } else {
                    if ($("#answer").val().trim() == "") {
                        $("#answer").removeClass("bg-incorrect");
                        $("#answer").removeClass("bg-correct");
                        $("#answer").addClass("bg-empty");
                        $("#answer").popover({content: '<span class="popover-alert"><?php _e('Please enter your answer!', 'iii-dictionary') ?></span>', html: true});
                        $("#answer").popover("show");
                        setTimeout(function () {
                            $("#answer").popover("destroy")
                        }, 1000);
                    } else {
                        $(this).removeClass("css-background-00a5bb");
                        $(this).addClass("css-background-ff8a00");
                        
                        $("#start-btn").removeClass("css-background-00a5bb");
                        $("#start-btn").addClass("css-background-ff8a00");
                        
                        $('#next-btn').removeClass("css-background-ff8a00");
                        $('#next-btn').addClass("css-background-00a5bb");
                        $(this).removeClass("css-background-00a5bb");
                        $(this).addClass("css-background-ff8a00");
                        var _cur_word_i = $("#current-word").val();
                        var _selected = $("#answer").val().toLowerCase();
                        words[_cur_word_i].selected = _selected;
                        var _a = {question: words[_cur_word_i].sound, selected: _selected};
                        _a.score = _selected == words[_cur_word_i].entry ? 1 : 0;
                        if (CMODE == "practice") {
                            if (_a.score == 1) {
                                $("#answer").removeClass("bg-incorrect");
                                $("#answer").removeClass("bg-empty");
                                $("#answer").addClass("bg-correct");
                                $("#answer").popover({content: '<span class=""><?php _e('Correct!', 'iii-dictionary') ?></span>', html: true});
                                $('.popover').css("width","200px !important");
                                $("#answer").popover("show");

                            } else {
                                $("#answer").removeClass("bg-correct");
                                $("#answer").removeClass("bg-empty");
                                $("#answer").addClass("bg-incorrect");
                                $("#answer").popover({content: '<span class=""><?php _e('Incorrect!', 'iii-dictionary') ?></span>', html: true});
                                $("#answer").popover("show");
                            }
                            setTimeout(function () {
                                $("#answer").popover("destroy")
                            }, 1000);
                            save_answer();
                        } else {
                            save_answer(JSON.stringify(_a));
                        }
                    }
                }
            });

            function save_answer(answer) {
                if (CMODE == "homework") {
                    $("#submit-btn").button("loading");
                    $.post(home_url + "/?r=ajax/homework/answer",
                            {rid: $("#rid").val(), homework_id: current_homework_id, qc: words.length, q: $("#current-word").val(), answer: answer},
                            function (data) {
                                var data = JSON.parse(data);
                                $("#rid").val(data);
                                $("#submit-btn").button("reset");
                            }
                    );
                } else {
                    var _selected = $("#answer").val().toLowerCase();
                    $.post(home_url + "/?r=ajax/practice/save",
                            {pid: pid, ptid: ptid, sid: csid, q: $("#current-word").val(), answer: _selected},
                            function (data) {
//                                data = JSON.parse(data);
//                                pid = data[0];
                            }
                    );
                }
            }

            $("#submit-homework").click(function () {
                var existclass=$( "#sheet-num option:selected" ).hasClass('classdisable');
                            if (DIS != ''&& existclass==false && window.location.href.indexOf("&pr=")<= -1) {
                    var modal = $("#require-modal1");
                    if (!isuserloggedin) {
                        modal.find("h3").text('Login Required');
                        modal.find(".modal-body").html('Please login in order to continue to use this function.', 'iii-dictionary');
                        modal.find(".modal-footer a").html('<span class="icon-issue2"></span> ' + 'Login');
                        modal.modal();
                    }/* else {
                        modal.find("h3").text('Subscription Required');
                        modal.find(".modal-body").html('Please subscribe Student Self-study to start');
                        modal.find(".modal-footer a").html('<span class="icon-issue2"></span> ' + 'Subscribe');
                    }
                    modal.modal();*/
                } else {
                $("#submit-lesson-modal-dialog").modal();
                $("#txt-feedback").focus();
                }
            });

            $(".submit-lesson-btn").click(function (e) {
            e.preventDefault();
            var existclass=$( "#sheet-num option:selected" ).hasClass('classdisable');
                            if (DIS != ''&& existclass==false && window.location.href.indexOf("&pr=")<= -1) {
                    var modal = $("#require-modal1");
                    if (!isuserloggedin) {
                        modal.find("h3").text('Login Required');
                        modal.find(".modal-body").html('Please login in order to continue to use this function.', 'iii-dictionary');
                        modal.find(".modal-footer a").html('<span class="icon-issue2"></span> ' + 'Login');
                        modal.modal();
                    }/* else {
                        modal.find("h3").text('Subscription Required');
                        modal.find(".modal-body").html('Please subscribe Student Self-study to start');
                        modal.find(".modal-footer a").html('<span class="icon-issue2"></span> ' + 'Subscribe');
                    }
                    modal.modal();*/
                } else {
                var _cur_word_i = $("#current-word").val();
                var _selected = $("#answer").val().toLowerCase();
                var _a = {question: words[_cur_word_i].sound, selected: _selected};
                _a.score = _selected == words[_cur_word_i].entry ? 1 : 0;
                save_answer(JSON.stringify(_a));
                var tthis = $(this);
                tthis.button("loading");
                $.post(home_url + "/?r=ajax/homework/submit",
                        {homework_id: current_homework_id, rid: $("#rid").val(), feedback: $("#txt-feedback").val()},
                        function (data) {
<?php if ($teacher_taking_test) : ?>
                                window.location.href = home_url + "/?r=teaching/teach-class";
<?php else : ?>
                                window.location.href = tthis.attr("data-ref");
<?php endif ?>
                        }
                );
        }
            });

            $("#reset-counter").click(function () {
                var existclass=$( "#sheet-num option:selected" ).hasClass('classdisable');
                            if (DIS != ''&& existclass==false && window.location.href.indexOf("&pr=")<= -1) {
                    var modal = $("#require-modal1");
                    if (!isuserloggedin) {
                        modal.find("h3").text('Login Required');
                        modal.find(".modal-body").html('Please login in order to continue to use this function.', 'iii-dictionary');
                        modal.find(".modal-footer a").html('<span class="icon-issue2"></span> ' + 'Login');
                        modal.modal();
                    }/* else {
                        modal.find("h3").text('Subscription Required');
                        modal.find(".modal-body").html('Please subscribe Student Self-study to start');
                        modal.find(".modal-footer a").html('<span class="icon-issue2"></span> ' + 'Subscribe');
                    }
                    modal.modal();*/
                } else {
                    $("#word-num").html(1);
                    $("#start-btn").html('<?php _e('Start', 'iii-dictionary') ?><span class="icon-start-new"></span>');
                    $("#answer").val("");
                    $("#current-word").val(0);
                    $("#word-hints").html('<?php _e('HINT (Click here !)', 'iii-dictionary') ?><span class="word-hints"></span>');
                    setup_question();
                }
            });

            $("#answer").keyup(function () {
                if ($(this).val().trim() != "") {
                    $("#answer").popover("destroy");
                }
            });
            $("#sheet-num").html($("#sheet-num-" + $("#grade").val()).html());
            $("#sheet-num").data("selectBox-selectBoxIt").refresh();
            $("#sheet-num").data("selectBox-selectBoxIt").selectOption(<?php echo $sid ? '"' . $sheet->sheet_name . '"' : 0 ?>);

            $("#word-hints").on("click", ".word-hints", function () {
                /* $("#subscribe-modal-dialog").modal(); */
                $("#word-hints").html(words[$("#current-word").val()].def);
            });

            $("#grade").on("option-click", function () {
                $("#sheet-num").html($("#sheet-num-" + $(this).val()).html());
                $("#sheet-num").data("selectBox-selectBoxIt").refresh();
                $("#sheet-num").trigger("option-click");
                $("#sel1").selectBoxIt('selectOption', '0'.toString()).data("selectBox-selectBoxIt");
                $("#sel1").data("selectBox-selectBoxIt").refresh();
            });

            $("#sheet-num").on("option-click", function (e) {
                e.preventDefault();
                $('#start-btn').addClass("css-background-00a5bb");
                $('#start-btn').removeClass("css-background-ff8a00");
                $('#submit-btn').removeClass("css-background-00a5bb");
                $('#next-btn').removeClass("css-background-00a5bb");
                
                $('#submit-btn').addClass("css-background-ff8a00");
                $('#next-btn').addClass("css-background-ff8a00");
                var existclass=$( "#sheet-num option:selected" ).hasClass('classdisable');
                            if (DIS != ''&& existclass==false) {
                    var modal = $("#require-modal1");
                    if (!isuserloggedin) {
                        modal.find("h3").text('Login Required');
                        modal.find(".modal-body").html('Please login in order to continue to use this function.', 'iii-dictionary');
                        modal.find(".modal-footer a").html('<span class="icon-issue2"></span> ' + 'Login');
                        modal.modal();
                    }/* else {
                        modal.find("h3").text('Subscription Required');
                        modal.find(".modal-body").html('Your subscription has been expired. Please subscribe Student Self-Study to start.');
                        modal.find(".modal-footer a").html('<span class="icon-issue2"></span> ' + 'Subscribe');
                    }
                    modal.modal();*/
                } else {
                    $(".words-pagin").hide();
                    $(".loading").fadeIn();
                    $("#start-btn").prop("disabled", true);
                    $("#submit-btn").prop("disabled", true);
                    $("#next-btn").prop("disabled", true);
                    var sid = $("#sheet-num :selected").attr("data-sheet-id");
                    $.getJSON(home_url + "/?r=ajax/question",
                            {sid: sid, cmod: CMODE},
                            function (data) {
                                csid = sid;
                                pid = data.pid;
                                words = data.sheet;
                                $("#word-total").html(data.sheet.length);
                                $("#word-num").html(1);
                                $("#htype-id").text(data.htype);
                                $("#start-btn").prop("disabled", false);
                                $("#submit-btn").prop("disabled", false);
                                $("#next-btn").prop("disabled", false);
                                $("#start-btn").html('<?php _e('Start', 'iii-dictionary') ?><span class="icon-start-new"></span>');
                                $("#answer").val("");
                                $("#current-word").val(0);
                                $("#word-hints").html('<span class="word-hints"><?php _e('HINT (Click here !)', 'iii-dictionary') ?></span>');
                                $(".loading").hide();
                                $(".words-pagin").fadeIn();
//                                setup_question();
                            }
                    );
                }
            });
            function setup_question() {
                if (typeof words[$("#current-word").val()] != "undefined") {
                    $("#answer").val(words[parseInt($("#current-word").val())].selected);
                } else {
                    $("#answer").val('');
                }
            }
            var ssp = <?php echo (int)$self_study_price ?>;
            function price_self_study() {
                    var months = isNaN(parseInt($("#sel-self-study-months1").val())) ? 0 : parseInt($("#sel-self-study-months1").val());                 
                    $("#ss-total-amount").text(months * ssp);
                }
            $('#ok-modal-req-sub').click(function (){
                if(!isuserloggedin) {
                    $('#require-modal1').modal('hide');
                    jQuery('#show_login').click();
                }/* else {                
                    $('#require-modal1').modal('hide');
                    $('#self-study-subscription').modal('show');
                }*/
            });
            $('#add-to-cart-ss').click(function (e){
                $selected = $('#sel-dictionary2 option:selected');
                if($selected.val() == '') {
                    e.preventDefault();
                    $selbox = $("#sel-dictionary2SelectBoxIt");
                    $selbox.popover({content: '<span class="text-danger">' +'You not selected dictionary'+ '</span>', html: true, trigger: "hover", placement: "bottom",delay: { "show": 500, "hide": 3000 }}).popover("show");
                    setTimeout(function () {
                        $selbox.popover("destroy")
                    }, 2000);
                } else {
                    price_self_study();
                }
            }); 
            $("#sel-self-study-months1,#sel-dictionary2").change(function () {
                if($('#sel-dictionary2 option:selected').val()=='') { 
                    $("#ss-total-amount").text(0);
                } else {
                   price_self_study();
                }
            });
            $('#sel-self-study-months1').change(function (){
                $('#self-sat-months').val($('#sel-self-study-months1').val());
            });
            if(window.location.href.indexOf("grid") > -1) 
            {
                var url = window.location.href;
                var string = url.split('grid=');
                var string1 = string[1].split('&');
                var grade = string1[0]-2;
                $("#grade").selectBoxIt('selectOption', grade.toString()).data("selectBox-selectBoxIt");
                $("#grade").data("selectBox-selectBoxIt").refresh();
                $("#grade").trigger("option-click");
                var string2 = url.split('%20');
                var name = 'Spelling '+string2[1];
                $("#sheet-num").selectBoxIt('selectOption', name.toString()).data("selectBox-selectBoxIt");
                $("#sheet-num").data("selectBox-selectBoxIt").refresh();
                $("#sheet-num").trigger("option-click");
            }
        // Handing click "ENTER" on keyboard tringger click button have class css-background-00a5bb (Blue background)
            document.addEventListener('keypress', function(e) {
                var start = $("#start-btn");
                var submit = $("#submit-btn");
                var next = $("#next-btn");
                var keynum = e.keyCode || e.which;
                if(keynum == 13) {
                    if(start.hasClass("css-background-00a5bb")) {
                        $("#start-btn").click();
                    } 
                // Handing Click enter keyboar submit   
                    else if (submit.hasClass("css-background-00a5bb")){
//                        $("#submit-btn").click();
                       var existclass=$( "#sheet-num option:selected" ).hasClass('classdisable');
                            if (DIS != ''&& existclass==false && window.location.href.indexOf("&pr=")<= -1) {
                        var modal = $("#require-modal1");
                        if (!isuserloggedin) {
                            modal.find("h3").text('Login Required');
                            modal.find(".modal-body").html('Please login in order to continue to use this function.', 'iii-dictionary');
                            modal.find(".modal-footer a").html('<span class="icon-issue2"></span> ' + 'Login');
                            modal.modal();
                        }/* else {
                            modal.find("h3").text('Subscription Required');
                            modal.find(".modal-body").html('Please subscribe Student Self-study to start');
                            modal.find(".modal-footer a").html('<span class="icon-issue2"></span> ' + 'Subscribe');
                        }
                        modal.modal();*/
                    } else {
                        if ($("#answer").val().trim() == "") {
                            $("#answer").removeClass("bg-incorrect");
                            $("#answer").removeClass("bg-correct");
                            $("#answer").addClass("bg-empty");
    //                        $("#answer").popover({content: '<span class=""><?php _e('Please enter your answer!', 'iii-dictionary') ?></span>', html: true,delay: { "show": 500, "hide": 3000 }});
                            $("#answer").popover({content: '<span class="popover-alert"><?php _e('Please enter your answer!', 'iii-dictionary') ?></span>', html: true});
                            $("#answer").popover("show");
                            setTimeout(function () {
                                $("#answer").popover("destroy")
                            }, 1000);
                        } else {
                            $('#submit-btn').removeClass("css-background-00a5bb");
                            $('#submit-btn').addClass("css-background-ff8a00");

                            $("#start-btn").removeClass("css-background-00a5bb");
                            $("#start-btn").addClass("css-background-ff8a00");

                            $('#next-btn').removeClass("css-background-ff8a00");
                            $('#next-btn').addClass("css-background-00a5bb");
                            var as = $('#answer').val();
                            var _cur_word_i = $("#current-word").val();
                            var _selected = $("#answer").val().toLowerCase();
                            words[_cur_word_i].selected = _selected;
                            var _a = {question: words[_cur_word_i].sound, selected: _selected};
                            _a.score = _selected == words[_cur_word_i].entry ? 1 : 0;
                            if (CMODE == "practice") {
                                if (_a.score == 1) {
                                    $("#answer").removeClass("bg-incorrect");
                                    $("#answer").removeClass("bg-empty");
                                    $("#answer").addClass("bg-correct");
                                    $('#answer').attr("disabled","disabled");
                                    $("#answer").popover({content: '<span class=""><?php _e('Correct!', 'iii-dictionary') ?></span>', html: true});
                                    $('.popover').css("width","200px !important");
                                    $("#answer").popover("show");

                                } else {
                                    $("#answer").removeClass("bg-correct");
                                    $("#answer").removeClass("bg-empty");
                                    $("#answer").addClass("bg-incorrect");
                                    $('#answer').attr("disabled","disabled");
                                    $("#answer").popover({content: '<span class=""><?php _e('Incorrect!', 'iii-dictionary') ?></span>', html: true});
                                    $("#answer").popover("show");
                                }
                                setTimeout(function () {
                                    $("#answer").popover("destroy")
                                }, 1000);
                                save_answer();
                            } else {
                                save_answer(JSON.stringify(_a));
                            }
                            $('#answer').on('hidden.bs.popover', function () {
                                $('#answer').removeAttr("disabled");
                            })
                        }
                    }                 

                    } else if(next.hasClass("css-background-00a5bb")) {
                        $("#next-btn").click();
                    }
                }
            });
            $("#sel1").change(function(){
                $("#start-btn").addClass("css-background-00a5bb");
                $("#start-btn").removeClass("css-background-ff8a00");
                
                $("#submit-btn").addClass("css-background-ff8a00");
                $("#submit-btn").removeClass("css-background-00a5bb");
                
                $("#next-btn").removeClass("css-background-00a5bb");
                $("#next-btn").addClass("css-background-ff8a00");
                var id = $(this).val();
                $("#current-word").val(id);
                $("#word-hints").html('<span class="word-hints"><?php _e('HINT (Click here !)', 'iii-dictionary') ?></span>');
                $("#word-num").html(parseInt(id)+1);
//                    $("#answer").val(words[current_word_index + 1].selected);
                $('#answer').val("");
            });
        });
    })(jQuery);
</script>
<?php get_dict_footer() ?>
