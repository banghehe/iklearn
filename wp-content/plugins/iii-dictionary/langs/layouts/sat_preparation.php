<?php
    $is_math_panel = is_math_panel();
    $login = is_user_logged_in();
    $check = $_GET["client"]; // vì math english (ikmath course và sat preparation sử dụng chung trang code này)
    if($check=="math-emathk") {
        $_page_title = __('ikMath Course', 'iii-dictionary');
        get_math_header($_page_title);
        get_dict_page_title($_page_title, '', '', array());
    }else if($check=="math-sat1"){
        $_page_title = __('SAT Preparation', 'iii-dictionary');
        $subtitle = '2016 version';
        get_math_header($_page_title);
        get_dict_page_title($_page_title, '', '', array());
    }else if($check=="math-sat2"){
        $_page_title = __('SAT 2 Preparation', 'iii-dictionary');
        get_math_header($_page_title);
        get_dict_page_title($_page_title, '', '', array());
    }else {
        $_page_title = __('SAT Preparation', 'iii-dictionary');
        get_dict_header($_page_title);
        get_dict_page_title($_page_title, '', '', array());
    }
    
?>

<script>
    var check_js = <?php if($_GET["client"]=="math-sat1")  // dùng để set subtitle cho mỗi trang khác nhau
                        {echo "0";}
                    else if($_GET["client"]=="math-sat2")
                        {echo "1";}
                    else if($_GET["client"]=="math-emathk")
                        {echo "2";}
                    else {
                        echo "3";
                    }
                    ?>;
    jQuery('#sat-preparation .article-header').css('background', '#ffffff');
    jQuery('#sat-preparation .entry-content').css('background', '#ffffff');
    if ((window.matchMedia('screen and (min-width: 1200px)').matches)) {
        jQuery('#main').removeClass('container');
        jQuery('#sat-preparation .article-header .row').attr('style', 'width:1050px; margin:auto !important');
        jQuery('#sat-preparation .entry-content .row:first').attr('style', 'width:1050px; margin:auto !important');
    }
    if ((window.matchMedia('screen and (max-width: 480px)').matches)) {    
        jQuery('#sat-preparation .article-header').attr('style', 'height: 0px;padding-top: 0px !important; background: #fff !important;border-bottom: 1px solid #fff !important;');
    }
    jQuery('#sat-preparation #page-tabs-container').attr('style', 'background: #ffffff;padding-top: 0px;border-bottom: 1px solid #C8C8C8 !important');
    jQuery('#sat-preparation .entry-content').css('color', 'black');
    jQuery('.main-article header .page-title').css('color', 'black');
    jQuery('.cs-select').css('color', 'black');
    jQuery('#page-info-tab').hide();
    if(check_js == "2") {
        jQuery('#span-title').html('Click the Courses to see the details');
    }else if(check_js == "0"){
        jQuery('#span-title').html('2016 version');
        jQuery('#span-title').attr("style","color: #ffc750;bottom: -5px;font-size: 15px;margin-left: -20px;");
        jQuery('#span-title-first').css("visibility","hidden");
    } else if(check_js == "1" || check_js == "3") {
        jQuery('#span-title-first').css("display","none");
    }
    if ((window.matchMedia('screen and (max-width: 450px)').matches)) {
        if(check_js == "0") {
            jQuery('#span-title').attr("style","color: #ffc750;bottom: -3px;font-size: 15px;");
            jQuery('.container .row > div:first-child').attr('style', 'padding-top: 15px;padding-bottom: 15px');
        }else if(check_js =="2") {
            jQuery('.container .row > div:first-child').attr('style', 'padding-top: 15px;padding-bottom: 15px');
            jQuery('.container .row > div:first-child >h1:first-child').css('style', 'margin-bottom: 0px;');
        }else{
            jQuery('.container .row > div:first-child').css('padding-top', '13px');
        }
        jQuery('.container-acc-login-signup-online').css('margin-top', '10px');
    }
//    jQuery('#page-tabs-container').remove();

</script>

