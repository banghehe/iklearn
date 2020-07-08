<?php
$date = date('m/d/Y H:i', time());
$subscription_basic = is_math_tutoring_class_subscribed(54);
$subscription_intensive = is_math_tutoring_class_subscribed(55);
$link_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$waiting = MWDB::get_list_waitting_tutoring();
$confirmed = MWDB::get_list_confirmed_tutoring();
// Get list Requested and Confirmed to set default on calendar
$array1 = [];
$array2 = [];
$array1_id = [];
$array2_id = [];
$array1_time = [];
$array2_time = [];
$data_timezon = 0;       // lấy timezon của user
if($waiting) {
    $data_timezon = $waiting[0]->time_zone;
}else if($confirmed){
    $data_timezon = $confirmed[0]->time_zone;
}
if(isset($waiting)){
      if(count($waiting)>1) {
          foreach($waiting as $req) {
            array_push($array1,$req->date);
            array_push($array1_id,$req->id);
            array_push($array1_time,$req->time);
          }
      }
      else {
            array_push($array1,$waiting[0]->date);
            array_push($array1_id,$waiting[0]->id);
            array_push($array1_time,$waiting[0]->time);
    }
} 
if(isset($confirmed)){
      if(count($confirmed)>1) {
          foreach($confirmed as $conf) {
             array_push($array2,$conf->date);
             array_push($array2_id,$conf->id);
             array_push($array2_time,$conf->time);
          }
      }
      else {
          array_push($array2,$confirmed[0]->date);
          array_push($array2_id,$confirmed[0]->id);
          array_push($array2_time,$confirmed[0]->time);
    }
} 
if (isset($_POST)) {
    add_math_request($_POST);
}
$subtitle = '';
$is_link = array();
$page_session;

// Math Sat 1
$page_session = 1;
$header_title = __('ikMath Tutoring Plan', 'iii-dictionary');
$form_action = locale_home_url() . '/?r=sat-preparation/' . $active_tab . '&client=math-sat1';
$subtitle = '';
$is_change = false;
if (in_array($class_type, array(9, 10, 11, 12, 13, 14))) {
    $is_sat_english_subscribed_package = is_sat_class_subscribed(52);
}

?>
<?php get_math_header($header_title, 'purple') ?>
<?php get_dict_page_title($header_title, '', $subtitle, $tab_options, $tab_info_url, $is_change, $is_link) ?>
<p><?php
//    _e('You can subscribe monthly Math Tutoring Plan. The Light Plan is three times a week '
//            . 'for 30 minutes private tutoring online, and the Intensive Plan is 5 times a week for 30 minutes tutoring'
//            . '. You can choose the date and time you wish. Sign up for the Math Tutoring Program below. ', 'iii-dictionary')
    ?></p>
<div class="css-parent-row">
    <div class="div-left-tutoring">
        <div id="sandbox-container">
            <span class="txt-tu">Tutoring Calendar</span>
            <div class='line-schedule' style="border: none;"></div>
            <input type="text" id="dateHidden" style="display: none;" />
        </div>
        <div class="line-schedule"></div>
        <div class="purchase-bottom">
            <span class="green-purchase"></span>Purchased & Waiting
            <span class="red-purchase"></span>Confirmed
        </div>
    </div>   

