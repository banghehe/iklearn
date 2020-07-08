<?php

/*
 * functions to handle subscription, shopping cart, transaction
 * and teaching related
 */

/*
 * init subscription status
 * this function set user and device subscription status to unsubscribed
 */

function init_subscription_status() {
    if (empty($_SESSION['subscription'])) {
        // init user subscription
        ik_init_user_subscription();

        // init device subscription
        $_SESSION['device_subscription'] = get_subscription_status();
    }
}

/*
 * set homework tool subscription
 */

function set_hw_tool_subscription($status) {
    $_SESSION['subscription']['HW_TOOL'] = $status;
}

/*
 * set homework tool subscription for math
 */

function set_math_hw_tool_subscription($status) {
    $_SESSION['subscription']['HW_TOOL_MATH'] = $status;
}

/*
 * set self-study subscription
 */

function set_self_study_subscription($status) {
    $_SESSION['subscription']['SELF_STUDY'] = $status;
}

/*
 * set self-study subscription for math
 */

function set_math_self_study_subscription($status) {
    $_SESSION['subscription']['SELF_STUDY_MATH'] = $status;
}

/*
 * set Math tutoring basic subscription for math
 */

function set_math_tutoring_basic_subscription($status) {
    $_SESSION['subscription']['54'] = $status;
}

/*
 * set Math tutoring intensive subscription for math
 */

function set_math_tutoring_intensive_subscription($status) {
    $_SESSION['subscription']['55'] = $status;
}

/*
 * set dictionary subscription
 *
 * @param int $dictionary_id
 * @param bool $status
 * @param bool $device_subscription
 *
 */

function set_dictionary_subscription($dictionary_id, $status, $device_subscription = false) {
    $_SESSION['subscription']['DICTIONARY'][$dictionary_id] = $status;

    // store device subscription so that it will not be cleared after the user logged out
    if ($device_subscription) {
        $_SESSION['device_subscription']['DICTIONARY'][$dictionary_id] = $status;
    }
}

/*
 * set SAT subscription
 */

function set_sat_subscription($class_type_id, $status) {
    $_SESSION['subscription']['SAT'][$class_type_id] = $status;
}

/*
 * get subscription status
 */

function get_subscription_status() {
    return $_SESSION['subscription'];
}

/*
 * get device subscription status
 */

function get_device_subscription_status() {
    return $_SESSION['device_subscription'];
}

/*
 * push an item to device subscription list
 *
 * @param object $data
 */

function set_device_sub_list($data) {
    $_SESSION['DEVICE_SUB_DETAIL'][$data->id] = $data;
}

/*
 * return device subscription list
 *
 * @return array
 */

function get_device_sub_list() {
    return (array) $_SESSION['DEVICE_SUB_DETAIL'];
}

/*
 * clear device subscription list
 */

function clear_device_sub_list() {
    $_SESSION['DEVICE_SUB_DETAIL'] = array();
}

/*
 * update user subscription status
 *
 * @param int $user_id
 */

