<?php
$user = wp_get_current_user();
$user_groups = MWDB::get_current_user_groups();
$user_groups_joined = MWDB::get_join_user_groups();
$current_user_id = get_current_user_id();
$link_url = ik_link_mw_apps();
$teacher_tool_price = mw_get_option('teacher-tool-price');
$math_teacher_tool_price = mw_get_option('math-teacher-tool-price');
$self_study_price = mw_get_option('self-study-price');
$self_study_price_math = mw_get_option('math-self-study-price');
$dictionary_price = mw_get_option('dictionary-price');
$status_student = '';
$is_math_panel = is_math_panel();
foreach ($user_groups as $group) :
    if (!is_null($group->expired_date)) :
        if (strtotime(date('Y-m-d')) < strtotime($group->expired_date)) {
            $status_student = 'Self-study student';
        }
    endif;
endforeach;

$group_name = array();
$group_id = array();
for ($i = 0; $i < count($user_groups_joined); $i++) {
    $group_name[$i] = $user_groups_joined[$i]->name;
    $group_id[$i] = $user_groups_joined[$i]->id;
}


if ($status_student == '') {
    $status_student = 'Not subscribed student';
}
$is_math_panel = is_math_panel();
$_page_title = __('My Account', 'iii-dictionary');

if (isset($_POST['save'])) {
    if (MWDB::update_user($user)) {
        wp_redirect(locale_home_url() . '/?r=my-account');
        exit;
    }
}
if (isset($_POST['credit-code'])) {
//    var_dump($_POST);die;
    if ($cid = MWDB::add_credit_code($_POST)) {
        wp_redirect(locale_home_url() . '/?r=my-account&cid=' . $cid);
        exit;
    }
}
$link_current = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
if (strpos($link_current, 'my-account') !== false) {
    $page_my_account = 1;
}
?>
<?php if (!$is_math_panel) : ?>
    <?php get_dict_header($_page_title) ?>
<?php else : ?>
    <?php get_math_header($_page_title, 'red-brown') ?>
<?php endif ?>

<?php get_dict_page_title($_page_title, '', '', array(), get_info_tab_cloud_url('Popup_info_9.jpg')) ?>