<div class="row">
    <?php if($check == "math-emathk") : ?>
    <!-- section Math Kindergarten -->
        <div class="col-md-12 css-div-kinder col-xs-12 css-mobile-margin-top-10" >
            <div class="col-md-6 css-padding-14-des col-xs-6 css-mobile-margin-top-35" style="display: flex" data-toggle="collapse" data-target="#section-38">
                <span class="glyphicon glyphicon-triangle-bottom css-icon-dow"></span></p> 
                <span class='txt-kinder'>Math Kindergarten</span>
            </div>
            <div class="col-md-6 col-xs-12 col-xs-6" >
                <div class="css-div-text" data-toggle="collapse" data-target="#section-38">
                    <span class='css-color-7F7F7F'>Subscription Fee:</span>
                    <span class='css-txt-month'>$ 20.00 / month</span>
                </div>
                <div>
                    <input type="button" id="btn-math-kinder" class="css-btn-ikmath btn-subscrible" value="Subscribe" data-type="38">
                </div>
            </div>
        </div>
        <div id="section-38" class="collapse">
            <!--Phần nội dung bị ẩn hiện khi click vào section!-->
            <?php load_html_by_class_type(38);?>  
        </div>
    <!-- section Math Kindergarten -->
        <div class="col-md-12 css-div-kinder col-xs-12 css-mobile-margin-top-10 css-margin-top-22-des" >
            <div class="col-md-6 css-padding-14-des col-xs-6 css-mobile-margin-top-35" style="display: flex" data-toggle="collapse" data-target="#section-39">
                <span class="glyphicon glyphicon-triangle-bottom css-icon-dow"></span></p> 
                <span class='txt-kinder' style="color: #6841c8">Math Grade 1</span>
            </div>
            <div class="col-md-6 col-xs-12 col-xs-6">
                <div class="css-div-text" data-toggle="collapse" data-target="#section-39">
                    <span class='css-color-7F7F7F'>Subscription Fee:</span>
                    <span class='css-txt-month'>$ 20.00 / month</span>
                </div>
                <div>
                    <input type="button" id="btn-math-kinder" class="css-btn-ikmath btn-subscrible" value="Subscribe" data-type="39">
                </div>
            </div>
        </div >
        <div id="section-39" class="collapse">
    <!--Phần nội dung bị ẩn hiện khi click vào section!-->
            <?php load_html_by_class_type(39);?> 
        </div>
    <!-- section Math Kindergarten -->
        <div class="col-md-12 css-div-kinder col-xs-12 css-mobile-margin-top-10 css-margin-top-22-des" >
            <div class="col-md-6 css-padding-14-des col-xs-6 css-mobile-margin-top-35" style="display: flex" data-toggle="collapse" data-target="#section-40">
                <span class="glyphicon glyphicon-triangle-bottom css-icon-dow"></span></p> 
                <span class='txt-kinder' style="color: #6841c8">Math Grade 2</span>
            </div>
            <div class="col-md-6 col-xs-12 col-xs-6">
                <div class="css-div-text" data-toggle="collapse" data-target="#section-40">
                    <span class='css-color-7F7F7F'>Subscription Fee:</span>
                    <span class='css-txt-month'>$ 20.00 / month</span>
                </div>
                <div>
                    <input type="button" id="btn-math-kinder" class="css-btn-ikmath btn-subscrible" value="Subscribe" data-type="40">
                </div>
            </div>
        </div>
        <div id="section-40" class="collapse">
    <!--Phần nội dung bị ẩn hiện khi click vào section!-->
            <?php load_html_by_class_type(40);?> 
        </div>
    <!-- section Math Kindergarten -->
        <div class="col-md-12 css-div-kinder col-xs-12 css-mobile-margin-top-10 css-margin-top-22-des" >
            <div class="col-md-6 css-padding-14-des col-xs-6 css-mobile-margin-top-35" style="display: flex" data-toggle="collapse" data-target="#section-41">
                <span class="glyphicon glyphicon-triangle-bottom css-icon-dow"></span></p> 
                <span class='txt-kinder' style="color: #6841c8">Math Grade 3</span>
            </div>
            <div class="col-md-6 col-xs-12 col-xs-6">
                <div class="css-div-text" data-toggle="collapse" data-target="#section-41">
                    <span class='css-color-7F7F7F'>Subscription Fee:</span>
                    <span class='css-txt-month'>$ 30.00 / month</span>
                </div>
                <div>
                    <input type="button" id="btn-math-kinder" class="css-btn-ikmath btn-subscrible" value="Subscribe" data-type="41">
                </div>
            </div>
        </div>
    <!--Phần nội dung bị ẩn hiện khi click vào section!-->
        <div id="section-41" class="collapse">
            <?php load_html_by_class_type(41);?> 
        </div>

    <!-- section Math Kindergarten -->
        <div class="col-md-12 css-div-kinder col-xs-12 css-mobile-margin-top-10 css-margin-top-22-des" >
            <div class="col-md-6 css-padding-14-des col-xs-6 css-mobile-margin-top-35" style="display: flex" data-toggle="collapse" data-target="#section-42">
                <span class="glyphicon glyphicon-triangle-bottom css-icon-dow"></span></p> 
                <span class='txt-kinder' style="color: #6841c8">Math Grade 4</span>
            </div>
            <div class="col-md-6 col-xs-12 col-xs-6">
                <div class="css-div-text" data-toggle="collapse" data-target="#section-42">
                    <span class='css-color-7F7F7F'>Subscription Fee:</span>
                    <span class='css-txt-month'>$ 30.00 / month</span>
                </div>
                <div>
                    <input type="button" id="btn-math-kinder" class="css-btn-ikmath btn-subscrible" value="Subscribe" data-type="42">
                </div>
            </div>
        </div>
    <!--Phần nội dung bị ẩn hiện khi click vào section!-->
        <div id="section-42" class="collapse">
            <?php load_html_by_class_type(42);?> 
        </div>

    <!-- section Math Kindergarten -->
        <div class="col-md-12 css-div-kinder col-xs-12 css-mobile-margin-top-10 css-margin-top-22-des" >
            <div class="col-md-6 css-padding-14-des col-xs-6 css-mobile-margin-top-35" style="display: flex" data-toggle="collapse" data-target="#section-43">
                <span class="glyphicon glyphicon-triangle-bottom css-icon-dow"></span></p> 
                <span class='txt-kinder' style="color: #6841c8">Math Grade 5</span>
            </div>
            <div class="col-md-6 col-xs-12 col-xs-6">
                <div class="css-div-text" data-toggle="collapse" data-target="#section-43">
                    <span class='css-color-7F7F7F'>Subscription Fee:</span>
                    <span class='css-txt-month'>$ 30.00 / month</span>
                </div>
                <div>
                    <input type="button" id="btn-math-kinder" class="css-btn-ikmath btn-subscrible" value="Subscribe" data-type="43">
                </div>
            </div>
        </div>
    <!--Phần nội dung bị ẩn hiện khi click vào section!-->
        <div id="section-43" class="collapse">
            <?php load_html_by_class_type(43);?> 
        </div>
    <!-- section Math Kindergarten -->
        <div class="col-md-12 css-div-kinder col-xs-12 css-mobile-margin-top-10 css-margin-top-22-des" >
            <div class="col-md-6 css-padding-14-des col-xs-6 css-mobile-margin-top-35" style="display: flex" data-toggle="collapse" data-target="#section-44">
                <span class="glyphicon glyphicon-triangle-bottom css-icon-dow"></span></p> 
                <span class='txt-kinder' style="color: #6841c8">Math Grade 6</span>
            </div>
            <div class="col-md-6 col-xs-12 col-xs-6">
                <div class="css-div-text" data-toggle="collapse" data-target="#section-44">
                    <span class='css-color-7F7F7F'>Subscription Fee:</span>
                    <span class='css-txt-month'>$ 30.00 / month</span>
                </div>
                <div>
                    <input type="button" id="btn-math-kinder" class="css-btn-ikmath btn-subscrible" value="Subscribe" data-type="44">
                </div>
            </div>
        </div>
    <!--Phần nội dung bị ẩn hiện khi click vào section!-->
        <div id="section-44" class="collapse">
            <?php load_html_by_class_type(44);?>
        </div>

    <!-- section Math Kindergarten -->
        <div class="col-md-12 css-div-kinder col-xs-12 css-mobile-margin-top-10 css-margin-top-22-des" >
            <div class="col-md-6 css-padding-14-des col-xs-6 css-mobile-margin-top-35" style="display: flex" data-toggle="collapse" data-target="#section-45">
                <span class="glyphicon glyphicon-triangle-bottom css-icon-dow"></span></p> 
                <span class='txt-kinder' style="color: #708b23">Math Grade 7</span>
            </div>
            <div class="col-md-6 col-xs-12 col-xs-6">
                <div class="css-div-text" data-toggle="collapse" data-target="#section-45">
                    <span class='css-color-7F7F7F'>Subscription Fee:</span>
                    <span class='css-txt-month'>$ 40.00 / month</span>
                </div>
                <div>
                    <input type="button" id="btn-math-kinder" class="css-btn-ikmath btn-subscrible" value="Subscribe" data-type="45">
                </div>
            </div>
        </div>
    <!--Phần nội dung bị ẩn hiện khi click vào section!-->
        <div id="section-45" class="collapse">
            <?php load_html_by_class_type(45);?> 
        </div>

    <!-- section Math Kindergarten -->
        <div class="col-md-12 css-div-kinder col-xs-12 css-mobile-margin-top-10 css-margin-top-22-des" >
            <div class="col-md-6 css-padding-14-des col-xs-6 css-mobile-margin-top-35" style="display: flex" data-toggle="collapse" data-target="#section-46">
                <span class="glyphicon glyphicon-triangle-bottom css-icon-dow"></span></p> 
                <span class='txt-kinder' style="color: #708b23">Math Grade 8</span>
            </div>
            <div class="col-md-6 col-xs-12 col-xs-6">
                <div class="css-div-text" data-toggle="collapse" data-target="#section-46">
                    <span class='css-color-7F7F7F'>Subscription Fee:</span>
                    <span class='css-txt-month'>$ 40.00 / month</span>
                </div>
                <div>
                    <input type="button" id="btn-math-kinder" class="css-btn-ikmath btn-subscrible" value="Subscribe" data-type="46">
                </div>
            </div>
        </div>
    <!--Phần nội dung bị ẩn hiện khi click vào section!-->
        <div id="section-46" class="collapse">
            <?php load_html_by_class_type(46);?> 
        </div>
    <!-- section Math Kindergarten -->
        <div class="col-md-12 css-div-kinder col-xs-12 css-mobile-margin-top-10 css-margin-top-22-des" >
            <div class="col-md-6 css-padding-14-des col-xs-6 css-mobile-margin-top-35" style="display: flex" data-toggle="collapse" data-target="#section-47">
                <span class="glyphicon glyphicon-triangle-bottom css-icon-dow"></span></p> 
                <span class='txt-kinder' style="color: #e98800">Math Grade 9</span>
            </div>
            <div class="col-md-6 col-xs-12 col-xs-6">
                <div class="css-div-text" data-toggle="collapse" data-target="#section-47">
                    <span class='css-color-7F7F7F'>Subscription Fee:</span>
                    <span class='css-txt-month'>$ 50.00 / month</span>
                </div>
                <div>
                    <input type="button" id="btn-math-kinder" class="css-btn-ikmath btn-subscrible" value="Subscribe" data-type="47">
                </div>
            </div>
        </div>
    <!--Phần nội dung bị ẩn hiện khi click vào section!-->
        <div id="section-47" class="collapse">
            <?php load_html_by_class_type(47);?> 
        </div>

    <!-- section Math Kindergarten -->
        <div class="col-md-12 css-div-kinder col-xs-12 css-mobile-margin-top-10 css-margin-top-22-des" >
            <div class="col-md-6 css-padding-14-des col-xs-6 css-mobile-margin-top-35" style="display: flex" data-toggle="collapse" data-target="#section-48">
                <span class="glyphicon glyphicon-triangle-bottom css-icon-dow"></span></p> 
                <span class='txt-kinder' style="color: #e98800">Math Grade 10</span>
            </div>
            <div class="col-md-6 col-xs-12 col-xs-6">
                <div class="css-div-text" data-toggle="collapse" data-target="#section-48">
                    <span class='css-color-7F7F7F'>Subscription Fee:</span>
                    <span class='css-txt-month'>$ 50.00 / month</span>
                </div>
                <div>
                    <input type="button" id="btn-math-kinder" class="css-btn-ikmath btn-subscrible" value="Subscribe" data-type="48">
                </div>
            </div>
        </div>
    <!--Phần nội dung bị ẩn hiện khi click vào section!-->
        <div id="section-48" class="collapse">
            <?php load_html_by_class_type(48);?> 
        </div>
    <!-- section Math Kindergarten -->
        <div class="col-md-12 css-div-kinder col-xs-12 css-mobile-margin-top-10 css-margin-top-22-des" >
            <div class="col-md-6 css-padding-14-des col-xs-6 css-mobile-margin-top-35" style="display: flex" data-toggle="collapse" data-target="#section-49">
                <span class="glyphicon glyphicon-triangle-bottom css-icon-dow"></span></p> 
                <span class='txt-kinder' style="color: #e98800">Math Grade 11</span>
            </div>
            <div class="col-md-6 col-xs-12 col-xs-6">
                <div class="css-div-text" data-toggle="collapse" data-target="#section-49">
                    <span class='css-color-7F7F7F'>Subscription Fee:</span>
                    <span class='css-txt-month'>$ 50.00 / month</span>
                </div>
                <div>
                    <input type="button" id="btn-math-kinder" class="css-btn-ikmath btn-subscrible" value="Subscribe" data-type="49">
                </div>
            </div>
        </div>
    <!--Phần nội dung bị ẩn hiện khi click vào section!-->
        <div id="section-49" class="collapse">
            <?php load_html_by_class_type(49);?> 
        </div>
    <!-- section Math Kindergarten -->
        <div class="col-md-12 css-div-kinder col-xs-12 css-mobile-margin-top-10 css-margin-top-22-des" >
            <div class="col-md-6 css-padding-14-des col-xs-6 css-mobile-margin-top-35" style="display: flex" data-toggle="collapse" data-target="#section-50">
                <span class="glyphicon glyphicon-triangle-bottom css-icon-dow"></span></p> 
                <span class='txt-kinder' style="color: #e98800">Math Grade 12</span>
            </div>
            <div class="col-md-6 col-xs-12 col-xs-6">
                <div class="css-div-text" data-toggle="collapse" data-target="#section-50">
                    <span class='css-color-7F7F7F'>Subscription Fee:</span>
                    <span class='css-txt-month'>$ 50.00 / month</span>
                </div>
                <div>
                    <input type="button" id="btn-math-kinder" class="css-btn-ikmath btn-subscrible" value="Subscribe" data-type="50">
                </div>
            </div>
        </div>
    <!--Phần nội dung bị ẩn hiện khi click vào section!-->
        <div id="section-50" class="collapse">
            <?php load_html_by_class_type(50);?> 
        </div>
    