function update_user_subscription($user_id = 0) {
    global $wpdb;

    $user_id = $user_id ? $user_id : get_current_user_id();

    // if the user is admin, set all subscription to true.
    if (is_mw_admin($user_id) || is_mw_super_admin($user_id)) {
        ik_init_user_subscription(true);
        return;
    }

    // get user's subscription
    $subs = $wpdb->get_results('SELECT typeid, sat_class_id, dictionary_id
								FROM ' . $wpdb->prefix . 'dict_user_subscription
								WHERE activated_by = ' . $user_id . ' AND activated_on <= \'' . date('Y-m-d', time()) . '\' AND expired_on >= \'' . date('Y-m-d', time()) . '\'');
    //subscription expand
    //free_SAT();
    // check which type of subscription the user's subscribed
    foreach ($subs as $sub) {
        switch ($sub->typeid) {
            case SUB_TEACHER_TOOL: // the user has subscribed Homework Tool
                set_hw_tool_subscription(true);
                set_dictionary_subscription($sub->dictionary_id, true);
                break;
            case SUB_DICTIONARY: // the user has subscribed Dictionary
                set_dictionary_subscription($sub->dictionary_id, true);
                break;
            case SUB_SAT_PREPARATION: // the user has subscribed SAT
            case SUB_MATH_SAT_I_PREP:
            case SUB_MATH_SAT_II_PREP:
            case SUB_MATH_CLASS_IK:
            case SUB_PACKAGE:
                switch ($sub->sat_class_id) {
                    case 31:
                        set_sat_subscription(CLASS_SAT1, true);
                        set_sat_subscription(CLASS_MATH_SAT1A, true);
                        break;
                    case 38:
                        set_sat_subscription(CLASS_MATH_IK, true);
                        break;
                    default :
                        set_sat_subscription($sub->sat_class_id, true);
                        break;
                }
                break;
            case SUB_MATH_TUTORING_BASIC:
                set_math_tutoring_basic_subscription(true);
                break;
            case SUB_MATH_TUTORING_INTENSIVE:
                set_math_tutoring_intensive_subscription(true);
                break;
            case SUB_SELF_STUDY: // the user has subscribed Self study
                set_self_study_subscription(true);
                set_dictionary_subscription($sub->dictionary_id, true);
                break;
            case SUB_TEACHER_TOOL_MATH: // the user has subscribed Homework Tool for Math
                set_math_hw_tool_subscription(true);
                break;
            case SUB_SELF_STUDY_MATH: // the user has subscribed Self study for Math
                set_math_self_study_subscription(true);
                break;
        }
    }

    // check if user has joined a Homework Tool subscribed group
    $sub_groups = $wpdb->get_results('SELECT us.typeid, us.dictionary_id
								 FROM ' . $wpdb->prefix . 'dict_user_subscription AS us
								 JOIN ' . $wpdb->prefix . 'dict_group_students AS gs ON gs.group_id = us.group_id
								 WHERE student_id = ' . $user_id . ' AND absented = 0');

    foreach ($sub_groups as $sub_group) {
        // the user inherit Dictionary subscription from the group
        set_dictionary_subscription($sub_group->dictionary_id, true);
    }
}

/*
 * init user subscription
 */

function ik_init_user_subscription($status = false) {
    $status1 = $status2 = $status3 = $status4 = $status5 = $status6 = $status7 = $status;
    $status8 = $status9 = $status10 = $status11 = $status12 = $status;
    // Teacher Homework Tool
    $_SESSION['subscription']['HW_TOOL'] = $status;

    // Teacher Homework Tool for Math
    $_SESSION['subscription']['HW_TOOL_MATH'] = $status;

    // Dictionary
    $_SESSION['subscription']['DICTIONARY'] = array(
        1 => $status, // E Learner's Dictionary
        2 => $status, // Collegiate Dictionary
        3 => $status, // Medical Dictionary
        4 => $status, // Intermediate Dictionary
        5 => $status, // Elementary Dictionary
        6 => $status  // All Dictionary
    );

    // SAT Preparation
    $_SESSION['subscription']['SAT'] = array(
        // English side
        CLASS_GRAMMAR => $status, // Vocabulary / Grammar
        CLASS_WRITING => $status, // Writing Skills
        CLASS_SAT1 => $status, // SAT Test 1
        CLASS_SAT2 => $status, // SAT Test 2
        CLASS_SAT3 => $status, // SAT Test 3
        CLASS_SAT4 => $status, // SAT Test 4
        CLASS_SAT5 => $status, // SAT Test 5
        // Math side
        CLASS_MATH_SAT1PREP => $status,
        CLASS_MATH_SAT1A => $status,
        CLASS_MATH_SAT1B => $status,
        CLASS_MATH_SAT1C => $status,
        CLASS_MATH_SAT1D => $status,
        CLASS_MATH_SAT1E => $status,
        CLASS_MATH_SAT2PREP => $status,
        CLASS_MATH_SAT2A => $status,
        CLASS_MATH_SAT2B => $status,
        CLASS_MATH_SAT2C => $status,
        CLASS_MATH_SAT2D => $status,
        CLASS_MATH_SAT2E => $status,
        CLASS_MATH_IK => $status,
        CLASS_MATH_IK1 => $status1,
        CLASS_MATH_IK2 => $status2,
        CLASS_MATH_IK3 => $status3,
        CLASS_MATH_IK4 => $status4,
        CLASS_MATH_IK5 => $status5,
        CLASS_MATH_IK6 => $status6,
        CLASS_MATH_IK7 => $status7,
        CLASS_MATH_IK8 => $status8,
        CLASS_MATH_IK9 => $status9,
        CLASS_MATH_IK10 => $status10,
        CLASS_MATH_IK11 => $status11,
        CLASS_MATH_IK12 => $status12,
    );

    // Self-study Subscription
    $_SESSION['subscription']['SELF_STUDY'] = $status;

    // Self-study Subscription for Math
    $_SESSION['subscription']['SELF_STUDY_MATH'] = $status;
}

/*
 * check if user can assign homework to group
 *
 * @param int $group_id
 *
 * @return boolean
 */

function user_can_assign_homework($group_id, $stypeid) {
    // admin can always assign homework
    if (is_mw_admin() || is_mw_super_admin()) {
        return true;
    }

    global $wpdb;

    $rows = $wpdb->get_results($wpdb->prepare('SELECT id FROM ' . $wpdb->prefix . 'dict_user_subscription
								WHERE typeid = %d AND expired_on >= \'' . date('Y-m-d', time()) . '\' AND group_id = %d', $stypeid, $group_id));

    return !empty($rows);
}

/*
 * check if user subscribed homework tool
 * this function will check self-study subscription too.
 *
 * @return boolean
 */

function is_homework_tools_subscribed() {
    $subscription = get_subscription_status();

    $subscribed = $subscription['HW_TOOL'];

    // if user hasn't subscribed Homework Tool, we check if user has subscribed Self-study
    if (!$subscribed && $subscription['SELF_STUDY']) {
        $subscribed = true;
    }

    return $subscribed;
}

/*
 * check if user subscribed homework tool for math
 * this function will check self-study subscription for math too.
 *
 * @return boolean
 */

function is_math_homework_tools_subscribed() {
    $subscription = get_subscription_status();

    $subscribed = $subscription['HW_TOOL_MATH'];

    // if user hasn't subscribed Homework Tool, we check if user has subscribed Self-study
    if (!$subscribed && $subscription['SELF_STUDY_MATH']) {
        $subscribed = true;
    }

    return $subscribed;
}

/*
 * check if user subscribed dictionary
 *
 * @param mixed $dictionary		string slug or dictionary id
 *
 * @return boolean
 */

function is_dictionary_subscribed($dictionary) {
    if (mw_get_option('sub-popup-interval') == 0 || mw_get_option('sub-popup-times') == 0) {
        // if the interval or number of searched words is 0, then we always let user using dictionary
        return true;
    }

    if (!is_numeric($dictionary)) {
        $dictionary = get_dictionary_id_by_slug($dictionary);
    }
    $subscription = get_subscription_status();
    $device_subscription = get_device_subscription_status();

    if (!is_user_logged_in()) {
        return false;
    }
    $user_id = wp_get_current_user()->ID;    
    $check_subscription_library = MWDB::check_subscription_library($user_id,$dictionary);
//    var_dump($check_subscription_library);die;
    if (MWDB::check_subscription_library($user_id,6)) {
        return true;
    }
    if ($device_subscription['DICTIONARY'][$dictionary]) {
        return true;
    }
    if (!empty($check_subscription_library)) {  // check month để biết đã hết hạn chưa
//        var_dump(count($check_subscription_library));die;
        $num = 0;
        if(count($check_subscription_library)>1) {
            for($i=0; $i<count($check_subscription_library); $i++){
        //KIỂM TRA NẾU THẰNG THỨ NHẤT CÒN HẠN THÌ KHÔNG CHECK NHỮNG THẰNG SAU        
                if($i == 0){
                    $string = $check_subscription_library[$i]->purchased_on;   // ngày bắt đầu sub
                    $id_code = $check_subscription_library[$i]->credit_code_id;
                // số tháng mua nếu typeid =2 thì sẽ lấy số tháng ở bảng wp_dict_credit_codes
                // nếu typeid =5 sẽ lấy số tháng ở wp_dict_user_subscription
                    if($check_subscription_library[$i]->typeid == 2){
                        $str_month = MWDB::get_month_sub($id_code);
                        $month = $str_month[0]->no_of_months_dictionary;
                    }else if($check_subscription_library[$i]->typeid == 5 || $check_subscription_library[$i]->typeid == 1){
                        $str_month = MWDB::get_month_user_sub($check_subscription_library[$i]->user_subscription_id);
                        $month = $str_month[0]->number_of_months;
                    }
                // check hết hạn sub
                    $string = strtotime($string);
                    $str = '+'.$month.' Months';
                    $date_end = strtotime($str,$string); 
                    $time_current = strtotime(date("Y/m/d"));
                    if($time_current < $date_end) {
                        return true;
                    }
                // KIỂM TRA NẾU CHỈ CÓ 2 THẰNG THÌ NẾU THẰNG THỨ NHẤT HẾT HẠN TRƯỚC NGÀY SUB THẰNG 2 THÌ TÍNH THEO THẰNG 2 NGƯỢC LẠI    
                    else if(count($check_subscription_library)==2){
                        $date_two = $check_subscription_library[1]->purchased_on;
                        $id_code1 = $check_subscription_library[1]->credit_code_id;
                        $date_two = strtotime($date_two);
                        // ngày bắt đầu thằng 2 nhỏ hơn ngày hết hạn thằng 1 thì gộp các tháng mua và tính theo thời gian thằng 1
                        // gộp 2 tháng sub và tính ngày bắt đầu theo thằng 1
                        if($date_two < $date_end) {
                            if($check_subscription_library[1]->typeid == 2){
                                $str_month1 = MWDB::get_month_sub($id_code1);
                                $month1 = $str_month1[0]->no_of_months_dictionary;
                            }else if($check_subscription_library[1]->typeid == 5 || $check_subscription_library[1]->typeid == 1){
                                $str_month1 = MWDB::get_month_user_sub($check_subscription_library[1]->user_subscription_id);
                                $month1 = $str_month1[0]->number_of_months;
                            }
                            $month2 = $month + $month1;
                            $str2 = '+'.$month2.' Months';
                            $date_end2 = strtotime($str2,$string);
                            if($time_current < $date_end2) {
                                return true;
                            }
                        }
                        // tính theo thằng 2
                        else{
                            if($check_subscription_library[1]->typeid == 2){
                                $str_month1 = MWDB::get_month_sub($id_code1);
                                $month1 = $str_month1[0]->no_of_months_dictionary;
                            }else if($check_subscription_library[1]->typeid == 5 || $check_subscription_library[1]->typeid == 1){
                                $str_month1 = MWDB::get_month_user_sub($check_subscription_library[1]->user_subscription_id);
                                $month1 = $str_month1[0]->number_of_months;
                            }
                            $string3 = $check_subscription_library[1]->purchased_on;
                            $string3 = strtotime($string3);
                            $str3 = '+'.$month1.' Months';
                            $date_end3 = strtotime($str3,$string3);
//                            var_dump($str3);
//                            var_dump("__");
//                            var_dump($string3);
                            if($time_current < $date_end3) {
                                return true;
                            }
                        }
                    }
                }else {
                    $j = $i-1;
                    $date1 = $check_subscription_library[$j]->purchased_on;
                    $date2 = $check_subscription_library[$i]->purchased_on;
                    $id_code1 = $check_subscription_library[$j]->credit_code_id;
                    $id_code2 = $check_subscription_library[$i]->credit_code_id;
                    $string1 = strtotime($date1);
                    $string2 = strtotime($date2);
                    if($check_subscription_library[$j]->typeid == 2){
                        $str_month1 = MWDB::get_month_sub($id_code1);
                        $month1 = $str_month1[0]->no_of_months_dictionary;
                    }else if($check_subscription_library[$j]->typeid == 5 || $check_subscription_library[$j]->typeid == 1){
                        $str_month1 = MWDB::get_month_user_sub($check_subscription_library[$j]->user_subscription_id);
                        $month1 = $str_month1[0]->number_of_months;
                    }
                    if($check_subscription_library[$i]->typeid == 2){
                        $str_month2 = MWDB::get_month_sub($id_code2);
                        $month2 = $str_month2[0]->no_of_months_dictionary;
                    }else if($check_subscription_library[$i]->typeid == 5 || $check_subscription_library[$i]->typeid == 1){
                        $str_month2 = MWDB::get_month_user_sub($check_subscription_library[$j]->user_subscription_id);
                        $month2 = $str_month2[0]->number_of_months;
                    }
                    $str2 = '+'.$month2.' Months';
                    $date_end2 = strtotime($str2,$string2);
                    if($time_current < $date_end2) {
                        return true;
                    }else{
                        
                    }
                }
            }
        }else{
            $string = $check_subscription_library[0]->purchased_on;   // ngày bắt đầu sub
            $string = strtotime($string);
            $id_code = $check_subscription_library[0]->credit_code_id;
            // số tháng mua nếu typeid =2 thì sẽ lấy số tháng ở bảng wp_dict_credit_codes
            // nếu typeid =5 sẽ lấy số tháng ở wp_dict_user_subscription
                if($check_subscription_library[0]->typeid == 2){
                    $str_month = MWDB::get_month_sub($id_code);
                    $month = $str_month[0]->no_of_months_dictionary;
                }else if($check_subscription_library[0]->typeid == 5 || $check_subscription_library[$i]->typeid == 1){
                    $str_month = MWDB::get_month_user_sub($check_subscription_library[$i]->user_subscription_id);
                    $month = $str_month[0]->number_of_months;
                }
            // check hết hạn sub
            $str = '+'.$month.' Months';
            $date_end = strtotime($str,$string); 
            $time_current = strtotime(date("Y/m/d"));
//            var_dump($string);die;
            if($time_current < $date_end) {
                return true;
            }
        }
    }    
    return false;
}

/*
 * check if user subscribed any dictionary (sử dụng để cho phép tạo folder)
 *
 * @param mixed $user_id ()
 *
 * @return boolean
 */

function check_user_subscrible_any_dictionary($user_id) { 
   for ($i=1;$i<=6;$i++) {
       $check_subscription_library = MWDB::check_subscription_library($user_id,$i);
       if(!empty($check_subscription_library)){
           return true;
       } 
   }
    return false;
}
/*
 * check if user subscribed SAT Preparation class
 *
 * @param int $class_id		the class id
 *
 * @return boolean
 */

function is_sat_class_subscribed($class_id) {
    $subscription = get_subscription_status();

    return $subscription['SAT'][$class_id];
}

/*
 * check if user subscribed Math tutoring class
 *
 * @param int $class_id		the class id
 *
 * @return boolean
 */

function is_math_tutoring_class_subscribed($class_id) {
    $subscription = get_subscription_status();

    return $subscription[$class_id];
}

/*
 * return subscription fee of SAT
 *
 * @param int $id	class type id
 *
 * @return mixed
 */

function ik_get_sat_subscription_fee($id) {
    switch ($id) {
        case CLASS_GRAMMAR:
            $price = number_format(mw_get_option('sat-grammar-price'), 2);
            break;
        case CLASS_WRITING:
            $price = number_format(mw_get_option('sat-writing-price'), 2);
            break;
        case CLASS_SAT1:
        case CLASS_SAT2:
        case CLASS_SAT3:
        case CLASS_SAT4:
        case CLASS_SAT5:
            $price = number_format(mw_get_option('sat-test-price'), 2);
            break;
        case CLASS_MATH_SAT1PREP:
            $price = number_format(mw_get_option('math-sat1-preparation'), 2);
            break;
        case CLASS_MATH_SAT1A:
        case CLASS_MATH_SAT1B:
        case CLASS_MATH_SAT1C:
        case CLASS_MATH_SAT1D:
        case CLASS_MATH_SAT1E:
            $price = number_format(mw_get_option('math-sat1-price'), 2);
            break;
        case CLASS_MATH_SAT2PREP:
            $price = number_format(mw_get_option('math-sat2-preparation'), 2);
            break;
        case CLASS_MATH_SAT2A:
        case CLASS_MATH_SAT2B:
        case CLASS_MATH_SAT2C:
        case CLASS_MATH_SAT2D:
        case CLASS_MATH_SAT2E:
            $price = number_format(mw_get_option('math-sat2-price'), 2);
            break;
        case CLASS_MATH_IK:
            $price = number_format(mw_get_option('math-ik-price'), 2);
            break;
        case CLASS_MATH_IK1:
            $price = number_format(mw_get_option('math-ik-price1'), 2);
            break;
        case CLASS_MATH_IK2:
            $price = number_format(mw_get_option('math-ik-price2'), 2);
            break;
        case CLASS_MATH_IK3:
            $price = number_format(mw_get_option('math-ik-price3'), 2);
            break;
        case CLASS_MATH_IK4:
            $price = number_format(mw_get_option('math-ik-price4'), 2);
            break;
        case CLASS_MATH_IK5:
            $price = number_format(mw_get_option('math-ik-price5'), 2);
            break;
        case CLASS_MATH_IK6:
            $price = number_format(mw_get_option('math-ik-price6'), 2);
            break;
        case CLASS_MATH_IK7:
            $price = number_format(mw_get_option('math-ik-price7'), 2);
            break;
        case CLASS_MATH_IK8:
            $price = number_format(mw_get_option('math-ik-price8'), 2);
            break;
        case CLASS_MATH_IK9:
            $price = number_format(mw_get_option('math-ik-price9'), 2);
            break;
        case CLASS_MATH_IK10:
            $price = number_format(mw_get_option('math-ik-price10'), 2);
            break;
        case CLASS_MATH_IK11:
            $price = number_format(mw_get_option('math-ik-price11'), 2);
            break;
        case CLASS_MATH_IK12:
            $price = number_format(mw_get_option('math-ik-price12'), 2);
            break;
        case CLASS_ENGLISH_PACKAGE:
            $price = number_format(mw_get_option('sat-grammar-price'), 2) + number_format(mw_get_option('sat-writing-price'), 2) + number_format(mw_get_option('sat-test-price'), 2) * 5 * (1 - (int) mw_get_option('sat-package-price-discount') / 100);
            break;
        case CLASS_MATH_PACKAGE_1:
            $price = number_format(mw_get_option('math-sat1-price'), 2) * 6 * (1 - (int) mw_get_option('sat-math-i-package-price-discount') / 100);
            break;
        case CLASS_MATH_PACKAGE_2:
            $price = number_format(mw_get_option('math-sat2-price'), 2) * 6 * (1 - (int) mw_get_option('sat-math-ii-package-price-discount') / 100);
            break;
    }

    return $price;
}

/*
 * generate credit code
 */

function generate_credit_code($subscription_type, $teacher_m, $num_students, $dictionary_id, $dictionary_m, $auto_generated = false, $sat_m = '', $sat_class = '', $num_points = 0, $is_math = false) {
    global $wpdb;

    if ($teacher_m == 10 || $teacher_m == 11 || $teacher_m > 12) {
        $teacher_m_digit = 0;
    } else if ($teacher_m == 12) {
        $teacher_m_digit = 9;
    } else {
        $teacher_m_digit = $teacher_m;
    }

    if ($dictionary_m % DICTIONARY_MONTHS_MULTIPLIER == 0 && $dictionary_m <= (4 * DICTIONARY_MONTHS_MULTIPLIER)) {
        $dictionary_m_digit = $dictionary_m / DICTIONARY_MONTHS_MULTIPLIER;
    } else {
        $dictionary_m_digit = 0;
    }
    //end action for dic_m
    $num_students = is_null($num_students) ? 0 : $num_students;
    $dictionary_m = is_null($dictionary_m) ? 0 : $dictionary_m;
    $teacher_m = is_null($teacher_m) ? 0 : $teacher_m;
    $sat_m = is_null($sat_m) ? 0 : $sat_m;


    $encoding_table = array(
        array(7, 6, 3, 2, 6, 7, 4, 1, 8, 5, 6, 5, 6, 5, 2, 4),
        array(1, 0, 7, 1, 0, 0, 3, 7, 0, 8, 1, 2, 2, 4, 9, 8),
        array(6, 7, 1, 6, 5, 2, 8, 6, 3, 0, 3, 9, 1, 0, 1, 0),
        array(8, 4, 4, 8, 8, 8, 7, 0, 7, 7, 7, 1, 7, 8, 6, 3),
        array(0, 3, 6, 0, 4, 6, 2, 9, 1, 9, 2, 6, 5, 6, 8, 5),
        array(5, 9, 0, 7, 1, 5, 5, 8, 9, 1, 9, 3, 0, 2, 3, 9),
        array(2, 5, 8, 9, 7, 9, 9, 2, 2, 6, 4, 0, 8, 9, 0, 6),
        array(4, 1, 2, 3, 9, 3, 1, 4, 5, 2, 8, 7, 3, 3, 5, 1),
        array(3, 8, 9, 5, 3, 1, 6, 5, 6, 3, 0, 8, 9, 7, 7, 8),
        array(9, 2, 5, 4, 2, 4, 0, 3, 4, 4, 5, 4, 4, 1, 4, 2),
    );

    // serial number
    if (!$is_math) {
        $sn = $wpdb->get_row('SELECT LPAD(sn + 1, 8, \'0\') AS new_sn FROM ' . $wpdb->prefix . 'dict_credit_code_serial');
        $sn = $sn->new_sn;
    } else {
        $sn = $wpdb->get_row('SELECT LPAD(snm + 1, 8, \'0\') AS new_sn FROM ' . $wpdb->prefix . 'dict_credit_code_serial');
        $sn = $sn->new_sn;
    }

    // 1st digit
    // Check digit
    $number = rand(0, 9);
    // 2nd digit
    // No. of month to subscribe teacher’s tool – 0 means no subscription.
    // This credit code is sued only for dictionary subscription in this case.
    // 12 months will be subtitude by digit 9
    $number .= $teacher_m_digit;
    // 3rd digit
    // NO. of student. – each unit is 5 students. 5, 10, 15, 20 students.
    $number .= revert_num_students_to_digit($num_students);
    // 4th digit
    // Dictionary type – My choice means user select dictionary when they enter the credit code and activate it. 
    // If dictionary is selected, the activation code just activate the dictionary for the number of month. 
    // Teacher’s tool subscriber gets one dictionary free for the same period as tools.
    $number .= $dictionary_id;
    // 5th digit
    // Check digit
    $number .= rand(0, 9);
    // 6th degit
    // No. of month for dictionary subscription – When dictionary only is subscribed, no. of dictionary is used.
    // Each unit is 6 months, so, 6, 12, 18, 24, months, etc.
    $number .= $dictionary_m_digit;
    // Digit 7 - 8 is from serial number
    $number .= $sn[0];
    $number .= $sn[1];
    // 9th degit
    // Check digit
    $number .= rand(0, 9);
    // Digit 10 - 12 is from serial number
    $number .= $sn[2];
    $number .= $sn[3];
    $number .= $sn[4];
    // 13rd degit
    // Check digit
    $number .= rand(0, 9);
    // Digit 14 - 16 is from serial number
    $number .= $sn[5];
    $number .= $sn[6];
    $number .= $sn[7];
    // step 0
    for ($i = 0; $i < strlen($number); $i++) {
        $n0[$i] = $encoding_table[$number[$i]][$i];
    }

    $encoded = mw_encode_credit_code($n0);

    ksort($encoded);
    $encoded = implode('', $encoded);

    // store the new credit code in the database
    $data['original_code'] = $number;
    $data['encoded_code'] = $encoded;
    $data['typeid'] = $subscription_type;
    $data['no_of_months_teacher_tool'] = $teacher_m;
    $data['no_of_students'] = $num_students;
    $data['no_of_months_dictionary'] = $dictionary_m;
    $data['dictionary_id'] = $dictionary_id;
    $data['num_points'] = $num_points;
    $data['sn'] = $sn;
    $data['is_math'] = $is_math;
    if ($auto_generated) {
        $data['auto_generated'] = 1;
    }

    if (!empty($sat_m)) {
        $data['no_of_months_sat'] = $sat_m;
        $data['sat_class_id'] = $sat_class;
        if ($is_math && ($sat_class > MATH_SAT2_S && $sat_class <= MATH_SAT2_E)) {
            $data['typeid'] ++;
        }
        if ($is_math && ($sat_class >= MATH_IKMATH1 && $sat_class <= MATH_IKMATH2)) {
            $data['typeid'] = 12;
        }
    }

    MWDB::store_credit_code($data);

    return $encoded;
}

/*
 * Recursion function to encode the array. Default to 4 times
 * 
 * @param array $a		Original array
 * @param int $step		Current step
 *
 * @return array
 */

function mw_encode_credit_code($a, $step = 1) {
    // recursion 4 times
    if ($step == 5) {
        return $a;
    }

    if ($step == 1) {
        $seed_index = 0;
    } else {
        $seed_index = $step - 1;
    }

    // init seed number
    $temp[$seed_index] = $a[$seed_index];

    // left side
    for ($j = $seed_index; $j > 0; $j--) {
        $temp[$j - 1] = ($temp[$j] + $a[$j - 1]) % 10;
    }

    // right side
    for ($i = $seed_index; $i < 15; $i++) {
        $temp[$i + 1] = ($temp[$i] + $a[$i + 1]) % 10;
    }

    // increase step by 1, recursion
    $step++;

    $encoded = mw_encode_credit_code($temp, $step);

    return $encoded;
}

// translate digit to number of students
function trans_num_student_digit($digit) {
    $a = array(
        0 => mw_get_option('min-students-subscription'),
        1 => 5,
        2 => 10,
        3 => 20,
        4 => 30,
        5 => 50,
        6 => 100,
        7 => 150,
        8 => 300,
        9 => 500
    );

    return $a[$digit];
}

// revert number of students to single digit
function revert_num_students_to_digit($num) {
    $min = mw_get_option('min-students-subscription');

    $a = array(
        $min => 0,
        5 => 1,
        10 => 2,
        20 => 3,
        30 => 4,
        50 => 5,
        100 => 6,
        150 => 7,
        300 => 8,
        500 => 9
    );

    return isset($a[$num]) ? $a[$num] : 0;
}

/*
 * add points to user account
 *
 * @param int $points
 * @param int $user_id
 *
 * @return int		return user points after added
 */

function ik_add_user_points($points, $user_id = 0) {
    $user_id = $user_id ? $user_id : get_current_user_id();

    $user_points = get_user_meta($user_id, 'user_points', true);

    $user_points = empty($user_points) ? $points : $user_points + $points;

    update_user_meta($user_id, 'user_points', $user_points);

    return $user_points;
}

/*
 * deduct points from user account
 *
 * @param int $points
 * @param int $user_id
 *
 * @return mixed		return remaining points after deduction or false if user doesn't have enough points
 */

function ik_deduct_user_points($points, $user_id = 0) {
    $user_id = $user_id ? $user_id : get_current_user_id();

    $user_points = get_user_meta($user_id, 'user_points', true);

    $user_points = empty($user_points) ? 0 : $user_points;

    // return false if user doesn't have enough points
    if ($user_points < $points) {
        return false;
    }

    $user_points = $user_points - $points;

    update_user_meta($user_id, 'user_points', $user_points);

    return $user_points;
}

/*
 * get the amount of points in user account
 *
 * @param int $user_id
 *
 * @return int
 */

function ik_get_user_points($user_id = 0) {
    $user_id = $user_id ? $user_id : get_current_user_id();

    $user_points = get_user_meta($user_id, 'user_points', true);

    $user_points = empty($user_points) ? 0 : $user_points;

    return $user_points;
}

function ik_get_user_earned($user_id = 0) {
    $user_id = $user_id ? $user_id : get_current_user_id();

    $user_earned = get_user_meta($user_id, 'user_earned', true);

    $user_earned = empty($user_earned) ? 0 : $user_earned;

    return $user_earned;
}
/*
 * convert point to price
 */

function ik_convert_points_to_price($points) {
    $point_ex_rate = mw_get_option('point-exchange-rate');

    return $points / $point_ex_rate;
}

/*
 * convert price to point
 */

function convert_price_to_points($price) {
    $point_ex_rate = mw_get_option('point-exchange-rate');

    return $price * $point_ex_rate;
}

/*
 * gift free point to a user
 */

function ik_gift_user_points($points, $user_id) {
    global $wpdb;

    // gift the points
    ik_add_user_points($points, $user_id);

    // store transaction history
    $result = $wpdb->insert(
            $wpdb->prefix . 'dict_user_point_transactions', array(
        'user_id' => $user_id,
        'point_transaction_type_id' => POINT_TXN_GIFT,
        'amount' => $points,
        'transaction_date' => date('Y-m-d H:m:i', time()),
        'note' => __('The credit from iklearn.com', 'iii-dictionary')
            )
    );

    return $result;
}

/*
 * get current user shopping cart
 */

function get_user_cart($user_id = 0) {
    $user_id = $user_id ? $user_id : get_current_user_id();

    $cart = MWDB::get_user_shopping_cart($user_id);

    if (!empty($cart)) {
        $cart->items = json_decode($cart->items, true);
        foreach ($cart->items as $k => $v) {
            $cart->items[$k] = (object) $v;
        }
    }

    return $cart;
}

/*
 * get current user time basic
 */

function get_user_time($user_id = 0) {
    $user_id = $user_id ? $user_id : get_current_user_id();

    $cart = MWDB::get_math_request($user_id);

    if (!empty($cart)) {
        $cart->basic = json_decode($cart->basic, true);
        foreach ($cart->basic as $k => $v) {
            $cart->basic[$k] = (object) $v;
        }
    }

    return $cart;
}

/*
 * get current user time intensive
 */

function get_user_time_intensive($user_id = 0) {
    $user_id = $user_id ? $user_id : get_current_user_id();

    $cart = MWDB::get_math_request($user_id);
//    var_dump($cart);
//    die;
    if (!empty($cart)) {
        $cart->intensive = json_decode($cart->intensive, true);
        foreach ($cart->intensive as $k => $v) {
            $cart->intensive[$k] = (object) $v;
        }
    }

    return $cart;
}

/*
 * add item to cart
 * 
 * @param array $data
 *
 */

function ik_add_to_cart(&$data) {
    global $wpdb;

    $current_user_id = get_current_user_id();

    // get shopping cart from db
    $cart = get_user_cart($current_user_id);

    if (!empty($cart)) {
        $_SESSION['cart']['items'] = $cart->items;
        $_SESSION['cart']['total_amount'] = $cart->total_amount;
        $_SESSION['cart']['cid'] = $cart->cid;
    } else {
        $_SESSION['cart']['items'] = array();
        $_SESSION['cart']['total_amount'] = 0;
        $_SESSION['cart']['cid'] = 0;
    }

    $item = new stdClass;
    $item->extending = false;

    // check if user want to extend current subscription
    
    if (!empty($data['sub-id'])) {
        $user_sub = MWDB::get_user_subscription_details($data['sub-id']);
//        echo '<pre>';
//        print_r($user_sub);
//        die;
        $item->extending = true;
        $item->sub_id = $data['sub-id'];
        $item->activation_code_id = $user_sub->activation_code_id;
        $item->extend_students = false;

        if (empty($user_sub)) {
            // invalid user subscription id
            return false;
        }

        switch ($user_sub->typeid) {
            // extend Homework Tool subscription
            case SUB_TEACHER_TOOL:
            case SUB_TEACHER_TOOL_MATH:
                // set no students or no of months to code value based what user want to extend
                if (empty($data['teacher-tool-months'])) {
                    $data['teacher-tool-months'] = $user_sub->number_of_months;
                } else {
                    $data['no-m-dict'] = $data['teacher-tool-months'];
                }

                if (empty($data['no-students'])) {
                    $data['no-students'] = $user_sub->number_of_students;
                } else {
                    $item->extend_students = true;
                }
                break;
            // extend Dictionary subscription
            case SUB_DICTIONARY:
                $data['no-students'] = $user_sub->number_of_students;
                break;
            // extend Self study subscription
            case SUB_SELF_STUDY:
            case SUB_SELF_STUDY_MATH:
                $data['no-m-dict'] = $data['teacher-tool-months'];
                $data['self-study-months'] = $data['teacher-tool-months'];
                $data['no-students'] = $user_sub->number_of_students;
                break;
        }

        $data['sub-type'] = $user_sub->typeid;
        $data['dictionary'] = $user_sub->dictionary_id;
        $data['assoc-group'] = $user_sub->group_id;
    }
     switch ($data['sub-type']) {
        // user want to purchase Dictionary Subscription
        case SUB_DICTIONARY:
//            var_dump($data);die;
            if($data['dictionary']==1) {
                $type = 'E Learner’s Dictionary';
            }else if($data['dictionary']==2){
                $type = 'Collegiate Dictionary';
            }else if($data['dictionary']==3){
                $type = 'Medical Dictionary';
            }else if($data['dictionary']==4){
                $type = 'Intermediate Dictionary';
            }else if($data['dictionary']==5){
                $type = 'Elementary Dictionary';
            }
            
            $data['no-m-dict'] = $data['sat-months'];
            if($data['dictionary']==""){
                $data['dictionary'] = $data['dictionary_id'];
            }
            $price = $data['no-students'] * $data['sat-months'];
            $item->no_months = $data['sat-months'];
            
            break;
        // user want to purchase Homework Tool Subscription
        case SUB_TEACHER_TOOL:
            $type = 'Teacher’s Homework Tool';
            $price = $data['sat-months'] * $data['no-students'] * mw_get_option('teacher-tool-price') / 100;
            $item->no_months = $data['sat-months'];
            break;
        case SUB_TEACHER_TOOL_MATH:
            $type = 'Homework Tool';
            $price = $data['sat-months'] * $data['user-teach-tool'] * mw_get_option('teacher-tool-price') / 100;
            $item->no_months = $data['sat-months'];
            break;
        // user want to purchase SAT Preparation Subscription
        case SUB_SAT_PREPARATION:
            $class = MWDB::get_group_class_type_by('id', $data['sat-class']);
            if($data['sat-class']==1){
                $type = 'SAT Prep - Grammar Review';
            }else if ($data['sat-class']==2){
                $type = 'SAT Prep - Writing Practice';
            }else {
                $type = 'English ' . $class->name;
            }
            $price = ik_get_sat_subscription_fee($data['sat-class']) * $data['sat-months'];
            $item->sat_months = $data['sat-months'];
            $item->sat_class = $data['sat-class'];
            $item->no_months = $data['sat-months'];
            break;
        case SUB_MATH_SAT_I_PREP:
            if($data['sat-class-7'] != "") {
                $class = MWDB::get_group_class_type_by('id', $data['sat-class-7']);
                $item->sat_clas1s = $data['sat-class-7'];
            }else{
                $class = MWDB::get_group_class_type_by('id', $data['sat-class']);
                $item->sat_clas1s = $data['sat-class'];
            }
            $type = $class->name;
            if($data['sat-class'] == 9){
                $price = $data['sat-months'] * mw_get_option('math-sat1-price');
            }else{
                $price = $data['sat-months'] * mw_get_option('sat-test-price');
            }
            $item->sat_months = $data['sat-months'];
            $item->no_months = $data['sat-months'];
            break;
        case SUB_MATH_SAT_II_PREP:
            if($data['sat-class-8'] != "") {
                $class = MWDB::get_group_class_type_by('id', $data['sat-class-8']);
                $item->sat_clas1s = $data['sat-class-8'];
            }else{
                $class = MWDB::get_group_class_type_by('id', $data['sat-class']);
                $item->sat_clas1s = $data['sat-class'];
            }
            $type = $class->name;
            if($data['sat-class'] == 15){
                $price = $data['sat-months'] * mw_get_option('math-sat2-price');
            }else{
                $price = $data['sat-months'] * mw_get_option('sat-test-price');
            }
            $item->sat_months = $data['sat-months'];
            $item->sat_class = $data['sat-class'];
            $item->no_months = $data['sat-months'];
            break;
        case SUB_PACKAGE:
            $class = MWDB::get_group_class_type_by('id', $data['sat-class']);
            //var_dump($class);
            if ((int) $data['sat-class'] == 51) {
                $price = ik_get_sat_subscription_fee($data['sat-class']) * $data['sat-months'];
                $type = $class->name;
            } else if ((int) $data['sat-class'] == 52) {
                $price = ik_get_sat_subscription_fee($data['sat-class']) * $data['sat-months'];
                $type = $class->name;
            } else if ((int) $data['sat-class'] == 53) {
                $price = ik_get_sat_subscription_fee($data['sat-class']) * $data['sat-months'];
                $type = $class->name;
            } else if ((int) $data['sat-class'] == 3) {
                $price = ik_get_sat_subscription_fee($data['sat-class']) * $data['sat-months'];
                $type = 'SAT Preparation - ' . $class->name;
            }else if ((int) $data['sat-class'] == 4) {
                $price = ik_get_sat_subscription_fee($data['sat-class']) * $data['sat-months'];
                $type = 'SAT Preparation - ' . $class->name;
            }else if ((int) $data['sat-class'] == 5) {
                $price = ik_get_sat_subscription_fee($data['sat-class']) * $data['sat-months'];
                $type = 'SAT Preparation - ' . $class->name;
            }else if ((int) $data['sat-class'] == 6) {
                $price = ik_get_sat_subscription_fee($data['sat-class']) * $data['sat-months'];
                $type = 'SAT Preparation - ' . $class->name;
            }else if ((int) $data['sat-class'] == 7) {
                $price = ik_get_sat_subscription_fee($data['sat-class']) * $data['sat-months'];
                $type = 'SAT Preparation - ' . $class->name;
            }
            $item->sat_months = $data['sat-months'];
            $item->sat_class = $data['sat-class'];
            $item->no_months = $data['sat-months'];
            break;
        case SUB_MATH_CLASS_IK:
            $class = MWDB::get_group_class_type_by('id', $data['sat-class']);
          // var_dump($class->name);
            $type = 'ikMath Course  - ' . $class->name;
            $price = ik_get_sat_subscription_fee($data['sat-class']) * $data['sat-months'];
            $item->sat_months = $data['sat-months'];
            if($data['sat-class-12'] != "") {
                $item->sat_class = $data['sat-class-12'];
            }else{
                $item->sat_class = $data['sat-class'];
            }
            $item->no_months = $data['sat-months'];
            break;
        // user want to purchase Points
        case SUB_POINTS_PURCHASE:
            $type = 'Points';
            $price = $_POST['no-of-points'] / mw_get_option('point-exchange-rate');
            $item->no_months = 'N/A';
            $item->no_of_points = $_POST['no-of-points'];
            break;
        // user want to purchase Self-study subscription
        case SUB_SELF_STUDY:
        case SUB_SELF_STUDY_MATH:
            if($data['sub-type']==SUB_SELF_STUDY) {
                $type = 'English Self-study';
            }else {
                $type = 'Math Self-study';
            }
            $item_price = $data['sub-type'] == SUB_SELF_STUDY ? mw_get_option('self-study-price') : mw_get_option('math-self-study-price');
//            var_dump($item_price);die;
            if(!is_null($item->self-study-months)){
                $item->no_months = $data['self-study-months'];
                $price = $item_price * $data['self-study-months'];
            }else{
                $item->no_months = $data['sat-months'];
                $price = $item_price * $data['sat-months'];
            }
            break;
        case SUB_GROUP:
            $type = 'Group - ' . $data['group-name'];
            $price = $data['group-price'];
            $item->no_of_points = "N/A";
            $item->no_months = 1;
            
            break;
        case SUB_MATH_TUTORING_BASIC:
            $type = mw_get_option('name-math-tutoring');
            $price = mw_get_option('price-math-tutoring') * $data['math-tutoring-months'];
            $item->no_months = $data['math-tutoring-months'];
            $item->sat_class = $data['sat-class'];
            break;
        case SUB_MATH_TUTORING_INTENSIVE:
            $type = mw_get_option('name-math-intensive-tutoring');
            $price = mw_get_option('price-math-intensive-tutoring') * $data['math-tutoring-months'];
            $item->no_months = $data['math-tutoring-months'];
            $item->sat_class = $data['sat-class'];
            break;
        case SUB_CLASS_ENGLISH:
            $type = $data['class-name'];
            $item->no_months = $data['sat-months'];
            $price = mw_get_option('price-course-at-english') * $data['sat-months'];
            $item->sat_class = $data['sat-class'];
            break;
        case SUB_CLASS_MATH:
            $type = $data['class-name'];
            $price = mw_get_option('price-course-at-math') * $data['sat-months'];           
            $iitem->no_months = $data['sat-months'];
            $item->sat_class = $data['sat-class'];
            break;
        case SUB_TUTORING_PLAN:
//            var_dump($data);die;
            $type = 'ikMath Tutoring Plan';
//            $type = $data['class-name'];
//            $price = mw_get_option('price-course-at-math') * $data['sat-months'];           
            $item->no_months = $data['date-plan'];
//            $item->sat_class = $data['sat-class'];
            $price = $data['duration'];
            break;
    }

    $dictionary = $wpdb->get_col($wpdb->prepare('SELECT name FROM ' . $wpdb->prefix . 'dict_dictionaries WHERE id = %d', $data['dictionary']));

    $item->id = $_SESSION['cart']['cid'] + 1;
    $item->type = $type;
    $item->typeid = $data['sub-type'];
    $item->tt_months = $data['teacher-tool-months'] == '' ? 0 : $data['teacher-tool-months'];
    $item->dictionary = $dictionary[0];
    $item->dictionary_id = $data['dictionary'];
    $item->no_students = $data['no-students'];
    $item->no_months_dict = $data['no-m-dict'];
    $item->price = $price;
    $item->group_id = $data['assoc-group'];
    $item->group_name = $data['group-name'];
    $item->group_pass = $data['group-pass'];
    if(count($_SESSION['cart']['items'])==0){
        $_SESSION['cart']['items'][] = $item;
    }else{
        $check = 0;
        foreach ($_SESSION['cart']['items'] as &$value) {
            if(!is_null($item->dictionary_id)){
                $type_new = $item->dictionary_id;
                $type = $value->dictionary_id;
                if((int)$type == (int)$type_new){
                    $value->no_months +=$item->no_months;
                    $value->price +=$item->price;
                    $check = 1;
                }
            }else{
                if(isset($value->sat_clas1s)){
                    $type = $value->sat_clas1s;
                }else{
                    $type = $value->sat_class;
                }
                if(isset($item->sat_clas1s)){
                    $type_new = $item->sat_clas1s;
                }else{
                    $type_new = $item->sat_class;
                }
                if((int)$type == (int)$type_new){
                    $value->no_months +=$item->no_months;
                    $value->price +=$item->price;
                    $check = 1;
                }
            }
        }
        
        if($check == 0){
            $_SESSION['cart']['items'][] = $item;
        }
    }
    $_SESSION['cart']['cid'] = $item->id;
    $_SESSION['cart']['total_amount'] += $item->price;

    // store shopping cart to db
    if (!empty($cart)) {
        $data = array(
            'items' => json_encode($_SESSION['cart']['items']),
            'total_amount' => $_SESSION['cart']['total_amount'],
            'cid' => $_SESSION['cart']['cid']
        );
        MWDB::update_user_shopping_cart($current_user_id, $data);
    } else {
        $data = array(
            'user_id' => $current_user_id,
            'items' => json_encode($_SESSION['cart']['items']),
            'total_amount' => $_SESSION['cart']['total_amount'],
            'cid' => $_SESSION['cart']['cid']
        );
        $a = MWDB::insert_user_shopping_cart($data);
//        var_dump($a);die;
    }
}

function add_math_request($data) {
    if (isset($_POST['datebasic']) && isset($_POST['datebasictime'])) {
        $item_basic = new stdClass;
        for ($i = 0; $i < 12; $i++) {
            $item_basic->datebasic[$i] = $_POST['datebasic'][$i] . ' ' . $_POST['datebasictime'][$i];
        }
        $data = array(
            'basic_time' => json_encode($item_basic),
            'requested_by' => get_current_user_id(),
            'accept_by' => get_user_by('email', $_POST['mail-tutor-basic'])->ID,
        );
        MWDB::insert_math_time_cart($data);
//        var_dump($data);
//        die;
    }

    if (isset($_POST['dateintensive']) && isset($_POST['dateintensivetime'])) {
        $item_intensive = new stdClass;
        for ($i = 0; $i < 3; $i++) {
            if ($i > 0) {
                if (in_array($_POST['dateintensive'][$i] . ' ' . $_POST['dateintensivetime'][$i], $item_intensive->dateintensive)) {
                    ik_enqueue_messages(__('Day ' . $i . ' or Time ' . $i . ' existed', 'iii-dictionary'), 'error');
                    return;
                } else {
                    $item_intensive->dateintensive[$i] = $_POST['dateintensive'][$i] . ' ' . $_POST['dateintensivetime'][$i];
                }
            } else {
                $item_intensive->dateintensive[$i] = $_POST['dateintensive'][$i] . ' ' . $_POST['dateintensivetime'][$i];
            }
        }
        $data = array(
            'intensive_time' => json_encode($item_intensive),
            'requested_by' => get_current_user_id(),
            'accept_by' => get_user_by('email', $_POST['email-tutor-intensive'])->ID,
        );
        MWDB::insert_math_time_cart($data);
//        var_dump($data);
//        die;
    }
}

/*
 * get cart items
 * 
 * @return array
 *
 */

function get_cart_items($user_id = 0) {
    $cart = get_user_cart($user_id);
    return isset($cart->items)?(array) $cart->items: array();
}

/*
 * get math time request basic
 * 
 * @return array
 *
 */

function get_math_time_basic($user_id = 0) {
    $cart = get_user_time($user_id);

    return (array) $cart->basic;
}

/*
 * get math time request intensive
 * 
 * @return array
 *
 */

function get_math_time_intensive($user_id = 0) {
    $cart = get_user_time_intensive($user_id);

    return (array) $cart->basic;
}

/*
 * get cart items
 * 
 * @return array
 *
 */

function get_cart_amount() {
    $cart = get_user_cart();

    return empty($cart->total_amount) ? 0 : $cart->total_amount;
}

/*
 * delete an item from cart
 *
 * @param array $data
 */

function ik_delete_cart_item(&$data) {
    $current_user_id = get_current_user_id();
    $cart = get_user_cart();

    if (isset($data['delete-cart-item'])) {
        foreach ($cart->items as $key => $item) {
            if ($item->id == $data['delete-cart-item']) {
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
}

/*
 * empty cart
 */

function empty_cart($user_id = 0) {
    $user_id = $user_id ? $user_id : get_current_user_id();
    MWDB::delete_user_shopping_cart($user_id);
}

/*
 * process transaction
 *
 */

function ik_process_transaction() {
    $process_url = 'https://www.myvirtualmerchant.com/VirtualMerchant/process.do';
    $payments_url = locale_home_url();

    $ssl_merchant_id = '554040';
    $ssl_user_id = 'iklearn123';
    $ssl_pin = 'Q7DQ5J4XXVLIK117LPB5RC4GEV4J416RXR88WR9JLLP9Q2H99U6S4NU43IA9LBOV';
    $ssl_show_form = 'false';
    $ssl_result_format = 'HTML';
    $ssl_receipt_link_method = 'REDG';
    $ssl_receipt_link_url = $payments_url;
    $ssl_error_url = $payments_url;
    $ssl_transaction_type = 'ccsale';
    $ssl_cvv2cvc2_indicator = '1';
    $ssl_test_mode = 'false';

    $ssl_salestax = '0';

    // data validating
    $data_valid = true;
    if ($_POST['payment-method'] == 1) {
        if ($_POST['credit-cards'] == '') {
            $data_valid = false;
            ik_enqueue_messages(__('Please select cart type', 'iii-dictionary'), 'error');
        }
        if (trim($_POST['ssl_card_number']) == '') {
            $data_valid = false;
            ik_enqueue_messages(__('Card number cannot empty', 'iii-dictionary'), 'error');
        }
        if ($_POST['exp_date_mm'] == '' || $_POST['exp_date_yy'] == '') {
            $data_valid = false;
            ik_enqueue_messages(__('Expiration date is not valid', 'iii-dictionary'), 'error');
        }
        if (trim($_POST['ssl_avs_address']) == '') {
            $data_valid = false;
            ik_enqueue_messages(__('Address cannot empty', 'iii-dictionary'), 'error');
        }
        if (trim($_POST['ssl_avs_zip']) == '') {
            $data_valid = false;
            ik_enqueue_messages(__('Zip code cannot empty', 'iii-dictionary'), 'error');
        }
        if (trim($_POST['ssl_cvv2cvc2']) == '') {
            $data_valid = false;
            ik_enqueue_messages(__('CVV/CVC cannot empty', 'iii-dictionary'), 'error');
        }

        $ssl_card_number = $_POST['ssl_card_number'];
        $ssl_cvv2cvc2 = $_POST['ssl_cvv2cvc2'];
        $ssl_exp_date = $_POST['exp_date_mm'] . $_POST['exp_date_yy'];
        $ssl_avs_address = $_POST['ssl_avs_address'];
        $ssl_avs_zip = $_POST['ssl_avs_zip'];
    } else {
        if ($_POST['user-credit-cards'] == '') {
            $data_valid = false;
            ik_enqueue_messages(__('Please select a Credit card', 'iii-dictionary'), 'error');
        }
        if (trim($_POST['re_ssl_cvv2cvc2']) == '') {
            $data_valid = false;
            ik_enqueue_messages(__('CVV/CVC cannot empty', 'iii-dictionary'), 'error');
        }
        // stop if data is not valid
        if (!$data_valid) {
            return false;
        }

        global $wpdb;

        $card = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'dict_user_credit_cards WHERE id = %d', $_POST['user-credit-cards']));

        if (is_null($card)) {
            ik_enqueue_messages(__('Card information not available', 'iii-dictionary'), 'error');
            return false;
        }

        $ssl_card_number = $card->card_number;
        $ssl_cvv2cvc2 = $_POST['re_ssl_cvv2cvc2'];
        $ssl_exp_date = date('my', strtotime($card->exp_date));
        $ssl_avs_address = $card->address;
        $ssl_avs_zip = $card->zip;
    }

    $ssl_amount = get_cart_amount();
    if (is_null($ssl_amount)) {
        $data_valid = false;
        ik_enqueue_messages(__('Your cart is empty', 'iii-dictionary'), 'error');
    }

    // stop if data is not valid
    if (!$data_valid) {
        return false;
    }

    $data = array(
        'ssl_card_number' => $ssl_card_number,
        'ssl_exp_date' => $ssl_exp_date,
        'ssl_amount' => $ssl_amount,
        'ssl_cvv2cvc2' => $ssl_cvv2cvc2,
        'ssl_avs_address' => $ssl_avs_address,
        'ssl_avs_zip' => $ssl_avs_zip
    );

    $queryString = '';
    foreach ($data as $key => $value) {
        $queryString .= '&' . $key . '=' . urlencode($value);
    }

    $postfields = 'ssl_merchant_id=' . $ssl_merchant_id .
            '&ssl_user_id=' . $ssl_user_id .
            '&ssl_pin=' . $ssl_pin .
            '&ssl_transaction_type=' . $ssl_transaction_type .
            '&ssl_cvv2cvc2_indicator=' . $ssl_cvv2cvc2_indicator .
            '&ssl_show_form=' . $ssl_show_form .
            '&ssl_result_format=' . $ssl_result_format .
            '&ssl_receipt_link_method=' . $ssl_receipt_link_method .
            '&ssl_receipt_link_url=' . $ssl_receipt_link_url .
            '&ssl_error_url=' . $ssl_error_url .
            '&ssl_test_mode=' . $ssl_test_mode .
            '&ssl_salestax=' . $ssl_salestax .
            $queryString;

    // start transaction
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $process_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $tran_result = curl_exec($ch);
//    echo '<pre>';
//    print_r(curl_error($ch));
//    die
    curl_close($ch);

    // store request data so that we can process it later
    $data['credit-cards'] = $_POST['credit-cards'];
    $data['payment-method'] = $_POST['payment-method'];
    $data['ssl_avs_address'] = $_POST['ssl_avs_address'];
    $data['ssl_avs_zip'] = $_POST['ssl_avs_zip'];
    $_SESSION['transaction']['request'] = $data;

    // complete transaction
    $dom = new DOMDocument;
    @$dom->loadHtml($tran_result);
    
    $nodes = $dom->getElementsByTagName('meta');
    $response_url = explode('?', $nodes->item(0)->getAttribute('content'));
    parse_str($response_url[1], $response);

    $_SESSION['transaction']['response'] = $response;

    return ik_complete_transaction();
}

/*
 * complete credit card transaction. Check response from transaction server
 *
 */

function ik_complete_transaction() {
    global $wpdb;

    $request = $_SESSION['transaction']['request'];
    $response = $_SESSION['transaction']['response'];
    $_SESSION['transaction'] = null;

    if (isset($response['ssl_result'])) {
        if ($response['ssl_result'] == 0) {
            // transaction success

            $user = wp_get_current_user();

            if ($request['payment-method'] == 1) {
                // store user card information to the database

                $wpdb->insert(
                        $wpdb->prefix . 'dict_user_credit_cards', array(
                    'user_id' => $user->ID,
                    'card_number' => $request['ssl_card_number'],
                    'display_card_number' => $response['ssl_card_number'],
                    'exp_date' => '20' . substr($response['ssl_exp_date'], 2, 2) . '-' . substr($response['ssl_exp_date'], 0, 2) . '-28',
                    'card_type_id' => $request['credit-cards'],
                    'address' => $request['ssl_avs_address'],
                    'zip' => $request['ssl_avs_zip']
                        )
                );
            }

            // save transaction history
            $data = array(
                'user_id' => $user->ID,
                'ssl_result' => $response['ssl_result'],
                'ssl_result_message' => $response['ssl_result_message'],
                'ssl_txn_id' => $response['ssl_txn_id'],
                'ssl_card_number' => $response['ssl_card_number'],
                'ssl_amount' => $response['ssl_amount'],
                'ssl_txn_time' => date('Y-m-d H:i:s', strtotime($response['ssl_txn_time'])),
                'ssl_approval_code' => $response['ssl_approval_code'],
                'ssl_cvv2_response' => $response['ssl_cvv2_response'],
                'ssl_avs_response' => $response['ssl_avs_response']
            );

            $result = $wpdb->insert($wpdb->prefix . 'dict_credit_card_transaction_history', $data);

            if ($result) {
                $transaction_id = $wpdb->insert_id;
            } else {
                $transaction_id = 0;
                ik_enqueue_messages(__('Cannot save transaction history', 'iii-dictionary'), 'error');
            }

            if ($transaction_id) {
                // check out shopping cart
                ik_checkout_cart_items(PAYMENT_METHOD_CREDIT_CARD, $transaction_id);

                return true;
            }

            return false;
        } else if ($response['ssl_result'] == 1) {
            // transaction failed
            // check CVV2/CVC2 Response Codes
            switch ($response['ssl_cvv2_response']) {
                case 'N': $errorMsg = 'CVV/CVC No match';
                    break;
                case 'S':
                case 'U': $errorMsg = 'CVV/CVC Invalid';
                    break;
            }

            ik_enqueue_messages($errorMsg, 'error');

            return false;
        }
    }

    ik_enqueue_messages($response['errorName'] . '. ' . $response['errorMsg'], 'error');

    return false;
}

/*
 * process payment with point
 */

function ik_process_point_payment() {
    global $wpdb;
    $total_amount = get_cart_amount();
    $point_amount = convert_price_to_points($total_amount);
//    var_dump($point_amount);die;
    if (ik_deduct_user_points($point_amount)) {
        // store history
        $wpdb->insert(
                $wpdb->prefix . 'dict_user_point_transactions', array(
            'user_id' => get_current_user_id(),
            'point_transaction_type_id' => 3,
            'amount' => $point_amount,
            'transaction_date' => date('Y-m-d H:i:s', time()),
            'note' => 'Purchase subscription'
                )
        );

        $transaction_id = $wpdb->insert_id;
        ik_checkout_cart_items(PAYMENT_METHOD_POINTS, $transaction_id);
        return true;
    } else {
        ik_enqueue_messages(__('You don\'t have enough point.', 'iii-dictionary'), 'error');

        return false;
    }
}

/*
 * check out all items in shopping cart
 */

function ik_checkout_cart_items($payment_method_id, $transaction_id, $user_id = 0) {
    global $wpdb;


    $user = $user_id ? get_user_by('ID', $user_id) : wp_get_current_user();    
    $cart = get_user_cart($user->ID);
    $cart_items = $cart->items;
    $total_amount = $cart->total_amount;

    foreach ($cart_items as $item) {
        // user extended current subscription
        if ($item->extending) {
            $updated_data['id'] = $item->sub_id;
            $updated_data['activation_code_id'] = $item->activation_code_id;
            $updated_data['typeid'] = $item->typeid;
            $updated_data['extend_students'] = $item->extend_students;
            $updated_data['no_students'] = $item->no_students;
            $updated_data['no_months_dict'] = $item->no_months_dict;
            $updated_data['sat_months'] = $item->sat_months;
            $updated_data['group_id'] = $item->group_id;
//            var_dump($updated_data);die;
            $subscription_id = MWDB::update_user_subscription_table($updated_data);
            $str_start_now = '';
            $re = $wpdb->get_row("SELECT sat_class_id FROM {$wpdb->prefix}dict_user_subscription WHERE id = {$item->sub_id}");
            $id_sat_class = $re->sat_class_id;
//              var_dump($updated_data);die;
            if (in_array($id_sat_class, array(1, 2, 3, 4, 5, 6, 7))) {
                $slug_class = $wpdb->get_row("SELECT slug FROM {$wpdb->prefix}dict_group_class_types WHERE id = {$id_sat_class}");
                $str_start_now = '	<div class="row" style="margin-top:20px">
										<div class="col-md-6">
											<a href="' . locale_home_url() . '?r=sat-preparation/' . $slug_class->slug . '" class="btn btn-default btn-block orange form-control">' . __('Start Now', 'iii-dictionary') . '</a>
										</div>
										<div class="col-md-6">
											<a href="' . locale_home_url() . '?r=online-learning" class="btn btn-default btn-block orange form-control">' . __('Start Later', 'iii-dictionary') . '</a>
										</div>
									</div>';
            } elseif (in_array($id_sat_class, array(9, 10, 11, 12, 13, 14))) {
                $slug_class = $wpdb->get_row("SELECT slug FROM {$wpdb->prefix}dict_group_class_types WHERE id = {$id_sat_class}");
                $str_start_now = '	<div class="row" style="margin-top:20px">
										<div class="col-md-6">
											<a href="' . locale_home_url() . '?r=sat-preparation/' . $slug_class->slug . '&client=math-sat1" class="btn btn-default btn-block orange form-control">' . __('Start Now', 'iii-dictionary') . '</a>
										</div>
										<div class="col-md-6">
											<a href="' . locale_home_url() . '?r=online-learning" class="btn btn-default btn-block orange form-control">' . __('Start Later', 'iii-dictionary') . '</a>
										</div>
									</div>';
            } elseif (in_array($id_sat_class, array(15, 16, 17, 18, 19, 20))) {
                $slug_class = $wpdb->get_row("SELECT slug FROM {$wpdb->prefix}dict_group_class_types WHERE id = {$id_sat_class}");
                $str_start_now = '	<div class="row" style="margin-top:20px">
										<div class="col-md-6">
											<a href="' . locale_home_url() . '?r=sat-preparation/' . $slug_class->slug . '&client=math-sat2" class="btn btn-default btn-block orange form-control">' . __('Start Now', 'iii-dictionary') . '</a>
										</div>
										<div class="col-md-6">
											<a href="' . locale_home_url() . '?r=online-learning" class="btn btn-default btn-block orange form-control">' . __('Start Later', 'iii-dictionary') . '</a>
										</div>
									</div>';
            } elseif (in_array($id_sat_class, array(38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50))) {
                $slug_class = $wpdb->get_row("SELECT slug FROM {$wpdb->prefix}dict_group_class_types WHERE id = {$id_sat_class}");
                $str_start_now = '	<div class="row" style="margin-top:20px">
										<div class="col-md-6">
											<a href="' . locale_home_url() . '?r=sat-preparation/' . $slug_class->slug . '&client=math-emathk" class="btn btn-default btn-block orange form-control">' . __('Start Now', 'iii-dictionary') . '</a>
										</div>
										<div class="col-md-6">
											<a href="' . locale_home_url() . '?r=online-learning" class="btn btn-default btn-block orange form-control">' . __('Start Later', 'iii-dictionary') . '</a>
										</div>
									</div>';
            } elseif ($id_sat_class == 51) {
                $slug_class = $wpdb->get_row("SELECT slug FROM {$wpdb->prefix}dict_group_class_types WHERE id = {$id_sat_class}");
                $str_start_now = '	<div class="row" style="margin-top:20px">
										<div class="col-md-6">
											<a href="' . locale_home_url() . '?r=sat-preparation/' . $slug_class->slug . '" class="btn btn-default btn-block orange form-control">' . __('Start Now', 'iii-dictionary') . '</a>
										</div>
										<div class="col-md-6">
											<a href="' . locale_home_url() . '?r=online-learning" class="btn btn-default btn-block orange form-control">' . __('Start Later', 'iii-dictionary') . '</a>
										</div>
									</div>';
            } elseif ($id_sat_class == 52) {
                $slug_class = $wpdb->get_row("SELECT slug FROM {$wpdb->prefix}dict_group_class_types WHERE id = {$id_sat_class}");
                $str_start_now = '	<div class="row" style="margin-top:20px">
										<div class="col-md-6">
											<a href="' . locale_home_url() . '?r=sat-preparation/' . $slug_class->slug . '&client=math-sat1" class="btn btn-default btn-block orange form-control">' . __('Start Now', 'iii-dictionary') . '</a>
										</div>
										<div class="col-md-6">
											<a href="' . locale_home_url() . '?r=online-learning" class="btn btn-default btn-block orange form-control">' . __('Start Later', 'iii-dictionary') . '</a>
										</div>
									</div>';
            } elseif ($id_sat_class == 53) {
                $slug_class = $wpdb->get_row("SELECT slug FROM {$wpdb->prefix}dict_group_class_types WHERE id = {$id_sat_class}");
                $str_start_now = '	<div class="row" style="margin-top:20px">
										<div class="col-md-6">
											<a href="' . locale_home_url() . '?r=sat-preparation/' . $slug_class->slug . '&client=math-sat2" class="btn btn-default btn-block orange form-control">' . __('Start Now', 'iii-dictionary') . '</a>
										</div>
										<div class="col-md-6">
											<a href="' . locale_home_url() . '?r=online-learning" class="btn btn-default btn-block orange form-control">' . __('Start Later', 'iii-dictionary') . '</a>
										</div>
									</div>';
            }
        }
        // user added new subscription
        else {
            // process the purchase
            switch ($item->typeid) {

                // User purchased Dictionary subscription
                case SUB_DICTIONARY:
                    // We generate a credit code for the user.
                    $item->subscription_code = generate_credit_code($item->typeid, $item->tt_months, $item->no_students, $item->dictionary_id, $item->no_months_dict, true);

                    // we don't need to auto activate the code anymore
                    $subscription_id = 0;
                    $code_obj = $wpdb->get_row(
                            'SELECT id FROM ' . $wpdb->prefix . 'dict_credit_codes WHERE encoded_code = \'' . $item->subscription_code . '\''
                    );
                    $starting_date = date('Y-m-d', time());
                    $expired_date = date('Y-m-d', strtotime('+' . $item->tt_months . ' months', strtotime($starting_date)));

                    // store user's subscription
//                    var_dump($sub_data);die;
                    $sub_data['activation_code_id'] = 0;
                    $sub_data['user_id'] = $user->ID;
                    $sub_data['activated_by'] = get_current_user_id();
                    $sub_data['starting_date'] = $starting_date;
                    $sub_data['expired_date'] = $expired_date;
                    $sub_data['code_typeid'] = $item->typeid;
                    $sub_data['group_id'] = $group_id;
                    $sub_data['sat_class_id'] = 0;
                    $sub_data['number_of_students'] = $item->no_students;
                    $sub_data['number_of_months'] = $item->tt_months;
                    $sub_data['dictionary_id'] = $item->dictionary_id;
                    $credit_code_id = $code_obj->id;
                    $subscription_id = MWDB::add_user_subscription($sub_data);
                    break;

                // User purchased Homework Tool subscription
                case SUB_TEACHER_TOOL:
                case SUB_TEACHER_TOOL_MATH:
                    // check if user want to create new group for the subscription
                    if (!empty($item->group_id)) {
                        $group_id = $item->group_id;
                    } else {
                        $group_id = MWDB::store_group(array('gname' => $item->group_name, 'gpass' => $item->group_pass));
                    }

                    // calculate expiration date
                    $starting_date = date('Y-m-d', time());
                    $expired_date = date('Y-m-d', strtotime('+' . $item->tt_months . ' months', strtotime($starting_date)));

                    // store user's subscription
//                    var_dump($sub_data);die;
                    $sub_data['activation_code_id'] = 0;
                    $sub_data['user_id'] = $user->ID;
                    $sub_data['starting_date'] = $starting_date;
                    $sub_data['expired_date'] = $expired_date;
                    $sub_data['code_typeid'] = $item->typeid;
                    $sub_data['group_id'] = $group_id;
                    $sub_data['sat_class_id'] = 0;
                    $sub_data['number_of_students'] = $item->no_students;
                    $sub_data['number_of_months'] = $item->tt_months;
                    $sub_data['dictionary_id'] = $item->dictionary_id;
                    $subscription_id = MWDB::add_user_subscription($sub_data);
                    break;

                // User purchased SAT Preparation subscription
                case SUB_SAT_PREPARATION:
                case SUB_MATH_SAT_I_PREP:
                case SUB_MATH_SAT_II_PREP:
                case SUB_MATH_CLASS_IK:
                case SUB_PACKAGE:
                    $starting_date = date('Y-m-d', time());
                    $expired_date = date('Y-m-d', strtotime('+' . $item->sat_months . ' months', strtotime($starting_date)));

                    // store user's subscription
//                    var_dump($item);die;
                    $sub_data['activation_code_id'] = 0;
                    $sub_data['user_id'] = $user->ID;
                    $sub_data['starting_date'] = $starting_date;
                    $sub_data['expired_date'] = $expired_date;
                    $sub_data['code_typeid'] = $item->typeid;
                    $sub_data['group_id'] = 0;
                    if(!$item->sat_clas1s){
                        $sub_data['sat_class_id'] = $item->sat_class;
                    }else{
                        $sub_data['sat_class_id'] = $item->sat_clas1s;
                    }
                    $sub_data['number_of_students'] = 0;
                    $sub_data['number_of_months'] = $item->sat_months;
                    $sub_data['dictionary_id'] = 0;
                    $subscription_id = MWDB::add_user_subscription($sub_data);
                    $str_start_now = '';
                    if (in_array($item->sat_class, array(1, 2, 3, 4, 5, 6, 7))) {
                        $slug_class = $wpdb->get_row("SELECT slug FROM {$wpdb->prefix}dict_group_class_types WHERE id = {$item->sat_class}");
                        $str_start_now = '	<div class="row" style="margin-top:20px">
												<div class="col-md-6">
													<a href="' . locale_home_url() . '?r=sat-preparation/' . $slug_class->slug . '" class="btn btn-default btn-block orange form-control">' . __('Start Now', 'iii-dictionary') . '</a>
												</div>
												<div class="col-md-6">
													<a href="' . locale_home_url() . '?r=online-learning" class="btn btn-default btn-block orange form-control">' . __('Start Later', 'iii-dictionary') . '</a>
												</div>
											</div>';
                    } elseif (in_array($item->sat_class, array(9, 10, 11, 12, 13, 14))) {
                        $slug_class = $wpdb->get_row("SELECT slug FROM {$wpdb->prefix}dict_group_class_types WHERE id = {$item->sat_class}");//?r=sat-preparation/' . $slug_class->slug . '&client=math-sat1
                        $str_start_now = '	<div class="row" style="margin-top:20px">
												<div class="col-md-6">
													<a href="' . locale_home_url() . '/?r=sat-preparation/' . $slug_class->slug . '&client=math-sat1" class="btn btn-default btn-block orange form-control">' . __('Start Now', 'iii-dictionary') . '</a>
												</div>
												<div class="col-md-6">
													<a href="' . locale_home_url() . '/?r=sat-preparation/emathk&client=math-emathk" class="btn btn-default btn-block orange form-control">' . __('Start Later', 'iii-dictionary') . '</a>
												</div>
											</div>';
                    } elseif (in_array($item->sat_class, array(15, 16, 17, 18, 19, 20))) {
                        $slug_class = $wpdb->get_row("SELECT slug FROM {$wpdb->prefix}dict_group_class_types WHERE id = {$item->sat_class}");
                        $str_start_now = '	<div class="row" style="margin-top:20px">
												<div class="col-md-6">
													<a href="' . locale_home_url() . '?r=sat-preparation/' . $slug_class->slug . '&client=math-sat2" class="btn btn-default btn-block orange form-control">' . __('Start Now', 'iii-dictionary') . '</a>
												</div>
												<div class="col-md-6">
													<a href="' . locale_home_url() . '?r=online-learning" class="btn btn-default btn-block orange form-control">' . __('Start Later', 'iii-dictionary') . '</a>
												</div>
											</div>';
                    } elseif (in_array($item->sat_class, array(38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50))) {
                        $slug_class = $wpdb->get_row("SELECT slug FROM {$wpdb->prefix}dict_group_class_types WHERE id = {$item->sat_class}");
                        $str_start_now = '	<div class="row" style="margin-top:20px">
										<div class="col-md-6">
											<a href="' . locale_home_url() . '?r=sat-preparation/' . $slug_class->slug . '&client=math-emathk" class="btn btn-default btn-block orange form-control">' . __('Start Now', 'iii-dictionary') . '</a>
										</div>
										<div class="col-md-6">
											<a href="' . locale_home_url() . '?r=online-learning" class="btn btn-default btn-block orange form-control">' . __('Start Later', 'iii-dictionary') . '</a>
										</div>
									</div>';
                    } elseif ($item->sat_class == 51) {
                        $slug_class = $wpdb->get_row("SELECT slug FROM {$wpdb->prefix}dict_group_class_types WHERE id = {$item->sat_class}");
                        $str_start_now = '	<div class="row" style="margin-top:20px">
										<div class="col-md-6">
											<a href="' . locale_home_url() . '?r=sat-preparation/' . $slug_class->slug . '" class="btn btn-default btn-block orange form-control">' . __('Start Now', 'iii-dictionary') . '</a>
										</div>
										<div class="col-md-6">
											<a href="' . locale_home_url() . '?r=online-learning" class="btn btn-default btn-block orange form-control">' . __('Start Later', 'iii-dictionary') . '</a>
										</div>
									</div>';
                    } elseif ($item->sat_class == 52) {
                        $slug_class = $wpdb->get_row("SELECT slug FROM {$wpdb->prefix}dict_group_class_types WHERE id = {$item->sat_class}");
                        $str_start_now = '	<div class="row" style="margin-top:20px">
										<div class="col-md-6">
											<a href="' . locale_home_url() . '?r=sat-preparation/' . $slug_class->slug . '&client=math-sat1" class="btn btn-default btn-block orange form-control">' . __('Start Now', 'iii-dictionary') . '</a>
										</div>
										<div class="col-md-6">
											<a href="' . locale_home_url() . '?r=online-learning" class="btn btn-default btn-block orange form-control">' . __('Start Later', 'iii-dictionary') . '</a>
										</div>
									</div>';
                    } elseif ($item->sat_class == 53) {
                        $slug_class = $wpdb->get_row("SELECT slug FROM {$wpdb->prefix}dict_group_class_types WHERE id = {$item->sat_class}");
                        $str_start_now = '	<div class="row" style="margin-top:20px">
										<div class="col-md-6">
											<a href="' . locale_home_url() . '?r=sat-preparation/' . $slug_class->slug . '&client=math-sat2" class="btn btn-default btn-block orange form-control">' . __('Start Now', 'iii-dictionary') . '</a>
										</div>
										<div class="col-md-6">
											<a href="' . locale_home_url() . '?r=online-learning" class="btn btn-default btn-block orange form-control">' . __('Start Later', 'iii-dictionary') . '</a>
										</div>
									</div>';
                    }
                    break;

                // User purchased Points
                case SUB_MATH_TUTORING_BASIC:
                case SUB_MATH_TUTORING_INTENSIVE:
                    $starting_date = date('Y-m-d', time());
                    $expired_date = date('Y-m-d', strtotime('+' . $item->no_months . ' months', strtotime($starting_date)));

                    // store user's subscription
                    $sub_data['activation_code_id'] = 0;
                    $sub_data['user_id'] = $user->ID;
                    $sub_data['starting_date'] = $starting_date;
                    $sub_data['expired_date'] = $expired_date;
                    $sub_data['code_typeid'] = $item->typeid;
                    $sub_data['group_id'] = 0;
                    $sub_data['sat_class_id'] = $item->sat_class;
                    $sub_data['number_of_students'] = 0;
                    $sub_data['number_of_months'] = $item->no_months;
                    $sub_data['dictionary_id'] = 0;
                    $subscription_id = MWDB::add_user_subscription($sub_data);
                    $str_start_now = '';
                    break;
                case SUB_POINTS_PURCHASE:
                    $subscription_id = 0;

                    // increase user's points
                    ik_add_user_points($item->no_of_points, $user->ID);
                    break;

                // User purchase Self-study Subscription
                case SUB_SELF_STUDY:
                case SUB_SELF_STUDY_MATH:
                    // create the default group
                    $group_id = MWDB::store_group(array('gname' => $item->group_name, 'gpass' => $item->group_pass, 'is_default' => 1));
                    // joint user to the group
                    MWDB::join_group($group_id);

                    // calculate expired date
                    $starting_date = date('Y-m-d', time());
                    $expired_date = date('Y-m-d', strtotime('+' . $item->no_months . ' months', strtotime($starting_date)));

                    // store user's subscription
                    $sub_data['activation_code_id'] = 0;
                    $sub_data['user_id'] = $user->ID;
                    $sub_data['starting_date'] = $starting_date;
                    $sub_data['expired_date'] = $expired_date;
                    $sub_data['code_typeid'] = $item->typeid;
                    $sub_data['group_id'] = $group_id;
                    $sub_data['sat_class_id'] = 0;
                    $sub_data['number_of_students'] = $item->no_students;
                    $sub_data['number_of_months'] = $item->no_months;
                    $sub_data['dictionary_id'] = $item->dictionary_id;
                    $subscription_id = MWDB::add_user_subscription($sub_data);
                    break;
                case SUB_GROUP:
                    $starting_date = date('Y-m-d', time());
                    $expired_date = date('Y-m-d', strtotime('+' . $item->no_months . ' months', strtotime($starting_date)));
                    // store user's subscription
                    $sub_data['activation_code_id'] = 0;
                    $sub_data['user_id'] = $user->ID;
                    $sub_data['starting_date'] = $starting_date;
                    $sub_data['expired_date'] = $expired_date;
                    $sub_data['code_typeid'] = $item->typeid;
                    $sub_data['group_id'] = $item->group_id;
                    $sub_data['sat_class_id'] = 0;
                    $sub_data['number_of_students'] = $item->no_students;
                    $sub_data['number_of_months'] = $item->no_months;
                    $sub_data['dictionary_id'] = $item->dictionary_id;
                    $subscription_id = MWDB::add_user_subscription($sub_data);
                    //join user to group
                    MWDB::join_group($item->group_id);
                    break;
            }
        }
        // store purchase history
        $record = array();
        $record['user_id'] = $user->ID;
        $record['payment_method_id'] = $payment_method_id;
        $record['user_subscription_id'] = $subscription_id;
        $record['typeid'] = $item->typeid;
        $record['dictionary_id'] = $item->dictionary_id;
        $record['amount'] = $item->price;
        if($item->activation_code_id !=null){
            $record['credit_code_id'] = $item->activation_code_id;
        }
        $record['purchased_on'] = date('Y-m-d H:i:s', time());

        switch ($payment_method_id) {
            case PAYMENT_METHOD_CREDIT_CARD:
                $record['credit_card_txn_id'] = $transaction_id;
                break;
            case PAYMENT_METHOD_PAYPAL:
                $record['paypal_txn_id'] = $transaction_id;
                break;
            case PAYMENT_METHOD_POINTS:
                $record['point_txn_id'] = $transaction_id;
                break;
        }

        if (!empty($credit_code_id)) {
            $record['credit_code_id'] = $credit_code_id;
        }
        
        $wpdb->insert($wpdb->prefix . 'dict_purchase_subscription_history', $record);

        $purchased_list[] = $item;
    }

    // empty the cart
    empty_cart($user->ID);

    // update current subscription status
    update_user_subscription();

    // send receipt email
    $headers = array('Content-Type: text/html; charset=UTF-8');
    $title = __('Thank you for purchasing on iklearn.com', 'iii-dictionary');
    $message = __('This is an automatically generated message to confirm receipt of your purchase via the Internet.', 'iii-dictionary') . '<br>' .
            __('You do not need to reply to this e-mail, but you may wish to save it for your records.', 'iii-dictionary') . '<br><br>' .
            '<table style="width: 100%; border-collapse: collapse; text-align: center; margin: 20px 0">' .
            '<tr>' .
            '<th style="border: 1px solid #000; padding: 10px">' . __('Type', 'iii-dictionary') . '</th>' .
            '<th style="border: 1px solid #000; padding: 10px">' . __('Month', 'iii-dictionary') . '</th>' .
            '<th style="border: 1px solid #000; padding: 10px">' . __('No. of Students', 'iii-dictionary') . '</th>' .
            '<th style="border: 1px solid #000; padding: 10px">' . __('No. of Users', 'iii-dictionary') . '</th>' .
            '<th style="border: 1px solid #000; padding: 10px">' . __('Dictionary', 'iii-dictionary') . '</th>' .
            '<th style="border: 1px solid #000; padding: 10px">' . __('No. of Points', 'iii-dictionary') . '</th>' .
            '<th style="border: 1px solid #000; padding: 10px">' . __('Activation Code', 'iii-dictionary') . '</th>' .
            '<th style="border: 1px solid #000; padding: 10px">' . __('Price', 'iii-dictionary') . '</th>' .
            '</tr>';
    foreach ($purchased_list as $item) {
        switch ($item->typeid) {
            case SUB_TEACHER_TOOL:
            case SUB_TEACHER_TOOL_MATH:
                $m = $item->tt_months;
                $no_s = $item->no_students;
                $no_l = $item->no_of_points = $ccode = 'N/A';
                break;
            case SUB_DICTIONARY:
                $m = $item->no_months_dict;
                $no_s = $item->no_of_points = 'N/A';
                $no_l = $item->no_students;
                $ccode = $item->subscription_code;
                break;
            case SUB_SAT_PREPARATION:
            case SUB_MATH_SAT_I_PREP:
            case SUB_MATH_SAT_II_PREP:
            case SUB_MATH_CLASS_IK:
            case SUB_PACKAGE:
                $m = $item->sat_months;
                $no_s = $no_l = $item->dictionary = $item->no_of_points = $ccode = 'N/A';
                break;
            case SUB_POINTS_PURCHASE:
                $m = $no_s = $no_l = $item->dictionary = $ccode = 'N/A';
                break;
            case SUB_SELF_STUDY:
            case SUB_SELF_STUDY_MATH:
                $m = $item->no_months;
                $no_l = $item->no_students;
                $no_s = $ccode = $item->no_of_points = 'N/A';
                break;
            case SUB_GROUP:
                $m = $no_s = $no_l = $item->dictionary = $ccode = $item->no_of_points = 'N/A';
                break;
        }

        $item->type = $item->extending ? $item->type . ' ' . __('(Additional)', 'iii-dictionary') : $item->type;

        $message .= '<tr>' .
                '<td style="border: 1px solid #000; padding: 10px">' . $item->type . '</td>' .
                '<td style="border: 1px solid #000; padding: 10px">' . $m . '</td>' .
                '<td style="border: 1px solid #000; padding: 10px">' . $no_s . '</td>' .
                '<td style="border: 1px solid #000; padding: 10px">' . $no_l . '</td>' .
                '<td style="border: 1px solid #000; padding: 10px">' . $item->dictionary . '</td>' .
                '<td style="border: 1px solid #000; padding: 10px">' . $item->no_of_points . '</td>' .
                '<td style="border: 1px solid #000; padding: 10px">' . $ccode . '</td>' .
                '<td style="border: 1px solid #000; padding: 10px">$' . $item->price . '</td>' .
                '</tr>';
    }
    $message .= '<tr>' .
            '<th colspan="7" style="border: 1px solid #000; padding: 10px">' . __('Total amount:', 'iii-dictionary') . '</th>' .
            '<td style="border: 1px solid #000; padding: 10px">$' . $total_amount . '</td>' .
            '</tr>' .
            '</table>' . '<br>' .
            __('If you have any questions, please send your inquiry to support@iklearn.com', 'iii-dictionary') . '<br>' .
            __('Thank you.', 'iii-dictionary') . '<br><br>' .
            __('Support', 'iii-dictionary');

    ik_clear_message_queue();

//    $msg = __('Transaction was successful', 'iii-dictionary') . ' <br> - ' . __('Your receipt has been sent to your email address.', 'iii-dictionary');
    
// Kiểm tra xem có 2 loại khác nhau trong danh sách mua sub không
    $check = 0; // Biến để kiểm tra có 2 loại khác nhau trong id sub user
    if(count($purchased_list)>=2){
        $arr_id = []; // compare 2 mảng gồm id của các sub mua và id của tất cả các sub có nếu có 2 loại khác nhau
        for($i=0; $i<count($purchased_list);$i++){
            array_push($arr_id, $purchased_list[$i]->typeid);
        }
        // VD: $arr_id = ["1","1"];
        $arr_exit = [];
        array_push($arr_exit,$arr_id[0]);
        for($j=1;$j<count($arr_id);$j++){
            if(!in_array($arr_id[$j], $arr_exit)){
                array_push($arr_exit,$arr_id[$j]);
            }
        }
        if(count($arr_exit)>=2){$check=1;};
    }
    
    if(count($purchased_list)>=2 && $check ==1){
        $arr_str = [];
        $arr_string_exit = [];
        $string = "";
        for($i=0; $i<count($purchased_list);$i++){
            array_push($arr_str, $purchased_list[$i]->type);
//            $string .= $purchased_list[$i]->type.", ";
        }
        array_push($arr_string_exit,$arr_str[0]);
        for($j=1;$j<count($arr_str);$j++){
            if(!in_array($arr_str[$j], $arr_string_exit)){
                array_push($arr_string_exit,$arr_str[$j]);
            }
        }
        for($i=0; $i<count($arr_string_exit);$i++){
            $string .= $arr_string_exit[$i] .", ";
        }
        $string_new = rtrim($string,", ");
//        var_dump($purchased_list);die;
        $msg_detail[1] = '<div>'."You have subscribed to ".'<b>'.$string_new.'</b>';
        $msg_detail[1] .= '<br>'.'Go to <a href="' . locale_home_url() . '/?r=online-learning" class="sub-link-a">' ."My Subscription". '</a>'.' to start.';
        $msg_detail[1] .= '<div class="row" style="margin-top:20px">
										<div class="col-md-12">
											<a href="' . locale_home_url() . '/?r=online-learning" class="btn-custom">' . __('OK', 'iii-dictionary') . '</a>
										</div>
									</div>';
    }else{
        foreach ($purchased_list as $item) {
            switch ($item->typeid) {
                case SUB_TEACHER_TOOL:
                case SUB_TEACHER_TOOL_MATH:
                    $msg_detail[1] = '<div><strong>' . __('Teacher\'s Tool', 'iii-dictionary') . '</strong>';
                    if (!$item->extending) {
                        $msg_detail[1] .= '<br>- ' . __('You can send a homework assignment to the group now. Once sent, each student can see the assignment in his/her Homework Status area.', 'iii-dictionary');
                    } else {
                        $msg_detail[1] .= '<br>- ' . __('Your subscription has been extended.', 'iii-dictionary');
                    }
                    $msg_detail[1] .= '</div>';
                    break;
                case SUB_DICTIONARY:
                    $msg_detail[2] = '<div><strong>' . __('Successfully Purcharsed Dictionary!', 'iii-dictionary') . '</strong><hr>';
                    if (!$item->extending) {
                        $msg_detail[2] .= '<div class="txt-code-1">' . __('Activation code: ', 'iii-dictionary') . '<span class="code1">' . $item->subscription_code.'</span></div>';
                        $msg_detail[2] .= '<br>'. __('Would you like to activate it now?', 'iii-dictionary');
                        $msg_detail[2] .= '<div style="font-style: italic;">' . __('(if purchase to be given as a gift, send the code to the recipient)', 'iii-dictionary').'<br>';
                        $other = array('activate_code' => $item->subscription_code, 'order' => 2);
                    } else {
                        $msg_detail[2] .= '<br>- ' . __('Your subscription has been extended.', 'iii-dictionary');
                    }
                    $msg_detail[2] .= '</div>';
                    break;
                case SUB_SAT_PREPARATION:
                case SUB_MATH_SAT_I_PREP:
                case SUB_MATH_SAT_II_PREP:
                    if($item->type=='SAT I - Grammar' ||$item->type=='SAT I - Writing Practice' || $item->type=='SAT I - Math SAT 1 Prep' || $item->type=='SAT II - Math SAT 2 Prep') {
                        $msg_detail[3] = '<div><strong>'."You have successfully join the Group: ".$item->type.'</strong>';
                        $msg_detail[3] .= '<br>' . __('You can start/restart at anytime from the "Student’s Box".', 'iii-dictionary');
                        $msg_detail[3] .= '<div class="row" style="margin-top:20px">
										<div class="col-md-6">
											<a href="' . locale_home_url() . '/?r=online-learning&backik=1&back-ikmath='.$sat_class .'" class="btn btn-default btn-block orange form-control">' . __('Start Now', 'iii-dictionary') . '</a>
										</div>
										<div class="col-md-6">
											<a href="' . locale_home_url() . '/?r=sat-preparation/emathk&client=math-emathk" class="btn btn-default btn-block orange form-control">' . __('Start Later', 'iii-dictionary') . '</a>
										</div>
									</div>';
                    }else {
                        $msg_detail[3] = '<div><strong>'."You have successfully join the Group: SAT I - SAT Test 5".'</strong>';
                        $msg_detail[3] .= '<br>'.'Go to <a href="' . locale_home_url() . '/?r=online-learning" class="sub-link-a">' ."My Subscription". '</a>'.' to start.';
                        $msg_detail[3] .= '<div class="row" style="margin-top:20px">
										<div class="col-md-6">
											<a href="' . locale_home_url() . '/?r=online-learning&back-en-sat1" class="btn btn-default btn-block orange form-control">' . __('Start Now', 'iii-dictionary') . '</a>
										</div>
										<div class="col-md-6">
											<a href="#" class="btn btn-default btn-block orange form-control">' . __('Start Later', 'iii-dictionary') . '</a>
										</div>
									</div>';
                    }
                    break;
                case SUB_PACKAGE:
                    $msg_detail[3] = '<div><strong>' . $item->type . '</strong>';
                    if (!$item->extending) {
                        $msg_detail[3] .= '<br>- ' . __('You may join SAT preparation class at SAT Preparation page.', 'iii-dictionary');
                        $msg_detail[3] .= $str_start_now;
                    } else {
                        $msg_detail[3] .= '<br>- ' . __('Your subscription has been extended.', 'iii-dictionary');
                        $msg_detail[3] .= $str_start_now;
                    }
                    $msg_detail[3] .= '</div>';
                    break;
                case SUB_MATH_CLASS_IK:
                    $sat_class = $item->sat_class;
                    $msg_detail[3] = '<div><strong>' . $item->type . '</strong>';
                    if (!$item->extending) {
                        $msg_detail[3] .= '<br>- ' . __('You may join IK Math class at IK Math class page.', 'iii-dictionary');
                        $msg_detail[3] .= '	<div class="row" style="margin-top:20px">
										<div class="col-md-6">
											<a href="' . locale_home_url() . '/?r=online-learning&backik=1&back-ikmath='.$sat_class .'" class="btn btn-default btn-block orange form-control">' . __('Start Now', 'iii-dictionary') . '</a>
										</div>
										<div class="col-md-6">
											<a href="' . locale_home_url() . '/?r=sat-preparation/emathk&client=math-emathk" class="btn btn-default btn-block orange form-control">' . __('Start Later', 'iii-dictionary') . '</a>
										</div>
									</div>';
                    } else {
                        $msg_detail[3] .= '<br>- ' . __('Your subscription has been extended.', 'iii-dictionary');
                        $msg_detail[3] .= '	<div class="row" style="margin-top:20px">
										<div class="col-md-6">
											<a href="' . locale_home_url() . '/?r=online-learning&backik=1&back-ikmath='.$sat_class .'" class="btn btn-default btn-block orange form-control">' . __('Start Now', 'iii-dictionary') . '</a>
										</div>
										<div class="col-md-6">
											<a href="' . locale_home_url() . '/?r=sat-preparation/emathk&client=math-emathk" class="btn btn-default btn-block orange form-control">' . __('Start Later', 'iii-dictionary') . '</a>
										</div>
									</div>';
                    }
                    $msg_detail[3] .= '</div>';
                    break;
                case SUB_MATH_TUTORING_BASIC:
                case SUB_MATH_TUTORING_INTENSIVE:
                    $msg_detail[3] = '<div><strong>' . $item->type . '</strong>';
                    $msg_detail[3] .= '<br>- ' . __('Your subscription has been extended.', 'iii-dictionary');
                    $msg_detail[3] .= $str_start_now;
                    $msg_detail[3] .= '</div>';
                    break;
    // lúc đầu là SUB_POINTS_PURCHASE
                case SUB_POINTS_PURCHASE1:
                    $msg_detail[4] = '<div><strong>' . __('Points', 'iii-dictionary') . '</strong>';
                    $msg_detail[4] .= '<br>- ' . __('You can now use your points to purchase Worksheet and request grading.', 'iii-dictionary') . '</div>';
                    break;
                case SUB_SELF_STUDY:
                    $msg_detail[5] = '<div><strong>' . __('Subscribled to English Self-Study', 'iii-dictionary') . '</strong>';
                    $msg_detail[5] .= '<br> ' . __('You can now use new worksheets for practice.', 'iii-dictionary') . '</div><br>';
                    $msg_detail[5] .= '<button type="button" id="ok-next-spelling-practice" class="btn-custom">' . __('OK', 'iii-dictionary') . '</button>';
                    break;
                case SUB_SELF_STUDY_MATH:
                    $msg_detail[5] = '<div><strong>' . __('Subscribled to Math Self-Study', 'iii-dictionary') . '</strong>';
                    $msg_detail[5] .= '<br> ' . __('Now you can use all worksheets for practice.', 'iii-dictionary') . '</div>';
                    $msg_detail[5] .= '<div class="modal-footer" style="padding-bottom: 0px !important;padding-top: 5%">
                                            <div class="row">
                                                <div class="col-sm-6" style="width: 100% !important">
                                                    <div class="form-group">
                                                        <button type="button" id="ok-close-sub-math" class="btn-custom">' . __('OK', 'iii-dictionary') . '</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>';
                    break;
                case SUB_GROUP:
                    $msg_detail[6] .= '<div> ' . __('You have successfully joined the class!', 'iii-dictionary') . '</div>';
                    //$other = array('group_id' => $item->group_id, 'order' => 1);
                    break;
                case SUB_TUTORING_PLAN:
                    $msg_detail[6] = '<div>' . __('ikMath Tutoring Plan has been scheduled successfully', 'iii-dictionary');
                    $msg_detail[6] .= '<div><input type="button" id="link-modal-tutoring-new" class="btn-can-modal1" value="OK"></div>';
                    break;
                case SUB_CLASS_ENGLISH:
                    $msg_detail[6] = '<div>' . __('You have successfully joined the class!', 'iii-dictionary');
                    $msg_detail[6] .= '<div><input type="button" id="cancel-modal-tutoring" class="btn-can-modal1" value="OK"></div>';
                    break;
            }
        }
    }
    /*
      if ( !wp_mail( $user->user_email, wp_specialchars_decode( $title ), $message, $headers ) ) {
      $msg .= '<br><hr><br>Your transaction is completed but we cannot send receipt email to ' . $user->user_email . '<br>The email content is: <hr>' . $message;
      ik_enqueue_messages($msg, 'notice');
      }
      else {
      ik_enqueue_messages($msg . implode('', $msg_detail), 'success', $title = 'Messages', $other);
      } */
    $msg = '';
    ik_enqueue_messages($msg . implode('', $msg_detail), 'success', $title = 'Messages', $other);
}

/*
 * server will check system of user and return link download app.
 */

function ik_link_apps() {
    $link_url['win'] = 'http://mwd.s3.amazonaws.com/desktop/IKLearn.msi';
    $link_url['mac'] = 'http://mwd.s3.amazonaws.com/desktop/IK%20Learn.app%20MAC.zip';
    return $link_url;
}

/*
 * server will check system of user and return link download app.
 */

function ik_link_mw_apps() {
    $link_url['win'] = 'http://mwd.s3.amazonaws.com/desktop/MW_collegiate_Win.exe';
    $link_url['mac'] = 'http://mwd.s3.amazonaws.com/desktop/MW_collegiate_Mac.zip';
    return $link_url;
}

/* SAT - Subscription section expand for user
 */

function free_SAT($status = true) {
    $_SESSION['subscription']['SAT'] = array(
        CLASS_GRAMMAR => $status,
        CLASS_MATH_SAT1PREP => $status,
            //CLASS_MATH_SAT2PREP => $status
    );
}
