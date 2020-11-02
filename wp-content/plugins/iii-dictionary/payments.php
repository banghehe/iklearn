<?php

//	var_dump($_REQUEST);die;
$is_math_panel = is_math_panel();
$_page_title = __('Payments', 'iii-dictionary');
if (isset($_POST['add-to-cart'])) {
    
    $_SESSION['payment'] = $_POST; 
    
//    echo '<pre>';
//    print_r($_POST);
//    die;
    ik_add_to_cart($_POST);
    wp_redirect(home_url_ssl() . '/iklearn/?r=payments');
    exit;
}

if (isset($_POST['delete-cart-item'])) {
    
    $_SESSION['payment'] = '';
    ik_delete_cart_item($_POST);

    wp_redirect(home_url_ssl() . '/iklearn/?r=payments');
    exit;
}

// process transaction
if (isset($_POST['process'])) {
//    var_dump($_POST);die;
    if ($_POST['payment-method'] == 2) {
        // pay with paypal
    } else if ($_POST['payment-method'] == 3) {
        // pay with point balance
        ik_process_point_payment();
    } else {
        // pay with credit card
        ik_process_transaction();
    }
    
    $_SESSION['payment'] = '';
    
    if (!empty($_SESSION['return_math'])) {
        $ref = $_SESSION['return_math'];
        unset($_SESSION['return_math']);
        wp_redirect($ref);
    } else {
        $payments_type = 0;
        wp_redirect(home_url_ssl() . '/iklearn/?r=my-account');
    }
    exit;
}
$point_ex_rate = mw_get_option('point-exchange-rate');
$cart_items = get_cart_items();
$cart_amount = is_null(get_cart_amount()) ? 0 : get_cart_amount();
//echo '<pre>';
//print_r($cart_items);
//die;
//var_dump($cart_items);die;
class obj_cart {
 
    public $extending;
    public $no_months;
    public $id;
    public $type;
    public $typeid;
    public $tt_months;
    public $dictionary;
    public $dictionary_id;
    public $no_students;
    public $no_months_dict;
    public $price;
    public $group_id;
    public $group_name;
    public $group_pass;
 
}
$obj_fisrt_cart = new obj_cart;
if($cart_items !=""){
    if(count($cart_items)==1){
        $obj_fisrt_cart = $cart_items;
    }else{
        $arr_id = [];
        array_push($arr_id,$cart_items[0]->typeid);
        $obj_fisrt_cart=$cart_items[0];
        for($i=1;$i<count($cart_items);$i++){
            in_array($cart_items[$i]->typeid,$arr_id);
            
        }
    }
}
//var_dump($obj_fisrt_cart);die;
?>
<?php if (!$is_math_panel) : ?>
    <?php get_dict_header($_page_title) ?>
<?php else : ?>
    <?php get_math_header($_page_title, 'red-brown') ?>
<?php endif ?>
<?php get_dict_page_title($_page_title) ?>
<script>
    (function ($) {
        jQuery('#payments .page-title').css('color', '#000000 !important');
    })(jQuery);
</script>
<form method="post" action="<?php echo home_url_ssl() ?>/iklearn/?r=payments">
    <div class="row">
        <div class="col-xs-12 css-pad-left0-pay">											
            <div class="row">
                <div class="col-sm-12">
                    <h2 class="title-border colorad8425"><?php _e('Subscription to purchase', 'iii-dictionary') ?></h2>
                </div>
                <div class="col-sm-12">
                    <div class="">
                        <div class="scroll-list" style="max-height: 300px">
                            <table class="table table-striped table-style2 text-center table-custom-2">
                                <thead>
                                    <tr>
                                        <th class="hidden-xs"></th>
                                        <th><?php _e('Type', 'iii-dictionary') ?></th>
                                        <th><?php _e('Months', 'iii-dictionary') ?></th>
                                        <th><?php _e('Size of Class', 'iii-dictionary') ?></th>
                                        <th><?php _e('No. of License', 'iii-dictionary') ?></th>
                                        <th class="hidden-xs"><?php _e('Dictionary', 'iii-dictionary') ?></th>
                                        <th><?php _e('No. of Points', 'iii-dictionary') ?></th>
                                        <th><?php _e('Price', 'iii-dictionary') ?></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
//                                    var_dump($cart_items);
                                    $arr_id = [];
                                    if (!empty($cart_items)) :
                                        $i=0;
                                        foreach ($cart_items as $key => $item) :
