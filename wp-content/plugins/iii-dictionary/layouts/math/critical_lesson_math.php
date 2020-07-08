<?php
$link_list_group = get_option_name_link();
$price_course_at_math = mw_get_option('price-course-at-math');
//some function at home
if (!empty($_POST['data-join'])) {
    MWDB::lang_join_group($_POST);
}
if(isset($_POST['credit-code']))
	{
		if($cid = MWDB::add_credit_code($_POST))
		{
			
		}
	}
?>
<style type="text/css">
    .popup-info-dialog {
        /*    width:200px;
            height:100px;*/
        position:absolute;
        /*    top:auto;
            left:auto;
            margin:auto;  [-(height/2)px 0 0 -(width/2)px] */
        display:none;
    }
</style>
<?php
get_header('math');

$URL = $_SERVER['REQUEST_URI'];
$segment = explode('/', $URL);
if (isset($segment) && $segment[2] == 'mathteacher') {
    include 'math_teacher.php';
} else {
    ?>
    <main class="home home-math" id="home">
        <div id="switch-menu" class="css-swith-menu">
            <div class="css-div-center">
                <div class="col-xs-6" id="start-lesson"><a href="<?php echo locale_home_url() . '/?r=critical-lesson-math' ?>" class="a-switch">Critical Lessons!</a><span class="glyphicon glyphicon-arrow-right arrow-right"></span></div>
                <div class="col-xs-6" id="code-active"><a style=" padding-left: 7%; " target="_blank" data-toggle="modal" href="#active_code" class="a-switch">Code Activation</a><span class="glyphicon glyphicon-arrow-right arrow-right"></span></div>
            </div>
        </div>

    <div id="content" class="body-crit-lesson-math">
        <div class="txt-critical-english txt-critical-math-mb">Critical Math Lessons</div>
        <div class="image-math-subject css-image1"></div>
        <?php        
            $_lang = get_short_lang_code();
            $is_math = is_math_panel();
            $current_page = max(1, get_query_var('page'));
            $filter['offset'] = 0;
            $filter['items_per_page'] = 14;
            $filter['group_type'] = GROUP_CLASS;
            $filter['lang'] = is_math_panel() ? get_short_lang_code() . '-math' : get_short_lang_code() . '-en';
            $filter['orderby'] = 'ordering';
            $filter['order-dir'] = 'ASC';
            $homeworkgroup = MWDB::get_groups_home_math($filter);
            ?>
    <form id="lang-form" method="post" action="">
        <div  id="div_hw3" style="margin-bottom: 50px">
            <div id="homeworkcourse" style="overflow: hidden;">
                <div class="homeworkcritical-online can-scroll" style="height:415px;" >
                    <div class="width-table-display">
                    <table class="table table-striped table-condensed ik-table1 table-critical-lesson can-scroll table-scroll scroll-fix-head-new " id="homeworkcritical" >
                        <thead class="homeworkcritical" style="font-size: 15px;">
                            <tr style="height: 40px;">
                                <th class="text-color-custom-1" style=""><?php _e('Name', 'iii-dictionary') ?></th>
                                <th class="text-color-custom-1" style=""><?php _e('Price', 'iii-dictionary') ?></th>
                                <th class="text-color-custom-1" style=""><?php _e('Course Name', 'iii-dictionary') ?></th>
                                <th class="text-color-custom-1" style=""><?php _e('Detail', 'iii-dictionary') ?></th>
                                <th class="text-color-custom-1 pad-left-critical-mb" style=""><?php _e('Join', 'iii-dictionary') ?></th>
                                <th class="text-color-custom-1" style=""><?php _e('No. of W.S.', 'iii-dictionary') ?></th>
                            </tr>
                        </thead>
                        <tfoot>
                        </tfoot>
                        <tbody style="height: 376px !important;">
                            <?php 
                            if (empty($homeworkgroup->items)) : ?>
                                <tr style="width: 100%;">
                                    <td colspan="6" style=""><?php _e('Haven\'t group', 'iii-dictionary') ?></td>
                                    <td></td>
                                </tr>
                                <?php for($i=1;$i<10;$i++){ ?>
                                <tr style="">
                                    <td colspan="6"></td>
                                    <td></td>
                                </tr>
                                <?php } ?>
                                <?php
                            else :
                                foreach ($homeworkgroup->items as $item) :
                                    $get_stg = MWDB::get_something_in_group($item->id);
//                                    var_dump($get_stg->step_of_user);
                                    ?>
                                    <tr>
                                        <td style=""><div class="width-columns-name-group"><?php echo $item->name ?></div></td>
                                        <td style=""><?php
                                            if ($item->price == 0) {
                                                echo 'FREE';
                                            } else {
                                                echo '$' . $item->price;
                                            }
                                            ?></td>

                                        <td style=""><div class="width-columns-name-worksheet"><?php echo $item->content ?></div></td>
                                        <td style=""><div><a href="#" class="bold-font btn btn-default btn-block btn-tiny css-text-left grey btn-a-link <?php
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
                                            <td style="">
                                                <?php 
//                                                        var_dump($get_stg->step['id']);
                                                if (empty($get_stg->step_of_user) && $item->price != 0 || !is_user_logged_in()) { ?>
                                                    <a href="#" data-name="<?php echo $item->name ?>" data-free="<?php echo ( $item->price == 0 ) ? '1' : '0' ?>" data-price="<?php echo $item->price;?>" data-jcid="<?php echo $item->id ?>" class="bold-font btn btn-default css-text-left btn-block css-color-006fbd btn-tiny btn-a-link join-class-lang-btn"><?php _e('JOIN', 'iii-dictionary') ?></a>
                                                    <?php } else { ?>
                                                    <a href="<?php echo locale_home_url() ?>?r=online-learning&math&lvid=<?php echo $item->id?>" class="bold-font btn btn-default btn-block btn-tiny css-color-006fbd btn-a-link css-text-left prevent-detail-btn-<?php echo $is_math ? 'math' : 'english' ?> "><?php _e('START', 'iii-dictionary') ?></a>
                                                <?php } ?>
                                            </td>
                                        <td style=""><div ><?php
                                                $count = MWDB::get_count_worksheets_group($item->id);
                                                $count_complete = MWDB::get_count_worksheets_completeed_group($item->id);
                                                echo $count[0]->count;
                                                ?></div>
                                        </td>
                                        
                                        </tr>
                                        <?php
                                    endforeach;
                                    if(count($homeworkgroup->items)<10)?>
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
<div id="require-pay-modal" class="modal fade">
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


<div id="new-to-our-product-dialog-math" class="modal fade modal-white" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="close2"></a>
            </div>
            <div class="modal-body visible-md visible-lg">
                <ul>
                    <li><a class="view-sub-modal" href="#" data-img="<?php echo get_info_tab_cloud_url_math('MPopup_info_3.jpg') ?>"><?php _e('Designed to support self-study with tutorials and plenty of excercises', 'iii-dictionary') ?></a></li>
                    <li><a class="view-sub-modal" href="#" data-img="<?php echo get_info_tab_cloud_url_math('MPopup_info_4.jpg') ?>"><?php _e('Start at any level from 1 to 12 grade', 'iii-dictionary') ?></a></li>
                    <li><a class="view-sub-modal" href="#" data-img="<?php echo get_info_tab_cloud_url_math('MPopup_info_5.jpg') ?>"><?php _e('Complete Preparation for SAT I and II', 'iii-dictionary') ?></a></li>
                    <li><a class="view-sub-modal" href="#" data-img="<?php echo get_info_tab_cloud_url_math('MPopup_info_6.jpg') ?>"><?php _e('Increase the confidence in Math in school', 'iii-dictionary') ?></a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div id="why-merriam-dialog-math" class="modal fade modal-white" aria-hidden="true"  >
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
<div id="popup-ikmath-dialog" class="modal fade modal-white" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="close-transp"></a>
            </div>
        </div>
    </div>
</div>
<div id="popup-tutor-dialog" class="modal fade modal-white" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="close-transp"></a>
                <a href="http://ikteachonline.com" target="_blank" class="movehttp"></a>
            </div>
        </div>
    </div>
</div>
<div id="popup-sat-dialog" class="modal fade modal-white" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" >
            <div class="modal-body" style="height: 100%">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="close-transp"></a>
                <p class="p-start-sat-i"><a class="a-link-start" href="<?php echo site_math_url(); ?>/?r=sat-preparation/sat1prep&client=math-sat1"></a></p>
                <p class="p-start-sat-ii"><a class="a-link-start" href="<?php echo site_math_url(); ?>/?r=sat-preparation/sat2prep&client=math-sat2"></a></p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-purple modal-large" id="class-detail-modal" tabindex="-1" role="dialog" aria-hidden="true">
     <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                 <span style="right: 3%;padding-top: 3%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3 class="modal-title" id="myModalLabel"><?php _e('Class Detail', 'iii-dictionary') ?></h3>
            </div>
           <div class="modal-body body-custom" style="max-height: calc(100vh - 210px); overflow-y: auto;">
                <div id="modal-body-detail" ></div>
           </div>

        </div>
    </div>
</div>

<div id="popup-library-dialog" class="modal fade modal-white modal-no-padding popup-info-dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="close-transp"></a>
            </div>
        </div>
    </div>
</div>
<div id="popup-level-dialog" class="modal fade modal-white modal-no-padding popup-info-dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="close-transp"></a>
            </div>
        </div>
    </div>
</div>
<div id="popup-prep-dialog" class="modal fade modal-white modal-no-padding popup-info-dialog" tabindex="-1" data-focus-on="input:first" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="close-transp"></a>
            </div>
        </div>
    </div>
</div>
<div id="popup-confident-dialog" class="modal fade modal-white modal-no-padding popup-info-dialog " tabindex="-1" data-focus-on="input:first" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="close-transp"></a>
            </div>
        </div>
    </div>
</div>
<div id="popup-tool-dialog" class="modal fade modal-white modal-no-padding popup-info-dialog"  tabindex="-1" data-focus-on="input:first" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body" style="height: 100%">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="close-transp"></a>
                <a href="#popup-library-dialog" data-toggle="modal" id="a-library" class="a-tool"></a>
                <a href="#popup-level-dialog"  data-toggle="modal" id="a-level" class="a-tool"></a>
                <a href="#popup-prep-dialog"  data-toggle="modal" id="a-sat" class="a-tool"></a>
                <a href="#popup-confident-dialog" data-toggle="modal" id="a-confident" class="a-tool"></a>
            </div>
        </div>
    </div>
</div>
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
<!-- dialog active code -->
<div id="active_code" class="modal fade modal-red-brown">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black" style="background: #fff; margin-top: 47px;">
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
                                <input type="text" class="form-control" name="credit-code" id="no-of-points" min="1" placeholder="Enter Code Here" style="padding-left: 5%">
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
                        <div><label class="text-points"><span class="css-txt-left"><?php _e('1 point is equivalent to 1 dollar. You can earn poin by selling your worksheet to the teachers.', 'iii-dictionary') ?></span></label></div>
                        <div><label class="text-points"><span class="css-txt-left"><?php _e('You can earn points by editing and grading writing assignment students submitted.', 'iii-dictionary') ?></span></label></div>
                </div>			
            </form>
        </div>
    </div>
</div>

<script>
    var pcm = <?php echo (int)$price_course_at_math ?>;
    (function ($) {
        $(function () {
             $('.check-calendar1').datepicker({         
                inline: true,            
                showOtherMonths: true, 
                beforeShow: function(elem, dp) { 
                }
            });
            $(".view-sub-modal").click(function (e) {
                e.preventDefault();
                var _img = $("#popup-info-img");
                var _m = $("#popup-info-dialog");
                _img.attr("src", $(this).attr("data-img")).load(function () {
                    _m.find(".modal-dialog").width(this.width);
                });
                $('#new-to-our-product-dialog-math')
                        .one('show.bs.modal', function () {

                            $(this).off('hidden.bs.modal');
                            _m.modal();
                            _m.style.display = 'block';
                        }).modal('show');

            });

            $("#popup-info-dialog").on("hidden.bs.modal", function () {
                $("#new-to-our-product-dialog-math").modal();
            });
            $("#a-library").click(function (e) {
                $("#popup-tool-dialog").modal('hide');
            });
            $("#a-level").click(function (e) {
                $("#popup-tool-dialog").modal('hide');
            });
            $("#a-sat").click(function (e) {
                $("#popup-tool-dialog").modal('hide');
            });
            $("#a-confident").click(function (e) {
                $("#popup-tool-dialog").modal('hide');
            });
            $("#popup-library-dialog").on("hidden.bs.modal", function () {
                $('.body-math').css("padding-right", "0px");
                $("#popup-tool-dialog").modal();
            });
            $("#popup-level-dialog").on("hidden.bs.modal", function () {
                $('.body-math').css("padding-right", "0px");
                $("#popup-tool-dialog").modal();
            });
            $("#popup-prep-dialog").on("hidden.bs.modal", function () {
                $('.body-math').css("padding-right", "0px");
                $("#popup-tool-dialog").modal();
            });
            $("#popup-confident-dialog").on("hidden.bs.modal", function () {
                $('.body-math').css("padding-right", "0px");
                $("#popup-tool-dialog").modal();
            });
//             $('.purchase-join-class-math').click (function (e){
//                e.preventDefault();
//                if (!isuserloggedin) {
//                } else {
//                    var name = $(this).attr("data-name");
//                    $("#selected-class").text(name);
//                    $('#popup-lesson-dialog').modal('hide');
//                    total_purchase_join_class();
//                    $('#modal-purchase-join-class-math').modal('show');
//                }
//            });
             $('.not-join').click(function (e) {
                var modal = $("#require-modal");
                
                    e.preventDefault();
                    var modal = $("#class-detail-modal");
                    count=$(this).next().html();
                    if($(count).filter("br").length > 13){
                        $('.modal-body').css('height','400px');
                        modal.find("#modal-body-detail").html($(this).next().html());
                        modal.modal();
                     modal.find('#modal-body-detail').css('height','350px')
                    }else{
                        modal.find("#modal-body-detail").html($(this).next().html());
                        modal.modal();
                    }
                    
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
                            $('#require-pay-modal').modal();
                            var price =$(this).attr("data-price");
                            $('#total-amount-sat').val(price);
                            return false;
                        }
                    }

                });
            function total_purchase_join_class() {
                var months = parseInt($("#sel-sat-months").val());
                $("#total-amount-sat").text(months * pcm);
            }
            $("#sel-sat-months").change(function () {
                total_purchase_join_class();
            });  
            
        });
    })(jQuery);
</script>
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
        </form>
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
<?php
MWHtml::ik_site_messages();
get_footer('math')
?>