<div class="schedule">
    <div class="border-schedule"> 
        <div class="txt-a-tutoring">Schedule a Tutoring</div>
        <div class="line-schedule1"></div>
        <div class="text-select-schedule" name="selector">Select a Time Zone</div>
        <select id="select-time-zone" class="select-background select-box-it form-control">
            <option value="0" data-value="">Select Time Zone</option>
            <option value="1" data-value="-5">New York</option>
            <option value="2" data-value="-6">Minneapolis</option>
            <option value="3" data-value="-5">Colorada</option>
            <option value="4" data-value="-7">San Francisco</option>
            <option value="5" data-value="-10">Hawaii</option>
            <option value="6" data-value="+10">Guam</option>
            <option value="7" data-value="+9">Tokyo</option>
            <option value="8" data-value="+9">Seoul</option>
            <option value="9" data-value="+8">Beijing</option>
            <option value="10" data-value="+8">Xianyang</option>
            <option value="11" data-value="+7">Hanoi</option>
            <option value="12" data-value="+7">Bangkok</option>
            <option value="13" data-value="+7">Myanmar</option>
            <option value="14" data-value="+6">Bangladesh</option>
            <option value="15" data-value="+5">Sri Lanka</option>
            <option value="16" data-value="+5">New Delhi</option>
            <option value="17" data-value="+5">Mumbai</option>
            <option value="18" data-value="0">London</option>
            <option value="19" data-value="+5">Sydney</option>
        </select> 
        <div class='margin-top-4'>
            <?php
                echo '<table class="schedule-table">';
                    echo '<tr style="font-style: italic;">';
                        echo '<td>Hour</td>';
                        echo '<td>Minute</td>';
                        echo '<td>am/pm</td>';
                        echo '<td></td>';
                        echo '<td>Hour</td>';
                        echo '<td>Minute</td>';
                        echo '<td>am/pm</td>';
                    echo '</tr>';
                    echo '<tr>';
                        echo '<td>';
                            echo '<select id="hour-start" name="hour-start" class="select-box-it form-control price-realtime" style="font-weight: bold;">';
                                for($i=1;$i<=12;$i++) {
                                    if($i>9){
                                        echo '<option value="'.$i.'">'.$i.'</option>';
                                    }else{
                                        echo '<option value="'.$i.'">'.'0'.$i.'</option>';
                                    }
                                }
                            echo '</select>';
                        echo '</td>';
                        echo '<td>';
                            echo '<select id="minute-start" name="minute-start" class="select-box-it form-control price-realtime" style="font-weight: bold;">';
                                for($i=0;$i<60;$i++) {
                                    if($i>9){
                                        if($i%30 == 0)echo '<option value="'.$i.'">'.$i.'</option>';
                                    }else{
                                        if($i%30 == 0)echo '<option value="'.$i.'">'.'0'.$i.'</option>';
                                    }
                                }
                            echo '</select>';
                        echo '</td>';
                        echo '<td>';
                            echo '<select id="format-start" name="format-start" class="select-box-it form-control color-pink price-realtime">';
                                echo '<option value="am">am</option>';
                                echo '<option value="pm">pm</option>';
                            echo '</select> ';
                        echo '</td>';
                        echo '<td style="text-align: center;">to</td>';
                        echo '<td>';
                            echo '<select id="hour-end" name="hour-end" class="select-box-it form-control price-realtime" style="font-weight: bold;">';
                                for($i=1;$i<=12;$i++) {
                                    if($i>9){
                                        echo '<option value="'.$i.'">'.$i.'</option>';
                                    }else{
                                        echo '<option value="'.$i.'">'.'0'.$i.'</option>';
                                    }
                                }
                            echo '</select>';
                        echo '</td>';
                        echo '<td>';
                            echo '<select id="minute-end" name="minute-end" class="select-box-it form-control price-realtime" style="font-weight: bold;">';
                                for($i=0;$i<60;$i++) {
                                    if($i>9){
                                        if($i%30 == 0)echo '<option value="'.$i.'">'.$i.'</option>';
                                    }else{
                                        if($i%30 == 0)echo '<option value="'.$i.'">'.'0'.$i.'</option>';
                                    }
                                }
                            echo '</select>';
                        echo '</td>';
                        echo '<td>';
                            echo '<select id="format-end" name="format-end" class="select-box-it form-control color-pink price-realtime" style="padding-left: 7% !important;">';
                            echo '<option value="am">am</option>';
                            echo '<option value="pm">pm</option>';
                            echo '</select>';
                        echo '</td>';
                    echo '</tr>    ';
                echo '</table>';
            ?>
        </div>
        <div style="margin-top: 4%;">
            <select id="select-subject" class="select-box-it form-control select-background">
                <option value="0">Select subject</option>
                <option value="1">English</option>
                <option value="2">Math</option>
                <option value="3">History</option>
                <option value="4">Art and Design</option>
                <option value="5">Science</option>
                <option value="6">Music</option>
                <option value="7">Other</option>
            </select>
        </div>
        <div>
            <!-- Đây là lớp private chỉ có học sinh đăng ký và giáo viên có thể xem -->
            <div class="text-write-sub">Name of Tutoring Plan</div>
            <input id="name-subject-private" type="text" name="" value="" maxlength="50" class="css-txt-write">
        </div>

        <div>
            <!-- Đây là lớp private chỉ có học sinh đăng ký và giáo viên có thể xem -->
            <div class="text-write-short">Short Message(200 character limit)</div>
            <input id="short-message-private" type="text" name="" value="" maxlength="200" class="css-txt-write">
        </div>
        
        <div style="display: -webkit-box;">
            <div class="radio" style="width: 50%">															
                <input id="rdo-yes" type="checkbox" class="gCheckbox css-cb1" name="joinchat" value="1" checked>
                <label for="rdo-yes" class="lab-checkbox" style="padding-left: 0px;"><?php _e('Request Previous Tutor', 'iii-dictionary') ?></label>
            </div>
            
            <div class="radio" style="width: 50%;float: right;margin-top: 10px;">
                <input id="rdo-no" type="checkbox" class="gCheckbox css-cb1" name="joinchat" value="0">  
                <label for="rdo-no" class="lab-checkbox" style="padding-left: 0px;"><?php _e('Request a New Tutor', 'iii-dictionary') ?></label>
            </div>
        </div>
        <div>
            <select id="select-tutor" class="select-box-it form-control select-background">
                <option value="0">Select a Tutor</option>
                <option value="98">Peter Chung</option>
                <option value="1845">teacher Y1</option>
                <option value="1846">teacher Y2</option>
                <option value="1847">teacher Y3</option>
                <option value="69">teacher Y4</option>
                <option value="70">teacher Y5</option>
            </select>
        </div>
        <div class="schedule1">
            <div class="margin-top-4 css-bottom-schedule">
                <span>Duration of this Tutoring: </span><span id="time-schedule" style="color: #718A24;font-weight: bold;">0 min</span>
                <span>Estimated Price: </span><span id="price-schedule" style="color: #718A24;font-weight: bold;">$0</span>
            </div>
            <div class="margin-top-4 css-btn-schedule"> 
                <button class="btn-schedule css-color-white">Schedule</button>
            </div>
        </div>
        <div class="line-schedule1"></div>
        <div class="css-italic" style="font-size: 14px;">Scheduling a tutoring session requires points. If you do not have enough points please click here. <a href="purchase-points-dialog" class="css-color-D12B51 css-font-style need-point btn-sub-need-point">Need Points?</a></div>
    </div>