//                                            var_dump($item);die;
                                            ?>
                                            <tr>
                                                <?php $i++; ?>
                                                <td class="hidden-xs"><?php echo $i ?>.</td>
                                                <td><?php
                                                    echo $item->type;
                                                    echo $item->extending ? ' ' . __('(Additional)', 'iii-dictionary') : ''
                                                    ?>
                                                </td>
                                                <td><?php echo $item->no_months;
                                                ?></td>
                                                <?php
                                                    array_push($arr_id,$item->typeid);
                                                ?>
                                                <td><?php echo in_array($item->typeid, array(SUB_TEACHER_TOOL, SUB_TEACHER_TOOL_MATH, SUB_GROUP)) ? $item->no_students : 'N/A' ?></td>
                                                <td><?php echo in_array($item->typeid, array(SUB_DICTIONARY, SUB_SELF_STUDY, SUB_SELF_STUDY_MATH, SUB_GROUP)) ? $item->no_students : '1' ?></td>
                                                <td class="hidden-xs"><?php echo empty($item->dictionary) ? 'N/A' : $item->dictionary ?></td>
                                                <td><?php echo empty($item->no_of_points) ? 'N/A' : $item->no_of_points ?></td>
                                                <td>$ <?php echo $item->price ?></td>
                                                <td><button type="submit" name="delete-cart-item" value="<?php echo $item->id ?>" class="btn-custom-2 delete-item" style="margin: 0"><?php _e('Delete', 'iii-dictionary') ?></button></td>
                                            </tr>
                                            <?php
                                        endforeach;
                                    else :
                                        ?>
                                        <tr><td colspan="9"><?php _e('Your cart is empty', 'iii-dictionary') ?></td></tr>
                                    <?php endif ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 margin-3" >
                    <p class="box-gray-dialog" style="text-align: right" >
                        <?php _e('Total Amount: ','iii-dictionary') ?> <span class="currency color-green708b23">$</span> <span id="total-amount" class="color-green708b23 css-font-weight"><?php echo $cart_amount ?></span>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <section class="col-xs-12 css-not-full">
            <div class="row">
                <div class="col-xs-12">
                    <h2 class="title-border colorad8425"><?php _e('Payment Method', 'iii-dictionary') ?></h2>
                </div>
            </div>
            <?php
            $is_tutoring_plan =0;
            if (!empty($cart_items)) :
                foreach ($cart_items as $key => $item) :
                    if($item->type == 'ikMath Tutoring Plan') {
                        $is_tutoring_plan = 1;
                    } 