<h3 class="txt-acount-info"><?php _e('Account information', 'iii-dictionary'); ?></h3>
<div class="row">
    <div id="my-acount-col5">
        <div class="box-gray box-10-30">
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-striped ik-table1 ik-table-break-all text-center table-margin-bottom width-110-mb">
                        <thead>
                            <tr>
                                <th class="title-green css-width-15"><?php _e('Name: ', 'iii-dictionary') ?></th>
                                <th class="css-text-name">
                                    <?php global $current_user;
                                    get_currentuserinfo();
                                    echo  $current_user->display_name;
                                    ?>
                                </th>
                            </tr>
                            <tr>
                                <td class="content-table-noboder" colspan="2" style="width: 100%;border-top:0px;">
                                    <a href="#change-login-info" class="btn-custom" target="_blank" data-toggle="modal"><?php _e('Change Info', 'iii-dictionary') ?></a>
                                </td>  
                            </tr> 
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <div class="box-gray box-10-30" style="margin-top: 15px;">
            <div class="row">
                <form method="post" action="<?php echo locale_home_url() ?>/?r=my-account">

                    <div class="col-sm-12">
                        <table class="table table-striped table-condensed table-no-boder ik-table-break-all text-center table-margin-bottom " style="border: none !important;">
                            <tbody> 
                                    <tr>
                                        <td class="title-green point-balance"><?php _e('Point ', 'iii-dictionary') ?><span class="css-balance"><?php _e('Balance:', 'iii-dictionary')?></span></td>                                     
                                        <td class="content-table font-bold" style="width: 50%;padding-top: 3px;padding-left: 0px; "><span class="css-get-points"><?php _e(ik_get_user_points($user->ID)) ?></span><span><?php echo "(USD)"?></span></td>
                                    </tr>
                                <tr>
                                    <td class="content-table-noboder" colspan="2">
                                        <a href="#purchase-points-dialog" class="btn-custom" target="_blank" data-toggle="modal">Purchase More Points</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="content-table-noboder color-gray838383" colspan="2"><?php _e('Enter a Credit Code ( if you', 'iii-dictionary') ?><span class="css-balance"><?php _e(' have any )', 'iii-dictionary')?></span></td>
                                </tr>
                                <tr>
                                    <td class="content-table-noboder" colspan="2"><input type="text" class="enter-code" name="credit-code"  placeholder="Enter Code Here"/></td>
                                </tr>
                                <tr>
                                    <td class="content-table-noboder" colspan="2">
                                        <button class="btn-custom"><?php _e('Apply', 'iii-dictionary') ?></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <div id="my-acount-col7" >
        <div class="box-gray list-group-subscribe1" style="height: 410px">
            <div class="row">
                <div class="col-sm-12 css-pad-0">
                    <p class="p-all-sub-group padd-left-20"><?php _e('All My Subscribed Groups', 'iii-dictionary') ?></p>
                </div>
                <div class="col-sm-12 css-pad-0">
                    <table class="table  table-condensed  ik-table-break-all text-center scroll-fix-head" id="homeworkcritical">
                        <thead style="background: #838383">
                            <tr>
                                <th class="text-color-custom-1" style="text-align: left;width: 20%;padding-left: 20px"><?php _e('Group Name', 'iii-dictionary') ?></th>
                                <th class="text-color-custom-1" style="text-align: left;"></th>
                            </tr>
                        </thead>
                        <tbody class="css-height-mb-my-sub">
                            <?php
                                if (!is_user_logged_in()) { 
                                    for ($i = 0; $i < 16; $i++) { ?>
                                    <tr ><td style="height : 35px;width: 1% !important" colspan="5"></td></tr>
                                <?php }}
                                else {?>
                                <?php for ($j = 0; $j < count($group_name); $j++) { ?>
                                <tr>
                                    <!--<td style="text-align: center; width: 5%;"><?php echo ($j + 1) . '.'; ?></td>-->
                                    <td  style="text-align:left; width: 45%;padding-left: 20px"><?php _e($group_name[$j], 'iii-dictionary') ?></td>
                                    <td><button type="button" data-name ="<?php _e($group_name[$j], 'iii-dictionary') ?>" data-id="<?php echo $group_id[$j] ?>" class="leave-group btn btn-default btn-block btn-tiny grey btn-a-link leave-group css-link"><?php _e('LEAVE GROUP', 'iii-dictionary') ?></button></td>
                                </tr>
                                <?php } ?>
                            <?php
                            if (count($group_name) < 16) {
                                for ($j = count($group_name); $j < 16; $j++) {
                                    ?>
                                    <tr>
                                        <td colspan="3" style="height : 32px"></td>
                                    </tr>
                                    <?php
                                }
                            }
                            if (count($group_name) == 0) {
                                for ($j = 0; $j < 16; $j++) {
                                    ?>
                                    <tr>
                                        <td colspan="3" style="height : 32px;width: 1% !important;"></td>
                                    </tr>
                                    <?php
                                    }
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="css-padd-lr7" style="padding-top:38px">
    <h3 id="nav-purchase-subscriptions" style="margin: 0 0 10px 0;"><?php _e('Purchased Subscriptions', 'iii-dictionary'); ?></h3>
    <ul class="nav nav-tabs" id="account-purchase">
        <li id="li-purchase-1" ><a data-toggle="tab" class="a-custom-color css-padd-pucharse" href="#pur-substatus"><?php _e('My Subscription Status', 'iii-dictionary'); ?></a></li>
        <li id="li-purchase-2" ><a data-toggle="tab" class="a-custom-color" href="#pur-history"><?php _e('Purchase History', 'iii-dictionary'); ?></a></li>
        <li id="li-purchase-hidden" class="active icon-tab-menu-math css-position-ic"><a data-toggle="tab" class="icon-hidden" id="hide-ik" href="#pur-1"></a></li>
        <li id="li-purchase-hidden-1"></li>
    </ul><div class="tab-content can-scroll scroll-tab-purchase" style="margin-left: 1px;">
        <div id="pur-substatus" class="tab-pane fade ">
            <div class="homeworkcritical-online" style="height: 521px;" >
                <?php
                $filter['offset'] = 0;
                $filter['items_per_page'] = 99999999;
                if(is_math_panel()){
                    MWHtml::load_subscription_status(MWDB::get_user_subscriptions_math($current_user_id, ''));
                }else{
                    MWHtml::load_subscription_status(MWDB::get_user_subscriptions_english($current_user_id, ''));
                }
                ?>
            </div>
        </div>
        <div id="pur-history" class="tab-pane fade">
            <div class="homeworkcritical-online" style="height:500px;" >
                <?php MWHtml::load_purchase_history(MWDB::get_user_purchase_history($current_user_id)); ?>
            </div>
        </div>
        <div id="pur-1" class="tab-pane fade in active"></div>
        <div id="pur-2" class="tab-pane fade"></div>
    </div>
</div>
<div class="padding-top-60px">
    <h3 class="css-padd-left7"style="margin: 0 0 10px 0;"><?php _e('Purchase Subscriptions (For yourself or as a Gift)', 'iii-dictionary'); ?></h3>
        <?php if ($is_math_panel) { ?>
        <div class="row boder-bottom-1px border-top-1px sub-pur">
            <div class="col-xs-8 col-sm-5 col-md-6 col-lg-6 text-sub-pur"><?php _e('Math Self-study', 'iii-dictionary'); ?></div>
            <div class="col-xs-4 col-sm-3 col-md-2  col-lg-2 "><span class="more-detai"></span><a href="#self-study-detail" class="a-link-pur" target="_blank" data-toggle="modal"><?php _e('More Detail', 'iii-dictionary'); ?></a></div>
            <div class="col-xs-12 col-sm-4 col-md-4  col-lg-4 padding-top15"><a href="" data-subscription-type="9" class="btn-custom choose-sub-btn" target="_blank" data-toggle="modal"><?php _e('Purchase Now', 'iii-dictionary'); ?></a></div>
        </div>
        <div class="row boder-bottom-1px sub-pur">
            <div class="col-xs-8 col-sm-5 col-md-6 col-lg-6 text-sub-pur"><?php _e('Teacher\'s Homework Tool', 'iii-dictionary'); ?></div>
            <div class="col-xs-4 col-sm-3 col-md-2  col-lg-2 "><span class="more-detai"></span><a href="#teacher-homework-tool-dialog" class="a-link-pur" target="_blank" data-toggle="modal"><?php _e('More Detail', 'iii-dictionary'); ?></a></div>
            <div class="col-xs-12 col-sm-4 col-md-4  col-lg-4 padding-top15"><button type="button" data-subscription-type="6" class="btn-custom choose-sub-btn" ><?php _e('Purchase Now', 'iii-dictionary'); ?></button></div>
        </div>
        <?php } else { ?>
        <div class="row boder-bottom-1px border-top-1px sub-pur">
            <div class="col-xs-8 col-sm-5 col-md-6 col-lg-6 text-sub-pur"><?php _e('English Self-study', 'iii-dictionary'); ?></div>
            <div class="col-xs-4 col-sm-3 col-md-2  col-lg-2 "><span class="more-detai"></span><a href="#self-study-detail" class="a-link-pur" target="_blank" data-toggle="modal"><?php _e('More Detail', 'iii-dictionary'); ?></a></div>
            <div class="col-xs-12 col-sm-4 col-md-4  col-lg-4 padding-top15"><a href="" data-subscription-type="5" class="btn-custom choose-sub-btn" target="_blank" data-toggle="modal"><?php _e('Purchase Now', 'iii-dictionary'); ?></a></div>
        </div>
        <div class="row boder-bottom-1px sub-pur">
            <div class="col-xs-8 col-sm-5 col-md-6 col-lg-6 text-sub-pur"><?php _e('Teacher\'s Homework Tool', 'iii-dictionary'); ?></div>
            <div class="col-xs-4 col-sm-3 col-md-2  col-lg-2 "><span class="more-detai"></span><a href="#teacher-homework-tool-dialog" class="a-link-pur" target="_blank" data-toggle="modal"><?php _e('More Detail', 'iii-dictionary'); ?></a></div>
            <div class="col-xs-12 col-sm-4 col-md-4  col-lg-4 padding-top15"><button type="button" data-subscription-type="1" class="btn-custom choose-sub-btn" ><?php _e('Purchase Now', 'iii-dictionary'); ?></button></div>
        </div>
        <?php } ?>
    
    <?php if ($is_math_panel) { ?>
        <div class="row boder-bottom-1px sub-pur">
            <div class="col-xs-8 col-sm-5 col-md-6 col-lg-6 text-sub-pur"><?php _e('ikMath Course', 'iii-dictionary'); ?></div>
            <div class="col-xs-4 col-sm-3 col-md-2  col-lg-2 "><span class="more-detai"></span><a href="#ikmath-classes" class="a-link-pur" target="_blank" data-toggle="modal"><?php _e('More Detail', 'iii-dictionary'); ?></a></div>
            <div class="col-xs-12 col-sm-4 col-md-4  col-lg-4 padding-top15"><button type="button" data-type="38" data-subscription-type="12" data-sat-class="IK Math classes" class="btn-custom choose-sub-btn" ><?php _e('Purchase Now', 'iii-dictionary'); ?></button></div>
        </div>
        <div class="row boder-bottom-1px sub-pur">
            <div class="col-xs-8 col-sm-5 col-md-6 col-lg-6 text-sub-pur"><?php _e('SAT Preparation', 'iii-dictionary'); ?></div>
            <div class="col-xs-4 col-sm-3 col-md-2  col-lg-2 "><span class="more-detai"></span><a href="#sat-i-preparation" class="a-link-pur" target="_blank" data-toggle="modal"><?php _e('More Detail', 'iii-dictionary'); ?></a></div>
            <div class="col-xs-12 col-sm-4 col-md-4  col-lg-4 padding-top15"><a href="" data-sat-class="SAT Preparation" data-subscription-type="7" data-type="9" class="btn-custom choose-sub-btn" target="_blank" data-toggle="modal"><?php _e('Purchase Now', 'iii-dictionary'); ?></a></div>
        </div>
        <div class="row boder-bottom-1px sub-pur">
            <div class="col-xs-8 col-sm-5 col-md-6 col-lg-6 text-sub-pur"><?php _e('SAT Simulation Test (New SAT Test)', 'iii-dictionary'); ?></div>
            <div class="col-xs-4 col-sm-3 col-md-2  col-lg-2 "><span class="more-detai"></span><a href="#sat-ii-simulated-test-new" class="a-link-pur" target="_blank" data-toggle="modal"><?php _e('More Detail', 'iii-dictionary'); ?></a></div>
            <div class="col-xs-12 col-sm-4 col-md-4  col-lg-4 padding-top15"><button type="button" data-sat-class="SAT I simulated test" data-subscription-type="7" data-type="10" class="btn-custom choose-sub-btn" ><?php _e('Purchase Now', 'iii-dictionary'); ?></button></div>
        </div>
        <div class="row boder-bottom-1px sub-pur">
            <div class="col-xs-8 col-sm-5 col-md-6 col-lg-6 text-sub-pur"><?php _e('SAT 2 Preparation', 'iii-dictionary'); ?></div>
            <div class="col-xs-4 col-sm-3 col-md-2  col-lg-2 "><span class="more-detai"></span><a href="#sat-ii-preparation" class="a-link-pur" target="_blank" data-toggle="modal"><?php _e('More Detail', 'iii-dictionary'); ?></a></div>
            <div class="col-xs-12 col-sm-4 col-md-4  col-lg-4 padding-top15"><button type="button" data-sat-class="SAT 2 Preparation" data-subscription-type="8" data-type="15" class="btn-custom choose-sub-btn" ><?php _e('Purchase Now', 'iii-dictionary'); ?></button></div>
        </div>
        <div class="row boder-bottom-1px sub-pur">
            <div class="col-xs-8 col-sm-5 col-md-6 col-lg-6 text-sub-pur"><?php _e('SAT 2 Simulation Test', 'iii-dictionary'); ?></div>
            <div class="col-xs-4 col-sm-3 col-md-2  col-lg-2 "><span class="more-detai"></span><a href="#sat-ii-simulated-test" class="a-link-pur" target="_blank" data-toggle="modal"><?php _e('More Detail', 'iii-dictionary'); ?></a></div>
            <div class="col-xs-12 col-sm-4 col-md-4  col-lg-4 padding-top15"><button type="button" data-sat-class="SAT II simlated test" data-subscription-type="8" data-type="16" class="btn-custom choose-sub-btn" ><?php _e('Purchase Now', 'iii-dictionary'); ?></button></div>
        </div>
        <div class="row boder-bottom-1px sub-pur" style="border-bottom: none">
            <div class="col-xs-8 col-sm-5 col-md-6 col-lg-6 text-sub-pur "><?php _e('Purchase More Points?', 'iii-dictionary'); ?></div>
            <div class="col-xs-4 col-sm-3 col-md-2  col-lg-2 "><span class="more-detai"></span><a href="#purchase-points" class="a-link-pur" target="_blank" data-toggle="modal"><?php _e('More Detail', 'iii-dictionary'); ?></a></div>
            <div class="col-xs-12 col-sm-4 col-md-4  col-lg-4 padding-top15"><button type="button" data-subscription-type="4" class="btn-custom choose-sub-btn" ><?php _e('Purchase Now', 'iii-dictionary'); ?></button></div>
        </div>
        <?php } else { ?>
        <div class="row boder-bottom-1px sub-pur">
            <div class="col-xs-8 col-sm-5 col-md-6 col-lg-6 text-sub-pur"><?php _e('Merriam-Webster Dictionary', 'iii-dictionary'); ?></div>
            <div class="col-xs-4 col-sm-3 col-md-2  col-lg-2 "><span class="more-detai"></span><a href="#merriam-webster-dictionary" class="a-link-pur" target="_blank" data-toggle="modal"><?php _e('More Detail', 'iii-dictionary'); ?></a></div>
            <div class="col-xs-12 col-sm-4 col-md-4  col-lg-4 padding-top15"><button type="button" data-subscription-type="2" class="btn-custom choose-sub-btn" ><?php _e('Purchase Now', 'iii-dictionary'); ?></button></div>
        </div>
        <div class="row boder-bottom-1px sub-pur">
            <div class="col-xs-8 col-sm-5 col-md-6 col-lg-6 text-sub-pur"><?php _e('SAT Preparation - Grammar Review', 'iii-dictionary'); ?></div>
            <div class="col-xs-4 col-sm-3 col-md-2  col-lg-2 "><span class="more-detai"></span><a href="#grammar-review" class="a-link-pur" target="_blank" data-toggle="modal"><?php _e('More Detail', 'iii-dictionary'); ?></a></div>
            <div class="col-xs-12 col-sm-4 col-md-4  col-lg-4 padding-top15"><a href="" data-sat-class="Grammar Review" data-subscription-type="3" data-type="1"class="btn-custom choose-sub-btn" target="_blank" data-toggle="modal"><?php _e('Purchase Now', 'iii-dictionary'); ?></a></div>
        </div>
        <div class="row boder-bottom-1px sub-pur">
            <div class="col-xs-8 col-sm-5 col-md-6 col-lg-6 text-sub-pur"><?php _e('SAT Preparation - Writing Practice', 'iii-dictionary'); ?></div>
            <div class="col-xs-4 col-sm-3 col-md-2  col-lg-2 "><span class="more-detai"></span><a href="#writing-practice" class="a-link-pur" target="_blank" data-toggle="modal"><?php _e('More Detail', 'iii-dictionary'); ?></a></div>
            <div class="col-xs-12 col-sm-4 col-md-4  col-lg-4 padding-top15"><button type="button" data-sat-class="Writing Practice" data-subscription-type="3" data-type="2" class="btn-custom choose-sub-btn" ><?php _e('Purchase Now', 'iii-dictionary'); ?></button></div>
        </div>
        <div class="row boder-bottom-1px sub-pur">
            <div class="col-xs-8 col-sm-5 col-md-6 col-lg-6 text-sub-pur"><?php _e('SAT Simulation Test', 'iii-dictionary'); ?></div>
            <div class="col-xs-4 col-sm-3 col-md-2  col-lg-2 "><span class="more-detai"></span><a href="#sat-practice-test" class="a-link-pur" target="_blank" data-toggle="modal"><?php _e('More Detail', 'iii-dictionary'); ?></a></div>
            <div class="col-xs-12 col-sm-4 col-md-4  col-lg-4 padding-top15"><button type="button"  data-subscription-type="3"  data-sat-class="SAT practice Test" data-type="3" class="btn-custom choose-sub-btn"><?php _e('Purchase Now', 'iii-dictionary'); ?></button></div>
        </div>
        <div class="row boder-bottom-1px sub-pur">
            <div class="col-xs-8 col-sm-5 col-md-6 col-lg-6 text-sub-pur "><?php _e('Purchase More Points?', 'iii-dictionary'); ?></div>
            <div class="col-xs-4 col-sm-3 col-md-2  col-lg-2 "><span class="more-detai"></span><a href="#purchase-points" class="a-link-pur" target="_blank" data-toggle="modal"><?php _e('More Detail', 'iii-dictionary'); ?></a></div>
            <div class="col-xs-12 col-sm-4 col-md-4  col-lg-4 padding-top15"><button type="button" data-subscription-type="4" class="btn-custom choose-sub-btn" ><?php _e('Purchase Now', 'iii-dictionary'); ?></button></div>
        </div>
        <?php } ?>
    
</div>
<?php if (!$is_math_panel) { ?>
    <div class="padding-top-60px">
        <h3 style="margin: 0 0 10px 0;"><?php _e('Download', 'iii-dictionary'); ?></h3>
        <div class="row sub-pur-dictionary">
            <div class="col-xs-8 col-sm-4 col-md-4 col-lg-4 text-sub-pur "><?php _e('Merriam-Webster Dictionary', 'iii-dictionary'); ?></div>
            <div class="col-xs-4 col-sm-2 col-md-2  col-lg-2">
                <span class="more-detai"></span><a href="#download-dictionary" class="a-link-pur" target="_blank" data-toggle="modal">More Detail</a>
            </div>
            <div class="col-xs-6 col-sm-3 col-md-3  col-lg-3 padding-top15"><a class="btn-custom" href="<?php echo $link_url['mac']; ?>"><?php _e('MAC', 'iii-dictionary') ?></a></div>
            <div class="col-xs-6 col-sm-3 col-md-3  col-lg-3 padding-top15"><a class="btn-custom" href="<?php echo $link_url['win']; ?>"><?php _e('WINDOWS', 'iii-dictionary') ?></a></div>
        </div>
    </div>
<?php } ?>

<!-- dialog purchase point -->

<div id="purchase-points-dialog" class="modal fade modal-red-brown">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div id="close-pur" class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Purchase Points', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo locale_home_url() ?>/?r=payments" onsubmit="return isValidForm()">
                <input type="hidden" name="sub-type" value="4">
                <div class="modal-body body-custom">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="font-dialog"><?php _e('Number of Points', 'iii-dictionary') ?></label>
                                <input type="number" class="form-control" name="no-of-points" id="no-of-points" min="1" >
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="box-gray-dialog">
                                <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color708b23">$ <span id="total-amount-points" class="color708b23">0</span></span>
                            </div>
                        </div>
                    </div>				
                </div>
                <div class="modal-footer footer-custom">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button type="submit" name="add-to-cart" class="btn-custom confirm check-no-point type-payment"><?php _e('Check out', 'iii-dictionary') ?></button>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1 secondary"><?php _e('Cancel', 'iii-dictionary') ?></a>
                            </div>
                        </div>
                    </div>
                    <div class="footer-dialog-purchase">
                        <span class="span-purchase-first"></span><p class="p-purchase-first">WHAT IS POINTS?</p>
                        <span class="span-purchase"></span><p class="p-purchase">Points are required to purchase worksheet that you can use for the homework assignment.</p>
                        <span class="span-purchase"></span><p class="p-purchase">1 point is equivalent to 1 dollar. You can earn points by selling your worksheet to other teachers.</p>
                        <span class="span-purchase"></span><p class="p-purchase">You can earn points by editing and grading writing assignment students submitted.</p>
                    </div>
                </div>			
            </form>
        </div>
    </div>
</div>

<!-- dialog Change login information -->

<div id="change-login-info" class="modal fade modal-red-brown">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Change Login Info', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo locale_home_url() ?>/?r=my-account">
                <div class="modal-body body-custom">
                    <div class="row padding-dialog">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="email" class="font-dialog"><?php _e('Current e-mail address', 'iii-dictionary') ?></label>
                                <input type="text" class="form-control" id="email" name="email" value="<?php echo $user->user_email ?>">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="new-email" class="font-dialog"><?php _e('New e-mail address', 'iii-dictionary') ?></label>
                                <input type="text" class="form-control" id="new-email" name="new-email" value="" placeholder="<?php _e('unchanged', 'iii-dictionary') ?>">
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="new-password" class="font-dialog"><?php _e('New password', 'iii-dictionary') ?></label>
                                <input type="password" class="form-control" id="new-password" name="new-password" value="" placeholder="<?php _e('unchanged', 'iii-dictionary') ?>">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="confirm-passwrd" class="font-dialog"><?php _e('Retype new password', 'iii-dictionary') ?></label>
                                <input type="password" class="form-control" id="confirm-passwrd" name="confirm-password" value="">
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="passwrd" class="font-dialog"><?php _e('Current password', 'iii-dictionary') ?></label>
                                <input type="password" class="form-control" id="passwrd" name="old-password" value="" placeholder="**********">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer footer-custom" style="padding: 0px 5% 4% 5%;">
                        <div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn-custom" name="save" id="save-btn" ><?php _e('Save', 'iii-dictionary') ?></button>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1 secondary"><?php _e('Cancel', 'iii-dictionary'); ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- dialog Change personal info -->

<div id="change-personal-info" class="modal fade ">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Change Login Info', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo locale_home_url() ?>/?r=my-account">
                <div class="row padding-dialog">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="current-first-name" class="font-dialog"><?php _e('First Name', 'iii-dictionary') ?></label>
                            <input type="text" class="form-control" id="current-first-name" value="<?php echo $user->first_name ?>">
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="first-name" class="font-dialog"><?php _e('New first Name', 'iii-dictionary') ?></label>
                            <input type="text" class="form-control" id="first-name" name="first-name" value="" placeholder="<?php _e('unchanged', 'iii-dictionary') ?>">
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="current-last-name" class="font-dialog"><?php _e('Last Name', 'iii-dictionary') ?></label>
                            <input type="text" class="form-control" id="current-last-name" value="<?php echo $user->last_name ?>">
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="last-name" class="font-dialog"><?php _e('New last Name', 'iii-dictionary') ?></label>
                            <input type="text" class="form-control" id="last-name" name="last-name" value="" placeholder="<?php _e('unchanged', 'iii-dictionary') ?>">
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="font-dialog"><?php _e('Date of Birth', 'iii-dictionary') ?> <small>(mm/dd/yyyy)</small></label>
                            <div class="row">
<?php
$date_of_birth = array();
if (strtotime($user->date_of_birth)) {
    $date_of_birth = explode('/', $user->date_of_birth);
}
?>
                                <div class="col-xs-4">
                                    <select class="select-box-it form-control" name="birth-m">
                                        <option value="00">mm</option>
<?php for ($i = 1; $i <= 12; $i++) : ?>
                                            <?php $pad_str = str_pad($i, 2, '0', STR_PAD_LEFT) ?>
                                            <option value="<?php echo $pad_str ?>"<?php echo $date_of_birth[0] == $pad_str ? ' selected' : '' ?>><?php echo $pad_str ?></option>
                                        <?php endfor ?>
                                    </select>
                                </div>
                                <div class="col-xs-4">
                                    <select class="select-box-it form-control" name="birth-d">
                                        <option value="00">dd</option>
<?php for ($i = 1; $i <= 31; $i++) : ?>
                                            <?php $pad_str = str_pad($i, 2, '0', STR_PAD_LEFT) ?>
                                            <option value="<?php echo $pad_str ?>"<?php echo $date_of_birth[1] == $pad_str ? ' selected' : '' ?>><?php echo $pad_str ?></option>
                                        <?php endfor ?>
                                    </select>
                                </div>
                                <div class="col-xs-4">
                                    <input class="form-control" name="birth-y" type="text" value="<?php echo $date_of_birth[2] ?>" placeholder="yyyy">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="last-name" class="font-dialog"><?php _e('Language', 'iii-dictionary') ?></label>
<?php
$langs = array(
    'en' => 'English',
    'ja' => '日本語',
    'ko' => '한국어',
    'vi' => 'Tiếng Việt',
    'zh' => '中文',
    'zh-tw' => '中國'
);

//$cur_lang = get_short_lang_code();
?>
                            <select name="language_type" class="form-control language_type">
                            <?php foreach ($langs as $code => $lang) : ?>
                                    <option value="<?php echo $code; ?>"<?php echo $user->language_type == $code ? ' selected' : '' ?>><?php echo $lang ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn-custom" name="save" id="save-btn" ><span class="icon-save"></span><?php _e('Save', 'iii-dictionary') ?></button>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1 secondary"><?php _e('Cancel', 'iii-dictionary'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- view subscription detail  -->
<div id="view-subscription-detail" class="modal fade ">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Subscription Info', 'iii-dictionary') ?></h3>
            </div>
            <div id="view-detail">
            </div>
        </div>
    </div>
</div>

<!-- sat-subscription-dialog -->
<div id="sat-subscription-dialog" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Purchase SAT Subscriptions', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo locale_home_url() ?>/?r=payments">
                <input type="hidden" name="sub-type" id="sat-sub-type" value="0">
                <input type="hidden" name="sat-class" id="sat-class" value="">
                <input type="hidden" name="sat-class-7" id="sat-class-7" value="">
                <input type="hidden" name="sat-class-8" id="sat-class-8" value="">
                <input type="hidden" name="sat-class-12" id="sat-class-12" value="">
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
                            <?php
                            if (!is_math_panel()) :
                                $select_class_options = array(CLASS_SAT1 => __('SAT Test 1', 'iii-dictionary'), CLASS_SAT2 => __('SAT Test 2', 'iii-dictionary'), CLASS_SAT3 => __('SAT Test 3', 'iii-dictionary'),
                                    CLASS_SAT4 => __('SAT Test 4', 'iii-dictionary'), CLASS_SAT5 => __('SAT Test 5', 'iii-dictionary'))
                                ?>

                            <div class="col-sm-12" id="sat-test-block" style="display: none">
                                <div class="form-group">
                                    <label class="font-dialog"><?php _e('Practice Test', 'iii-dictionary') ?></label>
                                    <select class="select-box-it form-control sel-sat-class" id="sat-class-sub">
                                        <?php foreach ($select_class_options as $key => $value) : ?>
                                            <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                        <?php
                        else :
                            $select1_class_options = array(CLASS_MATH_SAT1A => __('SAT 1A', 'iii-dictionary'), CLASS_MATH_SAT1B => __('SAT 1B', 'iii-dictionary'),
                                CLASS_MATH_SAT1C => __('SAT 1C', 'iii-dictionary'), CLASS_MATH_SAT1D => __('SAT 1D', 'iii-dictionary'), CLASS_MATH_SAT1E => __('SAT 1E', 'iii-dictionary'));
                            $select2_class_options = array(CLASS_MATH_SAT2A => __('SAT 2A', 'iii-dictionary'), CLASS_MATH_SAT2B => __('SAT 2B', 'iii-dictionary'),
                                CLASS_MATH_SAT2C => __('SAT 2C', 'iii-dictionary'), CLASS_MATH_SAT2D => __('SAT 2D', 'iii-dictionary'), CLASS_MATH_SAT2E => __('SAT 2E', 'iii-dictionary'));
                            $select3_class_options = array(CLASS_MATH_IK => __('Math Kindergarten', 'iii-dictionary'),
                                CLASS_MATH_IK1 => __('Math Grade 1', 'iii-dictionary'),
                                CLASS_MATH_IK2 => __('Math Grade 2', 'iii-dictionary'),
                                CLASS_MATH_IK3 => __('Math Grade 3', 'iii-dictionary'),
                                CLASS_MATH_IK4 => __('Math Grade 4', 'iii-dictionary'),
                                CLASS_MATH_IK5 => __('Math Grade 5', 'iii-dictionary'),
                                CLASS_MATH_IK6 => __('Math Grade 6', 'iii-dictionary'),
                                CLASS_MATH_IK7 => __('Math Grade 7', 'iii-dictionary'),
                                CLASS_MATH_IK8 => __('Math Grade 8', 'iii-dictionary'),
                                CLASS_MATH_IK9 => __('Math Grade 9', 'iii-dictionary'),
                                CLASS_MATH_IK10 => __('Math Grade 10', 'iii-dictionary'),
                                CLASS_MATH_IK11 => __('Math Grade 11', 'iii-dictionary'),
                                CLASS_MATH_IK12 => __('Math Grade 12', 'iii-dictionary'))
                            ?>

                            <div class="col-xs-12" id="sat-test-i-block" style="display: none">
                                <div class="form-group">
                                    <label class="font-dialog"><?php _e('Simulated Test', 'iii-dictionary') ?></label>
                                    <select class="select-box-it form-control sel-sat-class" id="sat-class-sub">
                                        <?php foreach ($select1_class_options as $key => $value) : ?>
                                            <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12" id="sat-test-ii-block" style="display: none">
                                <div class="form-group">
                                    <label class="font-dialog"><?php _e('Simulated Test', 'iii-dictionary') ?></label>
                                    <select class="select-box-it form-control sel-sat-class" id="sat-class-sub">
                                        <?php foreach ($select2_class_options as $key => $value) : ?>
                                            <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12" id="ik-test-class-block" style="display: none">
                                <div class="form-group">
                                    <label class="font-dialog"><?php _e('Simulated Test', 'iii-dictionary') ?></label>
                                    <select class="select-box-it form-control sel-sat-class" id="sel-sat-class" name='sat-class'>
                                        <?php foreach ($select3_class_options as $key => $value) : ?>
                                            <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                        <?php endif ?>

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
<!-- sat-subscription-dialog -->
<div id="sat-subscription-dialog-ikmath" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Purchase SAT Subscriptions', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo locale_home_url() ?>/?r=payments">
                <input type="hidden" name="sub-type" id="sat-sub-type" value="12">
                <input type="hidden" name="sat-class" id="sat-class" value="38">
                <input type="hidden" name="sub-id" value="0">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="font-dialog col-xs-12" style="border-bottom: 1px solid #d6d6d6;"><?php _e('Selected Class', 'iii-dictionary') ?></label>
                                <p class="selected-class col-xs-12" id="selected-class" >ikMath Course</p>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12" style="padding-top: 3%">
                            <div class="form-group">
                                <label class="font-dialog"><?php _e('Number of Months', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control" name="sat-months" id="sel-sat-months-ikmath">
                                        <?php for ($i = 1; $i <= 24; $i++) : ?>
                                        <option value="<?php echo $i ?>"><?php printf(_n('%s month', '%s months', $i, 'iii-dictionary'), $i) ?></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                        </div>
                        <?php
                            $select_ikmath_class_options = array(CLASS_MATH_IK => __('Math Kindergarten', 'iii-dictionary'),
                                CLASS_MATH_IK1 => __('Math Grade 1', 'iii-dictionary'),
                                CLASS_MATH_IK2 => __('Math Grade 2', 'iii-dictionary'),
                                CLASS_MATH_IK3 => __('Math Grade 3', 'iii-dictionary'),
                                CLASS_MATH_IK4 => __('Math Grade 4', 'iii-dictionary'),
                                CLASS_MATH_IK5 => __('Math Grade 5', 'iii-dictionary'),
                                CLASS_MATH_IK6 => __('Math Grade 6', 'iii-dictionary'),
                                CLASS_MATH_IK7 => __('Math Grade 7', 'iii-dictionary'),
                                CLASS_MATH_IK8 => __('Math Grade 8', 'iii-dictionary'),
                                CLASS_MATH_IK9 => __('Math Grade 9', 'iii-dictionary'),
                                CLASS_MATH_IK10 => __('Math Grade 10', 'iii-dictionary'),
                                CLASS_MATH_IK11 => __('Math Grade 11', 'iii-dictionary'),
                                CLASS_MATH_IK12 => __('Math Grade 12', 'iii-dictionary'))
                            ?>
                            <div class="col-xs-12" id="ikmath-class-block">
                                <div class="form-group">
                                    <label class="font-dialog"><?php _e('Select Class', 'iii-dictionary') ?></label>
                                    <select class="select-box-it form-control sel-sat-class" id="sel-sat-class-ikmath" name='sat-class'>
                                        <?php foreach ($select_ikmath_class_options as $key => $value) : ?>
                                            <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>

                        <div class="col-sm-12 padding-top-2">
                            <div class="box-gray-dialog" style="text-align: right">
                                    <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color708b23">$<span id="total-amount-sat-ikmath" class="color708b23">0</span></span>
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
<!-- grammar-subscription-dialog -->
<div id="grammar-subscription-dialog" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Purchase SAT Subscriptions', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo locale_home_url() ?>/?r=payments">
                <input type="hidden" name="sub-type" id="sat-sub-type" value="3">
                <input type="hidden" name="sat-class" id="sat-class" value="1">
                <input type="hidden" name="sub-id" value="0">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="font-dialog col-xs-12" style="border-bottom: 1px solid #d6d6d6;"><?php _e('Selected Class', 'iii-dictionary') ?></label>
                                <p class="selected-class col-xs-12" id="selected-class" >Grammar Review</p>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12" style="padding-top: 3%">
                            <div class="form-group">
                                <label class="font-dialog"><?php _e('Number of Months', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control" name="sat-months" id="sel-grammar-months">
                                        <?php for ($i = 1; $i <= 24; $i++) : ?>
                                        <option value="<?php echo $i ?>"><?php printf(_n('%s month', '%s months', $i, 'iii-dictionary'), $i) ?></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 padding-top-2">
                            <div class="box-gray-dialog" style="text-align: right">
                                <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color708b23">$<span id="total-amount-grammar" class="color708b23">0</span></span>
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

<!-- grammar-subscription-dialog -->
<div id="writing-subscription-dialog" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Purchase SAT Subscriptions', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo locale_home_url() ?>/?r=payments">
                <input type="hidden" name="sub-type" id="sat-sub-type" value="3">
                <input type="hidden" name="sat-class" id="sat-class" value="2">
                <input type="hidden" name="sub-id" value="0">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="font-dialog col-xs-12" style="border-bottom: 1px solid #d6d6d6;"><?php _e('Selected Class', 'iii-dictionary') ?></label>
                                <p class="selected-class col-xs-12" id="selected-class" >Writing Practice</p>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12" style="padding-top: 3%">
                            <div class="form-group">
                                <label class="font-dialog"><?php _e('Number of Months', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control" name="sat-months" id="sel-writting-months">
                                        <?php for ($i = 1; $i <= 24; $i++) : ?>
                                        <option value="<?php echo $i ?>"><?php printf(_n('%s month', '%s months', $i, 'iii-dictionary'), $i) ?></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 padding-top-2">
                            <div class="box-gray-dialog" style="text-align: right">
                                <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color708b23">$<span id="total-amount-writting" class="color708b23">0</span></span>
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
<!-- grammar-subscription-dialog -->
<div id="sat-english-subscription-dialog" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Purchase SAT Subscriptions', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo locale_home_url() ?>/?r=payments">
                <input type="hidden" name="sub-type" id="sat-sub-type" value="3">
                <input type="hidden" name="sat-class" id="sat-class" value="3">
                <input type="hidden" name="sub-id" value="0">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="font-dialog col-xs-12" style="border-bottom: 1px solid #d6d6d6;"><?php _e('Selected Class', 'iii-dictionary') ?></label>
                                <p class="selected-class col-xs-12" id="selected-class" >SAT Simulation Test</p>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12" style="padding-top: 3%">
                            <div class="form-group">
                                <label class="font-dialog"><?php _e('Number of Months', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control" name="sat-months" id="sel-sat-english-months">
                                        <?php for ($i = 1; $i <= 24; $i++) : ?>
                                        <option value="<?php echo $i ?>"><?php printf(_n('%s month', '%s months', $i, 'iii-dictionary'), $i) ?></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                        </div>
                        <?php
                        $select_sat_english_option = array(CLASS_SAT1 => __('SAT Test 1', 'iii-dictionary'), CLASS_SAT2 => __('SAT Test 2', 'iii-dictionary'), CLASS_SAT3 => __('SAT Test 3', 'iii-dictionary'),
                                CLASS_SAT4 => __('SAT Test 4', 'iii-dictionary'), CLASS_SAT5 => __('SAT Test 5', 'iii-dictionary'))
                        ?>

                            <div class="col-xs-12" id="sat-test-i-block">
                                <div class="form-group">
                                    <label class="font-dialog"><?php _e('Select the Type', 'iii-dictionary') ?></label>
                                    <select class="select-box-it form-control sel-sat-class" id="sat-english-class-sub">
                                        <?php foreach ($select_sat_english_option as $key => $value) : ?>
                                            <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                        <div class="col-sm-12 padding-top-2">
                            <div class="box-gray-dialog" style="text-align: right">
                                <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color708b23">$<span id="total-amount-sat-english" class="color708b23">0</span></span>
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

<!-- sat-1-preparation-dialog -->
<div id="sat-1-preparation-dialog" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Purchase SAT Subscriptions', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo locale_home_url() ?>/?r=payments">
                <input type="hidden" name="sub-type" id="sat-sub-type" value="7">
                <input type="hidden" name="sat-class" id="sat-class" value="9">
                <input type="hidden" name="sub-id" value="0">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="font-dialog col-xs-12" style="border-bottom: 1px solid #d6d6d6;"><?php _e('Selected Class', 'iii-dictionary') ?></label>
                                <p class="selected-class col-xs-12" id="selected-class" >SAT Preparation</p>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12" style="padding-top: 3%">
                            <div class="form-group">
                                <label class="font-dialog"><?php _e('Number of Months', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control" name="sat-months" id="sel-sat-months-pre1">
                                        <?php for ($i = 1; $i <= 24; $i++) : ?>
                                        <option value="<?php echo $i ?>"><?php printf(_n('%s month', '%s months', $i, 'iii-dictionary'), $i) ?></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 padding-top-2">
                            <div class="box-gray-dialog" style="text-align: right">
                                <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color708b23">$<span id="total-amount-pre1" class="color708b23">0</span></span>
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
<!-- sat-2-preparation-dialog -->
<div id="sat-2-preparation-dialog" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Purchase SAT Subscriptions', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo locale_home_url() ?>/?r=payments">
                <input type="hidden" name="sub-type" id="sat-sub-type" value="8">
                <input type="hidden" name="sat-class" id="sat-class" value="15">
                <input type="hidden" name="sub-id" value="0">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="font-dialog col-xs-12" style="border-bottom: 1px solid #d6d6d6;"><?php _e('Selected Class', 'iii-dictionary') ?></label>
                                <p class="selected-class col-xs-12" id="selected-class" >SAT 2 Preparation</p>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12" style="padding-top: 3%">
                            <div class="form-group">
                                <label class="font-dialog"><?php _e('Number of Months', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control" name="sat-months" id="sel-sat-months-pre2-math">
                                        <?php for ($i = 1; $i <= 24; $i++) : ?>
                                        <option value="<?php echo $i ?>"><?php printf(_n('%s month', '%s months', $i, 'iii-dictionary'), $i) ?></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 padding-top-2">
                            <div class="box-gray-dialog" style="text-align: right">
                                <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color708b23">$<span id="total-amount-pre2-math" class="color708b23">0</span></span>
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
<!-- sat-subscription-dialog -->
<div id="sat-1-dialog" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Purchase SAT Subscriptions', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo locale_home_url() ?>/?r=payments">
                <input type="hidden" name="sub-type" id="sat-sub-type" value="7">
                <input type="hidden" name="sat-class" id="sat-class" value="10">
                <input type="hidden" name="sub-id" value="0">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="font-dialog col-xs-12" style="border-bottom: 1px solid #d6d6d6;"><?php _e('Selected Class', 'iii-dictionary') ?></label>
                                <p class="selected-class col-xs-12" id="selected-class" >SAT Simulation Test</p>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12" style="padding-top: 3%">
                            <div class="form-group">
                                <label class="font-dialog"><?php _e('Number of Months', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control" name="sat-months" id="sel-sat-months-sat1-math">
                                        <?php for ($i = 1; $i <= 24; $i++) : ?>
                                        <option value="<?php echo $i ?>"><?php printf(_n('%s month', '%s months', $i, 'iii-dictionary'), $i) ?></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                        </div>
                        <?php
                            $select1_class_options = array(CLASS_MATH_SAT1A => __('SAT Test 1', 'iii-dictionary'), CLASS_MATH_SAT1B => __('SAT Test 2', 'iii-dictionary'),
                            CLASS_MATH_SAT1C => __('SAT Test 3', 'iii-dictionary'), CLASS_MATH_SAT1D => __('SAT Test 4', 'iii-dictionary'), CLASS_MATH_SAT1E => __('SAT Test 5', 'iii-dictionary'));
                        ?>
                            <div class="col-xs-12" id="sat-test-i-block">
                                <div class="form-group">
                                    <label class="font-dialog"><?php _e('Select the Type', 'iii-dictionary') ?></label>
                                    <select class="select-box-it form-control sel-sat-class" id="sat-class-sub-sat1-math">
                                        <?php foreach ($select1_class_options as $key => $value) : ?>
                                            <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                            <?php endforeach ?>
                                    </select>
                                </div>
                            </div>

                        <div class="col-sm-12 padding-top-2">
                            <div class="box-gray-dialog" style="text-align: right">
                                    <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color708b23">$<span id="total-amount-sat1" class="color708b23">0</span></span>
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
<!-- sat-subscription-dialog -->
<div id="sat-2-dialog" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Purchase SAT Subscriptions', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo locale_home_url() ?>/?r=payments">
                <input type="hidden" name="sub-type" id="sat-sub-type" value="8">
                <input type="hidden" name="sat-class" id="sat-class" value="16">
                <input type="hidden" name="sub-id" value="0">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="font-dialog col-xs-12" style="border-bottom: 1px solid #d6d6d6;"><?php _e('Selected Class', 'iii-dictionary') ?></label>
                                <p class="selected-class col-xs-12" id="selected-class" >SAT 2 Simulation Test</p>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12" style="padding-top: 3%">
                            <div class="form-group">
                                <label class="font-dialog"><?php _e('Number of Months', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control" name="sat-months" id="sel-sat-months-sat2-math">
                                        <?php for ($i = 1; $i <= 24; $i++) : ?>
                                        <option value="<?php echo $i ?>"><?php printf(_n('%s month', '%s months', $i, 'iii-dictionary'), $i) ?></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                        </div>
                        <?php
                            $select2_class_options = array(CLASS_MATH_SAT2A => __('SAT 2 Test 1', 'iii-dictionary'), CLASS_MATH_SAT2B => __('SAT 2 Test 2', 'iii-dictionary'),
                            CLASS_MATH_SAT2C => __('SAT 2 Test 3', 'iii-dictionary'), CLASS_MATH_SAT2D => __('SAT 2 Test 4', 'iii-dictionary'), CLASS_MATH_SAT2E => __('SAT 2 Test 5', 'iii-dictionary'));
                        ?>
                            <div class="col-xs-12" id="sat-test-i-block">
                                <div class="form-group">
                                    <label class="font-dialog"><?php _e('Select the Type', 'iii-dictionary') ?></label>
                                    <select class="select-box-it form-control sel-sat-class" id="sat-class-sub-sat2-math">
                                        <?php foreach ($select2_class_options as $key => $value) : ?>
                                            <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                            <?php endforeach ?>
                                    </select>
                                </div>
                            </div>

                        <div class="col-sm-12 padding-top-2">
                            <div class="box-gray-dialog" style="text-align: right">
                                    <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color708b23">$<span id="total-amount-sat2" class="color708b23">0</span></span>
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

<!-- Teacher's Homework Tool Subscription -->
<div id="teacher-sub-details-dialog" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Teacher\'s Homework Tool Subscription', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="font-dialog"><?php _e('Select existing group (Class Name)', 'iii-dictionary') ?></label>
                            <select class="select-box-it" id="sel-group-teacher">
                                <option value=""><?php _e('Select one', 'iii-dictionary') ?></option>
                                <?php foreach ($user_groups as $group) : if (is_null($group->expired_date)) : ?>
                                        <option value="<?php echo $group->id ?>" data-size="<?php echo $group->size ?>"><?php echo $group->name ?></option>
                                    <?php endif;
                                endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="font-dialog"><?php _e('Or Create New Group', 'iii-dictionary') ?></label>
                            <input type="text" class="form-control" id="teacher-gname" placeholder="<?php _e('Group name', 'iii-dictionary') ?>">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="font-dialog">&nbsp;</label>
                            <input type="text" class="form-control" id="teacher-gpass" placeholder="<?php _e('Group password', 'iii-dictionary') ?>">
                        </div>
                    </div>
                    <span class="span-dic-down span-exclamation-point-red span-left15"></span><p class="p-custom color-redd32d53 p-left8"><?php _e('One dictionary for all members in your group is included in the subscription.
                                The price for Teacher\'s Homework Tool is 5 per student per month.', 'iii-dictionary'); ?></p>
                    <div class="footer-dialog-purchase">
                        <h6 style="color: black;margin-bottom: 5%"><?php _e('Do you pay license fee for this group or do you collect group fee from your students?', 'iii-dictionary') ?></h6>
                    </div>
                    <div class="row">
                        <div class="col-xs-1"><input id="paymyseft" class="checkboxagree input-margin-0" type="checkbox" name="paymyseft" value="paymyseft" ></div>
                        <div class="col-xs-11"><p class="font-dialog"><?php _e('Pay by myself', 'iii-dictionary') ?></p></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-1"><input  id="paystudent" class="checkboxagree input-margin-0" type="checkbox" name="paystudent" value="paystudent" ></div>
                        <div class="col-xs-11"><p class="font-dialog"><?php _e('collect from students (enter amount to field below)', 'iii-dictionary') ?></p></div>
                    </div>
                    <div style="text-align: center">
                        <input id="payeachstudent" type="text" name="payeachstudent" value="" >
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <button type="submit" id="<?php if(is_math_panel()){echo "sub-continue-math";}else{echo "sub-continue";} ?>" class="btn-custom" data-loading-text="<?php _e('Checking...', 'iii-dictionary') ?>"><?php _e('Continue', 'iii-dictionary') ?></button>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1"><?php _e('Cancel', 'iii-dictionary') ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Teacher's Homework Tool MODAL  -->
<div id="teacher-home-tool-modal-english" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3 id="addi-popup-title" data-ts-text="<?php _e('Check Out - Teacher\'s Homework Tool Subscription', 'iii-dictionary') ?>" data-ds-text="<?php _e('Check Out - Dictionary', 'iii-dictionary') ?>" data-ext-text="<?php _e('Purchase Additional Subscriptions', 'iii-dictionary') ?>"><?php _e('Purchase Additional Subscriptions', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo locale_home_url() ?>/?r=payments">
                <input type="hidden" id="addi-sub-type" name="sub-type" value="2">
                <input type="hidden" id="addi-gid" name="assoc-group" value="">
                <input type="hidden" id="addi-gname" name="group-name" value="">
                <input type="hidden" id="addi-gpass" name="group-pass" value="">
                <input type="hidden" id="sub-id" name="sub-id" value="0">
                <?php if($is_math_panel){ ?>
                    <input type="hidden" id="is-math" value="1">
                <?php }else{ ?>
                    <input type="hidden" id="is-math" value="0">
                <?php } ?>
                <input type="hidden" id="sat-month-teach-tool" name="sat-months" value="1">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12" id="selected-group-label">
                            <div class="form-group">
                                <label class="font-dialog col-xs-12" style="border-bottom: 1px solid #d6d6d6;"><?php _e('Selected Group', 'iii-dictionary') ?></label>
                                <p class="selected-class col-xs-12" id="addi-selected-group" ></p>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group<?php echo $is_math_panel ? ' hidden' : '' ?>">
                                <label class="font-dialog"><?php _e('Select the type of dictionary', 'iii-dictionary') ?></label>
                                <?php MWHtml::select_dictionaries('', false, 'dictionary', 'sel-dictionary', 'form-control', true) ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label id="num-of-student-lbl" class="font-dialog"><?php _e('Number of Students', 'iii-dictionary') ?></label>
                                <?php $min_no_of_student = mw_get_option('min-students-subscription') ?>
                                <input type="number" name="no-students" id="student-num-add-continute-english" class="form-control" data-min="<?php echo $min_no_of_student ?>" value="1">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label id="num-of-months-lbl" class="font-dialog"><?php _e('Number of Months', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control" name="teacher-tool-months" id="sel-teacher-tool">
                                    <?php for ($i = 3; $i <= 24; $i++) : ?>
                                        <option value="<?php echo $i ?>"><?php printf(__('%s months', 'iii-dictionary'), $i) ?></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row padding-top15">
                        <div class="col-sm-12">
                            <div class="box-gray-dialog" style="text-align: right">
                                <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color-green708b23">$</span> <span id="total-amount-add-continute-english" class="color-green708b23">0</span>
                            </div>
                        </div>
                    </div>				
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button type="submit" id="add-to-cart-ss-english-continute" name="add-to-cart" class="btn-custom"><?php _e('Check out', 'iii-dictionary') ?></button>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1"><?php _e('Cancel', 'iii-dictionary') ?></a>
                            </div>
                        </div>
                    </div>
                </div>			
            </form>
        </div>
    </div>
</div>

<!-- Teacher's Homework Tool MODAL MATH -->
<div id="teacher-home-tool-modal-math" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3 id="addi-popup-title" data-ts-text="<?php _e('Check Out - Teacher\'s Homework Tool Subscription', 'iii-dictionary') ?>" data-ds-text="<?php _e('Check Out - Dictionary', 'iii-dictionary') ?>" data-ext-text="<?php _e('Purchase Additional Subscriptions', 'iii-dictionary') ?>"><?php _e('Purchase Additional Subscriptions', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo locale_home_url() ?>/?r=payments">
                <input type="hidden" id="addi-sub-type" name="sub-type" value="2">
                <input type="hidden" id="addi-gid" name="assoc-group" value="">
                <input type="hidden" id="addi-gname" name="group-name" value="">
                <input type="hidden" id="addi-gpass" name="group-pass" value="">
                <input type="hidden" id="sub-id" name="sub-id" value="0">
                <?php if($is_math_panel){ ?>
                    <input type="hidden" id="is-math" value="1">
                <?php }else{ ?>
                    <input type="hidden" id="is-math" value="0">
                <?php } ?>
                <input type="hidden" id="sat-month-teach-tool" name="sat-months" value="1">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12" id="selected-group-label">
                            <div class="form-group">
                                <label class="font-dialog col-xs-12" style="border-bottom: 1px solid #d6d6d6;"><?php _e('Selected Group', 'iii-dictionary') ?></label>
                                <p class="selected-class col-xs-12" id="addi-selected-group" ></p>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group<?php echo $is_math_panel ? ' hidden' : '' ?>">
                                <label class="font-dialog"><?php _e('Select the type of dictionary', 'iii-dictionary') ?></label>
                                <?php MWHtml::select_dictionaries('', false, 'dictionary', 'sel-dictionary', 'form-control', true) ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label id="num-of-student-lbl" class="font-dialog"><?php _e('Number of Students', 'iii-dictionary') ?></label>
                                <?php $min_no_of_student = mw_get_option('min-students-subscription') ?>
                                <input type="number" name="no-students" id="student-num-add-continute-english" class="form-control" data-min="<?php echo $min_no_of_student ?>" value="1">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label id="num-of-months-lbl" class="font-dialog"><?php _e('Number of Months', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control" name="teacher-tool-months" id="sel-teacher-tool">
                                    <?php for ($i = 3; $i <= 24; $i++) : ?>
                                        <option value="<?php echo $i ?>"><?php printf(__('%s months', 'iii-dictionary'), $i) ?></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row padding-top15">
                        <div class="col-sm-12">
                            <div class="box-gray-dialog" style="text-align: right">
                                <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color-green708b23">$</span> <span id="total-amount-add-continute-english" class="color-green708b23">0</span>
                            </div>
                        </div>
                    </div>				
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button type="submit" id="add-to-cart-ss-english-continute" name="add-to-cart" class="btn-custom"><?php _e('Check out', 'iii-dictionary') ?></button>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1"><?php _e('Cancel', 'iii-dictionary') ?></a>
                            </div>
                        </div>
                    </div>
                </div>			
            </form>
        </div>
    </div>
</div>

<!-- Merriam-Webster Dictionary MODAL  -->
<div id="additional-subscription-dialog-continue" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3 id="addi-popup-title" data-ts-text="<?php _e('Check Out - Teacher\'s Homework Tool Subscription', 'iii-dictionary') ?>" data-ds-text="<?php _e('Check Out - Dictionary', 'iii-dictionary') ?>" data-ext-text="<?php _e('Purchase Additional Subscriptions', 'iii-dictionary') ?>"><?php _e('Purchase Additional Subscriptions', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo locale_home_url() ?>/?r=payments">
                <input type="hidden" id="addi-sub-type" name="sub-type" value="2">
                <input type="hidden" id="addi-gid" name="assoc-group" value="">
                <input type="hidden" id="addi-gname" name="group-name" value="">
                <input type="hidden" id="addi-gpass" name="group-pass" value="">
                <input type="hidden" id="sub-id" name="sub-id" value="0">
                <input type="hidden" id="sat-month-teach-tool" name="sat-months" value="1">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12" id="selected-group-label">
                            <div class="form-group">
                                <label class="font-dialog col-xs-12" style="border-bottom: 1px solid #d6d6d6;"><?php _e('Selected Group', 'iii-dictionary') ?></label>
                                <p class="selected-class col-xs-12" id="addi-selected-group" ></p>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group<?php echo $is_math_panel ? ' hidden' : '' ?>">
                                <label class="font-dialog"><?php _e('Select the type of dictionary', 'iii-dictionary') ?></label>
                                <?php MWHtml::select_dictionaries('', false, 'dictionary', 'sel-dictionary', 'form-control', true) ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label id="num-of-student-lbl" class="font-dialog"><?php _e('Number of Students', 'iii-dictionary') ?></label>
                                <?php $min_no_of_student = mw_get_option('min-students-subscription') ?>
                                <input type="number" name="no-students" id="student-num" class="form-control" data-min="<?php echo $min_no_of_student ?>" value="1">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label id="num-of-months-lbl" class="font-dialog"><?php _e('Number of Months', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control" name="teacher-tool-months" id="sel-teacher-tool">
                                    <?php for ($i = 3; $i <= 24; $i++) : ?>
                                        <option value="<?php echo $i ?>"><?php printf(__('%s months', 'iii-dictionary'), $i) ?></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row padding-top15">
                        <div class="col-sm-12">
                            <div class="box-gray-dialog" style="text-align: right">
                                <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color-green708b23">$</span> <span id="total-amount" class="color-green708b23">0</span>
                            </div>
                        </div>
                    </div>				
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button type="submit" id="add-to-cart-ss-english-continute" name="add-to-cart" class="btn-custom"><?php _e('Check out', 'iii-dictionary') ?></button>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1"><?php _e('Cancel', 'iii-dictionary') ?></a>
                            </div>
                        </div>
                    </div>
                </div>			
            </form>
        </div>
    </div>
</div>

<!-- Student's Self-study Subscription -->
<div id="self-study-subscription-dialog" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Student\'s Self-study Subscription', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo locale_home_url() ?>/?r=payments">
                <input type="hidden" name="sub-type" value="<?php echo!$is_math_panel ? SUB_SELF_STUDY : SUB_SELF_STUDY_MATH ?>" id="self-study-sub">
                    <?php $self_study_group = generate_self_study_group_name() ?>
                <input type="hidden" name="group-name" value="<?php echo $self_study_group ?>">
                <input type="hidden" name="sat-months" id="self-sat-months" value="1">
                <div class="modal-body">
                    <div class="row">
                        
                        <div class="col-sm-12 form-group" id="ss-dict-block">
                            <label class="font-dialog"><?php _e('Select the type of dictionary', 'iii-dictionary') ?></label>
                            <?php MWHtml::select_dictionaries('', false, 'dictionary', 'sel-dictionary2', 'form-control', false) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="font-dialog"><?php _e('Number of Students', 'iii-dictionary') ?></label>
                                <input type="number" name="no-students" class="form-control" min="1" max="1" value="1" readonly>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="font-dialog"><?php _e('Number of Months', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control" name="self-study-months" id="sel-self-study-months">
                                    <?php for ($i = 1; $i <= 24; $i++) : ?>
                                        <option value="<?php echo $i ?>"><?php printf(_n('%s month', '%s months', $i, 'iii-dictionary'), $i) ?></option>
                                    <?php endfor ?>
                                </select>   
                            </div>
                        </div>
                        <div class="col-sm-12 padding-top-2">
                            <div class="box-gray-dialog" style="text-align: right">
                                <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color708b23">$</span> <span class="currency color708b23" id="ss-total-amount">0</span>
                            </div>
                        </div>
                    </div>				
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <?php if(is_math_panel()) { ?>
                                    <button type="submit" id="add-to-cart-ss-math" name="add-to-cart" class="btn-custom"><?php _e('Check out', 'iii-dictionary') ?></button>
                                <?php } else { ?>
                                    <button type="submit" id="add-to-cart-ss-english" name="add-to-cart" class="btn-custom"><?php _e('Check out', 'iii-dictionary') ?></button>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1"><?php _e('Cancel', 'iii-dictionary') ?></a>
                            </div>
                        </div>
                    </div>
                </div>			
            </form>
        </div>
    </div>
</div>
<!-- Student's Self-study Subscription -->
<div id="self-study-subscription-dialog-math" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Student\'s Self-study Subscription', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo locale_home_url() ?>/?r=payments">
                <input type="hidden" name="sub-type" value="9" id="self-study-sub-math">
                    <?php $self_study_group = generate_self_study_group_name() ?>
                <input type="hidden" name="group-name" value="<?php echo $self_study_group ?>">
                <input type="hidden" name="sat-months" id="self-sat-months" value="1">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="font-dialog"><?php _e('Number of Students', 'iii-dictionary') ?></label>
                                <input type="number" name="no-students" class="form-control" min="1" max="1" value="1" readonly>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="font-dialog"><?php _e('Number of Months', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control" name="self-study-months" id="sel-self-study-months-math">
                                    <?php for ($i = 1; $i <= 24; $i++) : ?>
                                        <option value="<?php echo $i ?>"><?php printf(_n('%s month', '%s months', $i, 'iii-dictionary'), $i) ?></option>
                                    <?php endfor ?>
                                </select>   
                            </div>
                        </div>
                        <div class="col-sm-12 padding-top-2">
                            <div class="box-gray-dialog" style="text-align: right">
                                <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color708b23">$</span> <span class="currency color708b23" id="ss-total-amount-math-self">0</span>
                            </div>
                        </div>
                    </div>				
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <?php if(is_math_panel()) { ?>
                                    <button type="submit" id="add-to-cart-ss-math" name="add-to-cart" class="btn-custom"><?php _e('Check out', 'iii-dictionary') ?></button>
                                <?php } else { ?>
                                    <button type="submit" id="add-to-cart-ss-english" name="add-to-cart" class="btn-custom"><?php _e('Check out', 'iii-dictionary') ?></button>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1"><?php _e('Cancel', 'iii-dictionary') ?></a>
                            </div>
                        </div>
                    </div>
                </div>			
            </form>
        </div>
    </div>
</div>

<!-- renew subscription -->
<div id="additional-subscription-dialog" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3 id="addi-popup-title" data-ts-text="<?php _e('Teacher\'s Homework Tool Subscription', 'iii-dictionary') ?>" data-ds-text="<?php _e('Dictionary Subscription', 'iii-dictionary') ?>" data-ext-text="<?php _e('Purchase Additional Subscriptions', 'iii-dictionary') ?>"><?php _e('Purchase Additional Subscriptions (Renewal)', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo locale_home_url() ?>/?r=payments">
                <input type="hidden" id="addi-sub-type" name="sub-type" value="">
                <input type="hidden" id="addi-gid" name="assoc-group" value="">
                <input type="hidden" id="addi-gname" name="group-name" value="">
                <input type="hidden" id="addi-gpass" name="group-pass" value="">
                <input type="hidden" id="sub-id" name="sub-id" value="0">
                <input type="hidden" id="sub-sat-class" name="sat-class" value="0">
                <input type="hidden" id="user-teacher-tool-count" name="user-teach-tool" value="1">
                <input type="hidden" id="id_dic" name="dictionary_id" value="0">
                <div class="modal-body">
                    <div class="hidden">
                        <?php MWHtml::select_dictionaries('', false, 'dictionary', 'sel-dictionary', 'form-control', true) ?>

                    </div>
                    <div class="row">
                        <h3 id="type-group" style="color: black;margin-bottom: 4%"></h3>
                        <div style="    border-bottom: 1px solid #ccc;margin-bottom: 4%"></div>
                        <div class="div-span-1"><span class="span-purchase-red"></span><p class="p-purchase italic color4c4c4c" id="name-group"></p></div>
                        <div id="dic-class"><span class="span-purchase-red"></span><p class="p-purchase italic color4c4c4c" id="name-dic"></p></div>
                        <div class="div-span-2"><span class="span-purchase-red"></span><p class="p-purchase italic color4c4c4c" id="number-std"></p></div>
                        <div class="div-span-1"><span class="span-purchase-red"></span><p class="p-purchase italic color4c4c4c" id="number-month"></p></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 div-span-1" style="padding-top: 3%;">
                            <div class="form-group">
                                <?php $min_no_of_student = mw_get_option('min-students-subscription') ?>
                                <label id="num-of-student-lbl" class="font-dialog"><?php _e('Number of Students', 'iii-dictionary') ?></label>
                                <input type="number" name="no-students" id="student_num" class="form-control" data-min="<?php echo $min_no_of_student ?>" value="<?php echo $min_no_of_student ?>">
                            </div>
                        </div>
                        <div class="col-sm-12 div-span-2" style="padding-top: 3%;">
                            <div class="form-group">
                                <label id="num-of-months-lbl" class="font-dialog"><?php _e('Number of Months', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control" name="sat-months" id="sel-teacher-tool1">
                                    <?php for ($i = 1; $i <= 24; $i++) : ?>
                                        <option value="<?php echo $i ?>"><?php printf(__('%s months', 'iii-dictionary'), $i) ?></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="box-dialog" style="padding-top: 2%;">
                                <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color708b23">$</span> <span id="total-amount-renew" class="color708b23">0</span>
                            </div>
                        </div>
                    </div>				
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button type="submit" id="add-to-cart" name="add-to-cart" class="btn-custom confirm"><?php _e('Check out', 'iii-dictionary') ?></button>
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

<!-- Student Self-study -->
<div id="self-study-detail" class="modal fade ">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Details', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-custom-first">
                <div class="body-modal">
                        <?php if ($is_math_panel) { ?>
                            <h3 style="color: black;margin-bottom: 3%"><?php _e('Student Self-study', 'iii-dictionary') ?></h3>
                            <div style="    border-bottom: 1px solid #ccc;margin-bottom:3%"></div>
                            <p class="p-custom span-left15 color-green708b23 font-auto1"><?php _e('You can use all worksheets listed in this site once you subscribe "Self-study" mode. Self-study 
                            mode is monthly subscription. You can see the complete list of worksheets by clicking below.', 'iii-dictionary'); ?></p>
                            <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('Word Problem', 'iii-dictionary'); ?></p>
                            <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('Add and Sub & Single Digit Multiplication', 'iii-dictionary'); ?></p>
                            <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('Two Digit Multiplication', 'iii-dictionary'); ?></p>
                            <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('Long Division by Single Digit', 'iii-dictionary'); ?></p>
                            <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('Equation', 'iii-dictionary'); ?></p>
                            <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('Flashcard', 'iii-dictionary'); ?></p>
                            <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('Fraction', 'iii-dictionary'); ?></p>
                            <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('Question Box', 'iii-dictionary'); ?></p>
                            <button id="math-detail-list" style="margin-bottom: 3%; padding: 0 ;   padding-left: 6% !important;" class="a-link-pur-custom btn-custom-2" ><?php _e('See list of new worksheets you will receive for this subscription', 'iii-dictionary'); ?></button>
                            <div class="row" style="padding-top: 3%">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <button data-subscription-type="5" class="btn-custom confirm choose-sub-btn"><?php _e('Check out', 'iii-dictionary'); ?></button>
                                </div>
                            </div>
                        <?php } else { ?>
                            <h3 style="color: black;margin-bottom: 3%"><?php _e('Student Self-study', 'iii-dictionary') ?></h3>
                            <div style="    border-bottom: 1px solid #ccc;margin-bottom: 4%"></div>
                            <p class="p-custom span-left15 color-green708b23"><?php _e('Our self study program is a good way for self improvement on different subject. We have over 200 new worksheet of:', 'iii-dictionary'); ?></p>
                            <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('Spelling', 'iii-dictionary'); ?></p>
                            <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('Vocabulary & Grammar', 'iii-dictionary'); ?></p>
                            <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('Reading Comprehension', 'iii-dictionary'); ?></p>
                            <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('Writing practice', 'iii-dictionary'); ?></p>
                            <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('Vocabulary Builder Tools', 'iii-dictionary'); ?></p>
                            <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('Your Choice of Dictionary', 'iii-dictionary'); ?></p>
                            <p class="p-custom p-left8"><?php _e('(E Learner’s, Collegiate, Medical, Intermediate, Elementary)', 'iii-dictionary'); ?></p>
                            <button id="english-detail-list" style="margin-bottom: 3%; padding: 0 ;   padding-left: 6% !important;" class="a-link-pur-custom btn-custom-2" ><?php _e('See list of new worksheets you will receive for this subscription', 'iii-dictionary'); ?></button>
                            <div class="row" style="padding-top: 3%">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <button data-subscription-type="5" class="btn-custom confirm choose-sub-btn">Check out</button>
                                    </div>
                                </div>
                        <?php } ?>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1 secondary">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Merriam-Webster Dictionary -->
<div id="merriam-webster-dictionary" class="modal fade ">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Details', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-custom-first">
                <div class="body-modal">
                    <h3 style="color: black;margin-bottom: 3%"><?php _e('Merriam-Webster Dictionary', 'iii-dictionary') ?></h3>
                    <div style="border-bottom: 1px solid #ccc;margin-bottom: 3%"></div>
                    <p class="p-custom span-left15 color-green708b23 font-auto1"><?php _e('A powerful electronic version of America’s best-selling language reference that delivers 
                    accurate, up-to-date information while word processing, composing email, designing 
                    spreadsheets, preparing presentation, or surfing the Web.', 'iii-dictionary'); ?></p>
                    <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('If you purchase a dictionary subscription for multiple users, give each user the activation 
                    code that is generated after purchase. Users can activate their subscriptions and view 
                    account information under My Account', 'iii-dictionary'); ?></p>
                    <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('For installation on public computers, such as school computer labs, click on the 
                    instructions (shown above on this page) after you have made the purchase.', 'iii-dictionary'); ?></p>
                    <label for="lbl_link-dwn-sc">
                        <p class="" id="lbl_link-dwn-sc" style="    font-size: 16px;color: #4c4c4c">
                            <?php _e('For download version of dictionary:&nbsp;', 'iii-dictionary') ?>
                            <a href="#download-dictionary" data-toggle="modal"><strong><u><?php _e('click here first.', 'iii-dictionary') ?></u></strong></a>
                            <?php _e('', 'iii-dictionary') ?>
                        </p>
                    </label>
                    <div class="row" style="padding-top: 3%">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button data-subscription-type="2" class="btn-custom confirm choose-sub-btn"><?php _e('Purchase Now', 'iii-dictionary'); ?></button>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1 secondary"><?php _e('Cancel', 'iii-dictionary'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SAT practice Test -->
<div id="sat-practice-test" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Details', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-custom-first">
                <div class="body-modal">
                    <h3 style="color: black;margin-bottom: 3%"><?php _e('SAT practice Test', 'iii-dictionary') ?></h3>
                    <div style="    border-bottom: 1px solid #ccc;margin-bottom: 3%"></div>
                    <p class="p-custom span-left15 color-green708b23 font-auto1"><?php _e('SAT Preparation: You may join SAT preparation class at this site. Once you join, then you can 
                        start your preparation study', 'iii-dictionary'); ?></p>
                    <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('You may subscribe to one of five tests available for SAT. Once subscribed, you may take the 
                        same test as many times as you want. ', 'iii-dictionary'); ?></p>
                    <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('If you want different set for practice test, you need to subscribe for the second practice 
                        test. You can subscribe up to five practice tests. You may retake subscribed tests as many 
                        times as you want during your valid subscription period.', 'iii-dictionary'); ?></p>

                    <div class="row" style="padding-top: 3%">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button type="button" data-subscription-type="3" data-sat-class="SAT practice Test" data-type="3" class="btn-custom choose-sub-btn"><?php _e('Purchase Now', 'iii-dictionary'); ?></button>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1 secondary"><?php _e('Cancel', 'iii-dictionary'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Download Version of Merriam-Webster Dictionary -->
<div id="download-dictionary" class="modal fade ">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Details', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-custom-first">
                <div class="body-modal">
                    <h3 style="color: black;margin-bottom: 3%"><?php _e('Download Version of Merriam-Webster Dictionary', 'iii-dictionary') ?></h3>
                    <div style="    border-bottom: 1px solid #ccc;margin-bottom: 4%"></div>
                    <p class="p-custom span-left15 color-green708b23"><?php _e('You can use the Merriam-Webster Dictionary offline. Simply follow the instruction from below.', 'iii-dictionary'); ?></p>
                    <span class="span-dic-down span-left15">1.</span><p class="p-custom p-left5"><?php _e('Download the dictionary and install.', 'iii-dictionary'); ?></p>
                    <span class="span-dic-down span-left15">2.</span><p class="p-custom p-left5"><?php _e('Enter the activation code', 'iii-dictionary'); ?></p>
                    <span class="span-dic-down span-left15">3.</span><p class="p-custom p-left5"><?php _e('Your email address registered in iklearn.com', 'iii-dictionary'); ?></p>
                    <span class="span-dic-down span-exclamation-point-red span-left15"></span><p class="p-custom color-redd32d53 p-left8"><?php _e('The dictionary program need to periodically check the license status, so you need to be 
                    connected online occasionally.', 'iii-dictionary'); ?></p>
                    <div class="footer-dialog-purchase">
                        <h3 style="color: black;margin-bottom: 3%"><?php _e('Download:', 'iii-dictionary') ?></h3>
                    </div>
                    <div class="row">
                        <div class="col-xs-6"><a class="btn-custom" href="<?php echo $link_url['mac']; ?>"><?php _e('MAC', 'iii-dictionary') ?></a></div>
                        <div class="col-xs-6"><a class="btn-custom" href="<?php echo $link_url['win']; ?>"><?php _e('WINDOWS', 'iii-dictionary') ?></a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SAT Preparation - Grammar Review -->
<div id="grammar-review" class="modal fade ">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Details', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-custom-first">
                <div class="body-modal">
                    <h3 style="color: black;margin-bottom: 3%"><?php _e('SAT Preparation - Grammar Review', 'iii-dictionary') ?></h3>
                    <div style="    border-bottom: 1px solid #ccc;margin-bottom: 3%"></div>
                    <p class="p-custom span-left15 color-green708b23 font-auto1"><?php _e('SAT Preparation: You may join SAT preparation class at this site. Once you join, then you can 
                    start your preparation study.', 'iii-dictionary'); ?></p>
                    <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('This class reviews grammar efficiently from beginning to end which is necessary for 
                    building basic skills for getting a high SAT score. ', 'iii-dictionary'); ?></p>
                    <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('You will also substantially build your vocabulary in this class.', 'iii-dictionary'); ?></p>

                    <div class="row" style="padding-top: 3%">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button data-sat-class="Grammar Review" data-subscription-type="3" data-type="1" class="btn-custom choose-sub-btn" target="_blank" data-toggle="modal"><?php _e('Purchase Now', 'iii-dictionary'); ?></button>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1 secondary"><?php _e('Cancel', 'iii-dictionary'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SAT Preparation - Writing Practice -->
<div id="writing-practice" class="modal fade ">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Details', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-custom-first">
                <div class="body-modal">
                    <h3 style="color: black;margin-bottom: 3%"><?php _e('SAT Preparation - Writing Practice', 'iii-dictionary') ?></h3>
                    <div style="    border-bottom: 1px solid #ccc;margin-bottom: 3%"></div>
                    <p class="p-custom span-left15 color-green708b23 font-auto1"><?php _e('SAT Preparation: You may join SAT preparation class at this site. Once you join, then you can 
start your preparation study.', 'iii-dictionary'); ?></p>
                    <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('This class lets you prepare writing section of the SAT test which covers: tips, writing style, 
methods, confusing words and phrases, and many more.', 'iii-dictionary'); ?></p>
                    <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('Get real teacher’s help by requesting it from the writing group that you are currently in
(Each request will cost the student. The price range will be vary to different task).', 'iii-dictionary'); ?></p>
                    <div class="row" style="padding-top: 3%">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button data-sat-class="Writing Practice" data-subscription-type="3" data-type="2" class="btn-custom choose-sub-btn confirm"><?php _e('Purchase Now', 'iii-dictionary'); ?></button>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1 secondary"><?php _e('Cancel', 'iii-dictionary'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Purchase Points -->
<div id="purchase-points" class="modal fade ">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Details', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-custom-first">
                <div class="body-modal">
                    <h3 style="color: black;margin-bottom: 3%"><?php _e('Purchase Points', 'iii-dictionary') ?></h3>
                    <div style="    border-bottom: 1px solid #ccc;margin-bottom: 3%"></div>
                    <p class="p-custom span-left15 color-green708b23 font-auto1"><?php _e('Points are required to purchase worksheet that you can use for the homework assignment.', 'iii-dictionary'); ?></p>
                    <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('One point is equivalent to one dollar. You can earn points by selling your worksheet to 
other teachers.', 'iii-dictionary'); ?></p>
                    <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('You can earn points by editing and grading writing assignment students submitted. ', 'iii-dictionary'); ?></p>

                    <div class="row" style="padding-top: 3%">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button class="btn-custom choose-sub-btn" data-subscription-type="4" ><?php _e('Purchase Now', 'iii-dictionary'); ?></button>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1 secondary"><?php _e('Cancel', 'iii-dictionary'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Teacher's Homework Tool -->
<div id="teacher-homework-tool-dialog" class="modal fade ">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Details', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-custom-first">
                <div class="body-modal">
                    <h3 style="color: black;margin-bottom: 3%"><?php _e('Teacher\'s Homework Tool', 'iii-dictionary') ?></h3>
                    <div style="border-bottom: 1px solid #ccc;margin-bottom: 3%"></div>
                    <p class="p-custom span-left15 color-green708b23 font-auto1"><?php _e('What is the Teacher\'s Tool subscription, and what can teachers do with this subscription?', 'iii-dictionary'); ?></p>
                    <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('Send homework to a group- students join the group to do the homework assignment. 
                    The homework is auto-graded (except for writing assignments).', 'iii-dictionary'); ?></p>
                    <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('See every student\'s homework status in Teacher\'s Box.', 'iii-dictionary'); ?></p>
                    <?php if (!$is_math_panel) { ?>
                        <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('Get many more homework sheets for all grade levels. ', 'iii-dictionary'); ?><button id="english-detail-list" class="a-link-pur-custom btn-custom-3" ><?php _e('(Click here to see the list)', 'iii-dictionary'); ?></button></p>
                        <?php } else { ?>
                        <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('Get many more homework sheets for all grade levels. ', 'iii-dictionary'); ?><button id="math-detail-list" class="a-link-pur-custom btn-custom-3" ><?php _e('(Click here to see the list)', 'iii-dictionary'); ?></button></p>
<?php } ?>
                    <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('Purchase additional homework sheets from other teachers or sell your own homework 
                    to other teachers. (in the teacher\'s exchange section)', 'iii-dictionary'); ?></p>
                    <div class="footer-dialog-purchase">
                        <span class="span-purchase-first-red"></span><p class="p-purchase-first"><?php _e('Things to Consider', 'iii-dictionary'); ?></p>
                        <span class="span-purchase-red"></span><p class="p-purchase"><?php _e('Points are required to purchase worksheet that you can use for the homework assignment.', 'iii-dictionary'); ?></p>
                        <span class="span-purchase-red"></span><p class="p-purchase"><?php _e('1 point is equivalent to 1 dollar. You can earn points by selling your worksheet to other teachers.', 'iii-dictionary'); ?></p>
                        <span class="span-purchase-red"></span><p class="p-purchase"><?php _e('You can earn points by editing and grading writing assignment students submitted.', 'iii-dictionary'); ?></p>
                    </div>
                    <div class="row" style="padding-top: 3%">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button  data-subscription-type="1" class="btn-custom confirm choose-sub-btn"><?php _e('New Subscription', 'iii-dictionary'); ?></button>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1 secondary"><?php _e('Cancel', 'iii-dictionary'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- IK Math Classes -->
<div id="ikmath-classes" class="modal fade ">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Details', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-custom-first">
                <div class="body-modal">
                    <h3 style="color: black;margin-bottom: 3%"><?php _e('IK Math Classes    ', 'iii-dictionary') ?></h3>
                    <div style="border-bottom: 1px solid #ccc;margin-bottom: 3%"></div>
                    <p class="p-custom span-left15 color-green708b23 font-auto1"><?php _e('Please select the grade you subscribe and pay for one month subscription below.', 'iii-dictionary'); ?></p>
                    <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('Select classes from Math Kindergarten to Math Grade 12.', 'iii-dictionary'); ?></p>
                    <div class="row" style="padding-top: 3%">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button  data-type="38" data-sat-class="IK Math classes" data-subscription-type="12" class="btn-custom confirm choose-sub-btn"><?php _e('New Subscription', 'iii-dictionary'); ?></button>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1 secondary"><?php _e('Cancel', 'iii-dictionary'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SAT I Preparation -->
<div id="sat-i-preparation" class="modal fade ">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Details', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-custom-first">
                <div class="body-modal">
                    <h3 style="color: black;margin-bottom: 3%"><?php _e('SAT I Preparation', 'iii-dictionary') ?></h3>
                    <div style="    border-bottom: 1px solid #ccc;margin-bottom: 3%"></div>
                    <p class="p-custom span-left15 color-green708b23 font-auto1"><?php _e('This practice is essential to prepare for SAT I preparation. You will quickly review entire Algebra 
in this class.', 'iii-dictionary'); ?></p>
                    <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('The review of Algebra cover subjects like: Function, Function Word Problems, Linear Word 
Problems, Quadratic Word Problems, Word Problems and so forth.', 'iii-dictionary'); ?></p>
                    <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('The review of Geometry cover subjects like: Geometry Review, Volume Area, Word 
Problems, Analytical and so forth.', 'iii-dictionary'); ?></p>
                    <div class="row" style="padding-top: 3%">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button  data-sat-class="SAT I Preparation" data-subscription-type="7" data-type="9" class="btn-custom confirm choose-sub-btn"><?php _e('New Subscription', 'iii-dictionary'); ?></button>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1 secondary"><?php _e('Cancel', 'iii-dictionary'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SAT II Preparation -->
<div id="sat-ii-preparation" class="modal fade ">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Details', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-custom-first">
                <div class="body-modal">
                    <h3 style="color: black;margin-bottom: 3%"><?php _e('SAT II Preparation', 'iii-dictionary') ?></h3>
                    <div style="    border-bottom: 1px solid #ccc;margin-bottom: 3%"></div>
                    <p class="p-custom span-left15 color-green708b23 font-auto1"><?php _e('This practice is essential to prepare for SAT II preparation. You will quickly review entire Algebra 
in this class.', 'iii-dictionary'); ?></p>
                    <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('The review of Algebra II cover subjects like: Polynomial, Rational, Exponent, Logarithm, 
Trigonometry, Conic and so forth.', 'iii-dictionary'); ?></p>
                    <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('The review of Precalculus cover subjects like: Precalculus and so forth.', 'iii-dictionary'); ?></p>
                    <div class="row" style="padding-top: 3%">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button  data-sat-class="SAT II Preparation" data-subscription-type="8" data-type="15" class="btn-custom confirm choose-sub-btn"><?php _e('New Subscription', 'iii-dictionary'); ?></button>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1 secondary"><?php _e('Cancel', 'iii-dictionary'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SAT II Simulated Test  -->
<div id="sat-ii-simulated-test" class="modal fade ">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Details', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-custom-first">
                <div class="body-modal">
                    <h3 style="color: black;margin-bottom: 3%"><?php _e('SAT II Simulated Test', 'iii-dictionary') ?></h3>
                    <div style="    border-bottom: 1px solid #ccc;margin-bottom: 3%"></div>
                    <p class="p-custom span-left15 color-green708b23 font-auto1"><?php _e('There are five simulated tests available for the subscription. Once subscribed, you may take the 
test immediately.', 'iii-dictionary'); ?></p>
                    <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('Five simulated tests which consists of multiple choices, numeric answers, with or 
without calculator questions and so forth.', 'iii-dictionary'); ?></p>
                    <div class="row" style="padding-top: 3%">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button  data-sat-class="SAT II simlated test" data-subscription-type="8" data-type="16"class="btn-custom confirm choose-sub-btn"><?php _e('New Subscription', 'iii-dictionary'); ?></button>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1 secondary"><?php _e('Cancel', 'iii-dictionary'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SAT I Simulated Test (New SAT Test)  -->
<div id="sat-ii-simulated-test-new" class="modal fade ">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Details', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-custom-first">
                <div class="body-modal">
                    <h3 style="color: black;margin-bottom: 3%"><?php _e('SAT I Simulated Test (New SAT Test)', 'iii-dictionary') ?></h3>
                    <div style="    border-bottom: 1px solid #ccc;margin-bottom: 4%"></div>
                    <p class="p-custom span-left15 color-green708b23"><?php _e('There are five simulated tests available for the subscription. Once subscribed, you may take the 
test immediately.', 'iii-dictionary'); ?></p>
                    <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('New updated SAT simulated test.', 'iii-dictionary'); ?></p>
                    <span class="span-custom span-left15"></span><p class="p-custom p-left5"><?php _e('Five simulated tests which consists of multiple choices, numeric answers, with or 
without calculator questions and so forth.', 'iii-dictionary'); ?></p>
                    <div class="row" style="padding-top: 3%">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button  data-sat-class="SAT I simulated test" data-subscription-type="7" data-type="10" class="btn-custom confirm choose-sub-btn"><?php _e('New Subscription', 'iii-dictionary'); ?></button>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1 secondary"><?php _e('Cancel', 'iii-dictionary'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- list of worksheet -->
<div id="self-study-detail-list" class="modal fade ">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;    top: 10px !important;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('List of Worksheet', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-custom-first" style="height: 500px; ">
                <div class="body-modal" >
                    <?php
                    $filter['offset'] = 0;
                    $filter['items_per_page'] = 99999999;
                    if (!$is_math_panel) {
                        $sheets_obj = MWDB::get_sheets($filter, false, true);
                        
                        $avail_sheets = $sheets_obj->items;
                    } else {
                        $sheets_obj = MWDB::get_math_sheets($filter, $filter['offset'], $filter['items_per_page']);
                        $avail_sheets = $sheets_obj->items;
                    }
                    ?>
                    <div style="border-bottom: 1px solid #ccc;margin-bottom: 3%"></div>
                    <div class=""></div>
                    <table class="table table-striped table-style3 table-custom-2">
                        <thead>
                            <tr>
                                <td class="color-gray838383 p-left5" ><?php _e('Assignment', 'iii-dictionary') ?></td>
                                <td class="color-gray838383"><?php _e('Grade', 'iii-dictionary') ?></td>
                                <td class="color-gray838383"><?php _e('Worksheet Name', 'iii-dictionary') ?></td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($avail_sheets)) : ?>
                                <tr><td colspan="3">No results</td></tr>
                                    <?php else : foreach ($avail_sheets as $sheet) : ?>
                                    <tr>
                                        <td class="p-left5">
                                            <?php
                                            if (!$is_math_panel) {
                                                echo $sheet->assignment;
                                            } else {
                                                echo $sheet->sublevel_name;
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if (!$is_math_panel) {
                                                echo $sheet->grade;
                                            } else {
                                                echo $sheet->level_name;
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo $sheet->sheet_name ?></td>
                                    </tr>
                                    <?php
                                endforeach;
                            endif;
                            ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Check Out - SAT Preparation Grammar Review -->
<div id="sat-grammar-review" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Check Out - SAT Preparation Grammar Review', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo locale_home_url() ?>/?r=payments">
                <input type="hidden" name="sub-type" id="sat-sub-type" value="0">
                <input type="hidden" name="sat-class" id="sat-class" value="">
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
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label><?php _e('Number of Months', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control" name="sat-months" id="sel-sat-months">
                                        <?php for ($i = 1; $i <= 24; $i++) : ?>
                                        <option value="<?php echo $i ?>"><?php printf(_n('%s month', '%s months', $i, 'iii-dictionary'), $i) ?></option>
                                <?php endfor ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12" style="padding-top: 10px">
                            <div class="box-gray-dialog">
                                <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color708b23">$ <span id="total-amount-sat color708b23">0</span></span>
                            </div>
                        </div>
                    </div>				
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button type="submit" name="add-to-cart" class="btn-custom confirm"></span><?php _e('Check out', 'iii-dictionary') ?></button>
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
<div id="snackbar"></div>
<!-- leave group -->
<div id="modal-leave_group" class="modal fade" aria-hidden="true">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header" >
                <span style="right: 3%;padding-top: 2% !important;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3 style="padding-left: 0%"><?php _e('Leave Group', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-body body-custom-2 black-color">
                <label><?php _e('Do you want Leave Group:&nbsp;', 'iii-dictionary') ?></label><label id="name-group-leave" class="font-600"></label>
            </div>
            <div class="modal-footer footer-custom">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button type="button" data-id="" data-dismiss="modal" aria-hidden="true" id="leave-group-id" class="btn-custom btn-leave-group"><?php _e('Ok', 'iii-dictionary') ?></button>
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
<!-- Check Out - SAT Preparation Writing Practice -->
<div id="sat-grammar-review" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Check Out - SAT Preparation Writing Practice', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo locale_home_url() ?>/?r=payments">
                <input type="hidden" name="sub-type" id="sat-sub-type" value="0">
                <input type="hidden" name="sat-class" id="sat-class" value="">
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
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label><?php _e('Number of Months', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control" name="sat-months" id="sel-sat-months">
                                        <?php for ($i = 1; $i <= 24; $i++) : ?>
                                        <option value="<?php echo $i ?>"><?php printf(_n('%s month', '%s months', $i, 'iii-dictionary'), $i) ?></option>
                                <?php endfor ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12" style="padding-top: 10px">
                            <div class="box-gray-dialog">
                                    <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color708b23">$ <span id="total-amount-sat color708b23">0</span></span>
                            </div>
                        </div>
                    </div>				
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button type="submit" name="add-to-cart" class="btn-custom confirm"></span><?php _e('Check out', 'iii-dictionary') ?></button>
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
<!-- modal message erro payment type new card-->
<div class="modal fade " id="modal-error-message" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content modal-content-custom">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 4% !important;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3 style="padding-left: 1%"><?php _e('Message', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-body body-custom">
                <div class="row">
                    <div class="col-xs-12" id="message-error">
                       
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var page_myaccount =<?php echo $page_my_account ?>;
    var payment_type = <?php echo isset($_SESSION['payments_type'])?$_SESSION['payments_type']:1 ?>;
    (function ($) {
        $(function () {            
            $('.check-calendar1').datepicker({         
                inline: true,            
                showOtherMonths: true, 
                beforeShow: function(elem, dp) { 
                    
                }
            });
            $('.icon-date').click (function (){
                $('.check-calendar1').datepicker("show");
            });
            
            $('.check-no-point').click(function (){
                var nopoints = $('#no-of-points').val();
                if(nopoints == '' || parseInt(nopoints) == 0){                    
                   var $selbox = $("#no-of-points");
                    $selbox.popover({content: '<span class="text-danger">' + 'Enter the Point Number' + '</span>', html: true, trigger: "hover", placement: "bottom"}).popover("show");
                    setTimeout(function(){$selbox.popover("destroy")}, 2000);
//                   
//                    $('[data-toggle="popover"]').popover($('#no-of-points'));
                    return false;
                }
            });
            $("input[type='text'], input[type='password']").keyup(function () {
                $("#save-btn").prop("disabled", false);
            });
            $("[name='birth-m'], [name='birth-d']").change(function () {
                $("#save-btn").prop("disabled", false);
            });
            $(".language_type").change(function () {
                $("#save-btn").prop("disabled", false);
            });
        });
        
        jQuery(document).ready(function () {
            $('#self-study-detail-list').find('.modal-custom-first').mCustomScrollbar({
                axis: "y",
                theme: "rounded-dark",
                scrollButtons: {enable: true}
            });
        });
    })(jQuery);
    var ttp = <?php echo (int)$teacher_tool_price ?>;
    var mttp = <?php echo (int)$math_teacher_tool_price ?>;
    var ssp = <?php echo (int)$self_study_price ?>;
    var ssp_math = <?php echo (int)$self_study_price_math ?>;
    var dp = <?php echo (int)$dictionary_price ?>;
    var sub_sat = <?php echo SUB_SAT_PREPARATION ?>;
    var sub_dic = <?php echo SUB_DICTIONARY ?>;
    var sub_teach = <?php echo SUB_TEACHER_TOOL ?>;
    var adp = <?php echo mw_get_option('all-dictionary-price') ?>;
    var student_multiplier = <?php echo STUDENT_MULTIPLIER ?>;
    var min_student = <?php echo mw_get_option('min-students-subscription') ?>;
    var satGp = <?php echo mw_get_option('sat-grammar-price') ?>;
    var satWp = <?php echo mw_get_option('sat-writing-price') ?>;
    var satStp = <?php echo mw_get_option('sat-test-price') ?>;
    var satMIP = <?php echo mw_get_option('math-sat1-price') ?>;
    var satMIIP = <?php echo mw_get_option('math-sat2-price') ?>;
    var satMIKP1 = <?php echo mw_get_option('math-ik-price1') ?>;
    var satMIKP2 = <?php echo mw_get_option('math-ik-price2') ?>;
    var satMIKP3 = <?php echo mw_get_option('math-ik-price3') ?>;
    var satMIKP4 = <?php echo mw_get_option('math-ik-price4') ?>;
    var satMIKP5 = <?php echo mw_get_option('math-ik-price5') ?>;
    var satMIKP6 = <?php echo mw_get_option('math-ik-price6') ?>;
    var satMIKP7 = <?php echo mw_get_option('math-ik-price7') ?>;
    var satMIKP8 = <?php echo mw_get_option('math-ik-price8') ?>;
    var satMIKP9 = <?php echo mw_get_option('math-ik-price9') ?>;
    var satMIKP10 = <?php echo mw_get_option('math-ik-price10') ?>;
    var satMIKP11 = <?php echo mw_get_option('math-ik-price11') ?>;
    var satMIKP12 = <?php echo mw_get_option('math-ik-price12') ?>;
    var satMIKP = <?php echo mw_get_option('math-ik-price') ?>;
    var ptsr = <?php echo mw_get_option('point-exchange-rate') ?>;
    var M_SINGLE = "<?php _e('month', 'iii-dictionary') ?>";
    var M_PLURAL = "<?php _e('months', 'iii-dictionary') ?>";
    var DICT_EMPTY_ERR = "<?php _e('Please select a Dictionary', 'iii-dictionary') ?>";
    var GRP_EMPTY_ERR = "<?php _e('Please select a group', 'iii-dictionary') ?>";
    var GRP_EXIST_ERR = "<?php _e('This group name is already taken. Please choose a different name.', 'iii-dictionary') ?>";
    var GRP_PW_ERR = "<?php _e('Group password cannot empty', 'iii-dictionary') ?>";
    var M_EMPTY_ERR = "<?php _e('Please select Number of Months', 'iii-dictionary') ?>";
    var NUMBER_INV = "<?php _e('Invalid number', 'iii-dictionary') ?>";
    var LBL_NO_USERS = "<?php _e('Number of Users', 'iii-dictionary') ?>";
    var LBL_NO_M = "<?php _e('Number of Months', 'iii-dictionary') ?>";
    var LBL_NO_STUDENTS = "<?php _e('Number of Students', 'iii-dictionary') ?>";
    var LBL_NO_STUDENTS_ADD = "<?php _e('Number of Students to Increase', 'iii-dictionary') ?>";
    var LBL_NO_M_REMAIN = "<?php _e('Number of Remaining Months', 'iii-dictionary') ?>";
    var LBL_NO_M_ADD = "<?php _e('Number of Months', 'iii-dictionary') ?>";
    var _ISMATH = <?php echo $is_math_panel ? 1 : 0 ?>;
    var _IM4 = <?php echo!empty($_SESSION['method_point']) ? $_SESSION['method_point'] : 0 ?>;
<?php
//remove session method point 
unset($_SESSION['method_point']);
//unset($_SESSION['payments_type']);
?>
</script>
<?php if (!$is_math_panel) : ?>
    <?php get_dict_footer() ?>
<?php else : ?>
    <?php get_math_footer() ?>
<?php endif ?>