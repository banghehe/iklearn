<?php
$route = get_route();
$_page_title = __('Create Level', 'iii-dictionary');
if (empty($route[1])) {
    $active_tab = 'english';
    $_page_title = __('Level Manager', 'iii-dictionary');
} else {
    $active_tab = $route[1];
    $_page_title = __('Level Manager', 'iii-dictionary');
}

$tab_options = array(
    'items' => array(
        'english' => array('url' => home_url() . '/?r=create-math-level', 'text' => 'English'),
        'mathematics' => array('url' => home_url() . '/?r=create-math-level/mathematics', 'text' => 'Mathematics')
    ),
    'active' => $active_tab
);
$tab_options['items'] = array(
    'english' => array('url' => home_url() . '/?r=create-math-level', 'text' => 'English'),
    'mathematics' => array('url' => home_url() . '/?r=create-math-level/mathematics', 'text' => 'Math')
);
switch ($active_tab) {
    // english homework
    case 'mathematics':
        $data['parent_id'] = $_SESSION['data_parent_id'];

        if (isset($_POST['create'])) {
            if ($_POST['cid']) {
                $data['id'] = $_POST['cid'];
            }
            $data['parent_id'] = $_POST['parent-level'];
            $data['name'] = $_REAL_POST['level-name'];
            $data['ordering'] = $_POST['ordering'];
            $data['type'] = 'MATH';
            $data['level'] = 1;

            $_SESSION['data_parent_id'] = $data['parent_id'];

            if (MWDB::store_grade($data)) {
                ik_enqueue_messages('Successfully store math level', 'success');
                wp_redirect(home_url() . '/?r=create-math-level/mathematics');
                exit;
            } else {
                ik_enqueue_messages('An error occured', 'error');
            }
        }

        if (isset($_POST['order-up'])) {
            MWDB::set_grade_order_up($_POST['cid']);
            wp_redirect(home_url() . '/?r=create-math-level/mathematics');
            exit;
        }

        if (isset($_POST['order-down'])) {
            MWDB::set_grade_order_down($_POST['cid']);
            wp_redirect(home_url() . '/?r=create-math-level/mathematics');
            exit;
        }

        $lid = empty($_GET['lid']) ? 0 : $_GET['lid'];

        if ($lid) {
            $grade = MWDB::get_grade_by_id($lid);
            $data['parent_id'] = $grade->parent_id;
            $data['name'] = $grade->name;
            $data['ordering'] = $grade->ordering;
        }

        $filter = get_page_filter_session();
        if (empty($filter)) {
            $filter['type'] = 'MATH';
            $filter['level'] = 1;
            $filter['orderby'] = 'ordering';
            $filter['order-dir'] = 'asc';
            // $filter['items_per_page'] = 99999999;
            // $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
        } else {
            if (isset($_REAL_POST['filter']['search'])) {
                $filter['parent_id'] = $_POST['filter']['category'];
            }

            if (isset($_REAL_POST['filter']['orderby'])) {
                $filter['orderby'] = $_REAL_POST['filter']['orderby'];
                $filter['order-dir'] = $_REAL_POST['filter']['order-dir'];
            }

            $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
        }

        set_page_filter_session($filter);

        $main_categories = MWDB::get_grades(array('type' => 'MATH', 'level' => 0, 'admin_only' => 1));
        $levels = MWDB::get_grades($filter);
        break;
    case 'english':
       // $data['parent_id'] = $_SESSION['data_parent_id'];

        if (isset($_POST['create'])) {
            if ($_POST['cid']) {
                $data['id'] = $_POST['cid'];
            }
          
            $data['name'] = $_REAL_POST['level-name'];
          
            $data['type'] = 'ENGLISH';
            $data['level'] = 2;
             $data['show_panel'] = 1;
            //$_SESSION['data_parent_id'] = $data['parent_id'];

            if (MWDB::store_grade($data)) {
                ik_enqueue_messages('Successfully store math level', 'success');
                wp_redirect(home_url() . '/?r=create-math-level');
                exit;
            } else {
                ik_enqueue_messages('An error occured', 'error');
            }
        }

        if (isset($_POST['order-up'])) {
            MWDB::set_grade_order_up($_POST['cid']);
            wp_redirect(home_url() . '/?r=create-math-level');
            exit;
        }

        if (isset($_POST['order-down'])) {
            MWDB::set_grade_order_down($_POST['cid']);
            wp_redirect(home_url() . '/?r=create-math-level');
            exit;
        }

        $lid = empty($_GET['lid']) ? 0 : $_GET['lid'];

        if ($lid) {
            $grade = MWDB::get_grade_by_id($lid);
            $data['parent_id'] = $grade->parent_id;
            $data['name'] = $grade->name;
            $data['ordering'] = $grade->ordering;
        }

        //$filter = get_page_filter_session();
        if (empty($filter)) {
            $filter['type'] = 'ENGLISH';
            $filter['level'] = 2;
          
             $filter['items_per_page'] = 99999999;
             $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
        } else {
            if (isset($_REAL_POST['filter']['search'])) {
                $filter['parent_id'] = $_POST['filter']['category'];
            }

            if (isset($_REAL_POST['filter']['orderby'])) {
                $filter['orderby'] = $_REAL_POST['filter']['orderby'];
                $filter['order-dir'] = $_REAL_POST['filter']['order-dir'];
            }

            $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
        }

        //set_page_filter_session($filter);

        $main_categories = MWDB::get_grades(array('type' => 'ENGLISH', 'level' => 0, 'admin_only' => 1));
        $levels = MWDB::get_grades($filter);
        break;
}
?>
<?php get_dict_header($_page_title, 'red-brown') ?>
<?php get_dict_page_title($_page_title, '', '', $tab_options, array(), get_info_tab_cloud_url('Popup_info_18.jpg')) ?>

