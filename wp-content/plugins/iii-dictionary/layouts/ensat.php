<?php 
	$link_list_group = get_option_name_link();
	//some function at home
	if(!empty($_POST['data-join'])) {
		MWDB::lang_join_group($_POST);
	}
?>
<?php get_header(); ?>	
<?php
$URL = $_SERVER['REQUEST_URI'];
$segment = explode('/',$URL);
if($segment[2] == '?r=ensat'){
	include 'home_sat_new.php';
}elseif($segment[2] == 'englishteacher'){
	include 'english_teacher.php';
}else{
	?>
	<main class="home" id="home">
	<div id="content">
    	<div class="sat_interface" >
            <div class="banner_sat">
                <h1><?php _e('Complete','iii-dictionary'); ?> <span style="color:#b52324"><?php _e('SAT','iii-dictionary'); ?></span><br>
                    <?php _e('Preparation','iii-dictionary'); ?>
                </h1>
                <p><?php _e('Get in to the college you want!','iii-dictionary'); ?></p>
            </div>
            <div class="content_sat">
                <div class="row">
                    <div class="col-md-4 col-sm-12 col-xs-12">
                        <div class="sat_prep sat_prep_1">
                            <div class="prep_sat">
                                <h2><?php _e('English SAT Prep.','iii-dictionary'); ?></h2>
                                <p><?php _e('(2016 style SAT)','iii-dictionary'); ?></p>
                                <p class="detail_sat"><a href="#" ><?php _e('See Details','iii-dictionary'); ?></a><img src="<?php echo get_template_directory_uri(); ?>/library/images/icon-rrr.png"></p>
                            </div>
                            <span class="check check_e glyphicon glyphicon-ok"></span>
                            <div class="popup_sat"> 
                                <h4 style="color: #ff0002;"><strong><?php _e('English SAT Prep.','iii-dictionary'); ?></strong><span><?php _e('(2016 style SAT)','iii-dictionary'); ?></span></h4>
                                <ol>
                                    <li><?php _e('Complete Grammar review and vocabulary enhancement','iii-dictionary'); ?></li>
                                    <li><?php _e('Writing practice with teacher\'s support','iii-dictionary'); ?></li>
                                    <li><?php _e('Five practice tests','iii-dictionary'); ?></li>
                                </ol>
                            </div>
                            <p class="letgo"><a href="<?php echo site_home_url(); ?>/?r=sat-preparation" ><?php _e('Let\'s Go!','iii-dictionary'); ?> <span class="glyphicon glyphicon-arrow-right"></span></a></p>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 col-xs-12">
                        <div class="sat_prep sat_prep_2">
                            <div class="prep_sat">
                                <h2><?php _e('Math SAT I Prep.','iii-dictionary'); ?></h2>
                                <p><?php _e('(2016 style SAT)','iii-dictionary'); ?></p>
                                <p class="detail_sat"><a href="#" ><?php _e('See Details','iii-dictionary'); ?></a><img src="<?php echo get_template_directory_uri(); ?>/library/images/icon-rrr.png"></p>
                            </div>
                            <span class="check check_m glyphicon glyphicon-ok"></span>
                            <div class="popup_sat">
                                <h4 style="color: #055fbb;"><strong><?php _e('Math SAT I Prep.','iii-dictionary'); ?> </strong><span><?php _e('(2016 style SAT)','iii-dictionary'); ?></span></h4>
                                <ol>
                                    <li><?php _e('Complete math review for SAT I','iii-dictionary'); ?></li>
                                    <li><?php _e('Five practice tests','iii-dictionary'); ?></li>
                                </ol>
                            </div>
                            <p class="letgo"><a href="<?php echo site_math_url(); ?>/?r=sat-preparation/sat1prep&client=math-sat1" ><?php _e('Let\'s Go!','iii-dictionary'); ?> <span class="glyphicon glyphicon-arrow-right"></span></a></p>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 col-xs-12">
                        <div class="sat_prep sat_prep_3">
                            <div class="prep_sat">
                                <h2><?php _e('Math SAT II Prep.','iii-dictionary'); ?></h2>
                                <p class="detail_sat"><a href="#" ><?php _e('See Details','iii-dictionary'); ?></a><img src="<?php echo get_template_directory_uri(); ?>/library/images/icon-rrr.png"></p>
                            </div>
                            <span class="check check_m2 glyphicon glyphicon-ok"></span>
                            <div class="popup_sat">
                                <h4 style="color: #b126ff;"><strong><?php _e('Math SAT II Prep.','iii-dictionary'); ?> </strong></h4>
                                <ol>
                                    <li><?php _e('Complete math review for SAT II','iii-dictionary'); ?></li>
                                    <li><?php _e('Five practice tests','iii-dictionary'); ?></li>
                                </ol>                                
                            </div>
                            <p class="letgo"><a href="<?php echo site_math_url(); ?>/?r=sat-preparation/sat2prep&client=math-sat2" ><?php _e('Let\'s Go!','iii-dictionary'); ?> <span class="glyphicon glyphicon-arrow-right"></span></a></p>
                        </div>
                    </div>
                </div>
                <div class="row"> 
                    <div class="col-md-6 col-sm-12 col-xs-12">
                        <div class="sat_bot sat_bot_left">
                            <div class="prep_bot">
                                <p><?php _e('Improve','iii-dictionary'); ?></p>
                                <h4>
                                    <span><?php _e('Essay Writing','iii-dictionary'); ?></span>
                                    <a href="" class="detail_sat"><?php _e('See Details','iii-dictionary'); ?></a>
                                    <img src="<?php echo get_template_directory_uri(); ?>/library/images/icon-rrr.png">
                                </h4>
                            </div>
                            <div class="popup_sat">
                                <h4 style="color:#00c5b7"><?php _e('Improve','iii-dictionary'); ?> <span><?php _e('Essay Writing','iii-dictionary'); ?></span></h4>
                                <p class="popup_detail"><?php _e('Improve English writing with worksheets','iii-dictionary'); ?><br /><?php _e('Teachers provide online English writing support','iii-dictionary'); ?></p>
                            </div><a class="letgo" href="<?php echo site_home_url(); ?>/?r=sat-preparation/writing" ><?php _e('Let\'s Go!','iii-dictionary'); ?> <span class="glyphicon glyphicon-arrow-right"></span></a>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 col-xs-12">
                        <div class="sat_bot sat_bot_right">
                            <div class="prep_bot">
                                <p><?php _e('Math Improvement','iii-dictionary'); ?></p>
                                <h4>
                                    <span><?php _e('Math Tutoring','iii-dictionary'); ?> </span>
                                    <a href="" class="detail_sat"><?php _e('See Details','iii-dictionary'); ?></a>
                                    <img src="<?php echo get_template_directory_uri(); ?>/library/images/icon-rrr.png">
                                </h4>
                            </div>
                            <div class="popup_sat">
                                <h4 style="color:#00c5b7"><?php _e('Math Improvement','iii-dictionary'); ?> - <span><?php _e('Math Tutoring','iii-dictionary'); ?></span></h4>
                                <p class="popup_detail"><?php _e('Self-study plus online tutoring support.','iii-dictionary'); ?><br /><?php _e('(Actual Math teacher)','iii-dictionary'); ?></p>
                            </div><a class="letgo" href="<?php echo site_math_url(); ?>/?r=homework-status" ><?php _e('Let\'s Go!','iii-dictionary'); ?> <span class="glyphicon glyphicon-arrow-right"></span></a>
                        </div>
                    </div>
                </div>
            </div>
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

<div id="popup-info-dialog" class="modal fade modal-white modal-no-padding popup-info-dialog" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-body">
            <a href="#" data-dismiss="modal" aria-hidden="true" class="close-transp"></a>
			<img id="popup-info-img" src="#" alt="">
        </div>
      </div>
    </div>
</div>
<script>
	(function($){
		$(function(){
			$(".view-sub-modal").click(function(e){
				e.preventDefault();
				var _img = $("#popup-info-img");
				var _m = $("#popup-info-dialog");
				_img.attr("src", $(this).attr("data-img")).load(function(){
					_m.find(".modal-dialog").width(this.width);
				});
				$("#new-to-our-product-dialog").one('show.bs.modal', function () {

                                                                $(this).off('hidden.bs.modal');
                                                                _m.modal();
                                                                _m.style.display = 'block';
                                                            }).modal("show");
			});

			$("#popup-info-dialog").on("hidden.bs.modal", function(){
				$("#new-to-our-product-dialog").modal();
			});

			$("#about-teacher-dialog").on("hidden.bs.modal", function(){
				window.location.href = home_url + "/?r=teaching";
			});

			$("#about-student-dialog").on("hidden.bs.modal", function(){
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
<?php if(is_user_logged_in() && isset($_SESSION['newuser'])) : ?>
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
		(function($){ $(function(){ $('#signup-success-dialog').modal('show'); }); })(jQuery);
	</script>
<?php $_SESSION['newuser'] = null; endif ?>
<?php MWHtml::ik_site_messages(); get_footer() ?>