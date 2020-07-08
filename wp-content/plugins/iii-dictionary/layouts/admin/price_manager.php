<?php
if (isset($_POST['submit-price'])) {
//            var_dump($_POST['sat-math-i-package-price-discount']);
//	            var_dump($_POST['sat-math-ii-package-price-discount']);
//        die;
    $tt_price = $_POST['teacher-tool-price'] != '' ? $_POST['teacher-tool-price'] : 0;
    $self_study_price = $_POST['self-study-price'] != '' ? $_POST['self-study-price'] : 0;
    $d_price = $_POST['dictionary-price'] != '' ? $_POST['dictionary-price'] : 0;
    $all_d_price = $_POST['all-dictionary-price'] != '' ? $_POST['all-dictionary-price'] : 0;
    $pop_in = $_POST['sub-popup-interval'] != '' ? $_POST['sub-popup-interval'] : 0;
    $pop_times = $_POST['sub-popup-times'] != '' ? $_POST['sub-popup-times'] : 0;
    $min_student = $_POST['min-students-subscription'] != '' ? $_POST['min-students-subscription'] : 1;

    $sat_grammar_p = $_POST['sat-grammar-price'] != '' ? $_POST['sat-grammar-price'] : 0;
    $sat_writing_p = $_POST['sat-writing-price'] != '' ? $_POST['sat-writing-price'] : 0;
    $sat_test_p = $_POST['sat-test-price'] != '' ? $_POST['sat-test-price'] : 0;
    $sat_package_price_discount = $_POST['sat-package-price-discount'] != '' ? $_POST['sat-package-price-discount'] : 0;
    $sat_math_i_package_price_discount = $_POST['sat-math-i-package-price-discount'] != '' ? $_POST['sat-math-i-package-price-discount'] : 0;
    $sat_math_ii_package_price_discount = $_POST['sat-math-ii-package-price-discount'] != '' ? $_POST['sat-math-ii-package-price-discount'] : 0;
    $pts_exchange_rate = $_POST['point-exchange-rate'] != '' ? $_POST['point-exchange-rate'] : 1;

    $teacher_sheet_margin = $_POST['teacher-sheet-price-margin'] != '' ? $_POST['teacher-sheet-price-margin'] : 0;
    $teacher_max_point = $_POST['teacher-max-point'] != '' ? $_POST['teacher-max-point'] : 0;
    $teacher_grading_margin = $_POST['teacher-grading-price-margin'] != '' ? $_POST['teacher-grading-price-margin'] : 0;


    $teacher_test_score_threshold = $_POST['teacher-test-score-threshold'] != '' ? $_POST['teacher-test-score-threshold'] : 0;
    $teacher_test_math_score_threshold = $_POST['teacher-math-test-score-threshold'] != '' ? $_POST['teacher-math-test-score-threshold'] : 0;

    $math_min_students_subscription = $_POST['math-min-students-subscription'] != '' ? $_POST['math-min-students-subscription'] : 0;
    $math_teacher_tool_price = $_POST['math-teacher-tool-price'] != '' ? $_POST['math-teacher-tool-price'] : 0;
    $math_self_study_price = $_POST['math-self-study-price'] != '' ? $_POST['math-self-study-price'] : 0;
    $math_sat1_price = $_POST['math-sat1-price'] != '' ? $_POST['math-sat1-price'] : 0;
    $math_sat2_price = $_POST['math-sat2-price'] != '' ? $_POST['math-sat2-price'] : 0;
    $math_ik_price = $_POST['math-ik-price'] != '' ? $_POST['math-ik-price'] : 0;
    $math_ik_price1 = $_POST['math-ik-price1'] != '' ? $_POST['math-ik-price1'] : 0;
    $math_ik_price2 = $_POST['math-ik-price2'] != '' ? $_POST['math-ik-price2'] : 0;
    $math_ik_price3 = $_POST['math-ik-price3'] != '' ? $_POST['math-ik-price3'] : 0;
    $math_ik_price4 = $_POST['math-ik-price4'] != '' ? $_POST['math-ik-price4'] : 0;
    $math_ik_price5 = $_POST['math-ik-price5'] != '' ? $_POST['math-ik-price5'] : 0;
    $math_ik_price6 = $_POST['math-ik-price6'] != '' ? $_POST['math-ik-price6'] : 0;
    $math_ik_price7 = $_POST['math-ik-price7'] != '' ? $_POST['math-ik-price7'] : 0;
    $math_ik_price8 = $_POST['math-ik-price8'] != '' ? $_POST['math-ik-price8'] : 0;
    $math_ik_price9 = $_POST['math-ik-price9'] != '' ? $_POST['math-ik-price9'] : 0;
    $math_ik_price10 = $_POST['math-ik-price10'] != '' ? $_POST['math-ik-price10'] : 0;
    $math_ik_price11 = $_POST['math-ik-price11'] != '' ? $_POST['math-ik-price11'] : 0;
    $math_ik_price12 = $_POST['math-ik-price12'] != '' ? $_POST['math-ik-price12'] : 0;

    mw_set_option('teacher-tool-price', $tt_price);
    mw_set_option('self-study-price', $self_study_price);
    mw_set_option('dictionary-price', $d_price);
    mw_set_option('all-dictionary-price', $all_d_price);
    mw_set_option('sub-popup-interval', $pop_in);
    mw_set_option('sub-popup-times', $pop_times);
    mw_set_option('min-students-subscription', $min_student);

    mw_set_option('sat-grammar-price', $sat_grammar_p);
    mw_set_option('sat-writing-price', $sat_writing_p);
    mw_set_option('sat-test-price', $sat_test_p);
    mw_set_option('sat-package-price-discount', $sat_package_price_discount);
    mw_set_option('sat-math-i-package-price-discount', $sat_math_i_package_price_discount);
    mw_set_option('sat-math-ii-package-price-discount', $sat_math_ii_package_price_discount);

    mw_set_option('teacher-sheet-price-margin', $teacher_sheet_margin);
    mw_set_option('teacher-grading-price-margin', $teacher_grading_margin);
    mw_set_option('teacher-max-point', $teacher_max_point);
    mw_set_option('point-exchange-rate', $pts_exchange_rate);

    mw_set_option('math-min-students-subscription', $math_min_students_subscription);
    mw_set_option('math-teacher-tool-price', $math_teacher_tool_price);
    mw_set_option('math-self-study-price', $math_self_study_price);
    mw_set_option('math-sat1-price', $math_sat1_price);
    mw_set_option('math-sat2-price', $math_sat2_price);
    mw_set_option('math-ik-price', $math_ik_price);
    mw_set_option('math-ik-price1', $math_ik_price1);
    mw_set_option('math-ik-price2', $math_ik_price2);
    mw_set_option('math-ik-price3', $math_ik_price3);
    mw_set_option('math-ik-price4', $math_ik_price4);
    mw_set_option('math-ik-price5', $math_ik_price5);
    mw_set_option('math-ik-price6', $math_ik_price6);
    mw_set_option('math-ik-price7', $math_ik_price7);
    mw_set_option('math-ik-price8', $math_ik_price8);
    mw_set_option('math-ik-price9', $math_ik_price9);
    mw_set_option('math-ik-price10', $math_ik_price10);
    mw_set_option('math-ik-price11', $math_ik_price11);
    mw_set_option('math-ik-price12', $math_ik_price12);

    mw_set_option('teacher-test-group', $_POST['teacher-test-group']);
    mw_set_option('teacher-math-test-group', $_POST['teacher-math-test-group']);
    mw_set_option('teacher-test-score-threshold', $teacher_test_score_threshold);
    mw_set_option('teacher-math-test-score-threshold', $teacher_test_math_score_threshold);

    wp_redirect(home_url() . '/?r=price-manager');
    exit;
}

