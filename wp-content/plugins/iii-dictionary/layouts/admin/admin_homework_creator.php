
<?php
wp_register_script('csv-js', get_stylesheet_directory_uri() . '/library/js/jquery.csv-0.71.min.js', array('jquery'));
wp_enqueue_script('csv-js');
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

$tab_options = array(
    'items' => array(
        'english' => array('url' => home_url() . '/?r=admin-homework-creator/english', 'text' => 'English'),
        'mathematics' => array('url' => home_url() . '/?r=admin-homework-creator/mathematics', 'text' => 'Mathematics')
    ),
    'active' => $active_tab
);

switch ($active_tab) {
    // english homework
    case 'english':

        // process task
        $data = array();
        $data['assignment-id'] = ASSIGNMENT_SPELLING;

        // update or create english sheet
        if (isset($task['create']) || isset($task['update'])) {
            $data['id'] = $_REAL_POST['sid'];
            $data['lesson_id'] = $_REAL_POST['lesson_id'];
            $data['homework-types'] = $_REAL_POST['homework-types'];
            $data['sheet-categories'] = $_REAL_POST['sheet-categories'];
            $data['trivia-exclusive'] = isset($_REAL_POST['trivia-exclusive']) ? $_REAL_POST['trivia-exclusive'] : 0;
            $data['grade'] = $_REAL_POST['grade'];
            $data['sheet-name'] = $_REAL_POST['sheet-name'];
            $data['grading-price'] = $_REAL_POST['grading-price'];
            $data['dictionary'] = $_REAL_POST['dictionary'];
            $data['questions'] = $_REAL_POST['words'];
            $data['reading_passage'] = $_REAL_POST['reading_passage'];
            $data['description'] = $_REAL_POST['description'];
            $data['wordchecked'] = $_REAL_POST['wordchecked'];
            $data['active'] = 0; // disable sheet by default
            $data['next-worksheet-id'] = $_REAL_POST['next-worksheet-id'];
            $data['lang'] = !empty($_POST['lang']) ? $_POST['lang'] : 'en';

            if (MWDB::store_sheet($data)) {
                wp_redirect(home_url() . '/?r=admin-homework-creator/english');
                exit;
            } else {
                /* if($_REAL_POST['sid']) {
                  wp_redirect(home_url() . '/?r=admin-homework-creator/english&layout=create&cid=' . $_REAL_POST['sid']);
                  exit;
                  } */
            }
        }
        if (isset($task['update6'])) {
            $data['id'] = $_REAL_POST['sid'];
            $data['lesson_id'] = $_REAL_POST['lesson_id'];
            $data['homework-types'] = $_REAL_POST['homework-types'];
            $data['sheet-categories'] = $_REAL_POST['sheet-categories'];
            $data['trivia-exclusive'] = isset($_REAL_POST['trivia-exclusive']) ? $_REAL_POST['trivia-exclusive'] : 0;
            $data['grade'] = $_REAL_POST['grade'];
            $data['sheet-name'] = $_REAL_POST['sheet-name'];
            $data['grading-price'] = $_REAL_POST['grading-price'];
            $data['dictionary'] = $_REAL_POST['dictionary'];
            $data['questions'] = $_REAL_POST['words'];
            $data['reading_passage'] = $_REAL_POST['reading_passage'];
            $data['description'] = $_REAL_POST['description'];
            $data['wordchecked'] = $_REAL_POST['wordchecked'];
            $data['active'] = 0; // disable sheet by default
            $data['next-worksheet-id'] = $_REAL_POST['next-worksheet-id'];
            $data['lang'] = !empty($_POST['lang']) ? $_POST['lang'] : 'en';

            if (MWDB::store_sheet_report($data)) {
                wp_redirect(home_url() . '/?r=admin-homework-creator/english');
                exit;
            } else {
                /* if($_REAL_POST['sid']) {
                  wp_redirect(home_url() . '/?r=admin-homework-creator/english&layout=create&cid=' . $_REAL_POST['sid']);
                  exit;
                  } */
            }
        }
        // toggle active
        if (isset($task['active'])) {
            $cid = $_REAL_POST['cid'];

            if (!empty($cid)) {
                if (MWDB::toggle_active_sheets($cid)) {
                    ik_enqueue_messages('Successfully active/deactive ' . count($cid) . ' sheets', 'success');

                    wp_redirect(home_url() . '/?r=admin-homework-creator/english');
                    exit;
                }
            } else {
                ik_enqueue_messages('Please select a sheet.', 'error');
            }
        }

        // remove sheet
        if (isset($task['remove'])) {
            $cid = $_REAL_POST['cid'];

            if (MWDB::delete_sheets($cid)) {
                wp_redirect(home_url() . '/?r=admin-homework-creator/english');
                exit;
            }
        }

        // export all sheets to CSV file
        if (isset($_POST['export'])) {
            $slist = MWDB::get_all_sheets();

            header('Content-Type: text/csv');
            header('Content-Disposition: attachment;filename=homework_export_' . date('mdY_Hms', time()));
            $fp = fopen('php://output', 'w');
            foreach ($slist as $item) {
                $row_header = array('Sheet Name: ' . $item->sheet_name . ' --- Grade: ' . $item->grade);
                fputcsv($fp, $row_header);
                $content = json_decode($item->questions);
                if ($item->assignment_id == ASSIGNMENT_SPELLING) {
                    foreach ($content as $item) {
                        fputcsv($fp, array(html_entity_decode($item, ENT_QUOTES)));
                    }
                } else {
                    foreach ($content->question as $key => $value) {
                        $col1 = html_entity_decode($content->quiz[$key], ENT_QUOTES);
                        $col2 = html_entity_decode($content->question[$key], ENT_QUOTES);
                        $col3 = html_entity_decode($content->c_answer[$key], ENT_QUOTES);
                        $col4 = html_entity_decode($content->w_answer1[$key], ENT_QUOTES);
                        $col5 = html_entity_decode($content->w_answer2[$key], ENT_QUOTES);

                        $row = array($col1, $col2, $col3, $col4, $col5);
                        if (!empty($content->w_answer3[$key])) {
                            $row[] = html_entity_decode($content->w_answer3[$key], ENT_QUOTES);
                        }
                        if (!empty($content->w_answer4[$key])) {
                            $row[] = html_entity_decode($content->w_answer4[$key], ENT_QUOTES);
                        }

                        fputcsv($fp, $row);

                        if ($item->assignment_id == ASSIGNMENT_READING) {
                            fputcsv($fp, array(strip_tags($item->passages)));
                        }
                    }
                }
                fputcsv($fp, array());
                fputcsv($fp, array());
            }
            fclose($fp);
            exit;
        }
        //$main_categories = MWDB::get_grades(array('type' => 'ENGLISH', 'level' => 0, 'admin_only' => 1, 'orderby' => 'ordering', 'order-dir' => 'asc'));
        $levels = MWDB::get_grades(array('type' => 'ENGLISH', 'level' => 2));
        $sublevels = MWDB::get_grades(array('type' => 'ENGLISH', 'level' => 3));

        $sel_sublevels_html = '';
        foreach ($levels as $level) {
            $sel_sublevels_html .= '<select class="hidden" id="_sl' . $level->id . '">';
            
            foreach ($sublevels as $sublevel) {
                if ($sublevel->parent_id == $level->id) {
                    $sel_sublevels_html .= '<option value="' . $sublevel->id . '">' . $sublevel->name . '</option>';
                }
            }
            $sel_sublevels_html .= '</select>';
        }

        // page content
        if ($cid) { // view a sheet
            $current_sheet = $wpdb->get_row($wpdb->prepare(
                            'SELECT s.*, gr.name,p.lid AS grade
					FROM ' . $wpdb->prefix . 'dict_sheets AS s
					JOIN ' . $wpdb->prefix . 'dict_grades AS gr ON gr.id = s.grade_id JOIN (SELECT id AS lid,parent_id AS pid, name AS parent_name
								FROM ' . $wpdb->prefix . 'dict_grades
								WHERE level = 3) AS p ON p.pid = gr.parent_id
					WHERE s.id = %s', $cid
            ));

            $data['lesson_id'] = $current_sheet->lid;
            $data['homework-types'] = $current_sheet->homework_type_id;
            $data['sheet-categories'] = $current_sheet->category_id;
            $data['trivia-exclusive'] = $current_sheet->trivia_exclusive;
            $data['grade'] = $current_sheet->grade_id;
            $data['group-name'] = $current_sheet->group_name;
            $data['sheet-name'] = $current_sheet->sheet_name;
            $data['grading-price'] = $current_sheet->grading_price;
            $data['dictionary'] = $current_sheet->dictionary_id;
            $data['questions'] = json_decode($current_sheet->questions, true);
            $data['reading_passage'] = $current_sheet->passages;
            $data['description'] = $current_sheet->description;
            $data['lang'] = $current_sheet->lang;
        } else { // sheet list
            $current_page = max(1, get_query_var('page'));
            // $filter = get_page_filter_session();
            if (empty($filter) && !isset($_REAL_POST['filter'])) {
                $filter['orderby'] = 'grade';
                //$filter['order-dir'] = 'asc';
                $filter['items_per_page'] = 20;
                $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
            } else {
                if (isset($_REAL_POST['filter']['search'])) {
                    $filter['lang'] = $_POST['filter']['lang'];
                    $filter['sheet-name'] = $_REAL_POST['filter']['sheet-name'];
                    $filter['group-name'] = $_REAL_POST['filter']['group-name'];
                    $filter['grade'] = $_REAL_POST['filter']['grade'];
                    $filter['lesson_id'] = $_REAL_POST['filter']['lesson_id'];
                    $filter['homework-types'] = $_REAL_POST['filter']['homework-types'];
                    $filter['trivia-exclusive'] = $_REAL_POST['filter']['trivia-exclusive'];
                    $filter['active'] = $_REAL_POST['filter']['active'];
                    $check_global = 1;
                }

                if (isset($_REAL_POST['filter']['orderby'])) {
                    $filter['orderby'] = $_REAL_POST['filter']['orderby'];
                    $filter['order-dir'] = $_REAL_POST['filter']['order-dir'];
                }

                $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
            }
            //set_page_filter_session($filter);
            $filter['offset'] = 0;
            $filter['items_per_page'] = 100;
            $sheets_obj = MWDB::get_sheets($filter, false, true);

            $avail_sheets = $sheets_obj->items;
            $total_rows = $sheets_obj->total;
            $total_pages = ceil($total_rows / $filter['items_per_page']);
            $pagination = paginate_links(array(
                'format' => '?page=%#%',
                'current' => $current_page,
                'total' => $total_pages
            ));
        }

        break; // end case english
    // Math homework
    case 'mathematics':

        // create or update a worksheet
        if (isset($task['create']) || isset($task['update'])) {
            $data['assignment_id'] = $_POST['math-assignments'];
            $data['homework_type_id'] = $_POST['homework-types'];
            $data['grade_id'] = $_POST['sublevel'];
            $data['sheet_name'] = $_REAL_POST['sheet-name'];
            $data['questions'] = $_REAL_POST['questions'];
            $data['description'] = $_REAL_POST['description'];
            $data['answer_time_limit'] = $_POST['answer-time-limit'];
            $data['show_answer_after'] = $_POST['show-answer-after'];
            $data['category_id'] = 5; // Set to Math category
            $data['active'] = 1;
            $data['created_on'] = date('Y-m-d', time());
            $data['lang'] = $_POST['lang'];
            switch ($data['assignment_id']) {
                case MATH_ASSIGNMENT_SINGLE_DIGIT_DIV:
                case MATH_ASSIGNMENT_TWO_DIGIT_DIV:
                    $data['questions']['sign'] = '&divide;';
                    $steps = explode("\r\n", $data['questions']['steps']);
                    foreach ($steps as $key => $v) {
                        $data['questions']['step']['s' . ($key + 1)] = $v;
                    }
                    $total_step = count($steps);
                    $data['questions']['step']['s' . ($total_step + 1)] = $data['questions']['remainder'];
                    $data['questions']['step']['s' . ($total_step + 2)] = $data['questions']['answer'];
                    break;

                case MATH_ASSIGNMENT_FLASHCARD:
                case MATH_ASSIGNMENT_FRACTION:
                case MATH_ASSIGNMENT_EQUATION:
                    foreach ($data['questions']['q'] as $key => $item) {
                        $data['questions']['q'][$key]['op'] = htmlentities($item['op']);
                        if (trim($item['answer']) == '') {
                            unset($data['questions']['q'][$key]);
                        }
                    }
                    break;

                case MATH_ASSIGNMENT_WORD_PROB:
                    foreach ($data['questions']['q'] as $key => $item) {
                        if (empty($item['image']) || trim($item['image']) == '') {
                            unset($data['questions']['q'][$key]);
                        }
                    }
                    break;
                case MATH_ASSIGNMENT_LIST:
                    foreach ($data['questions']['q'] as $key => $item) {
                        if (empty($item['name']) || trim($item['name']) == '') {
                            unset($data['questions']['q'][$key]);
                        }
                    }
                    break;
            }

            if (!empty($_POST['cid'])) {
                $data['id'] = $_POST['cid'];
            } else {
                $data['created_by'] = get_current_user_id();

                $hightest_order = $wpdb->get_col(
                        $wpdb->prepare('SELECT MAX(ordering) FROM ' . $wpdb->prefix . 'dict_sheets WHERE grade_id = %d', $data['grade_id'])
                );
                $data['ordering'] = $hightest_order[0] + 1;
            }

            $sel_level_category = $_POST['level-category'];
            $sel_level = $_POST['level'];
//            var_dump($data);die;
            if (MWDB::store_math_sheet($data)) {
                wp_redirect(home_url() . '/?r=admin-homework-creator/mathematics');
                exit;
            }
        }
        // Up and Down odering Math
        // change sheet order up
        if (isset($_POST['order-up'])) {
            MWDB::set_math_sheet_order_up($_POST['oid']);
            wp_redirect(locale_home_url() . '/?r=admin-homework-creator/mathematics');
            exit;
        }
        // change sheet order down
        if (isset($_POST['order-down'])) {
            MWDB::set_math_sheet_order_down($_POST['oid']);
//            wp_redirect(locale_home_url() . '/?r=admin-homework-creator/mathematics');
            exit;
        }
        // Up and Down odering English
        if (isset($_POST['order-up-english'])) {
//            var_dump("111");die;
            $check_global = 1;
            MWDB::set_math_sheet_order_up($_POST['oid']);
            wp_redirect(locale_home_url() . '/?r=admin-homework-creator/mathematics');
            exit;
        }

        if (isset($_POST['order-down-english'])) {
//            var_dump("222");die;
            $check_global = 1;
            MWDB::set_math_sheet_order_down($_POST['oid']);
//            wp_redirect(locale_home_url() . '/?r=admin-homework-creator/mathematics');
            exit;
        }

        // toggle active a sheet
        if (isset($task['active'])) {
            $cid = $_REAL_POST['cid'];

            if (!empty($cid)) {
                if (MWDB::toggle_active_math_sheets($cid)) {
                    ik_enqueue_messages('Successfully active/deactive ' . count($cid) . ' sheets', 'success');

                    wp_redirect(home_url() . '/?r=admin-homework-creator/mathematics');
                    exit;
                }
            } else {
                ik_enqueue_messages('Please select a sheet.', 'error');
            }
        }

        // delete math sheet
        if (isset($task['remove'])) {
            $cid = $_REAL_POST['cid'];

            if (MWDB::delete_math_sheets($cid)) {
                wp_redirect(home_url() . '/?r=admin-homework-creator/mathematics');
                exit;
            }
        }

        $main_categories = MWDB::get_grades(array('type' => 'MATH', 'level' => 0, 'admin_only' => 1, 'orderby' => 'ordering', 'order-dir' => 'asc'));
        $levels = MWDB::get_grades(array('type' => 'MATH', 'level' => 1, 'orderby' => 'ordering', 'order-dir' => 'asc'));
        $sublevels = MWDB::get_grades(array('type' => 'MATH', 'level' => 2, 'orderby' => 'ordering', 'order-dir' => 'asc'));
        $sel_levels_html = '';
        foreach ($main_categories as $item) {
            $sel_levels_html .= '<select class="hidden" id="_l' . $item->id . '">';
            foreach ($levels as $level) {
                if ($level->parent_id == $item->id) {
                    $sel_levels_html .= '<option value="' . $level->id . '">' . $level->name . '</option>';
                }
            }
            $sel_levels_html .= '</select>';
        }

        $sel_sublevels_html = '';
        foreach ($levels as $level) {
            $sel_sublevels_html .= '<select class="hidden" id="_sl' . $level->id . '">';
            foreach ($sublevels as $sublevel) {
                if ($sublevel->parent_id == $level->id) {
                    $sel_sublevels_html .= '<option value="' . $sublevel->id . '">' . $sublevel->name . '</option>';
                }
            }
            $sel_sublevels_html .= '</select>';
        }
        
        // page content
        if ($cid) { // view a sheet
            $current_sheet = MWDB::get_math_sheet_by_id($cid);

            $data['assignment_id'] = $current_sheet->assignment_id;
            $data['homework_type_id'] = $current_sheet->homework_type_id;
            $data['sublevel_id'] = $current_sheet->grade_id;
            $data['group-name'] = $current_sheet->group_name;
            $data['sheet_name'] = $current_sheet->sheet_name;
            $data['questions'] = json_decode($current_sheet->questions, true);
            $data['description'] = $current_sheet->description;
            $data['answer_time_limit'] = $current_sheet->answer_time_limit;
            $data['show_answer_after'] = $current_sheet->show_answer_after;
            $sel_level_category = $current_sheet->category_level_id;
            $sel_level = $current_sheet->level_id;
            $data['lang'] = $current_sheet->lang;
        } else { // sheet list
            $current_page = max(1, get_query_var('page'));
            // $filter = get_page_filter_session();
            if (empty($filter) && !isset($_REAL_POST['filter'])) {
                $filter['orderby'] = 'active';
                $filter['order-dir'] = 'asc';
                $filter['items_per_page'] = 20;
                $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
            } else {
                if (isset($_REAL_POST['filter']['search'])) {
                    $filter['lang'] = $_POST['filter']['lang'];
                    $filter['group-name'] = $_REAL_POST['filter']['group-name'];
                    $filter['sheet-name'] = $_REAL_POST['filter']['sheet-name'];
                    $filter['assignment-id'] = $_REAL_POST['filter']['math-assignments'];
                    $filter['homework-types'] = $_REAL_POST['filter']['homework-types'];
                    $filter['active'] = $_REAL_POST['filter']['active'];
                    $filter['cat-level'] = $_REAL_POST['filter']['cat-level'];
                    $filter['level'] = $_REAL_POST['filter']['level'];
                    $filter['sublevel'] = $_REAL_POST['filter']['sublevel'];
                }

                if (isset($_REAL_POST['filter']['orderby'])) {
                    $filter['orderby'] = $_REAL_POST['filter']['orderby'];
                    $filter['order-dir'] = $_REAL_POST['filter']['order-dir'];
                }

                $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
            }
            
            //set_page_filter_session($filter);
            $filter['offset'] = 0;
            $filter['items_per_page'] = 99999999;
            $sheets_obj = MWDB::get_math_sheets($filter, $filter['offset'], $filter['items_per_page']);
            $avail_sheets = $sheets_obj->items;

            $total_rows = $sheets_obj->total;

            $total_pages = ceil($total_rows / $filter['items_per_page']);
            $pagination = paginate_links(array(
                'format' => '?page=%#%',
                'current' => $current_page,
                'total' => $total_pages
            ));
        }

        break; // end case mathematics
}
?>
<?php get_dict_header('Admin Homework Creator') ?>
<?php get_dict_page_title('Worksheet Manager', 'admin-page', '', $tab_options) ?>

