<form id="login" class="ajax-auth" action="login" method="post" style="display: none;    border: 3px solid #000000 !important;">
    <div class="modal-header custom-header">
        <span style="right: 3%;padding-top: 3%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
        <h3 style="padding-left: 0%; color: #ffffff; font-weight: bold;font-family: Myriad_bold;">LOGIN</h3>
    </div>
    <div >
        <div class="form-group" style="padding: 2% 0 0 5% !important; margin-bottom: 0px !important;width: 95%">
            <label for="username" style="font-style: italic;width: 100%; font-size:15px;;    "><?php _e('Username (e-mail address)', 'iii-dictionary') ?></label>
            <input type="text" class="required" style="font-family: Myriad_bold;" id="username" name="log" value="">
        </div>     
        <div class="form-group content-form-custom" >
            <label for="password" style="font-style: italic;width: 100%; font-size:15px;"><?php _e('Password', 'iii-dictionary') ?></label>
            <input type="password" class="required" id="password" name="password" value="">
        </div>     
        <p class="status"></p>  
        <?php wp_nonce_field('ajax-login-nonce', 'security'); ?>
        <div class="clearfix"></div>
        <div class="col-sm-6" style="padding-left: 5%  !important;">
            <div class="form-group">
                <button type="submit" class="btn-submit-login" ><?php _e('Login', 'iii-dictionary') ?></button>
            </div>     
        </div>
        <div class="col-sm-6 btn-signup-mb"> 
            <div class="form-group">
                <label>&nbsp;</label>
                <a href="" class="btn-cancel-signup" id="pop_signup"><?php _e('Sign-up', 'iii-dictionary') ?></a>
            </div>
        </div>
        <div class="col-sm-12" style="    height: 35px;">
            <div class="forgot-pass">
                <!--<a class="text-link" href="<?php echo wp_lostpassword_url(); ?>">Lost password?</a>-->
                <a href="<?php echo locale_home_url() ?>/?r=login&amp;action=forgotpassword" class="lblForgot"><?php _e('Forgot password?', 'iii-dictionary') ?> &gt;</a>
            </div>
        </div>
        <hr />
        <div class="col-sm-12" style="padding :0 0 5% 5% !important">
            <div class="pull-left" style="margin-right: 15px">
                <img alt="" src="<?php echo get_template_directory_uri() ?>/library/images/desktop-icon.png">
            </div>
            <div class="pull-left">
                <p style="color: black"><?php _e('Get to the site faster! Download desktop startup icon', 'iii-dictionary') ?></p>
                <span style="font-weight: bold;color: black !important"><a href="<?php echo $link_url['mac']; ?>"><?php _e('Mac', 'iii-dictionary') ?></a> / <a href="<?php echo $link_url['win']; ?>"><?php _e('Windows', 'iii-dictionary') ?></a></span><span style="padding-left: 5%;font-style: italic"><?php _e('(For the mobile device, search for iklearn.com)', 'iii-dictionary') ?></span>
                </span>
            </div>
        </div>
    </div>
</form>

<form id="register" class="ajax-auth ajax-auth-signup"  action="register" method="post" style="display: none;    border: 3px solid #000000 !important;">
    <div class="modal-header custom-header">
        <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
        <h3 style="padding-left: 0%; color: #ffffff; font-weight: bold;font-family: Myriad_bold;">SIGN-UP</h3>
    </div>
    <div class="form-group" style="padding: 2% 0 0 5% !important; margin-bottom: 0px !important;width: 95%">
        <label for="signonname" style="font-style: italic;width: 100%; font-size:15px;"><?php _e('Username (e-mail address)', 'iii-dictionary') ?></label>
        <input type="text" class="required" id="signonname" name="signonname" value="">
    </div>     
    <div class="form-group content-form">
        <label for="signonpassword" style="font-style: italic;width: 100%; font-size:15px;"><?php _e('Create Password', 'iii-dictionary') ?></label>
        <input type="password" class="required" id="signonpassword" name="signonpassword" value="">
    </div> 
    <div class="form-group content-form">
        <label for="password2" style="font-style: italic;width: 100%; font-size:15px;"><?php _e('Confirm Password', 'iii-dictionary') ?></label>
        <input type="password" class="required" id="password2" name="password2" value="">
    </div> 
    <div class="form-group content-form">
        <label for="last_name" style="font-style: italic;width: 100%; font-size:15px;"><?php _e('Last Name', 'iii-dictionary') ?></label>
        <input type="text" class="required" id="last_name" name="last_name" value="">
    </div> 
    <div class="form-group content-form">
        <label for="first_name" style="font-style: italic;width: 100%; font-size:15px;"><?php _e('First Name', 'iii-dictionary') ?></label>
        <input type="text" class="required" id="first_name" name="first_name" value="">
    </div>
    <div class="form-group content-form">
					<label><?php _e('Date of Birth', 'iii-dictionary') ?> <small>(mm/dd/yyyy)</small></label>
					<div class="row tiny-gutter">
						<div class="col-xs-4">
							<select class="select-box-it form-control" name="birth-m" id="birth-m">
								<option value="00">mm</option>
								<?php for($i = 1; $i <= 12; $i++) : ?>
									<?php $pad_str = str_pad($i, 2, '0', STR_PAD_LEFT) ?>
									<option value="<?php echo $pad_str ?>"><?php echo $pad_str ?></option>
								<?php endfor ?>
							</select>
						</div>
						<div class="col-xs-4">
							<select class="select-box-it form-control" name="birth-d" id="birth-d">
								<option value="00">dd</option>
								<?php for($i = 1; $i <= 31; $i++) : ?>
									<?php $pad_str = str_pad($i, 2, '0', STR_PAD_LEFT) ?>
									<option value="<?php echo $pad_str ?>"><?php echo $pad_str ?></option>
								<?php endfor ?>
							</select>
						</div>
						<div class="col-xs-4">
                                                    <input class="form-control" id="birth-y" name="birth-y" type="text" value="" placeholder="yyyy">
						</div>
					</div>
				</div>
    <div class="form-group content-form" >
					<label><?php _e('Language', 'iii-dictionary') ?></label>
					<?php MWHtml::language_type('en') ?>
				</div>
    <div class="form-group content-form-custom" >
					<input class="btn-submit-sign" type="submit" value="Create Account">
				</div>
    
    <p class="status"></p>
    <?php wp_nonce_field('ajax-register-nonce', 'signonsecurity'); ?> 
<!--    <h3>Already have an account? <a id="pop_login"  href="">Login</a></h3>
    <hr />
    <h1>Signup</h1>
            
    <label for="signonname">Username</label>
    <input id="signonname" type="text" name="signonname" class="required">
    <label for="email">Email</label>
    <input id="email" type="text" class="required email" name="email">
    <label for="signonpassword">Password</label>
    <input id="signonpassword" type="password" class="required" name="signonpassword" >
    <label for="password2">Confirm Password</label>
    <input type="password" id="password2" class="required" name="password2">
    <input class="submit_button" type="submit" value="SIGNUP">
    <a class="close" href="">(close)</a>    -->
</form>