<!--Phần nội dung hiển thị MATH SAT 2--!>    
    <?php elseif($check == "math-sat2") : ?>
        <!-- section Math Kindergarten -->
        <div class="col-md-12 css-div-kinder col-xs-12 css-mobile-margin-top-10" >
            <div class="col-md-6 css-padding-14-des col-xs-6 css-mobile-margin-top-35" style="display: flex" data-toggle="collapse" data-target="#section-15">
                <span class="glyphicon glyphicon-triangle-bottom css-icon-dow"></span></p> 
                <span class='txt-kinder' style="color: #000">SAT 2 Preparation</span>
            </div>
            <div class="col-md-6 col-xs-12 col-xs-6">
                <div class="css-div-text" data-toggle="collapse" data-target="#section-15">
                    <span class='css-color-7F7F7F'>Subscription Fee:</span>
                    <span class='css-txt-month'>$ 30.00 / month</span>
                </div>
                <div>
                    <input type="button" id="btn-math-kinder" class="css-btn-ikmath btn-subscrible" value="Subscribe" data-type="15">
                </div>
            </div>
        </div>
        <div id="section-15" class="collapse">
            <!--Phần nội dung bị ẩn hiện khi click vào section!-->
            <?php load_html_by_class_type(15);?>  
        </div>
    <!-- section Math Kindergarten -->
        <div class="col-md-12 css-div-kinder col-xs-12 css-mobile-margin-top-10 css-margin-top-22-des" >
            <div class="col-md-6 css-padding-14-des col-xs-6 css-mobile-margin-top-35" style="display: flex" data-toggle="collapse" data-target="#section-16">
                <span class="glyphicon glyphicon-triangle-bottom css-icon-dow"></span></p> 
                <span class='txt-kinder' style="color: #000">SAT 2 Test 1</span>
            </div>
            <div class="col-md-6 col-xs-12 col-xs-6">
                <div class="css-div-text" data-toggle="collapse" data-target="#section-16">
                    <span class='css-color-7F7F7F'>Subscription Fee:</span>
                    <span class='css-txt-month'>$ 80.00 / month</span>
                </div>
                <div>
                    <input type="button" id="btn-math-kinder" class="css-btn-ikmath btn-subscrible" value="Subscribe" data-type="16">
                </div>
            </div>
        </div >
        <div id="section-16" class="collapse">
    <!--Phần nội dung bị ẩn hiện khi click vào section!-->
            <?php load_html_by_class_type(16);?> 
        </div>
    <!-- section Math Kindergarten -->
        <div class="col-md-12 css-div-kinder col-xs-12 css-mobile-margin-top-10 css-margin-top-22-des" >
            <div class="col-md-6 css-padding-14-des col-xs-6 css-mobile-margin-top-35" style="display: flex" data-toggle="collapse" data-target="#section-17">
                <span class="glyphicon glyphicon-triangle-bottom css-icon-dow"></span></p> 
                <span class='txt-kinder' style="color: #000">SAT 2 Test 2</span>
            </div>
            <div class="col-md-6 col-xs-12 col-xs-6">
                <div class="css-div-text" data-toggle="collapse" data-target="#section-17">
                    <span class='css-color-7F7F7F'>Subscription Fee:</span>
                    <span class='css-txt-month'>$ 80.00 / month</span>
                </div>
                <div>
                    <input type="button" id="btn-math-kinder" class="css-btn-ikmath btn-subscrible" value="Subscribe" data-type="17">
                </div>
            </div>
        </div>
        <div id="section-17" class="collapse">
    <!--Phần nội dung bị ẩn hiện khi click vào section!-->
            <?php load_html_by_class_type(17);?> 
        </div>
    <!-- section Math Kindergarten -->
        <div class="col-md-12 css-div-kinder col-xs-12 css-mobile-margin-top-10 css-margin-top-22-des" >
            <div class="col-md-6 css-padding-14-des col-xs-6 css-mobile-margin-top-35" style="display: flex" data-toggle="collapse" data-target="#section-18">
                <span class="glyphicon glyphicon-triangle-bottom css-icon-dow"></span></p> 
                <span class='txt-kinder' style="color: #000">SAT 2 Test 3</span>
            </div>
            <div class="col-md-6 col-xs-12 col-xs-6">
                <div class="css-div-text" data-toggle="collapse" data-target="#section-18">
                    <span class='css-color-7F7F7F'>Subscription Fee:</span>
                    <span class='css-txt-month'>$ 80.00 / month</span>
                </div>
                <div>
                    <input type="button" id="btn-math-kinder" class="css-btn-ikmath btn-subscrible" value="Subscribe" data-type="18">
                </div>
            </div>
        </div>
    <!--Phần nội dung bị ẩn hiện khi click vào section!-->
        <div id="section-18" class="collapse">
            <?php load_html_by_class_type(18);?> 
        </div>

    <!-- section Math Kindergarten -->
        <div class="col-md-12 css-div-kinder col-xs-12 css-mobile-margin-top-10 css-margin-top-22-des" >
            <div class="col-md-6 css-padding-14-des col-xs-6 css-mobile-margin-top-35" style="display: flex" data-toggle="collapse" data-target="#section-19">
                <span class="glyphicon glyphicon-triangle-bottom css-icon-dow"></span></p> 
                <span class='txt-kinder' style="color: #000">SAT 2 Test 4</span>
            </div>
            <div class="col-md-6 col-xs-12 col-xs-6">
                <div class="css-div-text" data-toggle="collapse" data-target="#section-19">
                    <span class='css-color-7F7F7F'>Subscription Fee:</span>
                    <span class='css-txt-month'>$ 80.00 / month</span>
                </div>
                <div>
                    <input type="button" id="btn-math-kinder" class="css-btn-ikmath btn-subscrible" value="Subscribe" data-type="19">
                </div>
            </div>
        </div>
    <!--Phần nội dung bị ẩn hiện khi click vào section!-->
        <div id="section-19" class="collapse">
            <?php load_html_by_class_type(19);?> 
        </div>

    <!-- section Math Kindergarten -->
        <div class="col-md-12 css-div-kinder col-xs-12 css-mobile-margin-top-10 css-margin-top-22-des" >
            <div class="col-md-6 css-padding-14-des col-xs-6 css-mobile-margin-top-35" style="display: flex" data-toggle="collapse" data-target="#section-20">
                <span class="glyphicon glyphicon-triangle-bottom css-icon-dow"></span></p> 
                <span class='txt-kinder' style="color: #000">SAT 2 Test 5</span>
            </div>
            <div class="col-md-6 col-xs-12 col-xs-6">
                <div class="css-div-text" data-toggle="collapse" data-target="#section-20">
                    <span class='css-color-7F7F7F'>Subscription Fee:</span>
                    <span class='css-txt-month'>$ 80.00 / month</span>
                </div>
                <div>
                    <input type="button" id="btn-math-kinder" class="css-btn-ikmath btn-subscrible" value="Subscribe" data-type="20">
                </div>
            </div>
        </div>
    <!--Phần nội dung bị ẩn hiện khi click vào section!-->
        <div id="section-20" class="collapse">
            <?php load_html_by_class_type(20);?> 
        </div>
