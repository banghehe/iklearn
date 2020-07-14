<?php
$route = get_route();

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
    // get Name 
    $homework_assignment = MWDB::get_homework_assignment_by_id($id);
    $sheet_id = $homework_assignment->sheet_id;
    $homework = MWDB::get_math_sheet_by_id($sheet_id);
    $questions = json_decode($homework->questions, true);
    $result_type = $homework->assignment_id;
    $name = MWDB::get_name_user($id);
    if($homework_assignment->for_practice ==1) {
        $result = MWDB::get_result_worksheet_practive($id,$id_g); 
    }else {
        $result = MWDB::get_result_worksheet_homework($id); 
    }
    $question = json_decode($result->questions);
    $answer = json_decode($result->answers);
    $user_avatar = ik_get_user_avatar($homework_result[0]->graded_by);
               
    $html='';
    $html .= '<span class="css-name-sheet">'.$questions['question'].'</span>';
    $html .='<div style="padding: 1% 5% 1% 5%;">';
    if (!empty($user_avatar)) :
        $html .='<img src="'. $user_avatar . '" width="130" height="140" alt="" class="css-image-user-load-db">';
    else :
        $html .='<div class="css-image-user"></div>';
    endif;
    $html .= '<div class="result-info">';
    $html .='<div><span class="css-7D7D7D">'."Teacher: ".'</span>';
    $html .='<span class="css-7C7C7C">'."N/A".'</span></div>';
    $html .='<div><span class="css-7D7D7D">'."Student's Name: ".'</span>';
    $html .='<span class="css-7C7C7C">'.$name->user_nicename.'</span></div>';
    $html .='<div><span class="css-7D7D7D">'."Level: ".'</span>';
    $html .='<span class="css-7C7C7C">'.$result->lv.'</span></div>';
    $html .='<div><span class="css-7D7D7D">'."Dictionary: ".'</span>';
    if($result->libname == '') {
        $html .='<span class="css-7C7C7C">'."N/A".'</span>';
    }else {
        $html .='<span class="css-7C7C7C">'.$result->libname.'</span>';
    }
    $html .= '</div>';
    $html .='<div><span class="css-7D7D7D">'."Last Attempt: ".'</span>';
    $html .='<span class="css-7C7C7C">'.$name->attempted_on.'</span></div>';
    
    $html .='<div class="css-mobile-display">';
    $html .='<div style="width:84%"><span class="css-7D7D7D">'."Completed Date: ".'</span>';
    $html .='<span class="css-7C7C7C">'.$name->submitted_on.'</span></div>';
    $html .='<div class="css-mobile-right"><span class="css-rs-score" style="color: #909090;">'."Score: ".'</span>';
    if($result->score != 0) {
        $html .='<span class="rs-score1" style="color: #00a6bc;">'.$result->score.'%</span></div>';
    } else {
        $html .='<span class="rs-score1" style="color: #00a6bc;">Not Graded</span></div>';
    }
    $html .= '</div>';
    
    $html .= '</div>';
    
    $html .= '<div class="line-result"></div>';
//    foreach load table question
    $html .= '<div style="width: 100%">';
// table    
    $html .= '<table class="table table-striped table-condensed ik-table1 scroll-fix-head1 vertical-middle" id="homeworkcritical">';
//thread    
        $html .=  '<thead class="homeworkcritical" style="background: #fff;">';
            $html .= '<tr style="background: #fff;">';
                $html .= '<th class="text-color-custom-1 css-th2-question" style="color: #00a6bc !important;">CORRECT ANSWER</th>';
                $html .= '<th class=" text-color-custom-1 css-th3-question" style="width: 18% !important;color: #909090 !important;">YOUR ANSWER</th>';
                $html .= '<th class="" style="width: 20% !important" ></th>';
            $html .= '</tr>';
        $html .= '</thead>';
//tbody    
//        $html .='<tbody style="height:250px !important";>'; 
            
            $arr_ansers = json_decode($result->answers,true); // conver json -> array
            if($arr_ansers ==""){$arr_ansers=$result->answers;}
            
            // Get dữ liệu ra loại 1 toán gồm các Add and Sub & Single Digit Multiplication / Two Digit Multiplication / Long Division by Single Digit / Long Division by Two Digits
            if($result_type ==7 || $result_type ==8 || $result_type ==9 || $result_type ==10){
                
                    // get answer from wp_dict_practice_results;
                    $data_answer = MWDB::get_answer_practive($id);
                    $obj_an = json_decode($data_answer->answers);
                    $arr_an =  (array) $obj_an;

    //                var_dump($arr_an);
                    // get question from wp_wp_dict_sheets;
                    $data_ques = MWDB::get_question_sheet($sheet_id);
    //                $as = $data_answer->answers;
                    $obj_answer_correct = json_decode($data_ques->questions);
                    // Set data tr 
                    $obj_step = $obj_answer_correct->step;
    //                var_dump($obj_step);
                    $arr_step =  (array) $obj_step;
                    if(count($arr_step)==3){
                        $html .='<tbody style="height:119px !important";>';
                    }else{
                        $html .='<tbody style="height:250px !important";>';
                    }
                    foreach ($arr_step as $key => $value) {
                        $step_ans = "";
                        $str_new = "";
                        $html .= '<tr>';
    //                    var_dump(explode("",$value));var_dump($split);
                        $split = str_split($value);
                        foreach ($split as $value1) {
                            if($value1 !='@')
                            $str_new .= $value1.',';
                        }
                        $str_new1 = rtrim($str_new,",");
    //                    var_dump($str_new1);die;
                        $html .='<td class="row-full-1 padding-left-3" style="width:50%! important;color: #007382 !important;" >'.$str_new1.'</td>';
                        $html .='<td class="row-full-1 css-url-ic" style="width:1%! important;"></td>';
                        foreach ($arr_an as $key1 => $value1) {
                            $str_res = substr($key1, 7, 2); 
                            if($key == $str_res) {
                                $step_ans .= $value1.",";
                            }
    //                        var_dump($str_res);
                        }
    //                    var_dump($str_res);die;
                        $step_ans1 = rtrim($step_ans,",");
                        $html .='<td class="row-full-1" style="width:48%! important;padding-left: 2% !important;">'.$step_ans1.'</td>';
                        $html .= '</tr>';
                    }
                    
        // Get dữ liệu ra loại 2 toán gồm FRACARD AND EQUATION       
            }else if($result_type ==11 || $result_type ==15){
                $html .='<tbody style="height:250px !important";>';
                    $html .= '<tr>';
                    $oj_ques = json_decode($result->questions,true);
                    $oj_ques1 = (object)$oj_ques;
                    $oj_ques2 = $oj_ques1->q;  // array vd: 20 element
                    $oj = (object)$questions;
                    $oj_new = (object)$oj->q;
                    for($i =1; $i<=count($oj_ques2);$i++) {
                        $html .= '<tr>';
                        $q_id = "q".$i;
                        $ar_string = $oj_new->$q_id;
                        $html .='<td class="row-full-1 padding-left-5" style="width:50%! important;">'.$ar_string["answer"].'</td>';
                        $html .='<td class="row-full-1 css-url-ic" style="width:1%! important;"></td>';
                        $html .='<td class="row-full-1" style="width:48%! important;padding-left: 5% !important;">'.$arr_ansers[$q_id].'</td>';
                        $html .= '</tr>';
                    }
                    $html .= '</tr>';
                    
        // Get dữ liệu ra loại 2 toán gồm FRACARD AND EQUATION       
            }else if($result_type ==12){
                $html .='<tbody style="height:250px !important";>';
                    $html .= '<tr>';
                    $data_answer = MWDB::get_answer_practive($id);
                    $obj_an = json_decode($data_answer->answers);  // Object array answer
                    
                    $oj_ques = json_decode($result->questions,true);
                    $oj_ques1 = (object)$oj_ques;
                    $oj_ques2 = $oj_ques1->q;  // array vd: 20 element
                    $oj = (object)$questions;
                    $oj_new = (object)$oj->q;
                    for($i =1; $i<=count($oj_ques2);$i++) {
                        $j = 'q'.$i;
                        $html .= '<tr>';
                        $q_id = "q".$i;
                        $ar_string = $oj_new->$q_id;
                        $html .='<td class="row-full-1 padding-left-5" style="width:50%! important;">'.$ar_string["answer"].'</td>';
                        $html .='<td class="row-full-1 css-url-ic" style="width:1%! important;"></td>';
                    // get each answer fomat vd: 1,9,3    
                        $arr_a = $obj_an->$j;
                        $ans3 = "";
                        foreach ($arr_a as $value) {
                            $ans3 .=$value.",";
                        }
                        $str_new3 = rtrim($ans3,",");
                        $html .='<td class="row-full-1" style="width:48%! important;padding-left: 4% !important;">'.$str_new3.'</td>';
                        $html .= '</tr>';
                    }
                    $html .= '</tr>';
            } else if($result_type ==13){
                $html .='<tbody style="height:40px !important";>';
                $html .= '<tr>';
                if($questions['answer']=="" || $questions['answer']==0) {
                    $html .='<td class="row-full-1 padding-left-3" style="width:50%! important;color: #007382 !important;" >'."N/A".'</td>';
                }else{
                    $html .='<td class="row-full-1 padding-left-3" style="width:50%! important;color: #007382 !important;" >'.$questions['answer'].'</td>';
                }
                $html .='<td class="row-full-1 css-url-ic" style="width:1%! important;"></td>';
                $html .='<td class="row-full-1" style="width:48%! important;padding-left: 2% !important;">'.$arr_ansers.'</td>';
                $html .= '</tr>';
            }
            else if($result_type ==14){
                $html .='<tbody style="height:40px !important";>';
                $oj_ques5 = json_decode($homework->questions);
                $ques5 = $oj_ques5->q;
                $arr_ques5 =  (array) $ques5;
                $str_ques_new5 = "";
                foreach ($arr_ques5 as $value) {
                    $strnew5 = $value->answer;
                    $str_ques_new5 .= $strnew5.",";
                }
                $str_ques_new5 = rtrim($str_ques_new5,",");
                $html .= '<tr>';
                $data_answer = MWDB::get_answer_practive($id);
                $obj_an = json_decode($data_answer->answers,true);  // Object array answer
//                var_dump($obj_an);die;
                $str5 ="";
                if($obj_an !=null){
                    foreach ($obj_an as $value) {
                        $str5 .=  $value.",";
                    }
                }
                $str_new5 = rtrim($str5,",");
                $html .='<td class="row-full-1 padding-left-5 css-txt-correct" >'.$str_ques_new5.'</td>';
                $html .='<td class="row-full-1 css-url-ic" style="width:1%! important;"></td>';
                $html .='<td class="row-full-1 padding-left-5 css-txt-answ" >'.$str_new5.'</td>';
                $html .= '</tr>';
            }
            else {
                $html .= '<tr>';
                $oj_ques = json_decode($result->questions,true);
                $oj_ques1 = (object)$oj_ques;
                $oj_ques2 = $oj_ques1->q;  // array vd: 20 element
                $oj = (object)$questions;
                $oj_new = (object)$oj->q;
                for($i =1; $i<=count($oj_ques2);$i++) {
                    $html .= '<tr>';
                    $q_id = "q".$i;
                    $html .='<td class="row-full-1 padding-left-5" style="width:50%! important;">'.$arr_ansers[$q_id].'</td>';
                    $html .='<td class="row-full-1 css-url-ic" style="width:1%! important;"></td>';
                    $ar_string = $oj_new->$q_id;
                    $html .='<td class="row-full-1" style="width:49%! important;padding-left: 4% !important;">'.$ar_string["answer"].'</td>';
                    $html .= '</tr>';
                }
                $html .= '</tr>';
            }
            
        $html .='</tbody>';
