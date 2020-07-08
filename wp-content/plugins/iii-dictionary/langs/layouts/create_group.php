<?php
$gname = $gpass = '';
$is_math_panel = is_math_panel();
$_page_title = __('Create a Group or Class', 'iii-dictionary');
$layout = isset($_GET['layout']) ? $_GET['layout'] : 'list';

$is_mw_admin = false;
if (is_mw_super_admin() || is_mw_admin()) {
    $is_mw_admin = true;
    $group = new stdClass;
    $group->group_type_id = GROUP_CLASS;
}

$current_user_id = get_current_user_id();
$a = '';

if (isset($_POST['submit']) || isset($_POST['update'])) {
    $data['id'] = $_REAL_POST['cid'];
    $data['gname'] = esc_html($_REAL_POST['group-name']);
    $data['gpass'] = esc_html($_REAL_POST['password']);

//    var_dump($_REQUEST);
//    var_dump($_REQUEST['group_detail']);
//    die;
    $gname = $data['gname'];
    $gpass = $data['gpass'];
    $gtype = $_REQUEST['group-types'];
    $ctype = $_REQUEST['class-types'];
    $ord = $_REQUEST['ordering'];
    $gprice = $_REQUEST['price'];
    $gcontent = $_REQUEST['group-content'];
    $gdetail = $_REQUEST['group_detail'];
    if (empty($data['id'])) {
        $data['created_by'] = $current_user_id;
        $data['created_on'] = date('Y-m-d', time());
        $data['active'] = 1;
    }

    if ($is_mw_admin) {
        $data['group_type_id'] = $_REAL_POST['group-types'];
        $data['class_type_id'] = $_REAL_POST['class-types'];
        $data['content'] = $_REAL_POST['group-content'];
        $data['detail'] = $_REAL_POST['group_detail'];
        $data['ordering'] = $_REAL_POST['ordering'];
        $data['price'] = !empty($_REAL_POST['price']) ? $_REAL_POST['price'] : 0;
        $data['special_group'] = isset($_REAL_POST['sat_special_group']) ? 1 : 0;
    }

    if (MWDB::store_group($data)) {
        $redirect_to = locale_home_url() . '/?r=create-group';

        if (!empty($data['id'])) {
            $redirect_to .= '&layout=create&cid=' . $data['id'];
        }

        wp_redirect($redirect_to);
        exit;
    }
}

if (isset($_POST['order-up'])) {
    MWDB::set_group_order_up($_POST['cid']);
//    echo $_POST['cid'];die;
    wp_redirect(locale_home_url() . '/?r=create-group');
    exit;
}

if (isset($_POST['order-down'])) {
    MWDB::set_group_order_down($_POST['cid']);
//    echo $_POST['cid'];die;
    wp_redirect(locale_home_url() . '/?r=create-group');
    exit;
}

if ($is_mw_admin) {
    $current_page = max(1, get_query_var('page'));
    $filter = get_page_filter_session();
    if (empty($filter) && !isset($_POST['filter'])) {
        $filter['orderby'] = 'ordering';
        $filter['order-dir'] = 'asc';
        $filter['items_per_page'] = 30;
        $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
        $filter['group_type'] = GROUP_CLASS;
        $filter['class_type'] = $_REAL_POST['filter']['class-types'];
    } else {
        if (isset($_POST['filter']['search'])) {
            $filter['group-name'] = $_REAL_POST['filter']['group-name'];
            $filter['class_type'] = $_REAL_POST['filter']['class-types'];
        }

        if (isset($_REAL_POST['filter']['orderby'])) {
            $filter['orderby'] = $_REAL_POST['filter']['orderby'];
            $filter['order-dir'] = $_REAL_POST['filter']['order-dir'];
        }

        $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
    }

    set_page_filter_session($filter);
    $group_types = MWDB::get_group_types();
    $class_types = MWDB::get_group_class_types();
    $filter['offset'] = 0;
    $filter['items_per_page'] = 99999999;
    $filter['is_admin_create_group'] = 1;
    $groups = MWDB::get_groups($filter, $filter['offset'], $filter['items_per_page']);
    $total_pages = ceil($groups->total / $filter['items_per_page']);

    $pagination = paginate_links(array(
        'format' => '?page=%#%',
        'current' => $current_page,
        'total' => $total_pages
    ));
}