if (isset($_POST['submit-agreement'])) {
    mw_set_option('registration-agreement', $_POST['registration_agreement']);
    mw_set_option('math-registration-agreement', $_POST['math_registration_agreement']);
    mw_set_option('teaching-agreement', $_POST['teaching_agreement']);
    mw_set_option('math-registration-agreement', $_POST['math_registration_agreement']);
    mw_set_option('math-teaching-agreement', $_POST['math_teaching_agreement']);
    mw_set_option('math-chat-notice', $_POST['math_chat_notice']);
    mw_set_option('math-chat-price', $_POST['math_chat_price']);
    mw_set_option('agreement-update-date', date('Y-m-d H:i:s', time()));

    wp_redirect(home_url() . '/?r=price-manager');
    exit;
}

if (isset($_POST['submit-link'])) {
    mw_set_option('english-link-en', $_POST['english-link-en']);
    mw_set_option('english-link-ja', $_POST['english-link-ja']);
    mw_set_option('english-link-ko', $_POST['english-link-ko']);
    mw_set_option('english-link-zh', $_POST['english-link-zh']);
    mw_set_option('english-link-vn', $_POST['english-link-vn']);

    mw_set_option('math-link-en', $_POST['math-link-en']);
    mw_set_option('math-link-ja', $_POST['math-link-ja']);
    mw_set_option('math-link-ko', $_POST['math-link-ko']);
    mw_set_option('math-link-zh', $_POST['math-link-zh']);
    mw_set_option('math-link-vn', $_POST['math-link-vn']);


    wp_redirect(home_url() . '/?r=price-manager');
    exit;
}

