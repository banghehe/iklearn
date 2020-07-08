<?php
	
	$is_math_panel = is_math_panel();

	if(isset($_POST['credit-code']))
	{
		if($cid = MWDB::add_credit_code($_POST))
		{
			wp_redirect(locale_home_url() . '/?r=view-subscription&cid=' . $cid);
			exit;
		}
	}

	$current_user_id = get_current_user_id();
	$current_page = max( 1, get_query_var('page'));
	$filter = get_page_filter_session();
	if(empty($filter))
	{
		$filter['items_per_page'] = 10;
		$filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
	}
	else {
		$filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
	}

	set_page_filter_session($filter);
	$filter['offset'] = 0;
	$filter['items_per_page'] = 99999999;
	$user_subscriptions = MWDB::get_user_subscriptions($current_user_id, $filter);
	$total_pages = ceil($user_subscriptions->total / $filter['items_per_page']);

	$pagination = paginate_links(array(
		'format' => '?page=%#%',
		'current' =>  $current_page,
		'total' => $total_pages
	));

	$inherit_subs = MWDB::get_user_inherit_subscriptions($current_user_id);
	$device_subs = get_device_sub_list();
	$user_subscriptions->items = array_merge($user_subscriptions->items, $inherit_subs, $device_subs);

	$user_groups = MWDB::get_current_user_groups();
	$teacher_tool_price = mw_get_option('teacher-tool-price');
	$self_study_price = mw_get_option('self-study-price');
	$self_study_price_math = mw_get_option('math-self-study-price');
	$dictionary_price = mw_get_option('dictionary-price');
	$cart_items = get_cart_items();
	$purchased_history = MWDB::get_user_purchase_history($current_user_id);
	$_page_title = __('Manage My Subscription', 'iii-dictionary');
	
	//link download of apps with system of user.
	$link_url = ik_link_mw_apps();
?>
<?php if(!$is_math_panel) : ?>
	<?php get_dict_header($_page_title) ?>
<?php else : ?>
	<?php get_math_header($_page_title, 'red-brown') ?>
<?php endif ?>
<?php get_dict_page_title($_page_title, '', '', array(), get_info_tab_cloud_url('Popup_info_10.jpg')) ?>
    <form id="main-form" method="post" action="<?php echo locale_home_url() ?>/?r=payments">

<div class="row">
			<div class="col-xs-12">
                            <?php if($current_user_id!=0){ ?>
				<p class="instructions-text text-right"><?php printf(__('Your current point balance is: %s', 'iii-dictionary'), '<strong>' . ik_get_user_points($current_user_id) . '</strong>') ?></p>
                            <?php } ?>
                        </div>
</div>
<div class="row">
			<div class="col-xs-12">
				<label for="lbl_link-dwn-sc" class="col-xs-12" >
                                                    <p class="omg_link-dwn text-right" id="lbl_link-dwn-sc" style="margin-top: 0px;font-size: 16px;">
								<?php _e('For download version of dictionary,', 'iii-dictionary') ?>
								<a href="#instructions-dialog" data-toggle="modal"><strong><u><?php _e('click here first.', 'iii-dictionary') ?></u></strong></a>
								<?php _e('','iii-dictionary')?>
							</p>
                                                </label>
			</div>
</div>
<p style="font-size: 28px;">Check or Purchase My Subscription</p>
<div class="row">
    <?php if($current_user_id!=0){ ?>
    <p><a role="button" style="color:#fff779" data-toggle="collapse" href="#step1-collapse" class="step-number" title="" ><?php _e('Check The Current Subscription Status', 'iii-dictionary'); ?></a></p>
    <div id="step1-collapse" class="collapse">
        <div class="row " style="padding-left: 5%">
            <div class="row">
			<div class="col-xs-12">
				<div class="box box-sapphire">
					<div class="row">
						<div class="col-sm-12">
							<div class="scroll-list2" style="max-height: 500px">
								<table class="table table-striped table-condensed ik-table1 ik-table-break-all text-center" id="user-subscriptions">
									<thead>
										<tr>
											<th><?php _e('Lesson Name', 'iii-dictionary') ?></th>
											<th class="hidden-xs"><?php _e('Size of class', 'iii-dictionary') ?></th>
											<th class="hidden-xs"><?php _e('No. of License', 'iii-dictionary') ?></th>
											<th class="hidden-xs"><?php _e('Sub. End', 'iii-dictionary') ?></th>
											<th class="hidden-xs"><?php _e('Dictionary', 'iii-dictionary') ?></th>
											<th class="hidden-xs"><?php _e('Group', 'iii-dictionary') ?></th>
											<th></th>
										</tr>
									</thead>
									<tfoot>
										<tr><td colspan="8"><?php echo $pagination ?></td></tr>
									</tfoot>
									<tbody>
										<?php if(empty($user_subscriptions->items)) : ?>
											<tr><td colspan="8"><?php _e('You haven\'t subscribed yet.', 'iii-dictionary') ?></td></tr>
										<?php else : ?>
											<?php foreach($user_subscriptions->items as $code) : 
											if(strtotime($code->expired_on) < strtotime(date('Y-m-d'))) $style = 'style="color:#7F7D7E"';
											else $style = '';
											?>
												<tr>
													<td <?php echo $style; ?>>
                                                                                                                <?php if(is_null($code->type)){ 
                                                                                                                    echo $code->sat_class;
                                                                                                                }else{ ?>
                                                                                                                <?php if(!$code->inherit) : ?>
															<?php echo $code->type ?><?php echo in_array($code->typeid, array(SUB_SAT_PREPARATION, SUB_MATH_SAT_I_PREP, SUB_MATH_SAT_II_PREP)) ? ' - ' . $code->sat_class : '' ?>
														<?php else : ?>
															<?php echo $code->type ?>
                                                                                                                <?php endif;} ?>
													</td>
													<td <?php echo $style; ?> class="hidden-xs"><?php echo in_array($code->typeid, array(SUB_TEACHER_TOOL, SUB_TEACHER_TOOL_MATH, SUB_GROUP)) ? $code->number_of_students : 'N/A' ?></td>
													<td <?php echo $style; ?> class="hidden-xs"><?php echo in_array($code->typeid, array(SUB_DICTIONARY, SUB_SELF_STUDY, SUB_SELF_STUDY_MATH, SUB_GROUP)) ? $code->number_of_students : 'N/A' ?></td>
													<td <?php echo $style; ?> class="hidden-xs"><?php echo ik_date_format($code->expired_on) ?></td>
													<td <?php echo $style; ?> class="hidden-xs"><?php echo $code->dictionary ?></td>
													<td <?php echo $style; ?> class="hidden-xs"><?php echo is_null($code->group_name) ? 'N/A' : $code->group_name ?></td>
													<?php $date1 = new DateTime();
														$date2 = new DateTime($code->expired_on);
														$interval = $date1->diff($date2);
														$months_left = $interval->d > 0 ? $interval->m + 1 : $interval->m;
														$checked_out_state = '';
														foreach($cart_items as $item) {
															if($item->sub_id == $code->id) {
																$checked_out_state = ' disabled';
															}
														}
													?><td data-subid="<?php echo $code->id ?>" data-type="<?php echo $code->typeid ?>" data-did="<?php echo $code->dictionary_id ?>" data-size="<?php echo $code->number_of_students ?>" data-months="<?php echo $months_left ?>"<?php echo !is_null($code->group_name) ? ' data-group="' . $code->group_name . '"' : '' ?> data-sat-class="<?php echo $code->sat_class ?>" data-sat-class-id="<?php echo $code->sat_class_id ?>"  data-gid="<?php echo $code->group_id ?>">
														<?php if(!$code->inherit) : ?>
															<?php if(in_array($code->typeid, array(SUB_TEACHER_TOOL_MATH, SUB_TEACHER_TOOL))) : ?>
																<button type="button" class="btn btn-default btn-block btn-tiny grey extend-sub-btn" data-task="add"<?php echo $checked_out_state ?>><?php _e('Add Members', 'iii-dictionary') ?></button>
															<?php endif ?>
															<?php if(!in_array($code->typeid, array(SUB_GROUP))) : ?>
															<button type="button" class="btn btn-default btn-block btn-tiny grey extend-sub-btn" <?php echo $checked_out_state ?>><?php _e('Renew Subscription', 'iii-dictionary') ?></button>
															<?php endif ?>
															<a href="<?php echo locale_home_url() ?>/?r=view-subscription&amp;cid=<?php echo $code->id ?>" class="btn btn-default btn-block btn-tiny grey"><?php _e('Detail', 'iii-dictionary') ?></a>
														<?php endif ?>
													</td>
												</tr>
											<?php endforeach ?>
										<?php endif ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
        </div>
    </div>
    <?php } ?>