//                    echo $item->extending ? ' ' . __('(Additional)', 'iii-dictionary') : ''
                endforeach;
            endif;
            ?>
            <div class="row " id="exist-card" data-type="0" <?php if(isset($_SESSION['payment']['sub-type']) && $_SESSION['payment']['sub-type'] == 30 || $is_tutoring_plan) echo 'style="display: none;"'; ?>>
                <div class="col-xs-1 width-45" form-group new-msg-style col-xs-12>
                    <span class="arrow_down"></span>
                </div>
                <div class="col-xs-11">
                    <label class="color4c4c4c pad-left-10" for="exist-card"><?php _e('Use existing credit card?', 'iii-dictionary') ?></label>
                </div>
            </div>
            <div class="row hidden" id="existing-card-block">
                <div class="col-xs-11 col-sm-11 col-md-10 col-lg-10 form-group ">													
                    <?php MWHtml::user_credit_cards() ?>
                </div>
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 form-group ">
                    <input type="text" class="form-control" name="re_ssl_cvv2cvc2" value="" placeholder="CVV" autocomplete="off">
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                    <div class="card-icons"></div>
                </div>
            </div>
            <div class="row" id="new-card" data-type="1" <?php if(isset($_SESSION['payment']['sub-type']) && $_SESSION['payment']['sub-type'] == 30 || $is_tutoring_plan) echo 'style="display: none;"'; ?>>
                <div class="col-xs-1 width-45" form-group new-msg-style col-xs-12>
                    <span class="arrow_down"></span>
                </div>
                <div class="col-xs-11">
                    <label class="color4c4c4c pad-left-10" for="exist-card"><?php _e('Use new credit card?', 'iii-dictionary') ?></label>
                </div>
            </div>
            <div class="row hidden" id="new-card-info-block">
                <div class="col-xs-12">
                    <div class="row card-info" style="margin-left: 25px">												
                        <div class="col-sm-6 col-md-6 col-lg-6 col-xs-12">
                            <div class="form-group">
                                <label class="font-dialog"><?php _e('Select credit card type', 'iii-dictionary') ?></label>
                                <?php MWHtml::credit_cards() ?>
                            </div>					
                        </div>
                        <div class="col-sm-6 col-md-6 col-lg-6 col-xs-12">
                            <div class="form-group">
                                <label for="ssl_card_number" class="font-dialog"><?php _e('Credit card number', 'iii-dictionary') ?></label>
                                <input type="text" class="form-control" id="ssl_card_number" name="ssl_card_number" value="" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-sm-8 col-md-8 col-lg-8 col-xs-12">
                            <div>
                                <label style="width: 100%" class="font-dialog"><?php _e('Expiration date', 'iii-dictionary') ?></label>
                                <div class="col-sm-6 col-md-6 col-lg-6 col-xs-12 form-group" style="padding-left: 0px !important;">
                                <select class="select-box-it sel-exp-date " id="exp-month" name="exp_date_mm">
                                    <option value="">MM</option>
                                    <?php for ($i = 1; $i <= 12; $i++) : ?>
                                        <?php $pad_str = str_pad($i, 2, '0', STR_PAD_LEFT) ?>
                                        <option value="<?php echo $pad_str ?>"><?php echo $pad_str ?></option>
                                    <?php endfor ?>
                                </select>
                                    </div>
                                <div class="col-sm-6 col-md-6 col-lg-6 col-xs-12 form-group" style="padding-left: 0px !important;">
                                <select class="select-box-it sel-exp-date" id="exp-year" name="exp_date_yy">
                                    <option value="">YY</option>
                                    <?php for ($i = 0; $i <= 52; $i++) : ?>
                                        <?php $pad_str = str_pad($i, 2, '0', STR_PAD_LEFT) ?>
                                        <option value="<?php echo $pad_str ?>"><?php echo $pad_str ?></option>
                                    <?php endfor ?>
                                </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-4 col-lg-4 col-xs-12">
                            <div class="form-group">
                                <label for="ssl_cvv2cvc2" class="font-dialog">CVV/CVC</label>
                                <input type="text" class="form-control" id="ssl_cvv2cvc2" name="ssl_cvv2cvc2" value="" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="row card-info" style="margin-left: 25px">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label for="firstname" class="font-dialog"><?php _e('First name', 'iii-dictionary') ?></label>
                                <input type="text" class="form-control" id="firstname" name="firstname" value="">
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label for="lastname" class="font-dialog"><?php _e('Last name', 'iii-dictionary') ?></label>
                                <input type="text" class="form-control" id="lastname" name="lastname" value="">
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label for="ssl_avs_address" class="font-dialog" ><?php _e('Billing address', 'iii-dictionary') ?></label>
                                <input type="text" class="form-control" id="ssl_avs_address" name="ssl_avs_address" value="">
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="city" class="font-dialog"><?php _e('City', 'iii-dictionary') ?></label>
                                <input type="text" class="form-control" id="city" name="city" value="">
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="state" class="font-dialog"><?php _e('State', 'iii-dictionary') ?></label>
                                <input type="text" class="form-control" id="state" name="state" value="">
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="ssl_avs_zip" class="font-dialog"><?php _e('Zip', 'iii-dictionary') ?></label>
                                <input type="text" class="form-control" id="ssl_avs_zip" name="ssl_avs_zip" value="">
                            </div>
                        </div>												
                    </div>
                </div>
            </div>
            <div class="row" id="paypal" data-type="2" <?php if(isset($_SESSION['payment']['sub-type']) && $_SESSION['payment']['sub-type'] == 30 || $is_tutoring_plan) echo 'style="display: none;"'; ?>>
                    <div class="col-xs-1 width-45" form-group new-msg-style col-xs-12>
                        <span class="arrow_down"></span>
                    </div>
                    <div class="col-xs-11">
                        <label class="color4c4c4c pad-left-10" for="exist-card"><?php _e('Pay with Paypal', 'iii-dictionary') ?></label>
                    </div>
                </div>
                <div class="col-xs-12 hidden" id="paypal-block">
                    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                        <input type="image" id="paypal-submit" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" alt="PayPal - The safer, easier way to pay online!">
                    </div>
                    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                        <p class="text-alert">
                            <?php _e('<strong>Note:</strong> Paypal might take sometimes to process your payment. If you don\'t see the item you paid in Subscription history, please log out and log in again after a few minutes', 'iii-dictionary') ?>
                        </p>
                    </div>
                </div>
            <?php
            $is_point =0;
            if (!empty($cart_items)) :
                foreach ($cart_items as $key => $item) :
                    if($item->type == 'Points') {
                        $is_point = 1;
                    } 