<form method="post" id="main-form" enctype="multipart/form-data" action="<?php
echo locale_home_url() . '/?r=create-math-level/' . $active_tab;
?>">

    <style>
        #checkboxagree{
            width: 28px;
            height: 28px;
            border-radius: 50px;
            position: relative;
        }
    </style>
    <?php
    switch ($active_tab) :

        case 'mathematics':
            ?>
            <form action="<?php echo home_url() ?>/?r=create-math-level/mathematics" method="post" id="main-form">
                <div class="row">
                    <div class="col-sm-12">
                        <h2 class="title-border">New Math Level</h2>
                    </div>
                    <div class="col-sm-3 form-group">
                        <label>Category</label>
                        <select class="select-box-it form-control" name="parent-level">
                            <?php foreach ($main_categories as $item) : ?>
                                <option value="<?php echo $item->id ?>"<?php echo $item->id == $data['parent_id'] ? ' selected' : '' ?>><?php echo $item->name ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="col-sm-4 form-group">
                        <label>Subject</label>
                        <input type="text" class="form-control" name="level-name" value="<?php echo $data['name'] ?>">
                    </div>
                    <div class="col-sm-2 form-group">
                        <label>Ordering</label>
                        <input type="number" class="form-control" name="ordering" value="<?php echo $data['ordering'] ?>"<?php echo $lid ? ' readonly' : '' ?>>
                    </div>
                    <div class="col-sm-3 form-group">
                        <label>&nbsp;</label>
                        <input type="submit" name="create" class="btn btn-default btn-block orange form-control" value="<?php echo $lid ? 'Update' : 'Create' ?> Math level">
                    </div>

                    <div class="col-sm-12">
                        <h2 class="title-border">Math Level</h2>
                    </div>
                    <div class="col-sm-12">
                        <div class="box">
                            <div class="row box-header">
                                <div class="col-sm-3 col-sm-offset-6">
                                    <select class="select-box-it form-control" name="filter[category]">
                                        <option value="">--Category--</option>
                                        <?php foreach ($main_categories as $item) : ?>
                                            <option value="<?php echo $item->id ?>"<?php echo $filter['parent_id'] == $item->id ? ' selected' : '' ?>><?php echo $item->name ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <button type="submit" name="filter[search]" class="btn btn-default btn-block grey form-control">Search</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="" style="max-height: 600px;overflow: auto;">
                                        <table class="table table-striped table-condensed ik-table1 text-center">
                                            <thead>
                                                <tr>
                                                    <th>Visibility</th>
                                                    <th>Category</th>
                                                    <th>Subject</th>
                                                    <th>
                                                        <a href="#" class="sortable<?php echo $filter['orderby'] == 'ordering' ? ' ' . $filter['order-dir'] : '' ?>" data-sort-by="ordering">Ordering <span class="sorting-indicator"></span></a>
                                                    </th>
                                                    <th style="width: 120px">Lesson</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($levels)) : ?>
                                                    <tr><td colspan="3">No results</td></tr>
                                                    <?php
                                                else :
                                                    foreach ($levels as $item) :
                                                        $sublevels = MWDB::get_grades(array('type' => 'MATH', 'level' => 2, 'parent_id' => $item->id, 'orderby' => 'ordering', 'order-dir' => 'asc'));
                                                        //var_dump($sublevels);
                                                    
                                                        ?>
                                                        <tr>
                                                            <td>  
                                                                <?php if ($item->show_panel == 1) { ?>
                                                                    <input id="checkbox-<?php echo $item->id; ?>" class="checkbox-custom css-cb-show-panel" name="checkbox-<?php echo $item->id; ?>" type="checkbox" data-id="<?php echo $item->id; ?>">
                                                                    <label for="checkbox-<?php echo $item->id; ?>" class="checkbox-custom-label"></label>
                                                                <?php } else { ?>
                                                                    <input id="checkbox-<?php echo $item->id; ?>" class="checkbox-custom css-cb-show-panel" name="checkbox-<?php echo $item->id; ?>" type="checkbox" checked data-id="<?php echo $item->id; ?>">
                                                                    <label for="checkbox-<?php echo $item->id; ?>" class="checkbox-custom-label"></label>
                                                                <?php } ?>
                                                            </td>
                                                            <td><?php echo $item->parent_name ?></td>
                                                            <td><a href="<?php echo home_url() . '/?r=create-math-level&amp;lid=' . $item->id ?>"><?php echo $item->name ?></a></td>
                                                            <td>
                                                                <button type="submit" name="order-up" class="btn btn-micro grey order-btn" data-id="<?php echo $item->id ?>"><span class="icon-uparrow"></span></button>
                                                                <button type="submit" name="order-down" class="btn btn-micro grey order-btn" data-id="<?php echo $item->id ?>"><span class="icon-downarrow"></span></button>
                                                                <span class="ordering"><?php echo $item->ordering ?></span>
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-default btn-block btn-tiny grey view-sublevel" data-id="<?php echo $item->id ?>" data-name="<?php echo $item->name ?>">View or Add</button>
                                                                <table class="hidden" id="_s<?php echo $item->id ?>"><tbody><?php