</div>
<div class="row">
    <p><a role="button" style="color:#fff779" data-toggle="collapse" href="#step2-collapse" class="step-number" title="" ><?php _e('Enter Activation Code If You Have Any', 'iii-dictionary'); ?></a></p>
    <div id="step2-collapse" class="collapse">
        <div class="row " style="padding-left: 10%">
            <div class="row">
			<div class="col-xs-12">
				<h2 class="title-border"><?php _e('Do you have an activation code?', 'iii-dictionary') ?></h2>
			</div>
		</div>
            <div class="row">
			<div class="col-xs-12 col-sm-6">
				<div class="form-group">
					<label for="credit-code"><?php _e('Enter a credit code (if you have any)', 'iii-dictionary') ?></label>
					<?php if(!is_math_panel()) : ?>
						<label for="lbl_link-dwn" >
							<p class="omg_link-dwn" id="lbl_link-dwn">
								<?php _e('(For download version of dictionary,', 'iii-dictionary') ?>
								<a href="#instructions-dialog" data-toggle="modal"><strong><u><?php _e('click here first.', 'iii-dictionary') ?></u></strong></a>
								<?php _e(')','iii-dictionary')?>
							</p>
						</label>
					<?php endif ?>
					<input type="text" class="form-control" id="credit-code" name="credit-code" value="">
				</div>
			</div>
			<div class="col-xs-12 col-sm-3">
				<div class="form-group">
					<label>&nbsp;</label>
					<?php if(!is_math_panel()) : ?>
						<label class="omg_fit-lbl">&nbsp;</label>
					<?php endif ?>
					<button type="button" data-loading-text="<?php _e('Checking...', 'iii-dictionary') ?>" data-error-text="<?php _e('Please enter a credit code', 'iii-dictionary') ?>" id="val-credit-code" class="btn btn-default btn-block orange form-control"><span class="icon-check"></span><?php _e('Apply', 'iii-dictionary') ?></button>
				</div>					
			</div>
		</div>
        </div>
    </div>
