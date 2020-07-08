<?php
$link_list_group = get_option_name_link();
$price_course_at_english = mw_get_option('price-course-at-english');
//some function at home
if (!empty($_POST['data-join'])) {
    MWDB::lang_join_group($_POST);
}

if(isset($_POST['credit-code'])) {
                if($cid = MWDB::add_credit_code($_POST)) {}		
	}
$link_current = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
if (strpos($link_current, '/en') !== false) {
    $enlanguage = 1;
} elseif (strpos($link_current, '/ja') !== false) {
    $enlanguage = 2;
} elseif (strpos($link_current, '/ko') !== false) {
    $enlanguage = 3;
} elseif (strpos($link_current, '/vi') !== false) {
    $enlanguage = 4;
} elseif (strpos($link_current, '/zh') !== false) {
    $enlanguage = 5;
} elseif (strpos($link_current, '/zh-tw') !== false) {
    $enlanguage = 5;
}
ik_enqueue_js_messages('login_req_h', __('Login Required', 'iii-dictionary'));
ik_enqueue_js_messages('login_req_err', __('Please login in order to continue to use this function.', 'iii-dictionary'));
ik_enqueue_js_messages('login_req_lbl', __('Login', 'iii-dictionary'));

$URL = $_SERVER['REQUEST_URI'];
$segment = explode('/', $URL);
if ($segment[2] == 'home') {
    get_header('main');
    include 'home_main.php';
} elseif ($segment[2] == 'englishteacher') {
    include 'english_teacher.php';
} else {
    get_header();
//    include 'home_sat_new.php';
    ?>
    <main class="home " id="home">
    <div id="switch-menu-english" class="css-swith-menu">
        <div class="css-div-center">
            <div class="col-xs-6" id="start-lesson"><a href="#" class="a-switch" >Critical Lessons!</a><span class="glyphicon glyphicon-arrow-right arrow-right"></span></div>
            <div class="col-xs-6" id="code-active"><a href="#" style=" padding-left: 7%;" class="a-switch">Code Activation</a><span class="glyphicon glyphicon-arrow-right arrow-right"></span></div>
        </div>
    </div>
    <div id="content" class="english-content">
        <div class="txt-critical-english">Critical English Subjects</div>
        <div class="image-english-subject css-image"></div>
        <?php
            $_lang = get_short_lang_code();
            $is_math = is_math_panel();
            $current_page = max(1, get_query_var('page'));
            $filter['offset'] = 0;
            $filter['items_per_page'] = 19;
            $filter['group_type'] = GROUP_CLASS;
            $filter['lang'] = is_math_panel() ? get_short_lang_code() . '-math' : get_short_lang_code() . '-en';
            $filter['orderby'] = 'ordering';
            $filter['order-dir'] = 'ASC'; 
            $homeworkgroup = MWDB::get_groups_home_english($filter)
            ?>
        <form id="lang-form" method="post" action="<?php echo locale_home_url() ?>">
        <div  id="div_hw3">
            <div id="homeworkcourse" style="overflow: hidden;">
                <div class="homeworkcritical-online can-scroll" style="height:444px;" >
                    <div class="width-table-display">
                    <table class="table table-striped table-condensed ik-table1 scroll-fix-head-new" id="homeworkcritical" >
                        <thead class="homeworkcritical" style="font-size: 15px;">
                            <tr style="height: 46px;">
                                <th class="text-color-custom-1" style="padding-left: 5%;width: 23%"><?php _e('Name', 'iii-dictionary') ?></th>
                                <th class="text-color-custom-1" style="width: 13%;padding-left: 2%"><?php _e('Price', 'iii-dictionary') ?></th>
                                <th class="text-color-custom-1" style="width: 42% !important"><?php _e('Course Name', 'iii-dictionary') ?></th>
                                <th class="text-color-custom-1" style="width: 10%;padding-left: 3%"><?php _e('Detail', 'iii-dictionary') ?></th>
                                <th class="text-color-custom-1" style="width: 6%"><?php _e('Join', 'iii-dictionary') ?></th>
                                <th class="text-color-custom-1" style="width: 10%"><?php _e('No. of W.S.', 'iii-dictionary') ?></th>
                            </tr>
                        </thead>
                        <tfoot>
                        </tfoot>
                        <tbody style="max-height: 393px !important;font-size: 14px;">
                            <?php 
                            if (empty($homeworkgroup->items)) : ?>
                                <tr style="width: 100%;">
                                    <td colspan="6" style="height:44px;width: 100% !important;padding-left: 5%"><?php _e('Haven\'t group', 'iii-dictionary') ?></td>
                                    <td></td>
                                </tr>
                                <?php for($i=1;$i<9;$i++){ ?>
                                <tr style="height:44px;width: 100% !important">
                                    <td colspan="6"></td>
                                    <td></td>
                                </tr>
                                <?php } 
                            else :
                                    foreach ($homeworkgroup->items as $item) :
                                        $get_stg = MWDB::get_something_in_group($item->id);//$rp_url = $get_stg->step['prt'] ? $practice_url : $homework_url;
    //                                    var_dump($get_stg->step);
                                        ?>
                                        <tr>
                                            <td style="padding-left: 5%;width: 54%"><div class="width-columns-name-group"><?php echo $item->name ?></div></td>
                                            <td style="font-weight: bold;width: 17%"><?php
                                                if ($item->price == 0) {
                                                    echo 'FREE';
                                                } else {
                                                    echo '$' . $item->price;
                                                }
                                                ?></td>

                                            <td style="width: 6%"><div class="width-columns-name-worksheet"><?php echo $item->content ?></div></td>
                                            <td style="font-weight: bold;width: 3%;text-decoration: underline"><div><a href="#" class="bold-font btn btn-default btn-block btn-tiny grey btn-a-link <?php
                                                    if (is_null($exist)) {
                                                        echo 'not-join';
                                                    } else {
                                                        echo '';
                                                    }
                                                    ?>" ><?php _e('DETAIL', 'iii-dictionary') ?></a>
                                                    <div class="hidden">
                                                        <div style="width: 800px"></div>
                                                        <?php
                                                        $namehomework = MWDB::get_name_homework_group($item->id);
                                                        foreach ($namehomework as $nhw):
                                                            echo ' - ' . $nhw->namehw . "<br>";
                                                        endforeach;
                                                        ?>
                                                    </div>
                                                </div></td>
                                                <td style="width: 16%;padding-right: 2%;">
                                                    <?php 
    //                                                        var_dump($get_stg->step['id']);
                                                    if (empty($get_stg->step_of_user) && $item->price != 0 || !is_user_logged_in()) { ?>
                                                        <a href="#" data-name="<?php echo $item->name ?>" data-free="<?php echo ( $item->price == 0 ) ? '1' : '0' ?>" data-price="<?php echo $item->price;?>" data-jcid="<?php echo $item->id ?>" class="bold-font btn btn-default btn-block btn-tiny css-color-006fbd btn-a-link join-class-lang-btn"><?php _e('JOIN', 'iii-dictionary') ?></a>
                                                        <?php } else { ?>
                                                        <a href="<?php echo locale_home_url() ?>?r=online-learning&english&lvid=<?php echo $item->id?>" class="bold-font btn btn-default btn-block btn-tiny css-color-006fbd btn-a-link prevent-detail-btn-<?php echo $is_math ? 'math' : 'english' ?> "><?php _e('START', 'iii-dictionary') ?></a>
                                                    <?php } ?>
                                                </td>
                                            <td style="width: 16%;"><div ><?php
                                                    $count = MWDB::get_count_worksheets_group($item->id);
                                                    $count_complete = MWDB::get_count_worksheets_completeed_group($item->id);
                                                    echo $count[0]->count;
                                                    ?></div>
                                            </td>
                                        </tr>
                                            <?php
                                        endforeach;
                                        if(count($homeworkgroup->items)<9)?>
                                        <?php for($i=count($homeworkgroup->items);$i<9;$i++) { ?>
                                            <tr style="width: 100%;">
                                                <td colspan="5" style="height:44px;width: 100% !important"></td>
                                                <td></td>
                                            </tr>
                                    <?php } 
                                endif;
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
<!-- modal purchase join class math-->
<div id="require-pay-modal-english" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Purchase Subscriptions Join Class', 'iii-dictionary') ?></h3>
            </div>
                <input type="hidden" name="sub-type" id="sat-sub-type" >
                <input type="hidden" name="sat-class" id="sat-class" value="">
                <input type="hidden" name="sub-id" value="0">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="font-dialog col-xs-12" style="border-bottom: 1px solid #d6d6d6;"><?php _e('Selected Class', 'iii-dictionary') ?></label>
                                <p class="selected-class col-xs-12" id="selected-class" name="class-name"></p>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12" style="padding-top: 3%">
                            <div class="form-group">
                                <label class="font-dialog"><?php _e('Number of Months', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control" id="sel-sat-months" disabled="true">
                                        <?php for ($i = 1; $i <= 24; $i++) : ?>
                                        <option value="<?php echo $i ?>"><?php printf(_n('%s month', '%s months', $i, 'iii-dictionary'), $i) ?></option>
                        <?php endfor ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-sm-12 padding-top-2">
                            <div class="box-gray-dialog" style="text-align: right">
                                <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color708b23">$<span id="total-amount-sat" class="color708b23">5</span></span>
                            </div>
                        </div>
                    </div>				
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button type="submit" name="add-to-cart" class="btn-custom confirm"><?php _e('Check out', 'iii-dictionary') ?></button>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1 secondary"><?php _e('Cancel', 'iii-dictionary') ?></a>
                            </div>
                        </div>
                    </div>
                </div>			
        </div>
    </div>
</div>
<input type="hidden" name="data-join" id="data-join" value="" />
<input type="hidden" name="sat-months" id="set-month-sub" value="1" />
</form>
</div>
</main>
<?php
}
?>	


<div id="new-to-our-product-dialog" class="modal fade modal-white" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="close2"></a>
            </div>
            <div class="modal-body visible-md visible-lg">
                <ul>
                    <li><a class="view-sub-modal" href="#" data-img="<?php echo get_info_tab_cloud_url('Popup_info_3.jpg') ?>"><?php _e('How to help teachers in the classroom', 'iii-dictionary') ?></a></li>
                    <li><a class="view-sub-modal" href="#" data-img="<?php echo get_info_tab_cloud_url('Popup_info_4.jpg') ?>"><?php _e('If you want to improve your Englsih writing...', 'iii-dictionary') ?></a></li>
                    <li><a class="view-sub-modal" href="#" data-img="<?php echo get_info_tab_cloud_url('Popup_info_5.jpg') ?>"><?php _e('Complete review of Grammar and Vocab', 'iii-dictionary') ?></a></li>
                    <li><a class="view-sub-modal" href="#" data-img="<?php echo get_info_tab_cloud_url('Popup_info_6.jpg') ?>"><?php _e('SAT test preparation', 'iii-dictionary') ?></a></li>
                </ul>
            </div>
            <a href="#" data-dismiss="modal" aria-hidden="true" class="btn green dismiss-modal"><?php _e('Got it', 'iii-dictionary') ?></a>
        </div>
    </div>
</div>

<div id="why-merriam-dialog" class="modal fade modal-white" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="close2"></a>
            </div>
        </div>
    </div>
</div>

<div id="about-teacher-dialog" class="modal fade modal-white" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="close2"></a>
                <a href="http://ikteachonline.com" target="_blank" class="movehttp"></a>
            </div>                      
        </div>
    </div>
</div>

<div id="about-student-dialog" class="modal fade modal-white" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="close2"></a>
            </div>           
        </div>
    </div>
</div>

<div id="made-teacher-dialog" class="modal fade modal-white" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
            </div>
            <a href="#" data-dismiss="modal" aria-hidden="true" class="btn orange dismiss-modal"><span class="icon-switch"></span> <?php _e('Go back', 'iii-dictionary') ?></a>
        </div>
    </div>
</div>

<div id="popup-info-dialog" class="modal fade modal-white" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="close-transp"></a>
                <img id="popup-info-img" src="#" alt="">
            </div>
        </div>
    </div>
</div>

<div id="popup-tool-english-dialog" class="modal fade modal-white" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body" style="height: 100%">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="close-transp"></a>
                <a href="#popup-teacher-dialog" data-toggle="modal" id="a-library" class="a-tool"></a>
                <a href="#popup-improvesat-dialog"  data-toggle="modal" id="a-level" class="a-tool"></a>
                <a href="#popup-improvevocab-dialog"  data-toggle="modal" id="a-sat" class="a-tool"></a>
                <a href="#popup-satprep-dialog" data-toggle="modal" id="a-confident" class="a-tool"></a>
            </div>
        </div>
    </div>
</div>
<div id="popup-teacher-dialog" class="modal fade modal-white" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="close-transp"></a>
            </div>
        </div>
    </div>
</div>
<div id="popup-improvesat-dialog" class="modal fade modal-white" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="close-transp"></a>
            </div>
        </div>
    </div>
</div>
<div id="popup-improvevocab-dialog" class="modal fade modal-white" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="close-transp"></a>
            </div>
        </div>
    </div>
</div>
<div id="popup-satprep-dialog" class="modal fade modal-white" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="close-transp"></a>
            </div>
        </div>
    </div>
</div>

<!-- dialog show detail worksheet in group-->
<div class="modal fade modal-purple modal-large" id="class-detail-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 3%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3 class="modal-title" id="myModalLabel"><?php _e('Class Detail', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-body body-custom" style="height: 400px !important;">
                <div id="modal-body-detail" ></div>
            </div>
            
        </div>
    </div>
</div>

<!-- dialog active code -->
<div id="active_code" class="modal fade modal-red-brown">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Purchase Points', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo locale_home_url() ?>">
                <div class="modal-body body-custom">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="font-dialog"><?php _e('Enter a Credit Code ( if you have any )', 'iii-dictionary') ?></label>
                                <input type="text" class="form-control" name="credit-code" id="no-of-points" min="1" placeholder="Enter Code Here" style="padding-left: 5%;">
                            </div>
                        </div>
                    </div>				
                </div>
                <div class="modal-footer footer-custom" style="padding-top: 0px !important;">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button type="submit"  class="btn-custom confirm"><?php _e('Apply', 'iii-dictionary') ?></button>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1 secondary"><?php _e('Cancel', 'iii-dictionary') ?></a>
                            </div>
                        </div>
                    </div>
                                <div><hr><label class="is-points"><?php _e('WHAT IS POINTS?', 'iii-dictionary') ?></label></div>
                                <div><label class="text-points"><span class="css-txt-left"><?php _e('Points are required to purchase worksheet that you can use for the homework assignment.', 'iii-dictionary') ?></span></label></div>
                                <div><label class="text-points" style="text-align: left;"><span class="css-txt-left"><?php _e('1 point is equivalent to 1 dollar. You can earn poin by selling your worksheet to the teachers.', 'iii-dictionary') ?></span></label></div>
                                <div><label class="text-points"><span class="css-txt-left"><?php _e('You can earn points by editing and grading writing assignment students submitted.', 'iii-dictionary') ?></span></label></div>
                </div>			
            </form>
        </div>
    </div>
</div>
<!-- dialog Home Result-->
<div id="require-modal" class="modal fade modal-purple" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header custom-header">
                    <span style="right: 3%;color: white;background: none;" href="#" data-dismiss="modal" aria-hidden="true" class="close glyphicon glyphicon-remove"></span>
                    <h3><?php _e('Subscription', 'iii-dictionary') ?></h3>
                </div>
                <div class="modal-body body-custom"></div>
                <div class="modal-footer footer-custom">
                    <a href="<?php echo locale_home_url() ?>/?r=manage-subscription#3" class="btn-block btn-custom"></a>
                </div>
            </div>
        </div>
</div>

<!-- modal purchase join class math-->
<div id="modal-purchase-join-class-english" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Purchase Subscriptions Join Class', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo home_url_ssl() ?>/?r=payments">
                <input type="hidden" name="sub-type" id="sat-sub-type" value="28">
                <input type="hidden" name="sat-class" id="sat-class" value="">
                <input type="hidden" id= "sat-name" name="class-name" value="">
                <input type="hidden" name="sub-id" value="0">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="font-dialog col-xs-12" style="border-bottom: 1px solid #d6d6d6;"><?php _e('Selected Class', 'iii-dictionary') ?></label>
                                <p class="selected-class col-xs-12" id="selected-class" ></p>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12" style="padding-top: 3%">
                            <div class="form-group">
                                <label class="font-dialog"><?php _e('Number of Months', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control" name="sat-months" id="sel-sat-months">
                                        <?php for ($i = 1; $i <= 24; $i++) : ?>
                                        <option value="<?php echo $i ?>"><?php printf(_n('%s month', '%s months', $i, 'iii-dictionary'), $i) ?></option>
                        <?php endfor ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-sm-12 padding-top-2">
                            <div class="box-gray-dialog" style="text-align: right">
                                <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color708b23">$<span id="total-amount-sat" class="color708b23">0</span></span>
                            </div>
                        </div>
                    </div>				
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button type="submit" name="add-to-cart" class="btn-custom confirm"><?php _e('Check out', 'iii-dictionary') ?></button>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1 secondary"><?php _e('Cancel', 'iii-dictionary') ?></a>
                            </div>
                        </div>
                    </div>
                </div>			
            </form>
        </div>
    </div>
</div>
<script>
    (function ($) {
        $(function () {
            $(".view-sub-modal").click(function (e) {
                e.preventDefault();
                var _img = $("#popup-info-img");
                var _m = $("#popup-info-dialog");
                _img.attr("src", $(this).attr("data-img")).load(function () {
                    _m.find(".modal-dialog").width(this.width);
                });
                $("#new-to-our-product-dialog").one('show.bs.modal', function () {

                    $(this).off('hidden.bs.modal');
                    _m.modal();
                    _m.style.display = 'block';
                }).modal("show");
            });

            $("#popup-info-dialog").on("hidden.bs.modal", function () {
                $("#new-to-our-product-dialog").modal();
            });

            $("#about-teacher-dialog").on("hidden.bs.modal", function () {
                window.location.href = home_url + "/?r=teaching";
            });

            $("#about-student-dialog").on("hidden.bs.modal", function () {
                window.location.href = home_url + "/?r=sat-preparation";
            });
            $(".join-class-lang-btn").on("click", function () {
                    var get_jcid = $(this).attr('data-jcid');
                    var get_free = $(this).attr('data-free');
                    var name = $(this).attr('data-name');
                    if (is_login == 0) {
                        $(location).attr('href', '<?php echo locale_home_url() ?>/?r=login');
                    } else {
                        $('#data-join').val(get_jcid);
                        if (get_free == 1) {
                            $('#lang-form').submit();
                        } else {
                            var _gprice = $(this).parents('tr').find('td:eq(1)').text();
                            $('#_gprice').text(_gprice);
                            $("#selected-class").html(name);
                            $('#require-pay-modal-english').modal();
                            var price =$(this).attr("data-price");
                            $('#total-amount-sat').html(price);
                            return false;
                        }
                    }

                });
        });
    })(jQuery);
</script>
<style type="text/css">
    .popup-info-dialog {
        /*    width:900px;
            height:800px;*/
        position:absolute;
        /*    top:50%;
            left:50%;
            margin:-50px 0 0 -100px;  [-(height/2)px 0 0 -(width/2)px] */
        display:none;
    }
</style>
<?php if (is_user_logged_in() && isset($_SESSION['newuser'])) : ?>
    <div id="signup-success-dialog" class="modal fade modal-red-brown" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
                    <h3><?php _e('Thank you for signing up!', 'iii-dictionary') ?></h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <p><?php _e('Thank you for signing up on Merriam-Webster and English Learning System.', 'iii-dictionary') ?></p>
                            <p><?php _e('If you have a group name for homework assignment, you can check it at <strong><em>My Account</em></strong> Area.', 'iii-dictionary') ?></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn btn-block orange secondary"><span class="icon-check"></span><?php _e('Let\'s Begin!', 'iii-dictionary') ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        (function ($) {
            $(function () {
                $('#signup-success-dialog').modal('show');
            });
        })(jQuery);
    </script>
    <?php
    $_SESSION['newuser'] = null;