<form method="POST" action="<?php
echo home_url() . '/?r=admin-homework-creator/' . $active_tab;
echo $layout == 'create' ? '&amp;layout=create' : ''
?><?php echo $cid ? '&amp;cid=' . $cid : '' ?>" id="main-form" enctype="multipart/form-data">

    <script>


        (function ($) {
            $(function () {
                // check When Level Category, Worksheet Format, Level, and Sublevel are Selected show Ordering
                if ($('#filter-level-categories').val() !== "" && $('#math-assignments').val() !== "" && $('#filter-levels').val() !== "" && $('#filter-sublevels').val() !== "") {
                    $('#th-ordering').removeClass("hidden");
                    $('#tb-admin-list-worksheet tbody tr td:nth-child(7)').removeClass("hidden");
                }
//                $("#filter-subject").change(function () {
//                    var level = $("#filter-subjectSelectBoxItText").text();
//                    
//                    $.ajax({
//                        url: home_url + "/?r=ajax/get_assignment",
//                        cache: false,
//                        type: 'GET',
//                        data: {data: level},
//                        success: function (data1) {
//                            console.log(data1);
//                            $('#filter-assignment').html(data1).data('selectBox-selectBoxIt').refresh();
//
//                        }
//                    });
//                });
//                $(window).on('load', function () {
//                    var level = $("#filter-subjectSelectBoxItText").text();
//                    var selected = $("#filter-assignment").attr("data-selected");
//
//                    $.ajax({
//                        url: home_url + "/?r=ajax/get_assignment",
//                        cache: false,
//                        type: 'GET',
//                        data: {data: level, selected: selected},
//                        success: function (data1) {
//
//                            $('#filter-assignment').html(data1).data('selectBox-selectBoxIt').refresh();
//
//                        }
//                    });
//                });

            });


        })(jQuery);


    </script>
    <?php
    switch ($active_tab) :

        case 'english':
            ?>

            <?php if ($layout != 'create') : ?>

                <div class="row">
                    <div class="col-sm-12">
                        <h2 class="title-border">English Worksheets</h2>
                    </div>
                    <div class="col-sm-5 col-md-4 col-sm-offset-7 col-md-offset-8">
                        <div class="form-group">
                            <a href="<?php echo home_url() ?>/?r=admin-homework-creator&amp;layout=create" class="btn btn-default orange form-control"><span class="icon-plus"></span>Create Worksheet</a>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="box box-sapphire">
                            <div class="row box-header">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="filter[group-name]" placeholder="Group Name" value="<?php echo $filter['group-name'] ?>">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <?php MWHtml::select_languages($filter['lang'], array('first_option' => '-Language-', 'name' => 'filter[lang]', 'class' => 'select-sapphire form-control')) ?>
                                </div>
                                <div class="col-sm-3">
                                    <button name="task[active]" type="submit" class="btn btn-default btn-block grey form-control">Active/Deactive</button>
                                </div>
                                <div class="col-sm-2">
                                    <button type="button" id="conf-del-btn" class="btn btn-default btn-block grey form-control">Remove</button>
                                </div>
                                <div class="col-sm-12">
                                    <div class="row search-tools">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <input type="text" id="filter-sheet-name" name="filter[sheet-name]" class="form-control" placeholder="Sheet Name" value="<?php echo $filter['sheet-name'] ?>">
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <?php
                                                MWHtml::sel_homework_types($filter['homework-types'], array('first_option' => __('-Homework Type-', 'iii-dictionary'),
                                                    'name' => 'filter[homework-types]', 'class' => 'select-sapphire form-control',
                                                    'id' => 'filter-homework-types', 'subscribed_option' => true,
                                                    'admin_panel' => true)
                                                )
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <select class="select-box-it select-sapphire form-control" name="filter[active]">
                                                    <option value="">-Status-</option>
                                                    <option value="1"<?php echo $filter['active'] == '1' ? ' selected' : '' ?>>Active</option>
                                                    <option value="0"<?php echo $filter['active'] == '0' ? ' selected' : '' ?>>Inactive</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <select class="select-box-it select-sapphire form-control" name="filter[trivia-exclusive]" >
                                                    <option value="">-Trivia Exclusive-</option>
                                                    <option value="1"<?php echo $filter['trivia-exclusive'] == '1' ? ' selected' : '' ?>>Yes</option>
                                                    <option value="0"<?php echo $filter['trivia-exclusive'] == '0' ? ' selected' : '' ?>>No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <select name="filter[grade]" class="select-box-it select-sapphire form-control" id="filter-subject" >
                                                    <option value="">-Subject-</option>
                                                    <?php foreach ($levels as $item) : ?>
                                                        <option value="<?php echo $item->id ?>"<?php echo $filter['grade'] == $item->id ? ' selected' : '' ?>><?php echo $item->name ?></option>
                                                    <?php endforeach ?>
                                                    <?php //echo MWHtml::select_grades('ENGLISH', $filter['grade'], array('class' => 'select-sapphire form-control')) ?>

                                                </select>

                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <select class="select-box-it select-sapphire form-control" id="filter-lesson" name="filter[lesson_id]" data-selected="<?php echo  $filter['lesson_id']; ?>">
                                                    <option value="">-Worksheet Format-</option>
                                                </select>
                                                <?php echo $sel_sublevels_html ?>

                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-default sky-blue form-control" name="filter[search]" id="search-btn">Search</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="">
                                        <table class="table table-striped table-condensed ik-table1 vertical-middle scroll-fix-head text-center">
                                            <thead>
                                                <tr>    
                                                    <th class="<?php
                                                    if ($check_global == 1) {
                                                        echo "css-width-4";
                                                    } else {
                                                        echo "css-width-7";
                                                    }
                                                    ?>"><input type="checkbox" class="check-all" data-name="cid[]"></th>
                                                    <th class="hidden-xs" style="width: 3% !important;    padding-left: 5%;">Lesson</th>
                                                    <th class="hidden-xs" style="width: 13% !important; padding-left: 5%;">
                                                        <a href="#" class="sortable<?php echo $filter['orderby'] == 'grade' ? ' ' . $filter['order-dir'] : '' ?>" data-sort-by="grade">Subject<span class="sorting-indicator"></span></a>
                                                    </th>
                                                    <th style="width: 16%">
                                                        <a href="#" class="sortable<?php echo $filter['orderby'] == 'sheet_name' ? ' ' . $filter['order-dir'] : '' ?>" data-sort-by="sheet_name">Worksheet<span class="sorting-indicator"></span></a>
                                                    </th>
                                                    <th class="hidden-xs <?php
                                                    if ($check_global == 1) {
                                                        echo "css-width-15";
                                                    } else {
                                                        echo "css-width-20";
                                                    }
                                                    ?>">Dictionary</th>
                                                    <th class="hidden-xs" style="padding-right: 7%;width: 14% !important;">Type</th>
                                                    <th style="width: 5% !important;" class="css-padding-destop">
                                                        <a href="#" class="sortable<?php echo $filter['orderby'] == 'ordering' ? ' ' . $filter['order-dir'] : '' ?> <?php
                                                        if ($check_global == 0) {
                                                            echo "hidden";
                                                        }
                                                        ?>" data-sort-by="ordering">Ordering<span class="sorting-indicator "></span></a>
                                                    </th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr><td colspan="7"><?php echo $pagination ?></td></tr>
                                            </tfoot>

                                            <tbody class="tbody_eng"><?php if (empty($avail_sheets)) : ?>
                                                    <tr><td colspan="7">No results</td></tr>
                                                <?php else : foreach ($avail_sheets as $sheet) : ?>
                                                        <tr<?php echo $sheet->active ? '' : ' class="text-muted"' ?> data-id="<?php echo $sheet->id ?>" data-assignment="<?php echo $sheet->assignment_id ?>">
                                                            <td><input type="checkbox" name="cid[]" value="<?php echo $sheet->id ?>"></td>
                                                            
                                                                <td class="hidden-xs" style="width: 15% !important"><?php echo $sheet->pname ?></td>
                                                            
                                                            <td class="hidden-xs <?php
                                                            if ($check_global == 1) {
                                                                echo "css-width-12";
                                                            } else {
                                                                echo "css-width-3";
                                                            }
                                                            ?>" ><?php echo $sheet->grade ?></td>
                                                            <td><?php echo $sheet->sheet_name ?></td>
                                                            <?php if (empty($sheet->name)) { ?>
                                                                <td class="hidden-xs" style="width: 12% !important"><?php echo $sheet->name ?></td>
                                                            <?php } else { ?> 
                                                                <td class="hidden-xs" style="width: 12% !important"><?php echo $sheet->name ?></td>
                                                            <?php } ?>
                                                            <td class="hidden-xs"><?php echo $sheet->homework_type ?></td>
                                                            <td style="width: 25% !important" class="<?php
                                                            if ($check_global == 0) {
                                                                echo "hidden";
                                                            }
                                                            ?>">
                                                                <button type="submit" name="order-up-english" class="btn btn-micro grey change-order" data-id="<?php echo $sheet->id ?>"><span class="icon-uparrow"></span></button>
                                                                <button type="submit" name="order-down-english" class="btn btn-micro grey change-order" data-id="<?php echo $sheet->id ?>"><span class="icon-downarrow"></span></button>
                                                                <span class="ordering"><?php echo $sheet->ordering ?></span>
                                                            </td>
                                                            <td>
                                                                <a href="<?php echo home_url() ?>/?r=admin-homework-creator&amp;layout=create&amp;cid=<?php echo $sheet->id ?>&amp;assid=<?php echo $sheet->assignment_id ?>" title="Edit this sheet" class="btn btn-default btn-block btn-tiny grey">Edit</a>
                                                                <?php if ($sheet->assignment_id != ASSIGNMENT_SPELLING && $sheet->assignment_id != ASSIGNMENT_VOCAB_BUILDER && $sheet->assignment_id != ASSIGNMENT_REPORT) : ?>
                                                                    <button type="button" class="btn btn-default btn-block btn-tiny grey preview-btn">Preview</button>
                                                                <?php endif ?>
                                                                <button type="button" class="btn btn-default btn-block btn-tiny grey worksheet-details-btn"><?php _e('Details', 'iii-dictionary') ?></button>
                                                                <div class="hidden"><?php echo $sheet->description ?></div>
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
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 col-sm-offset-3 col-md-offset-4">
                        <div class="form-group"></div>
                        <button type="submit" name="export" class="btn btn-default btn-block grey form-control">Export</button>
                    </div>
                    <div class="col-sm-5 col-md-4">
                        <div class="form-group"></div>
                        <a href="<?php echo home_url() ?>/?r=admin-homework-creator&amp;layout=create" class="btn btn-default btn-block orange form-control"><span class="icon-plus"></span>Create Worksheet</a>
                    </div>
                </div>

                <div class="modal fade modal-green" id="homework-viewer-modal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
                                <h3 class="modal-title">Homework Viewer <span><span id="homework-detail"></span> <span id="question-i">1</span></span></h3>
                            </div>
                            <div class="modal-body green">
                                <div class="row">
                                    <div class="col-sm-12" id="quiz-box">
                                        <span id="quiz"></span>
                                    </div>
                                    <div class="col-sm-12" style="display: none" id="passage-block">
                                        <div class="form-group">
                                            <label>Passage</label>
                                            <div id="reading-passage-box" class="" style="max-height: 200px;overflow: auto;">
                                                <div id="reading-passage"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <ul class="select-box multi-choice" id="question-box" data-placement="top" data-trigger="focus">
                                            <li class="vocab-keyword" id="vocab-question"></li>
                                            <li><a class="answer"><span class="box-letter">A</span> <span id="answer-a" class="ac"></span></a></li>
                                            <li><a class="answer"><span class="box-letter">B</span> <span id="answer-b" class="ac"></span></a></li>
                                            <li><a class="answer"><span class="box-letter">C</span> <span id="answer-c" class="ac"></span></a></li>
                                            <li class="hidden"><a class="answer"><span class="box-letter">D</span> <span id="answer-d" class="ac"></span></a></li>
                                            <li class="hidden"><a class="answer"><span class="box-letter">E</span> <span id="answer-e" class="ac"></span></a></li>
                                        </ul>
                                        <div class="box box-green" id="writing-subject-block" style="display: none; margin: 20px 0"><div class="" style="max-height: 250px;overflow: auto;"><div id="writing-subject"></div></div></div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <button type="button" id="next-btn" class="btn btn-default btn-block sky-blue"><span class="icon-next"></span>Next</button>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="current-row" value="1">
                                <input type="hidden" id="current-assignment" value="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade modal-red-brown" id="worksheet-details-modal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
                                <h3 class="modal-title">Worksheet details</h3>
                            </div>
                            <div class="modal-body">
                                <label>Worksheet Description</label>
                                <div class="box">
                                    <div class="" style="max-height: 350px;overflow: auto;">
                                        <div id="hw-desc"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else : ?>

                <div class="row">
                    <div class="col-md-12">
                        <h2 class="title-border"><?php echo $cid ? 'Update' : 'Create new' ?> Worksheet</h2>
                    </div>
                    <div class="col-md-5">
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label>Assignments</label>
                                    <?php $assignment_html = MWHtml::sel_assignments($data['assignment-id'], true, $data['questions']) ?>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label>Homework Types</label>
                                    <?php
                                    MWHtml::sel_homework_types($data['homework-types'], array('first_option' => 'Select one', 'subscribed_option' => true, 'admin_panel' => true)
                                    )
                                    ?>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label>Sheet Categories</label>
                                    <?php MWHtml::sel_sheet_categories($data['sheet-categories']) ?>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" value="1" name="trivia-exclusive"<?php echo $data['trivia-exclusive'] ? ' checked' : '' ?>> Trivia exclusive
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label for="grade">Grade</label>
                                    <?php MWHtml::select_grades('ENGLISH', $data['grade'], array('id' => 'grade', 'name' => 'grade', 'first_option' => 'Select Grade')) ?>
                                </div>					
                            </div>
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label for="sheet-name">Sheet name</label>
                                    <input type="text" class="form-control" id="sheet-name" name="sheet-name" value="<?php echo $data['sheet-name'] ?>">
                                </div>
                            </div>
                            <div class="col-xs-6 form-group">
                                <label>Language</label>
                                <?php MWHtml::select_languages($data['lang'], array('name' => 'lang')) ?>
                            </div>
                            <div class="col-xs-6"id="grading-price-block"<?php echo $data['assignment-id'] == ASSIGNMENT_WRITING ? '' : ' style="display: none"' ?>>
                                <div class="form-group">
                                    <label for="grading-price">Price</label>
                                    <input type="number" class="form-control" id="grading-price" name="grading-price" value="<?php echo $data['grading-price'] ?>">
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-xs-12 col-md-6">
                                <div class="form-group">
                                    <label for="imported-file">Import from a file</label>
                                    <input type="text" class="form-control" id="imported-file" name="imported-file" value="" readonly>
                                </div>					
                            </div>
                            <div class="col-xs-12 col-md-6">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <span class="btn btn-default btn-block grey btn-file">
                                        <span class="icon-browse"></span>Browse
                                        <input name="input-file" id="input-file" type="file">
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="box">
                            <section id="sheets-list">
                                <div class="row box-header">
                                    <div class="col-xs-12">
                                        <h3>Max. 20 lines per sheet</h3>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class=" col-xs-12 ">
                                        <div class="loading-overlay" style="overflow: auto;"></div>
                                        <table class="table table-striped no-padding sheet-editor" id="sheet"><?php echo $assignment_html['html'] ?></table>
                                    </div>
                                </div>
                                <div class="row box-footer">
                                    <div class="col-xs-6 col-sm-4 col-md-6">														
                                        <label class="sr-only">Select a dictionary to use</label>
                                        <?php MWHtml::select_dictionaries($data['dictionary'], false, 'dictionary', 'sel-dictionary', 'form-control') ?>														
                                    </div>
                                    <div class="col-xs-5 col-sm-4 col-md-5 col-sm-offset-3 col-md-offset-0">
                                        <button type="button" id="check-word" class="btn btn-default btn-block btn-tiny sky-blue form-control" data-loading-text="Checking...">Check words</button>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>										
                </div>

                <div class="row">
                    <?php
                    $editor_settings = array(
                        'wpautop' => false,
                        'media_buttons' => false,
                        'quicktags' => false,
                        'textarea_rows' => 7,
                        'tinymce' => array(
                            'toolbar1' => 'bold,italic,strikethrough,image,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,spellchecker,fullscreen,wp_adv'
                        )
                    );
                    ?>

                    <div class="col-sm-12" id="reading-passage-block" style="display: none">
                        <div class="form-group">
                            <label>Passage</label>
                            <?php wp_editor($data['reading_passage'], 'reading_passage', $editor_settings) ?>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Description of Homework</label>
                            <?php wp_editor($data['description'], 'description', $editor_settings) ?>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <?php if ($cid) : ?>
                                <?php if ($_GET["assid"] != 6) { ?>
                                    <button type="submit" name="task[update]" class="btn btn-default btn-block orange"><span class="icon-save"></span>Update worksheet</button>
                                <?php } else { ?>  
                                    <button type="submit" name="task[update6]" class="btn btn-default btn-block orange"><span class="icon-save"></span>Update worksheet</button>
                                <?php } ?>
                            <?php else : ?>
                                <button type="submit" name="task[create]" class="btn btn-default btn-block orange"><span class="icon-plus"></span>Create a new worksheet</button>
                            <?php endif ?>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <a href="<?php echo home_url() ?>/?r=admin-homework-creator/english" class="btn btn-default btn-block grey"><span class="icon-goback"></span>Go back</a>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="wordchecked" name="wordchecked" value="0">
                <input type="hidden" id="recheck" name="recheck" value="0">
                <input type="hidden" id="cid" name="sid" value="<?php echo $cid ?>">

                <div class="modal fade modal-red-brown modal-large" id="sheet-editor-modal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
                                <h3 class="modal-title">Editor: Question <span></span></h3>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" id="current-row-index" value="">
                                <div class="form-group">
                                    <label>Subject</label>
                                    <input type="text" id="editor-input-5" class="form-control" value="" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label>Question</label>
                                    <input type="text" id="editor-input-1i" class="form-control" value="" autocomplete="off">
                                    <textarea class="form-control" id="editor-input-1a" style="resize: vertical; height: 300px; display: none"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Correct Answer</label>
                                    <input type="text" id="editor-input-2" class="form-control" value="" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label>Incorrect Answer 1</label>
                                    <input type="text" id="editor-input-3" class="form-control" value="" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label>Incorrect Answer 2</label>
                                    <input type="text" id="editor-input-4" class="form-control" value="" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label>Incorrect Answer 3</label>
                                    <input type="text" id="editor-input-6" class="form-control" value="" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label>Incorrect Answer 4</label>
                                    <input type="text" id="editor-input-7" class="form-control" value="" autocomplete="off">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <button type="button" class="btn btn-block orange" id="editor-save-btn"><span class="icon-check"></span>Save</button>
                                    </div>
                                    <div class="col-sm-6">
                                        <button type="button" class="btn btn-block grey" data-dismiss="modal"><span class="icon-cancel"></span>Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endif ?>

            <?php
            break; // end english case
        case 'sheet-tree' :
            ?>
            <select class="select-box-it select-sapphire form-control" id="filter-sublevels" name="filter[sublevel]" data-selected="<?php echo $filter['sublevel'] ?>">
                <option value="">-Sublevel-</option>
            </select>
            <div class="col-sm-12">
                <div class="box">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="" style="max-height: 600px">
                                <table class="table table-striped table-condensed ik-table1 scroll-fix-head vertical-middle text-center">

                                    <thead>
                                        <tr>
                                            <th><?php _e('Sheet name', 'iii-dictionary') ?></th>
                                            <th class="hidden-xs"><?php _e('Grade', 'iii-dictionary') ?></th>
                                            <th class="hidden-xs"><?php _e('Assigned Date', 'iii-dictionary') ?></th>
                                            <th><?php _e('Deadline', 'iii-dictionary') ?></th>
                                            <th class="hidden-xs" title="<?php _e('Number of Students who is working on this homework', 'iii-dictionary') ?>"><?php _e('No. of Students', 'iii-dictionary') ?></th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr><td colspan="6"><?php echo $pagination ?></td></tr>
                                    </tfoot>
                                    <tbody>
                                        <?php if (empty($assignments->items)) : ?>
                                            <tr><td colspan="6"><?php _e('No Sheet  to this group yet.', 'iii-dictionary') ?></td></tr>
                                        <?php else : ?>
                                            <?php
                                            foreach ($assignments->items AS $assignment) :
                                                //format some variable 
                                                $omg_data_name = !empty($assignment->name) ? $assignment->name : $assignment->sheet_name;
                                                $omg_dead_line = ($assignment->deadline == '0000-00-00') ? 'N/A' : ik_date_format($assignment->deadline);
                                                ?>	
                                                <tr class="<?php echo!$assignment->active ? 'text-muted' : '' ?>" data-mode="<?php echo $assignment->for_practice ?>" data-rta="<?php echo $assignment->is_retryable ?>"  data-id="<?php echo $assignment->id ?>" data-name="<?php echo $omg_data_name ?>" data-deadline="<?php echo $omg_dead_line ?>">
                                                    <td><input type="checkbox" name="tid[]" value="<?php echo $assignment->id ?>"></td>
                                                    <td><?php
                                                        echo!empty($assignment->name) ? $assignment->name . '<br>' : '';
                                                        echo '<em>' . sprintf(__('Worksheet: %s', 'iii-dictionary'), $assignment->sheet_name) . '</em>';
                                                        ?></td>
                                                    <td class="hidden-xs"><?php echo $assignment->grade ?></td>
                                                    <td class="hidden-xs"><?php echo ik_date_format($assignment->created_on) ?></td>
                                                    <td><?php echo $assignment->deadline == '0000-00-00' ? 'None' : ik_date_format($assignment->deadline) ?></td>
                                                    <td class="hidden-xs"><?php echo $assignment->no_results ?></td>
                                                    <td>
                                                        <?php
                                                        // list of student's results button
                                                        if (!$assignment->for_practice) :
                                                            ?>
                                                            <a href="<?php echo locale_home_url() . '/?r=teachers-box&amp;gid=' . $gid . '&amp;hid=' . $assignment->id ?>" class="btn btn-default btn-block btn-tiny grey"><?php _e('Students Results', 'iii-dictionary') ?></a>
                                                        <?php else : ?>
                                                            <?php _e('Practice Worksheet', 'iii-dictionary') ?>
                                                        <?php endif ?>
                                                        <?php
                                                        // update button
                                                        if ($assignment->assignment_id != ASSIGNMENT_REPORT) :
                                                            ?>
                                                            <button type="button" class="btn btn-default btn-block btn-tiny grey update-homework" data-cid="<?php echo $assignment->id ?>" data-checkbox-teacher="<?php echo $assignment->teacherlastpage ?>" data-checkbox-admin="<?php echo $assignment->adminlastpage ?>" data-link="<?php echo $assignment->next_homework_id ?>"><?php _e('Update', 'iii-dictionary') ?></button>
                                                            <div class="hidden"><input class="checkboxpage"  type="checkbox" id="checkboxpage" value="<?php echo $assignment->teacherlastpage == 1 ? 1 : 0 ?>"  <?php echo $assignment->teacherlastpage == 1 ? 'checked' : '' ?> ></div>
                                                            <div class="hidden"><input class="checkboxpageadmin"  type="checkbox" id="checkboxpageadmin" value="<?php echo $assignment->adminlastpage == 1 ? 1 : 0 ?>"  <?php echo $assignment->adminlastpage == 1 ? 'checked' : '' ?> ></div>
                                                        <?php endif ?>
                                                        <?php
                                                        // remove button
                                                        //if($assignment->no_results == 0) : 
                                                        ?>
                                                        <button type="submit" name="remove-assignment" class="btn btn-default btn-block btn-tiny grey" data-cid="<?php echo $assignment->id ?>"><?php _e('Remove', 'iii-dictionary') ?></button>
                                                        <?php //endif  ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            break;
        case 'mathematics':
            ?>

            <?php if ($layout != 'create') : ?>

                <div class="row">
                    <div class="col-sm-12">
                        <h2 class="title-border">Math Worksheets</h2>
                    </div>
                    <div class="col-sm-5 col-md-4 col-sm-offset-7 col-md-offset-8">
                        <div class="form-group">
                            <a href="<?php echo home_url() ?>/?r=admin-homework-creator/mathematics&amp;layout=create" class="btn btn-default orange form-control"><span class="icon-plus"></span>Create Worksheet</a>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="box box-sapphire">
                            <div class="row box-header">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="filter[group-name]" placeholder="Group Name" value="<?php echo $filter['group-name'] ?>">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <?php MWHtml::select_languages($filter['lang'], array('first_option' => '-Language-', 'name' => 'filter[lang]', 'class' => 'select-sapphire form-control')) ?>
                                </div>
                                <div class="col-sm-3">
                                    <button name="task[active]" type="submit" class="btn btn-default btn-block grey form-control">Active/Deactive</button>
                                </div>
                                <div class="col-sm-2">
                                    <button type="button" id="conf-del-btn" class="btn btn-default btn-block grey form-control">Remove</button>
                                </div>
                                <div class="col-sm-12">
                                    <div class="row search-tools">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <input type="text" id="filter-sheet-name" name="filter[sheet-name]" class="form-control" placeholder="Sheet Name" value="<?php echo $filter['sheet-name'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <?php
                                                MWHtml::sel_homework_types($filter['homework-types'], array('first_option' => __('-Homework Type-', 'iii-dictionary'),
                                                    'name' => 'filter[homework-types]', 'class' => 'select-sapphire form-control',
                                                    'id' => 'filter-homework-types', 'subscribed_option' => true,
                                                    'admin_panel' => true)
                                                )
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <select class="select-box-it select-sapphire form-control" name="filter[active]">
                                                    <option value="">-Status-</option>
                                                    <option value="1"<?php echo $filter['active'] == '1' ? ' selected' : '' ?>>Active</option>
                                                    <option value="0"<?php echo $filter['active'] == '0' ? ' selected' : '' ?>>Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4 form-group">
                                            <select class="select-box-it select-sapphire form-control" name="filter[cat-level]" id="filter-level-categories">
                                                <option value="">-Category-</option>
                                                <?php foreach ($main_categories as $item) : ?>
                                                    <option value="<?php echo $item->id ?>"<?php echo $filter['cat-level'] == $item->id ? ' selected' : '' ?>><?php echo $item->name ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-5 form-group">
                                            <?php MWHtml::sel_math_assignments($filter['assignment-id'], array('first-option' => '-Worksheet Format-', 'name' => 'filter[math-assignments]', 'id' => 'math-assignments', 'class' => 'select-sapphire')) ?>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-default sky-blue form-control" name="filter[search]" id="search-btn">Search</button>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 form-group">
                                            <select class="select-box-it select-sapphire form-control" name="filter[level]" id="filter-levels" data-selected="<?php echo $filter['level'] ?>">
                                                <option value="">-Subject-</option>
                                            </select>
                                            <?php echo $sel_levels_html ?>
                                        </div>
                                        <div class="col-sm-6 form-group">
                                            <select class="select-box-it select-sapphire form-control" id="filter-sublevels" name="filter[sublevel]" data-selected="<?php echo $filter['sublevel'] ?>">
                                                <option value="">-Lesson-</option>
                                            </select>
                                            <?php echo $sel_sublevels_html ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-condensed ik-table1 scroll-fix-head vertical-middle text-center" id="tb-admin-list-worksheet">
                                            <thead>
                                                <tr>
                                                    <th style="width: 4% !important;"><input type="checkbox" class="check-all" data-name="cid[]"></th>
                                                    <th class="hidden-xs" style="width: 12% !important; padding-left: 4%;">Category</th>
                                                    <th class="hidden-xs" style="width: 16% !important; padding-left: 2%;">
                                                        <a href="#" class="sortable<?php echo $filter['orderby'] == 'level_name' ? ' ' . $filter['order-dir'] : '' ?>" data-sort-by="level_name">Subject<span class="sorting-indicator"></span></a>
                                                    </th>
                                                    <th class="hidden-xs" style="width: 21% !important;">
                                                        <a href="#" class="sortable<?php echo $filter['orderby'] == 'sublevel_name' ? ' ' . $filter['order-dir'] : '' ?>" data-sort-by="sublevel_name">Lesson<span class="sorting-indicator"></span></a>
                                                    </th>

                                                    <th style="width: 15% !important; padding-left: 7%;">
                                                        <a href="#" class="sortable<?php echo $filter['orderby'] == 'sheet_name' ? ' ' . $filter['order-dir'] : '' ?>" data-sort-by="sheet_name">Worksheet <span class="sorting-indicator"></span></a>
                                                    </th>
                                                    <th style="width: 23% !important;" id="th-ordering" class="css-padding-destop hidden">
                                                        <a href="#" class="sortable<?php echo $filter['orderby'] == 'ordering' ? ' ' . $filter['order-dir'] : '' ?>" data-sort-by="ordering">Ordering <span class="sorting-indicator"></span></a>
                                                    </th>
                                                    <th class="hidden-xs" style="width: 10% !important;    padding-left: 13%;">Type</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr><td colspan="7"><?php echo $pagination ?></td></tr>
                                            </tfoot>
                                            <tbody><?php if (empty($avail_sheets)) : ?>
                                                    <tr><td colspan="7">No results</td></tr>
                                                <?php else : foreach ($avail_sheets as $sheet) : ?>
                                                        <tr<?php echo $sheet->active ? '' : ' class="text-muted"' ?> data-id="<?php echo $sheet->id ?>" data-assignment="<?php echo $sheet->assignment_id ?>">
                                                            <td><input type="checkbox" name="cid[]" value="<?php echo $sheet->id ?>"></td>
                                                            <td class="hidden-xs" style="width: 15% !important"><?php echo $sheet->level_category_name ?></td>
                                                            <td style="width: 15% !important"><?php echo $sheet->level_name ?></td>
                                                            <td class="hidden-xs" style="width: 15% !important"><?php echo $sheet->sublevel_name ?></td>

                                                            <td class="hidden-xs" style="width: 27% !important"><?php echo $sheet->sheet_name ?></td>
                                                            <!-- Check The Ordering Section ONLY Appears, When Level Category, Worksheet Format, Level, and Sublevel are Selected.  -->
                                                            <!-- Using js-->        
                                                            <td style="width: 25% !important" class="hidden">
                                                                <button type="submit" name="order-up" class="btn btn-micro grey change-order" data-id="<?php echo $_POST['filter[cat-level]'] ?>"><span class="icon-uparrow"></span></button>
                                                                <button type="submit" name="order-down" class="btn btn-micro grey change-order" data-id="<?php echo $sheet->id ?>"><span class="icon-downarrow"></span></button>
                                                                <span class="ordering"><?php echo $sheet->ordering ?></span>
                                                            </td>
                                                            <td class="hidden-xs" style="width: 10% !important"><?php echo $sheet->type ?></td>
                                                            <td>
                                                                <a href="<?php echo home_url() ?>/?r=admin-homework-creator/mathematics&amp;layout=create&amp;cid=<?php echo $sheet->id ?>" class="btn btn-default btn-block btn-tiny grey">Edit</a>
                                                                <a href="<?php echo home_url() ?>/?r=admin-homework-creator/sheet-tree&cid=<?php echo $sheet->id ?>" class="btn btn-default btn-block btn-tiny grey">Assign</a>
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
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 col-sm-offset-3 col-md-offset-4">
                        <div class="form-group"></div>
                        <button type="submit" name="export" class="hidden btn btn-default btn-block grey form-control">Export</button>
                    </div>
                    <div class="col-sm-5 col-md-4">
                        <div class="form-group"></div>
                        <a href="<?php echo home_url() ?>/?r=admin-homework-creator/mathematics&amp;layout=create" class="btn btn-default btn-block orange form-control"><span class="icon-plus"></span>Create Worksheet</a>
                    </div>
                </div>

            <?php else : ?>

                <div class="row">
                    <div class="col-sm-12">
                        <h2 class="title-border"><?php echo $cid ? 'Update' : 'Create new' ?> Worksheet</h2>
                    </div>
                    <div class="col-sm-6 form-group">
                        <label>Assignments</label>
                        <?php MWHtml::sel_math_assignments($data['assignment_id']) ?>
                    </div>
                    <div class="col-sm-6 form-group">
                        <label>Level Category</label>
                        <select class="select-box-it form-control" name="level-category" id="sel-level-categories">
                            <option value="">Select one</option>
                            <?php foreach ($main_categories as $item) : ?>
                                <option value="<?php echo $item->id ?>"<?php echo $sel_level_category == $item->id ? ' selected' : '' ?>><?php echo $item->name ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="col-sm-6 form-group">
                        <label>Homework Types</label>
                        <?php
                        MWHtml::sel_homework_types($data['homework_type_id'], array('first_option' => 'Select one', 'subscribed_option' => true, 'admin_panel' => true)
                        )
                        ?>
                    </div>
                    <div class="col-sm-6 form-group">
                        <label>Level</label>
                        <select class="select-box-it form-control" name="level" id="sel-levels" data-selected="<?php echo $sel_level ?>"></select>
                        <?php echo $sel_levels_html ?>
                    </div>
                    <div class="col-sm-6 form-group">
                        <label for="sheet-name">Sheet Name</label>
                        <input type="text" class="form-control" id="sheet-name" name="sheet-name" value="<?php echo $data['sheet_name'] ?>">
                    </div>
                    <div class="col-sm-6 form-group">
                        <label>Sublevel</label>
                        <select class="select-box-it form-control" id="sel-sublevels" name="sublevel" data-selected="<?php echo $data['sublevel_id'] ?>"></select>
                        <?php echo $sel_sublevels_html ?>
                    </div>
                    <div class="col-sm-12 hidden" id="time-limit-block">
                        <div class="row form-group">
                            <div class="col-sm-6">
                                <label>Time limit (Homework mode) <small>(Seconds)</small></label>
                                <input type="number" class="form-control" name="answer-time-limit" value="<?php echo $data['answer_time_limit'] ?>">
                            </div>
                            <div class="col-sm-6">
                                <label>Time limit (Practice mode) <small>(Seconds)</small></label>
                                <input type="number" class="form-control" name="show-answer-after" value="<?php echo $data['show_answer_after'] ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-xs-6 form-group">
                                <label for="imported-file-math">Import from a file</label>
                                <input type="text" class="form-control" id="imported-file-math" name="imported-file" value="" readonly>
                            </div>
                            <div class="col-xs-6 form-group">
                                <label>&nbsp;</label>
                                <span class="btn btn-default btn-block grey btn-file">
                                    <span class="icon-browse"></span>Browse
                                    <input name="input-file" id="input-file-math" type="file">
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-6 form-group">
                        <label>Language</label>
                        <?php MWHtml::select_languages($data['lang'], array('name' => 'lang')) ?>
                    </div>
                    <div class="col-sm-6 form-group box css-box">
                        <label class="css-lable-sheet" for="sheet-name" >LINKED <span style="font-size: 14px;">Sheet Name</span></label>
                        <input type="text" class="form-control css-txt-sheet" disabled="disabled" id="linked-sheet" name="sheet-next" value="">
                    </div>
                    <div class="col-sm-12 form-group">
                        <div class="box">
                            <?php MWHtml::math_worksheet_form($data['questions']) ?>
                        </div>
                    </div>

                    <div class="col-sm-12 form-group">
                        <?php
                        $editor_settings = array(
                            'wpautop' => false,
                            'media_buttons' => false,
                            'quicktags' => false,
                            'textarea_rows' => 7,
                            'tinymce' => array(
                                'toolbar1' => 'bold,italic,strikethrough,image,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,spellchecker,fullscreen,wp_adv'
                            )
                        );
                        ?>
                        <label>Description of Homework</label>
                        <?php wp_editor($data['description'], 'description', $editor_settings) ?>
                    </div>

                    <div class="col-sm-4 form-group">
                        <?php if ($cid) : ?>
                            <button type="submit" name="task[update]" class="btn btn-default btn-block orange cache-form"></span>Update worksheet</button>
                        <?php else : ?>
                            <button type="submit" name="task[create]" class="btn btn-default btn-block orange cache-form"></span>Create a new worksheet</button>
                        <?php endif ?>
                    </div>
                    <div class="col-sm-4 form-group">
                        <button type="button" class="btn btn-default btn-block css-prev-math preview-btn-math" style="background: #00A6BC;color: #312108"><?php _e('Preview', 'iii-dictionary') ?></button>
                    </div> 
                    <div class="col-sm-4 form-group">
                        <a href="<?php echo home_url() ?>/?r=admin-homework-creator/mathematics" class="btn btn-default btn-block grey"></span>Go back</a>
                    </div>
                    <input type="hidden" name="cid" id="cid" value="<?php echo $cid ?>">
                </div>

            <?php endif ?>

            <?php
            break; // end mathematics case

    endswitch
    ?>

    <div class="modal fade modal-red-brown" id="confirm-deletion-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
                    <h3 class="modal-title" id="myModalLabel">Worksheet Deletion</h3>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-6">
                            <button type="submit" name="task[remove]" class="btn btn-block orange"><span class="icon-accept"></span>Yes</button>
                        </div>
                        <div class="col-sm-6">
                            <a href="#" data-dismiss="modal" aria-hidden="true" class="btn btn-block grey"><span class="icon-cancel"></span>No</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade modal-red-brown" id="myModal1" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
                    <h3 class="modal-title" id="myModalLabel">Description</h3>
                </div>
                <div class="modal-body">

                    <textarea id="textareaID1" style="height: 250px"class="form-control ">Stack Overflow is a question and answer site for professional and enthusiast programmers. It's built and run by you as part of the Stack Exchange network of Q&A sites. With your help, we're working together to build a library of detailed answers to every question about programming.</textarea>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-6">
                            <button type="button" class="btn btn-block grey" data-dismiss="modal">Close</button>
                        </div>
                        <div class="col-sm-6">
                            <button type="button" class="btn btn-block orange">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="oid" id="oid">
    <input type="hidden" name="filter[orderby]" id="filter-order" value="<?php echo $filter['orderby'] ?>">
    <input type="hidden" name="filter[order-dir]" id="filter-order-dir" value="<?php echo $filter['order-dir'] ?>">