</div>
</div>
<!-- Subscrible tutoring modal -->
<div id="sub-tutoring-modal" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black" >
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Subscrible to Tutoring', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo home_url_ssl() ?>/?r=payments">
                <div class="modal-body">
                    <div class="row">
                        <div class=" form-group" style="padding-left: 1%">
                            <input type="hidden" id="name-sub-post" name="sub-type" value="30">
                            <input type="hidden" id="date-sub-post" name ="date-plan" value="0">
                            <input type="hidden" id="time-sub-post" name="time" value="0">
                            <input type="hidden" id="duration-sub-post" name="duration" value="0">
                            <input type="hidden" id="tutor-sub-post" name="tutor" value="0">
                            <div style="color: #000;">Summary of Your Schedule</div>
                            <div class='line-schedule2'></div>
                            <div class="left-15">
                                <div class='inline'><span class='css-span1'>Subject:</span></div>
                                <div class='inline'><span class='css-span1'>Name:</span></div>
                                <div class='inline'><span class='css-span1'>Message:</span></div>
                                <div class='inline'><span class='css-span1'>Date:</span></div>
                                <div class='inline'><span class='css-span1'>Tutor:</span></div>
                                <div class='inline'><span class='css-span1'>Time:</span></div>
                                <div class='inline'><span class='css-span1'>Duration:</span></div>
                            </div>
                            <div class="right-85">
                                <p class="col-xs-12 font-bold-4c4c4c css-padding-left-0" id="date-subject"></p>
                                <p class="col-xs-12 font-bold-4c4c4c css-padding-left-0" id="date-name"></p>
                                <p class="col-xs-12 font-bold-4c4c4c css-padding-left-0" id="short-message"></p>
                                <p class="col-xs-12 font-bold-4c4c4c css-padding-left-0" id="date-sub" ></p>
                                <p class="col-xs-12 font-bold-4c4c4c css-padding-left-0" id="tutor-sub" ></p>
                                <p class="col-xs-12 font-bold-4c4c4c css-padding-left-0" id="time-sub"</p>
                                <p class="col-xs-12 font-bold-4c4c4c css-padding-left-0" id="duration-sub" ></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 padding-top-2">
                            <div class="box-gray-dialog" style="text-align: left">
                                <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color708b23"> $</span> <span class="currency color708b23" name='total-amount' id="ss-total-amount">0</span>
                            </div>
                        </div>
                    </div>				
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button type="submit" id="add-to-cart-tutoring-plan" name="add-to-cart" class="btn-custom"><?php _e('Check out', 'iii-dictionary') ?></button>
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
<!-- Subscrible tutoring modal-cancel -->
<div id="sub-tutoring-modal-cancel" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Canceling Tutoring Session', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo home_url_ssl() ?>/?r=payments">
                <div class="modal-body" style="color: #000;">
                    <!-- load date from ajax  -->
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-6" style="width: 100%">
                            <div class="form-group">
                                <button type="button" id="btn-refunded-points" class="btn-custom"><?php _e('OK', 'iii-dictionary') ?></button>
                            </div>
                        </div>
                    </div>
                </div>			
            </form>
        </div>
    </div>