endif
?>
<script>
    var pce = <?php echo (int)$price_course_at_english ?>;
    var LANGUAGE =<?php echo $enlanguage ?>;
    var ptsr = <?php echo mw_get_option('point-exchange-rate') ?>;
    switch (LANGUAGE) {
        case 2 :
        {
            if ((window.matchMedia('screen and (min-width: 992px)').matches)) {
                jQuery('header .user-nav .sub-menu').attr({'top': '98px', 'left': '-163px'});
                jQuery('header .navbar-default ul.user-nav > li:nth-child(2) .sub-menu > li > a').attr('width', '261px');
                jQuery('#img_interface_ensat').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/banner1_ja.jpg)');
                jQuery('#img_interface_math').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/banner2_ja.jpg)');
                jQuery('#img_interface_english').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/banner3_ja.jpg)');
                jQuery('#main-nav > nav > a > img').attr('src', 'http://ikstudy.com/wp-content/themes/ik-learn/library/images/logo_menu_ja.jpg');
            } else if ((window.matchMedia('screen and (min-width: 768px)').matches)) {
                jQuery('#img_interface_ensat').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/ja-tablet.jpg)');
                jQuery('#img_interface_math').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/ja1-tablet.jpg)');
                jQuery('#img_interface_english').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/ja2-tablet.jpg)');
            } else if ((window.matchMedia('screen and (min-width: 480px)').matches)) {
                jQuery('#img_interface_ensat').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/ja-tablet.jpg)');
                jQuery('#img_interface_math').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/ja1-tablet.jpg)');
                jQuery('#img_interface_english').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/ja2-tablet.jpg)');
            } else if ((window.matchMedia('screen and (min-width: 320px)').matches)) {
                jQuery('div > nav:first-of-type').hide();
                jQuery('#img_interface_ensat').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/ja_ip.jpg)');
                jQuery('#img_interface_math').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/ja1_ip.jpg)');
                jQuery('#img_interface_english').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/ja2_ip.jpg)');
            }
            break;
        }
        case 3:
        {
            if ((window.matchMedia('screen and (min-width: 992px)').matches)) {
                jQuery('header .user-nav .sub-menu').attr({'top': '98px', 'left': '-163px'});
                jQuery('header .navbar-default ul.user-nav > li:nth-child(2) .sub-menu > li > a').attr('width', '261px');
                jQuery('#img_interface_ensat').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/banner1_ko.jpg)');
                jQuery('#img_interface_math').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/banner2_ko.jpg)');
                jQuery('#img_interface_english').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/banner3_ko.jpg)');
                jQuery('#main-nav > nav > a > img').attr('src', 'http://ikstudy.com/wp-content/themes/ik-learn/library/images/logo_menu_ko.jpg');
            } else if ((window.matchMedia('screen and (min-width: 768px)').matches)) {
                jQuery('#img_interface_ensat').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/ko-tablet.jpg)');
                jQuery('#img_interface_math').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/ko1-tablet.jpg)');
                jQuery('#img_interface_english').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/ko2-tablet.jpg)');
            } else if ((window.matchMedia('screen and (min-width: 480px)').matches)) {
                jQuery('#img_interface_ensat').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/ko-tablet.jpg)');
                jQuery('#img_interface_math').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/ko1-tablet.jpg)');
                jQuery('#img_interface_english').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/ko2-tablet.jpg)');
            } else if ((window.matchMedia('screen and (min-width: 320px)').matches)) {
                jQuery('div > nav:first-of-type').hide();
                jQuery('#img_interface_ensat').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/ko_ip.jpg)');
                jQuery('#img_interface_math').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/ko1_ip.jpg)');
                jQuery('#img_interface_english').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/ko2_ip.jpg)');
            }
            break;
        }
        case 4:
        {
            if ((window.matchMedia('screen and (min-width: 992px)').matches)) {
                jQuery('header .user-nav .sub-menu').attr({'top': '98px', 'left': '-163px'});
                jQuery('header .navbar-default ul.user-nav > li:nth-child(2) .sub-menu > li > a').attr('width', '261px');
                jQuery('#img_interface_ensat').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/home_vi1.png)');
                jQuery('#img_interface_math').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/home_vi2.png)');
                jQuery('#img_interface_english').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/home_vi3.png)');
                jQuery('#main-nav > nav > a > img').attr('src', 'http://ikstudy.com/wp-content/themes/ik-learn/library/images/home_vi4.png');
            } else if ((window.matchMedia('screen and (min-width: 768px)').matches)) {
                jQuery('#img_interface_ensat').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/vi-tablet1.png)');
                jQuery('#img_interface_math').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/vi-tablet2.png)');
                jQuery('#img_interface_english').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/vi-tablet3.png)');
            } else if ((window.matchMedia('screen and (min-width: 480px)').matches)) {
                jQuery('#img_interface_ensat').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/vi-tablet1.png)');
                jQuery('#img_interface_math').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/vi-tablet2.png)');
                jQuery('#img_interface_english').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/vi-tablet3.png)');
            } else if ((window.matchMedia('screen and (min-width: 320px)').matches)) {
                jQuery('div > nav:first-of-type').hide();
                jQuery('#img_interface_ensat').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/vi-ip1.png)');
                jQuery('#img_interface_math').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/vi-ip2.png)');
                jQuery('#img_interface_english').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/vi-ip3.png)');
            }
            break;
        }
        case 5:
        {
            if ((window.matchMedia('screen and (min-width: 992px)').matches)) {
                jQuery('header .user-nav .sub-menu').attr({'top': '98px', 'left': '-163px'});
                jQuery('header .navbar-default ul.user-nav > li:nth-child(2) .sub-menu > li > a').attr('width', '261px');
                jQuery('#img_interface_ensat').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/13.jpg)');
                jQuery('#img_interface_math').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/14.jpg)');
                jQuery('#img_interface_english').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/16.jpg)');
                jQuery('#main-nav > nav > a > img').attr('src', 'http://ikstudy.com/wp-content/themes/ik-learn/library/images/15.jpg');
            } else if ((window.matchMedia('screen and (min-width: 768px)').matches)) {
                jQuery('#img_interface_ensat').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/en-tablet.jpg)');
                jQuery('#img_interface_math').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/en1-tablet.jpg)');
                jQuery('#img_interface_english').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/en2-tablet.jpg)');
            } else if ((window.matchMedia('screen and (min-width: 480px)').matches)) {
                jQuery('#img_interface_ensat').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/en-tablet.jpg)');
                jQuery('#img_interface_math').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/en1-tablet.jpg)');
                jQuery('#img_interface_english').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/en2-tablet.jpg)');
            } else if ((window.matchMedia('screen and (min-width: 320px)').matches)) {
                jQuery('div > nav:first-of-type').hide();
                jQuery('#img_interface_ensat').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/en_ip.jpg)');
                jQuery('#img_interface_math').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/en1_ip.jpg)');
                jQuery('#img_interface_english').css('background-image', 'url(../wp-content/themes/ik-learn/library/images/en2_ip.jpg)');
            }
            break;
        }
        case 1:
        {
            if ((window.matchMedia('screen and (min-width: 992px)').matches)) {
                jQuery('header .user-nav .sub-menu').attr({'top': '98px', 'left': '-163px'});
                jQuery('header .navbar-default ul.user-nav > li:nth-child(2) .sub-menu > li > a').attr('width', '261px');
            }
            break;
        }

    }
