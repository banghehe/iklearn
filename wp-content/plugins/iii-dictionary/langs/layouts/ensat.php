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
			<div id="content" style="position: relative;">
					<?php MWHtml::get_list_lang() ?>
					<?php MWHtml::manage_your_class() ?>
				<div id="top-content">
					<div class="column-header">
						<a href="<?php echo locale_home_url() ?>/?r=manage-subscription" class="black-color"><?php _e('Do you have an activation code? Click here', 'iii-dictionary') ?> &gt;</a>
					</div>
					<div class="left-column">
						<div class="column-content">
							<h2><?php _e('Complete Self-Study English Program', 'iii-dictionary') ?></h2>
							<small><?php _e('Grammar / Vocabulary / Spelling/ Writing / SAT Prep', 'iii-dictionary') ?></small>
						</div>
					</div>
					<div class="right-column">
						<div class="column-content">
							<div class="benefits-list">
								<h3><?php _e('Benefits for Students:', 'iii-dictionary') ?></h3>
								<ul>
									<li><?php _e('Various English Dictionary form Merriam-Webster', 'iii-dictionary') ?></li>
									<li><?php _e('Vocabulary Enhancement Tools', 'iii-dictionary') ?></li>
									<li><?php _e('Online grading and review', 'iii-dictionary') ?></li>
									<li><?php _e('Online English Writing tutor available', 'iii-dictionary') ?></li>
								</ul>
								<div class="separator"></div>
								<h3><?php _e('SAT Preparation', 'iii-dictionary') ?></h3>
								<ul>
									<li><?php _e('Quick review on grammar and vocabulary for SAT', 'iii-dictionary') ?></li>
									<li><?php _e('2016 New SAT format', 'iii-dictionary') ?></li>
									<li><?php _e('Autograde and online review session', 'iii-dictionary') ?></li>
								</ul>
							</div>
							<a href="#new-to-our-product-dialog" data-toggle="modal" class="btn green"><?php _e('Find out more', 'iii-dictionary') ?> <span class="icon-rarrow"></span></a>
						</div>
					</div>
					
				</div>
				<div class="bottom-content">
					<section class="block-content">
						<?php _e('<h2>Why</h2> <h3>Merriam-Webster?</h3>', 'iii-dictionary') ?>
						<a href="#why-merriam-dialog"  data-toggle="modal"><u><?php _e('Let\'s Find out', 'iii-dictionary') ?></u> &gt;</a>
					</section>

					<section class="block-content">
						<h3><?php _e('How can teachers earn extra income?', 'iii-dictionary') ?></h3>
						<a href="http://ikteach.com" target="_blank" data-toggle="modal"><u><?php _e('Let\'s Find out', 'iii-dictionary') ?></u> &gt;</a>
					</section>

					<section class="block-content">
						<h3><?php _e('How can students improve writing and prepare for the SAT?', 'iii-dictionary') ?></h3>
						<a href="#about-student-dialog" target="_blank" data-toggle="modal"><u><?php _e('Let\'s Find out', 'iii-dictionary') ?></u> &gt;</a>
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