<?php
$task = isset($_POST['task']) ? $_POST['task'] : '';

if ($task == 'toggle-active') {
    $cid = $_POST['cid'];
    if (!empty($cid)) {
        foreach ($cid as $id) {
            $result = $wpdb->query(
                    $wpdb->prepare('UPDATE ' . $wpdb->prefix . 'dict_groups SET active = ABS(active - 1) WHERE id = %d', $id)
            );

            if (!$result) {
                break;
            }
        }

        if ($result) {
            ik_enqueue_messages('Successfully active/deactive ' . count($cid) . ' Groups.', 'success');
            wp_redirect(home_url() . '/?r=check-groups');
            exit;
        } else {
            ik_enqueue_messages('There\'s error occurs during the operation.', 'error');
            wp_redirect(home_url() . '/?r=check-groups');
            exit;
        }
    }
}

// page content
$current_page = max(1, get_query_var('page'));
$filter = get_page_filter_session();
if (empty($filter)) {
    $filter['orderby'] = 'created_on';
    $filter['order-dir'] = 'desc';
    $filter['items_per_page'] = 15;
    $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
    $filter['subscription_status'] = true;
} else {
    if (isset($_POST['filter']['search'])) {
        $filter['role'] = $_POST['filter']['role'];
        $filter['group-name'] = $_REAL_POST['filter']['group-name'];
        $filter['owner-name'] = $_REAL_POST['filter']['owner-name'];
        $filter['state'] = $_POST['filter']['state'];
        $filter['group_type'] = $_POST['filter']['group_type'];
    }

    if (isset($_REAL_POST['filter']['orderby'])) {
        $filter['orderby'] = $_REAL_POST['filter']['orderby'];
        $filter['order-dir'] = $_REAL_POST['filter']['order-dir'];
    }

    $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
}

set_page_filter_session($filter);
$filter['offset'] = 0;
$filter['items_per_page'] = 99999999;
$groups = MWDB::get_groups($filter, $filter['offset'], $filter['items_per_page']);
$total_pages = ceil($groups->total / $filter['items_per_page']);

$pagination = paginate_links(array(
    'format' => '?page=%#%',
    'current' => $current_page,
    'total' => $total_pages
        ));

if (isset($_GET['g'])) {
    $f_g = esc_html($_GET['g']);
    $current_group = MWDB::get_group($f_g);
    $homeworks = MWDB::get_homeworks_by('group_id', $current_group->id);
    $students = MWDB::get_group_students($current_group->id);
}
?>
<?php get_dict_header('Check Groups\'s List') ?>
<?php get_dict_page_title('Check Groups\'s List', 'admin-page') ?>