if (isset($_POST['submit-mange-sub'])) {
    $fee_join_group = $_POST['fee-join-group'] != '' ? $_POST['fee-join-group'] : 0;
    $sub_rate_teacher = $_POST['sub-rate-teacher'] != '' ? $_POST['sub-rate-teacher'] : 0;
    $name_math_tutoring = $_POST['name-math-tutoring'] != '' ? $_POST['name-math-tutoring'] : 0;
    $price_math_tutoring = $_POST['price-math-tutoring'] != '' ? $_POST['price-math-tutoring'] : 0;
    $deducted_teacher = $_POST['deducted-teacher'] != '' ? $_POST['deducted-teacher'] : 0;
    $name_math_intensive_tutoring = $_POST['name-math-intensive-tutoring'] != '' ? $_POST['name-math-intensive-tutoring'] : 0;
    $price_math_intensive_tutoring = $_POST['price-math-intensive-tutoring'] != '' ? $_POST['price-math-intensive-tutoring'] : 0;
    $deducted_intensive_teacher = $_POST['deducted-intensive-teacher'] != '' ? $_POST['deducted-intensive-teacher'] : 0;
    mw_set_option('fee-join-group', $fee_join_group);
    mw_set_option('sub-rate-teacher', $sub_rate_teacher);
    mw_set_option('name-math-tutoring', $name_math_tutoring);
    mw_set_option('price-math-tutoring', $price_math_tutoring);
    mw_set_option('deducted-teacher', $deducted_teacher);
    mw_set_option('name-math-intensive-tutoring', $name_math_intensive_tutoring);
    mw_set_option('price-math-intensive-tutoring', $price_math_intensive_tutoring);
    mw_set_option('deducted-intensive-teacher', $deducted_intensive_teacher);
    global $wpdb;
    $param = $_POST['upt_sub'];

    $re = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}dict_subscription_type WHERE id = '11'");
    if (!$re) {
        $wpdb->insert(
                $wpdb->prefix . 'dict_subscription_type', array('id' => 11, 'name' => '')
        );
    }
    foreach ($param as $n => $data) {
        if (!empty($data)) {
            $wpdb->update(
                    $wpdb->prefix . 'dict_subscription_type', array('name' => trim($data)), array('id' => $n)
            );
        }
    }
    wp_redirect(home_url() . '/?r=price-manager');
    exit;
}

$teacher_test_groups = MWDB::get_groups(array('group_type' => GROUP_CLASS, 'class_type' => CLASS_OTHERS));
$sub = MWDB::_get_name_subscription_type();
?>
<?php get_dict_header('Admin Price Manager') ?>
<?php get_dict_page_title('Admin Price Manager', 'admin-page') ?>