<!--Phần nội dung hiển thị MATH SAT 1--!>    
    <?php elseif($check == "math-sat1") : ?>
        <!-- section Math Kindergarten -->
        <div class="col-md-12 css-div-kinder col-xs-12 css-mobile-margin-top-10" >
            <div class="col-md-6 css-padding-14-des col-xs-6 css-mobile-margin-top-35" style="display: flex" data-toggle="collapse" data-target="#section-9">
                <span class="glyphicon glyphicon-triangle-bottom css-icon-dow"></span></p> 
                <span class='txt-kinder' style="color: #000">SAT Preparation</span>
            </div>
            <div class="col-md-6 col-xs-12 col-xs-6">
                <div class="css-div-text" data-toggle="collapse" data-target="#section-9">
                    <span class='css-color-7F7F7F'>Subscription Fee:</span>
                    <span class='css-txt-month'>$ 30.00 / month</span>
                </div>
                <div>
                    <input type="button" id="btn-math-kinder" class="css-btn-ikmath btn-subscrible" value="Subscribe" data-type="9">
                </div>
            </div>
        </div>
        <div id="section-9" class="collapse">
            <!--Phần nội dung bị ẩn hiện khi click vào section!-->
            <?php load_html_by_class_type(9);?>  
        </div>
    <!-- section Math Kindergarten -->
        <div class="col-md-12 css-div-kinder col-xs-12 css-mobile-margin-top-10 css-margin-top-22-des" >
            <div class="col-md-6 css-padding-14-des col-xs-6 css-mobile-margin-top-35" style="display: flex" data-toggle="collapse" data-target="#section-10">
                <span class="glyphicon glyphicon-triangle-bottom css-icon-dow"></span></p> 
                <span class='txt-kinder' style="color: #000">SAT Test 1</span>
            </div>
            <div class="col-md-6 col-xs-12 col-xs-6">
                <div class="css-div-text" data-toggle="collapse" data-target="#section-10">
                    <span class='css-color-7F7F7F'>Subscription Fee:</span>
                    <span class='css-txt-month'>$ 50.00 / month</span>
                </div>
                <div>
                    <input type="button" id="btn-math-kinder" class="css-btn-ikmath btn-subscrible" value="Subscribe" data-type="10">
                </div>
            </div>
        </div >
        <div id="section-10" class="collapse">
    <!--Phần nội dung bị ẩn hiện khi click vào section!-->
            <?php load_html_by_class_type(10);?> 
        </div>
    <!-- section Math Kindergarten -->
        <div class="col-md-12 css-div-kinder col-xs-12 css-mobile-margin-top-10 css-margin-top-22-des" >
            <div class="col-md-6 css-padding-14-des col-xs-6 css-mobile-margin-top-35" style="display: flex" data-toggle="collapse" data-target="#section-11">
                <span class="glyphicon glyphicon-triangle-bottom css-icon-dow"></span></p> 
                <span class='txt-kinder' style="color: #000">SAT Test 2</span>
            </div>
            <div class="col-md-6 col-xs-12 col-xs-6">
                <div class="css-div-text" data-toggle="collapse" data-target="#section-11">
                    <span class='css-color-7F7F7F'>Subscription Fee:</span>
                    <span class='css-txt-month'>$ 50.00 / month</span>
                </div>
                <div>
                    <input type="button" id="btn-math-kinder" class="css-btn-ikmath btn-subscrible" value="Subscribe" data-type="11">
                </div>
            </div>
        </div>
        <div id="section-11" class="collapse">
    <!--Phần nội dung bị ẩn hiện khi click vào section!-->
            <?php load_html_by_class_type(11);?> 
        </div>
    <!-- section Math Kindergarten -->
        <div class="col-md-12 css-div-kinder col-xs-12 css-mobile-margin-top-10 css-margin-top-22-des" >
            <div class="col-md-6 css-padding-14-des col-xs-6 css-mobile-margin-top-35" style="display: flex" data-toggle="collapse" data-target="#section-12">
                <span class="glyphicon glyphicon-triangle-bottom css-icon-dow"></span></p> 
                <span class='txt-kinder' style="color: #000">SAT Test 3</span>
            </div>
            <div class="col-md-6 col-xs-12 col-xs-6">
                <div class="css-div-text" data-toggle="collapse" data-target="#section-12">
                    <span class='css-color-7F7F7F'>Subscription Fee:</span>
                    <span class='css-txt-month'>$ 50.00 / month</span>
                </div>
                <div>
                    <input type="button" id="btn-math-kinder" class="css-btn-ikmath btn-subscrible" value="Subscribe" data-type="12">
                </div>
            </div>
        </div>
    <!--Phần nội dung bị ẩn hiện khi click vào section!-->
        <div id="section-12" class="collapse">
            <?php load_html_by_class_type(12);?> 
        </div>

    <!-- section Math Kindergarten -->
        <div class="col-md-12 css-div-kinder col-xs-12 css-mobile-margin-top-10 css-margin-top-22-des">
            <div class="col-md-6 css-padding-14-des col-xs-6 css-mobile-margin-top-35" style="display: flex" data-toggle="collapse" data-target="#section-13">
                <span class="glyphicon glyphicon-triangle-bottom css-icon-dow"></span></p> 
                <span class='txt-kinder' style="color: #000">SAT Test 4</span>
            </div>
            <div class="col-md-6 col-xs-12 col-xs-6">
                <div class="css-div-text" data-toggle="collapse" data-target="#section-13">
                    <span class='css-color-7F7F7F'>Subscription Fee:</span>
                    <span class='css-txt-month'>$ 50.00 / month</span>
                </div>
                <div>
                    <input type="button" id="btn-math-kinder" class="css-btn-ikmath btn-subscrible" value="Subscribe" data-type="13">
                </div>
            </div>
        </div>
    <!--Phần nội dung bị ẩn hiện khi click vào section!-->
        <div id="section-13" class="collapse">
            <?php load_html_by_class_type(13);?> 
        </div>

    <!-- section Math Kindergarten -->
        <div class="col-md-12 css-div-kinder col-xs-12 css-mobile-margin-top-10 css-margin-top-22-des">
            <div class="col-md-6 css-padding-14-des col-xs-6 css-mobile-margin-top-35" style="display: flex" data-toggle="collapse" data-target="#section-14">
                <span class="glyphicon glyphicon-triangle-bottom css-icon-dow"></span></p> 
                <span class='txt-kinder' style="color: #000">SAT Test 5</span>
            </div>
            <div class="col-md-6 col-xs-12 col-xs-6">
                <div class="css-div-text" data-toggle="collapse" data-target="#section-14">
                    <span class='css-color-7F7F7F'>Subscription Fee:</span>
                    <span class='css-txt-month'>$ 50.00 / month</span>
                </div>
                <div>
                    <input type="button" id="btn-math-kinder" class="css-btn-ikmath btn-subscrible" value="Subscribe" data-type="14">
                </div>
            </div>
        </div>
    <!--Phần nội dung bị ẩn hiện khi click vào section!-->
        <div id="section-14" class="collapse">
            <?php load_html_by_class_type(14);?> 
        </div>
    
