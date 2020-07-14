<?php
	$is_math_panel = is_math_panel();
	$_page_title = __('Sign-up', 'iii-dictionary');
	
	if(isset($_POST['wp-submit']))
	{
		$form_valid = true;

		if(is_email($_POST['user_login'])) {
			if(email_exists($_POST['user_login'])) {
				ik_enqueue_messages(__('This email address is already registered. Please choose another one.', 'iii-dictionary'), 'error');
				$form_valid = false;
			}

			$user_email = $_POST['user_login'];
		}
		else {
			// we don't accept normal string as username anymore
			ik_enqueue_messages(__('This email address is invalid. Please choose another one.', 'iii-dictionary'), 'error');
			$form_valid = false;
			/* if(username_exists($_POST['user_login'])) {
				ik_enqueue_messages(__('This username is already registered. Please choose another one', 'iii-dictionary'), 'error');
				$form_valid = false;
			} */
		}

		if(trim($_POST['password']) == '') {
			ik_enqueue_messages(__('Passwords must not be empty', 'iii-dictionary'), 'error');
			$form_valid = false;
		}

		if($_POST['password'] !== $_POST['confirm_password'])
		{
			ik_enqueue_messages(__('Passwords must match', 'iii-dictionary'), 'error');
			$form_valid = false;
		}

		if(strlen($_POST['password']) < 6) 
		{
			ik_enqueue_messages(__('Passwords must be at least six characters long', 'iii-dictionary'), 'error');
			$form_valid = false;
		}

		if($_POST['birth-m'] != '00' || $_POST['birth-d'] != '00' || $_POST['birth-y'] != '')
		{
			if(checkdate($_POST['birth-m'], $_POST['birth-d'], $_POST['birth-y'])) {
				$_POST['date_of_birth'] = $_POST['birth-m'] . '/' . $_POST['birth-d'] . '/' . $_POST['birth-y'];
			}
			else {
				ik_enqueue_messages(__('Invalid date of birth', 'iii-dictionary'), 'error');
				$form_valid = false;
			}
		}
		else
		{
			$_POST['date_of_birth'] = '';
		}

		// form valid, create new user
		if($form_valid)
		{
			if(isset($user_email)) {
				$user_id = wp_create_user($_POST['user_login'], $_POST['password'], $user_email);
			}
			else {
				$user_id = wp_create_user($_POST['user_login'], $_POST['password']);
			}

			$userdata['ID'] = $user_id;

			if(isset($_POST['first_name']) && trim($_POST['first_name']) != '')
			{
				$userdata['first_name'] = $_POST['first_name'];
			}

			if(isset($_POST['last_name']) && trim( $_POST['last_name']) != '')
			{
				$userdata['last_name'] = $_POST['last_name'];
			}

			if(isset($userdata['first_name']) && isset($userdata['last_name']))
			{
				$userdata['display_name'] = $userdata['first_name'] . ' ' . $userdata['last_name'];
			}

			$new_user_id = wp_update_user( $userdata );

			update_user_meta( $user_id, 'date_of_birth', $_POST['date_of_birth'] );
			update_user_meta( $user_id, 'language_type', $_POST['language_type'] );

			// auto login the user
			$creds['user_login'] = $_POST['user_login'];
			$creds['user_password'] = $_POST['password'];
			$user = wp_signon( $creds, false );
                        $current_user = wp_get_current_user();
			// send confirmation email
			if(is_email($user_email))
			{
				$title = __('Congratulations! You have successfully signed up for iklearn.com', 'iii-dictionary');
                                $title_admin  = __('[Innovative Knowledge] New User Registration', 'iii-dictionary');
				$message =  __('You have successfully signed up for iklearn.com.', 'iii-dictionary') . "<br>" .
							__('If you have questions or need support, please contact us at support@iklearn.com.', 'iii-dictionary') . "<br>" .
							__('If you forgot your password, please click on the "forgot password" button after entering your username (email address).', 'iii-dictionary') . "<br>" . "<br>" .
							__('Please enjoy the Merriam Webster dictionary and English learning tools.', 'iii-dictionary') . "<br>". "<br>" .
							__('For teachers - You may assign homework practice sheets available on this site. You can also create your own homework sheets. Please click here for details. The homework that is turned in by students is automatically graded and saved in your teacher\'s box. Currently, the available homework worksheets are (1) spelling practice and (2) vocabulary and grammar.', 'iii-dictionary') . "<br>" .
							__('Happy learning!', 'iii-dictionary') . "<br>" . "<br>" .
							__('Support', 'iii-dictionary');
                                
                                $message_admin = __('New user registration on your site Innovative Knowledge: Username:').$userdata['display_name']. 
                                                 __(' Email:').$user_email;

				if ( $message && !wp_mail( $user_email, wp_specialchars_decode( $title ), $message ) ) {			
				}
                                $admin_email = get_option('admin_email');
                                if ( $message_admin && !wp_mail( $admin_email, wp_specialchars_decode( $title_admin ), $message_admin ) ) {			
				}
			}

			$_SESSION['newuser'] = 1;
			$_SESSION['mw_referer'] = locale_home_url();
			wp_redirect($_SESSION['mw_referer']);
			exit;
		}
	}
?>
<?php if(!$is_math_panel) : ?>
	<?php get_dict_header($_page_title) ?>
<?php else : ?>
	<?php get_math_header($_page_title, 'red-brown') ?>