<form action="<?php echo home_url() ?>/?r=price-manager" method="post">	
    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <div class="col-sm-12">
                    <h2 class="title-border">Homework Tools</h2>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="teacher-tool-price">Teacher's tool price <small>(cents)</small></label>
                        <input type="number" class="form-control" name="teacher-tool-price" min="0" id="teacher-tool-price" value="<?php echo mw_get_option('teacher-tool-price') ?>">
                        <div style="margin-top: 8px; text-align: right"><span style="color: #fff"><strong>X</strong></span> Number of Students <span style="color: #fff"><strong>X</strong></span> Number of months</div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="min-students-subscription">Min number of students/licenses</label>
                        <input type="number" class="form-control" name="min-students-subscription" min="1" id="min-students-subscription" value="<?php echo mw_get_option('min-students-subscription') ?>">
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="self-study-price">Student Self-study <small>(Per month, dollars)</small></label>
                        <input type="number" class="form-control" name="self-study-price" min="0" id="self-study-price" value="<?php echo mw_get_option('self-study-price') ?>">
                    </div>
                </div>
                <div class="col-sm-12 css-body-new-class" >
                    <h2 class="title-border css-title5" >Create a New Class</h2>
                    <h3 class="color-0997C7" style="padding-left: 4%;">Subscription Fee</h3>
                    <div class="css-class-sb">Class subscription is <span class="color-0997C7">$5.00</span> per month per student.</div>
                    <div class="col-sm-12" style="padding-left: 4% !important;font-size: 17px;">
                        <div id="admin-price-month" class="col-sm-2" style="padding-left: 0px !important;margin-top: 1%;">
                            <div style="color: #A7A7A7;margin-bottom: 10px">Number of Months</div>
                                <select id="select-month" class="select-box-it form-control color-0997C7 css-arrow-admin-price" style="font-weight: bold;padding-left: 15%">
                                    <?php for ($i = 1; $i <= 12; $i++) { ?>
                                        <?php if ($i >= 10) : ?>
                                            <option value="<?php echo $i ?>"><?php echo $i ?></option>
                                        <?php else: ?>
                                            <option value="<?php echo $i ?>"><?php echo '0' . $i ?></option>
                                        <?php endif; ?>
                                    <?php } ?>
                                </select>
                        </div>
                        <div id="admin-price-student" class="col-sm-2" style="padding-left: 0px !important;margin-top: 1%;">
                            <div style="color: #A7A7A7;margin-bottom: 10px">Number of Months</div>
                            <input id="btn-enter-student" type="number" min="<?php echo mw_get_option('min-students-subscription') ?>" step="1" style="width: 100%;height: 34px;padding-left: 10%">
                        </div>
                        <input type="hidden" id="hiddent_min_student" value="<?php echo mw_get_option('min-students-subscription') ?>">
                        <div id="admin-price-total" class="col-sm-4 css-sub-fee-ad">
                            <div class="css-sub-total"> = Subscription Fee: <span id="total-price" class="color-0997C7 css-font-weight"></span></div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="color: #000;font-size: 17px;">
                        <hr class="hr-admin-price">
                        <div style="padding-left: 4%">Will you charge student to join the class?</div>
                        <div style="padding-left: 4%">
                            <div class="col-sm-12" style="padding-left: 0px !important">
                                <div class="col-sm-2">
                                    <div class="radio" style="margin-bottom: 0px;">
                                        <input id="rdo-no" type="checkbox" class="gCheckbox" name="fooby[1][]" value="0">
                                        <label for="rdo-no" class="lab-checkbox css-font-style"><?php _e('No', 'iii-dictionary') ?></label>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="col-sm-1 " style="padding-left: 0px !important">
                                        <div class="radio" style="margin-bottom: 0px;margin-top: 0px">	
                                            <input id="rdo-yes" type="checkbox" class="gCheckbox" name="fooby[1][]" value="1" checked>
                                            <label for="rdo-yes" class="lab-checkbox css-font-style"><?php _e('Yes', 'iii-dictionary') ?></label>
                                        </div>
                                    </div>
                                    <div class="col-sm-11 css-ic">
                                        <span class="span-bor">$2.00</span>
                                        <span>Per Student/Month</span>
                                        <span class="icon-cls" id="icon-new-class"></span>
                                    </div>
                                </div>
                            </div>
                        </div>  
                    </div>
                    <div class="col-sm-12">
                        <hr class="hr-admin-price">
                        <h3 style="padding-left: 4%" class="color-0997C7">Class Name and Passswork</h3>
                    </div>
                </div>
                
                <div class="col-sm-12">
                    <h2 class="title-border">Dictionary</h2>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="dictionary-price">Dictionary subscription price <small>(cents)</small></label>
                        <input type="number" class="form-control" name="dictionary-price" min="0" id="dictionary-price" value="<?php echo mw_get_option('dictionary-price') ?>">
                        <div style="margin-top: 8px; text-align: right"><span style="color: #fff"><strong>X</strong></span> Number of Licenses <span style="color: #fff"><strong>X</strong></span> Number of months</div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="all-dictionary-price">All dictionary subscription price <small>(cents)</small></label>
                        <input type="number" class="form-control" name="all-dictionary-price" min="0" id="all-dictionary-price" value="<?php echo mw_get_option('all-dictionary-price') ?>">
                        <div style="margin-top: 8px; text-align: right"><span style="color: #fff"><strong>X</strong></span> Number of Licenses <span style="color: #fff"><strong>X</strong></span> Number of months</div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="sub-popup-interval">Dictionary subscription popup time interval <small>(seconds)</small></label>
                        <input type="number" class="form-control" name="sub-popup-interval" min="0" id="sub-popup-interval" value="<?php echo mw_get_option('sub-popup-interval') ?>">
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="sub-popup-times">Number of searched words without popup <small>(times)</small></label>
                        <input type="number" class="form-control" name="sub-popup-times" min="0" id="sub-popup-times" value="<?php echo mw_get_option('sub-popup-times') ?>">
                    </div>
                </div>

                <div class="col-sm-12">
                    <h2 class="title-border">SAT Preparation</h2>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="sat-grammar-price">Grammar Class per month <small>($)</small></label>
                        <input type="number" class="form-control" name="sat-grammar-price" min="0" step="any" id="sat-grammar-price" value="<?php echo mw_get_option('sat-grammar-price') ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="sat-writing-price">Writing Class per month <small>($)</small></label>
                        <input type="number" class="form-control" name="sat-writing-price" min="0" step="any" id="sat-writing-price" value="<?php echo mw_get_option('sat-writing-price') ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="sat-test-price">SAT Practice test per month <small>($)</small></label>
                        <input type="number" class="form-control" name="sat-test-price" min="0" step="any" id="sat-test-price" value="<?php echo mw_get_option('sat-test-price') ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="sat-package-price-discount">Discount English study package <small>(%)</small></label>
                        <input type="number" class="form-control" name="sat-package-price-discount" min="0" step="any" id="sat-package-price-discount" value="<?php echo mw_get_option('sat-package-price-discount') ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="sat-math-i-package-price-discount">Discount Math I study package <small>(%)</small></label>
                        <input type="number" class="form-control" name="sat-math-i-package-price-discount" min="0" step="any" id="sat-math-i-package-price-discount" value="<?php echo mw_get_option('sat-math-i-package-price-discount') ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="sat-math-ii-package-price-discount">Discount Math II study package <small>(%)</small></label>
                        <input type="number" class="form-control" name="sat-math-ii-package-price-discount" min="0" step="any" id="sat-math-ii-package-price-discount" value="<?php echo mw_get_option('sat-math-ii-package-price-discount') ?>">
                    </div>
                </div>

                <div class="col-sm-12">
                    <h2 class="title-border">Teacher</h2>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="teacher-sheet-price-margin">Margin to be added to the teacher's worksheet price <small>(%)</small></label>
                        <input type="number" class="form-control" name="teacher-sheet-price-margin" min="0" max="100" id="teacher-sheet-price-margin" value="<?php echo mw_get_option('teacher-sheet-price-margin') ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="teacher-grading-price-margin">Margin to be taken from the writing grading price <small>(%)</small></label>
                        <input type="number" class="form-control" name="teacher-grading-price-margin" min="0" max="100" id="teacher-grading-price-margin" value="<?php echo mw_get_option('teacher-grading-price-margin') ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="teacher-max-point">The MAX point for teachers</label>
                        <input type="number" class="form-control" name="teacher-max-point" min="0" id="teacher-max-point" value="<?php echo mw_get_option('teacher-max-point') ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="point-exchange-rate">Points conversion rate <small>(Point per 1$)</small></label>
                        <input type="number" class="form-control" name="point-exchange-rate" min="1" id="point-exchange-rate" value="<?php echo mw_get_option('point-exchange-rate') ?>">
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Select a Group containing Teacher's Test</label>
                        <select class="select-box-it form-control" name="teacher-test-group">
                            <option value="">Select one</option>
                            <?php foreach ($teacher_test_groups->items as $item) : ?>
                                <option value="<?php echo $item->id ?>"<?php echo $item->id == mw_get_option('teacher-test-group') ? ' selected' : '' ?>><?php echo $item->name ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Teacher Test Passing Threshold</label>
                        <input type="number" name="teacher-test-score-threshold" class="form-control" min="0" value="<?php echo mw_get_option('teacher-test-score-threshold') ?>">
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Select a Group containing Teacher's Test Math</label>
                        <select class="select-box-it form-control" name="teacher-math-test-group">
                            <option value="">Select one</option>
                            <?php foreach ($teacher_test_groups->items as $item) : ?>
                                <option value="<?php echo $item->id ?>"<?php echo $item->id == mw_get_option('teacher-math-test-group') ? ' selected' : '' ?>><?php echo $item->name ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Teacher Test Math Passing Threshold</label>
                        <input type="number" name="teacher-math-test-score-threshold" class="form-control" min="0" value="<?php echo mw_get_option('teacher-math-test-score-threshold') ?>">
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="col-sm-12">
                    <h2 class="title-border">Homework Tools For Math</h2>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="math-teacher-tool-price">Teacher's tool price <small>(cents)</small></label>
                        <input type="number" class="form-control" name="math-teacher-tool-price" min="0" id="math-teacher-tool-price" value="<?php echo mw_get_option('math-teacher-tool-price') ?>">
                        <div style="margin-top: 8px; text-align: right"><span style="color: #fff"><strong>X</strong></span> Number of Students <span style="color: #fff"><strong>X</strong></span> Number of months</div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="math-min-students-subscription">Min number of students/licenses</label>
                        <input type="number" class="form-control" name="math-min-students-subscription" min="1" id="math-min-students-subscription" value="<?php echo mw_get_option('math-min-students-subscription') ?>">
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="self-study-price">Student Self-study <small>(Per month, dollars)</small></label>
                        <input type="number" class="form-control" name="math-self-study-price" min="0" id="math-self-study-price" value="<?php echo mw_get_option('math-self-study-price') ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>SAT1 Preparation </label>
                        <input type="number" class="form-control" name="math-sat1-price" min="0" value="<?php echo mw_get_option('math-sat1-price') ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>SAT2 Preparation </label>
                        <input type="number" class="form-control" name="math-sat2-price" min="0" value="<?php echo mw_get_option('math-sat2-price') ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>IKMath Kindergarten</label>
                        <input type="number" class="form-control" name="math-ik-price" min="0" value="<?php echo mw_get_option('math-ik-price') ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Math grade 1</label>
                        <input type="number" class="form-control" name="math-ik-price1" min="0" value="<?php echo mw_get_option('math-ik-price1') ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Math grade 2</label>
                        <input type="number" class="form-control" name="math-ik-price2" min="0" value="<?php echo mw_get_option('math-ik-price2') ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Math grade 3</label>
                        <input type="number" class="form-control" name="math-ik-price3" min="0" value="<?php echo mw_get_option('math-ik-price3') ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Math grade 4</label>
                        <input type="number" class="form-control" name="math-ik-price4" min="0" value="<?php echo mw_get_option('math-ik-price4') ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Math grade 5</label>
                        <input type="number" class="form-control" name="math-ik-price5" min="0" value="<?php echo mw_get_option('math-ik-price5') ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Math grade 6</label>
                        <input type="number" class="form-control" name="math-ik-price6" min="0" value="<?php echo mw_get_option('math-ik-price6') ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Math grade 7</label>
                        <input type="number" class="form-control" name="math-ik-price7" min="0" value="<?php echo mw_get_option('math-ik-price7') ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Math grade 8</label>
                        <input type="number" class="form-control" name="math-ik-price8" min="0" value="<?php echo mw_get_option('math-ik-price8') ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Math grade 9</label>
                        <input type="number" class="form-control" name="math-ik-price9" min="0" value="<?php echo mw_get_option('math-ik-price9') ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Math grade 10</label>
                        <input type="number" class="form-control" name="math-ik-price10" min="0" value="<?php echo mw_get_option('math-ik-price10') ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Math grade 11</label>
                        <input type="number" class="form-control" name="math-ik-price11" min="0" value="<?php echo mw_get_option('math-ik-price11') ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Math grade 12</label>
                        <input type="number" class="form-control" name="math-ik-price12" min="0" value="<?php echo mw_get_option('math-ik-price12') ?>">
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="col-sm-6 form-group">
                    <label>&nbsp;</label>
                    <button name="submit-price" type="submit" class="btn btn-default btn-block orange"><span class="icon-hand"></span>Apply new pricing</button>
                </div>

                <div class="col-sm-12">
                    <h2 class="title-border">Agreement</h2>
                </div>

                <?php
                $settings = array(
                    'wpautop' => false,
                    'media_buttons' => false,
                    'quicktags' => false,
                    'textarea_rows' => 12,
                    'tinymce' => array(
                        'toolbar1' => 'bold,italic,strikethrough,image,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,spellchecker,fullscreen,wp_adv'
                    )
                );
                ?>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Teacher Registration Agreement</label>
                        <?php wp_editor(mw_get_option('registration-agreement'), 'registration_agreement', $settings); ?> 
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Teacher Teaching Agreement</label>
                        <?php wp_editor(mw_get_option('teaching-agreement'), 'teaching_agreement', $settings); ?> 
                    </div>
                </div>
                <div class="col-sm-12">
                    <h2 class="title-border">Agreement For Math</h2>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Teacher Registration Agreement</label>
                        <?php wp_editor(mw_get_option('math-registration-agreement'), 'math_registration_agreement', $settings); ?> 
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Teacher Teaching Agreement</label>
                        <?php wp_editor(mw_get_option('math-teaching-agreement'), 'math_teaching_agreement', $settings); ?> 
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Math chat requested</label>
                        <?php wp_editor(mw_get_option('math-chat-notice'), 'math_chat_notice', $settings); ?> 
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Price per 1 minutes</label>
                        <input type="number" class="form-control" name="math_chat_price" min="0" value="<?php echo mw_get_option('math-chat-price') ?>" />
                    </div>
                </div>
                <div class="clearfix" ></div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <button name="submit-agreement" type="submit" class="btn btn-default btn-block orange"><span class="icon-hand"></span>Apply new agreement</button>
                    </div>     
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-6">
                    <h2><?php _e('English side', 'iii-dictionary') ?></h2>
                </div>
                <div class="col-sm-6">
                    <h2><?php _e('Math side (main menu)', 'iii-dictionary') ?></h2>
                </div>
                <div class="col-sm-6">
                    <div class="row">
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-5 control-label"><?php _e('English', 'iii-dictionary') ?></label>
                            <div class="col-sm-7">
                                <input type="input" class="form-control" name="english-link-en" value="<?php echo mw_get_option('english-link-en') ?>">
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-5 control-label"><?php _e('Japanese', 'iii-dictionary') ?></label>
                            <div class="col-sm-7">
                                <input type="input" class="form-control" name="english-link-ja"  value="<?php echo mw_get_option('english-link-ja') ?>">
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-5 control-label"><?php _e('Korean', 'iii-dictionary') ?></label>
                            <div class="col-sm-7">
                                <input type="input" class="form-control" name="english-link-ko"  value="<?php echo mw_get_option('english-link-ko') ?>">
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-5 control-label"><?php _e('Chinese', 'iii-dictionary') ?></label>
                            <div class="col-sm-7">
                                <input type="input" class="form-control" name="english-link-zh" value="<?php echo mw_get_option('english-link-zh') ?>">
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-5 control-label"><?php _e('Vietnamese', 'iii-dictionary') ?></label>
                            <div class="col-sm-7">
                                <input type="input" class="form-control" name="english-link-vn" value="<?php echo mw_get_option('english-link-vn') ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="row">
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-5 control-label"><?php _e('English', 'iii-dictionary') ?></label>
                            <div class="col-sm-7">
                                <input type="input" class="form-control" name="math-link-en" value="<?php echo mw_get_option('math-link-en') ?>">
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-5 control-label"><?php _e('Japanese', 'iii-dictionary') ?></label>
                            <div class="col-sm-7">
                                <input type="input" class="form-control" name="math-link-ja"  value="<?php echo mw_get_option('math-link-ja') ?>">
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-5 control-label"><?php _e('Korean', 'iii-dictionary') ?></label>
                            <div class="col-sm-7">
                                <input type="input" class="form-control" name="math-link-ko"  value="<?php echo mw_get_option('math-link-ko') ?>">
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-5 control-label"><?php _e('Chinese', 'iii-dictionary') ?></label>
                            <div class="col-sm-7">
                                <input type="input" class="form-control" name="math-link-zh" value="<?php echo mw_get_option('math-link-zh') ?>">
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-5 control-label"><?php _e('Vietnamese', 'iii-dictionary') ?></label>
                            <div class="col-sm-7">
                                <input type="input" class="form-control" name="math-link-vn" value="<?php echo mw_get_option('math-link-vn') ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <button name="submit-link" type="submit" class="btn btn-default btn-block orange"><span class="icon-hand"></span><?php _e('Apply the new Links', 'iii-dictionary') ?></button>
                    </div>     
                </div>
                <div class="clearfix"></div>

                <div class="col-sm-12">
                    <h2 class="title-border">Manage Subscription</h2>
                </div>
                <div class="col-sm-6">
                    <h2><?php _e('English', 'iii-dictionary') ?></h2>
                </div>
                <div class="col-sm-6">
                    <h2><?php _e('Math', 'iii-dictionary') ?></h2>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="sub-en-hwt">Homework Tool</label>
                        <input type="text" class="form-control" name="upt_sub[1]" id="sub-en-hwt" value="<?php echo $sub[0] ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="sub-math-hwt">Homework Tool</label>
                        <input type="text" class="form-control" name="upt_sub[6]" id="sub-math-hwt" value="<?php echo $sub[5] ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="sub-en-sss">Student Self-study</label>
                        <input type="text" class="form-control" name="upt_sub[5]" id="sub-en-sss" value="<?php echo $sub[4] ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="sub-math-sss">Student Self-study</label>
                        <input type="text" class="form-control" name="upt_sub[9]" id="sub-math-sss" value="<?php echo $sub[8] ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="sub-en-sat">SAT</label>
                        <input type="text" class="form-control" name="upt_sub[3]" id="sub-en-sat" value="<?php echo $sub[2] ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="sub-math-sat1">SAT I</label>
                        <input type="text" class="form-control" name="upt_sub[7]" id="sub-math-sat1" value="<?php echo $sub[6] ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="sub-en-dic">Dictionary</label>
                        <input type="text" class="form-control" name="upt_sub[2]" id="sub-en-dic" value="<?php echo $sub[1] ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="sub-math-sat2">SAT II</label>
                        <input type="text" class="form-control" name="upt_sub[8]" id="sub-math-sat2" value="<?php echo $sub[7] ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="sub-group">English study Group</label>
                        <input type="text" class="form-control" name="upt_sub[11]" id="sub-group" value="<?php echo $sub[10] ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="sub-group">Math study Group</label>
                        <input type="text" class="form-control" name="upt_sub[10]" id="sub-group" value="<?php echo $sub[9] ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="sub-group">IKMath kindergarten</label>
                        <input type="text" class="form-control" name="upt_sub[12]" id="sub-group" value="<?php echo $sub[11] ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="sub-group">Math grade 1</label>
                        <input type="text" class="form-control" name="upt_sub[13]" id="sub-group" value="<?php echo $sub[12] ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="sub-group">Math grade 2</label>
                        <input type="text" class="form-control" name="upt_sub[14]" id="sub-group" value="<?php echo $sub[13] ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="sub-group">Math grade 3</label>
                        <input type="text" class="form-control" name="upt_sub[15]" id="sub-group" value="<?php echo $sub[14] ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="sub-group">Math grade 4</label>
                        <input type="text" class="form-control" name="upt_sub[16]" id="sub-group" value="<?php echo $sub[15] ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="sub-group">Math grade 5</label>
                        <input type="text" class="form-control" name="upt_sub[17]" id="sub-group" value="<?php echo $sub[16] ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="sub-group">Math grade 6</label>
                        <input type="text" class="form-control" name="upt_sub[18]" id="sub-group" value="<?php echo $sub[17] ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="sub-group">Math grade 7</label>
                        <input type="text" class="form-control" name="upt_sub[19]" id="sub-group" value="<?php echo $sub[18] ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="sub-group">Math grade 8</label>
                        <input type="text" class="form-control" name="upt_sub[20]" id="sub-group" value="<?php echo $sub[19] ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="sub-group">Math grade 9</label>
                        <input type="text" class="form-control" name="upt_sub[21]" id="sub-group" value="<?php echo $sub[20] ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="sub-group">Math grade 10</label>
                        <input type="text" class="form-control" name="upt_sub[22]" id="sub-group" value="<?php echo $sub[21] ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="sub-group">Math grade 11</label>
                        <input type="text" class="form-control" name="upt_sub[23]" id="sub-group" value="<?php echo $sub[22] ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="sub-group">Math grade 12</label>
                        <input type="text" class="form-control" name="upt_sub[24]" id="sub-group" value="<?php echo $sub[23] ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="sub-points">Points <small>(english/math)</small></label>
                        <input type="text" class="form-control" name="upt_sub[4]" id="sub-points" value="<?php echo $sub[3] ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="sub-fee-group">The name of fee for joining the group</label>
                        <input type="text" class="form-control" name="fee-join-group" id="sub-fee-group" value="<?php echo mw_get_option('fee-join-group') ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="sub-rate-teacher">The rate of percentage deducted from teacher (%) </label>
                        <input type="text" class="form-control" name="sub-rate-teacher" id="sub-rate-teacher" value="<?php echo mw_get_option('sub-rate-teacher') ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="name-math-tutoring">The name of Basic Math Tutoring </label>
                        <input type="text" class="form-control" name="name-math-tutoring" id="name-math-tutoring" value="<?php echo mw_get_option('name-math-tutoring') ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="price-math-tutoring">The price of Basic Math Tutoring </label>
                        <input type="text" class="form-control" name="price-math-tutoring" id="price-math-tutoring" value="<?php echo mw_get_option('price-math-tutoring') ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="deducted-teacher">The percentage (%) deducted from the teacher for the basic Program</label>
                        <input type="text" class="form-control" name="deducted-teacher" id="deducted-teacher" value="<?php echo mw_get_option('deducted-teacher') ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="name-math-intensive-tutoring">The name of Intensive Math Tutoring</label>
                        <input type="text" class="form-control" name="name-math-intensive-tutoring" id="name-math-tutoring" value="<?php echo mw_get_option('name-math-intensive-tutoring') ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="price-intensive-math-teacher">The price of Intensive Math Tutoring</label>
                        <input type="text" class="form-control" name="price-math-intensive-tutoring" id="price-intensive-math-teacher" value="<?php echo mw_get_option('price-math-intensive-tutoring') ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="deducted-intensive-teacher">The percentage (%) deducted from the teacher for the intensive Program</label>
                        <input type="text" class="form-control" name="deducted-intensive-teacher" id="deducted-intensive-teacher" value="<?php echo mw_get_option('deducted-intensive-teacher') ?>">
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="sub-group"> </label>
                        <button name="submit-mange-sub" type="submit" class="btn btn-default btn-block orange"><span class="icon-hand"></span><?php _e('Apply the new subscription', 'iii-dictionary') ?></button>
                    </div>     
                </div>
                <div class="clearfix"></div>

            </div>
        </div>
    </div>