</div>


<div id="modal-tutoring-reminder" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Canceling Tutoring Session', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo home_url_ssl() ?>/?r=payments">
                <div class="modal-body" style="color: #000;">
                    <!-- load date from ajax  -->
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-6" style="width: 100%">
                            <div class="form-group">
                                <button type="button" id="btn-refunded-points" class="btn-custom"><?php _e('OK', 'iii-dictionary') ?></button>
                            </div>
                        </div>
                    </div>
                </div>			
            </form>
        </div>
    </div>
</div>
<div id="purchase-points-dialog" class="modal fade modal-red-brown">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Purchase Points', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo home_url_ssl() ?>/?r=payments" onsubmit="return isValidForm()">
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
<div id="modal-cancel-schedule" class="modal fade modal-red-brown">
    <div class="modal-dialog modal-custom-first css-no-padding css-modal-center">
        <div class="modal-content boder-black">
            <span style="right: 3%;padding-top: 2%;margin-top: 10px;" href="#" data-dismiss="modal" aria-hidden="true" class="close icon-close3" ></span>
            <div class="modal-footer footer-custom" style="padding-left: 7%;padding-right: 7%;padding-bottom: 5% !important;">
                <div class="cancel-schedule txt-request-day txt-message-error" style="padding-bottom: 20px">Do you want to make another schedule on this date?</div>
                <input type="button" id="btn-maks-another-schedule" class="css-ok-rs1" value="Make Another Schedule">
                <div>
                    <div class="txt-cancel-schedule">Do you want to cancel schedule?</div>
                    <div id="load-data-cancel-schedule" style="color: #000">
                        <!--Load data ajax-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<div id="modal-message-error" class="modal fade modal-red-brown">
    <div class="modal-dialog modal-custom-first css-no-padding">
        <div class="modal-content boder-black">
            <span style="right: 3%;padding-top: 2%;margin-top: 10px;" href="#" data-dismiss="modal" aria-hidden="true" class="close icon-close3" ></span>
            <div id="view-result-writing-body" style="background: #fff;color: #000;">
            </div>
            <div class="modal-footer footer-custom" style="padding-left: 7%;padding-right: 7%;padding-bottom: 5% !important;">
                <div class="cancel-schedule txt-request-day txt-message-error"></div>
                <input type="button" id="btn-ok-request-day" class="css-ok-rs1" value="OK">
            </div>
        </div>
    </div>
</div>
<script>
    var pst = "<?php echo mw_get_option('price_schedule_tutoring') ?>";
    var MATH_TUTORING = "<?php echo mw_get_option('price-math-tutoring') ?>";
    var NAME_MATH_TUTORING = "<?php echo mw_get_option('name-math-tutoring') ?>";
    var MATH_INTENSIVE_TUTORING = "<?php echo mw_get_option('price-math-intensive-tutoring') ?>";
    var NAME_MATH_INTENSIVE_TUTORING = "<?php echo mw_get_option('name-math-intensive-tutoring') ?>";
    var BASIC = <?php echo $subscription_basic ? 'true' : 'false' ?>;
    var INTENSUVE = <?php echo $subscription_intensive ? 'true' : 'false' ?>;
    jQuery('#tutoring-plan .article-header').attr('style','background: #fff;border-bottom: 1px solid #d6d6d6;')
    jQuery('#tutoring-plan .entry-content').attr('style','background: #fff;color: #000;')
    jQuery('#span-title').html('Get help with your Math');
    
    if ((window.matchMedia('screen and (max-width: 767px)').matches)) {
        jQuery('#tutoring-plan .article-header').attr('style','background: #fff;border-bottom: 1px solid #d6d6d6;padding-top:20px;')
        jQuery('#span-title').css('bottom', '-12px');
        jQuery('#btn-main-menu').css('left', '0px');
        jQuery('.container-acc-login-signup-online').css('margin-top', '10px');
    }