//     if ((window.matchMedia('screen and (max-width: 767px)').matches)) {
//                jQuery('ul#user-nav').prepend('<li><a href="<?php echo site_home_url(); ?>/home" title="<?php _e('Home', 'iii-dictionary') ?>"><?php _e('Home', 'iii-dictionary') ?><span class="home-icon"></span></a></li>');
//            }
    (function ($) {
        $(function () {
//            jQuery(".homeworkcritical-online").mCustomScrollbar({
//                    axis: "yx",
//                    theme: "rounded-dark",
//                    scrollButtons: {enable: true}
//                });
            $('.not-join').click(function (e) {
                var modal = $("#require-modal");
                
                    e.preventDefault();
                    var modal = $("#class-detail-modal");
                    count=$(this).next().html();
                    if($(count).filter("br").length > 13){
                        $('.modal-body').css('height','400px');
                        modal.find("#modal-body-detai").html($(this).next().html());
                    modal.modal();
                    $('#class-detail-modal').find('.body-custom').mCustomScrollbar({
                        theme: "rounded-dark",
                        scrollButtons: {enable: true}
                    });
                     modal.find('#modal-body-detail').css('height','350px')
                    }else{
                        modal.find("#modal-body-detail").html($(this).next().html());
                    modal.modal();
                    }
                    
            });
            $('.check-sub-class').click (function (e){
                e.preventDefault();
                $('#purchase-join-class').modal('show');
                
            });
            $('.check-calendar1').datepicker({         
                inline: true,            
                showOtherMonths: true, 
                beforeShow: function(elem, dp) { 
                }
            });
            function total_purchase_join_class_english() {
                var months = parseInt($("#sel-sat-months").val());
                $("#total-amount-sat").text(months * pce);
            }
             $('.purchase-join-class-english').click (function (e){
                e.preventDefault();
                if (!isuserloggedin) {
                    window.location.href = home_url+'/?r=login';
                } else {
                    var name = $(this).attr("data-name");
                    $("#selected-class").text(name);
                    $('#popup-lesson-dialog').modal('hide');
                    total_purchase_join_class_english();
                    $('#sat-name').val($(this).attr("data-name"));
                    $('#modal-purchase-join-class-english').modal('show');
                }
            });
            $("#sel-sat-months").change(function () {
                total_purchase_join_class_english();
            });
            $('#code-active').click (function (e){
                e.preventDefault();
                $('#active_code').modal('show');
            });
        });
    })(jQuery);
</script>
<?php
MWHtml::ik_site_messages();
get_footer()
?>