</form>

<?php get_dict_footer() ?>
<script>
    (function ($){
        $(function(){
            $('#btn-enter-student').change (function(){
                var student = $(this).val();
                var min_student = $('#hiddent_min_student').val();
                console.log(min_student);
                if(student >= min_student) {
                    var month = $('#select-month').val();
                    $('#total-price').html('$'+student*month+'.00');
                }else{
                    $('#total-price').html();
                }
            });
            $('#select-month').change(function(){
                var month = $(this).val();
                var student = $('#btn-enter-student').val();
                $('#total-price').html('$'+student*month+'.00');
                console.log(student);
            });
            $('#icon-new-class').click(function(){
                var content = '<div class="css-color-000 css-fo">1.Student will be required to pay when joining class</div><div class="css-color-000 css-fo">2.xx% to be paid to teacher</div>'
                $(this).popover({
                    content: '<span class="text-danger">' + content + '</span>', html: true, placement: "bottom"}).popover("show");
            });
            $("input:checkbox").on('click', function() {
            // in the handler, 'this' refers to the box clicked on
            var $box = $(this);
            if ($box.is(":checked")) {
              // the name of the box is retrieved using the .attr() method
              // as it is assumed and expected to be immutable
              var group = "input:checkbox[name='" + $box.attr("name") + "']";
              // the checked state of the group/box on the other hand will change
              // and the current value is retrieved using .prop() method
              $(group).prop("checked", false);
              $box.prop("checked", true);
            } else {
              $box.prop("checked", false);
            }
          });
        });
    })(jQuery)
</script>