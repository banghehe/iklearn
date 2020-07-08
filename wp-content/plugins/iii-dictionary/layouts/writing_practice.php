<?php
$current_user_id = get_current_user_id();
$page_title_tag = __('Writing Homework', 'iii-dictionary');
include IK_PLUGIN_DIR . '/library/formatter.php';

if(isset($_GET['hid'])) {
    $h_id = $_GET['hid'];
    $check = MWDB::check_homework_is_practive($h_id);
    $mode = $check[0]->for_practice == 1 ? "practice" : "homework";
} else {
    $mode = get_query_var('mode', 'practice');
}
$select_grade_sheets = $insql = $tmp = $js_homework_list = array();
$cur_sheet_index = $count = 0;
$sid = isset($_GET['sid']) ? $_GET['sid'] : 0;
$_return_url = base64_decode(rawurldecode($_GET['ref']));
$actual_link_current = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$actual_link = strstr($actual_link_current, '&');
$self_study_price = mw_get_option('self-study-price');
$view = $_GET["hid"] ;
if($view > 0) {
    MWDB::update_user_is_view_homework($view);
}
if (isset($_POST['request-grading'])) {
    $hrid = $_POST['hrid']; // homework result id
    $hid = $_POST['hid']; // homework id
    // request grading
    if($_POST['compare']!=1){
    if (ik_request_worksheet_grading($hrid, $hid, $current_user_id, 1)) {
        wp_redirect(locale_home_url() . '/?r=writing-practice');
        exit;
    }
    }
}
// is teacher taking a test?
$teacher_taking_test = in_array($sid, (array) $_SESSION['teacher_tests']);
$homework_sheets = MWDB::get_homework_sheets(ASSIGNMENT_WRITING);
//var_dump($homework_sheets);die;
$sheet_list = $homework_sheets;
ik_enqueue_js_messages('point_err', sprintf(__('Your current points is <strong>%d</strong> pts. You don\'t have enough points to request grading for this homework', 'iii-dictionary'), ik_get_user_points($current_user_id)));
if ($mode == 'practice') {
    $page_title_tag = __('Writing Practice', 'iii-dictionary');
    $practice_sheets = MWDB::get_practice_sheets(ASSIGNMENT_WRITING);

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
    wp_redirect(locale_home_url() . '/?r=writing-practice');
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
        $_disabled_js = ($item->homework_type_id == HOMEWORK_SUBSCRIBED && !get_ws_subscribed()) ? 'disabled' : '';
        if ($_disabled_js == '') {
            $_disabled_js = 'disabled';
            $class = 'classdisable';
        } else {
            $class = '';
        }
        if (is_mw_super_admin() || is_mw_admin()) {
            $_disabled = '';
        }

        $select_grade_sheets[$item->grade] .= '<option data-sheet-id="' . $item->sheet_id . '" class="' . $class . '" value="' . $item->sheet_name . '"' . $_disabled . '>' . $item->sheet_name . '</option>';
//        var_dump($sid);die;
//        if ($sid && $sid == $item->sheet_id) {
            $sheet = $item;

            // we only need 1 sheet in homework mode if sheet id is provided
            if ($mode == 'homework') {
                $js_homework_list = array();
                $sheet_list = array($item);
                break;
            }
//        }
        
        if ($mode == 'homework' && $sheet->homework_id != $item->homework_id) {
            $js_homework_list[] = '{hid: ' . $item->homework_id . ', sid: ' . $item->sheet_id . ', grade: "' . $item->grade . '", sheet_num: "' . $item->sheet_name . '"}';
        }
    }
}

// get next homework
if (!empty($homework_assignment->next_homework_id)) {
    $next_homework = MWDB::get_homework_assignment_by_id($homework_assignment->next_homework_id);
    $sheet->next_homework_id = $homework_assignment->next_homework_id;
    $sheet->next_sheet = empty($next_homework->name) ? $next_homework->sheet_name : $next_homework->name;
    $sheet->next_assignment_id = $next_homework->assignment_id;
}
$sheet_total = count($sheet_list);
//var_dump($sheet->questions);die;
$words = json_decode($sheet->questions, true);
$dict_table = get_dictionary_table($sheet->dictionary_id);

// load user answers
if (!empty($sheet->answers)) {
    $answers = json_decode($sheet->answers, true);
} else {
    $answers = json_decode($sheet->practice_answers, true);
}

