<!--<main class="home" id="home">
        <div id="content">
        <div class="sat_interface" >
            <div class="banner_sat_main">
                <div id="banner-ensat"></div>
                <div id="banner-math"></div>
                <div id="banner-english"></div>
            </div>
        </div>
    </div>
</main>-->
<?php
$link_list_group = get_option_name_link();
?>
<main class="home" id="home">
    <div id="content">
        <div class="sat_interface" >
            <div class="content_sat">
                <div class="col-sm-4 col-xs-12 css-div-img-home">

                    <div id="img_interface_ensat" >
                        <p class="p-link-start" ><a class="a-link-start" href="<?php echo site_home_url();?>?r=ensat"></a></p>
                    </div>
                </div>
                <div class="col-sm-4 col-xs-12 css-div-img-home">
                    <div id="img_interface_math" >
                        <p class="p-link-start" ><a class="a-link-start" href="<?php echo site_math_url(); ?>"></a></p>
                    </div>
                </div>
                <div class="col-sm-4 col-xs-12 css-div-img-home">
                    <div id="img_interface_english" >
                        <p class="p-link-start" ><a class="a-link-start" href="<?php echo site_home_url(); ?>"></a></p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>