<form action="<?php echo home_url() ?>/?r=check-groups" method="post" id="main-form">
    <div class="row">
        <div class="col-xs-12 box box-sapphire">
            <div class="row box-header">
                <div class="col-xs-9">
                    <h3>Groups's List</h3>
                </div>
                <div class="col-xs-3">
                    <button name="toggle-active" id="toggle-active" type="button" class="btn btn-default grey btn-tiny form-control">Active/Deactive</button>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="row search-tools">
                            <div class="col-sm-3">
                                <input type="text" name="filter[group-name]" class="form-control" placeholder="Group name" value="<?php echo $filter['group-name'] ?>">
                            </div>
                            <div class="col-sm-3">
                                <input type="text" name="filter[owner-name]" class="form-control" placeholder="Owner name" value="<?php echo $filter['owner-name'] ?>">
                            </div>
                            <div class="col-sm-2">
                                <select name="filter[group_type]" class="select-box-it select-sapphire form-control">
                                    <option value="">--Type--</option>
                                    <option value="<?php echo GROUP_FREE ?>"<?php echo $filter['group_type'] == GROUP_FREE ? ' selected' : '' ?>>Free</option>
                                    <option value="<?php echo GROUP_SUBSCRIBED ?>"<?php echo $filter['group_type'] == GROUP_SUBSCRIBED ? ' selected' : '' ?>>Subscribed</option>
                                    <option value="<?php echo GROUP_CLASS ?>"<?php echo $filter['group_type'] == GROUP_CLASS ? ' selected' : '' ?>>Class</option>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <select name="filter[state]" class="select-box-it select-sapphire form-control">
                                    <option value="">--State--</option>
                                    <option value="1"<?php echo $filter['state'] == '1' ? ' selected' : '' ?>>Active</option>
                                    <option value="0"<?php echo $filter['state'] == '0' ? ' selected' : '' ?>>Inactive</option>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-default sky-blue form-control" name="filter[search]">Search</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="" style="max-height: 600px;overflow: auto;">
                        <div class=" grid-table grid-table-striped">
                            <div class="row grid-table-head">
                                <div class="col-xs-1 text-center"><input type="checkbox" class="check-all" data-name="cid[]"></div>
                                <div class="col-xs-1 text-center">Active</div>
                                <div class="col-xs-2 text-center"><a href="#" class="sortable<?php echo $filter['orderby'] == 'name' ? ' ' . $filter['order-dir'] : '' ?>" data-sort-by="name">Group Name<span class="sorting-indicator"></span></a></div>
                                <div class="col-xs-2 text-center"><a href="#" class="sortable<?php echo $filter['orderby'] == 'display_name' ? ' ' . $filter['order-dir'] : '' ?>" data-sort-by="display_name">Owner<span class="sorting-indicator"></span></a></div>
                                <div class="col-xs-2 text-center">No. of Students</div>
                                <div class="col-xs-2 text-center"><a href="#" class="sortable<?php echo $filter['orderby'] == 'expired_on' ? ' ' . $filter['order-dir'] : '' ?>" data-sort-by="expired_on">Sub. Exp<span class="sorting-indicator"></span></a></div>
                                <div class="col-xs-2 text-center"><a href="#" class="sortable<?php echo $filter['orderby'] == 'created_on' ? ' ' . $filter['order-dir'] : '' ?>" data-sort-by="created_on">Created On<span class="sorting-indicator"></span></a></div>
                            </div>
                            <?php
                            if (!empty($groups->items)) :
                                foreach ($groups->items as $group) :
                                    ?>
                                    <div class="row grid-table-row<?php echo!$group->active ? ' grid-row-gray' : '' ?>">
                                        <div class="col-xs-1 text-center"><input type="checkbox" name="cid[]" value="<?php echo $group->id ?>"></div>
                                        <div class="col-xs-1 text-center"><span class="icon-<?php echo $group->active ? 'check' : 'cancel' ?> icon-nomargin"></span></div>
                                        <div class="col-xs-2 text-center">
                                            <a href="<?php echo home_url() . '/?r=check-groups&g=' . $group->name . '#hlist' ?>" title="View homeworks"><?php echo $group->name ?></a>
                                        </div>
                                        <div class="col-xs-2 text-center">
                                            <?php if (!is_null($group->display_name)) : ?>
                                                <a href="<?php echo home_url() . '/?r=view-user&cid=' . $group->uid ?>"><?php echo $group->display_name ?></a>
                                            <?php else : ?>
                                                <span style="color: #f00">Owner deleted</span>
                                            <?php endif ?>
                                        </div>
                                        <div class="col-xs-2 text-center"><?php echo $group->no_of_student ?></div>
                                        <div class="col-xs-2 text-center"><?php echo is_null($group->expired_on) ? 'No Sub.' : ik_date_format($group->expired_on) ?></div>
                                        <div class="col-xs-2 text-center"><?php echo ik_date_format($group->created_on) ?></div>
                                    </div>
                                    <?php
                                endforeach;
                            else :
                                ?>
                                <div class="row grid-table-row">
                                    <div class="col-xs-12 text-center">No results found</div>
                                </div>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 text-center">
                    <?php echo $pagination ?>
                </div>
            </div>
        </div>												
    </div>

    <div class="row" style="margin-top: 20px" id="hlist">
        <div class="col-md-12 box">
            <div class="row box-header">
                <div class="col-xs-12">
                    <h3>Group Homeworks</h3>
                </div>
                <?php if (isset($f_g)) : ?>
                    <div class="col-xs-6 col-md-4">
                        <h4>Group Name: <span id="g-name" style="color: #fff"><?php echo $current_group->name ?></span></h4>
                    </div>
                    <div class="col-xs-6 col-md-4">
                        <h4>Owner: <span id="g-grade" style="color: #fff"><?php echo $current_group->display_name ?></span></h4>
                    </div>
                    <div class="col-xs-3 col-sm-3">
                        <?php MWHtml::get_sel_assignments('', false, array(), '-Assignment-', 'filter[assignment-id]', 'form-control', 'filter-assignment') ?>
                    </div>
                <?php endif ?>
            </div>
            <div class="row">
                <div class="col-md-12 grid-table grid-table-striped">
                    <div class="row grid-table-head">
                        <div class="col-xs-2 text-center">Type</div>
                        <div class="col-xs-3 text-center">Grade</div>
                        <div class="col-xs-2 text-center">Sheet Name</div>
                        <div class="col-xs-2 text-center">Deadline</div>
                        <div class="col-xs-2 text-center">Created On</div>
                        <div class="col-xs-1 text-center"></div>
                    </div>
                    <div class="" style="max-height: 400px;overflow: auto;">
                        <div class="grid-table-body" id="hw-list">
                            <?php 
                            if (isset($_GET['g'])) {
    $f_g = esc_html($_GET['g']);
    $current_group = MWDB::get_group($f_g);
    $homeworks = MWDB::get_homeworks_by('group_id', $current_group->id);
    $students = MWDB::get_group_students($current_group->id);
}
                            if($homeworks!=null && count($homeworks)!=0){
                            foreach ($homeworks as $homeworksgroup) {
                ?>
                            <div class="row grid-table-row">
                                <div class="col-xs-2 text-center"><?php echo $homeworksgroup->default_name ?></div>
                                <div class="col-xs-3 text-center"><?php echo $homeworksgroup->grade ?></div>
                                <div class="col-xs-2 text-center"><?php echo $homeworksgroup->sheet_name ?></div>
                                <div class="col-xs-2 text-center"><?php echo $homeworksgroup->deadline=="0000-00-00"? 'No deadline':$homeworksgroup->deadline ?></div>
                                <div class="col-xs-2 text-center"><?php echo $homeworksgroup->created_on ?></div>
                                <div class="col-xs-1 text-center" style="padding-left: 0px !important;">
                                    <button type="button" style="color: #fff;background-color: #c12e2a ;border:none;padding: 5px 10px;" class="delete-homework" name="delete-homework" data-id="<?php echo $homeworksgroup-> id?>" style="font-size: 13px" class="btn-danger btn-sm" >remove</button>
                                    <button type="button" style="color: #fff;background-color: #006505 ;font-size: 13px;border:none;padding: 5px 10px; height: 31px; width: 67.5px" class="edit-homework" name="edit-homework" 
                                            data-id="<?php echo $homeworksgroup-> id?>"
                                            data-name="<?php echo $homeworksgroup-> sheet_name?>"
                                            data-practice="<?php echo $homeworksgroup-> for_practice?>"
                                            data-retryable="<?php echo $homeworksgroup-> is_retryable?>"
                                            data-deadline="<?php echo $homeworksgroup-> deadline?>"
                                            data-displaylastpage="<?php echo $homeworksgroup-> teacherlastpage?>"
                                            data-group="<?php echo $homeworksgroup-> group_id?>"
                                    >edit</button>
                                </div>
                             

                            </div>
                            <?php } }else{ ?>
                            <div class="row grid-table-row">
                               <?php 
                               if (!isset($f_g)){ ?>
                                        <div class="col-xs-12 text-center">Select a Group to view details</div>
                               <?php }else{ ?>
                                        <div class="col-xs-12 text-center">No Homework</div>
                               <?php } ?>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" style="margin-top: 20px">
        <div class="col-xs-12 box box-sapphire">
            <div class="row box-header">
                <div class="col-xs-12">
                    <h3>Joined Users's List</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 grid-table grid-table-striped">
                    <div class="row grid-table-head">
                        <div class="col-xs-4 text-center">Username</div>
                        <div class="col-xs-4 text-center">Email</div>
                        <div class="col-xs-4 text-center">Homeworks Done/in Progress</div>
                    </div>
                    <div class="" style="max-height: 200px;overflow: auto;">
                        <div class="row grid-table-body">
                            <?php if (empty($students)) : ?>
                                <div class="row grid-table-row">
                                    <div class="col-xs-12 text-center">N/A</div>
                                </div>
                            <?php else : ?>
                                <?php foreach ($students as $student) : ?>
                                    <div class="row grid-table-row">
                                        <div class="col-xs-4 text-center"><?php echo $student->display_name ?></div>
                                        <div class="col-xs-4 text-center"><?php echo $student->user_email ?></div>
                                        <div class="col-xs-4 text-center"><?php echo $student->homeworks_done ?></div>
                                    </div>
                                <?php endforeach ?>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="filter[orderby]" id="filter-order" value="<?php echo $filter['orderby'] ?>">
    <input type="hidden" name="filter[order-dir]" id="filter-order-dir" value="<?php echo $filter['order-dir'] ?>">
    <input type="hidden" name="task" id="task" value="">
    
    <div class="modal fade modal-red-brown" id="assign-homework-modal" tabindex="-1" role="dialog" aria-hidden="true">
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
                                                                        <label style="color: white" name="homework-name" id="homework-name" ></label>
                                                                    </div>
                                                                    <div class="col-sm-6 form-group hidden">
                                                                        <label><?php _e('Homework Name', 'iii-dictionary') ?></label>
                                                                        <label name="homework-id" id="homework-id" ></label>
                                                                    </div>
                                                                    <div class="col-sm-6 form-group">
                                                                        <label for="deadline"><?php _e('Deadline', 'iii-dictionary') ?></label>
                                                                        <input type="text" class="form-control" id="deadline" name="deadline" value="<?php echo $deadline ?>" placeholder="<?php _e('No deadline', 'iii-dictionary') ?>">
                                                                    </div>
                                                                    <div class="col-sm-6 form-group">
                                                                        <label><?php _e('Homework mode', 'iii-dictionary') ?></label>
                                                                        <div class="row">
                                                                            <div class="col-xs-6">
                                                                                <div class="radio radio-style1">
                                                                                    <input id="hw-for-test" type="radio" name="hw-for-practice" value="0" >
                                                                                    <label for="hw-for-test"><?php _e('Test', 'iii-dictionary') ?></label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xs-6">
                                                                                <div class="radio radio-style1">
                                                                                    <input id="hw-for-practice" type="radio" name="hw-for-practice" value="1">
                                                                                    <label for="hw-for-practice"><?php _e('Practice', 'iii-dictionary') ?></label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <?php if (is_mw_admin() || is_mw_super_admin()) : ?>

                                                                        <div class="col-sm-6 form-group">
                                                                            <label>Homework is retryable?</label>
                                                                            <div class="row">
                                                                                <div class="col-xs-6">
                                                                                    <div class="radio radio-style1">
                                                                                        <input id="is-retryable-no" type="radio" name="is-retryable" value="0" <?php if(isset($_SESSION['is-retryable'])){echo $_SESSION['is-retryable']==0? 'checked':'';}else{ echo 'checked'; } ?>>
                                                                                        <label for="is-retryable-no">No</label>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-xs-6">
                                                                                    <div class="radio radio-style1">
                                                                                        <input id="is-retryable-yes" type="radio" name="is-retryable" value="1" <?php echo $_SESSION['is-retryable']==1? 'checked':'' ?> >
                                                                                        <label for="is-retryable-yes">Yes</label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                            <div class="row col-sm-12">
                                                                                <div class="col-xs-6">
                                                                                    <div style="float:left">
                                                                                        <input id="rdo-no" class="checkboxpage"  type="checkbox" name="checkboxpage" value="1" >
                                                                                    </div>
                                                                                    <div >
                                                                                        <label style="padding-left: 8%">Display last page</label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        <div class="col-sm-12" >
                                                                            <?php
                                                                            
                                                                            $class_group_types = MWDB::get_group_class_types();
                                                                            $group_classes = MWDB::get_groups(array('group_type' => GROUP_CLASS))
                                                                            ?>
                                                                            <div class="col-md-4 form-group" style="padding-left: 0px !important;">
                                                                                <label><?php _e('Group types', 'iii-dictionary') ?></label>
                                                                                <select class="select-box-it" id="sel-group-types" disabled>
                                                                                    <?php foreach ($class_group_types as $item) : ?>
                                                                                        <option value="<?php echo $item->id ?>" <?php echo $item->id==$current_group->class_type_id ? 'selected': ''?>><?php echo $item->name ?> </option>
                                                                                    <?php endforeach ?>
                                                                                    <option value="0" <?php ($current_group->class_type_id == null || $current_group->class_type_id =='' )? 'selected': ''?>><?php _e('Orther Groups', 'iii-dictionary') ?></option>
                                                                                </select>
                                                                                <?php foreach ($class_group_types as $item) :
                                                                                    ?><select id="class-group<?php echo $item->id ?>" class="hidden" ><?php
                                                                                    foreach ($group_classes->items as $class) :
                                                                                        if ($class->class_type_id == $item->id) :
                                                                                            ?><option value="<?php echo $class->id ?>" <?php echo $class->id==$current_group->id? 'selected': ''?>><?php echo $class->name ?></option><?php
                                                                                            endif;
                                                                                        endforeach
                                                                                        ?></select><?php endforeach ?>
                                                                            </div>
                                                                            <?php
                                                                        else :
                                                                            // make sure normal user always has his own group selected 
                                                                            ?>
                                                                            <select class="hidden" id="sel-group-types"><option value="0" selected disabled><?php _e('My Groups', 'iii-dictionary') ?></option></select>
                                                                        <?php endif ?>
                                                                            
                                                                        <?php if (is_mw_admin() || is_mw_super_admin()) : ?>    
                                                                        <select id="class-group0" class="hidden">
                                                                            <?php foreach ($group_list1->items as $grp) :
                                                                                ?><option value="<?php echo $grp->id ?>" <?php echo $grp->id==$current_group->id? 'selected': ''?>><?php echo $grp->name ?></option><?php endforeach ?>
                                                                        </select>
                                                                        <?php else : ?>
                                                                            <select id="class-group0" class="hidden">
                                                                            <?php foreach ($group_list->items as $grp) :
                                                                                ?><option value="<?php echo $grp->id ?>"><?php echo $grp->name ?></option><?php endforeach ?>
                                                                        </select>
                                                                            <?php endif?>
                                                                        <div class="col-sm-8 form-group">
                                                                            <label for="group-name"><?php _e('Group name', 'iii-dictionary') ?></label>
                                                                            <select  class="select-box-it form-control" id="sel-group" disabled></select>
                                                                        </div>

                                                                    <div >
                                                                        <div class="col-sm-12 form-group" style="padding-left: 0px !important;">
                                                                            <label>&nbsp;</label>
                                                                            <button type="button" id="update-btn" name="updateHomework" class="btn btn-default btn-block orange form-control"><?php _e('Update homework assignment', 'iii-dictionary') ?></button>
                                                                        </div>
                                                                    </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
</form>

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
<style>
                                                .checkboxpage, .checkboxlastpage{
                                                    width: 28px;
                                                    height: 28px;
                                                    border-radius: 50px;
                                                    box-shadow: inset 0px 1px 1px white, 0px 1px 3px rgba(0,0,0,0.5);
                                                    position: relative;
                                                }
                                            </style>
<?php get_dict_footer() ?>