$word_total = count($words['question']);
$_cur_word_index = 0;
if (isset($sheet->finished_question)) {
    $_cur_word_index = $sheet->finished_question + 1;
    if ($_cur_word_index == $word_total) {
        $_cur_word_index = 0;
    }
}

// generate javascript for default worksheet
$jsvar = 'var words = [];';
for ($i = 0; $i < $word_total; $i++) {
    $ans = '';
    if (isset($answers['q' . $i])) {
        $ans = $answers['q' . $i];
    }

    $jsvar .= 'words[' . $i . '] = {sentence: ' . json_encode($words['question'][$i]) .
            ', quiz: ' . json_encode($words['quiz'][$i]) .
            ', selected: ' . json_encode($ans) . '};';
}
?>
<?php get_dict_header($page_title_tag, 'green') ?>
<?php
$info_tab_url = get_info_tab_cloud_url('Popup_info_15.jpg');
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
<script>
    if ((window.matchMedia('screen and (max-width: 480px)').matches)) {
            jQuery('.col-sm-offset-1 h1:first-child').attr('style', 'color: #599180;margin-bottom: 4%;padding-top: 6%;');
        }    
</script>
<div class="row">
    <div class="col-sm-12">
        <h3 class="med-font-size"><?php _e('Write an essay', 'iii-dictionary') ?></h3>
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
    <div class="col-xs-12" id="quiz-box">
        <span id="quiz"></span>
    </div>
    <div class="col-xs-12">
        <div class="form-group select-box" id="writing-box">
            <label id="vocab-question"></label>
            <?php
            $settings = array(
                'wpautop' => false,
                'media_buttons' => false,
                'quicktags' => false,
                'textarea_rows' => 10,
                'tinymce' => array(
                    'toolbar1' => 'formatselect,bold,italic,underline,blockquote,alignleft,aligncenter,alignright,alignjustify,removeformat,charmap,outdent,indent,undo,redo,wp_help,fullscreen',
                    'toolbar2' => ''
                )
            );
            wp_editor("", 'writing_essay', $settings);
            ?>
        </div>
    </div>
</div>
<?php if ($actual_link == FALSE) { ?>
    <div class="row">
        <div class="col-sm-12">
            <label id="notifi"></label>
        </div>
        <div class="col-sm-12">
            <div style="float:left">
                <input id="rdo-agreed" class="checkboxagree" <?php if ($is_teaching_agreement_uptodate) echo 'checked'; ?> type="checkbox" name="agree-english-teacher" value="1" >
            </div>
            <div style="margin-top: 0.5%; color: white; ">
                <label style="padding-left: 3%;font-weight: bold">Request grading ( Click the box then do homework )</label>
            </div>
        </div>
    </div>
<?php } ?>
<div class="row" style="padding-top: 10px">
    <div class="col-sm-6">
        <div class="form-group">
            <button type="button" id="next-btn" class="btn btn-block css-background-ff8a00 css-color-fff css-height-45"><?php _e('Next', 'iii-dictionary') ?><span class="icon-next-new"></span></button>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <button type="button" id="submit-btn" class="btn btn-block css-background-00a5bb css-color-fff css-height-45"><?php _e('Submit Homework', 'iii-dictionary') ?><span class="icon-submit-new"></span></button>
        </div>
    </div>
</div>
<input type="hidden" id="current-word" value="0">
<input type="hidden" id="rid" value="<?php echo isset($sheet->homework_result_id) ? $sheet->homework_result_id : 0 ?>">
<input type="hidden" id="wtid" value="">
<input type="hidden" id="compareid" value="">
<input type="hidden" id="price" value="">