</form>
<div id="list-sheet" style="display: none">
    <span>Addition Basics, Adding 1 to small number</span>
</div>
<?php echo $assignment_html['js'] ?>

<!--modal show preview math homework-->
<div class="modal fade " id="modal-homework-math">
    <div class="modal-dialog modal-lg">
        <?php
        $homework = MWDB::get_math_sheet_by_id($_GET["cid"]);

        $questions = json_decode($homework->questions, true);
        ?>
        <div class="modal-content">
            <div class="modal-header col-xs-9 col-sm-10 article-header math-homework-header">
                <span style="right: 2% !important;padding-top: 3%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h4 class="page-subtitle css-title-modal1"><?php echo $homework->level_category_name ?></h4>
                <h2 class="page-title arithmetics css-title-modal2" itemprop="headline" ><?php echo $homework->level_name . ', ' . $homework->sublevel_name ?></h2>
                <p class="math-question css-title-modal3"><?php echo $questions['question'] ?></p>
            </div>
            <div class="modal-body green" style="padding: 0px">
                <div class="col-xs-12 math-homework-body">
                    <div class="row">
                        <form id="main-form" method="post" action="<?php
                        echo locale_home_url() . '/?r=math-homework&amp;mode=' . $curr_mode;
                        echo!empty($_GET['hid']) ? '&amp;hid=' . $_GET['hid'] : '';
                        echo!empty($_GET['ref']) ? '&amp;ref=' . $_GET['ref'] : ''
                        ?>">
                            <div class="col-sm-2 homework-nav" style=" height: 420px;float: right;background: #28423A;">
                                <?php
                                switch ($homework->assignment_id) :

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
                                        ?>

                                        <h5 class="nav-title css-question-modal"><?php _e('Steps:', 'iii-dictionary') ?></h5>
                                        <div class="scroll-list-v" style="max-height: 380px;">
                                            <ul class="nav-items" id="answer-steps">
                                                <?php
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
                                                    ?><li data-n="<?php echo $no_steps[$i] ?>"<?php echo!empty($nav_li_class) ? ' class="' . implode(' ', $nav_li_class) . '"' : '' ?>><?php echo $li_count ?></li><?php
                                                    $li_count++;
                                                endfor
                                                ?>
                                            </ul>
                                        </div>

                                        <?php
                                        break; // end add, sub, mul, div assignment

                                    case MATH_ASSIGNMENT_FLASHCARD:
                                    case MATH_ASSIGNMENT_FRACTION:
                                    case MATH_ASSIGNMENT_EQUATION:
                                        ?>

                                        <h5 class="nav-title css-question-modal"><?php _e('Question:', 'iii-dictionary') ?></h5>
                                        <?php if (count($questions['q']) < 10) { ?> 
                                            <div class="" style="max-height: 380px">
                                                <ul class="nav-items" id="question-nav">
                                                    <?php
                                                    for ($i = 1; $i <= count($questions['q']); $i++) :
                                                        ?><li data-n="<?php echo $i ?>"<?php echo $homework->assignment_id == MATH_ASSIGNMENT_FLASHCARD && $homework->answer_time_limit ? ' class="not-active"' : '' ?>><?php echo $i ?></li><?php endfor ?>
                                                </ul>
                                            </div>
                                        <?php } else { ?>
                                            <div class="" style="max-height: 340px;overflow: auto">
                                                <ul class="nav-items" id="question-nav">
                                                    <?php
                                                    for ($i = 1; $i <= count($questions['q']); $i++) :
                                                        ?><li data-n="<?php echo $i ?>"<?php echo $homework->assignment_id == MATH_ASSIGNMENT_FLASHCARD && $homework->answer_time_limit ? ' class="not-active"' : '' ?>><?php echo $i ?></li><?php endfor ?>
                                                </ul>
                                            </div>
                                        <?php } ?>
                                        <?php
                                        break; // end flash card, fraction assignment

                                    case MATH_ASSIGNMENT_WORD_PROB:
                                        foreach ($questions['q'] as $key => $item) {
                                            if (empty($item['image']) || trim($item['image']) == '') {
                                                unset($questions['q'][$key]);
                                            }
                                        }
                                        ?>

                                        <h5 class="nav-title css-question-modal"><?php _e('Steps:', 'iii-dictionary') ?></h5>
                                        <?php if (count($questions['q']) > 10) { ?>
                                            <div class="" style="max-height: 340px;overflow: auto">
                                            <?php } else { ?>
                                                <div class="" style="max-height: 364px">
                                                <?php } ?>
                                                <ul class="nav-items" id="step-nav">

                                                    <?php if (($admindp == 1 && $teacherdp == 0) || ($admindp == 0 && $teacherdp == 0) || ($admindp == 0 && $teacherdp == 1)) { ?>
                                                        <?php
                                                        for ($i = 1; $i < count($questions['q']); $i++) {
                                                            if ($questions['answer'] == 'no answer' || $questions['answer'] == 'No answer' || $questions['answer'] == 'No Answer' || $questions['answer'] == 'noanswer') {
                                                                ?>
                                                                <li data-n="<?php echo $i ?>" data-ctrl="<?php echo $questions['q']['q' . $i]['param'] ?>"<?php echo!empty($nav_li_class) ? ' class="' . implode(' ', $nav_li_class) . '"' : '' ?>><?php echo $i ?></li>
                                                                <?php
                                                            } else {
                                                                if ($i == $j) {
                                                                    ?>
                                                                    <li data-n="<?php echo $i ?>" class="last-step" data-ctrl="<?php echo $questions['q']['q' . $i]['param'] ?>"<?php echo!empty($nav_li_class) ? ' class="' . implode(' ', $nav_li_class) . '"' : '' ?>><?php echo $i ?></li>
                                                                <?php } else { ?>
                                                                    <li data-n="<?php echo $i ?>" data-ctrl="<?php echo $questions['q']['q' . $i]['param'] ?>"<?php echo!empty($nav_li_class) ? ' class="' . implode(' ', $nav_li_class) . '"' : '' ?>><?php echo $i ?></li>
                                                                    <?php
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    <?php } else { ?>
                                                        <?php
                                                        $j = count($questions['q']);
                                                        for ($i = 1; $i <= count($questions['q']); $i++) {
                                                            if ($questions['answer'] == 'no answer' || $questions['answer'] == 'No answer' || $questions['answer'] == 'No Answer' || $questions['answer'] == 'noanswer') {
                                                                ?>
                                                                <li data-n="<?php echo $i ?>" data-ctrl="<?php echo $questions['q']['q' . $i]['param'] ?>"<?php echo!empty($nav_li_class) ? ' class="' . implode(' ', $nav_li_class) . '"' : '' ?>><?php echo $i ?></li>
                                                                <?php
                                                            } else {
                                                                if ($i == $j) {
                                                                    ?>
                                                                    <li data-n="<?php echo $i ?>" class="last-step" data-ctrl="<?php echo $questions['q']['q' . $i]['param'] ?>"<?php echo!empty($nav_li_class) ? ' class="' . implode(' ', $nav_li_class) . '"' : '' ?>><?php echo $i ?></li>
                                                                <?php } else { ?>
                                                                    <li data-n="<?php echo $i ?>" data-ctrl="<?php echo $questions['q']['q' . $i]['param'] ?>"<?php echo!empty($nav_li_class) ? ' class="' . implode(' ', $nav_li_class) . '"' : '' ?>><?php echo $i ?></li>
                                                                    <?php
                                                                }
                                                            }
                                                        }
                                                        ?>

                                                    <?php }
                                                    ?>
                                                </ul>
                                            </div>
                                            <?php
                                            break; // end word problem assignment

                                        case MATH_ASSIGNMENT_QUESTION_BOX:
                                            foreach ($questions['q'] as $key => $item) {
                                                if (empty($item['answer']) || trim($item['answer']) == '') {
                                                    unset($questions['q'][$key]);
                                                }
                                            }
                                            ?>

                                            <h5 class="nav-title css-question-modal"><?php _e('Steps:', 'iii-dictionary') ?></h5>
                                            <div class="scroll-list-v" style="max-height: 380px">
                                                <ul class="nav-items" id="qbox-step-nav">
                                                    <?php
                                                    for ($i = 1; $i <= count($questions['q']); $i++) :
                                                        ?><li data-n="<?php echo $i ?>"><?php echo $i ?></li><?php endfor ?>
                                                </ul>
                                            </div>

                                            <?php
                                            break; // end question box
                                    endswitch
                                    ?>

                                </div>
                                <div class="col-sm-10 homework-content math-type-<?php echo $homework->assignment_id ?>" id="homework-content">

                                    <?php
                                    switch ($homework->assignment_id) :

                                        case MATH_ASSIGNMENT_SINGLE_DIGIT:
                                            ?>
                                            <?php MWHtml::math_digit_box($questions['op1'], null, 0, MATH_ASSIGNMENT_SINGLE_DIGIT, MATH_ASSIGNMENT_SINGLE_DIGIT) ?>
                                            <?php MWHtml::math_digit_box($questions['op2'], $questions['sign'], strlen($questions['op1']) - strlen($questions['op2']), MATH_ASSIGNMENT_SINGLE_DIGIT) ?>
                                            <hr class="hr-formula hr-num-4">
                                            <?php MWHtml::math_answer_box($questions['step']['s1'], 1, 'result[s1]', MATH_ASSIGNMENT_SINGLE_DIGIT) ?>
                                            <?php MWHtml::math_answer_box($questions['step']['s2'], 2, 'result[s2]', MATH_ASSIGNMENT_SINGLE_DIGIT, $questions['sign']) ?>
                                            <hr class="hr-formula hr-num-4">
                                            <?php MWHtml::math_answer_box($questions['step']['s3'], 3, 'result[s3]', MATH_ASSIGNMENT_SINGLE_DIGIT) ?>

                                            <?php
                                            break; // end single digit add, sub and mul

                                        case MATH_ASSIGNMENT_TWO_DIGIT_MUL:
                                            ?>

                                            <?php MWHtml::math_digit_box($questions['op1'], null, 0, MATH_ASSIGNMENT_TWO_DIGIT_MUL, MATH_ASSIGNMENT_TWO_DIGIT_MUL) ?>
                                            <?php MWHtml::math_digit_box($questions['op2'], 'x', strlen($questions['op1']) - strlen($questions['op2']), MATH_ASSIGNMENT_TWO_DIGIT_MUL) ?>
                                            <hr class="hr-formula hr-num-4">
                                            <?php
                                            for ($i = 1; $i <= 4; $i++) :MATH_ASSIGNMENT_TWO_DIGIT_MUL
                                                ?>
                                                <?php MWHtml::math_answer_box($questions['step']['s' . $i], $i, 'result[s' . $i . ']', MATH_ASSIGNMENT_TWO_DIGIT_MUL) ?>
                                            <?php endfor ?>
                                            <hr class="hr-formula hr-num-5">
                                            <?php MWHtml::math_answer_box($questions['step']['s5'], 5, 'result[s5]', MATH_ASSIGNMENT_TWO_DIGIT_MUL) ?>
                                            <?php MWHtml::math_answer_box($questions['step']['s6'], 6, 'result[s6]', MATH_ASSIGNMENT_TWO_DIGIT_MUL) ?>
                                            <hr class="hr-formula hr-num-5">
                                            <?php MWHtml::math_answer_box($questions['step']['s7'], 7, 'result[s7]', MATH_ASSIGNMENT_TWO_DIGIT_MUL) ?>

                                            <?php
                                            break; // end two digit mul

                                        case MATH_ASSIGNMENT_SINGLE_DIGIT_DIV:
                                            ?>
                                            <?php
                                            $last_step = count($no_steps);
                                            MWHtml::math_answer_box($questions['step']['s' . $last_step], $last_step, 'result[s' . $last_step . ']', MATH_ASSIGNMENT_SINGLE_DIGIT_DIV)
                                            ?>
                                            <?php MWHtml::math_digit_box_division($questions['op1'], $questions['op2']) ?>
                                            <?php
                                            for ($i = 1; $i <= $last_step - 2; $i++) :
                                                ?>
                                                <?php MWHtml::math_answer_box($questions['step']['s' . $i], $i, 'result[s' . $i . ']', MATH_ASSIGNMENT_SINGLE_DIGIT_DIV) ?>
                                            <?php endfor ?>
                                            <hr class="hr-formula hr-num-2">
                                            <?php
                                            $remainder_step = $last_step - 1;
                                            MWHtml::math_answer_box($questions['step']['s' . $remainder_step], $remainder_step, 'result[s' . $remainder_step . ']', MATH_ASSIGNMENT_SINGLE_DIGIT_DIV)
                                            ?>
                                            <script>var answer_step_num = <?php echo $last_step ?>;</script>

                                            <?php
                                            break;
                                        case MATH_ASSIGNMENT_TWO_DIGIT_DIV:
                                            ?>

                                            <?php
                                            $last_step = count($no_steps);
                                            MWHtml::math_answer_box($questions['step']['s' . $last_step], $last_step, 'result[s' . $last_step . ']', MATH_ASSIGNMENT_TWO_DIGIT_DIV)
                                            ?>
                                            <?php MWHtml::math_digit_box_division($questions['op1'], $questions['op2'], MATH_ASSIGNMENT_TWO_DIGIT_DIV) ?>
                                            <?php
                                            for ($i = 1; $i <= $last_step - 2; $i++) :
                                                ?>
                                                <?php MWHtml::math_answer_box($questions['step']['s' . $i], $i, 'result[s' . $i . ']', MATH_ASSIGNMENT_TWO_DIGIT_DIV) ?>
                                            <?php endfor ?>
                                            <hr class="hr-formula hr-num-2">
                                            <?php
                                            $remainder_step = $last_step - 1;
                                            MWHtml::math_answer_box($questions['step']['s' . $remainder_step], $remainder_step, 'result[s' . $remainder_step . ']', MATH_ASSIGNMENT_TWO_DIGIT_DIV)
                                            ?>
                                            <script>var answer_step_num = <?php echo $last_step ?>;</script>

                                            <?php
                                            break; // end single and two digit division

                                        case MATH_ASSIGNMENT_FLASHCARD:
                                            ?>
                                            <p id="boxtruefalse1">Green Box = Correct</p>
                                            <p id="boxtruefalse">Red Box = Incorrect</p>
                                            <?php foreach ($questions['q'] as $key => $item) : ?>
                                                <div class="flashcard-question hidden" id="flashcard-<?php echo $key ?>">
                                                    <span class="math-number"><?php echo $item['op1'] ?></span>
                                                    <span class="math-number"><?php echo str_replace('247', '&divide;', $item['op']); ?></span>
                                                    <span class="math-number"><?php echo $item['op2'] ?></span>
                                                    <span class="math-number">=</span> 
                                                    <?php if ($homework_assignment->for_practice == 0) { ?>
                                                        <span class="math-number input-box" style=" width: auto;"><input style="min-width: 100px;width: 126px;"  onkeypress="this.style.width = ((this.value.length + 1) * 20) + 'px';" data-answer="<?php echo $item['answer'] ?>" name="result[<?php echo $key ?>]" type="text" class="answer-box" autocomplete="off"></span>
                                                    <?php } else { ?>
                                                        <span class="math-number input-box" style=" width: auto;"><input style="min-width: 100px;width: 126px;"  onkeypress="this.style.width = ((this.value.length + 1) * 20) + 'px';" data-answer="<?php echo $item['answer'] ?>" name="presult[<?php echo $key ?>]" type="text" class="answer-box" autocomplete="off"></span>
                                                    <?php } ?>
                                                    <span class="math-number"><?php echo $item['note'] ?></span>
                                                </div>
                                            <?php endforeach ?>

                                            <?php
                                            break; // end flashcard assignment

                                        case MATH_ASSIGNMENT_FRACTION:
                                            ?>
                                            <p id="boxtruefalse1">Green Box = Correct</p>
                                            <p id="boxtruefalse">Red Box = Incorrect</p>

                                            <?php foreach ($questions['q'] as $key => $item) : ?>
                                                <div class="flashcard-question" id="flashcard-<?php echo $key ?>">
                                                    <?php
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
                                                    ?>

                                                    <?php if (!empty($op1[0])) : ?>
                                                        <div class="fraction left-number">
                                                            <span class="math-number" style="width: auto;font-size: 46px; margin-top: 72px;"><?php echo $op1[0] ?></span>
                                                        </div>
                                                    <?php endif ?>
                                                    <?php if (!empty($op1[1])) : ?>
                                                        <div class="fraction">
                                                            <?php if (empty($op1[2])) { ?>
                                                                <span class="math-number " style="width: auto;font-size: 46px; margin-top: 72px;"><?php echo $op1[1] ?></span>
                                                            <?php } else { ?>
                                                                <span class="math-number " style="width: auto"><?php echo $op1[1] ?></span>
                                                                <span class="icon-fraction fraction-answer"></span>
                                                                <span class="math-number " style="width: auto"><?php echo $op1[2] ?></span>
                                                            <?php } ?>
                                                        </div>
                                                        <div class="fraction">
                                                            <span class="math-number">&nbsp;</span>
                                                            <span class="sign"><?php echo str_replace('247', '&divide;', $item['op']); ?></span>
                                                            <span class="math-number">&nbsp;</span>
                                                        </div>
                                                    <?php endif ?>
                                                    <?php if (!empty($op2[0])) : ?>
                                                        <div class="fraction left-number">
                                                            <span class="math-number" style="width: auto;font-size: 46px; margin-top: 72px;"><?php echo $op2[0] ?></span>
                                                        </div>
                                                    <?php endif ?>
                                                    <div class="fraction">
                                                        <?php if (empty($op2[2])) { ?>
                                                            <span class="math-number " style="width: auto;font-size: 46px; margin-top: 72px;"><?php echo $op1[1] ?></span>
                                                        <?php } else { ?>
                                                            <span class="math-number " style="width: auto"><?php echo $op2[1] ?></span>
                                                            <span class="icon-fraction fraction-answer"></span>
                                                            <span class="math-number " style="width: auto"><?php echo $op2[2] ?></span>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="fraction">
                                                        <span class="math-number">&nbsp;</span>
                                                        <span class="sign">=</span>
                                                        <span class="math-number">&nbsp;</span>
                                                    </div>
                                                    <?php if (!empty($answer[0])) : ?>
                                                        <div class="fraction left-number">
                                                            <?php if (!empty($answer[1]) && !empty($answer[2])) { ?>
                                                                <span class="math-number input-box fraction-answer" style="margin-top: 68px;"><input style="min-width: 100px;"  onkeypress="this.style.width = ((this.value.length + 1) * 20) + 'px';" data-answer="<?php echo $answer[0] ?>" autocomplete="off" name="result[<?php echo $key ?>][]" type="text" class="answer-box"></span>
                                                            <?php } else { ?>
                                                                <span class="math-number input-box fraction-answer" ><input style="min-width: 100px;"  onkeypress="this.style.width = ((this.value.length + 1) * 20) + 'px';" data-answer="<?php echo $answer[0] ?>" autocomplete="off" name="result[<?php echo $key ?>][]" type="text" class="answer-box"></span>
                                                            <?php } ?>
                                                        </div>
                                                    <?php endif ?>
                                                    <div class="fraction left-number">
                                                        <?php if ($answer[2] == 0) { ?>
                                                            <span class="math-number input-box fraction-answer" style="margin-top: 68px;"><input style="min-width: 100px;"  onkeypress="this.style.width = ((this.value.length + 1) * 20) + 'px';" data-answer="<?php echo $answer[1] ?>" autocomplete="off" name="result[<?php echo $key ?>][]" type="text" class="answer-box"></span>
                                                        <?php } else { ?>
                                                            <span class="math-number input-box fraction-answer"><input style="min-width: 100px;"  onkeypress="this.style.width = ((this.value.length + 1) * 20) + 'px';" data-answer="<?php echo $answer[1] ?>" autocomplete="off" name="result[<?php echo $key ?>][]" type="text" class="answer-box"></span>
                                                        <?php } ?>
                                                        <?php if (!empty($answer[2])) : ?>
                                                            <span class="icon-fraction fraction-answer" style="margin-left: 20%;"></span>
                                                            <span class="math-number input-box fraction-answer"><input style="min-width: 100px;"  onkeypress="this.style.width = ((this.value.length + 1) * 20) + 'px';" data-answer="<?php echo $answer[2] ?>" autocomplete="off" name="result[<?php echo $key ?>][]" type="text" class="answer-box"></span>
                                                        <?php endif ?>
                                                    </div>
                                                </div>
                                            <?php endforeach ?>

                                            <?php
                                            break; // end fraction assignment

                                        case MATH_ASSIGNMENT_WORD_PROB:
                                            ?>  


                                            <?php if (($admindp == 1 && $teacherdp == 0) || ($admindp == 0 && $teacherdp == 0) || ($admindp == 0 && $teacherdp == 1)) { ?>


                                                <?php
                                                for ($i = 1; $i < count($questions['q']); $i++) {
                                                    ?>
                                                    <?php
                                                    $type_audio = explode('.', $questions['q']['q' . $i]['sound']);
                                                    if ($questions['q']['q' . $i]['sound'] != '' && ($type_audio[1] == 'mp4' || $type_audio[1] == 'ogg' || $type_audio[1] == 'm4v')):
                                                        ?>
                                                                                                                                                                                                                                                                            <!-- <img class="play_button" data-id="<?php echo $key ?>" id="button-play-video-<?php echo $key ?>" data-n="button-play-video-<?php echo $key ?>" src="<?php echo get_template_directory_uri(); ?>/library/images/play_video.png" width="50">
                                                                                                                                                                                                                                                                            <img class="pause_button" data-id="<?php echo $key ?>" id="button-pause-video-<?php echo $key ?>" data-n="button-pause-video-<?php echo $key ?>" src="<?php echo get_template_directory_uri(); ?>/library/images/player_stop.png" width="50"> -->
                                                        <video class="word-prob-video" controls id="word-prob-video-<?php echo 'q' . $i ?>" style="width:100%; max-height:100%; max-width:100%">
                                                            <source src="<?php echo MWHtml::math_video_url($questions['q']['q' . $i]['sound']) ?>" type="video/mp4">
                                                            <?php _e('Your browser does not support the video tag.', 'iii-dictionary') ?>
                                                        </video>
                                                        <?php
                                                    else:
                                                        if ($questions['q']['q' . $i]['image'] != ''):
                                                            ?>
                                                            <img src="<?php echo MWHtml::math_image_url($questions['q']['q' . $i]['image']) ?>" alt="" id="word-prob-step-<?php echo 'q' . $i ?>" class="word-prob-steps canvas-layer" data-img-src="<?php echo MWHtml::math_image_url($questions['q']['q' . $i]['image']) ?>">
                                                            <?php
                                                        endif;
                                                    endif;
                                                    if ($questions['q']['q' . $i]['sound'] != '' && $type_audio[1] == 'mp3'):
                                                        ?>
                                                        <audio class="word-prob-sound" id="word-prob-sound-<?php echo 'q' . $i ?>" preload="auto" style="width: 100%;">
                                                            <source src="<?php echo MWHtml::math_sound_url($questions['q']['q' . $i]['sound']) ?>" type="audio/mpeg">
                                                        </audio>
                                                    <?php endif ?>
                                                <?php } ?>
                                            <?php }else { ?>
                                                <?php
                                                foreach ($questions['q'] as $key => $item) :
                                                    $type_audio = explode('.', $item['sound']);
                                                    if ($item['sound'] != '' && ($type_audio[1] == 'mp4' || $type_audio[1] == 'ogg' || $type_audio[1] == 'm4v')):
                                                        ?>
                                                                                                                                                            <!-- <img class="play_button" data-id="<?php echo $key ?>" id="button-play-video-<?php echo $key ?>" data-n="button-play-video-<?php echo $key ?>" src="<?php echo get_template_directory_uri(); ?>/library/images/play_video.png" width="50">
                                                                                                                                                            <img class="pause_button" data-id="<?php echo $key ?>" id="button-pause-video-<?php echo $key ?>" data-n="button-pause-video-<?php echo $key ?>" src="<?php echo get_template_directory_uri(); ?>/library/images/player_stop.png" width="50"> -->
                                                        <video class="word-prob-video" controls id="word-prob-video-<?php echo $key ?>" style="width:100%; max-height:100%; max-width:100%">
                                                            <source src="<?php echo MWHtml::math_video_url($item['sound']) ?>" type="video/mp4">
                                                            <?php _e('Your browser does not support the video tag.', 'iii-dictionary') ?>
                                                        </video>
                                                        <?php
                                                    else:
                                                        if ($item['image'] != ''):
                                                            ?>
                                                            <img src="<?php echo MWHtml::math_image_url($item['image']) ?>" alt="" id="word-prob-step-<?php echo $key ?>" class="word-prob-steps canvas-layer" data-img-src="<?php echo MWHtml::math_image_url($item['image']) ?>">
                                                            <?php
                                                        endif;
                                                    endif;
                                                    if ($item['sound'] != '' && $type_audio[1] == 'mp3'):
                                                        ?>
                                                        <audio class="word-prob-sound" id="word-prob-sound-<?php echo $key ?>" preload="auto" style="width: 100%;">
                                                            <source src="<?php echo MWHtml::math_sound_url($item['sound']) ?>" type="audio/mpeg">
                                                        </audio>
                                                    <?php endif ?>
                                                <?php endforeach ?>
                                            <?php } ?>
                                            <?php
                                            break; // end word problem assignment

                                        case MATH_ASSIGNMENT_QUESTION_BOX:
                                            ?>
                                            <p id="boxtruefalse1">Green Box = Correct</p>
                                            <p id="boxtruefalse">Red Box = Incorrect</p>
                                            <?php foreach ($questions['q'] as $key => $item) : ?>
                                                <div id="qbox-step-<?php echo $key ?>" class="question-box-block">
                                                    <img src="<?php echo MWHtml::math_image_url($item['image']) ?>" alt="" class="word-prob-steps canvas-layer" data-img-src="<?php echo MWHtml::math_image_url($item['image']) ?>" >
                                                    <span class="math-number input-box" style=" width: auto;" style="z-index: <?php echo substr($key, 1) ?>;left: <?php echo $item['x-cord'] ?>%; top: <?php echo $item['y-cord'] ?>%; width: <?php echo $item['width'] ?>%; height: <?php echo $item['height'] ?>%">
                                                        <input style="min-width: 100px;width: 126px;"onkeypress="this.style.width = ((this.value.length + 1) * 20) + 'px';" data-answer="<?php echo $item['answer'] ?>" autocomplete="off" name="result[<?php echo $key ?>]" type="text" class="answer-box"></span>
                                                </div>
                                                <?php $count_q++; ?>
                                            <?php endforeach ?>

                                            <?php
                                            break; // end question box assignment

                                        case MATH_ASSIGNMENT_EQUATION:
                                            ?>
                                            <p id="boxtruefalse1">Green Box = Correct</p>
                                            <p id="boxtruefalse">Red Box = Incorrect</p>
                                            <?php foreach ($questions['q'] as $key => $item) : ?>
                                                <div class="flashcard-question equation-question hidden" id="flashcard-<?php echo $key ?>">
                                                    <span class="math-number"><?php echo strtr($item['equation'], array('\n' => '<br>', '-' => '&#8211;')) ?></span>
                                                    <span class="math-number input-box" style=" width: auto;"><input onkeypress="this.style.width = ((this.value.length + 1) * 20) + 'px';" data-answer="<?php echo $item['answer'] ?>" name="result[<?php echo $key ?>]" type="text" style="min-width: 100px;width: 126px;" class="answer-box" autocomplete="off"></span>
                                                    <span class="math-number"><?php echo $item['note'] ?></span>
                                                </div>
                                            <?php endforeach ?>

                                            <?php
                                            break; // end equation assignment
                                    endswitch
                                    ?>

                                </div>
                                <div class="col-sm-10 homework-user-answer">
                                    <div class="row">
                                        <?php // if ($is_word_prob_assignment) :  ?>
                                        <?php if (strpos($_SERVER['REQUEST_URI'], '&ismode=0') !== false) {
                                            ?>
                                            <div class="col-xs-7">
                                                <input type="text" class="homework-input tooltip-top-left" name="result" id="input-answer" data-answer="<?php echo $questions['answer']; ?>" data-correct="<?php _e('Correct!', 'iii-dictionary') ?>" data-incorrect="<?php _e('Incorrect!', 'iii-dictionary') ?>">
                                            </div>
                                            <div class="col-xs-2">
                                                <button type="button" style="background: #FF8A00 !important;color: #fff !important" id="submit-homework" class="btn btn-default brown"><?php _e('Submit', 'iii-dictionary') ?></button>
                                            </div>
                                        <?php } else { ?>    
                                            <div class="col-xs-9">
                                                <input type="text" class="homework-input tooltip-top-left" name="result" id="input-answer" data-answer="<?php echo $questions['answer']; ?>" data-correct="<?php _e('Correct!', 'iii-dictionary') ?>" data-incorrect="<?php _e('Incorrect!', 'iii-dictionary') ?>">
                                            </div>
                                        <?php } ?>        
                                        <?php // endif ?>
                                        <div class="col-xs-3">
                                            <?php
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
                                            ?>
                                            <!--<button type="button" style="background: #FF8A00 !important;color: #fff !important" id="submit-homework" class="btn btn-default brown"><?php _e('Submit', 'iii-dictionary') ?></button>-->
                                            <?php
                                            if (isset($_GET['id_parent'])) {
                                                $id = $_GET['id_parent'];
                                            }
                                            ?>
                                            <?php if ($homework_assignment->for_practice == 1) { ?>
                                                <?php if (!empty($_GET["sat"])) { ?>
                                                    <?php
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
                                                ?>
                                                <button type="submit" name="submit-practive" id="btn-next-practive" class="btn btn-default brown btn-next-practive" data-loading-text="<?php _e('Submitting...', 'iii-dictionary') ?>" data-ref="<?php echo $link; ?>"></span><?php _e('Next Assignment', 'iii-dictionary') ?></button>
                                                <input type="hidden" name="ref-practive" id="input-ref-practive" value="<?php echo $link; ?>">
                                            <?php } else { ?>
                                                <?php if (!empty($_GET["sat"])) { ?>
                                                    <?php if (!empty($homework_assignment->next_homework_id)) { ?>
                                                        <a href="<?php echo $_next_url . '&prid=' . $id . "&ismode=" . $homework_assignment->for_practice . "&sat=" . $_GET["sat"] . "&gid=" . $_GET["gid"] ?>" class="btn btn-default brown" id="next-worksheet"><?php _e('Next Assignment', 'iii-dictionary') ?></a>
                                                    <?php } else { ?>
                                                        <a href="<?php echo home_url() . "/?r=online-learning&sat=" . $_GET["sat"] . "&gid=" . $_GET["gid"] ?>" class="btn btn-default brown" id="next-worksheet"><?php _e('Next Assignment', 'iii-dictionary') ?></a>
                                                    <?php } ?>
                                                <?php } else if (!empty($_GET["page-back"])) { ?>
                                                    <?php if (!empty($homework_assignment->next_homework_id)) { ?>
                                                        <a href="<?php echo $_next_url . '&prid=' . $id . "&ismode=" . $homework_assignment->for_practice . "&sat=" . $_GET["sat"] . "&gid=" . $_GET["gid"] . "&page-back=" . $_GET["page-back"] ?>" class="btn btn-default brown" id="next-worksheet"><?php _e('Next Assignment', 'iii-dictionary') ?></a>
                                                    <?php } else { ?>
                                                        <a href="<?php echo home_url() . "/?r=sat-preparation/" . $_GET["page-back"] . "&client=math-emathk" ?>" class="btn btn-default brown" id="next-worksheet"><?php _e('Next Assignment', 'iii-dictionary') ?></a>
                                                    <?php } ?>
                                                <?php } else if (!empty($_GET["back-ikmath"])) { ?>        
                                                    <?php if (!empty($homework_assignment->next_homework_id)) { ?>
                                                        <a href="<?php echo $_next_url . '&prid=' . $id . "&ismode=" . $homework_assignment->for_practice . '&amp;back-ikmath=' . $_GET["back-ikmath"] . "&gid=" . $_GET["gid"] ?>" class="btn btn-default brown" id="next-worksheet"><?php _e('Next Assignment', 'iii-dictionary') ?></a>
                                                    <?php } else { ?>
                                                        <a href="<?php echo home_url() . "/?r=online-learning&back-ikmath=" . $_GET["back-ikmath"] . "&gid=" . $_GET["gid"] . "&issat-math=1" ?>" class="btn btn-default brown" id="next-worksheet"><?php _e('Next Assignment', 'iii-dictionary') ?></a>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <a href="<?php echo $_next_url . '&prid=' . $id . "&ismode=" . $homework_assignment->for_practice ?>" class="btn btn-default brown" id="next-worksheet"><?php _e('Next Assignment', 'iii-dictionary') ?></a>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2 homework-controls" style="padding-left: 6px !important;">
                                    <button type="button" class="btn btn-default dark-green" id="open-notepad-btn"><i class="icon-notepad"></i><?php _e('Notepad', 'iii-dictionary') ?></button>
                                    <hr class="hr-green hidden-xs">
                                    <button type="button" class="btn btn-default dark-green" id="open-chat-btn"><i class="icon-chat"></i><?php _e('Tutoring', 'iii-dictionary') ?></button>
                                    <?php if (!empty($_GET["sat"])) { ?>
                                        <a href="<?php echo home_url() . "/?r=online-learning&sat=" . $_GET["sat"] . "&gid=" . $_GET["gid"] ?>" class="btn brown" style="background: #28423A;color: #fff;padding-top: 15px;"><i class="glyphicon glyphicon-list" style="padding-right: 10px;"></i><?php _e('Back to List', 'iii-dictionary') ?></a>
                                    <?php } else if (!empty($_GET["page-back"])) { ?>
                                        <a href="<?php echo home_url() . "/?r=sat-preparation/" . $_GET["page-back"] . "&client=math-emathk" ?>" class="btn brown" style="background: #28423A;color: #fff;padding-top: 15px;"><i class="glyphicon glyphicon-list" style="padding-right: 10px;"></i><?php _e('Back to List', 'iii-dictionary') ?></a>
                                    <?php } else if (!empty($_GET["back-ikmath"])) { ?>    
                                        <a href="<?php echo home_url() . "/?r=online-learning&back-ikmath=" . $_GET["back-ikmath"] . "&gid=" . $_GET["gid"] . "&issat-math=1" ?>" class="btn brown" style="background: #28423A;color: #fff;padding-top: 15px;"><i class="glyphicon glyphicon-list" style="padding-right: 10px;"></i><?php _e('Back to List', 'iii-dictionary') ?></a>
                                    <?php } else if (!empty($_GET["lvid"])) { ?>
                                        <a href="<?php echo home_url() . "/?r=online-learning&math&lvid=" . $_GET["lvid"] ?>" class="btn brown" style="background: #28423A;color: #fff;padding-top: 15px;"><i class="glyphicon glyphicon-list" style="padding-right: 10px;"></i><?php _e('Back to List', 'iii-dictionary') ?></a>
                                    <?php } else { ?>
                                        <a href="<?php echo home_url() . "/?r=online-learning&sat=" . $_GET["sat"] . "&gid=" . $_GET["gid"] ?>" class="btn brown" style="background: #28423A;color: #fff;padding-top: 15px;"><i class="glyphicon glyphicon-list" style="padding-right: 10px;"></i><?php _e('Back to List', 'iii-dictionary') ?></a>
                                    <?php } ?>
                                </div>


                                <div id="submit-homework-modal" class="modal fade modal-green" data-keyboard="true" aria-hidden="true" <?php echo $teacher_taking_test ? ' data-backdrop="static"' : '' ?>>
                                    <div class="modal-dialog">
                                        <div class="modal-content" style="border: 2px solid #000;">
                                            <div class="modal-header" style="background: #838383;margin: 0px;">
                                                <h3 style="padding-left: 1% !important"><?php !$teacher_taking_test ? _e('Submitting Homework', 'iii-dictionary') : _e('The End of Test', 'iii-dictionary') ?></h3>
                                            </div>
                                            <?php if (!$teacher_taking_test) : ?>
                                                <?php if (!empty($homework_assignment->next_homework_id)) : ?>
                                                    <div class="modal-body" style="background: #fff !important;color: #000">
                                                        <strong><?php _e('You have completed this homework.', 'iii-dictionary') ?></strong><br>
                                                        <hr style="border-top: 2px solid #DCDCDC">
                                                        <span>Do you want to start next worksheet?</span>
                                                    </div>
                                                    <div class="modal-footer" style="background: #fff !important">
                                                        <div class="row">
                                                            <?php if (empty($homework_assignment->next_homework_id)) : ?>
                                                                <div class="col-sm-12 form-group">
                                                                    <button type="submit" name="submit-homework-finish" style="background: #4C4C4C !important;color: #E3C264" class="btn btn-block orange submit-lesson-btn" data-loading-text="<?php _e('Submitting...', 'iii-dictionary') ?>" data-ref="<?php echo $_ref_url ?>"></span><?php _e('Yes. Start Next Worksheet', 'iii-dictionary') ?></button>
                                                                </div>
                                                            <?php else : ?>
                                                                <div class="col-sm-6 form-group">
                                                                    <button type="submit" name="submit-homework-next" id="btn-next-worksheet" style="background: #4C4C4C !important;color: #E3C264" class="btn btn-block submit-lesson-btn" data-loading-text="<?php _e('Submitting...', 'iii-dictionary') ?>" data-ref="<?php echo $_next_url . '&prid=' . $id . "&ismode=" . $homework_assignment->for_practice . "&sat=" . $_GET["sat"] . "&gid=" . $_GET["gid"] ?>"></span><?php _e('Yes. Start Next Worksheet', 'iii-dictionary') ?></button>
                                                                </div>
                                                                <div class="col-sm-6 form-group">
                                                                    <button type="button" id="close-modal-homework" style="background: #B6B6B6 !important;color: #fff;" class="btn btn-block grey submit-lesson-btn" data-loading-text="<?php _e('Submitting...', 'iii-dictionary') ?>" data-ref="<?php echo $_ref_url ?>"></span><?php _e('No. Submit and Quit', 'iii-dictionary') ?></button>
                                                                </div>
                                                            <?php endif ?>
                                                            <input type="hidden" name="ref" id="input-ref" value="<?php echo $_next_url . '&prid=' . $id . "&ismode=" . $homework_assignment->for_practice . "&sat=" . $_GET["sat"] . "&gid=" . $_GET["gid"] ?>">
                                                        </div>
                                                    </div>
                                                <?php else : ?>
                                                    <div class="modal-body" style="background: #fff !important;color: #000;">
                                                        <strong><?php _e('You have completed this homework.', 'iii-dictionary') ?></strong><br>
                                                    </div>
                                                    <div class="modal-footer" style="background: #fff !important;padding-bottom: 25px">
                                                        <div class="row">
                                                            <?php if (!empty($_GET["sat"])) { ?>
                                                                <button type="submit" name="submit-homework-next" style="background: #4C4C4C !important;color: #E3C264" class="btn btn-block orange link-finish" data-link="<?php echo home_url() . "/?r=online-learning&sat=" . $_GET["sat"] . "&gid=" . $_GET["gid"] ?>"><?php echo "Back to List"; ?></button>>
                                                                <input type="hidden" name="ref-finish" id="input-ref-finish" value="<?php echo home_url() . "/?r=online-learning&sat=" . $_GET["sat"] . "&gid=" . $_GET["gid"] ?>">
                                                            <?php } else { ?>
                                                                <button type="submit" name="submit-homework-next" style="background: #4C4C4C !important;color: #E3C264" class="btn btn-block orange link-finish" data-link="<?php echo $_next_url . '&prid=' . $id ?>"><?php echo "Back to List"; ?></button>>
                                                                <input type="hidden" name="ref-finish" id="input-ref-finish" value="<?php echo $_next_url . '&prid=' . $id ?>">
                                                            <?php } ?>
                                                            <input type="hidden" name="ref" id="input-ref" value="<?php echo $_next_url . '&prid=' . $id . "&ismode=" . $homework_assignment->for_practice . "&sat=" . $_GET["sat"] . "&gid=" . $_GET["gid"] ?>">

                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            <?php else : ?>
                                                <div class="modal-body">
                                                    <?php _e('You have completed this test.', 'iii-dictionary') ?><br>
                                                    <?php _e('If you want to leave a message to the admin, type it in the box below.', 'iii-dictionary') ?><br>
                                                    <?php _e('Click OK to submit.', 'iii-dictionary') ?>
                                                    <hr>
                                                    <div class="form-group">
                                                        <textarea  class="form-control" id="txt-feedback" placeholder="<?php _e('Leave a Message to the Teacher (Optional)', 'iii-dictionary') ?>" style="resize: none; height: 111px; border-radius: 0px;font-size: 18px;margin-bottom: 3%;margin-top: 1%"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <button type="submit" name="submit-homework" class="btn btn-block orange submit-lesson-btn" data-loading-text="<?php _e('Submitting...', 'iii-dictionary') ?>" data-ref="<?php echo $_ref_url ?>"><span class="icon-accept"></span><?php _e('OK', 'iii-dictionary') ?></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="ref" id="input-ref" value="<?php echo $_next_url . '&prid=' . $id . "&ismode=" . $homework_assignment->for_practice . "&sat=" . $_GET["sat"] . "&gid=" . $_GET["gid"] ?>"> 
                                                    <?php if ($teacher_taking_test) : ?>
                                                        <input type="hidden" name="pass" value="<?php echo $pass ?>" />
                                                    <?php endif ?>
                                                </div>
                                            <?php endif ?>
                                        </div>
                                    </div>
                                </div>
                        </form>
                    </div></div>
            </div>
        </div>
    </div>
</div>
</div>
<table id="questions-table" style="display: none"></table>

<script>
    var _CMODE = "<?php echo $curr_mode ?>";
    var _PRELOAD<?php if ($layout == 'create') : ?> = <?php echo $cid ? 0 : 1 ?><?php endif ?>;
</script>

<?php get_dict_footer() ?>
<script>
    var is_div_type = <?php echo in_array($homework->assignment_id, array(MATH_ASSIGNMENT_SINGLE_DIGIT_DIV, MATH_ASSIGNMENT_TWO_DIGIT_DIV)) ? 1 : 0 ?>;


    var <?php echo $curr_mode == 'homework' ? '_ANSWER_TIME = ' . $homework->answer_time_limit : '_SHOW_TIME = ' . $homework->show_answer_after ?>;

    (function ($) {
        $(function () {
            // check When Level Category, Worksheet Format, Level, and Sublevel are Selected show Ordering
            if ($('#filter-level-categories').val() !== "" && $('#math-assignments').val() !== "" && $('#filter-levels').val() !== "" && $('#filter-sublevels').val() !== "") {
                $('#th-ordering').removeClass("hidden");
                $('#tb-admin-list-worksheet tbody tr td:nth-child(7)').removeClass("hidden");
            }

        });


    })(jQuery);


</script>