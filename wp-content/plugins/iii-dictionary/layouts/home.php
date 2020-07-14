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
    <main class="home" id="home">
        <div id="switch-menu-english" class="css-swith-menu">
            <div class="css-div-center">
                <div class="col-xs-6" id="start-lesson"><a href="<?php echo locale_home_url() . '/?r=critical-lesson' ?>" class="a-switch" >Critical Lessons!</a><span class="glyphicon glyphicon-arrow-right arrow-right"></span></div>
                <div class="col-xs-6" id="code-active"><a href="#" style=" padding-left: 7%;" class="a-switch">Code Activation</a><span class="glyphicon glyphicon-arrow-right arrow-right"></span></div>
            </div>
        </div>
        <div id="content">
            <div id="top-content" class="top-content-english css-mobile-img-home" style="position: relative;margin-top: 20px;">
                <div class="left-column-english">
                    <div class="column-content custom-left-english">
                        <p class="home-english-text-1 margin-0"><?php _e('Complete Self-Study Online', 'iii-dictionary') ?></p>
                        <p class="home-english-text-2 margin-0"><?php _e('ENGLISH PROGRAM', 'iii-dictionary') ?></p>
                        <span class="glyphicon glyphicon-ok span-english-custom-1"></span><p class="p-custom-1 css-width"><?php _e('Grammar, Vocabulary, Spelling, Writing & SAT Prep', 'iii-dictionary') ?></p>
                        <span class="glyphicon glyphicon-ok span-english-custom-1"></span><p class="p-custom-1 css-width1"><?php _e('Dictionary from Merriam-Webster', 'iii-dictionary') ?></p>
                        <span class="glyphicon glyphicon-ok span-english-custom-1"></span><p class="p-custom-1 css-width2"><?php _e('On-demand Tutor Available', 'iii-dictionary') ?></p>
                    </div>
                    <div id="online-tool"><a href="#popup-tool-english-dialog" target="_blank" data-toggle="modal">How does it work? </a></div>
                </div>
            </div>
            <div class="bottom-content">
                <section class="block-content ">
                    <h3 class="css-font-weight css-foreign-home" style="margin-top: 3px"><?php _e('Are you a foreign student?', 'iii-dictionary') ?></h3>
                    <h2 class="css-font-weight1 txt-let-english-home" style="font-size: 25px;margin-top: 19px"><?php _e('Let&rsquo;s Improve Your English ', 'iii-dictionary') ?></h2>
                    <h2 class="css-font-weight1 txt-skill-english-home" style="font-size: 25px;"><?php _e('Conversation Skills.', 'iii-dictionary') ?></h2>
                    <div style="display: inline-flex" class="css-div-link">
                        <span class="css-padding-right-25 css-mobile-link1"><a href="http://www.eigolive.jp" class="css-font-weight css-text-decoration css-font-weight css-link1-780" target="_blank"><?php _e('日本人', 'iii-dictionary') ?></u></a></span>
                        <span class="css-padding-right-25 css-mobile-link2"><a href="http://www.kenglish.kr" class="css-font-weight css-text-decoration css-font-weight" target="_blank"><?php _e('한국인', 'iii-dictionary') ?></u></a></span>
                        <span class="css-mobile-link3"><a href="http://www.americanenglishlive.com" class="css-font-weight css-text-decoration css-font-weight css-link2-780" target="_blank"><?php _e('Others', 'iii-dictionary') ?></u></a></span>
                    </div>
                </section>
                <section class="block-content block-content-english" style="padding-top: 23px;">
                    <h3 class="css-font-weight1 css-teach-home" ><?php _e('Are you a teacher?', 'iii-dictionary') ?></h3>
                    <h2 class="css-font-weight1 txt-earn-home" ><?php _e('Do you want to earn extra income?', 'iii-dictionary') ?></h2>
                    <a href="#about-teacher-dialog" id="a-tutor" class="css-mobile-right" target="_blank" data-toggle="modal"><u class="detail-color u-english-tutor"><?php _e('Details', 'iii-dictionary') ?></u></a>
                </section>

                <section class="block-content block-content-english css-padd-pre-sat" >
                    <h3 class="css-font-weight1 css-mobile-improve"><?php _e('Improving &', 'iii-dictionary') ?></h3>
                    <h2 class="css-font-weight1 css-width-100 css-mobile-pre"><?php _e('Perparing', 'iii-dictionary') ?></h2>
                    <h2 class="css-sat-home css-padd-sat css-sat-eng"><?php _e('SAT', 'iii-dictionary') ?></h2>
                    <a href="#about-student-dialog" class="css-mobile-right" target="_blank" data-toggle="modal"><u class="detail-color u-english-sat"><?php _e('Details', 'iii-dictionary') ?></u></a>
                </section>
            </div>
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
                                <div><label class="text-points media-txt-point-home"><span class="css-txt-left"><?php _e('1 point is equivalent to 1 dollar. You can earn poin by selling your worksheet to the teachers.', 'iii-dictionary') ?></span></label></div>
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
                    <a href="<?php echo locale_home_url() ?>" class="btn-block btn-custom"></a>
                </div>
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
        <div class="modal-dialog modal-custom-first">
            <div class="modal-content boder-black">
                <div class="modal-header custom-header">
                    <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                    <h3><?php _e('Thank you for signing up!', 'iii-dictionary') ?></h3>
                </div>
                <div class="modal-body body-custom">
                    <div class="row">
                        <div class="col-sm-12">
                            <p><?php _e('Welcome to the ikLearn!', 'iii-dictionary') ?></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer footer-custom">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom confirm check-no-point secondary"><?php _e('LET\'S BEGIN!', 'iii-dictionary') ?></a>
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
                jQuery('#main-nav > nav > a > img').attr('src', '/wp-content/themes/ik-learn/library/images/logo_menu_ja.jpg');
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
                jQuery('#main-nav > nav > a > img').attr('src', '/wp-content/themes/ik-learn/library/images/logo_menu_ko.jpg');
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
                jQuery('#main-nav > nav > a > img').attr('src', '/wp-content/themes/ik-learn/library/images/home_vi4.png');
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
                jQuery('#main-nav > nav > a > img').attr('src', '/wp-content/themes/ik-learn/library/images/15.jpg');
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
            $("#sel-sat-months").change(function () {
                total_purchase_join_class_english();
            });
            
            $('#code-active').click (function (e){
                e.preventDefault();
                $('#active_code').modal('show');
            });
