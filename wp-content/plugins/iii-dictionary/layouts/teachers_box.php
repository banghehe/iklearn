<?php
$gid = empty($_GET['gid']) ? 0 : $_GET['gid'];
$hid = empty($_GET['hid']) ? 0 : $_GET['hid'];
$task = isset($_POST['task']) ? $_POST['task'] : '';

$is_admin = is_mw_super_admin() || is_mw_admin() ? true : false;
$current_user_id = get_current_user_id();

$is_math_panel = is_math_panel();
$_page_title = __('Subject Manager', 'iii-dictionary');

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
        'english' => array('url' => home_url() . '/?r=teachers-box/english', 'text' => 'English'),
        'mathematics' => array('url' => home_url() . '/?r=teachers-box/mathematics', 'text' => 'Mathematics')
    ),
    'active' => $active_tab
);
switch ($active_tab) {
    // english homework
    case 'english':
        if ($task == 'toggle-active') {
    $tid = $_POST['tid'];
    if (!empty($tid)) {
        foreach ($tid as $id) {
            $result = $wpdb->query(
                    $wpdb->prepare('UPDATE ' . $wpdb->prefix . 'dict_homeworks SET active = ABS(active - 1) WHERE id = %d', $id)
            );

            if (!$result) {
                break;
            }
        }

        if ($result) {
            ik_enqueue_messages('Successfully active/deactive ' . count($tid) . ' Homework.', 'success');
            wp_redirect(home_url() . '/?r=teachers-box');
            exit;
        } else {
            ik_enqueue_messages('There\'s error occurs during the operation.', 'error');
            wp_redirect(home_url() . '/?r=teachers-box');
            exit;
        }
    }
}
// export student's results to .CSV file
if (isset($_POST['export'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=worksheets_result_' . date('mdY_Hms', time()));
    $fp = fopen('php://output', 'w');

    $worksheets = MWDB::get_homework_assignments(array('group_id' => $_POST['group-id']));

    foreach ($worksheets->items as $worksheet) {
        $worksheets_result = MWDB::get_homework_results($worksheets->items[0]->id);

        // outputting
        $row_header = array('<<<<<', 'Worksheet: ' . $worksheet->sheet_name, 'Grade: ' . $worksheet->grade, '>>>>>');
        fputcsv($fp, $row_header);
        fputcsv($fp, array());
        fputcsv($fp, array('', 'Student', 'Score', 'Completed Date'));

        foreach ($worksheets_result as $key => $result) {
            $row = array($key + 1, $result->display_name, $result->score, $result->submitted_on);
            fputcsv($fp, $row);
        }
        fputcsv($fp, array());
        fputcsv($fp, array());
    }
    fclose($fp);
    exit;
}

// update a homework assignment
if (isset($_POST['update-homework'])) {
    if (!empty($_POST['homework-name'])) {
        $data['name'] = $_POST['homework-name'];
    }

    $data['deadline'] = !empty($_POST['deadline']) ? date('Y-m-d', strtotime($_POST['deadline'])) : '0000-00-00';
    $data['next_homework_id'] = $_POST['link-id'];
    $data['id'] = $_POST['_cid'];
    $data['for_practice'] = $_POST['for-practice'];
    $data['is_retryable'] = $_POST['is-retryable'];

    if (MWDB::update_homework_assignment($data)) {
        ik_enqueue_messages(__('Homework updated.', 'iii-dictionary'), 'success');
        wp_redirect(locale_home_url() . '/?r=teachers-box&gid=' . $gid);
        exit;
    } else {
        ik_enqueue_messages(__('An error occurred, cannot update homework.', 'iii-dictionary'), 'error');
    }
}

$current_page = max(1, get_query_var('page'));
//$filter = get_page_filter_session();
 
if (empty($filter) && !isset($_POST['filter'])) {
    $filter['orderby'] = 'g.name';
    $filter['order-dir'] = 'asc';
    $filter['items_per_page'] = 25;
    $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
    $filter['created_by'] = get_current_user_id();
    $filter['subscription_status'] = true;
    if ($is_admin) {
        $filter['fetch_classes'] = true;
    }
   
} else {
    $filter['created_by'] = $current_user_id;
    if (isset($_POST['filter']['search'])) {
        $filter['group-name'] = $_REAL_POST['filter']['group-name'];
        $filter['class_type'] = $_POST['filter']['class-types'];
         $filter['subscription_status'] = true;
          if ($is_admin) {
        $filter['fetch_classes'] = true;
    }
    }

    if (isset($_REAL_POST['filter']['orderby'])) {
        $filter['orderby'] = $_REAL_POST['filter']['orderby'];
        $filter['order-dir'] = $_REAL_POST['filter']['order-dir'];
    }

    $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
   
}

if (!$gid) {
    $waiting_homeworks = MWDB::get_waiting_grading_homeworks($current_user_id);
    $filter['offset'] = 0;
    $filter['items_per_page'] = 99999999;
    $groups = MWDB::get_groups($filter, $filter['offset'], $filter['items_per_page']);
    $total_pages = ceil($groups->total / $filter['items_per_page']);
      
} else {
    $filter['group_id'] = $gid;
    $filter['check_result'] = true;
    $filter['created_by'] = '';
    $filter['offset'] = 0;
    $filter['items_per_page'] = 99999999;
    $assignments = MWDB::get_homework_assignments($filter, $filter['offset'], $filter['items_per_page']);
   // var_dump($assignments);
    $total_pages = ceil($assignments->total / $filter['items_per_page']);

    if (isset($_POST['remove-assignment'])) {
        if (MWDB::remove_homework($_POST['cid']) !== false) {
            wp_redirect(locale_home_url() . '/?r=teachers-box&gid=' . $gid);
            exit;
        }
    }
}

if (isset($_POST['update-group'])) {
    $description = $_REAL_POST['description'];
    if (MWDB::update_edit_class($gid, $description)) {
        ik_enqueue_messages(__('Worksheet updated.', 'iii-dictionary'), 'success');
        wp_redirect(locale_home_url() . '/?r=teachers-box&gid=' . $gid);
        exit;
    } else {
        ik_enqueue_messages(__('An error occurred, cannot update Worksheet.', 'iii-dictionary'), 'error');
    }
}
$group_about = MWDB::get_group($gid, 'id');

//set_page_filter_session($filter);

$class_types = MWDB::get_group_class_types(false,1);

$pagination = paginate_links(array(
    'format' => '?page=%#%',
    'current' => $current_page,
    'total' => $total_pages
        ));
       

        break; // end case english
    // Math homework
    case 'mathematics':
if ($task == 'toggle-active') {
    $tid = $_POST['tid'];
    if (!empty($tid)) {
        foreach ($tid as $id) {
            $result = $wpdb->query(
                    $wpdb->prepare('UPDATE ' . $wpdb->prefix . 'dict_homeworks SET active = ABS(active - 1) WHERE id = %d', $id)
            );

            if (!$result) {
                break;
            }
        }

        if ($result) {
            ik_enqueue_messages('Successfully active/deactive ' . count($tid) . ' Homework.', 'success');
            wp_redirect(home_url() . '/?r=teachers-box/mathematics');
            exit;
        } else {
            ik_enqueue_messages('There\'s error occurs during the operation.', 'error');
            wp_redirect(home_url() . '/?r=teachers-box/mathematics');
            exit;
        }
    }
}
// export student's results to .CSV file
if (isset($_POST['export'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=worksheets_result_' . date('mdY_Hms', time()));
    $fp = fopen('php://output', 'w');

    $worksheets = MWDB::get_homework_assignments(array('group_id' => $_POST['group-id']));

    foreach ($worksheets->items as $worksheet) {
        $worksheets_result = MWDB::get_homework_results($worksheets->items[0]->id);

        // outputting
        $row_header = array('<<<<<', 'Worksheet: ' . $worksheet->sheet_name, 'Grade: ' . $worksheet->grade, '>>>>>');
        fputcsv($fp, $row_header);
        fputcsv($fp, array());
        fputcsv($fp, array('', 'Student', 'Score', 'Completed Date'));

        foreach ($worksheets_result as $key => $result) {
            $row = array($key + 1, $result->display_name, $result->score, $result->submitted_on);
            fputcsv($fp, $row);
        }
        fputcsv($fp, array());
        fputcsv($fp, array());
    }
    fclose($fp);
    exit;
}

// update a homework assignment
if (isset($_POST['update-homework'])) {
    if (!empty($_POST['homework-name'])) {
        $data['name'] = $_POST['homework-name'];
    }

    $data['deadline'] = !empty($_POST['deadline']) ? date('Y-m-d', strtotime($_POST['deadline'])) : '0000-00-00';
    $data['next_homework_id'] = $_POST['link-id'];
    $data['id'] = $_POST['_cid'];
    $data['for_practice'] = $_POST['for-practice'];
    $data['is_retryable'] = $_POST['is-retryable'];

    if (MWDB::update_homework_assignment($data)) {
        ik_enqueue_messages(__('Homework updated.', 'iii-dictionary'), 'success');
        wp_redirect(locale_home_url() . '/?r=teachers-box/mathematics&gid=' . $gid);
        exit;
    } else {
        ik_enqueue_messages(__('An error occurred, cannot update homework.', 'iii-dictionary'), 'error');
    }
}

$current_page = max(1, get_query_var('page'));
//$filter = get_page_filter_session();
if (empty($filter) && !isset($_POST['filter'])) {
    $filter['orderby'] = 'g.name';
    $filter['order-dir'] = 'asc';
    $filter['items_per_page'] = 25;
    $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
    $filter['created_by'] = get_current_user_id();
    $filter['subscription_status'] = true;
    if ($is_admin) {
        $filter['fetch_classes'] = true;
    }
} else {
    $filter['created_by'] = $current_user_id;
    if (isset($_POST['filter']['search'])) {
        $filter['group-name'] = $_REAL_POST['filter']['group-name'];
        $filter['class_type'] = $_POST['filter']['class-types'];
        $filter['subscription_status'] = true;
    if ($is_admin) {
        $filter['fetch_classes'] = true;
    }
    }

    if (isset($_REAL_POST['filter']['orderby'])) {
        $filter['orderby'] = $_REAL_POST['filter']['orderby'];
        $filter['order-dir'] = $_REAL_POST['filter']['order-dir'];
    }

    $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
}

if (!$gid) {
    $waiting_homeworks = MWDB::get_waiting_grading_homeworks($current_user_id);

    $filter['offset'] = 0;
    $filter['items_per_page'] = 99999999;
    $groups = MWDB::get_groups($filter, $filter['offset'], $filter['items_per_page']);
    $total_pages = ceil($groups->total / $filter['items_per_page']);
} else {
    $filter['group_id'] = $gid;
    $filter['check_result'] = true;
    $filter['created_by'] = '';
    $filter['offset'] = 0;
    $filter['items_per_page'] = 99999999;
    $assignments = MWDB::get_homework_assignments($filter, $filter['offset'], $filter['items_per_page']);
    $total_pages = ceil($assignments->total / $filter['items_per_page']);

    if (isset($_POST['remove-assignment'])) {
        if (MWDB::remove_homework($_POST['cid']) !== false) {
            wp_redirect(locale_home_url() . '/?r=teachers-box&gid=' . $gid);
            exit;
        }
    }
}

if (isset($_POST['update-group'])) {
    $description = $_REAL_POST['description'];
    if (MWDB::update_edit_class($gid, $description)) {
        ik_enqueue_messages(__('Worksheet updated.', 'iii-dictionary'), 'success');
        wp_redirect(locale_home_url() . '/?r=teachers-box&gid=' . $gid);
        exit;
    } else {
        ik_enqueue_messages(__('An error occurred, cannot update Worksheet.', 'iii-dictionary'), 'error');
    }
}
$group_about = MWDB::get_group($gid, 'id');

//set_page_filter_session($filter);

$class_types = MWDB::get_group_class_types(false,2);

$pagination = paginate_links(array(
    'format' => '?page=%#%',
    'current' => $current_page,
    'total' => $total_pages
        ));
      
        break; // end case mathematics
}

?>
<?php if (!$is_math_panel) : ?>
    <?php get_dict_header($_page_title) ?>
<?php else : ?>
    <?php get_math_header($_page_title, 'red-brown') ?>
<?php endif ?>

<?php get_dict_page_title($_page_title, 'admin-page', '', $tab_options) ?>

<form method="post" action="" id="main-form">
    
    <div class="row">

        <?php if (!$gid) : ?>
<?php switch ($active_tab) {
    // english homework
    case 'english':?>
            <div class="col-sm-12">
                <h2 class="title-border"><?php _e('English Subjects', 'iii-dictionary') ?></h2>
            </div>
          <?php  break;
      case 'mathematics':?>
          <div class="col-sm-12">
                <h2 class="title-border"><?php _e('Math Subjects', 'iii-dictionary') ?></h2>
            </div>
          <?php
          break;
}?>
            <div class="col-sm-12">
                <div class="box box-sapphire">
                    <div class="row box-header">
                        <div class="col-sm-5">
                            <div class="form-group">
                                <input type="text" name="filter[group-name]" class="form-control" placeholder="<?php _e('Group name', 'iii-dictionary') ?>" value="<?php echo $filter['group-name'] ?>">
                            </div>
                        </div>
                        <?php if ($is_admin) : ?>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <select name="filter[class-types]" class="select-box-it select-sapphire form-control" id="filter-class-type">
                                        <option value="">- Subject -</option>
                                        <?php  foreach ($class_types as $class_type) : ?>
                                       
                                            <option value="<?php echo $class_type->id ?>"<?php echo $filter['class_type'] == $class_type->id ? ' selected' : '' ?>><?php echo $class_type->name; ?></option>
                                            
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                        <?php endif ?>
                        <div class="col-sm-3<?php echo $is_admin ? '' : ' col-sm-offset-4' ?>">
                            <div class="form-group">
                                <button type="submit" class="btn btn-default btn-block sky-blue form-control" name="filter[search]"><?php _e('Search', 'iii-dictionary') ?></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 type_description">
                        <div class="col-sm-12 form-group" style="margin: 10px 0;">
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
                            <label>Description</label>
                            <?php wp_editor('', 'type_description', $editor_settings) ?>
                        </div>
                        <div class="col-sm-6 col-sm-offset-6" style="margin-top: 20px">
                            <div class="form-group col-md-12" style="padding-right: 0 !important;">
                                <button type="button" name="update-class-type" class="btn btn-default btn-block orange" id="btn-update-class-type">Update</button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="scroll-list2" style="height: 500px">
                                <table class="table table-striped table-condensed ik-table1 ik-table-break-all text-center">
                                    <thead>
                                        <tr>
                                            <th>
                                                <a href="#" class="sortable<?php echo $filter['orderby'] == 'g.name' ? ' ' . $filter['order-dir'] : '' ?>" data-sort-by="g.name"><?php _e('Lesson', 'iii-dictionary') ?> <span class="sorting-indicator"></span></a>
                                            </th>
                                            <th><?php _e('Password', 'iii-dictionary') ?></th>
                                            <th class="hidden-xs"><?php _e('No. of Homework', 'iii-dictionary') ?></th>
                                            <th class="hidden-xs"><?php _e('No. of Students', 'iii-dictionary') ?></th>
                                            <th class="hidden-xs"><?php _e('Subscribed Till', 'iii-dictionary') ?></th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr><td colspan="6"><?php echo $pagination ?></td></tr>
                                    </tfoot>
                                    <tbody>
                                        <?php if (empty($groups->items)) : ?>
                                            <tr><td colspan="6"><?php _e('No results', 'iii-dictionary') ?></td></tr>
                                        <?php else : ?>
                                            <?php foreach ($groups->items AS $group) : ?>														
                                                <tr>
                                                    <td><input class="gid" type="hidden" value="<?php echo $group->id ?>">
                                                        <?php echo $group->name ?>
                                                    </td>
                                                    <td><?php if ($is_admin) : ?>
                                                            <?php echo $group->password ?>
                                                        <?php else : ?>
                                                            <a href="#" class="btn btn-default btn-tiny grey view-password" id="_group-<?php echo $group->id ?>" data-group-id="<?php echo $group->id ?>" data-group-name="<?php echo $group->name ?>" data-group-pass="<?php echo $group->password ?>"><?php _e('View', 'iii-dictionary') ?></a>
                                                        <?php endif ?>
                                                    </td>
                                                    <td class="hidden-xs"><?php echo is_null($group->no_homeworks) ? 0 : $group->no_homeworks ?></td>
                                                    <td class="hidden-xs"><?php echo is_null($group->no_of_student) ? 0 : $group->no_of_student ?></td>
                                                    <td class="hidden-xs"><?php if ($is_admin) : ?>
                                                            N/A
                                                        <?php else : ?>
                                                            <?php if (is_null($group->expired_on)) : ?>
                                                                <a href="<?php echo locale_home_url() ?>/?r=manage-subscription" class="btn btn-default btn-tiny grey"><?php _e('Subscribe', 'iii-dictionary') ?></a>
                                                            <?php else : ?>
                                                                <?php echo ik_date_format($group->expired_on) ?>
                                                            <?php endif ?>
                                                        <?php endif ?>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-default btn-block btn-tiny grey view-students" data-gid="<?php echo $group->id ?>" data-loading-text="<?php _e('Loading...', 'iii-dictionary') ?>"><?php _e('Members', 'iii-dictionary') ?></button>
                                                        <a href="<?php echo locale_home_url() . '/?r=teachers-box&amp;gid=' . $group->id ?>" class="btn btn-default btn-block btn-tiny grey" title="<?php _e('List of Homeworks assigned to this Group', 'iii-dictionary') ?>"><?php _e('Assigned Homeworks', 'iii-dictionary') ?></a>
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
            <div class="col-sm-12">
                <h2 class="title-border"><?php _e('Completed Homework', 'iii-dictionary') ?></h2>
            </div>
            <div class="col-sm-12">
                <div class="box">
                    <div class="scroll-list2" style="max-height: 500px">
                        <table class="table table-striped table-condensed ik-table1 ik-table-break-all text-center">
                            <thead>
                                <tr>
                                    <th><?php _e('Group name', 'iii-dictionary') ?></th>
                                    <th><?php _e('Homework', 'iii-dictionary') ?></th>
                                    <th><?php _e('Student', 'iii-dictionary') ?></th>
                                    <th class="hidden-xs"><?php _e('Last Attempt', 'iii-dictionary') ?></th>
                                    <th class="hidden-xs"><?php _e('Comp. Date', 'iii-dictionary') ?></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($waiting_homeworks)) : ?>
                                    <tr><td colspan="6"><?php _e('No results', 'iii-dictionary') ?></td></tr>
                                <?php else : ?>
                                    <?php foreach ($waiting_homeworks AS $item) : ?>														
                                        <tr>
                                            <td><?php echo $item->group_name ?></td>
                                            <td><?php echo $item->sheet_name ?></td>
                                            <td><?php echo $item->user_name ?></td>
                                            <td class="hidden-xs"><?php echo ik_date_format($item->attempted_on) ?></td>
                                            <td class="hidden-xs"><?php echo ik_date_format($item->submitted_on) ?></td>
                                            <td>
                                                <?php if (!empty($item->report_file)) : ?>
                                                    <button type="button" class="btn btn-default btn-block btn-tiny grey download-report" data-hrid="<?php echo $item->homework_result_id ?>" data-score="0" data-url="<?php echo plugins_url('reports/' . $item->report_file, __DIR__) ?>"><?php _e('Grade', 'iii-dictionary') ?></button>
                                                <?php else : ?>
                                                    <a href="<?php echo locale_home_url() . '/?r=grade-homework&amp;hid=' . $item->hid . '&amp;sid=' . $item->userid ?>" class="btn btn-default btn-block btn-tiny grey"><?php _e('Grade', 'iii-dictionary') ?></a>
                                                <?php endif ?>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                <?php endif ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal fade modal-red-brown modal-large" id="list-members-modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
                            <h3 class="modal-title"><?php _e('List of Students', 'iii-dictionary') ?></h3>
                        </div>
                        <div class="modal-body">
                            <table class="table table-striped table-condensed ik-table1 text-center" id="list-students">
                                <thead>
                                    <tr>
                                        <th><?php _e('Student', 'iii-dictionary') ?></th>
                                        <th><?php _e('Email', 'iii-dictionary') ?></th>
                                        <th><?php _e('Joined Date', 'iii-dictionary') ?></th>
                                        <th><?php _e('Homeworks Done / In Progress', 'iii-dictionary') ?></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            <div class="text-right">
                                <button type="submit" name="export" class="btn btn-default grey form-control" style="width: 150px"><?php _e('Export', 'iii-dictionary') ?></button>
                                <input type="hidden" name="group-id" id="group-id">
                            </div>
                        </div>
                    </div>
                </div>
            </div>



        <?php else : if (!$hid) : ?>
                <div class="col-sm-12">
                    <h2 class="title-border"><?php printf(__('Lesson Name %s', 'iii-dictionary'), $assignments->items[0]->group_name) ?></h2>
                </div>
                <div class="col-sm-3" style="margin-bottom: 10px">
                    <button name="toggle-active" id="toggle-active" type="button" class="btn btn-default grey btn-tiny form-control">Active/Deactive</button>
                </div>
                <div class="col-sm-12">
                    <div class="box" id="table-homework">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="scroll-list2" style="max-height: 600px">
                                    <table class="table table-striped table-condensed ik-table1 vertical-middle text-center">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" class="check-all" data-name="tid[]"></th>
                                                <th><?php _e('Homework', 'iii-dictionary') ?></th>
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
                                                <tr><td colspan="6"><?php _e('No Homeworks assigned to this group yet.', 'iii-dictionary') ?></td></tr>
                                            <?php else : ?>
                                                <?php
                                                foreach ($assignments->items AS $assignment) :
                                                    //format some variable 
                                                    $omg_data_name = !empty($assignment->name) ? $assignment->name : $assignment->sheet_name;
                                                    $omg_dead_line = ($assignment->deadline == '0000-00-00') ? 'N/A' : ik_date_format($assignment->deadline);
                                                    ?>	
                                                    <tr class="<?php echo!$assignment->active ? 'text-muted' : '' ?>" data-mode="<?php echo $assignment->for_practice ?>" data-rta="<?php echo $assignment->is_retryable ?>" <?php if ($is_admin) : ?> data-id="<?php echo $assignment->id ?>" data-name="<?php echo $omg_data_name ?>" data-deadline="<?php echo $omg_dead_line ?>"<?php endif ?>>
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
                                                            if ($is_admin && $assignment->assignment_id != ASSIGNMENT_REPORT) :
                                                                ?>
                                                                <button type="button" class="btn btn-default btn-block btn-tiny grey update-homework" data-cid="<?php echo $assignment->id ?>" data-link="<?php echo $assignment->next_homework_id ?>"><?php _e('Update', 'iii-dictionary') ?></button>
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
                <div class="col-sm-12 form-group" style="margin: 10px 0;">
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
                    if (isset($group_about->about_class))
                        $group_description = $group_about->about_class;
                    else
                        $group_description = '';
                    ?>
                    <label>Description</label>
                    <?php wp_editor($group_description, 'description', $editor_settings) ?>
                </div>
                <div class="col-sm-6 col-sm-offset-6" style="margin-top: 20px">
                    <div class="form-group col-md-6">
                        <a href="<?php echo locale_home_url() ?>/?r=teachers-box" class="btn btn-default grey form-control" style="padding-bottom: 32px; padding-top: 8px;"><span class="icon-goback"></span><?php _e('Go back', 'iii-dictionary') ?></a>
                    </div>
                    <div class="form-group col-md-6" style="padding-right: 0 !important;">
                        <button type="submit" name="update-group" class="btn btn-default btn-block orange"><span class="icon-save"></span>Update</button>
                    </div>
                </div>

                <?php if ($is_admin) : ?>
                    <div class="modal fade modal-red-brown" id="update-homework-modal" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
                                    <h3 class="modal-title"><?php _e('Update Homework', 'iii-dictionary') ?></h3>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-sm-6 form-group">
                                            <label><?php _e('Homework Name', 'iii-dictionary') ?></label>
                                            <input type="text" class="form-control" id="homework-name" name="homework-name" value="<?php echo $homework_name ?>">
                                        </div>
                                        <div class="col-sm-6 form-group">
                                            <label for="deadline"><?php _e('Deadline', 'iii-dictionary') ?></label>
                                            <div class="row">
                                                <div class="col-xs-8">
                                                    <input type="text" class="form-control" id="deadline" name="deadline" value="<?php echo $deadline ?>" placeholder="<?php _e('No deadline', 'iii-dictionary') ?>">
                                                </div>
                                                <div class="col-xs-4">
                                                    <button type="button" id="reset-deadline" class="btn btn-default btn-block grey form-control"><?php _e('Reset', 'iii-dictionary') ?></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 form-group">
                                            <label><?php _e('Homework mode', 'iii-dictionary') ?></label>
                                            <div class="row">
                                                <div class="col-xs-6">
                                                    <div class="radio radio-style1">
                                                        <input id="for-test" type="radio" name="for-practice" value="0" checked>
                                                        <label for="for-test"><?php _e('Test', 'iii-dictionary') ?></label>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6">
                                                    <div class="radio radio-style1">
                                                        <input id="for-practice" type="radio" name="for-practice" value="1">
                                                        <label for="for-practice"><?php _e('Practice', 'iii-dictionary') ?></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6 form-group">
                                            <label><?php _e('Homework is retryable?', 'iii-dictionary') ?></label>
                                            <div class="row">
                                                <div class="col-xs-6">
                                                    <div class="radio radio-style1">
                                                        <input id="is-retryable-no" type="radio" name="is-retryable" value="0" checked>
                                                        <label for="is-retryable-no">No</label>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6">
                                                    <div class="radio radio-style1">
                                                        <input id="is-retryable-yes" type="radio" name="is-retryable" value="1">
                                                        <label for="is-retryable-yes">Yes</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 form-group">
                                            <label><?php _e('Homework Link', 'iii-dictionary') ?></label>
                                            <div class="scroll-list2 scrollbar-white" style="max-height: 400px">
                                                <table class="table table-striped table-condensed ik-table1 vertical-middle text-left" id="link-homework-tbl"><tr><td><?php _e('No results', 'iii-dictionary') ?></td></tr></table>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 form-group">
                                            <button type="submit" name="update-homework" class="btn btn-default btn-block orange"><span class="icon-check"></span> <?php _e('Update', 'iii-dictionary') ?></button>
                                        </div>
                                        <div class="col-sm-6 form-group">
                                            <a href="#" data-dismiss="modal" class="btn btn-default btn-block grey"><span class="icon-cancel"></span> <?php _e('Cancel', 'iii-dictionary') ?></a>
                                        </div>
                                        <input type="hidden" id="_cid" name="_cid">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif ?>


            <?php else : ?>

                <?php
                $hw_results = MWDB::get_homework_results($hid);
                $hw = MWDB::get_homework_assignment_by_id($hw_results[0]->homework_id);
                ?>

                <div class="col-sm-12">
                    <h2 class="title-border"><?php _e('List of Student\'s Results', 'iii-dictionary') ?></h2>
                </div>
                <div class="col-sm-12">
                    <div class="box">
                        <div class="row box-header">
                            <div class="col-sm-5">
                                <span><?php _e('Grade:', 'iii-dictionary') ?> <span style="color: #fff"><?php echo $hw->grade ?></span></span>
                            </div>
                            <div class="col-sm-7">
                                <span><?php _e('Homework name:', 'iii-dictionary') ?> <span style="color: #fff"><?php echo $hw->sheet_name ?></span></span>
                            </div>
                            <div class="col-sm-5">
                                <span><?php _e('Group:', 'iii-dictionary') ?> <span style="color: #fff"><?php echo $hw->group_name ?></span></span>
                            </div>
                            <div class="col-sm-7">
                                <span><?php _e('Due Date:', 'iii-dictionary') ?> <span style="color: #fff"><?php echo $hw->deadline == '0000-00-00' ? __('No deadline', 'iii-dictionary') : ik_date_format($hw->deadline) ?></span></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="scroll-list2" style="max-height: 600px">
                                    <table class="table table-striped table-condensed ik-table1 text-center">
                                        <thead>
                                            <tr>
                                                <th><?php _e('Student', 'iii-dictionary') ?></th>
                                                <th class="hidden-xs"><?php _e('Last Attempt', 'iii-dictionary') ?></th>
                                                <th><?php _e('Comp. Date', 'iii-dictionary') ?></th>
                                                <th class="hidden-xs"><?php _e('Score', 'iii-dictionary') ?></th>
                                                <th>%</th>
                                                <th><?php _e('Feedback', 'iii-dictionary') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($hw_results)) : ?>
                                                <tr><td colspan="6"><?php _e('No results', 'iii-dictionary') ?></td></tr>
                                            <?php else : ?>
                                                <?php foreach ($hw_results AS $result) : ?>														
                                                    <tr>
                                                        <td><?php echo $result->display_name ?></td>
                                                        <td class="hidden-xs"><?php echo $result->attempted_on == 'N/A' ? 'N/A' : ik_date_format($result->attempted_on) ?></td>
                                                        <td><?php echo in_array($result->submitted_on, array('0000-00-00', 'N/A')) === true ? 'Incomplete' : ik_date_format($result->submitted_on) ?></td>
                                                        <td><?php
                                    if ($result->assignment_id == ASSIGNMENT_WRITING) :
                                        if ($result->graded) :
                                                            ?>
                                                                    <?php _e('Graded', 'iii-dictionary') ?>
                                                                <?php else : ?>
                                                                    <?php if ($result->attempted_on != 'N/A') : ?>
                                                                        <a href="<?php echo locale_home_url() . '/?r=grade-homework&amp;hid=' . $hid . '&amp;sid=' . $result->userid ?>" class="btn btn-default btn-tiny grey"><?php _e('Grade', 'iii-dictionary') ?></a>
                                                                    <?php else : ?>
                                                                        N/A
                                                                    <?php endif ?>
                                                                <?php endif ?>
                                                                <?php
                                                            else :
                                                                if ($result->assignment_id != ASSIGNMENT_REPORT) :
                                                                    echo $result->correct_answers_count;
                                                                else :
                                                                    if (!empty($result->report_file)) :
                                                                        if ($result->graded) :
                                                                            _e('Graded', 'iii-dictionary');
                                                                        else :
                                                                            ?>
                                                                            <button type="button" class="btn btn-default btn-block btn-tiny grey download-report" data-hrid="<?php echo $result->homework_result_id ?>" data-score="<?php echo $result->score ?>" data-url="<?php echo plugins_url('reports/' . $result->report_file, __DIR__) ?>"><?php _e('Grade Report', 'iii-dictionary') ?></button>
                                                                        <?php
                                                                        endif;
                                                                    else :
                                                                        _e('No report', 'iii-dictionary');
                                                                    endif;
                                                                endif;
                                                            endif
                                                            ?></td>
                                                        <td class="hidden-xs"><?php echo $result->score ?></td>
                                                        <td>
                                                            <button type="button" tabindex="0" class="btn btn-default btn-tiny grey" title="<?php _e('Feedback from Student', 'iii-dictionary') ?>" data-toggle="popover" data-content="<?php echo nl2br($result->message) ?>" data-html="true" data-placement="bottom" data-container="body" data-trigger="focus"><?php _e('View', 'iii-dictionary') ?></button>
                                                            <div style="display: none"><?php echo $result->message ?></div>
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
                <div class="col-sm-4 col-sm-offset-8" style="margin-top: 20px">
                    <div class="form-group">
                        <a href="<?php echo locale_home_url() . '/?r=teachers-box&amp;gid=' . $gid ?>" class="btn btn-default grey form-control"><span class="icon-goback"></span><?php _e('Go back', 'iii-dictionary') ?></a>
                    </div>
                </div>

            <?php
            endif;
        endif
        ?>

    </div>
    <input type="hidden" name="cid" id="cid">
    <input type="hidden" name="filter[orderby]" id="filter-order" value="<?php echo $filter['orderby'] ?>">
    <input type="hidden" name="filter[order-dir]" id="filter-order-dir" value="<?php echo $filter['order-dir'] ?>">
    <input type="hidden" name="task" id="task" value="">
</form>

<div class="modal fade modal-red-brown" id="view-password-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" style="width: 680px;">
        <div class="modal-content">
            <div class="modal-header">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
                <h3 class="modal-title"><?php _e('View Password', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-body">
                <div class="row form-group">
                    <div class="col-md-12">
                        <span id="modal-group-name"><?php _e('Group name:', 'iii-dictionary') ?> <strong></strong></span><br>
                        <span id="modal-group-pass" style="display: none"><?php _e('Group password:', 'iii-dictionary') ?> <strong></strong></span>
                        <input type="hidden" id="modal-group-id">
                    </div>
                </div>
                <div class="row form-group" id="p-change" style="display: none">
                    <div class="col-sm-12" style="margin-bottom: 10px">
                        <?php _e('Do you want to change your group password?', 'iii-dictionary') ?>
                    </div>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" id="n_pw" placeholder="<?php _e('New password', 'iii-dictionary') ?>">
                    </div>
                    <div class="col-sm-6">
                        <button type="button" id="btn-change-pass" data-loading-text="<?php _e('Saving...', 'iii-dictionary') ?>" class="btn btn-default btn-block orange form-control"><span class="icon-save"></span><?php _e('Save', 'iii-dictionary') ?></button>
                    </div>
                </div>
                <div class="row form-group" id="p-check">
                    <div class="col-sm-12" style="margin-bottom: 10px">
                        <?php _e('Enter your account password to view this group\'s password.', 'iii-dictionary') ?>
                    </div>
                    <div class="col-sm-6">
                        <input type="password" class="form-control" id="a_pw">
                    </div>
                    <div class="col-sm-6">
                        <button type="button" id="btn-check-pass" data-loading-text="<?php _e('Checking...', 'iii-dictionary') ?>" class="btn btn-default btn-block orange form-control"><span class="icon-check"></span><?php _e('Check', 'iii-dictionary') ?></button>
                    </div>
                </div><hr>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-red-brown" id="download-report-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" style="width: 680px;">
        <div class="modal-content">
            <div class="modal-header">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
                <h3 class="modal-title"><?php _e('Grade Report', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 form-group">
                        <p><?php _e('Download student\'s Report:', 'iii-dictionary') ?> <a href="#" id="report-download-link"><?php _e('Click here', 'iii-dictionary') ?></a></p>
                    </div>
                    <div class="col-sm-6 form-group">
                        <label><?php _e('Score', 'iii-dictionary') ?></label>
                        <input type="number" id="txt-score" data-err-msg="<?php _e('Score is invalid', 'iii-dictionary') ?>" class="form-control">
                    </div>
                    <div class="col-sm-6 form-group">
                        <label>&nbsp;</label>
                        <button type="button" id="btn-grade-report" class="btn btn-default btn-block orange form-control" data-loading-text="<?php _e('Grading...', 'iii-dictionary') ?>"><?php _e('Grade', 'iii-dictionary') ?></button>
                    </div>
                </div>
                <input type="hidden" id="input-hrid">
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-red-brown" id="confirm-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" style="width: 680px;">
        <div class="modal-content">
            <div class="modal-header">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
                <h3 class="modal-title" id="myModalLabel">Confirmation</h3>
            </div>
            <div class="modal-body">		
            </div>
            <div class="modal-footer">
                <div class="row" style="padding-left: 30px; padding-right: 30px;">
                    <div class="col-md-6">
                        <a href="#" data-dismiss="modal" id="btnConfirm" class="btn btn-block orange confirm"><span class="icon-accept"></span>Yes</a>
                    </div>
                    <div class="col-md-6">
                        <a href="#" data-dismiss="modal" aria-hidden="true" class="btn btn-block grey secondary"><span class="icon-cancel"></span>No</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
ik_enqueue_js_messages('pw_inc', __('Password incorrect.', 'iii-dictionary'));
ik_enqueue_js_messages('pw_changed', __('Saved!', 'iii-dictionary'));
ik_enqueue_js_messages('pw_change_err', __('Cannot change group password.', 'iii-dictionary'));
ik_enqueue_js_messages('empty_group', __('No student has joined this group yet.', 'iii-dictionary'));
ik_enqueue_js_messages('empty_op', __('(No Link)', 'iii-dictionary'));
?>
<script>

    (function ($) {
        $("#toggle-active").click(function () {
            var check_count = $('[name="tid[]"]:checked').length;
            if (check_count == 0) {
                $("#confirm-modal .modal-body").html("You must select a Group first.");
                $("#confirm-modal .modal-footer > .row").html('<div class="col-md-12"><a href="#" data-dismiss="modal" class="btn btn-block grey"><span class="icon-cancel"></span>Back</a></div>');
            } else {
                $("#task").val("toggle-active");
                $("#confirm-modal .modal-body").html("You are about to Active/Deactive " + check_count + " Groups.<br>Do you want to process?");
                $("#confirm-modal .modal-footer > .row").html('<div class="col-md-6"><a href="#" data-dismiss="modal" id="btnConfirm" class="btn btn-block orange confirm"><span class="icon-accept"></span>Yes</a></div><div class="col-md-6"><a href="#" data-dismiss="modal" aria-hidden="true" class="btn btn-block grey secondary"><span class="icon-cancel"></span>No</a></div>');
            }
            $("#confirm-modal").modal();
        });

        $("body").on("click", "#btnConfirm", function () {
            $("#main-form").submit();
        });
        $("#filter-class-type").change(function () {
            var subid = $(this).val();
             if(subid==''){
            $(".type_description").addClass("hidden");
        }else{
            $(".type_description").removeClass("hidden");
            $.get(home_url + "/?r=ajax/description_class_type", {id: subid}, function (data) {
                $('#type_description_ifr').contents().find('#tinymce').text(data);
            });
        }

        });


        $("#btn-update-class-type").click(function () {
            var subid = $("#filter-class-typeSelectBoxItText").attr('data-val');
            var desc = $('#type_description_ifr').contents().find('#tinymce').text();
            if (subid != '') {
                $.post(home_url + "/?r=ajax/update_class_type", {id: subid, desc: desc}, function (data) {
                    if (data == 1) {
                        alert("Success");
                        $('#type_description_ifr').contents().find('#tinymce').text(desc);
                    }
                });
            }
        });
    })(jQuery);
    $(document).ready(function () {
        var subid = $("#filter-class-typeSelectBoxItText").attr('data-val');
        $.get(home_url + "/?r=ajax/description_class_type", {id: subid}, function (data) {
            $('#type_description_ifr').contents().find('#tinymce').text(data);
        });
        if(subid==''){
            $(".type_description").addClass("hidden");
        }
    });

</script>
</script>
<?php if (!$is_math_panel) : ?>
    <?php get_dict_footer() ?>
<?php else : ?>
    <?php get_math_footer() ?>
<?php endif ?>