// end table        
    $html .= '</table>';
    $html .= '</div>';
    $html .= '<input type="button" value="Preview Worksheet" class="css-btn-priview" id="preview-btn-math" data_id="'.$homework->id.'">';
    $html .= '<div class="line-result"></div>';
    echo $html;
    die;
}
// Load data when click button priview Worksheet
if($task == "load_preview_modal") {
    $sid = $_REQUEST['sid'];
    $homework = MWDB::get_math_sheet_by_id($sid); 
    $questions = json_decode($homework->questions, true);
    $curr_mode = empty($_GET['mode']) ? 'practice' : $_GET['mode'];
    $gidlink=esc_html(base64_decode(rawurldecode($_GET['ref'])));
    $gidsub = strstr($gidlink,'gid=');
    $getgroup_id = substr($gidsub,4);
    $checkdisplay=  MWDB::get_display_last_page($sheet_id, $getgroup_id);
    if($checkdisplay!=null){
    $admindp=$checkdisplay->adminlastpage;
    $teacherdp=$checkdisplay->teacherlastpage;
    }else{
        $admindp=2;
        $teacherdp=2;
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
    $html .= '<div class="modal-header col-xs-9 col-sm-10 article-header math-homework-header css-head-preview-modal">';
    $html .= '<span style="right: 2% !important;padding-top: 3%;" class="close close-dialog hidden-modal-preview"></span>';
    $html .= '<h4 class="page-subtitle css-title-modal1">'.$homework->level_category_name.'</h4>';
    $html .= '<h2 class="page-title arithmetics css-title-modal2" itemprop="headline" >'.$homework->level_name . ', ' . $homework->sublevel_name.'</h2>';
    $html .= '<p class="math-question css-title-modal3">'.$questions['question'].'</p>';        
    $html .= '</div>';
    $html .= '<div class="modal-body green" style="padding: 0px">';
    $html .= '<div class="col-xs-12 math-homework-body">';
    
    $html .= '<div class="row">';
    $html .= '<form id="main-form" method="post" action="">';
    $html .= '<div class="col-sm-2 homework-nav" style=" height: 420px;float: right;background: #28423A;">';
    switch ($homework->assignment_id):
        case MATH_ASSIGNMENT_SINGLE_DIGIT:
        case MATH_ASSIGNMENT_TWO_DIGIT_MUL:
        case MATH_ASSIGNMENT_SINGLE_DIGIT_DIV:
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
        $html .= '<h5 class="nav-title css-question-modal">';
        $html .= 'Steps:';
        $html .= '</h5>';
        $html .= '<div class="scroll-list-v" style="max-height: 380px;">';
        $html .= '<ul class="nav-items" id="answer-steps">';
        $loop_step = 1;
        $loop_count = count($no_steps);
        if (in_array($homework->assignment_id, array(MATH_ASSIGNMENT_SINGLE_DIGIT_DIV, MATH_ASSIGNMENT_TWO_DIGIT_DIV))) {
            $loop_step = 2;
            $loop_count = $loop_count % 2 == 0 ? $loop_count : $loop_count - 1;
        }
        $li_count = 1;
        for ($i = 0; $i < $loop_count; $i = $i + $loop_step) :
            if ($i == count($no_steps) - 1) {
                $nav_li_class[] = 'nlast';
            }
        $html .= '<li data-n="'.$no_steps[$i].'"';
        if($nav_li_class !=''){
        $html .= 'class="'.implode(' ', $nav_li_class).'"';
        }else{
            $html .='';
        }
        $html .= $li_count;
        $html .= '</li>';
        $li_count++;
        endfor;
        $html .= '</ul>';
        $html .= '</div>';
        break; // end add, sub, mul, div assignment

            case MATH_ASSIGNMENT_FLASHCARD:
            case MATH_ASSIGNMENT_FRACTION:
            case MATH_ASSIGNMENT_EQUATION:
        $html .= '<h5 class="nav-title css-question-modal">';
        $html .= 'Question:';
        $html .= '</h5>';
        if(count($questions['q'])<10) {
            $html .= '<div class="" style="max-height: 380px">';
            $html .= '<ul class="nav-items" id="question-nav">';
            for ($i = 1; $i <= count($questions['q']); $i++) :
            if($homework->assignment_id == MATH_ASSIGNMENT_FLASHCARD && $homework->answer_time_limit){
            
                $html .= '<li class="not-active" data-n="'.$i.'">'.$i.'</li>';
            }else{
                $html .= '<li data-n="'.$i.'">'.$i.'</li>';
            }
            $html .= '</ul>';
            endfor;
        }else{
            $html .= '<div class="" style="max-height: 340px;overflow: auto">';
            $html .= '<ul class="nav-items" id="question-nav">';
            for ($i = 1; $i <= count($questions['q']); $i++) {
                if( $homework->assignment_id == MATH_ASSIGNMENT_FLASHCARD && $homework->answer_time_limit){
                $html .= '<li class="not-active" data-n="'.$i.'">'.$i.'</li>';
                }else{
                    $html .= '<li data-n="'.$i.'">'.$i.'</li>';
                }
                $html .= '</ul>';
            }
        }
        break;
        case MATH_ASSIGNMENT_WORD_PROB:
            foreach ($questions['q'] as $key => $item) {
                if (empty($item['image']) || trim($item['image']) == '') {
                    unset($questions['q'][$key]);
                }
            }
        $html .= '<h5 class="nav-title css-question-modal">'."Steps:".'</h5>';
        if(count($questions['q']) >10) {
            $html .= '<div class="" style="max-height: 340px;overflow: auto">';
        }else{
            $html .= '<div class="" style="max-height: 364px">';
        }
        $html .='<ul class="nav-items" id="step-nav">';
        if(($admindp==1 && $teacherdp==0)||($admindp==0 && $teacherdp==0)||($admindp==0 && $teacherdp==1)){ 
                if ($questions['answer'] == 'no answer' || $questions['answer'] == 'No answer' || $questions['answer'] == 'No Answer' || $questions['answer'] == 'noanswer') {
                    for ($i = 1; $i < count($questions['q']); $i++) {
                        if(!empty($nav_li_class)){
                            $html .='<li data-n="'.$i.'" data-ctrl="'.$questions['q']['q' . $i]['param'].'class="' . implode(' ', $nav_li_class) . '"'.'>'.$i.'</li>';
                        }else{
                            $html .='<li data-n="'.$i.'" data-ctrl="'.$questions['q']['q' . $i]['param'].'"'.'>'.$i.'</li>';
                        }
                    }
                }else{
                    if ($i == $j) {
                        if(!empty($nav_li_class)){
                            $html .= '<li data-n="'.$i.'" class="last-step" data-ctrl="'.$questions['q']['q' . $i]['param'].'" class="' . implode(' ', $nav_li_class) . '"'.'>'.$i.'</li>';
                        }else{
                            $html .= '<li data-n="'.$i.'" class="last-step" data-ctrl="'.$questions['q']['q' . $i]['param'].'">'.$i.'</li>';
                        }
                    }else{
                        if(!empty($nav_li_class)){
                            $html .= '<li data-n="'.$i.'" data-ctrl="'.$questions['q']['q' . $i]['param'].'" class="' . implode(' ', $nav_li_class) . '"'.'>'.$i.'</li>';
                        }else{
                            $html .= '<li data-n="'.$i.'" data-ctrl="'.$questions['q']['q' . $i]['param'].'">'.$i.'</li>';
                        }
                    }
                }
        }else{
            $j = count($questions['q']); 
            for ($i = 1; $i <= count($questions['q']); $i++) {
                if ($questions['answer'] == 'no answer' || $questions['answer'] == 'No answer' || $questions['answer'] == 'No Answer' || $questions['answer'] == 'noanswer') {
                        if(!empty($nav_li_class)){
                            $html .= '<li data-n="'.$i.'" data-ctrl="'.$questions['q']['q' . $i]['param'].'" class="' . implode(' ', $nav_li_class) . '"'.'>'.$i.'</li>';
                        }else{
                            $html .= '<li data-n="'.$i.'" data-ctrl="'.$questions['q']['q' . $i]['param'].'">'.$i.'</li>';
                        }
                }else{
                    if ($i == $j) {
                        if(!empty($nav_li_class)){
                            $html .= '<li data-n="'.$i.'" class="last-step" data-ctrl="'.$questions['q']['q' . $i]['param'].'" class="' . implode(' ', $nav_li_class) . '"'.'>'.$i.'</li>';
                        }else{
                            $html .= '<li data-n="'.$i.'" class="last-step" data-ctrl="'.$questions['q']['q' . $i]['param'].'">'.$i.'</li>';
                        }
                    }else{
                        if(!empty($nav_li_class)){
                            $html .= '<li data-n="'.$i.'" data-ctrl="'.$questions['q']['q' . $i]['param'].'" class="' . implode(' ', $nav_li_class) . '"'.'>'.$i.'</li>';
                        }else{
                            $html .= '<li data-n="'.$i.'" data-ctrl="'.$questions['q']['q' . $i]['param'].'">'.$i.'</li>';
                        }
                    }
                }
            }
        }
        $html .='</ul>';
        $html .='</div>';
        break;
        case MATH_ASSIGNMENT_QUESTION_BOX:
            foreach ($questions['q'] as $key => $item) {
                if (empty($item['answer']) || trim($item['answer']) == '') {
                    unset($questions['q'][$key]);
                }
            }
            $html .='<h5 class="nav-title css-question-modal">'.'Steps:'.'</h5>';
            $html .='<div class="scroll-list-v" style="max-height: 380px">';
            $html .='<ul class="nav-items" id="qbox-step-nav">';
            for ($i = 1; $i <= count($questions['q']); $i++) {
                $html .='li data-n="'.$i.'">'.$i.'</li>';
            }
            $html .='</ul>';
             break;
    endswitch;
    $html .='</div>';
    $html .='<div id="homework-content" class="col-sm-10 homework-content math-type-'.$homework->assignment_id.'">';
    switch ($homework->assignment_id) :
        case MATH_ASSIGNMENT_SINGLE_DIGIT:
            MWHtml::math_digit_box($questions['op1'],null,0,MATH_ASSIGNMENT_SINGLE_DIGIT,MATH_ASSIGNMENT_SINGLE_DIGIT);
        MWHtml::math_digit_box($questions['op2'], $questions['sign'], strlen($questions['op1']) - strlen($questions['op2']),MATH_ASSIGNMENT_SINGLE_DIGIT);
        $html .='<hr class="hr-formula hr-num-4">';
        MWHtml::math_answer_box($questions['step']['s1'], 1, 'result[s1]', MATH_ASSIGNMENT_SINGLE_DIGIT);
        MWHtml::math_answer_box($questions['step']['s2'], 2, 'result[s2]', MATH_ASSIGNMENT_SINGLE_DIGIT,$questions['sign']);
        $html .='<hr class="hr-formula hr-num-4">';
        MWHtml::math_answer_box($questions['step']['s3'], 3, 'result[s3]',MATH_ASSIGNMENT_SINGLE_DIGIT);
        break;
    case MATH_ASSIGNMENT_TWO_DIGIT_MUL:
        MWHtml::math_digit_box($questions['op1'],null,0,MATH_ASSIGNMENT_TWO_DIGIT_MUL,MATH_ASSIGNMENT_TWO_DIGIT_MUL);
        MWHtml::math_digit_box($questions['op2'], 'x', strlen($questions['op1']) - strlen($questions['op2']),MATH_ASSIGNMENT_TWO_DIGIT_MUL);
        $html .='<hr class="hr-formula hr-num-4">';
        for ($i = 1; $i <= 4; $i++) {
            MATH_ASSIGNMENT_TWO_DIGIT_MUL;
            MWHtml::math_answer_box($questions['step']['s' . $i], $i, 'result[s' . $i . ']',MATH_ASSIGNMENT_TWO_DIGIT_MUL);
        }
        $html .='<hr class="hr-formula hr-num-4">';    
        MWHtml::math_answer_box($questions['step']['s5'], 5, 'result[s5]',MATH_ASSIGNMENT_TWO_DIGIT_MUL);
        MWHtml::math_answer_box($questions['step']['s6'], 6, 'result[s6]',MATH_ASSIGNMENT_TWO_DIGIT_MUL);
        $html .='<hr class="hr-formula hr-num-4">'; 
        MWHtml::math_answer_box($questions['step']['s7'], 7, 'result[s7]',MATH_ASSIGNMENT_TWO_DIGIT_MUL);
        break;
    case MATH_ASSIGNMENT_SINGLE_DIGIT_DIV:
        $last_step = count($no_steps);
        $last_step = count($no_steps);
        MWHtml::math_answer_box($questions['step']['s' . $last_step], $last_step, 'result[s' . $last_step . ']',MATH_ASSIGNMENT_SINGLE_DIGIT_DIV);
        MWHtml::math_digit_box_division($questions['op1'], $questions['op2']);
        for ($i = 1; $i <= $last_step - 2; $i++) {
            MWHtml::math_answer_box($questions['step']['s' . $i], $i, 'result[s' . $i . ']',MATH_ASSIGNMENT_SINGLE_DIGIT_DIV);
        }
        $html .='<hr class="hr-formula hr-num-2">';
        $remainder_step = $last_step - 1;
        MWHtml::math_answer_box($questions['step']['s' . $remainder_step], $remainder_step, 'result[s' . $remainder_step . ']',MATH_ASSIGNMENT_SINGLE_DIGIT_DIV);
        break;
    case MATH_ASSIGNMENT_TWO_DIGIT_DIV:
        $last_step = count($no_steps);
        MWHtml::math_answer_box($questions['step']['s' . $last_step], $last_step, 'result[s' . $last_step . ']',MATH_ASSIGNMENT_TWO_DIGIT_DIV);
        MWHtml::math_digit_box_division($questions['op1'], $questions['op2'],MATH_ASSIGNMENT_TWO_DIGIT_DIV);
        for ($i = 1; $i <= $last_step - 2; $i++) {
            MWHtml::math_answer_box($questions['step']['s' . $i], $i, 'result[s' . $i . ']',MATH_ASSIGNMENT_TWO_DIGIT_DIV);
        }
        $html .='<hr class="hr-formula hr-num-2">';
        $remainder_step = $last_step - 1;
        MWHtml::math_answer_box($questions['step']['s' . $remainder_step], $remainder_step, 'result[s' . $remainder_step . ']',MATH_ASSIGNMENT_TWO_DIGIT_DIV);
        break;
    case MATH_ASSIGNMENT_FLASHCARD:
        $html .='<p id="boxtruefalse1">Green Box = Correct</p>';
        $html .='<p id="boxtruefalse">Red Box = Incorrect</p>';
        foreach ($questions['q'] as $key => $item){
            $html .='<div class="flashcard-question hidden" id="flashcard-'.$key .'">';
            $html .='<span class="math-number">'.$item['op1'].'</span>';
            $html .='<span class="math-number">'.str_replace('247', '&divide;', $item['op']).'</span>';
            $html .='<span class="math-number">'.$item['op2'].'</span>';
            $html .='<span class="math-number">=</span> ';
            if($homework_assignment->for_practice ==0 ) {
                $html .='<span class="math-number input-box" style=" width: auto;"><input style="min-width: 100px;width: 126px;"'.'></span>';
            }else{
                $html .='<span class="math-number input-box" style=" width: auto;"><input style="min-width: 100px;width: 126px;"'.'></span>';
            }
            $html .='<span class="math-number">'.$item['note'].'</span>';
            
        }
        break;
    case MATH_ASSIGNMENT_FRACTION:    
        $html .='<p id="boxtruefalse1">Green Box = Correct</p>';
        $html .='<p id="boxtruefalse">Red Box = Incorrect</p>';
        foreach ($questions['q'] as $key => $item) :
            $html.='<div class="flashcard-question" id="flashcard-'.$key .'">';    
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
                $html .='<div class="fraction left-number">';
                $html .='<span class="math-number" style="width: auto;font-size: 46px; margin-top: 72px;">'.$op1[0].'</span>';
                $html .='</div>';
            endif;    
                if (!empty($op1[1])) :
                    $html .='<div class="fraction">';
                    if(empty($op1[2])){
                        $html .='<span class="math-number " style="width: auto;font-size: 46px; margin-top: 72px;">'. $op1[1].'</span>';
                    }else {
                        $html .='<span class="math-number " style="width: auto">'.$op1[1].'</span>';
                        $html .='<span class="icon-fraction fraction-answer"></span>';
                        $html .='<span class="math-number " style="width: auto">'. $op1[2].'</span>';
                    }
                    $html .='</div>';
                    $html .='<div class="fraction">';
                    $html .='<span class="math-number">&nbsp;</span>';
                    $html .='<span class="sign">'. str_replace('247', '&divide;', $item['op']).'</span>';
                    $html .='<span class="math-number">&nbsp;</span>';
                    $html .='</div>';
                endif;
                if (!empty($op2[0])) :
                    $html .='<div class="fraction left-number">';
                    $html .='<span class="math-number" style="width: auto;font-size: 46px; margin-top: 72px;">'. $op2[0].'</span>';
                    $html .='</div>';
                endif;
                $html .='<div class="fraction">';
                if(empty($op2[2])){
                    $html .='<span class="math-number " style="width: auto;font-size: 46px; margin-top: 72px;">'.$op1[1].'</span>';
                }else{
                    $html .='<span class="math-number " style="width: auto">'. $op2[1].'</span>';
                    $html .='<span class="icon-fraction fraction-answer"></span>';
                    $html .='<span class="math-number " style="width: auto">'.$op2[2].'</span>';
                }
                $html .='</div>';
                $html .='<div class="fraction">';
                $html .='<span class="math-number">&nbsp;</span>';
                $html .='<span class="sign">=</span>';
                $html .='<span class="math-number">&nbsp;</span>';
                $html .='</div>';
                if (!empty($answer[0])) : 
                    $html .='<div class="fraction left-number">';
                    if(!empty($answer[1]) && !empty($answer[2])){
                        $html .='<span class="math-number input-box fraction-answer" style="margin-top: 68px;"><input style="min-width: 100px;"'.'type="text" class="answer-box"></span>';
                    }else{
                        $html .='<span class="math-number input-box fraction-answer" ><input style="min-width: 100px;"'.'type="text" class="answer-box"></span>';
                    }
                    $html .='</div>';
                endif;
                $html .='<div class="fraction left-number">';
                if($answer[2]==0){
                    $html='<span class="math-number input-box fraction-answer" style="margin-top: 68px;"><input style="min-width: 100px;"'.'type="text" class="answer-box"></span>';
                }else {
                    $html .='<span class="math-number input-box fraction-answer"><input style="min-width: 100px;"'.'type="text" class="answer-box"></span>';
                }
                if (!empty($answer[2])) :
                    $html .='<span class="icon-fraction fraction-answer" style="margin-left: 20%;"></span>';
                    $html .='<span class="math-number input-box fraction-answer"><input style="min-width: 100px;"'.'type="text" class="answer-box"></span>';
                endif;
                $html .='</div>';
                $html .='</div>';
        endforeach;
        break;
        case MATH_ASSIGNMENT_WORD_PROB:
            if(($admindp==1 && $teacherdp==0)||($admindp==0 && $teacherdp==0)||($admindp==0 && $teacherdp==1)){
                for($i=1;$i<count($questions['q']);$i++){
                    $type_audio = explode('.', $questions['q']['q'.$i]['sound']);
                    if ($questions['q']['q'.$i]['sound'] != '' && ($type_audio[1] == 'mp4' || $type_audio[1] == 'ogg' || $type_audio[1] == 'm4v')):
                        $html .='<video class="word-prob-video" controls id="word-prob-video-'. "q".$i.'" style="width:100%; max-height:100%; max-width:100%">';
                        $html .='<source src="'.MWHtml::math_video_url($questions['q']['q'.$i]['sound']).'" type="video/mp4">';
                        $html .='Your browser does not support the video tag.';
                        $html .='</video>';
                    else :
                       if ($questions['q']['q'.$i]['image'] != ''):
                           $html .='<img src="'. MWHtml::math_image_url($questions['q']['q'.$i]['image']) .'" alt="" id="word-prob-step-'. 'q'.$i.'" class="word-prob-steps canvas-layer" data-img-src="'.MWHtml::math_image_url($questions['q']['q'.$i]['image']).'">';
                       endif;
                    endif;
                    if ($questions['q']['q'.$i]['sound'] != '' && $type_audio[1] == 'mp3'):
                        $html .='<audio class="word-prob-sound" id="word-prob-sound-'. 'q'.$i .'" preload="auto" style="width: 100%;">';
                    endif;
                }
            }else{
                foreach ($questions['q'] as $key => $item) :
                    $type_audio = explode('.', $item['sound']);
                    if ($item['sound'] != '' && ($type_audio[1] == 'mp4' || $type_audio[1] == 'ogg' || $type_audio[1] == 'm4v')):
                        $html .='<video class="word-prob-video" controls id="word-prob-video-'.$key.'" style="width:100%; max-height:100%; max-width:100%">';
                        $html .='<source src="'.MWHtml::math_video_url($item['sound']).'" type="video/mp4">';
                        $html .='Your browser does not support the video tag.';
                        $html .='</video>';
                    else :
                        if ($item['image'] != ''):
                            $html .='<img src="'. MWHtml::math_image_url($item['image']).'" alt="" id="word-prob-step-'. $key .'" class="word-prob-steps canvas-layer" data-img-src="'. MWHtml::math_image_url($item['image']).'">';
                        endif;
                    endif;
                    if ($item['sound'] != '' && $type_audio[1] == 'mp3'):
                        $html .='<audio class="word-prob-sound" id="word-prob-sound-'.$key.'" preload="auto" style="width: 100%;">';
                        $html .='<source src="'.MWHtml::math_sound_url($item['sound']).'" type="audio/mpeg">';
                        $html .='</audio>';
                    endif;
                endforeach;
            }
        break;
        case MATH_ASSIGNMENT_QUESTION_BOX:
        $html .='<p id="boxtruefalse1">Green Box = Correct</p>';
        $html .='<p id="boxtruefalse1">Red Box = Incorrect</p>';
        foreach ($questions['q'] as $key => $item) :
            $html .='<div id="qbox-step-'.$key.'" class="question-box-block">';
            $html .='<img src="'.MWHtml::math_image_url($item['image']).'" alt="" class="word-prob-steps canvas-layer" data-img-src="'.MWHtml::math_image_url($item['image']).'" >';
            $html .='<span class="math-number input-box" style=" width: auto;" style="z-index:'.substr($key, 1).';'.' left:'.$item['x-cord'].'%; top:'.$item['y-cord'].'%; width:'. $item['width'].'%; height:'.$item['height'].'%">';
            $html .='<input style="min-width: 100px;width: 126px;"onkeypress="this.style.width = ((this.value.length + 1) * 20) + '.px.';" data-answer="'.$item['answer'].'" autocomplete="off" name="result['. $key .']" type="text" class="answer-box"></span>';
            $html .= '</div>';
            $count_q++;
        endforeach;
        break;
        case MATH_ASSIGNMENT_EQUATION:
        $html .='<p id="boxtruefalse1">Green Box = Correct</p>';
        $html .='<p id="boxtruefalse1">Red Box = Incorrect</p>';
        foreach ($questions['q'] as $key => $item) :
            $html .='<div class="flashcard-question equation-question hidden" id="flashcard-'. $key.'">';
            $arr_ = array('\n' => '<br>', '-' => '&#8211;');
            $html .='<span class="math-number">'.strtr.'('.$item['equation'].','.$arr_.')'.'</span>';
            $html .='<span class="math-number input-box" style=" width: auto;"><input onkeypress="this.style.width = ((this.value.length + 1) * 20) + '.px.';" data-answer="'.$item['answer'].'" name="result['.$key.']" type="text" style="min-width: 100px;width: 126px;" class="answer-box" autocomplete="off"></span>';
            $html .='<span class="math-number">'.$item['note'].'</span>';
            $html .= '</div>';
        endforeach;
        break;
    endswitch;
    $html .='</div>';
    $html .='<div class="col-sm-10 homework-user-answer">';
    $html .='<div class="row">';
    if(strpos($_SERVER['REQUEST_URI'], '&ismode=0') !== false){
        $html .='<div class="col-xs-7">';
        $html .='<input type="text" class="homework-input tooltip-top-left" name="result" id="input-answer" data-answer="'.$questions['answer'].';'.'" data-correct="'.'Correct!'.'" data-incorrect="'.'Incorrect!'.'">';
        $html .='</div>';
        $html .='<div class="col-xs-2">';
        $html .='<button type="button" style="background: #FF8A00 !important;color: #fff !important" id="submit-homework" class="btn btn-default brown">'.'Submit'.'</button>';
        $html .='</div>';
    }else { 
        $html .='<div class="col-xs-9">';
        $html .='<input type="text" class="homework-input tooltip-top-left" name="result" id="input-answer" data-answer="'.$questions['answer'].';'.'" data-correct="'.'Correct!'.'" data-incorrect="'.'Incorrect!'.'">';
        $html .='</div>';
    }
    $html .='<div class="col-xs-3">';
    if (!$teacher_taking_test) {
        $_ref_url = empty($_GET['ref']) ? "#" : esc_html(base64_decode(rawurldecode($_GET['ref'])));
    }else{
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
    if($homework_assignment->for_practice == 1) {
        if(!empty($_GET["sat"])){
        if (!empty($homework_assignment->next_homework_id)) {
        $link = $_next_url.'&prid='.$id."&ismode=".$homework_assignment->for_practice ."&sat=".$_GET["sat"]."&gid=".$_GET["gid"];
                    } else {
                        $link = home_url()."/?r=online-learning&sat=".$_GET["sat"]."&gid=".$_GET["gid"];
                    } 
            } else if(!empty($_GET["page-back"])){ 
                    if (!empty($homework_assignment->next_homework_id)) { 
                    $link = $_next_url.'&prid='.$id."&ismode=".$homework_assignment->for_practice ."&sat=".$_GET["sat"]."&gid=".$_GET["gid"]."&page-back=".$_GET["page-back"];
                    } else {
                        $link = home_url()."/?r=sat-preparation/".$_GET["page-back"]."&client=math-emathk";
                    } 
            }else if(!empty($_GET["back-ikmath"])){     
                    if (!empty($homework_assignment->next_homework_id)) { 
                        $link = $_next_url.'&prid='.$id."&ismode=".$homework_assignment->for_practice .'&amp;back-ikmath='.$_GET["back-ikmath"]."&gid=".$_GET["gid"];
                    } else {
                        $link = home_url()."/?r=online-learning&back-ikmath=".$_GET["back-ikmath"]."&gid=".$_GET["gid"]."&issat-math=1";
                    } 
            } else if(!empty($_GET["lvid"])){
                    if (!empty($homework_assignment->next_homework_id)) { 
                        $link = $_next_url.'&prid='.$id."&ismode=".$homework_assignment->for_practice .'&amp;back-ikmath='.$_GET["back-ikmath"]."&lvid=".$_GET["lvid"]."&gid=".$_GET["gid"];
                    } else {
                        $link = home_url()."/?r=online-learning&math&lvid=".$_GET["lvid"];
                    } 
            }else{
                    $link = $_next_url.'&prid='.$id."&ismode=".$homework_assignment->for_practice;
            }
            $html .='<button type="submit" name="submit-practive" id="btn-next-practive" class="btn btn-default brown btn-next-practive" data-loading-text="'.'Submitting...'.'" data-ref="'. $link.'"></span>'.'Next Assignment'.'</button>';
            $html .='<input type="hidden" name="ref-practive" id="input-ref-practive" value="'.$link.'">';
    }else{
        if(!empty($_GET["sat"])){
            if (!empty($homework_assignment->next_homework_id)) {
                $html .='<a href="'. $_next_url.'&prid='.$id."&ismode=".$homework_assignment->for_practice ."&sat=".$_GET["sat"]."&gid=".$_GET["gid"].'" class="btn btn-default brown" id="next-worksheet">'.'Next Assignment'.'</a>';
            } else {
                $html .='<a href="'.'home_url()."/?r=online-learning&sat=".$_GET["sat"]."&gid=".$_GET["gid"] ?>" class="btn btn-default brown" id="next-worksheet">'.'Next Assignment'.'</a>';
            }
        }else if(!empty($_GET["page-back"])){
            if (!empty($homework_assignment->next_homework_id)) {
                $html .='<a href="'. $_next_url.'&prid='.$id."&ismode=".$homework_assignment->for_practice ."&sat=".$_GET["sat"]."&gid=".$_GET["gid"]."&page-back=".$_GET["page-back"].'" class="btn btn-default brown" id="next-worksheet">'.'Next Assignment'.'</a>';
            }else{
                $html .='<a href="'.home_url()."/?r=sat-preparation/".$_GET["page-back"]."&client=math-emathk".'" class="btn btn-default brown" id="next-worksheet">'.'Next Assignment'.'</a>';
            }
        }else if(!empty($_GET["back-ikmath"])){
            if (!empty($homework_assignment->next_homework_id)) {
                $html .='<a href="'.$_next_url.'&prid='.$id."&ismode=".$homework_assignment->for_practice .'&amp;back-ikmath='.$_GET["back-ikmath"]."&gid=".$_GET["gid"].'" class="btn btn-default brown" id="next-worksheet">'.'Next Assignment'.'</a>';
            }else{
                $html .='<a href="'.home_url()."/?r=online-learning&back-ikmath=".$_GET["back-ikmath"]."&gid=".$_GET["gid"]."&issat-math=1".'" class="btn btn-default brown" id="next-worksheet">'.'Next Assignment'.'</a>';
            }
        } else {
            $html .='<a href="'. $_next_url.'&prid='.$id."&ismode=".$homework_assignment->for_practice .'" class="btn btn-default brown" id="next-worksheet">'.'Next Assignment'.'</a>';
        }
    }
    $html .='</div>';
    $html .='</div>';
    $html .='</div>';
    
    $html .='<div class="col-sm-2 homework-controls" style="padding-left: 6px !important;">';
        $html .='<button type="button" class="btn btn-default dark-green" id="open-notepad-btn"><i class="icon-notepad"></i>'.'Notepad'.'</button>';
        $html .='<hr class="hr-green hidden-xs">';
        $html .='<button type="button" class="btn btn-default dark-green" id="open-chat-btn"><i class="icon-chat"></i>'.'Tutoring'.'</button>';
        if(!empty($_GET["sat"])){
            $html .='<a href="'.home_url()."/?r=online-learning&sat=".$_GET["sat"]."&gid=".$_GET["gid"].'" class="btn brown" style="background: #28423A;color: #fff;padding-top: 15px;"><i class="glyphicon glyphicon-list" style="padding-right: 10px;"></i>'.'Back to List'.'</a>';
        }else if(!empty ($_GET["page-back"])){
            $html .='<a href="'. home_url()."/?r=sat-preparation/".$_GET["page-back"]."&client=math-emathk".'" class="btn brown" style="background: #28423A;color: #fff;padding-top: 15px;"><i class="glyphicon glyphicon-list" style="padding-right: 10px;"></i>'.'Back to List'.'</a>';
        }else if(!empty($_GET["back-ikmath"])){
            $html .='<a href="'. home_url()."/?r=online-learning&back-ikmath=".$_GET["back-ikmath"]."&gid=".$_GET["gid"]."&issat-math=1".'" class="btn brown" style="background: #28423A;color: #fff;padding-top: 15px;"><i class="glyphicon glyphicon-list" style="padding-right: 10px;"></i>'.'Back to List'.'</a>';
        } else if(!empty($_GET["lvid"])){
            $html .='<a href="'. home_url()."/?r=online-learning&math&lvid=".$_GET["lvid"].'" class="btn brown" style="background: #28423A;color: #fff;padding-top: 15px;"><i class="glyphicon glyphicon-list" style="padding-right: 10px;"></i>'.'Back to List'.'</a>';
        } else {
            $html .='<a href="'. home_url()."/?r=online-learning&sat=".$_GET["sat"]."&gid=".$_GET["gid"].'" class="btn brown" style="background: #28423A;color: #fff;padding-top: 15px;"><i class="glyphicon glyphicon-list" style="padding-right: 10px;"></i>'.'Back to List'.'</a>';
        }
    $html .='</div>';
//submit-homework-modal!    
    if($teacher_taking_test){
        $html .='<div id="submit-homework-modal" class="modal fade modal-green" data-keyboard="true" aria-hidden="true" data-backdrop="static">';
    }else{
        $html .='<div id="submit-homework-modal" class="modal fade modal-green" data-keyboard="true" aria-hidden="true" data-backdrop="">';
    }
    $html .='<div class="modal-dialog">';
        $html .='<div class="modal-content" style="border: 2px solid #000;">';
            $html .='<div class="modal-header" style="background: #838383;margin: 0px;">';
            if(!$teacher_taking_test){
                $html .='<h3 style="padding-left: 1% !important">'.'Submitting Homework'.'</h3>';
            }else{
                $html .='<h3 style="padding-left: 1% !important">'.'The End of Test'.'</h3>';
            }
            $html .='</div>';
                if (!$teacher_taking_test) :
                    if (!empty($homework_assignment->next_homework_id)) {
                            $html .='<div class="modal-body" style="background: #fff !important;color: #000">';
                                $html .='<strong>'.'You have completed this homework.'.'</strong><br>';
                                $html .='<hr style="border-top: 2px solid #DCDCDC">';
                                $html .='<span>Do you want to start next worksheet?</span>';
                            $html .='</div>';
                            $html .='<div class="modal-footer" style="background: #fff !important">';
                                $html .='<div class="row">';
                                    if (empty($homework_assignment->next_homework_id)) :
                                        $html .='<div class="col-sm-12 form-group">';
                                        $html .='<button type="submit" name="submit-homework-finish" style="background: #4C4C4C !important;color: #E3C264" class="btn btn-block orange submit-lesson-btn" data-loading-text="'.'Submitting...'.'" data-ref="'. $_ref_url.'"></span>'.'Yes. Start Next Worksheet'.'</button>';
                                        $html .='</div>';
                                    else : 
                                        $html .='<div class="col-sm-6 form-group">';
                                        $html .='<button type="submit" name="submit-homework-next" id="btn-next-worksheet" style="background: #4C4C4C !important;color: #E3C264" class="btn btn-block submit-lesson-btn" data-loading-text="'.'Submitting...'.'" data-ref="'. $_next_url.'" &prid="'.$id.'" &ismode="'.$homework_assignment->for_practice .'"&sat=".$_GET["sat"]."&gid=".$_GET["gid"]'.'"></span>'.'Yes. Start Next Worksheet'.'</button>';
                                        $html .='</div>';
                                        $html .='<div class="col-sm-6 form-group">';
                                        $html .='<button type="button" id="close-modal-homework" style="background: #B6B6B6 !important;color: #fff;" class="btn btn-block grey submit-lesson-btn" data-loading-text="'.'Submitting...'.'" data-ref="'. $_ref_url .'"></span>'.'No. Submit and Quit'.'</button>';
                                        $html .='</div>';
                                    endif;
                                    $html .='<input type="hidden" name="ref" id="input-ref" value="'. $_next_url.'" &prid=" '.$id.' "&ismode="'.$homework_assignment->for_practice .'" &sat=".$_GET["sat"]."&gid=".$_GET["gid"]'.'">';
                                $html .='</div>';
                            $html .='</div>';
                    } else { 
                        $html .='<div class="modal-body" style="background: #fff !important;color: #000;">';
                        $html .='<strong>'.'You have completed this homework.'.'</strong><br>';
                        $html .='</div>';
                        $html .='<div class="modal-footer" style="background: #fff !important;padding-bottom: 25px">';
                        $html .='<div class="row">';
                            if(!empty($_GET["sat"])){
                                $html .='<button type="submit" name="submit-homework-next" style="background: #4C4C4C !important;color: #E3C264" class="btn btn-block orange link-finish" data-link="'.home_url().'"/?r=online-learning&sat="'.$_GET["sat"].'"&gid="'.$_GET["gid"] .'">Back to List</button>>';
                                $html .='<input type="hidden" name="ref-finish" id="input-ref-finish" value="'.home_url().'"/?r=online-learning&sat="'.$_GET["sat"].'"&gid="'.$_GET["gid"].'">';
                            }else {
                                $html .='<button type="submit" name="submit-homework-next" style="background: #4C4C4C !important;color: #E3C264" class="btn btn-block orange link-finish" data-link="'. $_next_url.'" &prid="'.$id.'">'.'Back to List'.'</button>>';
                                $html .='<input type="hidden" name="ref-finish" id="input-ref-finish" value="'.$_next_url.'&prid='.$id .'">';
                            }
                            $html .='<input type="hidden" name="ref" id="input-ref" value="'. $_next_url.'"&prid="'.$id.'"&ismode="'.$homework_assignment->for_practice .'"&sat="'.$_GET["sat"].'"&gid="'.$_GET["gid"].'">';
                        $html .='</div>';
                        $html .='</div>';
                    }
                else:
                        $html .='<div class="modal-body">';
                            $html .='You have completed this test.';
                            $html .='If you want to leave a message to the admin, type it in the box below.';
                            $html .='Click OK to submit.';
                            $html .='<hr>';
                            $html .='<div class="form-group">';
                                $html .='<textarea  class="form-control" id="txt-feedback" placeholder="'.'Leave a Message to the Teacher (Optional)'.'" style="resize: none; height: 111px; border-radius: 0px;font-size: 18px;margin-bottom: 3%;margin-top: 1%"></textarea>';
                            $html .='</div>';
                        $html .='</div>';
                        $html .='<div class="modal-footer">';
                            $html .='<div class="row">';
                                $html .='<div class="col-sm-12">';
                                    $html .='<div class="form-group">';
                                        $html .='<button type="submit" name="submit-homework" class="btn btn-block orange submit-lesson-btn" data-loading-text="'.'Submitting...'.'" data-ref="'. $_ref_url.'"><span class="icon-accept"></span>'.OK.'</button>';
                                    $html .='</div>';
                                $html .='</div>';
                            $html .='</div>';
                            $html .='<input type="hidden" name="ref" id="input-ref" value="'. $_next_url.'&prid='.$id."&ismode=".$homework_assignment->for_practice ."&sat=".$_GET["sat"]."&gid=".$_GET["gid"].'"> ';
                            if ($teacher_taking_test) :
                                $html ='<input type="hidden" name="pass" value="'. $pass.'" />';
                            endif;
                        $html .='</div>';
                    
                endif;
            $html .='</div>';
        $html .='</div>';
    $html .='</div>';
    
    $html .= '</form>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    
    echo $html;
    die;        
}
// Load html for modal-view-result-writing
if ($task == 'view_result_writing') {
    $id = $_REQUEST['hw_id'];
        $select= $_REQUEST['select'];
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

        $html='';
        $arr_quiz = json_decode($sheet->questions);
        $html .= '<span class="css-name-sheet">';
            if($arr_quiz->quiz !='') {    
                $html .=$arr_quiz->quiz[$select];
            }
        $html .= '</span>';
        $html .='<div style="padding: 1% 5% 1% 5%;">';
        if (!empty($user_avatar)) :
            $html .='<img src="'. $user_avatar . '" width="130" height="140" alt="" class="css-image-user-load-db">';
        else :
            $html .='<div class="css-image-user"></div>';
        endif;
        $html .= '<div class="result-info">';
        $html .='<div><span class="css-7D7D7D">'."Teacher: ".'</span>';
        $html .='<span class="css-7C7C7C">'."N/A".'</span></div>';
        $html .='<div><span class="css-7D7D7D">'."Student's Name: ".'</span>';
        $html .='<span class="css-7C7C7C">'.$name->user_nicename.'</span></div>';
        $html .='<div><span class="css-7D7D7D">'."Level: ".'</span>';
        $html .='<span class="css-7C7C7C">'.$result->lv.'</span></div>';
        $html .='<div><span class="css-7D7D7D">'."Dictionary: ".'</span>';
        if($result->libname == '') {
            $html .='<span class="css-7C7C7C">'."N/A".'</span>';
        }else {
            $html .='<span class="css-7C7C7C">'.$result->libname.'</span>';
        }
        $html .= '</div>';
        $html .='<div><span class="css-7D7D7D">'."Last Attempt: ".'</span>';
        $html .='<span class="css-7C7C7C">'.$name->attempted_on.'</span></div>';

        $html .='<div class="css-mobile-display">';
        $html .='<div style="width:84%"><span class="css-7D7D7D">'."Completed Date: ".'</span>';
        $html .='<span class="css-7C7C7C">'.$name->submitted_on.'</span></div>';
        $html .='<div class="css-mobile-right"><span class="css-rs-score">'."Score: ".'</span>';
        if($result->score != 0) {
            $html .='<span class="rs-score1">'.$result->score.'%</span></div>';
        } else {
            $html .='<span class="rs-score1">Not Graded</span></div>';
        }
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="line-result"></div>';
    //    foreach load table question
        $words = json_decode($sheet->questions, true);
        $word_total = count($words['question']);

        $html .= '<div id="div-all-select" style="height: 20px !important;">';
        $html .='<div id="div-select" class="css-option-writing">';
        $html .='<ul id="css-ul-dropdown" class="css-image-option-writing image-select-ikmath">';
        $html .='<li class="init">Page 1</li>';
        for($i = 0; $i< $word_total ;$i++) { 
            $j=$i+1;    
            $html .='<li data-value="'.$j.'" class="click-test-ikmath border-left-right-ul-dropdown" style="display:none">Page '.$j.'</li>';
        }
        $html .='</ul>';
        $html .='</div>';
        $html .='</div>';

        $html .= '<div class="css-assign">ASSIGNMENT</div>';
        $arr = json_decode($sheet->questions);
        $html .= '<div class="css-assing">';
//        var_dump($arr);die;
        if($arr->question !='') {    
            $html .=$arr->question[$select];
        }
        $html .='</div>';
        $html .= '<div class="css-ans">YOUR ANSWER</div>';
        $arr_ans = json_decode($result->answers);
          //    echo '<pre>';
    //    print_r($ques);
        $html .= '<div class="css-answer">';
        $str_ans = "q".$select;
        if($arr_ans !='') {    
            $html .=$arr_ans->$str_ans;
        }
        $html .='</div>';
        $html .= '<div class="css-note">NOTE BY TEACHER</div>';
        $arr_comment = json_decode($result->teacher_comments);
        $html .= '<div class="css-note1">';
        if($arr_comment !='') {    
            $html .=$arr_comment->$str_ans;
        }
        $html .='</div>';
    //    $html .= '<div class="css-note1">'.$result->teacher_comments.'</div>';

        echo $html;
        die;
    }
    if ($do == 'update-question') {
        $id = $_REQUEST['hw_id'];
        $select= $_REQUEST['select'];
        echo $words->quiz[$select];
        die;
    }
    if ($do == 'update-assignment') {
        $id = $_REQUEST['hw_id'];
        $select= $_REQUEST['select'];
        $arr = json_decode($sheet->questions);
        echo $arr->question[$select];
        die;
    }
    if ($do == 'update-answer') {
        $id = $_REQUEST['hw_id'];
        $select= $_REQUEST['select'];
        $arr_ans = json_decode($result->answers);
        $str_ans = "q".$select;
        echo $arr_ans->$str_ans;
        die;
    }
    if ($do == 'update-note-teacher') {
        $id = $_REQUEST['hw_id'];
        $select= $_REQUEST['select'];
        $arr_comment = json_decode($result->teacher_comments);
        $str_ans = "q".$select;
        echo $arr_comment->$str_ans;
        die;
    }
}
/**
 * load class ikmath course follow selected 
 */
if ($task == 'load_class_ikmath_course') {
    $id_class_selected = (int)$_REQUEST['id_class_selected']; 
    if($id_class_selected == 0) {
        $html='';
        $html .= '<tr>';                                                                                                    
        $html .= '<td style="padding-left: 5%;width: 20% !important;">You haven’t joined any groups yet. Please select from.';
        $html .='<a href="'.home_url() . '?r=sat-preparation/emathk&client=math-emathk" style="text-decoration: underline;"> ikMath Course </a>Section.</td>';
        $html .= '</tr>';
        for ($i = 0; $i < 13; $i++) {
        $html .= '<tr ><td style="height : 35px; width: 1% !important;" colspan="5" ></td></tr>';
        }
    }else {
    $current_page = max(1, get_query_var('page'));   
    $filter['orderby'] = 'ordering';
    $filter['offset'] = 0;
    $filter['items_per_page'] = 99999999;
    $filter["class_type"] = $id_class_selected;
    $filter['group_type'] = GROUP_CLASS;
   
    $groups = MWDB::get_groups($filter, $filter['offset'], $filter['items_per_page']); 
     //echo '<pre>';print_r($filter);die;
    $html='';
    if (count($groups->items) > 0) :
        foreach ($groups->items as $group) :
            $class_type_id = $group->class_type_id;
                $html .= '<tr>';
                    $html .= '<td style="padding-left: 5%;width: 70% !important;">'.$group->content.'</td>';
                    
                    if($group->no_homeworks == 0) {
                        $html .= '<td>0</td>';
                    } else {
                        $html .= '<td>'.$group->no_homeworks.'</td>';
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
//                        if (is_student_in_group(get_current_user_id(), $group->id)) :
                            $sat_results = get_sat_class_score($group->id);
                            if (is_sat_class_completed($sat_results)) :
//                                $html .='<a href="#" role="button" class="view-score" data-jid="'.$group->id.'">Completed</a>';
                                $html .='<a href="'.home_url() . '/?r=online-learning&back-ikmath='.$id_class_selected.'&issat-math=1&amp;gid='.$group->id.'" class="view-score" data-jid="'.$group->id.'">Completed</a>';
                            else:
//                                if ($is_sat_class_subscribed || $is_sat_english_subscribed_package):
                                    $html .= '<a href="'.home_url() . '/?r=online-learning&back-ikmath='.$id_class_selected.'&issat-math=1&amp;gid='.$group->id.'" class="color-0 css-link">Working</a>';
//                                else:
//                                    $html .= '<a href="javascript:void(0);" style="color: #000;" class=" working-btn">Working</a>';
//                                endif;
                            endif;
//                        endif;
//
                            $html .= '<table class="hidden">';
                                $html .= '<tbody>';
                                    foreach ($sat_results as $result) : 
                                        $html .= '<tr>';
                                            $html .= '<td>'.$result->sheet_name.'</td>';
                                            $html .= '<td>'.$result->score.'</td>';
                                            $html .= '<td><a href="'.locale_home_url() . '/?r=online-learning&hid='.$result->hid.'" class="btn btn-default btn-tiny grey" >View</a></td>';
                                            $html .= '<td>'.$result->submitted_on.'</td>';
                                        $html .= '</tr>';
                                    endforeach;
                                    if (is_sat_class_completed($sat_results)) :
                                        if (check_admin_by_id($group->uid)):
                                            $html .= '<tr>';
                                                $html .= '<td colspan="3"></td>';
                                                $html .= '<td><a href="'.home_url() . '/?r=online-learning&amp;gid=' . $group->id.'" class="">Restart</a></td>'; 
                                            $html .= '</tr>';
                                         endif;
                                    endif;
                                $html .='</tbody>';
                            $html .= '</table>';
                    $html .='</td>';
                    $html .='<td></td>';
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
 */
if ($task == 'load_tutoring_plan') {
    $id = (int)$_REQUEST['id_schedule'];
    $groups = MWDB::get_tutoring_plan($id); 
    if (empty($groups)){
        $html='';
        $html .= '<tr>';  
        if(count(MWDB::get_tutoring_plan(0)) >0) {
            if($id == 1) {
                $html .= '<td style="padding-left: 5%;width: 100% !important;">No sessions pending.';
            } else if($id == 2) {
                $html .= '<td style="padding-left: 5%;width: 100% !important;">You have no confirmed tutoring session.';
            } else if($id == 3) {
                $html .= '<td style="padding-left: 5%;width: 100% !important;">No canceled sessions.';
            } else {
                $html .= '<td style="padding-left: 5%;width: 100% !important;">You haven’t joined any groups yet. Please select from.';
                $html .='<a href="'.home_url() . '/?r=tutoring-plan" style="text-decoration: underline;"> Tutoring Plan </a>Section.</td>';
            }
        } else {
            $html .= '<td style="padding-left: 5%;width: 100% !important;">You haven’t joined any groups yet. Please select from.';
                $html .='<a href="'.home_url() . '/?r=tutoring-plan" style="text-decoration: underline;"> Tutoring Plan </a>Section.</td>';
        }
        $html .= '</tr>';
    }else {
        $html='';
        foreach ($groups as $group) :
            $html .= '<tr>';
            $html .= '<td style="padding-left: 5%;width: 40% !important;">'.$group->private_subject.'</td>';
            if($group->assigned_id != null){
                $html .= '<td style="width: 14% !important;">'.$group->assigned_id.'</td>';
            }else{
                $html .= '<td style="width: 14% !important;">N/A</td>';
            }    
            $html .= '<td style="width: 17% !important;">'.$group->date.'</td>';
            $html .= '<td style="width: 18% !important;">'.$group->time.'</td>';
            if($group->finished == 1){
                $html .= '<td class="css-link" style="width: 12% !important;text-decoration:none !important">Confirmed</td>';
            }else if($group->scheduled == 1){
                $html .= '<td class="css-link" style="width: 12% !important;text-decoration:none !important">Waiting</td>';
            } else if ($group->canceled == 1){
                $html .= '<td class="css-link" style="width: 12% !important;text-decoration:none !important">Canceled</td>';
            }
            $html .= '<td></td>';
            $html .= '</tr>';       
        endforeach;
        ?>
        <?php   
        }
    if(count($groups) < 13) {
        for ($i = count($groups); $i < 13; $i++) {
            $html .= '<tr ><td class="row-full-1" colspan="5" ></td><td></td></tr>';
        }
    }    
        echo $html;
    die;
}
/**
 * remove message in group 
 */
if ($task == 'groupmessageremove') {
    $id= $_REQUEST['id'];
    MWDB::remove_group_messages($id);
    echo json_encode($id);
    die;
}
      
/**
 * get private message  send
 */
if ($task == 'getsentmsg') {
    if(isset($_REQUEST['id'])){
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
    if(isset($_REQUEST['id'])){
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
        if (ik_deduct_user_points($_REQUEST['point'])!==false) {
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
    }else{
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
                    $wpdb->prefix . 'dict_homework_results', $result_data, array('homework_id' => $hid,'userid'=>$userid)
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

        if ($result) {
            die(json_encode(array($wpdb->insert_id)));
        } else {
            die(json_encode(array(0)));
        }
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
            if (MWDB::check_word_exist_folder($id_folder,$word)!=null) {
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
        if(isset($_GET['id'])){
            $code = MWDB::view_detail_subscriptions($_GET['id']);
            echo json_encode($code);
            exit;
        }
}
if ($task === 'math_worksheet') {
    if ($do === 'get') {
        //check user subscription
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
                $roles = array("r-teacher","q-teacher","mr-teacher","mq-teacher");

                $subject_email = 'The subject of tutoring, Title: '.$row_sheet->sheet_name;

                $message = '<p>Here is the email notification (Choose one in the (   ) area)</p></br>';
                $message .= '<p>A student is requesting (English writing tutoring, Math tutoring).</p>';
                $message .= '<p>Please check ikteach.com and check to see if you can help.</p></br>';
                $message .= '<p>The student name: '.$current_user->display_name.'</p>';
                $message .= '<p>The tutoring language requested: Math</p>';
                $message .= '<p>The subject of tutoring, Title: '.$row_sheet->sheet_name.'</p></br>';
                $message .= '<p>After you complete the request, the student has an opportunity to grade your tutoring quality.</p>';
                $message .= '<p>We will be keeping such data to provide the better tutoring to students.</p>';
                $message .= '</br><p>Thanks</p></br>';
                $message .= '<p>Support, iklearn.com</p>';
                $message .= '<p>This is email Notification Text</p>';
                
                $headers = array('Content-Type: text/html; charset=UTF-8');

                $teaches = MWDB::get_users_with_role($roles);
                if(count($teaches) > 0){
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

if($task == "update_evaluation"){
    $id = isset($_REQUEST['id'])?$_REQUEST['id']:0;
    $txt_eval = isset($_REQUEST['txt_eval'])?$_REQUEST['txt_eval']:'';
    
    if($id != 0){
        MWDB::update_evaluation($txt_eval, $id);   
        echo(1);
    }else{
        echo(0);
    }
    exit();
}

if($task == "update-evaluation-english"){
    $id = isset($_REQUEST['id'])?$_REQUEST['id']:0;
    $txt_eval = isset($_REQUEST['txt_eval'])?$_REQUEST['txt_eval']:'';
    
    if($id != 0){
        MWDB::update_evaluation_english($txt_eval, $id);   
        echo(1);
    }else{
        echo(0);
    }
    exit();
}

if ($task == 'get_detail_sub') {
    $subid = $_REQUEST['subid'];
    $result = MWDB::get_user_subscription_details($subid);
    if($result){
        echo '<h2 class="title-border" style="color: black; margin-left: 5%;">'.$result->code_type.' Subscription</h2>';        
        echo '<div style="padding: 0px 5% 0px 5%; font-size: 15px;">';
        echo '<table class="table table-striped table-style3 table-custom-2">';
        if ($result->encoded_code != null) {
        echo '<tr><td>Subscription Code:</td>';
        echo '<td colspan="2">'.$result->encoded_code.'</td></tr>';
        }
        echo '<tr>';
        echo '<td style="width: 200px">Subscription Type: </td>';
        if ($result->typeid == 25) {
        echo '<td colspan="2">'.$result->sat_class.'</td>';  
        } else {
        echo '<td colspan="2">'.$result->code_type.'';
        if($result->typeid == SUB_SAT_PREPARATION) {
        echo ''.$result->sat_class.'';
        } else {
        echo ' ';   
        }
        }
        echo '</tr>';
        echo '<tr>';
    echo '<td>Subscription Start: </td>';
    echo '<td colspan="2">'.$result->activated_on.'</td>';
    echo '</tr>';
        echo '<tr>';
    echo '<td>Subscription End:</td>';
    echo '<td colspan="2">'.$result->expired_on.' '; 
//        if($date = $result->expired_on - $result->activated_on >=0) {
//        echo ''.$result->expired_on - $result->activated_on.'day left'; 
//        }       
    echo '</td>';
    echo '</tr>';
        if(!empty($result->dictionary)) : 
        echo '<tr>';    
    echo '<td>Dictionary: </td>';
    echo '<td colspan="2">'.$result->dictionary.'</td>';
    echo '</tr>';
    endif;
        if(!empty($result->group_name)) : 
    echo '<tr>';
    echo '<td>Group Name: </td>';
    echo '<td colspan="2">'.$result->group_name.'</td>';
    echo '</tr>';
        endif; 
    if($result->typeid != SUB_SAT_PREPARATION) : 
    echo '<tr>';
    echo '<td>';
        if($result->typeid) {
        echo 'Number of Students';
        } else {
        echo 'Number of Users';
        }        
        echo '</td>';
    echo '<td colspan="2">'.$result->number_of_students.'</td>';
    echo '</tr>';
    endif;  
        if($result->typeid == SUB_DICTIONARY) : 
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
    }else{
        echo '<p>Chưa có dữ liệu</p>';
    }
}
if($task == "change_status"){
    $id = $_REQUEST['st_id'];
    if($id != 0){
        $ch_status = MWDB::update_status_private_input($id);
    }
    else {
        
    }
    exit();
}
if ($task == 'insert_ikmath_tutoring_plan') {
//        $array_date = $_REQUEST['date'];
        $subject = $_REQUEST['subject'];
        $date = $_REQUEST['date'];
        $time = $_REQUEST['time'];
        $zone = $_REQUEST['zone'];
        $sub= $_REQUEST['subject_private'];
        $total= $_REQUEST['total'];
        $tutor= $_REQUEST['tutor'];
            if (MWDB::store_ikmath_tutoring_plan($subject,$date,$time,$zone,$sub,$total,$tutor)) {
                echo 'update success';
            } else {
                echo 'update error';
            }
        
        exit;
    }
    
// View info to cancel schedule page Tutoring Plan     
if ($task == 'get_info_cancel_schedule') {
    $date = $_REQUEST['date']; 
    $result = MWDB::get_infos_cancel($date); 
    $html='';
    $html .= '<div class="row">';
    $html .= '<div class="form-group" style="padding-left: 1%">';
    $html .= '<input type="hidden" id="name-sub-post" name="sub-type" value="31">';
    $html .= '<input type="hidden" id="date-sub-post" name ="date" value="0">';
    $html .= '<input type="hidden" id="time-sub-post" name="time" value="0">';
    $html .= '<input type="hidden" id="duration-sub-post" name="duration" value="0">';
    $html .= '<div style="color: #2E6690;">The following Tutoring Schedule has been cancelled.</div>';
    $html .= '<div class="line-schedule2"></div>';
    $html .= '<div class="left-15">';
    $html .= '<div class="inline"><span class="css-span1">Subject:</span></div>';
    $html .= '<div class="inline"><span class="css-span1">Name:</span></div>';
    $html .= '<div class="inline"><span class="css-span1">Date:</span></div>';
    $html .= '<div class="inline"><span class="css-span1">Time:</span></div>';
    $html .= '<div class="inline"><span class="css-span1">Duration:</span></div>';
    $html .= '</div>';
    $html .= '<div class="right-85">';
    $html .= '<p class="col-xs-12 font-bold-4c4c4c css-padding-left-0" id="date-subject-refunded">'.$result[0]->subject.'</p>';
    $html .= '<p class="col-xs-12 font-bold-4c4c4c css-padding-left-0" id="date-name-refunded">'.$result[0]->private_subject.'</p>';
    $html .= '<p class="col-xs-12 font-bold-4c4c4c css-padding-left-0" id="date-sub-refunded">'.$result[0]->date.'</p>';
    $html .= '<p class="col-xs-12 font-bold-4c4c4c css-padding-left-0" id="time-sub-refunded">'.$result[0]->time.'</p>';
    $html .= '<p class="col-xs-12 font-bold-4c4c4c css-padding-left-0" id="duration-sub-refunded" >'.$result[0]->total_time.' Minutes Total'.'</p>';
    $html .= '</div>';
    $html .= '</div>';                          
    $html .= '<div class="row">';                          
    $html .= '<div class="col-sm-12 padding-top-2">';                          
    $html .= '<div class="box-gray-dialog" style="text-align: left">';   
    $total_time = $result[0]->total_time;
    $pst = mw_get_option('price_schedule_tutoring');
    $total = $total_time * $pst/100;
    $html .= 'Total Refunded:<span class="currency" style="color:#697E31;font-weight: bold;" id="total-refunded-points">'.' '. $total .'</span><span style="font-weight: normal;"> Points</span>';                          
    $html .= '</div>';
    $html .= '</div>';                          
    $html .= '</div>'; 
    
    echo $html;
    die;
}
if($task == "update_total_point"){
    $point = $_REQUEST['point']; 
    $date = $_REQUEST['date']; 
    $get_point = ik_get_user_points($user->ID);
    $point = $point+$get_point;
    MWDB::update_refunded_point($point,$date);   
    exit();
}
if($task == "update_wp_dict_tutoring"){
    $point = $_REQUEST['point']; 
    $date = $_REQUEST['arr']; 
    $get_point = ik_get_user_points($user->ID);
    if($get_point>=$point) {
        foreach ($date as $value){
            MWDB::update_wp_dic_tutoring_plan($value);   
        }
    }
    exit();
}
if($task == "update-id-home") {
//    echo 1;die;
    $homes_id = MWDB::get_id_all_dict_homeworks();
    for($i=0; $i<count($homes_id)-1;$i++) {
        $id = $homes_id[$i]->id;
        $id_next = $homes_id[$i+1]->id;
        MWDB::update_next_homework_id_for_homeworks($id,$id_next);
    }
    exit();
}
if($task == "check_sub_by_type"){
    $type = $_REQUEST['type']; 
    $is_sat_class_subscribed = is_sat_class_subscribed($type);
    if($is_sat_class_subscribed==false ||$is_sat_class_subscribed==null) {
        echo 0;
    }else{
        echo "1";
    }
    exit();
}