//                    echo $item->extending ? ' ' . __('(Additional)', 'iii-dictionary') : ''
                endforeach;
            endif;
            ?>
            <div class="row" id="points-balance" data-type="3" <?php if(isset($_SESSION['payment']['sub-type']) && $_SESSION['payment']['sub-type'] == 4 || $is_point ==1) echo 'style="display: none;"'; ?>>
                <div class="col-xs-1 width-45" form-group new-msg-style col-xs-12>
                    <span class="arrow_down"></span>
                </div>
                <div class="col-xs-11">
                    <label class="color4c4c4c pad-left-10" for="exist-card"><?php _e('Pay with my points balance', 'iii-dictionary') ?></label>
                </div>
            </div>
                <div class="col-xs-12 hidden" id="points-balance-block">
                    <div>
                        <div class="form-group">
                            <input id="points-balance" type="radio" class="rd-points" name="payment-method-point" value="3">
                            <label class="font-dialog"><?php _e('Your current points is', 'iii-dictionary') ?> <em class="text-info color-green708b23">(Exchange rate: <?php echo $point_ex_rate ?>pts = 1$)</em></label>
                            <div class="box-gray-dialog">
                                <h3 ><?php echo 'TOTAL POINTS:&nbsp;' ?><span class="color-green708b23" ><?php echo number_format(ik_get_user_points(), 2) ?></span></h3>
                                
                            </div>
                        </div>
                    </div>
                </div>

            <div class="row" style="margin-left: 25px">
                <div class="col-xs-12 col-sm-6 top-buffer">
                    <button type="submit" name="process" id="process-btn" class="btn-custom"></span><?php _e('Check out', 'iii-dictionary') ?></button>
                </div>
                <div class="col-xs-12 col-sm-6 top-buffer">
                    <a href="#" class="btn-custom-1"></span><?php _e('Continue shopping', 'iii-dictionary') ?></a>
                </div>
            </div>
        </section>
    </div>
    <input type="hidden" id="item-count" value="<?php echo count($cart_items) ?>">
    <input type="hidden" id="type-payment" name="payment-method" value="0">
</form>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top" class="hidden">
    <input type="hidden" name="cmd" value="_xclick">
    <input type="hidden" name="business" value="payment@innovative-knowledge.com">
    <input type="hidden" name="item_name" value="Subscription">
    <input type="hidden" name="amount" value="<?php echo $cart_amount ?>">
    <input type="hidden" name="custom" value="<?php echo get_current_user_id() ?>">
    <input type="hidden" name="return" value="<?php echo home_url_ssl() ?>/iklearn/?r=payments">
    <input type="image" id="paypal-btn" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit">
    <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>