//                                                                var_dump($sublevels);
//                                                                die();


                                                                foreach ($sublevels as $subitem) :
                                                                    ?>
                                                                            <tr>
                                                                                <td><input class="checkboxagree"  type="checkbox"   data-id="<?php echo $subitem->id; ?>" value="<?php echo $subitem->show_panel == 1 ? 1 : 0 ?>"  <?php echo $subitem->show_panel == 1 ? 'checked' : '' ?>></td>
                                                                                <td><?php echo $subitem->name ?></td>
                                                                                <td><input type="text" class="form-control txt-name" placeholder="New name"></td>
                                                                                <td><button type="button" class="btn btn-default btn-block grey form-control btn-rename" data-loading-text="Saving..." data-id="<?php echo $subitem->id ?>">Save</button></td>
                                                                                <td style="width: 100px">
                                                                                    <button type="button" name="order-up" class="btn btn-micro grey order-btn sub-order-up" data-id="<?php echo $subitem->id ?>"><span class="icon-uparrow"></span></button>
                                                                                    <button type="button" name="order-down" class="btn btn-micro grey order-btn sub-order-down" data-id="<?php echo $subitem->id ?>"><span class="icon-downarrow"></span></button>
                                                                                    <span class="ordering"><?php echo $subitem->ordering ?></span>
                                                                                </td>
                                                                            </tr>
                                                                        <?php endforeach ?>
                                                                    </tbody></table>
                                                            </td>
                                                            <td>
                                                                <a href="<?php echo home_url() . '/?r=create-math-level&amp;lid=' . $item->id ?>" class="btn btn-default btn-block btn-tiny grey">Edit</a>
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
                </div>
                <input type="hidden" name="cid" value="<?php echo $lid ?>">
                <input type="hidden" name="filter[orderby]" id="filter-order" value="<?php echo $filter['orderby'] ?>">
                <input type="hidden" name="filter[order-dir]" id="filter-order-dir" value="<?php echo $filter['order-dir'] ?>">
            </form>

           
            <?php
            break; // end math case


        case 'english':
            ?>
            <form action="<?php echo home_url() ?>/?r=create-math-level" method="post" id="main-form">
                <div class="row">
                    <div class="col-sm-12">
                        <h2 class="title-border">New English Level</h2>
                    </div>
                    <div class="col-sm-3 form-group">
                        <label>Category</label>
                        <select class="select-box-it form-control" name="parent-level">
                            <?php foreach ($main_categories as $item) : ?>
                                <option value="<?php echo $item->id ?>"<?php echo $item->id == $data['parent_id'] ? ' selected' : '' ?>><?php echo $item->name ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="col-sm-4 form-group">
                        <label>Subject</label>
                        <input type="text" class="form-control" name="level-name" value="<?php echo $data['name'] ?>">
                    </div>
                    <div class="col-sm-2 form-group">
                        <label>Ordering</label>
                        <input type="number" class="form-control" name="ordering" value="<?php echo $data['ordering'] ?>"<?php echo $lid ? ' readonly' : '' ?>>
                    </div>
                    <div class="col-sm-3 form-group">
                        <label>&nbsp;</label>
                        <input type="submit" name="create" class="btn btn-default btn-block orange form-control" value="<?php echo $lid ? 'Update' : 'Create' ?> English level">
                    </div>

                    <div class="col-sm-12">
                        <h2 class="title-border">English Level</h2>
                    </div>
                    <div class="col-sm-12">
                        <div class="box">
                            <div class="row box-header">
                                <div class="col-sm-3 col-sm-offset-6">
                                    <select class="select-box-it form-control" name="filter[category]">
                                        <option value="">--Category--</option>
                                        <?php foreach ($main_categories as $item) : ?>
                                            <option value="<?php echo $item->id ?>"<?php echo $filter['parent_id'] == $item->id ? ' selected' : '' ?>><?php echo $item->name ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <button type="submit" name="filter[search]" class="btn btn-default btn-block grey form-control">Search</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="" style="max-height: 600px;overflow: auto;">
                                        <table class="table table-striped table-condensed ik-table1 text-center">
                                            <thead>
                                                <tr>
                                                    <th>Visibility</th>
                                                    <th>Category</th>
                                                    <th>Subject</th>
                                                    <th>
                                                        <a href="#" class="sortable<?php echo $filter['orderby'] == 'ordering' ? ' ' . $filter['order-dir'] : '' ?>" data-sort-by="ordering">Ordering <span class="sorting-indicator"></span></a>
                                                    </th>
                                                    <th style="width: 120px">Lesson</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($levels)) : ?>
                                                    <tr><td colspan="3">No results</td></tr>
                                                    <?php
                                                else :
                                                    foreach ($levels as $item) :
                                                        $sublevels = MWDB::get_grades(array('type' => 'ENGLISH', 'level' => 3, 'parent_id' => $item->id, 'orderby' => 'ordering', 'order-dir' => 'asc'))
                                                        
                                                        ?>
                                                        <tr>
                                                            <td>  
                                                                <?php if ($item->show_panel == 1) { ?>
                                                                    <input id="checkbox-<?php echo $item->id; ?>" class="checkbox-custom css-cb-show-panel" name="checkbox-<?php echo $item->id; ?>" type="checkbox" data-id="<?php echo $item->id; ?>">
                                                                    <label for="checkbox-<?php echo $item->id; ?>" class="checkbox-custom-label"></label>
                                                                <?php } else { ?>
                                                                    <input id="checkbox-<?php echo $item->id; ?>" class="checkbox-custom css-cb-show-panel" name="checkbox-<?php echo $item->id; ?>" type="checkbox" checked data-id="<?php echo $item->id; ?>">
                                                                    <label for="checkbox-<?php echo $item->id; ?>" class="checkbox-custom-label"></label>
                                                                <?php } ?>
                                                            </td>
                                                            <td><?php echo $item->type ?></td>
                                                            <td><a href="<?php echo home_url() . '/?r=create-math-level&amp;lid=' . $item->id ?>"><?php echo $item->name ?></a></td>
                                                            <td>
                                                                <button type="submit" name="order-up" class="btn btn-micro grey order-btn" data-id="<?php echo $item->id ?>"><span class="icon-uparrow"></span></button>
                                                                <button type="submit" name="order-down" class="btn btn-micro grey order-btn" data-id="<?php echo $item->id ?>"><span class="icon-downarrow"></span></button>
                                                                <span class="ordering"><?php echo $item->ordering ?></span>
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-default btn-block btn-tiny grey view-sublevel" data-type="english" data-id="<?php echo $item->id ?>" data-name="<?php echo $item->name ?>">View or Add</button>
                                                                <table class="hidden" id="_s<?php echo $item->id ?>"><tbody><?php