</div>
<div class="row">
    <p><a role="button" style="color:#fff779" data-toggle="collapse" href="#step3-collapse" class="step-number" title="" ><?php _e('Select The Subscription for Purchasing', 'iii-dictionary'); ?></a></p>
    <div id="step3-collapse" class="collapse">
        <div class="row " style="padding-left: 10%">
            <div class="row">
			<div class="col-xs-12" id="5">													
				<div class="radio radio-style1">
					<input id="sub-self-study" type="radio" name="subscription-type" value="<?php echo !is_math_panel() ? SUB_SELF_STUDY : SUB_SELF_STUDY_MATH ?>" checked>
					<label for="sub-self-study" class="subscription-type">
						<?php !is_math_panel() ? _e('Student Self-study', 'iii-dictionary') : _e('Math Self-study', 'iii-dictionary') ?>
					</label>
				</div>
				<div class="row" id="self-study-detail">
					<div class="col-sm-12">
						<p class="self-study-detail">
							<?php 
								!is_math_panel() 
								?
									_e('You can receive over 200 new worksheets in Spelling, Vocab/Grammar, Reading Comprehension, Writing Practice, and Vocabulary Builder tools with your choice of dictionary.', 'iii-dictionary') 
								:
									_e('You can use all worksheets listed in this site once you subscribe "Self-study" mode. Self-study mode is monthly subscription. You can see the complete list of worksheets by clicking below.', 'iii-dictionary')
							?>
							<br><br>
							<a href="#" class="instructions-text worksheets-preview"><strong><u><?php _e('List of new worksheet you will receive for this subscription', 'iii-dictionary') ?></u></strong></a>
						</p>
						
					</div>
					<div class="col-sm-4">
						<button type="button" class="btn btn-default orange choose-sub-btn"><span class="icon-choose"></span> <?php _e('New subscription', 'iii-dictionary') ?></button>
					</div>
				</div>
			</div>

			<div class="col-xs-12" id="1" style="margin-top: 10px">													
				<div class="radio radio-style1">
					<input id="sub-teacher-tool" type="radio" name="subscription-type" value="<?php echo !is_math_panel() ? SUB_TEACHER_TOOL : SUB_TEACHER_TOOL_MATH ?>">
					<label for="sub-teacher-tool" class="subscription-type"><?php _e('Teacher\'s Homework Tool', 'iii-dictionary') ?></label>
				</div>
				<div class="row" id="teacher-detail" style="display: none">
					<div class="col-sm-7">
						<h3><?php _e('What is the Teachers\'s Tool subscription?', 'iii-dictionary') ?></h3>
						<h4 class="list-header"><?php _e('With this subscription, teachers can:', 'iii-dictionary') ?></h4>
						<ul>
							<li><span class="cbullet">1)</span><span><?php _e('Send homework to a group- students join the group to do the homework assignment. The homework is auto-graded (except for writing assignments).', 'iii-dictionary') ?></span></li>
							<li><span class="cbullet">2)</span><span><?php _e('See every student\'s homework status in Teacher\'s Box.', 'iii-dictionary') ?></span></li>
							<li><span class="cbullet">3)</span><span><?php _e('Get many more homework sheets for all grade levels.', 'iii-dictionary') ?></span></li>
							<li><span class="cbullet"></span><span><a href="#" class="instructions-text worksheets-preview"><u><?php _e('(Click here to see the list)', 'iii-dictionary') ?></u></a></span></li>
							<li><span class="cbullet">4)</span><span><?php _e('Purchase additional homework sheets from other teachers or sell your own homework to other teachers. (in the teacher\'s exchange  section)', 'iii-dictionary') ?></span></li>
						</ul>
					</div>
					<div class="col-sm-5">
						<img alt="" src="<?php echo get_template_directory_uri() ?>/library/images/teacher-subscription-bg.png">
					</div>
					<div class="col-sm-12">
						<div class="separator"></div>
						<ul>
							<li><?php _e('If you are starting a new subscription for a group (class), click "New subscription".', 'iii-dictionary') ?></li>
							<li><?php _e('If you are extending the subscription period for an existing group, click "Renew subscription".', 'iii-dictionary') ?></li>
							<li><?php _e('If you are adding students to the existing group, click "Add members to the subscription".', 'iii-dictionary') ?></li>
							<li><?php _e('You cannot renew or add members at the same time. If you are renewing and adding more members, please do so in two steps.', 'iii-dictionary') ?></li>
						</ul>
					</div>
					<div class="col-sm-4">
						<button type="button" class="btn btn-default orange choose-sub-btn"><span class="icon-choose"></span> <?php _e('New subscription', 'iii-dictionary') ?></button>
					</div>
				</div>
			</div>

			<?php if(!is_math_panel()) : ?>
				<div class="col-xs-12" id="2" style="margin-top: 10px">
					<div class="radio radio-style1">
						<input id="sub-dictionary" type="radio" name="subscription-type" value="<?php echo SUB_DICTIONARY ?>">
						<label for="sub-dictionary" class="subscription-type"><?php _e('Dictionary', 'iii-dictionary') ?></label>
					</div>
					<div class="row" id="dictionary-detail" style="display: none">
						<div class="col-sm-12">
							<ul>
								<li><?php _e('If you purchase a dictionary subscription for multiple users, give each user the activation code that is generated after purchase. Users can activate their subscriptions and view account information under <em>My Account</em> > <em>Manage Subscription</em>.', 'iii-dictionary') ?></li>
								<li><?php _e('For installation on public computers, such as school computer labs, click on the instructions (shown above on this page) after you have made the purchase.', 'iii-dictionary') ?></li>
							</ul>
						</div>
                                            <div class="col-sm-12" style="    margin-left: 40px;">
						<label for="lbl_link-dwn-sc" >
                                                    <p class="omg_link-dwn" id="lbl_link-dwn-sc" style="    font-size: 16px;">
								<?php _e('For download version of dictionary,', 'iii-dictionary') ?>
								<a href="#instructions-dialog" data-toggle="modal"><strong><u><?php _e('click here first.', 'iii-dictionary') ?></u></strong></a>
								<?php _e('','iii-dictionary')?>
							</p>
                                                </label>
                                                </div>
						<div class="col-sm-4">
							<button type="button" class="btn btn-default orange choose-sub-btn"><span class="icon-choose"></span> <?php _e('New subscription', 'iii-dictionary') ?></button>
						</div>
					</div>
				</div>

				<div class="col-xs-12" id="3" style="margin-top: 10px">
					<div class="radio radio-style1">
						<input id="sub-sat" type="radio" name="subscription-type" value="<?php echo SUB_SAT_PREPARATION ?>">
						<label for="sub-sat" class="subscription-type"><?php _e('SAT Preparation', 'iii-dictionary') ?></label>
					</div>
					<div class="row" id="sat-detail" style="display: none">
						<div class="col-sm-12">
							<h3><?php _e('You may join SAT preparation class at this site. Once you join, then you can start your preparation study.', 'iii-dictionary') ?></h3>
						</div>
						<div class="col-sm-12">
							<section class="sat-group">
								<div class="sat-group-icon">
									<img alt="" src="<?php echo get_template_directory_uri() ?>/library/images/icon-grammar.png">
								</div>
								<div class="sat-group-detail">
									<h5 class="sat-group-name"><?php _e('Grammar Review', 'iii-dictionary') ?></h5>
									<div class="sat-group-description">
										<?php _e('This class reviews grammar efficiently from beginning to end which is necessary for building basic skills for getting a high SAT score. You will also substantially build your vocabulary in this class.', 'iii-dictionary') ?>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-4">
										<button type="button" class="btn btn-default orange form-control choose-sub-btn" data-sat-class="<?php _e('Grammar Review', 'iii-dictionary') ?>" data-type="<?php echo CLASS_GRAMMAR ?>"><span class="icon-choose"></span> <?php _e('Start subscription', 'iii-dictionary') ?></button>
									</div>
								</div>
								<div class="separator"></div>
							</section>
							<section class="sat-group">
								<div class="sat-group-icon">
									<img alt="" src="<?php echo get_template_directory_uri() ?>/library/images/icon-writing.png">
								</div>
								<div class="sat-group-detail">
									<h5 class="sat-group-name"><?php _e('Writing Practice', 'iii-dictionary') ?></h5>
									<div class="sat-group-description">
										<?php _e('This class lets you prepare writing section of the SAT test. It covers many aspects of writing practice from "confusing words and phrases" to "writing style and methods". You will learn many aspects of writing knowledge, tips, and practice methods in this section. If you want to get real teacher\'s help, you can request "Real Teacher" in the writing group you are currently practicing. We will ask teachers to respond to your request, but there will be an additional cost for getting a teacher\'s help for each writing homework page. The price is set for each homework assignment, so make a request for a real teacher for each homework assignment you are working on.', 'iii-dictionary') ?>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-4">
										<button type="button" class="btn btn-default orange form-control choose-sub-btn" data-sat-class="<?php _e('Writing Practice', 'iii-dictionary') ?>" data-type="<?php echo CLASS_WRITING ?>"><span class="icon-choose"></span> <?php _e('Start subscription', 'iii-dictionary') ?></button>
									</div>
								</div>
								<div class="separator"></div>
							</section>
							<section class="sat-group">
								<div class="sat-group-icon">
									<img alt="" src="<?php echo get_template_directory_uri() ?>/library/images/icon-sat.png">
								</div>
								<div class="sat-group-detail">
									<h5 class="sat-group-name"><?php _e('SAT practice Test', 'iii-dictionary') ?></h5>
									<div class="sat-group-description">
										<?php _e('You may subscribe to one of five tests available for SAT. Once subscribed, you may take the same test as many times as you want. But, if you want to try another practice test, you need to subscribe for the second practice test. You can subscribe up to five practice tests. You may retake subscribed tests as many times as you want during your valid subscription period.', 'iii-dictionary') ?>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-4">
										<button type="button" class="btn btn-default orange form-control choose-sub-btn" data-sat-class="<?php _e('SAT practice Test', 'iii-dictionary') ?>" data-type="<?php echo CLASS_SAT1 ?>"><span class="icon-choose"></span> <?php _e('Start subscription', 'iii-dictionary') ?></button>
									</div>
								</div>
							</section>
						</div>
					</div>
				</div>
			<?php else : // Math side ?>
                                <div class="col-xs-12" id="" style="margin-top: 10px">
					<div class="radio radio-style1">
						<input id="sub-ik-class" type="radio" name="subscription-type" value="<?php echo SUB_MATH_CLASS_IK ?>">
						<label for="sub-ik-class" class="subscription-type"><?php _e('IK Math classes', 'iii-dictionary') ?></label>
					</div>
					<div class="row" id="ik-class-detail" style="display: none">
						<div class="col-sm-12">
							<section class="sat-group">
								<div class="sat-group-icon">
									<img alt="" src="<?php echo get_template_directory_uri() ?>/library/images/icon-grammar.png">
								</div>
								<div class="sat-group-detail">
									<h5 class="sat-group-name"><?php _e('IKMath kindergarten', 'iii-dictionary') ?></h5>
									<div class="sat-group-description">
										<?php _e('Please select the grade you subscribe and pay for one month subscription below.', 'iii-dictionary') ?>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-4">
										<button type="button" class="btn btn-default orange form-control choose-sub-btn" data-sat-class="<?php _e('IK Math classes', 'iii-dictionary') ?>" data-type="<?php echo CLASS_MATH_IK ?>"><span class="icon-choose"></span> <?php _e('Start subscription', 'iii-dictionary') ?></button>
									</div>
								</div>
								<div class="separator"></div>
							</section>
						</div>
					</div>
				</div>
				<div class="col-xs-12" id="7" style="margin-top: 10px">
					<div class="radio radio-style1">
						<input id="sub-i-sat" type="radio" name="subscription-type" value="<?php echo SUB_MATH_SAT_I_PREP ?>">
						<label for="sub-i-sat" class="subscription-type"><?php _e('SAT I Preparation', 'iii-dictionary') ?></label>
					</div>
					<div class="row" id="sat-i-detail" style="display: none">
						<div class="col-sm-12">
							<section class="sat-group">
								<div class="sat-group-icon">
									<img alt="" src="<?php echo get_template_directory_uri() ?>/library/images/icon-grammar.png">
								</div>
								<div class="sat-group-detail">
									<h5 class="sat-group-name"><?php _e('SAT I Preparation', 'iii-dictionary') ?></h5>
									<div class="sat-group-description">
										<?php _e('This practice is essential to prepare for SATI preparation. You will quickly review entire Algebra in this class.', 'iii-dictionary') ?>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-4">
										<button type="button" class="btn btn-default orange form-control choose-sub-btn" data-sat-class="<?php _e('SAT I Preparation', 'iii-dictionary') ?>" data-type="<?php echo CLASS_MATH_SAT1PREP ?>"><span class="icon-choose"></span> <?php _e('Start subscription', 'iii-dictionary') ?></button>
									</div>
								</div>
								<div class="separator"></div>
							</section>
							<section class="sat-group">
								<div class="sat-group-icon">
									<img alt="" src="<?php echo get_template_directory_uri() ?>/library/images/icon-sat.png">
								</div>
								<div class="sat-group-detail">
									<h5 class="sat-group-name"><?php _e('SAT I simulated test  (New SAT test)', 'iii-dictionary') ?></h5>
									<div class="sat-group-description">
										<?php _e('There are five simulated tests available for the subscription. Once subscribed, you may take the test immediately.', 'iii-dictionary') ?>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-4">
										<button type="button" class="btn btn-default orange form-control choose-sub-btn" data-sat-class="<?php _e('SAT I simulated test', 'iii-dictionary') ?>" data-type="<?php echo CLASS_MATH_SAT1A ?>"><span class="icon-choose"></span> <?php _e('Start subscription', 'iii-dictionary') ?></button>
									</div>
								</div>
								<div class="separator"></div>
							</section>
						</div>
					</div>
				</div>

				<div class="col-xs-12" id="8" style="margin-top: 10px">
					<div class="radio radio-style1">
						<input id="sub-ii-sat" type="radio" name="subscription-type" value="<?php echo SUB_MATH_SAT_II_PREP ?>">
						<label for="sub-ii-sat" class="subscription-type"><?php _e('SAT II Preparation', 'iii-dictionary') ?></label>
					</div>
					<div class="row" id="sat-ii-detail" style="display: none">
						<div class="col-sm-12">
							<section class="sat-group">
								<div class="sat-group-icon">
									<img alt="" src="<?php echo get_template_directory_uri() ?>/library/images/icon-grammar.png">
								</div>
								<div class="sat-group-detail">
									<h5 class="sat-group-name"><?php _e('SAT II Preparation', 'iii-dictionary') ?></h5>
									<div class="sat-group-description">
										<?php _e('This practice is essential to prepare for SATII preparation. You will quickly review entire Algebra in this class.', 'iii-dictionary') ?>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-4">
										<button type="button" class="btn btn-default orange form-control choose-sub-btn" data-sat-class="<?php _e('SAT II Preparation', 'iii-dictionary') ?>" data-type="<?php echo CLASS_MATH_SAT2PREP ?>"><span class="icon-choose"></span> <?php _e('Start subscription', 'iii-dictionary') ?></button>
									</div>
								</div>
								<div class="separator"></div>
							</section>
							<section class="sat-group">
								<div class="sat-group-icon">
									<img alt="" src="<?php echo get_template_directory_uri() ?>/library/images/icon-sat.png">
								</div>
								<div class="sat-group-detail">
									<h5 class="sat-group-name"><?php _e('SAT II simlated test', 'iii-dictionary') ?></h5>
									<div class="sat-group-description">
										<?php _e('There are five simulated tests available for the subscription. Once subscribed, you may take the test immediately.', 'iii-dictionary') ?>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-4">
										<button type="button" class="btn btn-default orange form-control choose-sub-btn" data-sat-class="<?php _e('SAT II simlated test', 'iii-dictionary') ?>" data-type="<?php echo CLASS_MATH_SAT2A ?>"><span class="icon-choose"></span> <?php _e('Start subscription', 'iii-dictionary') ?></button>
									</div>
								</div>
								<div class="separator"></div>
							</section>
						</div>
					</div>
				</div>
                                
			<?php endif ?>

			<div class="col-xs-12" id="4" style="margin-top: 10px">
				<div class="radio radio-style1">
					<input id="sub-purchase-points" type="radio" name="subscription-type" value="<?php echo SUB_POINTS_PURCHASE ?>">
					<label for="sub-purchase-points" class="subscription-type"><?php _e('Purchase points?', 'iii-dictionary') ?></label>
				</div>
				<div class="row" id="purchase-points-detail" style="display: none">
					<div class="col-sm-12">
						<ul>
							<li><?php _e('Points are required to purchase worksheet that you can use for the homework assignment.', 'iii-dictionary') ?></li>
							<li><?php _e('One point is equivalent to one dollar. You can earn points by selling your worksheet to other teachers.', 'iii-dictionary') ?></li>
							<li><?php _e('You can earn points by editing and grading writing assignment students submitted.', 'iii-dictionary') ?></li>
						</ul>
					</div>
					<div class="col-sm-4">
						<button type="button" class="btn btn-default btn-block orange form-control choose-sub-btn"><span class="icon-choose"></span> <?php _e('Purchase', 'iii-dictionary') ?></button>
					</div>
				</div>
			</div>
		</div>
        </div>
    </div>
