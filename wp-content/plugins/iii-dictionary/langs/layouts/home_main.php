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
                <div class="col-md-4 col-sm-12 col-xs-12">

                    <div id="img_interface_ensat" >
<!--                        <h1 style=" text-align: center; padding-top: 25px; "><?php _e('Complete', 'iii-dictionary'); ?></h1><br>
                        <h1 style=" text-align: center; padding-top: 60px; "><?php _e('Preparation', 'iii-dictionary'); ?></h1>
                        <p style=" text-align: center;margin:auto;border-top: 2px solid rgb(217, 217, 217); width: 70%"></p>
                        <p id="text_ensat" style=" text-align: center;margin-top: 5px;margin-bottom: 5px "><?php _e('Get into the college you want!', 'iii-dictionary'); ?></p>
                        <p style=" text-align: center;margin:auto;border-top: 2px solid rgb(217, 217, 217); width: 70%"></p>-->
                        <p class="p-link-start" ><a class="a-link-start" href="<?php echo site_home_url();?>?r=ensat"></a></p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12 col-xs-12">
                    <div id="img_interface_math" >
                        <!--<h1 style=" text-align: center; padding-top: 25px; "><?php _e('Complete Online', 'iii-dictionary'); ?></h1><br>-->
                        <!--<p style="padding-top: 37%"></p>-->
                        <!--<p style=" text-align: center;margin:auto;border-top: 2px solid rgb(217, 217, 217); width: 70%"></p>-->
                        <!--<p style=" text-align: center; padding-top: 5px; "><?php _e('Single stream learning system', 'iii-dictionary'); ?></p>-->
                        <!--<p style=" text-align: center; "><?php _e('Plus on-demand tutor available', 'iii-dictionary'); ?></p>-->
                        <!--<p style=" text-align: center;margin:auto;border-top: 2px solid rgb(217, 217, 217); width: 70%"></p>-->
                        <p class="p-link-start" ><a class="a-link-start" href="<?php echo site_math_url(); ?>"></a></p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12 col-xs-12">
                    <div id="img_interface_english" >
<!--                        <h1 style=" text-align: center; padding-top: 25px; "><?php _e('Complete Online', 'iii-dictionary'); ?></h1><br>
                        <p style="padding-top: 35%"></p>
                        <p style=" text-align: center;margin:auto;border-top: 2px solid rgb(217, 217, 217); width: 70%"></p>
                        <p style=" text-align: center; padding-top: 5px; "><?php _e('Grammar / Vocabulary /', 'iii-dictionary'); ?></p>
                        <p style=" text-align: center; "><?php _e('Spelling / Writing with Tutor', 'iii-dictionary'); ?></p>
                        <p style=" text-align: center;margin:auto;border-top: 2px solid rgb(217, 217, 217); width: 70%"></p>-->
                        <p class="p-link-start" ><a class="a-link-start" href="<?php echo site_home_url(); ?>"></a></p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>