</script>
<?php if (empty($client)) : ?>
    <?php get_dict_footer() ?>
<?php else : ?>
    <?php get_math_footer() ?>
<?php endif ?>
<!--<link href="lib/css/bootstrap.min.css" rel="stylesheet">-->
<link href="/wp-content/themes/ik-learn/library/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet" >
<script src="/wp-content/themes/ik-learn/library/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script>
    (function ($) {
        $(function (){
            var test = <?php echo json_encode($date)?>;
            var get_timezon = <?php echo json_encode($data_timezon)?>;
            var is_login = <?php if(is_user_logged_in()){echo 1;}else{echo 0;}?>;
            var today = new Date(test);
            today.setHours(today.getHours() + parseInt(get_timezon));
            var dd = today.getDate();
            var mm = today.getMonth()+1; //January is 0!
            var yyyy = today.getFullYear();
            var now_hour = today.getHours();
            var now_minute = today.getMinutes();
            
//            console.log(strtotime(test+ '+7hour'));
//            $b = (int)strtotime('now +'.$time_zone .'hour');
            if(dd<10){
                dd='0'+dd;
            } 
            if(mm<10){
                mm='0'+mm;
            } 
            var today = mm+'/'+dd+'/'+yyyy;   // 19-04-2018
            var date_time_now = today +" "+ now_hour + ":" + now_minute;
            var active_dates = [];
            var active_dates_conf = [];
            var active_dates_expried = [];
            var arr_id_can_cancel = [];         // array id có thể cancel
            var active_dates_conf_expried = [];
            var array_date = <?php echo json_encode($array1); ?>;               // 2018-04-17, 2018-04-18
            var array_date_id = <?php echo json_encode($array1_id); ?>;               // 2018-04-17, 2018-04-18
            var array_date_confirmed = <?php echo json_encode($array2); ?>;     //
            var array_date_confirmed_id = <?php echo json_encode($array2_id); ?>;     //
            var array_date_time = <?php echo json_encode($array1_time); ?>;     //
            var array_date_confirmed_time = <?php echo json_encode($array2_time); ?>;     //
//            console.log(test.getMinutes());
//            console.log(Date.parse(test.addHours(5)));
//            console.log((date_time_now));
            if(array_date!= '' || active_dates_conf!='') {
                if(array_date!= '') {
                    for(var i = 0; i<array_date.length; i++) {
                        // Kiểm tra nếu date có hết hạn
                        var str1 = array_date_time[i].split("~");
                        var str2 = str1[1].split(":");
                        var str = array_date[i].split("-"); // 
                        var date_time_data = str[1]+"/"+str[2]+"/"+str[0]+" "+str2[0]+":"+str2[1];
//                        console.log(date_time_data);
//                        console.log(date_time_now);
                        if( (Date.parse(date_time_data)) > (Date.parse(date_time_now))){
                            var string = array_date[i].split('-');
                            if(string[2]<10) {
                                string[2] = string[2].slice(-1);
                            }
                            var date = string[2]+'/'+string[1]+'/'+string[0];
                            active_dates.push(date);    // 17/04/2018, 19/04/2018
                            arr_id_can_cancel.push(array_date_id[i]);
                        }else{
                            active_dates_expried.push(array_date_id[i]);
                        }
                    }
                }
//                alert(arr_id_can_cancel);
                if(array_date_confirmed != ''){
                    for(var j = 0; j<array_date_confirmed.length; j++) {
                        var str1 = array_date_confirmed_time[j].split("~");
                        var str2 = str1[1].split(":");
                        if(str2[2] == 'PM'){
                            str2[0] = parseInt(str2[0])+12;
                        }
                        var str = array_date_confirmed[j].split("-"); // 
                        var date_time_conf_data = str[1]+"/"+str[2]+"/"+str[0]+" "+str2[0]+":"+str2[1];
//                        console.log(date_time_conf_data);
                        if( (Date.parse(date_time_conf_data)) > (Date.parse(date_time_now))){
                            var string1 = array_date_confirmed[j].split('-');
                            if(string1[2]<10) {
                                string1[2] = string1[2].slice(-1);
                            }
                            var date1 = string1[2]+'/'+string1[1]+'/'+string1[0];
                            active_dates_conf.push(date1);
                        }else{
                                active_dates_conf_expried.push(array_date_confirmed_id[j]);
                        }
                    }
                }
                
                // Hoàn lại tiền khi các đặt lịch  là quá khứ
                if(active_dates_expried.length >=1) {
                    for(var m = 0; m < active_dates_expried.length ; m++){
                    $.post(home_url + "/?r=ajax/auto_cancel_schedule",{id:active_dates_expried[m]},
                        function(data){
                        }  
                    );
                    }
                }
                $('#sandbox-container').datepicker({
                    todayHighlight: true,
                    startDate: today,
                    beforeShowDay: function(date){
                        var d = date;
                        var curr_date = d.getDate();
                        var curr_month = d.getMonth() + 1; //Months are zero based
                        if(curr_month < 10) {
                            curr_month = "0"+curr_month;
                        }
                        var curr_year = d.getFullYear();
                        var formattedDate = curr_date + "/" + curr_month + "/" + curr_year;
                // Xử lý khi vừa load trang sẽ set màu cho những date là waiting or comfirmed     
                        if(active_dates.length >=1){
                            for(var $i=0;$i<active_dates.length;$i++){
                            }
                        }
                        if($.inArray(formattedDate, active_dates) != -1){
                            return {
                               classes: 'activeClass'
                            };
                        }
                        if($.inArray(formattedDate, active_dates_conf) != -1){
                            return {
                               classes: 'activeClass1'
                            };
                        }
                        return;
                    }
                });
                
                $('.table-condensed').find('.prev').addClass("css-show");
                $('.table-condensed').find('.next').addClass("css-show");
                $(".datepicker .datepicker-days").on('click', 'td.activeClass', function (e) {
                    var get_date = $(this).text();
                    var date1 = $(".datepicker-switch").text();
                    var st = date1.split(" ");
                    var month_text = st[0];
                    switch(month_text) {
                        case "January" : 
                        var month = 1; 
                        break;
                        
                        case "February" : 
                        var month = 2; 
                        break;
                        
                        case "March" : 
                        var month = 3; 
                        break;
                        
                        case "April" : 
                        var month = 4; 
                        break;
                        
                        case "May" : 
                        var month = 5; 
                        break;
                        
                        case "June" : 
                        var month = 6; 
                        break;
                        
                        case "July" : 
                        var month = 7; 
                        break;
                        
                        case "August" : 
                        var month = 8; 
                        break;
                        
                        case "September" : 
                        var month = 9; 
                        break;
                        
                        case "October" : 
                        var month = 10; 
                        break;
                        
                        case "November" : 
                        var month = 11; 
                        break;
                        
                        case "December" : 
                        var month = 12; 
                        break;
                    }
                    var year = st[1].substring(0,4);
                    var full_date = month+"/"+get_date+"/"+year;
                    $('#dateHidden').val(full_date);
                    $.post(home_url + "/?r=ajax/load_list_can_schedule",{arr_id:arr_id_can_cancel,day:full_date},
                        function(data){
                            $.post(home_url + "/?r=ajax/check_login_user",
                            function(data1){
                                if(data1 == 1){
                                    $('#modal-cancel-schedule').modal('show');
                                    $("#load-data-cancel-schedule").html(data);
                                    $('.btn-close-window-schedule').click(function(){
                                        $('#modal-cancel-schedule').modal('hide');
                                    });
                                    $('.btn-yes-cancel').one("click",function(){
                                        var point = $(this).attr("data-points");
                                        var id= $(this).attr("data-id");
                                        $.post(home_url + "/?r=ajax/update_total_point", {point:point,id:id}, function (data) {
                                            window.location.reload();
                                        });
                                    });
                                    $('#btn-maks-another-schedule').click(function(){
                                        $('#modal-cancel-schedule').modal("hide");
                                    });
                                }else{
                                    window.location.replace(home_url+'/?r=login');
                                }
                            });
                            
                        }  
                    );
                    $(this).attr('style','border="2px solid #1155D5 !important;"');
                });
            }
            else{
                $('#sandbox-container').datepicker({
                    startDate: today,
                });
                $('.table-condensed').find('.prev').addClass("css-show");
                $('.table-condensed').find('.next').addClass("css-show");
            }
            //$( document ).ready(function() {
                $('.btn-schedule').click(function (){
                    var date = $('#dateHidden').val();
                    var time_zone =$('#select-time-zone :selected').attr("data-value");
                    var subject =$('#select-subject :selected').text();
                    var sub = $('#name-subject-private').val();
                    var message = $('#short-message-private').val();
                    var tutor_id = $('#select-tutorSelectBoxItText').attr('data-val');
                    hour = $('select[name=hour-start]').val();
                    minute = $('select[name=minute-start]').val();
                    if(minute < 10) {
                        minute = "0"+minute;
                    }
                    format = $('select[name=format-start]').val();
                    if(format == "pm"){
                        hour = parseInt(hour) + 12;
                    }
                    if(date=='') {
                        $('#modal-message-error .txt-message-error').html("Please select a day from the calendar before entering the schedule detail.");
                        $('#modal-message-error').modal("show");
                    }else{
                        
                        hour1 = $('select[name=hour-end]').val();
                        minute1 = $('select[name=minute-end]').val();
                        if(minute1 < 10) {
                            minute1 = "0"+minute1;
                        }
                        format1 = $('select[name=format-end]').val();
                        if(format1 == "pm"){
                            hour1 = parseInt(hour1) + 12;
                        }
                        var time_schedule = (hour1 - hour)*60 + (minute1 - minute);
                        if(date=='' || time_zone== '0' || subject=='Select subject' || sub.trim() =='') {
                            $('#modal-message-error .txt-message-error').html("Please select all the required fields.");
                            $('#modal-message-error').modal("show");
                        } else if(time_schedule <=0) {
                            $('#modal-message-error .txt-message-error').html("Time error!");
                            $('#modal-message-error').modal("show");
                        }
                        else {
                            var date_time_start = hour+':'+minute+':'+format.toUpperCase(); // Định dạng 24h
                            var date_time_end = hour1+':'+minute1+':'+format1.toUpperCase(); //Định dạng 24h
                            if(hour > 12) {
                                hour = hour - 12;
                            }
                            
                            if(hour1 > 12) {
                                hour1 = hour1 -12;
                            }
                            
                            var date_time_start_format = hour+':'+minute+format.toLowerCase();  // Định dạng 12h
                            var date_time_end_format = hour1+':'+minute1+format1.toLowerCase();  // Định dạng 12h
                             $('#sub-tutoring-modal').modal('show');
                            $('#date-sub').html(date);
                            $('#time-sub').html(date_time_start_format+" to "+date_time_end_format);
                            $('#duration-sub').html(time_schedule + " Minutes Total");
                            $('#ss-total-amount').html(time_schedule*pst/100);
                            $('#date-sub-post').val(date);
                            $('#date-subject').html(subject);
                            $('#short-message').html(message);
                            $('#date-name').html(sub);
                            if($('#tutor-sub-post').val()==0) {
                                $('#tutor-sub').html('Request Previous Tutor');
                            } else {
                                $('#tutor-sub').html('Request a New Tutor');
                            }
                            $('#duration-sub-post').val(time_schedule*pst/100);
                        // add data on button Checkout    
                            $('#add-to-cart-tutoring-plan').attr('data-date',date);
                            $('#add-to-cart-tutoring-plan').attr('data-time',date_time_start+" ~ "+date_time_end);
                            $('#add-to-cart-tutoring-plan').attr('data-subject',subject);
                            $('#add-to-cart-tutoring-plan').attr('data-tutor',$('#tutor-sub-post').val());
                            $('#add-to-cart-tutoring-plan').attr('data-zone',time_zone);
                            $('#add-to-cart-tutoring-plan').attr('data-subject_private',sub);
                            $('#add-to-cart-tutoring-plan').attr('total-time',time_schedule);
                            $('#add-to-cart-tutoring-plan').attr('data-short-message',message);
                            var time = date_time_start+" ~ "+date_time_end;
                            var tutor = $('#tutor-sub-post').val();
                            $.post(home_url + "/?r=ajax/insert_ikmath_tutoring_plan",{date: date,subject:subject,time:time,zone:time_zone,subject_private:sub,total:time_schedule,tutor:tutor,short_message:message,tutor_id: tutor_id},
                                function(data){

                                }  
                            );
                        }
                    }
                });
            //});
            $('#add-to-cart-tutoring-plan').click (function(){
                var date = $('#add-to-cart-tutoring-plan').attr('data-date');
                var time = $('#add-to-cart-tutoring-plan').attr('data-time');
                var zone = $('#add-to-cart-tutoring-plan').attr('data-zone');
                var subject = $('#add-to-cart-tutoring-plan').attr('data-subject');
                var total = $('#add-to-cart-tutoring-plan').attr('total-time');
                var tutor = $('#add-to-cart-tutoring-plan').attr('data-tutor');
                var subject_private = $('#add-to-cart-tutoring-plan').attr('data-subject_private');
                var short_message = $('#add-to-cart-tutoring-plan').attr('data-short-message');
                /*$.post(home_url + "/?r=ajax/insert_ikmath_tutoring_plan",{date: date,subject:subject,time:time,zone:zone,subject_private:subject_private,total:total,tutor:tutor,short_message:short_message},
                    function(data){
                    }  
                );*/
            });
            $('#short-message-private').unbind('keyup change input paste').bind('keyup change input paste',function(e){
                var $this = $(this);
                var val = $this.val();
                var valLength = val.length;
                var maxCount = $this.attr('maxlength');
                if(valLength>maxCount){
                    $this.val($this.val().substring(0,maxCount));
                }
            }); 
            function total_price_schedule() {
                hour = parseInt($('select[name=hour-start]').val());
                minute = parseInt($('select[name=minute-start]').val());
                format = $('select[name=format-start]').val();
                if(format == 'pm') {
                    hour += 12;
                }
                hour1 = parseInt($('select[name=hour-end]').val());
                minute1 = parseInt($('select[name=minute-end]').val());
                format1 = $('select[name=format-end]').val();
                if(format1 == 'pm') {
                    hour1 += 12;
                }
                
                var time = (hour1 - hour)*60 + (minute1 - minute);
                if(time >0) {
                    $('#time-schedule').text(time+"min");
                    $('#price-schedule').text("$"+time * pst/100);
                }
            }
            $('.price-realtime').change (function (){
                total_price_schedule();
            });
            $('.datepicker-switch').click(function() { return false; }); 
            $('.btn-sub-need-point').click (function(e){
                e.preventDefault();
                $('#purchase-points-dialog').modal('show');
            });
            $('.check-no-point').click(function (){
                var nopoints = $('#no-of-points').val();
                if(nopoints == '' || parseInt(nopoints) == 0){                    
                   var $selbox = $("#no-of-points");
                    $selbox.popover({content: '<span class="text-danger">' + 'Enter the Point Number' + '</span>', html: true, trigger: "hover", placement: "bottom"}).popover("show");
                    setTimeout(function(){$selbox.popover("destroy");}, 2000);
//                   
//                    $('[data-toggle="popover"]').popover($('#no-of-points'));
                    return false;
                } else {
                    $('#total-amount-points').html(nopoints);
                }
                
            });
            $('#btn-no').click (function (){
                location.reload();
            });
            $('#btn-ok-cancel').click (function (){
                $('#modal-question').modal('hide');
                $('#sub-tutoring-modal-cancel').modal('show');
            });
            $('#sub-tutoring-modal-cancel').on('show.bs.modal',function(){
                var str = $('#dateHidden').val();
                var str1 = str.split('/');
                var date = str1[2]+'-'+str1[0]+'-'+str1[1];
                $.get(home_url + "/?r=ajax/get_info_cancel_schedule", {date:date}, function (data) {
                    $('#sub-tutoring-modal-cancel').find('.modal-body').html(data);
                    $('#sub-tutoring-modal-cancel').find('.modal-content').css("margin-top", "20%");
                });
            });
            
            $('.radio input:checkbox').click(function () {
                //$('.radio input:checkbox').not(this).prop('checked', false);
                $(".radio input:checkbox").prop('checked', false);
                $(this).prop('checked', true);
            });
            $('#rdo-yes').click(function(){
                $('#tutor-sub-post').val('0');
                $('#select-tutor').parent().css("visibility","initial");
            });
            $('#rdo-no').click(function(){
                $('#tutor-sub-post').val('1');
                $('#select-tutor').parent().css("visibility","hidden");
            });
            $(".radio input:checkbox").change(function() {
                var checked = $(this).is(':checked');
                $(".radio input:checkbox").prop('checked',false);
                if(checked) {
                    $(this).prop('checked',true);
                }
            });
            $('#btn-ok-request-day').click(function(){
                $('#modal-message-error').modal("hide");
            });
        }); //close
    })(jQuery);
    </script>
    