</div>
<div class="row">
    <?php if($current_user_id!=0){ ?>
    <p><a role="button" style="color:#fff779" data-toggle="collapse" href="#step4-collapse" class="step-number" title="" ><?php _e('Subscription Purchase History', 'iii-dictionary'); ?></a></p>
    <div id="step4-collapse" class="collapse">
        <div class="row " style="padding-left: 10%">
            <div class="col-xs-12">
				<div class="box">
					<div class="row">
						<div class="col-sm-12">
							<div class="scroll-list2" style="max-height: 450px">
								<table class="table table-striped table-condensed ik-table1 text-center">
									<thead>
										<tr>
											<th><?php _e('Lesson Name', 'iii-dictionary') ?></th>
											<th><?php _e('Activation Code', 'iii-dictionary') ?></th>
											<th><?php _e('Payment Method', 'iii-dictionary') ?></th>
											<th><?php _e('Paid Amount', 'iii-dictionary') ?></th>
											<th><?php _e('Purchased On', 'iii-dictionary') ?></th>
										</tr>
									</thead>
									<tbody><?php
										if(empty($purchased_history)) : ?>
											<tr><td colspan="5"><?php _e('No history', 'iii-dictionary') ?></td></tr>
									<?php else : 
											foreach($purchased_history as $item) : ?>
												<tr>
													<td><?php echo $item->purchased_item_name ?></td>
													<td><?php echo !empty($item->encoded_code) ? $item->encoded_code : 'NULL'; ?></td>
													<td><?php echo $item->payment_method ?></td>
													<td>$ <?php echo $item->amount ?></td>
													<td><?php echo ik_date_format($item->purchased_on, 'm/d/Y H:m:i') ?></td>
												</tr>
										<?php endforeach;
										endif ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
        </div>
    </div>
    <?php } ?>