<!--Phần nội dung hiển thị SAT PREPARATION bên English--!>    
    <?php else : ?>
        <!-- section Math Kindergarten -->
        <div class="col-md-12 css-div-kinder col-xs-12 css-mobile-margin-top-10">
            <div class="col-md-6 css-padding-14-des col-xs-6 css-mobile-margin-top-35" style="display: flex" data-toggle="collapse" data-target="#section-1">
                <span class="glyphicon glyphicon-triangle-bottom css-icon-dow"></span></p>   
                <span class='txt-kinder' style="color: #000">Vocabulary and Grammar</span>
            </div>
            <div class="col-md-6 col-xs-12 col-xs-6">
                <div class="css-div-text" data-toggle="collapse" data-target="#section-1">
                    <span class='css-color-7F7F7F'>Subscription Fee:</span>
                    <span class='css-txt-month'>$ 50.00 / month</span>
                </div>
                <div>
                    <input type="button" id="btn-math-kinder" class="css-btn-ikmath btn-subscrible" value="Subscribe" data-type="1">
                </div>
            </div>
        </div>
        <div id="section-1" class="collapse">
            <!--Phần nội dung bị ẩn hiện khi click vào section!-->
            <?php load_html_by_class_type(1);?>  
        </div>
    <!-- section Math Kindergarten -->
        <div class="col-md-12 css-div-kinder col-xs-12 css-mobile-margin-top-10 css-margin-top-22-des" >
            <div class="col-md-6 css-padding-14-des col-xs-6 css-mobile-margin-top-35" style="display: flex" data-toggle="collapse" data-target="#section-2">
                <span class="glyphicon glyphicon-triangle-bottom css-icon-dow"></span></p> 
                <span class='txt-kinder' style="color: #000">Writing Practice</span>
            </div>
            <div class="col-md-6 col-xs-12 col-xs-6">
                <div class="css-div-text" data-toggle="collapse" data-target="#section-2">
                    <span class='css-color-7F7F7F'>Subscription Fee:</span>
                    <span class='css-txt-month'>$ 20.00 / month</span>
                </div>
                <div>
                    <input type="button" id="btn-math-kinder" class="css-btn-ikmath btn-subscrible" value="Subscribe" data-type="2">
                </div>
            </div>
        </div >
        <div id="section-2" class="collapse">
    <!--Phần nội dung bị ẩn hiện khi click vào section!-->
            <?php load_html_by_class_type(2);?> 
        </div>
    <!-- section Math Kindergarten -->
        <div class="col-md-12 css-div-kinder col-xs-12 css-mobile-margin-top-10 css-margin-top-22-des" >
            <div class="col-md-6 css-padding-14-des col-xs-6 css-mobile-margin-top-35" style="display: flex" data-toggle="collapse" data-target="#section-3">
                <span class="glyphicon glyphicon-triangle-bottom css-icon-dow"></span></p> 
                <span class='txt-kinder' style="color: #000">SAT Test 1</span>
            </div>
            <div class="col-md-6 col-xs-12 col-xs-6">
                <div class="css-div-text" data-toggle="collapse" data-target="#section-3">
                    <span class='css-color-7F7F7F'>Subscription Fee:</span>
                    <span class='css-txt-month'>$ 50.00 / month</span>
                </div>
                <div>
                    <input type="button" id="btn-math-kinder" class="css-btn-ikmath btn-subscrible" value="Subscribe" data-type="3">
                </div>
            </div>
        </div>
        <div id="section-3" class="collapse">
    <!--Phần nội dung bị ẩn hiện khi click vào section!-->
            <?php load_html_by_class_type(3);?> 
        </div>
    <!-- section Math Kindergarten -->
        <div class="col-md-12 css-div-kinder col-xs-12 css-mobile-margin-top-10 css-margin-top-22-des" >
            <div class="col-md-6 css-padding-14-des col-xs-6 css-mobile-margin-top-35" style="display: flex" data-toggle="collapse" data-target="#section-4">
                <span class="glyphicon glyphicon-triangle-bottom css-icon-dow"></span></p> 
                <span class='txt-kinder' style="color: #000">SAT Test 2</span>
            </div>
            <div class="col-md-6 col-xs-12 col-xs-6">
                <div class="css-div-text" data-toggle="collapse" data-target="#section-4">
                    <span class='css-color-7F7F7F'>Subscription Fee:</span>
                    <span class='css-txt-month'>$ 50.00 / month</span>
                </div>
                <div>
                    <input type="button" id="btn-math-kinder" class="css-btn-ikmath btn-subscrible" value="Subscribe" data-type="4">
                </div>
            </div>
        </div>
    <!--Phần nội dung bị ẩn hiện khi click vào section!-->
        <div id="section-4" class="collapse">
            <?php load_html_by_class_type(4);?> 
        </div>

    <!-- section Math Kindergarten -->
        <div class="col-md-12 css-div-kinder col-xs-12 css-mobile-margin-top-10 css-margin-top-22-des">
            <div class="col-md-6 css-padding-14-des col-xs-6 css-mobile-margin-top-35" style="display: flex" data-toggle="collapse" data-target="#section-5">
                <span class="glyphicon glyphicon-triangle-bottom css-icon-dow"></span></p> 
                <span class='txt-kinder' style="color: #000">SAT Test 3</span>
            </div>
            <div class="col-md-6 col-xs-12 col-xs-6">
                <div class="css-div-text" data-toggle="collapse" data-target="#section-5">
                    <span class='css-color-7F7F7F'>Subscription Fee:</span>
                    <span class='css-txt-month'>$ 50.00 / month</span>
                </div>
                <div>
                    <input type="button" id="btn-math-kinder" class="css-btn-ikmath btn-subscrible" value="Subscribe" data-type="5">
                </div>
            </div>
        </div>
    <!--Phần nội dung bị ẩn hiện khi click vào section!-->
        <div id="section-5" class="collapse">
            <?php load_html_by_class_type(5);?> 
        </div>

    <!-- section Math Kindergarten -->
        <div class="col-md-12 css-div-kinder col-xs-12 css-mobile-margin-top-10 css-margin-top-22-des">
            <div class="col-md-6 css-padding-14-des col-xs-6 css-mobile-margin-top-35" style="display: flex" data-toggle="collapse" data-target="#section-6">
                <span class="glyphicon glyphicon-triangle-bottom css-icon-dow"></span></p> 
                <span class='txt-kinder' style="color: #000">SAT Test 4</span>
            </div>
            <div class="col-md-6 col-xs-12 col-xs-6">
                <div class="css-div-text" data-toggle="collapse" data-target="#section-6">
                    <span class='css-color-7F7F7F'>Subscription Fee:</span>
                    <span class='css-txt-month'>$ 50.00 / month</span>
                </div>
                <div>
                    <input type="button" id="btn-math-kinder" class="css-btn-ikmath btn-subscrible" value="Subscribe" data-type="6">
                </div>
            </div>
        </div>
    <!--Phần nội dung bị ẩn hiện khi click vào section!-->
        <div id="section-6" class="collapse">
            <?php load_html_by_class_type(6);?> 
        </div>
    <!-- section Math Kindergarten -->
        <div class="col-md-12 css-div-kinder col-xs-12 css-mobile-margin-top-10 css-margin-top-22-des">
            <div class="col-md-6 css-padding-14-des col-xs-6 css-mobile-margin-top-35" style="display: flex" data-toggle="collapse" data-target="#section-7">
                <span class="glyphicon glyphicon-triangle-bottom css-icon-dow"></span></p> 
                <span class='txt-kinder' style="color: #000">SAT Test 5</span>
            </div>
            <div class="col-md-6 col-xs-12 col-xs-6">
                <div class="css-div-text" data-toggle="collapse" data-target="#section-7">
                    <span class='css-color-7F7F7F'>Subscription Fee:</span>
                    <span class='css-txt-month'>$ 50.00 / month</span>
                </div>
                <div>
                    <input type="button" id="btn-math-kinder" class="css-btn-ikmath btn-subscrible" value="Subscribe" data-type="7">
                </div>
            </div>
        </div>
    <!--Phần nội dung bị ẩn hiện khi click vào section!-->
        <div id="section-7" class="collapse">
            <?php load_html_by_class_type(7);?> 
        </div>
    <?php endif; ?>