//            var login = <?php if(is_user_logged_in()){echo 1;}else{echo 0;}?>;
//            if(login  ==1){
//                if(arr_yesterday.length >=1) {
//                $('#modal-reminder-schedule').modal("show");
//                $.post(home_url + "/?r=ajax/get-info-schedule-reminder",{id: arr_yesterday[0]}, //Hien tai dang set cung 1 du lieu
//                    function(data){
//                        $('#tbody-reminder-modal').html(data);
//                    }  
//                );
//            }
//            }
        });
    })(jQuery);
</script>
<?php
$newuser = 0;
if (is_user_logged_in()){
    $current_user = wp_get_current_user();
    $newuser = get_user_meta($current_user->ID, 'newuser', true);
} 
if ($newuser == 1) : ?>    
    <script>
        (function ($) {
            $(function () {
                $("#my-account-modal").modal('show');
                $("#sub-createacc").removeClass("active");
                $("#sub-profile").addClass("active");
                $("#create-account").removeClass("active");
                $("#create-account").removeClass("in");
                $("#login-user").removeClass("active");
                $("#profile").addClass("active");
                $("#profile").addClass("in");
            });
        })(jQuery);
    </script>
    <?php
    update_user_meta($current_user->ID, 'newuser', 0);
        
    $_SESSION['newuser'] = null;
endif
?>
<?php
MWHtml::ik_site_messages();
get_footer()
?>