</div>
<div class="row">
    <p><a role="button" style="color:#fff779" data-toggle="collapse" href="#step5-collapse" class="step-number" title="" ><?php _e('Download Version of Dictionary', 'iii-dictionary'); ?></a></p>
    <div id="step5-collapse" class="collapse">
        <div class="row " style="padding-left: 10%">
            <div class="col-xs-6">
						<label for="lbl_link-dwn-sc" >
							<p class="omg_link-dwn" id="lbl_link-dwn-sc">
								<?php _e('For download version of dictionary,', 'iii-dictionary') ?>
								<a href="#instructions-dialog" data-toggle="modal"><strong><u><?php _e('click here first.', 'iii-dictionary') ?></u></strong></a>
								<?php _e('','iii-dictionary')?>
							</p>
						</label>
					</div>
        </div>
    </div>
</div>



		<input type="hidden" name="dictionary-id" id="dictionary-id" value="">
		<input type="hidden" name="starting-date" id="starting-date-txt" value="">
		<input type="hidden" name="assoc-group" id="assoc-group" value="">
		<input type="hidden" name="group-name" id="group-name" value="">
		<input type="hidden" name="group-pass" id="group-pass" value="">
		<input type="hidden" id="activation-code" name="activation-code" value="">
	</form>

<div id="additional-subscription-dialog" class="modal fade modal-red-brown">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
             <h3 id="addi-popup-title" data-ts-text="<?php _e('Teacher\'s Homework Tool Subscription', 'iii-dictionary') ?>" data-ds-text="<?php _e('Dictionary Subscription', 'iii-dictionary') ?>" data-ext-text="<?php _e('Purchase Additional Subscriptions', 'iii-dictionary') ?>"><?php _e('Purchase Additional Subscriptions', 'iii-dictionary') ?></h3>
        </div>
		<form method="post" action="<?php echo home_url_ssl() ?>/?r=payments">
			<input type="hidden" id="addi-sub-type" name="sub-type" value="">
			<input type="hidden" id="addi-gid" name="assoc-group" value="">
			<input type="hidden" id="addi-gname" name="group-name" value="">
			<input type="hidden" id="addi-gpass" name="group-pass" value="">
			<input type="hidden" id="sub-id" name="sub-id" value="0">
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-6" id="selected-group-label">
						<div class="form-group">
							<label><?php _e('Selected Group', 'iii-dictionary') ?></label>
							<p class="box" id="addi-selected-group" style="padding: 5px 15px"></p>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group<?php echo $is_math_panel ? ' hidden' : '' ?>">
							<label><?php _e('Select the type of dictionary', 'iii-dictionary') ?></label>
							<?php MWHtml::select_dictionaries('', false, 'dictionary', 'sel-dictionary', 'form-control', true) ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label id="num-of-student-lbl"><?php _e('Number of Students', 'iii-dictionary') ?></label>
							<?php $min_no_of_student = mw_get_option('min-students-subscription') ?>
							<input type="number" name="no-students" id="student_num" class="form-control" data-min="<?php echo $min_no_of_student ?>" value="<?php echo $min_no_of_student ?>">
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label id="num-of-months-lbl"><?php _e('Number of Months', 'iii-dictionary') ?></label>
							<select class="select-box-it form-control" name="teacher-tool-months" id="sel-teacher-tool">
								<?php for($i = 3; $i <= 24; $i++) : ?>
									<option value="<?php echo $i ?>"><?php printf(__('%s months', 'iii-dictionary'), $i) ?></option>
								<?php endfor ?>
							</select>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<div class="box" style="text-align: right">
							<?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency">$</span> <span id="total-amount">0</span>
						</div>
					</div>
				</div>				
			</div>
			<div class="modal-footer">
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<button type="submit" id="add-to-cart" name="add-to-cart" class="btn btn-block orange confirm"><span class="icon-cart"></span><?php _e('Check out', 'iii-dictionary') ?></button>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<a href="#" data-dismiss="modal" aria-hidden="true" class="btn btn-block grey secondary"><span class="icon-cancel"></span><?php _e('Cancel', 'iii-dictionary') ?></a>
						</div>
					</div>
				</div>
			</div>			
		</form>
      </div>
    </div>
</div>

<div id="self-study-subscription-dialog" class="modal fade modal-red-brown">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
             <h3><?php _e('Student\'s Self-study Subscription', 'iii-dictionary') ?></h3>
        </div>
		<form method="post" action="<?php echo home_url_ssl() ?>/?r=payments">
			<input type="hidden" name="sub-type" value="<?php echo !$is_math_panel ? SUB_SELF_STUDY : SUB_SELF_STUDY_MATH ?>" id="self-study-sub">
			<?php $self_study_group = generate_self_study_group_name() ?>
			<input type="hidden" name="group-name" value="<?php echo $self_study_group ?>">
			<input type="hidden" name="group-pass" value="<?php echo $self_study_group ?>">
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-6 form-group">
						<label><?php _e('Default Group for this subscription', 'iii-dictionary') ?></label>
						<p class="box" style="padding: 5px 15px"><?php echo $self_study_group ?></p>
					</div>
					<div class="col-sm-6 form-group" id="ss-dict-block">
						<label><?php _e('Select the type of dictionary', 'iii-dictionary') ?></label>
						<?php MWHtml::select_dictionaries('', false, 'dictionary', 'sel-dictionary2', 'form-control', true) ?>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label><?php _e('Number of Students', 'iii-dictionary') ?></label>
							<input type="number" name="no-students" class="form-control" min="1" max="1" value="1" readonly>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label><?php _e('Number of Months', 'iii-dictionary') ?></label>
							<select class="select-box-it form-control" name="self-study-months" id="sel-self-study-months">
								<?php for($i = 1; $i <= 24; $i++) : ?>
									<option value="<?php echo $i ?>"><?php printf(_n('%s month', '%s months', $i, 'iii-dictionary'), $i) ?></option>
								<?php endfor ?>
							</select>
						</div>
					</div>
					<div class="col-sm-12">
						<div class="box" style="text-align: right">
							<?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency">$</span> <span class="currency" id="ss-total-amount">0</span>
						</div>
					</div>
				</div>				
			</div>
			<div class="modal-footer">
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<button type="submit" id="add-to-cart-ss" name="add-to-cart" class="btn btn-block orange"><span class="icon-cart"></span><?php _e('Check out', 'iii-dictionary') ?></button>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<a href="#" data-dismiss="modal" aria-hidden="true" class="btn btn-block grey"><span class="icon-cancel"></span><?php _e('Cancel', 'iii-dictionary') ?></a>
						</div>
					</div>
				</div>
			</div>			
		</form>
      </div>
    </div>