if (isset($_GET['cid'])) {
    $group = MWDB::get_group($_GET['cid'], 'id');
    $gname = $group->name;
    $gpass = $group->password;
    $gspecial = $group->special_group;
}
?>
<?php if (!$is_math_panel) : ?>
    <?php get_dict_header($_page_title) ?>
<?php else : ?>
    <?php get_math_header($_page_title, 'red-brown') ?>
<?php endif ?>
<?php get_dict_page_title($_page_title, '', '', array(), get_info_tab_cloud_url('Popup_info_17.jpg')) ?>

<div class="row">
    <div class="col-md-12">
        <form method="post" action="<?php echo locale_home_url() ?>/?r=create-group<?php echo $layout == 'create' ? '&amp;layout=create' : '' ?><?php echo isset($_GET['cid']) ? '&amp;cid=' . $_GET['cid'] : '' ?>" id="main-form">
            <?php if ($is_mw_admin && $layout == 'list') : ?>
                <div class="row">
                    <div class="col-sm-12">
                        <h2 class="title-border"><?php _e('List of "Class" Groups', 'iii-dictionary') ?></h2>
                    </div>
                    <div class="col-sm-4 col-sm-offset-8">
                        <div class="form-group">
                            <a href="<?php echo locale_home_url() ?>/?r=create-group&amp;layout=create" class="btn btn-default orange form-control">
                                <span class="icon-plus"></span><?php _e('Create Group', 'iii-dictionary') ?>
                            </a>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="box">
                            <div class="row box-header">
                                <div class="col-sm-12">
                                    <div class="row search-tools">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <input type="text" name="filter[group-name]" class="form-control" placeholder="<?php _e('Group name', 'iii-dictionary') ?>" value="<?php echo $filter['group-name'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <select name="filter[class-types]" class="select-box-it form-control">
                                                    <option value="">--Class--</option>
                                                    <option value="0" <?php echo ($filter['class_type'] == '0' && !is_null($filter['class_type'])) ? ' selected' : '' ?>><?php _e('Free', 'iii-dictionary') ?></option>
                                                    <?php foreach ($class_types as $class_type) : ?>
                                                        <option value="<?php echo $class_type->id ?>"<?php echo ($filter['class_type'] == $class_type->id) ? ' selected' : '' ?>><?php echo $class_type->name ?></option>
                                                            <?php endforeach ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-default orange form-control" name="filter[search]"><?php _e('Search', 'iii-dictionary') ?></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--When lick button "List of Assignments" isset($_GET[gid])-->
                            <?php if(isset($_GET[gid])) { ?>
                            <?php $list_home = MWDB::get_homework_by_group_id_create_group($_GET["gid"])?>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="" style="max-height: 600px;overflow: auto;">
                                            <table class="table table-striped table-condensed ik-table1 text-center">
                                                <thead>
                                                    <tr>
                                                        <th class="<?php if ($check_global==1){echo "css-width-4";}else {echo "css-width-7";}?>"><input type="checkbox" class="check-all" data-name="cid[]"></th>
                                                        <th class="hidden-xs" style="width: 3% !important">Assignment</th>
                                                        <th class="hidden-xs" style="width: 13% !important">
                                                            <a href="#" class="sortable<?php echo $filter['orderby'] == 'grade' ? ' ' . $filter['order-dir'] : '' ?>" data-sort-by="grade">Grade <span class="sorting-indicator"></span></a>
                                                        </th>
                                                        <th style="width: 16%">
                                                            <a href="#" class="sortable<?php echo $filter['orderby'] == 'sheet_name' ? ' ' . $filter['order-dir'] : '' ?>" data-sort-by="sheet_name">Sheet Name <span class="sorting-indicator"></span></a>
                                                        </th>
                                                        <th class="hidden-xs <?php if ($check_global==1){echo "css-width-15";}else {echo "css-width-20";}?>">Dictionary</th>
                                                        <th class="hidden-xs" style="padding-right: 7%;width: 14% !important;">Type</th>
                                                        <th style="width: 5% !important;" class="css-padding-destop">
                                                            <a href="#" class="sortable<?php echo $filter['orderby'] == 'ordering' ? ' ' . $filter['order-dir'] : '' ?> <?php if ($check_global==0){echo "hidden";}?>" data-sort-by="ordering">Ordering<span class="sorting-indicator "></span></a>
                                                        </th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr><td colspan="5"><?php echo $pagination ?></td></tr>
                                                </tfoot>
                                                <tbody>
                                                    <?php if (empty($list_home)) : ?>
                                                        <tr><td colspan="5"><?php _e('You haven\'t created any group yet.', 'iii-dictionary') ?></td></tr>
                                                        <?php
                                                    else :
                                                        foreach ($list_home as $sheet) :
                                                            ?>
                                                            <tr<?php echo $sheet->active ? '' : ' class="text-muted"' ?> data-id="<?php echo $sheet->id ?>" data-assignment="<?php echo $sheet->assignment_id ?>">
                                                            <td><input type="checkbox" name="cid[]" value="<?php echo $sheet->id ?>"></td>
                                                            <?php if(empty($sheet->name)) { ?>
                                                                <td class="hidden-xs" style="width: 15% !important"><?php echo $sheet->assignment ?></td>
                                                            <?php } else { ?> 
                                                                <td class="hidden-xs" style="width: 15% !important"><?php echo $sheet->assignment ?></td>
                                                            <?php } ?>
                                                            <td class="hidden-xs <?php if ($check_global==1){echo "css-width-12";}else {echo "css-width-3";}?>" ><?php echo $sheet->grade ?></td>
                                                            <td><?php echo $sheet->sheet_name ?></td>
                                                            <?php if(empty($sheet->name)) { ?>
                                                                <td class="hidden-xs" style="width: 12% !important"><?php echo "Dic.Type or N/A" ?></td>
                                                            <?php } else { ?> 
                                                                <td class="hidden-xs" style="width: 12% !important"><?php echo $sheet->name ?></td>
                                                            <?php } ?>
                                                            <td class="hidden-xs"><?php echo $sheet->homework_type ?></td>
                                                            <td style="width: 25% !important" >
                                                                <button type="submit" name="order-up-english" class="btn btn-micro grey change-order" data-id="<?php echo $sheet->id ?>"><span class="icon-uparrow"></span></button>
                                                                <button type="submit" name="order-down-english" class="btn btn-micro grey change-order" data-id="<?php echo $sheet->id ?>"><span class="icon-downarrow"></span></button>
                                                                <span class="ordering"><?php echo $sheet->ordering ?></span>
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
                            <?php } else { ?>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="" style="max-height: 600px;overflow: auto;">
                                            <table class="table table-striped table-condensed ik-table1 text-center">
                                                <thead>
                                                    <tr>
                                                        <th>
                                                            <a href="#" class="sortable<?php echo $filter['orderby'] == 'g.name' ? ' ' . $filter['order-dir'] : '' ?>" data-sort-by="g.name"><?php _e('Group Name', 'iii-dictionary') ?> <span class="sorting-indicator"></span></a>
                                                        </th>
                                                        <th><?php _e('Class', 'iii-dictionary') ?></th>
                                                        <th>
                                                            <a href="#" class="sortable<?php echo $filter['orderby'] == 'ordering' ? ' ' . $filter['order-dir'] : '' ?>" data-sort-by="ordering"><?php _e('Ordering', 'iii-dictionary') ?> </a>
                                                        </th>
                                                        <th class="hidden-xs">
                                                            <a href="#" class="sortable<?php echo $filter['orderby'] == 'created_on' ? ' ' . $filter['order-dir'] : '' ?>" data-sort-by="created_on"><?php _e('Created on', 'iii-dictionary') ?> <span class="sorting-indicator"></span></a>
                                                        </th>
                                                        <th></th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr><td colspan="5"><?php echo $pagination ?></td></tr>
                                                </tfoot>
                                                <tbody>
                                                    <?php if (empty($groups->items)) : ?>
                                                        <tr><td colspan="5"><?php _e('You haven\'t created any group yet.', 'iii-dictionary') ?></td></tr>
                                                        <?php
                                                    else :
                                                        foreach ($groups->items as $item) :
                                                            ?>
                                                            <tr>
                                                                <td style="text-align: left;" class="css-padding-5"><?php echo $item->name ?></td>
                                                                <td class="css-padding-5"><?php echo!empty($item->class_name) ? $item->class_name : 'Free'; ?></td>
                                                                <td class="css-padding-5"> 
                                                                    <?php if (!empty($item->ordering)) : ?>
                                                                        <button type="submit" name="order-up" class="btn btn-micro grey order-btn" data-id="<?php echo $item->id ?>"><span class="icon-uparrow"></span></button>
                                                                        <button type="submit" name="order-down" class="btn btn-micro grey order-btn" data-id="<?php echo $item->id ?>"><span class="icon-downarrow"></span></button>
                                                                        <span class="ordering"><?php echo $item->ordering ?></span>
                                                                    <?php endif ?>
                                                                </td>
                                                                <td class="hidden-xs css-padding-5"><?php echo ik_date_format($item->created_on) ?></td>
                                                                <td class="hidden-xs css-padding-5">
                                                                    <button type="button" class="btn btn-default btn-block btn-tiny grey btn-list-assignment" data-id="<?php echo $item->id ?>"><a href="<?php home_url()?>?r=create-group&gid=<?php echo $item->id?>" style="color: #000;text-decoration: none;">List of Assignments</a></button>
                                                                </td>
                                                                <td class="css-padding-5"><a href="<?php echo locale_home_url() . '/?r=create-group&amp;layout=create&amp;cid=' . $item->id ?>" class="btn btn-default btn-block btn-tiny grey">Edit</a></td>
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
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php endif ?>
            <input type="hidden" name="gid" id="id-group" value="0">
            <input type="hidden" name="cid" value="<?php echo $_GET['cid'] ?>">
            <input type="hidden" name="filter[orderby]" id="filter-order" value="<?php echo $filter['orderby'] ?>">
            <input type="hidden" name="filter[order-dir]" id="filter-order-dir" value="<?php echo $filter['order-dir'] ?>">

            <?php if (!$is_mw_admin || $layout == 'create') : ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="title-border row">
                            <div class="col-md-6">
                                <h2><?php _e('Create New Group', 'iii-dictionary') ?></h2>
                            </div>
                            <div class="col-md-6">
                                <?php if (!$is_mw_admin) : ?>
                                    <a href="<?php echo locale_home_url() ?>/?r=manage-your-class" class="omg_link-format"><?php _e('See how to manage a classroom?', 'iii-dictionary') ?></a>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-9">
                        <div class="form-group">
                            <label for="group-name"><?php _e('Group name', 'iii-dictionary') ?></label>
                            <input type="text" class="form-control" id="group-name" name="group-name" value="<?php echo $gname ?>" required>
                        </div>     
                    </div>

                    <div class="col-md-9">
                        <div class="form-group">
                            <label for="password"><?php _e('Group password', 'iii-dictionary') ?></label>
                            <input type="text" class="form-control" id="password" name="password" value="<?php echo $gpass ?>" required>
                        </div>     
                    </div>

                    <?php if ($is_mw_admin) : ?>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><?php _e('Group Type', 'iii-dictionary') ?></label>
                                        <select class="select-box-it form-control" name="group-types" id="group-types">
                                            <?php foreach ($group_types as $group_type) : ?>
                                                <option value="<?php echo $group_type->id ?>"<?php echo $group->group_type_id == $group_type->id ? ' selected' : '' ?>><?php echo $group_type->name ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 class-free-block">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div class="radio radio-style1">
                                            <input id="sat_special_group" type="radio" name="sat_special_group" <?php echo $gspecial == 1 ? 'checked' : '' ?>  />
                                            <label for="sat_special_group"><?php _e('Special Group', 'iii-dictionary') ?></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 class-group-block">
                                    <div class="form-group">
                                        <label><?php _e('Class Name', 'iii-dictionary') ?></label>
                                        <select class="select-box-it form-control" name="class-types" id="class-types">
                                            <?php foreach ($class_types as $class_type) : ?>
                                                <option value="<?php echo $class_type->id ?>"<?php 
                                                
                                                if($ctype==''&&$ctype==null){
                                                echo $group->class_type_id == $class_type->id ? ' selected' : '' ;
                                                }else{
                                                 echo   $ctype==$class_type->id ? ' selected' : '' ;
                                                }
                                                        
                                                        ?>><?php echo $class_type->name ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 class-group-block">
                                    <div class="form-group">
                                        <label><?php _e('Ordering', 'iii-dictionary') ?></label>
                                        <input type="number" name="ordering" class="form-control" min="0" value="<?php echo ($ord == null && $ord == '') ? $group->ordering : $ord ?>">
                                    </div>
                                </div>

                                <div class="col-md-3 class-group-block">
                                    <div class="form-group">
                                        <label><?php _e('Price', 'iii-dictionary') ?></label>
                                        <input id="group_price" type="number" name="price" class="form-control" min="0" value="<?php echo $group->price ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 class-group-block">
                            <div class="form-group">
                                <label for="group-content"><?php _e('Content', 'iii-dictionary') ?></label>
                                <input type="text" name="group-content" id="group-content" class="form-control" value="<?php echo ($gcontent == '' && $gcontent == null) ? $group->content : $gcontent ?>">
                            </div>
                        </div>
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
                        <div class="col-sm-12 class-group-block">
                            <div class="form-group">
                                <label for="group-details"><?php _e('Detail', 'iii-dictionary') ?></label>
                                <?php wp_editor(($gdetail == '' && $gdetail == null) ? $group->detail : $gdetail, 'group_detail', $editor_settings); ?>
                            </div>
                        </div>

                    <?php endif ?>

                    <div class="col-sm-9">
                        <?php if (empty($_GET['cid'])) : ?>
                            <div class="form-group">
                                <button type="submit" name="submit" class="btn btn-default btn-block orange"><span class="icon-user-plus"></span><?php _e('Create a new group', 'iii-dictionary') ?></button>
                            </div>
                        <?php else : ?>
                            <div class="form-group">
                                <button type="submit" name="update" class="btn btn-default btn-block orange"><span class="icon-user-plus"></span><?php _e('Update group', 'iii-dictionary') ?></button>
                            </div>
                        <?php endif ?>
                        <?php if ($is_mw_admin) : ?>
                            <div class="form-group">
                                <a href="<?php echo locale_home_url() ?>/?r=create-group" class="btn btn-default btn-block grey"><span class="icon-goback"></span><?php _e('Go back', 'iii-dictionary') ?></a>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            <?php endif ?>

            <?php if (!$is_mw_admin) : ?>
                <div class="row">
                    <div class="col-md-9 group-info">
                        <div class="box">
                            1. <?php _e('Create a name for your group (class).', 'iii-dictionary') ?><br>
                            2. <?php _e('Give the group name and password to your students to join the class.', 'iii-dictionary') ?><br>
                            <?php //_e('Send homework online to your class.', 'iii-dictionary')    ?>
                            3. <?php _e('Go to Homework Assignment under "Teacher" and select a worksheet and send it to the group as the homework assignment.', 'iii-dictionary') ?><br>
                            4. <?php _e('Homework completed by students is auto-graded.', 'iii-dictionary') ?><br>
                            5. <?php _e('View the homework results at the', 'iii-dictionary') ?> <a href="<?php echo locale_home_url() ?>/?r=teachers_box"><?php _e('Teacher\'s Box', 'iii-dictionary') ?></a>.
                        </div>
                    </div>
                </div>
            <?php endif ?>
        </form>
    </div>
</div>

<script>
    (function ($) {
        $(function () {
            var group_price = $('#group_price').parents('div.class-group-block');
            var select_box = $('#class-types');
            if (select_box.val() <= 20) {
                group_price.slideUp();
            }

            if ($("#group-types").val() == "1") {
                $(".class-group-block").hide();
            } else {
                $(".class-free-block").hide();
            }

            $("#group-types").change(function () {
                if ($(this).val() == "1") {
                    $(".class-free-block").slideDown();
                    $(".class-group-block").slideUp();
                } else {
                    $(".class-free-block").hide();
                    $(".class-group-block").slideDown();
                    if (select_box.find('option:selected').val() <= 20) {
                        group_price.hide();
                    }
                }
            });

            select_box.change(function () {
                if ($(this).val() > 20) {
                    group_price.slideDown();
                } else {
                    group_price.slideUp();
                    $('#group_price').val('');
                }
            });
            $('.btn-list-assignment').click(function(){
                $('#id-group').val($(this).attr("data-id"));
            });   
        });
    })(jQuery);
</script>
<?php if (!$is_math_panel) : ?>
    <?php get_dict_footer() ?>
<?php else : ?>
    <?php get_math_footer() ?>
<?php endif ?>