<div id="submit-lesson-modal-dialog" class="modal fade" data-keyboard="true" aria-hidden="true"<?php echo $teacher_taking_test ? ' data-backdrop="static"' : '' ?>>
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black" style="color: #000 !important">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3 style="color: #FBD582"><?php !$teacher_taking_test ? _e('Submitting Homework', 'iii-dictionary') : _e('The End of Test', 'iii-dictionary') ?></h3>
            </div>
            <?php if (!$teacher_taking_test) : ?>
                <div class="modal-body">
                    <div class="css-font-weight"><?php _e('You have completed this Homework', 'iii-dictionary') ?></div>
                    <hr style="border-top: 1px solid #D5D5D5;">
                    <div>
                        <span class="css-color-515151"><?php _e('Cost for grading:', 'iii-dictionary') ?><span><span style="color:#CE3156"> 5 Points.</span><span class="css-color-515151"> Click Submit to Proceed.</span><br>
                        <span class="txt-submit-writing">Your Currently have</span>
                        <span class="txt-submit-writing css-font-weight" id="total-point">
                        <?php
                        echo ' ' . ik_get_user_points($current_user_id) . ' ';
                        _e('points.', 'iii-dictionary');
                        ?>
                        </span>
                    </div>
                    
                    <div class="form-group">
                        <textarea class="form-control lv-feedback" id="txt-feedback" placeholder="<?php _e('Leave a Message to the Teacher (Optional)', 'iii-dictionary') ?>" style="resize: none"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <?php if (empty($sheet->next_homework_id)) : ?>
                            <div class="col-sm-6 form-group">
                                <button type="button" class="btn btn-block orange confirm submit-lesson-btn bt-create-fl" data-loading-text="<?php _e('Submitting...', 'iii-dictionary') ?>" data-ref="<?php echo $_return_url ?>"><?php _e('Submit', 'iii-dictionary') ?></button>
                            </div>
                            <div class="col-sm-6 form-group">
                                <button type="button" class="btn btn-block white bt-create-folder"  style="background:#B6B6B6 !important; color: #fff !important" data-dismiss="modal" ></span><?php _e('Quit', 'iii-dictionary') ?></button>
                            </div>
                        <?php else : ?>
                            <div class="col-sm-6 form-group">
                                <button type="button" class="btn btn-block orange confirm submit-lesson-btn bt-create-fl" data-loading-text="<?php _e('Submitting...', 'iii-dictionary') ?>" data-ref="<?php echo MWHtml::get_practice_page_url($sheet->next_assignment_id) . '&amp;mode=homework&amp;hid=' . $sheet->next_homework_id . '&amp;ref=' . $_GET['ref'] ?>"></span><?php _e('Submit', 'iii-dictionary') ?></button>
                            </div>
                            <div class="col-sm-6 form-group">
                                <button type="button" class="btn btn-block grey confirm submit-lesson-btn bt-create-fl" style="background:#B6B6B6 !important; color: #fff !important" data-loading-text="<?php _e('Submitting...', 'iii-dictionary') ?>" data-ref="<?php echo $_return_url ?>"></span><?php _e('Quit', 'iii-dictionary') ?></button>
                            </div>
                        <?php endif ?>
                    </div>
                    <span style="color: #A3A3A3;float: left;font-size: 17px;">Submit button will direct user to the Next Worksheet</span>
                </div>
            <?php else : ?>
                <div class="modal-body">
                    <?php _e('You have completed this test.', 'iii-dictionary') ?><br>
                    <?php _e('If you want to leave a message to the admin, type it in the box below.', 'iii-dictionary') ?><br>
                    <?php _e('Click OK to submit.', 'iii-dictionary') ?>
                    <hr>
                    <div class="form-group">
                        <textarea class="form-control" id="txt-feedback" placeholder="<?php _e('Leave feedback', 'iii-dictionary') ?>" style="resize: none"></textarea>
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
<div id="submit-lesson-writing-modal-dialog" class="modal fade modal-green" data-keyboard="true" aria-hidden="true"<?php echo $teacher_taking_test ? ' data-backdrop="static"' : '' ?>>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
                <h3><?php _e('The End of Homework', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-body">
                <?php _e('You have completed this test.', 'iii-dictionary') ?><br>
                <?php _e('If you want to leave a message to the admin, type it in the box below.', 'iii-dictionary') ?><br>
                <?php _e('Click OK to submit.', 'iii-dictionary') ?>
                <hr>
                <div class="form-group">
                    <textarea class="form-control" id="txt-feedback" placeholder="<?php _e('Leave feedback', 'iii-dictionary') ?>" style="resize: none"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <button type="button" class="btn btn-block orange confirm submit-lesson-writing-btn" data-loading-text="<?php _e('Submitting...', 'iii-dictionary') ?>"><span class="icon-accept"></span><?php _e('OK', 'iii-dictionary') ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


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
                            <a href="<?php echo $_return_url ?>" class="btn btn-block orange"><span class="icon-accept"></span><?php _e('OK', 'iii-dictionary') ?></a>
                        </div>
                    <?php else : ?>
                        <div class="col-sm-6 form-group">
                            <a href="<?php echo locale_home_url() . '/?r=writing-practice&amp;hid=' . $sheet->next_homework_id . '&amp;ref=' . $_GET['ref'] ?>" class="btn btn-block orange"><span class="icon-accept"></span><?php _e('Yes', 'iii-dictionary') ?></a>
                        </div>
                        <div class="col-sm-6 form-group">
                            <a href="<?php echo $_return_url ?>" class="btn btn-block grey"><span class="icon-cancel"></span><?php _e('Quit', 'iii-dictionary') ?></a>
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
                    <a href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></a>
                    <h3 style="color: #FBD582"><?php _e('Save to Folder', 'iii-dictionary') ?></h3>
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
                <input type="hidden" name="group-pass" value="<?php echo $self_study_group ?>">
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
<form method="post" action="<?php echo locale_home_url() ?>/?r=writing-practice" id="main-form" enctype="multipart/form-data">
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
                <?php if(!empty($_GET["hid"])){?>
                    <input type="hidden" id="hid_check" value="<?php echo $_GET["hid"]?>">
                <?php } ?>
                <input type="hidden" name="compare" id="compare">
            </div>
        </div>
    </div>
</form>
<div id="modal-message-enough-point" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content modal-content-custom">
                <div class="modal-header custom-header">
                    <span style="right: 3%;padding-top: 4% !important;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                    <h3 style="padding-left: 0%"><?php _e('Error', 'iii-dictionary') ?></h3>
                </div>
                <div class="modal-body body-custom">
                    <span>You don't have enough point.</span>
                </div>
        </div>
    </div>
</div>
<script>
    var CMODE = "<?php echo $mode ?>";
    var COUNTQUESTION = "<?php echo is_null($word_total) ? 0 : $word_total ?>";
    var CHECK = "<?php echo is_bool($actual_link) ? 0 : $actual_link ?>";
    if (!isuserloggedin) 
        var DIS = "<?php echo $_disabled_js ?>";
    else
        var DIS = "";
    var DISNULL = "<?php echo $_disabled ?>";
    var price = <?php echo is_null($sheet->grading_price) ? 0 : $sheet->grading_price // homework pricec         ?>;
    var csid = <?php echo is_null($sheet->sheet_id) ? 0 : $sheet->sheet_id // current sheet id          ?>;
    var pid = <?php echo is_null($sheet->pid) ? 0 : $sheet->pid // practice result id          ?>;
    var ptid = <?php echo is_numeric($_GET['hid']) ? $_GET['hid'] : 0 ?>;
    var current_homework_id = <?php echo!empty($homework_assignment->id) ? $homework_assignment->id : 0 ?>;
<?php echo $jsvar ?>
<?php if ($mode == 'homework') : ?>
        var homework_list = [<?php echo implode(',', $js_homework_list) ?>];
        var current_homework_id = <?php echo!empty($homework_assignment->id) ? $homework_assignment->id : 0 ?>;
<?php endif ?>
    (function ($) {
        
        $(function () {
            var all_answer = []; // array store all answer when next
            $("#sheet-num").on('change', function (e) {
                e.preventDefault();
                var id_homework = $("#sheet-num option:selected").attr('data-sheet-id')
                $.post(home_url + "/?r=ajax/getidhomework",
                        {homework_id: id_homework},
                        function (data) {
                            var data = JSON.parse(data);
                            if (data.idcompare != null) {
                                $('#wtid').val(data.idcompare.id);
                                $('#compareid').val(1);
                                
                            } else {
                                $('#compareid').val(0);
                                if (data.id != null) {
                                    $('#wtid').val(data.id.id);
                                } else {
                                    $('#wtid').val(0);
                                }
                            }

                            if (data.price != null) {
                                var price = data.price.grading_price;
                                $('#notifi').text('The grading of this writing practice costs ' + price + ' point. The graded result will be in your Student Box. The correction and suggestion will be included in the graded result.');
                                $('#price').val(data.price.grading_price);
                            } else {
                                $('#price').val('0');
                                $('#notifi').text('The grading of this writing practice costs ' + '(not price)' + ' point. The graded result will be in your Student Box. The correction and suggestion will be included in the graded result.');
                            }

                        }
                );
            });
            $("#grade").on('change', function (e) {
                e.preventDefault();
                var id_homework = $("#sheet-num option:selected").attr('data-sheet-id')
                $.post(home_url + "/?r=ajax/getidhomework",
                        {homework_id: id_homework},
                        function (data) {
                            var data = JSON.parse(data);
                            if (data.price != null) {
                                var price = data.price.grading_price;
                                $('#notifi').text('The grading of this writing practice costs ' + price + ' point. The graded result will be in your Student Box. The correction and suggestion will be included in the graded result.');
                                $('#price').val(data.price.grading_price);
                            } else {
                                $('#price').val('0');
                                $('#notifi').text('The grading of this writing practice costs ' + '(not price)' + ' point. The graded result will be in your Student Box. The correction and suggestion will be included in the graded result.');
                            }
                            if (data.idcompare != null) {
                                $('#wtid').val(data.idcompare.id);
                                $('#compareid').val(1);
                            } else {
                                $('#compareid').val(0);
                                if (data.id != null) {
                                    $('#wtid').val(data.id.id);
                                } else {
                                    $('#wtid').val('0');
                                }
                            }
                        }
                );
            });


            if (CMODE == "homework") {
                is_all_questions_answered();
            }
            function is_all_questions_answered() {
                var $answered = true;
                $.each(words, function (i, v) {
                    if (v.selected == "") {
                        $answered = false;
                    }
                });
                if ($answered) {
                    $("#submit-test").parent().parent().removeClass("hidden");
                }
            }

            setup_question();
            $("#sheet-num").html($("#sheet-num-" + $("#grade").val()).html());
            $("#sheet-num").data("selectBox-selectBoxIt").refresh();
            $("#sheet-num").data("selectBox-selectBoxIt").selectOption(<?php echo $sid ? '"' . $sheet->sheet_name . '"' : 0 ?>);

            $("#reset-counter").click(function () {
                var existclass = $("#sheet-num option:selected").hasClass('classdisable');
                if (DIS != '' && existclass == false && window.location.href.indexOf("&pr=")<=-1) {
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
                    $("#current-word").val(0);
                    setup_question();
                }
            });
            var ssp = <?php echo (int)$self_study_price ?>;
            function price_self_study() {
                    var months = isNaN(parseInt($("#sel-self-study-months1").val())) ? 0 : parseInt($("#sel-self-study-months1").val());                 
                    $("#ss-total-amount").text(months * ssp);
                }
            $('#ok-modal-req-sub').click(function (){
                if(!isuserloggedin) {
                    $('#require-modal1').modal('hide');
                    jQuery('#show_login').click();
                } else {                
                    $('#require-modal1').modal('hide');
                    $('#self-study-subscription').modal('show');
                }
            });
            $('#add-to-cart-ss').click(function (e){
                $selected = $('#sel-dictionary2 option:selected');
                if($selected.val() == '') {
                    e.preventDefault();
                    $selbox = $("#sel-dictionary2SelectBoxIt");
                    $selbox.popover({content: '<span class="text-danger">' +'You not selected dictionary'+ '</span>', html: true, trigger: "hover", placement: "bottom"}).popover("show");
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
            $("#next-btn").click(function () {
                var existclass = $("#sheet-num option:selected").hasClass('classdisable');
                if (DIS != '' && existclass == false && window.location.href.indexOf("&pr=")<=-1) {
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
                    save_answer($(this));
                    var current_word_index = parseInt($("#current-word").val());
                    if (current_word_index == (words.length - 1)) {
                        current_word_index = -1;
                    }
                    if (current_word_index == (words.length - 2)) {
                        $("#submit-btn").prop("disabled", false);
                    }
                    $("#submit-btn").button("reset");
                    $("#current-word").val(current_word_index + 1);
                    $("#word-num").html(current_word_index + 2);
                    setup_question();
                    if (CMODE == "homework") {
                        is_all_questions_answered();
                    }
                    tinymce.activeEditor.setContent('');
                    $('#sel1').val(parseInt(current_word_index)+1).trigger('change');
                } 
            });

            function save_answer(button) {
                var tthis = button;
                var $popover = $("#wp-writing_essay-wrap");
                var content = tinyMCE.activeEditor.getContent();
                var q = $("#sel1").val();
                var stt = "q"+q; // get example q1;
                all_answer[stt] = content;   // add answer on array store
                $popover.popover("destroy");
                if (content.trim() == "") {
                    $popover.popover({content: '<span class="popover-alert"><?php _e('Please write your essay!', 'iii-dictionary') ?></span>', html: true, placement: "top"});
                    $popover.popover("show");
                    setTimeout(function () {
                        $popover.popover("destroy")
                    }, 1500);
                } else {
                    tthis.button("loading");
                    words[$("#current-word").val()].selected = content;
                    if ($('input.checkboxagree').is(':checked')) {
                        $.post(home_url + "/?r=ajax/homework/answer",
                                {rid: $("#wtid").val(), homework_id: csid, qc: COUNTQUESTION, q: $("#current-word").val(), answer: content, graded: 0, writing: 1},
                                function (data) {
                                    tthis.button("reset");
                                    is_all_questions_answered();
                                    var id_homework = $("#sheet-num option:selected").attr('data-sheet-id')
                                    $.post(home_url + "/?r=ajax/getidhomework",
                                            {homework_id: id_homework},
                                            function (data) {
                                                var data = JSON.parse(data);
                                                if (data.price != null) {
                                                    var price = data.price.grading_price;
                                                    $('#notifi').text('The grading of this writing practice costs ' + price + ' point. The graded result will be in your Student Box. The correction and suggestion will be included in the graded result.');
                                                    $('#price').val(data.price.grading_price);
                                                } else {
                                                    $('#price').val('0');
                                                    $('#notifi').text('The grading of this writing practice costs ' + '(not price)' + ' point. The graded result will be in your Student Box. The correction and suggestion will be included in the graded result.');
                                                }
                                                if (data.idcompare != null) {
                                                    $('#wtid').val(data.idcompare.id);
                                                    $('#compareid').val(1);
                                                } else {
                                                    $('#compareid').val(0);
                                                    if (data.id != null) {
                                                        $('#wtid').val(data.id.id);
                                                    } else {
                                                        $('#wtid').val(0);
                                                    }
                                                }
                                            }
                                    );
                                }
                        );
                    } else {
                        if (CMODE == "homework") {
                            var id=$('#hid_check').val();
//                            console.log(all_answer);
                            $.post(home_url + "/?r=ajax/homework/answer",
                                    {hid:id,rid: $("#rid").val(), homework_id: current_homework_id, qc: words.length, q: $("#current-word").val(), answer: all_answer, graded: 0, writing: 1},
                                    function (data) {
                                        alert(data);
                                        tthis.button("reset");
                                        var data = JSON.parse(data);
                                        $("#rid").val(data);
                                        is_all_questions_answered();
                                    }
                            );
                        } else {
                            $.post(home_url + "/?r=ajax/practice/save",
                                    {pid: pid, ptid: ptid, sid: csid, q: $("#current-word").val(), answer: content},
                                    function (data) {
                                        tthis.button("reset");
                                        data = JSON.parse(data);
                                        pid = data[0];
                                    }
                            );
                        }
                    }
                }
                $('#mceu_22').hide();
            }

            $("#submit-btn").click(function () {
                if ($('input.checkboxagree').is(':checked')) {
                    var existclass = $("#sheet-num option:selected").hasClass('classdisable');
                    if (DIS != '' && existclass == false && window.location.href.indexOf("&pr=")<=-1) {
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
                        save_answer($(this));
                        $("#submit-lesson-writing-modal-dialog").modal();
                        $("#txt-feedback").focus();
                    }
                } else {
                    var existclass = $("#sheet-num option:selected").hasClass('classdisable');
                    if (DIS != '' && existclass == false  && window.location.href.indexOf("&pr=")<=-1) {
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
                        var $popover = $("#wp-writing_essay-wrap");
                        var content = tinyMCE.activeEditor.getContent();
                        if (content.trim() == "") {
                            $popover.popover({content: '<span class="popover-alert"><?php _e('Please write your essay!', 'iii-dictionary') ?></span>', html: true, placement: "top"});
                            $popover.popover("show");
                            setTimeout(function () {
                                $popover.popover("destroy")
                            }, 1500);
                        } else {
                        $("#submit-lesson-modal-dialog").modal();
                        $("#txt-feedback").focus();
                        }
                    }
                }
            });

            $(".submit-lesson-btn").click(function (e) {
                e.preventDefault();
                var tthis=$(this);
                var existclass = $("#sheet-num option:selected").hasClass('classdisable');
                if (DIS != '' && existclass == false && window.location.href.indexOf("&pr=")<=-1) {
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
                // get and split point current
                    var string = $('#total-point').html();
                    var point = parseInt(string);
                    if(point>=5) {
                        save_answer($(this));
                        <?php if ($teacher_taking_test) : ?>
                            window.location.href = home_url + "/?r=teaching/teach-class";
                        <?php else : ?>
                            window.location.href = tthis.attr("data-ref");
                        <?php endif ?>
                    }else {
                        $('#submit-lesson-modal-dialog').modal("hide");
                        $('#modal-message-enough-point').modal("show");
                    }
                }    
            });
            
            $(".submit-lesson-writing-btn").click(function (e) {
                e.preventDefault();
                var tthis = this;
                var existclass = $("#sheet-num option:selected").hasClass('classdisable');
                if (DIS != '' && existclass == false && window.location.href.indexOf("&pr=")<=-1) {
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
                    var tthis = $(this);
                    tthis.button("loading");
                    $.post(home_url + "/?r=ajax/homework/submit",
                            {homework_id: csid, rid: $("#wtid").val(), feedback: $("#txt-feedback").val()},
                            function (data) {
                                tthis.button("reset");
                                $("#grading-cost").text($('#price').val());
                                $("#hrid").val($("#wtid").val());
                                $("#hid").val(csid);
                                $("#compare").val($("#compareid").val());
                                $('#request-grading-dialog').modal('show');
//                                    window.location.href = home_url + "/?r=writing-practice";
                            }
                    );
                }
            });

            $("#grade").on("option-click", function () {
                $("#sheet-num").html($("#sheet-num-" + $(this).val()).html());
                $("#sheet-num").data("selectBox-selectBoxIt").refresh();
                $("#sheet-num").trigger("option-click");
                $("#sel1").selectBoxIt('selectOption', '0'.toString()).data("selectBox-selectBoxIt");
                $("#sel1").data("selectBox-selectBoxIt").refresh();
            });
            $("#sheet-num").on("option-click", function () {
                var existclass = $("#sheet-num option:selected").hasClass('classdisable');
                if (DIS != '' && existclass == false && window.location.href.indexOf("&pr=")<=-1) {
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
                    $(".words-pagin").hide();
                    $(".loading").fadeIn();
                    $("#submit-btn").prop("disabled", true);
                    $("#next-btn").prop("disabled", true);
                    var sid = $("#sheet-num :selected").attr("data-sheet-id");
                    $.getJSON(home_url + "/?r=ajax/question",
                            {sid: sid, cmod: CMODE},
                            function (data) {

                                csid = sid;
                                pid = data.pid;
                                $("#word-total").html(0);
                                words = [];
                                if (data.sheet[0] != null) {
                                    words = data.sheet[0];
                                    $("#word-total").html(data.sheet[0].length);
                                }
                                $("#reading-passage").html(data.sheet.passage);
                                $("#word-num").html(1);
                                $("#htype-id").text(data.htype);
                                $("#submit-btn").prop("disabled", false);
                                $("#next-btn").prop("disabled", false);
                                $("#current-word").val(0);
                                $(".loading").hide();
                                $(".words-pagin").fadeIn();
                                setup_question();
                            }
                    );
                }
            });
            function setup_question() {
                if (typeof words[$("#current-word").val()] != "undefined") {
                    $("#vocab-question").html(words[$("#current-word").val()].sentence.replace(/(?:\r\n|\r|\n)/g, "<br>"));
                    $("#quiz").html(words[$("#current-word").val()].quiz);
                    if (tinyMCE.activeEditor) {
                       // tinyMCE.activeEditor.setContent(words[$("#current-word").val()].selected);
                    }
                } else {
                    $("#vocab-question").html("");
                    $("#quiz").html("");
                    if (tinyMCE.activeEditor) {
                        tinyMCE.activeEditor.setContent("");
                    }
                }
            }
            $("#sel1").change(function(){
                var id = $(this).val();
                $("#current-word").val(id);
                $("#word-hints").html('<span class="word-hints"><?php _e('HINT (Click here !)', 'iii-dictionary') ?></span>');
                $("#word-num").html(parseInt(id)+1);
                setup_question();
                $('#answer').val("");
            });
        });
    })(jQuery);
</script>
<script>
     jQuery( document ).ready(function() {
         timeout = setTimeout(function () {
            jQuery('#mceu_22').hide();
                    }, 100);
            
        });
</script>
<style>
    .checkboxagree{
        width: 28px;
        height: 28px;
        border-radius: 50px;
        box-shadow: inset 0px 1px 1px white, 0px 1px 3px rgba(0,0,0,0.5);
        position: relative;
    }
</style>
<?php get_dict_footer() ?>