</div>

<div id="sat-subscription-dialog" class="modal fade modal-red-brown">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
             <h3><?php _e('Purchase SAT Subscriptions', 'iii-dictionary') ?></h3>
        </div>
		<form method="post" action="<?php echo home_url_ssl() ?>/?r=payments">
			<input type="hidden" name="sub-type" id="sat-sub-type" value="0">
			<input type="hidden" name="sat-class" id="sat-class" value="">
			<input type="hidden" name="sub-id" value="0">
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<label><?php _e('Selected Class', 'iii-dictionary') ?></label>
							<p class="box" id="selected-class" style="padding: 5px 15px"></p>
						</div>
					</div>
					<div class="clearfix"></div>
					<div class="col-sm-6">
						<div class="form-group">
							<label><?php _e('Number of Months', 'iii-dictionary') ?></label>
							<select class="select-box-it form-control" name="sat-months" id="sel-sat-months">
								<?php for($i = 1; $i <= 24; $i++) : ?>
									<option value="<?php echo $i ?>"><?php printf(_n('%s month', '%s months', $i, 'iii-dictionary'), $i) ?></option>
								<?php endfor ?>
							</select>
						</div>
					</div>
					<?php if(!is_math_panel()) : 
						$select_class_options = array(CLASS_SAT1 => __('SAT Test 1', 'iii-dictionary'), CLASS_SAT2 => __('SAT Test 2', 'iii-dictionary'), CLASS_SAT3 => __('SAT Test 3', 'iii-dictionary'),
							CLASS_SAT4 => __('SAT Test 4', 'iii-dictionary'), CLASS_SAT5 => __('SAT Test 5', 'iii-dictionary')) ?>

						<div class="col-sm-6" id="sat-test-block" style="display: none">
							<div class="form-group">
								<label><?php _e('Practice Test', 'iii-dictionary') ?></label>
								<select class="select-box-it form-control sel-sat-class">
									<?php foreach($select_class_options as $key => $value) : ?>
										<option value="<?php echo $key ?>"><?php echo $value ?></option>
									<?php endforeach ?>
								</select>
							</div>
						</div>
					<?php else :
						$select1_class_options = array(CLASS_MATH_SAT1A => __('SAT 1A', 'iii-dictionary'), CLASS_MATH_SAT1B => __('SAT 1B', 'iii-dictionary'),
							CLASS_MATH_SAT1C => __('SAT 1C', 'iii-dictionary'), CLASS_MATH_SAT1D => __('SAT 1D', 'iii-dictionary'), CLASS_MATH_SAT1E => __('SAT 1E', 'iii-dictionary'));
						$select2_class_options = array(CLASS_MATH_SAT2A => __('SAT 2A', 'iii-dictionary'), CLASS_MATH_SAT2B => __('SAT 2B', 'iii-dictionary'),
							CLASS_MATH_SAT2C => __('SAT 2C', 'iii-dictionary'), CLASS_MATH_SAT2D => __('SAT 2D', 'iii-dictionary'), CLASS_MATH_SAT2E => __('SAT 2E', 'iii-dictionary')) ;
						$select3_class_options = array( CLASS_MATH_IK => __('Math Kindergarten', 'iii-dictionary'), 
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
                                                                                CLASS_MATH_IK12 => __('Math Grade 12', 'iii-dictionary')) ?>

						<div class="col-sm-6" id="sat-test-i-block" style="display: none">
							<div class="form-group">
								<label><?php _e('Simulated Test', 'iii-dictionary') ?></label>
								<select class="select-box-it form-control sel-sat-class">
									<?php foreach($select1_class_options as $key => $value) : ?>
										<option value="<?php echo $key ?>"><?php echo $value ?></option>
									<?php endforeach ?>
								</select>
							</div>
						</div>
						<div class="col-sm-6" id="sat-test-ii-block" style="display: none">
							<div class="form-group">
								<label><?php _e('Simulated Test', 'iii-dictionary') ?></label>
								<select class="select-box-it form-control sel-sat-class">
									<?php foreach($select2_class_options as $key => $value) : ?>
										<option value="<?php echo $key ?>"><?php echo $value ?></option>
									<?php endforeach ?>
								</select>
							</div>
						</div>
						<div class="col-sm-6" id="ik-test-class-block" style="display: none">
							<div class="form-group">
								<label><?php _e('Simulated Test', 'iii-dictionary') ?></label>
								<select class="select-box-it form-control sel-sat-class" id="sel-sat-class">
									<?php foreach($select3_class_options as $key => $value) : ?>
										<option value="<?php echo $key ?>"><?php echo $value ?></option>
									<?php endforeach ?>
								</select>
							</div>
						</div>
					<?php endif ?>

					<div class="col-sm-12">
						<div class="box" style="text-align: right">
							<?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency">$</span> <span id="total-amount-sat">0</span>
						</div>
					</div>
				</div>				
			</div>
			<div class="modal-footer">
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<button type="submit" name="add-to-cart" class="btn btn-block orange confirm"><span class="icon-cart"></span><?php _e('Check out', 'iii-dictionary') ?></button>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<a href="#" data-dismiss="modal" aria-hidden="true" class="btn btn-block grey secondary"><span class="icon-cancel"></span><?php _e('Cancel', 'iii-dictionary') ?></a>
						</div>
					</div>
				</div>
			</div>			
		</form>
      </div>
    </div>
</div>

<div id="purchase-points-dialog" class="modal fade modal-red-brown">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
             <h3><?php _e('Purchase Points', 'iii-dictionary') ?></h3>
        </div>
		<form method="post" action="<?php echo home_url_ssl() ?>/?r=payments">
			<input type="hidden" name="sub-type" value="4">
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label><?php _e('Number of Points', 'iii-dictionary') ?></label>
							<input type="number" class="form-control" name="no-of-points" id="no-of-points" min="1">
						</div>
					</div>
					<div class="col-sm-12">
						<div class="box" style="text-align: right">
							<?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency">$ <span id="total-amount-points">0</span></span>
						</div>
					</div>
				</div>				
			</div>
			<div class="modal-footer">
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<button type="submit" name="add-to-cart" class="btn btn-block orange confirm"><span class="icon-cart"></span><?php _e('Check out', 'iii-dictionary') ?></button>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<a href="#" data-dismiss="modal" aria-hidden="true" class="btn btn-block grey secondary"><span class="icon-cancel"></span><?php _e('Cancel', 'iii-dictionary') ?></a>
						</div>
					</div>
				</div>
			</div>			
		</form>
      </div>
    </div>