<?php endif ?>
<?php get_dict_page_title($_page_title) ?>
<script>
    (function ($) {
        jQuery('#signup .article-header').css('background', '#ffffff');
        jQuery('#signup #page-tabs-container').css('background', '#ffffff');
        jQuery('#signup .entry-content').css('background', '#ffffff');
        jQuery('#signup .entry-content').css('color', '#000000');
        jQuery('#page-info-tab').hide();
        jQuery('#span-title-first').addClass('icon-key');
        jQuery('#signup .page-title').css('color', '#000000 !important');
        jQuery('#span-title').html('Create a new account by fill in the blank below.');
        if ((window.matchMedia('screen and (min-width: 1200px)').matches)) {
        jQuery('#main').removeClass('container');
        jQuery('#signup .article-header .row').attr('style', 'width:1050px; margin:auto !important');
        jQuery('#signup .entry-content .row:first').attr('style', 'width:1050px; margin:auto !important');
    }
    })(jQuery);
</script>
	<form method="post" action="<?php echo locale_home_url() ?>/?r=signup" name="registerform">
		<div class="row">
			<div class="col-sm-9">
				<div class="form-group">
					<label for="user_login" class="font-gray-italic"><?php _e('Username (e-mail address)', 'iii-dictionary') ?></label>
					<input id="user_login" class="form-control" name="user_login" type="text" value="">
				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-group">														
					<a href="#" id="check-availability" class="check-availability"><?php _e('Find out availability', 'iii-dictionary') ?>
						<span class="icon-loading"></span>
						<span class="icon-availability" data-toggle="popover" data-placement="bottom" data-container="body" data-trigger="hover" data-html="true" data-max-width="420px" data-content="If username availability is “not available”, someone has already signed up with the email address you entered.<br>If username is “available”, please continue on with the sign up page."></span>
					</a>
				</div>
			</div>

			<div class="col-sm-6">
				<div class="form-group">
					<label for="password" class="font-gray-italic"><?php _e('Create Password', 'iii-dictionary') ?></label>
					<input id="password" class="form-control" name="password" type="password" value="">
				</div>
			</div>
			<div class="col-sm-6">
				<div class="form-group">
					<label for="confirmpassword" class="font-gray-italic"><?php _e('Confirm Password', 'iii-dictionary') ?></label>
					<input id="confirmpassword" class="form-control" name="confirm_password" type="password" value="">
				</div>
			</div>

			<div class="col-sm-6 col-md-6">
				<div class="form-group">
					<label for="lastname" class="font-gray-italic"><?php _e('Last Name', 'iii-dictionary') ?></label>
					<input id="lastname" class="form-control" name="last_name" type="text" value="">
				</div>
			</div>
			<div class="col-sm-6 col-md-6">
				<div class="form-group">
					<label for="firstname" class="font-gray-italic"><?php _e('First Name', 'iii-dictionary') ?></label>
					<input id="firstname" class="form-control" name="first_name" type="text" value="">
				</div>
			</div>
			<div class="col-sm-6 col-md-6">
				<div class="form-group">
					<label class="font-gray-italic"><?php _e('Date of Birth', 'iii-dictionary') ?> <small>(mm/dd/yyyy)</small></label>
					<div class="row tiny-gutter">
						<div class="col-xs-4">
							<select class="select-box-it form-control" name="birth-m">
								<option value="00">Month(mm)</option>
								<?php for($i = 1; $i <= 12; $i++) : ?>
									<?php $pad_str = str_pad($i, 2, '0', STR_PAD_LEFT) ?>
									<option value="<?php echo $pad_str ?>"><?php echo $pad_str ?></option>
								<?php endfor ?>
							</select>
						</div>
						<div class="col-xs-4">
							<select class="select-box-it form-control" name="birth-d">
								<option value="00">Day(dd)</option>
								<?php for($i = 1; $i <= 31; $i++) : ?>
									<?php $pad_str = str_pad($i, 2, '0', STR_PAD_LEFT) ?>
									<option value="<?php echo $pad_str ?>"><?php echo $pad_str ?></option>
								<?php endfor ?>
							</select>
						</div>
						<div class="col-xs-4">
							<input class="form-control" name="birth-y" type="text" value="" placeholder="Year(yyyy)">
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-md-6">
				<div class="form-group">
					<label class="font-gray-italic"><?php _e('Language', 'iii-dictionary') ?></label>
					<?php MWHtml::language_type('en') ?>
				</div>
			</div>
			<div class="col-sm-12">
				<div class="form-group">
					<label>&nbsp;</label>
					<button class="btn-custom" type="submit" name="wp-submit"><?php _e('Create Account', 'iii-dictionary') ?></button>
				</div>
			</div>
		</div>
		<input type="hidden" name="redirect_to" value="<?php echo locale_home_url() ?>/?r=login"/>
		<input type="hidden" name="self-reg" value="1"/>
	</form>

<script>
	(function($){
		$(function(){
			var availability_checking = false;
			$("#check-availability").click(function(e){
				e.preventDefault();
				if(availability_checking){return;}
				var tthis = $(this);
				var user_login = $("#user_login").val().trim();
				if(user_login != "") {
					tthis.popover("destroy");
					availability_checking = true;
					tthis.find(".icon-loading").fadeIn();
					$.getJSON(home_url + "/?r=ajax/availability/user", {user_login: user_login}, function(data){
						if(data[0] == 0) {
							var p_c = '<span class="popover-alert"><?php _e('Not available', 'iii-dictionary') ?></span>';
						}else{
							var p_c = '<span class="popover-alert"><?php _e('Available', 'iii-dictionary') ?></span>';
						}
						tthis.find(".icon-loading").fadeOut();
						tthis.popover({
							placement: "bottom",
							content: p_c,
							trigger: "click hover",
							html: true
						}).popover("show");
						setTimeout(function(){tthis.popover("hide")}, 1500);
						availability_checking = false;
					});
				}
			});
		});
	})(jQuery);
</script>
<?php if(!$is_math_panel) : ?>
	<?php get_dict_footer() ?>
<?php else : ?>
	<?php get_math_footer() ?>
<?php endif ?>