</div>

<!--modal show data when click-->
<div class="modal fade modal-purple modal-large" id="class-detail-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                 <span style="padding-top: 4%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3 class="modal-title" id="myModalLabel"><?php _e('Class Detail', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-body body-custom" style="max-height: calc(67vh - 210px);overflow-y: auto;"></div>
        </div>
    </div>
</div>
<div id="require-modal-sat1-preparation" class="modal fade modal-purple" aria-hidden="true">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3 style="color:#ffd26b;"><?php _e('Subscription', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-body body-custom"></div>
            <div class="modal-footer footer-custom">
                <button id="sub-modal-sat1-preparation" data-sat-class="" data-subscription-type="" data-type="" class="btn-custom choose-sub-btn" target="_blank" data-toggle="modal"></button>
            </div>
        </div>
    </div>
</div>
<!--modal login-->
<div id="modal-show-login" class="modal fade modal-purple" aria-hidden="true">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 4%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3 style="color:#ffd26b;"><?php _e('Login Required', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-body body-custom">
                <span>Please login in order to continue to use this function.</span>
            </div>
            <div class="modal-footer footer-custom">
                <button id="btn-login" class="btn-custom choose-sub-btn" style="height: 42px;">Login</button>
            </div>
        </div>
    </div>
</div>
<!--modal Vocabulary/Grammar-->
<div id="grammar-subscrible-modal" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Purchase SAT Subscriptions', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo home_url_ssl() ?>/?r=payments">
                <input type="hidden" name="sub-type" id="grammar-sub-type" value="3">
                <input type="hidden" name="sat-class" id="grammar-class" value="1">
                <input type="hidden" name="sub-id" value="0">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="font-dialog col-xs-12" style="border-bottom: 1px solid #d6d6d6;"><?php _e('Selected Class', 'iii-dictionary') ?></label>
                                <p class="selected-class col-xs-12" id="selected-class-grammar" >Vocabulary and Grammar</p>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12" style="padding-top: 3%">
                            <div class="form-group">                                
                                <label class="font-dialog"><?php _e('Number of Months', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control" name="sat-months" id="grammar-month">
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
<div id="writing-subscrible-modal" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Purchase SAT Subscriptions', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo home_url_ssl() ?>/?r=payments">
                <input type="hidden" name="sub-type" id="writing-sub-type" value="3">
                <input type="hidden" name="sat-class" id="writing-class" value="2">
                <input type="hidden" name="sub-id" value="0">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="font-dialog col-xs-12" style="border-bottom: 1px solid #d6d6d6;"><?php _e('Selected Class', 'iii-dictionary') ?></label>
                                <p class="selected-class col-xs-12" id="selected-class-writing" >Writing Practice</p>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12" style="padding-top: 3%">
                            <div class="form-group">                                
                                <label class="font-dialog"><?php _e('Number of Months', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control" name="sat-months" id="writing-month">
                                    <?php for ($i = 1; $i <= 24; $i++) : ?>
                                        <option value="<?php echo $i ?>"><?php printf(_n('%s month', '%s months', $i, 'iii-dictionary'), $i) ?></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-sm-12 padding-top-2">
                            <div class="box-gray-dialog" style="text-align: right">
                                <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color708b23">$<span id="total-amount-writing" class="color708b23">0</span></span>
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
</div><div id="sat-test-subscrible-modal" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Purchase SAT Subscriptions', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo home_url_ssl() ?>/?r=payments">
                <input type="hidden" name="sub-type" id="sat-test-sub-type" value="3">
                <input type="hidden" name="sat-class" id="sat-test-class" value="">
                <input type="hidden" name="sub-id" value="0">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="font-dialog col-xs-12" style="border-bottom: 1px solid #d6d6d6;"><?php _e('Selected Class', 'iii-dictionary') ?></label>
                                <p class="selected-class col-xs-12" id="selected-class1" >SAT TEST</p>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12" style="padding-top: 3%">
                            <div class="form-group">                                
                                <label class="font-dialog"><?php _e('Number of Months', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control" name="sat-months" id="select-sat-test-month">
                                    <?php for ($i = 1; $i <= 24; $i++) : ?>
                                        <option value="<?php echo $i ?>"><?php printf(_n('%s month', '%s months', $i, 'iii-dictionary'), $i) ?></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                        </div>
                        <?php
                           $select_class_options = array(CLASS_SAT1 => __('SAT Test 1', 'iii-dictionary'), CLASS_SAT2 => __('SAT Test 2', 'iii-dictionary'), CLASS_SAT3 => __('SAT Test 3', 'iii-dictionary'),
                                CLASS_SAT4 => __('SAT Test 4', 'iii-dictionary'), CLASS_SAT5 => __('SAT Test 5', 'iii-dictionary'))
                            ?>

                            <div class="col-sm-12" id="sat-test-block">
                                <div class="form-group">
                                    <label class="font-dialog"><?php _e('Practice Test', 'iii-dictionary') ?></label>
                                    <select class="select-box-it form-control sel-sat-class" id="select-sat-test-class">
                                        <?php foreach ($select_class_options as $key => $value) : ?>
                                            <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>  
                        <div class="col-sm-12 padding-top-2">
                            <div class="box-gray-dialog" style="text-align: right">
                                <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color708b23">$<span id="total-sat-test" class="color708b23">0</span></span>
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
<div id="sat1-subscrible-modal" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Purchase SAT Subscriptions', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo home_url_ssl() ?>/?r=payments">
                <input type="hidden" name="sub-type" id="sat1-sub-type" value="7">
                <input type="hidden" name="sat-class" id="sat1-class" value="">
                <input type="hidden" name="sub-id" value="0">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="font-dialog col-xs-12" style="border-bottom: 1px solid #d6d6d6;"><?php _e('Selected Class', 'iii-dictionary') ?></label>
                                <p class="selected-class col-xs-12" id="class-sat1" >SAT</p>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12" style="padding-top: 3%">
                            <div class="form-group">                                
                                <label class="font-dialog"><?php _e('Number of Months', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control" name="sat-months" id="select-sat1-months">
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

                            <div class="col-sm-12" id="sat-test-block">
                                <div class="form-group">
                                    <label class="font-dialog"><?php _e('Practice Test', 'iii-dictionary') ?></label>
                                    <select class="select-box-it form-control sel-sat-class" id="select-sat1-class">
                                        <?php foreach ($select1_class_options as $key => $value) : ?>
                                            <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>  
                        <div class="col-sm-12 padding-top-2">
                            <div class="box-gray-dialog" style="text-align: right">
                                <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color708b23">$<span id="total-amount-sat1-class" class="color708b23">1</span></span>
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
<div id="sat1-preparation-subscrible-modal" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Purchase SAT Subscriptions', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo home_url_ssl() ?>/?r=payments">
                <input type="hidden" name="sub-type" id="sat1-preparation-sub-type" value="7">
                <input type="hidden" name="sat-class" id="sat1-preparation-class" value="9">
                <input type="hidden" name="sub-id" value="0">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="font-dialog col-xs-12" style="border-bottom: 1px solid #d6d6d6;"><?php _e('Selected Class', 'iii-dictionary') ?></label>
                                <p class="selected-class col-xs-12" id="selected-class1" >SAT</p>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12" style="padding-top: 3%">
                            <div class="form-group">                                
                                <label class="font-dialog"><?php _e('Number of Months', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control" name="sat-months" id="select-sat1-preparation-months" >
                                    <?php for ($i = 1; $i <= 24; $i++) : ?>
                                        <option value="<?php echo $i ?>"><?php printf(_n('%s month', '%s months', $i, 'iii-dictionary'), $i) ?></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                        </div>   
                        <div class="col-sm-12" id="sat-test-block">
                            <div class="form-group">
                                <label class="font-dialog"><?php _e('Practice Test', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control sel-sat-class" id="select-sat1-preparation-class" disabled="disabled">
                                        <option value="">SAT Preparation</option>
                                </select>
                            </div>
                        </div>  
                        <div class="col-sm-12 padding-top-2">
                            <div class="box-gray-dialog" style="text-align: right">
                                <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color708b23">$<span id="total-amount-sat1-preparation" class="color708b23">1</span></span>
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
<div id="sat2-subscrible-modal" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Purchase SAT Subscriptions', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo home_url_ssl() ?>/?r=payments">
                <input type="hidden" name="sub-type" id="sat2-sub-type" value="7">
                <input type="hidden" name="sat-class" id="sat2-class" value="">
                <input type="hidden" name="sub-id" value="0">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="font-dialog col-xs-12" style="border-bottom: 1px solid #d6d6d6;"><?php _e('Selected Class', 'iii-dictionary') ?></label>
                                <p class="selected-class col-xs-12" id="class-sat2" >SAT 2</p>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12" style="padding-top: 3%">
                            <div class="form-group">                                
                                <label class="font-dialog"><?php _e('Number of Months', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control" name="sat-months" id="select-sat2-months">
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

                            <div class="col-sm-12" id="sat-test-block">
                                <div class="form-group">
                                    <label class="font-dialog"><?php _e('Practice Test', 'iii-dictionary') ?></label>
                                    <select class="select-box-it form-control sel-sat-class" id="select-sat2-class">
                                        <?php foreach ($select2_class_options as $key => $value) : ?>
                                            <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>  
                        <div class="col-sm-12 padding-top-2">
                            <div class="box-gray-dialog" style="text-align: right">
                                <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color708b23">$<span id="total-amount-sat2-class" class="color708b23">1</span></span>
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
<div id="sat2-preparation-subscrible-modal" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Purchase SAT Subscriptions', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo home_url_ssl() ?>/?r=payments">
                <input type="hidden" name="sub-type" id="sat2-preparation-sub-type" value="8">
                <input type="hidden" name="sat-class" id="sat2-preparation-class" value="15">
                <input type="hidden" name="sub-id" value="0">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="font-dialog col-xs-12" style="border-bottom: 1px solid #d6d6d6;"><?php _e('Selected Class', 'iii-dictionary') ?></label>
                                <p class="selected-class col-xs-12" id="selected-class1" >SAT 2 Preparation</p>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12" style="padding-top: 3%">
                            <div class="form-group">                                
                                <label class="font-dialog"><?php _e('Number of Months', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control" name="sat-months" id="select-sat2-preparation-months" >
                                    <?php for ($i = 1; $i <= 24; $i++) : ?>
                                        <option value="<?php echo $i ?>"><?php printf(_n('%s month', '%s months', $i, 'iii-dictionary'), $i) ?></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                        </div>   
                        <div class="col-sm-12" id="sat-test-block">
                            <div class="form-group">
                                <label class="font-dialog"><?php _e('Practice Test', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control sel-sat-class" id="select-sat2-preparation-class" disabled="disabled">
                                        <option value="">SAT 2 Preparation</option>
                                </select>
                            </div>
                        </div>  
                        <div class="col-sm-12 padding-top-2">
                            <div class="box-gray-dialog" style="text-align: right">
                                <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color708b23">$<span id="total-amount-sat2-preparation" class="color708b23">1</span></span>
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
<div id="ikmath-course-subscrible-modal" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Purchase SAT Subscriptions', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo home_url_ssl() ?>/?r=payments">
                <input type="hidden" name="sub-type" id="ikmath-course-sub-type" value="12">
                <input type="hidden" name="sat-class" id="ikmath-course-sat-class" value="">
                <input type="hidden" name="sub-id"  value="0">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="font-dialog col-xs-12" style="border-bottom: 1px solid #d6d6d6;"><?php _e('Selected Class', 'iii-dictionary') ?></label>
                                <p class="selected-class col-xs-12" id="selected-class1" >IK MATH</p>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12" style="padding-top: 3%">
                            <div class="form-group">                                
                                <label class="font-dialog"><?php _e('Number of Months', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control" name="sat-months" id="sel-month-ikmath-course">
                                    <?php for ($i = 1; $i <= 24; $i++) : ?>
                                        <option value="<?php echo $i ?>"><?php printf(_n('%s month', '%s months', $i, 'iii-dictionary'), $i) ?></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                        </div>
                        <?php
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
                        <div class="col-xs-12" id="">
                            <div class="form-group">
                                <label class="font-dialog"><?php _e('Simulated Test', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control sel-sat-class" id="select-class-ikmath-course">
                                <?php foreach ($select3_class_options as $key => $value) : ?>
                                    <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 padding-top-2">
                            <div class="box-gray-dialog" style="text-align: right">
                                <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color708b23">$<span id="total-ikmath-course" class="color708b23">1</span></span>
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
<?php if (empty($client)) : ?>
    <?php get_dict_footer() ?>
<?php else : ?>
    <?php get_math_footer() ?>
<?php endif ?>

<?php
    function load_html_by_class_type($class_type_id) {
?>
            <div class="box-purple css-des-table-ik">
                <?php
                    $filter['orderby'] = 'ordering';
                    $filter['items_per_page'] = 25;
                    $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
                    $filter['class_type'] = $class_type;
                    $filter['offset'] = 0;
                    $filter['items_per_page'] = 99999999;
                    $filter['group_type'] = GROUP_CLASS;
                    $filter['class_type'] = $class_type_id;
                    $groups = MWDB::get_groups($filter, $filter['offset'], $filter['items_per_page']);
                ?>
                <table class="table table-striped table-condensed ik-table1 vertical-middle" id="ikmath">
                    <thead >
                        <tr css-des-th-ik style="height: 45px;">
                            <th class="css-color-838383 padding-left-4"><?php _e('Content', 'iii-dictionary') ?></th>
                            <th class="hidden-xs css-color-838383"><?php _e('No. of Worksheets', 'iii-dictionary') ?></th>
                            <th class="css-color-838383"><?php _e('Detail', 'iii-dictionary') ?></th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr><td colspan="4"><?php echo $pagination ?></td></tr>
                    </tfoot>
                    <tbody>
                    <?php if (!empty($groups->items)) :
                    foreach ($groups->items as $group) :
                    $class_type_id=$group->class_type_id;
                    ?>
                    <tr>
                        <td style="text-align: left" class="padding-left-4"><?php echo $group->content ?></td>
                        <td class="hidden-xs"><?php echo is_null($group->no_homeworks) ? 0 : $group->no_homeworks ?></td>
                        <td><a href="#" class="class-detail-btn css-color-000 css-font-weight">Click</a><div><?php
                        $filter['homework_result'] = true;
                        $filter['user_id'] = get_current_user_id();
                        $filter['is_active'] = 1;
                        $homeworks = MWDB::get_group_homeworks($group->id, $filter, $filter['offset'], $filter['items_per_page']);
                        echo $group->detail;
                        echo '<h3 class="modal-title" style="     color: #fff779;" id="myModalLabel">List Homework in Group ' . $group->content . ' </h3>';
                        foreach ($homeworks->items as $hw):
                            echo ' - ' . $hw->sheet_name . "<br>";
                        endforeach;
                        ?></div>
                        </td>
                    </tr>
                    <?php endforeach;
                    else :
                        ?>
                        <tr><td colspan="4"><?php _e('No classes', 'iii-dictionary') ?></td></tr>
                    <?php endif ?>
                </tbody>
            </table>
        </div> 
<?php } ?>
<script>
    var check_login = <?php if($login){ echo "1"; }else{ echo "0";} ?>;
    var satGp = <?php echo mw_get_option('sat-grammar-price') ?>;
    var satWp = <?php echo mw_get_option('sat-writing-price') ?>;
    var satStp = <?php echo mw_get_option('sat-test-price') ?>;
    var satMIP = <?php echo mw_get_option('math-sat1-price') ?>;
    var sat1Pre = <?php echo mw_get_option('math-sat1-preparation') ?>;
    var satMIIP = <?php echo mw_get_option('math-sat2-price') ?>;
    var sat2Pre = <?php echo mw_get_option('math-sat2-preparation') ?>;
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
</script>