</div>

<div id="teacher-sub-details-dialog" class="modal fade modal-red-brown">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
			<h3><?php _e('Teacher\'s Homework Tool Subscription', 'iii-dictionary') ?></h3>
        </div>
        <div class="modal-body">
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<label><?php _e('Select existing group (Class Name)', 'iii-dictionary') ?></label>
						<select class="select-box-it" id="sel-group-teacher">
							<option value=""><?php _e('Select one', 'iii-dictionary') ?></option>
							<?php foreach($user_groups as $group) : if(is_null($group->expired_date)) : ?>
								<option value="<?php echo $group->id ?>" data-size="<?php echo $group->size ?>"><?php echo $group->name ?></option>
							<?php endif; endforeach ?>
						</select>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="col-sm-6">
					<div class="form-group">
						<label><?php _e('Or Create New Group', 'iii-dictionary') ?></label>
						<input type="text" class="form-control" id="teacher-gname" placeholder="<?php _e('Group name', 'iii-dictionary') ?>">
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<label>&nbsp;</label>
						<input type="text" class="form-control" id="teacher-gpass" placeholder="<?php _e('Group password', 'iii-dictionary') ?>">
					</div>
				</div>
				<div class="col-sm-12">
					<ol>
						<li><?php _e('One dictionary for all members in your group is included in the subscription.', 'iii-dictionary') ?></li>
						<li><?php printf(__('The price for Teacher\'s Homework Tool is %s per student per month.', 'iii-dictionary'), '<strong>' . $teacher_tool_price / 100 . '</strong>') ?></li>
					</ol>
				</div>
                                <div>
                                        <li><?php _e('Do you pay for this group license fee, or you collect group fee from your students?', 'iii-dictionary')?></li>
                                </div>
                                <div>
                                    <li style="float:left"><?php _e('Pay by myself', 'iii-dictionary')?></li>
                                    <input style="margin-left: 100px;" id="paymyseft" class="checkboxagree" type="checkbox" name="paymyseft" value="paymyseft" >
                                </div>
                                <div>
                                    <li style="float:left"><?php _e('collect from students', 'iii-dictionary')?></li>
                                    <input style="margin-left: 45px;" id="paystudent" class="checkboxagree" type="checkbox" name="paystudent" value="paystudent" >
                                </div>
                                <div style="text-align: center">
                                    <li><?php _e('How much from each student?', 'iii-dictionary')?></li>
                                    <input id="payeachstudent" type="text" name="payeachstudent" value="" >
                                </div>
			</div>
        </div>
        <div class="modal-footer">
			<div class="row">
				<div class="col-sm-4 pull-right">
					<button type="button" id="sub-continue" class="btn btn-default btn-block orange" data-loading-text="<?php _e('Checking...', 'iii-dictionary') ?>"><span class="icon-check"></span><?php _e('Continue', 'iii-dictionary') ?></button>
				</div>
			</div>
        </div>
      </div>
    </div>
</div>

<div id="add-code-dialog" class="modal fade modal-red-brown">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
             <h3><?php _e('Use a credit code to add subscription', 'iii-dictionary') ?></h3>
        </div>
        <div class="modal-body">
			<div class="row" id="teacher-tool-block">
				<div class="col-sm-6">
					<div class="form-group">
						<label><?php _e('Select existing group (Class Name)', 'iii-dictionary') ?></label>
						<select class="select-box-it" id="pop-sel-group-teacher">
							<option value=""><?php _e('Select one', 'iii-dictionary') ?></option>
							<?php foreach($user_groups as $group) : if(is_null($group->expired_date)) : ?>
								<option value="<?php echo $group->id ?>" data-size="<?php echo $group->size ?>"><?php echo $group->name ?></option>
							<?php endif; endforeach ?>
						</select>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="col-sm-6">
					<div class="form-group">
						<label><?php _e('Or Create New Group', 'iii-dictionary') ?></label>
						<input type="text" class="form-control" id="pop-gname-teacher" placeholder="<?php _e('Group name', 'iii-dictionary') ?>" title="<?php _e('Group name', 'iii-dictionary') ?>">
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<label>&nbsp;</label>
						<input type="text" class="form-control" id="pop-gpass-teacher" placeholder="<?php _e('Group password', 'iii-dictionary') ?>" title="<?php _e('Group password', 'iii-dictionary') ?>">
					</div>
				</div>
			</div>

            <div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<label for="pop-starting-date"><?php _e('Starting When', 'iii-dictionary') ?></label>
						<input type="text" id="pop-starting-date" class="form-control" value="">
					</div>
				</div>
				<div class="col-sm-6" id="choose-dictionary-block" style="display: none">
					<div class="form-group">
						<label><?php _e('Select the type of dictionary', 'iii-dictionary') ?></label>
						<?php MWHtml::select_dictionaries('', false, 'dictionary', 'choose-dictionary', 'form-control') ?>
					</div>
				</div>
			</div>
			<input type="hidden" id="ltype">
        </div>
        <div class="modal-footer">
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<button type="button" id="submit-code" class="btn btn-block orange confirm"><span class="icon-check"></span><?php _e('Add Subscription', 'iii-dictionary') ?></button>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<a href="#" data-dismiss="modal" aria-hidden="true" class="btn btn-block grey"><span class="icon-cancel"></span><?php _e('Cancel', 'iii-dictionary') ?></a>
					</div>
				</div>
			</div>
        </div>
      </div>
    </div>
</div>

<div id="credit-error-dialog" class="modal fade modal-red-brown">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
             <h3><?php _e('Credit Code Error', 'iii-dictionary') ?></h3>
        </div>
        <div class="modal-body">
			<h5 class="title-border error">&nbsp;</h5>
			<p class="error-msg"></p>
        </div>
        <div class="modal-footer">
			<a href="#" data-dismiss="modal" aria-hidden="true" class="btn btn-block orange"><span class="icon-check"></span><?php _e('OK', 'iii-dictionary') ?></a>
        </div>
      </div>
    </div>
</div>