<div id="process-tran-modal" class="modal fade modal-white" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="three-quarters-loader"><?php _e('Loading...', 'iii-dictionary') ?></div>
                <h3><span class="icon-warning"></span><?php _e('Payment Processing...', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <span class="icon-credit"></span>
                        <p><?php _e('Now processing the payment.', 'iii-dictionary') ?><br>
                            <?php _e('Please don\'t close the window until it is completed.', 'iii-dictionary') ?></p>
                    </div>
                </div>
            </div>
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
            <?php if (!empty($other)) : ?>
                <?php
                switch ($other['order']) {
                    case 1 : MWHtml::get_first_assign($other);
                        break;
                    case 2 : MWHtml::auto_active_code_dic($other);
                        break;
                }
                ?>	
            <?php endif ?>
        </div>
    </div>
</div>
<script>
    var open_mt4 = <?php echo!is_null($_SESSION['open_method_point']) ? 1 : 0 ?>;
    (function ($) {
        $(function () {
            id_check ="";
            $('#exist-card,#new-card, #paypal, #points-balance').click(function(){
                data_type=$(this).attr('data-type');
                switch(data_type){
                    case "0" : 
                        data_id=$("#existing-card-block");
                        $('#type-payment').val("0");
                        $('#new-card-info-block').addClass("hidden");
                        $('#paypal-block').addClass("hidden");
                        $('#points-balance-block').addClass("hidden");
                        $(".rd-points").prop("checked", false);
                        
                        $('#new-card div:first-child span').removeClass("arrow_up");
                        $('#new-card div:first-child span').addClass("arrow_down");
                        $('#paypal div:first-child span').removeClass("arrow_up");
                        $('#paypal div:first-child span').addClass("arrow_down");
                        $('#points-balance div:first-child span').removeClass("arrow_up");
                        $('#points-balance div:first-child span').addClass("arrow_down");
                        if(id_check != 0) {
                            $('#exist-card div:first-child span').removeClass("arrow_down");
                            $('#exist-card div:first-child span').addClass("arrow_up");
                            $('#existing-card-block').removeClass("hidden");
                        }else{
                            if($('#existing-card-block').hasClass("hidden")){
                                $('#exist-card div:first-child span').removeClass("arrow_down");
                                $('#exist-card div:first-child span').addClass("arrow_up");
                                $('#existing-card-block').removeClass("hidden");
                            }else{
                                $('#exist-card div:first-child span').removeClass("arrow_up");
                                $('#exist-card div:first-child span').addClass("arrow_down");
                                $('#existing-card-block').addClass("hidden");
                            }
                        }
                        id_check =0;
                        break;
                    case "1" : 
                        data_id=$("#new-card-info-block");
                        $('#type-payment').val("1");
                        $('#existing-card-block').addClass("hidden");
                        $('#paypal-block').addClass("hidden");
                        $('#points-balance-block').addClass("hidden");
                        $(".rd-points").prop("checked", false);
                        
                        $('#exist-card div:first-child span').removeClass("arrow_up");
                        $('#exist-card div:first-child span').addClass("arrow_down");
                        $('#paypal div:first-child span').removeClass("arrow_up");
                        $('#paypal div:first-child span').addClass("arrow_down");
                        $('#points-balance div:first-child span').removeClass("arrow_up");
                        $('#points-balance div:first-child span').addClass("arrow_down");
                        if(id_check != 1 || id_check == null) {
                            $('#new-card div:first-child span').removeClass("arrow_down");
                            $('#new-card div:first-child span').addClass("arrow_up")
                            $('#new-card-info-block').removeClass("hidden");
                        }else{
                            if($('#new-card-block').hasClass("hidden")){
                                $('#new-card div:first-child span').removeClass("arrow_down");
                                $('#new-card div:first-child span').addClass("arrow_up");
                                $('#new-card-info-block').removeClass("hidden");
                            }else{
                                $('#new-card div:first-child span').removeClass("arrow_up");
                                $('#new-card div:first-child span').addClass("arrow_down");
                                $('#new-card-info-block').addClass("hidden");
                            }
                        }
                        id_check =1;
                        break;
                    case "2" : 
                        data_id=$("#paypal-block");
                        $('#type-payment').val("2");
                        $('#existing-card-block').addClass("hidden");
                        $('#new-card-info-block').addClass("hidden");
                        $('#points-balance-block').addClass("hidden");
                        $(".rd-points").prop("checked", false);
                        
                        $('#exist-card div:first-child span').removeClass("arrow_up");
                        $('#exist-card div:first-child span').addClass("arrow_down");
                        $('#new-card div:first-child span').removeClass("arrow_up");
                        $('#new-card div:first-child span').addClass("arrow_down");
                        $('#points-balance div:first-child span').removeClass("arrow_up");
                        $('#points-balance div:first-child span').addClass("arrow_down");
                        if(id_check != 2 || id_check == null) {
                            $('#paypal div:first-child span').removeClass("arrow_down");
                            $('#paypal div:first-child span').addClass("arrow_up")
                            $('#paypal-block').removeClass("hidden");
                        }else{
                            if($('#paypal-block').hasClass("hidden")){
                                $('#paypal div:first-child span').removeClass("arrow_down");
                                $('#paypal div:first-child span').addClass("arrow_up");
                                $('#paypal-block').removeClass("hidden");
                            }else{
                                $('#paypal div:first-child span').removeClass("arrow_up");
                                $('#paypal div:first-child span').addClass("arrow_down");
                                $('#paypal-block').addClass("hidden");
                            }
                        }
                        id_check =2;
                        break;
                    case "3" : 
                        data_id=$("#points-balance-block");
                        $('#type-payment').val("3");
                        $('#existing-card-block').addClass("hidden");
                        $('#new-card-info-block').addClass("hidden");
                        $('#paypal-block').addClass("hidden");
                        $(".rd-points").prop("checked", false);
                        
                        $('#exist-card div:first-child span').removeClass("arrow_up");
                        $('#exist-card div:first-child span').addClass("arrow_down");
                        $('#new-card div:first-child span').removeClass("arrow_up");
                        $('#new-card div:first-child span').addClass("arrow_down");
                        $('#paypal div:first-child span').removeClass("arrow_up");
                        $('#paypal div:first-child span').addClass("arrow_down");
                        if(id_check != 3 || id_check == null) {
                            $('#points-balance div:first-child span').removeClass("arrow_down");
                            $('#points-balance div:first-child span').addClass("arrow_up")
                            $('#points-balance-block').removeClass("hidden");
                        }else{
                            if($('#points-balance-block').hasClass("hidden")){
                                $('#points-balance div:first-child span').removeClass("arrow_down");
                                $('#points-balance div:first-child span').addClass("arrow_up");
                                $('#points-balance-block').removeClass("hidden");
                            }else{
                                $('#points-balance div:first-child span').removeClass("arrow_up");
                                $('#points-balance div:first-child span').addClass("arrow_down");
                                $('#points-balance-block').addClass("hidden");
                            }
                        }
                        id_check =3;
                        break;
                }
//                var clicked = $(this).data('clicked');
//                        if ( clicked ) {
//                            $(this).find('span').removeClass('arrow_up');
//                            $(this).find('span').addClass('arrow_down');
//                            data_id.slideUp();
//                        }else{
//                            $(this).find('span').removeClass('arrow_down');
//                            $(this).find('span').addClass('arrow_up');
//                            data_id.slideDown();
//                        }
//                        $(this).data('clicked', !clicked);
            });
            

            $("#paypal-submit").click(function (e) {
                e.preventDefault();
                if ($("#item-count").val() != "0") {
                    $("#paypal-btn").click();
                }
            });

            if (open_mt4) {
                $("#points-balance").click();
            }
            $('.btn-custom-1').click(function (e){
                e.preventDefault();
                history.back(1);
            });
            var tt_point = <?php echo ik_get_user_points($user->ID);?>;
            var point = '<?php echo $cart_amount ?>';
            var arr_id = <?php echo json_encode($arr_id);?>;
        // Handing add data on table wp_dict_tutoring_plan only pay with "tutoring plan"
            $('#process-btn').click(function(e){
                
                var select_card = $('#select-credit-card').val();
                var card = $('#ssl_card_number').val();
                var exp_month= $('#exp-month').val();
                var exp_year= $('#exp-year').val();
                var ccvv= $('#ssl_cvv2cvc2').val();
                var address= $('#ssl_avs_address').val();
                var city= $('#city').val();
                var state= $('#state').val();
                var zip= $('#ssl_avs_zip').val();
                var string = "" ;// message 
                if($('#type-payment').val()==1){
                    if(!select_card || !card || !exp_month || !exp_year || !ccvv || !address || !city || !state || !zip){
                        e.preventDefault();
                        if(!select_card){string='Please select cart type<br>';}
                        if(!card){string+='Card number cannot empty';}
                        if(!exp_month || !exp_year){string+='<br>Expiration date is not valid';}
                        if(!address || !city || !state){string+='<br>Address cannot empty';}
                        if(!zip){string+='<br>Zip code cannot empty';}
                        $('#message-error').html(string);
                        $('#modal-error-message').modal("show");
                    }   
                }else if($('#type-payment').val()==0){
                    if(!select_card || !card){
                        e.preventDefault();
                        if(!select_card){string='Please select a Credit Card,<br>';}
                        if(!card){string+='CVV/CVC cannot empty';}
                        $('#message-error').html(string);
                        $("#process-tran-modal").modal("hide");
                        $('#modal-error-message').modal("show");
                    }
                }else{
                    console.log(arr_id);
                    if(tt_point > point) {
                        if(arr_id.indexOf("30") != -1)
                        {  
                            $.get(home_url + "/?r=ajax/paid_wp_dict_tutoring", {}, function (data) {
                            });
                        }
                    }
                    $("#process-tran-modal").modal();
                }
            });
        // Handing uncheck point plance
            var checked=false;
            $('.rd-points').click(function(){
                checked=!checked;
                $(this).prop("checked",checked);
                if($(this).is(':checked'))
                {
                }
                else{
                    $('#points-balance div:first-child span').removeClass("arrow_up");
                    $('#points-balance div:first-child span').addClass("arrow_down");
                }
            });
        });
    })(jQuery);
<?php
//unset session to open method point  
unset($_SESSION['open_method_point']);
?>
</script>

<?php if (!is_math_panel()) : ?>
    <?php get_dict_footer() ?>
<?php else : ?>
    <?php get_math_footer() ?>
<?php endif ?>