//                                                                var_dump($sublevels);
//                                                                die();

                                                                if(!empty($sublevels)){
                                                                foreach ($sublevels as $subitem) :
                                                                    ?>
                                                                            <tr>
                                                                                <td><input class="checkboxagree"  type="checkbox"   data-id="<?php echo $subitem->id; ?>" value="<?php echo $subitem->show_panel == 1 ? 1 : 0 ?>"  <?php echo $subitem->show_panel == 1 ? 'checked' : '' ?>></td>
                                                                                <td><?php echo $subitem->name ?></td>
                                                                                <td><input type="text" class="form-control txt-name" placeholder="New name"></td>
                                                                                <td><button type="button" class="btn btn-default btn-block grey form-control btn-rename" data-loading-text="Saving..." data-id="<?php echo $subitem->id ?>">Save</button></td>
                                                                                <td style="width: 100px">
                                                                                    <button type="button" name="order-up" class="btn btn-micro grey order-btn sub-order-up" data-id="<?php echo $subitem->id ?>"><span class="icon-uparrow"></span></button>
                                                                                    <button type="button" name="order-down" class="btn btn-micro grey order-btn sub-order-down" data-id="<?php echo $subitem->id ?>"><span class="icon-downarrow"></span></button>
                                                                                    <span class="ordering"><?php echo $subitem->ordering ?></span>
                                                                                </td>
                                                                            </tr>
                                                                        <?php endforeach; 
                                                                            }else{
                                                                                ?>
                                                                            <tr><td>There is No Lesson.</td></tr>
                                                                           <?php } ?>
                                                                    </tbody></table>
                                                            </td>
                                                            <td>
                                                                <a href="<?php echo home_url() . '/?r=create-math-level&amp;lid=' . $item->id ?>" class="btn btn-default btn-block btn-tiny grey">Edit</a>
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
                </div>
                <input type="hidden" name="cid" value="<?php echo $lid ?>">
                <input type="hidden" name="filter[orderby]" id="filter-order" value="<?php echo $filter['orderby'] ?>">
                <input type="hidden" name="filter[order-dir]" id="filter-order-dir" value="<?php echo $filter['order-dir'] ?>">
            </form>

           
            <?php
            break; //end case english
    endswitch
    ?>
     <div id="sublevel-dialog" class="modal fade modal-large modal-red-brown" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
                            <h3><span id="parent-name"></span></h3>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-6 form-group">
                                    <label>Lesson Name</label>
                                    <input type="text" class="form-control" id="sub-level-name">
                                </div>
                                <div class="col-sm-6 form-group">
                                    <label>&nbsp;</label>
                                    <button type="button" class="btn btn-default btn-block orange form-control" id="create-sublevel" data-loading-text="Creating...">Create</button>
                                </div>
                                <div class="col-sm-12">
                                    <h2 class="title-border">Lesson List</h2>
                                </div>
                                <div class="col-sm-12">
                                    <table class="table table-striped table-condensed ik-table1" id="sublevel-tbl">
                                        <thead>
                                            <tr><th>Display panel</th><th>Lesson Name</th></tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <input type="hidden" id="parent-id">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    <script>
        (function ($) {
            $(function () {
                $("button.view-sublevel").click(function () {
                    $("#parent-id").val($(this).attr("data-id"));
                    $("#parent-name").text($(this).attr("data-name"));
                    var type=$(this).attr("data-type");
                    if(type=='english'){
                        $('#create-sublevel').attr("data-type",'english');
                    }
                    var tbody=$(this).next().find("tbody").html();
                   
                         $("#sublevel-tbl").find("tbody").html(tbody);
                        
                   
                    $("#sublevel-dialog").modal();
                    
                });

                $("#create-sublevel").click(function () {
                    var tthis = $(this);
                    var _n = $("#sub-level-name");
                    if (_n.val().trim() == "") {
                        _n.popover({content: '<span class="text-danger">Level name cannot be empty</span>', html: true, trigger: "hover", placement: "bottom"})
                                .popover("show");
                        setTimeout(function () {
                            _n.popover("destroy")
                        }, 1500);
                    } else {
                        tthis.button("loading");
                        var _name = _n.val();
                        var _pid = $("#parent-id").val();
                        var type=$(this).attr("data-type");
                        if(type=='english'){
                              $.post(home_url + "/?r=ajax/grade/add", {name: _name, parent_id: _pid, level: 3, type: "ENGLISH",show_panel: 1}, function (data) {
                            tthis.button("reset");
                            if (data != 0) {
                                var tr = "<tr><td>" + _name + "</td></tr>";
                                $("#sublevel-tbl").find("tbody").append(tr);
                                $("#_s" + _pid).find("tbody").append(tr);
                                _n.val("").focus();
                                location.reload();
                            }
                        });
                        }
                        else{
                              $.post(home_url + "/?r=ajax/grade/add", {name: _name, parent_id: _pid, level: 2, type: "MATH"}, function (data) {
                            tthis.button("reset");
                            if (data != 0) {
                                var tr = "<tr><td>" + _name + "</td></tr>";
                                $("#sublevel-tbl").find("tbody").append(tr);
                                $("#_s" + _pid).find("tbody").append(tr);
                                _n.val("").focus();
                                location.reload();
                            }
                        });
                        }
                      
                    }
                });
                $("#sublevel-tbl").on("click", ".checkboxagree", function () {
                    if ($(this).is(':checked')) {
                        $(this).val('1');
                    } else {
                        $(this).val('0');
                    }
                });
                $("#sublevel-tbl").on("click", "button.btn-rename", function () {
                    var tthis = $(this);
                    var _id = $(this).attr("data-id");
                    var _t = tthis.parents("tr").find("input.txt-name");
                    var _check = tthis.parents("tr").find("input.checkboxagree");
    //                if (_t.val().trim() != "") {
                    tthis.button("loading");
                    $.post(home_url + "/?r=ajax/grade/rename", {id: _id, n: _t.val(), check: _check.val()}, function (data) {
                        //tthis.button("reset");
                        location.reload();
                    });
    //                }
                });
                $("#sublevel-tbl").on("click", ".sub-order-up", function () {
                    var tr = $(this).parents("tr");
                    var corder = tr.find("span.ordering"), porder = tr.prev().find("span.ordering");
                    $.post(home_url + "/?r=ajax/grade/change_order", {id: $(this).attr("data-id"), dir: "up"});
                    tr.fadeOut(400).insertBefore(tr.prev()).fadeIn(400);
                    corder.text(parseInt(corder.text()) - 1);
                    porder.text(parseInt(porder.text()) + 1);
                });

                $("#sublevel-tbl").on("click", ".sub-order-down", function () {
                    var tr = $(this).parents("tr");
                    var corder = tr.find("span.ordering"), porder = tr.next().find("span.ordering");
                    $.post(home_url + "/?r=ajax/grade/change_order", {id: $(this).attr("data-id"), dir: "down"});
                    tr.fadeOut(400).insertAfter(tr.next()).fadeIn(400);
                    corder.text(parseInt(corder.text()) + 1);
                    porder.text(parseInt(porder.text()) - 1);
                });
                $('#sublevel-dialog').on('hidden', function () {
                    $.ajax({
                        type: "POST",
                        url: "create-math-level.php",
                        data: infoPO,
                        success: function () {
                            location.reload();
                        }
                    });
                })
            });
            // Change visibility ajax colum " show_panel "
            $('.checkbox-custom').change(function () {
                var id = $(this).attr("data-id");
                var check;
                if ($(this).is(':checked')) {
                    check = 0;
                } else {
                    check = 1;
                }
                
                $.get(home_url + "/?r=ajax/show_panel", {id: id, show_panel: check}, function (data) {
                    console.log(data);
                });
            });
             $(".checkboxagree").live("change", function () {
                var id = $(this).attr("data-id");
                 
                var check;
                var a=$(this).is(':checked');
                if (a==true) {
                    check = 1;
                } else {
                    check = 0;
                }
               
                $.get(home_url + "/?r=ajax/show_panel", {id: id, show_panel: check}, function (data) {
                    console.log(data);
                });
            });
        })(jQuery);
      
    </script>
    <?php get_dict_footer() ?>