<div id="instructions-dialog" class="modal fade modal-red-brown modal-large">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
             <h3><?php _e('To use dictionary without internet connection', 'iii-dictionary') ?></h3>
        </div>
        <div class="modal-body">
            <div class="row">
				<div class="col-xs-2 text-center">
					<img alt="" src="<?php echo get_template_directory_uri() ?>/library/images/desktop-shortcut.png">
				</div>
				<div class="col-xs-10">
					<h4><?php _e('DICTIONARY - DOWNLOAD VERSION', 'iii-dictionary') ?></h4>
					<!-- sửa lại nội dung của popup
					<p><?php //_e('To use the dictionary subscription on a public computer, download the "starter program".', 'iii-dictionary') ?></p>
					<p><?php //_e('A desktop icon will be installed, and the user needs to use that icon to use the dictionary. The dictionary subscription license will only be recognized when the desktop icon is used to start the dictionary program. This feature is currently available only on Windows and Mac.', 'iii-dictionary') ?></p>
					-->
					<p><?php _e('Download the dictionary to use offline. Once installed, you need to', 'iii-dictionary') ?></p>
					<p><?php _e('(1) enter the activation code and', 'iii-dictionary') ?></p>
					<p><?php _e('(2) your email address registered in iklearn.com.', 'iii-dictionary') ?></p>
					<p><?php _e('The dictionary program needs to periodically check the license status,', 'iii-dictionary') ?></p>
					<p><?php _e('so you need to be connected online occasionally', 'iii-dictionary') ?></p>
				</div>
			</div>
        </div>
        <div class="modal-footer">
			<div class="separator"></div>
			<div class="row">
				<div class="col-md-6 col-xs-offset-2">
					<div class="form-group">
						<span class="downloads-block"><span class="icon-download"></span> <?php _e('DOWNLOAD:', 'iii-dictionary') ?>
						</span>
					</div>
				</div>
				<!-- loại bỏ các bước hướng dẫn
				<div class="col-md-10 col-xs-offset-2">
					<div class="radio radio-style1">
						<input id="app-s1" type="radio" name="app-instructions" value="1">
						<label for="app-s1"><?php //_e('Installation Option', 'iii-dictionary') ?></label>
					</div>
					<div id="app-s1-in" class="app-ins">
						<p><?php //_e('After downloading the program, you "open" the program to start installing it. During installation, please choose the option:', 'iii-dictionary') ?></p>
						<img alt="" src="<?php //echo get_template_directory_uri() ?>/library/images/app-ins-step1.jpg">
					</div>
					<div class="radio radio-style1">
						<input id="app-s2" type="radio" name="app-instructions" value="2">
						<label for="app-s2"><?php //_e('Setting Admin Mode for running the program', 'iii-dictionary') ?></label>
					</div>
					<div id="app-s2-in" class="app-ins">
						<p><?php //_e('Right click on Desktop Icon and change the property (Win only)', 'iii-dictionary') ?></p>
						<img alt="" src="<?php //echo get_template_directory_uri() ?>/library/images/app-ins-step2a.jpg">
						<img alt="" src="<?php //echo get_template_directory_uri() ?>/library/images/app-ins-step2b.jpg">
					</div>
					<div class="radio radio-style1">
						<input id="app-s3" type="radio" name="app-instructions" value="3">
						<label for="app-s3"><?php// _e('Entering Activation Code for the successful activation', 'iii-dictionary') ?></label>
					</div>
					<div id="app-s3-in" class="app-ins">
						<p><?php //_e('Enter an activation code, and confirm it is activated.', 'iii-dictionary') ?></p>
						<img alt="" src="<?php //echo get_template_directory_uri() ?>/library/images/app-ins-step3a.jpg">
						<img alt="" src="<?php //echo get_template_directory_uri() ?>/library/images/app-ins-step3b.jpg">
					</div>
				</div>
				-->
                                <div class="col-md-12">
                                    <div class="col-md-2"></div>
                                    <div class="col-md-8"><div class="col-md-6">
                                            <a class="btn btn-block orange" href="<?php echo $link_url['mac']; ?>"><?php _e('Mac', 'iii-dictionary') ?></a>
                                    </div>
                                    <div class="col-md-6">
                                            <a class="btn btn-block orange" href="<?php echo $link_url['win']; ?>"><?php _e('Windows', 'iii-dictionary') ?></a>
                                    </div></div>
                                    <div class="col-md-2"></div>
                                    
                                </div>
			</div>
        </div>
      </div>
    </div>
</div>

<div id="subscribed-worksheets-dialog" class="modal fade modal-red-brown">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
             <h3><?php _e('Subscribed Worksheets', 'iii-dictionary') ?> : <span id="omg_sub-total">0</span></h3>
			 
        </div>
        <div class="modal-body">
			<div class="row">
				<div class="col-sm-12">
					<div class="scroll-list2 scrollbar-white" style="max-height: 450px">
						<table class="table table-striped table-condensed ik-table1 text-center">
							<thead>
								<tr>
									<th><?php _e('Assignment', 'iii-dictionary') ?></th>
									<th><?php _e('Grade', 'iii-dictionary') ?></th>
									<th><?php _e('Worksheet Name', 'iii-dictionary') ?></th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>
        </div>
      </div>
    </div>
</div>

<script>
var ttp = <?php echo $teacher_tool_price ?>;
var ssp = <?php echo $self_study_price ?>;
var ssp_math = <?php echo $self_study_price_math ?>;
var dp = <?php echo $dictionary_price ?>;
var adp = <?php echo mw_get_option('all-dictionary-price') ?>;
var student_multiplier = <?php echo STUDENT_MULTIPLIER ?>;
var min_student = <?php echo mw_get_option('min-students-subscription') ?>;
var satGp = <?php echo mw_get_option('sat-grammar-price') ?>;
var satWp = <?php echo mw_get_option('sat-writing-price') ?>;
var satStp = <?php echo mw_get_option('sat-test-price') ?>;
var satMIP = <?php echo mw_get_option('math-sat1-price') ?>;
var satMIIP = <?php echo mw_get_option('math-sat2-price') ?>;
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
var ptsr = <?php echo mw_get_option('point-exchange-rate') ?>;
var M_SINGLE = "<?php _e('month', 'iii-dictionary') ?>";
var M_PLURAL = "<?php _e('months', 'iii-dictionary') ?>";
var DICT_EMPTY_ERR = "<?php _e('Please select a Dictionary', 'iii-dictionary') ?>";
var GRP_EMPTY_ERR = "<?php _e('Please select a group', 'iii-dictionary') ?>";
var GRP_EXIST_ERR = "<?php _e('This group name is already taken. Please choose a different name.', 'iii-dictionary') ?>";
var GRP_PW_ERR = "<?php _e('Group password cannot empty', 'iii-dictionary') ?>";
var M_EMPTY_ERR = "<?php _e('Please select Number of Months', 'iii-dictionary') ?>";
var NUMBER_INV = "<?php _e('Invalid number', 'iii-dictionary') ?>";
var LBL_NO_USERS = "<?php _e('Number of Users', 'iii-dictionary') ?>";
var LBL_NO_M = "<?php _e('Number of Months', 'iii-dictionary') ?>";
var LBL_NO_STUDENTS = "<?php _e('Number of Students', 'iii-dictionary') ?>";
var LBL_NO_STUDENTS_ADD = "<?php _e('Number of Students to Increase', 'iii-dictionary') ?>";
var LBL_NO_M_REMAIN = "<?php _e('Number of Remaining Months', 'iii-dictionary') ?>";
var LBL_NO_M_ADD = "<?php _e('Number of Months to Add', 'iii-dictionary') ?>";
var _ISMATH = <?php echo $is_math_panel ? 1 : 0?>;
var _IM4 	= <?php echo !empty($_SESSION['method_point']) ? $_SESSION['method_point'] : 0 ?>;
<?php //remove session method point 
	unset($_SESSION['method_point']);
?>
</script>

<?php if(!is_math_panel()) : ?>
	<?php get_dict_footer() ?>
<?php else : ?>
	<?php get_math_footer() ?>
<?php endif ?>